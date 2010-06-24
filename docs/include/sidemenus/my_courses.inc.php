<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');

// can only be used by login user
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) return;

global $savant;

$userCoursesDAO = new UserCoursesDAO();
$output = '';

// retrieve data to display
if ($_SESSION['user_id'] > 0) {
	$my_courses = $userCoursesDAO->getByUserID($_SESSION['user_id']); 
}

if (!is_array($my_courses)) {
	$num_of_courses = 0;
	$output = _AT('none_found');
} else {
	$num_of_courses = count($my_courses);

    $output .= '<ol class="remove-margin-left">'."\n";
	foreach ($my_courses as $row) {
		// only display the first 200 character of course description

		$output .= '  <li>'."\n";
		if ($row['role'] == TR_USERROLE_AUTHOR) {
			$output .= '    <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/my_own_course.gif" alt="'. _AT('my_authoring_course').'" title="'. _AT('my_authoring_course').'" />'."\n";
		} else {
			$output .= '    <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/others_course.png" alt="'. _AT('others_course').'" title="'. _AT('others_course').'" />'."\n";
		}
		$output .= '    <a href="'. TR_BASE_HREF.'home/course/index.php?_course_id='. $row['course_id'].'">'.$row['title'].'</a>'."\n";
		if ($row['role'] == TR_USERROLE_VIEWER) {
			$output .= '    <a href="'. TR_BASE_HREF.'home/'. $caller_url.'action=remove&cid='. $row['course_id'].'">'."\n";
            $output .= '      <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/delete.gif" alt="'. htmlspecialchars(_AT('remove_from_list')).'" title="'. htmlspecialchars(_AT('remove_from_list')).'" border="0" />'."\n";
			$output .= '    </a>'."\n";
		} 
		if ($row['role'] == NULL && $_SESSION['user_id']>0) {
			$output .= '    <a href="'. TR_BASE_HREF.'home/'. $caller_url.'action=add&cid='. $row['course_id'].'">'."\n";
			$output .= '      <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/add.gif" alt="'. htmlspecialchars(_AT('add_into_list')).'" title="'. htmlspecialchars(_AT('add_into_list')).'" border="0" />'."\n";
			$output .= '    </a>'."\n";
		}
		//$output .= '    <a href="'. TR_BASE_HREF.'home/ims/ims_export.php?course_id='. $row['course_id'].'">'."\n";
		//$output .= '      <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/export.png" alt="'. _AT('download_content_package').'" title="'. _AT('download_content_package').'" border="0" />'."\n";
		//$output .= '    </a>'."\n";
		if ($row['role'] == TR_USERROLE_AUTHOR) {
			//$output .= '    <a href="'. TR_BASE_HREF.'home/imscc/ims_export.php?course_id='. $row['course_id'].'">'."\n";
			//$output .= '      <img src="'. TR_BASE_HREF.'themes/'. $_SESSION['prefs']['PREF_THEME'].'/images/export_cc.png" alt="'. _AT('download_common_cartridge').'" title="'. _AT('download_common_cartridge').'" border="0" />'."\n";
			//$output .= '    </a>'."\n";
		}
		$output .= '  </li>'."\n";				
	} // end of foreach; 
    $output .= '</ol>'."\n";
}
$savant->assign('title', _AT('my_courses').'&nbsp;'.'('.$num_of_courses.')');
$savant->assign('dropdown_contents', $output);
//$savant->assign('default_status', "hide");

$savant->display('include/box.tmpl.php');
?>
