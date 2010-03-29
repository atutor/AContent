<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'../tests/lib/test_question_queries.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();

if (isset($_POST['cancel']) || isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php?_course_id='.$_course_id);
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['required'] = intval($_POST['required']);
	$_POST['feedback'] = trim($_POST['feedback']);
	$_POST['question'] = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);
	$_POST['answer']      = intval($_POST['answer']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}
		
	if (!$msg->containsErrors()) {
		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
		}

		$answers = array_fill(0, 10, 0);
		$answers[$_POST['answer']] = 1;

		$_POST['feedback']   = $addslashes($_POST['feedback']);
		$_POST['question']   = $addslashes($_POST['question']);

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
								$answers[0], 
								$answers[1], 
								$answers[2], 
								$answers[3], 
								$answers[4], 
								$answers[5], 
								$answers[6], 
								$answers[7], 
								$answers[8], 
								$answers[9]);
		$sql = vsprintf(TR_SQL_QUESTION_MULTI, $sql_params);
		
		if ($testsQuestionsDAO->execute($sql)) {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: question_db.php?_course_id='.$_course_id);
			exit;
		}
	}
} else {
	$_POST['answer'] = 0;
}

$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

$msg->printConfirm();

$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_multichoice.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); ?>