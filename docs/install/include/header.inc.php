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

if (!defined('TR_INCLUDE_PATH')) { exit; }
error_reporting(E_ALL ^ E_NOTICE);

if ($step < 3) {
	error_reporting(0);
	include('../include/config.inc.php');
	error_reporting(E_ALL ^ E_NOTICE);
	if (defined('TR_INSTALL')) {
		echo 'Transformable appears to have been installed already.';
		exit;
	}
}

$install_steps[0] = array('name' => 'Introduction');
$install_steps[1] = array('name' => 'Terms of Use');
$install_steps[2] = array('name' => 'Database');
$install_steps[3] = array('name' => 'Accounts & Preferences');
$install_steps[4] = array('name' => 'Content Directory');
$install_steps[5] = array('name' => 'Save Configuration');
$install_steps[6] = array('name' => 'Anonymous Usage Collection');
$install_steps[7] = array('name' => 'Done!');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8"> 
<head>
	<title>Transformable Installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<link rel="stylesheet" href="stylesheet.css" type="text/css" />
</head>
<body>
<div style="height: 70px; vertical-align: bottom; background-color: #354A81">
	<h1 id="header">Transformable <?php echo $new_version; ?> Installation</h1>
	<img src="../images/logo.png" height="29" width="84" alt="Logo" id="logo" />
</div>
<div style="clear: all"></div>
<div class="content">