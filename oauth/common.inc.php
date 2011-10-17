<?php
require_once("lib/OAuth.php");
require_once("classes/MyOAuthServer.class.php");

/*
 * Config Section
 */
//$domain = $_SERVER['HTTP_HOST'];
//$base = "/oauth/example";
//$base_url = "http://$domain$base";

/**
 * Default objects
 */
$oauth_server = new MyOAuthServer(new MyOAuthDataStore());
$hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
$plaintext_method = new OAuthSignatureMethod_PLAINTEXT();
$rsa_method = new MyOAuthSignatureMethod_RSA_SHA1();

$oauth_server->add_signature_method($hmac_method);
$oauth_server->add_signature_method($plaintext_method);
$oauth_server->add_signature_method($rsa_method);

$sig_methods = $oauth_server->get_signature_methods();
?>
