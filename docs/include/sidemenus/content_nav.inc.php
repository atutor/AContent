<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');

global $_base_path;
global $savant;
global $contentManager, $_course_id;

ob_start();

echo '<div style="white-space:nowrap;">';

echo '<a href="'.$_base_path.'home/course/outline.php?_course_id='.$_course_id.'">'._AT('outline').'</a><br />';

/* @See classes/ContentManager.class.php	*/
$contentManager->printMainMenu();

echo '</div>';

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$coursesDAO = new CoursesDAO();
$course_row = $coursesDAO->get($_course_id);

$savant->assign('title', $course_row['title']);
//$savant->assign('title', _AT('content_navigation'));
$savant->display('include/box.tmpl.php');
?>