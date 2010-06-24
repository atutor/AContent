<?php 
/************************************************************************/
/* AContent                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

global $onload;
$onload = 'document.form.form_email.focus();';

require(TR_INCLUDE_PATH.'header.inc.php'); 
?>

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('password_reminder'); ?></legend>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="form_password_reminder" value="true" />

		<table class="form-data" align="center" width="60%">
			<tr>
				<td colspan="2" align="left"><?php echo _AT('password_blurb'); ?></td>
			</tr>
			
			<tr><td><br /></td></tr>

			<tr>
				<td align="left">
					<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
					<label for="email"><?php echo _AT('email_address'); ?></label>:
				</td>
				<td align="left">
					<input type="text" name="form_email" id="email" size="60" />
				</td>
			</tr>
		
			<tr>
				<td colspan="2">
					<p class="submit_button">
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