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

$page = 'tests';
define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
	
	if (isset($_POST['submit_no'])) {
		$msg->addFeedback('CANCELLED');
		header('Location: index.php');
		exit;
	} else if (isset($_POST['submit_yes'])) {
		
		$tid = intval($_POST['tid']);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		if (mysql_affected_rows($db) == 1) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid";
			$result	= mysql_query($sql, $db);

			//delete test content association as well
			$sql	= "DELETE FROM ".TABLE_PREFIX."content_tests_assoc WHERE test_id=$tid";
			$result = mysql_query($sql, $db);

			/* it has to delete the results as well... */
			$sql	= "SELECT result_id FROM ".TABLE_PREFIX."tests_results WHERE test_id=$tid";
			$result	= mysql_query($sql, $db);
			if ($row = mysql_fetch_array($result)) {
				$result_list = '('.$row['result_id'];

				while ($row = mysql_fetch_array($result)) {
					$result_list .= ','.$row['result_id'];
				}
				$result_list .= ')';
			}

			if ($result_list != '') {
				$sql	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id IN $result_list";
				$result	= mysql_query($sql, $db);


				$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE test_id=$tid";
				$result	= mysql_query($sql, $db);
			}
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.TR_BASE_HREF.'tests/index.php');
		exit;

	} /* else: */

	require_once(TR_INCLUDE_PATH.'header.inc.php');

	$_GET['tid'] = intval($_GET['tid']);

	$sql	= "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=$_GET[tid] AND course_id=$_SESSION[course_id]";
	$result	= mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	unset($hidden_vars);
	$hidden_vars['tid'] = $_GET['tid'];

	$msg->addConfirm(array('DELETE_TEST', $row['title']), $hidden_vars);
	$msg->printConfirm();

	require_once(TR_INCLUDE_PATH.'footer.inc.php');
?>