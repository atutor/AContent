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

global $_custom_css;
$_custom_css = TR_BASE_HREF."include/jscripts/infusion/components/inlineEdit/css/InlineEdit.css";

include(TR_INCLUDE_PATH.'header.inc.php');
?>
<div id="editable_table">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form1">
<h2 align="center"><?php echo $this->title ;?></h2>

<table class="data" rules="rows">

<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('name_in_translated');?></th>
		<th scope="col"><?php echo _AT('name_in_english');?></th>
		<th scope="col"><?php echo _AT('lang_code');?></th>
		<th scope="col"><?php echo _AT('charset');?></th>
		<th scope="col"><?php echo _AT('status');?></th>
	</tr>
</thead>

<tfoot>
	<tr>
		<td colspan="6">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		</td>
	</tr>
</tfoot>

<tbody>
<?php foreach ($this->rows as $row) {?>
  	<tr onmousedown="document.form1['m<?php echo $row["language_code"]."_".$row["charset"]; ?>'].checked = true; rowselect(this);" 
	    onkeydown="document.form1['m<?php echo $row["language_code"]."_".$row["charset"]; ?>'].checked = true; rowselect(this);"
	    id="r_<?php echo $row["language_code"]."_".$row["charset"]; ?>">
 		<td><input type="radio" name="id" value="<?php echo $row["language_code"]."_".$row["charset"]; ?>" id="m<?php echo $row['language_code']."_".$row["charset"]; ?>" 
		           onmouseup="this.checked=!this.checked" onkeyup="this.checked=!this.checked" /></td>
		<td><label for="m<?php echo $row["language_code"]."_".$row["charset"]; ?>"><span class="inlineEdits" id="<?php echo "native_name:".$row["language_code"].":".$row["charset"]; ?>"><?php echo $row["native_name"]; ?></span></label></td>
		<td><span class="inlineEdits" id="<?php echo "english_name:".$row["language_code"].":".$row["charset"]; ?>"><?php echo $row['english_name']; ?></span></td>
		<td><?php echo $row['language_code']; ?></td>
		<td><?php echo $row['charset']; ?></td>
		<td><?php if ($row['status']) echo _AT('enabled'); else echo _AT('disabled'); ?></td>
	</tr>
<?php }?>
</tbody>

</table>
</form>
</div>
<br /><br />

<form name="import_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="input-form">
	<div class="row">
		<h2><label for="file"><?php echo _AT('import_a_new_lang') ;?></label></h2>
	</div>
	
	<div class="row">
		<input type="file" name="file" id="file" size="50"/>
		<input type="submit" name="import" value="<?php echo _AT('import'); ?>" onclick="javascript: return validate_filename(); " />
	</div>
</div>
</form>

<script type="text/javascript">
<!--

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

// This function validates if and only if a zip file is given
function validate_filename() {
  // check file type
  var file = document.import_form.file.value;
  if (!file || file.trim()=='') {
    alert('Please select a language pack zip file.');
    return false;
  }
  
  if(file.slice(file.lastIndexOf(".")).toLowerCase() != '.zip') {
    alert('Please upload ZIP file only!');
    return false;
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
			afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
				if (newValue != oldValue)
					rtn = jQuery.post("<?php echo TR_BASE_HREF; ?>language/index_inline_editor_submit.php", { "field":viewNode.id, "value":newValue }, 
						          function(data) {handleAjaxResponse(data, viewNode, oldValue); }, "json");
			}
		}
	});
});

//  End -->
//-->
</script>

<?php 
// display footer
include(TR_INCLUDE_PATH.'footer.inc.php');
?>