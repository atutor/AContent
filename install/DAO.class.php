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
	
	// Possible solution to PHP Deprecated:  Methods with the same name as their class...
	//public function __construct($host, $queryPort)
    function __construct()
	{

		if (!isset(self::$db))
		{
            if(defined('DB_NAME')){
                self::$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, null, DB_PORT)
                or die("Connect DB_NAME Failed for: ".$sql . "<br />". self::$db->error);
                self::$db->set_charset("utf8");
            } else if(isset($_POST['step1']['old_path'])) {
                    self::$db = new mysqli($_POST['step1']['db_host'], $_POST['step1']['db_login'], $_POST['step1']['db_password'], $_POST['step1']['db_name'], $_POST['step1']['db_port'])
                    or die("Upgrade Step1 Failed for: ".$sql . "<br />". self::$db->error);
                    self::$db->set_charset("utf8");	
            }else if(isset($_POST['step1'])) {
                    self::$db = new mysqli($_POST['step1']['db_host'], $_POST['step1']['db_login'], $_POST['step1']['db_password'], $_POST['step1']['db_name'], $_POST['step1']['db_port'])
                    or die("Upgrade Final Step Failed for: ".$sql . "<br />". self::$db->error);
                    self::$db->set_charset("utf8");	
            }else if (isset($_POST['step2']['db_name'])) {
                    self::$db = new mysqli($_POST['step2']['db_host'],  $_POST['step2']['db_login'], $_POST['step2']['db_password'], $_POST['step2']['db_name'], $_POST['step2']['db_port']) 
                    or die("Connect step 2 Failed for: ".$sql . "<br />". self::$db->error);
                    self::$db->set_charset("utf8");  
            }else if (isset($_POST['db_name']) && !isset($_POST['create'])) {
                    self::$db = new mysqli($_POST['db_host'],  $_POST['db_login'], $_POST['db_password'], $_POST['db_name'], $_POST['db_port'])
                    or die("Connect Failed for: ".$sql . "<br />". self::$db->error);
                    self::$db->set_charset("utf8");
            } else if (isset($_POST['create']))  {
                    global $sql;
                    self::$db = new mysqli($_POST['db_host'],  $_POST['db_login'], $_POST['db_password'], null, $_POST['db_port'])
                    or die("Create Failed for: ".$sql . "<br />". self::$db->error);
                    self::$db->set_charset("utf8");
                    //self::$db->query($sql);                    
            }
            if (!self::$db) {
                die('Unable to connect to db.');
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
	function execute($sql, &$values='', &$types='')
	{
		$sql = trim($sql);

		if($types !='' && $values !=''){
		    $stmt = self::$db->prepare($sql) or die("Failed Prepare:".$sql . "<br />". self::$db->error);
				if(is_array($values)){
				    $inputArray[] = &$types;
                    $j = count($values);
                    for($i=0;$i<$j;$i++){
                        $inputArray[] = &$values[$i];
                    }
                    
				    // This is a more elegant solution using ... than call_user_func_array()
				    // but only works with php 5.6+
		            //$stmt->bind_param($types, ...$values) or die($sql . "<br />". self::$db->error);

		            call_user_func_array(array(&$stmt, 'bind_param'), $inputArray);
		            $stmt->execute() or die("Array Execute Failed for: ".$sql . "<br />". self::$db->error);
                    $result = $stmt->get_result();
                    $stmt->close();
                    
		        }else{
		        
		            $this_type = &$types;
		            $this_value = &$values;
		            $stmt->bind_param($this_type, $this_value) or die("Bind Failed for: ".$sql . "<br />". self::$db->error);
		            $stmt->execute() or die("Single Execute Failed for: ".$sql . "<br />". self::$db->error);
		            $result = $stmt->get_result();
		            $stmt->close();
		        }  
        } else{
		        $result = self::$db->query($sql) or die("Failed MySQLi Query:".$sql . "<br />". self::$db->error);
		}

		// for 'select' SQL, return retrieved rows
		if (strtolower(substr($sql, 0, 6)) == 'select'){

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
		    
		} else {
			return true;
		}
	}
    function create($sql, $db_name = ''){
       self::execute($sql) or die("Failed CREATE Query:".$sql . "<br />". self::$db->error);
        return true;
    }
    function select($selected){
            //return self::$db->server_version();
           return self::$db->select_db($selected);
    }
    function version(){
        $result = self::$db->query("SELECT version() as version");
        $row = $result->fetch_assoc();
        return $row['version'];
    }
    function ac_insert_id(){
            return self::$db->insert_id;
    }
    function my_add_null_slashes( $string ) {
            return self::$db->real_escape_string(stripslashes($string));
    }

}
?>