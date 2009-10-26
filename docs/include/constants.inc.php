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

if (!defined('AF_INCLUDE_PATH')) { exit; }

/**
 * constants, some more constants are loaded from table 'config' @ include/vitals.inc.php
 **/

/* config variables. if they're not in the db then it uses the installation default values: */
$_config_defaults = array();
$_config_defaults['site_name']          = '';
$_config_defaults['contact_email']      = '';
$_config_defaults['max_file_size']      = 1048576;  // 1MB
$_config_defaults['illegal_extentions'] = 'exe|asp|php|php3|bat|cgi|pl|com|vbs|reg|pcd|pif|scr|bas|inf|vb|vbe|wsc|wsf|wsh';
$_config_defaults['default_language']   = 'eng';
$_config_defaults['use_captcha']		= 0;	//use captcha?
$_config_defaults['latex_server']       = 'http://www.atutor.ca/cgi/mimetex.cgi?'; // the full URL to an external LaTeX parse
$_config_defaults['pref_defaults']      = 'a:1:{s:10:"PREF_THEME";s:7:"default";}';
$_config = $_config_defaults;

define('VERSION',		'0.1');
define('UPDATE_SERVER', 'http://update.atutor.ca');
define('SVN_TAG_FOLDER', 'http://atutorsvn.atrc.utoronto.ca/repos/atutor/tags/');

// language constants
define('DEFAULT_LANGUAGE_CODE', 'eng');
define('DEFAULT_CHARSET', 'utf-8');
define('AF_LANGUAGE_LOCALE_SEP', '-');
//$_config['default_language'] = DEFAULT_LANGUAGE_CODE;

/* User group type */
define('AF_USER_GROUP_ADMIN', 1);
define('AF_USER_GROUP_USER', 2);

/* User status */
define('AF_STATUS_DISABLED', 0);
define('AF_STATUS_ENABLED', 1);
define('AF_STATUS_DEFAULT', 2);
define('AF_STATUS_UNCONFIRMED', 3);

function get_status_by_code($status_code)
{
	if ($status_code == AF_STATUS_DISABLED)
		 return _AT('disabled');
	else if ($status_code == AF_STATUS_ENABLED)
		 return _AT('enabled');
	else if ($status_code == AF_STATUS_DEFAULT)
		 return _AT('default');
	else if ($status_code == AF_STATUS_UNCONFIRMED)
		 return _AT('unconfirmed');
	else
		return '';
}

/* User status */
/* how many days until the password reminder link expires */
define('AF_PASSWORD_REMINDER_EXPIRY', 2);

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
} else {
	$server_protocol = 'http://';
}

$dir_deep	 = substr_count(AF_INCLUDE_PATH, '..');
$url_parts	 = explode('/', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
$_base_href	 = array_slice($url_parts, 0, count($url_parts) - $dir_deep-1);
$_base_href	 = $server_protocol . implode('/', $_base_href).'/';

$endpos = strlen($_base_href); 

$_base_href	 = substr($_base_href, 0, $endpos);
$_base_path  = substr($_base_href, strlen($server_protocol . $_SERVER['HTTP_HOST']));

define('AF_BASE_HREF', $_base_href);
define('AF_GUIDES_PATH', $_base_path . 'documentation/');

/* relative uri */
$_rel_url = '/'.implode('/', array_slice($url_parts, count($url_parts) - $dir_deep-1));

?>
