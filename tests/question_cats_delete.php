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

$page = 'tests';
define('TR_INCLUDE_PATH', '../include/');

require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsCategoriesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

$testsQuestionsCategoriesDAO = new TestsQuestionsCategoriesDAO();

if (isset($_POST['submit_yes'])) {
	$_POST['catid'] = intval($_POST['catid']);

	$testsQuestionsDAO = new TestsQuestionsDAO();
	//remove category
	if ($testsQuestionsCategoriesDAO->Delete($_POST['catid']) && $testsQuestionsDAO->UpdateField($_POST['catid'], 'category_id', 0))
	{
		//set all qestions that use this category to have category=0
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.TR_BASE_HREF.'tests/question_cats.php?_course_id='.$_course_id);
		exit;
	}

} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.TR_BASE_HREF.'tests/question_cats.php?_course_id='.$_course_id);
	exit;
} else if (!isset($_GET['catid'])) {
	require_once(TR_INCLUDE_PATH.'header.inc.php');
	$msg->addError('ITEM_NOT_FOUND');
	$msg->printErrors();
	require_once(TR_INCLUDE_PATH.'footer.inc.php');
	exit;
} 

require_once(TR_INCLUDE_PATH.'header.inc.php');

$_GET['catid'] = intval($_GET['catid']);

$row = $testsQuestionsCategoriesDAO->get($_GET['catid']);

$hidden_vars['catid'] = $_GET['catid'];
$hidden_vars['_course_id'] = $_course_id;

$msg->addConfirm(array('DELETE_TEST_CATEGORY', $row['title']), $hidden_vars);
	
$msg->printConfirm();

require_once(TR_INCLUDE_PATH.'footer.inc.php');
?>
