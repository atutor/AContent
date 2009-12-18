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

define('TR_INCLUDE_PATH', 'include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');

// unset all session variables
session_unset();
$_SESSION = array();

$msg->addFeedback('LOGOUT');
header('Location: index.php');
exit;
?>