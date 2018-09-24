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

define('TR_INCLUDE_PATH', '../../include/');

require(TR_INCLUDE_PATH.'vitals.inc.php');
require(TR_INCLUDE_PATH.'../home/editor/editor_tab_functions.inc.php');

// commented out this require which was causing the a redeclare error #4846
// delete the following line when its confirmed the require is not needed
// require(TR_INCLUDE_PATH.'../home/classes/ContentUtility.class.php');

global $_course_id, $_content_id, $contentManager;

Utility::authenticate(TR_PRIV_ISAUTHOR);

$cid = $_content_id;

if ($cid == 0) {
	require(TR_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('NO_PAGE_CONTENT');
	require (TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($contentManager)) $content_row = $contentManager->getContentPage($cid);

if (!$content_row || !isset($contentManager)) {
	require(TR_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('MISSING_CONTENT');
	require (TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_course_id . '/';
}

if ($content_row['content_path']) {
	$content_base_href .= $content_row['content_path'].'/';
}

$popup = intval($_GET['popup']);
require(TR_INCLUDE_PATH.'header.inc.php');
?>
	<div class="row">
	<?php 
		echo '<h2>'.AT_print(htmlspecialchars(trim(stripslashes(strip_tags($_POST['title'])))), 'content.title').'</h2>';
		if ($_POST['formatting'] == CONTENT_TYPE_WEBLINK) {
		    $url = $_POST['weblink_text'];
            $validated_url = isValidURL($url);
            if (!validated_url || $validated_url !== $url) {
                $msg->addError(array('INVALID_INPUT', _AT('weblink')));
                $msg->printErrors();
            } else {
                  echo ContentUtility::formatContent($url, $_POST['formatting']);
            }
        } else {
            echo ContentUtility::formatContent(htmlspecialchars(trim(stripslashes(strip_tags($_POST['body_text'])))), $_POST['formatting']);
        }
    ?>		
	</div>
<?php 
require(TR_INCLUDE_PATH.'footer.inc.php');
?>
