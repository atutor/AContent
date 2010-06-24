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
require_once(TR_INCLUDE_PATH.'classes/DAO/CourseCategoriesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');

global $savant;

$courseCategoriesDAO = new CourseCategoriesDAO();
$coursesDAO = new CoursesDAO();
$output = '';

// get the number of courses in each category
$courses_in_category = $coursesDAO->getCategoriesAndNumOfCourses();

if (is_array($courses_in_category)) {
	foreach ($courses_in_category as $row){
		$course_num_summary[$row['category_id']] = $row['num_of_courses'];
	}
}

// get all categories
$categories = $courseCategoriesDAO->getAll();

if (is_array($categories)) {
	foreach ($categories as $category) {
		$output .= '<a href="'.TR_BASE_HREF.'home/index.php?catid='.$category['category_id'].'">';
		
		if ($_GET['catid'] <> '' && $_GET['catid'] == $category['category_id']) {
			$output .= '<span class="selected-sidemenu">';
		}
		$output .= $category['category_name'].'&nbsp;';
		if (isset($course_num_summary[$category['category_id']])) {
			$output .= '('.$course_num_summary[$category['category_id']].')';
		}
		else {
			$output .= '(0)';
		}
		if ($_GET['catid'] <> '' && $_GET['catid'] == $category['category_id']) {
			$output .= '</span>';
		}
		$output .= '</a><br />';
	}
}

// Uncategorized
if (isset($course_num_summary[0])) {
	$output .= '<a href="'.TR_BASE_HREF.'home/index.php?catid=0">';
	
	if ($_GET['catid'] <> '' && $_GET['catid'] == 0) {
		$output .= '<span class="selected-sidemenu">';
	}
		
	$output .= _AT('cats_uncategorized').'&nbsp;('.$course_num_summary[0].')';
	if ($_GET['catid'] <> '' && $_GET['catid'] == 0) {
		$output .= '</span>';
	}
	$output .= '</a><br />';
}

if ($output == '') {
	$output = _AT('none_found');
}
$savant->assign('title', _AT('category'));
$savant->assign('dropdown_contents', $output);
//$savant->assign('default_status', "hide");

$savant->display('include/box.tmpl.php');
?>
