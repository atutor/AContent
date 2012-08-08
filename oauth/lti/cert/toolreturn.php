<?php session_start();
require_once("../util/lti_util.php");
?>
<html>
<head>
  <title>IMS LTI Tool Return URL</title>
  <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body style="background-color: pink">
<a href="http://www.imsglobal.org" target="_new">
<img src="http://www.imsglobal.org/images/imslogo96dpi-ialsm2.jpg" align="right" border="0"/>
</a>
<h1>Welcome Back to the LTI return_url</h1>
<?php

if ( ! isset($_SESSION['cert_consumer_key']) ) {
    echo("<p>This test environment is not yet configured.\n");
    echo("please run the setup program in the same browser as you\n");
    echo("will run the tests as the tests use the session for configuration\n");
    echo("and results.</p>\n");
    echo('<p><a href="toolsetup.php" target="_new">Test Setup</a></p>'."\n");
    exit;
}

echo("<center>\n");
if ( isset($_SESSION['cert_consumer_key']) ) {
    echo('<p>LTI Certification: '.$_SESSION['software'].' ('.$_SESSION['version'].') ');
    echo('KEY='.$_SESSION['cert_consumer_key']);
    echo("</p>\n");
}
echo("</center>\n");

if ( isset($_GET['lti_errormsg']) ) echo("<p>errormsg:".$_GET['lti_errormsg']."</p>\n");
if ( isset($_GET['lti_errorlog']) ) echo("<p>errorlog:".$_GET['lti_errorlog']."</p>\n");
if ( isset($_GET['lti_msg']) ) echo("<p>msg:".$_GET['lti_msg']."</p>\n");
if ( isset($_GET['lti_log']) ) echo("<p>log:".$_GET['lti_log']."</p>\n");
?>
