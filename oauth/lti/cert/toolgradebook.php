<?php
if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
 error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else { 
 error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
}

$old_error_handler = set_error_handler("myErrorHandler");

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    // echo("YO ". $errorno . $errstr . "\n");
    if ( strpos($errstr, 'deprecated') !== false ) return true;
    return false;
}

ini_set("display_errors", 1);

if ( !isset ( $_REQUEST['b64'] ) ) {
   die("Missing b64 parameter");
}

$b64 = $_REQUEST['b64'];
ini_set("session.use_cookies",0);
ini_set("session.use_only_cookies",0);
ini_set("session.use_trans_sid",1); # Forgot this one!
session_id(md5($b64));
session_start();

include "../common/header.php";
?>
<style type="text/css">
table {
  border-collapse: collapse;
  border: 1px solid black;
}

th {
  text-align: left;
  border: 1px solid black;
  background: gray;
  color: white;
  padding: 0.2em;
}

td {
  border: 1px solid black;
  padding: 0.2em;
}
</style>

<?php

$gradebook = $_SESSION['cert_gradebook'];
if ( !isset($gradebook) ) $gradebook = Array();

$expected = Array(
    'con-182:::rlig-1234:::654321',
    'con-182:::rlig-1234:::543216',
    'con-777:::rlid-777:::777777',
    'con-182:::rlig-2341:::654321');

echo("<center>\n");
echo('<p><b>LTI 1.1 Gradebook: ' . sizeof($gradebook). ' entries</b></p>');

if ( sizeof($gradebook) < 1 ) return;

echo("<!--\n");
print_r($gradebook);
echo("-->\n");

$matched = 0;
echo("<table><tr><th>Course</th><th>Resource</th><th>User</th><th>Grade</th><th>Status</th></tr>\n");

foreach($gradebook as $key => $val ) {
    $pass = ' ';
    foreach ($expected as $ek => $ev ) {
        if ( $ev == $key ) {
             unset($expected[$ek]);
             $pass = 'Pass';
        }
    }
 
    $entry = explode(":::", $key);
    $context_id = $entry[0];
    $resource_link_id = $entry[1];
    $user_id = $entry[2];
    echo("<tr><td>$context_id</td><td>$resource_link_id</td><td>$user_id</td><td>$val</td><td>$pass</td></tr>\n");
}
echo("</table><br/>\n");

if ( sizeof($expected) == 0 ) {
   echo("<p>Congratulations, you have passed the gradebook test.</p>");
   return; 
}

echo('<hr><p style="color:red">You are missing the following gradebook entries 
required to pass the gradebook test.</p>
');

echo("<table><tr><th>Course</th><th>Resource</th><th>User</th><th>Status</th></tr>\n");

foreach($expected as $key => $val ) {
    $entry = explode(":::", $val);
    $context_id = $entry[0];
    $resource_link_id = $entry[1];
    $user_id = $entry[2];
    echo("<tr><td>$context_id</td><td>$resource_link_id</td><td>$user_id</td><td>Missing</td></tr>\n");
}
echo("</table>");


?>
