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

if (!defined('TR_INCLUDE_PATH')) { exit; }

define('TR_DEVEL', 1);
define('TR_ERROR_REPORTING', E_ALL ^ E_NOTICE); // default is E_ALL ^ E_NOTICE, use E_ALL or E_ALL + E_STRICT for developing

// Emulate register_globals off. src: http://php.net/manual/en/faq.misc.php#faq.misc.registerglobals
function unregister_GLOBALS() {
   if (!ini_get('register_globals')) { return; }

   // Might want to change this perhaps to a nicer error
   if (isset($_REQUEST['GLOBALS'])) { die('GLOBALS overwrite attempt detected'); }

   // Variables that shouldn't be unset
   $noUnset = array('GLOBALS','_GET','_POST','_COOKIE','_REQUEST','_SERVER','_ENV', '_FILES');
   $input = array_merge($_GET,$_POST,$_COOKIE,$_SERVER,$_ENV,$_FILES,isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
  
   foreach ($input as $k => $v) {
       if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) { unset($GLOBALS[$k]); }
   }
}

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
if (intval($_REQUEST['_cid']) > 0) $_content_id = intval($_REQUEST['_cid']);
else if (intval($_POST['_cid']) > 0) $_content_id = intval($_POST['_cid']);

if (intval($_REQUEST['_course_id']) > 0) $_course_id = intval($_REQUEST['_course_id']);
else if (intval($_POST['_course_id']) > 0) $_course_id = intval($_POST['_course_id']);

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
	$_sequence_links = $contentManager->generateSequenceCrumbs($_content_id);
}

/*** 10. register pages based on user's priviledge ***/
require_once(TR_INCLUDE_PATH.'page_constants.inc.php');

/**
 * This function is used for printing variables for debugging.
 * @access  public
 * @param   mixed $var	The variable to output
 * @param   string $title	The name of the variable, or some mark-up identifier.
 * @author  Joel Kronenberg
 */
function debug($var, $title='') {
	if (!defined('TR_DEVEL') || !TR_DEVEL) {
		return;
	}
	
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}

/**
* This function is used for printing variables into log file for debugging.
* @access  public
* @param   mixed $var	The variable to output
* @param   string $log	The location of the log file. If not provided, use the default one.
* @author  Cindy Qi Li
*/
function debug_to_log($var, $log='') {
	if (!defined('TR_DEVEL') || !TR_DEVEL) {
		return;
	}
	
	if ($log == '') $log = TR_CONTENT_DIR. 'debug.log';
	$handle = fopen($log, 'a');
	fwrite($handle, "\n\n");
	fwrite($handle, date("F j, Y, g:i a"));
	fwrite($handle, "\n");
	fwrite($handle, var_export($var,1));
	
	fclose($handle);
}

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

function my_add_null_slashes( $string ) {
//    return mysql_real_escape_string(stripslashes($string));
    return addslashes(stripslashes($string));
}

function my_null_slashes($string) {
	return $string;
}

if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes   = 'my_add_null_slashes';
	$stripslashes = 'stripslashes';
} else {
	$addslashes   = 'mysql_real_escape_string';
	$stripslashes = 'my_null_slashes';
}

/**
 * If MBString extension is loaded, then use it.
 * Otherwise we will have to use include/utf8 library
 */
 if (extension_loaded('mbstring')){
	 $strtolower = 'mb_strtolower';
	 $strtoupper = 'mb_strtoupper';
	 $substr = 'mb_substr';
	 $strpos = 'mb_strpos';
	 $strrpos = 'mb_strrpos';
	 $strlen = 'mb_strlen';
 } else {
 	 $strtolower = 'utf8_strtolower';
	 $strtoupper = 'utf8_strtoupper';
	 $substr = 'utf8_substr';
	 $strpos = 'utf8_strpos';
	 $strrpos = 'utf8_strrpos';
	 $strlen = 'utf8_strlen';
 }

function get_default_theme() {
	$themesDAO = new ThemesDAO();
	
	$rows = $themesDAO->getDefaultTheme();

	if (!is_dir(TR_INCLUDE_PATH . '../themes/' . $rows[0]['dir_name']))
		return 'default';
	else
		return $rows[0]['dir_name'];
}

?>
