<?php
/************************************************************************/
/* AContent                                                        									*/
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

$page = 'tests';
define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

$tid = intval($_REQUEST['tid']);
$testsDAO = new TestsDAO();
$row = $testsDAO->get($tid);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?_course_id='.$_course_id);
	exit;
} else if (isset($_POST['submit'])) {
	if ($testsDAO->Update($_POST['tid'], $_POST['title'], $_POST['description']))
	{
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');		
		header('Location: index.php?_course_id='.$_course_id);
		exit;
	}
}

$onload = 'document.form.title.focus();';

require_once(TR_INCLUDE_PATH.'header.inc.php');
$msg->printErrors();

$savant->assign('course_id', $_course_id);
$savant->assign('tid', $tid);
$savant->assign('row', $row);

$savant->display('tests/create_edit_test.tmpl.php');

require (TR_INCLUDE_PATH.'footer.inc.php'); 

?>