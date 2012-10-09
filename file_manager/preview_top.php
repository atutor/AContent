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
require(TR_INCLUDE_PATH.'vitals.inc.php');
global $_course_id;

if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
	$get_file = TR_BASE_HREF.'get.php/';
} else {
	$get_file = TR_BASE_HREF.'content/' . $_course_id . '/';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html lang="<?php echo $myLang->getCode(); ?>">
<head>
	<title><?php echo _AT('file_manager_frame'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; <?php echo $myLang->getCharacterSet(); ?>" />
</head>

<body>
<p align="bottom">

<a href="index.php?framed=<?php echo SEP; ?>popup=<?php echo SEP; ?>pathext=<?php echo htmlentities_utf8($_GET['pathext']).SEP . 'popup=' . htmlentities_utf8($_GET['popup']) . SEP . 'framed=' . htmlentities_utf8($_GET['framed']).SEP.'_course_id='.$_course_id; ?>" target="_top"><?php echo _AT('return_file_manager'); ?></a> 
<?php if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE): ?>
	 | 
	<a href="<?php echo $get_file; ?>@/<?php echo htmlentities_utf8($_GET['file']); ?>" target="_top"><?php echo _AT('download_file'); ?></a>
<?php endif; ?> |
<a href="<?php echo $get_file; ?><?php echo htmlentities_utf8($_GET['file']); ?>" target="_top"><?php echo _AT('remove_frame'); ?></a>
</p>

</body>
</html>