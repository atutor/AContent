<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }
require_once(TR_INCLUDE_PATH.'vitals.inc.php');

global $savant;

$savant->assign('title', _AT('getting_start'));
$savant->assign('dropdown_contents', _AT('getting_start_info'));
//$savant->assign('default_status', "hide");

$savant->display('include/box.tmpl.php');
?>