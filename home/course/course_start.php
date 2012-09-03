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

define('TR_INCLUDE_PATH', '../../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');

global $msg, $contentManager, $_course_id;

if ($_course_id <= 0)
{
	$msg->addError('MISSING_COURSE_ID');
	header('Location: '.TR_BASE_HREF.'home/index.php');
	exit;
}

$msg->addInfo('NO_CONTENT_IN_COURSE');

require(TR_INCLUDE_PATH.'header.inc.php'); 
require(TR_INCLUDE_PATH.'../home/course/course_start_tabs.php');


if (isset($_current_user) && $_current_user->isAuthor($_course_id)) {
	$savant->assign('course_id', $_course_id);
	
}

	$current_tab = 0;
		
	$tabs = get_tabs();	
	$num_tabs = count($tabs);
	for ($i=0; $i < $num_tabs; $i++) {
		if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
			$current_tab = $i;
			$_POST['current_tab'] = $i;
			break;
		}
	}
	
	if (isset($_POST['current_tab'])) {
		$current_tab = intval($_POST['current_tab']);
	}
	
	?>
	<form enctype="multipart/form-data" name="form" method="post" action="<?php echo TR_BASE_HREF.'home/course/course_start.php?_course_id='.$_course_id;?>">
	<input type="hidden" name="current_tab" value="<?php echo $current_tab;?>" />
	<div align="center">
		<?php output_tabs($current_tab); ?>
	</div>

<!--  -->
	<div class="input-form" style="width: 95%;">
		<?php include('course_start_tabs/'.$tabs[$current_tab][1]); ?>
		
		
	</div>
	<!--  -->
	<input type="submit" value="Create course with the selected structure" style="margin: 20px; position: relative; left: 65%;"  />
	</form>
	
	
		
	

<?php 

require(TR_INCLUDE_PATH.'footer.inc.php'); 
?>