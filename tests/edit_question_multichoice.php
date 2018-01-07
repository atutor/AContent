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

define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();

$qid = intval($_GET['qid']);
if ($qid == 0){
	$qid = intval($_POST['qid']);
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	if ($_POST['tid']) {
		header('Location: questions.php?tid='.$_POST['tid'].'&_course_id='.$_course_id);			
	} else {
		header('Location: question_db.php?_course_id='.$_course_id);
	}
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['feedback'] = trim($_POST['feedback']);
	$_POST['question'] = trim($_POST['question']);
	$_POST['tid']	   = intval($_POST['tid']);
	$_POST['qid']	   = intval($_POST['qid']);
	$_POST['weight']   = intval($_POST['weight']);
	$_POST['answer']   = intval($_POST['answer']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}

	if (!$msg->containsErrors()) {
		$answers    = array_fill(0, 10, 0);
		$answers[$_POST['answer']] = 1;

		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = trim($_POST['choice'][$i]);
		}
				$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET
                            category_id=?,
                            feedback=?,
                            question=?,
                            choice_0=?,
                            choice_1=?,
                            choice_2=?,
                            choice_3=?,
                            choice_4=?,
                            choice_5=?,
                            choice_6=?,
                            choice_7=?,
                            choice_8=?,
                            choice_9=?,
                            answer_0=?,
                            answer_1=?,
                            answer_2=?,
                            answer_3=?,
                            answer_4=?,
                            answer_5=?,
                            answer_6=?,
                            answer_7=?,
                            answer_8=?,
                            answer_9=?
                            WHERE question_id=?";	
		$values= array($_POST['category_id'],
		                    $_POST['feedback'],
		                    $_POST['question'],
		                    $_POST['choice'][0],
		                    $_POST['choice'][1],
		                    $_POST['choice'][2],
		                    $_POST['choice'][3],
		                    $_POST['choice'][4],
		                    $_POST['choice'][5],
		                    $_POST['choice'][6],
		                    $_POST['choice'][7],
		                    $_POST['choice'][8],
		                    $_POST['choice'][9],
		                    $answers[0],
		                    $answers[0],
		                    $answers[0],
		                    $answers[0],
		                    $answers[0],
		                    $answers[0],
		                    $answers[0],
		                    $answers[0],
		                    $answers[0],
		                    $answers[0],
		                    $_POST['qid']);
		$types = "issssssssssssiiiiiiiiiii";
		if ($testsQuestionsDAO->execute($sql, $values, $types)) {
			$msg->addFeedback('QUESTION_UPDATED');
			if ($_POST['tid']) {
				header('Location: questions.php?tid='.$_POST['tid'].'&_course_id='.$_course_id);			
			} else {
				header('Location: question_db.php?_course_id='.$_course_id);
			}
			exit;
		}
		else
			$msg->addError('DB_NOT_UPDATED');
	}
}

if (!isset($_POST['submit'])) {
	if (!($row = $testsQuestionsDAO->get($qid))){
		require_once(TR_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (TR_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$_POST['category_id'] = $row['category_id'];
	$_POST['feedback']	  = $row['feedback'];
	$_POST['weight']	  = $row['weight'];
	$_POST['question']	  = $row['question'];

	for ($i=0; $i<10; $i++) {
		$_POST['choice'][$i] = $row['choice_'.$i];
		$_POST['answer'][$i] = $row['answer_'.$i];
	}
}

$onload = 'document.form.category_id.focus();';
require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('qid', $qid);
$savant->assign('tid', $_REQUEST['tid']);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_multichoice.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); 
?>