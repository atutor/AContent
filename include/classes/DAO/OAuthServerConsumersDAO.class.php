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
* DAO for "oauth_server_consumers" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');

class OAuthServerConsumersDAO extends DAO {

	/**
	 * Create a new consumer record
	 * @access  public
	 * @param   consumer
	 * @return  consumer id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($consumer, $expire_threshold)
	{
		global $addslashes, $msg;
		$consumer = $addslashes($consumer);
		$expire_threshold = intval($expire_threshold);
		
		$missing_fields = array();

		/* email check */
		$consumer = $addslashes(trim($consumer));

		/* login name check */
		if ($consumer == '')
		{
			$missing_fields[] = _AT('consumer');
		}

		if ($missing_fields)
		{
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}

		if (!$msg->containsErrors())
		{
			/* insert into the db */
			$consumer_key = Utility::getRandomStr(16);
			$consumer_secret = Utility::getRandomStr(16);
			
			$sql = "INSERT INTO ".TABLE_PREFIX."oauth_server_consumers
			              (consumer,
			               consumer_key,
			               consumer_secret,
			               expire_threshold,
			               create_date
			               )
			       VALUES ('".$consumer."',
			               '".$consumer_key."',
			               '".$consumer_secret."',
			               ".$expire_threshold.",
			               now()
			              )";

			if (!$this->execute($sql))
			{
				$msg->addError('DB_NOT_UPDATED');
				return false;
			}
			else
			{
				//return mysql_insert_id();
				return ac_insert_id();
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update expire threshold
	 * @access  public
	 * @param   consumer, expire threshold
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function updateExpireThreshold($consumer, $expire_threshold)
	{
		global $addslashes, $msg;
		$consumer = $addslashes($consumer);
		$expire_threshold = intval($expire_threshold);
		
		$missing_fields = array();

		/* email check */
		$consumer = $addslashes(trim($consumer));

		/* login name check */
		if ($consumer == '')
		{
			$missing_fields[] = _AT('consumer');
		}

		if ($missing_fields)
		{
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}

		if (!$msg->containsErrors())
		{
			/* update db */
			$sql = "UPDATE ".TABLE_PREFIX."oauth_server_consumers
			           SET expire_threshold = ".$expire_threshold."
			         WHERE consumer = '".$consumer."'";

			if (!$this->execute($sql))
			{
				$msg->addError('DB_NOT_UPDATED');
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	* Return row by consumer ID
	* @access  public
	* @param   $consumer_id
	* @return  table row
	* @author  Cindy Qi Li
	*/
	function get($consumer_id)
	{
		$consumer_id = intval($consumer_id);
		
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_server_consumers WHERE consumer_id='".$consumer_id."'";
	    $rows = $this->execute($sql);
	    return $rows[0];
  	}

	/**
	* Return row by consumer
	* @access  public
	* @param   $consumer
	* @return  table row
	* @author  Cindy Qi Li
	*/
	function getByConsumer($consumer)
	{
	    global $addslashes;
		$consumer = $addslashes($consumer);
		
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_server_consumers WHERE consumer='".$addslashes($consumer)."'";
	    return $this->execute($sql);
  	}

  	/**
	* Return row by consumer key
	* @access  public
	* @param   $consumer_key
	* @return  table row
	* @author  Cindy Qi Li
	*/
	function getByConsumerKey($consumer_key)
	{
		global $addslashes;
		$consumer_key = $addslashes($consumer_key);
	
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_server_consumers 
	             WHERE consumer_key = '".$consumer_key."'";
	    return $this->execute($sql);
  	}

  	/**
	* Return row by consumer key and secret
	* @access  public
	* @param   $consumer_key, $consumer_secret
	* @return  table row
	* @author  Cindy Qi Li
	*/
	function getByConsumerKeyAndSecret($consumer_key, $consumer_secret)
	{
		global $addslashes;
		$consumer_key = $addslashes($consumer_key);
		$consumer_secret = $addslashes($consumer_secret); 	
		
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_server_consumers 
	             WHERE consumer_key = '".$consumer_key."'
	               AND consumer_secret = '".$consumer_secret."'";
	    return $this->execute($sql);
  	}

}
?>