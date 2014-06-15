<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

global $_custom_css;
$_custom_css = TR_BASE_HREF."include/jscripts/infusion/components/inlineEdit/css/InlineEdit.css";

include(TR_INCLUDE_PATH.'header.inc.php');
?>

<div class="input-form">
	<form name="filter_form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT("filter"); ?></legend>
		<table class="filter">
		<tr>
			<td colspan="2"><h2><?php echo _AT('results_found', $this->num_results); ?></h2></td>
		</tr>

		<tr>
			<th><?php echo _AT('user_status'); ?>:</th>
			<td>
			<input type="radio" name="status" value="0" id="s0" <?php if ($_GET['status'] == TR_STATUS_DISABLED) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('disabled'); ?></label> 
			<input type="radio" name="status" value="1" id="s1" <?php if ($_GET['status'] == TR_STATUS_ENABLED) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('enabled'); ?></label> 
			<input type="radio" name="status" value="" id="s" <?php if ($_GET['status'] === '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
			</td>
		</tr>

		<?php if (is_array($this->all_user_groups)) { ?>
		<tr>
			<th><label for="user_group_id"><?php echo _AT('user_group'); ?></label>:</th>
			<td>
			<select name="user_group_id" id="user_group_id">
				<option value="-1">- <?php echo _AT('select'); ?> -</option>
				<?php foreach ($this->all_user_groups as $user_group) {?>
				<option value="<?php echo $user_group['user_group_id']; ?>" <?php if($_GET['user_group_id']==$user_group['user_group_id']) { echo 'selected="selected"';}?>><?php echo $user_group['title']; ?></option>
				<?php } ?>
			</select>
			</td>
		</tr>
		<?php } ?>

		<tr>
			<th><label for="search"><?php echo _AT('search'); ?>:</label></th>
			<td><input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_GET['search']); ?>" /><br /><small>&middot; <?php echo _AT('login_name').', '._AT('first_name').', '._AT('last_name') .', '._AT('email'); ?></small></td>
		</tr>

		<tr>
			<td colspan="2" align="center">
			<input type="radio" name="include" value="all" id="match_all" <?php echo $this->checked_include_all; ?> /><label for="match_all"><?php echo _AT('match_all_words'); ?></label> 
			<input type="radio" name="include" value="one" id="match_one" <?php echo $this->checked_include_one; ?> /><label for="match_one"><?php echo _AT('match_any_word'); ?></label>
			</td>
		</tr>

		<tr>
			<td colspan="2"><p class="submit_button">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
			</p></td>
		</tr>
		</table>
	</fieldset>
</form>
</div>
	
<div id="output_div" class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT("users"); ?></legend>


<?php print_paginator($this->page, $this->num_results, $this->page_string . htmlspecialchars(SEP) . $this->order .'='. $this->col, $this->results_per_page); ?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <table class="filter">
    <tr>
			<th><label for="group_name"><?php echo _AT('group_name'); ?>:</label></th>
			<td><input type="text" name="group_name" id="group_name" size="40" value="<?php echo htmlspecialchars($_POST['group_name']); ?>" /></td>
    </tr>
  </table>
<br>
<input type="hidden" name="status" value="<?php echo $_GET['status']; ?>" />
<input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
<input type="hidden" name="include" value="<?php echo htmlspecialchars($_GET['include']); ?>" />

<table summary="<?php echo _AT('user_table_summary'); ?>" class="data" rules="rows" id="editable_table">
<colgroup>
	<?php if ($this->col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="<?php echo 5 + $this->col_counts; ?>" />
	<?php elseif($this->col == 'public_field'): ?>
		<col span="<?php echo 1 + $this->col_counts; ?>" />
		<col class="sort" />
		<col span="6" />
	<?php elseif($this->col == 'first_name'): ?>
		<col span="<?php echo 2 + $this->col_counts; ?>" />
		<col class="sort" />
		<col span="5" />
	<?php elseif($this->col == 'last_name'): ?>
		<col span="<?php echo 3 + $this->col_counts; ?>" />
		<col class="sort" />
		<col span="4" />
	<?php elseif($this->col == 'user_group'): ?>
		<col span="<?php echo 4 + $this->col_counts; ?>" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($this->col == 'email'): ?>
		<col span="<?php echo 5 + $this->col_counts; ?>" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($this->col == 'status'): ?>
		<col span="<?php echo 6 + $this->col_counts; ?>" />
		<col class="sort" />
		<col />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col" align="left" width="5%"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>

	<th scope="col" width="15%"><a href="home/create_author_user_group.php?<?php echo $this->orders[$this->order]; ?>=login<?php echo $page_string; ?>"><?php echo _AT('login_name');      ?></a></th>
	<th scope="col" width="15%"><a href="home/create_author_user_group.php?<?php echo $this->orders[$this->order]; ?>=first_name<?php echo $page_string; ?>"><?php echo _AT('first_name'); ?></a></th>
	<th scope="col" width="10%"><a href="home/create_author_user_group.php?<?php echo $this->orders[$this->order]; ?>=last_name<?php echo $page_string; ?>"><?php echo _AT('last_name');   ?></a></th>
	<th scope="col" width="10%"><a href="home/create_author_user_group.php?<?php echo $this->orders[$this->order]; ?>=user_group<?php echo $page_string; ?>"><?php echo _AT('user_group'); ?></a></th>
	<th scope="col" width="15%"><a href="home/create_author_user_group.php?<?php echo $this->orders[$this->order]; ?>=email<?php echo $page_string; ?>"><?php echo _AT('email');           ?></a></th>
	<th scope="col" width="10%"><a href="home/create_author_user_group.php?<?php echo $this->orders[$this->order]; ?>=status<?php echo $page_string; ?>"><?php echo _AT('user_status'); ?></a></th>
</tr>

</thead>
<?php if ($this->num_results > 0): ?>
	<tfoot>
            
	<tr>
		<td>
                    <input type="submit" name="submitusergroup" value="<?php echo _AT('create_group'); ?>" /> 
		</td>
	</tr>
	</tfoot>
	<tbody>
		<?php if (is_array($this->user_rows)){ foreach ($this->user_rows as $row) {?>
			<tr onmousedown="document.form['m<?php echo $row['user_id']; ?>'].checked = !document.form['m<?php echo $row['user_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['user_id']; ?>');" 
			    onkeydown="document.form['m<?php echo $row['user_id']; ?>'].checked = !document.form['m<?php echo $row['user_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['user_id']; ?>');"
			    id="rm<?php echo $row['user_id']; ?>">
				<td><input type="checkbox" name="id[]" value="<?php echo $row['user_id']; ?>" id="m<?php echo $row['user_id']; ?>" 
				           onmouseup="this.checked=!this.checked" onkeyup="this.checked=!this.checked" /></td>
				<td><?php echo $row['login']; ?></td>
				<td><?php echo $row['first_name']; ?></td>
				<td><?php echo $row['last_name']; ?></td>
				<td><?php echo $row['user_group']; ?></td>
				<td><?php echo $row['email']; ?></td>
				<td><?php echo get_status_by_code($row['status']);?></td>
			</tr>
		<?php }} ?>
	</tbody>
<?php else: ?>
	<tr>
		<td colspan="<?php echo 8 + $this->col_counts; ?>"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</table><br />


</form>
</fieldset>
</div>

<script language="JavaScript" type="text/javascript">
//<!--
function CheckAll() {
	for (var i=0;i<document.form.elements.length;i++)	{
		var e = document.form.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox')) {
			e.checked = document.form.selectall.checked;
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
//-->
</script>
<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>