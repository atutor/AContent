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
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

$tid = intval($_REQUEST['tid']);
$rids = explode(',', $_REQUEST['rid']);
foreach ($rids as $k => $id) {
	$rids[$k] = intval($id);
}
$rid = implode(',', $rids);

// Check that the user deletes submissions in his own test; if not, exit like authenticate()
$sql	= "SELECT count(*) AS cnt FROM ".TABLE_PREFIX."tests_results R LEFT JOIN ".TABLE_PREFIX."tests USING (test_id) WHERE result_id IN ($rid) AND course_id = $_SESSION[course_id] AND R.test_id = $tid";

$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);
if ($row['cnt'] < count($rids)) {
	exit;
}

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.TR_BASE_HREF.'tests/results.php?tid='.$tid);
	exit;

} else if (isset($_POST['submit_yes'])) {
	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id IN ($rid)";
	$result	= mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE result_id IN ($rid)";
	$result	= mysql_query($sql, $db);
		
	$msg->addFeedback('RESULT_DELETED');
	header('Location: '.TR_BASE_HREF.'tests/results.php?tid='.$tid);
	exit;
} 

$_pages['tests/delete_result.php']['title_var']  = 'delete_results';
$_pages['tests/delete_result.php']['parent'] = 'tests/results.php?tid='.$tid;

$_pages['tests/results.php?tid='.$tid]['title_var'] = 'submissions';
$_pages['tests/results.php?tid='.$tid]['parent'] = 'tests/index.php';

require_once(TR_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);
$hidden_vars['tid'] = $tid;
$hidden_vars['rid'] = $rid;
$msg->addConfirm(array('DELETE', _AT('submissions') .': <strong>'. count($rids) .'</strong>'), $hidden_vars);

$msg->printConfirm();

require_once(TR_INCLUDE_PATH.'footer.inc.php');
?>