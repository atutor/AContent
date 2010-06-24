<?php
/************************************************************************/
/* AContent                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; } 

global $contentManager;

require(TR_INCLUDE_PATH.'header.inc.php');
?>
<div class="input-form">
<form action="<?php echo $_SERVER['PHP_SELF'].'?_course_id='.$this->course_id; if ($this->cid > 0) echo SEP.'_cid='.$this->cid; else if ($this->pid > 0) echo SEP.'pid='.$this->pid;?>" method="post" name="form"> 
	<input type="hidden" name="button_1" value="-1" />
<?php
	if ($contentManager->getNumSections() > (1 - (bool)(!$cid))) {
		echo '<p>' 
			, _AT('editor_properties_instructions', 
				'<img src="'.$_base_path.'images/after.gif" alt="'._AT('after_topic', '').'" title="'._AT('after_topic', '').'" />', 
				'<img src="'.$_base_path.'images/before.gif" alt="'._AT('before_topic', '').'" title="'._AT('before_topic', '').'" />',
				'<img src="'.$_base_path.'images/child_of.gif" alt="'._AT('child_of', '').'" title="'._AT('child_of', '').'"  />')
			, '</p>';

	}

	?><br />
	<table border="0" align="center">
	<tr>
		<th colspan="3"><?php echo _AT('move'); ?></th>
		<th><?php echo _AT('content'); ?></th>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
		<td><?php echo _AT('home'); ?></td>
	</tr>
<?php
		$contentManager->printActionMenu($contentManager->_menu, 0, 0, '', array(), "movable");
			
?>
	</table>
</form>
</div>
<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>