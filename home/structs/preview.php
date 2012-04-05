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
$mod_path['dnd_themod']		= TR_BASE_HREF . 'dnd_themod/';

$mod_path['dnd_themod_int']	= realpath(TR_INCLUDE_PATH		. '../dnd_themod').'/';
$mod_path['dnd_themod_sys']	= $mod_path['dnd_themod_int']	. 'system/';
$mod_path['models_dir']		= $mod_path['dnd_themod']		. 'models/';

$mod_path['models_dir_int']	= $mod_path['dnd_themod_int']	. 'models/';
$path = $mod_path['models_dir'] . $prev_page_temp . '/';
$path_int = $mod_path['models_dir_int'] . $prev_page_temp . '/';


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