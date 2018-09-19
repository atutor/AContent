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

define('TR_INCLUDE_PATH', '../../include/');
define('TR_HTMLPurifier_PATH', '../../protection/xss/htmlpurifier/library/');
require(TR_INCLUDE_PATH.'vitals.inc.php');

global $msg, $contentManager;

//if (!isset($contentManager))
//{
//	$msg->addError('MISSING_COURSE_ID');
//	require(TR_INCLUDE_PATH.'header.inc.php');
//}

require(TR_INCLUDE_PATH.'header.inc.php');

if (isset($contentManager))
{
	echo '<p>';
	$contentManager->printSiteMapMenu();
	echo '</p>';
}
else
{
	$msg->addError('MISSING_COURSE_ID');
	$msg->printAll();
}

require(TR_INCLUDE_PATH.'footer.inc.php');
?>
