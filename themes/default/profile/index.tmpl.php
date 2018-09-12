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

// show or hide the author information based on the status of the checkbox "author content" 
global $onload;
$onload = "if (jQuery('#is_author').attr('checked')) jQuery('#table_is_author').show(); else jQuery('#table_is_author').hide();";

require(TR_INCLUDE_PATH.'header.inc.php'); 
require_once(TR_ClassCSRF_PATH.'class_csrf.php');
require_once(TR_HTMLPurifier_PATH.'HTMLPurifier.auto.php');

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

//Timer
$mtime = microtime(); 
$mtime = explode(' ', $mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime;
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form" autocomplete="off">
<input name="password_error" type="hidden" />
<input type="hidden" name="form_password_hidden" value="" />

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_profile'); ?></legend>

	<table class="form-data" align="center">
		<tr align="center"><td>
		<table>
		<tr>
			<td colspan="2" align="left"><?php echo _AT('required_field_text') ;?></td>
		</tr>

		<tr>
			<th align="left"><?php echo _AT('login_name'); ?>:</th>
			<td align="left"><?php echo $purifier->purify(stripslashes(htmlspecialchars($this->row['login']))); ?></td>
		</tr>

		<tr>
			<th align="left"><?php echo _AT('web_service_id'); ?>:</th>
			<td align="left"><?php echo $this->row['web_service_id']; ?></td>
		</tr>

		<tr><td><br /></td></tr>

		<tr>
			<th align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="first_name"><?php echo _AT('first_name'); ?></label>:</th>
			<td align="left"><input id="first_name" name="first_name" type="text" value="<?php if (isset($_POST['first_name']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['first_name']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['first_name']))); ?>" /></td>
		</tr>

		<tr>
			<th align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="last_name"><?php echo _AT('last_name'); ?></label>:</th>
			<td align="left"><input id="last_name" name="last_name" type="text" value="<?php if (isset($_POST['last_name']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['last_name']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['last_name']))); ?>" /></td>
		</tr>

		<tr>
			<td colspan="2">
				<input type="checkbox" name="is_author" id="is_author" <?php if ($_POST['is_author'] == 'on' || $_POST['is_author']==1 && Token::isValid() AND Token::isRecent()) echo 'checked="checked"'; ?> onclick="if (this.checked) jQuery('#table_is_author').show('slow'); else jQuery('#table_is_author').hide('slow');" /><label for="is_author"><?php echo _AT('is_author'); ?></label> 
			</td>
		</tr>
		</table>
		</td></tr>
		
		<tr align="center"><td>
		<table id="table_is_author">
		<tr>
			<td align="left"><label for="organization"><?php echo _AT('organization'); ?></label>:</td>
			<td align="left"><input id="organization" name="organization" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['organization']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['organization']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['organization']))); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="phone"><?php echo _AT('phone'); ?></label>:</td>
			<td align="left"><input id="phone" name="phone" type="text" size="30" maxlength="30" value="<?php if (isset($_POST['phone']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['phone']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['phone']))); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="address"><?php echo _AT('address'); ?></label>:</td>
			<td align="left"><input id="address" name="address" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['address']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['address']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['address']))); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="city"><?php echo _AT('city'); ?></label>:</td>
			<td align="left"><input id="city" name="city" type="text" size="30" maxlength="30" value="<?php if (isset($_POST['city']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['city']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['city']))); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="province"><?php echo _AT('province'); ?></label>:</td>
			<td align="left"><input id="province" name="province" type="text" size="30" maxlength="30" value="<?php if (isset($_POST['province']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['province']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['province']))); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="country"><?php echo _AT('country'); ?></label>:</td>
			<td align="left"><input id="country" name="country" type="text" size="30" maxlength="30" value="<?php if (isset($_POST['country']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['country']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['country']))); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="postal_code"><?php echo _AT('postal_code'); ?></label>:</td>
			<td align="left"><input id="postal_code" name="postal_code" type="text" size="10" maxlength="10" value="<?php if (isset($_POST['postal_code']) AND Token::isValid() AND Token::isRecent()) echo $purifier->purify(stripslashes(htmlspecialchars($_POST['postal_code']))); else echo $purifier->purify(stripslashes(htmlspecialchars($this->row['postal_code']))); ?> "/></td>
		</tr>

		</table>
		</td></tr>
		
		<tr align="center"><td>
		<table>
		<tr>
			<td colspan="2">
			<p class="submit_button">
				<?php echo Token::display(); ?><br>
				<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" class="submit" /> 
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
