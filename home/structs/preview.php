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

define('TR_INCLUDE_PATH', '../../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');


$prev_page_temp = $_GET["prev"];

$mod_path					= array();
$mod_path['templates']		= TR_BASE_HREF . 'templates/';

$mod_path['templates_int']	= realpath(TR_INCLUDE_PATH		. '../templates').'/';
$mod_path['templates_sys']	= $mod_path['templates_int']	. 'system/';
$mod_path['page_template_dir']		= $mod_path['templates']		. 'page_templates/';

$mod_path['page_template_dir_int']	= $mod_path['templates_int']	. 'page_templates/';
$path = $mod_path['page_template_dir'] . $prev_page_temp . '/';
$path_int = $mod_path['page_template_dir_int'] . $prev_page_temp . '/';


//echo $path;

if(is_dir($path_int)) {
	
	$img_int = $path_int . 'screenshot.png';

	if(is_file($img_int)) {
		
		$img = $path . 'screenshot.png';
		
		echo '<div style="margin: 15px; margin-left: 40px;">';
		echo '<img src="'.$img.'" title="preview of the page template: '.$prev_page_temp.'" />';
		echo '</div>';
	}
	
	
	
} else {
	echo '<div><p style="color:red; font-size:13px;">The page template is not available!</p></div>';
}




//echo '<p>funziona</p>';


?>