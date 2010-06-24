<?php
/************************************************************************/
/* AContent                                                        									*/
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

/**
* Tests Utility functions 
* @access	public
* @author	Cindy Qi Li
*/

if (!defined('TR_INCLUDE_PATH')) exit;

class TestsUtility {

	/**
	* This function prints the drop down list box for question categories
	* @access  public
	* @param   categoryID
	* @author  Cindy Qi Li
	*/
	public static function printQuestionCatsInDropDown($categoryID = 0) {	
		global $_course_id;
		
		require_once(TR_INCLUDE_PATH."classes/DAO/TestsQuestionsCategoriesDAO.class.php");
	
		echo '<option value="0"';
		if ($categoryID == 0) {
			echo ' selected="selected"';
		}
		echo '>'._AT('cats_uncategorized').'</option>' . "\n";
	
		$testsQuestionsCategoriesDAO = new TestsQuestionsCategoriesDAO();
		$rows = $testsQuestionsCategoriesDAO->getByCourseID($_course_id);

		if (is_array($rows)) {
			foreach ($rows as $row) {
				echo '<option value="'.$row['category_id'].'"';
				if ($row['category_id'] == $categoryID) {
					echo ' selected="selected"';
				}
				echo '>'.$row['title'].'</option>'."\n";
			}
		}
	}
	
	/**
	* This function prints the link to open up the visual editor popup page
	* @access  public
	* @param   area: One of the values: question
	* @author  Cindy Qi Li
	*/
	public static function printVisualEditorLink ($area) {
		global $_course_id;
?>
	<script type="text/javascript" language="javascript">
		document.writeln('<a href="#" onclick="javascript:window.open(\'<?php echo TR_BASE_HREF; ?>tests/form_editor.php?area=<?php echo $area; ?>&_course_id=<?php echo $_course_id; ?>\',\'formEditorWin\',\'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480\'); return false;" style="cursor: pointer; text-decoration: none" ><?php echo _AT('use_visual_editor'); ?></a>');
	</script>

<?php
	}
	
}
?>