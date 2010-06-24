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
$onload = 'document.form.form_login.focus();';

include(TR_INCLUDE_PATH.'header.inc.php');
?>

<script language="JavaScript" src="include/jscripts/sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
/* 
 * Encrypt login password with sha1
 */
function encrypt_password() {
	document.form.form_password_hidden.value = hex_sha1(hex_sha1(document.form.form_password.value) + "<?php echo $_SESSION['token']; ?>");
	document.form.form_password.value = "";
	return true;
}
</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php if (isset($_REQUEST['oauth_token'])) {?>
<input type="hidden" name="oauth_token" value="<?php echo $_REQUEST['oauth_token']; ?>" />
<?php }?>
<?php if (isset($_REQUEST['oauth_callback'])) {?>
<input type="hidden" name="oauth_callback" value="<?php echo $_REQUEST['oauth_callback']; ?>" />
<?php }?>
<input type="hidden" name="form_password_hidden" value="" />
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('login') ;?></legend>
	    <div  style="width:60%; margin-left:auto; margin-right:auto;">  
	    <p><?php echo _AT('login_text'). _AT('required_field_text') ;?></p>
	    <dl class="form_layout">
	    <dt><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="login"><?php echo _AT('login_name_or_email'); ?></label></dt>
	    <dd><input type="text" name="form_login" size="50" id="login"  class="formfield" /></dd>
	    <dt><span class="required" align="right" title="<?php echo _AT('required_field'); ?>">*</span><label for="pass"><?php echo _AT('password'); ?></label></dt>
	    <dd><input type="password" name="form_password" size="50" id="pass" class="formfield" /></dd>
	    </dl>
	    <p class="submit_button">
	    <input type="submit" name="submit" value="<?php echo _AT('login'); ?>" class="submit" onclick="return encrypt_password();" /> 		
	    </p>
	  </div>
	</fieldset>			
	</div>
</form>

<?php  include(TR_INCLUDE_PATH.'footer.inc.php'); ?>