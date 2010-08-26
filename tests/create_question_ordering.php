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
require_once(TR_INCLUDE_PATH.'lib/test_question_queries.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php?_course_id='.$_course_id);
	exit;
} else if ($_POST['submit']) {
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
		$_POST['feedback'] = $addslashes($_POST['feedback']);
		$_POST['question'] = $addslashes($_POST['question']);
	
		$sql_params = array(	$_POST['category_id'], 
								$_course_id,
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
								$answer_new[0], 
								$answer_new[1], 
								$answer_new[2], 
								$answer_new[3], 
								$answer_new[4], 
								$answer_new[5], 
								$answer_new[6], 
								$answer_new[7], 
								$answer_new[8], 
								$answer_new[9]);
		$sql = vsprintf(TR_SQL_QUESTION_ORDERING, $sql_params);

		if ($testsQuestionsDAO->execute($sql)) {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: question_db.php?_course_id='.$_course_id);
			exit;
		}
		else
			$msg->addError('DB_NOT_UPDATED');
	}
}

$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_ordering.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); ?>