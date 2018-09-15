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

require_once(TR_ClassCSRF_PATH.'class_csrf.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
<?php 
if (isset($this->catid)) {
	echo '<input type="hidden" value="'.$this->catid.'" name="catid" />';
}
?>
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo $this->title; ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cat"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="cat" value="<?php if (isset($_POST['title']) AND CSRF_Token::isValid() AND CSRF_Token::isRecent()) echo htmlspecialchars($_POST['title']); else echo htmlspecialchars($this->row['title']); ?>" />
	</div>

	<div class="row buttons">
		<?php echo CSRF_Token::display(); ?><br>
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>
