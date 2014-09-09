<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }

if(isset($_POST['submit']) && ($_POST['action'] == 'process')) {
	unset($errors);

	$_POST['admin_username'] = trim($_POST['admin_username']);
	$_POST['admin_email'] = trim($_POST['admin_email']);
	$_POST['site_name'] = trim($_POST['site_name']);
	$_POST['email'] = trim($_POST['email']);
	$_POST['account_username'] = trim($_POST['account_username']);
	$_POST['account_email'] = trim($_POST['account_email']);
	$_POST['account_fname'] = trim($_POST['account_fname']);
	$_POST['account_lname'] = trim($_POST['account_lname']);
	$_POST['account_organization'] = trim($_POST['account_organization']);
	$_POST['account_phone'] = trim($_POST['account_phone']);
	$_POST['account_address'] = trim($_POST['account_address']);
	$_POST['account_city'] = trim($_POST['account_city']);
	$_POST['account_province'] = trim($_POST['account_province']);
	$_POST['account_country'] = trim($_POST['account_country']);
	$_POST['account_postal_code'] = trim($_POST['account_postal_code']);
	
	/* Super Administrator Account checking: */
	if ($_POST['admin_username'] == ''){
		$errors[] = 'Administrator username cannot be empty.';
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/", $_POST['admin_username']))){
			$errors[] = 'Administrator username is not valid.';
		}
	}
	if ($_POST['form_admin_password_hidden'] == '') {
		$errors[] = 'Administrator password cannot be empty.';
	}
	if ($_POST['admin_email'] == '') {
		$errors[] = 'Administrator email cannot be empty.';
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/", $_POST['admin_email'])) {
		$errors[] = 'Administrator email is not valid.';
	}

	/* System Preferences checking: */
	if ($_POST['site_name'] == '') {
		$errors[] = 'Site name cannot be empty.';
	}
	if ($_POST['email'] == '') {
		$errors[] = 'Contact email cannot be empty.';
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/", $_POST['email'])) {
		$errors[] = 'Contact email is not valid.';
	}

	/* Personal Account checking: */
	if ($_POST['account_username'] == ''){
		$errors[] = 'Personal Account Username cannot be empty.';
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/i", $_POST['account_username']))){
			$errors[] = 'Personal Account Username is not valid.';
		} else {
			if ($_POST['account_username'] == $_POST['admin_username']) {
				$errors[] = 'That Personal Account Username is already being used for the Administrator account, choose another.';
			}
		}
	}
	if ($_POST['form_account_password_hidden'] == '') {
		$errors[] = 'Personal Account Password cannot be empty.';
	}
	if ($_POST['account_email'] == '') {
		$errors[] = 'Personal Account email cannot be empty.';
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['account_email'])) {
		$errors[] = 'Invalid Personal Account email is not valid.';
	}
	if ($_POST['account_fname'] == '') {
		$errors[] = 'Personal Account First Name cannot be empty.';
	}
	if ($_POST['account_lname'] == '') {
		$errors[] = 'Personal Account Last Name cannot be empty.';
	}
	if ($_POST['account_organization'] == '') {
		$errors[] = 'Personal Account Organization cannot be empty.';
	}
	if ($_POST['account_phone'] == '') {
		$errors[] = 'Personal Account Phone cannot be empty.';
	}
	if ($_POST['account_address'] == '') {
		$errors[] = 'Personal Account Address cannot be empty.';
	}
	if ($_POST['account_city'] == '') {
		$errors[] = 'Personal Account City cannot be empty.';
	}
	if ($_POST['account_province'] == '') {
		$errors[] = 'Personal Account Province cannot be empty.';
	}
	if ($_POST['account_country'] == '') {
		$errors[] = 'Personal Account Country cannot be empty.';
	}
	if ($_POST['account_postal_code'] == '') {
		$errors[] = 'Personal Account Postal Code cannot be empty.';
	}
	
	if (!isset($errors)) {
	
	     if(defined('MYSQLI_ENABLED')){
        $db = new mysqli($_POST['step2']['db_host'], $_POST['step2']['db_login'], urldecode($_POST['step2']['db_password']), $_POST['step2']['db_name'], $_POST['step2']['db_port']);
        $db->set_charset("utf8");

	     }else{
		$db = @mysql_connect($_POST['step2']['db_host'] . ':' . $_POST['step2']['db_port'], $_POST['step2']['db_login'], urldecode($_POST['step2']['db_password']));
		@mysql_select_db($_POST['step2']['db_name'], $db);
        }
		// for admin account
		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."users 
		        (login, password, user_group_id, email, web_service_id, create_date, is_author)
		        VALUES ('".$addslashes($_POST[admin_username])."', 
		                '".$_POST[form_admin_password_hidden]."', 
		                1, 
		                '".$addslashes($_POST[admin_email])."', 
		                '".substr(md5(uniqid(rand(), true)),0,32)."', 
		                NOW(),
		                '1')";
		                
		if(defined('MYSQLI_ENABLED')){
		    $result = $db->query($sql);
		}else{
		    $result= mysql_query($sql, $db);
        }
		// for author account
		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."users
               (login, password, user_group_id, first_name, last_name, email, is_author, organization, phone,
               address, city, province, country, postal_code, web_service_id, status, create_date)
               VALUES ('".$addslashes($_POST['account_username'])."',
               '".$_POST['form_account_password_hidden']."',
               2,
               '".$addslashes($_POST['account_fname'])."',
               '".$addslashes($_POST['account_lname'])."', 
               '".$addslashes($_POST['account_email'])."',
               1,
               '".$addslashes($_POST['account_organization'])."',
               '".$addslashes($_POST['account_phone'])."',
               '".$addslashes($_POST['account_address'])."',
               '".$addslashes($_POST['account_city'])."',
               '".$addslashes($_POST['account_province'])."',
               '".$addslashes($_POST['account_country'])."',
               '".$addslashes($_POST['account_postal_code'])."',
		       '".substr(md5(uniqid(rand(), true)),0,32)."', 
               1, 
               now())";
		if(defined('MYSQLI_ENABLED')){
		    $result = $db->query($sql);
		}else{
		    $result= mysql_query($sql, $db);
        }
		//$user_id = mysql_insert_id();
		$user_id = ac_insert_id();
		
		// associate the default HowTo lesson with this author account 
		$sql = "UPDATE ".$_POST['step2']['tb_prefix']."courses SET user_id=".$user_id." WHERE course_id=1";
		//$result = mysql_query($sql ,$db);
		if(defined('MYSQLI_ENABLED')){
		    $result = $db->query($sql);
		}else{
		    $result= mysql_query($sql, $db);
        }
		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."user_courses (user_id, course_id, role) VALUES (".$user_id.", 1, 1)";
		if(defined('MYSQLI_ENABLED')){
		    $result = $db->query($sql);
		}else{
		    $result= mysql_query($sql, $db);
        }
		//$result = mysql_query($sql ,$db);
		
		// configurations
		$_POST['site_name'] = $addslashes($_POST['site_name']);
		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."config (name, value) VALUES ('site_name', '$_POST[site_name]')";
		//$result = mysql_query($sql ,$db);
		if(defined('MYSQLI_ENABLED')){
		    $result = $db->query($sql);
		}else{
		    $result= mysql_query($sql, $db);
        }
		$_POST['email'] = $addslashes($_POST['email']);
		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."config (name, value) VALUES ('contact_email', '$_POST[email]')";
		//$result = mysql_query($sql ,$db);
		if(defined('MYSQLI_ENABLED')){
		    $result = $db->query($sql);
		}else{
		    $result= mysql_query($sql, $db);
        }
		unset($_POST['admin_username']);
		unset($_POST['form_admin_password_hidden']);
		unset($_POST['admin_email']);
		unset($_POST['email']);
		unset($_POST['site_name']);
		unset($_POST['account_username']);
		unset($_POST['form_account_password_hidden']);
		unset($_POST['account_email']);
		unset($_POST['account_fname']);
		unset($_POST['account_lname']);
		unset($_POST['account_organization']);
		unset($_POST['account_phone']);
		unset($_POST['account_address']);
		unset($_POST['account_city']);
		unset($_POST['account_province']);
		unset($_POST['account_country']);
		unset($_POST['account_postal_code']);
												
		unset($errors);
		unset($_POST['submit']);
		unset($action);
		store_steps($step);
		$step++;
		return;
	}
}

print_progress($step);

if (isset($errors)) {
	print_errors($errors);
}

if (isset($_POST['step1']['old_version']) && $_POST['upgrade_action']) {
	$defaults['admin_username'] = urldecode($_POST['step1']['admin_username']);
	$defaults['admin_email']    = urldecode($_POST['step1']['admin_email']);

	$defaults['site_name']   = urldecode($_POST['step1']['site_name']);
	$defaults['header_img']  = urldecode($_POST['step1']['header_img']);
	$defaults['header_logo'] = urldecode($_POST['step1']['header_logo']);
	$defaults['home_url']    = urldecode($_POST['step1']['home_url']);
} else {
	$defaults = $_defaults;
}

?>
<script language="JavaScript" src="<?php echo TR_INCLUDE_PATH; ?>../../include/jscripts/sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	if (document.form.admin_password.value != "") {
		document.form.form_admin_password_hidden.value = hex_sha1(document.form.admin_password.value);
		document.form.admin_password.value = "";
	}
	
	if (document.form.account_password.value != "") {
		document.form.form_account_password_hidden.value = hex_sha1(document.form.account_password.value);
		document.form.account_password.value = "";
	}
}
</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="action" value="process" />
	<input type="hidden" name="form_admin_password_hidden" value="" />
	<input type="hidden" name="form_account_password_hidden" value="" />
	<input type="hidden" name="step" value="<?php echo $step; ?>" />
	<?php print_hidden($step); ?>

	<?php
		/* detect mail settings. if sendmail_path is empty then use SMTP. */
		if (@ini_get('sendmail_path') == '') { 
			echo '<input type="hidden" name="smtp" value="true" />';
		} else {
			echo '<input type="hidden" name="smtp" value="false" />';
		}
	?>
	<br />
		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">Super Administrator Account</th>
		</tr>
		<tr>
			<td colspan="2" class="row1">The Super Administrator account is used for managing AContent. The Super Administrator can also create additional Administrators each with their own privileges and roles. </td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="username">Administrator Username:</label></b><br />
			May contain only letters, numbers, or underscores.</td>
			<td class="row1"><input type="text" name="admin_username" id="username" maxlength="20" size="20" value="<?php if (!empty($_POST['admin_username'])) { echo stripslashes(htmlspecialchars($_POST['admin_username'])); } else { echo $defaults['admin_username']; } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="password">Administrator Password:</label></b></td>
			<td class="row1"><input type="text" name="admin_password" id="password" maxlength="15" size="15" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="email">Administrator Email:</label></b></td>
			<td class="row1"><input type="text" name="admin_email" id="email" size="40" value="<?php if (!empty($_POST['admin_email'])) { echo stripslashes(htmlspecialchars($_POST['admin_email'])); } else { echo $defaults['admin_email']; } ?>" class="formfield" /></td>
		</tr>
		</table>

	<br />

		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">System Preferences</th>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="sitename">Site Name:</label></b><br />
			The name of your course server website.<br />Default: <kbd><?php echo $defaults['site_name']; ?></kbd></td>
			<td class="row1"><input type="text" name="site_name" size="28" maxlength="60" id="sitename" value="<?php if (!empty($_POST['site_name'])) { echo stripslashes(htmlspecialchars($_POST['site_name'])); } else { echo $defaults['site_name']; } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="cemail">Contact Email:</label></b><br />
			The email that will be used as the return email when needed.</td>
			<td class="row1"><input type="text" name="email" id="cemail" size="40" value="<?php if (!empty($_POST['email'])) { echo stripslashes(htmlspecialchars($_POST['email'])); } else { echo $defaults['email']; } ?>" class="formfield" /></td>
		</tr>
		</table>

	<br />

		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">Author Account</th>
		</tr>
		<tr>
			<td colspan="2" class="row1">You will need an author account to create lessons.</td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="account_username">Username:</label></b><br />
			May contain only letters, numbers, and underscores.</td>
			<td class="row1"><input type="text" name="account_username" id="account_username" maxlength="20" size="20" value="<?php if (!empty($_POST['account_username'])) { echo stripslashes(htmlspecialchars($_POST['account_username'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="account_password">Password:</label></b></td>
			<td class="row1"><input type="text" name="account_password" id="account_password" maxlength="15" size="15" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="account_email">Email:</label></b></td>
			<td class="row1"><input type="text" name="account_email" id="account_email" size="40" maxlength="60" value="<?php if (!empty($_POST['account_email'])) { echo stripslashes(htmlspecialchars($_POST['account_email'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="account_fname">First Name:</label></b></td>
			<td class="row1"><input type="text" name="account_fname" id="account_fname" size="40" maxlength="60" value="<?php if (!empty($_POST['account_fname'])) { echo stripslashes(htmlspecialchars($_POST['account_fname'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="account_lname">Last Name:</label></b></td>
			<td class="row1"><input type="text" name="account_lname" id="account_lname" size="40" maxlength="60" value="<?php if (!empty($_POST['account_lname'])) { echo stripslashes(htmlspecialchars($_POST['account_lname'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="">Organization:</label></b></td>
			<td class="row1"><input type="text" name="account_organization" id="account_organization" size="40" maxlength="60" value="<?php if (!empty($_POST['account_organization'])) { echo stripslashes(htmlspecialchars($_POST['account_organization'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="">Phone:</label></b></td>
			<td class="row1"><input type="text" name="account_phone" id="account_phone" size="40" maxlength="60" value="<?php if (!empty($_POST['account_phone'])) { echo stripslashes(htmlspecialchars($_POST['account_phone'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="">Address:</label></b></td>
			<td class="row1"><input type="text" name="account_address" id="account_address" size="40" maxlength="60" value="<?php if (!empty($_POST['account_address'])) { echo stripslashes(htmlspecialchars($_POST['account_address'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="">City:</label></b></td>
			<td class="row1"><input type="text" name="account_city" id="account_city" size="40" maxlength="60" value="<?php if (!empty($_POST['account_city'])) { echo stripslashes(htmlspecialchars($_POST['account_city'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="">Province:</label></b></td>
			<td class="row1"><input type="text" name="account_province" id="account_province" size="40" maxlength="60" value="<?php if (!empty($_POST['account_province'])) { echo stripslashes(htmlspecialchars($_POST['account_province'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="">Country:</label></b></td>
			<td class="row1"><input type="text" name="account_country" id="account_country" size="40" maxlength="60" value="<?php if (!empty($_POST['account_country'])) { echo stripslashes(htmlspecialchars($_POST['account_country'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="">Postal Code:</label></b></td>
			<td class="row1"><input type="text" name="account_postal_code" id="account_postal_code" size="40" maxlength="60" value="<?php if (!empty($_POST['account_postal_code'])) { echo stripslashes(htmlspecialchars($_POST['account_postal_code'])); } ?>" class="formfield" /></td>
		</tr>
		</table>
	<br />
	<br />
	<div align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" onclick="return encrypt_password();" /></div>
</form>