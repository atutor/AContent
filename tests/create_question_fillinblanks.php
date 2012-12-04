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

/**
* Create fill in the blanks assessment
* @access	public
* @author	Catia Prandi
*/

define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'lib/test_question_queries.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();

if (isset($_POST['cancel']) || isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php?_course_id='.$_course_id);
	exit;
} else if (isset($_POST['add'])) {
	if(!isset($_POST['word']))
		$msg->addError('You must choose at least one word!');
	else {
		for($i=0; $i<10; $i++)
			$_POST['choice'][$i] = $_POST['word'][$i];
		
	}
		
} else if (isset($_POST['transform'])) {
	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}
	
	if (!$msg->containsErrors()) {
			$words_in_text = explode(' ', $_POST['question']);
			$transform_text = '';
			$count = 0;
			$answer = array();
			$not_words = array(';', '.', ',', ';', '\'', '"', '?', '!', ':', '(', ')');
			foreach ($words_in_text as $word) {
				$flag = false;
				$word = trim($word);
				$last_char = substr($word, -1);
				if(in_array($last_char, $not_words)) {
					
					$word = substr($word, 0, -1);
					$flag = true;
				} 
				$transform_text .= '<span id="'.$word.'_'.$count.'_span" style="display: inline; padding-left: 3px; padding-right: 3px; margin-left: 2px; margin-right: 2px;">';	
				$transform_text .= '<input type="checkbox" id="'.$word.'_'.$count.'" name="word[]" value="'.$word.'_'.$count.'" onclick="
				if(document.getElementById(\''.$word.'_'.$count.'\').checked) {
					document.getElementById(\''.$word.'_'.$count.'_span\').style.border= \'2px solid #C0C0C0\';
				} else {
					document.getElementById(\''.$word.'_'.$count.'_span\').style.border= \'none\';
				}"/>';
				$transform_text .= '<label for="'.$word.'_'.$count.'">'.$word.'</label>';
				$transform_text .= '</span>';
				
				if($flag) {
					$transform_text .= '<span>';
					$transform_text .= $last_char;
					$transform_text .= '</span>';
					
				}
					
				
				$count++;
			}
			$_POST['transf_question'] = $transform_text;
			
		}
	
} else if ($_POST['submit']) {
	$_POST['feedback'] = trim($_POST['feedback']);
	$_POST['question'] = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);
	
	
	if ($_POST['question'] == '') 
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	else if ($_POST['choice'][0] == '')
		$msg->addError(array('EMPTY_FIELDS', "Correct answer 1"));	
		
	if (!$msg->containsErrors()) {
		$choice_new = array(); // stores the non-blank choices
		$option_new = array(); // stores the associated "answer" for the choices
		for ($i=0; $i<10; $i++) {
			/**
			 * Db defined it to be 255 length, chop strings off it it's less than that
			 * @harris
			 */
			$_POST['choice'][$i] = Utility::validateLength($_POST['choice'][$i], 255);
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
			//catia
			$_POST['option'][$i] = Utility::validateLength($_POST['option'][$i], 255);
			$_POST['option'][$i] = $addslashes(trim($_POST['option'][$i]));

			if ($_POST['choice'][$i] == '') {
				/* an empty option can't be correct */
				$_POST['answer'][$i] = 0;
			} else {
				/* filter out empty choices/ remove gaps */
				$choice_new[] = $_POST['choice'][$i];
				$answer_new[] = $_POST['answer'][$i];

				if ($_POST['answer'][$i] != 0)
					$has_answer = TRUE;
			}
		}
			
		if ($has_answer != TRUE && !$_POST['submit_yes']) {
	
			$hidden_vars['required']    = htmlspecialchars($_POST['required']);
			$hidden_vars['feedback']    = htmlspecialchars($_POST['feedback']);
			$hidden_vars['question']    = htmlspecialchars($_POST['question']);
			$hidden_vars['category_id'] = htmlspecialchars($_POST['category_id']);

			for ($i = 0; $i < count($choice_new); $i++) {
				$hidden_vars['answer['.$i.']'] = htmlspecialchars($answer_new[$i]);
				$hidden_vars['choice['.$i.']'] = htmlspecialchars($choice_new[$i]);
			}

			$msg->addConfirm('NO_ANSWER', $hidden_vars);
		} else {
		
			//add slahes throughout - does that fix it?
			$_POST['answer'] = $answer_new;
			$_POST['choice'] = $choice_new;
			$_POST['answer'] = array_pad($_POST['answer'], 10, -1);
			$_POST['choice'] = array_pad($_POST['choice'], 10, '');
			$_POST['option'] = array_pad($_POST['option'], 10, '');
		
		
			
			$_POST['feedback'] = $addslashes($_POST['feedback']);
			$_POST['question'] = $addslashes($_POST['question']);

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
									$_POST['answer'][9],
									$_POST['option'][0],
									$_POST['option'][1],
									$_POST['option'][2],
									$_POST['option'][3],
									$_POST['option'][4],
									$_POST['option'][5],
									$_POST['option'][6],
									$_POST['option'][7],
									$_POST['option'][8],
									$_POST['option'][9]);
			$sql = vsprintf(TR_SQL_QUESTION_FILLINBLANKS, $sql_params);

			if ($testsQuestionsDAO->execute($sql)) {
				$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
				header('Location: question_db.php?_course_id='.$_course_id);
				exit;
			}
		}
	}
}

$onload = 'document.form.category_id.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');

$msg->printConfirm();

$savant->assign('qid', $qid);
$savant->assign('tid', $_REQUEST['tid']);
$savant->assign('course_id', $_course_id);
$savant->display('tests/create_edit_question_fillinblanks.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); ?>



?>