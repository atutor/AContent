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
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_matching'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cats"><?php echo _AT('category'); ?></label><br />
		<select name="category_id" id="cats">
			<?php TestsUtility::printQuestionCatsInDropDown($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<label for="optional_feedback"><?php echo _AT('optional_feedback'); ?></label> 
		<?php TestsUtility::printVisualEditorLink('optional_feedback'); ?>

		<textarea id="optional_feedback" cols="50" rows="3" name="feedback"><?php if (isset($_POST['feedback']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent())
		echo htmlspecialchars(stripslashes($_POST['feedback'])); else echo htmlspecialchars(stripslashes($this->row['feedback'])); ?></textarea>
	</div>

	<div class="row">
		<label for="instructions"><?php echo _AT('instructions'); ?></label> 
		<?php TestsUtility::printVisualEditorLink('instructions'); ?>
		<textarea id="instructions" cols="50" rows="3" name="instructions"><?php if (isset($_POST['instructions']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent())
		echo htmlspecialchars(stripslashes($_POST['instructions'])); else echo htmlspecialchars(stripslashes($this->row['instructions'])); ?></textarea>
	</div>

	<div class="row">
		<h2><?php echo _AT('questions');?></h2>
	</div>
<?php for ($i=0; $i<10; $i++): ?>
	<div class="row">
		<?php if ($i < 2) :?>
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<?php endif; ?>
		<?php echo _AT('question'); ?> <?php echo ($i+1); ?>
		
		<?php TestsUtility::printVisualEditorLink('question_' . $i); ?>
		
		<br />

		<select name="question_answer[<?php echo $i; ?>]">
			<option value="-1">-</option>
			<?php foreach ($this->letters as $key => $value): ?>
				<option value="<?php echo $key; ?>" <?php if (isset($_POST['question_answer']) && $key == $_POST['question_answer'][$i]) { echo 'selected="selected"'; }?>><?php echo $value; ?></option>
			<?php endforeach; ?>
		</select>
		
		<textarea id="question_<?php echo $i; ?>" cols="50" rows="2" name="question[<?php echo $i; ?>]"><?php 
		if (isset($_POST['question']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent()) 
		echo htmlspecialchars(stripslashes($_POST['question'][$i]));
		else echo htmlspecialchars(stripslashes($this->row['question'][$i])); ?></textarea> 
	</div>
<?php endfor; ?>
	
	<div class="row">
		<h2><?php echo _AT('answers');?></h2>
	</div>
	<?php for ($i=0; $i<10; $i++): ?>
		<div class="row">
			<?php if ($i < 2) :?>
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<?php endif; ?>
			<?php echo _AT('answer'); ?> <?php echo $this->letters[$i]; ?>
			<?php TestsUtility::printVisualEditorLink('answer' . $i); ?>
			<br />
			<textarea id="answer_<?php echo $i; ?>" cols="50" rows="2" name="answer[<?php echo $i; ?>]"><?php 
			if (isset($_POST['answer']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent()) 
			echo htmlspecialchars(stripslashes($_POST['answer'][$i]));
			else echo htmlspecialchars(stripslashes($this->row['answer'][$i])); ?></textarea>
		</div>
	<?php endfor; ?>

	<div class="row buttons">
		<?php echo CSRF_Token::display(); ?><br>
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>
