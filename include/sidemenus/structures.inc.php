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
$mod_path['structs_dir']		= $mod_path['templates']		. 'structures/';
$mod_path['structs_dir_int']	= $mod_path['templates_int']	. 'structures/';

// includo immediatamente il file "applicaTema" così che possa ereditare variabili e costanti definite dal sistema
include_once($mod_path['templates_sys'].'Structures.class.php');


list($caller_url, $url_param) = Utility::getRefererURLAndParams();


$structs	= new Structures($mod_path);

$structsList = $structs->getStructsList();

$output	= '';
		
if (!is_array($structsList)) {
	$output = _AT('none_found');
} else {
	$num_of_courses = count($structsList);
	
	
    $output .= '<ol class="remove-margin-left">'."\n";
   	$output .=  '<p style="">Simple structures: </p>';
	
   	$simpleList = array();
   	
	foreach ($structsList as $val) {
		
		$output .= ' <li title="'.$val['name'].'"> '."\n";
		$output .= ' <a href="'. TR_BASE_HREF.'home/structs/outline.php?_struct_name='.$val['short_name'].'">'."\n";
		$output .= $val['name'];
		$output .= ' </a>'."\n";
    	$output .= '  </li>'."\n";
    	
    	$simpleList[] = $val['name'];
    }
  
	
}

$output .= '</ol>'."\n";
		
			
$savant->assign('title', _AT('structures'));

// contenuto

$savant->assign('dropdown_contents', $output);
//$savant->assign('default_status', "hide");

$savant->display('include/box.tmpl.php');


?>
