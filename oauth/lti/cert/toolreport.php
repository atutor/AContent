<?php session_start(); ?><html>
<head>
  <title>IMS LTI Producer Certification</title>
  <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<?php 
require_once("official.php");
?>
<p>
<b>IMS LTI Producer Certification Test Report</b>
</p>
<p>
This report is to be filed with IMS as part of an application 
for certification for IMS Learning Tools Interoperability
Producer.
</p>
<p>
<b>Organizational Information</b>
</p>
<p>
Please fill out the following information:
<pre>
Date of test:
Software being tested:
Version:

Producer Used for Testing: 

Vendor Name, Address, Web site, and Phone Number




Primary contact:
E-Mail:
Phone Number:

Person performing the test:
E-Mail:
Phone Number:

</pre>
</p>
<p>
By filling out this form and submitting it, you are agreeing that
these test results are an accurate representation of a properly
executed certification test and that this document is a true
and accurate representation of the results of that test.
</p>
<p>
<b>Individual Tests Notes</b>
</p>
<p>
Please indicate whether your software passed or failed each of the 
tests below.   Please include a short description of 
how your software passed the test.  Also include any anomalies 
in your test results.  For example, if your software did 
not pass a particular test because it does not have a feature 
needed by the test, please
explain below.
<p>
<?php

require_once("../util/lti_util.php");
require_once("cert_util.php");

$idno = 100;
$count = 0;
$good = 0;

// Print out the Table of Contents
foreach($tool_tests as $test => $testinfo ) {
    echo("\n<p><b>\n");
    echo($test);
    if ( array_key_exists('detail', $testinfo) ) {
        echo(" ".$testinfo["detail"]. "\n");
    }
    echo("</b><br/>\n");
    if ( array_key_exists('result', $testinfo) ) {
        echo("Expected Result:\n".$testinfo["result"]. "<br/>\n");
    }
    echo("Actual Result: Pass / Fail</p>\n");
    echo("<p><i>Notes</i>\n</p><p>&nbsp;</p>\n");
}

?>
<p>
<b>Issues with the Certification Test</b>
</p>
<p>
Please note here if you encountered any issues with the certification test
or have any suggestions for improvement.
</p>
<p>&nbsp;</p>
<p>
&copy; 2010 IMS Global Learning Consortium, Inc.</p>
