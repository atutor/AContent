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
$mod_path['layout_dir']		= $mod_path['templates']		. 'layouts/';
$mod_path['layout_dir_int']	= $mod_path['templates_int']	. 'layouts/';

// include the file "applicaTema" so that he can inherit variables and constants defined by the system
include_once($mod_path['templates_sys'].'Layout.class.php');

// instantiate the class layout (which calls the constructor)
$layout		= new Layout($mod_path);

// take the list of available valid layout
$layout_list	= $layout->getLayoutList();


// array containing the current contents (text, header, bit that indicates that the header is included)
$content	= getContent($contentDAO, $cid);

// call the function that creates the graphics module layout selection
$output = $layout->createUI($layout_list,$_content_id);
######################################
#	JQUERY SCRIPT MODULE
######################################

$textContent	= $content['text'];
$textContent	= htmlentities($textContent);
$textContent	= str_replace("\r\n","", $textContent);

$headContent	= $content['head'];
$headContent	= str_replace("\r\n","", $headContent);

$formatContent	= $content['formatting'];

$course_id		= $content['course_id'];

$content_layout	= $content['layout'];

$templates		= TR_BASE_HREF.'templates/';
$templates_int	= $mod_path['templates_int'];
$templates_sys	= $mod_path['templates_sys'];
// path containing the layout list
$layout_dir		= $templates.'layouts/';
$layout_dir_int	= $templates_int.'layouts/';

$config					= parse_ini_file($mod_path['templates_sys'].'config.ini');
$apply_lesson_layout		= $config['apply_to_the_lesson'];



######################################
#	RETURN THE OUTPUT
######################################
if($cid==null){
	$msg->printErrors('SAVE_BEFORE_PROCEED');
	require_once(TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}else{

echo '<p style="padding:1em;">'; 

if($content_layout == null) 
    echo _AT('no_layout_associated');
else
	echo _AT('layout_associated', $content_layout);
echo '</p>';


if ($output == '') {
	$output = _AT('none_found');
}

// title of the side block
// if there is no translation in the choosen language, use the default one

// content
echo $output;
}
######################################
#	PHP FUNCTIONS
######################################

/*
 * Take the values of the current contents
 * $text		: must be handled and used in the preview
 * $head 		: if the layout should be overwritten, this is done in JQuery
 * $formatting	: need to know how to show the preview (Plain Text, HTML, Web Link)
 */

function getContent($contentDAO, $cid){

	if(isset($cid)){
		$db =  $contentDAO->get($cid);
		
		$content['text']				= $db['text'];
		$content['head']				= $db['head'];
		$content['formatting']			= $db['formatting'];
		$content['course_id']			= $db['course_id'];
		$content['layout']				= $db['layout'];
		
		return $content;
	}else
		return '';
}

?>
