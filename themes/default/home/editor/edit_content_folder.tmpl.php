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

if (!defined('TR_INCLUDE_PATH')) { exit; } 

global $onload, $contentManager;
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
		<div style="font-weight:bold;"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="ftitle"><?php echo _AT('content_folder_title');  ?></label></div>
		<input type="text" name="title" id="ftitle" size="70" class="formfield" value="<?php echo $contentManager->cleanOutput($this->ftitle); ?>" />
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" />
	</div>
</div>
</form>
