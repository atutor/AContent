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

/**
 * constants, some more constants are loaded from table 'config' @ include/vitals.inc.php
 **/

/* config variables. if they're not in the db then it uses the installation default values: */
$_config_defaults = array();
$_config_defaults['site_name']          = '';
$_config_defaults['contact_email']      = '';
$_config_defaults['max_file_size']      = 10485760;  // 10MB
$_config_defaults['max_course_size']    = 104857600; // 100 MB
$_config_defaults['max_file_size']      = 10485760;  // 10MB
$_config_defaults['illegal_extentions'] = 'exe|asp|php|php3|bat|cgi|pl|com|vbs|reg|pcd|pif|scr|bas|inf|vb|vbe|wsc|wsf|wsh';
$_config_defaults['default_language']   = 'en';
$_config_defaults['use_captcha']		= 0;	//use captcha?
$_config_defaults['latex_server']       = 'http://www.atutor.ca/cgi/mimetex.cgi?'; // the full URL to an external LaTeX parse
$_config_defaults['pref_defaults']      = 'a:1:{s:10:"PREF_THEME";s:7:"default";}';
$_config_defaults['enable_template']    = 1; //use template 
$_config = $_config_defaults;

define('VERSION',		'1.3');

define('UPDATE_SERVER', 'http://update.atutor.ca/acontent');
define('RESULTS_PER_PAGE', 15);

// language constants
define('DEFAULT_LANGUAGE_CODE', 'en');
define('DEFAULT_CHARSET', 'utf-8');
define('TR_LANGUAGE_LOCALE_SEP', '-');
//$_config['default_language'] = DEFAULT_LANGUAGE_CODE;

// User group type
define('TR_USER_GROUP_ADMIN', 1);
define('TR_USER_GROUP_USER', 2);

// User status
define('TR_STATUS_DISABLED', 0);
define('TR_STATUS_ENABLED', 1);
define('TR_STATUS_DEFAULT', 2);
define('TR_STATUS_UNCONFIRMED', 3);
define('TR_STATUS_PERSONAL', 4);

// User role
define('TR_USERROLE_AUTHOR', 1);
define('TR_USERROLE_VIEWER', 2);

// User privilege
define('TR_PRIV_ISAUTHOR', 1);
define('TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE', 2);
define('TR_PRIV_IN_A_COURSE', 3);

// course size
define('TR_COURSESIZE_UNLIMITED',	   -1); 
define('TR_COURSESIZE_DEFAULT',		   -2);  /* can be changed in config.inc.php */

// course category
define('TR_COURSECATEGORY_UNCATEGORIZED',   0); 

// the maximum length of the category name in category picker
define('TR_MAX_LAN_CATEGORY_NAME', 28);

// content type
define('CONTENT_TYPE_CONTENT',  0);
define('CONTENT_TYPE_FOLDER', 1);
define('CONTENT_TYPE_WEBLINK', 2);

/* ways of releasing a test */
//define('TR_RELEASE_NEVER',		   0); // do not release 
define('TR_RELEASE_IMMEDIATE',	   1); // release after submitted
//define('TR_RELEASE_MARKED',		   2); // release after all q's marked

define('TR_KBYTE_SIZE', 1024);

/* valid date format_types:						*/
/* @see ./include/lib/output.inc.php	*/
define('TR_DATE_MYSQL_DATETIME',		1); /* YYYY-MM-DD HH:MM:SS	*/
define('TR_DATE_MYSQL_TIMESTAMP_14',	2); /* YYYYMMDDHHMMSS		*/
define('TR_DATE_UNIX_TIMESTAMP',		3); /* seconds since epoch	*/
define('TR_DATE_INDEX_VALUE',			4); /* index to the date arrays */

function get_status_by_code($status_code)
{
	if ($status_code == TR_STATUS_DISABLED)
		 return _AT('disabled');
	else if ($status_code == TR_STATUS_ENABLED)
		 return _AT('enabled');
	else if ($status_code == TR_STATUS_DEFAULT)
		 return _AT('default');
	else if ($status_code == TR_STATUS_UNCONFIRMED)
		 return _AT('unconfirmed');
	else
		return '';
}

/* User status */
/* how many days until the password reminder link expires */
define('TR_PASSWORD_REMINDER_EXPIRY', 2);

/* how long cache objects can persist	*/
/* in seconds. should be low initially, but doesn't really matter. */
/* in practice should be 0 (ie. INF)    */
define('CACHE_TIME_OUT',	60);

// separator used in composing URL
if (strpos(@ini_get('arg_separator.input'), ';') !== false) {
	define('SEP', ';');
} else {
	define('SEP', '&');
}

/* get the base url	*/
if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) {
	$server_protocol = 'https://';
} else if (@$_SERVER['HTTP_X_FORWARDED_PROTO'] =='https') {
	$server_protocol = 'https://';
} else {
	$server_protocol = 'http://';
}


$dir_deep	 = substr_count(TR_INCLUDE_PATH, '..');
$url_parts	 = explode('/', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
$_base_href	 = array_slice($url_parts, 0, count($url_parts) - $dir_deep-1);
$_base_href	 = $server_protocol . implode('/', $_base_href).'/';

$endpos = strlen($_base_href); 

$_base_href	 = substr($_base_href, 0, $endpos);
$_base_path  = substr($_base_href, strlen($server_protocol . $_SERVER['HTTP_HOST']));

define('TR_BASE_HREF', $_base_href);
define('TR_GUIDES_PATH', $_base_path . 'documentation/');

// third party URL for web accessibility validation
define('TR_ACHECKER_URL', 'http://www.achecker.ca/');
define('TR_ACHECKER_WEB_SERVICE_ID', '2f4149673d93b7f37eb27506905f19d63fbdfe2d');

/* relative uri */
$_rel_url = '/'.implode('/', array_slice($url_parts, count($url_parts) - $dir_deep-1));

/* control how user inputs get formatted on output: */
/* note: v131 not all formatting options are available on each section. */

define('TR_FORMAT_NONE',	      0); /* LEQ to ~AT_FORMAT_ALL */
define('TR_FORMAT_EMOTICONS',     1);
define('TR_FORMAT_LINKS',         2);
define('TR_FORMAT_IMAGES',        4);
define('TR_FORMAT_HTML',          8);
define('TR_FORMAT_GLOSSARY',     16);
define('TR_FORMAT_ATCODES',      32);
define('TR_FORMAT_CONTENT_DIR', 64); /* remove CONTENT_DIR */
define('TR_FORMAT_QUOTES',      128); /* remove double quotes (does this get used?) */
define('TR_FORMAT_ALL',       TR_FORMAT_EMOTICONS 
							   + TR_FORMAT_LINKS 
						       + TR_FORMAT_IMAGES 
						       + TR_FORMAT_HTML 
						       + TR_FORMAT_GLOSSARY 
							   + TR_FORMAT_ATCODES
							   + TR_FORMAT_CONTENT_DIR);

$_field_formatting = array();
$_field_formatting['input.*'] = TR_FORMAT_QUOTES; /* All input should have '<' and quotes escaped.*/

?>
