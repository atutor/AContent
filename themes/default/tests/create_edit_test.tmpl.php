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

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" autocomplete="off">
<?php if (isset($this->tid)) { ?><input type="hidden" name="tid" value="<?php echo $this->tid; ?>" /> <?php }?>
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_test'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" size="40" value="<?php if (isset($_POST['title']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent()) echo stripslashes(htmlspecialchars($_POST['title'])); else echo AT_print($this->row['title'], 'input.text'); ?>" />
	</div>
	
	<div class="row">
		<label for="description"><?php echo _AT('test_description'); ?></label><br />
		<textarea name="description" cols="35" rows="3" id="description"><?php if (isset($_POST['description']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent()) echo htmlspecialchars($_POST['description']); else echo $this->row['description']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save');  ?>"  name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>
