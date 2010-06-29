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

?>
	<fieldset class="group_form" style="width:43%;float:left;height:18em;min-width:15em;margin-left:4em;"><legend class="group_form"><?php echo _AT('create_new_question'); ?></legend>
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

	<fieldset class="group_form" style="width:38%;float:left;clear:right;height:18em;"><legend class="group_form"><?php echo _AT('import_question'); ?></legend>
		<form method="post" action="<?php echo 'tests/question_import.php?_course_id='.$this->course_id; ?>" enctype="multipart/form-data" >	<label for="to_file"><?php echo _AT('upload_question'); ?></label><br />
			<input type="file" name="file" id="to_file" /><br /><br />
			<div class="row buttons">
			<input type="submit" name="submit_import" value="<?php echo _AT('import'); ?>" />
			</div>
		</form>
	</fieldset>
