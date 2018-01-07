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
	$_POST['required']     = 1; //intval($_POST['required']);
	$_POST['feedback']     = trim($_POST['feedback']);
	$_POST['question']     = trim($_POST['question']);
	$_POST['category_id']  = intval($_POST['category_id']);
	$_POST['answer']       = intval($_POST['answer']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('statement')));
	}

	if (!$msg->containsErrors()) {
		$sql = TR_SQL_QUESTION_TRUEFALSE;
		$values = array($_POST['category_id'], 
		                            $_course_id, 
		                            $_POST['feedback'], 
		                            $_POST['question'],
		                            $_POST['answer']);
		$types = "iisss";
		if ($testsQuestionsDAO->execute($sql, $values, $types)) {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: question_db.php?_course_id='.$_course_id);
		}
		else
			$msg->addError('DB_NOT_UPDATED');
	}
}

$onload = 'document.form.category_id.focus();';
require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_truefalse.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); ?>