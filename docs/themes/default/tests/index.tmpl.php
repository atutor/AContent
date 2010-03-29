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
<form method="post" action="tests/import_test.php" enctype="multipart/form-data" >
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
<div class="input-form" style="width: 90%">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('import_test'); ?></legend>
	<div class="row">
		<label for="to_file"><?php echo _AT('upload_test'); ?></label><br />
		<input type="file" name="file" id="to_file" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit_import" value="<?php echo _AT('import'); ?>" />
	</div>
	</fieldset>
</div>
</form>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
<!-- 	<th scope="col"><?php echo _AT('assigned_to');	  ?></th> -->
</tr>
</thead>

<?php if (is_array($this->rows)){?>
	<tfoot>
	<tr>
		<td colspan="3">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />
			<input type="submit" name="preview" value="<?php echo _AT('preview'); ?>" />
			<input type="submit" name="questions" value="<?php echo _AT('questions'); ?>" />
		</td>
	</tr>
	<tr>	
		<td colspan="3" style="padding-left:38px;">
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
		</td>
	</tr>
	</tfoot>
	<tbody>

	<?php foreach ($this->rows as $row) { ?>
		<tr onmousedown="document.form['t<?php echo $row['test_id']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['test_id']; ?>">
			<td><input type="radio" name="id" value="<?php echo $row['test_id']; ?>" id="t<?php echo $row['test_id']; ?>" /></td>
			<td><label for="t<?php echo $row['test_id']; ?>"><?php echo $row['title']; ?></label></td>
			<td><?php echo $row['description']; ?></td>
		</tr>
	<?php }} // end of if (is_array($rows)) 
	else { ?>
	<tbody>
	<tr>
		<td colspan="7"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php } // end of else ?>
</tbody>
</table>
</form>

