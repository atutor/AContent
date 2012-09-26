<script type="text/javascript">

	var removeModelTopBar	= '<div class="removeModelTopBar"><div class="removeModel">X</div></div>';
	var sortTools			= '<div class="sortTools">\
								<img src="<?php echo $templates; ?>system/top.png" class="moveModelTop" alt="move top" />\
								<img src="<?php echo $templates; ?>system/up.png" class="moveModelUp" alt="move up" />\
								<img src="<?php echo $templates; ?>system/down.png" class="moveModelDown" alt="move down" />\
								<img src="<?php echo $templates; ?>system/bottom.png" class="moveModelBottom" alt="move bottom" />\
								</div>';

	$(document).ready(function(){ 
		
		// this row allow to show the form just if JS is enabled
		// il selector depends by the module name (customizable in the language file)
		var titolo_modulo	= "<?php echo _AT('models'); ?>";
		titolo_modulo		= titolo_modulo.replace(/ /g, '');
		
		// if the user is an authenticated author
		// show the module
		
		if("<?php echo $is_author; ?>" == 1 && "<?php echo basename($_SERVER['PHP_SELF']); ?>" == "content.php")
			//$('#dnd_moduli').show();
			$('#menu_' + titolo_modulo + ' form').show();
		else
			//$('#dnd_moduli').hide();
			$('#menu_' + titolo_modulo + ' form').hide();
		

		/*
		if($('#view').is('*'))
			base			= $('#view');
		else
		*/
			base			= $('#content-text');

		////////////////////////////////////////
		//	INCLUSIONS / DECLARATIONS / DEFINITIONS
		////////////////////////////////////////

		var boxModel			= '<div class="boxModel"><ul></ul></div>';

		// consider only models during the content preview

		// put on the content top the model box
		boxModelToolbox = "<div class=\"boxModelToolbox\"><ul>";

		// paste
		boxModelToolbox = boxModelToolbox + "<li id=\"modelPaste\"><img src=\"<?php echo $templates; ?>system/paste.png\" title=\"<?php echo _AT('paste'); ?>\" alt=\"\" /> <?php echo _AT('paste_model_sequence'); ?></li>";
		
		// copy
		boxModelToolbox	= boxModelToolbox + "<li id=\"modelCopy\"><img src=\"<?php echo $templates; ?>system/copy.png\" title=\"<?php echo _AT('copy'); ?>\" alt=\"\" /> <?php echo _AT('copy_model_sequence'); ?></li>";
		
		boxModelToolbox = boxModelToolbox + "</ul></div>";


		////////////////////////////////////////
		//	MODELS EVENT ON / OFF
		////////////////////////////////////////

		$('#modelCopy').live("click", function(){

			var allModels	= '';

			$('.model').each(function(index) {
				allModels = allModels + "|" + $(this).attr('class');
			});

			var c_name		= 'modelClipboard';
			var value		= allModels;
			var exdays		= '1';

			// create  cookie
			var exdate		= new Date();
			exdate.setDate(exdate.getDate() + exdays);
			var c_value		= escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
			document.cookie	= c_name + "=" + c_value;

			$('#modelCopy').css('background','#f0f8ff');

			$('#modelPaste').css('display','inline');

		});

		$('#modelPaste').live("click", function(){

			var c_name		= 'modelClipboard';
	
			// read cookie
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

			// if there are already other models
			// ask if you want to add the clipboard in head
			if($('.model').attr('class') != 'model noModel'){
				if(!confirm("<?php echo _AT('add_to_existing_models'); ?>") ) {
            		return false;
		  		}
		  	}

			// add models
			//$('#dnd').html(Models + $('#dnd').html());
			var m = Models.split('|');

			var dopoNoModel = 0;
			// the cycle starts from 1 because the first element is ''
			for(i=1; i<m.length; i++){
				var modelID = m[i].replace("model ", "");
				
				if(modelID == 'noModel')
					dopoNoModel = 1;
				else
					aggiungiModello(modelID, dopoNoModel);
			}
			
			// save the new content
			salvaModificheContenuto();

		});

	
		$('#attivaModelli_btn').change(function (event) {
			
			if($('#attivaModelli_btn').is(':checked')) {
				
				// disable SORT MODELS
				$('#ordinaModelli_btn').attr('disabled','disabled');

				$('head').append('<link rel="stylesheet" href="<?php echo $templates; ?>system/models.css" type="text/css" />');

				// cut and paste toolBar
				base.before(boxModel + boxModelToolbox);

				// CUT & PASTE

				// cookie name
				var c_name		= 'modelClipboard';

				// read cookie
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
	
				// fill the box with the models available
				// choose to view models
				var m = '';

				var count = 0;
				<?php
						
						
				foreach($listaModelli as $key => $value) {
					
					echo 'count++;';
					echo '$(".boxModel").append($("<li>"));';
					echo 'm = m + "<li><table id=\"'.$key.'\"><tr><td><img src=\"'.$templates.'models/'.$key.'/screenshot.png\" /></td></tr><td class=\"desc\">'.$value['name'].'</td></tr></table></li>";';
				}		
				?>
				//HERE!!
			
				$(".boxModel ul").append(m);

				// show models box
				$('.boxModel').slideToggle('slow', function(){
					
					$(this).css('display','block');
				
				});

				visualizzaModelli();

			}else{
				
				// temporarily turn off the models button
				$('#attivaModelli_btn').attr("disabled", "disabled");

				nascondiModelli();

				// show models box

				$('.boxModel').slideToggle('slow', function(){

					// remove the "cut" box & paste it from DOM
					$('.boxModelToolbox').remove();
					// remove the boxModel from DOM
					$('.boxModel').remove();

					// save the content
					salvaModificheContenuto();

					// remove the stylesheet
					var modelStylesheet	= $('link[href="<?php echo $templates; ?>system/models.css"]');
					modelStylesheet.remove();

				});
			}
		});


		////////////////////////////////////////
		//	ARRANGE MODELS BUTTON
		////////////////////////////////////////

		$('#ordinaModelli_btn').click(function(){
			if($('#ordinaModelli_btn').is(':checked')){

				// disable MODELS ACTIVATE
				$('#attivaModelli_btn').attr('disabled', 'disabled');

				$('.noModel').addClass('noModelSorting');
				
				$('head').append('<link rel="stylesheet" href="<?php echo $templates; ?>system/models.css" type="text/css" />');

				visualizzaModelli();
				
			}else{
				
				nascondiModelli();

				//$('.boxModelToolbox').hide();
				
				// disable MODELS ACTIVATE
				$('#ordinaModelli_btn').attr('disabled', 'disabled');

				$('.noModel').removeClass('noModelSorting');

				// show box models
				
				// remove the boxModel from DOM
				$('.boxModel').remove();

				// save the file
				/*
				var url			= "<?php echo $templates; ?>" + "system/AJAX_actions.php";
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

				// remove the stylesheet
				var modelStylesheet	= $('link[href="<?php echo $templates; ?>system/models.css"]');
				modelStylesheet.remove();
			}
		});


		////////////////////////////////////////
		//	MODELS SORTING
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
		//	ADD A NEW MODEL
		////////////////////////////////////////

		$('.boxModel li').live("click", function(){

			var structure	= "";

			// take the name of the template you want to insert
			var modelID		= $(this).find('table').attr('id');
			
			// add model
			aggiungiModello(modelID, 0);

		});

		////////////////////////////////////////
		//	DELETE SELECTED MODEL
		////////////////////////////////////////

		$('.removeModel').live("click", function(){


			// slideUp effect
			/*
			$(this).parent().parent().slideUp(300,function(){
				$(this).remove();
			});
			*/
			var modello	= $(this).parents('.model');

			// fade effect

			modello.fadeOut(300, function(){
				modello.remove();
			});

		});


		$("#body_text_ifr").live("mouseover", function(){
			
			var oldContent	= tinyMCE.activeEditor.getContent();
	
			var newContent;
				
			tinyMCE.activeEditor.setContent(newContent);
			

		});
		
		/*
		*	Fix an annoying behavior of browsers:
		*	when I vertically scroll the contents of a div (in this case the model)
		*	and reach the bottom, the focus is automatically taken from the page that scrolls.
		*/
		$(".boxModel").live({

			mouseover: function() {
    			$('body').css('overflow','hidden');
    			$('body').css('padding-right','15px');
    			
  			},
  			mouseout: function() {
    			$('body').css('overflow','auto');
    			$('body').css('padding-right','0px');
  			}
		});


		/*######################################
			FUNCTIONS
		######################################*/
		
		function aggiungiModello(modelID, afterNoModel){

			var url			= "<?php echo $templates; ?>" + "system/AJAX_actions.php";

			// structure is nothing else the mere HTML code model
			$.post(url, {mID: modelID}, function(structure){

				if(afterNoModel == 0){
				
					if(base.children(":first").is("*")){
						base.children(":first").before(creaModello(structure, modelID));
					}else{
						base.append(creaModello(structure, modelID));
					}
				}else{
					$('.noModel').after('<div class="model ' + modelID + '" id="newModel">' + creaModello(structure, modelID) + "</div>");
				}

				// upgrade to models preview

				
				// insert the model
				$('#newModel').fadeIn(300);
			

				$('#content-text .model img').each(function(index) {
					if($(this).attr('src') == 'dnd_image'){
						$(this).attr('src', "<?php echo $templates.'system/model_image.png'; ?>");
						$(this).addClass("insert_image");
					}
				});

				$('#newModel').removeAttr('id');
			});
		}
		
		function salvaModificheContenuto(){
			var url			= "<?php echo $templates; ?>" + "system/AJAX_actions.php";
			var vcid		= "<?php echo $cid; ?>";
			var vaction		= 'saveModelContent';
			
			var vtext		= $('#content-text').html();

			$.post(url, {cid: vcid, text: vtext, action: vaction}, function(data){

				// enable the models button
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

			// show the models options (delete, sort)
			$('.model').each(function(index) {
				// show the "X"
				$(this).find(' tr:first').before("<tr><td>" + removeModelTopBar);

				// show the sorting bar
				$(this).append("<tr><td>" + sortTools);
			});
			
			return;


		}
		
		function nascondiModelli(){

			$('.model').each(function(index) {
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
</script>