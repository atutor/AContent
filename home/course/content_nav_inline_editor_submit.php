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
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

global $db;

if (trim($_POST['field']) <> "" && trim($_POST['value']) <> "")
{
	$fields = explode('-', $_POST['field']);
	$content_id = intval($fields[1]);
	
	if ($content_id > 0)
	{
		$contentDAO = new ContentDAO();
		
		if ($contentDAO->UpdateField($content_id, 'title', $_POST['value']))
		{
			$rtn['status'] = 'success';
			$rtn['success'][] = _AT('TR_FEEDBACK_ACTION_COMPLETED_SUCCESSFULLY');
		}
		else
		{
			$rtn['status'] = 'fail';
			$rtn['success'][] = _AT('TR_ERROR_UNABLE_UPDATE_DB');
		}
		echo json_encode($rtn);
	}
}
?>
