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

define('AF_INCLUDE_PATH', '../include/');
include_once(AF_INCLUDE_PATH.'vitals.inc.php');
include_once(AF_INCLUDE_PATH.'classes/DAO/ConfigDAO.class.php');
include_once(AF_INCLUDE_PATH.'classes/DAO/ThemesDAO.class.php');
include_once(AF_INCLUDE_PATH.'classes/Utility.class.php');

// handle submit
if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit']) || isset($_POST['factory_default'])) {
	if (isset($_POST['submit']))
	{
		$missing_fields = array();
	
		$_POST['site_name']          = trim($_POST['site_name']);
		$_POST['contact_email']      = trim($_POST['contact_email']);
		$_POST['default_language']   = trim($_POST['default_language']);
		$_POST['max_file_size']      = intval($_POST['max_file_size']);
		$_POST['max_file_size']      = max(0, $_POST['max_file_size']);
		$_POST['illegal_extentions'] = str_replace(array('  ', ' '), array(' ','|'), $_POST['illegal_extentions']);
		$_POST['latex_server']       = (trim($_POST['latex_server'])==''?$_config['latex_server']:trim($_POST['latex_server']));
		$_POST['use_captcha']        = $_POST['use_captcha'] ? 1 : 0;
	
		//check that all values have been set	
		if (!$_POST['site_name']) {
			$missing_fields[] = _AT('site_name');
		}
	
		/* email check */
		if (!$_POST['contact_email']) {
			$missing_fields[] = _AT('contact_email');
		} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/", $_POST['contact_email'])) {
			$msg->addError('EMAIL_INVALID');	
		}
	
		if ($missing_fields) {
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}

		if (!$msg->containsErrors()) {
			$_config['site_name'] = $addslashes($_POST['site_name']);
			$_config['contact_email'] = $addslashes($_POST['contact_email']);
			$_config['default_language'] = $addslashes($_POST['default_language']);
			$_config['max_file_size'] = $_POST['max_file_size'];
			$_config['illegal_extentions'] = $addslashes($_POST['illegal_extentions']);
			$_config['latex_server'] = $addslashes($_POST['latex_server']);
			$_config['use_captcha'] = $_POST['use_captcha'];
		}
		
		// set $_config['pref_defaults']
		$pref_defaults['PREF_THEME'] = $addslashes($_POST['theme']);
		$_config['pref_defaults'] = serialize($pref_defaults);
	}
	else
	{
		// don't reset 'site name' and 'contact email'
		$_config['default_language'] = $_config_defaults['default_language'];
		$_config['max_file_size'] = $_config_defaults['max_file_size'];
		$_config['illegal_extentions'] = $_config_defaults['illegal_extentions'];
		$_config['latex_server'] = $_config_defaults['latex_server'];
		$_config['use_captcha'] = $_config_defaults['use_captcha'];
		$_config['pref_defaults'] = $_config_defaults['pref_defaults'];
	}
		
	if (!$msg->containsErrors()) {
		$configDAO = new ConfigDAO();
		foreach ($_config as $name => $value) {
			// the isset() is needed to avoid overridding settings that don't get set here (ie. modules)
			if (stripslashes($value) != $_config_defaults[$name]) {
				$configDAO->Replace($name, $value);
			} else {
				$configDAO->Delete($name);
			}
		}

		// set $_config['pref_defaults'] into session variable
		$pref_defaults = unserialize($_config['pref_defaults']);
		if (is_array($pref_defaults))
			foreach ($pref_defaults as $name => $value)
				$pref_defaults[$name] = $value;
		
		Utility::assign_session_prefs($pref_defaults);
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}
// end of handle submit

/*****************************/
/* template starts down here */

// get all enabled themes
$themesDAO = new ThemesDAO();
$theme_rows = $themesDAO->getEnabledTheme();
$savant->assign('enabled_themes', $theme_rows);

$savant->assign('title', _AT("system_settings"));
$savant->assign('config', $_config);
$savant->assign('languageManager', $languageManager);

$savant->display('system/index.tmpl.php');

?>