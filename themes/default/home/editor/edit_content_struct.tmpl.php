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

global $onload;
$onload = 'document.form.title.focus();';
?>
<form action="<?php echo $_SERVER['PHP_SELF'].'?'; if ($this->cid > 0) echo '_cid='.$this->cid; else if ($this->pid > 0) echo 'pid='.$this->pid.SEP.'_course_id='.$this->course_id; else echo '_course_id='.$this->course_id;?>" method="post" name="form"> 
<div class="input-form" style="width:95%;margin-left:1.5em;">
<!-- <?php
if ($this->shortcuts): 
?>
 <fieldset id="shortcuts" style="margin-top:1em;float:right;clear:right;"><legend><?php echo _AT('shortcuts'); ?></legend>
	<ul>
		<?php foreach ($this->shortcuts as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
</fieldset>
<?php endif; ?> -->
	<div class="row">
		<div style="font-weight:bold;"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="ftitle">Choose the structure</label></div>
		
		<?php  
			
			$mod_path['templates']		= realpath(TR_BASE_HREF			. 'templates').'/';
			$mod_path['templates_int']	= realpath(TR_INCLUDE_PATH		. '../templates').'/';
			$mod_path['templates_sys']	= $mod_path['templates_int']	. 'system/';
			$mod_path['structs_dir']		= $mod_path['templates']		. 'structures/';
			$mod_path['structs_dir_int']	= $mod_path['templates_int']	. 'structures/';

			include_once($mod_path['templates_sys'].'Structures.class.php');
			
			$structs	= new Structures($mod_path);

			$structsList = $structs->getStructsList();
			if (!is_array($structsList)) {
					$num_of_structs = 0;
					$output = _AT('none_found');
			} else {
			
                            echo '<div style=" weight: 10%; margin: 10px;">';
                            
                            
				foreach ($structsList as $struct) {
					echo '<input type="radio" name="title" id="'.$struct['name'].'" class="formfield" value="'.$struct['short_name'].'"/>';
					echo '<label for="'.$struct['name'].'">'.$struct['name'].'</label>';
                                        $value = "";
                                        
                                        
                                        
		foreach ($structsList as $val) { 
		  	if(isset($_POST['struct']) && $_POST['struct'] == $val['short_name'])
				$check = true;
			else 
				$check = false;
                                        
                        if($val['name'] == $struct['name']){
                  ?>                      
                <div style=" margin-bottom: 10px; <?php if($check) echo 'border: 2px #cccccc dotted;';?> ">
		
		
	<!--	<li id="<?php echo $val['short_name'];?>"> <?php echo $val['name'];?> </li> -->

		<p style="margin-left: 10px; font-size:90%;"><span style="font-style:italic;">Description:</span>
                <?php echo $val['description']; ?></p>


                <div style="font-size:95%; margin-left: 10px;">

                        <a title="outline_collapsed" id="a_outline_<?php echo $val['short_name'];?>" onclick="javascript: trans.utility.toggleOutline('<?php echo $val['short_name'];?>', 'Hide outline', 'Show outline'); " href="javascript:void(0)">Show outline</a>
                        <div style="display: none;" id="div_outline_<?php echo $val['short_name'];?>">
                                <?php $struc_manag = new StructureManager($val['short_name']);
                                $struc_manag->printPreview(false, $val['short_name']); ?>
                        </div>
            
            </div>
                </div>

                                        <?php 
                                        }
                }
                                        
                                        
					echo '</br>';
				}
                            
                            echo '</div>';    
                                
			}
		
		?>
		
		<!-- <input type="checkbox"" name="title" id="ftitle1" class="formfield" value="bao"></input> -->
		
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" />
	</div>
</div>
</form>
