<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

global $savant;
global $_base_path, $_course_id;
global $framed, $popup;

if (!defined('TR_INCLUDE_PATH')) { exit; }

// get course copyright
if ($_course_id > 0)
{
	require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
	$coursesDAO = new CoursesDAO();
	$course_row = $coursesDAO->get($_course_id);
	if ($course_row['copyright'] <> '') $savant->assign('course_copyright', $course_row['copyright']);
}

$savant->assign('course_id', $_course_id);
$savant->assign('base_path', $_base_path);
$savant->assign('theme', $_SESSION['prefs']['PREF_THEME']);

if ($framed || $popup) {
	$savant->display('include/fm_footer.tmpl.php');
}
else {
	$savant->display('include/footer.tmpl.php');
}
?>
