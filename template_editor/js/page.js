var template,editmode=0;
var last_selection=false,last_range=false;
var base_url,last_code="";

$(function() {
    template=get_template_name();
    $("input:radio[name=edit_mode]").change(function() {
        editmode=$("input:radio[name=edit_mode]:checked").val();
        setup_toolbar();
    });
    $('#page_text').bind('input propertychange', function() {
        update_preview();
    });
    $('#page_preview').mouseup(function() { 
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

    $(".buttons.wrap").click(function() {
        wrap_selection($(this).attr('arg'));
        update_code();
    });
    $(".buttons.insert").click(function() {
        insert_html($(this).attr('id'));
    });
    $(".buttons.attrib").click(function() {
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
        if($(this).val()!=='null') wrap_elements($(this).val(),['div','p','h1','h2']);
        $(this).val('null');
    });
    $("#font-family, #font-size").change(function() {
        if($(this).val()!=='null') change_attribute($(this).attr('id'),$(this).val(),true);
        $(this).val('null');
    });

    $.get("template_editor/ajax_handler.php?get=base_path", function(data) {
        base_url=data;
        update_preview();
    });
    setup_toolbar();
});


function setup_toolbar(){
    if(editmode==0){
        $("#page_text").hide();
        $("#page_toolbar").show();
    }else{
        $("#page_text").show();
        $("#page_toolbar").hide();
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

function update_code(){
    var temp=$('#page_preview').html().replace(/src=(.*?)dummy_template_image.png/g,'src="dnd_image');
    $('#page_text').val(temp);
}

function prettify_code(){
    var html_str=$('#page_preview').html();
    var temp_element = document.createElement("DIV");
    temp_element.innerHTML = html_str;
    $('#page_text').val(html_to_xml($(temp_element.children(0)),""));
}

function update_preview(){
    var temp=$('#page_text').val().replace(/dnd_image(.png)*/g,base_url+"images/dummy_template_image.png")
    $('#page_preview').html(temp);
}

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
    parent = element;
    while(parent){
        if (parent.id==container)
            return true;
        else
            parent=parent.parentNode;
    }
    return false;
}

function html_to_xml(element,prefix) {
    var str=prefix+ "<"+element.prop("tagName") ;
    $(element[0].attributes).each(function() {
        console.log(this.nodeName+':'+this.nodeValue);
        str=str+ ' ' + this.nodeName+'="'+this.nodeValue+'"';
    });
    if(element[0].childNodes.length>0 ){
        var data=element[0].firstChild.data;
        str=str+">\n";
        $(element[0].childNodes).each(function (){
            if(this.nodeType !=3){
                var childstr=   html_to_xml($(this),prefix+"	");
                str=str+childstr;
            }else{
                var data=$.trim(this.data);
                if(data!="") str=str+ prefix+"	"+ data+"\n";
            }
        });
        str=str+ prefix+ "</"+element.prop("tagName")+">\n";
    }else{
        str=str+"/>\n";
    }
    return str;
}

function insert_html(cmd){
    if(cmd=='insert-ulist') insert_to_selection('<ul><li>&nbsp;</ul>');
    else if(cmd=='insert-olist') insert_to_selection('<ol><li>&nbsp;</ol>');
    else if(cmd=='insert-image') insert_to_selection('<img src="dnd_image" />');
    else if(cmd=='add_table' && $("#num_rows").val()*$("#num_cols").val()<=25) insert_to_selection(generate_table($("#num_rows").val(),$("#num_cols").val()));
    update_code();
}

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