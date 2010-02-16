<?php 
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/*
 * Called by "system/index.php"
 * 
 * Accept parameters:
 * 
 * show_user_group: true/false. Indicates whether show section "User Group"
 *                  Set to true when admin creates/edits user; set to false at new registration.
 *                  The new user registered via registration form is automatically set into group "User" 
 * show_password:  true/false. Indicates whether show section "Password" & "Password Again"
 *                 Set to true when admin creates new user or new user registration; 
 *                 Set to false when admin edits existing user.
 * show_status: true/false. Indicates whether show section "status"
 *              Set to true when admin creates/edits user; set to false at new registration.
 * user_row: only need when edit existing user.
 * all_user_groups: display selections in dropdown list box "User Group"
 * title: page title
 * submit_button_text: button text for submit button. "Register" at registration, "Save" at admin creating/editing user
 */

global $onload;
$onload = 'document.form.site_name.focus();';

require(TR_INCLUDE_PATH.'header.inc.php'); 
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo $this->title; ?></legend>

	<table class="form-data" align="center">
		<tr>
			<td colspan="2" align="left"><br/><?php echo _AT('required_field_text') ;?></td>
		</tr>

		<tr>
			<td align="left"><div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="site_name"><?php echo _AT('site_name'); ?></label>:</td>
			<td align="left"><input id="site_name" name="site_name" type="text" maxlength="20" size="30" value="<?php if (isset($_POST['site_name'])) echo stripslashes(htmlspecialchars($_POST['site_name'])); else echo stripslashes(htmlspecialchars($this->config['site_name'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="contact_email"><?php echo _AT('contact_email'); ?></label>:</td>
			<td align="left"><input id="contact_email" name="contact_email" type="text" size="50" maxlength="50" value="<?php if (isset($_POST['contact_email'])) echo stripslashes(htmlspecialchars($_POST['contact_email'])); else echo stripslashes(htmlspecialchars($this->config['contact_email'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="theme"><?php echo _AT('theme'); ?></label>:</td>
			<td align="left">
				<select name="theme" id="theme"><?php
					if (isset($_POST['theme']))
						$selected_theme = $_POST['theme'];
					else
						$selected_theme = $_SESSION['prefs']['PREF_THEME'];
						
					foreach ($this->enabled_themes as $theme) {
						if (!$theme) {
							continue;
						}

						if ($theme_fldr == $selected_theme) {
							echo '<option value="'.$theme['dir_name'].'" selected="selected">'.$theme['title'].'</option>';
						} else {
							echo '<option value="'.$theme['dir_name'].'">'.$theme['title'].'</option>';
						}
					}
				?>
				</select>
			</td>
		</tr>

		<tr>
			<td align="left"><label for="default_language"><?php echo _AT('default_language'); ?></label>:</td>
			<td align="left">
		<?php if (!empty($_POST['default_language'])) { 
				$select_lang = $_POST['default_language']; 
			} else { 
				$select_lang = $_config['default_language'];
			} 
			$this->languageManager->printDropdown($select_lang, 'default_language', 'default_language'); ?>
			</td>
		</tr>

		<tr>
			<td align="left"><?php echo _AT('use_captcha'); ?>:</td>
			<td align="left">
				<input type="radio" name="use_captcha" id="statusD" value="<?php echo TR_STATUS_DISABLED; ?>" <?php if ((isset($_POST['use_captcha']) && $_POST['use_captcha']==TR_STATUS_DISABLED) || (!isset($_POST['use_captcha']) && $this->config['use_captcha']==TR_STATUS_DISABLED)) echo 'checked="checked"'; ?> /><label for="statusD"><?php echo _AT('disabled'); ?></label> 
				<input type="radio" name="use_captcha" id="statusE" value="<?php echo TR_STATUS_ENABLED; ?>" <?php if ((isset($_POST['use_captcha']) && $_POST['use_captcha']==TR_STATUS_ENABLED) || (!isset($_POST['use_captcha']) && $this->config['use_captcha']==TR_STATUS_ENABLED)) echo 'checked="checked"'; ?> /><label for="statusE"><?php echo _AT('enabled'); ?></label>
			</td>
		</tr>
		
		<tr>
			<td align="left" colspan="2">
				<small>&middot; <?php echo _AT('default_use_captcha'); ?></small>
			</td>
		</tr>
		
		<tr>
			<td align="left"><label for="max_file_size"><?php echo _AT('max_file_size'); ?></label>:</td>
			<td align="left"><input id="max_file_size" name="max_file_size" type="text" value="<?php if (isset($_POST['max_file_size'])) echo stripslashes(htmlspecialchars($_POST['max_file_size'])); else echo stripslashes(htmlspecialchars($this->config['max_file_size'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left" colspan="2">
				<small>&middot; <?php echo _AT('default_max_file_size'); ?></small>
			</td>
		</tr>
		
		<tr>
			<td align="left"><label for="illegal_extentions"><?php echo _AT('illegal_extentions'); ?></label>:</td>
			<td align="left"><textarea name="illegal_extentions" cols="50" id="illegal_extentions" rows="2" class="formfield" ><?php if ($this->config['illegal_extentions']) { echo str_replace('|',' ',$this->config['illegal_extentions']); }?></textarea></td>
		</tr>

		<tr>
			<td align="left"><label for="latex_server"><?php echo _AT('latex_server'); ?></label>:</td>
			<td align="left"><input id="latex_server" name="latex_server" size="50" type="text" value="<?php if (isset($_POST['latex_server'])) echo stripslashes(htmlspecialchars($_POST['latex_server'])); else echo stripslashes(htmlspecialchars($this->config['latex_server'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left" colspan="2">
				<small>&middot; <?php echo _AT('latex_server_info'); ?></small>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<p class="submit_button">
				<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" class="submit" /> 
				<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> "  class="submit" />
				<input type="submit" name="factory_default" value=" <?php echo _AT('factory_default'); ?> "  class="submit" />
			</p>
			</td>
		</tr>
	</table>
</fieldset>

</div>
</form>

<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>