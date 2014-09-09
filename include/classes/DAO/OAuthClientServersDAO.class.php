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
* DAO for "oauth_client_servers" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');

class OAuthClientServersDAO extends DAO {

	/**
	 * Create a new oauth server record
	 * @access  public
	 * @param   server
	 *          consumer key
	 *          consumer secret
	 *          expire threshold
	 * @return  server id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($oauth_server, $consumer_key, $consumer_secret, $expire_threshold)
	{
		global $addslashes, $msg;
		$oauth_server = $addslashes($oauth_server);
		$consumer_key = $addslashes($consumer_key);
		$consumer_secret = $addslashes($consumer_secret);		
		$expire_threshold = intval($expire_threshold);			
			
		$missing_fields = array();

		/* email check */
		$oauth_server = $addslashes(trim($oauth_server));
		$expire_threshold = intval($expire_threshold);

		/* login name check */
		if ($oauth_server == '')
		{
			$missing_fields[] = _AT('oauth_server');
		}

		if ($missing_fields)
		{
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}

		if (!$msg->containsErrors())
		{
			/* insert into the db */
			$sql = "INSERT INTO ".TABLE_PREFIX."oauth_client_servers
			              (oauth_server,
			               consumer_key,
			               consumer_secret,
			               expire_threshold,
			               create_date
			               )
			       VALUES ('".$oauth_server."',
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
	 * update an existing oauth server record
	 * @access  public
	 * @param   server
	 *          consumer key
	 *          consumer secret
	 *          expire threshold
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Update($oauth_server, $consumer_key, $consumer_secret, $expire_threshold)
	{
		global $addslashes, $msg;
		$oauth_server = $addslashes($oauth_server);
		$consumer_key = $addslashes($consumer_key);
		$consumer_secret = $addslashes($consumer_secret);		
		$expire_threshold = intval($expire_threshold);			
			
		$missing_fields = array();

		/* email check */
		$oauth_server = $addslashes(trim($oauth_server));
		$expire_threshold = intval($expire_threshold);

		/* login name check */
		if ($oauth_server == '')
		{
			$missing_fields[] = _AT('oauth_server');
		}

		if ($missing_fields)
		{
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}

		if (!$msg->containsErrors())
		{
			$sql = "UPDATE ".TABLE_PREFIX."oauth_client_servers
			           SET consumer_key = '".$consumer_key."',
			               consumer_secret = '".$consumer_secret."',
			               expire_threshold = ".$expire_threshold."
			         WHERE oauth_server = '".$oauth_server."'";

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
	* Return row by oauth server ID
	* @access  public
	* @param   $oauth_server_id
	* @return  table row
	* @author  Cindy Qi Li
	*/
	function get($oauth_server_id)
	{
		$oauth_server_id = intval($oauth_server_id);	
		
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_client_servers WHERE oauth_server_id='".$oauth_server_id."'";
	    $rows = $this->execute($sql);
	    return $rows[0];
  	}

	/**
	* Return row by oauth server name
	* @access  public
	* @param   $oauth_server
	* @return  table row
	* @author  Cindy Qi Li
	*/
	function getByOauthServer($oauth_server)
	{
		global $addslashes;
		$oauth_server = $addslashes($oauth_server);
		
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_client_servers WHERE oauth_server='".$oauth_server."'";
	    return $this->execute($sql);
  	}

}
?>