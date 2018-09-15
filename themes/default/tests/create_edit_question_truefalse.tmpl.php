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

session_start();

require_once(TR_INCLUDE_PATH.'../tests/classes/TestsUtility.class.php');
require_once(TR_ClassCSRF_PATH.'class_csrf.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php if (isset($this->qid)) {?><input type="hidden" name="qid" value="<?php echo $this->qid; ?>" /><?php }?>
<?php if (isset($this->tid)) {?><input type="hidden" name="tid" value="<?php echo $this->tid; ?>" /><?php }?>
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_tf'); ?></legend>
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label>
		<select name="category_id" id="cats">
			<?php TestsUtility::printQuestionCatsInDropDown($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<label for="optional_feedback"><?php echo _AT('optional_feedback'); ?></label> 
		<?php TestsUtility::printVisualEditorLink('optional_feedback'); ?>
		<textarea id="optional_feedback" cols="50" rows="3" name="feedback">
		<?php if (isset($_POST['feedback']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent()) 
		echo htmlspecialchars(stripslashes($_POST['feedback']));
		else echo htmlspecialchars(stripslashes($this->row['feedback']));  ?></textarea>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('statement'); ?></label> 
		<?php TestsUtility::printVisualEditorLink('question'); ?>	
		<textarea id="question" cols="50" rows="6" name="question">
		<?php if (isset($_POST['question']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent()) 
		echo htmlspecialchars(stripslashes($_POST['question']));
		else echo htmlspecialchars(stripslashes($this->row['question'])); ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('answer'); ?><br />
		<input type="radio" name="answer" value="1" id="answer1"<?php echo $this->ans_yes; ?> /><label for="answer1"><?php echo _AT('true'); ?></label>, <input type="radio" name="answer" value="2" id="answer2"<?php echo $this->ans_no; ?> /><label for="answer2"><?php echo _AT('false'); ?></label>
	</div>

	<div class="row buttons">
		<?php echo CSRF_Token::display(); ?><br>
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s"/>
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>
