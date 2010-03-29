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
require_once(TR_INCLUDE_PATH.'../tests/classes/testQuestions.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

if (isset($_GET['submit_create'])) {
	header('Location: create_question_'.$_GET['question_type'].'.php');
	exit;
}

$_pages['tests/questions.php?tid='.$_GET['tid']]['title_var']    = 'questions';
$_pages['tests/questions.php?tid='.$_GET['tid']]['parent']   = 'tests/index.php';
$_pages['tests/questions.php?tid='.$_GET['tid']]['children'] = array('tests/add_test_questions.php');

$_pages['tests/add_test_questions.php']['title_var']    = 'add_questions';
$_pages['tests/add_test_questions.php']['parent']   = 'tests/questions.php?tid='.$_GET['tid'];

require_once(TR_INCLUDE_PATH.'header.inc.php');
?>

<?php $tid = intval($_GET['tid']); ?>

<?php require_once(TR_INCLUDE_PATH.'../tests/html/tests_questions.inc.php'); ?>

<?php require_once(TR_INCLUDE_PATH.'footer.inc.php'); ?>