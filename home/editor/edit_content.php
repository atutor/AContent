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

global $associated_forum, $_course_id, $_content_id;

require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'lib/tinymce.inc.php');
require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/DAO.class.php');

Utility::authenticate(TR_PRIV_ISAUTHOR);

/* In $cid abbiamo il numero della pagina aperta*/
$cid = $_content_id;
$dao = new DAO();

if ($_POST) {
	$do_check = TRUE;
} else {
	$do_check = FALSE;
}

if($_current_user->isAdmin()){
$savant->assign('isAdmin', $_current_user->isAdmin());
}
require(TR_INCLUDE_PATH.'../home/editor/editor_tab_functions.inc.php');

if ($_POST['close'] || $_GET['close']) {
	if ($_GET['close']) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	} else {
		$msg->addFeedback('CLOSED');
		if ($cid == 0) {
			header('Location: '.TR_BASE_HREF.'home/course/index.php?_course_id='.$_course_id);
			exit;
		}
	}
	
	if (!isset($_content_id) || $_content_id == 0) {
		header('Location: '.TR_BASE_HREF.'home/course/index.php?_course_id='.$_course_id);
		exit;
	}
	header('Location: '.TR_BASE_HREF.'home/course/content.php?_cid='.$_content_id);
	exit;
}

$tabs = get_tabs();	
$num_tabs = count($tabs);
for ($i=0; $i < $num_tabs; $i++) {
	if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
		$current_tab = $i;
		$_POST['current_tab'] = $i;
		break;
	}
}

if (isset($_GET['tab'])) {
	$current_tab = intval($_GET['tab']);
}
if (isset($_POST['current_tab'])) {
	$current_tab = intval($_POST['current_tab']);
}

if (isset($_POST['submit_file'])) {
	paste_from_file(body_text);
} else if (isset($_POST['submit']) && ($_POST['submit'] != 'submit1')) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (isset($_POST['submit_file_alt'])) {
	paste_from_file(body_text_alt);
} else if (isset($_POST['submit']) && ($_POST['submit'] != 'submit1')) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (isset($_POST['submit'])) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (!isset($current_tab) && isset($_POST['button_1']) && ($_POST['button_1'] == -1) && !isset($_POST['submit'])) {
	$current_tab = 1;
} else if (!isset($current_tab)) {
	$current_tab = 0;
}

if ($cid) {
	$_section[0][0] = _AT('edit_content');
} else {
	$_section[0][0] = _AT('add_content');
}

if($current_tab == 0) {
    $_custom_head .= '
    <link rel="stylesheet" type="text/css" href="'.TR_BASE_HREF.'include/jscripts/infusion/framework/fss/css/fss-layout.css" />
    <link rel="stylesheet" type="text/css" href="'.TR_BASE_HREF.'include/jscripts/infusion/framework/fss/css/fss-text.css" />
    <script type="text/javascript" src="'.$_base_path.'home/editor/js/edit.js"></script>';
}

if ($cid) {
	if (isset($contentManager)) $content_row = $contentManager->getContentPage($cid);

	if (!$content_row || !isset($contentManager)) {
		require(TR_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('MISSING_CONTENT');
		require (TR_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$path	= $contentManager->getContentPath($cid);
	$content_tests = $contentManager->getContentTestsAssoc($cid);

	if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
		$course_base_href = 'get.php/';
	} else {
		$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
	}

	if ($content_row['content_path']) {
		$content_base_href .= $content_row['content_path'].'/';
	}
} else {
	if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
		$content_base_href = 'get.php/';
	} else {
		$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
	}
}

/* TAB 0 --> Content *//* TAB 2 --> Page */
if (($current_tab == 0) || ($current_tab == 2)) {
    if ($_POST['formatting'] == null){ 
        // this is a fresh load from just logged in
	    if (isset($_SESSION['prefs']['PREF_CONTENT_EDITOR']) && $_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 0) {
			$_POST['formatting'] = 0;
		} else {
			$_POST['formatting'] = 1;
		}
    }
}

require(TR_INCLUDE_PATH.'header.inc.php');

if ($current_tab == 0 || $current_tab == 2) 
{
    $simple = true;
    if ($_POST['complexeditor'] == '1') {
        $simple = false;
    }
    load_editor($simple, false, "none");    
}

$pid = intval($_REQUEST['pid']);
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?_cid=<?php echo $cid; ?>" method="post" name="form" enctype="multipart/form-data">
<?php

	if ($cid) {
		//$content_row = sql_quote($content_row);
		if (isset($_POST['current_tab'])) {
			//$changes_made = check_for_changes($content_row);
		} else {
			$changes_made = array();

			$_POST['formatting'] = $content_row['formatting'];
			$_POST['head'] = $content_row['head'];
			$_POST['use_customized_head'] = $content_row['use_customized_head'];
			$_POST['title']      = $content_row['title'];
			$_POST['body_text']  = $content_row['text'];
			$_POST['weblink_text'] = $content_row['text'];
			$_POST['keywords']   = $content_row['keywords'];
			$_POST['test_message'] = $content_row['test_message'];                     
			$_POST['ordering'] = $content_row['ordering'];
			$_POST['pid'] = $pid = $content_row['content_parent_id'];

		}

	} else {
		$cid = 0;
		if (!isset($_POST['current_tab'])) {
			$_POST['day']  = date('d');
			$_POST['month']  = date('m');
			$_POST['year'] = date('Y');
			$_POST['hour'] = date('H');
			$_POST['min']  = 0;

			if (isset($_GET['pid'])) {
				$pid = $_POST['pid'] = intval($_GET['pid']);
				$_POST['ordering'] = count($contentManager->getContent($pid))+1;
			} else {
				$_POST['pid'] = 0;
				$_POST['ordering'] = count($contentManager->getContent(0))+1;
			}
		}
	}
	
	echo '<input type="hidden" name="_course_id" value="'.$_course_id.'" />';
	echo '<input type="hidden" name="_cid" value="'.$cid.'" />';
	echo '<input type="hidden" name="title" value="'.htmlspecialchars(trim(stripslashes(strip_tags($_POST['title'])))).'" />';
	if ($_REQUEST['sub'] == 1)
	{
		echo '<input type="hidden" name="sub" value="1" />';
		echo '<input type="hidden" name="folder_title" value="'.htmlspecialchars(trim(stripslashes(strip_tags($_POST['folder_title'])))).'" />';
	}
	echo '<input type="submit" name="submit" style="display:none;"/>';
	if (($current_tab != 0) && (($_current_tab != 2))) {
        echo '<input type="hidden" name="body_text" value="'.htmlspecialchars(trim(stripslashes(strip_tags($_POST['body_text'])))).'" />';
        echo '<input type="hidden" name="weblink_text" value="'.htmlspecialchars(trim(stripslashes(strip_tags($_POST['weblink_text'])))).'" />';
        echo '<input type="hidden" name="head" value="'.htmlspecialchars(trim(stripslashes(strip_tags($_POST['head'])))).'" />';
		echo '<input type="hidden" name="use_customized_head" value="'.(($_POST['use_customized_head']=="") ? 0 : $_POST['use_customized_head']).'" />';
        echo '<input type="hidden" name="displayhead" id="displayhead" value="'.AT_print($_POST['displayhead'], 'input.hidden').'" />';
        echo '<input type="hidden" name="complexeditor" id="complexeditor" value="'.AT_print($_POST['complexeditor'], 'input.hidden').'" />';
        echo '<input type="hidden" name="formatting" value="'.AT_print($_POST['formatting'], 'input.hidden').'" />';
	
  
        
        }

	echo '<input type="hidden" name="ordering" value="'.AT_print($_POST['ordering'], 'input.hidden').'" />';
	echo '<input type="hidden" name="pid" value="'.$pid.'" />';
	
	echo '<input type="hidden" name="alternatives" value="'.AT_print($_POST['alternatives'], 'input.hidden').'" />';
	
	echo '<input type="hidden" name="current_tab" value="'.$current_tab.'" />';

	echo '<input type="hidden" name="keywords" value="'.htmlspecialchars(trim(stripslashes(strip_tags($_POST['keywords'])))).'" />';

	//content test association
	echo '<input type="hidden" name="test_message" value="'.AT_print($_POST['test_message'], 'input.hidden').'" />';
	
	/* get glossary terms */

	// adapted content
	$sql = "SELECT pr.primary_resource_id, prt.type_id
	          FROM ".TABLE_PREFIX."primary_resources pr, ".
	                 TABLE_PREFIX."primary_resources_types prt
	         WHERE pr.content_id = ?
	           AND pr.language_code = ?
	           AND pr.primary_resource_id = prt.primary_resource_id";
	$values = array($cid, $_SESSION['lang']);
	$types = "is";
	$types = $dao->execute($sql, $values, $types);
	
	$i = 0;
	if (is_array($types)) {
		foreach ($types as $type) {
			$row_alternatives['alt_'.$type['primary_resource_id'].'_'.$type['type_id']] = 1;
		}
	}
	
	if ($current_tab != 2 && isset($_POST['use_post_for_alt']))
	{
		echo '<input type="hidden" name="use_post_for_alt" value="1" />';
		if (is_array($_POST)) {
			foreach ($_POST as $alt_id => $alt_value) {
				if (substr($alt_id, 0 ,4) == 'alt_'){
					echo '<input type="hidden" name="'.$alt_id.'" value="'.$alt_value.'" />';
				}
			}
		}
	}
	
	//tests
	if ($current_tab != 5){
		// set content associated tests
		if (isset($_POST['visited_tests'])) {
			echo '<input type="hidden" name="visited_tests" value="1" />'."\n";
			if (is_array($_POST['tid'])) {
				foreach ($_POST['tid'] as $i=>$tid){
					echo '<input type="hidden" name="tid['.$i.']" value="'.AT_print($tid, 'input.hidden').'" />';
				}
			}
		} else {
			$i = 0;
			if (is_array($content_tests)) {
				foreach ($content_tests as $content_test_row) {
					echo '<input type="hidden" name="tid['.$i++.']" value="'.$content_test_row['test_id'].'" />';
				}
			}
		}
	} 
	
	if ($do_check) {
		$changes_made = check_for_changes($content_row, $row_alternatives);
	}
?>

<div align="center">
	<?php output_tabs($current_tab, $changes_made); ?>
</div>

<div class="input-form" style="width: 95%;">

<?php if ($changes_made): ?>
		<div class="unsaved">
			<span style="color:red;"><?php echo _AT('save_changes_unsaved'); ?></span> 
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" style="border: 1px solid red;" /> 
			<input type="submit" name="close" class="button green" value="<?php echo _AT('close'); ?>" />  <input type="checkbox" id="close" name="save_n_close" value="1" <?php if ($_SESSION['save_n_close']) { echo 'checked="checked"'; } ?> />
			<label for="close"><?php echo _AT('close_after_saving'); ?></label>
		</div>

	<?php else: ?>
		<div class="saved">
			<?php if ($cid) { echo _AT('save_changes_saved'); } ?> <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" class="button"/> <input type="submit" name="close" value="<?php echo _AT('close'); ?>"  class="button"/> <input type="checkbox" style="border:0px;" id="close" name="save_n_close" value="1" <?php if ($_SESSION['save_n_close']) { echo 'checked="checked"'; } ?>/><label for="close"><?php echo _AT('close_after_saving'); ?></label>
		</div>
	<?php endif; ?>
    
	<?php 
	
	include('editor_tabs/'.$tabs[$current_tab][1]); ?>	

</div>
</form>

<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>
