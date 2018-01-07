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

global $_course_id;

if (isset($_GET['reset_filter'])) {
	unset($_GET['category_id']);
}
if (!isset($_GET['category_id'])) {
	// Suppress warnings
	$_GET['category_id'] = -1;
}
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsCategoriesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

$testsQuestionsDAO = new TestsQuestionsDAO();
$testsQuestionsCategoriesDAO = new TestsQuestionsCategoriesDAO();

$cats = array();
if ($_GET['category_id'] >= 0) {
	$category_row = $testsQuestionsCategoriesDAO->get($_GET[category_id]);
} else {
	$category_rows = $testsQuestionsCategoriesDAO->getByCourseID($_course_id);
}

if ($_GET['category_id'] <= 0) {
	$cats[] = array('title' => _AT('cats_uncategorized'), 'category_id' => 0);
}

if (is_array($category_rows)) {
	foreach ($category_rows as $row) $cats[] = $row;
}
else if (isset($category_row) && $category_row <> '') {
	$cats[] = $category_row;
}

$cols = 3;
?>

<?php if ($tid): ?>
	<form method="post" action="tests/add_test_questions_confirm.php?_course_id=<?php echo $_course_id; ?>" name="form">
<?php else: ?>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<?php endif; ?>
<input type="hidden" name="_course_id" value="<?php echo $_course_id; ?>" />
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('question'); ?></th>
	<th scope="col"><?php echo _AT('type'); ?></th>
</tr>
</thead>
<tfoot>
<?php if ($tid): ?>
	<tr>
		<td colspan="3">
			<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
			<input type="submit" name="submit" value="<?php echo _AT('add_to_test_survey'); ?>" accesskey="s" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="3">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
			<input type="submit" name="preview" value="<?php echo _AT('preview'); ?>" />
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
			<select name='qti_export_version' >
				<option selected='selected' value='1.2.1'>QTI 1.2.1</option>
				<option value='2.1'>QTI 2.1</option>
			</select>
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		</td>
	</tr>
<?php endif; ?>
</tfoot>
<tbody>
<?php

$question_flag = FALSE;

//output categories
foreach ($cats as $cat) {
	//ouput questions
	$rows = $testsQuestionsDAO->getByCourseIDAndCategoryID($_course_id, $cat['category_id']);
	if (is_array($rows)) {
		$question_flag = TRUE;
		echo '<tr>';
		echo '<th colspan="'.$cols.'">';

		echo '<input type="checkbox" name="cat'.$cat['category_id'].'" id="cat'.$cat['category_id'].'" onclick="javascript:selectCat('.$cat['category_id'].', this);" /><label for="cat'.$cat['category_id'].'">'.$cat['title'].'</label>';
		echo '</th>';
		echo '</tr>';

		foreach ($rows as $row) {
			echo '<tr onmousedown="document.form[\'q' . $row['question_id'] . '\'].checked = !document.form[\'q' . $row['question_id'] . '\'].checked; togglerowhighlight(this, \'q'.$row['question_id'].'\');" id="rq'.$row['question_id'].'">';
			echo '<td>';
			echo '<input type="checkbox" value="'.$row['question_id'].'|'.$row['type'].'" name="questions['.$cat['category_id'].'][]" id="q'.$row['question_id'].'" onmouseup="this.checked=!this.checked" /></td>';
			echo '<td>';
			echo '<a title="'.AT_print($row['question'], 'tests_questions.question').'">';
			echo AT_print(Utility::validateLength($row['question'], 100, 1), 'tests_questions.question');
			echo '</a>';
			echo '</td>';
			echo '<td>';
			$o = TestQuestions::getQuestion($row['type']);
			$o->printName();
					
			echo '</td>';
			
			echo '</tr>';
		}
	} 
}  
if (!$question_flag) {
	echo '<tr><td colspan="'.$cols.'">'._AT('none_found').'</td></tr>';
}
?>
</tbody>
</table>
</form>

<script language="javascript" type="text/javascript">
// <!--
	function selectCat(catID, cat) {
		for (var i=0;i<document.form.elements.length;i++) {
			var e = document.form.elements[i];
			if ((e.name == 'questions[' + catID + '][]') && (e.type=='checkbox')) {
				e.checked = cat.checked;
				togglerowhighlight(document.getElementById("r" + e.id), e.id);
			}
		}
	}
	
function togglerowhighlight(obj, boxid) {
	if (document.getElementById(boxid).checked) {
		obj.className = 'selected';
	} else {
		obj.className = '';
	}
}
// -->
</script>
