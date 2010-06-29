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
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');

global $msg, $contentManager, $_course_id;

if ($_course_id <= 0)
{
	$msg->addError('MISSING_COURSE_ID');
	header('Location: '.TR_BASE_HREF.'home/index.php');
	exit;
}

$coursesDAO = new CoursesDAO();

require(TR_INCLUDE_PATH.'header.inc.php'); 

$savant->assign('course_id', $_course_id);
$savant->display('home/course/course_start.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php'); 
?>