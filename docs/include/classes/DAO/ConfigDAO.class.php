<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
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
	            VALUES ('".$name."', '".$value."')";
	    return $this->execute($sql);
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
	             VALUES ('".$name."', '".$value."')";
	    return $this->execute($sql);
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
	             WHERE name = '".$name."'";
	    return $this->execute($sql);
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
	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'config ORDER BY name';
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
	    $sql = "SELECT * FROM ".TABLE_PREFIX."config WHERE name = '".$name."'";
	    $rows = $this->execute($sql);
	    return $rows[0];
	}
}
?>