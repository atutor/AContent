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

define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'../tests/classes/testQuestions.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$testsQuestionsDAO = new TestsQuestionsDAO();

$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

if (isset($_GET['submit'])) {
	header('Location: '.TR_BASE_HREF.'tests/question_db.php?_course_id='.$_course_id);
	exit;
}

if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require_once(TR_INCLUDE_PATH.'header.inc.php');

$qids = explode(',', $_GET['qid']);
$rows = $testsQuestionsDAO->getByQuestionIDs($qids);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="_course_id" value="<?php echo $_course_id; ?>" />
<div class="input-form">
	<?php
	if (is_array($rows)) {
		foreach ($rows as $row) {
			$obj = TestQuestions::getQuestion($row['type']);
			$obj->display($row);
		}
	}
	?>
	<div class="row buttons"><input type="submit" name="submit" value="<?php echo _AT('back'); ?>"/></div>
</div>
</form>
<script type="text/javascript">
//<!--
function iframeSetHeight(id, height) {
	document.getElementById("qframe" + id).style.height = (height + 30) + "px";
}
//-->
</script>
<?php require_once(TR_INCLUDE_PATH.'footer.inc.php'); ?>