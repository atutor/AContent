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

/**
* DAO for "config" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class ConfigDAO extends DAO {

	/**
	* Insert a new config row
	* @access  public
	* @param   name, value
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function Create($name, $value)
	{
	     $sql = "INSERT INTO ".TABLE_PREFIX."config (name, value)
	            VALUES (?, ?)";
	    $values = array($name, $value);
	    $types = "ss";
	    return $this->execute($sql,$values,$types);
	}
	
	/**
	* Update a config row
	* @access  public
	* @param   name, value
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function Replace($name, $value)
	{

	    $sql = "REPLACE INTO ".TABLE_PREFIX."config 
	             VALUES (?, ?)";
	    $values = array($name, $value);
	    $types = "ss";
	    return $this->execute($sql, $values,$types);
	}
	
	/**
	* Delete a config row
	* @access  public
	* @param   name
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function Delete($name)
	{

	   $sql = "DELETE FROM ".TABLE_PREFIX."config 
	             WHERE name = ?"; 
	    $values =  $name; 
	    $types="s";
	    return $this->execute($sql,$values,$types);
	}
	
	/**
	* Return all config' information
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAll()
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."config ORDER BY name";
	    return $this->execute($sql);
	}

	/**
	* Return a config row by name
	* @access  public
	* @param   name
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function get($name)
	{
		
	    //$sql = "SELECT * FROM ".TABLE_PREFIX."config WHERE name = '".$name."'";
	    $sql = "SELECT * FROM ".TABLE_PREFIX."config WHERE name = '".$name."'";
	    $values = $names;
	    $types = "s";
	    $rows = $this->execute($sql, $values, $types);
	    return $rows[0];
	}
}
?>