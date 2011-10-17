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
require (TR_INCLUDE_PATH.'vitals.inc.php');

global $_course_id, $contentManager;

Utility::authenticate(TR_PRIV_ISAUTHOR);

if (isset($_POST['move']) && isset($_POST['moved_cid'])) {
	$arr = explode('_', key($_POST['move']), 2);
	$new_pid = $arr[0];
	$new_ordering = $arr[1];

	$contentManager->moveContent($_POST['moved_cid'], $new_pid, $new_ordering);
	header('Location: '.TR_BASE_HREF.'home/editor/arrange_content.php?_course_id='.$_course_id);
	exit;
}
	
if (!defined('TR_INCLUDE_PATH')) { exit; }

$savant->assign('languageManager', $languageManager);
$savant->assign('course_id', $_course_id);

$savant->display('home/editor/arrange_content.tmpl.php');

?>
