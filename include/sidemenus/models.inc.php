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
$mod_path['dnd_themod']		= realpath(TR_BASE_HREF			. 'dnd_themod').'/';
$mod_path['dnd_themod_int']	= realpath(TR_INCLUDE_PATH		. '../dnd_themod').'/';
$mod_path['dnd_themod_sys']	= $mod_path['dnd_themod_int']	. 'system/';
$mod_path['models_dir']		= $mod_path['dnd_themod']		. 'models/';
$mod_path['models_dir_int']	= $mod_path['dnd_themod_int']	. 'models/';

// include the file "applicaModello" so that he can inherit variables and constants defined by the system
include_once($mod_path['dnd_themod_sys'].'Models.class.php');

// instantiate the class Models (which calls the constructor)
$mod		= new Models($mod_path);

$user_priv	= $privilegesDAO->getUserPrivileges($_SESSION['user_id']);
$is_author	= $user_priv[1]['is_author'];

// take the list of available valid models

$listaModelli = array();

if($_content_id != "" && $_course_id != "") {
	
	//$course = $coursesDAO->get($_course_id);
	$content = $contentDAO->get($_content_id);
	
	if($content['structure']!='') {
		$structManager = new StructureManager($content['structure']);
		$item = $structManager->getPageTemplatesItem($content['title']);
		$listaModelli = $mod->getPageTemplates($item);
		
		
			
	}  else 
		$listaModelli = $mod->getListaModelli();
	

}

	           
//}
// call the function that creates the graphics module selection
$output	= $mod->createUI();

$dnd_themod		= TR_BASE_HREF.'dnd_themod/';
$dnd_themod_int	= TR_INCLUDE_PATH.'../dnd_themod/';

// path containing the models list
$model_dir		= $dnd_themod.'models/';
$model_dir_int	= $dnd_themod_int.'models/';

// directory and file systems to be excluded from the models list
$except	= array('.', '..', '.DS_Store', 'desktop.ini', 'Thumbs.db');

// content id
$cid	= $this->cid;
// if not present, take the _cid (content id to be edited)
if($cid == '' and isset($_GET['_cid'])and $_GET['_cid'] != '')
	$cid = htmlentities($_GET['_cid']);


######################################
#	JQUERY SCRIPT MODULE
######################################
include $mod_path['dnd_themod_sys'].'Models.js';
include $mod_path['dnd_themod_sys'].'prova.js';

######################################
#	RETURN OUTPUT
######################################

$savant->assign('title', _AT('models'));


$savant->assign('dropdown_contents', $output);

$savant->display('include/box.tmpl.php');

?>
