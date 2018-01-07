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
//require_once(TR_INCLUDE_PATH.'../tests/lib/likert_presets.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();

// for matching test questions
$_letters = array(_AT('a'), _AT('b'), _AT('c'), _AT('d'), _AT('e'), _AT('f'), _AT('g'), _AT('h'), _AT('i'), _AT('j'));

$qid = intval($_GET['qid']);
if ($qid == 0){
	$qid = intval($_POST['qid']);
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	if (isset($_POST['tid'])) {
		header('Location: questions.php?tid='.$_POST['tid'].'&_course_id='.$_course_id);			
	} else {
		header('Location: question_db.php?_course_id='.$_course_id);
	}
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['tid']          = intval($_POST['tid']);
	$_POST['qid']          = intval($_POST['qid']);
	$_POST['feedback']     = trim($_POST['feedback']);
	$_POST['instructions'] = trim($_POST['instructions']);
	$_POST['category_id']  = intval($_POST['category_id']);

	for ($i = 0 ; $i < 10; $i++) {
		$_POST['question'][$i]        = trim($_POST['question'][$i]);
		$_POST['question_answer'][$i] = (int) $_POST['question_answer'][$i];
		$_POST['answer'][$i]          = trim($_POST['answer'][$i]);
	}

	if (!$_POST['question'][0] 
		|| !$_POST['question'][1] 
		|| !$_POST['answer'][0] 
		|| !$_POST['answer'][1]) {

		$msg->addError('QUESTION_EMPTY');
	}

	if (!$msg->containsErrors()) {
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
                            answer_9=?,
                            option_0=?,
                            option_1=?,
                            option_2=?,
                            option_3=?,
                            option_4=?,
                            option_5=?,
                            option_6=?,
                            option_7=?,
                            option_8=?,
                            option_9=?
                            WHERE question_id=?";
	    $values = array($_POST['category_id'],
	                        $_POST['feedback'],
	                        $_POST['instructions'],
	                        $_POST['question'][0],
	                        $_POST['question'][1],
	                        $_POST['question'][2],
	                        $_POST['question'][3],
	                        $_POST['question'][4],
	                        $_POST['question'][5],
	                        $_POST['question'][6],
	                        $_POST['question'][7],
	                        $_POST['question'][8],
	                        $_POST['question'][9],
	                        $_POST['question_answer'][0],
	                        $_POST['question_answer'][1],
	                        $_POST['question_answer'][2],
	                        $_POST['question_answer'][3],
	                        $_POST['question_answer'][4],
	                        $_POST['question_answer'][5],
	                        $_POST['question_answer'][6],
	                        $_POST['question_answer'][7],
	                        $_POST['question_answer'][8],
	                        $_POST['question_answer'][9],
	                        $_POST['answer'][0],
	                        $_POST['answer'][1],
	                        $_POST['answer'][2],
	                        $_POST['answer'][3],
	                        $_POST['answer'][4],
	                        $_POST['answer'][5],
	                        $_POST['answer'][6],
	                        $_POST['answer'][7],
	                        $_POST['answer'][8],
	                        $_POST['answer'][9],
	                        $_POST['qid']
	                        );
	    $types = "issssssssssssiiiiiiiiiissssssssssi";
		if ($testsQuestionsDAO->execute($sql, $values, $types)) {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			if ($_POST['tid']) {
				header('Location: questions.php?tid='.$_POST['tid'].'&_course_id='.$_course_id);			
			} else {
				header('Location: question_db.php?_course_id='.$_course_id);
			}
			exit;
		}
	}
} else {
	if (!($row = $testsQuestionsDAO->get($qid))){
		require_once(TR_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (TR_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$_POST['feedback']		= $row['feedback'];
	$_POST['instructions']	= $row['question'];
	$_POST['category_id']	= $row['category_id'];

	for ($i=0; $i<10; $i++) {
		$_POST['question'][$i]        = $row['choice_'.$i];
		$_POST['question_answer'][$i] = $row['answer_'.$i];
		$_POST['answer'][$i]          = $row['option_'.$i];
	}
	
}
$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('qid', $qid);
$savant->assign('tid', $_REQUEST['tid']);
$savant->assign('letters', $_letters);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_matchingdd.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php');  ?>