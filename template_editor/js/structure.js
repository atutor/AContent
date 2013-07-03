var selected_item;
var history_stack=new HistoryStack();

$(function() {  
    var parser = new DOMParser();    
    $("#toxml").click(function() {
        generate_xml();
    });
    $("#totree").click(function() {
        draw_tree();
    });
    $(".btn_insert").click(function() {
        var insert_node=$(this).attr('id');
        insert_node=insert_node.replace("insert_","");
        insert_to_tree(insert_node,selected_item);
        refresh_tree();
    });
    $("#btn_delete").click(function() {
        delete_from_tree(selected_item);
        refresh_tree();
    });
    $(".btn_history").click(function() {
        var id=$(this).attr('id');
        var data;
        if(id=='btn_undo' && history_stack.hasUndo()){
            data=history_stack.undo($('#tarea').val());
        } else if(id=='btn_redo' && history_stack.hasRedo()){
            data=history_stack.redo($('#tarea').val());
        }
        $('#tarea').val(data);
        draw_tree();
        setup_toolbar("");
    });
    $("#node_name").keyup(function() { 
        selected_item.attr('name',$(this).val());
        generate_xml();
    });
    draw_tree();
});

function refresh_tree(){
    generate_xml();
    draw_tree();    
    setup_toolbar("");
}

function setup_toolbar(node_type){
    $('[class^=btn_]' ).attr("disabled", "disabled");
    if(selected_item){
        if(node_type=='structure' || node_type=='folder'){
            $('#insert_folder, #insert_page' ).removeAttr("disabled");
        }else if(node_type=='page'){
            $('#insert_page_templates, #insert_tests' ).removeAttr("disabled");
        }else if(node_type=='page_templates'){
            $('#insert_page_template' ).removeAttr("disabled");
        }else if(node_type=='tests'){
            $('#insert_test' ).removeAttr("disabled");
        }
        if(node_type!='structure') $('#btn_delete' ).removeAttr("disabled");
        if(node_type==""){
            $('#node_name').val("");
        }else{
            $('#node_name').val(selected_item.attr('name'));
        }
    }
    if(history_stack.hasUndo()) $('#btn_undo' ).removeAttr("disabled");
    if(history_stack.hasRedo()) $('#btn_redo' ).removeAttr("disabled");
}

function draw_tree(){
    var parser = new DOMParser();
    var xml = $('#tarea').val();
    var doc = parser.parseFromString(xml, "text/xml");
    $('#preview').html(generate_tree(doc))  ;

    $('[class^=tree_]' ).each(function(index, item) {
        $(item).sortable({
            revert: false ,
            connectWith: "."+$(item).attr('class'),
            update: function( event, ui ) {
                generate_xml(); setup_toolbar();
            }
        }).disableSelection();
    });
    $('.items').click(function(e) {
        selected_item=$(this).parent();
        setup_toolbar($(this).parent().attr('type'));
    });
}

function generate_xml(){
    history_stack.record($('#tarea').val());
    var parser = new DOMParser();
    var htmlstr=$('#preview').html();
    htmlstr=htmlstr.replace(/<ol(.*?)>/g,"");
    htmlstr=htmlstr.replace(/<\/ol(.*?)>/g,"");
    htmlstr=htmlstr.replace(/<div(.*?)div>/g,"");
    var xmldoc= parser.parseFromString(htmlstr, "text/xml");
    $('#tarea').val(html_to_xml(xmldoc,xmldoc.firstChild,""));
}

function generate_tree(element, parent) {
    var parent_class=parent || "folder";
    var str="<ol class='"+ parent_class+"'>";
    $.each(element.childNodes,function(index, child){
        if(child.nodeType !=3){
            if(child.nodeName=="structure")
                str=str+ "<li type='"+child.nodeName +"' name='"+ child.getAttribute('name') +"'><div class='items'>"+ child.nodeName+"</div>" ;
            else{
                str=str+ "<li type='"+child.nodeName +"' name='"+ child.getAttribute('name') +"' max='"+child.getAttribute('max')
                +"' min='"+child.getAttribute('min')+"' style='cursor:move;'>"+
                "<div class='items'>"+ child.nodeName+"</div>" ;
            }
            if(child.hasChildNodes()){
                str=str+ generate_tree(child,get_class_type(child.nodeName));
            }
            str=str+ "</li>";
        }
    });
    str=str+"</ol>";
    return str;
}

function insert_to_tree(element, parent){
    var insrting_list=parent.children("ol");
    if(insrting_list.length==0){
        parent.append(document.createElement("ol"));
        insrting_list=parent.children("ol");
    }
    newNode = "<li type='" +element + "' ><div class='items'>"+element+"</div></li>";
    insrting_list.append(newNode);
}

function delete_from_tree(element){
    element.remove();
}

function get_class_type(node_name) {
    if(node_name=="structure" ||node_name=="folder" ) return "tree_folder";
    else if(node_name=="page" ) return "tree_page";
    else if(node_name=="page_templates" ) return "tree_page_templates";
    else if(node_name=="tests" ) return "tree_tests";
    else return "";
}

function html_to_xml(xmldoc,element,prefix) {
    var str=prefix+ "<"+element.getAttribute("type") ;
    if(element.getAttribute("max") && element.getAttribute("max")!="null"){
        str=str+ " max='"+element.getAttribute("max")+"'";
    }if(element.getAttribute("min") && element.getAttribute("min")!="null"){
        str=str+ " min='"+element.getAttribute("min")+"'";
    }if(element.getAttribute("name") && element.getAttribute("name")!="null"){
        str=str+ " name='"+element.getAttribute("name")+"'";
    }
    if(element.hasChildNodes() && element.children.length>0 ){
        str=str+">\n";
        $.each(element.children,function(index, child){
            if(child && child.nodeType !=3){                
                var childstr=    html_to_xml(xmldoc,child,prefix+"  ");
                str=str+childstr;
            }
        });
        str=str+ prefix+ "</"+element.getAttribute("type")+">\n";
    }else{
        str=str+"/>\n";
    }    
    return str;
}

function HistoryStack()
{
    this.undo_stack=new Array();
    this.redo_stack=new Array();

    this.undo=function(data){
        var temp= this.undo_stack.pop();
        this.redo_stack.push(data);
        return temp;
    }
    this.redo=function(data){
        var temp= this.redo_stack.pop();
        this.undo_stack.push(data);
        return temp;
    }
    this.record=function(data){
        this.undo_stack.push(data);
        this.redo_stack=[];
    }
    this.hasRedo=function(){
        if(this.redo_stack.length>0) return true;
        else return false;
    }
    this.hasUndo=function(){
        if(this.undo_stack.length>0) return true;
        else return false;
    }
}