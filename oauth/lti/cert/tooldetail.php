<?php
   session_start();
   include "../common/header.php";
   require "official.php";
?>
<h1>IMS LTI 1.1 Tool Provider Certification</h1>
<p>Quick Links:
<a href="http://www.imsglobal.org/developers/alliance/LTI/cert-v1p1/">Certification Overview</a> |
<a href="toolsetup.php">Provider Setup</a> |
<a href="lmsdetail.php">Consumer Test</a> |
<a href="http://www.imsglobal.org/cc/statuschart.cfm" target="_new">Certified Products</a>
</p>
<p>
Welcome to IMS LTI Tool Provider Certification Testing.  The goal of the IMS
LTI Certification is to encourage interoperable implementations of both
LMS Systems or LMS Extensions (LTI Tool Consumers)
and External Tool (LTI Tool Providers).
</p>
<p>
The certification
is for a particular version of a Tool Provider
and the certification must
be re-done for each new release of the software.
</p>
<p>
This certification is a self-assessment where the software vendor
or their designee runs the tests, produces a report of the results
including explanations of any anomalies, and then sends the
report to IMS.  Then IMS reviews the
report and issues the certification and adds the Tool (and
version) to the list of certified products on the IMS
web site.
</p>
<p>
This certification test demands features and capabilities
beyond those which are <i>strictly required</i> by
the IMS
LTI specification.  The specification is intentionally left
very flexible to allow it to be used for many purposes.
This certification is particularly aimed at maximizing
interoperability between LMS systems and their
External Tools so it requires much more than
the specification.  Gaining this certification
is expected to be more difficult than simply meeting
the minimal requirements of the LTI
launch protocol.
</p>
<h1>Submitting Your Request for Certification</h1>
<p>
Begin by downloading the
<a href="imslti-provider-cert-v1p1.doc">Certification Report</a>.  This
document must be filled out and submitted to
<a href="mailto:conformance@imsglobal.org">conformance@imsglobal.org</a>.
</p>
<p>
By filling out this report and submitting it, you are agreeing that
these test results are an accurate representation of a properly executed
certification test and that this document is a true and accurate
representation of the results of that test.
</p>
<h1>Testing Approach</h1>
<p>
The test suite takes the launch url, key and secret for the tool
and then quickly runs through a battery of launches with
different parameters. The primary goals of the tests are:
</p>
<ul>
<li>Explore the dimensions of privacy settings so the tool does not
assume that it gets all user-identifiable data all of the time.</li>
<li>Explore the various formats of the <b>roles=</b> string.  While
the simple case if <b>roles=Learner</b> or <b>roles=Instructor</b>,
the parameter is actually a list of urns or handles as follows:
<pre>
urn:lti:role:ims/lis/Instructor
urn:non:ims/something/Else,Instructor,urn:lti:instrole:ims/lis/Alumni
urn:non:ims/something/Else,urn:lti:role:ims/lis/Instructor,urn:lti:instrole:ims/lis/Alumni
urn:non:ims/something/Else,urn:lti:role:ims/lis/Learner,urn:lti:instrole:ims/lis/Alumni
</pre>
These tests make sure that the Tool Provider has compliant and robust
roles string parsing.
</li>
<li>Send some dysfunctional launches with a
<b>launch_presentation_return_url</b> to see if the Tool Provider
calls back the LMS in the case of an error.  Calling the LMS
back on error is an <i>optional</i> feature.
</li>
</ul>
<p>
There is a lot of manual verification on the part of the
Tool Provider vendor - so the precise details of
what it takes to be certified are still up in the air.
However, until those details are worked out - the
test is a good unit test for any Tool Provider.
</p>
<p>
You can look at the
<a href="tooldoc.php">Detailed Test List</a>
to see information about the individual tests.</p>
<h1>Test Outline</h1>
<p>
The test stores all
of its intermediate data in session so you need to use multiple
windows of the same browser and your browser must allow cookies
to be used across multiple windows (typically this is default).
And you need to allow JavaScript as well.
</p>
<ul>
<li>Bring up an instance of your Tool and get a
launch url, <b>oauth_consumer_key</b> and secret.
</li>
<li>Run the <a href="toolsetup.php">Tool Setup</a> tool
and enter the software name, version, launch url, oauth_consumer_key,
and secret and set them in your browser's session.
From this point forward, you cannot close all browser windows
or you will need to restart
your testing.</li>
<li>Navigate to the <a href="toolstatus.php">Test Status</a>
page.  All the tests will be "ToDo" until you mark them as passsed.</li>

<li>Navigate to the <a href="toolcert.cfm">Start Test</a>
page.  You can go back and forth between the tests.
The test is run in an iframe.
If you turn on <b>Debug mode</b> it pauses before sending the launch
data to your tool so you can look at exactly what is being sent.
You can press the button in the iframe to submit the
form data and do the actual launch.
</li>

<li>
The hard bit will be to verify that the Tool
is properly functioning.   As such you as the tester
get to indicate the Pass/Fail for each test - there
is no automated determination of Pass/Fail.
</li>

<li>The last few tests do broken launches and
give you an opportunity to transfer the user back to the
LMS on error instead of simply putting up an error message.
Make sure to check to see if the
<b>launch_presentation_return_url</b> is present and deal
with the lack of this optional parameter when it is missing.
</li>

<li>Feel free to raise issues with Certification or discuss
experiences with the rest of the IMS Developer Community in the
<a href="http://www.imsglobal.org/community/forum/index.cfm?forumid=7">
Alliance Developer Forum</a>.
</li>
</ul>
<?php
   include "../common/footer.php";
?>
