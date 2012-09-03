<script type="text/javascript">

	// Apply a custom stylesheet to format "dnd_themod"
	$('head').append('<link rel="stylesheet" href="<?php echo $dnd_themod; ?>system/dnd_themod.css" type="text/css" />');
	
	// if the current content has a theme, just apply it!

	if("<?php echo $content_theme; ?>" != ''){
		
		current_theme	= "<?php echo $themes_dir.$content_theme.'/'.$content_theme.'.css'; ?>";
		$('head').append('<link rel="stylesheet" href="' + current_theme + '" type="text/css" />');
	}


	var DEFAULT_SCREENSHOT	= '<?php echo $dnd_themod; ?>system/noTheme.png';

	var uniq		= 'content-text';

	var footer	= "";


	$(document).ready(function() {
		
		// this row allow to show the form just if JS is enabled
		// il selector depends by the module name (customizable in the language file)
		var titolo_modulo	= "<?php echo _AT('themes'); ?>";
		titolo_modulo		= titolo_modulo.replace(/ /g, '');
		
		// if the user is an authenticated author
		// show the module
		if("<?php echo $is_author; ?>" == 1 && "<?php echo basename($_SERVER['PHP_SELF']); ?>" == "content.php"){
			$('#menu_' + titolo_modulo + ' form').show();
		}else{
			$('#menu_' + titolo_modulo).hide();
			//$('#menu_' + titolo_modulo).prev().hide();
			//$('#menu_' + titolo_modulo).siblings('br').slice(-1).remove();
		}


		var url		= "dnd_themod/system/AJAX_actions.php";


		// HIDE / SHOW "APPLY TO THE LESSON" BUTTON
		// ADMIN SECTION
		
		$("#apply_lesson_on").live("click", function() {
			// show the button
			$("#applicaTemaLezione_btn").show();


			$.post(url, { dnd_request: "759e647ad85438ed2669dbabfb77a602"}, function(data) {
			});
		});
		$("#apply_lesson_off").live("click", function() {
			// hide the button
			$("#applicaTemaLezione_btn").hide();

			$.post(url, { dnd_request: "c1388816ccd2cc64905595c526ca678b"}, function(data) {
			});
		});
		

		// SYSTEM OPTION

		var path	= "<?php echo htmlentities($_SERVER['PHP_SELF']); ?>";

		// if we are in 'system'
		if(path.indexOf('/system/') >= 0){
			
			var disabled	= '';
			var enabled		= '';

			if("<?php echo $apply_lesson_theme; ?>" == 0){
				disabled	= "checked=\"checked\"";
			}else
				enabled		= "checked=\"checked\"";

			$(".form-data tr:last").before("<tr><td colspan=\"2\"><fieldset class=\"dnd_themod_system_fieldset\"><legend><?php echo _AT('themes'); ?></legend>\
										<table><tr>\
										<td align=\"left\"><?php echo _AT('theme_lesson_apply'); ?></td>\
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
		$('#themeScreenshot').attr('src',DEFAULT_SCREENSHOT);
		
		/**
		 *	Themes pewview management
		 *	show the theme when editing or in content preview
		 * 	reset the theme and redirect the user to the main theme
		 */

		$('#listatemi').change(function() {

			str = $(this).val();

			// remove the previous theme

			$('#dnd_themod_themes').remove();
			$('#dnd_themod_view').remove();
			/////////////////////////////
			
			// clean the preview to not leave stacks of previews
			$('#content-text').removeClass('view');

			switch(str){

				// remove the "LINK" tag related to the applied theme
				case '-':

					// if the current content has a theme, disable it (delete it)
					if("<?php echo $content_theme; ?>" != ''){
						current_theme	= "<?php echo $themes_dir.$content_theme.'/'.$content_theme.'.css'; ?>";
						$('head link').each(function(link) {
							if($(this).attr('href') == 'ViewMode')
								$(this).attr('href', current_theme);
						});
					}

					// set the screenshot
					$('#themeScreenshot').attr('src', DEFAULT_SCREENSHOT);

					// remove the previous theme
					$('#dnd_themod_themes').remove();
					$('#dnd_themod_view').remove();

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

					// if the current content has a theme, disable it (delete it)
					if("<?php echo $content_theme; ?>" != ''){
						current_theme	= "<?php echo $themes_dir.$content_theme.'/'.$content_theme.'.css'; ?>";
						$('head link').each(function(link) {
							if($(this).attr('href') == current_theme)
								$(this).attr('href', 'ViewMode');
						});

					}

					// set the screenshot, if it exists
					percorso_screenshot = '<?php echo $themes_dir; ?>' + str;
					immagine			= percorso_screenshot + '/screenshot.png';

					$.ajax({
						// check if it exists
					    url:	immagine,
					    esiste: false,
					    type:	'HEAD',
					    error:
					        function(){
					        	$('#themeScreenshot').attr('src', DEFAULT_SCREENSHOT);
								return;
					        },
					    success:
					        function(){
					            $('#themeScreenshot').attr('src', immagine);
					            return;
							}
					});

						// include the CSS that resets the default settings
						$('head').append('<link rel="stylesheet" href="<?php echo $dnd_themod; ?>system/themes.css" type="text/css" id="dnd_themod_themes" />');

						// include the choosen CSS
						$('head').append('<link rel="stylesheet" href="<?php echo $themes_dir; ?>' + str + '/' + str + '.css" type="text/css" id="dnd_themod_view" />');
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
							
					} // IF -> if the theme is not already applied, it will do it!
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
		
	});

</script>