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

$page = 'tests';
define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsAssocDAO.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

$tid = intval($_REQUEST['tid']);
$qid = intval($_REQUEST['qid']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: questions.php?tid=' . $tid.SEP.'_course_id='.$_course_id);
	exit;
} else if (isset($_POST['submit_yes'])) {
	$testsQuestionsAssocDAO = new TestsQuestionsAssocDAO();
	$testsQuestionsAssocDAO->Delete($tid, $qid);
	$msg->addFeedback('QUESTION_REMOVED');
	header('Location: questions.php?tid=' . $tid.SEP.'_course_id='.$_course_id);
	exit;

} /* else: */

$_pages['tests/questions.php?tid='.$_GET['tid']]['title_var']    = 'questions';
$_pages['tests/questions.php?tid='.$_GET['tid']]['parent']   = 'tests/index.php';
$_pages['tests/questions.php?tid='.$_GET['tid']]['children'] = array('tests/add_test_questions.php?tid='.$_GET['tid']);

$_pages['tests/add_test_questions.php?tid='.$_GET['tid']]['title_var']    = 'add_questions';
$_pages['tests/add_test_questions.php?tid='.$_GET['tid']]['parent']   = 'tests/questions.php?tid='.$_GET['tid'];

$_pages['tests/question_remove.php']['title_var'] = 'remove_question';
$_pages['tests/question_remove.php']['parent']    = 'tests/questions.php?tid='.$_GET['tid'];

require_once(TR_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);
$hidden_vars['qid'] = $_GET['qid'];
$hidden_vars['tid'] = $_GET['tid'];
$hidden_vars['_course_id'] = $_course_id;
$msg->addConfirm('REMOVE_TEST_QUESTION', $hidden_vars);

$msg->printConfirm();

require_once(TR_INCLUDE_PATH.'footer.inc.php');
?>