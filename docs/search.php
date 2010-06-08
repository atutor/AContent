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

/*
 * This is the web service interface to search transformable and return
 * search results in REST
 * Expected parameters:
 * id: to identify the user. must be given
 * keywords: The keywords to search for. must be given
 * start: start receiving from this record number, 0 if not specified
 * maxResults: Number of results desired, 10 if not specified
 */

define('TR_INCLUDE_PATH', 'include/');

include(TR_INCLUDE_PATH.'vitals.inc.php');
include_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');
include_once(TR_INCLUDE_PATH. 'classes/DAO/UsersDAO.class.php');
include_once(TR_INCLUDE_PATH. 'classes/DAO/CoursesDAO.class.php');
include_once(TR_INCLUDE_PATH. 'classes/RESTWebServiceOutput.class.php');

$keywords = trim(urldecode($_REQUEST['keywords']));
$web_service_id = trim($_REQUEST['id']);
$start = intval(trim(strtolower($_REQUEST['start'])));
$maxResults = intval(trim(strtolower($_REQUEST['maxResults'])));

if ($maxResults == 0) $maxResults = 10;  // default

// validate parameters
if ($keywords == '')
{
	$errors[] = 'TR_ERROR_EMPTY_KEYWORDS';
}

if ($web_service_id == '')
{
	$errors[] = 'TR_ERROR_EMPTY_WEB_SERVICE_ID';
}
else
{ // validate web service id
	$usersDAO = new UsersDAO();
	$user_row = $usersDAO->getUserByWebServiceID($web_service_id);

	if (!$user_row) $errors[] = 'TR_ERROR_INVALID_WEB_SERVICE_ID';
	
	$user_id = $user_row['user_id'];
}

// return errors
if (is_array($errors))
{
	echo RESTWebServiceOutput::generateErrorRpt($errors);
	exit;
}

$coursesDAO = new CoursesDAO();
$results = $coursesDAO->getSearchResult($keywords, '', $start, $maxResults);

// get total number of search results regardless of $maxResults
$all_results = $coursesDAO->getSearchResult($keywords);
if (is_array($all_results)) $total_num = count($all_results);
else $total_num = 0;

// calculate the last record number
if (is_array($results))
{
	$num_of_results = count($results);
	
	if ($maxResults > $num_of_results) $last_rec_number = $start + $num_of_results;
	else $last_rec_number = $start + $maxResults;
}
else $last_rec_number = $total_num;

//debug($results);exit;
$restWebServiceOutput = new RESTWebServiceOutput($results, $total_num, $last_rec_number);
echo $restWebServiceOutput->getWebServiceOutput();
?>
