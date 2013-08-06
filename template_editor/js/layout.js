var template,lastselected;
var csssheet;
var cssmap={};

$(function() {
    template=get_template_name();
    $('#css_dumy').hide();
   
    $("#css_text").click(function() { 
        get_selected_style();     setup_toolbar();
    });
    
    $(".buttons").click(function() {
        var cmd=$(this).attr('id');
        var value=$(this).attr('arg');
        insert_css_rule( cmd, value);
    });
    $("#font-family").change(function() {
        insert_css_rule("font-family", $(this).val());
    });
    $("#font-size").change(function() {
        insert_css_rule( "font-size", $(this).val());
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
    convert_code();
});
function setup_toolbar(){
    var rules=csssheet[cssmap[lastselected]].rules;
    if('font-size' in rules) $('#font-size').val(rules['font-size']);
    else $('#font-size').val("");

    if('font-family' in rules) $('#font-family').val(rules['font-family']);

    if('background-color' in rules) $('#background-color').val(rules['background-color'].replace(/#/g,""));
    else $('#background-color').val("");

    if('border' in rules){
        $('#border-width').val(rules['border'].width);
        $('#border-style').val(rules['border'].style);
        $('#border-color').val(rules['border'].color.replace(/#/g,""));
    }else $('#border-width, #border-color').val("");

}

function add_preview_styles(arrCSS){
    var style="";
    var len=arrCSS.length;
    for (i = 0; i < len; i++) {
        style=style+"#css_preview "+ arrCSS[i].toString();
    //style=style+"{"+ arrCSS[i].rule+ "}\n"
    }
    style=style.replace(/url\('*/g,"url('templates/layout/"+template+"/");
    $('#preview_styles').html(style);
}

function convert_code(){
    csssheet=parseCSS($("#css_text").val());
    for (i = 0; i < csssheet.length; i++) {
        cssmap[csssheet[i].selector]=i;
    }
    add_preview_styles(csssheet);
    $("#css_text").val(get_css_code(csssheet));
}
/**
 * Parse a CSS string into an array of css rules
 * @author SupunGS
 * @param {string} css_text css string to parse
 * @return {array} array of css rules
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

function parse_block(css_block) {
    var lines = css_block.split(';');
    var length = lines.length;
    var rules={};
    for (var i = 0; i < length-1; i++){
        var segments=lines[i].split(":");
        var property=$.trim(segments[0]);
        var value=$.trim(segments[1]);
        rules[property]=parse_rule(property,value);
    }
    return rules;
}

function parse_rule(property,rule) {
    var value;
    if(property=='padding' || property=='margin'){
        value=parse_css_padding(rule);
    }if(property=='border'){
        value=parse_css_border(rule);
    }else{
        value=rule;
    }
    return value;
}

function parse_css_padding(value) {
    var temp=value.split(" ");
    var padding=new cssRuleValue();
    padding['top']= temp[0];
    if(temp.length>1) padding['right']=temp[1];
    if(temp.length>2) padding['bottom']=temp[2];
    if(temp.length>3) padding['left']=temp[3];
    return padding;
}

function parse_css_border(value) {
    var temp=value.split(" ");
    var border=new cssRuleValue();
    border['width']= temp[0];
    if(temp.length>1) border['style']=temp[1];
    if(temp.length>2) border['color']=temp[2];
    return border;
}

function cssobj_to_string(obj){
    var str="";
    for (var property in obj) {
        str=str+obj[property]+" ";
    }
    return str;
}

function get_css_code(arrCSS){
    var code="";
    var len=arrCSS.length;
    for (i = 0; i < len; i++) {
        code=code+ arrCSS[i].toString()+"\n";
    }
    return code;
}

function get_selected_style(){
    var pos=getCaret(document.getElementById("css_text"));
    var code=$("#css_text").val();
    if(pos<code.length){
        var precode=code.substring(0, pos);
        var postcode=code.substring(pos,code.length);
        var block=precode.substring(precode.lastIndexOf("}"),precode.length)+postcode.substring(0,postcode.indexOf("}")+1);
        block=block.replace(/^}/,"");
        var selector=$.trim(block.replace(/ *{[^}]*} */g,""));
        lastselected=selector;
    }else{
        selector=lastselected;
    }
    //convert_code();
    return selector;
}

function cssObject(selector){
    this.selector=selector;
    this.rules;
    
    this.toString=function(){
        var str=this.selector+" {";
        for (var property in this.rules) {
            str=str+"\n\t"+property+":\t";
            if(typeof this.rules[property]=="string") str=str+this.rules[property];
            else  str=str+this.rules[property].toString();
            str=str+ ";"
        }
        str=str+"\n}";
        return str;
    }
    
    this.setSelector=function(selector){
        this.selector=selector;
    }
}

function cssRuleValue(){
    this.toString=function(){
        var str="";
        for (var property in this) {
            if (property!='toString') {
                str=str+this[property]+" ";
            }
        }
        return str;
    }
}

function getCaret(el) {
    if (el.selectionStart) {
        return el.selectionStart;
    } else if (document.selection) {
        el.focus();

        var r = document.selection.createRange();
        if (r == null) {
            return 0;
        }

        var re = el.createTextRange(),
        rc = re.duplicate();
        re.moveToBookmark(r.getBookmark());
        rc.setEndPoint('EndToStart', re);

        return rc.text.length;
    }
    return 0;
}

function get_template_name(){
    var str=window.location.search;
    str=str.match(/temp=([a-zA-Z0-9_-]*)&*/);
    str=str[0].replace("temp=","");
    str=str.replace("&","");
    return str;
}

function insert_css_rule(property, value){
    var style=get_selected_style();
    convert_code();
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
        var border;
        if('border' in rules) border=rules['border'];
        else border=new cssRuleValue();
        border['width']=$('#border-width').val();
        border['style']=$('#border-style').val();
        border['color']="#"+ $('#border-color').val();
        rules['border']=border;
    }else if(property.match(/align-.*/)) {
        csssheet[cssmap[style]].rules['text-align']=value;
    }else{
        csssheet[cssmap[style]].rules[property]=value;
    }
    add_preview_styles(csssheet);
    $("#css_text").val(get_css_code(csssheet));
}