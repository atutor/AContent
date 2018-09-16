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
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_ClassCSRF_PATH.'class_csrf.php');
require_once(TR_HTMLPurifier_PATH.'HTMLPurifier.auto.php');

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

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
	$_POST['feedback']    = $purifier->purify(trim($_POST['feedback']));
	$_POST['question']    = $purifier->purify(trim($_POST['question']));
	$_POST['category_id'] = intval($_POST['category_id']);
	$_POST['properties']  = intval($_POST['properties']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}

	if (!$msg->containsErrors()) {
		if (CSRF_Token::isValid() AND CSRF_Token::isRecent())
		{
			$_POST['question'] = addslashes($_POST['question']);
		$_POST['feedback'] = addslashes($_POST['feedback']);
/*
		$sql = "UPDATE ".TABLE_PREFIX."tests_questions SET	category_id=$_POST[category_id],
			feedback='$_POST[feedback]',
			question='$_POST[question]',
			properties=$_POST[properties]
		WHERE question_id=$_POST[qid]"; */
		$sql = "UPDATE ".TABLE_PREFIX."tests_questions SET	category_id=?,
			feedback=?,
			question=?,
			properties=?
		WHERE question_id=?";
		$values = array($_POST['category_id'], 
		                        $_POST['feedback'], 
		                        $_POST['question'], 
		                        $_POST['properties'], 
		                        $_POST['qid'] );
		$types = "issii";
		$testsQuestionsDAO->execute($sql, $values, $types);

		$msg->addFeedback('QUESTION_UPDATED');
		if ($_POST['tid']) {
			header('Location: questions.php?tid='.$_POST['tid'].'&_course_id='.$_course_id);			
		} else {
			header('Location: question_db.php?_course_id='.$_course_id);
		}
		exit;
		} else
		{
			$msg->addError('INVALID_TOKEN');
		}
	}
}

if (!isset($_POST['submit'])) {
	if (!($row = $testsQuestionsDAO->get($qid))){
		$msg->printErrors('ITEM_NOT_FOUND');
		require (TR_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$_POST	= $row;
}

require_once(TR_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

$savant->assign('qid', $qid);
$savant->assign('tid', $_REQUEST['tid']);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_long.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); 
?>
