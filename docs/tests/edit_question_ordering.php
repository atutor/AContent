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
	$missing_fields = array();

	$_POST['feedback']    = trim($_POST['feedback']);
	$_POST['question']    = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);

	if ($_POST['question'] == ''){
		$missing_fields[] = _AT('question');
	}

	if (trim($_POST['choice'][0]) == '') {
		$missing_fields[] = _AT('item').' 1';
	}
	if (trim($_POST['choice'][1]) == '') {
		$missing_fields[] = _AT('item').' 2';
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}
	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);
		$_POST['feedback'] = $addslashes($_POST['feedback']);

		$choice_new = array(); // stores the non-blank choices
		$answer_new = array(); // stores the non-blank answers
		$order = 0; // order count
		for ($i=0; $i<10; $i++) {
			/**
			 * Db defined it to be 255 length, chop strings off it it's less than that
			 * @harris
			 */
			$_POST['choice'][$i] = Utility::validateLength($_POST['choice'][$i], 255);
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));

			if ($_POST['choice'][$i] != '') {
				/* filter out empty choices/ remove gaps */
				$choice_new[] = $_POST['choice'][$i];
				$answer_new[] = $order++;
			}
		}
		
		$_POST['choice']   = array_pad($choice_new, 10, '');
		$answer_new        = array_pad($answer_new, 10, 0);

		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET
			category_id=$_POST[category_id],
			feedback='$_POST[feedback]',
			question='$_POST[question]',
			choice_0='{$_POST[choice][0]}',
			choice_1='{$_POST[choice][1]}',
			choice_2='{$_POST[choice][2]}',
			choice_3='{$_POST[choice][3]}',
			choice_4='{$_POST[choice][4]}',
			choice_5='{$_POST[choice][5]}',
			choice_6='{$_POST[choice][6]}',
			choice_7='{$_POST[choice][7]}',
			choice_8='{$_POST[choice][8]}',
			choice_9='{$_POST[choice][9]}',
			answer_0=$answer_new[0],
			answer_0=$answer_new[1],
			answer_0=$answer_new[2],
			answer_0=$answer_new[3],
			answer_0=$answer_new[4],
			answer_0=$answer_new[5],
			answer_0=$answer_new[6],
			answer_0=$answer_new[7],
			answer_0=$answer_new[8],
			answer_0=$answer_new[9]

			WHERE question_id=$_POST[qid]";
		
		if ($testsQuestionsDAO->execute($sql)) {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
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
} else {
	if (!($row = $testsQuestionsDAO->get($qid))){
		require_once(TR_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (TR_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$_POST['required']		= $row['required'];
	$_POST['question']		= $row['question'];
	$_POST['category_id']	= $row['category_id'];
	$_POST['feedback']		= $row['feedback'];

	for ($i=0; $i<10; $i++) {
		$_POST['choice'][$i] = $row['choice_'.$i];
	}
}

$onload = 'document.form.category_id.focus();';
require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('qid', $qid);
$savant->assign('tid', $_REQUEST['tid']);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_ordering.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php');  ?>