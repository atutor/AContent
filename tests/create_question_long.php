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

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php?_course_id='.$_course_id);
	exit;
} else if ($_POST['submit']) {
	if (CSRF_Token::isValid() AND CSRF_Token::isRecent())
	{
	$_POST['feedback']    = $purifier->purify(trim($_POST['feedback']));
	$_POST['question']    = $purifier->purify(trim($_POST['question']));
	$_POST['category_id'] = intval($_POST['category_id']);
	$_POST['properties']  = intval($_POST['properties']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}

	if (!$msg->containsErrors()) {

		$values = array($_POST['category_id'], 
								$_course_id,
								$_POST['feedback'], 
								$_POST['question'], 
								$_POST['properties']);
		$types = "iissi";
		$sql = TR_SQL_QUESTION_LONG;

		if ($testsQuestionsDAO->execute($sql, $values, $types))
		{
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

$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

if (!isset($_POST['properties'])) {
	$_POST['properties'] = 1;
}

$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_long.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); 
?>
