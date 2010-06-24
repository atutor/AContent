<?php
/************************************************************************/
/* AContent                                                        */
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
require_once(TR_INCLUDE_PATH.'classes/testQuestions.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

// converts array entries to ints
function intval_array ( & $value, $key) { $value = (int) $value; }

if ( (isset($_GET['edit']) || isset($_GET['delete']) || isset($_GET['export']) || isset($_GET['preview']) || isset($_GET['add'])) && !isset($_GET['questions'])){
	$msg->addError('NO_ITEM_SELECTED');
} else if (isset($_GET['submit_create'], $_GET['question_type'])) {
	header('Location: '.TR_BASE_HREF.'tests/create_question_'.$addslashes($_GET['question_type']).'.php?_course_id='.$_course_id);
	exit;
} else if (isset($_GET['edit'])) {
	$id  = current($_GET['questions']);
	$num_selected = count($id);

	if ($num_selected == 1) {
		$ids = explode('|', $id[0], 2);
		$o = TestQuestions::getQuestion($ids[1]);
		if ($name = $o->getPrefix()) {
			header('Location: '.TR_BASE_HREF.'tests/edit_question_'.$name.'.php?qid='.intval($ids[0]).'&_course_id='.$_course_id);
			exit;
		} else {
			header('Location: '.TR_BASE_HREF.'tests/index.php?_course_id='.$_course_id);
			exit;
		}
	} else {
		$msg->addError('SELECT_ONE_ITEM');
	}

} else if (isset($_GET['delete'])) {
	$id  = current($_GET['questions']);
	$ids = array();
	foreach ($_GET['questions'] as $category_questions) {
		$ids = array_merge($ids, $category_questions);
	}

	array_walk($ids, 'intval_array');
	$ids = implode(',',$ids);

	header('Location: '.TR_BASE_HREF.'tests/delete_question.php?qid='.$ids.'&_course_id='.$_course_id);
	exit;
} else if (isset($_GET['preview'])) {
	$ids = array();
	foreach ($_GET['questions'] as $category_questions) {
		$ids = array_merge($ids, $category_questions);
	}

	array_walk($ids, 'intval_array');
	$ids = implode(',',$ids);

	header('Location: '.TR_BASE_HREF.'tests/preview_question.php?qid='.$ids.'&_course_id='.$_course_id);
	exit;
} else if (isset($_GET['add'])) {
	$id  = current($_GET['questions']);
	$ids = explode('|', $id[0], 2);
} else if (isset($_GET['export'])) {
	$ids = array();
	foreach ($_GET['questions'] as $category_questions) {
		$ids = array_merge($ids, $category_questions);
	}

	array_walk($ids, 'intval_array');

	if ($_GET['qti_export_version']=='2.1'){
		test_question_qti_export_v2p1($ids);
	} else {
		test_question_qti_export($ids);
	}

	exit;
}

require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('course_id', $_course_id);
$savant->assign('tid', $tid);
$savant->assign('questions', TestQuestions::getQuestionPrefixNames());

$savant->display('tests/question_db_top.tmpl.php');

$tid = 0; 

require_once(TR_INCLUDE_PATH.'../tests/html/tests_questions.inc.php'); 

require_once(TR_INCLUDE_PATH.'footer.inc.php'); 

?>