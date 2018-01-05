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
* DAO for "themes" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class ThemesDAO extends DAO {

	/**
	* Return all theme' information
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAll()
	{
    $sql = 'SELECT * FROM '.TABLE_PREFIX.'themes ORDER BY dir_name';
    return $this->execute($sql);
  }

	/**
	* Return theme by theme dir name
	* @access  public
	* @param   dirName : theme dir name
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getByID($dirName)
	{

    $sql = "SELECT * FROM ".TABLE_PREFIX."themes WHERE dir_name=?";
    $values = $dirName;
    $types = "s";
    if ($rows = $this->execute($sql,$values,$types))
    	return $rows[0];
  }

	/**
	* Return all default themes
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getDefaultTheme()
	{

    $sql = "SELECT * FROM ".TABLE_PREFIX."themes WHERE status=".TR_STATUS_DEFAULT;
    return $this->execute($sql);
  }

	/**
	* Return all enabled themes
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getEnabledTheme()
	{
    $sql = "SELECT * FROM ".TABLE_PREFIX."themes WHERE status in (".TR_STATUS_ENABLED.", ".TR_STATUS_DEFAULT.")";
    return $this->execute($sql);
  }

}
?>