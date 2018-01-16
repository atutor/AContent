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
* @access    public
* @author    Cindy Qi Li
* @package    DAO
*/

class DAO {

    // private
    static private $db;     // global database connection

    function __construct()
    {

        if (!isset(self::$db))
        {

			if (!defined('DB_NAME') && !isset($_POST['db_name'])) {
                self::$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, null, DB_PORT);
            } else {
                if (isset($_POST['db_name'])) {
                    self::$db = new mysqli($_POST['db_host'],  $_POST['db_login'], $_POST['db_password'], $_POST['db_name'], $_POST['db_port']);
                    define('DB_NAME', $_POST['db_name']);
                } else {
                    self::$db = new mysqli(DB_HOST,  DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
                }
            }
            if (self::$db) {
                self::$db->set_charset("utf8");
            } else {
                die('Unable to connect to db.');
            }

            if (!self::$db->select_db(DB_NAME)) {
                die('DB connection established, but database "'.DB_NAME.'" cannot be selected.');
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

        if ($types !='' && $values !='') {
            $stmt = self::$db->prepare($sql) or die("Failed Prepare:".$sql . "<br />". self::$db->error);
            if (is_array($values)) {
                $inputArray[] = &$types;
                $j = count($values);
                for($i=0;$i<$j;$i++) {
                    $inputArray[] = &$values[$i];
                }

                // This is a more elegant solution using ... than call_user_func_array()
                // but only works with php 5.6+
                //$stmt->bind_param($types, ...$values) or die($sql . "<br />". self::$db->error);

                call_user_func_array(array(&$stmt, 'bind_param'), $inputArray);
                $stmt->execute() or die("Array Execute Failed for: ".$sql . "<br />". self::$db->error);
                if (function_exists('mysqli_fetch_all')) {
                    // Returns an object
                    $result = $stmt->get_result();
                } else {
                    // alternative if mysqlnd is not installed
                    // But, his returns an array, instead of a mysqli object
                    $result = self::get_result($stmt);
                }
                $stmt->close();

            } else {

                $this_type = &$types;
                $this_value = &$values;
                $stmt->bind_param($this_type, $this_value) or die("Bind Failed for: ".$sql . "<br />". self::$db->error);
                $stmt->execute() or die("Single Execute Failed for: ".$sql . "<br />". self::$db->error);

                if (function_exists('mysqli_fetch_all')) {
                    $result = $stmt->get_result();
                } else {
                    // alternative if mysqlnd is not installed
                    $result = self::get_result($stmt);
                }
                $stmt->close();
            }
        } else{
            $result = self::$db->query($sql) or die("Failed MySQLi Query:".$sql . "<br />". self::$db->error);
        }

        // for 'select' SQL, return retrieved rows
        if (strtolower(substr($sql, 0, 6)) == 'select') {

             if (isset($result->num_rows) && $result->num_rows > 0) {
				 // handles the result fetched by $stmt->get_result() when mysqlnd is installed
                for($i = 0; $i < $result->num_rows; $i++)
                {
                    $rows[] = $result->fetch_assoc();
                }
                $result->free_result;
                return $rows;
            } else if (!isset($result->num_rows) && count($result) > 0) {
				// handles the result fetched by self::get_result() when mysqlnd is not installed
                return $result;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    function ac_insert_id() {
        global $db;
        return self::$db->insert_id;
    }
    function my_add_null_slashes( $string ) {
        return self::$db->real_escape_string(stripslashes($string));
    }
    // Used if mysqlnd is not installed
    function get_result( $Statement ) {
        $RESULT = array();
        $Statement->store_result();
        for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
            $Metadata = $Statement->result_metadata();
            $PARAMS = array();
            while ( $Field = $Metadata->fetch_field() ) {
                $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
            }
            call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
            $Statement->fetch();
        }
        return $RESULT;
    }
}
?>
