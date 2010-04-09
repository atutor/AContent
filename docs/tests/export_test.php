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

define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/testQuestions.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsDAO = new TestsDAO();

$tid = intval($_GET['tid']);

/* Retrieve the content_id of this test */
if (!($test_row = $testsDAO->get($tid))) {
	$msg->addError('ITEM_NOT_FOUND');
	header('Location: index.php?_course_id='.$_course_id);
	exit;
}

//export
test_qti_export($tid, $test_row['title']);
?>