<?php
define('AF_INCLUDE_PATH', '../include/');
require_once(AF_INCLUDE_PATH.'vitals.inc.php');
require_once("common.inc.php");

try {
	$req = OAuthRequest::from_request();
	$token = $oauth_server->fetch_access_token($req);
	print $token;
} catch (OAuthException $e) {
	print($e->getMessage() . "\n<hr />\n");
	print_r($req);
	die();
}

?>
