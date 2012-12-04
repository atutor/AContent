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

/*******************************************************************
 * How to add a new module
 * 1. Add a privilege row into table "privileges";
 * 2. Define the new privilege as a new constant in this script, 
 *    the privilege number must be same as privileges.privilege_id.
 *    @see below
 * 3. define all accessible pages in the new module down below.
 *    If the page is accessible by public, define it outside the
 *    if statement to check user privilege. Otherwise, define inside
 *    privilege check "if" statment.
*******************************************************************/

/* when the request is an oauth import request, this script is not loaded. 
 Because the Utility::authenticate on the following each page section 
 messes up the oauth user authentication. 
*/
global $oauth_import, $_course_id, $_content_id, $_struct_name;
if ($oauth_import) return;

// constants to map privileges.privilege_id, used to load constant pages
define('TR_PRIV_HOME', 1);
define('TR_PRIV_SYSTEM', 2);
define('TR_PRIV_COURSE_CATEGORIES_MANAGEMENT', 3);
define('TR_PRIV_USER_MANAGEMENT', 4);
define('TR_PRIV_LANGUAGE_MANAGEMENT', 5);
define('TR_PRIV_TRANSLATION', 6);
define('TR_PRIV_UPDATER', 7);
define('TR_PRIV_MANAGE_TESTS', 8);
define('TR_PRIV_FILE_MANAGER', 9);
define('TR_PRIV_PROFILE', 10);

/* constants used for menu item generation. Used in class Menu (include/classes/Menu.class.php) */
define('TR_NAV_PUBLIC', 'TR_NAV_PUBLIC');  // public menus, when no user login
define('TR_NAV_TOP', 'TR_NAV_TOP');        // top tab menus

global $_current_user;

include_once('classes/Utility.class.php');
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
	$privs[$row['privilege_id']] = $row['user_requirement'];

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

$_pages['oauth/authorization.php']['title_var'] = 'oauth_authenticate';
$_pages['oauth/authorization.php']['parent']    = TR_NAV_PUBLIC;
$_pages['oauth/authorization.php']['guide']    = 'TR_HELP_OAUTH_AUTHENTICATE';

// The scripts below need to be accessible by public. 
$_pages['documentation/web_service_api.php']['title_var'] = 'web_service_api';
$_pages['documentation/web_service_api.php']['parent'] = TR_NAV_PUBLIC;

$_pages['documentation/oauth_server_api.php']['title_var'] = 'oauth_server_api';
$_pages['documentation/oauth_server_api.php']['parent'] = TR_NAV_PUBLIC;

$_pages['tests/preview.php']['title_var'] = 'preview_questions';
$_pages['tests/preview.php']['parent']    = TR_NAV_PUBLIC;
//$_pages['tests/preview.php']['guide']     = 'TR_HELP_PREVIEW';

// home pages
if (array_key_exists(TR_PRIV_HOME, $privs) && Utility::authenticate($privs[TR_PRIV_HOME]))
{
	$_pages['home/index.php']['title_var'] = 'home';
	$_pages['home/index.php']['parent']    = TR_NAV_PUBLIC;
	$_pages['home/index.php']['guide']    = 'TR_HELP_INDEX';
	
	$_pages['home/search.php']['title_var'] = 'search_results';
	$_pages['home/search.php']['parent']    = TR_NAV_PUBLIC;
	
	// $_pages['home/course/search.php']['title_var'] is defined in home/course/outline.php with current course title
	$_pages['home/course/outline.php']['parent']    = 'home/index.php';
	$_pages['home/course/outline.php']['title_var'] = 'outline';
	
	// catia
	$_pages['home/structs/outline.php']['parent'] = 'home/index.php';
	
	if(isset($_struct_name)) 
		$_pages['home/structs/outline.php']['title'] = '"'. $_struct_name . ' based" structure outline';

	if (isset($_current_user) && ($_current_user->isAuthor() || $_current_user->isAdmin()))
	{
		if ((!isset($_course_id) || $_course_id == 0)) {
			$_pages['home/index.php']['children']  = array_merge(array('home/create_course.php'), isset($_pages['home/index.php']['children']) ? $_pages['home/index.php']['children'] : array());
			
			$_pages['home/create_course.php']['title_var'] = 'create_course';
			$_pages['home/create_course.php']['parent']    = 'home/index.php';
			$_pages['home/create_course.php']['guide']    = 'TR_HELP_CREATE_COURSE';
		}

		$_pages['home/course/course_start.php']['title_var'] = 'course_start';
		$_pages['home/course/course_start.php']['parent']    = 'home/index.php';
		$_pages['home/course/course_start.php']['guide']    = 'TR_HELP_CONTENT_WIZARD';
		
		$_pages['home/course/del_course.php']['title_var'] = 'del_course';
		$_pages['home/course/del_course.php']['parent']    = 'home/index.php';
		
		$_pages['home/course/course_property.php']['title_var'] = 'course_property';
		$_pages['home/course/course_property.php']['parent']    = 'home/index.php';
		$_pages['home/course/course_property.php']['guide']    = 'TR_HELP_COURSE_PROPERTY';

		$_pages['home/editor/add_content.php']['title_var']    = 'add_content';
		$_pages['home/editor/add_content.php']['parent']   = 'home/index.php'; 
		$_pages['home/editor/add_content.php']['guide']     = 'TR_HELP_ADD_CONTENT';
		
		$_pages['home/editor/arrange_content.php']['title_var']    = 'arrange_content';
		$_pages['home/editor/arrange_content.php']['parent']   = 'home/index.php';
		$_pages['home/editor/arrange_content.php']['guide']     = 'TR_HELP_ARRANGE_CONTENT';
		
		$_pages['home/editor/edit_content.php']['title_var'] = 'edit_content';
		$_pages['home/editor/edit_content.php']['parent']    = 'home/index.php';
		$_pages['home/editor/edit_content.php']['guide']     = 'TR_HELP_EDIT_CONTENT';
		
		$_pages['home/editor/edit_content_folder.php']['title_var'] = 'edit_content_folder';
		$_pages['home/editor/edit_content_folder.php']['parent']    = 'home/index.php';
		$_pages['home/editor/edit_content_folder.php']['guide']     = 'TR_HELP_EDIT_CONTENT_FOLDER';
		
		$_pages['home/editor/edit_content_struct.php']['title'] = 'Edit content structure';
		$_pages['home/editor/edit_content_struct.php']['parent']    = 'home/index.php';
		//$_pages['home/editor/edit_content_folder.php']['guide']     = 'TR_HELP_EDIT_CONTENT_FOLDER';
		
		$_pages['home/editor/delete_content.php']['title_var'] = 'delete_content';
		$_pages['home/editor/delete_content.php']['parent']    = 'home/index.php';
		$_pages['home/editor/delete_content.php']['guide']     = 'TR_HELP_DELETE_CONTENT';
		
		$_pages['home/editor/preview.php']['title_var'] = 'preview';
		$_pages['home/editor/preview.php']['parent']    = 'home/editor/edit_content.php';
		
		//catia
		$_pages['home/editor/forums_tool.php']['title'] = 'Forum tool';
		$_pages['home/editor/forums_tool.php']['parent'] = 'home/editor/edit_content.php';
		
		$_pages['home/editor/add_forum.php']['title'] = 'Create Forum';
		$_pages['home/editor/add_forum.php']['parent'] = 'home/editor/forums_tool.php';
		
		
		$_pages['home/editor/accessibility.php']['title_var'] = 'accessibility';
		$_pages['home/editor/accessibility.php']['parent']    = 'home/editor/edit_content.php';

		$_pages['home/editor/import_export_content.php']['title_var']    = 'content_packaging';
		$_pages['home/editor/import_export_content.php']['parent']   = 'home/index.php';
		$_pages['home/editor/import_export_content.php']['guide']     = 'TR_HELP_IMPORT_EXPORT_CONTENT';

		//Tests and Surveys 
		$_pages['tests/index.php']['title_var']    = 'manage_tests';
		$_pages['tests/index.php']['parent']   = 'home/index.php';
		$_pages['tests/index.php']['guide']     = 'TR_HELP_MANAGE_TESTS';

		$_pages['tests/create_test.php']['title_var']    = 'create_test';
		$_pages['tests/create_test.php']['parent']   = 'tests/index.php';
		$_pages['tests/create_test.php']['guide']     = 'TR_HELP_CREATE_TESTS';

		$_pages['tests/edit_test.php']['title_var']    = 'edit_test';
		$_pages['tests/edit_test.php']['parent']   = 'tests/index.php';
		$_pages['tests/edit_test.php']['guide']     = 'TR_HELP_EDIT_TESTS';

		$_pages['tests/question_db.php']['title_var']    = 'question_database';
		$_pages['tests/question_db.php']['parent']   = 'tests/index.php';
		$_pages['tests/question_db.php']['guide']     = 'TR_HELP_QUESTION_BANK';

		$_pages['tests/questions.php']['title_var']    = 'add_questions';
		$_pages['tests/questions.php']['parent']   = 'tests/index.php';
		$_pages['tests/questions.php']['guide']     = 'TR_HELP_QUESTIONS_ADD';

		$_pages['tests/question_cats.php']['title_var']    = 'question_categories';
		$_pages['tests/question_cats.php']['parent']   = 'tests/index.php';
		$_pages['tests/question_cats.php']['guide']     = 'TR_HELP_QUESTION_CATEGORIES';

		$_pages['file_manager/index.php']['title_var']    = 'file_manager';
		$_pages['file_manager/index.php']['parent']   = 'home/index.php';
		$_pages['file_manager/index.php']['guide']     = 'TR_HELP_FILE_MANAGER';
		
		

	}
}

// system pages
if (array_key_exists(TR_PRIV_SYSTEM, $privs) && Utility::authenticate($privs[TR_PRIV_SYSTEM], false))
{
	$_pages['system/index.php']['title_var'] = 'system';
	$_pages['system/index.php']['parent']    = TR_NAV_PUBLIC;
	$_pages['system/index.php']['guide']    = 'TR_HELP_SYSTEM';
}

// course categories pages
if (array_key_exists(TR_PRIV_COURSE_CATEGORIES_MANAGEMENT, $privs) && Utility::authenticate($privs[TR_PRIV_COURSE_CATEGORIES_MANAGEMENT], false))
{
	$_pages['course_category/index.php']['title_var'] = 'course_categories';
	$_pages['course_category/index.php']['parent']    = TR_NAV_TOP;
	$_pages['user/index.php']['guide']    = 'TR_HELP_COURSE_CATEGORY';

	$_pages['course_category/course_category_delete.php']['title_var'] = 'delete_course_category';
	$_pages['course_category/course_category_delete.php']['parent']    = 'course_category/index.php';
}

// user pages
if (array_key_exists(TR_PRIV_USER_MANAGEMENT, $privs) && Utility::authenticate($privs[TR_PRIV_USER_MANAGEMENT], false))
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
if (array_key_exists(TR_PRIV_LANGUAGE_MANAGEMENT, $privs) && Utility::authenticate($privs[TR_PRIV_LANGUAGE_MANAGEMENT], false))
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
if (array_key_exists(TR_PRIV_TRANSLATION, $privs) && Utility::authenticate($privs[TR_PRIV_TRANSLATION], false))
{
	$_pages['translation/index.php']['title_var'] = 'translation';
	$_pages['translation/index.php']['parent']    = TR_NAV_TOP;
	$_pages['translation/index.php']['guide']    = 'TR_HELP_TRANSLATION';
}

// profile pages
if (array_key_exists(TR_PRIV_PROFILE, $privs) && Utility::authenticate($privs[TR_PRIV_PROFILE], false))
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
if (array_key_exists(TR_PRIV_UPDATER, $privs) && Utility::authenticate($privs[TR_PRIV_UPDATER], false))
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

// manage tests
if (array_key_exists(TR_PRIV_MANAGE_TESTS, $privs) && Utility::authenticate($privs[TR_PRIV_MANAGE_TESTS], false))
{
	$_pages['tests/index.php']['title_var'] = 'manage_tests';
	$_pages['tests/index.php']['parent']    = TR_NAV_TOP;
	$_pages['tests/index.php']['guide']     = 'TR_HELP_TESTS_SURVEYS';
	$_pages['tests/index.php']['children']  = array('tests/create_test.php', 'tests/question_db.php', 'tests/question_cats.php');
	
	$_pages['tests/create_test.php']['title_var'] = 'create_test';
	$_pages['tests/create_test.php']['parent']    = 'tests/index.php';
	$_pages['tests/create_test.php']['guide']     = 'TR_HELP_CREATE_TEST';
	
	$_pages['tests/import_test.php']['title_var'] = 'import_test';
	$_pages['tests/import_test.php']['parent']    = 'tests/index.php';
	
	$_pages['tests/question_import.php']['title_var'] = 'import_question';
	$_pages['tests/question_import.php']['parent']    = 'tests/index.php';
	
	$_pages['tests/question_db.php']['title_var'] = 'question_database';
	$_pages['tests/question_db.php']['parent']    = 'tests/index.php';
	$_pages['tests/question_db.php']['guide']     = 'TR_HELP_QUESTION_DB';
	
	$_pages['tests/preview.php']['parent']    = 'tests/index.php';
	
	$_pages['tests/question_cats.php']['title_var'] = 'question_categories';
	$_pages['tests/question_cats.php']['parent']    = 'tests/index.php';
	$_pages['tests/question_cats.php']['children']  = array('tests/question_cats_manage.php');
	$_pages['tests/question_cats.php']['guide']     = 'TR_HELP_QUESTION_CATEGORIES';
	
	$_pages['tests/question_cats_manage.php']['title_var'] = 'create_category';
	$_pages['tests/question_cats_manage.php']['parent']    = 'tests/question_cats.php';
	
	$_pages['tests/question_cats_delete.php']['title_var'] = 'delete_category';
	$_pages['tests/question_cats_delete.php']['parent']    = 'tests/question_cats.php';
	
	$_pages['tests/edit_test.php']['title_var'] = 'edit_test';
	$_pages['tests/edit_test.php']['parent']    = 'tests/index.php';
	$_pages['tests/edit_test.php']['guide']     = 'TR_HELP_CREATE_TEST';
	
	$_pages['tests/preview_question.php']['title_var'] = 'preview';
	$_pages['tests/preview_question.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/results.php']['title_var'] = 'submissions';
	$_pages['tests/results.php']['parent']    = 'tests/index.php';
	
	$_pages['tests/results_all.php']['guide'] = 'TR_HELP_STUDENT_SUBMISSIONS';
	
	//$_pages['tests/results_all_quest.php']['title_var']  =  _AT('question')." "._AT('statistics');
	//$_pages['tests/results_all_quest.php']['parent'] = 'tests/index.php';
	$_pages['tests/results_all_quest.php']['guide']     = 'TR_HELP_TEST_STATISTICS';
	
	$_pages['tests/delete_test.php']['title_var'] = 'delete_test';
	$_pages['tests/delete_test.php']['parent']    = 'tests/index.php';
	
	// test questions
	$_pages['tests/create_question_truefalse.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_truefalse.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/create_question_multichoice.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_multichoice.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/create_question_multianswer.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_multianswer.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/create_question_long.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_long.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/create_question_likert.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_likert.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/create_question_matching.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_matching.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/create_question_matchingdd.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_matchingdd.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/create_question_ordering.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_ordering.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/create_question_fillinblanks.php']['title_var'] = 'create_new_question';
	$_pages['tests/create_question_fillinblanks.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_truefalse.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_truefalse.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_multichoice.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_multichoice.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_multianswer.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_multianswer.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_long.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_long.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_likert.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_likert.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_matching.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_matching.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_matchingdd.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_matchingdd.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_ordering.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_ordering.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/edit_question_fillinblanks.php']['title_var'] = 'edit_question';
	$_pages['tests/edit_question_fillinblanks.php']['parent']    = 'tests/question_db.php';
	
	$_pages['tests/delete_question.php']['title_var'] = 'delete';
	$_pages['tests/delete_question.php']['parent'] = 'tests/question_db.php';
	
	
}

// file manager
if (array_key_exists(TR_PRIV_FILE_MANAGER, $privs) && Utility::authenticate($privs[TR_PRIV_FILE_MANAGER], false))
{
	$_pages['file_manager/index.php']['title_var'] = 'file_manager';
	$_pages['file_manager/index.php']['parent']    = TR_NAV_TOP;
	$_pages['file_manager/index.php']['guide']     = 'instructor/?p=file_manager.php';
	$_pages['file_manager/index.php']['children']  = array('file_manager/new.php');
	
	$_pages['file_manager/new.php']['title_var'] = 'create_new_file';
	$_pages['file_manager/new.php']['parent']    = 'file_manager/index.php';
	
	$_pages['file_manager/zip.php']['title_var'] = 'zip_file_manager';
	$_pages['file_manager/zip.php']['parent']    = 'file_manager/index.php';
	
	$_pages['file_manager/rename.php']['title_var'] = 'rename';
	$_pages['file_manager/rename.php']['parent']    = 'file_manager/index.php';
	
	$_pages['file_manager/move.php']['title_var'] = 'move';
	$_pages['file_manager/move.php']['parent']    = 'file_manager/index.php';
	
	$_pages['file_manager/edit.php']['title_var'] = 'edit';
	$_pages['file_manager/edit.php']['parent']    = 'file_manager/index.php';
	
	$_pages['file_manager/delete.php']['title_var'] = 'delete';
	$_pages['file_manager/delete.php']['parent']    = 'file_manager/index.php';
}
?>
