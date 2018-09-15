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

if (isset($_POST['cancel']) || isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php?_course_id='.$_course_id);
	exit;
} else if ($_POST['submit'] || $_POST['submit_yes']) {
	if (CSRF_Token::isValid() AND CSRF_Token::isRecent())
	{
	$_POST['required'] = intval($_POST['required']);
	$_POST['feedback'] = $purifier->purify(trim($_POST['feedback']));
	$_POST['question'] = $purifier->purify(trim($_POST['question']));
	$_POST['category_id'] = intval($_POST['category_id']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}
		
	if (!$msg->containsErrors()) {
		$choice_new = array(); // stores the non-blank choices
		$answer_new = array(); // stores the associated "answer" for the choices
		for ($i=0; $i<10; $i++) {
			/**
			 * Db defined it to be 255 length, chop strings off it it's less than that
			 * @harris
			 */
			$_POST['choice'][$i] = Utility::validateLength($_POST['choice'][$i], 255);
			$_POST['choice'][$i] = $purifier->purify(trim($_POST['choice'][$i]));
			$_POST['answer'][$i] = intval($_POST['answer'][$i]);

			if ($_POST['choice'][$i] == '') {
				/* an empty option can't be correct */
				$_POST['answer'][$i] = 0;
			} else {
				/* filter out empty choices/ remove gaps */
				$choice_new[] = $purifier->purify($_POST['choice'][$i]);
				$answer_new[] = $purifier->purify($_POST['answer'][$i]);

				if ($_POST['answer'][$i] != 0)
					$has_answer = TRUE;
			}
		}
			
		if ($has_answer != TRUE && !$_POST['submit_yes']) {
	
			$hidden_vars['required']    = $purifier->purify(trim($_POST['required']));
			$hidden_vars['feedback']    = $purifier->purify(trim($_POST['feedback']));
			$hidden_vars['question']    = $purifier->purify(trim($_POST['question']));
			$hidden_vars['category_id'] = intval($_POST['category_id']);
			$hidden_vars['_course_id']  = $_course_id;

			for ($i = 0; $i < count($choice_new); $i++) {
				$hidden_vars['answer['.$i.']'] = $purifier->purify($answer_new[$i]);
				$hidden_vars['choice['.$i.']'] = $purifier->purify($choice_new[$i]);
			}

			$msg->addConfirm('NO_ANSWER', $hidden_vars);
		} else {
		
			$_POST['answer'] = $answer_new;
			$_POST['choice'] = $choice_new;
			$_POST['answer'] = array_pad($_POST['answer'], 10, 0);
			$_POST['choice'] = array_pad($_POST['choice'], 10, '');

        $values = array(	$_POST['category_id'], 
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
	    $types = "iissssssssssssiiiiiiiiii";
        $sql = TR_SQL_QUESTION_MULTIANSWER;
			if ($testsQuestionsDAO->execute($sql, $values, $types)) {
				$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
				header('Location: question_db.php?_course_id='.$_course_id);
				exit;
			}
		}
	}
	} else
	{
		$msg->addError('INVALID_TOKEN');
	}
}

$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

$msg->printConfirm();

$savant->assign('qid', $qid);
$savant->assign('tid', $_REQUEST['tid']);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_multianswer.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); ?>
