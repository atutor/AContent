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
 
if (!defined('TR_INCLUDE_PATH')) { exit; }
if (!defined('TR_BASE_HREF')) { exit; }
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH.'/../home/classes/StructureManager.class.php');


global $_course_id;
$contentDAO = new ContentDAO();

$mod_path					= array();
$mod_path['dnd_themod']		= realpath(TR_BASE_HREF			. 'dnd_themod').'/';
$mod_path['dnd_themod_int']	= realpath(TR_INCLUDE_PATH		. '../dnd_themod').'/';
$mod_path['dnd_themod_sys']	= $mod_path['dnd_themod_int']	. 'system/';
$mod_path['structs_dir']		= $mod_path['dnd_themod']		. 'structures/';
$mod_path['structs_dir_int']	= $mod_path['dnd_themod_int']	. 'structures/';

include_once($mod_path['dnd_themod_sys'].'Structures.class.php');

$structs	= new Structures($mod_path);

$structsList = $structs->getStructsList();

$output = '';

if (!is_array($structsList) || count($structsList) == 0) {
	/*catia CHANGE */
	//echo _AT('none_found');
	$msg->addWarning('NO_STRUCT');
	$msg->printWarnings();
} else {
	
	$check = false;
	 
	
?>

<!--  -->
<div style=" weight: 10%; margin: 10px;">
	<p style="font-style:italic;">Choose the structure to use as model for your lesson:</p>
	
	
	<div style="margin: 10px;">
	<?php foreach ($structsList as $val) { 
		  	if(isset($_POST['struct']) && $_POST['struct'] == $val['short_name'])
				$check = true;
			else 
				$check = false;
	?>
	
	
		<div style=" margin-bottom: 10px; <?php if($check) echo 'border: 2px #cccccc dotted;';?> ">
		<input  type="checkbox" id="<?php echo $val['short_name'];?>" name="struct" value="<?php echo $val['short_name'];?>" onclick="document.form.submit();" <?php if($check) echo 'checked="checked";'?>/>
		<label for="<?php echo $val['short_name'];?>"><?php echo $val['name'];?></label><br />
		<p style="margin-left: 10px; font-size:90%;"><span style="font-style:italic;">Description:</span>
					<?php echo $val['description']; ?></p>
			
			<?php if(isset($_POST['struct']) && $_POST['struct'] == $val['short_name']) { 
					
					echo '<div style="font-size:95%; margin-left: 10px; ">';
					
					echo '<p style="font-style:italic;">Outline:</>';
					$struc_manag = new StructureManager($_POST['struct']);
					$struc_manag->printPreview(false, $_POST['struct']);
					echo '</div>';
			} ?>
			</div>
<?php } ?>
<!-- style="margin: 50px; margin-right: 100px; float: right;" -->


</div>

</div>
<!--  -->



<?php }?>
	
		
	