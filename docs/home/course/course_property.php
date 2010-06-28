<?php
/************************************************************************/
/* AContent                                                        									*/
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');

global $_course_id;

$coursesDAO = new CoursesDAO();

if ($_course_id > 0) {
	Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
} else {
	Utility::authenticate(TR_PRIV_ISAUTHOR);
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.TR_BASE_HREF.'home/course/index.php?_course_id='.$_course_id);
	exit;
}
else if($_POST['submit']){
		if (isset($_POST['hide_course']))
			$access = 'private';
		else
			$access = 'public';
		
	if ($_course_id > 0) { // update an existing course
		$coursesDAO->UpdateField($_course_id, 'title', $_POST['title']);
		$coursesDAO->UpdateField($_course_id, 'category_id', $_POST['category_id']);
		$coursesDAO->UpdateField($_course_id, 'primary_language', $_POST['pri_lang']);
		$coursesDAO->UpdateField($_course_id, 'description', $_POST['description']);
		$coursesDAO->UpdateField($_course_id, 'copyright', $_POST['copyright']);
		
		$coursesDAO->UpdateField($_course_id, 'access', $access);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
	else { // create a new course
		if ($course_id = $coursesDAO->Create($_SESSION['user_id'], 'top', $access, $_POST['title'], $_POST['description'], 
		                    null, null, null, $_POST['copyright'], $_POST['pri_lang'], null, null))
		{
			// create the course content directory
			$path = TR_CONTENT_DIR . $course_id . '/';
			@mkdir($path, 0700);
			
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: '.TR_BASE_HREF.'home/course/index.php?_course_id='.$course_id);
			exit;
		}
	}
}

// display
if ($_course_id > 0) {
	$savant->assign('course_id', $_course_id);
	$savant->assign('course_row', $coursesDAO->get($_course_id));
}

global $onload;
$onload = "document.form.title.focus();";
require(TR_INCLUDE_PATH.'header.inc.php'); 

$savant->display('home/course/course_property.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php'); 
?>