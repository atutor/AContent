<?php

function get_current_date() {
    global $current_date;
    if ( isset($current_date) ) return $current_date;
    $current_date = gmDate("Y-m-d\TH:i:s\Z");
    return $current_date;
}

function load_cert_data() {

    global $requests;
    global $passed;
    global $failed;
    global $notes;
    global $errors;
    global $mapping;

    if ( ! is_array($requests) ) $requests = $_SESSION['requests'];
    if ( ! is_array($requests) ) $requests = array();

    if ( ! is_array($passed) ) $passed = $_SESSION['passed'];
    if ( ! is_array($passed) ) $passed = array();

    if ( ! is_array($failed) ) $failed = $_SESSION['failed'];
    if ( ! is_array($failed) ) $failed = array();

    if ( ! is_array($notes) ) $notes = $_SESSION['notes'];
    if ( ! is_array($notes) ) $notes = array();

    if ( ! is_array($errors) ) $errors = $_SESSION['errors'];
    if ( ! is_array($errors) ) $errors = array();

    // (resource_link_id, user_id, context_id, roles)
    if ( ! is_array($mapping) ) $mapping = $_SESSION['mapping'];
    if ( ! is_array($mapping) ) $mapping = array();
}

function update_cert_data() {
    global $requests;
    global $passed;
    global $failed;
    global $notes;
    global $errors;
    global $mapping;
    global $_SESSION;

    ksort($requests);
    $_SESSION['requests'] = $requests;
    ksort($passed);
    $_SESSION['passed'] = $passed;
    ksort($failed);
    $_SESSION['failed'] = $failed;
    ksort($notes);
    $_SESSION['notes'] = $notes;
    ksort($errors);
    $_SESSION['errors'] = $errors;
    ksort($mapping);
    $_SESSION['mapping'] = $mapping;
}

function mark_pass($test, $msg) {
    global $passed;
    global $thispass;
    load_cert_data();
    $thispass[$test] = array(get_current_date(), $msg);
    if ( ! isset($passed[$test]) ) {
        $passed[$test] = array(get_current_date(), $msg);
    }
}

function mark_fail($test, $msg) {
    global $failed;
    global $thisfail;
    load_cert_data();
    $thisfail[$test] = array(get_current_date(), $msg);
    if ( ! isset($failed[$test]) ) {
        $failed[$test] = array(get_current_date(), $msg);
    }
}

function doerror($msg) {
    global $errors;
    load_cert_data();
    echo("<p>\n");
    echo($msg);
    echo("</p>\n");
    $reqdebug = print_r($_REQUEST, true);
    $errors[get_current_date()] = $reqdebug;
    update_cert_data();
}

$cert_lms_text = array(
"1.0" => array("Protocol Compliance and Resource Information", "doc", ""),
"1.1" => array("All messages have required parameters", "",
"All messages require lti_version, lti_message_type, and resource_link_id"),
"1.2" => array("Sends resource_link_title", "pass", 
"A title for the resource. This is the clickable text that appears in the link. This parameter is recommended."),
"1.3" => array("Sends resource_link_description", "pass", 
"A plain text description of the link’s destination, suitable for display alongside the link. Typically no more than several lines long. This parameter is optional."),
"1.4" => array("Sends tool_consumer_info_product_family_code", "", "Indicates the vendor of the calling LMS (LTI 1.1). This parameter is optional."),
"1.5" => array("Sends tool_consumer_info_version", "", "Indicates the version of the calling LMS (LTI 1.1). This parameter is optional."),

"2.0" => array("OAuth and Signing Requests", "doc", ""),
"2.1" => array("Sign with a producer-provided secret and oauth_consumer_key", "", ""),
"2.2" => array("Can sign requests with URL parameters", "", ""),
"2.3" => array("Can sign a request with a URL parameter with space in the value parameter", "", 
"This checks for the presense of a parameter x=With%20Space since some OAuth reference ".
"implementations (i.e. C#) implementations have problems encoding special characters in ".
"URL parameters - so a space in a URL parameter should be %2520 ".
"in the base message string - not %20." .
"While this seems like double-encoding, it is how OAuth works."),
"2.4" => array("Must include oauth_callback for OAuth 1.0A compliance", "fail", 
"Even though oauth_callback is not needed by LTI, it should be included ".
"and set to 'about:blank' so that OAuth library code such as the OAuth for perl ".
"properly can check LTI Signatures."),

"3.0" => array("Custom Field Support", "doc", ""),
"3.1" => array("Can send custom fields", "", 
"Send this custom field <b>simple_key=custom_simple_value</b>\n"),
"3.2" => array("Properly maps special characters and case in custom fields", "", 
"The LTI spec forces all characters in key names to lower case and ".
"maps anything that is not a number or letter to an underscore.".
"Send this field: <b>Complex!@#$^*(){}[]KEY=Complex!@#$^*(){}[]Value</b>\n"),

"4.0" => array("User Information", "doc", ""),
"4.1" => array("Sends a user_id", "" , ""),
"4.2" => array("Sends Learner role", "" , 
"The simplest practice is to simply send either the Learner or Instructor role ".
" or to at least include the Instructor or Learner role with no urn prefix ".
" as one of the comma-separated values.  Very simplistic tools will simply ".
" split the role string based on commas and ".
" look for 'Instructor' as one of roles and treat ".
" all other roles as 'Learner'"),
"4.3" => array("Sends Instructor role", "" , 
"The simplest practice is to simply send either the Learner or Instructor role ".
" or to at least include the Instructor or Learner role with no urn prefix ".
" as one of the comma-separated values.  Very simplistic tools will simply ".
" split the role string based on commas and ".
" look for 'Instructor' as one of roles and treat ".
" all other roles as 'Learner'"),
"4.4" => array("Follows role naming rules.", "fail", 
"Roles is a comma-separated list of URN values for roles.  If this ".
"list is non-empty, it should contain at least one role from the ".
"listed role vocabularies (See Appendix A). Context roles such ".
"as 'Learner' or 'Instructor' do not need the 'urn:' prefix. ".
"If the TC wants to include a role from another namespace, ".
"a fully-qualified URN should be used.  Usage of roles from ".
"non-LIS vocabularies is discouraged as it may limit interoperability. ".
"This test fails if a non-standard role is present without a 'urn:' ".
"prefix."
),

"4.5" => array("Sends request a valid name for the user and email", "",
"We define a valid name as either lis_person_name_full or both lis_person_name_family and lis_person_name_given."),
"4.6" => array("Sends request with only lis_person_contact_email_primary but no name information", "", ""),
"4.7" => array("Sends valid user name information but no email address", "",
"We define a valid name as either lis_person_name_full or both lis_person_name_family and lis_person_name_given."),
"4.8" => array("Can suppress all identifiable user information", "" , 
"This request should not include any of: ".
"lis_person_name_family, lis_person_name_given or lis_person_name_full."),

"5.0" => array("Context support", "doc", ""),
"5.1" => array("Can send a context_id.", "", ""),
"5.2" => array("Can send a context_label.", "", ""),
"5.3" => array("Can send a context_title.", "", ""),
"5.4" => array("Can send a request without a context_id", "pass", 
"This would happen if the LMS used LTI to launch a tool from outside ".
"a course - for example, in a menu in the LMS"),
"5.5" => array("Can send a context_type.", "pass", ""),
"5.6" => array("Follows context_type rules.", "fail", 
"This string is a comma-separated list of URN values that ".
"identify the type of context.  At a minimum, the list MUST ".
"include a URN value drawn from the LIS vocabulary (see Appendix A). ".
"The assumed namespace of these URNs is the LIS vocabulary so ".
"TCs can use the handles when the intent is to refer to an ".
"LIS context type.  If the TC wants to include a context type ".
"from another namespace, a fully-qualified URN should be used."
),


"6.0" => array("Multiple Requests and Consistency", "doc", 
"This section looks across multiple requests for things we want to see ".
"and things we do not want to see."),
"6.1" => array("Send message from a second resource_link_id", "", ""),
"6.2" => array("Sends a different user_id", "", ""),
"6.3" => array("Can send a different context_id", "", ""),
"6.4" => array("Can send multiple resource_link_id values with the same context_id", "pass",
"This test is looking for multiple placements in a single course.  This is optional ".
"and very much depends on features in the LMS."),
"6.5" => array("A resource_link_id never moves from one context_id to another", "fail",
"This is technically possible in general but should not happen during a short test."),
"6.6" => array("Consistency of context_id/user_id/role mappings", "fail",
"For all the requests, a user should have the same role in the same context. ".
"This test fails if we see a user's role change from request to request in a context. ".
"This is technically possible in general but should not happen during a short test."),
"7.0" => array("Launch Support", "doc", "This section is optional."),
"7.1" => array("Sends valid launch_presentation_locale", "", ""),
"7.2" => array("If launch_presentation_document_target is valid", "pass", ""),
"7.3" => array("If launch_presentation_width is present, it is valid", "pass", 
"This must be a positive integer."),
"7.4" => array("If launch_presentation_height is present, it is valid", "pass", 
"This must be a positive integer."),
"8.0" => array("LTI 1.1: Suport for Outcomes Service", "doc", 
"This section verifies proper support for returning grades back to the consumer."),
"8.1" => array("Sends lis_result_sourcedid and lis_outcome_service_url", "", ""),
"8.2" => array("Outcomes Service supports replaceResult operation", "", ""),
"8.3" => array("Outcomes Service supports readResult operation and returns the proper value", "", ""),
"8.4" => array("Outcomes Service supports deleteResult operation", "", ""),
"8.5" => array("Result is properly deleted after deleteResult operation", "", ""),
"8.6" => array("Outcomes Service properly handles unsupported operations", "", 
"This should response with a imsx_codeMajor of unsupported"),
"8.7" => array("replaceResult does not accept out of range values", "", 
   "This should response with a imsx_codeMajor of failure"),
"8.8" => array("replaceResult does not accept invalid values", "", 
   "This should response with a imsx_codeMajor of failure"),
);
ksort($cert_lms_text);

$tool_tests = array(

"001" => array(
   "doc" => "Launch in SI182 as Instructor",
   "detail" => "This user has all user information specified.",
   "result" => "Should have Instructor powers to edit as appropriate",
   "parms" => array( 
      "resource_link_id" => "rli-1234",
      "user_id" => "123456",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Jane Q. Lastname',
      "lis_person_name_given" => 'Jane',
      "lis_person_name_family" => 'Lastname',
      "lis_person_contact_email_primary" => "jane@school.edu",
      "lis_person_sourcedid" => "school.edu:jane",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"002" => array(
   "doc" => "Launch in SI182 as Learner",
   "detail" => "This user has all user information specified.",
   "result" => "Should not have Instructor powers",
   "parms" => array( 
      "resource_link_id" => "rli-1234",
      "user_id" => "654321",
      "roles" => "Learner",  // or Learner
      "lis_person_name_full" => 'Bob R. Person',
      "lis_person_name_given" => 'Bob',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "bob@school.edu",
      "lis_person_sourcedid" => "school.edu:bob",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"003" => array(
   "doc" => "Second resource in SI182 as Instructor",
   "detail" => "This test changes the resource_link_id without changing context_id",
   "result" => "Should have Instructor power but there should be a new instance ".
               "of the tool but still in the same course.",
   "parms" => array( 
      "resource_link_id" => "rli-5678",
      "user_id" => "123456",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Jane Q. Lastname',
      "lis_person_name_given" => 'Jane',
      "lis_person_name_family" => 'Lastname',
      "lis_person_contact_email_primary" => "jane@school.edu",
      "lis_person_sourcedid" => "school.edu:jane",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

// Test privacy options
"004" => array(
   "doc" => "SI301 as Instructor with max privacy.",
   "detail" => "This starts a series of tests that explore data that may be suppressed ".
               "based on the settings in the LMS that control privacy. ".
               "A new user and new course but no identifying information. So ".
               "lis_person_name_full, lis_person_name_family, lis_person_name_given, ".
               "lis_person_contact_email_primary are not sent.  The tool should *not* ".
               "use the email address as primary key - only the combination of ".
               "oauth_consumer_key and user_id.  All of the lis_ data needs to be ".
               "informative only.",
   "result" => "This should work and the user should have Instructor power ".
               "but there should be no user name or e-mail info.",
   "parms" => array( 
      "resource_link_id" => "rli-1000",
      "user_id" => "user-1000",
      "roles" => "Instructor",  // or Learner
      // "lis_person_name_full" => 'Jane Q. Lastname',
      // "lis_person_name_given" => 'Jane',
      // "lis_person_name_family" => 'Lastname',
      // "lis_person_contact_email_primary" => "jane@school.edu",
      // "lis_person_sourcedid" => "school.edu:jane",
      "context_id" => "con-301",
      "context_title" => "Social Computing",
      "context_label" => "SI301",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"005" => array(
   "doc" => "SI301 as Learner with E-Mail but no name.",
   "detail" => "A new user where only the lis_person_contact_email_primary is sent.",
   "result" => "The tool should deal with the lack of name information.",
   "parms" => array( 
      "resource_link_id" => "rli-1000",
      "user_id" => "user-1001",
      "roles" => "Learner",  // or Learner
      // "lis_person_name_full" => 'Jane Q. Lastname',
      // "lis_person_name_given" => 'Jane',
      // "lis_person_name_family" => 'Lastname',
      "lis_person_contact_email_primary" => "user1001@school.edu",
      "context_id" => "con-301",
      "context_title" => "Social Computing",
      "context_label" => "SI301",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"006" => array(
   "doc" => "SI301 as Learner with given/family but no full name.",
   "detail" => "A tool should tolerate receiving the given and family names but not the full name.",
   "result" => "The tool should detect the user's name properly.",
   "parms" => array( 
      "resource_link_id" => "rli-1000",
      "user_id" => "user-1002",
      "roles" => "Learner",  // or Learner
      // "lis_person_name_full" => 'Jane Q. Lastname',
      "lis_person_name_given" => 'User1002',
      "lis_person_name_family" => 'Learnername',
      // "lis_person_contact_email_primary" => "user1001@school.edu",
      "context_id" => "con-301",
      "context_title" => "Social Computing",
      "context_label" => "SI301",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"007" => array(
   "doc" => "SI301 as Learner with full but not given/family name.",
   "detail" => "A tool should tolerate receiving the full name but not given or family names.",
   "result" => "The tool should detect the user's name properly.",
   "parms" => array( 
      "resource_link_id" => "rli-1000",
      "user_id" => "user-1003",
      "roles" => "Learner",  // or Learner
      "lis_person_name_full" => 'User1003 Q. Learnername',
      // "lis_person_name_given" => 'User1002',
      // "lis_person_name_family" => 'Learnername',
      // "lis_person_contact_email_primary" => "user1001@school.edu",
      "context_id" => "con-301",
      "context_title" => "Social Computing",
      "context_label" => "SI301",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

// Test different ways of specifying roles
"008" => array(
   "doc" => "SI131 as fully-qualified Instructor urn",
   "detail" => "This starts a series of tests that will send the Instructor ".
               "role for a series of users using various legal syntax options. ".
               "This starts a new course and new resource.".
               " urn:lti:role:ims/lis/Instructor",
   "result" => "The user should have Instructor privilege.",
   "parms" => array( 
      "resource_link_id" => "rli-6789",
      "user_id" => "user-2001",
      "roles" => "urn:lti:role:ims/lis/Instructor",  // or Learner
      "lis_person_name_full" => 'User2001 Q. Lastname',
      "lis_person_contact_email_primary" => "user2001@school.edu",
      "context_id" => "con-131",
      "context_title" => "Movies, Culture, and Technology",
      "context_label" => "SI131",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"009" => array(
   "doc" => "SI131 with a role where Instructor is in a list",
   "detail" => "urn:non:ims/something/Else,Instructor,urn:lti:instrole:ims/lis/Alumni",
   "result" => "The user should have Instructor privilege.",
   "parms" => array( 
      "resource_link_id" => "rli-6789",
      "user_id" => "user-2002",
      "roles" => "urn:non:ims/something/Else,Instructor,urn:lti:instrole:ims/lis/Alumni",  
      "lis_person_name_full" => 'User2002 Q. Lastname',
      "lis_person_contact_email_primary" => "user2002@school.edu",
      "context_id" => "con-131",
      "context_title" => "Movies, Culture, and Technology",
      "context_label" => "SI131",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"010" => array(
   "doc" => "SI131 where full urn Instructor is in a list",
   "detail" => "urn:non:ims/something/Else,urn:lti:role:ims/lis/Instructor,urn:lti:instrole:ims/lis/Alumni",
   "result" => "The user should have Instructor privilege.",
   "parms" => array( 
      "resource_link_id" => "rli-6789",
      "user_id" => "user-2003",
      "roles" => "urn:non:ims/something/Else,urn:lti:role:ims/lis/Instructor,urn:lti:instrole:ims/lis/Alumni",  
      "lis_person_name_full" => 'User2003 Q. Lastname',
      "lis_person_contact_email_primary" => "user2003@school.edu",
      "context_id" => "con-131",
      "context_title" => "Movies, Culture, and Technology",
      "context_label" => "SI131",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"011" => array(
   "doc" => "SI131 where full urn Learner is in a list",
   "detail" => "urn:non:ims/something/Else,urn:lti:role:ims/lis/Learner,urn:lti:instrole:ims/lis/Alumni",
   "result" => "The user should have Learner privilege.",
   "parms" => array( 
      "resource_link_id" => "rli-6789",
      "user_id" => "user-2004",
      "roles" => "urn:non:ims/something/Else,urn:lti:role:ims/lis/Learner,urn:lti:instrole:ims/lis/Alumni",  
      "lis_person_name_full" => 'User2004 Q. Lastname',
      "lis_person_contact_email_primary" => "user2004@school.edu",
      "context_id" => "con-131",
      "context_title" => "Movies, Culture, and Technology",
      "context_label" => "SI131",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"012" => array(
   "doc" => "Launch in SI182 as Instructor 999999 providing grade service info.",
   "detail" => "A graded resource, launched as instructor.",
   "result" => "This allows an instructor to set up the resource before student launch if necessary.  It is not necessary to send grades back for non-students.",
   "parms" => array( 
      "resource_link_id" => "rlig-1234",
      "user_id" => "999999",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Instruct R. Person',
      "lis_person_name_given" => 'Instruct',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "instruct@school.edu",
      "lis_person_sourcedid" => "school.edu:instruct",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"013" => array(
   "doc" => "Launch in SI182 as Learner 654321 providing grade service info.",
   "detail" => "You can test the replace, read, and delete operations, monitoring the 
                grade book as you progress to make sure things are working, but when you leave this
                test, make sure that you have a grade set.",
   "result" => "A grade should appear in the gradebook for user 654321 if the application sends grades based on learner action.",
   "parms" => array( 
      "resource_link_id" => "rlig-1234",
      "user_id" => "654321",
      "roles" => "Learner",  // or Learner
      "lis_person_name_full" => 'Bob R. Person',
      "lis_person_name_given" => 'Bob',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "bob@school.edu",
      "lis_person_sourcedid" => "school.edu:bob",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"014" => array(
   "doc" => "Launch in SI182 from the same resource as in test 012 as Learner 543216 providing grade service info.",
   "detail" => "Make sure to set a grade for this launch to pass the grade feature.",
   "result" => "A grade should appear in the gradebook for user 543216 in the same resource as test 012 if the application sends grades based on learner action",
   "parms" => array( 
      "resource_link_id" => "rlig-1234",
      "user_id" => "543216",
      "roles" => "Learner",  // or Learner
      "lis_person_name_full" => 'Sally R. Person',
      "lis_person_name_given" => 'Sally',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "sally@school.edu",
      "lis_person_sourcedid" => "school.edu:sally",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"015" => array(
   "doc" => "Re-Launch in SI182 as Instructor 999999 providing grade service info.",
   "detail" => "Make sure to set a grade for this launch to pass the grade feature.",
   "result" => "A second grade should appear in the grade book for user 654321 and 543216 if the application sends grades based on instructor action",
   "parms" => array( 
      "resource_link_id" => "rlig-1234",
      "user_id" => "999999",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Instruct R. Person',
      "lis_person_name_given" => 'Instruct',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "instruct@school.edu",
      "lis_person_sourcedid" => "school.edu:instruct",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"016" => array(
   "doc" => "Launch in SI182 as Instructor 999999 in second resource providing grade service info.",
   "detail" => "A graded resource, launched as instructor.",
   "result" => "This allows an instructor to set up the resource before student launch if necessary.  It is not necessary to send grades back for non-students.",
   "parms" => array( 
      "resource_link_id" => "rlig-2341",
      "user_id" => "999999",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Instruct R. Person',
      "lis_person_name_given" => 'Instruct',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "instruct@school.edu",
      "lis_person_sourcedid" => "school.edu:instruct",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),


"017" => array(
   "doc" => "Launch different resource in SI182 as Learner 654321 providing grade service info.",
   "detail" => "Make sure to set a grade for this launch to pass the grade feature.",
   "result" => "A third grade should appear in the gradebook for user 654321 with a new resource id and the same course id if the application sends grades based on learner action.",
   "parms" => array( 
      "resource_link_id" => "rlig-2341",
      "user_id" => "654321",
      "roles" => "Learner",  // or Learner
      "lis_person_name_full" => 'Bob R. Person',
      "lis_person_name_given" => 'Bob',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "bob@school.edu",
      "lis_person_sourcedid" => "school.edu:bob",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"018" => array(
   "doc" => "Re-Launch in SI182 as Instructor 999999 in second resource providing grade service info.",
   "detail" => "Make sure to set a grade for this launch to pass the grade feature.",
   "result" => "A second grade should appear in the gradebook for user 543216 if the application sends grades based on instructor action",
   "parms" => array( 
      "resource_link_id" => "rlig-2341",
      "user_id" => "999999",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Instruct R. Person',
      "lis_person_name_given" => 'Instruct',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "instruct@school.edu",
      "lis_person_sourcedid" => "school.edu:instruct",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-182",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"019" => array(
   "doc" => "Launch in GAM101 as Instructor 999999 in second resource providing grade service info.",
   "detail" => "A graded resource, launched as instructor.",
   "result" => "This allows an instructor to set up the resource before student launch if necessary.  It is not necessary to send grades back for non-students.",
   "parms" => array( 
      "resource_link_id" => "rlid-777",
      "user_id" => "999999",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Instruct R. Person',
      "lis_person_name_given" => 'Instruct',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "instruct@school.edu",
      "lis_person_sourcedid" => "school.edu:instruct",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-777",
      "context_title" => "Introduction to Gambling",
      "context_label" => "GAM101",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"020" => array(
   "doc" => "Launch different resource in GAM101 as Learner 777777 providing grade service info.",
   "detail" => "Make sure to set a grade for this launch to pass the grade feature.",
   "result" => "A grade should appear in the gradebook context_id=con-777, rlid=rlid-777, user_id=777777 if the application sends grades based on learner action",
   "parms" => array( 
      "resource_link_id" => "rlid-777",
      "user_id" => "777777",
      "roles" => "Learner",  // or Learner
      "lis_person_name_full" => 'Luck Y. Seven',
      "lis_person_name_given" => 'Luck',
      "lis_person_name_family" => 'Seven',
      "lis_person_contact_email_primary" => "seven@school.edu",
      "lis_person_sourcedid" => "school.edu:seven",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-777",
      "context_title" => "Introduction to Gambling",
      "context_label" => "GAM101",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"021" => array(
   "doc" => "Re-Launch in GAM101 as Instructor 999999 in second resource providing grade service info.",
   "detail" => "Make sure to set a grade for this launch to pass the grade feature.",
   "result" => "A grade should appear in the gradebook context_id=con-777, rlid=rlid-777, user_id=777777 if the application sends grades based on instructor action",
   "parms" => array( 
      "resource_link_id" => "rlid-777",
      "user_id" => "999999",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Instruct R. Person',
      "lis_person_name_given" => 'Instruct',
      "lis_person_name_family" => 'Person',
      "lis_person_contact_email_primary" => "instruct@school.edu",
      "lis_person_sourcedid" => "school.edu:instruct",
      "lis_outcome_service_url" => "replaceme",
      "context_id" => "con-777",
      "context_title" => "Introduction to Gambling",
      "context_label" => "GAM101",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

// Minimal features - Slowly remove information
"090" => array(
   "doc" => "Launch in SI502 as Instructor",
   "detail" => "This is a instructor user going into a new resource/context. ".
               "This starts a series of tests that provide less and less ".
               "information until we get to the minimum information.  In ".
               "each test, we change the user_id.",
   "result" => "Should have Instructor powers to edit as appropriate",
   "parms" => array( 
      "resource_link_id" => "rli-3000",
      "user_id" => "user-3000",
      "roles" => "Instructor", 
      "lis_person_name_full" => 'User3000 Q. Lastname',
      "lis_person_contact_email_primary" => "User3000@school.edu",
      "context_id" => "con-502",
      "context_title" => "Networked Computing",
      "context_label" => "SI502",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"091" => array(
   "doc" => "Launch in SI502 as Instructor (remove name/email)",
   "detail" => "This is the same as 020 except we remove name/email",
   "result" => "Should have Instructor powers to edit as appropriate",
   "parms" => array( 
      "resource_link_id" => "rli-3000",
      "user_id" => "user-3001",
      "roles" => "Instructor",  
      "context_id" => "con-502",
      "context_title" => "Networked Computing",
      "context_label" => "SI502",
      "context_type" => "CourseSection",
      "launch_presentation_locale" => "en_US",
   )
),

"092" => array(
   "doc" => "Launch in SI502 as Instructor (remove context info)",
   "detail" => "This removes all the context information except context_id",
   "result" => "Should have Instructor powers to edit as appropriate",
   "parms" => array( 
      "resource_link_id" => "rli-3000",
      "user_id" => "user-3002",
      "roles" => "Instructor",  
      "context_id" => "con-502",
      "launch_presentation_locale" => "en_US",
   )
),

"093" => array(
   "doc" => "Launch as Instructor with no context_id",
   "detail" => "This has no context information.",
   "result" => "The tool should do something reasonable.  This may cause the tool to indicate an error and transfer back to the LMS.",
   "parms" => array( 
      "resource_link_id" => "rli-3000",
      "user_id" => "user-3003",
      "roles" => "Instructor",  
      "launch_presentation_locale" => "en_US",
   )
),

"094" => array(
   "doc" => "Launch SI502 with no roles",
   "detail" => "This removes the roles but keeps context_id",
   "result" => "The user should get the lowest level of privilege.",
   "parms" => array( 
      "resource_link_id" => "rli-3000",
      "user_id" => "user-3004",
      "context_id" => "con-502",
      "launch_presentation_locale" => "en_US",
   )
),

"095" => array(
   "doc" => "Launch with no roles and no context",
   "detail" => "This only has a resource_link_id, user_id, and locale",
   "result" => "The tool should do something reasonable.",
   "parms" => array( 
      "resource_link_id" => "rli-3000",
      "user_id" => "user-3005",
      "launch_presentation_locale" => "en_US",
   )
),

"096" => array(
   "doc" => "Launch with only resource_link_id",
   "detail" => "This is the absolute minimum launch.",
   "result" => "The tool should do something reasonable (i.e. have a decent error message).",
   "parms" => array( 
      "resource_link_id" => "rli-3000",
   )
),

// A Completely Broken launch
"099" => array(
   "doc" => "Send a completely broken launch.",
   "detail" => "Send a completely broken launch.",
   "result" => "This should trigger a call back to the launch_presentation_return_url if the Tool supports it.",
   "parms" => array( 
      "resourcelinkid" => "rli-3000",
   )
),

);
ksort($tool_tests);

$valid_post = Array(
"lti_message_type",
"lti_version",
"resource_link_id",
"resource_link_title",
"resource_link_description",
"user_id",
"user_image",
"roles",
"lis_person_name_given",
"lis_person_name_family",
"lis_person_name_full",
"lis_person_contact_email_primary",
"lis_result_sourcedid",
"lis_outcome_service_url",
"tool_consumer_info_product_family_code",
"tool_consumer_info_version",
"context_id",
"context_type",
"context_title",
"context_label",
"launch_presentation_locale",
"launch_presentation_document_target",
"launch_presentation_width",
"launch_presentation_height",
"launch_presentation_return_url",
"launch_presentation_css_url",
"tool_consumer_instance_guid",
"tool_consumer_instance_name",
"tool_consumer_instance_description",
"tool_consumer_instance_url",
"tool_consumer_instance_contact_email",
"oauth_consumer_key",
"oauth_signature_method",
"oauth_timestamp",
"oauth_nonce",
"oauth_version",
"oauth_signature",
"oauth_callback",
"lis_person_sourcedid",
"lis_course_offering_sourcedid",
"lis_course_section_sourcedid",
"lis_result_sourcedid",
"basiclti_submit"    // Just to be nice :)
);

$valid_roles = Array(
"urn:lti:role:ims/lis/Learner",
"urn:lti:role:ims/lis/Learner/Learner",
"urn:lti:role:ims/lis/Learner/NonCreditLearner",
"urn:lti:role:ims/lis/Learner/GuestLearner",
"urn:lti:role:ims/lis/Learner/ExternalLearner",
"urn:lti:role:ims/lis/Learner/Instructor",
"urn:lti:role:ims/lis/Instructor",
"urn:lti:role:ims/lis/Instructor/PrimaryInstructor",
"urn:lti:role:ims/lis/Instructor/Lecturer",
"urn:lti:role:ims/lis/Instructor/GuestInstructor",
"urn:lti:role:ims/lis/Instructor/ExternalInstructor",
"urn:lti:role:ims/lis/ContentDeveloper",
"urn:lti:role:ims/lis/ContentDeveloper/ContentDeveloper",
"urn:lti:role:ims/lis/ContentDeveloper/Librarian",
"urn:lti:role:ims/lis/ContentDeveloper/ContentExpert",
"urn:lti:role:ims/lis/ContentDeveloper/ExternalContentExpert",
"urn:lti:role:ims/lis/Member",
"urn:lti:role:ims/lis/Member/Member",
"urn:lti:role:ims/lis/Manager",
"urn:lti:role:ims/lis/Manager/AreaManager",
"urn:lti:role:ims/lis/Manager/CourseCoordinator",
"urn:lti:role:ims/lis/Manager/Observer",
"urn:lti:role:ims/lis/Manager/ExternalObserver",
"urn:lti:role:ims/lis/Mentor",
"urn:lti:role:ims/lis/Mentor/Mentor",
"urn:lti:role:ims/lis/Mentor/Reviewer",
"urn:lti:role:ims/lis/Mentor/Advisor",
"urn:lti:role:ims/lis/Mentor/Auditor",
"urn:lti:role:ims/lis/Mentor/Tutor",
"urn:lti:role:ims/lis/Mentor/LearningFacilitator",
"urn:lti:role:ims/lis/Mentor/ExternalMentor",
"urn:lti:role:ims/lis/Mentor/ExternalReviewer",
"urn:lti:role:ims/lis/Mentor/ExternalAdvisor",
"urn:lti:role:ims/lis/Mentor/ExternalAuditor",
"urn:lti:role:ims/lis/Mentor/ExternalTutor",
"urn:lti:role:ims/lis/Mentor/ExternalLearningFacilitator",
"urn:lti:role:ims/lis/Administrator",
"urn:lti:role:ims/lis/Administrator/Administrator",
"urn:lti:role:ims/lis/Administrator/Support",
"urn:lti:role:ims/lis/Administrator/ExternalDeveloper",
"urn:lti:role:ims/lis/Administrator/SystemAdministrator",
"urn:lti:role:ims/lis/Administrator/ExternalSystemAdministrator",
"urn:lti:role:ims/lis/Administrator/ExternalDeveloper",
"urn:lti:role:ims/lis/Administrator/ExternalSupport",
"urn:lti:role:ims/lis/TeachingAssistant",
"urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistant",
"urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantSection",
"urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantSectionAssociation",
"urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantOffering",
"urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantTemplate",
"urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantGroup",
"urn:lti:role:ims/lis/TeachingAssistant/Grader",
"urn:lti:sysrole:ims/lis/SysAdmin",
"urn:lti:sysrole:ims/lis/SysSupport",
"urn:lti:sysrole:ims/lis/Creator",
"urn:lti:sysrole:ims/lis/AccountAdmin",
"urn:lti:sysrole:ims/lis/User",
"urn:lti:sysrole:ims/lis/Administrator",
"urn:lti:sysrole:ims/lis/None",
"urn:lti:instrole:ims/lis/Student",
"urn:lti:instrole:ims/lis/Faculty",
"urn:lti:instrole:ims/lis/Member",
"urn:lti:instrole:ims/lis/Learner",
"urn:lti:instrole:ims/lis/Instructor",
"urn:lti:instrole:ims/lis/Mentor",
"urn:lti:instrole:ims/lis/Staff",
"urn:lti:instrole:ims/lis/Alumni",
"urn:lti:instrole:ims/lis/ProspectiveStudent",
"urn:lti:instrole:ims/lis/Guest",
"urn:lti:instrole:ims/lis/Other",
"urn:lti:instrole:ims/lis/Administrator",
"urn:lti:instrole:ims/lis/Observer",
"urn:lti:instrole:ims/lis/None",
);

$valid_types = array(
"urn:lti:context-type:ims/lis/CourseTemplate",
"urn:lti:context-type:ims/lis/CourseOffering",
"urn:lti:context-type:ims/lis/CourseSection",
"urn:lti:context-type:ims/lis/Group",
);

/*
urn:lti:sysrole:ims/lis/SysAdmin
urn:lti:instrole:ims/lis/Staff
urn:lti:role:ims/lis/Learner   (Context Role)
Learner                        (Assume Context Role)
*/

// Breaks roles by comman and removes whitespace
function split_string($rolestr) {
    $x = explode(",",$rolestr);
    $pieces = array(); 
    foreach ($x as $key => $value) {
        $pieces[$key] = trim($value);
    }
    return $pieces;
}

function instructor_or_learner($rolestr) {
    $pieces = split_string($rolestr);
    foreach ($pieces as $key => $value) {
        if ( strpos($value, 'Instructor' ) === 0 ) return 'Instructor';
        if ( strpos($value, 'urn:lti:role:ims/lis/Instructor' ) === 0 ) return 'Instructor';
    }
    foreach ($pieces as $key => $value) {
        if ( strpos($value, 'Learner' ) === 0 ) return 'Learner';
        if ( strpos($value, 'urn:lti:role:ims/lis/Learner' ) === 0 ) return 'Learner';
    }
    return false;
}
// Tests
// print_r(instructor_or_learner("Learner, Instructor ,urn:ok:non:standard/Role, Fred,urn:lti:role:ims/lis/Learner"));
// print_r(instructor_or_learner("Instructor,urn:ok:non:standard/Role,urn:lti:role:ims/lis/Learner"));
// print_r(instructor_or_learner("urn:ok:non:standard/Role,urn:lti:role:ims/lis/Learner"));
// print_r(instructor_or_learner("ContentDeveloper"));

// Returns an array of errors - no errors returns a zero length array
function check_roles($rolestr) {
    global $valid_roles;
    $pieces = split_string($rolestr);
    $notes = array();
    $stdrole = 0;
    foreach ($pieces as $key => $value) {
        if ( in_array($value, $valid_roles) or 
             in_array('urn:lti:role:ims/lis/'.$value, $valid_roles) ) {
            $stdrole = $stdrole + 1;
            // print "Good ".$value."\n";
        } else if ( strpos($value, "urn:" ) === 0 ) {
            // print "OK WITH URN ".$value."\n";
        } else {
            // print "Bad ".$value."\n";
            $notes[$value] = "Non-standard roles must be fully-qualified urns";
        }
    }
    if ($stdrole == 0 ) {
       $notes[] = "Must include at least one standard role";
    }
    return $notes;
}
// Tests
// print_r(check_roles("Learner, Instructor ,urn:ok:non:standard/Role, Fred,urn:lti:role:ims/lis/Learner"));
// print_r(check_roles("urn:not:ok:needs:one:standard/Role"));
// print_r(check_roles("ContentDeveloper"));

// Returns an array of errors - no errors returns a zero length array
function check_types($typestr) {
    global $valid_types;
    $pieces = split_string($typestr);
    $notes = array();
    $stdtype = 0;
    foreach ($pieces as $key => $value) {
        if ( in_array($value, $valid_types) or 
             in_array('urn:lti:context-type:ims/lis/'.$value, $valid_types) ) {
            $stdtype = $stdtype + 1;
            // print "Good ".$value."\n";
        } else if ( strpos($value, "urn:" ) === 0 ) {
            // print "OK WITH URN ".$value."\n";
        } else {
            // print "Bad ".$value."\n";
            $notes[$value] = "Non-standard types must be fully-qualified urns";
        }
    }
    if ($stdtype == 0 ) {
       $notes[] = "Must include at least one standard type";
    }
    return $notes;
}

// Returns an array of POST parameters that are not allowed
function check_post(){
    global $valid_post;
    $badpost = Array();
    foreach ($_POST as $key => $value) {
       if ( $key == 'x' ) continue;  // In case they sneak in...
       if ( $key == 'y' ) continue;  // In case they sneak in...
       if ( strpos($key,'custom_') === 0 ) continue;
       if ( strpos($key,'ext_') === 0 ) continue;
       if ( in_array($key, $valid_post) ) continue;
       $badpost[] = $key; 
    }
    return $badpost;
}

// Reset the session retaining the official value
function lti_reset_session() {
    $official = $_SESSION['ims:official'];
    session_unset();
    if ( strlen($official) > 0 ) $_SESSION['ims:official'] = $official;
}

// Tests
// print_r(check_types("ContentDeveloper"));
// print_r(check_types("CourseSection"));
// print_r(check_types("urn:ok:non:standard/Type,urn:lti:context-type:ims/lis/CourseSection"));


?>
