<script type="text/javascript">

	var removeModelTopBar	= '<div class="removeModelTopBar"><div class="removeModel">X</div></div>';
	var sortTools			= '<div class="sortTools">\
								<img src="<?php echo $dnd_themod; ?>system/top.png" class="moveModelTop" alt="move top" />\
								<img src="<?php echo $dnd_themod; ?>system/up.png" class="moveModelUp" alt="move up" />\
								<img src="<?php echo $dnd_themod; ?>system/down.png" class="moveModelDown" alt="move down" />\
								<img src="<?php echo $dnd_themod; ?>system/bottom.png" class="moveModelBottom" alt="move bottom" />\
								</div>';

	$(document).ready(function(){
	//document.onload = function() {
		// questa riga ci consente di mostrare il form solo se JS e' abilitato
		// il selettore dipende dal nome del modulo (che, nel file della lingua, e' personalizzabile)
		var titolo_modulo	= "<?php echo _AT('models'); ?>";
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

		/*
		if($('#view').is('*'))
			base			= $('#view');
		else
		*/
			base			= $('#content-text');

		////////////////////////////////////////
		//	INCLUSIONI / DICHIARAZIONI / DEFINIZIONI
		////////////////////////////////////////

		var boxModel			= '<div class="boxModel"><ul></ul></div>';

		// considero i modelli solo in fase di anteprima dei contenuti

		// determino se sono in fase di modifica o in fase di anteprima del contenuto
		// TEXTAREA
		/*
		if($('#body_text').is('*')){
			// fase di modfica
			base		= $('#body_text');
		}else{
			// anteprima del contenuto
			base		= $('#content-text');
		}

		base			= $('#content-text');
		*/

		// inserisco in testa al contenuto testuale il box relativo ai modelli
		boxModelToolbox = "<div class=\"boxModelToolbox\"><ul>";

		// paste
		boxModelToolbox = boxModelToolbox + "<li id=\"modelPaste\"><img src=\"<?php echo $dnd_themod; ?>system/paste.png\" title=\"<?php echo _AT('paste'); ?>\" alt=\"\" /> <?php echo _AT('paste_model_sequence'); ?></li>";
		
		// copy
		boxModelToolbox	= boxModelToolbox + "<li id=\"modelCopy\"><img src=\"<?php echo $dnd_themod; ?>system/copy.png\" title=\"<?php echo _AT('copy'); ?>\" alt=\"\" /> <?php echo _AT('copy_model_sequence'); ?></li>";
		
		boxModelToolbox = boxModelToolbox + "</ul></div>";


		////////////////////////////////////////
		//	EVENTO ATTIVAZIONE / DISATTIVAZIONE MODELLI
		////////////////////////////////////////

		$('#modelCopy').live("click", function(){

			var allModels	= '';

			$('.model').each(function(index) {
				allModels = allModels + "|" + $(this).attr('class');
			});

			var c_name		= 'modelClipboard';
			var value		= allModels;
			var exdays		= '1';

			// creo il cookie
			var exdate		= new Date();
			exdate.setDate(exdate.getDate() + exdays);
			var c_value		= escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
			document.cookie	= c_name + "=" + c_value;

			$('#modelCopy').css('background','#f0f8ff');

			$('#modelPaste').css('display','inline');

		});

		$('#modelPaste').live("click", function(){

			var c_name		= 'modelClipboard';
	
			// leggo il cookie
			var i,x,y,ARRcookies=document.cookie.split(";");
			for (i=0;i<ARRcookies.length;i++){
				x	= ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
				y	= ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
				x	= x.replace(/^\s+|\s+$/g,"");
				if (x==c_name){
					if(unescape(y) == '')
						alert("<?php echo _AT('no_set_copied'); ?>");
					else
						var Models = unescape(y);
				}
			}

			// se ci sono gia' altri modelli
			// chiedo se si vuole aggiungere la clipboard in testa
			if($('.model').attr('class') != 'model noModel'){
				if(!confirm("<?php echo _AT('add_to_existing_models'); ?>") ) {
            		return false;
		  		}
		  	}

			// aggiungi modelli
			//$('#dnd').html(Models + $('#dnd').html());
			var m = Models.split('|');

			var dopoNoModel = 0;
			// il ciclo parte da 1 in quanto il primo elemento è ''
			for(i=1; i<m.length; i++){
				var modelID = m[i].replace("model ", "");
				
				if(modelID == 'noModel')
					dopoNoModel = 1;
				else
					aggiungiModello(modelID, dopoNoModel);
			}
			
			// salva nuovo contenuto
			salvaModificheContenuto();

		});

		
		$('#attivaModelli_btn').bind("pageTemplateStruct", function(event) {
			  alert("cippa2");
			  //if($('#attivaModelli_btn').is(':checked')) {
					
					// disabilito ORDINA MODELLI				
					$('#ordinaModelli_btn').attr('disabled','disabled');

					$('head').append('<link rel="stylesheet" href="<?php echo $dnd_themod; ?>system/models.css" type="text/css" />');

					// cut and paste toolBar
					base.before(boxModel + boxModelToolbox);

					// CUT & PASTE

					// cookie name
					var c_name		= 'modelClipboard';

					// leggo il cookie
					var i,x,y,ARRcookies=document.cookie.split(";");
					for (i=0;i<ARRcookies.length;i++){
						x	= ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
						y	= ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
						x	= x.replace(/^\s+|\s+$/g,"");
						if (x==c_name){
							if(unescape(y) != '')
								$('#modelPaste').css('display','inline');
						}
					}
		
					// riempo il box con i modelli disponibili solo nel caso
					// si scelga di visualizzare i modelli
					var m = '';

					var count = 0;
					<?php
						
						foreach($listaModelli as $key => $value) {
							echo 'count++;';
							echo '$(".boxModel").append($("<li>"));';
							echo 'm = m + "<li><table id=\"'.$key.'\"><tr><td><img src=\"'.$dnd_themod.'/models/'.$key.'/screenshot.png\" /></td></tr><td class=\"desc\">'.$value['name'].'</td></tr></table></li>";';
						}
					?>

					alert("change "+ count);
					$(".boxModel ul").append(m);

					// mostro il box dei modelli
					$('.boxModel').slideToggle('slow', function(){
						$(this).css('display','block');
					});

					visualizzaModelli();

				
		});

		
		
		
		
		$('#attivaModelli_btn').change(function (event) {
			alert("caccA ");
			if($('#attivaModelli_btn').is(':checked')) {
				
				// disabilito ORDINA MODELLI				
				$('#ordinaModelli_btn').attr('disabled','disabled');

				$('head').append('<link rel="stylesheet" href="<?php echo $dnd_themod; ?>system/models.css" type="text/css" />');

				// cut and paste toolBar
				base.before(boxModel + boxModelToolbox);

				// CUT & PASTE

				// cookie name
				var c_name		= 'modelClipboard';

				// leggo il cookie
				var i,x,y,ARRcookies=document.cookie.split(";");
				for (i=0;i<ARRcookies.length;i++){
					x	= ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
					y	= ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
					x	= x.replace(/^\s+|\s+$/g,"");
					if (x==c_name){
						if(unescape(y) != '')
							$('#modelPaste').css('display','inline');
					}
				}
	
				// riempo il box con i modelli disponibili solo nel caso
				// si scelga di visualizzare i modelli
				var m = '';

				var count = 0;
				<?php
					
					foreach($listaModelli as $key => $value) {
						echo 'count++;';
						echo '$(".boxModel").append($("<li>"));';
						echo 'm = m + "<li><table id=\"'.$key.'\"><tr><td><img src=\"'.$dnd_themod.'/models/'.$key.'/screenshot.png\" /></td></tr><td class=\"desc\">'.$value['name'].'</td></tr></table></li>";';
					}
				?>

				alert("change "+ count);
				$(".boxModel ul").append(m);

				// mostro il box dei modelli
				$('.boxModel').slideToggle('slow', function(){
					$(this).css('display','block');
				});

				visualizzaModelli();

			}else{
				
				// disabilito temporaneamente il pulsante modelli
				$('#attivaModelli_btn').attr("disabled", "disabled");

				nascondiModelli();

				// mostro il box dei modelli

				$('.boxModel').slideToggle('slow', function(){

					// rimuovo il box del cut & paste dal DOM
					$('.boxModelToolbox').remove();
					// rimuovo il boxModel dal DOM
					$('.boxModel').remove();

					// salvo il documento
					salvaModificheContenuto();

					// rimuovo il foglio di stile
					var modelStylesheet	= $('link[href="<?php echo $dnd_themod; ?>system/models.css"]');
					modelStylesheet.remove();

				});
			}
		});


		////////////////////////////////////////
		//	ARRANGE MODELS BUTTON
		////////////////////////////////////////

		$('#ordinaModelli_btn').click(function(){
			if($('#ordinaModelli_btn').is(':checked')){

				// disabilito ATTIVA MODELLI
				$('#attivaModelli_btn').attr('disabled', 'disabled');

				$('.noModel').addClass('noModelSorting');
				
				$('head').append('<link rel="stylesheet" href="<?php echo $dnd_themod; ?>system/models.css" type="text/css" />');

				visualizzaModelli();
				
			}else{
				
				nascondiModelli();

				//$('.boxModelToolbox').hide();
				
				// disabilito ATTIVA MODELLI
				$('#ordinaModelli_btn').attr('disabled', 'disabled');

				$('.noModel').removeClass('noModelSorting');

				// mostro il box dei modelli
				
				// rimuovo il boxModel dal DOM
				$('.boxModel').remove();

				// salvo il documento
				/*
				var url			= "<?php echo $dnd_themod; ?>" + "system/AJAX_actions.php";
				var vcid		= "<?php echo $cid; ?>";
				var vaction		= 'saveModelContent';
				//var vtext		= base.html();
				var vtext		= duplicatedTextFix();

				$.post(url, {cid: vcid, text: vtext, action: vaction}, function(data){

					// riabilito il pulsante modelli
					$('#attivaModelli_btn').removeAttr("disabled");
					$('#ordinaModelli_btn').removeAttr('disabled');

				});
				*/
				salvaModificheContenuto();

				// rimuovo il foglio di stile
				var modelStylesheet	= $('link[href="<?php echo $dnd_themod; ?>system/models.css"]');
				modelStylesheet.remove();
			}
		});


		////////////////////////////////////////
		//	ORDINAMENTO MODELLI
		////////////////////////////////////////

		// top

		$('.moveModelTop').live("click", function(){

			// this model
			var model = $(this).parents('.model');

			base.prepend(model);

		});

		// up

		$('.moveModelUp').live("click", function(){

			// this model
			var model = $(this).parents('.model');

			if(model.prev().attr('class') != undefined){
			
				var parent = model.prev();
				parent.before(model);
			}else{
				base.prepend(model);
			}

		});

		// down

		$('.moveModelDown').live("click", function(){

			// this model
			var model = $(this).parents('.model');

			//model.next('.model').css('background', 'red');
			//alert(model.next().attr('class'));
			//model.css('background', 'red');

			if(model.next().attr('class') != undefined){
			
				var child = model.next();
				child.after(model);
			}else
			{
				base.append(model);
			}

		});

		// bottom

		$('.moveModelBottom').live("click", function(){

			// this model
			var model = $(this).parents('.model');

			base.append(model);
		});

		////////////////////////////////////////
		//	AGGIUNGO UN NUOVO MODELLO
		////////////////////////////////////////

		$('.boxModel li').live("click", function(){

			var structure	= "";

			// prendo il nome del modello che si desidera inserire
			var modelID		= $(this).find('table').attr('id');
			
			// aggiungi modello
			aggiungiModello(modelID, 0);

		});

		////////////////////////////////////////
		//	ELIMINO IL MODELLO SELEZIONATO
		////////////////////////////////////////

		$('.removeModel').live("click", function(){


			// effetto slideUp
			/*
			$(this).parent().parent().slideUp(300,function(){
				$(this).remove();
			});
			*/
			var modello	= $(this).parents('.model');

			// effetto fade

			modello.fadeOut(300, function(){
				modello.remove();
			});

		});


		$("#body_text_ifr").live("mouseover", function(){
			//var a = $('#body_text').contents().text();
			//a = a.find('plain_text').css('color','gold');
			//alert(a.html());
			//var a = tinyMCE.activeEditor.getContent();
			//var a = tinyMCE.activeEditor.getContent({format : 'raw'});
			var oldContent	= tinyMCE.activeEditor.getContent();
			
			//newContent = oldContent.replace('a','@');
			
			var newContent;
			
			//$('.page-title').html(a);
			//alert(a);
			
			tinyMCE.activeEditor.setContent(newContent);
			//var a = tinyMCE.get('.modelContent').getContent();

			//$('.page-title').html(a);
			/*
			var a = tinyMCE.activeEditor.getContent({format : 'raw'});

			a.find(".modelContent").live("mouseover", function(){
				$(this).css('background','red');
			});
			*/

		});
		/*
		$("#body_text_ifr").contents().find(".modelContent").live("mouseover", function(){
			alert('aaaaaaa');
		});*/


		/*
		*	Correggo un fastidiosissimo bug di progettazione dei browser:
		* 	quando scorro verticalmente i contenuti di un div (in questo caso dei modelli)
		* 	e arrivo in fondo, il focus viene automaticamente preso dalla pagina che scorre
		* 	fastidiosamente in basso. 
		*/
		$(".boxModel").live({

			mouseover: function() {
    			$('body').css('overflow','hidden');
    			$('body').css('padding-right','15px');
    			//$('body').css('display','block');
  			},
  			mouseout: function() {
    			$('body').css('overflow','auto');
    			$('body').css('padding-right','0px');
  			}
		});


		/*######################################
			FUNZIONI
		######################################*/
		
		function aggiungiModello(modelID, afterNoModel){

			var url			= "<?php echo $dnd_themod; ?>" + "system/AJAX_actions.php";

			// structure non e' altro che il mero codice HTML del modello
			$.post(url, {mID: modelID}, function(structure){

				if(afterNoModel == 0){
					//$('.model:first').before('<div class="model ' + modelID + '" id="newModel">' + creaModello(structure) + "</div>");
					//$('.model:first').before('<table class="model ' + modelID + '" id="newModel"><tr><td>' + creaModello(structure) + "</td></tr></table>");
					if(base.children(":first").is("*")){
						base.children(":first").before(creaModello(structure, modelID));
					}else{
						base.append(creaModello(structure, modelID));
					}
				}else{
					$('.noModel').after('<div class="model ' + modelID + '" id="newModel">' + creaModello(structure, modelID) + "</div>");
				}

				// aggiorno la visualizzazione dell'anteprima temi
/*
				if($('.view').is('*')){

					$('#newModel').fadeIn(1, function(){
						var text = $('#content-text').html();

						// aggiungo anche il tag "dnd" che, dal testo orignale di "content-text" non esiste						
						$('.view').html("<div id=\"dnd\">" + text + "</div>");
					});

				}else{
					*/
					// inserisco il modello
					$('#newModel').fadeIn(300);
				//}

				// sostituisco l'immagine del modello nell'immagine di default

				$('#content-text .model img').each(function(index) {
					if($(this).attr('src') == 'dnd_image'){
						$(this).attr('src', "<?php echo $dnd_themod.'system/model_image.png'; ?>");
						$(this).addClass("insert_image");
					}
				});

				$('#newModel').removeAttr('id');
			});
		}
		
		function salvaModificheContenuto(){
			var url			= "<?php echo $dnd_themod; ?>" + "system/AJAX_actions.php";
			var vcid		= "<?php echo $cid; ?>";
			var vaction		= 'saveModelContent';
			//var vtext		= duplicatedTextFix();
			
			var vtext		= $('#content-text').html();

			$.post(url, {cid: vcid, text: vtext, action: vaction}, function(data){

				// riabilito il pulsante modelli
				$('#attivaModelli_btn').removeAttr("disabled");
				$('#ordinaModelli_btn').removeAttr('disabled');

			});
		}
		
		function creaModello(contenuto, modelID){

			modello = '<table style="width:100%" class="model ' + modelID + '" id="newModel">';
				//modello = modello + '<tr><td>' + removeModelTopBar + '</tr></td>';
				modello = modello + '<tr><td>' + removeModelTopBar;

				modello = modello + '<tr><td class="modelContent">' + contenuto + '</tr></td>';

			 	modello = modello + '<tr><td>' + sortTools + '</tr></td>';
			modello = modello + '</table>';

			return modello;
		}
		
		function visualizzaModelli() {

			// mostro le opzioni dei modelli (elimina, ordina)
			$('.model').each(function(index) {
				// mostro la X di eliminazione del modello
				$(this).find(' tr:first').before("<tr><td>" + removeModelTopBar);

				// mostro la barra di ordinamento
				$(this).append("<tr><td>" + sortTools);
			});
			
			// incapsulo il contenuto esistente in un "noModel"
			//base..css('background','lightgreen');

			return;

			// se e' impostato un tema (quindi esiste un ID "dnd")
			/*
			if($('#content-text #dnd').is('*')){
				var dad	= $('#dnd');
			}else{
				var dad	= $('#content-text');
			}

			// per fare in modo che i modelli assumano il tema assegnato alla lezione
			// devono esserre contenuti nel div "#dnd".
			// per differenziare i modelli dal "vecchio contenuto" ho inserito tutto dentro content-text;
			// i "vecchi contenuti" sono dentro oldContent e i nuovi modelli sono dentro newModel ma tutti dentro #dnd

			// se noModel (il "vecchio contenuto") non e' presente, lo aggiungo
			if(!$('.noModel').is('*')){
				dad.html('<div class="model noModel">' + dad.html());
				//creaModello(dad.html()));
			}else{
				$('.noModel').addClass('model');
			}

			// rendo visibili tutti i modelli personalizzati
			// per ogni tag
			$('.customModel').each(function(index) {
				$(this).removeClass('customModel');
				$(this).addClass('model');
			});

			// mostro la X di eliminazione del modello
			//$('.modelContent').before(removeModelTopBar);
			$('.modelContent').before(removeModelTopBar);
			//$('.removeModelTopBar').show();
			
			//$('.modelContent').append(sortTools);
			$('.model').append(sortTools);
			*/
		}
		
		function nascondiModelli(){

			$('.model').each(function(index) {
				// nascondo la X di eliminazione del modello
				$(this).find(' tr:first').remove();

				// rimuovo la barra di ordinamento
				$(this).find(' tr:last').remove();
			});

			return;
		}

		function duplicatedTextFix(){

			// parto dal primo
			$('#content-text div[id*="_header_"]:first').each(function() {

				// primo
				var element = $(this);
	
				// per ogni altro elemento
				// verifico sia unique rispetto ai suoi figli!
				//while(element.next().is('*')){
					while(element.next().is('*')){
					//alert("contenuto iniziale: " + element.html());

					element.html(uniqChildren(element));

					//alert("contenuto finale: " + $(this).html());

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

				// se esiste
				if($.inArray($(this).attr('id'), c) > -1){
					//alert('duplicato: ' + $(this).attr('id'));
					//alert($(this).html());
					$(this).parent().html($(this).html());
					$(this).remove();
					//alert($(this).html());
					//$(this).css('border-left','20px solid blue');
				}else{
					//alert('originale: ' + $(this).attr('id'));
					c.push($(this).attr('id'));
					//$(this).css('color','black');
				}
				
				res = $(this).html();
			});
//alert(res);
			return res;
		}

		// scrivo nella textarea
		//$('form[name="form"] textarea').html('<div style="background:red">Mauro</div>');

		//$('mceContentBody iframe').contents().append('<div style="background:red">Mauro</div>');

		/*
		 *	Aggiungo gli estremi
		 */
		//$('#content-text').append('<div class="bottom-stripe"></div>');
		//$('#content-text').prepend('<div id=".boxModel"></div>');
		/*
		$('#bottom-stripe').live("hover", function(){
			
			$('#bottom-stripe').css('height','100');
			$('#bottom-stripe').css('background','#EEE');
		});
		*/

/*
		$(".noModel").sortable({
			placeholder: "ui-state-highlight";
		});
		$(".noModel").disableSelection();
*/
	});
</script>