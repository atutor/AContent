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

	$_POST['question'] = trim($_POST['question']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('statement')));
	}

	if (!$msg->containsErrors()) {
		$_POST['feedback']    = $addslashes(trim($_POST['feedback']));
		$_POST['question']    = $addslashes($_POST['question']);
		$_POST['qid']	      = intval($_POST['qid']);
		$_POST['category_id'] = intval($_POST['category_id']);
		$_POST['answer']      = intval($_POST['answer']);

		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET	category_id=$_POST[category_id],
			feedback='$_POST[feedback]',
			question='$_POST[question]',
			answer_0={$_POST[answer]}
			WHERE question_id=$_POST[qid]";

		if ($testsQuestionsDAO->execute($sql)) {
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

if (!$_POST['submit']) {
	if (!($row = $testsQuestionsDAO->get($qid))){
		$msg->printErrors('ITEM_NOT_FOUND');
		require (TR_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$_POST	= $row;
}

if ($_POST['answer'] == '') {
	if ($_POST['answer_0'] == 1) {
		$ans_yes = ' checked="checked"';
	} else if ($_POST['answer_0'] == 2){
		$ans_no  = ' checked="checked"';
	} else if ($_POST['answer_0'] == 3) {
		$ans_yes1 = ' checked="checked"';
	} else {
		$ans_no1  = ' checked="checked"';
	}
} else {
	if ($_POST['answer'] == 1) {
		$ans_yes = ' checked="checked"';
	} else if($_POST['answer'] == 2){
		$ans_no  = ' checked="checked"';
	} else if ($_POST['answer'] == 3) {
		$ans_yes1 = ' checked="checked"';
	} else {
		$ans_no1  = ' checked="checked"';
	}
}

$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php'); 

$savant->assign('qid', $qid);
$savant->assign('tid', $_REQUEST['tid']);
$savant->assign('ans_yes', $ans_yes);
$savant->assign('ans_no', $ans_no);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_truefalse.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); ?>