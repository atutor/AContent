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

//// unset $_SESSION['user_id'] to avoid page redirecting in vitals.inc.php
//if (isset($_SESSION['user_id']))
//{
//	$_SESSION['current_user'] = $_SESSION['user_id'];
//	unset($_SESSION['user_id']);
//}

define('TR_INCLUDE_PATH', '../include/');
require (TR_INCLUDE_PATH.'vitals.inc.php');

require_once(TR_INCLUDE_PATH. 'classes/DAO/UsersDAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/DAO/OAuthServerTokensDAO.class.php');

$usersDAO = new UsersDAO();
$oAuthServerTokensDAO = new OAuthServerTokensDAO();

// Validation input parameters
if ($_REQUEST['oauth_token'] == '')
{
	echo 'error='.urlencode('Empty oauth token');
	exit;
}

$token_row = $oAuthServerTokensDAO->getByTokenAndType($_REQUEST['oauth_token'], 'request');
if (!is_array($token_row))
{
	echo 'error='.urlencode('Invalid oauth token');
	exit;
}

// $_SESSION['token'] is used to encrypt the password from web form
if (!isset($_SESSION['token']))
	$_SESSION['token'] = sha1(mt_rand() . microtime(TRUE));

if (isset($_POST['submit']))
{
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
			$oAuthServerTokensDAO->updateUserIDByToken($_REQUEST['oauth_token'], $user_id);
			
			if (isset($_REQUEST['oauth_callback']))
			{
				if (strpos($_REQUEST['oauth_callback'], '?') > 0)
					header('Location: '.$_REQUEST['oauth_callback'].'&oauth_token='.$_REQUEST['oauth_token']);
				else
					header('Location: '.$_REQUEST['oauth_callback'].'?oauth_token='.$_REQUEST['oauth_token']);
			}
			else
				echo 'User is authenticated successfully.';
			
			exit;
		}
	}
	
}

//header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
$savant->display('login.tmpl.php');
?>
