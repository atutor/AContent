<?php session_start();

require_once("../util/lti_util.php");
require_once('cert_util.php');

if ( $_SESSION['testing'] == 'tool') {
    lti_reset_session();
    header("Location: ".curPageURL());
    exit;
}
?>
<html>
<head>
  <title>IMS LTI LMS Certification</title>
  <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body style="background: #E3E4DA;">
<a href="http://www.imsglobal.org" target="_new">
<img src="http://www.imsglobal.org/images/imslogo96dpi-ialsm2.jpg" align="right" border="0"/>
</a>
</p>
<?php

require_once("official.php");

if ( isset($_SESSION['cert_consumer_key']) ) {
    echo('<p>Test Setup key='.$_SESSION['cert_consumer_key'].' software='.$_SESSION['software'].' '.$_SESSION['version']);
    echo(' (<a href="lmssetup.php" target="_new">Setup</a>)</p>'."\n");
} else {
    echo("<p>This test environment is not yet configured.\n");
    echo("please run the setup program in the same browser as you\n");
    echo("will run the tests as the tests use the session for configuration\n");
    echo("and results.</p>\n");
    echo('<p><a href="lmssetup.php" target="_new">Test Setup</a></p>'."\n");
    echo("<p>You can track the progress of your testing using</p>\n");
    echo('<p><a href="lmsstatus.php" target="_new">Test Status</a></p>'."\n");
    echo("<p>The status screen can be kept running in a separate tab.\m");
    echo("</p>\n");
    exit;
}

$current_date = gmDate("Y-m-d\TH:i:s\Z");

$good_message_type = $_REQUEST["lti_message_type"] == "basic-lti-launch-request";
$good_lti_version = $_REQUEST["lti_version"] == "LTI-1p0";
$resource_link_id = $_REQUEST["resource_link_id"];

require_once('cert_util.php');

load_cert_data();

$thispass = array();
$thisfail = array();

/*
echo("<p>Request count=");echo(count($requests));echo("</p>\n");
echo("<p>Passed count=");echo(count($passed));echo("</p>\n");
echo("<p>Failed count=");echo(count($failed));echo("</p>\n");

print_r($passed);
*/

$protocol_minimum = false;
if ($good_message_type and $good_lti_version and isset($resource_link_id) ) {

    $protocol_minimum = true;
    mark_pass("1.1", "Required parameters present");
    $reqdebug = print_r($_REQUEST, true);
    $requests[$current_date] = $reqdebug;
}

if ( ! $protocol_minimum ) {
    doerror("Protocol minimum not reached lti_message_type=basic-lti-launch-request lti_version=LTI-1p0 resource_link_id required.");
    return;
}

$oauth_consumer_key = $_SESSION['cert_consumer_key'];

if ( ! isset($oauth_consumer_key) ) {
    doerror("No oauth_consumer_key found");
    return;
}

// Check the signature
// Set up our two consumer/secret pairs
$store = new TrivialOAuthDataStore();
if ( ! isset($_SESSION['cert_consumer_key'] ) ) {
    echo("<p>Please set an LMS-wide consumer</p>\n");
    return;
} else if ( $oauth_consumer_key == $_SESSION['cert_consumer_key'] ) {
    $store->add_consumer($oauth_consumer_key, $_SESSION['cert_secret']);
} else {
    echo("<p>Unexpected oauth_consumer_key=$oauth_consumer_key - should be ".$_SESSION['cert_consumer_key']."</p>\n");
    return;
}

$server = new OAuthServer($store);

$method = new OAuthSignatureMethod_HMAC_SHA1();
$server->add_signature_method($method);
$request = OAuthRequest::from_request();

$base = $request->get_signature_base_string();
print "<!--\nOAuth Base String:\n" . $base . "\n-->\n";

try {
    $server->verify_request($request);
} catch (Exception $e) {
    doerror('Caught OAuth exception: '.$e->getMessage());
    echo("<p>\n");
    echo("Base String: (View source to see real string)\n");
    echo("<p>\n");
    echo($base);
    echo("\n");
    echo("<pre>\n");
    print "Raw GET Parameters:\n\n";
    foreach($_GET as $key => $value ) {
        print "$key=$value\n";
    }
    print "Raw POST Parameters:\n\n";
    ksort($_POST);
    foreach($_POST as $key => $value ) {
        if (get_magic_quotes_gpc()) $value = stripslashes($value);
        print "$key=$value\n";
    }
    echo("\n");
    return;
}

// Lets check to see if we have any excess parameters

$badpost = check_post();

if ( count($badpost) > 0 ) {
    echo "<p><b>Note unexpected POST values ignored:</b>\n";
    foreach ( $badpost as $key => $val ) {
        echo(' '.$val);
    }
    echo("</p>\n");
}

// Now we have a valid, signed request - lets run the tests

$context_id = $_REQUEST['context_id'];
$user_id = $_REQUEST['user_id'];
$roles = $_REQUEST['roles'];

$resource_link_title = $_REQUEST["resource_link_title"];
$resource_link_description = $_REQUEST["resource_link_description"];

// Resource Link information
if ( isset($resource_link_title) ) {
    mark_pass("1.2", "Recommended resource_link_title present");
}
if ( isset($resource_link_description) ) {
    mark_pass("1.3", "Optional resource_link_description present");
}

if ( isset($_REQUEST['tool_consumer_info_product_family_code']) ) {
    mark_pass("1.4", "Optional tool_consumer_info_product_family_code present (1.1)");
}

if ( isset($_REQUEST['tool_consumer_info_version']) ) {
    mark_pass("1.5", "Optional tool_consumer_info_version present (1.1)");
}

// 2.0 OAuth Checks

// TODO: May want to demand this comes with custom to make it harder...
if ( $oauth_consumer_key == $_SESSION['cert_consumer_key'] ) {
    mark_pass("2.1", "Provider-generated key");
}

if ( count($_GET) > 0 ) {
    mark_pass("2.2", "Request with URL parameter");
}

// Check if GET parameters with a space are received properly
if ( $_GET['x'] == 'With Space' ) {
    mark_pass("2.3", "URL Parameter x=With%20Space Properly Encoded");
}

// Enforce 1.0A compliance (even though it is silly)
// We do not use this field - about:blank is fine
if ( ! isset($_REQUEST['oauth_callback']) ) {
    mark_fail("2.4", "oauth_callback missing 1.0A Compliance");
}

/*
<?xml version="1.0" encoding="UTF-8"?>
<basic_lti_link xmlns="http://www.imsglobal.org/services/cc/imsblti_v1p0"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <title>A Simple Descriptor</title>
  <custom>
    <parameter key="simple_key">custom_simple_value</parameter>
    <parameter key="Complex!@#$^*(){}[]KEY">Complex!@#$^*(){}[]Value</parameter>
  </custom>
  <launch_url>http://localhost/~csev/blti-cert/lmscert.php?x=With%20Space&amp;from-xml=yes</launch_url>
</basic_lti_link>
*/

// 3.0 Custom fields
if ( $oauth_consumer_key == $_SESSION['cert_consumer_key'] and
     $_REQUEST['custom_simple_key'] == 'custom_simple_value' ) {
    mark_pass("3.1", "Custom fields supported");
}

// Complex!@#$^*(){}[]KEY=Complex!@#$^*(){}[]Value
if ( $oauth_consumer_key == $_SESSION['cert_consumer_key'] and
     $_REQUEST['custom_complex____________key'] == 'Complex!@#$^*(){}[]Value' ) {
    mark_pass("3.2", "Custom field key character mapping correct");
}

// 4.0 User Identity and Roles
if ( isset($user_id) ) mark_pass("4.1", "Sends user_id");

if ( isset($roles) ) {
    $therole = instructor_or_learner($roles);
    if ( $therole == 'Learner' ) {
         mark_pass("4.2", "Sends Learner role");
    } else if ( $therole == 'Instructor' ) {
         mark_pass("4.3", "Sends Istructor role");
    } else {
  // Might want to get grouchy here...
    }
    $notes = check_roles($roles);
    if ( count($notes) > 0 ) {
        mark_fail("4.4", "Bad roles format");
        echo("<p>Your roles string is in error:</p>\n");
        echo("<pre>\n$roles\n\n");
        foreach($notes as $key => $value ) {
            echo($key.' => '.$value."\n");
        }
        echo("</pre>");
    }
}

function isthere($val) {
   return isset($val) && strlen($val) > 0 ;
}

$good_name = false;
if ( ( isthere($_REQUEST['lis_person_name_given']) and
       isthere($_REQUEST['lis_person_name_family']) ) or
       isthere($_REQUEST['lis_person_name_full']) ) {
    $good_name = true;
}

if ( $good_name and
     isthere($_REQUEST['lis_person_contact_email_primary']) ) {
    mark_pass("4.5", "Full User Info");
}

if ( ! isthere($_REQUEST['lis_person_name_given']) and
     ! isthere($_REQUEST['lis_person_name_family']) and
     ! isthere($_REQUEST['lis_person_name_full']) and
     isthere($_REQUEST['lis_person_contact_email_primary']) ) {
    mark_pass("4.6", "E-Mail only");
}

if ( $good_name and
     ! isthere($_REQUEST['lis_person_contact_email_primary']) ) {
    mark_pass("4.7", "User name but not E-Mail");
}

if ( isthere($_REQUEST['lis_person_name_given']) or
     isthere($_REQUEST['lis_person_name_family']) or
     isthere($_REQUEST['lis_person_name_full']) or
     isthere($_REQUEST['lis_person_contact_email_primary']) ) {
    // pass
} else {
    mark_pass("4.8", "No User Info");
}

// 5.0 Context support
if ( isthere($context_id) ) {
    mark_pass("5.1", "Sends context_id");
} else {
    mark_pass("5.4", "Request w/o context_id (i.e. a menu link)");
}

if ( isthere($_REQUEST['context_label']) ) mark_pass("5.2", "Sends context_label");
if ( isthere($_REQUEST['context_title']) ) mark_pass("5.3", "Sends context_title");

if ( isthere($_REQUEST['context_type']) ) {
    mark_pass("5.5", "Sends context_type");
    $context_type = $_REQUEST['context_type'];
    $notes = check_types($context_type);
    if ( count($notes) > 0 ) {
        mark_fail("5.6", "Bad context_type format");
        echo("<p>Your context_type string is in error:</p>\n");
        echo("<pre>\n$context_type\n");
        foreach($mapping as $key => $value ) {
            echo($key.' => '.$value."\n");
        }
        echo("</pre>");
    }
}


// 6.0 Consistency / Mapping

// Add this mapping if not found
$newmap = array($resource_link_id, $user_id, $context_id, $roles);
$found = false;
foreach($mapping as $key => $value ) {
    if ( $value == $newmap ) {
        $found = true;
        break;
    }
}
if ( ! $found ) $mapping[] = $newmap;

// Look for things that are supposed to change
// array($resource_link_id, $user_id, $context_id, $roles);
foreach($mapping as $key => $value ) {
    if ( isset($resource_link_id) and isset($value[0]) and $resource_link_id != $value[0] ) {
        mark_pass("6.1", "resource_link_id changed");
    }
    if ( isset($user_id) and isset($value[1]) and $user_id != $value[1] ) {
        mark_pass("6.2", "user_id changed");
    }
    if ( isset($context_id) and isset($value[2]) and $context_id != $value[2] ) {
        mark_pass("6.3", "context_id changed");
    }
}

// Look for good or bad combinations of things in an n-squared loop!
// array($resource_link_id, $user_id, $context_id, $roles);
foreach($mapping as $k1 => $value ) {
    if ( is_string($value[0]) and is_string($value[1]) and
         is_string($value[2]) and is_string($value[3]) ) {
        // Good news
    } else {
       continue;  // Ignore this mapping due to missing data
    }
    foreach($mapping as $k2 => $check ) {
        if ( $value == $check ) continue;
        if ( is_string($check[0]) and is_string($check[1]) and
             is_string($check[2]) and is_string($check[3]) ) {
            // Good news
        } else {
           continue;  // Ignore this mapping due to missing data
        }
  // echo("<pre>\nKEY and CHECK\n");
        // print_r($value);
        // print_r($check);
  // echo("</pre>");

        if ( $value[2] == $check[2] and $value[0] != $check[0] ) {
            mark_pass("6.4", "Support for more than one resource_link_id from a context_id");
        }
        if ( $value[0] == $check[0] and $value[2] != $check[2] ) {
            mark_fail("6.5", "A resource_link_id must always come from the same context_id");
        }
        if ( $value[1] == $check[1] and $value[3] != $check[3] ) {
            mark_fail("6.6", "A user_id must not switch roles in a context_id");
        }
    }
}

// 7.0 Launch materials
if ( isset($_REQUEST['launch_presentation_locale']) ) mark_pass("7.1", "launch_presentation_locale");

$target = $_REQUEST['launch_presentation_document_target'];
if ( isset($target) ) {
    if ( $target == 'frame' or $target == 'iframe' or $target == 'window' ) {
        mark_pass("7.2", "launch_presentation_document_target");
    } else {
        mark_fail("7.2", "launch_presentation_document_target");
    }
}

if ( isset($_REQUEST['launch_presentation_height']) ) {
    $height = intval($_REQUEST['launch_presentation_height']);
    if ( $height > 0 ) {
        mark_pass("7.3", "launch_presentation_height");
    } else {
        mark_fail("7.3", "launch_presentation_height");
    }
}

if ( isset($_REQUEST['launch_presentation_width']) ) {
    $width = intval($_REQUEST['launch_presentation_width']);
    if ( $width > 0 ) {
        mark_pass("7.4", "launch_presentation_width");
    } else {
        mark_fail("7.4", "launch_presentation_width");
    }
}

if ( isset($_REQUEST['lis_result_sourcedid']) && isset($_REQUEST['lis_outcome_service_url']) ) {
   mark_pass("8.1", "Sends lis_result_sourcedid and lis_outcome_service_url");
   print "<p>This launch provided information to call the LTI Outcome Service.  Press\n";
   $sourcedid = $_REQUEST['lis_result_sourcedid'];
   if (get_magic_quotes_gpc()) $sourcedid = stripslashes($sourcedid);
   print '<a href="lmsoutcome.php?sourcedid='.
      urlencode($sourcedid);
   print '&key='.urlencode($oauth_consumer_key);
   print '&secret='.urlencode($_SESSION['cert_secret']);
   print '&url='.urlencode($_REQUEST['lis_outcome_service_url']).'" target="_outcome">';
   print 'here to run the 8.x tests of that service.</a>.</p>'."\n";
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

update_cert_data();
echo("<!--\n");
echo("Passed:\n");
print_r($passed);
echo("\nFailed:\n");
print_r($failed);
echo("-->\n");

print "<pre>\n";
print "POST Data:\n";
ksort($_POST);
foreach($_POST as $key => $value ) {
  if (get_magic_quotes_gpc()) $value = stripslashes($value);
  print "$key=$value (".mb_detect_encoding($value).")\n";
}
print "\n";
print "GET Data:\n";
foreach($_GET as $key => $value ) {
  if (get_magic_quotes_gpc()) $value = stripslashes($value);
  print "$key=$value (".mb_detect_encoding($value).")\n";
}
print "</pre>";

?>
