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

?>
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('search'); ?></legend>
		<form target="_top" action="<?php echo TR_BASE_HREF; ?>home/search.php" method="get" name="frm_search">
		<?php if (isset($this->user_row)) echo _AT('search_and_add').':'; ?>
		<input type="text" name="search_text" id="search_text" value="<?php if (isset($_POST['search_text'])) echo $_POST['search_text']; ?>" size="50"   />
		<input type="submit" name="search" size="100" value="<?php echo _AT("search"); ?>" />
		</form>
	</fieldset>
	</div>

<?php 
include('create_course_tmpl.tmpl.php');
?>