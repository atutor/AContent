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

define('TR_INCLUDE_PATH', 'include/');
require (TR_INCLUDE_PATH.'vitals.inc.php');

require_once(TR_INCLUDE_PATH. 'classes/DAO/UsersDAO.class.php');

$usersDAO = new UsersDAO();

// For security reasons the token has to be generated anew before each login attempt.
// The entropy of SHA-1 input should be comparable to that of its output; in other words, the more randomness you feed it the better.
/***
* Remove comments below and add comments to the 2 lines in the following block to enable a remote login form.
*/
//if (isset($_POST['token']))
//{
//	$_SESSION['token'] = $_POST['token'];
//}
//else
//{
//	if (!isset($_SESSION['token']))
//		$_SESSION['token'] = sha1(mt_rand() . microtime(TRUE));
//}

/***
* Add comments 2 lines below to enable a remote login form.
*/
if (!isset($_SESSION['token']))
	$_SESSION['token'] = sha1(mt_rand() . microtime(TRUE));

if (isset($_POST['submit']))
{
	//$user_id = $usersDAO->Validate($addslashes($_POST['form_login']), $addslashes($_POST['form_password_hidden']));
    $user_id = $usersDAO->Validate($_POST['form_login'], $_POST['form_password_hidden']);
	if (!$user_id)
	{
		$msg->addError('INVALID_LOGIN');
	}
	else
	{
		if ($usersDAO->getStatus($user_id) == TR_STATUS_DISABLED)
		{
			$msg->addError('ACCOUNT_DISABLED');
		}
		else
		{
			$usersDAO->setLastLogin($user_id);
			$_SESSION['user_id'] = $user_id;
			$msg->addFeedback('LOGIN_SUCCESS');
			header('Location: index.php');
			exit;
		}
	}
	
}

global $onload;
$onload = 'document.form.form_login.focus();';

//header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
$savant->display('login.tmpl.php');
?>
