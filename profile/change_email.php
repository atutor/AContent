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

define('TR_INCLUDE_PATH', '../include/');

require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
require_once('../class_csrf.php');

global $_current_user;

if (!isset($_current_user)) 
{
	require(TR_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('INVALID_USER');
	require(TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	Header('Location: ../index.php');
	exit;
}

if (isset($_POST['submit']))
{
	if (CSRF_Token::isValid() AND CSRF_Token::isRecent())
	{
		$this_password = $_POST['form_password_hidden'];
	
	// password check
	if (!empty($this_password)) 
	{
		//check if old password entered is correct
		if ($row = $_current_user->getInfo()) 
		{
			if ($row['password'] != $this_password) 
			{
				$msg->addError('WRONG_PASSWORD');
				Header('Location: change_email.php');
				exit;
			}
		}
	} 
	else 
	{
		$msg->addError(array('EMPTY_FIELDS', _AT('password')));
		header('Location: change_email.php');
		exit;
	}
	
	// email check
	if ($_POST['email'] == '') 
	{
		$msg->addError(array('EMPTY_FIELDS', _AT('email')));
	} 
	else 
	{
		if(!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) 
		{
			$msg->addError('EMAIL_INVALID');
		}
		
		$usersDAO = new UsersDAO();
		$row = $usersDAO->getUserByEmail($_POST['email']);
		if ($row['user_id'] > 0 && $row['user_id'] <> $_SESSION['user_id'])
		{
			$msg->addError('EMAIL_EXISTS');
		}
	}

	if (!$msg->containsErrors()) 
	{

		if (defined('TR_EMAIL_CONFIRMATION') && TR_EMAIL_CONFIRMATION) 
		{
			//send confirmation email
			$row    = $_current_user->getInfo();

			if ($row['email'] != $_POST['email']) {
				$code = substr(md5($_POST['email'] . $row['creation_date'] . $_SESSION['user_id']), 0, 10);
				$confirmation_link = TR_BASE_HREF . 'confirm.php?id='.$_SESSION['user_id'].SEP .'e='.urlencode($_POST['email']).SEP.'m='.$code;

				/* send the email confirmation message: */
				require(TR_INCLUDE_PATH . 'classes/phpmailer/transformablemailer.class.php');
				$mail = new TransformableMailer();

				$mail->From     = $_config['contact_email'];
				$mail->AddAddress($_POST['email']);
				$mail->Subject = SITE_NAME . ' - ' . _AT('email_confirmation_subject');
				$mail->Body    = _AT('email_confirmation_message2', $_config['site_name'], $confirmation_link);

				$mail->Send();

				$msg->addFeedback('CONFIRM_EMAIL');
			} else {
				$msg->addFeedback('CHANGE_TO_SAME_EMAIL');
			}
		} else {

		//insert into database
		$_current_user->setEmail($_POST[email]);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
	}
	} else
	{
		$msg->addError('INVALID_TOKEN');
	}	
}

$row = $_current_user->getInfo();

if (!isset($_POST['submit'])) {
	$_POST = $row;
}

/* template starts here */
$savant->assign('row', $row);
$savant->display('profile/change_email.tmpl.php');

?>
