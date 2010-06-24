<?php 
/************************************************************************/
/* AContent                                                        									*/
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

?>
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_content'); ?></legend>
		<table class="form_data">
		<tr><td>
		<?php echo _AT('create_content_1'); ?>
		<a href="home/course/content_wizard.php"><?php echo _AT('content_wizard'); ?></a><br /><br />
		</td></tr>

		<tr><td>
		<?php echo _AT('create_content_2', TR_BASE_HREF.'home/editor/edit_content.php?_course_id='.$this->course_id, TR_BASE_HREF.'home/editor/edit_content_folder.php?_course_id='.$this->course_id); ?>
		</td></tr>
		</table>
	</fieldset>
	</div>

