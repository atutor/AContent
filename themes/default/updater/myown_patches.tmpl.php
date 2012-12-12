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

global $_custom_css;
$_custom_css = TR_BASE_HREF."include/jscripts/infusion/components/inlineEdit/css/InlineEdit.css";

require(TR_INCLUDE_PATH.'header.inc.php'); 
?>

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT("myown_updates"); ?></legend>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" rules="rows" id="editable_table">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('system_update_id'); ?></th>
	<th scope="col"><?php echo _AT('transformable_version_to_apply'); ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
	<th scope="col"><?php echo _AT('last_modified'); ?></th>
</tr>
</thead>
<?php if (!is_array($this->patch_rows)) { ?>
<tbody>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
</tbody>
<?php } else { ?>
<tfoot>
<tr>
	<td colspan="5">
		<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="remove" value="<?php echo _AT('remove'); ?>" /> 
		</div>
	</td>
</tr>
</tfoot>
<tbody>
<?php foreach ($this->patch_rows as $row) { ?>
		<tr onmousedown="document.form['m<?php echo $row['myown_patch_id']; ?>'].checked = true; trans.utility.rowselect(this);" onkeydown="document.form['m<?php echo $row['myown_patch_id']; ?>'].checked = true; trans.utility.rowselect(this);" id="r_<?php echo $row['myown_patch_id']; ?>">
			<td width="10"><input type="radio" name="myown_patch_id" value="<?php echo $row['myown_patch_id']; ?>" id="m<?php echo $row['myown_patch_id']; ?>" <?php if ($row['myown_patch_id']==$_POST['myown_patch_id']) echo 'checked'; ?> /></td>
			<td><label for="m<?php echo $row['myown_patch_id']; ?>"><span id="<?php echo 'system_patch_id-'.$row['myown_patch_id']; ?>" class="inlineEdits"><?php echo $row['system_patch_id']; ?></span></label></td>
			<td><span id="<?php echo 'applied_version-'.$row['myown_patch_id']; ?>" class="inlineEdits"><?php echo $row['applied_version']; ?></span></td>
			<td><span id="<?php echo 'description-'.$row['myown_patch_id']; ?>" class="inlineEdits"><?php echo $row['description']; ?></span></td>
			<td><?php echo $row['last_modified']; ?></td>
		</tr>
<?php } // end of foreach ?>
</tbody>
<?php } // end of else ?>

</table>

</form>

</fieldset>
</div>

<script language="JavaScript" type="text/javascript">
//<!--
jQuery(document).ready(function () {
	var tableEdit = fluid.inlineEdits("#editable_table", {
		selectors : {
			text : ".inlineEdits",
			editables : "td:has(span.inlineEdits)"
		},
		defaultViewText: "",
		useTooltip: true,
		listeners: {
			afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
				if (newValue != oldValue)
					rtn = jQuery.post("<?php echo TR_BASE_HREF; ?>updater/myown_patches_inline_editor_submit.php", 
				          { "field":viewNode.id, "value":newValue }, 
		                  function(data) { handleAjaxResponse(data, viewNode, oldValue); }, 
		                  "json");
			}
		}
	});
});
//-->
</script>

<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>
