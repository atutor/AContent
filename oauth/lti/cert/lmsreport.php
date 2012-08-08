<?php session_start(); ?><html>
<head>
  <title>IMS LTI 1.1 Consumer Certification</title>
  <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<p>
<b>IMS LTI 1.1 Consumer Certification Test Report</b>
</p>
<?
require_once("official.php");
?>
<p>
This report is to be filed with IMS as part of an application 
for certification for IMS Learning Tools Interoperability
1.1 Consumer.
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

Consumer Used for Testing: 

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
<b>Table of Test Results</b>
</p>
<p>
Replace this text with the data from the test status at the end
of the test.   You can copy and paste the contents of the 
"Final Report" document directly into this section, replacing this text.
You can include screen  shots as well if needed.
</p>
<p>
<b>Individual Tests Notes</b>
</p>
<p>
Please include any notes and/or anomalies in your test results.
For example, if your software did not pass a particular test 
because it does not have a feature neede by the test, please
explain below.
<p>
<?php

require_once("../util/lti_util.php");
require_once("cert_util.php");

// Print out the Table of Contents
foreach($cert_lms_text as $key => $value ) {
    if ( $value[1] == 'doc' ) {
       echo("\n<p><b>\n");
    } else {
       echo("\n<p><i>\n");
    }
    echo($key);
    echo(" ");
    echo($value[0]);

    if ( $value[1] == 'pass' ) {
        echo(' (Free Pass)');
    }
    if ( $value[1] == 'fail' ) {
        echo(' (Fail Only)');
    }
    if ( $value[1] == 'doc' ) {
       echo("\n</b></p>\n");
    } else {
       echo("\n</i></p>\n");
       echo("<p>&nbsp;</p>\n");
    }
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
