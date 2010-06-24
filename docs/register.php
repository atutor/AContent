<?php
/************************************************************************/
/* AContent                                                         */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', 'include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
include(TR_INCLUDE_PATH."securimage/securimage.php");

if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	require_once(TR_INCLUDE_PATH. 'classes/DAO/UsersDAO.class.php');
	$usersDAO = new UsersDAO();
	
	/* password check: password is verified front end by javascript. here is to handle the errors from javascript */
	if ($_POST['password_error'] <> "")
	{
		$pwd_errors = explode(",", $_POST['password_error']);

		foreach ($pwd_errors as $pwd_error)
		{
			if ($pwd_error == "missing_password")
				$missing_fields[] = _AT('password');
			else
				$msg->addError($pwd_error);
		}
	}
	//CAPTCHA
	if ($_config['use_captcha']==TR_STATUS_ENABLED){
		$img = new Securimage();
		$valid = $img->check($_POST['secret']);
		if (!$valid)
			$msg->addError('SECRET_ERROR');
	}

	if (!$msg->containsErrors())
	{
		if (isset($_POST['is_author'])) $is_author = 1;
		else $is_author = 0;
		
		$user_id = $usersDAO->Create(TR_USER_GROUP_USER,
                      $_POST['login'],
		              $_POST['form_password_hidden'],
		              $_POST['email'],
		              $_POST['first_name'],
		              $_POST['last_name'],
		              $is_author,
		              $_POST['organization'],
		              $_POST['phone'],
		              $_POST['address'],
		              $_POST['city'],
		              $_POST['province'],
		              $_POST['country'],
		              $_POST['postal_code'],
		              TR_STATUS_ENABLED);
		
		if (is_int($user_id) && $user_id > 0)
		{
			if (defined('TR_EMAIL_CONFIRMATION') && TR_EMAIL_CONFIRMATION) {
				$msg->addFeedback('REG_THANKS_CONFIRM');
	
				$code = substr(md5($_POST['email'] . $now . $user_id), 0, 10);
				
				$confirmation_link = $_base_href . 'confirm.php?id='.$user_id.SEP.'m='.$code;
	
				/* send the email confirmation message: */
				require(TR_INCLUDE_PATH . 'classes/phpmailer/transformablemailer.class.php');
				$mail = new TransformableMailer();
	
				$mail->From     = $_config['contact_email'];
				$mail->AddAddress($_POST['email']);
				$mail->Subject = SITE_NAME . ' - ' . _AT('email_confirmation_subject');
				$mail->Body    = _AT('email_confirmation_message', SITE_NAME, $confirmation_link)."\n\n";
	
				$mail->Send();
			} 
			else 
			{
				// auto login
				$usersDAO->setLastLogin($user_id);
				$_SESSION['user_id'] = $user_id;
				
				// show web service ID in success message
				$row = $usersDAO->getUserByID($user_id);
				$msg->addFeedback(array('REGISTER_SUCCESS', $row['web_service_id']));
				header('Location: index.php');
				exit;
			}
		}
	}
}

/*****************************/
/* template starts down here */

global $onload;
$onload = 'document.form.login.focus();';

$savant->assign('title', _AT('registration'));
$savant->assign('submit_button_text', _AT('register'));
$savant->assign('show_user_group', false);
$savant->assign('show_status', false);
$savant->assign('show_password', true);
if ($_config['use_captcha'] == TR_STATUS_ENABLED) $savant->assign('use_captcha', true);

$savant->display('register.tmpl.php');

?>