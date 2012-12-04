<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CourseCategoriesDAO.class.php');


global $_current_user;

// clean up the session vars from the previous course
unset($_SESSION['course_id']);

$userCoursesDAO = new UserCoursesDAO();
$coursesDAO = new CoursesDAO();
$courseCategoriesDAO = new CourseCategoriesDAO();

if (isset($_GET['catid']) && trim($_GET['catid']) <> '') $catid = intval($_GET['catid']);

if (isset($_GET['action'], $_GET['cid']) && $_SESSION['user_id'] > 0)
{
	$cid = intval($_GET['cid']);
	
	if ($_GET['action'] == 'remove') $userCoursesDAO->Delete($_SESSION['user_id'], $cid);
	if ($_GET['action'] == 'add') $userCoursesDAO->Create($_SESSION['user_id'], $cid, TR_USERROLE_VIEWER, 0);
	
	$msg->addFeedback(ACTION_COMPLETED_SUCCESSFULLY);
}

// retrieve data to display
//if ($_SESSION['user_id'] > 0) {
//	$courses = $userCoursesDAO->getByUserID($_SESSION['user_id']);
//	$is_my_courses = true; 
//}

if (isset($catid)) {
	$courses = $coursesDAO->getByCategory($catid);
	$is_for_category = true;
} else {
	$courses = $coursesDAO->getByMostRecent();
}

// 22/11/2012

$name_struct=$_GET['stuid'];
if(isset($_GET['stuid'])){
    //die($name_struct); OK Competenze digitali
    $courses = $coursesDAO->getByStructure($name_struct);
}


require(TR_INCLUDE_PATH.'header.inc.php'); 

$curr_page_num = intval($_GET['p']);
if (!$curr_page_num) {
	$curr_page_num = 1;
}
$savant->assign('courses', $courses);
$savant->assign('categories', $courseCategoriesDAO->getAll());
$savant->assign('curr_page_num', $curr_page_num);
if ($is_for_category) {
	$savant->assign('title', _AT('search_results'));
} else {
	$savant->assign('title', _AT('most_recent_courses'));
}

$savant->display('home/index_course.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php'); 
?>