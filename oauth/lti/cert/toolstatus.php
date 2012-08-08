<?php session_start();

require_once("../util/lti_util.php");
require_once('cert_util.php');

if ( $_SESSION['testing'] == 'lms') {
    lti_reset_session();
    header("Location: ".curPageURL());
    exit;
}

include "../common/header.php";
require_once("official.php");
?>
<h1>IMS LTI 1.1 Provider Certification Setup</h1>
<p>
<a href="tooldetail.php">Test Description</a> |
<a href="toolstatus.php">Test Status</a>
<p>
<?php

require_once("cert_util.php");

load_cert_data();

if ( ! isset($_SESSION['cert_consumer_key']) ) {
    echo("<p>This test environment is not yet configured.\n");
    echo("please run the setup program in the same browser as you\n");
    echo("will run the tests as the tests use the session for configuration\n");
    echo("and results.</p>\n");
    echo('<p><a href="toolsetup.php">Test Setup</a></p>'."\n");
    exit;
} else {
    echo('<p>IMS LTI Tool Certification: ');
    echo($_SESSION['software'].' ('.$_SESSION['version'].') ');
    echo(' KEY='.$_SESSION['cert_consumer_key']);
    echo(' (<a href="toolsetup.php">Setup</a>'."\n");
    echo(' | <a href="toolcert.php">Go To Test</a>)'."\n");
    echo("<br/>\n");
    echo(gmDate("Y-m-d\TH:i:s\Z").' ');
    echo("</p>\n");
}
        
// Print out the status
echo('<table><tr><th>Test</th><th>Decription</th><th>Result</th></tr>'."\n");
$idno = 100;
$count = 0;
$good = 0;

foreach($tool_tests as $test => $testinfo ) {
    echo('<tr><td>');
    echo($test);
    echo('</td><td>');
    echo($testinfo["doc"]."\n");
    $extra = "";
    if ( array_key_exists('detail', $testinfo) ) {
        $extra = $extra . "<hr/>Detail:\n".$testinfo["detail"]. "\n";
    }
    if ( array_key_exists('result', $testinfo) ) {
        $extra = $extra . "<hr/>Expected Result:\n".$testinfo["result"]. "\n";
    }
    if ( strlen($extra) > 0 ) {
        echo(' <span id="m'.$idno.'" style="display: none;">');
        echo($extra);
        echo(' (<a href="javascript:');
        echo("document.getElementById('l$idno').style.display = 'inline';");
        echo("document.getElementById('m$idno').style.display = 'none';");
        echo('">Less</a>)');
        echo("</span>\n");
        echo('<span id="l'.$idno.'">');
        echo(' (<a href="javascript:');
        echo("document.getElementById('l$idno').style.display = 'none';");
        echo("document.getElementById('m$idno').style.display = 'inline';");
        echo('">More</a>)');
        echo("</span>\n");
        $idno =  $idno + 1;
    }
    $color="yellow";
    $status ="ToDo";
    if ( $color == "blue" or $color == "green" ) $good = $good + 1;
    $count = $count + 1;
    if ( array_key_exists($test, $passed) ) {
        $status = 'Passed';
        $color = 'Green';
        $good = $good + 1;
    }
    echo('</td><td><span style="background-color: '.$color.'">');
    echo($status);
    echo("</span></td></tr>\n");
}
echo("</table>\n");
echo("<p>Test Count=$count Tests Passed=$good ");
if ($good == $count ) {
   echo(" -- <b>Congratulations - the test is complete</b></p>\n");
}

include "../common/footer.php";

?>
