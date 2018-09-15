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
define('TR_ClassCSRF_PATH', '../protection/csrf/');
define('TR_HTMLPurifier_PATH', '../protection/xss/htmlpurifier/library/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'lib/test_question_queries.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_ClassCSRF_PATH.'class_csrf.php');
require_once(TR_HTMLPurifier_PATH.'HTMLPurifier.auto.php');

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php');
	exit;
} else if ($_POST['submit']) {
	if (CSRF_Token::isValid() AND CSRF_Token::isRecent())
	{
	$_POST['feedback']    = $purifier->purify(trim($_POST['feedback']));
	$_POST['instructions'] = $purifier->purify(trim($_POST['instructions']));
	$_POST['category_id'] = intval($_POST['category_id']);

	for ($i = 0 ; $i < 10; $i++) {
		$_POST['question'][$i]        = $purifier->purify(trim($_POST['question'][$i]));
		$_POST['question_answer'][$i] = (int) $_POST['question_answer'][$i];
		$_POST['answer'][$i]          = $purifier->purify(trim($_POST['answer'][$i]));
	}

	if (!$_POST['question'][0] 
		|| !$_POST['question'][1] 
		|| !$_POST['answer'][0] 
		|| !$_POST['answer'][1]) {

		$msg->addError('QUESTION_EMPTY');
	}
	

	if (!$msg->containsErrors()) {
        $values = array($_POST['category_id'],
                            $_course_id,
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
	                        $_POST['answer'][9]
	                        );
	     $types = "iissssssssssssiiiiiiiiiissssssssss";
	    $sql = TR_SQL_QUESTION_MATCHING;                   
		if ($testsQuestionsDAO->execute($sql, $values, $types)) {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: question_db.php?_course_id='.$_course_id);
			exit;
		}
	} 
	} else
	{
		$msg->addError('INVALID_TOKEN');
	}
}

// for matching test questions
$_letters = array(_AT('a'), _AT('b'), _AT('c'), _AT('d'), _AT('e'), _AT('f'), _AT('g'), _AT('h'), _AT('i'), _AT('j'));

$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('letters', $_letters);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_matching.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); ?>
