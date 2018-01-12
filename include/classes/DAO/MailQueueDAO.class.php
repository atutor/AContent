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
* DAO for "mail_queue" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class MailQueueDAO extends DAO {

	/**
	* Create a record
	* @access  public
	* @param   infos
	* @return  mail_queue_id: if success
	*          false: if unsuccess
	* @author  Cindy Qi Li
	*/
	function Create($to_email, $to_name, $from_email, $from_name, $subject, $body, $charset)
	{

		$sql = "INSERT INTO ".TABLE_PREFIX."mail_queue 
						VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";
		$values = array($to_email, $to_name, $from_email, $from_name, $charset, $subject, $body);
		$types = "sssssss";
		if ($this->execute($sql, $values, $types))
		{
			return ac_insert_id($this->db);
		}
		else
		{
			return false;			
		}
	}

	/**
	* Return all records
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function GetAll()
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."mail_queue"; 
		
		return $this->execute($sql);
	}

	/**
	* Delete a record by mail ids
	* @access  public
	* @param   $mids : mail IDs, for example: "1, 2, 3"
	* @return  true: if successful
	*          false: if unsuccessful
	* @author  Cindy Qi Li
	*/
	function DeleteByIDs($mids)
	{
	    $num_of_ids = count($mids);
		$sql = 'DELETE FROM '.TABLE_PREFIX.'mail_queue WHERE mail_id IN ('.substr(str_repeat("? , ", $num_of_ids), 0, -2).')';
        $types = str_pad("", $num_of_ids, "i");
		return $this->execute($sql, $mids, $types);

	}

}
?>