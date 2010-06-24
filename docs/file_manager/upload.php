<?php
/************************************************************************/
/* AContent                                                         */
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
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');

global $_course_id;
Utility::authenticate(TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE);
$coursesDAO = new CoursesDAO();

$_SESSION['done'] = 1;
$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];
$alter = $_REQUEST['alter'];

//echo $_REQUEST['cid'];
//echo $_REQUEST['tab'];

//echo $alter;

/* get this courses MaxQuota and MaxFileSize: */
$row = $coursesDAO->get($_course_id);
$my_MaxCourseSize = $row['max_quota'];
$my_MaxFileSize	= $row['max_file_size'];

if ($my_MaxCourseSize != TR_COURSESIZE_UNLIMITED) $my_MaxCourseSize = $MaxCourseSize;
$my_MaxFileSize = FileUtility::megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));

//	if ($my_MaxCourseSize == TR_COURSESIZE_DEFAULT) {
//		$my_MaxCourseSize = $MaxCourseSize;
//	}
//	if ($my_MaxFileSize == TR_FILESIZE_DEFAULT) {
//		$my_MaxFileSize = $MaxFileSize;
//	} else if ($my_MaxFileSize == TR_FILESIZE_SYSTEM_MAX) {
//		$my_MaxFileSize = megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));
//	}

$path = TR_CONTENT_DIR . $_course_id.'/'.$_POST['pathext'];

if (isset($_POST['submit'])) {

	if($_FILES['uploadedfile']['name'])	{

		$_FILES['uploadedfile']['name'] = trim($_FILES['uploadedfile']['name']);
		$_FILES['uploadedfile']['name'] = str_replace(' ', '_', $_FILES['uploadedfile']['name']);

		$path_parts = pathinfo($_FILES['uploadedfile']['name']);
		$ext = $path_parts['extension'];

		/* check if this file extension is allowed: */
		/* $IllegalExtentions is defined in ./include/config.inc.php */
		if (in_array($ext, $IllegalExtentions)) {
			$errors = array('FILE_ILLEGAL', $ext);
			$msg->addError($errors);
			header('Location: index.php?pathext='.$_POST['pathext'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'_course_id='.$_course_id);
			exit;
		}

		/* also have to handle the 'application/x-zip-compressed'  case	*/
		if (   ($_FILES['uploadedfile']['type'] == 'application/x-zip-compressed')
			|| ($_FILES['uploadedfile']['type'] == 'application/zip')
			|| ($_FILES['uploadedfile']['type'] == 'application/x-zip')){
			$is_zip = true;						
		}

	
		/* anything else should be okay, since we're on *nix.. hopefully */
		$_FILES['uploadedfile']['name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_FILES['uploadedfile']['name']);


		/* if the file size is within allowed limits */
		if( ($_FILES['uploadedfile']['size'] > 0) && ($_FILES['uploadedfile']['size'] <= $my_MaxFileSize) ) {

			/* if adding the file will not exceed the maximum allowed total */
			$course_total = FileUtility::dirsize($path);

			if ((($course_total + $_FILES['uploadedfile']['size']) <= $my_MaxCourseSize) || ($my_MaxCourseSize == TR_COURSESIZE_UNLIMITED)) {

				/* check if this file exists first */
				if (file_exists($path.$_FILES['uploadedfile']['name'])) {
					/* this file already exists, so we want to prompt for override */

					/* save it somewhere else, temporarily first			*/
					/* file_name.time ? */
					$_FILES['uploadedfile']['name'] = substr(time(), -4).'.'.$_FILES['uploadedfile']['name'];

					$f = array('FILE_EXISTS',
									substr($_FILES['uploadedfile']['name'], 5));
					$msg->addFeedback($f);
				}

				/* copy the file in the directory */
				$result = move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $path.$_FILES['uploadedfile']['name'] );

				if (!$result) {
					require(TR_INCLUDE_PATH.'header.inc.php');
					$msg->printErrors('FILE_NOT_SAVED');
					echo '<a href="../file_manager/index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'] . SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'_course_id='.$_course_id.'">' . _AT('back') . '</a>';
					require(TR_INCLUDE_PATH.'footer.inc.php');
					exit;
				} else {
					if ($is_zip) {
						$f = array('FILE_UPLOADED_ZIP',
										urlencode($_POST['pathext']), 
										urlencode($_FILES['uploadedfile']['name']), 
										$_GET['popup'],
										$_course_id,
										SEP);
						$msg->addFeedback($f);
						if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab'].SEP.'_course_id='.$_course_id);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'_course_id='.$_course_id);
						exit;
					} /* else */

					// uploading an alternative content object
					if ($_GET['a_type'] > 0) {
						header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'uploadfile='.urlencode($_FILES['uploadedfile']['name']).SEP.'_course_id='.$_course_id);
					}
					else {
						$msg->addFeedback('FILE_UPLOADED');

						if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab'].SEP.'_course_id='.$_course_id);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'_course_id='.$_course_id);
					}
					exit;
				}
			} else {
				$msg->addError(array('MAX_STORAGE_EXCEEDED', get_human_size($my_MaxCourseSize)));
				if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab'].SEP.'_course_id='.$_course_id);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'_course_id='.$_course_id);
						
				exit;
			}
		} else {
			$msg->addError(array('FILE_TOO_BIG', get_human_size($my_MaxFileSize)));
			if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab'].SEP.'_course_id='.$_course_id);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'_course_id='.$_course_id);
						
			exit;
		}
	} else {
		$msg->addError('FILE_NOT_SELECTED');
		if ($alter)
			header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab'].SEP.'_course_id='.$_course_id);
		else
			header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'_course_id='.$_course_id);
		exit;
	}
}

?>