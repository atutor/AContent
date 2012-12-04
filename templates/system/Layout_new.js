/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var app=location.pathname;
var ap=window.location.href;
var a=ap.split('/');
var path= "/"+a[3]+"/"+a[4];

base = $('#content');
var layout_click;

// Control LAYUOT NOTHING
$('#radio_nothing').live("click",function(){
    $('#newLayoutTemplate').remove();
    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled');    
});

/*  Control LAYOUT CANADA   */
$('#radio_canada').live("click",function(){
    $('#newLayoutTemplate').remove();

    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled'); 

    $('input[id="radio-canada"]').attr('checked','checked');
    $('input[id="radio-canada"]').attr('disabled','disabled');


    var cid = $('#radio_canada').attr('name');
    
    alert(cid);
    addLayoutTemplate(cid,"canada");
    layout_click="canada";
   
});

/*  Control LAYOUT ITALY   */
$('#radio_italy').live("click",function(){
    $('#newLayoutTemplate').remove();
    
    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled'); 

    $('input[id="radio-italy"]').attr('checked','checked');
    $('input[id="radio-italy"]').attr('disabled','disabled');
    
    var cid = $('#radio_italy').attr('name');
    addLayoutTemplate(cid,"italy");
    layout_click="italy";
  
});

/*  Control LAYOUT SETI   */
$('#radio_seti').live("click",function(){
    $('#newLayoutTemplate').remove();
    
    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled'); 
    
    $('input[id="radio-seti"]').attr('checked','checked');
    $('input[id="radio-seti"]').attr('disabled','disabled');

    var cid = $('#radio_seti').attr('name');
    addLayoutTemplate(cid,"seti");
    layout_click="seti";
    
});

/*  Control LAYOUT UNIBO   */
$('#radio_unibo').live("click",function(){
    $('#newLayoutTemplate').remove();

    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled'); 

    $('input[id="radio-unibo"]').attr('checked','checked');
    $('input[id="radio-unibo"]').attr('disabled','disabled');

    var cid = $('#radio_unibo').attr('name');
    addLayoutTemplate(cid,"unibo");
    layout_click="unibo";

});

/*  Control LAYOUT WINDOWS   */
$('#radio_windows').live("click",function(){
    $('#newLayoutTemplate').remove();

    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled'); 

    $('input[id="radio-windows"]').attr('checked','checked');
    $('input[id="radio-windows"]').attr('disabled','disabled');

    var cid = $('#radio_windows').attr('name');
    addLayoutTemplate(cid,"windows");
    layout_click="windows";
});


$('input[id="apply_layout_to_content"]').live("click",function(){
    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled');  
});
$('input[id="apply_layout_to_course"]').live("click",function(){
    $('input[id="radio-'+layout_click+'"]').removeAttr('disabled');  
});

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

    if(structure.length>24){
        layout_template= layout_template + 'Preview ' + layout + ':';
        layout_template= layout_template + '<link rel="stylesheet" href="'+path+'/templates/layout/'+layout+'/'+layout+'.css" type="text/css" />';
        layout_template= layout_template + '<p>'+structure+'</p>';
    }else{
        layout_template= layout_template + '<p>Content devoid of text, below is an example with default text.</p>';
        layout_template= layout_template + 'Preview ' + layout + ':';
        layout_template= layout_template + '<link rel="stylesheet" href="'+path+'/templates/layout/'+layout+'/'+layout+'.css" type="text/css" />';
        layout_template= layout_template + '<div id="content"><h1>Title</h1><p>Body of the document</p></div>';
    }   
    layout_template =layout_template + '</div>';
    alert(layout_template);
    return layout_template;
}
