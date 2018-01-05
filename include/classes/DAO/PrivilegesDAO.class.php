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
* DAO for "themes" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class PrivilegesDAO extends DAO {

	/**
	* Return privileges that are open to public
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAll()
	{
		$sql = 'SELECT *
				FROM '.TABLE_PREFIX.'privileges p
				ORDER BY privilege_id';

		return $this->execute($sql);
  	}

	/**
	* Return privileges that are open to public
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getPublicPrivileges()
	{
		$sql = 'SELECT *
						FROM '.TABLE_PREFIX.'privileges p
						WHERE open_to_public = 1
						ORDER BY p.menu_sequence';

		return $this->execute($sql);
  	}

  	/**
	* Return privileges of the given user
	* @access  public
	* @param   $userID
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getUserPrivileges($userID)
	{

		$sql = 'SELECT *
				FROM '.TABLE_PREFIX.'users u, '.TABLE_PREFIX.'user_groups ug, '.TABLE_PREFIX.'user_group_privilege ugp, '.TABLE_PREFIX.'privileges p
				WHERE u.user_id = ?
				AND u.user_group_id = ug.user_group_id
				AND ug.user_group_id = ugp.user_group_id
				AND ugp.privilege_id = p.privilege_id
				ORDER BY p.menu_sequence';  
		$values = $userID;
		$types = "i";
	    return $this->execute($sql,$values,$types);
	    
	  }

	/**
	* Return privileges of the given user group
	* @access  public
	* @param   $userGroupID
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getUserGroupPrivileges($userGroupID)
	{

		$sql = 'SELECT *, ug.description user_group_desc, p.description privilege_desc
				FROM '.TABLE_PREFIX.'user_groups ug, '.TABLE_PREFIX.'user_group_privilege ugp, '.TABLE_PREFIX.'privileges p
				WHERE ug.user_group_id = ?
				AND ug.user_group_id = ugp.user_group_id
				AND ugp.privilege_id = p.privilege_id
				ORDER BY p.menu_sequence';
	    $values = $userGroupID;    
	    $types = "i";
		return $this->execute($sql, $values,$types);

	}

	/**
	* Return all privileges except the privilege ids in given string  
	* @access  public
	* @param   $privilegeIDs : a string of check ids separated by comma. for example: 1, 2, 3
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAllPrivsExceptListed($privilegeIDs)
	{
		$privilegeIDs = intval($privilegeIDs);
		
		if (trim($privilegeIDs) == '')
			return $this->getAll();
		else
		{
	
		    $sql = "SELECT * FROM ". TABLE_PREFIX ."privileges 
			         WHERE privilege_id NOT IN (?)";
			         
			$values = $privilegeIDs;
			$types = "s";
			return $this->execute($sql,$values,$types);
		}
	}
	
}
?>