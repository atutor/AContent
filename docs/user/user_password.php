<?php
/************************************************************************/
/* AFrame                                                               */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('AF_INCLUDE_PATH', '../include/');
include_once(AF_INCLUDE_PATH.'vitals.inc.php');
include_once(AF_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: ../index.php');
	exit;
}

if (isset($_POST['submit'])) {
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

	if (!$msg->containsErrors()) {
		// insert into the db.
		$password   = $addslashes($_POST['form_password_hidden']);
		
		$usersDAO = new UsersDAO();

		if (!$usersDAO->setPassword($_GET['id'], $password)) 
		{
			require(AF_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('DB_NOT_UPDATED');
			require(AF_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		// send email to user
		$user_row = $usersDAO->getUserByID($_GET['id']);

		$tmp_message  = _AT('password_change_msg')."\n\n";
		$tmp_message .= _AT('web_site').' : '.AF_BASE_HREF."\n";
		$tmp_message .= _AT('login_name').' : '.$user_row['login']."\n";
		
		require(AF_INCLUDE_PATH . 'classes/phpmailer/aframemailer.class.php');
		$mail = new AFrameMailer;
		$mail->From     = $_config['contact_email'];
		$mail->AddAddress($user_row['email']);
		$mail->Subject = $_config['site_name'] . ': ' . _AT('password_changed');
		$mail->Body    = $tmp_message;

		if(!$mail->Send()) 
		{
		   $msg->addError('SENDING_ERROR');
		}
		else
		{
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
		
		header('Location: index.php');
		exit;
	}
}

/* template starts here */
$savant->display('user/user_password.tmpl.php');

?>