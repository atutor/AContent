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
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require(TR_INCLUDE_PATH.'../home/classes/StructureManager.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ForumsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ForumsCoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentForumsAssocDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');

global $_course_id;

$coursesDAO = new CoursesDAO();
$contentDAO = new ContentDAO();

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
				
				if(isset($_POST['_struct_name'])) {
		
					$struc_manag = new StructureManager($_POST['_struct_name']);
					$page_temp = $struc_manag->get_page_temp();
					
					createStruct($page_temp, $struc_manag, -1, $course_id);
					
					
			}
			
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



function createStruct($page_temp, $struc_manag, $id_folder, $course_id) {
		
		$contentDAO = new ContentDAO();
		$coursesDAO = new CoursesDAO();
		
		foreach ($page_temp as $page) {
			
			$max = $struc_manag->getMax($page);
			$min = $struc_manag->getMin($page);
			
			for($i=0; $i<$max; $i++) {
				
				// if $opt = '1' the page is optional
			    // else the page is mandatory
				$opt = 	($i < $min) ? 0 : 1; 
			
				$content_type = 0;
				if($struc_manag->isFolder($page))
					$content_type = 1;
				
				$body = $struc_manag->getBody($page);
				$title = $struc_manag->getTitle($page);
					
				if($id_folder == -1) {
					$content_id = $contentDAO->Create($course_id, 0, 1, 0, 1, null, null, $title, $body, null, 0, null, $content_type);
					
				} else {
					$content_id = $contentDAO->Create($course_id, 0, 1, 0, 1, null, null, $title, $body, null, 0, null, $content_type);
					$contentDAO->UpdateField($content_id, 'content_parent_id', $id_folder);
				}
				
				//update the field 'optional'
				$contentDAO->UpdateField($content_id, 'optional', $opt);
				
				//update the field 'structure'
				$coursesDAO->UpdateField($course_id, 'structure', $_POST['_struct_name']);
				
				
				if($struc_manag->isForum($page)) {
					$forums_dao = new ForumsDAO();
					$forum_course = new ForumsCoursesDAO();
					$forum_content = new ContentForumsAssocDAO();
					
					$forum_id = $forums_dao->Create($page, 'This is the description of the forum');
					$forum_content->Create($content_id, $forum_id);
					$forum_course->Create($forum_id, $course_id);
				} else if($struc_manag->isTest($page)) {
					$testsDAO = new TestsDAO();
					$test_ass_cont = new ContentTestsAssocDAO();
					
					$test_id = $testsDAO->Create($course_id, $page, 'This is the test description');
					$test_ass_cont->Create($content_id, $test_id);
					
				} else if($content_type == 1) {
					//the content is a folder
					$child = $struc_manag->getChild($page);
					$id_folder = $content_id;
					createStruct($child, $struc_manag, $id_folder, $course_id);
					$id_folder = -1;
				} 
			
			}
				
		}
	}
?>
