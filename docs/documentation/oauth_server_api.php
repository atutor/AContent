<?php
/************************************************************************/
/* AFrame                                                               */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('AF_INCLUDE_PATH', '../include/');

include(AF_INCLUDE_PATH.'vitals.inc.php');
include(AF_INCLUDE_PATH.'header.inc.php');
?>
<div class="output-form" style="line-height:150%">

<h1>OAuth Server API</h1>
<p>AFrame implements the OAuth Core 1.0 specification.</p>
<p>The <a href="http://oauth.net/documentation/getting-started" target="_blank">OAuth</a> protocol enables web services consumers to access protected resources via an API without requiring users to supply the service credentials to the consumers. It's a generic methodology for unobtrusive, wire protocol level authenticated data access over HTTP.</p>

<p>AFrame exposes the following API endpoints:</p>

    <div id="toc">
      <ul>
        <li><a href="<?php echo AF_BASE_HREF.'documentation/oauth_server_api.php'; ?>#register_consumer">Register consumer</a></li>
        <li><a href="<?php echo AF_BASE_HREF.'documentation/oauth_server_api.php'; ?>#request_token">Request token</a></li>
        <li><a href="<?php echo AF_BASE_HREF.'documentation/oauth_server_api.php'; ?>#authorization">Authorization</a></li>
        <li><a href="<?php echo AF_BASE_HREF.'documentation/oauth_server_api.php'; ?>#access_token">Access token</a></li>
      </ul>
    </div>
    
    <p id="skip"></p>

<h2 id="register_consumer">Register consumer</h2>

<h3>Endpoint: </h3><p>http://server-cname/oauth/register_consumer.php</p>
<h3>Parameters</h3><br />

<table class="data" rules="all">
<tbody><tr>
<th>Parameter</th><th>Description</th><th>Default value</th>
</tr>

<tr>
  <th>consumer</th>
  <td>Required. The encoded string of consumer name.</td>
  <td>None</td>
</tr>

<tr>
  <th>expire</th>
  <td>Optional. The seconds that the access token is valid. The access token expires after this number of seconds since it is assigned. When "expire" is set to 0, the access token never expires.</td>
  <td>0</td>
</tr>

</tbody></table>
<br />

<h3>Example</h3><br />
<span style="font-weight: bold">Request</span>
<pre style="background-color:#F7F3ED;"> 
<?php echo AF_BASE_HREF; ?>oauth/register_consumer.php?consumer=http%3A%2F%2Flocalhost%2Faframe%2F&expire=300<br />
</pre>
<p>Goal: Registers consumer http://localhost/aframe/ and requests that the assigned access token expires in 5 minutes.</p>

<span style="font-weight:bold">Success response</span>
<pre style="background-color:#F7F3ED;"> 
consumer_key=8862a51faa12c1b1&consumer_secret=79d591810c803167&expire=300<br />
</pre>
<p>consumer_key and consumer_secret are both 16 characters long. expire_threshold confirms the access token expire duration.</p> 

<span style="font-weight:bold">Fail response</span>
<pre style="background-color:#F7F3ED;"> 
error=Empty+parameter+%22consumer%22.<br />
</pre>
<p>A fail response returns error message.</p> 

<h2 id="register_consumer">Request token</h2>

<h3>Endpoint: </h3><p>http://server-cname/oauth/request_token.php</p>
<h3>Parameters</h3><br />
<p>Both GET or POST method are supported.</p>

<table class="data" rules="all">
<tbody><tr>
<th>Parameter</th><th>Description</th><th>Default value</th>
</tr>

<tr>
  <th>oauth_consumer_key</th>
  <td>Required. The consumer key.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_signature_method</th>
  <td>Required. The signature method the Consumer used to sign the request.</td>
  <td>None. <br /> Or, One of these values: HMAC-SHA1, RSA-SHA1, and PLAINTEXT.</td>
</tr>

<tr>
  <th>oauth_signature</th>
  <td>Required. The signature as defined in <a href="http://oauth.net/core/1.0#signing_process">Signing Requests</a>.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_timestamp</th>
  <td>Required. As defined in <a href="http://oauth.net/core/1.0#nonce">Nonce and Timestamp</a>.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_nonce</th>
  <td>Required. As defined in <a href="http://oauth.net/core/1.0#nonce">Nonce and Timestamp</a>.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_version</th>
  <td>OPTIONAL. If present, value MUST be 1.0.</td>
  <td>1.0</td>
</tr>

</tbody></table>
<br />

<h3>Example</h3><br />
<span style="font-weight: bold">Request</span>
<pre style="background-color:#F7F3ED;"> 
<?php echo AF_BASE_HREF; ?>oauth/request_token.php?oauth_consumer_key=8862a51faa12c1b1&<br />oauth_signature_method=HMAC-SHA1&oauth_signature=tVWpcskRSY34wxhv%2BP9NcgXuuGk%3D&<br />oauth_timestamp=1255524495&oauth_nonce=3e43dd6ce0e09614e79e2a4b53e124c8&oauth_version=1.0<br />
</pre>

<span style="font-weight:bold">Success response</span>
<pre style="background-color:#F7F3ED;"> 
oauth_token=086cbfe90b41a7fdf9&oauth_token_secret=55e2bd8454b2f75a21<br />
</pre>
<p>oauth_token and oauth_token_secret are both 18 characters long.</p> 

<span style="font-weight:bold">Fail response</span>
<pre style="background-color:#F7F3ED;"> 
error=Consumer+is+not+registered.<br />
</pre>
<p>A fail response returns error message.</p> 

<h2 id="authorization">Authorization</h2>

<h3>Endpoint: </h3><p>http://server-cname/oauth/authorization.php</p>
<h3>Parameters</h3><br />

<table class="data" rules="all">
<tbody><tr>
<th>Parameter</th><th>Description</th><th>Default value</th>
</tr>

<tr>
  <th>oauth_token</th>
  <td>Required. The Request Token obtained in the previous step.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_callback</th>
  <td>Optional. The Consumer MAY specify a URL the Service Provider will use to redirect the User 
  back to the Consumer when <a href="http://oauth.net/core/1.0#auth_step2">Obtaining User Authorization</a> 
  is complete. If this parameter was not given or empty, the message "User is authenticated successfully" 
  will be displayed as success response.</td>
  <td>0</td>
</tr>

</tbody></table>
<br />

<h3>Example</h3><br />
<span style="font-weight: bold">Request</span>
<pre style="background-color:#F7F3ED;"> 
<?php echo AF_BASE_HREF; ?>oauth/authorization.php?oauth_token=086cbfe90b41a7fdf9&oauth_callback=<?php echo urlencode(AF_BASE_HREF);?><br />
</pre>

<span style="font-weight:bold">Success response</span>
<p>Redirect the User back to the URL specified in oauth_callback. If oauth_callback is not given or empty, the message 
"User is authenticated successfully" will be displayed.</p> 

<span style="font-weight:bold">Fail response</span>
<pre style="background-color:#F7F3ED;"> 
error=Empty+oauth+token.<br />
</pre>
<p>A fail response returns error message.</p> 

<h2 id="register_consumer">Access token</h2>

<h3>Endpoint: </h3><p>http://server-cname/oauth/access_token.php</p>
<h3>Parameters</h3><br />

<table class="data" rules="all">
<tbody><tr>
<th>Parameter</th><th>Description</th><th>Default value</th>
</tr>

<tr>
  <th>oauth_consumer_key</th>
  <td>Required. The consumer key.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_token</th>
  <td>Required. The Request Token obtained previously.</td>
  <td>None.</td>
</tr>

<tr>
  <th>oauth_signature_method</th>
  <td>Required. The signature method the Consumer used to sign the request.</td>
  <td>None. <br /> Or, One of these values: HMAC-SHA1, RSA-SHA1, and PLAINTEXT.</td>
</tr>

<tr>
  <th>oauth_signature</th>
  <td>Required. The signature as defined in <a href="http://oauth.net/core/1.0#signing_process">Signing Requests</a>.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_timestamp</th>
  <td>Required. As defined in <a href="http://oauth.net/core/1.0#nonce">Nonce and Timestamp</a>.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_nonce</th>
  <td>Required. As defined in <a href="http://oauth.net/core/1.0#nonce">Nonce and Timestamp</a>.</td>
  <td>None</td>
</tr>

<tr>
  <th>oauth_version</th>
  <td>OPTIONAL. If present, value MUST be 1.0.</td>
  <td>1.0</td>
</tr>

</tbody></table>
<br />

<h3>Example</h3><br />
<span style="font-weight: bold">Request</span>
<pre style="background-color:#F7F3ED;"> 
<?php echo AF_BASE_HREF; ?>oauth/access_token.php?oauth_consumer_key=8862a51faa12c1b1&oauth_token=086cbfe90b41a7fdf9&
oauth_signature_method=HMAC-SHA1&oauth_signature=tVWpcskRSY34wxhv%2BP9NcgXuuGk%3D&oauth_timestamp=1255524495&
oauth_nonce=3e43dd6ce0e09614e79e2a4b53e124c8&oauth_version=1.0<br />
</pre>

<span style="font-weight:bold">Success response</span>
<pre style="background-color:#F7F3ED;"> 
oauth_token=086cbfe90b41a7fdf9&oauth_token_secret=55e2bd8454b2f75a21<br />
</pre>
<p>oauth_token and oauth_token_secret are both 18 characters long.</p> 

<span style="font-weight:bold">Fail response</span>
<pre style="background-color:#F7F3ED;"> 
error=Invalid+oauth+request+token.<br />
</pre>
<p>A fail response returns error message.</p> 

<?php include(AF_INCLUDE_PATH.'footer.inc.php'); ?>
