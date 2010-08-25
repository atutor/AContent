<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }

function load_editor($simple = TRUE, $name = FALSE, $mode="textareas") {
	global $_base_path, $content_base_href, $_course_id;

	 if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) { 
		$course_base_href = 'get.php/'; 
	} else {  
		$course_base_href = 'content/' . $_course_id . '/'; 
	}

// Note: Some tinymce buttons are removed due to lack of accessibility for disabled.
// They are:
//New Document: newdocument
//Insert Time: inserttime
//Insert Date: insertdate
//Preview: preview
//toggle guidelines: visualaid
//spellcheck: iespell
//embed media: media
//print: print
//Insert Layer: insertlayer
//move forward: moveforward
//move backward: movebackward
//toggle absolute positioning: absolute
//citation: cite
//deletion: del
//insertion: ins
//visual control characters on/off: visualchars
//insert non-breaking space character: nonbreaking
//insert predefined template: template
//insert page break: pagebreak

echo '<script language="javascript" type="text/javascript" src="'.TR_BASE_HREF.'include/jscripts/tiny_mce/tiny_mce_src.js"></script>
<script language="javascript" type="text/javascript">';

echo 'var ATutor = ATutor || {};
      trans.tinymce = trans.tinymce || {};

      (function () {
';

echo 'tinymce.PluginManager.load("insert_tag", "'.TR_BASE_HREF.'include/jscripts/tiny_mce_plugins/insert_tag/editor_plugin.js");
';
echo 'tinymce.PluginManager.load("swap_toolbar", "'.TR_BASE_HREF.'include/jscripts/tiny_mce_plugins/swap_toolbar/editor_plugin.js");
';

echo 'var initSettings = {';
    if ($name) {
        echo '  mode : "exact",';
        echo '  elements : "'.$name.'",';
    } else {
        echo '  mode : "'.$mode.'",';
    }   
echo 'theme: "advanced",
      relative_urls : true,
      content_css :"'.$_base_path.'/include/lib/tinymce_styles.css",
      convert_urls : true,
      accessibility_warnings : true,
      entity_encoding : "raw",
      accessibility_focus : true,
      plugins : "-insert_tag, -swap_toolbar, acheck, table,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_path_location : "bottom",
      theme_advanced_resizing : true,
      remove_linebreaks: false,
      plugin_insertdate_dateFormat : "%Y-%m-%d",
      plugin_insertdate_timeFormat : "%H:%M:%S",
      extended_valid_elements : "a[name|href|target|title|onclick|style|class],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
      document_base_url: "'.TR_BASE_HREF.$course_base_href.$content_base_href.'"
    };

    //these are the simple tools used on the content editor page
    var simpleToolBars = {
        theme_advanced_buttons1 : "swap_toolbar_complex,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,|,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,pasteword,link,unlink,|,acheck",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_buttons4 : ""
    };
    
    //these are the more complex tools used on the content editor page
    var complexToolBars = {
        theme_advanced_buttons1 : "swap_toolbar_simple,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,ltr,rtl,blockquote,|,forecolor,backcolor,|,sub,sup,|,tablecontrols",
        theme_advanced_buttons3 : "cut,copy,paste,pastetext,pasteword,|,link,unlink,anchor,image,|,abbr,acronym,|,charmap,emotions,advhr,|,insert_term_tag, insert_media_tag, insert_tex_tag",
        theme_advanced_buttons4 : "search,replace,|,removeformat,undo,redo,|,styleprops,attribs,|,acheck,|,cleanup,code,|,fullscreen "
    };

    trans.tinymce.initSimple = function() {
        tinyMCE.init(jQuery.extend({}, initSettings, simpleToolBars));
    };
      
    trans.tinymce.initComplex = function() {
        tinyMCE.init(jQuery.extend({}, initSettings, complexToolBars));
    };
';
    
    if ($simple) {
      echo 'trans.tinymce.initSimple();
      ';
    } else {
      echo 'trans.tinymce.initComplex();
      ';
    }
echo '})();
';
echo '</script>';
}

?>
