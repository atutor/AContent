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

if (is_array($this->rows)) {
?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('question_categories'); ?></legend>
<?php	foreach ($this->rows as $row) { ?>
			<div class="row">
				<input type="radio" id="cat_<?php echo $row['category_id']; ?>" name="category" value="<?php echo $row['category_id']; ?>" />	<label for="cat_<?php echo $row['category_id']; ?>"><?php echo AT_print($row['title'], 'tests_questions_categories.title'); ?></label>
			</div>
<?php 
		}
?>
		<div class="row buttons">
			<input type="submit" value="<?php echo _AT('edit'); ?>"   name="edit" />
			<input type="submit" value="<?php echo _AT('delete'); ?>" name="delete" />
		</div>
	</div>
	</form>
<?php

	} else {
	echo '<div class="input-form">';
		$this->msg->addFeedback('NO_QUESTION_CATS');
		$this->msg->printAll();
	echo'</div>';
	}
?>
