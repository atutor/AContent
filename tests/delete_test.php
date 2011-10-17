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
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsAssocDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
	
$testsDAO = new TestsDAO();

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?_course_id='.$_course_id);
	exit;
} else if (isset($_POST['submit_yes'])) {
	
	$tid = intval($_POST['tid']);

	if ($testsDAO->Delete($tid)) {
		$testsQuestionsAssocDAO = new TestsQuestionsAssocDAO();
		$testsQuestionsAssocDAO->DeleteByTestID($tid);

		//delete test content association as well
		$contentTestsAssocDAO = new ContentTestsAssocDAO();
		$contentTestsAssocDAO->DeleteByTestID($tid);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.TR_BASE_HREF.'tests/index.php?_course_id='.$_course_id);
	exit;

} /* else: */

require_once(TR_INCLUDE_PATH.'header.inc.php');

$_GET['tid'] = intval($_GET['tid']);

$row = $testsDAO->get($_GET['tid']);

unset($hidden_vars);
$hidden_vars['tid'] = $_GET['tid'];
$hidden_vars['_course_id'] = $_course_id;

$msg->addConfirm(array('DELETE_TEST', $row['title']), $hidden_vars);
$msg->printConfirm();

require_once(TR_INCLUDE_PATH.'footer.inc.php');
?>