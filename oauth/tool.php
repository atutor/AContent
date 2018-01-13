<?php 
// Load up the Basic LTI Support code
exit;
require_once 'ims-blti/blti.php';

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
header('Content-Type: text/html; charset=utf-8'); 

$parm	= array('table'			=> 'AC_oauth_server_consumers',
				'key_column'	=> 'consumer_key',
				'secret_column'	=> 'consumer_secret',
				'context_column'=> 'consumer'
				);

// Initialize, all secrets are 'secret', do not set session, and do not redirect
//$context = new BLTI("secret", false, false);
$context = new BLTI($parm, false, false);

?>
<?php

if ( $context->valid ) {
    if ( $_POST['lis_result_sourcedid'] && $_POST['ext_ims_lis_basic_outcome_url'] ) {
        print "<p><b>Note:</b> This launch can submit a grade back to the LMS using POX-Based Outcomes.  Press\n";
        print '<a href="setoutcome_pox.php?sourcedid='.$_POST['lis_result_sourcedid'];
        print '&key='.$_POST['oauth_consumer_key'];
        print '&url='.$_POST['ext_ims_lis_basic_outcome_url'].'">';
        print 'here to send a grade back via POX</a>.</p>'."\n";
    } 
    if ( $_POST['lis_result_sourcedid'] && $_POST['ext_ims_lis_basic_outcome_url'] ) {
        print "<p><b>Note:</b> This launch can submit a grade back to the LMS using Basic Outcomes.  Press\n";
        print '<a href="setoutcome.php?sourcedid='.$_POST['lis_result_sourcedid'];
        print '&key='.$_POST['oauth_consumer_key'];
        print '&url='.$_POST['ext_ims_lis_basic_outcome_url'].'">';
        print 'here to send a grade back</a>.</p>'."\n";
    } 
    if ( $_POST['ext_ims_lis_memberships_id'] && $_POST['ext_ims_lis_memberships_url'] ) {
        print "<p><b>Note:</b> This launch can retrieve a full roster the LMS.  Press\n";
        print '<a href="memberships.php?id='.$_POST['ext_ims_lis_memberships_id'];
        print '&key='.$_POST['oauth_consumer_key'];
        print '&url='.$_POST['ext_ims_lis_memberships_url'].'">';
        print 'here to retrieve a roster from this LMS</a>.</p>'."\n";
    }
    if ( $_POST['ext_ims_lti_tool_setting_id'] && $_POST['ext_ims_lti_tool_setting_url'] ) {
        print "<p><b>Note:</b> This launch can store an instance setting in the LMS.  Press\n";
        print '<a href="setting.php?id='.$_POST['ext_ims_lti_tool_setting_id'];
        print '&key='.$_POST['oauth_consumer_key'];
        print '&url='.$_POST['ext_ims_lti_tool_setting_url'].'">';
        print 'here to exercise the tool setting service</a>.</p>'."\n";
    }
    print "<pre>\n";
    print "Context Information:\n\n";
    print $context->dump();
    print "</pre>\n";
} else {
    print "<p style=\"color:red\">Could not establish context: ".$context->message."<p>\n";
}

print "<pre>\n";
print "Raw POST Parameters:\n\n";
foreach($_POST as $key => $value ) {
    print "$key=$value (".mb_detect_encoding($value).")\n";
}
print "</pre>";

?>
