<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }
global $_course_id, $_content_id, $contentManager;

include_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');
include_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
/* Get the list of associated tests with this content on page load */
$cid = $_REQUEST['cid'] = $_content_id;	//uses request 'cause after 'saved', the cid will become $_GET.

?>
<div class="row">
	<span style="font-weight:bold"><label for="keys"><?php echo _AT('keywords'); ?></label></span><br />
	<textarea name="keywords" class="formfield" cols="73" rows="2" id="keys"><?php echo $contentManager->cleanOutput($_POST['keywords']); ?></textarea>
</div>
