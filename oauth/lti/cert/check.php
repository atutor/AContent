<?php

// Perform some basic checks on the input data
if ( $_REQUEST["lti_message_type"] != "basic-lti-launch-request" ) {
  print "<p style=\"color:red\">Warning: Missing required parameter lti_message_type=basic-lti-launch-request</p>";
}

if ( $_REQUEST["lti_version"] != "LTI-1p0" ) {
  print "<p style=\"color:red\">Warning: Missing required parameter lti_version=LTI-1p0</p>";
}

if ( empty($_REQUEST["resource_link_id"]) ) {
  print "<p style=\"color:red\">Warning: Missing required parameter resource_link_id</p>";
}

if ( empty($_REQUEST["user_id"]) ) {
  print "<p style=\"color:blue\">Note: Missing recommended parameter user_id</p>";
}

if ( empty($_REQUEST["roles"]) ) {
  print "<p style=\"color:blue\">Note: Missing recommended parameter roles</p>";
}

if ( empty($_REQUEST["context_id"]) ) {
  print "<p style=\"color:green\">You do not have a context_id parameter - if this is a launch from a course, you should include context_id</p>";
}
?>
