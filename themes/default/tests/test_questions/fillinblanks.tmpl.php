<?php $text = AT_print($this->row['question'], 'tests_questions.question'); 
$exp_text = explode(' ', $text);
$j=0;
?><p>
<?php 
for ($i=0; $i<count($exp_text); $i++) {
	if ($i == $this->row['answer_'.$j]) {
		if ($this->row['option_'.$j]== '') { ?>
		<span><input style="width: 80px;" type="text" id="choice_<?php $this->row['question_id'];?>_<?php echo $i;?>" name="answers[<?php echo $this->row['question_id']; ?>][<?php echo $i; ?>]" </span>
		<?php  } else { ?>
			<span><select id="choice_<?php $this->row['question_id'];?>_<?php echo $i;?>" name="answers[<?php echo $this->row['question_id']; ?>][<?php echo $i; ?>]">
				<?php 
					$rand = rand(0, 1); 
					$values = array($exp_text[$i], $this->row['option_'.$j]);	
				?>
				<option style="width: 80px;" value="leave_blank"> </option>
				<option style="width: 80px;" value="<?php echo $values[$rand];?>"><?php echo $values[$rand];?></option>
				<option style="width: 80px;" value="<?php echo $values[!$rand];?>"><?php echo $values[!$rand];?></option>
			</select> </span>
		<?php }	
		$j++;
	} else {
		echo $exp_text[$i] .' ';
	}

}

?>
</p>





