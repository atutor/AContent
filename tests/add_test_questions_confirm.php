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
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsAssocDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();
$testsQuestionsAssocDAO = new TestsQuestionsAssocDAO();

$tid = intval($_POST['tid']);

$_pages['tests/questions.php?tid='.$tid.'&_course_id='.$_course_id]['title_var']    = 'questions';
$_pages['tests/questions.php?tid='.$tid.'&_course_id='.$_course_id]['parent']   = 'tests/index.php';
$_pages['tests/questions.php?tid='.$tid.'&_course_id='.$_course_id]['children'] = array('tests/add_test_questions.php');

$_pages['tests/add_test_questions.php']['title_var']  = 'add_questions';
$_pages['tests/add_test_questions.php']['parent'] = 'tests/questions.php?tid='.$tid.'&_course_id='.$_course_id;

$_pages['tests/add_test_questions_confirm.php']['title_var'] = 'add_questions';
$_pages['tests/add_test_questions_confirm.php']['parent']    = 'tests/questions.php?tid='.$tid.'&_course_id='.$_course_id;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: questions.php?tid='.$tid.'&_course_id='.$_course_id);
	exit;
} else if (isset($_POST['submit_yes'])) {
	//get order
	$order = $testsQuestionsAssocDAO->getMaxOrderByTestID($tid);

	$sql = "REPLACE INTO ".TABLE_PREFIX."tests_questions_assoc VALUES ";
	$values = array();
	foreach ($_POST['questions'] as $question) {
		$order++;
		$question = intval($question);
		//$sql .= '('.$tid.', '.$question.', 0, '.$order.'),';
		$sql .= '(?, ?, 0, ?),';
		$values = array_merge($values, array($tid, $question, $order));
		$types .= "iii";
	}
	$sql = substr($sql, 0, -1);

	if ($testsQuestionsAssocDAO->execute($sql, $values, $types)) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: questions.php?tid='.$tid.'&_course_id='.$_course_id);
		exit;
	}
	else {
		$msg->addError('DB_NOT_UPDATED');
	}
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: add_test_questions.php?tid='.$tid.'&_course_id='.$_course_id);
	exit;
}

if (!is_array($_POST['questions']) || !count($_POST['questions'])) {
	$msg->addError('NO_QUESTIONS_SELECTED');
	header('Location: add_test_questions.php?tid='.$tid.'&_course_id='.$_course_id);
	require_once(TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require_once(TR_INCLUDE_PATH.'header.inc.php');

foreach ($_POST['questions'] as $id => $cat_array) {
	foreach ($cat_array as $idx => $q) {
		$_POST['questions'][$id][$idx] = intval($q);
		$questions[] = intval($q);
	}
}

$rows = $testsQuestionsDAO->getByQuestionIDs($questions);

$questions = '';
if (is_array($rows)) {
	foreach ($rows as $row) {
		$questions .= '<li>'.htmlspecialchars($row['question']).'</li>';
		$questions_array['questions['.$row['question_id'].']'] = $row['question_id'];
	}
}
$questions_array['tid'] = $_POST['tid'];
$questions_array['_course_id'] = $_course_id;
$msg->addConfirm(array('ADD_TEST_QUESTIONS', $questions), $questions_array);

$msg->printConfirm();
?>

<?php require_once(TR_INCLUDE_PATH.'footer.inc.php'); ?>
