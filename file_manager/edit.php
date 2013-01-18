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
require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$editable_file_types = array('txt', 'html', 'htm', 'xml', 'css', 'asc', 'csv', 'sql');

$current_path = TR_CONTENT_DIR.$_course_id.'/';

$popup  = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];
$file    = $_REQUEST['file'];
$pathext = $_REQUEST['pathext']; 

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'_course_id='.$_course_id);
	exit;
}

if (isset($_POST['save'])) {
	$content = str_replace("\r\n", "\n", $stripslashes($_POST['body_text']));
	$file = $_POST['file'];

	if (FileUtility::course_realpath($current_path . $pathext . $file) == FALSE) {
		$msg->addError('FILE_NOT_SAVED');
	} else {
		if (($f = @fopen($current_path.$pathext.$file, 'w')) && (@fwrite($f, $content) !== false) && @fclose($f)) {
			$msg->addFeedback(array('FILE_SAVED', $file));
			header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'_course_id='.$_course_id);
			exit;
		} else {
			$msg->addError('FILE_NOT_SAVED');
		}
	}
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'_course_id='.$_course_id);
	exit;
}


$path_parts = pathinfo($current_path.$pathext.$file);
$ext = strtolower($path_parts['extension']);

// open file to edit
$real = realpath($current_path . $pathext . $file);

if (FileUtility::course_realpath($current_path . $pathext . $file) == FALSE) {
	// error: File does not exist
	$msg->addError('FILE_NOT_EXIST');
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'_course_id='.$_course_id);
	exit;
} else if (is_dir($current_path.$pathext.$file)) {
	// error: cannot edit folder
	$msg->addError('BAD_FILE_TYPE');
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'_course_id='.$_course_id);
	exit;
} else if (!is_readable($current_path.$pathext.$file)) {
	// error: File cannot open file
	$msg->addError(array('CANNOT_OPEN_FILE', $file));
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'_course_id='.$_course_id);
	exit;
} else if (in_array($ext, $editable_file_types)) {
	$_POST['body_text'] = file_get_contents($current_path.$pathext.$file);
} else {
	//error: bad file type
	$msg->addError('BAD_FILE_TYPE');
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'_course_id='.$_course_id);
	exit;
}

$onload = "on_load();";
require(TR_INCLUDE_PATH.'header.inc.php');
require_once(TR_INCLUDE_PATH.'lib/tinymce.inc.php');

// load tinymce library
load_editor(true, false, "none");

if (!isset($_POST['extension'])) {
	if ($ext == 'html' || $ext == 'htm')
		$_POST['extension'] = 'html';
	else
		$_POST['extension'] = 'txt';
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="pathext" value="<?php echo AT_print($pathext, 'input.hidden'); ?>" />
<input type="hidden" name="framed" value="<?php echo AT_print($framed, 'input.hidden'); ?>" />
<input type="hidden" name="popup" value="<?php echo AT_print($popup, 'input.hidden'); ?>" />
<input type="hidden" name="file" value="<?php echo AT_print($file, 'input.hidden'); ?>" />
<input type="hidden" name="_course_id" value="<?php echo AT_print($_course_id, 'input.hidden'); ?>" />
<input type="submit" name="submit" style="display:none;"/>
<div class="input-form">
	<div class="row">
		<h3><?php echo AT_print($file, 'input.h3'); ?></h3>
	</div>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('type'); ?><br />
		<input type="radio" name="extension" value="txt" id="text" <?php if ($_POST['extension'] == 'txt') { echo 'checked="checked"'; } ?> onclick="trans.editor.switch_content_type(this.value);" />
		<label for="text"><?php echo _AT('plain_text'); ?></label>

		, <input type="radio" name="extension" value="html" id="html" <?php if ($_POST['extension'] == 'html') { echo 'checked="checked"'; } ?> onclick="trans.editor.switch_content_type(this.value);" />
		<label for="html"><?php echo _AT('html'); ?></label>
	</div>
	<div class="row">
		<label for="body_text"><?php echo _AT('body'); ?></label><br />
		<textarea  name="body_text" id="body_text" rows="25"><?php echo htmlspecialchars($_POST['body_text']); ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<script type="text/javascript" language="javascript">
//<!--
function on_load()
{
	if (jQuery('#html').attr("checked")) { 
		tinyMCE.execCommand('mceAddControl', false, 'body_text');
	}
}

trans.editor.switch_content_type = function (extension) {
  if (extension === 'txt') { //text type
    tinyMCE.execCommand('mceRemoveControl', false, 'body_text');
  }
  else { //html type
    tinyMCE.execCommand('mceAddControl', false, 'body_text');
  }
};

//-->
</script>
<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>
