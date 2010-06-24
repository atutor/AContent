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

global $onload;
$onload = "initial();";

include(TR_INCLUDE_PATH.'header.inc.php');
?>

<form name="input_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?><?php if (isset($_GET["id"])) echo '?id='.$_GET["id"]; ?>" >
<?php if (isset($this->user_group_row["user_group_id"])) {?>
<input type="hidden" name="user_group_id" value="<?php echo $this->user_group_row["user_group_id"]; ?>" />
<?php }?>

<div class="input-form">

<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_edit_user_group'); ?></legend>
	<table class="form-data">
		<tr>
			<td colspan="2" align="left"><?php echo _AT('required_field_text') ;?></td>
		</tr>

		<tr>
			<th align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label></th>
			<td><input type="text" name="title" size="100" id="title" value="<?php if (isset($_POST['title'])) echo $_POST['title']; else echo $this->user_group_row["title"]; ?>" /></td>
		</tr>

		<tr>
			<th align="left"><label for="description"><?php echo _AT('description'); ?></label></th>
			<td><textarea rows="3" cols="30" name="description" id="description"><?php if (isset($_POST['description'])) echo $_POST['description']; else echo $this->user_group_row["description"]; ?></textarea></td>
		</tr>

		<?php if (isset($this->user_group_row['user_group_id'])) {?>
		<tr>
			<th align="left"><?php echo _AT('date_created'); ?></th>
			<td>
				<?php echo $this->user_group_row['create_date']; ?>
			</td>
		</tr>

		<tr>
			<th align="left"><?php echo _AT('last_update'); ?></th>
			<td>
				<?php echo $this->user_group_row['last_update']; ?>
			</td>
		</tr>
		<?php }?>
	</table>
	<br />
	
	<!-- section of displaying existing checks in current guideline -->
	<?php if (is_array($this->privs_rows)) { ?>
		<h2><?php echo _AT('privileges');?></h2>
		<table class="data" rules="rows" >
			<thead>
			<tr>
				<th align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all_del" title="<?php echo _AT('select_all'); ?>" name="selectall_delprivileges" onclick="CheckAll('del_privileges_id[]','selectall_delprivileges');" /></th>
				<th><?php echo _AT('privileges'); ?></th>
				<th><?php echo _AT('user_requirement'); ?></th>
			</tr>
			</thead>
			
			<tfoot>
				<tr>
					<td colspan="4">
						<input type="submit" name="remove" value="<?php echo _AT('remove'); ?>" />
					</td>
				</tr>
			</tfoot>

			<tbody>
	<?php foreach ($this->privs_rows as $privs_row) { ?>
			<tr id="rdel_privileges_<?php echo $privs_row['privilege_id']; ?>">
				<td onmousedown="document.input_form['del_privileges_<?php echo $privs_row['privilege_id']; ?>'].checked = !document.input_form['del_privileges_<?php echo $privs_row['privilege_id']; ?>'].checked; togglerowhighlight(document.getElementById('rdel_privileges_<?php echo $privs_row['privilege_id']; ?>'), 'del_privileges_<?php echo $privs_row['privilege_id']; ?>');" 
			    onkeydown="document.input_form['del_privileges_<?php echo $privs_row['privilege_id']; ?>'].checked = !document.input_form['del_privileges_<?php echo $privs_row['privilege_id']; ?>'].checked; togglerowhighlight(document.getElementById('rdel_privileges_<?php echo $privs_row['privilege_id']; ?>'), 'del_privileges_<?php echo $privs_row['privilege_id']; ?>');">
			    	<input type="checkbox" name="del_privileges_id[]" value="<?php echo $privs_row['privilege_id']; ?>" id="del_privileges_<?php echo $privs_row['privilege_id']; ?>" 
				           onmouseup="this.checked=!this.checked" onkeyup="this.checked=!this.checked" 
				           <?php if (is_array($_POST['del_privileges_id']) && in_array($privs_row['privilege_id'], $_POST['del_privileges_id'])) echo 'checked="checked"';?> />
				</td>

				<td onmousedown="document.input_form['del_privileges_<?php echo $privs_row['privilege_id']; ?>'].checked = !document.input_form['del_privileges_<?php echo $privs_row['privilege_id']; ?>'].checked; togglerowhighlight(document.getElementById('rdel_privileges_<?php echo $privs_row['privilege_id']; ?>'), 'del_privileges_<?php echo $privs_row['privilege_id']; ?>');" 
			    onkeydown="document.input_form['del_privileges_<?php echo $privs_row['privilege_id']; ?>'].checked = !document.input_form['del_privileges_<?php echo $privs_row['privilege_id']; ?>'].checked; togglerowhighlight(document.getElementById('rdel_privileges_<?php echo $privs_row['privilege_id']; ?>'), 'del_privileges_<?php echo $privs_row['privilege_id']; ?>');">
					<label for="del_privileges_<?php echo $privs_row['privilege_id']; ?>"><?php echo $privs_row['description']; ?></label>
				</td>

				<td>
				<select name="user_requirement[<?php echo $privs_row['privilege_id']; ?>]" id="user_requirement">
					<option value="0" <?php if ((!isset($_POST["user_requirement"][$privs_row['privilege_id']]) && $privs_row['user_requirement'] == 0) || $_POST["user_requirement"][$privs_row['privilege_id']] == 0) echo ' selected="selected"';?>><?php echo _AT('none'); ?></option>
					<option value="<?php echo TR_PRIV_ISAUTHOR; ?>" <?php if ((!isset($_POST["user_requirement"][$privs_row['privilege_id']]) && $privs_row['user_requirement'] == TR_PRIV_ISAUTHOR) || $_POST["user_requirement"][$privs_row['privilege_id']] == TR_PRIV_ISAUTHOR) echo ' selected="selected"';?>><?php echo _AT('must_be_author'); ?></option>
					<option value="<?php echo TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE; ?>" <?php if ((!isset($_POST["user_requirement"][$privs_row['privilege_id']]) && $privs_row['user_requirement'] == TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE) || $_POST["user_requirement"][$privs_row['privilege_id']] == TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE) echo ' selected="selected"';?>><?php echo _AT('must_be_author_of_course'); ?></option>
					<option value="<?php echo TR_PRIV_IN_A_COURSE; ?>" <?php if ((!isset($_POST["user_requirement"][$privs_row['privilege_id']]) && $privs_row['user_requirement'] == TR_PRIV_IN_A_COURSE) || $_POST["user_requirement"][$privs_row['privilege_id']] == TR_PRIV_IN_A_COURSE) echo ' selected="selected"';?>><?php echo _AT('must_in_course'); ?></option>
				</select>
				</td>
			</tr>
	<?php } // end of foreach?>
			</tbody>
		</table>
	<?php } ?>

	<!-- section of displaying privileges to add -->
	<div class="row">
		<h2>
			<img src="images/arrow-closed.png" alt="<?php echo _AT("expand_add_privileges"); ?>" title="<?php echo _AT("expand_add_privileges"); ?>" id="toggle_image" border="0" />
			<a href="javascript:trans.utility.toggleDiv('div_add_privs')"><?php echo _AT("add_privileges"); ?></a>
		</h2>
	</div>
	
	<div id="div_add_privs">
	<?php 
	if (!is_array($this->privs_to_add_rows)){ 
		echo _AT('none_found');
	} 
	else {?>
		<table class="data" rules="rows" >
			<thead>
			<tr>
				<th align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all_add" title="<?php echo _AT('select_all'); ?>" name="selectall_addprivileges" onclick="CheckAll('add_privileges_id[]','selectall_addprivileges');" /></th>
				<th><?php echo _AT('privileges'); ?></th>
			</tr>
			</thead>
			
			<tbody>
	<?php foreach ($this->privs_to_add_rows as $privileges_to_add_row) { ?>
			<tr onmousedown="document.input_form['add_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>'].checked = !document.input_form['add_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>'].checked; togglerowhighlight(this, 'add_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>');" 
			    onkeydown="document.input_form['add_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>'].checked = !document.input_form['add_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>'].checked; togglerowhighlight(this, 'add_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>');"
			    id="radd_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>">
				<td><input type="checkbox" name="add_privileges_id[]" value="<?php echo $privileges_to_add_row['privilege_id']; ?>" id="add_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>" 
				           onmouseup="this.checked=!this.checked" onkeyup="this.checked=!this.checked" 
				           <?php if (is_array($_POST['add_privileges_id']) && in_array($privileges_to_add_row['privilege_id'], $_POST['add_privileges_id'])) echo 'checked="checked"';?> /></td>
				<td><label for="add_privileges_<?php echo $privileges_to_add_row['privilege_id']; ?>"><?php echo $privileges_to_add_row['description']; ?></label></td>
			</tr>
	<?php } // end of foreach?>
			</tbody>
		</table>
	<?php } // end of if?>
	</div>
	
	<div class="row">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</fieldset>
</div>
</form>

<script type="text/JavaScript">
//<!--

function initial()
{
	// hide guideline div
	document.getElementById("div_add_privs").style.display = 'none';

	// set cursor focus
	document.input_form.title.focus();
}

function CheckAll(element_name, selectall_checkbox_name) {
	for (var i=0;i<document.input_form.elements.length;i++)	{
		var e = document.input_form.elements[i];
		if ((e.name == element_name) && (e.type=='checkbox')) {
			e.checked = document.input_form[selectall_checkbox_name].checked;
			togglerowhighlight(document.getElementById("r" + e.id), e.id);
		}
	}
}

function togglerowhighlight(obj, boxid) {
	if (document.getElementById(boxid).checked) {
		obj.className = 'selected';
	} else {
		obj.className = '';
	}
}
//  End -->
//-->
</script>

<?php include(TR_INCLUDE_PATH.'footer.inc.php'); ?>
