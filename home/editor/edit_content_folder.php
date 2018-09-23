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

define('TR_INCLUDE_PATH', '../../include/');

require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'../home/editor/editor_tab_functions.inc.php');
require_once(TR_INCLUDE_PATH.'../home/classes/ContentUtility.class.php');
require_once('../../class_csrf.php');

global $_content_id, $_content_id, $contentManager, $_course_id;
$cid = $_content_id;

Utility::authenticate(TR_PRIV_ISAUTHOR);

if (isset($_GET['pid'])) $pid = intval($_GET['pid']);

if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_course_id . '/';
}

if ($cid > 0 && isset($contentManager)) {
	$content_row = $contentManager->getContentPage($cid);
}

// save changes
if ($_POST['submit'])
{
	if (CSRF_Token::isValid() AND CSRF_Token::isRecent())
	{
		if ($_POST['title'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}
		
	if (!$msg->containsErrors()) 
	{
		$_POST['title']	= $content_row['title'] = $_POST['title'];
	
		if ($cid > 0)
		{ // edit existing content
			$err = $contentManager->editContent($cid, 
			                                    $_POST['title'], 
			                                    '', 
			                                    '', 
			                                    $content_row['formatting'], 
			                                    '', 
			                                    $content_row['use_customized_head'], 
			                                    '');
		}
		else
		{ // add new content
			// find out ordering and content_parent_id
			if ($pid)
			{ // insert sub content folder
				$ordering = count($contentManager->getContent($pid))+1;
			}
			else
			{ // insert a top content folder
				$ordering = count($contentManager->getContent(0)) + 1;
				$pid = 0;
			}
			
			$cid = $contentManager->addContent($_SESSION['course_id'],
			                                   $pid,
			                                   $ordering,
			                                   $_POST['title'],
			                                   '',
			                                   '',
			                                   '',
			                                   0,
			                                   '',
			                                   0,
			                                   '',
			                                   1,
			                                   CONTENT_TYPE_FOLDER);
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_base_path.'home/editor/edit_content_folder.php?_cid='.$cid);
		exit;
	}
	} else
	{
		$msg->addError('INVALID_TOKEN');
	}
}

if ($cid > 0)
{ // edit existing content folder
	if (!$content_row || !isset($contentManager)) {
		$_pages['home/editor/edit_content_folder.php']['title_var'] = 'missing_content';
		$_pages['home/editor/edit_content_folder.php']['parent']    = 'index.php';
		$_pages['home/editor/edit_content_folder.php']['ignore']	= true;

		require(TR_INCLUDE_PATH.'header.inc.php');
	
		$msg->addError('MISSING_CONTENT');
		$msg->printAll();
	
		require (TR_INCLUDE_PATH.'footer.inc.php');
		exit;
	} /* else: */

	/* the "heading navigation": */
	$path	= $contentManager->getContentPath($cid);
	
	if ($content_row['content_path']) {
		$content_base_href = $content_row['content_path'].'/';
	}

	$parent_headings = '';
	$num_in_path = count($path);
	
	/* the page title: */
	$page_title = '';
	$page_title .= $content_row['title'];
	
	for ($i=0; $i<$num_in_path; $i++) {
		$content_info = $path[$i];
		if ($_SESSION['prefs']['PREF_NUMBERING']) {
			if ($contentManager->_menu_info[$content_info['content_id']]['content_parent_id'] == 0) {
				$top_num = $contentManager->_menu_info[$content_info['content_id']]['ordering'];
				$parent_headings .= $top_num;
			} else {
				$top_num = $top_num.'.'.$contentManager->_menu_info[$content_info['content_id']]['ordering'];
				$parent_headings .= $top_num;
			}
			if ($_SESSION['prefs']['PREF_NUMBERING']) {
				$path[$i]['content_number'] = $top_num . ' ';
			}
			$parent_headings .= ' ';
		}
	}
	
	if ($_SESSION['prefs']['PREF_NUMBERING']) {
		if ($top_num != '') {
			$top_num = $top_num.'.'.$content_row['ordering'];
			$page_title .= $top_num.' ';
		} else {
			$top_num = $content_row['ordering'];
			$page_title .= $top_num.' ';
		}
	}
	
	$parent = 0;

	reset($path);
	$first_page = current($path);
	
	ContentUtility::saveLastCid($cid);
	
	if (isset($top_num) && $top_num != (int) $top_num) {
		$top_num = substr($top_num, 0, strpos($top_num, '.'));
	}
	$_tool_shortcuts = ContentUtility::getToolShortcuts($content_row);  // used by header.tmpl.php
	
	// display pre-tests
	$savant->assign('ftitle', $content_row['title']);
	$savant->assign('cid', $cid);
}

if ($pid > 0 || !isset($pid)) {
	$savant->assign('pid', $pid);
	$savant->assign('course_id', $_course_id);
}

$onload = "document.form.title.focus();";
require(TR_INCLUDE_PATH.'header.inc.php');
$savant->display('home/editor/edit_content_folder.tmpl.php');
require(TR_INCLUDE_PATH.'footer.inc.php');

//save last visit page.
$_SESSION['last_visited_page'] = $server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
