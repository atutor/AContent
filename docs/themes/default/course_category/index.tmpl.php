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

global $_custom_css, $onload;
$_custom_css = TR_BASE_HREF."include/jscripts/infusion/components/inlineEdit/css/InlineEdit.css";
$onload = "document.add_form.category_name.focus();";
include(TR_INCLUDE_PATH.'header.inc.php');
?>

<div class="input-form">
  <form name="add_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
  <fieldset class="group_form"><legend class="group_form"><?php echo _AT("add_course_category"); ?></legend>
    <table class="form-data" align="left">
    <tr align="left">
      <td align="left">
      <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="category_name"><?php echo _AT('category_name'); ?></label>:
      <input id="category_name" name="category_name" type="text" maxlength="255" size="30" />
      <input type="submit" name="add" value="<?php echo _AT('add'); ?>" />
      </td>
    </tr>
    </table>
  </fieldset>
</form>
</div>
  
<div id="output_div" class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT("course_categories"); ?></legend>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table summary="<?php echo _AT('course_category_table_summary'); ?>" class="data" rules="rows" id="editable_table">
<thead>
<tr>
  <th scope="col" align="left" width="5%"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>
  <th scope="col"><?php echo _AT('category_name'); ?></th>
</tr>
</thead>

<?php if (is_array($this->rows) && count($this->rows) > 0): ?>
  <tfoot>
  <tr>
    <td colspan="2">
      <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
    </td>
  </tr>
  </tfoot>
  <tbody>
    <?php if (is_array($this->rows)){ foreach ($this->rows as $row) {?>
      <!-- <tr onmousedown="document.form['m<?php echo $row['category_id']; ?>'].checked = !document.form['m<?php echo $row['category_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['category_id']; ?>');" 
          onkeydown="document.form['m<?php echo $row['category_id']; ?>'].checked = !document.form['m<?php echo $row['category_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['category_id']; ?>');"
          id="rm<?php echo $row['category_id']; ?>"> 
		<td><input type="checkbox" name="id[]" value="<?php echo $row['category_id']; ?>" id="m<?php echo $row['category_id']; ?>" 
		           onmouseup="this.checked=!this.checked" onkeyup="this.checked=!this.checked" /></td>
          -->
      <tr id="rm<?php echo $row['category_id']; ?>">
		<td><input type="checkbox" name="id[]" value="<?php echo $row['category_id']; ?>" id="m<?php echo $row['category_id']; ?>" /></td>
        <td><span class="inlineEdits" id="<?php echo "category_name-".$row['category_id']; ?>"><?php echo $row['category_name']; ?></span></td>
      </tr>
    <?php }} ?>
  </tbody>
<?php else: ?>
  <tr>
    <td colspan="2"><?php echo _AT('none_found'); ?></td>
  </tr>
<?php endif; ?>
</table><br />
<small class="data-table-tip"><?php echo _AT('inline_editor_tip'); ?></small>

</form>
</fieldset>
</div>

<script language="JavaScript" type="text/javascript">
//<!--
function CheckAll() {
  for (var i=0;i<document.form.elements.length;i++)  {
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
      afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
        if (newValue != oldValue)
          rtn = jQuery.post("<?php echo TR_BASE_HREF; ?>course_category/index_inline_editor_submit.php", { "field":viewNode.id, "value":newValue }, 
                  function(data) { handleAjaxResponse(data, viewNode, oldValue); }, "json");
      }
    }
  });
});

//-->
</script>
<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>