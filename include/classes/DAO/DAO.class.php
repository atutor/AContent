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

/**
* Root data access object
* Each table has a DAO class, all inherits from this class
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

class DAO {

	// private
	static private $db;     // global database connection
	
	function DAO()
	{

		if (!isset(self::$db))
		{

		    if(defined('MYSQLI_ENABLED')){
		    
		        if(!defined('DB_NAME') && !isset($_POST['db_name'])){
                    //self::$db = @mysqli_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
                    self::$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, null, DB_PORT);
                    self::$db->set_charset("utf8");
                }else{
                    if(isset($_POST['db_name'])){
                        self::$db = new mysqli($_POST['db_host'],  $_POST['db_login'], $_POST['db_password'], $_POST['db_name'], $_POST['db_port']);
                        define('DB_NAME', $_POST['db_name']);
                    
                        self::$db->set_charset("utf8");
                    }else{
                        self::$db = new mysqli(DB_HOST,  DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
                        self::$db->set_charset("utf8");                 
                    }
                }
                if (!self::$db) {
                    die('Unable to connect to db.');
                }
                
                if (!self::$db->select_db(DB_NAME)) {
                    die('DB connection established, but database "'.DB_NAME.'" cannot be selected.');
                }		    
		    
		    }else{
		         if(!defined('DB_NAME') && !isset($_POST['db_name'])){
                    self::$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
                } else{
                    if(isset($_POST['db_name'])){
                        self::$db = @mysql_connect($_POST['db_host'].':'. $_POST['db_port'],  $_POST['db_login'], $_POST['db_password'], $_POST['db_name']);
                        define('DB_NAME', $_POST['db_name']);
                    }else{
                        self::$db = @mysql_connect(DB_HOST.':'.DB_PORT,  DB_USER, DB_PASSWORD, DB_NAME);               
                    }
                
                }
                
                
                
                if (!self::$db) {
                    die('Unable to connect to db.');
                }
                if (!@mysql_select_db(DB_NAME, self::$db)) {
                    die('DB connection established, but database "'.DB_NAME.'" cannot be selected.');
                }
			}
		}
	}
	
	/**
	* Execute SQL
	* @access  protected
	* @param   $sql : SQL statment to be executed
	* @return  $rows: for 'select' sql, return retrived rows, 
	*          true:  for non-select sql
	*          false: if fail
	* @author  Cindy Qi Li
	*/
	function execute($sql)
	{
		$sql = trim($sql);
		
		 if(defined('MYSQLI_ENABLED')){
		    $result = self::$db->query($sql) or die($sql . "<br />". self::$db->error);
		 }else{
		    $result = mysql_query($sql, self::$db) or die($sql . "<br />". mysql_error());
		 }

		// for 'select' SQL, return retrieved rows
		if (strtolower(substr($sql, 0, 6)) == 'select'){
		    if(defined('MYSQLI_ENABLED')){
		         if ($result->num_rows > 0) {
		            for($i = 0; $i < $result->num_rows; $i++) 
                    {
                        $rows[] = $result->fetch_assoc();
                    }
                    $result->free;
                    return $rows;
		         }else{
		            return false;
		         }
		    
		    }else{
                if (mysql_num_rows($result) > 0) {
                    for($i = 0; $i < mysql_num_rows($result); $i++) 
                    {
                        $rows[] = mysql_fetch_assoc($result);
                    }
                    mysql_free_result($result);
                    return $rows;
                } else {
                    return false;
                }
			}
		} else {
			return true;
		}
	}
    function ac_insert_id(){
        //global $db;
        if(defined('MYSQLI_ENABLED')){
            return self::$db->insert_id;
        }else{
            return mysql_insert_id(self::$db);
        }
    }
}
?>