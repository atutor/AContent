<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2014              								*/
/* ATutorSpaces Inc.                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

function ac_insert_id(){
    global $db;
    if(defined('MYSQLI_ENABLED')){
        return $db->insert_id;
    }else{
        return mysql_insert_id($db);
    }
}

function at_db_errno(){
    global $db;
    if(defined('MYSQLI_ENABLED')){    
        return $db->errno;
    }else{
        return mysql_errno($db);
    }
}
function at_db_error(){
    global $db;
    if(defined('MYSQLI_ENABLED')){    
        return $db->error; 
    }else{
        return mysql_error($db);
    }
}

function at_get_db_info(){
    global $db;
    if(defined('MYSQLI_ENABLED')){    
        return $db->get_client_info; 
    }else{
        return mysql_get_client_info($db);
    }
}

// Detect the mysql version from the command line
function getMySQLVersion() { 
  $output = shell_exec('mysql -V'); 
  preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
  return $version[0]; 
}
/*
function at_db_select($db_name, $db){
 if(defined('MYSQLI_ENABLED')){
    if(!$db->select_db($db_name)){
        require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
        $err = new ErrorHandler();
        //trigger_error('VITAL#DB connection established, but database "'.$db_name.'" cannot be selected.',
        //                E_USER_ERROR);
        //exit;
    }

 }else{
    if (!@mysql_select_db($db_name, $db)) {
        require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
        $err = new ErrorHandler();
        //trigger_error('VITAL#DB connection established, but database "'.$db_name.'" cannot be selected.',
        //                E_USER_ERROR);
        //exit;
    }
 }

}*/