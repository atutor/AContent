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

define('TR_INCLUDE_PATH', '../../include/');
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