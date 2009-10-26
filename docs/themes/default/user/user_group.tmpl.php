<?php
/************************************************************************/
/* AFrame                                                               */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

global $_custom_css;
$_custom_css = AF_BASE_HREF."include/jscripts/infusion/components/inlineEdit/css/InlineEdit.css";

include(AF_INCLUDE_PATH.'header.inc.php');
?>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table summary="<?php echo _AT("user_group"); ?>" class="data" rules="rows" id="editable_table">
	<thead>
	<tr>
		<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>
	
		<th scope="col"><?php echo _AT('title'); ?></th>
		<th scope="col"><?php echo _AT('description'); ?></th>
		<th scope="col"><?php echo _AT('privileges'); ?></th>
	</tr>
	
	</thead>
<?php if (is_array($this->user_group_rows)): ?>
	<tfoot>
	<tr>
		<td colspan="4">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		</td>
	</tr>
	</tfoot>
	<tbody>
		<?php foreach ($this->user_group_rows as $row) 
			{
			// get privileges
			$privileges = $this->privilegesDAO->getUserGroupPrivileges($row['user_group_id']);
			
			if (is_array($privileges))
			{
				$priv_str = '<ul>';
				foreach ($privileges as $priv)	$priv_str .= '<li>'. $priv['privilege_desc'].'</li>';
				$priv_str .= '</ul>';
			}
		?>
			<tr onmousedown="document.form['m<?php echo $row['user_group_id']; ?>'].checked = !document.form['m<?php echo $row['user_group_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['user_group_id']; ?>');" 
			    onkeydown="document.form['m<?php echo $row['user_group_id']; ?>'].checked = !document.form['m<?php echo $row['user_group_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['user_group_id']; ?>');"
			    id="rm<?php echo $row['user_group_id']; ?>">
				<td><input type="checkbox" name="id[]" value="<?php echo $row['user_group_id']; ?>" id="m<?php echo $row['user_group_id']; ?>" 
				           onmouseup="this.checked=!this.checked" onkeyup="this.checked=!this.checked" /></td>
				<td width='20%'><label for="m<?php echo $row['user_group_id']; ?>"><span id="<?php echo 'title-'.$row['user_group_id']?>" class="inlineEdits"><?php echo $row['title']; ?></span></label></td>
				<td width='30%'><span id="<?php echo 'description-'.$row['user_group_id']?>" class="inlineEdits"><?php echo $row['description']; ?></span></td>
				<td><?php echo $priv_str; ?></td>
			</tr>
		<?php } ?>
	</tbody>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</table>
</form>
<br/><br/>

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

jQuery(document).ready(function () {
	var tableEdit = fluid.inlineEdits("#editable_table", {
		selectors : {
			text : "span",
			editables : "td:has(span.inlineEdits)"
		},
		defaultViewText: "",
		useTooltip: true,
		listeners: {
			afterFinishEdit : function (newValue, oldValue, editNode, viewNode) 
			{
				if (newValue != oldValue)
				{
					rtn = jQuery.post("<?php echo AF_BASE_HREF; ?>user/user_group_inline_editor_submit.php", { "field":viewNode.id, "value":newValue }, 
				          function(data) 
				          {
					        if (data.status=="fail")
					        {
					          for (var i = 0; i < tableEdit.length; i++) 
						      {
					            if(tableEdit[i].editField[0] == editNode) 
					              tableEdit[i].updateModelValue(oldValue);
					          }
						    }
				        	
			                handleResponse(data); 
				          }, "json");
				}
			}
		}
	});
});
//-->
</script>
<?php require(AF_INCLUDE_PATH.'footer.inc.php'); ?>