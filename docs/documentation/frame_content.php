<?php 
/************************************************************************/
/* AContent                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
include(TR_INCLUDE_PATH.'vitals.inc.php');

if (isset($_GET['p'])) {
	$this_page = htmlentities($_GET['p']);
} else {
	$this_page = 'index.php';
} 

require('handbook_header.inc.php'); 

if (isset($_pages[$this_page]['guide'])) 
{
	echo _AT($_pages[$this_page]['guide']);
}

require('handbook_footer.inc.php');
?>