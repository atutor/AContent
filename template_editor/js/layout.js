var template,editmode=0, lastselected, dltimage;
var csssheet;
var cssmap={};

$(function() {
    template=get_template_name();
    $('#css_dumy').hide();
    $('#image_confirm').hide();

    $("input:radio[name=edit_mode]").change(function() {
        editmode=$("input:radio[name=edit_mode]:checked").val();
        setup_toolbar();
    });
    
    $("#css_text").click(function() { 
        get_selected_style();
        setup_toolbar();
    });
    $("#css_text").change(function() {
        convert_code();
        add_preview_styles(parseCSS($("#css_text").val()));
    });
    
    $(".buttons").click(function() {
        var cmd=$(this).attr('id');
        var value=$(this).attr('arg');
        insert_css_rule( cmd, value);
    });
    
    $("#font-family, #font-size").change(function() {
        insert_css_rule($(this).attr('id'), $(this).val());
    });

    $("#font-color").change(function() {
        insert_css_rule( "color", "#"+$(this).val());
    });
    $("#background-color").change(function() {
        insert_css_rule( "background-color", "#"+$(this).val());
    });
    $("#border-width, #border-style, #border-color").change(function() {
        insert_css_rule( "border");
    });
    $("#background-image").change(function() {
        insert_css_rule( "background-image",$(this).val());
    });
    $(".delete_image").click(function() {
        dltimage=$(this).attr('file');
        $('#image_confirm strong').html(dltimage);
        $('#image_confirm').show();
    });
    $(".btn_delete").click(function() {
        if($(this).attr('name')=='delete') delete_image(dltimage);
        $('#image_confirm').hide();
    });
    $("#css_preview *").click(function(event) {
        event.stopPropagation();
        get_selected_style($(this).get(0).tagName);
        setup_toolbar();
    });

    $("#add_rule").click(function() {
        if($("#new_property").val()!="" && $("#new_value").val()!=""){            
            insert_css_rule( $("#new_property").val(),$("#new_value").val());
            $("#new_property").val("");
            $("#new_value").val("");
            setup_toolbar();
        }
    });
    convert_code();
    setup_toolbar();
});

$(window).load(function() {
    $('.thumbnail img').each(function() {
        var THUMBSIZE=128;
        var width=$(this).width();
        var height=$(this).height();
        if(width>THUMBSIZE || height>THUMBSIZE){
            if(width>height){
                $(this).width(THUMBSIZE);
                $(this).height(THUMBSIZE*height/width);
            //$(this).css({ "margin-top": THUMBSIZE*(1-height/width)/2});
            }else{
                $(this).width(128*width/height);
                $(this).height(128);
            //$(this).css({ "margin-left": THUMBSIZE*(1-width/height)/2});
            }
        }//else $(this).css({ "margin-left": (THUMBSIZE-width)/2, "margin-top":(THUMBSIZE-height)/2});
    });
});

/**
 * Setup the toolbar according to the currently selected CSS block
 * @author SupunGS
 */
function setup_toolbar(){
    $('#selector').val(lastselected);
    $('#layout_exttools').html("");

    if(editmode==0){
        $("#css_text").hide();
        $("#layout_toolbar").show();
        $('#font-size, #background-color, #border-width, #border-color').val("");

        if(typeof cssmap[lastselected] == 'undefined') return;

        var rules=csssheet[cssmap[lastselected]].rules;
        var str="<table>";
        for (var property in rules) {
            var ruleval=rules[property];

            if(property=='color') $('#font-color').val(ruleval.replace(/#/g,""));
            else if(property=='background-color') $('#background-color').val(ruleval.replace(/#/g,""));
            else if(property=='background-image') $('#background-image').val(ruleval.url0);
            else if(property=='border'){
                $('#border-width').val(ruleval.width);
                $('#border-style').val(ruleval.style);
                $('#border-color').val(ruleval.color.replace(/#/g,""));
            }else if($('#layout_toolbar #'+property).length ) $('#layout_toolbar #'+property).val(ruleval);
            else if(property.match(/comment|font-weight|font-style|text-align/)) continue;
            else{   //custom property
                str=str+"<tr><td>";
                str=str+'<label for="'+property+'">'+property+':</label></td>';
                str=str+'<td><input class="custom_property" id="'+property+'" type="text" size="15" value="'+rules[property]+'">';
                str=str+"</td></tr>";
            }
        }
        str=str+"</table>";
        $('#layout_exttools').html(str);

        $(".custom_property").change(function() {
            insert_css_rule($(this).attr('id'),$(this).val());
        }); 
    }else{
        $("#css_text").show();
        $("#layout_toolbar").hide();
    }
}

/**
 * Convert the css sheet into preview-able format and apply it to the preview panel
 * @author SupunGS
 * @param {array} sheet css string to parse
 */
function add_preview_styles(sheet){
    var style="";
    var len=sheet.length;
    for (i = 0; i < len; i++) {
        style=style+"#css_preview "+ sheet[i].toString();
    //style=style+"{"+ arrCSS[i].rule+ "}\n"
    }
    style=style.replace(/url\('*/g,"url('templates/layout/"+template+"/");
    $('#preview_styles').html(style);
}

/**
 * Convert the CSS code into object format and generate back the text format
 * @author SupunGS
 */
function convert_code(){
    cssmap={};
    csssheet=parseCSS($("#css_text").val());
    for (i = 0; i < csssheet.length; i++) {
        cssmap[csssheet[i].selector]=i;
    }
    add_preview_styles(csssheet);
    $("#css_text").val(get_css_code(csssheet));
}

/**
 * Parse a CSS string into an array of cssObjects
 * @author SupunGS
 * @param {string} css_text css string to parse
 * @return {array} array of cssObjects
 */
function parseCSS (css_text) {
    //css_text = css_text.replace(/\/\*(\r|\n|.)*\*\//g,"");
    var blocks = css_text.split('}');
    var length = blocks.length;
    var css_rules=new Array();
    for (var i = 0; i < length-1; i++)
    {
        var temp = blocks[i].split('{');
        var selector=$.trim(temp[0]);
        var cssobj =new cssObject(selector);
        cssobj.rules=parse_block(temp[1]);
        css_rules.push(cssobj);
    }
    return css_rules;
}

/**
 * Parse a CSS code block into an array of css rules
 * @author SupunGS
 * @param {string} css_block css code block to parse
 * @return {array} array of css rules
 */
function parse_block(css_block) {
    var lines = css_block.split(/;|\*\//);
    var length = lines.length;
    var rules={};
    var comment_count=0;
    for (var i = 0; i < length-1; i++){
        var segments=lines[i].split(":");
        var property=$.trim(segments[0]);
        var value=$.trim(segments[1]);
        if(property.match(/\/\*/)){
            rules["comment"+comment_count]=$.trim(property.replace("/*",""));
            comment_count++;
        }else{
            rules[property]=parse_rule(property,value);
        }
    }
    return rules;
}

/**
 * Parse a css value string into a css rule value object
 * @author SupunGS
 * @param {string} property related css property of the value
 * @param {string} rule css value to parse
 * @return {cssRuleValue} css rule value
 */
function parse_rule(property,rule) {
    var value;
    if(property=='padding' || property=='margin'){
        value=parse_css_padding(rule);
    }else if(property=='border'){
        value=parse_css_border(rule);
    }else if(property=='background-image'){
        value=parse_css_bgimage(rule);
    }else{
        value=rule;
    }
    return value;
}

/**
 * Parse a css padding or margin value into cssRuleValue
 * @author SupunGS
 * @param {string} value padding/margin value to parse
 * @return {cssRuleValue} padding/margin cssRuleValue
 */
function parse_css_padding(value) {
    var temp=value.split(" ");
    var padding=new cssRuleValue();
    padding['top']= temp[0];
    if(temp.length>1) padding['right']=temp[1];
    if(temp.length>2) padding['bottom']=temp[2];
    if(temp.length>3) padding['left']=temp[3];
    return padding;
}

/**
 * Parse a css border value into cssRuleValue
 * @author SupunGS
 * @param {string} value border value to parse
 * @return {cssRuleValue} border cssRuleValue
 */
function parse_css_border(value) {
    var temp=value.split(" ");
    var border=new cssRuleValue();
    border['width']= temp[0];
    if(temp.length>1) border['style']=temp[1];
    if(temp.length>2) border['color']=temp[2];
    return border;
}

/**
 * Parse a css background image value into cssRuleValue
 * @author SupunGS
 * @param {string} value background image value to parse
 * @return {cssRuleValue} background image cssRuleValue
 */
function parse_css_bgimage(value){
    value=value.replace(/'|"/,"");
    var urls=value.match(/url\((.*?)\)/g);
    var back_image=new cssRuleValue();
    for (i = 0; i < urls.length; i++) {
        urls[i]=urls[i].replace(/url\('*/g,"").replace(/'*\)/g,"");
        back_image['url'+i]=urls[i];
    }
    return back_image;
}

/**
 * Get the code/text/string representation of an array of cssObjects
 * @author SupunGS
 * @param {array} sheet cssObject array to get the code/text
 * @return {string} string representation
 */
function get_css_code(sheet){
    var code="";
    var len=sheet.length;
    for (i = 0; i < len; i++) {
        code=code+ sheet[i].toString()+"\n";
    }
    return code;
}

/**
 * Get the selector of the currently selected style
 * @author SupunGS
 * @param {string} tag optional parameter to get the selected style from tag.
 * @return {string} selector
 */
function get_selected_style(tag){
    var selector="";
    if (typeof tag == 'undefined') { // selected on code
        var pos=get_caret(document.getElementById("css_text"));
        var code=$("#css_text").val();
        if(pos<code.length){
            var precode=code.substring(0, pos);
            var postcode=code.substring(pos,code.length);
            var block=precode.substring(precode.lastIndexOf("}"),precode.length)+postcode.substring(0,postcode.indexOf("}")+1);
            block=block.replace(/^}/,"");
            selector=$.trim(block.replace(/ *{[^}]*} */g,""));
            lastselected=selector;
        }else{
            selector=lastselected;
        }
    }else{ // selected on preview
        tag=tag.toLowerCase();
        for (i = 0; i < csssheet.length; i++) {
            var temp=csssheet[i].selector.toLowerCase();
            if(temp.match(tag)){
                selector=csssheet[i].selector;
                break;
            }
        }
        if(selector=="")  selector="#content " + tag;            
        lastselected=selector;
    }
    //convert_code();
    return selector;
}

/**
 * Creates an instance of a cssObject
 * @constructor
 * @this {cssObject}
 */
function cssObject(selector){
    this.selector=selector;
    this.rules;

    /**
     * Get the string representation of the cssObject
     * @author SupunGS
     * @return {string} string representation
     */
    this.toString=function(){
        var str=this.selector+" {";
        for (var property in this.rules) {
            if(property.match(/comment/)){
                str=str+"\n\t/*"+this.rules[property]+"*/";
                continue;
            }
            str=str+"\n\t"+property+":\t";
            //if(typeof this.rules[property]=="string") str=str+this.rules[property];
            str=str+this.rules[property].toString();
            str=str+ ";"
        }
        str=str+"\n}";
        return str;
    }
    
    this.setSelector=function(selector){
        this.selector=selector;
    }
}

/**
 * Creates an instance of cssRuleValue
 * @constructor
 * @this {cssRuleValue}
 */
function cssRuleValue(){
    this.toString=function(){
        var str="";
        for (var property in this) {
            if (property!='toString') {
                if(property.match(/url./)) str=str+"url('"+this[property]+"'), ";
                else str=str+this[property]+" ";
            }
        }
        str=str.replace(/, $/,"");
        return str;
    }
}

/**
 * Get the current caret position of an element
 * @author SupunGS
 * @param {DOMElement} element to get the caret position
 * @return {int} caret position
 */
function get_caret(element) {
    if (element.selectionStart) {
        return element.selectionStart;
    } else if (document.selection) {
        element.focus();

        var rng = document.selection.createRange();
        if (rng == null) {
            return 0;
        }

        var re = element.createTextRange(),
        rc = re.duplicate();
        re.moveToBookmark(rng.getBookmark());
        rc.setEndPoint('EndToStart', re);

        return rc.text.length;
    }
    return 0;
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
 * Insert a CSS rule to the curently selected style block
 * @author SupunGS
 * @param {string} property CSS property to insert
 * @param {string} value value of the property
 */
function insert_css_rule(property, value){
    var style=lastselected; // get_selected_style();
    convert_code();
    if(typeof cssmap[style] == 'undefined') insert_css_block(style);
    var rules=csssheet[cssmap[style]].rules;
    if(property=="bold"){
        if('font-weight' in rules) delete rules['font-weight'];
        else rules['font-weight']='700';
    }else if(property=="italic") {
        if('font-style' in rules) delete rules['font-style'];
        else csssheet[cssmap[style]].rules['font-style']='italic';
    }else if(property=="underline") {
        if('text-decoration' in rules) delete rules['text-decoration'];
        else csssheet[cssmap[style]].rules['text-decoration']='underline';
    }else if(property=="border") {
        var border=new cssRuleValue();
        border['width']=$('#border-width').val();
        border['style']=$('#border-style').val();
        border['color']="#"+ $('#border-color').val();
        rules['border']=border;
    }else if(property=="background-image") {
        var back_image;
        if('background-image' in rules) back_image= rules['background-image'];
        else back_image=new cssRuleValue();
        if(value =='none' && 'url0' in back_image){
            delete back_image['url0'];
            if(!('url1' in back_image)) delete  rules['background-image'];
        }
        else{
            back_image['url0']=value;
            rules['background-image']=back_image;
        }
    }else if(property.match(/align-.*/)) {
        rules['text-align']=value;
    }else{
        rules[property]=value;
    }
    add_preview_styles(csssheet);
    $("#css_text").val(get_css_code(csssheet));
}

/**
 * Insert a CSS block to the csssheet
 * @author SupunGS
 * @param {string} selector selector of the CSS block
 */
function insert_css_block(selector){
    var cssobj =new cssObject(selector);
    cssobj.rules={};
    csssheet.push(cssobj);
    cssmap[selector]=csssheet.length-1;
}

/**
 * Delete a specified image from the template
 * @author SupunGS
 * @param {string} file file name to delete
 */
function delete_image(file){
    $.get("template_editor/ajax_handler.php?",{ 
        'action': 'delete_image',
        'temp': template,
        'file':file
    }, function(data) {
        $("#background-image option[value='"+template+"/"+file+"']").remove();
        $(".image_item[file='" + file+ "']").remove();
    });
}