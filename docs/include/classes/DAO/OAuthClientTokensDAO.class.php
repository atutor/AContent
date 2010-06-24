<?php
/************************************************************************/
/* AContent                                                         */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* DAO for "oauth_client_tokens" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class OAuthClientTokensDAO extends DAO {

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
	public function Create($oauth_server_id, $token, $token_type, $token_secret, $user_id)
	{
		global $addslashes, $msg;

		$missing_fields = array();

		/* token type check */
		if ($token_type <> 'request' && $token_type <> 'access')
		{
			$msg->addError('INVALID_TOKEN_TYPE');
		}

		if (!$msg->containsErrors())
		{
			/* insert into the db */
			$sql = "INSERT INTO ".TABLE_PREFIX."oauth_client_tokens
			              (oauth_server_id,
			               token,
			               token_type,
			               token_secret,
			               user_id,
			               assign_date
			               )
			       VALUES (".$oauth_server_id.",
			               '".$token."',
			               '".$token_type."',
			               '".$token_secret."',
			               ".$user_id.",
			               now()
			              )";

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
	* Delete token row by token, token_type
	* @access  public
	* @param   $token, $token_type
	* @return  true if successful, otherwise, return false
	* @author  Cindy Qi Li
	*/
	function deleteByTokenAndType($token, $token_type)
	{
	    $sql = "DELETE FROM ".TABLE_PREFIX."oauth_client_tokens 
	             WHERE token = '".$token."'
	               AND token_type = '".$token_type."'";
	    return $this->execute($sql);
  	}

	/**
	* Return row by consumer
	* @access  public
	* @param   $oauth_server_id, $token_type
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function get($oauth_server_id, $token_type)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_client_tokens 
	             WHERE oauth_server_id='".$oauth_server_id."'
	               AND token_type='".$token_type."'";
	    return $this->execute($sql);
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
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_client_servers c, ".TABLE_PREFIX."oauth_client_tokens t 
	             WHERE c.oauth_server_id = t.oauth_server_id
	               AND c.consumer_key='".$consumer_key."'
	               AND t.token = '".$token."'";
	    return $this->execute($sql);
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
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_client_tokens 
	             WHERE token = '".$token."'
	               AND token_type = '".$token_type."'";
	    return $this->execute($sql);
  	}

  	/**
	* Return token row by consumer key, token, nounce
	* @access  public
	* @param   $consumer_key, $token, $nounce
	* @return  table rows if successful, otherwise, return false
	* @author  Cindy Qi Li
	*/
	function getByTokenAndNounce($consumer_key, $token, $nonce)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."oauth_client_servers, c".TABLE_PREFIX."oauth_client_tokens t 
	             WHERE c.oauth_server_id = t.oauth_server_id
	               AND c.consumer_key='".$consumer_key."'
	               AND t.token = '".$token."'
	               AND t.nounce = '".$nonce."'";
	    return $this->execute($sql);
  	}

}
?>