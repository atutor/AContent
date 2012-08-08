<?php session_start(); 
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

$sourcedid = $_REQUEST['sourcedid'];
if (get_magic_quotes_gpc()) $sourcedid = stripslashes($sourcedid);

 ?><html>
<head>
  <title>IMS Learning Tools Interoperability 1.1</title>
</head>
<body style="font-family:sans-serif; background-color:#add8e6">

<p><b>Tool Provider Calling the IMS LTI 1.1 Outcome Service</b></p>
<p>This is a simple implementation of the LTI 1.1 Outcomes Service.</p>
<?php 
// Load up the LTI Support code
require_once("../util/lti_util.php");
require_once('cert_util.php');

if ( isset($_SESSION['cert_consumer_key']) ) {
    echo('<p>Test Setup key='.$_SESSION['cert_consumer_key'].' software='.$_SESSION['software'].' '.$_SESSION['version']);
} else {
    echo("<p>This test environment is not yet configured.\n");
    echo("please run the setup program in the same browser as you\n");
    echo("will run the tests as the tests use the session for configuration\n");
    echo("and results.</p>\n");
    exit;
}

load_cert_data();

$oauth_consumer_key = $_SESSION['cert_consumer_key'];
$oauth_consumer_secret = $_SESSION['cert_secret'];

// echo("OAK=$oauth_consumer_key OAS=$oauth_consumer_secret\n");
$hc = 0;
$array = str_split($stringEmailAddress);
foreach(str_split(session_id()) as $char) {
   $hc = $hc + ord($char);
}
$grade = 1.0 - ($hc % 10)/50.0;
if ( $grade < 0.75 ) $grade = 0.75;
if ( $grade > 1.0 ) $grade = 1.0;
$grade = (string) $grade;
?>
<p>
<form method="post">
Service URL: <input type="text" name="url" size="80" disabled="true" value="<?php echo($_REQUEST['url']);?>"/></br>
lis_result_sourcedid: <input type="text" name="sourcedid" disabled="true" size="100" value="<?php echo(htmlentities($sourcedid));?>"/></br>
OAuth Consumer Key: <input type="text" name="key" disabled="true" size="80" value="<?php echo(htmlentities($oauth_consumer_key));?>"/></br>
OAuth Consumer Secret: <input type="text" name="secret" size="80" disabled="true" value="<?php echo(htmlentities($oauth_consumer_secret));?>"/></br>
Grade to Send to LMS: <input type="text" disabled="true" name="grade" value="<?php echo($grade);?>"/><br/>
<input type='submit' name='submit' value="Send Grade (8.2)">
<input type='submit' name='submit' value="Read Grade (after send) (8.3)">
<input type='submit' name='submit' value="Delete Grade (8.4)">
<input type='submit' name='submit' value="Check Grade (after delete) (8.5)">
<input type='submit' name='submit' value="Test Unsupported Operation (8.6)">
<input type='submit' name='submit' value="Out-of-range test (8.7)">
<input type='submit' name='submit' value="Non-numeric (8.8)">

<br/>
You can run the tests as many times as you like, to pass test 8.3, it should be run right after 8.2 and
to pass test 8.4 it should be run after 8.3.
</form>
<?php 
$url = $_REQUEST['url'];
if(!in_array($_SERVER['HTTP_HOST'],array('localhost','127.0.0.1')) && strpos($url,'localhost') > 0){ ?>
<p>
<b>Note</b> This service call may not work.  It appears as though you are 
calling a service running on <b>localhost</b> from a tool that
is not running on localhost.
Because these services are server-to-server calls if you are 
running your LMS on "localhost", you must also run this script
on localhost as well.  If your LMS has a real Internet
address you should be OK.
</p>
<?php
}

$method="POST";
$endpoint = $_REQUEST['url'];
$content_type = "application/xml";

$body = '<?xml version = "1.0" encoding = "UTF-8"?>  
<imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">      
	<imsx_POXHeader>         
		<imsx_POXRequestHeaderInfo>            
			<imsx_version>V1.0</imsx_version>  
			<imsx_messageIdentifier>MESSAGE</imsx_messageIdentifier>         
		</imsx_POXRequestHeaderInfo>      
	</imsx_POXHeader>      
	<imsx_POXBody>         
		<OPERATION>            
			<resultRecord>
				<sourcedGUID>
					<sourcedId>SOURCEDID</sourcedId>
				</sourcedGUID>
				<result>
					<resultScore>
						<language>en</language>
						<textString>GRADE</textString>
					</resultScore>
				</result>
			</resultRecord>       
		</OPERATION>      
	</imsx_POXBody>   
</imsx_POXEnvelopeRequest>';

$shortBody = '<?xml version = "1.0" encoding = "UTF-8"?>  
<imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">      
	<imsx_POXHeader>         
		<imsx_POXRequestHeaderInfo>            
			<imsx_version>V1.0</imsx_version>  
			<imsx_messageIdentifier>MESSAGE</imsx_messageIdentifier>         
		</imsx_POXRequestHeaderInfo>      
	</imsx_POXHeader>      
	<imsx_POXBody>         
		<OPERATION>            
			<resultRecord>
				<sourcedGUID>
					<sourcedId>SOURCEDID</sourcedId>
				</sourcedGUID>
			</resultRecord>       
		</OPERATION>      
	</imsx_POXBody>   
</imsx_POXEnvelopeRequest>';

$messageid = uniqid();
if ( strpos($_POST['submit'], "Send") === 0 ) {
    $operation = 'replaceResultRequest';
    $postBody = str_replace(
	array('SOURCEDID', 'GRADE', 'OPERATION','MESSAGE'), 
	array($sourcedid, $grade, $operation, $messageid), 
	$body);
} else if ( strpos($_POST['submit'], "Read") === 0 ) {
    $operation = 'readResultRequest';
    $postBody = str_replace(
	array('SOURCEDID', 'OPERATION','MESSAGE'), 
	array($sourcedid, $operation, $messageid), 
	$shortBody);
} else if ( strpos($_POST['submit'], "Delete") === 0 ) {
    $operation = 'deleteResultRequest';
    $postBody = str_replace(
	array('SOURCEDID', 'OPERATION','MESSAGE'), 
	array($sourcedid, $operation, $messageid), 
	$shortBody);
} else if ( strpos($_POST['submit'], "Check") === 0 ) {
    $operation = 'readResultRequest';
    $postBody = str_replace(
	array('SOURCEDID', 'OPERATION','MESSAGE'), 
	array($sourcedid, $operation, $messageid), 
	$shortBody);
} else if ( strpos($_POST['submit'], "Test") === 0 ) {
    $operation = 'smurfResultRequest';
    $postBody = str_replace(
	array('SOURCEDID', 'OPERATION','MESSAGE'), 
	array($sourcedid, $operation, $messageid), 
	$shortBody);
} else if ( strpos($_POST['submit'], "Out") === 0 ) {
    $operation = 'replaceResultRequest';
    $postBody = str_replace(
	array('SOURCEDID', 'GRADE', 'OPERATION','MESSAGE'), 
	array($sourcedid, "42", $operation, $messageid), 
	$body);
} else if ( strpos($_POST['submit'], "Non") === 0 ) {
    $operation = 'replaceResultRequest';
    $postBody = str_replace(
	array('SOURCEDID', 'GRADE', 'OPERATION','MESSAGE'), 
	array($sourcedid, "smurf", $operation, $messageid), 
	$body);
} else {
    exit();
}

global $LastOAuthBodyBaseString;
$lbs = FALSE;
try {
    echo("Contacting $endpoint ....<br/>\n");
	flush();
    $response = sendOAuthBodyPOST($method, $endpoint, $oauth_consumer_key, $oauth_consumer_secret, $content_type, $postBody);
	$lbs = $LastOAuthBodyBaseString;
    echo("Received ".strlen($response)." bytes.<br/>\n");
	flush();
} catch (Exception $e) {
    echo('<p>Error: Unable to send XML Request ' . $e->getMessage() . "</p>\n");
    return;
}

// Parse Response
print("<pre>\n");
try {
    $xml = new SimpleXMLElement($response);
    $imsx_header = $xml->imsx_POXHeader->children();
    $parms = $imsx_header->children();
    $status_info = $parms->imsx_statusInfo;
    $code_major = (string) $status_info->imsx_codeMajor;
    $message_ref = (string) $status_info->imsx_messageRefIdentifier;
    $imsx_body = $xml->imsx_POXBody->children();
    $operation = $imsx_body->getName();
    $parms = $imsx_body->children();
} catch (Exception $e) {
    echo('<p>Error: Unable to parse XML response' . $e->getMessage() . "</p>\n");
}
// echo($operation."\n");
// echo($code_major."\n");
// print_r($parms);
print("</pre>\n");

if ( $message_ref != $messageid ) {
   echo("Warning: Sent message id=".$messageid." received message id=".$message_ref."\n");
}

$textString = false;
if ( $code_major != 'success' ) {
          echo("<p>Error imsx_codeMajor=$code_major.</p>\n");
} else if ( $operation == 'readResultResponse' ) {
   try {
       $language =  (string) $parms->result->resultScore->language;
       $textString =  (string) $parms->result->resultScore->textString;
       if ( $language != 'en' ) {
          echo("<p>Error returned resultScore language must be 'en' : ".htmlentities($language)."</p>\n");
       } 
   } catch (Exception $e) {
       echo("<p>Exception while parsing response: ".htmlentities( $e->getMessage()) );
  }
}

if ( strpos($_POST['submit'], "Send") === 0 && $code_major == 'success'  && $operation == 'replaceResultResponse' ) {
   mark_pass("8.2", "Handles replaceResult");
}

if ( strpos($_POST['submit'], "Read") === 0 && $code_major == 'success' && $textString !== false ) {
   $difference = abs($grade - $textString);
   if ( $difference < 0.01 ) {
      mark_pass("8.3", "Handles readResult");
   } else {
      echo("<p>Error expecting resultScore textstring of '".htmlentities($grade)."' found: ".htmlentities($textString)."</p>\n");
   }
}

if ( strpos($_POST['submit'], "Delete") === 0 && $code_major == 'success'  && $operation == 'deleteResultResponse' ) {
   mark_pass("8.4", "Handles deleteResult");
}

if ( strpos($_POST['submit'], "Check") === 0 ) {
   if ( $code_major == 'failure' ||
      ( $code_major == 'success' && strlen($textString) == 0 ) ) {
      mark_pass("8.5", "Proper response to deleteResult");
   } else {
      echo("<p>Error expecting empty resultScore textstring after delete found: ".htmlentities($textString)."</p>\n");
   }
}

if ( strpos($_POST['submit'], "Test") === 0 && $code_major == 'unsupported' ) {
   mark_pass("8.6", "Handles invalid operations");
}

if ( strpos($_POST['submit'], "Out") === 0 && $code_major == 'failure' ) {
   mark_pass("8.7", "Handles out of range values with failure");
}

if ( strpos($_POST['submit'], "Non") === 0 && $code_major == 'failure' ) {
   mark_pass("8.8", "Handles non-numeric values");
}

if ( count($thispass) > 0 ) {
    echo("<p>This Launch Passed Test(s): ");
    foreach($thispass as $key => $value ) {
        echo($key." ");
    }
    echo(' (<a href="lmsstatus.php" target="_new">Test Status</a>)');
    echo("</p>\n");
}

if ( count($thisfail) > 0 ) {
    echo("<p>This Launch Failed Test(s): ");
    foreach($thisfail as $key => $value ) {
        echo($key." ");
    }
    echo("</p>\n");
}

echo("<p>Total Requests=");echo(count($requests));
echo(" Passed=");echo(count($passed));
echo(" Failed=");echo(count($failed));echo("</p>\n");

require_once("../util/validate_util.php");
libxml_use_internal_errors(true);

$xsd = "OMSv1p0_LTIv1p1Profile_SyncXSD_V1p0.xsd";
$oldns = "http://www.imsglobal.org/lis/oms1p0/pox";
$newns = "http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0";

if ( strpos($_POST['submit'], "Test") === 0 ) {
   echo("<p><b>Note:</b> It is normal for the unsupported 
         operation test to encounter schema errors.</p>\n");
}

echo("\n<pre>\n");
echo("------------ POST RETURNS ------------\n");
$patch = $response;
if ( strpos($patch, $oldns) !== false ) {
    echo "Please update namespace to $newns \n";
    $patch = str_replace($oldns,$newns,$patch);
}
$doc = new DOMDocument();
$doc->loadXML($patch);
if ( $doc->schemaValidate($xsd) ) {
    echo("This XML passed schema check...\n");
} else {
    print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
    print wordwrap(libxml_error_message());
	print "\n";
}

$response = str_replace("><","&gt;\n&lt;",$response);
$response = str_replace("<","&lt;",$response);
$response = str_replace(">","&gt;",$response);
echo($response);

echo("\n\n------------ WE SENT ------------\n");
$patch = $postBody;
if ( strpos($patch, $oldns) !== false ) {
    echo "Please update namespace to $newns \n";
    $patch = str_replace($oldns,$newns,$patch);
}
$doc = new DOMDocument();
$doc->loadXML($patch);
if ( $doc->schemaValidate($xsd) ) {
    echo("This XML passed schema check...\n");
} else {
    print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
    print wordwrap(libxml_error_message());
	print "\n";
}
$postBody = str_replace("<","&lt;",$postBody);
$postBody = str_replace(">","&gt;",$postBody);
echo($postBody);

echo("\nBase String:\n".$lbs."\n");
echo("\n</pre>\n");

update_cert_data();

?>
