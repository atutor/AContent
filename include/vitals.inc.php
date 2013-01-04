<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }

define('TR_DEVEL', 0);
define('TR_ERROR_REPORTING', E_ALL ^ E_NOTICE); // default is E_ALL ^ E_NOTICE, use E_ALL or E_ALL + E_STRICT for developing

require(TR_INCLUDE_PATH.'lib/vital_funcs.inc.php');

/*
 * structure of this document (in order):
 *
 * 0. load config.inc.php
 * 1. initilize db connection
 * 2. load constants
 * 3. initilize session
 * 4. load $_config from table 'config'
 * 5. start language block
 * 6. load common libraries
 * 7. initialize theme and template management
 * 8. initialize a user instance without user id. 
 *    if $_SESSION['user_id'] is set, it's assigned to instance in include/header.inc.php
 * 9. register pages based on current user's priviledge
 * 10. initialize course information if $_SESSION['course_id'] is set 
 ***/

/**** 0. start system configuration options block ****/
error_reporting(0);
include_once(TR_INCLUDE_PATH.'config.inc.php');
error_reporting(TR_ERROR_REPORTING);

if (!defined('TR_INSTALL') || !TR_INSTALL) {
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Pragma: no-cache');

	$relative_path = substr(TR_INCLUDE_PATH, 0, -strlen('include/'));
	header('Location: ' . $relative_path . 'install/not_installed.php');
	exit;
}
/*** end system config block ****/

/***** 1. database connection *****/
//if (!defined('TR_REDIRECT_LOADED')){
//	require_once(TR_INCLUDE_PATH.'lib/mysql_connect.inc.php');
//}
/***** end database connection ****/

/*** 2. constants ***/
require_once(TR_INCLUDE_PATH.'constants.inc.php');

/*** 3. initilize session ***/
@set_time_limit(0);
@ini_set('session.gc_maxlifetime', '36000'); /* 10 hours */
@session_cache_limiter('private, must-revalidate');

session_name('TransformableID');
error_reporting(TR_ERROR_REPORTING);

ob_start();
session_set_cookie_params(0, $_base_path);
session_start();
$str = ob_get_contents();
ob_end_clean();
unregister_GLOBALS();

// $_user_id could be set in home/ims/ims_import.php
// @see home/ims/ims_import.php
if (isset($_user_id) && $_user_id > 0) $_SESSION['user_id'] = $_user_id;
/***** end session initilization block ****/

/***** 4. load $_config from table 'config' *****/
require(TR_INCLUDE_PATH.'phpCache/phpCache.inc.php'); // cache library
require(TR_INCLUDE_PATH.'classes/DAO/ThemesDAO.class.php');
require(TR_INCLUDE_PATH.'classes/DAO/ConfigDAO.class.php');

$configDAO = new ConfigDAO();
$rows = $configDAO->getAll();
if (is_array($rows))
{
	foreach ($rows as $id => $row)
	{
		$_config[$row['name']] = $row['value'];
	}
}

//set the timezone, php 5.3+ problem. http://atutor.ca/atutor/mantis/view.php?id=4409
date_default_timezone_set('UTC');

// define as constants. more constants are defined in include/constants.inc.php
define('EMAIL', $_config['contact_email']);
define('SITE_NAME', $_config['site_name']);
$MaxFileSize = $_config['max_file_size']; 
$MaxCourseSize = $_config['max_course_size'];
$MaxFileSize = $_config['max_file_size']; 
$IllegalExtentions = explode('|',$_config['illegal_extentions']);
/***** end loading $_config *****/

/***** 5. start language block *****/
// set current language
require(TR_INCLUDE_PATH . 'classes/Language/LanguageManager.class.php');
$languageManager = new LanguageManager();

$myLang = $languageManager->getMyLanguage();

if ($myLang === FALSE) {
	echo 'There are no languages installed!';
	exit;
}

$myLang->saveToSession();

/* set right-to-left language */
$rtl = '';
if ($myLang->isRTL()) {
	$rtl = 'rtl_'; /* basically the prefix to a rtl variant directory/filename. eg. rtl_atee */
}
/***** end language block ****/

/***** 6. load common libraries *****/
	require(TR_INCLUDE_PATH.'lib/output.inc.php');           /* output functions */
/***** end load common libraries ****/

/***** 7. initialize theme and template management *****/
	require(TR_INCLUDE_PATH.'classes/Savant2/Savant2.php');

	// set default template paths:
	$savant = new Savant2();

	if (isset($_SESSION['prefs']['PREF_THEME']) && file_exists(TR_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME']) && $_SESSION['user_id']>0) 
	{
		if (!is_dir(TR_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME']))
		{
			$_SESSION['prefs']['PREF_THEME'] = 'default';
		} 
		else 
		{
			//check if enabled
			$themesDAO = new ThemesDAO();
			$row = $themesDAO->getByID($_SESSION['prefs']['PREF_THEME']);

			if ($row['status'] == 0) 
			{
				// get default
				$_SESSION['prefs']['PREF_THEME'] = get_default_theme();
			}
		}
	} else 
	{
		$_SESSION['prefs']['PREF_THEME'] = get_default_theme();
	}

	$savant->addPath('template', TR_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/');

	require(TR_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/theme.cfg.php');

	require(TR_INCLUDE_PATH.'classes/Message/Message.class.php');
	$msg = new Message($savant);

/***** end of initialize theme and template management *****/

/***** 8. initialize user instance *****/
// used as global var
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0)
{
	// check if $_SESSION['user_id'] is valid
	include_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
	$usersDAO = new UsersDAO();
	$user = $usersDAO->getUserByID($_SESSION['user_id']);
	
	if (!$user)  // invalid user
		unset($_SESSION['user_id']);
	else
	{
		include_once(TR_INCLUDE_PATH.'classes/User.class.php');
		$_current_user = new User($_SESSION['user_id']);
	}
}
/***** end of initialize user instance *****/

/* 9. initialize course information if $course_id or $cid is set 
 * This section generates global variables: 
 * $_content_id if set, 
 * $_course_id if set or $cid is set
 * $_SESSION['s_cid']: record the last content_id on (user_id + course_id) basis
 * $_sequence_links: resume/first/next/previous content links
 */ 
if (isset($_REQUEST['_cid']) && intval($_REQUEST['_cid']) > 0) $_content_id = intval($_REQUEST['_cid']);
else if (isset($_POST['_cid']) && intval($_POST['_cid']) > 0) $_content_id = intval($_POST['_cid']);

if (isset($_REQUEST['_course_id']) && intval($_REQUEST['_course_id']) > 0) $_course_id = intval($_REQUEST['_course_id']);
else if (isset($_POST['_course_id']) && intval($_POST['_course_id']) > 0) $_course_id = intval($_POST['_course_id']);

/*
 * catia
 */
if (isset($_REQUEST['_struct_name'])) $_struct_name = strval($_REQUEST['_struct_name']);

// find course_id thru content_id
if ($_content_id > 0)
{
	include_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
	$contentDAO = new ContentDAO();
	$content_row = $contentDAO->get($_content_id);
	$_course_id = $content_row['course_id'];
}

// Generate $_SESSION['s_cid']: record the last visited content_id
// for authors and the users who have the current course in "my courses" list, 
//     save the last visited content_id into user_courses and set the session var.
//     @see ContentUtility::saveLastCid()
// for the users who don't have the current course in "my courses" list,
//     set the session var as $_GET['cid']
if ($_course_id > 0)
{
	if ($_SESSION['user_id'] > 0)
	{
		include_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');
		$userCoursesDAO = new UserCoursesDAO();
		$user_courses_row = $userCoursesDAO->get($_SESSION['user_id'], $_course_id);
		
		if ($user_courses_row && $user_courses_row['last_cid'] > 0) 
			$_SESSION['s_cid'] = $user_courses_row['last_cid'];
		else if ($_content_id > 0)
			$_SESSION['s_cid'] = $_content_id;
		else // first time accessing this course, no last cid yet
			unset($_SESSION['s_cid']);
	}
	else // guest
	{
		$_SESSION['s_cid'] = $_content_id;
	}
}

// Generate contentManager. 
// Must be called after generating $_SESSION['s_cid'] as it's used in contentManager class
if ($_course_id > 0)
{
	global $contentManager;
	
	include_once(TR_INCLUDE_PATH. '../home/classes/ContentManager.class.php');
	
	$contentManager = new ContentManager($_course_id);
	//echo("CIPPA ".count($contentManager->_menu_in_order));
	$_sequence_links = $contentManager->generateSequenceCrumbs($_content_id);
	
}

/*** 10. register pages based on user's priviledge ***/
require_once(TR_INCLUDE_PATH.'page_constants.inc.php');

/*~~~~~~~~~~~~~~~~~flash detection~~~~~~~~~~~~~~~~*/
if(isset($_COOKIE["flash"])){
	$_SESSION['flash'] = $_COOKIE["flash"];
}

if (!isset($_SESSION["flash"])) {
	$_custom_head .= '
		<script type="text/javascript">
		<!--

			//VB-Script for InternetExplorer
			function iExploreCheck()
			{
				document.writeln("<scr" + "ipt language=\'VBscript\'>");
				//document.writeln("\'Test to see if VBScripting works");
				document.writeln("detectableWithVB = False");
				document.writeln("If ScriptEngineMajorVersion >= 2 then");
				document.writeln("   detectableWithVB = True");
				document.writeln("End If");
				//document.writeln("\'This will check for the plugin");
				document.writeln("Function detectActiveXControl(activeXControlName)");
				document.writeln("   on error resume next");
				document.writeln("   detectActiveXControl = False");
				document.writeln("   If detectableWithVB Then");
				document.writeln("      detectActiveXControl = IsObject(CreateObject(activeXControlName))");
				document.writeln("   End If");
				document.writeln("End Function");
				document.writeln("</scr" + "ipt>");
				return detectActiveXControl("ShockwaveFlash.ShockwaveFlash.1");
			}


			var plugin = (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]) ? navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin : false;
			if(!(plugin) && (navigator.userAgent && navigator.userAgent.indexOf("MSIE")>=0 && (navigator.appVersion.indexOf("Win") != -1)))
				if (iExploreCheck())
					flash_detect = "flash=yes";
				else
					flash_detect = "flash=no";

			else if(plugin)
				flash_detect = "flash=yes";
			else
				flash_detect = "flash=no";

			writeCookie(flash_detect);

			function writeCookie(value)
			{
				var today = new Date();
				var the_date = new Date("December 31, 2099");
				var the_cookie_date = the_date.toGMTString();
				var the_cookie = value + ";expires=" + the_cookie_date;
				document.cookie = the_cookie;
			}
		//-->
		</script>
';
}



/*~~~~~~~~~~~~~~end flash detection~~~~~~~~~~~~~~~*/

/****************************************************/
/* compute the $_my_uri variable					*/
	$bits	  = explode(SEP, getenv('QUERY_STRING'));
	$num_bits = count($bits);
	$_my_uri  = '';

	for ($i=0; $i<$num_bits; $i++) {
//		if (	(strpos($bits[$i], 'enable=')	=== 0) 
//			||	(strpos($bits[$i], 'disable=')	=== 0)
//			||	(strpos($bits[$i], 'expand=')	=== 0)
//			||	(strpos($bits[$i], 'collapse=')	=== 0)
//			||	(strpos($bits[$i], 'lang=')		=== 0)
//			) {
		if (	(strpos($bits[$i], 'lang=')		=== 0)
			) {
			/* we don't want this variable added to $_my_uri */
			continue;
		}

		if (($_my_uri == '') && ($bits[$i] != '')) {
			$_my_uri .= '?';
		} else if ($bits[$i] != ''){
			$_my_uri .= SEP;
		}
		$_my_uri .= $bits[$i];
	}
	if ($_my_uri == '') {
		$_my_uri .= '?';
	} else {
		$_my_uri .= SEP;
	}
	$_my_uri = $_SERVER['PHP_SELF'].$_my_uri;

?>
