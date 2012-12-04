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
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsCategoriesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id, $msg;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

if (isset($_POST['edit'], $_POST['category'])) {

	header('Location: question_cats_manage.php?catid='.$_POST['category'].'&_course_id='.$_course_id);
	exit;
} else if (isset($_POST['delete'], $_POST['category'])) {
	header('Location: question_cats_delete.php?catid='.$_POST['category'].'&_course_id='.$_course_id);
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require_once(TR_INCLUDE_PATH.'header.inc.php');

$testsQuestionsCategories = new TestsQuestionsCategoriesDAO();
$rows = $testsQuestionsCategories->getByCourseID($_course_id);

$savant->assign('course_id', $_course_id);
$savant->assign('rows', $rows);
$savant->assign('msg', $msg);

$savant->display('tests/question_cats.tmpl.php');

require_once(TR_INCLUDE_PATH.'footer.inc.php'); ?>