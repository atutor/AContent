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

define('TR_INCLUDE_PATH', '../../include/');
define('TR_HTMLPurifier_PATH', '../../protection/xss/htmlpurifier/library/');
require(TR_INCLUDE_PATH.'vitals.inc.php');

global $contentManager;

// save updated content navigation menu into cache
echo $contentManager->printMainMenu();
?>
