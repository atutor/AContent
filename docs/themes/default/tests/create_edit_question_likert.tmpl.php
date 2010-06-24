<?php
/************************************************************************/
/* AContent                                                        									*/
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

require_once(TR_INCLUDE_PATH.'../tests/classes/TestsUtility.class.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php if (isset($this->qid)) {?><input type="hidden" name="qid" value="<?php echo $this->qid; ?>" /><?php }?>
<?php if (isset($this->tid)) {?><input type="hidden" name="tid" value="<?php echo $this->tid; ?>" /><?php }?>
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('preset_scales'); ?></legend>

	<div class="row">
		<select name="preset_num">
			<optgroup label="<?php echo _AT('presets'); ?>">
			<?php // presets
				foreach ($this->likert_preset as $val => $preset) {
					echo '<option value="'.$val.'">'.$preset[0].' - '.$preset[count($preset)-1].'</option>';
				}
			//previously used
			echo '</optgroup>';

			$rows = $this->testsQuestionsDAO->getByCourseIDAndType($this->course_id, 4);
			if (is_array($rows)) {
				echo '<optgroup label="'. _AT('prev_used').'">';
				$used_choices = array();
				foreach ($rows as $row) {
					$choices = array_slice($row, 9, 10);
					if (in_array($choices, $used_choices)) {
						continue;
					}

					$used_choices[] = $choices;

					for ($i=0; $i<=10; $i++) {
						if ($row['choice_'.$i] == '') {
							$i--;
							break;
						}
					}
					echo '<option value="'.$row['question_id'].'">'.$row['choice_0'].' - '.$row['choice_'.$i].'</option>';
				}
				echo '</optgroup>';
			}
		?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="preset" value="<?php echo _AT('set_preset'); ?>" class="button" />
	</div>
	</fieldset>
</div>

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_lk'); ?></legend>
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label><br />
		<select name="category_id" id="cats">
			<?php TestsUtility::printQuestionCatsInDropDown($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label> 
		
		<?php TestsUtility::printVisualEditorLink('question'); ?>
		
		<textarea id="question" cols="50" rows="6" name="question"><?php echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>

<?php
	for ($i=0; $i<10; $i++) { ?>
		<div class="row">
			<?php if ($i==0 || $i==1) { ?>
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<?php } ?>

			<label for="choice_<?php echo $i; ?>">
			<?php echo _AT('choice'); ?> <?php echo ($i+1); ?></label><br />
			<input type="text" id="choice_<?php echo $i; ?>" size="40" name="choice[<?php echo $i; ?>]" value="<?php echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?>" />
		</div>
<?php } ?>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>
