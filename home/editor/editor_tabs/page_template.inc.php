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
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/PrivilegesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'../home/classes/StructureManager.class.php');

include('templates/system/page_template.css');

/* Provo ad inserire la pagina che contiene il codice javascript*/
include_once($mod_path['templates_sys'].'Page_templates.js');

global $savant;

$contentDAO		= new ContentDAO();
$privilegesDAO	= new PrivilegesDAO();
//$coursesDAO = new CoursesDAO();
$output = '';

?>

<?php

######################################
#	Variables declarations / definitions
######################################
global $_course_id, $_content_id;

$_course_id		= $course_id = (isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : $_course_id);
$_content_id	= $cid = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : $_content_id; /* content id of an optional chapter */
// paths settings

$mod_path					= array();
$mod_path['templates']		= realpath(TR_BASE_HREF			. 'templates').'/';
$mod_path['templates_int']	= realpath(TR_INCLUDE_PATH		. '../templates').'/';
$mod_path['templates_sys']	= $mod_path['templates_int']	. 'system/';
$mod_path['page_template_dir']		= $mod_path['templates']		. 'page_template/';
$mod_path['page_template_dir_int']	= $mod_path['templates_int']	. 'page_template/';

// include the file "apply_model" so that he can inherit variables and constants defined by the system
include_once($mod_path['templates_sys'].'Page_template.class.php');

// instantiate the class page_template (which calls the constructor)
$mod		= new Page_template($mod_path);

$user_priv	= $privilegesDAO->getUserPrivileges($_SESSION['user_id']);
$is_author	= $user_priv[1]['is_author'];

// take the list of available valid page_template

$pageTemplateList = array();


/*  //OLD
 *  Now the command is run in the file Page_template.class.php 
 *
if($_content_id != "" && $_course_id != "") {
	
	$content = $contentDAO->get($_content_id);
 
	if($content['structure']!='') {
		$structManager = new StructureManager($content['structure']);

		$array = $structManager->getContentByTitle($content['title']);

		$pageTemplateList = $mod->validatedPageTemplate($array);
                
	}  else {
		$pageTemplateList = $mod->getPageTemplateList();
	}
}*/



$content_page	= $content['text'];

//}

$templates		= TR_BASE_HREF.'templates/';
$templates_int	= TR_INCLUDE_PATH.'../templates/';

// path containing the page_template list
$page_template_dir		= $templates.'page_template/';
$page_template_dir_int	= $templates_int.'page_template/';

// directory and file systems to be excluded from the page_template list
$except	= array('.', '..', '.DS_Store', 'desktop.ini', 'Thumbs.db');


//  I HAVE TO PUT THE FOLLOWING STATEMENT COMMENTED OUT IF NO OUTPUT IS DISPLAYED
// content id
//$cid	= $this->cid; //da errore senza mettere sotto commento

// if not present, take the _cid (content id to be edited)
if($cid == '' and isset($_GET['_cid'])and $_GET['_cid'] != '')
	$cid = htmlentities($_GET['_cid']);


######################################
#	JQUERY SCRIPT MODULE
######################################
//include $mod_path['templates_sys'].'Page_template.js';

######################################
#	RETURN OUTPUT
######################################

/* OLD VERSION
$savant->assign('title', _AT('page_template'));

$savant->assign('dropdown_contents', $output);

$savant->display('include/box.tmpl.php');
 * 
 */
$content_layout = $content['layout']; // Retrieving the value of the layout

if($cid==null){
    echo '<div id="error">';
    echo '<h4>The following errors occurred:</h4><ul>';
    echo '<li>Please save the content before proceeding to define "Page templates".</li></ul></div>';
}else{
$sql="SELECT layout FROM ".TABLE_PREFIX."content WHERE content_id=".$cid."";
$result=$dao->execute($sql);

    if(is_array($result))
    {
        foreach ($result as $support) {
           //echo $support['head']; 
           $layout=$support['layout'];
           break;
        }  
    }


$sql="SELECT text FROM ".TABLE_PREFIX."content WHERE content_id=".$cid."";
$result=$dao->execute($sql);

    if(is_array($result))
    {
        foreach ($result as $support) {
           //echo $support['head']; 
           $text=$support['text'];
           break;
        }  
    }
    
    //content-length if less than 24 there is content, being 24 to the div id = "content" that is inserted automatically 
    $sup=strlen($text);
    
    // call the function that creates the graphics module selection
    // step content length and the value cid-->content_id
    $output	= $mod->createUI($sup,$cid);
echo '<link type="text/css" rel="stylesheet" href="'.TR_BASE_HREF.'/themes/default/form.css">';    
echo '<div id="success" style="display:none;">';
echo '<label  class="success_label">Action completed successfully.</label>';
echo '</div>';

echo '<div id="no-cont-pre" style="display:none; margin: 10px; margin-top: 20px; margin-bottom: 15px;">';
echo '<div style="margin-left:10px;"><b>No content</b> associated</div>';
echo '</div>';

echo '<div id="whit-cont-pre" style="display:none; margin: 10px; margin-top: 20px; margin-bottom: 15px;">';
echo '<div style="margin-left:10px;">Content associated:</div></div>';

if($sup<=24){ 
    echo '<div style="margin: 10px; margin-top: 10px; margin-bottom: 15px;">';
    echo '<div id="no-cont"><b>No content</b> associated</div>';
    echo '</div>';
    $whit_content=0;
    $mod->view_page_templates($whit_content);
}
else{
    echo '<link type="text/css" rel="stylesheet" href="'.TR_BASE_HREF.'templates/layout/'.$layout.'/'.$layout.'.css">';
    echo '<div style="margin: 10px; margin-top: 10px; margin-bottom: 15px;">';
    echo '<div id="whit-cont">Content associated: </div>';
    // I insert a new div to try not to lose the old contents in the case of rescue
    echo '<div id="content-previous">';
        echo $text;
    echo '</div>';
    echo'</div>';
    
    $whit_content=1;
    $mod->view_page_templates($whit_content);
}







// NEW VERSION
if ($output == '') {
	$output = _AT('none_found');
}

// title of the side block
// if there is no translation in the choosen language, use the default one

// content
echo $output;
}
?>
<script>
$('.unsaved').css('display','none');
</script>