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

define('TR_INCLUDE_PATH', '../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/OAuthServerConsumersDAO.class.php');

if (!isset($_GET['consumer']))
{
	echo "error=".urlencode('Empty parameter: consumer.');
	return;
}
else
{
	$consumer = $_GET['consumer'];
	$expire_threshold = intval($_GET['expire']);
	
	$oAuthServerConsumersDAO = new OAuthServerConsumersDAO();
	
	$consumer_info = $oAuthServerConsumersDAO->getByConsumer($consumer);
	
	if (!is_array($consumer_info))
	{ // new consumer. save consumer and generate consumer key and secret
		$consumer_id = $oAuthServerConsumersDAO->Create($consumer, $expire_threshold);
		$consumer_info = $oAuthServerConsumersDAO->get($consumer_id);
	}
	else 
	{ // existing consumer
		if ($expire_threshold <> $consumer_info[0]['expire_threshold'])
		{
			$oAuthServerConsumersDAO->updateExpireThreshold($consumer, $expire_threshold);
			$consumer_info[0]['expire_threshold'] = $expire_threshold;
		}
		$consumer_info = $consumer_info[0];
	}
	
	$consumer_key = $consumer_info['consumer_key'];
	$consumer_secret = $consumer_info['consumer_secret'];
	
	echo 'consumer_key='.$consumer_key.'&consumer_secret='.$consumer_secret.'&expire='.$expire_threshold;
}
?>