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

global $_content_id, $_content_id, $contentManager;

Utility::authenticate(TR_PRIV_ISAUTHOR);

$cid = $_GET['cid'] = $_content_id;

if (isset($_POST['submit_yes'])) {

	$cid = intval($_POST['_cid']);

	$result = $contentManager->deleteContent($cid);

	unset($_SESSION['s_cid']);
	unset($_SESSION['from_cid']);
		
	$msg->addFeedback('CONTENT_DELETED');
	header('Location: '.TR_BASE_HREF.'home/course/index.php?_course_id='.$_course_id);
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	$cid = intval($_POST['_cid']);
	$row = $contentManager->getContentPage($cid);
	if ($row['content_type'] == CONTENT_TYPE_FOLDER) {
		header('Location: '.TR_BASE_HREF.'home/editor/edit_content_folder.php?_cid='.$cid);
	} else {
		header('Location: '.TR_BASE_HREF.'home/course/content.php?_cid='.$cid);
	}
	exit;
}

$path	= $contentManager->getContentPath($cid);
require(TR_INCLUDE_PATH.'header.inc.php');

if ($_GET['cid'] == 0) {
	$msg->printErrors('ID_ZERO');
	require(TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$children = $contentManager->getContent($_GET['cid']);

$hidden_vars['_cid'] = $_GET['cid'];

if (is_array($children) && (count($children)>0) ) {
	$msg->addConfirm('SUB_CONTENT_DELETE', $hidden_vars);
//	$msg->addConfirm('GLOSSARY_REMAINS', $hidden_vars);
//} else {
//	$msg->addConfirm('GLOSSARY_REMAINS', $hidden_vars);
}
	
$row = $contentManager->getContentPage($_GET['cid']);
$title = $row['title'];

$msg->addConfirm(array('DELETE', $title),  $hidden_vars);
$msg->printConfirm();
	
require(TR_INCLUDE_PATH.'footer.inc.php');
?>