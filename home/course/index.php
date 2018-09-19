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
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_HTMLPurifier_PATH.'HTMLPurifier.auto.php');

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

global $msg, $contentManager, $_course_id;

if ($_course_id <= 0)
{
	$msg->addError('MISSING_COURSE_ID');
	header('Location: '.TR_BASE_HREF.'home/index.php');
	exit;
}


if (isset($_sequence_links['resume']['url'])) {
	$url = $_sequence_links['resume']['url'];
} else if (isset($_sequence_links['first']['url'])) {
	$url = $_sequence_links['first']['url'];
} else
	$url = TR_BASE_HREF.'home/course/course_start.php?_course_id='.$_course_id;

header('Location: '.$url);
?>
