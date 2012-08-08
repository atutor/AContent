<?php session_start();

unset($_SESSION['cert_gradebook']);

require_once("../util/lti_util.php");
require_once('cert_util.php');

if ( $_REQUEST['certaction'] == 'reset' or $_SESSION['testing'] == 'lms') {
    lti_reset_session();
    header("Location: ".curPageURL());
    exit;
}

if ( isset($_REQUEST['oauth_consumer_key']) and
     isset($_REQUEST['oauth_consumer_secret']) and
     strlen($_REQUEST['oauth_consumer_secret']) >= 1 ) {
    lti_reset_session();
    $_SESSION['cert_consumer_key'] = $_REQUEST['oauth_consumer_key'];
    $_SESSION['cert_secret'] = $_REQUEST['oauth_consumer_secret'];
    $_SESSION['software'] = $_REQUEST['software'];
    $_SESSION['version'] = $_REQUEST['version'];
    $_SESSION['endpoint'] = $_REQUEST['endpoint'];
    $_SESSION['testing'] = "tool";
    header("Location: ".curPageURL());
    exit;
}

include "../common/header.php";
require_once("official.php");
?>
<h1>IMS LTI 1.1 Provider Certification Setup</h1>
<p>
<a href="tooldetail.php">Test Description</a> |
<a href="toolcert.php">Start Test</a>
</p>
<p>
This screen allows you to configure the LTI 1.1 Testing Environment
or to reset the testing session and start over.
</p>
<?php
if ( isset($_REQUEST['oauth_consumer_key']) and
     isset($_REQUEST['oauth_consumer_secret']) and
     strlen($_REQUEST['oauth_consumer_secret']) < 10 ) {
     echo("<p><b>Error: Please enter a secret that is 10 characters or more</b></p>\n");
}
?>
<form method="post" action="toolsetup.php">
<p>
Software Being Tested:
<input type="text" name="software"
value="<?php echo($_SESSION['software']); ?>"
/>
</p>
<p>
Version:
<input type="text" name="version"
value="<?php echo($_SESSION['version']); ?>"
/>
</p>
<p>
End Point:
<input type="text" size="60" name="endpoint"
value="<?php echo($_SESSION['endpoint']); ?>"
/>
</p>
<p>
oauth_consumer_key
<input type="text" name="oauth_consumer_key"
value="<?php echo($_SESSION['cert_consumer_key']); ?>"
/>
</p>
<p>
oauth_consumer_secret
<input type="text" name="oauth_consumer_secret"
value="<?php echo($_SESSION['cert_secret']); ?>"
/>
</p>
<p>
<input type="submit" value="Set Data in Session"/>
<input type="submit" value="Clear All"
          onclick="window.location='toolsetup.php?certaction=reset'; return false;"/>
</p>
</form>
<?php

if ( isset($_SESSION['cert_consumer_key']) ) {
    echo('<p>Software='.$_SESSION['software'].' version='.$_SESSION['version']);
    echo(' key='.$_SESSION['cert_consumer_key'].' secret='.$_SESSION['cert_secret']);
    echo("<br/>\n");
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

echo("<p>Requests=");echo(count($requests));
echo(" Tests Passed=");echo(count($passed));
echo(" Tests Failed=");echo(count($failed));echo("</p>\n");

include "../common/footer.php";
?>
