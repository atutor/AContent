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
 * Called by "register.php" and "user/user_create_edit.php
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
$default_user_group_id = TR_USER_GROUP_USER;

// show or hide the author information based on the status of the checkbox "author content" 
global $onload;
$onload = "
document.form.login.focus(); 

if (jQuery('#is_author').attr('checked')) jQuery('#table_is_author').show(); 
else jQuery('#table_is_author').hide();
";

require(TR_INCLUDE_PATH.'header.inc.php'); 
?>

<script language="JavaScript" src="include/jscripts/sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.password_error.value = "";

	err = verify_password(document.form.form_password1.value, document.form.form_password2.value);
	
	if (err.length > 0)
	{
		document.form.password_error.value = err;
	}
	else
	{
		document.form.form_password_hidden.value = hex_sha1(document.form.form_password1.value);
		document.form.form_password1.value = "";
		document.form.form_password2.value = "";
	}
}
</script>

<form method="post" action="<?php $id_str = ''; if (isset($_GET['id'])) $id_str='?id='.$_GET['id']; echo $_SERVER['PHP_SELF'].$id_str; ?>" name="form">
<input name="password_error" type="hidden" />
<input type="hidden" name="form_password_hidden" value="" />

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo $this->title; ?></legend>

	<table class="form-data" align="center">
	<tr align="center"><td>
		<table>
		<tr>
			<td colspan="2" align="left"><br/><?php echo _AT('required_field_text') ;?><br/></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="login"><?php echo _AT('login_name'); ?></label>:</td>
			<td align="left"><input id="login" name="login" type="text" maxlength="20" size="30" value="<?php if (isset($_POST['login'])) echo stripslashes(htmlspecialchars($_POST['login'])); else echo stripslashes(htmlspecialchars($this->user_row['login'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left" colspan="2">
				<small>&middot; <?php echo _AT('contain_only'); ?><br />
					   &middot; <?php echo _AT('20_max_chars'); ?></small>
			</td>
		</tr>
		
		<?php if ($this->show_user_group) { ?>
		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="user_group_id"><?php echo _AT('user_group'); ?>:</label>:</td>
			<td align="left">
			<select name="user_group_id" id="user_group_id">
				<option value="-1">- <?php echo _AT('select'); ?> -</option>
				<?php foreach ($this->all_user_groups as $user_group) {?>
				<option value="<?php echo $user_group['user_group_id']; ?>" <?php if ((isset($_POST['user_group_id']) && $_POST['user_group_id']==$user_group['user_group_id']) || (!isset($_POST['user_group_id']) && !isset($this->user_row['user_group_id']) && $user_group['user_group_id'] == $default_user_group_id) || (!isset($_POST['user_group_id']) && isset($this->user_row['user_group_id']) && $this->user_row['user_group_id'] == $user_group['user_group_id'] )) echo 'selected="selected"'; ?>><?php echo $user_group['title']; ?></option>
				<?php } ?>
			</select>
			</td>
		</tr>
		<?php } ?>

		<?php if ($this->show_password) { ?>
		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="form_password1"><?php echo _AT('password'); ?></label>:</td>
			<td align="left"><input id="form_password1" name="form_password1" type="password" size="15" maxlength="15" /></td>
		</tr>

		<tr>
			<td colspan="2" align="left"><small>&middot; <?php echo _AT('combination'); ?><br />
				   &middot; <?php echo _AT('15_max_chars'); ?></small></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="form_password2"><?php echo _AT('password_again'); ?></label>:</td>
			<td align="left"><input id="form_password2" name="form_password2" type="password" size="15" maxlength="15" /></td>
		</tr>

		<?php if ($this->use_captcha) { ?>
		<tr>
			<td colspan="2">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<?php echo _AT('image_validation_text'); ?>
			</td>
		</tr>
		<tr>
			<td>
			<label for="secret"><img src="<?php echo TR_INCLUDE_PATH; ?>securimage/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>" id="simage" align="left" /></label>
			<a href="<?php echo TR_INCLUDE_PATH; ?>securimage/securimage_play.php" title="<?php echo _AT('audible_captcha'); ?>"><img src="<?php echo TR_INCLUDE_PATH; ?>securimage/images/audio_icon.gif" alt="<?php echo _AT('audible_captcha'); ?>" onclick="this.blur()" align="top" border="0"></a><br>
			<a href="#" title="<?php echo _AT('refresh_image'); ?>" onclick="document.getElementById('simage').src = '<?php echo TR_INCLUDE_PATH; ?>securimage/securimage_show.php?sid=' + Math.random(); return false"><img src="<?php echo TR_INCLUDE_PATH; ?>securimage/images/refresh.gif" alt="<?php echo _AT('refresh_image'); ?>" onclick="this.blur()" align="bottom" border="0"></a>
			</td>
			<td><input id="secret" name="secret" type="text" size="6" maxlength="6" value="" /></td>
		</tr>
		<tr>
			<td><small><?php echo _AT('image_validation_text2'); ?><br /></td>
		</tr>
		<?php }} ?>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="email"><?php echo _AT('email_address'); ?></label>:</td>
			<td align="left"><input id="email" name="email" type="text" size="50" maxlength="50" value="<?php if (isset($_POST['email'])) echo stripslashes(htmlspecialchars($_POST['email'])); else echo stripslashes(htmlspecialchars($this->user_row['email'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="first_name"><?php echo _AT('first_name'); ?></label>:</td>
			<td align="left"><input id="first_name" name="first_name" type="text" value="<?php if (isset($_POST['first_name'])) echo stripslashes(htmlspecialchars($_POST['first_name'])); else echo stripslashes(htmlspecialchars($this->user_row['first_name'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="last_name"><?php echo _AT('last_name'); ?></label>:</td>
			<td align="left"><input id="last_name" name="last_name" type="text" value="<?php if (isset($_POST['last_name'])) echo stripslashes(htmlspecialchars($_POST['last_name'])); else echo stripslashes(htmlspecialchars($this->user_row['last_name'])); ?>" /></td>
		</tr>

		<?php if ($this->show_status) {?>
		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('status'); ?>:</td>
			<td align="left">
				<input type="radio" name="status" id="statusD" value="<?php echo TR_STATUS_DISABLED; ?>" <?php if ((isset($_POST['status']) && $_POST['status']==0) || (!isset($_POST['status']) && $this->user_row['status']==TR_STATUS_DISABLED)) echo 'checked="checked"'; ?> /><label for="statusD"><?php echo _AT('disabled'); ?></label> 
				<input type="radio" name="status" id="statusE" value="<?php echo TR_STATUS_ENABLED; ?>" <?php if ((isset($_POST['status']) && $_POST['status']==1) || (!isset($_POST['status']) && $this->user_row['status']==TR_STATUS_ENABLED)) echo 'checked="checked"'; ?> /><label for="statusE"><?php echo _AT('enabled'); ?></label>
				<?php if (defined('TR_EMAIL_CONFIRMATION') && TR_EMAIL_CONFIRMATION) {?>
				<input type="radio" name="status" id="statusU" value="<?php echo TR_STATUS_UNCONFIRMED; ?>" <?php if ((isset($_POST['status']) && $_POST['status']==1) || (!isset($_POST['status']) && $this->user_row['status']==TR_STATUS_UNCONFIRMED)) echo 'checked="checked"'; ?> /><label for="statusU"><?php echo _AT('enabled'); ?></label>
				<?php }?>
			</td>
		</tr>
		<?php }?>
		
		<?php if (isset($this->user_row['web_service_id'])) {?>
		<tr>
			<td align="left"><?php echo _AT('web_service_id'); ?>:</td>
			<td align="left"><?php echo $this->user_row['web_service_id']; ?></td>
		</tr>
		<?php }?>

		<tr>
			<td colspan="2">
				<input type="checkbox" name="is_author" id="is_author" <?php if (isset($_POST['is_author']) || (!isset($_POST['is_author']) && $this->user_row['is_author']==1)) echo 'checked="checked"'; ?> onclick="if (this.checked) jQuery('#table_is_author').show('slow'); else jQuery('#table_is_author').hide('slow');" /><label for="is_author"><?php echo _AT('is_author'); ?></label> 
			</td>
		</tr>
		</table>
		</td></tr>
		
		<tr align="center"><td>
		<table id="table_is_author">
		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="organization"><?php echo _AT('organization'); ?></label>:</td>
			<td align="left"><input id="organization" name="organization" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['organization'])) echo stripslashes(htmlspecialchars($_POST['organization'])); else echo stripslashes(htmlspecialchars($this->user_row['organization'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="phone"><?php echo _AT('phone'); ?></label>:</td>
			<td align="left"><input id="phone" name="phone" type="text" size="30" maxlength="30" value="<?php if (isset($_POST['phone'])) echo stripslashes(htmlspecialchars($_POST['phone'])); else echo stripslashes(htmlspecialchars($this->user_row['phone'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="address"><?php echo _AT('address'); ?></label>:</td>
			<td align="left"><input id="address" name="address" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['address'])) echo stripslashes(htmlspecialchars($_POST['address'])); else echo stripslashes(htmlspecialchars($this->user_row['address'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="city"><?php echo _AT('city'); ?></label>:</td>
			<td align="left"><input id="city" name="city" type="text" size="30" maxlength="30" value="<?php if (isset($_POST['city'])) echo stripslashes(htmlspecialchars($_POST['city'])); else echo stripslashes(htmlspecialchars($this->user_row['city'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="province"><?php echo _AT('province'); ?></label>:</td>
			<td align="left"><input id="province" name="province" type="text" size="30" maxlength="30" value="<?php if (isset($_POST['province'])) echo stripslashes(htmlspecialchars($_POST['province'])); else echo stripslashes(htmlspecialchars($this->user_row['province'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="country"><?php echo _AT('country'); ?></label>:</td>
			<td align="left"><input id="country" name="country" type="text" size="30" maxlength="30" value="<?php if (isset($_POST['country'])) echo stripslashes(htmlspecialchars($_POST['country'])); else echo stripslashes(htmlspecialchars($this->user_row['country'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="postal_code"><?php echo _AT('postal_code'); ?></label>:</td>
			<td align="left"><input id="postal_code" name="postal_code" type="text" size="10" maxlength="10" value="<?php if (isset($_POST['postal_code'])) echo stripslashes(htmlspecialchars($_POST['postal_code'])); else echo stripslashes(htmlspecialchars($this->user_row['postal_code'])); ?>" /></td>
		</tr>

		</table>
		</td></tr>
		
		<tr align="center"><td>
		<table>
		<tr>
			<td colspan="2">
			<p class="submit_button">
				<input type="submit" name="submit" value="<?php echo $this->submit_button_text; ?>" class="submit" onclick="return encrypt_password();" /> 
				<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> "  class="submit" />
			</p>
			</td>
		</tr>
		</table>
		</td></tr>
	</table>
</fieldset>

</div>
</form>

<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>