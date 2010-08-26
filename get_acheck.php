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

/* this file simply gets the TR_CONTENT_DIR/CID.html file that was generated
 * by the AChecker page of the content editor.
 * there is no authentication on this page. either the file exists (in which
 * case it is then quickly deleted after), or it doesn't.
 */

$_user_location	= 'public';

define('TR_INCLUDE_PATH', 'include/');
require(TR_INCLUDE_PATH . '/vitals.inc.php');

//get path to file
$args = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']));
$file = TR_CONTENT_DIR . $args;

//check that this file is within the content directory & exists

$real = realpath($file);

if (substr($real, 0, strlen(TR_CONTENT_DIR)) == TR_CONTENT_DIR) {
 	header('Content-Type: text/html');
	echo file_get_contents($real);
	exit;
} else {
	header('HTTP/1.1 404 Not Found');
	exit;
}


?>