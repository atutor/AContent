<?php
/************************************************************************/
/* AContent                                                        									*/
/************************************************************************/
/* Copyright (c) 2010                                                   								*/
/* Inclusive Design Institute   										                */
/*                                                                      							                */
/* This program is free software. You can redistribute it and/or        				        */
/* modify it under the terms of the GNU General Public License          			        */
/* as published by the Free Software Foundation.                         				        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH."../oauth/lib/OAuth.php");
require_once(TR_INCLUDE_PATH.'classes/DAO/OAuthServerConsumersDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/OAuthServerTokensDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');

class MyOAuthServer extends OAuthServer {
  public function get_signature_methods() {
    return $this->signature_methods;
  }
}

class MyOAuthSignatureMethod_RSA_SHA1 extends OAuthSignatureMethod_RSA_SHA1 {
  public function fetch_private_cert(&$request) {
    $cert = <<<EOD
-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALRiMLAh9iimur8V
A7qVvdqxevEuUkW4K+2KdMXmnQbG9Aa7k7eBjK1S+0LYmVjPKlJGNXHDGuy5Fw/d
7rjVJ0BLB+ubPK8iA/Tw3hLQgXMRRGRXXCn8ikfuQfjUS1uZSatdLB81mydBETlJ
hI6GH4twrbDJCR2Bwy/XWXgqgGRzAgMBAAECgYBYWVtleUzavkbrPjy0T5FMou8H
X9u2AC2ry8vD/l7cqedtwMPp9k7TubgNFo+NGvKsl2ynyprOZR1xjQ7WgrgVB+mm
uScOM/5HVceFuGRDhYTCObE+y1kxRloNYXnx3ei1zbeYLPCHdhxRYW7T0qcynNmw
rn05/KO2RLjgQNalsQJBANeA3Q4Nugqy4QBUCEC09SqylT2K9FrrItqL2QKc9v0Z
zO2uwllCbg0dwpVuYPYXYvikNHHg+aCWF+VXsb9rpPsCQQDWR9TT4ORdzoj+Nccn
qkMsDmzt0EfNaAOwHOmVJ2RVBspPcxt5iN4HI7HNeG6U5YsFBb+/GZbgfBT3kpNG
WPTpAkBI+gFhjfJvRw38n3g/+UeAkwMI2TJQS4n8+hid0uus3/zOjDySH3XHCUno
cn1xOJAyZODBo47E+67R4jV1/gzbAkEAklJaspRPXP877NssM5nAZMU0/O/NGCZ+
3jPgDUno6WbJn5cqm8MqWhW1xGkImgRk+fkDBquiq4gPiT898jusgQJAd5Zrr6Q8
AO/0isr/3aa6O6NLQxISLKcPDk2NOccAfS/xOtfOz4sJYM3+Bs4Io9+dZGSDCA54
Lw03eHTNQghS0A==
-----END PRIVATE KEY-----
EOD;
    return $cert;
  }

  public function fetch_public_cert(&$request) {
    $cert = <<<EOD
-----BEGIN CERTIFICATE-----
MIIBpjCCAQ+gAwIBAgIBATANBgkqhkiG9w0BAQUFADAZMRcwFQYDVQQDDA5UZXN0
IFByaW5jaXBhbDAeFw03MDAxMDEwODAwMDBaFw0zODEyMzEwODAwMDBaMBkxFzAV
BgNVBAMMDlRlc3QgUHJpbmNpcGFsMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKB
gQC0YjCwIfYoprq/FQO6lb3asXrxLlJFuCvtinTF5p0GxvQGu5O3gYytUvtC2JlY
zypSRjVxwxrsuRcP3e641SdASwfrmzyvIgP08N4S0IFzEURkV1wp/IpH7kH41Etb
mUmrXSwfNZsnQRE5SYSOhh+LcK2wyQkdgcMv11l4KoBkcwIDAQABMA0GCSqGSIb3
DQEBBQUAA4GBAGZLPEuJ5SiJ2ryq+CmEGOXfvlTtEL2nuGtr9PewxkgnOjZpUy+d
4TvuXJbNQc8f4AMWL/tO9w0Fk80rWKp9ea8/df4qMq5qlFWlx6yOLQxumNOmECKb
WpkUQDIDJEoFUzKMVuJf4KO/FJ345+BNLGgbJ6WujreoM1X/gYfdnJ/J
-----END CERTIFICATE-----
EOD;
    return $cert;
  }
} 

/**
 * OAuth data store
 */
class MyOAuthDataStore extends OAuthDataStore {/*{{{*/
    private $oauthServerConsumersDAO;
    private $oauthServerTokensDAO;
    
    function __construct() {/*{{{*/
        $this->oauthServerConsumersDAO = new OAuthServerConsumersDAO();
        $this->oauthServerTokensDAO = new OAuthServerTokensDAO();
    }

    function lookup_consumer($consumer_key) {/*{{{*/
        $consumer_row = $this->oauthServerConsumersDAO->getByConsumerKey($consumer_key);
        
    	if (is_array($consumer_row)) 
    		return new OAuthConsumer($consumer_key, $consumer_row[0]['consumer_secret'], NULL);
        else
    		return NULL;
    }

    function lookup_token($consumer, $token_type, $token) {/*{{{*/
        if ($token == '') return NULL;
        
    	$token_row = $this->oauthServerTokensDAO->getByToken($consumer->key, $token);
        if ($token_row[0]['token_type'] == $token_type) 
        	return $token_row[0]['token'];
        else 
        	return NULL;
    }

    function lookup_nonce($consumer, $token, $nonce, $timestamp) {/*{{{*/
        if ($nonce == '') return NULL;
        
    	$row_token = $this->oauthServerTokensDAO->getByToken($consumer->key, $token);
        if ($row_token[0]['nonce'] == $nonce) 
        	return $nonce;
        else 
        	return NULL;
    }/*}}}*/

    function lookup_authenticate_request_token($token) {
    	$token_row = $this->oauthServerTokensDAO->getByTokenAndType($token, 'request');
    	if ($token_row[0]['user_id'] > 0) return true;
    	else return false;
    }
    
    function lookup_expire_threshold($consumer) {
    	$consumer_row = $this->oauthServerConsumersDAO->getByConsumerKey($consumer->key);
    	return $consumer_row[0]['expire_threshold'];
    }
    
    function new_request_token($consumer) {/*{{{*/
        $token = Utility::getRandomStr(18);
        $token_secret = Utility::getRandomStr(18);
        
        // save token into db
        // Problem: need $user_id
        $consumer_row = $this->oauthServerConsumersDAO->getByConsumerKey($consumer->key);
        
        $this->oauthServerTokensDAO->Create($consumer_row[0]['consumer_id'], $token, 'request',
             $token_secret, 0);
        $request_token = new OAuthToken($token, $token_secret);
        
        return $request_token;
    }/*}}}*/

    function new_access_token($token, $consumer) {/*{{{*/
        $access_token_key = Utility::getRandomStr(18);
        $access_token_secret = Utility::getRandomStr(18);
            
        $request_token_row = $this->oauthServerTokensDAO->getByTokenAndType($token, 'request');
        
        $this->oauthServerTokensDAO->Create($request_token_row[0]['consumer_id'], $access_token_key, 'access',
             $access_token_secret, $request_token_row[0]['user_id']);
        $this->oauthServerTokensDAO->deleteByTokenAndType($token, 'request');
        
		$access_token = new OAuthToken($access_token_key, $access_token_secret);
        
		return $access_token;
    }/*}}}*/
}/*}}}*/

?>