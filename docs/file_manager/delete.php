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

define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/PrimaryResourcesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/PrimaryResourcesTypesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/SecondaryResourcesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/SecondaryResourcesTypesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

$current_path = TR_CONTENT_DIR.$_course_id.'/';

$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type'].SEP.'_course_id='.$_course_id);
	exit;
}

if (isset($_POST['submit_yes'])) {
	/* delete files and directories */
	/* delete the file  */
	$pathext = $_POST['pathext'];
	if (isset($_POST['listoffiles']))  {
		$checkbox = explode(',',$_POST['listoffiles']);
		$count = count($checkbox);
		$result=true;
		for ($i=0; $i<$count; $i++) {
			$filename=$checkbox[$i];

			if (FileUtility::course_realpath($current_path . $pathext . $filename) == FALSE) {
				$msg->addError('FILE_NOT_DELETED');
				$result=false;
				break;
			} else if (!(@unlink($current_path.$pathext.$filename))) {
				$msg->addError('FILE_NOT_DELETED');
				$result=false;
				break;
			}			
		}
		if ($result)
		{ 
			// delete according definition of primary resources and alternatives for adapted content
			$filename = '../'.$pathext.$filename;
			
			// 1. delete secondary resources types
			$secondaryResourcesTypesDAO = new SecondaryResourcesTypesDAO();
			$secondaryResourcesTypesDAO->DeleteByResourceName($filename);
			
			// 2. delete secondary resources 
			$secondaryResourcesDAO = new SecondaryResourcesDAO();
			$secondaryResourcesDAO->DeleteByResourceName($filename);
			
			// 3. delete primary resources types
			$primaryResourcesTypesDAO = new PrimaryResourcesTypesDAO();
			$primaryResourcesTypesDAO->DeleteByResourceName($filename);
			
			// 4. delete primary resources 
			$primaryResourcesDAO = new PrimaryResourcesDAO();
			$primaryResourcesDAO->DeleteByResourceName($filename);
			
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
	}
	/* delete directory */
	if (isset($_POST['listofdirs'])) {
				
		$checkbox = explode(',',$_POST['listofdirs']);
		$count = count($checkbox);
		$result=true;
		for ($i=0; $i<$count; $i++) {
			$filename=$checkbox[$i];
				
			if (strpos($filename, '..') !== false) {
				$msg->addError('UNKNOWN');
				$result=false;
				header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type'].SEP.'_course_id='.$_course_id);
				exit;
			} else if (!is_dir($current_path.$pathext.$filename)) {
				$msg->addError(array('DIR_NOT_DELETED',$filename));
				$result=false;
				header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type'].SEP.'_course_id='.$_course_id);
				exit;
			} else if (!($result = clr_dir($current_path.$pathext.$filename))) { 
				$msg->addError('DIR_NO_PERMISSION');
				$result=false;
				header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type'].SEP.'_course_id='.$_course_id);
				exit;
			} 
		}
		if ($result)
			$msg->addFeedback('DIR_DELETED');
	}
	
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type'].SEP.'_course_id='.$_course_id);
	exit;
}

	require(TR_INCLUDE_PATH.'header.inc.php');
	// find the files and directories to be deleted 
	$total_list = explode(',', $_GET['list']);
	$pathext = $_GET['pathext']; 
	$popup   = $_GET['popup'];
	$framed  = $_GET['framed'];
	$cp = $_GET['cp'];
	$cid = $_GET['cid'];
	$pid = $_GET['pid'];
	$a_type = $_GET['a_type'];
	
	$count = count($total_list);
	$countd = 0;
	$countf = 0;
	
	foreach ($total_list as $list_item) {
		if (is_dir($current_path.$pathext.$list_item)) {
			$_dirs[$countd]  = $list_item;
			$countd++;
		} else {
			$_files[$countf] = $list_item;
			$countf++;
		}
	}
				
	$hidden_vars['pathext'] = $pathext;
	$hidden_vars['popup']   = $popup;
	$hidden_vars['framed']  = $framed;
	$hidden_vars['cp']  = $cp;
	$hidden_vars['cid']  = $cid;
	$hidden_vars['pid']  = $pid;
	$hidden_vars['a_type']  = $a_type;
	$hidden_vars['_course_id']  = $_course_id;
	
	if (isset($_files)) {
		$list_of_files = implode(',', $_files);
		$hidden_vars['listoffiles'] = $list_of_files;

		foreach ($_files as $file) {
			$file_list_to_print .= '<li>'.$file.'</li>';
		}
		$msg->addConfirm(array('FILE_DELETE', $file_list_to_print), $hidden_vars);
	}
		
	if (isset($_dirs)) {
		$list_of_dirs = implode(',', $_dirs);
		$hidden_vars['listofdirs'] = $list_of_dirs;

		foreach ($_dirs as $dir) {
			$dir_list_to_print .= '<li>'.$dir.'</li>';
		}

		$msg->addConfirm(array('DIR_DELETE',$dir_list_to_print), $hidden_vars);
	}

	$msg->printConfirm();
	
	require(TR_INCLUDE_PATH.'footer.inc.php');
?>
