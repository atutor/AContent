<?php 


//abhi***************** this file is copied from create_course.tmpl.php and my_course.inc.php and then changes are made to it..

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

global $_current_user;

if (isset($_current_user) && ($_current_user->isAuthor() || $_current_user->isAdmin()))
{


	$userCoursesDAO = new UserCoursesDAO();
	$output = '';

	//abhi not required I guess
	// // The main page can be home/index.php or home/search.php
	// // Find out the caller URL and direct the page back to the caller 
	// // after adding/removing the course from "My Courses"
	// list($caller_url, $url_param) = Utility::getRefererURLAndParams();

	// retrieve data to display
	$my_courses = $userCoursesDAO->getByUserID($_current_user->getInfo()['user_id']); 

	// if (!is_array($my_courses)) {
	// 	$num_of_courses = 0;
	// 	$output = _AT('none_found');
	// } else {
	// 	$num_of_courses = count($my_courses);

	//     $output .= '<ol class="remove-margin-left">'."\n";
		
	//     foreach ($my_courses as $row) {
	// 		// only display the first 200 character of course description

	// 		if ($row['role'] == TR_USERROLE_AUTHOR) {
	// 			$output .= ' <li class="mine" title="'. _AT('my_authoring_course').': '. $row['title'].'"> '."\n";
	// 		} else {
	// 			$output .= ' <li class="theirs" title="'. _AT('others_course').': '. $row['title'].'">'."\n";
	// 		}
	// 		$output .= '    <a href="'. TR_BASE_HREF.'home/course/index.php?_course_id='. $row['course_id'].'"'.(($_course_id == $row['course_id']) ? ' class="selected-sidemenu"' : '').'>'.$row['title'].'</a>'."\n";
	// 		if ($row['role'] == TR_USERROLE_VIEWER) {
	// 			$output .= '    <a href="'. TR_BASE_HREF.'home/'. $caller_url.'action=remove'.SEP.'cid='. $row['course_id'].'">'."\n";
	//             $output .= '      <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/bookmark_remove.png" alt="'. htmlspecialchars(_AT('remove_from_list')).'" title="'. htmlspecialchars(_AT('remove_from_list')).'" border="0" class="shortcut_icon"/>'."\n";
	// 			$output .= '    </a>'."\n";
	// 		} 
	// 		if ($row['role'] == NULL && $_SESSION['user_id']>0) {
	// 			$output .= '    <a href="'. TR_BASE_HREF.'home/'. $caller_url.'action=add'.SEP.'cid='. $row['course_id'].'">'."\n";
	// 			$output .= '      <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/bookmark_add.png" alt="'. htmlspecialchars(_AT('add_into_list')).'" title="'. htmlspecialchars(_AT('add_into_list')).'" border="0"  class="shortcut_icon"/>'."\n";
	// 			$output .= '    </a>'."\n";
	// 		}
	// 		//$output .= '    <a href="'. TR_BASE_HREF.'home/ims/ims_export.php?course_id='. $row['course_id'].'">'."\n";
	// 		//$output .= '      <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/export.png" alt="'. _AT('download_content_package').'" title="'. _AT('download_content_package').'" border="0" />'."\n";
	// 		//$output .= '    </a>'."\n";
	// 		//if ($row['role'] == TR_USERROLE_AUTHOR) {
	// 			//$output .= '    <a href="'. TR_BASE_HREF.'home/imscc/ims_export.php?course_id='. $row['course_id'].'">'."\n";
	// 			//$output .= '      <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/export_cc.png" alt="'. _AT('download_common_cartridge').'" title="'. _AT('download_common_cartridge').'" border="0" />'."\n";
	// 			//$output .= '    </a>'."\n";
	// 		//}
	// 		$output .= '  </li>'."\n";				
	// 	} // end of foreach; 
	//     $output .= '</ol>'."\n";
	// }
	// $savant->assign('title', _AT('my_courses').'&nbsp;'.'('.$num_of_courses.')');
	// $savant->assign('dropdown_contents', $output);
	// //$savant->assign('default_status', "hide");

	// $savant->display('include/box.tmpl.php');


?>
	<div class="input-form">
	<p>
	<?php 
		echo $_current_user->getUserName();
		print_r($my_courses);
		echo "<br>";
		echo sizeof($my_courses);
	?>
	</p>
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_course'); ?></legend>
	<form name="form1" method="post" action="home/ims/ims_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo TR_BASE_HREF; ?>home/prog.php');">
	

		<input type="hidden" name="allow_test_import" value="1" />
		<input type="hidden" name="allow_a4a_import" value="1" />
		<table class="form_data">
		<tr><td>
		<?php echo _AT('create_course_1'); ?>
		<a href="home/course/course_property.php"><?php echo htmlentities_utf8(_AT('course_wizard')); ?></a><br /><br />
		</td></tr>

		<tr><td>
		<?php echo _AT('create_course_2'); ?>
		</td></tr>
		<tr><td>
		<?php		
			if($_current_user->isAdmin()){
				echo '<label for="this_author">'. _AT('assign_author').'</label> <select name="this_author" id="this_author">';
				foreach ($this->isauthor as $author){
				 echo '<option value="'.$author['user_id'].'">'.$author['first_name'].' '.$author['last_name'].' ('.$author['login'].')</option>';
				}
				echo '</select>';
			}
			?>
		</td></tr>
		<tr><td>
			<label for="to_file"><?php echo _AT('upload_content_package'); ?></label>
			<input type="file" name="file" id="to_file" />
		</td></tr>
	
		<tr><td>
			<label for="to_url"><?php echo _AT('specify_url_to_content_package'); ?></label>
			<input type="text" name="url" value="http://" size="40" id="to_url" />
		</td></tr>

		<tr><td>
			<input type="checkbox" name="ignore_validation" id="ignore_validation" value="1" />
			<label for="ignore_validation"><?php echo _AT('ignore_validation'); ?></label> <br />
		</td></tr>
	
		<tr><td>
			<input type="checkbox" name="hide_course" id="hide_course" value="1" /><label for="hide_course"><?php echo _AT('hide_course'); ?></label>
		</td></tr>
	
		<tr align="center"><td>
			<input type="submit" name="submit" value="<?php echo _AT('import'); ?>" />
		</td></tr>
		</table>
	</form>
	</fieldset>
	</div>

<script language="javascript" type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<?php 
}
?>