<?php 
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

// Load up the LTI Support code
require_once 'util/lti_util.php';

session_start();
header('Content-Type: text/html; charset=utf-8'); 

/*
*	Mauro Donadio
*/
// Initialize, all secrets are 'secret', do not set session, and do not redirect
$parm	= array('table'			=> 'AC_oauth_server_consumers',
				'key_column'	=> 'consumer_key',
				'secret_column'	=> 'consumer_secret',
				'context_column'=> 'consumer'
				);

$context = new BLTI($parm, false, false);
echo $context->dump();
die();
?>
<html>
<head>
  <title>IMS Learning Tools Interoperability 1.1</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body style="font-family:sans-serif; background-color:#add8e6">
<?php
echo("<p><b>IMS LTI 1.1 PHP Provider</b></p>\n");
echo("<p>This is a very simple reference implementaton of the Tool side (i.e. provider) 
for IMS LTI 1.1.</p>\n");

$sourcedid = $_REQUEST['lis_result_sourcedid'];
if (get_magic_quotes_gpc()) $sourcedid = stripslashes($sourcedid);
$sourcedid = htmlentities($sourcedid);

if ( $context->valid ) {
   if ( $_POST['launch_presentation_return_url']) {
     $msg = 'A%20message%20from%20the%20tool%20provider.';
     $error_msg = 'An%20error%20message%20from%20the%20tool%20provider.';
     $sep = (strpos($_POST['launch_presentation_return_url'], '?') === FALSE) ? '?' : '&amp;';
     print "<p><a href=\"{$_POST['launch_presentation_return_url']}\">Return to tool consumer</a> (";
     print "<a href=\"{$_POST['launch_presentation_return_url']}{$sep}lti_msg={$msg}&amp;lti_log=LTI%20log%20entry:%20{$msg}\">with a message</a> or ";
     print "<a href=\"{$_POST['launch_presentation_return_url']}{$sep}lti_errormsg={$error_msg}&amp;lti_errorlog=LTI%20error%20log%20entry:%20{$error_msg}\">with an error</a>";
     print ")</p>\n";
   }

    if ( $_POST['lis_result_sourcedid'] && $_POST['lis_outcome_service_url'] ) {
        print "<p><b>Note:</b> This launch can submit a grade back to the LMS using LTI 1.1 Outcome Service.  Press\n";
        print '<a href="common/tool_provider_outcome.php?sourcedid='.$sourcedid;
        print '&key='.urlencode($_POST['oauth_consumer_key']);
        print '&seret=secret';
        print '&url='.urlencode($_POST['lis_outcome_service_url']).'">';
        print 'here to send a grade back via LIS/LTI Outcome Service</a>.</p>'."\n";
    } 
    if ( $_POST['lis_result_sourcedid'] && $_POST['ext_ims_lis_basic_outcome_url'] ) {
        print "<p><b>Note:</b> This launch can submit a grade back to the LMS using Outcomes Extensions <i>(unpubished spec)</i>.  Press\n";
        print '<a href="ext/setoutcome.php?sourcedid='.$sourcedid;
        print '&key='.urlencode($_POST['oauth_consumer_key']);
        print '&url='.urlencode($_POST['ext_ims_lis_basic_outcome_url']).'">';
        print 'here to send a grade back</a>.</p>'."\n";
    } 
    if ( $_POST['ext_ims_lis_memberships_id'] && $_POST['ext_ims_lis_memberships_url'] ) {
        print "<p><b>Note:</b> This launch can retrieve a full roster from the LMS <i>(unpubished spec)</i>.  Press\n";
        print '<a href="ext/memberships.php?id='.htmlentities($_POST['ext_ims_lis_memberships_id']);
        print '&key='.urlencode($_POST['oauth_consumer_key']);
        print '&url='.urlencode($_POST['ext_ims_lis_memberships_url']).'">';
        print 'here to retrieve a roster from this LMS</a>.</p>'."\n";
    }
    if ( $_POST['ext_ims_lti_tool_setting_id'] && $_POST['ext_ims_lti_tool_setting_url'] ) {
        print "<p><b>Note:</b> This launch can store an instance setting in the LMS <i>(unpubished spec)</i>.  Press\n";
        print '<a href="ext/setting.php?id='.htmlentities($_POST['ext_ims_lti_tool_setting_id']);
        print '&key='.urlencode($_POST['oauth_consumer_key']);
        print '&url='.urlencode($_POST['ext_ims_lti_tool_setting_url']).'">';
        print 'here to exercise the tool setting service</a>.</p>'."\n";
    }
    print "<pre>\n";
    print "Context Information:\n\n";
    print $context->dump();
    print "</pre>\n";
} else {
    print "<p style=\"color:red\">Could not establish context: ".$context->message."<p>\n";
    print "<p>Base String:<br/>\n";
    print $context->basestring;
    print "<br/></p>\n";
}

print "<pre>\n";
print "Raw POST Parameters:\n\n";
ksort($_POST);
foreach($_POST as $key => $value ) {
    if (get_magic_quotes_gpc()) $value = stripslashes($value);
    print "$key=$value (".mb_detect_encoding($value).")\n";
}
print "</pre>";

?>
