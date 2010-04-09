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
require_once(TR_INCLUDE_PATH.'../tests/lib/likert_presets.inc.php');
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
} else if (isset($_POST['submit'])) {
	$_POST['question']    = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);

	$empty_fields = array();
	if ($_POST['question'] == ''){
		$empty_fields[] = _AT('question');
	}
	if ($_POST['choice'][0] == '') {
		$empty_fields[] = _AT('choice').' 1';
	}

	if ($_POST['choice'][1] == '') {
		$empty_fields[] = _AT('choice').' 2';
	}

	if (!empty($empty_fields)) {
		$msg->addError(array('EMPTY_FIELDS', implode(', ', $empty_fields)));
	}

	if (!$msg->containsErrors()) {
		$_POST['feedback']   = '';
		$_POST['question']   = $addslashes($_POST['question']);

		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
			$_POST['answer'][$i] = intval($_POST['answer'][$i]);

			if ($_POST['choice'][$i] == '') {
				/* an empty option can't be correct */
				$_POST['answer'][$i] = 0;
			}
		}
		
		$sql_params = array(	$_POST['category_id'], 
								$_course_id,
								$_POST['feedback'], 
								$_POST['question'], 
								$_POST['choice'][0], 
								$_POST['choice'][1], 
								$_POST['choice'][2], 
								$_POST['choice'][3], 
								$_POST['choice'][4], 
								$_POST['choice'][5], 
								$_POST['choice'][6], 
								$_POST['choice'][7], 
								$_POST['choice'][8], 
								$_POST['choice'][9], 
								$_POST['answer'][0], 
								$_POST['answer'][1], 
								$_POST['answer'][2], 
								$_POST['answer'][3], 
								$_POST['answer'][4], 
								$_POST['answer'][5], 
								$_POST['answer'][6], 
								$_POST['answer'][7], 
								$_POST['answer'][8], 
								$_POST['answer'][9]);

		$sql = vsprintf(TR_SQL_QUESTION_LIKERT, $sql_params);
		if ($testsQuestionsDAO->execute($sql)) {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: question_db.php?_course_id='.$_course_id);
			exit;
		}
		else {
			$msg->addError('DB_NOT_UPDATED');
		}
	}
} else if (isset($_POST['preset'])) {
	// load preset
	$_POST['preset_num'] = intval($_POST['preset_num']);

	if (isset($_likert_preset[$_POST['preset_num']])) {
		$_POST['choice'] = $_likert_preset[$_POST['preset_num']];
	} else if ($_POST['preset_num']) {
		$row = $testsQuestionsDAO->get($_POST[preset_num]);
		if (isset($row)) {
			for ($i=0; $i<10; $i++) {
				$_POST['choice'][$i] = $row['choice_' . $i];
			}
		}
	}
}

global $onload;
$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('likert_preset', $_likert_preset);
$savant->assign('testsQuestionsDAO', $testsQuestionsDAO);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_likert.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php');  ?>