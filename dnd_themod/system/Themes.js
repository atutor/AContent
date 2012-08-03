<script type="text/javascript">

	// applico un mio foglio di stile per formattare dnd_themod
	$('head').append('<link rel="stylesheet" href="<?php echo $dnd_themod; ?>system/dnd_themod.css" type="text/css" />');
	
	// se il contenuto attuale ha un tema, lo applico

	if("<?php echo $content_theme; ?>" != ''){
		
		current_theme	= "<?php echo $themes_dir.$content_theme.'/'.$content_theme.'.css'; ?>";
		$('head').append('<link rel="stylesheet" href="' + current_theme + '" type="text/css" />');
	}


	var DEFAULT_SCREENSHOT	= '<?php echo $dnd_themod; ?>system/noTheme.png';
	// creo un valore univoco da assegnare come classe generale
	// do not change (fixed for "Models" too)
	//var uniq		= 'dnd';
	var uniq		= 'content-text';
	// creo un eventuale footer
	//var footer	= "<div id=\"anteprima-footer\"> </div>";
	var footer	= "";


	$(document).ready(function() {
		
		// questa riga ci consente di mostrare il form solo se JS e' abilitato
		// il selettore dipende dal nome del modulo (che, nel file della lingua, e' personalizzabile)
		var titolo_modulo	= "<?php echo _AT('themes'); ?>";
		titolo_modulo		= titolo_modulo.replace(/ /g, '');
		
		// se l'utente e' un autore autenticato
		// mostro il modulo
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

		// se siamo in 'system'
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
		 *	TinyMCE non è preciso con i carriage return, quindi, cerco di riparare
		 *	alle differenze di visualizzazione tra TinyMCE e l'anteprima di AContent.
		 *
		 *	IN QUESTO CASO CORREGGO SOLAMENTE LA VISUALIZZAZIONE DEL CONTENUTO SALVATO
		 *	DA TINYMCE E VISUALIZZATO DA ACONTENT
		*/
		if ($('#content-text').is('*')){
			text	= $('#content-text').html();

			text	= textFixJS(text);
			
			$('#content-text').html(text);
		}
		
		// imposto la screenshot per il tema di default
		$('#themeScreenshot').attr('src',DEFAULT_SCREENSHOT);
		
		/**
		 *	Gestione dell'anteprima dei temi 
		 *	visualizzo il tema scelto in fase di editing o di anteprima del contenuto
		 * 	resetto il tema e riporto l'utente al tema principale
		 */

		$('#listatemi').change(function() {

			str = $(this).val();

			// rimuovo il tema precedente

			$('#dnd_themod_themes').remove();
			$('#dnd_themod_view').remove();
			/////////////////////////////
			
			// pulisco l'anteprima per non lasciare accodamenti di anteprime
			//$("#view").remove();
			$('#content-text').removeClass('view');

			//*****************************************************************
			//$('#content-text').attr('name', uniq);

/*
			// se il contenuto attuale ha un tema, lo disabilito (lo elimino)
			if("<?php echo $content_theme; ?>" != ''){
				current_theme	= "<?php echo $themes_dir.$content_theme.'/'.$content_theme.'.css'; ?>";
				$('head link').each(function(link) {
					if($(this).attr('href') == current_theme)
						$(this).attr('href', 'ViewMode');
				});
			}
			*/

			switch(str){

				// rimuovo il tag "LINK" relativo al tema applicato
				case '-':

					// se il contenuto attuale ha un tema, lo disabilito (lo elimino)
					if("<?php echo $content_theme; ?>" != ''){
						current_theme	= "<?php echo $themes_dir.$content_theme.'/'.$content_theme.'.css'; ?>";
						$('head link').each(function(link) {
							if($(this).attr('href') == 'ViewMode')
								$(this).attr('href', current_theme);
						});
					}

					// imposto la screenshot
					$('#themeScreenshot').attr('src', DEFAULT_SCREENSHOT);

					// rimuovo il tema precedente
					$('#dnd_themod_themes').remove();
					$('#dnd_themod_view').remove();

					// nascondo l'anteprima
					//$("#view").hide();
					$('#content-text').removeClass('view');

					// nascondo l'anteprima in fase di editing o in fase di anteprima del contenuto
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

					// se il contenuto attuale ha un tema, lo disabilito (lo elimino)
					if("<?php echo $content_theme; ?>" != ''){
						current_theme	= "<?php echo $themes_dir.$content_theme.'/'.$content_theme.'.css'; ?>";
						$('head link').each(function(link) {
							if($(this).attr('href') == current_theme)
								$(this).attr('href', 'ViewMode');
						});
						
						//alert('rimosso tema ' + current_theme);
					}

					// imposto la screenshot, SE ESISTE
					percorso_screenshot = '<?php echo $themes_dir; ?>' + str;
					immagine			= percorso_screenshot + '/screenshot.png';

					$.ajax({
						// controllo se esiste
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

					// se il tema non è già applicato, lo applico
					//if(!esiste){
						
						// includo il CSS che resetta le impostazioni di default
						$('head').append('<link rel="stylesheet" href="<?php echo $dnd_themod; ?>system/themes.css" type="text/css" id="dnd_themod_themes" />');

						// includo il CSS desiderato
						$('head').append('<link rel="stylesheet" href="<?php echo $themes_dir; ?>' + str + '/' + str + '.css" type="text/css" id="dnd_themod_view" />');
						var c = '<div id="content">' + $('#content-text').html() + '</div>';
						$('#content-text').html(c);

						// posso visualizzare l'anteprima in fase di editing o in fase di anteprima del contenuto
						// per fare questa distinzione devo verificare l'esistenza del FORM name = "form" o dell'ID #content-text (fase di anteprima del contenuto)

						// anteprima in fase di editing
						if ($('form[name="form"]').is('*')){
							// prendo il contenuto
							text = $('#body_text').text();

							// nascondo il contenuto della pagina attuale
							main = $('form[name="form"]');
						}
						// anteprima in fase di anteprima del contenuto
						else if ($('#content-text').is('*')){

							// prendo il contenuto
							text = $('#content-text').html();

							// nascondo il contenuto della pagina attuale
							main = $('#content-text');
						}else
							main = null;

						if(main != null){

							var formatType = '<?php echo $formatContent; ?>';

							// se il testo viene visualizzato in Plain Text
							// l'anteprima dovra rispettare la richiesta di visualizzazione
							// per l'HTML e il Web Link non ci sono problemi
							if(formatType == 0)
								text = jQuery(text).text();
							else{
								// pulisco il testo per l'anteprima (TinyMCE ha problemi con i carriage return)
								//text	= jQuery(text).text();
								text	= textFixJS(text);
							}

							//main.after('<div id="view"><div id="' + uniq + '">' + text + '</div>');
							//main.after('<div id="view">' + text);
							$('#content-text').addClass('view');
							
							//main.hide();

							// mostro la pagina di anteprima
							//$("#view").show();
						//}

					} // IF -> se il tema non è già applicato, lo applico
			} // switch
		});
		
		/*
		 *	exaggeration
		 *	TinyMCE non è preciso con i carriage return, quindi, cerco di riparare
		 *	alle differenze di visualizzazione tra TinyMCE e l'anteprima di AContent.
		 *	text	= testo da ripulire
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