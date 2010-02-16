<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');

global $_current_user;

$userCoursesDAO = new UserCoursesDAO();

if (isset($_GET['action'], $_GET['cid']) && $_SESSION['user_id'] > 0)
{
	$cid = intval($_GET['cid']);
	
	if ($_GET['action'] == 'remove') $userCoursesDAO->Delete($_SESSION['user_id'], $cid);
	if ($_GET['action'] == 'add') $userCoursesDAO->Create($_SESSION['user_id'], $cid, TR_USERROLE_VIEWER, 0);
}

// retrieve data to display
if ($_SESSION['user_id'] > 0) {
	$my_courses = $userCoursesDAO->getByUserID($_SESSION['user_id']); 
}

if (is_array($my_courses))
{
	$curr_page_num = intval($_GET['p']);
	if (!$curr_page_num) {
		$curr_page_num = 1;
	}	
	$savant->assign('courses', $my_courses);
	$savant->assign('curr_page_num', $curr_page_num);
	$savant->assign('title', _AT('my_courses'));
	
	$savant->display('home/index_course.tmpl.php');
}
else
{
	$savant->display('home/index_search.tmpl.php');
}
?>