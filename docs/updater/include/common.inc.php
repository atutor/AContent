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

function print_errors( $errors, $notes='' ) {
	?>
	<div class="input-form">
	<table style="align:center; padding: 3px; background-color: #F8F8F8; border: 0">
	<tr>
		<td>
		<h3><img src="images/bad.gif" align="top" alt="" class="img" /> Warning</h3>
		<?php
			echo '<ul>';
			foreach ($errors as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?>
		</td>
	</tr>
	<tr>
		<td>
		<?php echo $notes; ?>
		</td>
	</tr>
	</table>
	</div>
<?php
}

function print_feedback( $feedback, $notes='' ) {
?>
	<div class="input-form">
	<table style="align:center; padding: 3px; background-color: #F8F8F8; border: 0">
	<tr>
	<td><h3><img src="images/feedback.gif" align="top" alt="" class="img" /> <?php echo _AT('TR_FEEDBACK_UPDATE_INSTALLED_SUCCESSFULLY')?></h3>
		<?php
			echo '<ul>';
			foreach ($feedback as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?></td>
	</tr>
	<tr>
		<td>
		<?php echo $notes; ?>
		</td>
	</tr>
	</table>
	</div>
<?php
}

/**
 * Check if the patch has been installed
 */
function is_patch_installed($patch_id)
{
	$patchesDAO = new PatchesDAO();
	$rows = $patchesDAO->getInstalledPatchByIDAndVersion($patch_id, VERSION);

	if (is_array($rows)) return true;
	else return false;
}

?>
