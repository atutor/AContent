<script type="text/javascript">

	// Apply a custom stylesheet to format "templates"
	$('head').append('<link rel="stylesheet" href="<?php echo $templates; ?>system/templates.css" type="text/css" />');
	
	// if the current content has a layout, just apply it!
        if("<?php echo $content_layout; ?>" != ''){
		
		//current_layout	= " <?php echo $layout_dir.$content_layout.'/'.$content_layout.'.css'; ?> ";
		//$('head').append('<link rel="stylesheet" href="' + current_layout + '" type="text/css" />');
	}

     

	var DEFAULT_SCREENSHOT	= '<?php echo $templates; ?>system/nolayout.png';

	var uniq		= 'content-text';

	var footer	= "";


	$(document).ready(function() {
		
		// this row allow to show the form just if JS is enabled
		// il selector depends by the module name (customizable in the language file)
		var module_name	= "<?php echo _AT('layout'); ?>";
		module_name		= module_name.replace(/ /g, '');
		
		// if the user is an authenticated author
		// show the module
		if("<?php echo $is_author; ?>" == 1 && "<?php echo basename($_SERVER['PHP_SELF']); ?>" == "content.php")
			$('#menu_' + module_name + ' form').show();
		else
			$('#menu_' + module_name).hide();
		
                var url		= "templates/system/AJAX_actions.php";


		// HIDE / SHOW "APPLY TO THE LESSON" BUTTON
		// ADMIN SECTION
		
		$("#apply_lesson_on").live("click", function() {
			// show the button
			$("#apply_layout_to_content").show();


			$.post(url, { dnd_request: "759e647ad85438ed2669dbabfb77a602"}, function(data) {
			});
		});
		$("#apply_lesson_off").live("click", function() {
			// hide the button
			$("#apply_layout_to_content").hide();

			$.post(url, { dnd_request: "c1388816ccd2cc64905595c526ca678b"}, function(data) {
			});
		});
		

		// SYSTEM OPTION

		var path	= "<?php echo htmlentities($_SERVER['PHP_SELF']); ?>";

		// if we are in 'system'
		if(path.indexOf('/system/') >= 0){
			
			var disabled	= '';
			var enabled		= '';

			if("<?php echo $apply_lesson_layout; ?>" == 0){
				disabled	= "checked=\"checked\"";
			}else
				enabled		= "checked=\"checked\"";

			$(".form-data tr:last").before("<tr><td colspan=\"2\"><fieldset class=\"templates_system_fieldset\"><legend><?php echo _AT('layout'); ?></legend>\
										<table><tr>\
										<td align=\"left\"><?php echo _AT('layout_content_apply'); ?></td>\
										<td align=\"left\">\
											<input type=\"radio\" name=\"apply_lesson\" id=\"apply_lesson_off\" " + disabled + " />\
											<label for=\"apply_lesson_on\"><?php echo _AT('disabled'); ?></label> \
											<input type=\"radio\" name=\"apply_lesson\" id=\"apply_lesson_on\" " + enabled + " />\
											<label for=\"apply_lesson_off\"><?php echo _AT('enabled'); ?></label>\
										</td>\
										</tr></table>\
									</fieldset></tr>");
		}
		
		/*
		 *	exaggeration
		 *	TinyMCE is not precise with the carriage return, then, I try to repair
		 *	the display differences between TinyMCE and AContent preview.
		 *
		 *	IN THIS CASE I JUST CORRECT THE SAVED CONTENT SHOWN
		 *	BY TINYMCE AND DISPLAYED BY ACONTENT
		*/
		if ($('#content-text').is('*')){
			text	= $('#content-text').html();

			text	= textFixJS(text);
			
			$('#content-text').html(text);
		}
		
		// set the default screenshot
		$('#layoutcreenshot').attr('src',DEFAULT_SCREENSHOT);
		
		/**
		 *	layout pewview management
		 *	show the layout when editing or in content preview
		 * 	reset the layout and redirect the user to the main layout
		 */

		$('#layout_list').change(function() {

			str = $(this).val();

			// remove the previous layout

			$('#templates_layout').remove();
			$('#templates_view').remove();
			
			// clean the preview to not leave stacks of previews
			$('#content-text').removeClass('view');

			switch(str){

				// remove the "LINK" tag related to the applied layout
				case '-':

					// if the current content has a layout, disable it (delete it)
					if("<?php echo $content_layout; ?>" != ''){
						current_layout	= "<?php echo $layout_dir.$content_layout.'/'.$content_layout.'.css'; ?>";
						$('head link').each(function(link) {
							if($(this).attr('href') == 'ViewMode')
								$(this).attr('href', current_layout);
						});
					}

					// set the screenshot
					$('#layoutcreenshot').attr('src', DEFAULT_SCREENSHOT);

					// remove the previous layout
					$('#templates_layout').remove();
					$('#templates_view').remove();

					// hide the preview
					$('#content-text').removeClass('view');

					// hide the preview when editing or in content preview
					if ($('form[name="form"]').is('*'))
						main = $('form[name="form"]');
					else if ($('#content-text').is('*'))
						main = $('#content-text');
					else
						main = null;

					if(main != null){

						main.show();
					}

					break;

				default:

					// if the current content has a layout, disable it (delete it)
					if("<?php echo $content_layout; ?>" != ''){
						current_layout	= "<?php echo $layout_dir.$content_layout.'/'.$content_layout.'.css'; ?>";
						$('head link').each(function(link) {
							if($(this).attr('href') == current_layout)
								$(this).attr('href', 'ViewMode');
						});

					}

					// set the screenshot, if it exists
					path_screenshot = '<?php echo $layout_dir; ?>' + str;
					image			= path_screenshot + '/screenshot.png';

					$.ajax({
						// check if it exists
					    url:	image,
					    esiste: false,
					    type:	'HEAD',
					    error:
					        function(){
					        	$('#layoutcreenshot').attr('src', DEFAULT_SCREENSHOT);
								return;
					        },
					    success:
					        function(){
					            $('#layoutcreenshot').attr('src', image);
					            return;
							}
					});

						// include the CSS that resets the default settings
						$('head').append('<link rel="stylesheet" href="<?php echo $templates; ?>system/layout.css" type="text/css" id="templates_layout" />');

						// include the choosen CSS
						$('head').append('<link rel="stylesheet" href="<?php echo $layout_dir; ?>' + str + '/' + str + '.css" type="text/css" id="templates_view" />');
						var c = '<div id="content">' + $('#content-text').html() + '</div>';
						$('#content-text').html(c);

						// can show the preview during editing or during the content preview
						// to do this I have to check the FORM name = "form" existence or the ID #content-text existence (during the content preview)

						// editing preview
						if ($('form[name="form"]').is('*')){
							// take the content
							text = $('#body_text').text();

							// hide the current page content
							main = $('form[name="form"]');
						}
						// preview during the content preview
						else if ($('#content-text').is('*')){

							// take the content
							text = $('#content-text').html();

							// hide the current page content
							main = $('#content-text');
						}else
							main = null;

						if(main != null){

							var formatType = '<?php echo $formatContent; ?>';

							// if the text is displayed in Plain Text
							// the preview will have to comply with the display request
							// no problems for HTML and Web Link
							if(formatType == 0)
								text = jQuery(text).text();
							else{
								// clean up the text for the preview (TinyMCE has problems with carriage return)
								//text	= jQuery(text).text();
								text	= textFixJS(text);
							}

							//main.after('<div id="view"><div id="' + uniq + '">' + text + '</div>');
							//main.after('<div id="view">' + text);
							$('#content-text').addClass('view');
							
					} // IF -> if the layout is not already applied, it will do it!
			} // switch
		});
		
		/*
		 *	exaggeration
		 *	TinyMCE is not precise with the carriage return, then, I try to repair
		 *	the display differences between TinyMCE and AContent preview.
		 *	text	= text to clean up
		*/
		function textFixJS(text){
			text	= text.replace(/<p>&nbsp;<\/p>/g, "<br />");
			text	= text.replace(/<p><\/p>/g, "<br />");
			text	= text.replace(/<br>/g, "<br />");
			text	= text.replace(/<p>/g, "<div>");
			text	= text.replace(/<\/p>/g, "</div>");

			return text;
		}
    
    
// ceppini matteo
// Code to handle the layout preview
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

    var url ="/AContent/templates/system/AJAX_actions.php";

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
        layout_template= layout_template + '<link rel="stylesheet" href="/AContent/templates/layout/'+layout+'/'+layout+'.css" type="text/css" />';
        layout_template= layout_template + '<p>'+structure+'</p>';
    }else{
        layout_template= layout_template + '<p>Content devoid of text, below is an example with default text.</p>';
        layout_template= layout_template + 'Preview ' + layout + ':';
        layout_template= layout_template + '<link rel="stylesheet" href="/AContent/templates/layout/'+layout+'/'+layout+'.css" type="text/css" />';
        layout_template= layout_template + '<div id="content"><h1>Title</h1><p>Body of the document</p></div>';
    }   
    layout_template =layout_template + '</div>';
    
    return layout_template;
}
		
	});

</script>