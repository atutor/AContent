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

global $onload;
$onload = 'document.form.old_password.focus();';

require(TR_INCLUDE_PATH.'header.inc.php');
require_once(TR_ClassCSRF_PATH.'class_csrf.php');

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

?>

<script
	language="JavaScript" src="include/jscripts/sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.password_error.value = "";

	document.form.form_old_password_hidden.value = hex_sha1(document.form.old_password.value);
	document.form.old_password.value = "";

	// verify new password
	err = verify_password(document.form.password.value, document.form.password2.value);
	
	if (err.length > 0)
	{
		document.form.password_error.value = err;
	}
	else
	{
		document.form.form_password_hidden.value = hex_sha1(document.form.password.value);
		document.form.password.value = "";
		document.form.password2.value = "";
	}
}
</script>

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('change_password'); ?></legend>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="form_change" value="true" /> 
		<input name="password_error" type="hidden" /> 
		<input type="hidden" name="form_old_password_hidden" value="" /> 
		<input type="hidden" name="form_password_hidden" value="" />

		<table class="form-data" align="center">
			<tr>
				<td align="left">
					<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
					<label for="old_password"><?php echo _AT('password_old'); ?></label>:
				</td>
				<td align="left">
					<input id="old_password" name="old_password" type="password" size="25" maxlength="15" />
				</td>
			</tr>
		
			<tr>
				<td align="left">
					<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
					<label for="password"><?php echo _AT('new_password'); ?></label>:
				</td>
				<td align="left">
					<input id="password" name="password" type="password" size="25" maxlength="15" />
				</td>
			</tr>
		
			<tr>
				<td colspan="2">
					<small>&middot; <?php echo _AT('combination'); ?><br />
					&middot; <?php echo _AT('15_max_chars'); ?></small>
				</td>
			</tr>
		
			<tr>
				<td align="left">
					<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
					<label for="password2"><?php echo _AT('password_again'); ?></label>:
				</td>
				<td align="left">
					<input id="password2" name="password2" type="password" size="25" maxlength="15" />
				</td>
			</tr>
		
			<tr>
				<td colspan="2">
					<p class="submit_button">
						<?php echo CSRF_Token::display(); ?><br>
						<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" onclick="encrypt_password()" />
						<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
					</p>
				</td>
			</tr>
		</table>
	</form>

</fieldset>
</div>

<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>
