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
require (TR_INCLUDE_PATH.'vitals.inc.php');

// URL called by form action
$savant->assign('url', dirname($_SERVER['PHP_SELF']) . "/patch_creator.php");

if($_SESSION['POST']){
    $_POST = $_SESSION['POST'];
    unset($_SESSION['POST']);
}

$savant->assign('patch_row', $_POST);
$savant->assign('dependent_rows', $_POST['dependent_patch']);
$savant->display('updater/patch_create_edit.tmpl.php');
?>
