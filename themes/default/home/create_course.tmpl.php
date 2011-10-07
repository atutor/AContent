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

global $_current_user;

if (isset($_current_user) && $_current_user->isAuthor())
{
?>
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_course'); ?></legend>
	<form name="form1" method="post" action="home/ims/ims_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo TR_BASE_HREF; ?>home/prog.php');">
		<input type="hidden" name="allow_test_import" value="1" />
		<input type="hidden" name="allow_a4a_import" value="1" />
		<table class="form_data">
		<tr><td>
		<?php echo _AT('create_course_1'); ?>
		<a href="home/course/course_property.php"><?php echo htmlentities_utf8(_AT('course_wizard')); ?></a><br /><br />
		</td></tr>

		<tr><td>
		<?php echo _AT('create_course_2'); ?>
		</td></tr>
		<tr><td>
			<label for="to_file"><?php echo _AT('upload_content_package'); ?></label>
			<input type="file" name="file" id="to_file" />
		</td></tr>
	
		<tr><td>
			<label for="to_url"><?php echo _AT('specify_url_to_content_package'); ?></label>
			<input type="text" name="url" value="http://" size="40" id="to_url" />
		</td></tr>

		<tr><td>
			<input type="checkbox" name="ignore_validation" id="ignore_validation" value="1" />
			<label for="ignore_validation"><?php echo _AT('ignore_validation'); ?></label> <br />
		</td></tr>
	
		<tr><td>
			<input type="checkbox" name="hide_course" id="hide_course" value="1" /><label for="hide_course"><?php echo _AT('hide_course'); ?></label>
		</td></tr>
	
		<tr align="center"><td>
			<input type="submit" name="submit" value="<?php echo _AT('import'); ?>" />
		</td></tr>
		</table>
	</form>
	</fieldset>
	</div>

<script language="javascript" type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<?php 
}
?>