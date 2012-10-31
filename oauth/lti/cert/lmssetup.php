<?php session_start(); 

require_once("../util/lti_util.php");
require_once('cert_util.php');

if ( $_REQUEST['certaction'] == 'reset' or $_SESSION['testing'] == 'tool') {
    lti_reset_session();
    header("Location: ".curPageURL());
    exit;
}

if ( isset($_REQUEST['oauth_consumer_key']) and
     isset($_REQUEST['oauth_consumer_secret']) ) {
    $official = $_SESSION['ims:official'];
    lti_reset_session();
    if ( strlen($official) > 0 ) $_SESSION['ims:official'] = $official;
    $_SESSION['cert_consumer_key'] = $_REQUEST['oauth_consumer_key'];
    $_SESSION['cert_secret'] = $_REQUEST['oauth_consumer_secret'];
    $_SESSION['software'] = $_REQUEST['software'];
    $_SESSION['version'] = $_REQUEST['version'];
    $_SESSION['testing'] = "lms";
    header("Location: ".curPageURL());
    exit;
}

include "../common/header.php";
require_once("official.php");
?>
<h1>IMS LTI 1.1 Consumer Certification Setup</h1>
<p>
<a href="lmsdetail.php">Test Description</a> | 
<a href="lmsstatus.php">Test Status</a> 
<p>
This screen allows you to configure the LTI 1.1 Testing Environment
or to reset the testing session and start over.
</p>
<form method="post">
<p>
Software Being Tested:
<input type="text" name="software">
<p>
Version:
<input type="text" name="version">
<p>
oauth_consumer_key
<input type="text" name="oauth_consumer_key">
</p>
oauth_consumer_secret
<input type="text" name="oauth_consumer_secret">
</p>
<p>
<input type="submit" value="Set Data in Session">
<input type="submit" value="Clear All" 
          onclick="window.location='lmssetup.php?certaction=reset'; return false;"/>
</p>
</form>
<?php

if ( isset($_SESSION['cert_consumer_key']) ) {
    echo('<p>Software='.$_SESSION['software'].' version='.$_SESSION['version']);
    echo("<br/>\n");
    $curURL = curPageURL();
    $curURL = str_replace("lmssetup","lmscert",$curURL);
    echo("<h2>Launch URL</h2>\n");
    echo("<p>Here is the launch info to use for your URL/Key/Secret placements</p>\n");
    echo("<pre>\n");
    echo($curURL."?x=With%20Space&amp;y=yes\n");
    echo('Key='.$_SESSION['cert_consumer_key']."\n");
    echo('Secret='.$_SESSION['cert_secret']."\n");
    echo("\nIf your LMS supports custom fields, enter these two fields:\n");
    echo("simple_key=custom_simple_value\n");
    echo("Complex!@#$^*(){}[]KEY=Complex!@#$^*(){}[]Value\n");
    echo("</pre>\n");
}

$requests = $_SESSION['requests'];
if ( ! is_array($requests) ) $requests = array();
$passed = $_SESSION['passed'];
if ( ! is_array($passed) ) $passed = array();
$failed = $_SESSION['failed'];
if ( ! is_array($failed) ) $failed = array();
$notes = $_SESSION['notes'];
if ( ! is_array($notes) ) $notes = array();
$errors = $_SESSION['errors'];
if ( ! is_array($errors) ) $errors = array();
// (resource_link_id, user_id, context_id, roles)
$mapping = $_SESSION['mapping'];
if ( ! is_array($mapping) ) $mapping = array();

echo("Requests=");echo(count($requests));
echo(" Tests Passed=");echo(count($passed));
echo(" Tests Failed=");echo(count($failed));echo("</p>\n");

if ( isset($_SESSION['cert_consumer_key']) ) {
?>
<p>If your LMS Supports XML-Paste, you can also use the following XML:
<pre>
&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;cartridge_basiclti_link xmlns="http://www.imsglobal.org/xsd/imslticc_v1p0"
    xmlns:blti = "http://www.imsglobal.org/xsd/imsbasiclti_v1p0"
    xmlns:lticm ="http://www.imsglobal.org/xsd/imslticm_v1p0"
    xmlns:lticp ="http://www.imsglobal.org/xsd/imslticp_v1p0"
    xmlns:xsi = "http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation = "http://www.imsglobal.org/xsd/imslticc_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticc_v1p0.xsd
                          http://www.imsglobal.org/xsd/imsbasiclti_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imsbasiclti_v1p0.xsd
                          http://www.imsglobal.org/xsd/imslticm_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticm_v1p0.xsd
                          http://www.imsglobal.org/xsd/imslticp_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticp_v1p0.xsd"&gt;
    &lt;blti:title&gt;IMS Test&lt;/blti:title&gt;
    &lt;blti:description&gt;IMS Test&lt;/blti:description&gt;
    &lt;blti:custom&gt;
        &lt;lticm:property name="simple_key"&gt;custom_simple_value&lt;/lticm:property&gt;
        &lt;lticm:property name="Complex!@#$^*(){}[]KEY"&gt;Complex!@#$^*(){}[]Value&lt;/lticm:property&gt;
    &lt;/blti:custom&gt;
    &lt;blti:launch_url&gt;http://www.imsglobal.org/developers/alliance/LTI/blti-cert/lmscert.php?x=With%20Space&amp;amp;y=yes
&lt;/blti:launch_url&gt;
    &lt;blti:vendor&gt;
        &lt;lticp:code&gt;IMS&lt;/lticp:code&gt;
        &lt;lticp:name&gt;IMS&lt;/lticp:name&gt;
        &lt;lticp:description&gt;IMS&lt;/lticp:description&gt;
        &lt;lticp:url&gt;http://www.imsglobal.org/&lt;/lticp:url&gt;
        &lt;lticp:contact&gt;
            &lt;lticp:email&gt;test@example.com&lt;/lticp:email&gt;
        &lt;/lticp:contact&gt;
    &lt;/blti:vendor&gt;
&lt;/cartridge_basiclti_link&gt;
</pre>
<?php
}

include "../common/footer.php";
?>
