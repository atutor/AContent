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

define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsAssocDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();
$testsQuestionsAssocDAO = new TestsQuestionsAssocDAO();

$tid = intval($_REQUEST['tid']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php?_course_id='.$_course_id);
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['qid'] = explode(',', $_POST['qid']);

	foreach ($_POST['qid'] as $id) {
		$id = intval($id);

		if ($testsQuestionsDAO->Delete($id)) $testsQuestionsAssocDAO->DeleteByQuestionID($id);
	}

	$msg->addFeedback('QUESTION_DELETED');
	header('Location: question_db.php?_course_id='.$_course_id);
	exit;
} /* else: */

require_once(TR_INCLUDE_PATH.'header.inc.php');

$these_questions= explode(",", $_REQUEST['qid']);

foreach($these_questions as $this_question){
	$this_question = intval($this_question);
	$row = $testsQuestionsDAO->get($this_question);
	$confirm .= "<li>".$row['question']."</li>";
}

$confirm = array('DELETE', $confirm);
$hidden_vars['qid'] = $_REQUEST['qid'];
$hidden_vars['_course_id'] = $_course_id;

$msg->addConfirm($confirm, $hidden_vars);
$msg->printConfirm();

require_once(TR_INCLUDE_PATH.'footer.inc.php');
?>