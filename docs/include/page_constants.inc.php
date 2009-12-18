<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/* constants to map privileges.privilege_id, used to load constant pages */

define('TR_PRIV_HOME', 1);
define('TR_PRIV_SYSTEM', 2);
define('TR_PRIV_USER_MANAGEMENT', 3);
define('TR_PRIV_LANGUAGE_MANAGEMENT', 4);
define('TR_PRIV_TRANSLATION', 5);
define('TR_PRIV_UPDATER', 6);
define('TR_PRIV_PROFILE', 7);

/* constants used for menu item generation. Used in class Menu (include/classes/Menu.class.php) */
define('TR_NAV_PUBLIC', 'TR_NAV_PUBLIC');  // public menus, when no user login
define('TR_NAV_TOP', 'TR_NAV_TOP');        // top tab menus

global $_current_user;

include_once('classes/DAO/PrivilegesDAO.class.php');
$priviledgesDAO = new PrivilegesDAO();

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] <> 0)
{
	$rows = $priviledgesDAO->getUserPrivileges($_SESSION['user_id']);
}
else
{
	$rows = $priviledgesDAO->getPublicPrivileges();
}

foreach ($rows as $row)
	$privs[] = $row['privilege_id'];

/* initialize pages accessed by public */
//$_pages[TR_NAV_PUBLIC] = array('index.php' => array('parent'=>TR_NAV_PUBLIC));

/* define all accessible pages */
// 1. public pages
$_pages['register.php']['title_var'] = 'registration';
$_pages['register.php']['parent']    = TR_NAV_PUBLIC;
$_pages['register.php']['guide']    = 'TR_HELP_REGISTRATION';

$_pages['confirm.php']['title_var'] = 'confirm';
$_pages['confirm.php']['parent']    = TR_NAV_PUBLIC;

$_pages['login.php']['title_var'] = 'login';
$_pages['login.php']['parent']    = TR_NAV_PUBLIC;
$_pages['login.php']['guide']    = 'TR_HELP_LOGIN';
$_pages['login.php']['children']  = array_merge(array('password_reminder.php'), isset($_pages['login.php']['children']) ? $_pages['login.php']['children'] : array());

$_pages['logout.php']['title_var'] = 'logout';
$_pages['logout.php']['parent']    = TR_NAV_PUBLIC;

$_pages['password_reminder.php']['title_var'] = 'password_reminder';
$_pages['password_reminder.php']['parent']    = 'login.php';
$_pages['password_reminder.php']['guide']    = 'TR_HELP_PASSWORD_REMINDER';

$_pages['oauth/oauth_authenticate.php']['title_var'] = 'oauth_authenticate';
$_pages['login.php']['parent']    = TR_NAV_PUBLIC;
$_pages['login.php']['guide']    = 'TR_HELP_OAUTH_AUTHENTICATE';

// The scripts below need to be accessible by public. 
$_pages['guideline/view_guideline.php']['title_var'] = 'view_guideline';   // used in web service validation response
$_pages['checker/suggestion.php']['title_var'] = 'details';
$_pages['documentation/web_service_api.php']['title_var'] = 'web_service_api';
$_pages['documentation/oauth_server_api.php']['title_var'] = 'oauth_server_api';

// home pages
if (in_array(TR_PRIV_HOME, $privs))
{
	$_pages['home/index.php']['title_var'] = 'home';
	$_pages['home/index.php']['parent']    = TR_NAV_PUBLIC;
	$_pages['home/index.php']['guide']    = 'TR_HELP_INDEX';
	
	if (isset($_current_user) && $_current_user->isAuthor())
	{
		$_pages['home/index.php']['children']  = array_merge(array('home/create_course.php'), isset($_pages['home/index.php']['children']) ? $_pages['home/index.php']['children'] : array());
		
		$_pages['home/create_course.php']['title_var'] = 'create_course';
		$_pages['home/create_course.php']['parent']    = 'home/index.php';
		$_pages['home/create_course.php']['guide']    = 'TR_HELP_CREATE_COURSE';
	}
}

// system pages
if (in_array(TR_PRIV_SYSTEM, $privs))
{
	$_pages['system/index.php']['title_var'] = 'system';
	$_pages['system/index.php']['parent']    = TR_NAV_PUBLIC;
	$_pages['system/index.php']['guide']    = 'TR_HELP_SYSTEM';
}

// user pages
if (in_array(TR_PRIV_USER_MANAGEMENT, $privs))
{
	$_pages['user/index.php']['title_var'] = 'users';
	$_pages['user/index.php']['parent']    = TR_NAV_TOP;
	$_pages['user/index.php']['children']  = array_merge(array('user/user_create_edit.php',
	                                                           'user/user_group.php'), 
	                                                     isset($_pages['user/index.php']['children']) ? $_pages['user/index.php']['children'] : array());
	$_pages['user/index.php']['guide']    = 'TR_HELP_USER';

	$_pages['user/user_create_edit.php']['title_var'] = 'create_user';
	$_pages['user/user_create_edit.php']['parent']    = 'user/index.php';
	$_pages['user/user_create_edit.php']['guide']    = 'TR_HELP_CREATE_EDIT_USER';
	
	$_pages['user/user_password.php']['title_var'] = 'change_password';
	$_pages['user/user_password.php']['parent']    = 'user/index.php';
	$_pages['user/user_password.php']['guide']    = 'TR_HELP_USER_PASSWORD';

	$_pages['user/user_delete.php']['title_var'] = 'delete_user';
	$_pages['user/user_delete.php']['parent']    = 'user/index.php';

	$_pages['user/user_group.php']['title_var'] = 'user_group';
	$_pages['user/user_group.php']['parent']    = 'user/index.php';
	$_pages['user/user_group.php']['children']  = array_merge(array('user/user_group_create_edit.php'), 
	                                                     isset($_pages['user/user_group.php']['children']) ? $_pages['user/user_group.php']['children'] : array());
	$_pages['user/user_group.php']['guide']    = 'TR_HELP_USER_GROUP';
	
	$_pages['user/user_group_create_edit.php']['title_var'] = 'create_edit_user_group';
	$_pages['user/user_group_create_edit.php']['parent']    = 'user/user_group.php';
	$_pages['user/user_group_create_edit.php']['guide']    = 'TR_HELP_CREATE_EDIT_USER_GROUP';
	
	$_pages['user/user_group_delete.php']['title_var'] = 'delete_user_group';
	$_pages['user/user_group_delete.php']['parent']    = 'user/user_group.php';
}

// language pages
if (in_array(TR_PRIV_LANGUAGE_MANAGEMENT, $privs))
{
	$_pages['language/index.php']['title_var'] = 'language';
	$_pages['language/index.php']['parent']    = TR_NAV_TOP;
	$_pages['language/index.php']['children']  = array_merge(array('language/language_add_edit.php'), 
	                                                     isset($_pages['language/index.php']['children']) ? $_pages['language/index.php']['children'] : array());
	$_pages['language/index.php']['guide']    = 'TR_HELP_LANGUAGE';

	$_pages['language/language_add_edit.php']['title_var'] = 'add_language';
	$_pages['language/language_add_edit.php']['parent']    = 'language/index.php';
	$_pages['language/language_add_edit.php']['guide']    = 'TR_HELP_ADD_EDIT_LANGUAGE';
	
	$_pages['language/language_delete.php']['title_var'] = 'delete_language';
	$_pages['language/language_delete.php']['parent'] = 'language/index.php';
}

// translation
if (in_array(TR_PRIV_TRANSLATION, $privs))
{
	$_pages['translation/index.php']['title_var'] = 'translation';
	$_pages['translation/index.php']['parent']    = TR_NAV_TOP;
	$_pages['translation/index.php']['guide']    = 'TR_HELP_TRANSLATION';
}

// profile pages
if (in_array(TR_PRIV_PROFILE, $privs))
{
	$_pages['profile/index.php']['title_var'] = 'profile';
	$_pages['profile/index.php']['parent']    = TR_NAV_TOP;
	$_pages['profile/index.php']['guide']    = 'TR_HELP_PROFILE';
	$_pages['profile/index.php']['children']  = array_merge(array('profile/change_password.php', 
	                                                              'profile/change_email.php'), 
	                                                        isset($_pages['profile/index.php']['children']) ? $_pages['profile/index.php']['children'] : array());
	
	$_pages['profile/change_password.php']['title_var'] = 'change_password';
	$_pages['profile/change_password.php']['parent']    = 'profile/index.php';
	$_pages['profile/change_password.php']['guide']    = 'TR_HELP_CHANGE_PASSWORD';
	
	$_pages['profile/change_email.php']['title_var'] = 'change_email';
	$_pages['profile/change_email.php']['parent']    = 'profile/index.php';
	$_pages['profile/change_email.php']['guide']    = 'TR_HELP_CHANGE_EMAIL';
}

// updater pages
if (in_array(TR_PRIV_UPDATER, $privs))
{
	$_pages['updater/index.php']['title_var'] = 'updater';
	$_pages['updater/index.php']['parent']    = TR_NAV_TOP;
	$_pages['updater/index.php']['guide']    = 'TR_HELP_UPDATER';
	$_pages['updater/index.php']['children']  = array_merge(array('updater/myown_patches.php', 
	                                                              'updater/patch_create.php'), 
	                                                        isset($_pages['updater/index.php']['children']) ? $_pages['updater/index.php']['children'] : array());
	
	$_pages['updater/myown_patches.php']['title_var'] = 'myown_updates';
	$_pages['updater/myown_patches.php']['parent']    = 'updater/index.php';
	$_pages['updater/myown_patches.php']['children']    = array('updater/patch_create.php');
	
	$_pages['updater/patch_create.php']['title_var'] = 'create_update';
	$_pages['updater/patch_create.php']['parent']    = 'updater/index.php';
	$_pages['updater/patch_create.php']['guide']    = 'TR_HELP_CREATE_UPDATE';

	$_pages['updater/patch_edit.php']['title_var'] = 'edit_update';
	$_pages['updater/patch_edit.php']['parent']    = 'updater/index.php';

	$_pages['updater/patch_delete.php']['title_var'] = 'delete_update';
	$_pages['updater/patch_delete.php']['parent']    = 'updater/index.php';
}
?>