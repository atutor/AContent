<?php 
session_start(); 

require "../common/header.php";
require_once("official.php");
?>
<h1>IMS LTI 1.1 LMS Certification Tests</h1>
<p>
This document describes the tests which a Learning Management System (LMS) 
(a.k.a IMS LTI Consumer) must pass for certification.  The certification
is for a particular version of an LMS and the certification must
be re-done for each new release of of an LMS system. 
<p>
There are three types of tests in the certification:
<ul>
<li><b>Normal</b> tests are tests that your code must pass to be certified.</li>
<li><b>Free Pass</b> tests are optional and depend on design choices in the LMS.
Your LMS does not have to pass these tests, but there is an indication when an LMS
does pass these tests.
</li>
</li>
<li><b>Fail Only</b> tests check for mistakes in every one of your launches.  
These tests are marked "Fail" if <i>any</i> of your tests cause a fail consition.
Once you fail a "Fail Only" test, passing it later does not clear the 
"Fail" status.  You must reset and start over using the test setup utility.
</li>
</ul>
</p>
<p>
So to pass the certification, you must pass all the normal tests and 
as many of the "Free Pass" tests as you can without failing any of the 
"Fail Only" tests.
</p>
<?php

require_once("../util/lti_util.php");
require_once("cert_util.php");

// Print out the status
echo('<table><tr><th>Test</th><th>Decription</th><th width="100px">Note</th></tr>'."\n");
foreach($cert_lms_text as $key => $value ) {
    echo('<tr><td>');
    echo($key);
    echo('</td><td>');
    echo($value[0]);
    $extra = trim($value[2]);
    if ( strlen($extra) > 0 ) {
        $check = trim($value[0]);
	if ( strlen($check) > 0 ) {
            if ($check[strlen($check)-1] != '.' ) echo('.  ');
            echo("<br/>");
        }
        echo($extra);
    }
    echo('</td><td>');
    if ( $value[1] == 'doc' ) {
        echo('&nbsp;');
    }
    if ( $value[1] == 'pass' ) {
        echo(' (Free Pass)');
    }
    if ( $value[1] == 'fail' ) {
        echo(' (Fail Only)');
    }
    echo("</td></tr>\n");
}
echo("</table>\n");
include "../common/footer.php";
?>
