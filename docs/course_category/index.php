<?php
/************************************************************************/
/* AContent                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
include(TR_INCLUDE_PATH.'vitals.inc.php');
include(TR_INCLUDE_PATH.'classes/DAO/CourseCategoriesDAO.class.php');

$courseCategoriesDAO = new CourseCategoriesDAO();

// handle submit
if (isset($_POST['edit']) && isset($_POST['id']) && count($_POST['id']) > 1) {
	$msg->addError('SELECT_ONE_ITEM');
} else if (isset($_POST['edit'], $_POST['id'])) {
	header('Location: course_category_edit.php?id='.$_POST['id'][0]);
	exit;
} else if ( isset($_POST['delete'], $_POST['id'])) {
	$ids = implode(',', $_POST['id']);
	header('Location: course_category_delete.php?id='.$ids);
	exit;
} else if (isset($_POST['edit']) || isset($_POST['delete'])) {
	$msg->addError('NO_ITEM_SELECTED');
} else if (isset($_POST['add'])) {
	if ($courseCategoriesDAO->Create($_POST['category_name']))
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
}

$savant->assign('rows', $courseCategoriesDAO->getAll());

$savant->display('course_category/index.tmpl.php');

?>
