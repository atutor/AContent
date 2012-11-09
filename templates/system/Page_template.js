<script type="text/javascript">

	var removePageTemplateTopBar	= '<div class="removePageTemplateTopBar"><div class="removePageTemplate">X</div></div>';
/* OLD	var sortTools			= '<div class="sortTools">\
								<img src="<?php echo $templates; ?>system/top.png" class="movePageTemplateTop" alt="move top" />\
								<img src="<?php echo $templates; ?>system/up.png" class="movePageTemplateUp" alt="move up" />\
								<img src="<?php echo $templates; ?>system/down.png" class="movePageTemplateDown" alt="move down" />\
								<img src="<?php echo $templates; ?>system/bottom.png" class="movePageTemplateBottom" alt="move bottom" />\
								</div>'; */
// new 22/10/2012
var sortTools= '<div class="sortTools"><img src="/AContent/templates/system/top.png" class="movePageTemplateTop" alt="move top" /><img src="/AContent/templates/system/up.png" class="movePageTemplateUp" alt="move up" /><img src="/AContent/templates/system/down.png" class="movePageTemplateDown" alt="move down" /><img src="/AContent/templates/system/bottom.png" class="movePageTemplateBottom" alt="move bottom" /></div>';


	$(document).ready(function(){ 
		
		// this row allow to show the form just if JS is enabled
		// il selector depends by the module name (customizable in the language file)
//OLD		var module_name	= "<?php echo _AT('page_template'); ?>";

// new
                <$php $support=_AT('page_template'); ?>
                var module_name	= '<?php echo $support; ?>';



		module_name		= module_name.replace(/ /g, '');
		
		// if the user is an authenticated author
		// show the module
		
		if("<?php echo $is_author; ?>" == 1 && "<?php echo basename($_SERVER['PHP_SELF']); ?>" == "content.php")
			$('#menu_' + module_name + ' form').show();
		else
			$('#menu_' + module_name + ' form').hide();
		

		/*
		if($('#view').is('*'))
			base			= $('#view');
		else
		*/
			base			= $('#content-text');

		////////////////////////////////////////
		//	INCLUSIONS / DECLARATIONS / DEFINITIONS
		////////////////////////////////////////

		var boxPageTemplate			= '<div class="boxPageTemplate"><ul></ul></div>';

		// consider only page_template during the content preview

		// put on the content top the page template box
		boxPageTemplateToolbox = "<div class=\"boxPageTemplateToolbox\"><ul>";

		// paste
		boxPageTemplateToolbox = boxPageTemplateToolbox + "<li id=\"pageTemplatePaste\"><img src=\"<?php echo $templates; ?>system/paste.png\" title=\"<?php echo _AT('paste'); ?>\" alt=\"\" /> <?php echo _AT('paste_page_template_sequence'); ?></li>";
		
		// copy
		boxPageTemplateToolbox	= boxPageTemplateToolbox + "<li id=\"pageTemplateCopy\"><img src=\"<?php echo $templates; ?>system/copy.png\" title=\"<?php echo _AT('copy'); ?>\" alt=\"\" /> <?php echo _AT('copy_page_template_sequence'); ?></li>";
		
		boxPageTemplateToolbox = boxPageTemplateToolbox + "</ul></div>";


		////////////////////////////////////////
		//	page_template EVENT ON / OFF
		////////////////////////////////////////

		$('#pageTemplateCopy').live("click", function(){

			var allpage_template = '';

			$('.page_template').each(function(index) {
				allpage_template = allpage_template + "|" + $(this).attr('class');
			});

			var c_name		= 'pageTemplateClipboard';
			var value		= allpage_template;
			var exdays		= '1';

			// create  cookie
			var exdate		= new Date();
			exdate.setDate(exdate.getDate() + exdays);
			var c_value		= escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
			document.cookie	= c_name + "=" + c_value;

			$('#pageTemplateCopy').css('background','#f0f8ff');

			$('#pageTemplatePaste').css('display','inline');

		});

		$('#pageTemplatePaste').live("click", function(){

			var c_name		= 'pageTemplateClipboard';
	
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
						var page_template = unescape(y);
				}
			}

			// if there are already other page_template
			// ask if you want to add the clipboard in head
			if($('.page_template').attr('class') != 'pageTemplate  noPageTemplate'){
				if(!confirm("<?php echo _AT('add_to_existing_page_template'); ?>") ) {
            		return false;
		  		}
		  	}

			// add page_template
			//$('#dnd').html(page_template + $('#dnd').html());
			var m = page_template.split('|');

			var noPageTemplateAfter = 0;
			// the cycle starts from 1 because the first element is ''
			for(i=1; i<m.length; i++){
				var pageTempalteID = m[i].replace("pageTemplate ", "");
				
				if(pageTempalteID == 'noPageTemplate')
					noPageTemplateAfter = 1;
				else
					addPageTemplate(pageTempalteID, noPageTemplateAfter);
			}
			
			// save the new content
			saveChangeInContent();

		});

	
		$('#activate_page_template').change(function (event) {
			
			if($('#activate_page_template').is(':checked')) {
				
				// disable SORT page_template
				$('#orderPageTemplate').attr('disabled','disabled');

				$('head').append('<link rel="stylesheet" href="<?php echo $templates; ?>system/page_template.css" type="text/css" />');

				// cut and paste toolBar
				base.before(boxPageTemplate + boxPageTemplateToolbox);

				// CUT & PASTE

				// cookie name
				var c_name		= 'pageTemplateClipboard';

				// read cookie
				var i,x,y,ARRcookies=document.cookie.split(";");
				for (i=0;i<ARRcookies.length;i++){
					x	= ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
					y	= ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
					x	= x.replace(/^\s+|\s+$/g,"");
					if (x==c_name){
						if(unescape(y) != '')
							$('#pageTemplatePaste').css('display','inline');
					}
				}
	
				// fill the box with the page_template available
				// choose to view page_template
				var m = '';

				var count = 0;
				<?php
						
						
				foreach($pageTemplateList as $key => $value) {
					
					echo 'count++;';
					echo '$(".boxPageTemplate").append($("<li>"));';
					echo 'm = m + "<li><table id=\"'.$key.'\"><tr><td><img src=\"'.$templates.'page_template/'.$key.'/screenshot.png\" /></td></tr><td class=\"desc\">'.$value['name'].'</td></tr></table></li>";';
				}		
				?>
				//HERE!!
			
				$(".boxPageTemplate ul").append(m);

				// show page_template box
				$('.boxPageTemplate').slideToggle('slow', function(){
					
					$(this).css('display','block');
				
				});

				showPageTemplate();

			}else{
				
				// temporarily turn off the page_template button
				$('#activate_page_template').attr("disabled", "disabled");

				hidePageTemplate();

				// show page_template box

				$('.boxPageTemplate').slideToggle('slow', function(){

					// remove the "cut" box & paste it from DOM
					$('.boxPageTemplateToolbox').remove();
					// remove the boxPageTemplate from DOM
					$('.boxPageTemplate').remove();

					// save the content
					saveChangeInContent();

					// remove the stylesheet
					var page_templatetylesheet  = $('link[href="<?php echo $templates; ?>system/page_template.css"]');
					page_templatetylesheet.remove();

				});
			}
		});


		////////////////////////////////////////
		//	ARRANGE page_template BUTTON
		////////////////////////////////////////

		$('#orderPageTemplate').click(function(){
			if($('#orderPageTemplate').is(':checked')){

				// disable page_template ACTIVATE
				$('#activate_page_template').attr('disabled', 'disabled');

				$('.noPageTemplate').addClass('nopage_templateorting');
				
				$('head').append('<link rel="stylesheet" href="<?php echo $templates; ?>system/page_template.css" type="text/css" />');

				showPageTemplate();
				
			}else{
				
				hidePageTemplate();

				//$('.boxPageTemplateToolbox').hide();
				
				// disable page_template ACTIVATE
				$('#orderPageTemplate').attr('disabled', 'disabled');

				$('.noPageTemplate').removeClass('nopage_templateorting');

				// show box page_template
				
				// remove the boxPageTemplate from DOM
				$('.boxPageTemplate').remove();

				saveChangeInContent();

				// remove the stylesheet
				var page_templatetylesheet	= $('link[href="<?php echo $templates; ?>system/page_template.css"]');
				page_templatetylesheet.remove();
			}
		});


		////////////////////////////////////////
		//	page_template SORTING
		////////////////////////////////////////

		// top

		$('.movePageTemplateTop').live("click", function(){

			// this page_template
			var pageTemplate = $(this).parents('.page_template');

			base.prepend(pageTemplate);

		});

		// up

		$('.movePageTemplateUp').live("click", function(){

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

		$('.movePageTemplateDown').live("click", function(){

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

		$('.movePageTemplateBottom').live("click", function(){

			// this page_template
			var page_template = $(this).parents('.page_template');

			base.append(page_template);
		});

		////////////////////////////////////////
		//	ADD A NEW page_template
		////////////////////////////////////////

		$('.boxPageTemplate li').live("click", function(){

			var structure	= "";

			// take the name of the template you want to insert
			var pageTempalteID		= $(this).find('table').attr('id');
			
			// add page_template
			addPageTemplate(pageTempalteID, 0);
                       

		});

		////////////////////////////////////////
		//	DELETE SELECTED page_template
		////////////////////////////////////////

		$('.removePageTemplate').live("click", function(){


			// slideUp effect
			/*
			$(this).parent().parent().slideUp(300,function(){
				$(this).remove();
			});
			*/
			var page_template	= $(this).parents('.page_template');

			// fade effect

			page_template.fadeOut(300, function(){
				page_template.remove();
			});

		});


		$("#body_text_ifr").live("mouseover", function(){
			
			var oldContent	= tinyMCE.activeEditor.getContent();
	
			var newContent;
				
			tinyMCE.activeEditor.setContent(newContent);
			

		});
		
		/*
		*	Fix an annoying behavior of browsers:
		*	when I vertically scroll the contents of a div (in this case the page_template)
		*	and reach the bottom, the focus is automatically taken from the page that scrolls.
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


		/*######################################
			FUNCTIONS
		######################################*/
		
		function addPageTemplate(pageTempalteID, afternoPageTemplate){

			var url			= "<?php echo $templates; ?>" + "system/AJAX_actions.php";

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
						$(this).attr('src', "<?php echo $templates.'system/page_template_image.png'; ?>");
						$(this).addClass("insert_image");
					}
				});

				$('#newPageTemplate').removeAttr('id');
			});
		}
		
		function saveChangeInContent(){
			var url			= "<?php echo $templates; ?>" + "system/AJAX_actions.php";
			var vcid		= "<?php echo $cid; ?>";
			var vaction		= 'savePageTemplateContent';
			
			var vtext		= $('#content-text').html();

			$.post(url, {cid: vcid, text: vtext, action: vaction}, function(data){

				// enable the page_template button
				$('#activate_page_template').removeAttr("disabled");
				$('#orderPageTemplate').removeAttr('disabled');

			});
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
</script>