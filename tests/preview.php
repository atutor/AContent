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

define('TR_INCLUDE_PATH', '../include/');

require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/testQuestions.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsAssocDAO.class.php');

global $_course_id;
//Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsDAO = new TestsDAO();
$testsQuestionsAssocDAO = new TestsQuestionsAssocDAO();

$_letters = array(_AT('a'), _AT('b'), _AT('c'), _AT('d'), _AT('e'), _AT('f'), _AT('g'), _AT('h'), _AT('i'), _AT('j'));

if ($_POST['back']) {
	header('Location: index.php?_course_id='.$_course_id);
	exit;
} 

if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require_once(TR_INCLUDE_PATH.'header.inc.php');

$tid = intval($_GET['tid']);

// check that the test_id is correct
if (!($test_row = $testsDAO->get($tid))) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require (TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$rows = $testsQuestionsAssocDAO->getByTestID($tid);
$count = 1;
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?_course_id='.$_course_id; ?>" name="preview">

<?php if (is_array($rows)) {?>
	<div class="input-form">
	<div class="row"><h2><?php echo $test_row['title']; ?></h2></div>


	<?php if ($test_row['instructions'] != ''): ?>
		<div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
			<strong><?php echo _AT('instructions'); ?></strong>
		</div>
		<div class="row" style="padding-bottom: 20px"><?php echo $test_row['instructions']; ?></div>
	<?php endif; ?>
	
	<?php
	foreach ($rows as $row) {
		$o = TestQuestions::getQuestion($row['type']);
		$o->display($row);
	}
	
	// "back" button only appears when the request is from index page of "tests" module
	if (stripos($_SERVER['HTTP_REFERER'], 'tests/index.php')) { ?>
	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('back'); ?>" name="back" />
	</div>
	<?php }?>
	</div>
</form>
<script type="text/javascript">
//<!--
function iframeSetHeight(id, height) {
	document.getElementById("qframe" + id).style.height = (height + 20) + "px";
}
//-->
</script>
<?php
} else {
	$msg->printErrors('NO_QUESTIONS');
}


require_once(TR_INCLUDE_PATH.'footer.inc.php');
?>
