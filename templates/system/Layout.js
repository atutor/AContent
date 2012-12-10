var app=location.pathname;
var ap=window.location.href;
var a=ap.split('/');
var path=a[0];
var cont=1;

while(cont!=a.length-3){
    path= path + "/" + a[cont];
    cont++;
}

base = $('#content');
var layout_click;

$('input[id="apply_layout_to_content"]').live("click",function(){
    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled');  
});
$('input[id="apply_layout_to_course"]').live("click",function(){
    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled');  
});

function preview(lay){
   $('#newLayoutTemplate').remove();

   $('input').removeAttr('disabled'); 

   $('input[id="radio-'+lay+'"]').attr('checked','checked');
   $('input[id="radio-'+lay+'"]').attr('disabled','disabled');
   $('tr').removeClass('selected');
   $('input[id="radio-'+lay+'"]').closest('tr').addClass('selected');

    var cid = $("#radio_"+lay).attr('name');

    addLayoutTemplate(cid,lay);
}

function addLayoutTemplate(cid,layout){

    var url =path + "/templates/system/AJAX_actions.php";
    
    

    $.post(url, {content: cid}, function(structure){
        base.append(createLayoutTemplate(layout,structure));
    });
    
    $('#newLayoutTemplate').fadeIn(300);
}   

function createLayoutTemplate(layout,structure)
{
    layout_template='<div id="newLayoutTemplate" style="margin: 10px; margin-bottom: 15px;">';
    if(layout!="nothing"){
        if(structure.length>24){
            layout_template= layout_template + trans.templates.preview + ' ' + layout + ':';
            layout_template= layout_template + '<link rel="stylesheet" href="'+path+'/templates/layout/'+layout+'/'+layout+'.css" type="text/css" />';
            layout_template= layout_template + '<p>'+structure+'</p>';
        }else{
            layout_template= layout_template + '<p>' + trans.templates.devoid_content + '</p>';
            layout_template= layout_template + trans.templates.preview + ' ' + layout + ':';
            layout_template= layout_template + '<link rel="stylesheet" href="'+path+'/templates/layout/'+layout+'/'+layout+'.css" type="text/css" />';
            layout_template= layout_template + '<div id="content"><h1>' + trans.templates.title + '</h1><p>' + trans.templates.document_body + '</p></div>';
        }
    }else{
        if(structure.length>24){
                layout_template= layout_template + trans.templates.preview + ' ' + layout + ':';
                layout_template= layout_template + '<p>'+structure+'</p>';
            }else{
                layout_template= layout_template + '<p>' + trans.templates.devoid_content + '</p>';
                layout_template= layout_template + trans.templates.preview + ' ' + layout + ':';
                layout_template= layout_template + '<div id="content"><h1>' + trans.templates.title + '</h1><p>' + trans.templates.document_body + '</p></div>';
            }
    }
    layout_template =layout_template + '</div>';

    return layout_template;
}
