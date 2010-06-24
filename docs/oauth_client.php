<?php
/************************************************************************/
/* AContent                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', 'include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once("oauth/lib/OAuth.php");

require_once(TR_INCLUDE_PATH.'classes/DAO/OAuthClientServersDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/OAuthClientTokensDAO.class.php');

// This part should be moved into include/constants.inc.php
$oauth_server_url = "http://localhost/transformable/";

$register_consumer_url = $oauth_server_url.'oauth/register_consumer.php';
$request_token_url = $oauth_server_url.'oauth/request_token.php';
$authorization_url = $oauth_server_url.'oauth/authorization.php';
$access_token_url = $oauth_server_url.'oauth/access_token.php';

//$client_callback_url = TR_BASE_HREF.'index.php';
$client_callback_url = 'http://www.google.ca';

// initialize oauth client
$oAuthClientServersDAO = new OAuthClientServersDAO();
$oAuthClientTokensDAO = new OAuthClientTokensDAO();

$server_info = $oAuthClientServersDAO->getByOauthServer($oauth_server_url);
$expire_threshold = 0;
$sig_method = new OAuthSignatureMethod_HMAC_SHA1(); // use HMAC signature method as default

// 1. register consumer
$oauth_server_response = file_get_contents($register_consumer_url.'?consumer='.urlencode(TR_BASE_HREF).'&expire='.$expire_threshold);
debug('register consumer - request: '.$register_consumer_url.'?consumer='.urlencode(TR_BASE_HREF).'&expire='.$expire_threshold);
debug('register consumer - OAUTH response'.$oauth_server_response);

// handle OAUTH response on register consumer
foreach (explode('&', $oauth_server_response) as $rtn)
{
	$rtn_pair = explode('=', $rtn);
	
	if ($rtn_pair[0] == 'consumer_key') $consumer_key = $rtn_pair[1];
	if ($rtn_pair[0] == 'consumer_secret') $consumer_secret = $rtn_pair[1];
	if ($rtn_pair[0] == 'expire_threshold') $expire_threshold = $rtn_pair[1];
	if ($rtn_pair[0] == 'error') $error = $rtn_pair[1];
}

if ($error <> '') echo $error;
else
{
	if (!is_array($server_info))
	{ // new oauth server. save server and according consmer key/secret to communicating with this server.  
		$oAuthClientServersDAO->Create($oauth_server_url, $consumer_key, $consumer_secret, $expire_threshold);
	}
	else if ($server_info[0]['expire_threshold'] <> $expire_threshold)
	{
		$oAuthClientServersDAO->Update($oauth_server_url, $consumer_key, $consumer_secret, $expire_threshold);
	}
	else
	{
		$consumer_key = $server_info[0]['consumer_key'];
		$consumer_secret = $server_info[0]['consumer_secret'];
		$expire_threshold = $server_info[0]['expire_threshold'];
	}
}

$consumer = new OAuthConsumer($consumer_key, $consumer_secret, $client_callback_url);

debug('consumer: '.$consumer);
debug('--- END OF REGISTERING CONSUMER ---');

// 2. get request token
$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $request_token_url);
$req_req->sign_request($sig_method, $consumer, NULL);

$oauth_server_response = file_get_contents($req_req);

debug('request token - request: '."\n".$req_req);
debug('request token - response: '."\n".$oauth_server_response);

// handle OAUTH response on request token
$server_info = $oAuthClientServersDAO->getByOauthServer($oauth_server_url);

foreach (explode('&', $oauth_server_response) as $rtn)
{
	$rtn_pair = explode('=', $rtn);
	
	if ($rtn_pair[0] == 'oauth_token') $request_token_key = $rtn_pair[1];
	if ($rtn_pair[0] == 'oauth_token_secret') $request_token_secret = $rtn_pair[1];
	if ($rtn_pair[0] == 'error') $error = $rtn_pair[1];
}

if ($error == '' && strlen($request_token_key) > 0 && strlen($request_token_secret) > 0)
{
	$oAuthClientTokensDAO->Create($server_info[0]['oauth_server_id'], $request_token_key, 'request', $request_token_secret, 0);
}
else
{
	echo $error;
}
//$request_token_key = '67adbd3067564a7ebe';
//$request_token_secret = '8fc6f5eeae0af5d90e';
$request_token = new OAuthToken($request_token_key, $request_token_secret);

debug('--- END OF REQESTING REQUEST TOKEN ---');

//// 3. authorization
//// update oauth_client_tokens.user_id
//$auth_req = $authorization_url.'?oauth_token='.$oauth_token.'&oauth_callback='.urlencode($client_callback_url);
//header('Location: '.$auth_req);

// 4. get access token
$access_req = OAuthRequest::from_consumer_and_token($consumer, $request_token, "GET", $access_token_url);
$access_req->sign_request($sig_method, $consumer, NULL);

$oauth_server_response = file_get_contents($access_req);

debug('access token - request: '."\n".$access_req);
debug('access token - response: '."\n".$oauth_server_response);

// handle OAUTH response on access token
foreach (explode('&', $oauth_server_response) as $rtn)
{
	$rtn_pair = explode('=', $rtn);
	
	if ($rtn_pair[0] == 'oauth_token') $access_token_key = $rtn_pair[1];
	if ($rtn_pair[0] == 'oauth_token_secret') $access_token_secret = $rtn_pair[1];
	if ($rtn_pair[0] == 'error') $error = $rtn_pair[1];
}

if ($error == '' && strlen($access_token_key) > 0 && strlen($access_token_secret) > 0)
{
	$token_info = $oAuthClientTokensDAO->getByTokenAndType($request_token_key, 'request');
	$oAuthClientTokensDAO->Create($token_info[0]['oauth_server_id'], $access_token_key, 'access', $access_token_secret, $token_info[0]['user_id']);
	$oAuthClientTokensDAO->deleteByTokenAndType($request_token_key, 'request');
}
else
{
	echo $error;
}
debug('--- END OF REQESTING ACCESS TOKEN ---');
?>