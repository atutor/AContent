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
require(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');

global $_current_user;

$coursesDAO = new CoursesDAO();
$userCoursesDAO = new UserCoursesDAO();

if (isset($_GET['action'], $_GET['cid']) && $_SESSION['user_id'] > 0)
{
	$cid = intval($_GET['cid']);
	
	if ($_GET['action'] == 'remove') $userCoursesDAO->Delete($_SESSION['user_id'], $cid);
}

// retrieve data to display
if ($_SESSION['user_id'] > 0) {
	// get my authoring courses
	$my_authoring_courses = $coursesDAO->getAuthoringCourses($_SESSION['user_id']);
	if (is_array($my_authoring_courses))
	{
		foreach ($my_authoring_courses as $course)
		{
			$course['my_own_course'] = 1;
			$my_courses[] = $course;
		}
	}
	
	// get courses that are authored by others
	$other_ppl_courses = $userCoursesDAO->getByUserID($_SESSION['user_id']); 
	if (is_array($other_ppl_courses))
	{
		foreach ($other_ppl_courses as $course)
		{
			$course['my_own_course'] = 0;
			$other_courses[] = $course;
		}
	}
	
	// merge my courses and other ppl's courses
	if (is_array($my_courses) && is_array($other_courses)) $courses = array_merge($my_courses, $other_courses);
	else if (is_array($my_courses)) $courses = $my_courses;
	else $courses = $other_courses;
}


if (is_array($courses))
{
	$curr_page_num = intval($_GET['p']);
	if (!$curr_page_num) {
		$curr_page_num = 1;
	}	
	$savant->assign('courses', $courses);
	$savant->assign('curr_page_num', $curr_page_num);
	$savant->assign('title', _AT('my_courses'));
	
	$savant->display('home/index_course.tmpl.php');
}
else
{
	$savant->display('home/index_search.tmpl.php');
}
?>