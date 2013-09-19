var template,editmode=0;
var last_selection=false,last_range=false;
var base_url,last_code="";
var imgy=3;

$(function() {
    /* ---- Event handlers -------- */
    template=get_template_name();
    $("input:radio[name=edit_mode]").change(function() {    //radio box for edit mode
        editmode=$("input:radio[name=edit_mode]:checked").val();
        setup_toolbar();
    });
    $('#page_text').bind('input propertychange keyup', function() {     //preview panel edit
        update_preview();
    });
    $('#generate_scrn').click(function() {  //screenshot generate button
        generate_screenshot(); return false;
    });
    
    $('#page_preview').mouseup(function() { //store last selection on preview panel mouse up
        get_selection_range();
    });
    $('#page_preview').change(function() {
        update_code();
    });
    $('#page_preview').live('focus', function() {
        last_code = $(this).html();
    })
    $('#page_preview').live('blur keyup paste', function() {
        if (last_code != $(this).html()) {
            $(this).trigger('change');
        }
    });
    $('#page_preview').mouseover(function(event) {  
        event.stopPropagation();
    });

    $(".buttons.wrap").click(function() {   //toolbar buttons that wraps selection with a tag
        wrap_selection($(this).attr('arg'));
        update_code();
    });
    $(".buttons.insert").click(function() {   //toolbar buttons that insert into selection
        insert_html($(this).attr('id'));
    });
    $(".buttons.attrib").click(function() {   //toolbar buttons that changes selected elements' attributes
        change_attribute("align",$(this).attr('arg'));
    });
    $(".tagbtn").click(function() {
        insert_html(" ");
        return 0;
    });
    $("#insert-table").click(function() {
        $('#table_settings').toggle();
    });
    $("#format").change(function() {
        if($(this).val()!=='null') wrap_elements($(this).val(),['div','p','h2','h3','h4']);
        $(this).val('null');
    });
    $("#font-family, #font-size").change(function() {
        if($(this).val()!=='null') change_attribute($(this).attr('id'),$(this).val(),true);
        $(this).val('null');
    });

    /* --- Runs onload ---- */
    $.get("template_editor/ajax_handler.php?get=base_path", function(data) {
        base_url=data;
        update_preview();        
    });
    setup_toolbar();
    $("#screenshot_canvas").hide();
});


function setup_toolbar(){
    if(editmode==0){
        $("#page_text").hide();
        $("#page_preview, #page_toolbar").show();
    }else{
        $("#page_text").show();
        $("#page_preview, #page_toolbar").hide();
    }
    $('#table_settings').hide();
}

/**
 * Get the name of the currently edditing template
 * @author SupunGS
 * @return {string} template name
 */
function get_template_name(){
    var str=window.location.search;
    str=str.match(/temp=([a-zA-Z0-9_-]*)&*/);
    str=str[0].replace("temp=","");
    str=str.replace("&","");
    return str;
}

/**
 * Update the code in text area
 * @author SupunGS
 */
function update_code(){
    var temp=$('#page_preview').html().replace(/src=(.*?)dummy_template_image.png/g,'src="dnd_image');
    $('#page_text').val(temp);
}

/**
 * Update the preview panel
 * @author SupunGS
 */
function update_preview(){
    var temp=$('#page_text').val().replace(/dnd_image(.png)*/g,base_url+"images/dummy_template_image.png")
    $('#page_preview').html(temp);
}

/**
 * Insert an html string in to the current selection
 * @author SupunGS
 * @param {string} html_str html string to insert
 */
function insert_to_selection(html_str) {
    var range = last_range;
    if(range){
        var prnt_element = range.commonAncestorContainer;
        if(is_within("page_preview",prnt_element)){	//insert only if selection on #page_preview
            range.deleteContents();
            var temp_element = document.createElement("div");
            temp_element.innerHTML = html_str;
            var fragment = document.createDocumentFragment();
            var temp_node;
            while (temp_node= temp_element.firstChild ) {
                fragment.appendChild(temp_node);
            }
            range.insertNode(fragment);
        }
    }else if (document.selection && document.selection.type != "Control") { // IE < 9
        document.selection.createRange().pasteHTML(html_str);
    }
    update_code();
}

/**
 * Wrap current selection with a html tag
 * @author SupunGS
 * @param {string} tag html tag to surround
 */
function wrap_selection(tag) {
    var range = get_selection_range();
    if(range){
        var prnt_element = range.commonAncestorContainer;
        if(is_within("page_preview",prnt_element)){	//insert only if selection on #page_preview
            var selection = window.getSelection();
            var temp_element = document.createElement(tag);            
            temp_element.innerHTML = selection.toString();
            range.deleteContents();
            var fragment = document.createDocumentFragment();
            fragment.appendChild(temp_element);
            range.insertNode(fragment);
        }
    }else if (document.selection && document.selection.type != "Control") { // IE < 9
        document.selection.createRange().pasteHTML(html_str);
    }
    update_code();
}

/**
 * Wrap elements in current selection with a html tag. If a given wraping tag already exists replace it
 * @author SupunGS
 * @param {string} tag tag to surround
 * @param {array} replace array of tag names to replace if currently wrpaing
 */
function wrap_elements(tag, replace) {
    var range = last_range;
    if(range){
        var prnt_element = range.commonAncestorContainer;
        if(is_within("page_preview",prnt_element)){	//insert only if selection on #page_preview
            if(prnt_element.nodeType==3)prnt_element=prnt_element.parentNode;
            var temp_element = document.createElement(tag);
            var prev_sibling=null;
            $(prnt_element.childNodes).each(function (){
                if (last_selection.containsNode(this, true) || range.startContainer==this || range.startContainer==this.firstChild || range.endContainer==this.firstChild){
                    if(prev_sibling==null){
                        prev_sibling=$(this).prev();
                        if(this.nodeType==3) prev_sibling=$(this.parentNode).prev();
                    }
                    temp_element.appendChild(this);
                }
            });
            if(prnt_element.childNodes.length==0 && prnt_element.nodeName.toLowerCase()==tag.toLowerCase()){
                prnt_element.innerHTML=temp_element.innerHTML;
            }else if(prnt_element.childNodes.length==0 && $.inArray(prnt_element.nodeName.toLowerCase(),replace)+1){
                prnt_element.outerHTML=temp_element.outerHTML;
            }else if(prev_sibling.length) prev_sibling.after(temp_element);
            else $(prnt_element).prepend(temp_element);
        }
    }else if (document.selection && document.selection.type != "Control") { // IE < 9
        document.selection.createRange().pasteHTML(tag);
    }
    update_code();
}

/**
 * Change an attribute of the elements in current selection
 * @author SupunGS
 * @param {string} attribute attribute to change
 * @param {string} value new value
 * @param {boolean} style whether css attribute
 */
function change_attribute(attribute,value,style){
    var range = last_range;
    if(range){
        var prnt_element = range.commonAncestorContainer;
        if(is_within("page_preview",prnt_element)){	//insert only if selection on #page_preview
            if(prnt_element.nodeType==3)prnt_element=prnt_element.parentNode;
            $(prnt_element.childNodes).each(function (){
                if (last_selection.containsNode(this, true) || range.startContainer==this || range.startContainer==this.firstChild || range.endContainer==this.firstChild){
                    if(this.nodeType!=3 || range.startContainer==range.endContainer){
                        if(!style) $(this).closest( "div,span,p,h2,h3" ).attr(attribute,value);
                        else $(this).closest( "div,span,p,h1,h2,h3,h4,li" ).css(attribute,value);
                    }
                }
            });
        }
    }
    update_code();
}

/**
 * Get the current selection range on the page
 * @author SupunGS
 * @return {Range} current selection range
 */
function get_selection_range(){
    if (window.getSelection){   // IE9 and non-IE
        last_selection = window.getSelection();
        if (last_selection.getRangeAt && last_selection.rangeCount) {
            last_range = last_selection.getRangeAt(0);
        }    
    }else {
        last_selection=false;
        last_range=false;
    }
    return last_range;
}

/**
 * Check whether a given element is within a container with a specific id
 * @author SupunGS
 * @param {string} container id of the contatiner
 * @param {DOMElement} element element to check
 * @return {boolean} whether or not inside the contatiner
 */
function is_within(container,element) {
    rst=false;
    var parent = element;
    while(parent){
        if (parent.id==container)
            return true;
        else
            parent=parent.parentNode;
    }
    return false;
}

/**
 * Insert the relevent html code in to the selection
 * @author SupunGS
 * @param {string} cmd command specifying the code to insert
 */
function insert_html(cmd){
    if(cmd=='insert-ulist') insert_to_selection('<ul><li>item1<li>item2</ul>');
    else if(cmd=='insert-olist') insert_to_selection('<ol><li>item1<li>item2</ol>');
    else if(cmd=='insert-image') insert_to_selection('<img src="dnd_image" />');
    else if(cmd=='insert-paragraph') insert_to_selection('<p>Paragraph</p>');
    else if(cmd=='insert-link') insert_to_selection('<a href="#">Link</a>');
    else if(cmd=='add_table' && $("#num_rows").val()*$("#num_cols").val()<=25) insert_to_selection(generate_table($("#num_rows").val(),$("#num_cols").val()));
    update_code();
    update_preview();
}

/**
 * Get the html code for a table with given number of rows and columns
 * @author SupunGS
 * @param {Number} rows number of rows
 * @param {Number} columns number of columns
 * @return {Number} html code for the table
 */
function generate_table(rows, columns){
    var str="<table>";
    for(var r = 0; r < rows; r++){
        str=str+"<tr>";
        for(var c = 0; c < columns; c++){
            str=str+"<td>cell</td>";
        }
        str=str+"</tr>";
    }
    str=str+"</table>";
    return str;
}

/**
 * Auto generate a screenshot image for the template
 * @author SupunGS
 */
function generate_screenshot(){
    var canvas = document.getElementById("screenshot_canvas");
    var ctx = canvas.getContext("2d");
    canvas.width=canvas.width;
    ctx.lineWidth=1;ctx.strokeStyle="#666";
    imgy=3;
    draw_on_canvas($('#page_preview')[0],ctx);
    $("#screenshot_img").attr("src", canvas.toDataURL());

    $.post("template_editor/ajax_handler.php", {
       action: 'upload_image', temp: template ,image: canvas.toDataURL()
    });
}

/**
 * Recursively draw elements
 * @author SupunGS
 * @param {HTMLElement} element element to draw
 * @param {CanvasContext} ctx context of the canvas to draw on
 */
function draw_on_canvas(element,ctx){
    if(element.nodeType != 3)draw_element(element,ctx);
    if (element.hasChildNodes()) {
        var child = element.firstChild;
        while (child) {
            draw_on_canvas(child,ctx);
            child = child.nextSibling;
        }
    }
}

/**
 * Draw an element on the canvas for screenshot
 * @author SupunGS
 * @param {HTMLElement} element element to draw
 * @param {CanvasContext} ctx context of the canvas to draw on
 */
function draw_element(element, ctx){
    var width=124, height=128; //width and height of the image
    var tag_name=element.tagName.toLowerCase();
    if(tag_name=='h2'){
        ctx.strokeRect(3,imgy,width,21); imgy=imgy+25;
    }if(tag_name=='h3'){
        ctx.strokeRect(3,imgy,width,18); imgy=imgy+22;
    }if(tag_name=='h4'){
        ctx.strokeRect(3,imgy,width,15); imgy=imgy+19;
    }if(tag_name=='table'){
        var ch=80/element.children[0].children.length;
        var cw=width/get_column_count(element);
        var y=0;
        $.each(element.children[0].children,function(index, row){
            var x=0;
            $.each(row.children,function(index, col){
                ctx.strokeRect(4+ x*cw,imgy+y*ch,cw-2.5,ch-2.5);
                draw_line(ctx,3+ (x+0.2)*cw,imgy+(y+0.3)*ch,3+ (x+0.8)*cw,imgy+(y+0.3)*ch);
                draw_line(ctx,3+ (x+0.2)*cw,imgy+(y+0.6)*ch,3+ (x+0.8)*cw,imgy+(y+0.6)*ch);
                draw_containing_images(col,ctx,x*cw,imgy+y*ch,cw-2.5,ch-2.5);
                x++;
            });
            y++;
        });
        imgy=imgy+83;
    }if(tag_name=='div' && is_rendable(element)){        
        draw_line(ctx,width/6,imgy+10,width-width/6,imgy+10);
        draw_line(ctx,width/6,imgy+20,width-width/6,imgy+20);
        ctx.strokeRect(3,imgy,width,30); 
        draw_containing_images(element,ctx,3,imgy,width,30);
        imgy=imgy+34;
    }if(tag_name=='img'){
        if(element.parentElement==$('#page_preview')[0] || !is_rendable(element.parentElement)&& element.parentElement.parentElement==$('#page_preview')[0]){
            var imgrect = element.getBoundingClientRect();
            var prntrect = $('#page_preview')[0].getBoundingClientRect();
            var relx=(imgrect.left-prntrect.left)*width/prntrect.width;
            if(relx+25>width) relx=width-26;
            ctx.drawImage(element,3+relx,imgy,25,25);
            imgy=imgy+30;
        }
    }
}

/**
 * Draw all images contained in a given element in their relative positions
 * @author SupunGS
 * @param {HTMLElement} element element to check for images
 * @param {CanvasContext} ctx contex of the canvas element to draw
 * @param { Number} x x cordinate of the box represnting the container
 * @param { Number} y y cordinate of the box represnting the container
 * @param { Number} cw width of the box represnting the container
 * @param { Number} ch height of the box represnting the container
 */
function draw_containing_images(element,ctx,x,y,cw,ch){
    $(element).find('img').each(function(){
        if(this.parentElement==element || this.parentElement.parentElement==element && !is_rendable(this.parentElement)){
            var img=this;
            var imgrect = img.getBoundingClientRect();
            var elmrect = element.getBoundingClientRect();
            var relx=(imgrect.left-elmrect.left)*cw/elmrect.width;
            var rely=(imgrect.top-elmrect.top)*ch/elmrect.height;
            if(relx+25>cw)relx=cw-29;
            ctx.drawImage(img,x+relx,y+rely,25,25);
        }
    });
}

/**
 * Draw a line with given cordinates
 * @author SupunGS
 * @param {CanvasContext} ctx contex of the canvas element to draw
 * @param { Number} x1 x cordinate of the start point
 * @param { Number} y1 y cordinate of the start point
 * @param { Number} x2 x cordinate of the end point
 * @param { Number} y2 y cordinate of the end point
 */
function draw_line(ctx,x1,y1,x2,y2){
    var temp=ctx.strokeStyle;
    ctx.strokeStyle="#999";
    ctx.beginPath();
    ctx.moveTo(x1,y1);
    ctx.lineTo(x2,y2);
    ctx.stroke();
    ctx.strokeStyle=temp;
}

/**
 * Get the maximum number of columns in a row of a given table
 * @author SupunGS
 * @param {HTMLElement} table table to get the number of columns
 * @return {integer} number of columns
 */
function get_column_count(table){
    var colcount=0;
    var rows = table.children[0].children;
    for (i = 0; i < rows.length; i++) {
        clmns=rows[i].children.length;
        if(clmns>colcount) colcount=clmns;
    }
    return colcount;
}

/**
 * Check whether a given element should be rendered for screenshot
 * @author SupunGS
 * @param {HTMLElement} element element to check
 * @return {boolean} whether or not rendable
 */
function is_rendable(element){
    var children=element.childNodes;
    for (i = 0; i < children.length; i++) {
        if(children[i].nodeType==3 && $.trim(children[i].data) !="") return true;
    }
    return false;
}