<?php
/************************************************************************/
/* AContent                                                        									*/
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');

global $_current_user;

if (!isset($_current_user))
{
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
	if (isset($_POST['is_author'])) $is_author = 1;
	else $is_author = 0;
		
	$usersDAO = new UsersDAO();
	$user_row = $usersDAO->getUserByID($_SESSION['user_id']);
	
	if ($usersDAO->Update($_SESSION['user_id'], 
	                  $user_row['user_group_id'],
                      $user_row['login'],
	                  $user_row['email'],
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
	                  $_POST['status']))
	
	{
		$msg->addFeedback('PROFILE_UPDATED');
	}
}

$row = $_current_user->getInfo();

if (!isset($_POST['submit'])) {
	$_POST = $row;
}

/* template starts here */
$savant->assign('row', $row);

global $onload;
$onload = 'document.form.first_name.focus();';

$savant->display('profile/index.tmpl.php');
?>