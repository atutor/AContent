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

global $_current_user;

if (!isset($_current_user)) {
	require(TR_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('INVALID_USER');
	require(TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: ../index.php');
	exit;
}

if (isset($_POST['submit'])) {
	if (!empty($_POST['form_old_password_hidden']))
	{
		//check if old password entered is correct
		if ($row = $_current_user->getInfo()) 
		{
			if ($row['password'] != $_POST['form_old_password_hidden']) 
			{
				$msg->addError('WRONG_PASSWORD');
				Header('Location: change_password.php');
				exit;
			}
		}
	}
	else
	{
		$msg->addError(array('EMPTY_FIELDS', _AT('password')));
		header('Location: change_password.php');
		exit;
	}

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

		if (!$_current_user->setPassword($password)) 
		{
			require(TR_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('DB_NOT_UPDATED');
			require(TR_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		$msg->addFeedback('PASSWORD_CHANGED');
	}
}

/* template starts here */
$savant->display('profile/change_password.tmpl.php');

?>