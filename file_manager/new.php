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
define('TR_HTMLPurifier_PATH', '../protection/xss/htmlpurifier/library/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require(TR_INCLUDE_PATH.'classes/FileUtility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);

$current_path = TR_CONTENT_DIR.$_course_id.'/';

$popup  = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'_course_id='.$_course_id);
	exit;
}

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'_course_id='.$_course_id);
	exit;
}

if (isset($_POST['submit_yes'])) {
	$filename = preg_replace("{[^a-zA-Z0-9_]}","_", trim($_POST['filename']));
	$pathext  = $_POST['pathext'];

	/* only html or txt extensions allowed */
	if ($_POST['extension'] == 'html') {
		$extension = 'html';
	} else {
		$extension = 'txt';
	}
	
	if (FileUtility::course_realpath($current_path . $pathext . $filename.'.'.$extension) == FALSE) {
		$msg->addError('FILE_NOT_SAVED');
		/* take user to home page to avoid unspecified error warning */
		header('Location: index.php?pathext='.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'_course_id='.$_course_id);
		exit;
	}

	if (($f = @fopen($current_path.$pathext.$filename.'.'.$extension,'w')) && @fwrite($f, stripslashes($_POST['body_text'])) !== FALSE && @fclose($f)){
		$msg->addFeedback('FILE_OVERWRITE');
	} else {
		$msg->addError('CANNOT_OVERWRITE_FILE');
	}
	unset($_POST['newfile']);
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'_course_id='.$_course_id);
	exit;
}

if (isset($_POST['savenewfile'])) {

	if (isset($_POST['filename']) && ($_POST['filename'] != "")) {
		$filename     = preg_replace("{[^a-zA-Z0-9_]}","_", trim($_POST['filename']));
		$pathext      = $_POST['pathext'];
		$current_path = TR_CONTENT_DIR.$_course_id.'/';

		/* only html or txt extensions allowed */
		if ($_POST['extension'] == 'html') {
			$extension = 'html';
			$head_html = "<html>\n<head>\n<title>".$_POST['filename']."</title>\n<head>\n<body>";
			$foot_html ="\n</body>\n</html>";
		} else {
			$extension = 'txt';
		}

		if (!@file_exists($current_path.$pathext.$filename.'.'.$extension)) {
			$content = str_replace("\r\n", "\n", $head_html.$_POST['body_text'].$foot_html);
			
			if (FileUtility::course_realpath($current_path . $pathext . $filename.'.'.$extension) == FALSE) {
				$msg->addError('FILE_NOT_SAVED');
				/* take user to home page to avoid unspecified error warning */
				header('Location: index.php?pathext='.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'_course_id='.$_course_id);
				exit;
			}

			if (($f = fopen($current_path.$pathext.$filename.'.'.$extension, 'w')) && (@fwrite($f, stripslashes($content)) !== false)  && (@fclose($f))) {
				$msg->addFeedback(array('FILE_SAVED', $filename.'.'.$extension));
				header('Location: index.php?pathext='.urlencode($_POST['pathext']).SEP.'popup='.$_POST['popup'].SEP.'_course_id='.$_course_id);
				exit;
			} else {
				$msg->addError('FILE_NOT_SAVED');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'_course_id='.$_course_id);
				exit;
			}
		}
		else {
			require(TR_INCLUDE_PATH.'header.inc.php');
			$pathext = $_POST['pathext']; 
			$popup   = $_POST['popup'];

			$_POST['newfile'] = "new";

			$hidden_vars['pathext']   = $pathext;
			$hidden_vars['filename']  = $filename;
			$hidden_vars['extension'] = $extension;
			$hidden_vars['_course_id'] = $_course_id;
			$hidden_vars['body_text'] = $_POST['body_text'];

			$hidden_vars['popup']  = $popup;
			$hidden_vars['framed'] = $framed;

			$msg->addConfirm(array('FILE_EXISTS', $filename.'.'.$extension), $hidden_vars);
			$msg->printConfirm();

			require(TR_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
	} else {
		$msg->addError(array('EMPTY_FIELDS', _AT('file_name')));
	}
}

$onload="on_load()";

require(TR_INCLUDE_PATH.'header.inc.php');
require_once(TR_INCLUDE_PATH.'lib/tinymce.inc.php');

// set default body editor to tinymce editor
if (!isset($_POST['extension'])) $_POST['extension'] = 'html';

// load tinymce library
load_editor(true, false, "none");

$pathext = $_GET['pathext']; 
$popup   = $_GET['popup'];

$msg->printAll();

?>
	<form action="<?php echo $_SERVER['PHP_SELF'].'?_course_id='.$_course_id; ?>" method="post" name="form">
	<input type="hidden" name="pathext" value="<?php echo $_REQUEST['pathext'] ?>" />
	<input type="hidden" name="popup" value="<?php echo $popup; ?>" />

	<div class="input-form">	
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_new_file'); ?></legend>
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="ctitle"><?php echo _AT('file_name');  ?></label><br />
			<input type="text" name="filename" id="ctitle" size="40" <?php if (isset($_POST['filename'])) echo 'value="'.AT_print($_POST['filename'], 'input.text').'"'?> />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('type'); ?><br />
			<input type="radio" name="extension" value="txt" id="text" <?php if ($_POST['extension'] == 'txt') { echo 'checked="checked"'; } ?> onclick="trans.editor.switch_content_type(this.value);" />
			<label for="text"><?php echo _AT('plain_text'); ?></label>
	
			, <input type="radio" name="extension" value="html" id="html" <?php if ($_POST['extension'] == 'html') { echo 'checked="checked"'; } ?> onclick="trans.editor.switch_content_type(this.value);" />
			<label for="html"><?php echo _AT('html'); ?></label>
		</div>
	
		<div class="row">
			<label for="body_text"><?php echo _AT('body');  ?></label><br />
			<textarea name="body_text" id="body_text" rows="25"><?php echo $contentManager->cleanOutput($_POST['body_text']); ?></textarea>
		</div>
	
		<div class="row buttons">
			<input type="submit" name="savenewfile" value="<?php echo _AT('save'); ?>" accesskey="s" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />		
		</div>
	</fieldset>
	</div>
	</form>

<script type="text/javascript" language="javascript">
//<!--
function on_load()
{
	document.form.filename.focus();
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
