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
include_once(TR_INCLUDE_PATH.'classes/DAO/CourseCategoriesDAO.class.php');

$courseCategoriesDAO = new CourseCategoriesDAO();

$ids = explode(',', $_REQUEST['id']);

if (isset($_POST['submit_no'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} 
else if (isset($_POST['submit_yes']))
{
	foreach($ids as $id) 
	{
		$courseCategoriesDAO->Delete($id);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
}

require(TR_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);

foreach($ids as $id) 
{
	$row = $courseCategoriesDAO->get($id);
	$names[] = $row['category_name'];
}

$names_html = '<ul>'.html_get_list($names).'</ul>';
$hidden_vars['id'] = $_REQUEST['id'];

$msg->addConfirm(array('DELETE_COURSE_CATEGORY', $names_html), $hidden_vars);
$msg->printConfirm();

require(TR_INCLUDE_PATH.'footer.inc.php');
?>
