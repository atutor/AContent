var template,editmode=0;

$(function() {
    template=get_template_name();
    $('#page_preview').html($('#page_text').val())  ;
    $('#page_text').bind('input propertychange', function() {
        update_preview();
    });
    $('#page_preview').mouseup(function() {
        //insert_to_preview("<h2>Link</h2>");
        update_code();
    });
});

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
    $('#page_text').val($('#page_preview').html());
}

function prettify_code(){
    var html_str=$('#page_preview').html();
    var temp_element = document.createElement("DIV");
    temp_element.innerHTML = html_str;
    $('#page_text').val(html_to_xml($(temp_element.children(0)),""));
}

function update_preview(){
    $('#page_preview').html($('#page_text').val());
}

function insert_to_preview(html_str) {
    var selection, range;
    if (window.getSelection){   // IE9 and non-IE
        selection = window.getSelection();
        if (selection.getRangeAt && selection.rangeCount) {
            range = selection.getRangeAt(0);
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
        }
    }else if (document.selection && document.selection.type != "Control") { // IE < 9
        document.selection.createRange().pasteHTML(html_str);
    }
}

function get_selection_range(){
    var selection, range;
    if (window.getSelection){   // IE9 and non-IE
        selection = window.getSelection();
        if (selection.getRangeAt && selection.rangeCount) {
            range = selection.getRangeAt(0);
        }    
    }else range=false;
    return range;
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