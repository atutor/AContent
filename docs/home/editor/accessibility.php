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

define('TR_INCLUDE_PATH', '../../include/');

require(TR_INCLUDE_PATH.'vitals.inc.php');
require(TR_INCLUDE_PATH.'../home/editor/editor_tab_functions.inc.php');

global $_course_id, $_content_id, $contentManager;

Utility::authenticate(TR_PRIV_ISAUTHOR);

$cid = $_content_id;

if ($cid == 0) {
	require(TR_INCLUDE_PATH.'header.inc.php');
	$missing_fields[] = _AT('content_id');
	$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	require (TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($contentManager)) $content_row = $contentManager->getContentPage($cid);

if (!$content_row || !isset($contentManager)) {
	require(TR_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('MISSING_CONTENT');
	require (TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$course_base_href = '';
$content_base_href = '';

//make decisions
if ($_POST['make_decision']) 
{
	//get list of decisions	
	$desc_query = '';
	if (is_array($_POST['d'])) {
		foreach ($_POST['d'] as $sequenceID => $decision) {
			$desc_query .= '&'.$sequenceID.'='.$decision;
		}
	}

	$checker_url = TR_ACHECKER_URL. 'decisions.php?'
				.'uri='.urlencode($_POST['pg_url']).'&id='.TR_ACHECKER_WEB_SERVICE_ID
				.'&session='.$_POST['sessionid'].'&output=html'.$desc_query;

	if (@file_get_contents($checker_url) === false) {
		$msg->addInfo('DECISION_NOT_SAVED');
	}
} 
else if (isset($_POST['reverse'])) 
{
	$reverse_url = TR_ACHECKER_URL. 'decisions.php?'
				.'uri='.urlencode($_POST['pg_url']).'&id='.TR_ACHECKER_WEB_SERVICE_ID
				.'&session='.$_POST['sessionid'].'&output=html&reverse=true&'.key($_POST['reverse']).'=N';
	
	if (@file_get_contents($reverse_url) === false) {
		$msg->addInfo('DECISION_NOT_REVERSED');
	} else {
		$msg->addInfo('DECISION_REVERSED');
	}
}

$popup = intval($_GET['popup']);
require(TR_INCLUDE_PATH.'header.inc.php');
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?popup=1" method="post" name="form">
  <div class="row">
<?php 					
	echo '    <input type="hidden" name="body_text" value="'.htmlspecialchars(stripslashes($_POST['body_text'])).'" />';
	echo '    <input type="hidden" name="_cid" value="'.$cid.'" />';
	
	if (!$cid) {
		$msg->printInfos('SAVE_CONTENT');

		echo '  </div>';

		return;
	}

$msg->printInfos();
if ($_POST['body_text'] != '') {
	//save temp file
	$_POST['content_path'] = $content_row['content_path'];
	write_temp_file();

	$pg_url = TR_BASE_HREF.'get_acheck.php/'.$cid . '.html';
	$checker_url = TR_ACHECKER_URL.'checkacc.php?uri='.urlencode($pg_url).'&id='.TR_ACHECKER_WEB_SERVICE_ID
					. '&guide=WCAG2-L2&output=html';

	$report = @file_get_contents($checker_url);

	if (stristr($report, '<div id="error">')) {
		$msg->printErrors('INVALID_URL');
	} else if ($report === false) {
		$msg->printInfos('SERVICE_UNAVAILABLE');
	} else {
		echo '    <input type="hidden" name="pg_url" value="'.$pg_url.'" />';
		echo $report;	

		echo '    <p>'._AT('access_credit').'</p>';
	}
	//delete file
	@unlink(TR_CONTENT_DIR . $cid . '.html');

} else {
	$msg->printInfos('NO_PAGE_CONTENT');
} 
?>
  </div>
</form>
<?php 
require(TR_INCLUDE_PATH.'footer.inc.php');
?>