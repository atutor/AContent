<? require_once(TR_INCLUDE_PATH.'../tests/classes/TestsUtility.class.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php if (isset($this->qid)) {?><input type="hidden" name="qid" value="<?php echo $this->qid; ?>" /><?php }?>
<?php if (isset($this->tid)) {?><input type="hidden" name="tid" value="<?php echo $this->tid; ?>" /><?php }?>
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_fib'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cats"><?php echo _AT('category'); ?></label>
		<select name="category_id" id="cats">
			<?php TestsUtility::printQuestionCatsInDropDown($_POST['category_id']); ?>
		</select>
	</div>
	
	<div class="row">
		<label for="optional_feedback"><?php echo _AT('optional_feedback'); ?></label> 
		<?php TestsUtility::printVisualEditorLink('optional_feedback'); ?>	

		<textarea id="optional_feedback" cols="50" rows="3" name="feedback"><?php echo htmlspecialchars(stripslashes($_POST['feedback'])); ?></textarea>
	</div>

	<div class="row">
		<?php if(isset($_POST['transf_question'])) {?>
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="transf_text">Choose the words (max 10) to hide </label> 
			<div id="transf_text"><?php echo $_POST['transf_question']; ?></div>
			<textarea id="question" style="display: none;" cols="50" rows="4" name="question"><?php echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
         <?php } else { ?>
         		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question">Insert your text below: </label> 
				<?php TestsUtility::printVisualEditorLink('question'); ?>
                <textarea id="question" cols="50" rows="4" name="question"><?php echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
                     
        <?php }?>
				
		<div class="row buttons">
			<?php if(isset($_POST['transf_question'])) {?>
				<input type="submit" name="add" value="Add words" class="button" size="40"  ></input>
				<input type="hidden" value="<?php echo htmlspecialchars(stripslashes($_POST['transf_question']));?> " name="transf_question" ></input>
			<?php } else { ?>
				<input type="submit" name="trasform" value="Trasform" class="button" size="40"  />
			<?php }?>
		</div>
		<!-- onclick="javascript:alert((document.getElementById('question').value).substring(document.getElementById('question').selectionStart, document.getElementById('question').selectionEnd)); "  -->
	</div>

	<div style="float:left; width:50%;">
	<?php 
	for ($i=0; $i<10; $i++) { ?>
		<div class="row">
			<?php if($i==0) { ?>
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<?php } ?>
			<label for="choice_<?php echo $i; ?>">Correct answer  <?php echo ($i+1); ?></label> 
			<?php TestsUtility::printVisualEditorLink('choice_' . $i); ?>
			<br />
			<input type="text" size="40" id="choice_<?php echo $i; ?>" name="choice[<?php echo $i; ?>]" class="formfield" value="<?php $word = explode('_', htmlspecialchars(stripslashes($_POST['choice'][$i]))); echo $word[0]; ?>" />
			
			</div>
	 <?php } ?>
	</div>
	<div style="float:left; width:50%;">
	<?php 
	for ($i=0; $i<10; $i++) { ?>
		<div class="row">
			<label for="option_<?php echo $i; ?>">Another option for answer <?php echo ($i+1); ?></label> 
			<?php TestsUtility::printVisualEditorLink('choice_' . $i); ?>
			<br />
			<input type="text" size="40" id="option_<?php echo $i; ?>" name="option[<?php echo $i; ?>]" class="formfield" />
				
		</div>
	<?php } ?>
	</div>	
	
	<div style="display: block; clear: both; padding-top: 20px;">
		<input id="leave_fill" type="checkbox" name="leave_fill" value="leave_fill" />
		<label for="leave_fill">Add the option <span style="font-style: italic;">Leave blank</span></label>
	</div>	
	

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="hidden" value="<?php $_POST['answer'];?> " name="answer" ></input>
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>
