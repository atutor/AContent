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

if($_content_id != "" && $_course_id != "") {
	
	//$course = $coursesDAO->get($_course_id);
	$content = $contentDAO->get($_content_id);
	
	if($content['structure']!='') {
		$structManager = new StructureManager($content['structure']);
                die('qui '.$content['title']);

		$array = $structManager->getContentByTitle($content['title']);
		$pageTemplateList = $mod->validatedPageTemplate($array);
			
	}  else {
		$pageTemplateList = $mod->getPageTemplateList();
		
	}


}

	           
//}
// call the function that creates the graphics module selection
$output	= $mod->createUI();

$templates		= TR_BASE_HREF.'templates/';
$templates_int	= TR_INCLUDE_PATH.'../templates/';

// path containing the page_template list
$page_template_dir		= $templates.'page_template/';
$page_template_dir_int	= $templates_int.'page_template/';

// directory and file systems to be excluded from the page_template list
$except	= array('.', '..', '.DS_Store', 'desktop.ini', 'Thumbs.db');

// content id
$cid	= $this->cid;
// if not present, take the _cid (content id to be edited)
if($cid == '' and isset($_GET['_cid'])and $_GET['_cid'] != '')
	$cid = htmlentities($_GET['_cid']);



######################################
#	RETURN OUTPUT
######################################

$savant->assign('title', _AT('page_template'));

$savant->assign('dropdown_contents', $output);

$savant->display('include/box.tmpl.php');

?>
