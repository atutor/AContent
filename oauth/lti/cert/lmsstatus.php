<?php session_start(); 
require_once("../util/lti_util.php");
require_once('cert_util.php');

if ( $_SESSION['testing'] == 'tool') {
    lti_reset_session();
    header("Location: ".curPageURL());
    exit;
}

$final = $_GET['final'] == 'yes';

$curURL = curPageURL();

require "../common/header.php";
require_once("official.php");
?>
<h1>IMS LTI 1.1 Consumer Certification Status</h1>
<?php

require_once("cert_util.php");

echo("<p>\n");
if ( $final ) {
    echo("<b>IMS LTI 1.1 Certification Results Report</b></p>\n");
} else {
    echo('<a href="lmssetup.php">Setup</a>'."\n");
}

if ( isset($_SESSION['cert_consumer_key']) ) {
    if ( ! $final ) {
        if ( $_GET['autorefresh'] == 'yes' ) { 
            echo('<a href="lmsstatus.php">Manual Refresh</a>'."\n");
        } else {
            echo('<a href="lmsstatus.php">Refresh</a>'."\n");
            // echo('<a href="lmsstatus.php?autorefresh=yes">Auto Refresh</a>'."\n");
        }
        echo('<a href="lmsstatus.php?final=yes">Final Report</a>'."\n");
    }
    echo(' Software='.$_SESSION['software'].' version='.$_SESSION['version']);
    if ( $final ) {
        echo("<p>");
    } 
    echo(' key='.$_SESSION['cert_consumer_key']);
    if ( $final ) {
        echo("<p>Test Date: ");
    } else {
        echo("<br/>\n");
    }
    echo(gmDate("Y-m-d\TH:i:s\Z"));
} else {
   echo("<p>This test is not yet configured.</p>\n");
   exit; 
}

load_cert_data();

if ( $final ) {
    echo('<p>Test URL: '.$curURL."</p>\n");
    echo('<table width="70%">');
} else {
    echo('<p>Scroll to the bottom of this page to see the URL, Key, and Secret to use for this test.</p>'."\n");
    echo('<table>');
}
echo('<tr><th width="15%">Test</th><th width="65%">Description</th><th width="20%">Result</th></tr>'."\n");
$idno = 100;
$count = 0;
$good = 0;
foreach($cert_lms_text as $key => $value ) {
    echo('<tr><td>');
    echo($key);
    echo('</td><td>');
    echo($value[0]);
    $extra = $value[2];
    if ( ! $final && strlen($extra) > 0 ) {
        echo(' <span id="m'.$idno.'" style="display: none;">');
        echo($extra);
        echo(' (<a href="#" onclick="');
        echo("document.getElementById('l$idno').style.display = 'inline';");
        echo("document.getElementById('m$idno').style.display = 'none';");
        echo("return false;");
	echo('">Less</a>)');
        echo("</span>\n");
        echo('<span id="l'.$idno.'">');
        echo(' (<a href="#" onclick="');
        echo("document.getElementById('l$idno').style.display = 'none';");
        echo("document.getElementById('m$idno').style.display = 'inline';");
        echo("return false;");
	echo('">More</a>)');
        echo("</span>\n");
        $idno =  $idno + 1;
    }
    $color = 'yellow';
    $status =  'ToDo';
    $count = $count + 1;
    if ( $value[1] == 'doc' ) {
        $color = 'white';
        $status =  '&nbsp;';
        $count = $count - 1;
    }
    if ( $value[1] == 'pass' ) {
        echo(' (Free Pass)');
        $color = 'blue';
        $status =  'OK';
    }
    if ( $value[1] == 'fail' ) {
        echo(' (Fail Only)');
        $color = 'green';
        $status =  'OK';
    }
    if ( isset($passed[$key]) ) {
        $color = 'green';
        $status =  'Passed';
    }
    if ( isset($failed[$key]) ) {
        $color = 'red';
        $status =  'Failed';
    }
    if ( $color == "blue" or $color == "green" ) $good = $good + 1;
    echo('</td><td><span style="background-color: '.$color.'">');
    echo($status);
    echo("</span></td></tr>\n");
}
echo("</table>\n");
echo("<p>Test Count=$count Tests Passed=$good ");
if ($good == $count ) {
   echo(" -- <b>Congratulations - the test is complete</b></p>\n");
}
if ( $final ) exit();
?>
<p>
There are three types of tests:
<ul>
<li><b>Normal</b> tests start out yellow as "ToDo".  As you make launches which meet
the requirements for the tests, these will turn green and become "Passed".</li>
<li><b>Free Pass</b> tests are optional and depend on design choices in the LMS.
These tests start out blue and "OK".  If a launch meets the requirements
of the test, they will become green and "Passed". 
For these tests, Blue/OK is good enough but Green/Passed is even better.
</li>
<li><b>Fail Only</b> tests start out green and "OK".  If you do a launch that makes a 
mistake that these tests are designed to detect, these tests will become 
"Failed" and turn red.  Once you fail a test, passing it later does not clear the 
"Failed" status.  You must reset and start over using the 
<a href="lmssetup.php">Test Setup</a> utility.</li>
</ul>
</p>
<p>
The goal of the test is to have no yellow/ToDo or red/Failed tests.  All the 
tests should be either green/Passed, blue/OK, green/OK and you should try to 
have as many green/Passed tests as possible.
</p>

<?php
/*
echo("<pre>\n");
echo("Passed:\n");
print_r($passed);
echo("\nFailed:\n");
print_r($failed);
echo("</pre>\n");
*/
$curURL = curPageURL();
$curURL = str_replace("lmsstatus","lmscert",$curURL);
?>
<h2>Launch URL</h2>
<p>Here is the launch info to use for your URL/Key/Secret placements</p>
<pre>
<?php
echo($curURL."?x=With%20Space&amp;y=yes\n");
echo('Key='.$_SESSION['cert_consumer_key']."\n");
echo('Secret='.$_SESSION['cert_secret']."\n");
echo("\nIf your LMS supports custom fields, enter these two fields:\n");
echo("simple_key=custom_simple_value\n");
echo("Complex!@#$^*(){}[]KEY=Complex!@#$^*(){}[]Value\n");
?>
</pre>
<!--
<p>If your LMS supports XML Paste, you can use this descriptor for the test</p>
<pre>
&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;basic_lti_link xmlns="http://www.imsglobal.org/services/cc/imsblti_v1p0" 
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;
  &lt;title&gt;A Simple Descriptor&lt;/title&gt;
  &lt;custom&gt;
    &lt;parameter key="simple_key"&gt;custom_simple_value&lt;/parameter&gt;
    &lt;parameter key="Complex!@#$^*(){}[]KEY"&gt;Complex!@#$^*(){}[]Value&lt;/parameter&gt;
  &lt;/custom&gt;
  &lt;launch_url&gt;<?php echo($curURL); ?>?x=With%20Space&amp;amp;y=yes&lt;/launch_url&gt;
&lt;/basic_lti_link&gt;
</pre>
-->
<?php
include "../common/footer.php";
?>
