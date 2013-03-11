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
global $_course_id, $_content_id;

include_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');
include_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
/* Get the list of associated tests with this content on page load */
$cid = $_REQUEST['cid'] = $_content_id;	//uses request 'cause after 'saved', the cid will become $_GET.

$testsDAO = new TestsDAO();

$num_tests = 0;
$all_tests = $testsDAO->getByCourseID($_course_id);
/* get a list of all the tests we have, and links to create, edit, delete, preview */
//$sql	= "SELECT *, UNIX_TIMESTAMP(start_date) AS us, UNIX_TIMESTAMP(end_date) AS ue 
//             FROM ".TABLE_PREFIX."tests 
//            WHERE course_id=$_SESSION[course_id] 
//            ORDER BY start_date DESC";
//$result	= mysql_query($sql, $db);
if (is_array($all_tests)) $num_tests = count($all_tests);

//If there are no tests, don't display anything except a message
if ($num_tests == 0){
	$msg->addInfo(array('NO_TESTS', TR_BASE_HREF.'tests/create_test.php?_course_id='.$_course_id));
	$msg->printInfos();
	return;
}
else {
	$i = 0;
	foreach ($all_tests as $row) {
		$results[$i]['test_id'] = $row['test_id'];
		$results[$i]['title'] = $row['title'];
			
		$i++;
	}
}
?>
<div class="row">
	<span style="font-weight:bold"><?php echo _AT('about_content_tests', TR_BASE_HREF.'tests/create_test.php?_course_id='.$_course_id); ?></span>
</div>

<div class="row">
	<p><?php echo _AT('custom_test_message'); ?></p>
	<textarea name="test_message"><?php echo $_POST['test_message']; ?></textarea>
</div>

<?php 

	include_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');
	$contentTestsAssocDAO = new ContentTestsAssocDAO();
	$test_rows = $contentTestsAssocDAO->getByContent($cid);
	if(is_array($test_rows)){
	    foreach($test_rows as $test_row){
	        $test_row_ids[] = $test_row['test_id'];
	    }
	}
if (is_array($results)) print_test_table($results, $test_row_ids);

function print_test_table($results, $post_tids, $id_prefix='') {?>
	<div>
	<input type="hidden" name="visited_tests" value="1" />
	<table class="data" summary="" style="width: 90%" rules="cols">
	<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('title');          ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (is_array($results)) { ?>
	<?php foreach ($results as $row) { ?>
	<?php
		$checkMe = '';
		if (is_array($post_tids) && in_array($row['test_id'], $post_tids)){
			$checkMe = ' checked="checked"';
		} 
	?>
	<tr onmousedown="toggleTestSelect('<?php echo $id_prefix; ?>r_<?php echo $row['test_id']; ?>');rowselect(this);" id="<?php echo $id_prefix; ?>r_<?php echo $row['test_id']; ?>">
		<td><input type="checkbox" name="<?php echo $id_prefix; ?>tid[]" value="<?php echo $row['test_id']; ?>" id="<?php echo $id_prefix; ?>t<?php echo $row['test_id']; ?>" <?php echo $checkMe; ?> onmouseup="this.checked=!this.checked" /></td>
		<td><?php echo $row['title']; ?></td>
	</tr>
	<?php } ?>
	<?php } ?>
	</tbody>
	</table>
	</div>
	<br />
<?php }?>

<script language="javascript" type="text/javascript">
	function toggleTestSelect(r_id){
		var row = document.getElementById(r_id);
		var checkBox = row.cells[0].firstChild;

		if (checkBox.checked == true){
			checkBox.checked = false;
		} else {
			checkBox.checked = true;
		}
	}
</script>
