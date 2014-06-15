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

define('TR_INCLUDE_PATH', '../include/');
include(TR_INCLUDE_PATH.'vitals.inc.php');
include(TR_INCLUDE_PATH.'classes/DAO/UserGroupsDAO.class.php');
unset($_SESSION['course_id']);

// initialize constants
$results_per_page = 50;
$dao = new DAO();

//Handle submit of form2, no validation that the current user is an author.. just that his user id should be in the session variable
if( (isset($_POST['group_name'])) && (isset($_POST['id'])) && (isset($_SESSION['user_id'])) && (count($_POST['id']) > 0) ){
	echo $_POST['group_name'];
	print_r($_POST['id']);
	global $_user_id;
	echo "<br> global user id ".$_SESSION['user_id']."<br>";
	for($i=0;$i<count($_POST['id']);$i++){
		$sql="INSERT INTO ".TABLE_PREFIX."group_users (group_name, group_creator, user_id)
     				        VALUES ('".$_POST['group_name']."',".$_SESSION['user_id'].",".$_POST['id'][$i].")";
     	echo $sql;
     	echo "<br>";
		$dao->execute($sql);
	}
}

// page initialize
if ($_GET['reset_filter']) {
	unset($_GET);
}

$page_string = '';
$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('login' => 1, 'public_field' => 1, 'first_name' => 1, 'last_name' => 1, 'user_group' => 1, 'email' => 1, 'status' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'login';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'login';
} else {
	// no order set
	$order = 'asc';
	$col   = 'login';
}
if (isset($_GET['status']) && ($_GET['status'] != '')) {
	$_GET['status'] = intval($_GET['status']);
	$status = '=' . intval($_GET['status']);
	$page_string .= htmlspecialchars(SEP).'status'.$status;
} else {
	$status = '<>-1';
	$_GET['status'] = '';
}

if (isset($_GET['include']) && $_GET['include'] == 'one') {
	$checked_include_one = ' checked="checked"';
	$page_string .= htmlspecialchars(SEP).'include=one';
} else {
	$_GET['include'] = 'all';
	$checked_include_all = ' checked="checked"';
	$page_string .= htmlspecialchars(SEP).'include=all';
}

if ($_GET['search']) {
	$page_string .= htmlspecialchars(SEP).'search='.urlencode($stripslashes($_GET['search']));
	$search = $addslashes($_GET['search']);
	$search = explode(' ', $search);

	if ($_GET['include'] == 'all') {
		$predicate = 'AND ';
	} else {
		$predicate = 'OR ';
	}

	$sql = '';
	foreach ($search as $term) {
		$term = trim($term);
		$term = str_replace(array('%','_'), array('\%', '\_'), $term);
		if ($term) {
			$term = '%'.$term.'%';
			$sql .= "((U.first_name LIKE '$term') OR (U.last_name LIKE '$term') OR (U.email LIKE '$term') OR (U.login LIKE '$term')) $predicate";
		}
	}
	$sql = '('.substr($sql, 0, -strlen($predicate)).')';
	$search = $sql;
} else {
	$search = '1';
}

if ($_GET['user_group_id'] && $_GET['user_group_id'] <> -1) {
	$user_group_sql = "U.user_group_id = ".$_GET['user_group_id'];
	$page_string .= htmlspecialchars(SEP).'user_group_id='.urlencode($_GET['user_group_id']);
}
else
{
	$user_group_sql = '1';
}

$sql	= "SELECT COUNT(user_id) AS cnt FROM ".TABLE_PREFIX."users U WHERE status $status AND $search AND $user_group_sql";

$rows = $dao->execute($sql);
$num_results = $rows[0]['cnt'];

$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

if ( isset($_GET['apply_all']) && $_GET['change_status'] >= -1) {
	$offset = 0;
	$results_per_page = 999999;
}

$sql = "SELECT U.user_id, U.login, U.first_name, U.last_name, UG.title user_group, U.email, U.status
          FROM ".TABLE_PREFIX."users U, ".TABLE_PREFIX."user_groups UG
          WHERE U.user_group_id = UG.user_group_id
          AND U.status $status AND $search AND $user_group_sql ORDER BY $col $order LIMIT $offset, $results_per_page";

$user_rows = $dao->execute($sql);

if ( isset($_GET['apply_all']) && $_GET['change_status'] >= -1) {
	$ids = '';
	while ($row = mysql_fetch_assoc($result)) {
		$ids .= $row['user_id'].','; 
	}
	$ids = substr($ids,0,-1);
	$status = intval($_GET['change_status']);

	if ($status==-1) {
		header('Location: user_delete.php?id='.$ids);
		exit;
	} else {
		header('Location: user_status.php?ids='.$ids.'&status='.$status);
		exit;
	}
}

$userGroupsDAO = new UserGroupsDAO();

$savant->assign('user_rows', $user_rows);
$savant->assign('all_user_groups', $userGroupsDAO->getAll());
$savant->assign('results_per_page', $results_per_page);
$savant->assign('num_results', $num_results);
$savant->assign('checked_include_all', $checked_include_all);
$savant->assign('col_counts', $col_counts);
$savant->assign('page',$page);
$savant->assign('page_string', $page_string);
$savant->assign('orders', $orders);
$savant->assign('order', $order);
$savant->assign('col', $col);

$savant->display('home/create_author_user_group.tmpl.php');

?>