<?php session_start();

require_once("../util/lti_util.php");
require_once("cert_util.php");

if ( $_SESSION['testing'] == 'lms') {
    lti_reset_session();
    header("Location: ".curPageURL());
    exit;
}

if ( isset($_GET['passed']) ) {
    $passid = $_GET['passed'];
    mark_pass($passid,"");
    update_cert_data();
    $url = curPageURL()."?testshow=".$passid;
    if ( isset($_GET['debug']) ) $url = $url . '&debug=yes';
    header("Location: $url");
    exit;
}

if ( ! isset($_SESSION['cert_consumer_key']) ) {
    include "../common/header.php";
    echo("<p>This test environment is not yet configured.\n");
    echo("please run the setup program in the same browser as you\n");
    echo("will run the tests as the tests use the session for configuration\n");
    echo("and results.</p>\n");
    echo('<p><a href="toolsetup.php">Test Setup</a></p>'."\n");
    exit;
}

$key = $_SESSION['cert_consumer_key'];
$secret = $_SESSION['cert_secret'];
$b64 = base64_encode($key.":::".$secret);

// We are inside of the iFrame.
$testid = $_GET['testid'];
if ( isset($testid) and isset($tool_tests[$testid]) ) {
   $test = $tool_tests[$testid];
   $parms = $test["parms"];
   $url = curPageURL();
   $url = str_replace ( "toolcert.php" , "toolreturn.php" , $url);
   $parms['launch_presentation_return_url'] = $url;
   $parms['tool_consumer_info_product_family_code'] = 'imsglc';
   $parms['tool_consumer_info_version'] = '1.1';
   $endpoint = $_SESSION['endpoint'];

   if ( isset($parms['lis_outcome_service_url']) ) {
      $serviceurl = curPageURL();
      $serviceurl = str_replace ( "cert/toolcert.php" , "common/tool_consumer_outcome.php" , $serviceurl);
      $serviceurl .= "?b64=" . htmlentities($b64);
      $parms['lis_outcome_service_url'] = $serviceurl;
      $sourcedid = $parms['context_id'] . ':::' . $parms['resource_link_id'] . ':::' . 
                   $parms['user_id'] ;
      $parms['lis_result_sourcedid'] = $sourcedid;
  }


   $dodebug = false;
   if ( isset($_GET['debug']) ) $dodebug = true;
   $tool_consumer_instance_guid = "IMS Testing";
   $tool_consumer_instance_description = "IMS Testing Description";
   $parms = signParameters($parms, $endpoint, "POST", $key, $secret, "Press to Launch", $tool_consumer_instance_guid, $tool_consumer_instance_description);

   $content = postLaunchHTML($parms, $endpoint, $dodebug);
   print($content);
   exit;
}

include "../common/header.php";

require_once("official.php");

load_cert_data();

echo('<center style="font-size: 14px">'."\n");
if ( isset($_SESSION['cert_consumer_key']) ) {
    echo('<p>LTI 1.1 Certification: '.$_SESSION['software'].' ('.$_SESSION['version'].') ');
    echo('KEY='.$_SESSION['cert_consumer_key']);
    echo(' <a href="toolsetup.php">Setup</a>'."\n");
    if ( isset($b64) ) {
        echo(' <a href="toolgradebook.php?b64='.htmlentities($b64).'" target="_new">View Gradebook</a>'."\n");
    }
}
        
$testshow = $_GET['testshow'];
if ( ! isset($testshow) ) $testshow = '001';
if ( ! array_key_exists($testshow, $tool_tests) ) $testshow = '001';
$thistest = $tool_tests[$testshow];

$found = false;
foreach($tool_tests as $test => $testinfo ) {
    if ( $found ) {
        $next = $test;
        break;
    }
    if ( $test == $testshow ) {
        $found = true;
        if ( isset($last) ) $prev = $last;
    }
    $last = $test;
}

echo('<hr/>');
if ( isset($prev) ) {
    $url = 'toolcert.php?testshow='.$prev;
    if ( isset($_GET['debug']) ) $url = $url . '&debug=yes';
    echo('<a href="'.$url.'">&lt;&lt; '.$prev.'</a> | ');
}
echo($testshow.': '.$thistest['doc']);
if ( isset($next) ) {
    $url = 'toolcert.php?testshow='.$next;
    if ( isset($_GET['debug']) ) $url = $url . '&debug=yes';
    echo(' | <a href="'.$url.'">'.$next.' &gt;&gt;</a> ');
}

$extra = "";
if ( array_key_exists('detail', $thistest) ) {
    $extra = $extra . "<hr/>Detail:\n".$thistest["detail"]. "\n";
}
if ( array_key_exists('result', $thistest) ) {
    $extra = $extra . "<hr/>Expected Result:\n".$thistest["result"]. "\n";
}
if ( strlen($extra) > 0 ) echo($extra);

/*
if ( array_key_exists($testshow, $passed) ) {
    echo(' <span style="background-color: aqua;">(Passed)</a></span>');
} else {
    $url = 'toolcert.php?passed='.$testshow;
    if ( isset($_GET['debug']) ) $url = $url . '&debug=yes';
    echo(' (<a href="'.$url.'">Mark as Passed</a>)'."\n");
}
*/
$url = 'toolcert.php?testshow='.$testshow;
if ( isset($_GET['debug']) ) {
    echo(' (<a href="'.$url.'">Turn Debug Off</a>)'."\n");
} else {
    $url = $url . '&debug=yes';
    echo(' (<a href="'.$url.'">Turn Debug On</a>)'."\n");
}
echo("\n</p>\n");
echo("</center>\n");
?>
<center>
<script type="text/javascript">
$(".bodywrap").css("width","95%");
</script>
<iframe id="myframe" height="600px" src="toolcert.php?testid=<?php 
echo($testshow); 
if ( isset($_GET['debug']) ) echo('&debug=yes');
?>" 
style="border-width: 5px;"
height="2400px" width="95%">
</iframe>
</center>
