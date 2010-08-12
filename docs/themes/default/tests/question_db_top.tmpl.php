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

require_once(TR_INCLUDE_PATH.'../tests/classes/TestsUtility.class.php');

?>
<div class="input-form">
<table class="qdb_table">
<tr>
<td>
	<fieldset class="group_form1" ><legend class="group_form1"><?php echo _AT('create_new_question'); ?></legend>
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="tid" value="<?php echo $this->tid; ?>" />
			<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
			<label for="question"><?php echo _AT('create_new_question'); ?></label><br />
			<select name="question_type" class="dropdown" id="question" size="8">
			<?php foreach ($this->questions as $type => $name): ?>
				<option value="<?php echo $type; ?>"><?php echo $name; ?></option>
			<?php endforeach; ?>
			</select><br /><br />
			<div class="row buttons">
			<input type="submit" name="submit_create" value="<?php echo _AT('create'); ?>" />
			</div>
		</form>
	</fieldset>
</td>
<td>
	<fieldset class="group_form1" ><legend class="group_form1"><?php echo _AT('import_question'); ?></legend>
		<form method="post" action="<?php echo 'tests/question_import.php?_course_id='.$this->course_id; ?>" enctype="multipart/form-data" >	<label for="to_file"><?php echo _AT('upload_question'); ?></label><br />
			<input type="file" name="file" id="to_file" /><br /><br />
			<div class="row buttons">
			<input type="submit" name="submit_import" value="<?php echo _AT('import'); ?>" />
			</div>
		</form>
	</fieldset>
</td>
<td>
	<fieldset class="group_form1"><legend class="group_form1"><?php echo _AT('category'); ?></legend>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
		<div class="row">
			<label for="cats"><?php echo _AT('category'); ?></label><br />
			<select name="category_id" id="cats">
				<option value="-1"><?php echo _AT('cats_all'); ?></option>
				<?php TestsUtility::printQuestionCatsInDropDown($_GET['category_id']); ?>
			</select>
		</div>
		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</form>

	</fieldset>

</td></tr></table>
</div>