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
require_once(TR_INCLUDE_PATH.'../tests/lib/likert_presets.inc.php');
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
	$_POST['question']    = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);
	$_POST['alignment']   = intval($_POST['alignment']);

	$empty_fields = array();
	if ($_POST['question'] == ''){
		$empty_fields[] = _AT('question');
	}
	if ($_POST['choice'][0] == '') {
		$empty_fields[] = _AT('choice').' 1';
	}

	if ($_POST['choice'][1] == '') {
		$empty_fields[] = _AT('choice').' 2';
	}

	if (!empty($empty_fields)) {
		$msg->addError(array('EMPTY_FIELDS', implode(', ', $empty_fields)));
	}

	if (!$msg->containsErrors()) {
		$_POST['question'] = addslashes($_POST['question']);

		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = addslashes(trim($_POST['choice'][$i]));
			$_POST['answer'][$i] = intval($_POST['answer'][$i]);

			if ($_POST['choice'][$i] == '') {
				/* an empty option can't be correct */
				$_POST['answer'][$i] = 0;
			}
		}		
		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET
			category_id=$_POST[category_id],
			feedback='',
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
			answer_0={$_POST[answer][0]},
			answer_1={$_POST[answer][1]},
			answer_2={$_POST[answer][2]},
			answer_3={$_POST[answer][3]},
			answer_4={$_POST[answer][4]},
			answer_5={$_POST[answer][5]},
			answer_6={$_POST[answer][6]},
			answer_7={$_POST[answer][7]},
			answer_8={$_POST[answer][8]},
			answer_9={$_POST[answer][9]}
			WHERE question_id=$_POST[qid]";
		$testsQuestionsDAO->execute($sql);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		if ($_POST['tid']) {
			header('Location: questions.php?tid='.$_POST['tid'].'&_course_id='.$_course_id);			
		} else {
			header('Location: question_db.php?_course_id='.$_course_id);
		}
		exit;
	}
} else if (isset($_POST['preset'])) {
	// load preset
	$_POST['preset_num'] = intval($_POST['preset_num']);

	if (isset($_likert_preset[$_POST['preset_num']])) {
		$_POST['choice'] = $_likert_preset[$_POST['preset_num']];
	} else if ($_POST['preset_num']) {
		if ($row = $testsQuestionsDAO->get($_POST['preset_num'])){
			for ($i=0; $i<10; $i++) {
				$_POST['choice'][$i] = $row['choice_' . $i];
			}
		}
	}
} else {
	if (!($row = $testsQuestionsDAO->get($qid))){
		require_once(TR_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (TR_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	$_POST['question']		= $row['question'];
	$_POST['category_id']	= $row['category_id'];

	for ($i=0; $i<10; $i++) {
		$_POST['choice'][$i] = $row['choice_'.$i];
	}
}

global $onload;
$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('qid', $qid);
$savant->assign('tid', $_REQUEST['tid']);
$savant->assign('likert_preset', $_likert_preset);
$savant->assign('testsQuestionsDAO', $testsQuestionsDAO);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_likert.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php');  ?>