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

global $languageManager;
require_once(TR_INCLUDE_PATH.'classes/CoursesUtility.class.php');
require_once(TR_INCLUDE_PATH.'../home/classes/GoalsManager.class.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?_course_id='.$this->course_id; ?>" name="form">
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
<?php if(isset( $_REQUEST['_struct_name'])) { 

 	echo '<input type="hidden" name="_struct_name" value="'. $_REQUEST['_struct_name']. '" />';
}
?>


<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('course_property'); ?></legend>

	<table class="form-data" align="center">
		<tr>
			<td colspan="2" align="left"><br/><?php echo _AT('required_field_text') ;?></td>
		</tr>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="title"><?php echo _AT('title'); ?></label>:</td>
			<td align="left"><input id="title" name="title" type="text" maxlength="255" size="45" value="<?php if (isset($_POST['login'])) echo stripslashes(htmlspecialchars($_POST['title'])); else echo stripslashes(htmlspecialchars($this->course_row['title'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="category"><?php  echo _AT('category_name'); ?></label></td>
			<td align="left">
			<select name="category_id" id="category">
				<?php if (isset($_POST['category_id'])) $category_id = $_POST['category_id'];
				      else $category_id = $this->course_row['category_id'];
				      CoursesUtility::printCourseCatsInDropDown($category_id); ?>
			</select>
			</td>
		</tr>

		<tr>
			<td align="left"><label for="pri_lang"><?php  echo _AT('primary_language'); ?></label></td>
			<td align="left"><?php $languageManager->printDropdown($this->course_row['primary_language'], 'pri_lang', 'pri_lang'); ?></td>
		</tr>

		<tr>
			<td align="left"><label for="description"><?php echo _AT('description'); ?></label></td>
			<td align="left"><textarea id="description" cols="45" rows="2" name="description"><?php if (isset($_POST['description'])) echo stripslashes(htmlspecialchars($_POST['description'])); else echo stripslashes(htmlspecialchars($this->course_row['description'])); ?></textarea></td>
		</tr>
		<!--  catia GOAL -->
		<!--  
		<tr>
		<td align="left"><p>Goals</p></td>
		<td>
			<fieldset>
				<legend>Choose the lesson's goals</legend>
						 <?php 
						 /*$goals_manager = new GoalsManager();
						 
						 $goals = $goals_manager->getGoals();
						 	foreach ($goals as $goal) {
						 		
						 		echo '<div style="display:inline-block">';
						 		echo '<input id="'.$goal.'" type="checkbox" name="'.$goal.'"></input>';
						 		echo '<label for="'.$goal.'">'.$goal.'</label>';
								echo '</div>';
								
						 	}*/
						 
						 ?>
						
				
			</fieldset>
			
		</td>	
		</tr> -->
		
		<tr>
			<td align="left"><label for="copyright"><?php echo _AT('course_copyright'); ?></label></td>
			<td align="left"><textarea name="copyright" rows="2" cols="65" id="copyright"><?php if (isset($_POST['copyright'])) echo stripslashes(htmlspecialchars($_POST['copyright'])); else echo stripslashes(htmlspecialchars($this->course_row['copyright'])); ?></textarea></td>
		</tr>

		<tr>
			<td colspan="2" align="left">
			  <input type="checkbox" name="hide_course" id="hide_course" value="1" <?php if ($this->course_row['access']=='private') echo "checked"; ?> />
			  <label for="hide_course"><?php echo _AT('hide_course'); ?></label>
			</td>
		</tr>

		<tr>
			<td colspan="2">
			
			
			<p class="submit_button">
				<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" class="submit" />
				<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="submit" />
				
			</p>
			</td>
		</tr>
	</table>

</fieldset>
</div>
</form>
