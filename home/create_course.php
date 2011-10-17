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

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

// make sure the user has author privilege
Utility::authenticate(TR_PRIV_ISAUTHOR);

require(TR_INCLUDE_PATH.'header.inc.php');
$savant->display('home/create_course.tmpl.php');
require(TR_INCLUDE_PATH.'footer.inc.php');
?>