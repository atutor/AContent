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
global $strlen, $substr;

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?tid=<?php echo $this->tid.SEP; ?>_course_id=<?php echo $this->course_id;?>" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo $this->tid; ?>" />
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
<table class="data static" summary="" rules="rows">
<thead>
<tr>
	<th scope="col"><?php echo _AT('num');      ?></th>
	<th scope="col"><?php echo _AT('points');   ?></th>
	<th scope="col"><?php echo _AT('order'); ?></th>
	<th scope="col"><?php echo _AT('question'); ?></th>
	<th scope="col"><?php echo _AT('type');     ?></th>
	<th scope="col"><?php echo _AT('category'); ?></th>
	<th scope="col">&nbsp;</th>
</tr>
</thead>
<?php
if (is_array($this->rows)) {
	foreach ($this->rows as $row) {
		$count++;

		if (isset($_POST['submit'])) {
			$row['weight'] = $_POST['weight'][$row['question_id']];
			$row['required'] = (isset($_POST['required'][$row['question_id']]) ? 1 : 0);
		}
?>
	<tr>
		<td class="row1" align="center"><strong><?php echo $count; ?></strong></td>
		<td class="row1" align="center">
		
		<?php if ($row['type'] == 4) {?>
			<?php echo _AT('na'); ?>
			<input type="hidden" value="0" name="weight[<?php echo $row['question_id']; ?>]" />
		<?php } else {?>
			<input type="text" value="<?php echo $row['weight']; ?>" name="weight[<?php echo $row['question_id']; ?>]" size="2" />
		<?php }?>
		</td>

		<td class="row1" align="center"><input type="text" name="ordering[<?php echo $row['question_id']; ?>]" value="<?php echo $row['ordering']; ?>" size="2" /></td>

		<td class="row1">
		<?php if ($strlen($row['question']) > 45) {
			echo htmlspecialchars(AT_print($substr($row['question'], 0, 43), 'tests_questions.question'), ENT_COMPAT, "UTF-8") . '...';
		} else {
			echo AT_print(htmlspecialchars($row['question'], ENT_COMPAT, "UTF-8"), 'tests_questions.question');
		}?>

		</td>
		<td nowrap="nowrap">
		<?php $o = TestQuestions::getQuestion($row['type']); echo $o->printName();
		$link = 'tests/edit_question_'.$o->getPrefix().'.php?tid='.$this->tid.SEP.'qid='.$row['question_id'].SEP.'_course_id='.$this->course_id;?>
		</td>

		<td align="center"><?php echo $this->cats[$row['category_id']]; ?></td>

		<td nowrap="nowrap">
		<a href="<?php echo $link; ?>"><?php echo _AT('edit'); ?></a> |
		<a href="tests/question_remove.php?tid=<?php echo $this->tid . SEP; ?>qid=<?php echo $row['question_id'].SEP; ?>_course_id=<?php echo $this->course_id; ?>"><?php echo _AT('remove'); ?></a>
		</td>

		</tr>
	<?php } ?>

	<tfoot>
	<tr><td>&nbsp;</td>
	<td colspan="6" align="left" nowrap="nowrap"><input type="submit" value="<?php echo _AT('update'); ?>" name="submit" /></td>
	</tr>
	</tfoot>
<?php } else {?>
	<tr><td colspan="6" ><?php echo _AT('none_found'); ?></td></tr>
<?php }?>

</table><br />
</form>
