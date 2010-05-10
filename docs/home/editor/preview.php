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

define('TR_INCLUDE_PATH', '../../include/');

require(TR_INCLUDE_PATH.'vitals.inc.php');
require(TR_INCLUDE_PATH.'../mods/_core/editor/editor_tab_functions.inc.php');

$cid = intval($_POST['cid']);

if ($cid == 0) {
	require(TR_INCLUDE_PATH.'header.inc.php');
	$missing_fields[] = _AT('content_id');
	$msg->addError(array('EMPTY_FIELDS', $missing_fields));
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
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

if ($content_row['content_path']) {
	$content_base_href .= $content_row['content_path'].'/';
}

require(TR_INCLUDE_PATH.'header.inc.php');

?>
	<div class="row">
	<?php 
		echo '<h2>'.TR_print($stripslashes($_POST['title']), 'content.title').'</h2>';
		if ($_POST['formatting'] == CONTENT_TYPE_WEBLINK) {
		    $url = $_POST['weblink_text'];
            $validated_url = isValidURL($url);
            if (!validated_url || $validated_url !== $url) {
                $msg->addError(array('INVALID_INPUT', _AT('weblink')));
                $msg->printErrors();
            } else {
                  echo format_content($url, $_POST['formatting'], array());
            }
        } else {
            echo format_content($stripslashes($_POST['body_text']), $_POST['formatting'], $_POST['glossary_defs']);
        }
    ?>		
	</div>
<?php 
require(TR_INCLUDE_PATH.'footer.inc.php');
?>