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

define('TR_INCLUDE_PATH', '../../include/');
define('TR_HTMLPurifier_PATH', '../../protection/xss/htmlpurifier/library/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_HTMLPurifier_PATH.'HTMLPurifier.auto.php');

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

$coursesDAO = new CoursesDAO();
$course_info = $coursesDAO->get($_course_id);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.TR_BASE_HREF.'home/course/index.php?_course_id='.$_course_id);
	exit;
} else if (isset($_POST['step']) && ($_POST['step'] == 2) && isset($_POST['submit_yes'])) {
	$coursesDAO->Delete($_course_id);
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.TR_BASE_HREF.'home/index.php');
	exit;
}

require(TR_INCLUDE_PATH.'header.inc.php'); 

if (!isset($_POST['step'])) {
	$hidden_vars['step'] = 1;
	$hidden_vars['_course_id'] = $_course_id;
	$msg->addConfirm(array('DELETE_COURSE_1', $purifier->purify($course_info['title'])), $hidden_vars);
	$msg->printConfirm();
} else if ($_POST['step'] == 1) {
	$hidden_vars['step'] = 2;
	$hidden_vars['_course_id'] = $_course_id;
	$msg->addConfirm(array('DELETE_COURSE_2', $purifier->purify($course_info['title'])), $hidden_vars);
	$msg->printConfirm();
}

require(TR_INCLUDE_PATH.'footer.inc.php'); 
?>
