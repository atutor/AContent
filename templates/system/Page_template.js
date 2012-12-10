/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var num_layout_select=0; // for layout control
var removePageTemplateTopBar    = '<div class="removePageTemplateTopBar"><button type="button" class="removePageTemplate">X</button></div>';

var app=location.pathname;
var ap=window.location.href;
var a=ap.split('/');
var path=a[0];
var cont=1;

while(cont!=a.length-3){
    path= path + "/" + a[cont];
    cont++;
}

var sortTools= '<div class="sortTools">\n\
<button type="button" class="movePageTemplateTop no-style"><img src="'+ path + '/templates/system/top.png" class="" alt="move top" /></button>\n\
<button type="button" class="movePageTemplateUp no-style"><img src="'+ path + '/templates/system/up.png" class="" alt="move up" /></button>\n\
<button type="button" class="movePageTemplateDown no-style"><img src="'+ path + '/templates/system/down.png" class="" alt="move down" /></button>\n\
<button type="button" class="movePageTemplateBottom no-style"><img src="'+ path + '/templates/system/bottom.png" alt="move bottom" /></button>\n\
</div>';

$(document).ready(function(){
    var module_name = "Page templates";
    module_name = module_name.replace(/ /g, '');

    // if the user is an authenticated author
    // show the module
    if("1" == 1 && "content.php" == "content.php")    
        $('#menu_' + module_name + ' form').show();
    else
        $('#menu_' + module_name + ' form').hide();

    base = $('#content-text');
    
    // show "edit templates" button if there are existing templates on the page
    var unsaved = false;
    
    var hasTemplatesOnPage = function () {
        return $('.page_template').length > 0;
    };

    if (hasTemplatesOnPage()) {
        $('#orderPageTemplate_bar').css('display','inline');
        $('#savePageTemplate_bar').css('display','inline');
    }

    // Hide content editor default "save" and "close" buttons
    // Note that the id of their container div could be "saved" or "unsaved" depending on whether there's unsaved info
    $('.unsaved').css('display','none');
    $('.saved').css('display','none');
    
    ////////////////////////////////////////
    //    INCLUSIONS / DECLARATIONS / DEFINITIONS
    ////////////////////////////////////////

    var boxPageTemplate = '<div class="boxPageTemplate"><ul></ul></div>';

    // consider only page_template during the content preview
    // put on the content top the page template box
    boxPageTemplateToolbox = "<div class=\"boxPageTemplateToolbox\"><ul>";

    // paste
    boxPageTemplateToolbox = boxPageTemplateToolbox + "<li id=\"pageTemplatePaste\"><img src=\"<?php echo $templates; ?>system/paste.png\" title=\"<?php echo _AT('paste'); ?>\" alt=\"\" /> <?php echo _AT('paste_page_template_sequence'); ?></li>";
    
    // copy
    boxPageTemplateToolbox    = boxPageTemplateToolbox + "<li id=\"pageTemplateCopy\"><img src=\"<?php echo $templates; ?>system/copy.png\" title=\"<?php echo _AT('copy'); ?>\" alt=\"\" /> <?php echo _AT('copy_page_template_sequence'); ?></li>";
    
    boxPageTemplateToolbox = boxPageTemplateToolbox + "</ul></div>";

    ////////////////////////////////////////
    //    page_template EVENT ON / OFF
    ////////////////////////////////////////

    $('#pageTemplateCopy').live("click", function(){

        var allpage_template = '';

        unsaved = true;
        
        $('.page_template').each(function(index) {
            allpage_template = allpage_template + "|" + $(this).attr('class');
        });

        var c_name        = 'pageTemplateClipboard';
        var value        = allpage_template;
        var exdays        = '1';

        // create  cookie
        var exdate        = new Date();
        exdate.setDate(exdate.getDate() + exdays);
        var c_value        = escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
        document.cookie    = c_name + "=" + c_value;


        $('#pageTemplateCopy').css('background','#f0f8ff');

        $('#pageTemplatePaste').css('display','inline');

    });

    $('#pageTemplatePaste').live("click", function(){

        var c_name        = 'pageTemplateClipboard';

        unsaved = true;
        
        // read cookie
        var i,x,y,ARRcookies=document.cookie.split(";");
        for (i=0;i<ARRcookies.length;i++){
            x    = ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
            y    = ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
            x    = x.replace(/^\s+|\s+$/g,"");
            if (x==c_name){
                if(unescape(y) == '')
                    alert("no set copied!");    
                else
                    var page_template = unescape(y);
            }
        }

        // if there are already other page_template
        // ask if you want to add the clipboard in head
        if($('.page_template').attr('class') != 'pageTemplate  noPageTemplate'){
            if(!confirm("There are models already on the page. Do you want to insert the copied models on the top of the page?")){
                return false;
            }
        }

        // add page_template
        //$('#dnd').html(page_template + $('#dnd').html());
        var m = page_template.split('|');

        var noPageTemplateAfter = 0;
        // the cycle starts from 1 because the first element is ''
        for(i=1; i<m.length; i++){
                        var pageTempalteID = m[i].replace("page_template ", "");
            //alert(pageTemplateID);
            if(pageTempalteID == 'noPageTemplate')
                noPageTemplateAfter = 1;
            else
                addPageTemplate(pageTempalteID, noPageTemplateAfter);
        }
        
        // save the new content
        //saveChangeInContent();

    });

    ////////////////////////////////////////
    //    ARRANGE page_template BUTTON
    ////////////////////////////////////////
    
    $('#orderPageTemplate').live("click",function(event){
        event.preventDefault();
        
        unsaved = true;
        
        $('#success').css('display','none');
        $('.removePageTemplateTopBar').css('display','inline');
        $('.sortTools').css('visibility','visible');
        $('.pageTemplateContent').css('border','1px solid #DDDDDD');
    });
    
    ////////////////////////////////////////
    //    page_template SORTING
    ////////////////////////////////////////

    // top
    $('.movePageTemplateTop').live("click", function(event){

        event.preventDefault();

        unsaved = true;
        
        // this page_template
        var pageTemplate = $(this).parents('.page_template');

        base.prepend(pageTemplate);

    });

    // up

    $('.movePageTemplateUp').live("click", function(event){

        event.preventDefault();

        unsaved = true;
        
        // this page_template
        var page_template = $(this).parents('.page_template');

        if(page_template.prev().attr('class') != undefined){
            var parent = page_template.prev();
            parent.before(page_template);
        }else{
            base.prepend(page_template);
        }

    });

    // down

    $('.movePageTemplateDown').live("click", function(event){

        event.preventDefault();

        unsaved = true;
        
        // this page_template
        var page_template = $(this).parents('.page_template');

        //page_template.next('.page_template').css('background', 'red');
        //alert(page_template.next().attr('class'));
        //page_template.css('background', 'red');

        if(page_template.next().attr('class') != undefined){
        
            var child = page_template.next();
            child.after(page_template);
        }else
        {
            base.append(page_template);
        }

    });

    // bottom

    $('.movePageTemplateBottom').live("click", function(event){

        event.preventDefault();
        
        unsaved = true;
        
        // this page_template
        var page_template = $(this).parents('.page_template');

        base.append(page_template);
    });

    ////////////////////////////////////////
    //    ADD A NEW page_template
    ////////////////////////////////////////

    $('.boxPageTemplate li').live("click", function(){

        var structure    = "";

        unsaved = true;
        num_layout_select++;

        // take the name of the template you want to insert
        var pageTempalteID    = $(this).find('table').attr('id');
        
        // add page_template
        addPageTemplate(pageTempalteID, 0);
    });

    ////////////////////////////////////////
    //    DELETE SELECTED page_template
    ////////////////////////////////////////

    $('.removePageTemplate').live("click", function(event){
        event.preventDefault();
        
        unsaved = true;
        num_layout_select--;
        // Se non vi sono layout selezionati il bottone per aprire
        // la preview layout va sempre visibile e l'arrange va nascosto 
        if(num_layout_select==0){
            $('#activate_page_template_bar').css('display','inline');
            $('#orderPageTemplate_bar').css('display','none');
            $('#deactivate_page_template_bar').css('display','none');
            $('.boxTotal').css('display','none');
        }

        var page_template = $(this).parents('.page_template');

        // fade effect
        page_template.fadeOut(300, function(){
            page_template.remove();
            var supp= $('#content-previous').html();
            if(supp==''){
                $('#with-cont').css('display','none');
                $('#with-cont-pre').css('display','none');
                $('#no-cont-pre').css('display','inline');
            }
        });

    });

    $("#body_text").live("mouseover", function(){
        var oldContent    = tinyMCE.activeEditor.getContent();
        var newContent;
        tinyMCE.activeEditor.setContent(newContent);
    });
    
    /*
    *    Fix an annoying behavior of browsers:
    *    when I vertically scroll the contents of a div (in this case the page_template)
    *    and reach the bottom, the focus is automatically taken from the page that scrolls.
    */
    $(".boxPageTemplate").live({
        mouseover: function() {
            $('body').css('overflow','hidden');
            $('body').css('padding-right','15px');
        },
        mouseout: function() {
            $('body').css('overflow','auto');
            $('body').css('padding-right','0px');
        }
    });

     $('#deactivate_page_template').live("click",function(event){
        event.preventDefault();
        
        $('#success').css('display','none');
    
        $('.boxTotal').css('display','none');
        
        if (!hasTemplatesOnPage()) {
            $('#orderPageTemplate_bar').css('display','none');
            
            if (!unsaved) {
                $('#savePageTemplate_bar').css('display','none');
            }
        }
    
        // Remove Board and label X
        $('.pageTemplateContent').css('border','none');
        $('.removePageTemplateTopBar').css('display','none');
        $('.sortTools').css('visibility','hidden');
        $('#activate_page_template_bar').css('display','inline');
         
        $('#deactivate_page_template_bar').css('display','none');
     });
    
    $('#activate_page_template').live("click",function(event){
        event.preventDefault();
        $(document).scrollTo('#activate_page_template_bar');
        
        $('#success').css('display','none');
        $('.boxTotal').css('display','inline');
        
        $('#activate_page_template_bar').css('display','none');
        $('#deactivate_page_template_bar').css('display','inline');

        $('#orderPageTemplate_bar').css('display','inline');
        $('#savePageTemplate_bar').css('display','inline');

    });

    /*######################################
            FUNCTIONS
    ######################################*/
    
    function addPageTemplate(pageTempalteID, afternoPageTemplate){
    
        var url = path + "/templates/system/AJAX_actions.php";
        
        // structure is nothing else the mere HTML code page_template
        $.post(url, {mID: pageTempalteID}, function(structure){
            if(afternoPageTemplate == 0){
                             
                                if(base.children(":first").is("*")){ 
                                            base.children(":first").before(createPageTemplate(structure, pageTempalteID));
                
                                }else{
                                           base.append(createPageTemplate(structure, pageTempalteID));
                }
            }else{
                
                $('.noPageTemplate').after('<div class="page_template ' + pageTempalteID + '" id="newPageTemplate">' + createPageTemplate(structure, pageTempalteID) + "</div>");
            }
    
            // upgrade to page_template preview
    
            
            // insert the page template
            $('#newPageTemplate').fadeIn(300);
    
            $('#content-text .page_template img').each(function(index) {
                if($(this).attr('src') == 'dnd_image'){
                                        // old <?php echo $templates.
                    $(this).attr('src',path + "/templates/system/page_template_image.png");
                    $(this).addClass("insert_image");
                }
            });
    
            $('#newPageTemplate').removeAttr('id');
        });
    }
    
    /********************************************
     * SAVE BUTTON PAGE TEMPLATE
     ********************************************/
     $('#savePageTemplate').live("click",function(event){
         event.preventDefault();
      
         var cid= $('#content_id').attr('value');
         var server=$('#server_url').attr('value');

         $('.pageTemplateContent').css('border','none');
         $('.removePageTemplateTopBar').css('display','none');
         $('.sortTools').css('visibility','hidden');

         saveChangeInContent(cid);

         $('.unsaved').css('display','none');
         $('#activate_page_template_bar').css('display','inline');
         $('#deactivate_page_template_bar').css('display','none');
         $('.boxTotal').css('display','none');
         $('#success').css('display','inline');

         // Page redirect
         setTimeout(function(){window.location = server; },150);
     });

    function createLabelSuccess()
    {
        label= '<link type="text/css" rel="stylesheet" href="'+ path + '/themes/default/form.css">';    
        label= label + '<div id="success" style="display:none; ">';
        label= label + '<label  class="success_label">Action completed successfully.</label>';
        label= label + '</div>';
        
        return label;
    }

    function saveChangeInContent(cid){
        var vcid        = cid;
        var vaction        = 'savePageTemplateContent';
        var vtext        = $('#content-text').html();
          
        var cont= $('#content-previous').html();
        
        if(cont!=null){
            $('#content-previous').append(vtext);
            vtext=cont+vtext;
        }
        else{
            //content-previous nn esiste
            $('#with-cont-pre').append(vtext);
        }

        if(vtext!=''){
            $('#with-cont').css('display','none');
            $('#with-cont-pre').css('display','inline');
            $('#no-cont').css('display','none');
            $('#no-cont-pre').css('display','none');
        }else{
            $('#with-cont').css('display','none');
            $('#no-cont-pre').css('display','inline');
            $('#no-cont').css('display','none');
        }
        $('#content-text').remove();

        var url =path+"/templates/system/AJAX_actions.php";
        $.post(url, {cid: vcid, text: vtext, action: vaction}, function(){}).complete();
    }
        
    function createPageTemplate(contenuto, pageTempalteID){

        page_template = '<table style="width:100%" class="page_template ' + pageTempalteID + '" id="newPageTemplate">';
            
                    page_template = page_template + '<tr><td>' + removePageTemplateTopBar;

                    page_template = page_template + '<tr><td class="pageTemplateContent">' + contenuto + '</tr></td>';

                    page_template = page_template + '<tr><td>' + sortTools + '</tr></td>';
                    
        page_template = page_template + '</table>';
           
                  return page_template;
    }
    
    function showPageTemplate() {

        // show the page_template options (delete, sort)
        $('.page_template').each(function(index) {
            // show the "X"
            $(this).find(' tr:first').before("<tr><td>" + removePageTemplateTopBar);

            // show the sorting bar
            $(this).append("<tr><td>" + sortTools);
        });
        
        return;


    }
    
    function hidePageTemplate(){

        $('.page_template').each(function(index) {
            // hide the "X"
            $(this).find(' tr:first').remove();

            // remove the sorting bar
            $(this).find(' tr:last').remove();
        });

        return;
    }

    function duplicatedTextFix(){

        // start from the first
        $('#content-text div[id*="_header_"]:first').each(function() {

        // first
        var element = $(this);

        // for every other element
        // check if it is unique respect to her children!
        while(element.next().is('*')){
            element.html(uniqChildren(element));
            element = element.next();
            }
        });

        return $('#content-text').html();
    }

    function uniqChildren(element){

        var c = new Array();
        var res;

        c.push(element.attr('id'));

        element.find('[id*="_header_"]').each(function() {

            // if it exists
            if($.inArray($(this).attr('id'), c) > -1){
                $(this).parent().html($(this).html());
                $(this).remove();
            }else{
                c.push($(this).attr('id'));
            }
            
            res = $(this).html();
        });
        return res;
    }
});