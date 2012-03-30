<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');

include(TR_INCLUDE_PATH.'vitals.inc.php');
include(TR_INCLUDE_PATH.'header.inc.php');
?>
<div class="output-form" style="line-height:150%">

<h1>AContent Web Service API</h1>
<p>Access AContent from remote applications through its web service API. This is version 0.1, dated Jun 2010.</p>
<p>AContent provides a search API that allows users to send search requests to AContent. AContent returns results in REST format. AContent also has an 
<a href="<?php echo TR_BASE_HREF;?>documentation/oauth_server_api.php">OAuth web service API</a> for viewing, downloading, importing and 
exporting content from remote applications. </p>

<h2 id="TableOfContents">Table of Contents</h2>

    <div id="toc">
      <ul>
        <li><a href="<?php echo TR_BASE_HREF.'documentation/web_service_api.php'; ?>#search">Search Web Service</a>
          <ul>
            <li><a href="<?php echo TR_BASE_HREF.'documentation/web_service_api.php'; ?>#request_format">Search request format</a></li>
            <li><a href="<?php echo TR_BASE_HREF.'documentation/web_service_api.php'; ?>#response_format">Search response format</a></li>
          </ul>
        </li>
        <li><a href="<?php echo TR_BASE_HREF.'documentation/web_service_api.php'; ?>#urls">URLs to view, download, import, export course</a>
          <ul>
            <li><a href="<?php echo TR_BASE_HREF.'documentation/web_service_api.php'; ?>#view">View course</a></li>
            <li><a href="<?php echo TR_BASE_HREF.'documentation/web_service_api.php'; ?>#download">Download course</a></li>
            <li><a href="<?php echo TR_BASE_HREF.'documentation/web_service_api.php'; ?>#import">Import course into AContent</a></li>
          </ul>
        </li>
      </ul>
    </div>

<div id="search">
<h2 id="request_format">Search request format</h2>

<p>Below is a table of the parameters you can use to send a request to AContent to search through the repository
by keywords. AContent returns the matching results in REST format.</p>

<p>URL:<br />
<kbd><?php echo TR_BASE_HREF; ?>search.php</kbd> <br />
(replace with the address of your own server if you want to call a private instance of AContent)</p>

<table class="data" rules="all">
<tbody><tr>
<th>Parameter</th><th>Description</th><th>Default value</th>
</tr>

<tr>
  <th>id</th>
  <td>The "Web Service ID" generated once successfully registering into AContent. 
  This ID is a 32 characters long string. It can always be found on your "Profile" page.</td>
  <td>None, must be given.</td>
</tr>

<tr>
  <th>keywords</th>
  <td>The keywords to search for.</td>
  <td>None, must be given.</td>
</tr>

<tr>
  <th>start</th>
  <td>Start receiving from this record number</td>
  <td>0. Optional</td>
</tr>

<tr>
  <th>maxResults</th>
  <td>Number of results to return.</td>
  <td>10. Optional</td>
</tr>
</tbody></table>
<br />

<span style="font-weight: bold">Sample search request</span>
<p><?php echo TR_BASE_HREF; ?>search.php?id=fb170575596b4a5b52a87da39ef59586&keywords=atutor+all&start=10&maxResults=25</p>
<p>Goal: Search for keywords "atutor all". Return search results from record #10 and maximum 25 results returned.</p>

<h2 id="response_format">Search response format</h2><br/>
<span style="font-weight:bold">Success REST Response</span>
<p>A REST success response for the validation of a document (invalid) will look like this:</p>

<pre style="background-color:#F7F3ED;"> 
&lt;?xml version=&quot;1.0&quot; encoding=&quot;ISO-8859-1&quot;?&gt;
&lt;resultset&gt;
  &lt;summary&gt;
    &lt;numOfTotalResults&gt;11&lt;/numOfTotalResults&gt;

    &lt;numOfResults&gt;3&lt;/numOfResults&gt;
    &lt;lastResultNumber&gt;3&lt;/lastResultNumber&gt;
  &lt;/summary&gt;

  &lt;results&gt;

    &lt;result&gt;
      &lt;courseID&gt;3&lt;/courseID&gt;
      &lt;title&gt;another import - welcome to atutor11sdf&lt;/title&gt;
      &lt;description&gt;&lt;/description&gt;

      &lt;creationDate&gt;2010-01-13 13:51:53&lt;/creationDate&gt;
    &lt;/result&gt; 
    &lt;result&gt;

      &lt;courseID&gt;4&lt;/courseID&gt;
      &lt;title&gt;an introduction to data and information&lt;/title&gt;

      &lt;description&gt;this work is licensed under a creative commons attribution-noncommercial-sharealike 2.0 http://creativecommons.org/licenses/by-nc-sa/2.0/uk/&lt;/description&gt;
      &lt;creationDate&gt;2010-01-13 13:52:07&lt;/creationDate&gt;
    &lt;/result&gt; 
    &lt;result&gt;
      &lt;courseID&gt;7&lt;/courseID&gt;

      &lt;title&gt;atutor howto 1.4.3&lt;/title&gt;
      &lt;description&gt;documentation for students, instructors, and administrators.&lt;/description&gt;
      &lt;creationDate&gt;2010-02-02 12:07:43&lt;/creationDate&gt;
    &lt;/result&gt; 

  &lt;/results&gt;

&lt;/resultset&gt;
</pre>

<br />
<span style="font-weight:bold">Error Response</span>

<pre style="background-color:#F7F3ED;"> 
&lt;?xml version=&quot;1.0&quot; encoding=&quot;ISO-8859-1&quot;?&gt;
&lt;errors&gt;
  &lt;totalCount&gt;1&lt;/totalCount&gt;
  &lt;error code=&quot;401&quot;&gt;

    &lt;message&gt;Empty keywords&lt;/message&gt;
  &lt;/error&gt;

&lt;/errors&gt;
</pre>

</div>

<div id="urls">
<h2 id="view">View course URL</h2>
<kbd><?php echo TR_BASE_HREF;?>home/course/index.php?_course_id=10</kbd><br />
<p>_course_id is the "courseID" element returned in the REST search result</p>
<p>This URL leads to the first content page of the lesson.</p>

<h2 id="download">Download course URL</h2>
<kbd><?php echo TR_BASE_HREF;?>home/imscc/ims_export.php?course_id=10</kbd><br />
<p>course_id is the "courseID" element returned in the REST search result</p>
<p>This URL returns a zipped common cartridge of the lesson.</p>
</div>

<h2 id="import">Import common cartridge or content package into AContent</h2>
<p>An AContent user ID with author privilege is required for this action. If you
don't have an AContent ID yet, <a href="<?php echo TR_BASE_HREF;?>register.php">register</a>.</p> 

<p>After an AContent user ID has been created, the first step is to go through the
<a href="<?php echo TR_BASE_HREF;?>documentation/oauth_server_api.php">
OAuth authentication process</a> to get an OAuth access token. The 
authentication process prompts users to login with an AContent user ID, after which an
access token is returned. This access 
token must be sent along with the import request as a user privilege check. The access
token can be used repeatedly until its expiration. After the
user is confirmed as an valid author, the common cartridge or content package is retrieved
from the request "url" parameter and imported as a new AContent lesson. The 
ID of the newly-imported lesson is returned at success.</p>

<kbd><?php echo TR_BASE_HREF;?>home/ims/ims_import.php?oauth_token=xxx&url=xxx</kbd><br />

<p>oauth_token is the valid access token returned by an AContent OAuth server.<br />
url points to a zip file of a common cartridge or a content package.<br />
Refer to <a href="<?php echo TR_BASE_HREF;?>documentation/oauth_server_api.php#import">
Importing Common Cartridges or Content Packages into AContent via OAuth</a> for detailed 
request parameters and success/fail responses.</p>

</div>
<?php include(TR_INCLUDE_PATH.'footer.inc.php'); ?>
