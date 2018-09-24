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

require_once(TR_INCLUDE_PATH.'classes/DAO/ForumsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ForumsCoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentForumsAssocDAO.class.php');

global $msg;

Utility::authenticate(TR_PRIV_ISAUTHOR);

$cid = $_POST['cid'];
$crid = $_POST['crid'];


$popup = intval($_GET['popup']);

require(TR_INCLUDE_PATH.'header.inc.php');

$forums_dao = new ForumsDAO();
$forum_course = new ForumsCoursesDAO();
$forum_content = new ContentForumsAssocDAO();

if(isset($_POST['create_forum'])) {

	$forum_id = $forums_dao->Create($_POST['title'], $_POST['body']);
	if($forum_id) {
		
		if($forum_content->Create($cid, $forum_id) & $forum_course->Create($forum_id, $crid)) {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			$msg->printFeedbacks();
		}
	} else {
		$msg->printErrors();
	}

} else if(isset($_POST['save'])) {
	$checks = $_POST['check'];
	
	$rows_forums_content = $forum_content->getByContent($cid);
	
	$forums_id = array();
	foreach ($rows_forums_content as $row_forum_content) {
		$forums_id[] =  $row_forum_content['forum_id'];
	}
	
	$new_ass = array_diff($checks, $forums_id);	
	
	if(count($checks) == 0)
		$del_ass = $forums_id;
	else
		$del_ass = array_diff($forums_id, $checks);
	


	
	
	foreach ($new_ass as $new) {
		if($forum_content->Create($cid, $new))
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		
	}
	

	
	foreach ($del_ass as $del) {
	
		if($forum_content->Delete($del, $cid))
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		
	}	
	
	
	$msg->printAll();
	

}

?>


