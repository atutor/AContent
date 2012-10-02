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
$mod_path['templates']		= realpath(TR_BASE_HREF			. 'templates').'/';
$mod_path['templates_int']	= realpath(TR_INCLUDE_PATH		. '../templates').'/';
$mod_path['templates_sys']	= $mod_path['templates_int']	. 'system/';
$mod_path['structs_dir']		= $mod_path['templates']		. 'structures/';
$mod_path['structs_dir_int']	= $mod_path['templates_int']	. 'structures/';

include_once($mod_path['templates_sys'].'Structures.class.php');

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

<div style=" weight: 10%; margin: 10px;">
<p style="font-style:italic;">The structures available are:</p>
	
	
	<div style="margin: 10px;">
	<?php 
		$value = "";
		foreach ($structsList as $val) { 
		  	if(isset($_POST['struct']) && $_POST['struct'] == $val['short_name'])
				$check = true;
			else 
				$check = false;
	?>
	
	
		<div style=" margin-bottom: 10px; <?php if($check) echo 'border: 2px #cccccc dotted;';?> ">
		
		<ul>
		<li id="<?php echo $val['short_name'];?>"> <?php echo $val['name'];?> </li>

		<p style="margin-left: 10px; font-size:90%;"><span style="font-style:italic;">Description:</span>
					<?php echo $val['description']; ?></p>
		
					
					<div style="font-size:95%; margin-left: 10px;">
					
						<a title="outline_collapsed" id="a_outline_<?php echo $val['short_name'];?>" onclick="javascript: trans.utility.toggleOutline('<?php echo $val['short_name'];?>', 'Hide outline', 'Show outline'); " href="javascript:void(0)">Show outline</a>
						<div style="display: none;" id="div_outline_<?php echo $val['short_name'];?>">
							<?php $struc_manag = new StructureManager($val['short_name']);
							$struc_manag->printPreview(false, $val['short_name']); ?>
						</div>
			</ul>
			</div>
<?php } ?>


</div>

<input type="hidden" name="struct" />
<input type="hidden" name="current_tab" value="1" />

</div>

<?php echo _AT('create_content_3', TR_BASE_HREF.'home/editor/edit_content_struct.php?_course_id='.$_course_id, "");

} ?>
	
		
	