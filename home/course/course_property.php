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
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH.'../home/classes/StructureManager.class.php');
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
			
			 if($_current_user->isAdmin()) { 
			 		$coursesDAO->UpdateField($_course_id, 'user_id', $_POST['this_author']);
			 }
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
		else 
		{ // create a new course
			if ($course_id = $coursesDAO->Create($_SESSION['user_id'], $_POST['category_id'],'top', $access, $_POST['title'], $_POST['description'], 
			                    null, null, null, $_POST['copyright'], $_POST['pri_lang'], null, null))
			{
				if(isset($_POST['_struct_name'])) {
					
					$structs = explode("_", $_POST['_struct_name']);
					
					foreach ($structs as $s) {
						$content_id = $contentDAO->Create($course_id, 0, 1, 0, 1, null, null, $s, 'null', null, 0, null, 1);
						
						$struc_manag = new StructureManager($s);
						$page_temp = $struc_manag->get_page_temp();
						$struc_manag->createStruct($page_temp, $content_id , $course_id);
					}
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
// get a list of authors if admin is creating a lesson	
$dao = new DAO();
if($_current_user->isAdmin()){
	$sql = "SELECT user_id, login, first_name, last_name FROM ".TABLE_PREFIX."users WHERE is_author = '1'";
	$user_rows = $dao->execute($sql);;
}
$savant->assign('isauthor', $user_rows);

global $onload;
$onload = "document.form.title.focus();";
require(TR_INCLUDE_PATH.'header.inc.php'); 

$savant->display('home/course/course_property.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php');





?>
