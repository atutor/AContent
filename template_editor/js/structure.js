var selected_item;
var history_stack=new HistoryStack();
var loc = window.location;
var base_url = loc.protocol + "//" + loc.host + "/" + loc.pathname.split('/')[1];

$(function() {  
    //var parser = new DOMParser();
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
    $("#node_min, #node_max").keyup(function() {
        selected_item.attr('min',$("#node_min").val());
        selected_item.attr('max',$("#node_max").val());
        generate_xml();
    });
    $(".btn_move").click(function() {
        var id=$(this).attr('id');
        if(id=='btn_up' ) move_up(selected_item)
        else if(id=='btn_down' ) move_down(selected_item)
        refresh_tree();
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
        if(node_type!='structure') $('#btn_delete, .btn_move' ).removeAttr("disabled");
        if(node_type==""){
            $('#node_name').val("");
        }else{
            $node_name=selected_item.attr('name');
            if($node_name=='null') $node_name="";
            $('#node_name').val($node_name);

            $node_min=selected_item.attr('min');
            if($node_min=='null') $node_min="";
            $('#node_min').val($node_min);

            $node_max=selected_item.attr('max');
            if($node_max=='null') $node_max="";
            $('#node_max').val($node_max);
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
        setup_toolbar(selected_item.attr('type'));
        return false;
    });
}

function generate_xml(){
    history_stack.record($('#tarea').val());
    var parser = new DOMParser();
    var htmlstr=$('#preview').html();
    htmlstr=htmlstr.replace(/<ol(.*?)>/g,"");
    htmlstr=htmlstr.replace(/<\/ol(.*?)>/g,"");
    htmlstr=htmlstr.replace(/<span(.*?)span>/g,"");
    htmlstr=htmlstr.replace(/<a(.*?)a>/g,"");
    var xmldoc= parser.parseFromString(htmlstr, "text/xml");
    $('#tarea').val(html_to_xml(xmldoc,xmldoc.firstChild,""));
}

function generate_tree(element, parent) {
    var parent_class=parent || "folder";
    var str="<ol class='"+ parent_class+"'>";
    $.each(element.childNodes,function(index, child){
        if(child.nodeType !=3){
            if(child.nodeName=="structure")
                str=str+ "<li type='"+child.nodeName +"' name='"+ child.getAttribute('name') +
                    "'><span class='node_icons'><img class='img-size-tree' src='"+base_url+"/images/tree/tree_folder.gif'></span>"+
                    "<a href='javascript:;' class='items' accesskey='z'>"+ child.nodeName+"</a>" ;
            else{
                str=str+ "<li type='"+child.nodeName +"' name='"+ child.getAttribute('name') +"' max='"+child.getAttribute('max')
                +"' min='"+child.getAttribute('min')+"' style='cursor:move;'>"+
                "<span class='node_icons'><img class='img-size-tree' src='"+base_url+"/images/tree/tree_end.gif'>"+
                "<img class='img-size-tree' src='"+base_url+"/images/tree/"+get_class_type(child.nodeName)+".gif'>"+
                "</span><a href='javascript:;' class='items' accesskey='z'>"+ child.nodeName+"</a>" ;
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
    newNode = "<li type='" +element + "' ><a class='items'>"+element+"</a></li>";
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
    else return "tree_"+node_name;
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
    if(element.hasChildNodes() && element.childElementCount>0 ){
        str=str+">\n";
        $.each(element.childNodes,function (index, child){
            if(child && child.nodeType !=3){
                var childstr=   html_to_xml(xmldoc,child,prefix+"  ");
                str=str+childstr;
            }
        });
        //        var i=0;
        //        for (i = 0; i < element.childElementCount;++i) {
        //            var aa=$(element);
        //            var child=aa.children[i];
        //            if(child && child.nodeType !=3){
        //                var childstr=   html_to_xml(xmldoc,child,prefix+"  ");
        //                str=str+childstr;
        //            }
        //        }
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

function move_up(element){
    var parent=element.parent().parent();
    var sibling=element.prev();
    if (sibling.length){
        $(sibling).before(element);
    }else if(parent.attr('type')!='structure'){
        var prev=parent.prev();
        var cls=element.parent().attr('class').split(" ")[0];
        while(true){
            if(!prev.length){
                if(parent.parent().attr('class').split(" ")[0]==cls){
                    parent.before(element);
                    break;
                }
                parent=parent.parent().parent();
                if(parent.attr('type')=='structure') break;
                prev=parent.prev();
                continue;
            }
            var insert_node=prev.find('.'+cls +':last');
            insert_node.append(element);
            if(insert_node.length) break;
            prev=prev.prev();
        }
    }
}

function move_down(element){
    var parent=element.parent().parent();
    var sibling=element.next();
    if (sibling.length){
        $(sibling).after(element);
    }else if(parent.attr('type')!='structure'){       
        var next=parent.next();
        var cls=element.parent().attr('class').split(" ")[0];
        while(true){
            if(!next.length){
                if(parent.parent().attr('class').split(" ")[0]==cls){
                    parent.after(element);
                    break;
                }
                parent=parent.parent().parent();
                if(parent.attr('type')=='structure') break;
                next=parent.next();
                continue;
            }
            var insert_node=next.find('.'+cls +':first');
            insert_node.prepend(element);
            if(insert_node.length) break;
            next=next.next();
        }
    }
}