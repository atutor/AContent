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

//include(TR_INCLUDE_PATH.'header.inc.php');
global $dependent_patches;

if (isset($this->javascript_run_now)) echo $this->javascript_run_now;
?>

<div class="input-form">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT("updates"); ?></legend>

<table class="data" rules="rows">
<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('system_update_id');?></th>
		<th scope="col"><?php echo _AT('description');?></th>
		<th scope="col"><?php echo _AT('status');?></th>
		<th scope="col"><?php echo _AT('available_to');?></th>
		<th scope="col"><?php echo _AT('author');?></th>
		<th scope="col"><?php echo _AT('installed_date');?></th>
		<th scope="col"><?php echo _AT('view_message');?></th>
	</tr>
</thead>

<?php if ($this->num_of_patches == 0){?>
<tbody>
<tr>
	<td colspan="8">
<?php echo _AT('none_found'); ?>
	</td>
</tr>
</tbody>

<?php } else { ?>
<tfoot>
<tr>
	<td colspan="8">
		<input type="submit" name="install" value="<?php echo _AT('install'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php	if (is_array($this->patches_in_db))
		foreach ($this->patches_in_db as $row)
				print_patch_row($row, $row['patches_id'], false);
	
	$array_id = 0;
	// display un-installed patches
	if(is_array($this->patch_list_array))
	{
		foreach ($this->patch_list_array as $row_num => $new_patch)
		{
			if (!is_patch_installed($new_patch['system_patch_id']))
			{
				$dependent_patches_installed = true;
				$dependent_patches = "";
				
				// check if the dependent patches are installed
				if (is_array($new_patch["dependent_patches"]))
				{
					
					foreach ($new_patch["dependent_patches"] as $num => $dependent_patch)
					{
						if (!is_patch_installed($dependent_patch))
						{
							$dependent_patches_installed = false;
							$dependent_patches .= $dependent_patch. ", ";
						}
					}
					
					// remove the last comma in the string
					if ($dependent_patches <> "") $dependent_patches = substr($dependent_patches, 0, -2);
				}

				// display patch row
				if ($dependent_patches_installed)
					print_patch_row($new_patch, $array_id++, true);
				else
				{
					print_patch_row($new_patch, $array_id++, false);
					$dependent_patches_installed = true;
				}
			}
			else
				$array_id++;
		}
	}
?>
</tbody>

<?php } ?>
</table>
</fieldset>

</form>
</div>

<div class="input-form">
<form name="frm_upload" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT("upload"); ?></legend>
	
		<div class="row"><label for="patchfile"><?php echo _AT("upload_update"); ?></label></div>

		<div class="row">
			<input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
			<input type="file" name="patchfile"  id="patchfile" size="50" />
		</div>
		
		<div class="row buttons">
			<input type="submit" name="install_upload" value="Install" onclick="javascript: return validate_filename(); " class="submit" />
			<input type="hidden" name="uploading" value="1" />
		</div>
	</fieldset>
</form>
</div>

<script language="JavaScript" type="text/javascript">
<!--

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

// This function validates if and only if a zip file is given
function validate_filename() {
  // check file type
  var file = document.frm_upload.patchfile.value;
  if (!file || file.trim()=='') {
    alert('Please give a zip file!');
    return false;
  }
  
  if(file.slice(file.lastIndexOf(".")).toLowerCase() != '.zip') {
    alert('Please upload ZIP file only!');
    return false;
  }
}

//  End -->
//-->
</script>

<?php require (TR_INCLUDE_PATH.'footer.inc.php'); ?>
