<?php
/************************************************************************/
/* AContent                                                         */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
global $_course_id;

if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
	$get_file = $_base_path . 'get.php/';
	$file = 'b64:'.base64_encode($_GET['file']);
} else {
	$get_file = $_base_path . 'content/' . $_course_id . '/';
	$file = $_GET['file'];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN" "http://www.w3.org/TR/REC-html40/frameset.dtd" />
<html lang="<?php echo $myLang->getCode(); ?>">
<head>
	<title><?php echo _AT('file_manager_frame'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; <?php echo $myLang->getCharacterSet(); ?>" />
</head>

<frameset rows="50,*">

<frame src="preview_top.php?file=<?php echo $file.SEP.'pathext='. $_GET['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP.'_course_id='.$_course_id; ?>" scrolling="no" marginwidth="0" marginheight="0" />
<frame src="<?php echo $get_file; ?><?php echo $file; ?>" />

<noframes>
  <p><?php echo _AT('frame_contains'); ?><br />
  * <a href="../file_manager/index.php"><?php echo _AT('file_manager'); ?></a>
  </p>
</noframes>

</frameset>
</html>