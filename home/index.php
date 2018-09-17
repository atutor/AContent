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
define('TR_HTMLPurifier_PATH', '../protection/xss/htmlpurifier/library/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CourseCategoriesDAO.class.php');
$_custom_head .= '<script type="text/javascript" src="home/js/misc.js"></script>';

global $_current_user;

// clean up the session vars from the previous course
unset($_SESSION['course_id']);

$userCoursesDAO = new UserCoursesDAO();
$coursesDAO = new CoursesDAO();
$courseCategoriesDAO = new CourseCategoriesDAO();

$catid = $_GET['catid'];
$name_struct = $_GET['stuid'];
$session_user_id = $_SESSION['user_id'];
$action = $_GET['action'];

$catid = (isset($catid) && trim($catid) <> '') ? intval($catid) : NULL;

if (isset($action, $_GET['cid']) && $session_user_id > 0) {
    $cid = intval($_GET['cid']);
    
    if ($action == 'remove') {
       $userCoursesDAO->Delete($session_user_id, $cid);
    } else if ($action == 'add') {
        $userCoursesDAO->Create($session_user_id, $cid, TR_USERROLE_VIEWER, 0);
    }
    
    $msg->addFeedback(ACTION_COMPLETED_SUCCESSFULLY);
}

unset($courses);
$courses = isset($catid) && $catid != 0  ? $coursesDAO->getByCategory($catid) : $coursesDAO->getByMostRecent();

// If the user is not an admin then we better filter out courses with empty content
if (!$session_user_id || ($session_user_id && $_current_user->isAdmin($session_user_id) != 1) && !empty($courses)) {
    foreach ($courses as $i => $course) {
        $course_user_id = $course['user_id'];
        $course_id = $course['course_id'];
        
        $user_role = isset($session_user_id) ? $userCoursesDAO->get($session_user_id, $course_id) : NULL;
        $user_role = isset($user_role) ? $user_role['role'] : NULL;
        
        // If the user is not the owner of the course or owner but not an author
        if ($course_user_id != $session_user_id || ($course_user_id == $session_user_id && $user_role != TR_USERROLE_AUTHOR)) {
            // Do the check that course should not be empty
            if (!$userCoursesDAO->hasContent($course_id)) {
               // unset($courses[$i]);
            }
        }
    }
    $courses = array_values($courses);
}

// 22/11/2012
if(isset($name_struct)){
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
$savant->assign('title', isset($catid) ? _AT('search_results') : _AT('most_recent_courses'));

$savant->display('home/index_course.tmpl.php');
//debug(MYSQLI_ENABLED);
require(TR_INCLUDE_PATH.'footer.inc.php'); 
?>
