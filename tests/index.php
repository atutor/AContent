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

$page = 'tests';
define('TR_INCLUDE_PATH', '../include/');
define('TR_HTMLPurifier_PATH', '../protection/xss/htmlpurifier/library/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: edit_test.php?tid='.$_GET['id'].'&_course_id='.$_course_id);
	exit;
} else if (isset($_GET['preview'], $_GET['id'])) {
	header('Location: preview.php?tid='.$_GET['id'].'&_course_id='.$_course_id);
	exit;
} else if (isset($_GET['questions'], $_GET['id'])) {
	header('Location: questions.php?tid='.$_GET['id'].'&_course_id='.$_course_id);
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: delete_test.php?tid='.$_GET['id'].'&_course_id='.$_course_id);
	exit;
} else if (isset($_GET['export'], $_GET['id'])){
	header('Location: export_test.php?tid='.$_GET['id'].'&_course_id='.$_course_id);
} else if (isset($_GET['edit']) 
		|| isset($_GET['preview']) 
		|| isset($_GET['questions']) 
		|| isset($_GET['delete'])
		|| isset($_GET['export'])) {

	$msg->addError('NO_ITEM_SELECTED');
}

$testsDAO = new TestsDAO();
/* get a list of all the tests we have, and links to create, edit, delete, preview */
$rows = $testsDAO->getByCourseID($_course_id);

require_once(TR_INCLUDE_PATH.'header.inc.php');

$savant->assign('course_id', $_course_id);
$savant->assign('rows', $rows);

$savant->display('tests/index.tmpl.php');

require_once(TR_INCLUDE_PATH.'footer.inc.php'); ?>
