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
require_once(TR_INCLUDE_PATH.'classes/testQuestions.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsCategoriesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsAssocDAO.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsDAO = new TestsDAO();
$testsQuestionsCategoriesDAO = new TestsQuestionsCategoriesDAO();
$testsQuestionsAssocDAO = new TestsQuestionsAssocDAO();

$_pages['tests/questions.php']['title_var']    = 'questions';
$_pages['tests/questions.php']['parent']   = 'tests/index.php';
$_pages['tests/questions.php']['children'] = array('tests/add_test_questions.php?tid='.$_GET['tid'].'&_course_id='.$_course_id);

$_pages['tests/add_test_questions.php?tid='.$_GET['tid'].'&_course_id='.$_course_id]['title_var']    = 'add_questions';
$_pages['tests/add_test_questions.php?tid='.$_GET['tid'].'&_course_id='.$_course_id]['parent']   = 'tests/questions.php?tid='.$_GET['tid'].'&_course_id='.$_course_id;

$_pages['tests/questions.php']['guide']    = 'instructor/?p=add_questions.php';

$tid = intval($_REQUEST['tid']);

if (isset($_POST['submit'])) {
	$count = 1;
	foreach ($_POST['weight'] as $qid => $weight) {
		$qid    = intval($qid);
		$weight = intval($weight);

		$orders = $_POST['ordering'];
		asort($orders);
		$orders = array_keys($orders);

		foreach ($orders as $k => $id)
			$orders[$k] = intval($id);
			
		$orders = array_flip($orders);
		
		$testsQuestionsAssocDAO->Update($tid, $qid, $weight, $orders[$qid]+1);
		$count++;
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_SERVER['PHP_SELF'] .'?tid='.$tid.'&_course_id='.$_course_id);
	exit;
}

$cats    = array();
$cats[0] = _AT('cats_uncategorized');
$cat_rows = $testsQuestionsCategoriesDAO->getByCourseID($_course_id);
if (is_array($cat_rows)) {
	foreach ($cat_rows as $cat_row) {
		$cats[$cat_row['category_id']] = $cat_row['title'];
	}
}

require_once(TR_INCLUDE_PATH.'header.inc.php');

$row = $testsDAO->get($tid);
echo '<h3>'._AT('questions_for').' '.AT_print($row['title'], 'tests.title').'</h3>';

$rows = $testsQuestionsAssocDAO->getZeroWeightRowsByTestID($tid);
if (is_array($rows)) {
	$msg->printWarnings('QUESTION_WEIGHT');
}

$msg->printAll();

$rows = $testsQuestionsAssocDAO->getByTestID($tid);

$savant->assign('cats', $cats);
$savant->assign('rows', $rows);
$savant->assign('tid', $tid);
$savant->assign('course_id', $_course_id);
$savant->display('tests/questions.tmpl.php');

require_once(TR_INCLUDE_PATH.'footer.inc.php');?>
