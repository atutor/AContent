<?php
   session_start();
   include "../common/header.php";
   require "official.php";
?>
<h1>IMS LTI 1.1 Tool Consumer Certification</h1>
<p>Quick Link: 
<a href="http://www.imsglobal.org/developers/alliance/LTI/cert-v1p1/">Certification Overview</a> |
<a href="lmssetup.php">Consumer Setup</a> |
<a href="tooldetail.php">Provider Test</a> | 
<a href="http://www.imsglobal.org/cc/statuschart.cfm" target="_new">Certified Products</a> 
</p>
<p>
Welcome to IMS LTI Tool Consumer Certification Testing.  The goal of the IMS 
LTI Certification is to encourage interoperable implementations of both 
LMS Systems or LMS Extensions (LTI Tool Consumers) 
and External Tools (LTI Tool Providers).
<p>
The certification
is for a particular version of a Tool Consumer
and the certification must
be re-done for each new release of the software. 
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
External Tools so it requires more than 
the specification.  Gaining this certification
is expected to be more difficult than simply meeting
the minimal requirements of the LTI
launch protocol.
</p>
<h1>Submitting Your Request for Certification</h1>
<p>
Begin by downloading the
<a href="imslti-consumer-cert-v1p1.doc">Certification Report</a>.  This
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
There are three types of tests in the certification:
<ul>
<li><b>Normal</b> tests are tests that your code must pass to be certified.</li>
<li><b>Free Pass</b> tests are optional and depend on design choices in the LMS.
Your LMS does not have to pass these tests, but there is an indication when an LMS
does pass these tests.
</li>
</li>
<li><b>Fail Only</b> tests check for mistakes in every one of your launches.  
These tests are marked "Fail" if <i>any</i> of your tests cause a fail condition.
Once you fail a "Fail Only" test, passing it later does not clear the 
"Fail" status.  You must reset and start over using the test setup utility.
</li>
</ul>
</p>
<p>
So to pass the certification, you must pass all the normal tests and 
as many of the "Free Pass" tests as you can without failing any of the 
"Fail Only" tests.
</p>
<p>
You can look at the 
<a href="lmsdoc.php">Detailed Test List</a> 
to see information about the individual tests.</p>
<h1>Certification Outline</h1>
<p>
The certification is designed to be relatively lightweight and should
be easily completed in 30 minutes or less, once the LMS has all the 
features needed to pass the test.  The first certification may take
somewhat longer as it may expose bugs, missing features, 
or interoperability issues that will take some time to figure out 
and address.
</p>
<p>
However once the first certification is done, we expect recertification
to take on the order of 10 minutes of time.  The hope is that this
certification suite is useful enough and easy enough to use to 
become part of the Quality Assurance processes for the LMS systems.
</p>
<p>
The overall goal is to get all of the tests to pass (Green or Blue).
This section describes how to set up an instance of your LMS to 
pass the tests most quickly.
<p>
The test stores all 
of its intermediate data in session so you need to use multiple
windows of the same browser and your browser must allow cookies 
to be used across multiple windows (typically this is default).
And you need to allow JavaScript as well.
<ul>
<li>Bring up an instance of your LMS and install your
LMS-wide <b>oauth_consumer_key</b> and <b>secret</b> if this feature
is supported.  If your LMS does not support an LMS-wide password
you will need to enter the key and password in each of the launch
instances so just choose and remember the key and password.  

<li>Create two user accounts in your system one will be an instructor
and one will be a student.

<li>Create two courses in your system and add both users to both 
sites.

<li>Run the <a href="lmssetup.php">Test Setup</a> tool
and enter the software name, version, oauth_consumer_key, and secret
and set them in your browser's session.  From this point forward,
you cannot close all browser windows or you will need to restart 
your testing.
<li>Navigate to the <a href="lmsstatus.php">Test Status</a>
page.  You should see most of the tests marked as "ToDo" with a 
few of the "Free Pass" or "Fail Only" tests marked as "OK".

<li>Add an instance of LTI to each of your sites.  If your LMS
supports multiple LTI placements in a site, add a 
second placement to one of the sites making a total of three placements.  
The URL, key, secret, and custom fields to use are shown at the bottom
of the setup and status screens.

<li>
Now you might want to have your LMS in one tab and the
Status page in another tab.  In your
LMS, log in as one user and do all of the launches and then 
log in as another user and do all of the launches.   Each time
you do a launch, it shows you which tests were passed 
and which tests failed for that particular test.
<p>
You can keep pressing "refresh" the status display to see overall 
test progress.
If all goes well, after both users have launched each
of the placements from each of the sites, you should
have nearly all of the tests passed except the privacy
tests.
<p>
There is debugging information in each of the test outputs
and there is even more debugging information if you view
source of the frame with the test output.
<li>
The privacy tests was to make sure that all combinations
of (name / email) can be sent from the LMS.  The certification
wants the following combinations: (1) neither name nor email
address, (2) name only, (3) email address only, and (4)
both name and email address.  The quick way to pass these tests
is just to be the instructor and edit the privacy 
settings and re-launch repeatedly.  But make sure that the
user account(s) you use for the privacy testing 
have a name and email address in the LMS.

<li>
Feel free to raise issues with Certification or discuss
experiences with 
the rest of the IMS Developer Community in the
<a href="http://www.imsglobal.org/community/forum/index.cfm?forumid=7">
Alliance Developer Forum</a>.

<li>At some point, you have done enough launches so that 
all of the tests are green or blue / OK or Passed.
There cannot be any yellow or red / ToDo or Failed
tests.

</ul>
<?php
   include "../common/footer.php";
?>
