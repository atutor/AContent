<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2011                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

global $savant;

$contentDAO = new ContentDAO();
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
$mod_path['themes_dir']		= $mod_path['dnd_themod']		. 'themes/';
$mod_path['themes_dir_int']	= $mod_path['dnd_themod_int']	. 'themes/';

// include the file "applicaTema" so that he can inherit variables and constants defined by the system
include_once($mod_path['dnd_themod_sys'].'Themes.class.php');

// instantiate the class Themes (which calls the constructor)
$the		= new Themes($mod_path);

// take the list of available valid themes
$listaTemi	= $the->getListaTemi();

// call the function that creates the graphics module theme selection
$resArray	= $the->createUI($listaTemi);

// array containing the current contents (text, header, bit that indicates that the header is included)
$content	= getContent($contentDAO, $cid);

######################################
#	JQUERY SCRIPT MODULE
######################################

// the following "Conversioni" da una matrice variabile Sono necessarie a risolvere problemi di compatibilita fra JS e PHP
$textContent	= $content['text'];
$textContent	= htmlentities($textContent);
$textContent	= str_replace("\r\n","", $textContent);

$headContent	= $content['head'];
$headContent	= str_replace("\r\n","", $headContent);

$formatContent	= $content['formatting'];

$course_id		= $content['course_id'];

$content_theme	= $content['theme'];



$dnd_themod		= TR_BASE_HREF.'dnd_themod/';
$dnd_themod_int	= $mod_path['dnd_themod_int'];
$dnd_themod_sys	= $mod_path['dnd_themod_sys'];
// path containing the themes list
$themes_dir		= $dnd_themod.'themes/';
$themes_dir_int	= $dnd_themod_int.'themes/';

$config					= parse_ini_file($mod_path['dnd_themod_sys'].'config.ini');
$apply_lesson_theme		= $config['apply_to_the_lesson'];

include $mod_path['dnd_themod_sys'].'Themes.js';


######################################
#	RETURN THE OUTPUT
######################################

// restituisco l'output
$output		= $resArray;

if ($output == '') {
	$output = _AT('none_found');
}

// title of the side block
// if there is no translation in the choosen language, use the default one

$savant->assign('title', _AT('themes'));

// content

$savant->assign('dropdown_contents', $output);

$savant->display('include/box.tmpl.php');

######################################
#	PHP FUNCTIONS
######################################

/*
 * Take the values of the current contents
 * $text		: must be handled and used in the preview
 * $head 		: if the theme should be overwritten, this is done in JQuery
 * $formatting	: need to know how to show the preview (Plain Text, HTML, Web Link)
 */

function getContent($contentDAO, $cid){

	if(isset($cid)){
		$db =  $contentDAO->get($cid);
		
		$content['text']				= $db['text'];
		$content['head']				= $db['head'];
		$content['formatting']			= $db['formatting'];
		$content['course_id']			= $db['course_id'];
		$content['theme']				= $db['theme'];
		
		return $content;
	}else
		return '';
}

?>
