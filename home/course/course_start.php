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
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require(TR_INCLUDE_PATH.'../home/classes/StructureManager.class.php');
require(TR_INCLUDE_PATH.'header.inc.php'); 

require(TR_INCLUDE_PATH.'../home/course/course_start_tabs.php');


global $msg, $_course_id, $contentManager;

if ($_course_id <= 0)
{
	$msg->addError('MISSING_COURSE_ID');
	header('Location: '.TR_BASE_HREF.'home/index.php');
	exit;
}


if (isset($_current_user) && ($_current_user->isAuthor($_course_id) || $_current_user->isAdmin())) {
	$savant->assign('isAdmin', $_current_user->isAdmin() );
	$savant->assign('course_id', $_course_id);
	
}



if(isset($_POST['struct']) && isset($_POST['create_struct'])) {
	
	
	$_POST['struct']	= $content_row['title'] = $_POST['struct'];
	
	$ordering = count($contentManager->getContent(0)) + 1;
	$pid = 0;
	
	$cid = $contentManager->addContent($_SESSION['course_id'],
			                                   $pid,
			                                   $ordering,
			                                   $_POST['struct'],
			                                   '',
			                                   '',
			                                   '',
			                                   0,
			                                   '',
			                                   0,
			                                   '',
			                                   1,
			                                   CONTENT_TYPE_FOLDER);
	
	$struc_manag = new StructureManager($_POST['struct']);	     
	$page_temp = $struc_manag->get_page_temp();
			
	$struc_manag->createStruct($page_temp, $cid, $_course_id); 
	//$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	
	header('Location: '.TR_BASE_HREF.'home/index.php');
	//header('Location: '.TR_BASE_HREF.'home/course/index.php?_course_id='.$_course_id);
	//exit;
	//$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	//header('Location: '.$_base_path.'home/editor/edit_content_folder.php?_cid='.$cid);
	
	exit;
	
} else {
	
	$msg->printInfos('NO_CONTENT_IN_COURSE');
	
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
		<div class="input-form" style="width: 95%;">
			<?php include('course_start_tabs/'.$tabs[$current_tab][1]); ?>
		</div>
		
	</form>

<?php 

}

require(TR_INCLUDE_PATH.'footer.inc.php');
?>

<!-- elimino la scitta There is no content in this course -->
<script>
    $('#server-msg').css('display','none');
</script>
