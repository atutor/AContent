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
require_once(TR_INCLUDE_PATH.'lib/pclzip.lib.php');
require_once(TR_INCLUDE_PATH.'lib/pclzip_callback.lib.php');
require_once(TR_INCLUDE_PATH.'lib/qti.inc.php'); 
require_once(TR_INCLUDE_PATH.'classes/QTI/QTIImport.class.php');
require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsAssocDAO.class.php');

global $_course_id;

/* to avoid timing out on large files */
@set_time_limit(0);
$_SESSION['done'] = 1;

$element_path = array();
$character_data = '';
$test_title = '';
$resource_num = 0;
$qids = array();	//store all the question ids that's being inserted into the db by this import
$overwrite = false;	//files will not be overwrite and prompt

/* handle get */
if (isset($_POST['submit_yes'])){
	$overwrite = true;
} elseif (isset($_POST['submit_no'])){
	$msg->addFeedback('IMPORT_CANCELLED');
	header('Location: index.php?_course_id='.$_course_id);
	exit;
}

/* functions */
/* called at the start of en element */
/* builds the $path array which is the path from the root to the current element */
function startElement($parser, $name, $attrs) {
	global $attributes, $element_path, $resource_num;
	//save attributes.
	switch($name) {
		case 'resource':
			$attributes[$name.$resource_num]['identifier'] = $attrs['identifier'];
			$attributes[$name.$resource_num]['href'] = $attrs['href'];
			$attributes[$name.$resource_num]['type'] = $attrs['type'];
			$resource_num++;
			break;
		case 'file':
			if(in_array('resource', $element_path)){
				$attributes['resource'.($resource_num-1)]['file'][] = $attrs['href'];
			}
			break;
		case 'dependency':
			if(in_array('resource', $element_path)){
				$attributes['resource'.($resource_num-1)]['dependency'][] = $attrs['identifierref'];
			}
			break;

	}
	array_push($element_path, $name);		
}

/* called when an element ends */
/* removed the current element from the $path */
function endElement($parser, $name) {
	global $element_path, $test_title, $character_data;
	switch($name) {
		case 'title':
			if (in_array('organization', $element_path)){
				$test_title = $character_data;
			}
	}
	$character_data = '';
	array_pop($element_path);
}

/* called when there is character data within elements */
/* constructs the $items array using the last entry in $path as the parent element */
function characterData($parser, $data){
	global $character_data;
	if (trim($data)!=''){
		$character_data .= preg_replace('/[\t\0\x0B]*/', '', $data);
	}
}

//If overwrite hasn't been set to true, then the file has not been exported and still in the cache.
//otherwise, the zip file is extracted but has not been deleted (due to the confirmation).
if (!$overwrite){
	if (!isset($_POST['submit_import'])) {
		/* just a catch all */
		
		$errors = array('FILE_MAX_SIZE', ini_get('post_max_size'));
		$msg->addError($errors);

		header('Location: index.php?_course_id='.$_course_id);
		exit;
	} 


	//Handles import
	/*
	if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
		if ($content = @file_get_contents($_POST['url'])) {

			// save file to /content/
			$filename = substr(time(), -6). '.zip';
			$full_filename = TR_CONTENT_DIR . $filename;

			if (!$fp = fopen($full_filename, 'w+b')) {
				echo "Cannot open file ($filename)";
				exit;
			}

			if (fwrite($fp, $content, strlen($content) ) === FALSE) {
				echo "Cannot write to file ($filename)";
				exit;
			}
			fclose($fp);
		}	
		$_FILES['file']['name']     = $filename;
		$_FILES['file']['tmp_name'] = $full_filename;
		$_FILES['file']['size']     = strlen($content);
		unset($content);
		$url_parts = pathinfo($_POST['url']);
		$package_base_name_url = $url_parts['basename'];
	}
	*/
	$ext = pathinfo($_FILES['file']['name']);
	$ext = $ext['extension'];

	if ($ext != 'zip') {
		$msg->addError('IMPORTDIR_IMS_NOTVALID');
	} else if ($_FILES['file']['error'] == 1) {
		$errors = array('FILE_MAX_SIZE', ini_get('upload_max_filesize'));
		$msg->addError($errors);
	} else if ( !$_FILES['file']['name'] || (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_POST['url'])) {
		$msg->addError('FILE_NOT_SELECTED');
	} else if ($_FILES['file']['size'] == 0) {
		$msg->addError('IMPORTFILE_EMPTY');
	} 
}

if ($msg->containsErrors()) {
//	if (isset($_GET['tile'])) {
//		header('Location: '.$_base_path.'tile/index.php');
//	} else {
		header('Location: index.php?_course_id='.$_course_id);
//	}
	exit;
}

/* check if ../content/import/ exists */
$import_path = TR_CONTENT_DIR . 'import/';
$content_path = TR_CONTENT_DIR;

if (!is_dir($import_path)) {
	if (!@mkdir($import_path, 0700)) {
		$msg->addError('IMPORTDIR_FAILED');
	}
}

$import_path .= $_course_id.'/';
if (!$overwrite){
	if (is_dir($import_path)) {
		FileUtility::clr_dir($import_path);
	}

	if (!@mkdir($import_path, 0700)) {
		$msg->addError('IMPORTDIR_FAILED');
	}

	/* extract the entire archive into TR_COURSE_CONTENT . import/$course using the call back function to filter out php files */
	error_reporting(0);
	$archive = new PclZip($_FILES['file']['tmp_name']);
	if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
							PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
		$msg->addError('IMPORT_FAILED');
		echo 'Error : '.$archive->errorInfo(true);
		FileUtility::clr_dir($import_path);
		header('Location: question_db.php?_course_id='.$_course_id);
		exit;
	}
	error_reporting(TR_ERROR_REPORTING);
}
/* get the course's max_quota */
$coursesDAO = new CoursesDAO();
$q_row	= $coursesDAO->get($_course_id);

if ($q_row['max_quota'] != TR_COURSESIZE_UNLIMITED) {
	$zip_size_limit = $MaxCourseSize;
	
	$totalBytes   = FileUtility::dirsize($import_path);
	
	$total_after  = $zip_size_limit - $totalBytes;
	
	if (is_dir(TR_CONTENT_DIR . $_course_id.'/')) 
	{
		$course_total = FileUtility::dirsize(TR_CONTENT_DIR . $_course_id.'/');
		$total_after  -= $course_total;
	}
	
	if ($total_after < 0) {
		/* remove the content dir, since there's no space for it */
		$errors = array('NO_CONTENT_SPACE', number_format(-1*($total_after/TR_KBYTE_SIZE), 2 ) );
		$msg->addError($errors);
		
		// Clean up import path and inserted course row
		FileUtility::clr_dir($import_path);
	
		header('Location: index.php?_course_id='.$_course_id);
		exit;
	}
}

$ims_manifest_xml = @file_get_contents($import_path.'imsmanifest.xml');

if ($ims_manifest_xml === false) {
	$msg->addError('NO_IMSMANIFEST');

	if (file_exists($import_path . 'atutor_backup_version')) {
		$msg->addError('NO_IMS_BACKUP');
	}

	FileUtility::clr_dir($import_path);

//	if (isset($_GET['tile'])) {
//		header('Location: '.$_base_path.'tile/index.php');
//	} else {
		header('Location: index.php?_course_id='.$_course_id);
//	}
	exit;
}

$xml_parser = xml_parser_create();

xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
xml_set_element_handler($xml_parser, 'startElement', 'endElement');
xml_set_character_data_handler($xml_parser, 'characterData');

if (!xml_parse($xml_parser, $ims_manifest_xml, true)) {
	die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)));
}

xml_parser_free($xml_parser);

//assign folder names
//if (!$package_base_name){
//	$package_base_name = substr($_FILES['file']['name'], 0, -4);
//}

//$package_base_name = strtolower($package_base_name);
//$package_base_name = str_replace(array('\'', '"', ' ', '|', '\\', '/', '<', '>', ':'), '_' , $package_base_name);
//$package_base_name = preg_replace("/[^A-Za-z0-9._\-]/", '', $package_base_name);

//if (is_dir(TR_CONTENT_DIR . $_SESSION['course_id'].'/'.$package_base_name)) {
//	echo 'Already exist: Quitting.  (Need better msg here)';
//	exit;
//	$package_base_name .= '_'.date('ymdHis');
//}

if ($package_base_path) {
	$package_base_path = implode('/', $package_base_path);
}

//debug($attributes);
//Dependency handling
//$media_items = array();
$xml_items = array();
//foreach($attributes as $resource=>$attrs){
//	if ($attrs['type'] != 'webcontent'){
//		$media_items[$attrs['identifier']] = $attrs['file'];
//	}
//}

//Check if the files exist, if so, warn the user.
$existing_files = isQTIFileExist($attributes);
//debug($existing_files);
if (!$overwrite && !empty($existing_files)){
	$existing_files = implode('<br/>', $existing_files);
	require_once(TR_INCLUDE_PATH.'header.inc.php');
//	$msg->addConfirm(array('MEDIA_FILE_EXISTED', $existing_files));
//	$msg->printConfirm();
	echo '<form action="" method="POST">';
	echo '<div class="input-form">';
	echo '<div class="row">';
	$msg->printInfos(array('MEDIA_FILE_EXISTED', $existing_files));
	echo '</div>';
	echo '<div class="row buttons">';
	echo '<input type="submit" class="" name="submit_yes" value="'._AT('yes').'"/>';
	echo '<input type="submit" class="" name="submit_no" value="'._AT('no').'"/>';
	echo '<input type="hidden" name="submit_import" value="submit_import" />';
	ECHO '<input type="hidden" name="url" value="'.$_POST['url'].'" />';
	echo '</div></div>';
	echo '</form>';
	require (TR_INCLUDE_PATH.'footer.inc.php');

	exit;
}

//Get the XML file out and start importing them into our database.
//TODO: See question_import.php 287-289.
$qti_import = new QTIImport($import_path);
$qids = $qti_import->importQuestions($attributes);

//import test
$tid = $qti_import->importTest();

//associate question and tests
foreach ($qids as $order=>$qid){
	if (isset($qti_import->weights[$order])){
		$weight = round($qti_import->weights[$order]);
	} else {
		$weight = 0;
	}
	$new_order = $order + 1;
//	$sql = "INSERT INTO " . TABLE_PREFIX . "tests_questions_assoc" . 
//			"(test_id, question_id, weight, ordering, required) " .
//			"VALUES ($tid, $qid, $weight, $new_order, 0)";
//	$result = mysql_query($sql, $db);
	$testsQuestionsAssocDAO = new TestsQuestionsAssocDAO();
	$testsQuestionsAssocDAO->Create($tid, $qid, $weight, $new_order);
}
//debug('imported test');
if (!$msg->containsErrors()) {
	$msg->addFeedback('IMPORT_SUCCEEDED');
}

//clear directory
FileUtility::clr_dir(TR_CONTENT_DIR . 'import/'.$_course_id);

header('Location: index.php?_course_id='.$_course_id);
exit;
?>