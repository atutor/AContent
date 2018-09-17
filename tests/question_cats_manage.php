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

$page = 'tests';
define('TR_INCLUDE_PATH', '../include/');
define('TR_ClassCSRF_PATH', '../protection/csrf/');
define('TR_HTMLPurifier_PATH', '../protection/xss/htmlpurifier/library/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsCategoriesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_ClassCSRF_PATH.'class_csrf.php');
require_once(TR_HTMLPurifier_PATH.'HTMLPurifier.auto.php');

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

$testsQuestionsCategoriesDAO = new TestsQuestionsCategoriesDAO();

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_cats.php');
	exit;
} else if (isset($_POST['submit'])) {
	if (CSRF_Token::isValid() AND CSRF_Token::isRecent())
	{
		$_POST['title'] = $purifier->purify(trim($_POST['title']));

		if (!empty($_POST['title']) && !isset($_POST['catid'])) {
		if ($testsQuestionsCategoriesDAO->Create($_course_id, $_POST['title']))
		{
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: question_cats.php?_course_id='.$_course_id);
			exit;
		}
	} else if (!empty($_POST['title']) && isset($_POST['catid']))  {
		if ($testsQuestionsCategoriesDAO->Update($_POST['catid'], $_POST['title']))
		{
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: question_cats.php?_course_id='.$_course_id);
			exit;
		}
	} else {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}
	} else
	{
		$msg->addError('INVALID_TOKEN');
	}
}

if (isset($_GET['catid'])) {
	$row = $testsQuestionsCategoriesDAO->get($_GET['catid']);
	$_POST['title'] = $row['title'];
}

$onload = "document.form.title.focus();";
require_once(TR_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

if (isset($_GET['catid'])) 
{
	$savant->assign('catid', $_GET['catid']);
	$savant->assign('title', _AT('manage_category'));
}
else 
{
	$savant->assign('title', _AT('create_category'));
}
$savant->assign('course_id', $_course_id);
$savant->display('tests/question_cats_manage.tmpl.php');

require_once(TR_INCLUDE_PATH.'footer.inc.php'); 

?>
