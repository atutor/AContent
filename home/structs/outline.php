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



global $_struct_name;

require(TR_INCLUDE_PATH.'header.inc.php');

/*
$mod_path					= array();
$mod_path['dnd_themod']		= realpath(TR_BASE_HREF			. 'dnd_themod').'/';
$mod_path['dnd_themod_int']	= realpath(TR_INCLUDE_PATH		. '../dnd_themod').'/';
$mod_path['dnd_themod_sys']	= $mod_path['dnd_themod_int']	. 'system/';
$mod_path['structs_dir']		= $mod_path['dnd_themod']		. 'structures/';
$mod_path['structs_dir_int']	= $mod_path['dnd_themod_int']	. 'structures/';

$path = $mod_path['structs_dir_int'] . $_struct_name;*/



require_once(TR_INCLUDE_PATH.'../home/classes/StructureManager.class.php');



include TR_INCLUDE_PATH. '../dnd_themod/system/'.'Struct.js';

	
$structs = explode("_", $_struct_name);

$flag_button = false;
$count = count($structs);
$i = 1;
foreach ($structs as $s) {
		if($i == $count)
			$flag_button = true;
			
		$structManager = new StructureManager($s);
		$structManager->printPreview($flag_button, $_struct_name);
		echo '</br>';
		$i++;
}
		



//$name = $structManager->getName();

//echo '<p>'.$name.'</p>';




/*if (isset($contentManager))
{
	echo '<p>';
	$contentManager->printSiteMapMenu();
	echo '</p>';
} else {
	echo '<p>';
	echo 'ciao';
	echo '</p>';
}*/


/*if (isset($contentManager))
{
	echo '<p>';
	$contentManager->printSiteMapMenu();
	echo '</p>';
}
else
{
	$msg->addError('MISSING_COURSE_ID');
	$msg->printAll();
}*/

require(TR_INCLUDE_PATH.'footer.inc.php');
?>