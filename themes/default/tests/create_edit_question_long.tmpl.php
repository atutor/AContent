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
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_open'); ?></legend>
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label>
		<select name="category_id" id="cats">
			<?php TestsUtility::printQuestionCatsInDropDown($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<label for="optional_feedback"><?php echo _AT('optional_feedback'); ?></label> 
		<?php TestsUtility::printVisualEditorLink('optional_feedback'); ?>

		<textarea id="optional_feedback" cols="50" rows="3" name="feedback"><?php if (isset($_POST['feedback']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent()) echo htmlspecialchars(stripslashes($_POST['feedback'])); else echo htmlspecialchars(stripslashes($this->row['feedback'])) ?></textarea>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label> 
		<?php TestsUtility::printVisualEditorLink('question'); ?>

		<textarea id="question" cols="50" rows="6" name="question"><?php if (isset($_POST['question']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent())
		echo htmlspecialchars(stripslashes($_POST['question'])); else echo htmlspecialchars(stripslashes($this->row['question']))?></textarea>
	</div>
	
	<div class="row">
		<?php echo _AT('answer_size'); ?><br />
		<input type="radio" name="properties" value="1" id="az1" <?php if ($_POST['properties'] == 1) { echo 'checked="checked"'; } ?> /><label for="az1"><?php echo _AT('one_word'); ?></label><br />
		<input type="radio" name="properties" value="2" id="az2" <?php if ($_POST['properties'] == 2) { echo 'checked="checked"'; } ?> /><label for="az2"><?php echo _AT('one_sentence'); ?></label><br />
		<input type="radio" name="properties" value="3" id="az3" <?php if ($_POST['properties'] == 3) { echo 'checked="checked"'; } ?> /><label for="az3"><?php echo _AT('short_paragraph'); ?></label><br />
		<input type="radio" name="properties" value="4" id="az4" <?php if ($_POST['properties'] == 4) { echo 'checked="checked"'; } ?> /><label for="az4"><?php echo _AT('one_page'); ?></label>
	</div>

	<div class="row buttons">
		<?php echo CSRF_Token::display(); ?><br>
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>
