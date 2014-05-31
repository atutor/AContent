<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
include(TR_INCLUDE_PATH.'classes/DAO/UserGroupsDAO.class.php');
$dao = new DAO();
// make sure the user has author privilege
Utility::authenticate(TR_PRIV_ISAUTHOR);

// get a list of authors if admin is creating a lesson	

require(TR_INCLUDE_PATH.'header.inc.php');
$savant->display('home/create_author_user_group.tmpl.php');
require(TR_INCLUDE_PATH.'footer.inc.php');
?>