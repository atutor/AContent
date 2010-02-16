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

global $msg, $contentManager, $_course_id;

if ($_course_id <= 0)
{
	$msg->addError('MISSING_COURSE_ID');
	header('Location: '.TR_BASE_HREF.'home/index.php');
	exit;
}

if (isset($_sequence_links['resume']['url']))
	$url = $_sequence_links['resume']['url'];
else if (isset($_sequence_links['first']['url']))
	$url = $_sequence_links['first']['url'];
else
{
	// go to content wizard
}

header('Location: '.$url);
?>