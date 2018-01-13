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
* DAO for "oauth_server_tokens" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class OAuthServerTokensDAO extends DAO {

	/**
	 * Create a new token
	 * @access  public
	 * @param   token type
	 *          token
	 *          token secret
	 * @return  token id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($consumer_id, $token, $token_type, $token_secret, $user_id)
	{
		global$msg;			
			
		$missing_fields = array();

		/* token type check */
		if ($token_type <> 'request' && $token_type <> 'access')
		{
			$msg->addError('INVALID_TOKEN_TYPE');
		}

		if (!$msg->containsErrors())
		{
			/* insert into the db */

			$sql = "INSERT INTO ".TABLE_PREFIX."oauth_server_tokens
			              (consumer_id,
			               token,
			               token_type,
			               token_secret,
			               user_id,
			               assign_date
			               )
			       VALUES (?,?,?,?,?, now())";
			$values = array($consumer_id, 
                            $token, 
                            $token_type, 
                            $token_secret, 
                            $user_id);
			$types = "isssi";
			
			if (!$this->execute($sql, $values, $types))
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
	* Update user_id by token
	* @access  public
	* @param   $token, $user_id
	* @return  true if successful, otherwise, return false
	* @author  Cindy Qi Li
	*/
	function updateUserIDByToken($token, $user_id)
	{
	    $sql = "UPDATE ".TABLE_PREFIX."oauth_server_tokens 
	               SET user_id = ?
	             WHERE token = ?";
	    $values = array($user_id, $token);
	    $types = "is";
	    return $this->execute($sql, $values, $types);
  	}

	/**
	* Delete token row by token, token_type
	* @access  public
	* @param   $token, $token_type
	* @return  true if successful, otherwise, return false
	* @author  Cindy Qi Li
	*/
	function deleteByTokenAndType($token, $token_type)
	{
	    $sql = "DELETE FROM ".TABLE_PREFIX."oauth_server_tokens 
	             WHERE token = ?
	               AND token_type = ?";
	    $values = array($token,$token_type);
	    $types = "ss";
	    return $this->execute($sql, $values, $types);
  	}

	/**
	* Return row by consumer
	* @access  public
	* @param   $consumer_id, $token_type
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function get($consumer_id, $token_type)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_server_tokens 
	             WHERE consumer_id=?
	               AND token_type=?";
	    $values = array($consumer_id, $token_type);
	    $types = "is";
	    return $this->execute($sql, $values, $types);
  	}

	/**
	* Return token row by consumer key, token type, token
	* @access  public
	* @param   $consumer_key, $token_type, $token
	* @return  table rows if successful, otherwise, return false
	* @author  Cindy Qi Li
	*/
	function getByToken($consumer_key, $token)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_server_consumers c, ".TABLE_PREFIX."oauth_server_tokens t 
	             WHERE c.consumer_id = t.consumer_id
	               AND c.consumer_key=?
	               AND t.token = ?";
	    $values = array($consumer_key, $token);
	    $types = "ss";
	    return $this->execute($sql, $values, $types);
  	}

  	/**
	* Return token row by token, token_type
	* @access  public
	* @param   $token, $token_type
	* @return  table rows if successful, otherwise, return false
	* @author  Cindy Qi Li
	*/
	function getByTokenAndType($token, $token_type)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_server_tokens 
	             WHERE token = ?
	               AND token_type = ?";
	    $values = array($token, $token_type);
	    $types = "ss";
	    return $this->execute($sql, $values, $types);
  	}


  	/**
	* Check whether the given token is expired. If expired, return true, otherwise, return false.
	* @access  public
	* @param   $token
	* @return  true if expired, otherwise, return false
	* @author  Cindy Qi Li
	*/
	function isTokenExpired($token)
	{

		$sql = "SELECT unix_timestamp(now()) AS 'now_timestamp', 
		               osc.expire_threshold,
		               unix_timestamp(addtime(ost.assign_date, sec_to_time(osc.expire_threshold))) AS 'expire_timestamp'
		          FROM ".TABLE_PREFIX."oauth_server_consumers osc, ".TABLE_PREFIX."oauth_server_tokens ost
		         WHERE osc.consumer_id=ost.consumer_id
		           AND ost.token=?
		           AND ost.token_type='access'
		         ORDER BY ost.assign_date DESC";
		         
		$values = $token;
		$types = "s";
		$row = $this->execute($sql, $values, $types);

		if ((!is_array($row[0]) || $row[0]['now_timestamp'] > $row[0]['expire_timestamp']) && $row[0]['expire_threshold'] != 0) {
			return true;
		} else {
			return false;
		}
  	}
}
?>