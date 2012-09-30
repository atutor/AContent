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

global $savant, $_course_id, $_content_id;

if ($_course_id > 0) {
	$side_menu[] = TR_INCLUDE_PATH.'sidemenus/content_nav.inc.php';
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
	// anonymous user
	$side_menu[] = TR_INCLUDE_PATH.'sidemenus/getting_start.inc.php';
	$side_menu[] = TR_INCLUDE_PATH.'sidemenus/category.inc.php';
} else {
	// authenticated user
	$side_menu[] = TR_INCLUDE_PATH.'sidemenus/my_courses.inc.php';
	$side_menu[] = TR_INCLUDE_PATH.'sidemenus/category.inc.php';
	// show templates menu
	
	//$side_menu[] = TR_INCLUDE_PATH.'sidemenus/page_template.inc.php';
	//$side_menu[] = TR_INCLUDE_PATH.'sidemenus/layout.inc.php';
	//catia
	//$side_menu[] = TR_INCLUDE_PATH.'sidemenus/structures.inc.php';
	
}

$savant->assign('side_menu', $side_menu);
$savant->display('include/side_menu.tmpl.php');

?>
