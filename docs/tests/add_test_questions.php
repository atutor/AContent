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
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/testQuestions.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

if (isset($_GET['submit_create'])) {
	header('Location: create_question_'.$_GET['question_type'].'.php?_course_id='.$_course_id);
	exit;
}

$_pages['tests/questions.php?tid='.$_GET['tid'].'&_course_id='.$_course_id]['title_var']    = 'questions';
$_pages['tests/questions.php?tid='.$_GET['tid'].'&_course_id='.$_course_id]['parent']   = 'tests/index.php';
$_pages['tests/questions.php?tid='.$_GET['tid'].'&_course_id='.$_course_id]['children'] = array('tests/add_test_questions.php');

$_pages['tests/add_test_questions.php']['title_var']    = 'add_questions';
$_pages['tests/add_test_questions.php']['parent']   = 'tests/questions.php?tid='.$_GET['tid'].'&_course_id='.$_course_id;

require_once(TR_INCLUDE_PATH.'header.inc.php');
require_once(TR_INCLUDE_PATH.'../tests/classes/TestsUtility.class.php');
?>

<?php $tid = intval($_GET['tid']); ?>
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('category'); ?></legend>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
		<input type="hidden" name="_course_id" value="<?php echo $_course_id; ?>" />

			<label for="cats"><?php echo _AT('category'); ?></label><br />
			<select name="category_id" id="cats">
				<option value="-1"><?php echo _AT('cats_all'); ?></option>
				<?php TestsUtility::printQuestionCatsInDropDown($_GET['category_id']); ?>
			</select>

			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
	</form>

	</fieldset>

</div>
<?php require_once(TR_INCLUDE_PATH.'../tests/html/tests_questions.inc.php'); ?>

<?php require_once(TR_INCLUDE_PATH.'footer.inc.php'); ?>