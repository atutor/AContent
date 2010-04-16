<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* Courses Utility functions 
* @access	public
* @author	Cindy Qi Li
*/

if (!defined('TR_INCLUDE_PATH')) exit;

class CoursesUtility {

	/**
	* This function prints the drop down list box for course categories
	* @access  public
	* @param   categoryID
	* @author  Cindy Qi Li
	*/
	public static function printCourseCatsInDropDown($categoryID = 0) {	
		require_once(TR_INCLUDE_PATH."classes/DAO/CourseCategoriesDAO.class.php");
	
		echo '<option value="'.TR_COURSECATEGORY_UNCATEGORIZED.'"';
		if ($categoryID == TR_COURSECATEGORY_UNCATEGORIZED) {
			echo ' selected="selected"';
		}
		echo '>'._AT('cats_uncategorized').'</option>' . "\n";
	
		$courseCategoriesDAO = new CourseCategoriesDAO();
		$rows = $courseCategoriesDAO->getAll();

		if (is_array($rows)) {
			foreach ($rows as $row) {
				echo '<option value="'.$row['category_id'].'"';
				if ($row['category_id'] == $categoryID) {
					echo ' selected="selected"';
				}
				echo '>'.$row['category_name'].'</option>'."\n";
			}
		}
	}
}
?>