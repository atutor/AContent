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

if (!defined('TR_INCLUDE_PATH')) { exit; }

global $db;

define('TR_INCLUDE_PATH', '../include/');
include_once(TR_INCLUDE_PATH.'../home/classes/ContentUtility.class.php');

$body_text 	= htmlspecialchars(stripslashes($_POST['body_text']));
$body		= html_entity_decode($body_text);

require(TR_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
require(TR_INCLUDE_PATH.'../home/imscc/include/ims_template.inc.php');				/* for ims templates + print_organizations() */

/*
the following resources are to be identified:
even if some of these can't be images, they can still be files in the content dir.
theoretically the only urls we wouldn't deal with would be for a <!DOCTYPE and <form>

img		=> src
a		=> href				// ignore if href doesn't exist (ie. <a name>)
object	=> data | classid	// probably only want data
applet	=> classid | archive			// whatever these two are should double check to see if it's a valid file (not a dir)
script	=> src
input	=> src
iframe	=> src
*/
	
class MyHandler {
 	function MyHandler(){}
	function openHandler(& $parser,$name,$attrs) {
		global $my_files;

		$name = strtolower($name);
		$attrs = array_change_key_case($attrs, CASE_LOWER);

		$elements = array(	'img'		=> 'src',
							'a'			=> 'href',				
							'object'	=> array('data', 'classid'),
							'applet'	=> array('classid', 'archive'),
							'script'	=> 'src',
							'input'		=> 'src',
							'iframe'	=> 'src',
							'embed'		=> 'src',
							);

		/* check if this attribute specifies the files in different ways: (ie. java) */
		if (is_array($elements[$name])) {
			$items = $elements[$name];
	
			foreach ($items as $item) {
				if ($attrs[$item] != '') {

					/* some attributes allow a listing of files to include seperated by commas (ie. applet->archive). */
					if (strpos($attrs[$item], ',') !== false) {
						$files = explode(',', $attrs[$item]);
						foreach ($files as $file) {
							$my_files[] = trim($file);
						}
					} else {
						$my_files[] = $attrs[$item];
					}
				}
			}	
		} else if (isset($elements[$name]) && ($attrs[$elements[$name]] != '')) {
			/* we know exactly which attribute contains the reference to the file. */
			$my_files[] = $attrs[$elements[$name]];
		}
	}
   	function closeHandler(& $parser,$name) { }
	}

/* get all the content */
$handler=new MyHandler();
$parser = new XML_HTMLSax();
$parser->set_object($handler);
$parser->set_element_handler('openHandler','closeHandler');

/* generate the resources and save the HTML files */
			
global $parser, $my_files;
global $course_id;

/* add the resource dependancies */
$my_files 		= array();
$content_files 	= "\n";

//in order to control if some [media] is in the body_text
//$body = ContentUtility::embedMedia($body_t);

$parser->parse($body);

// find all [media] resources
preg_match_all("/\[media[0-9a-z\|]*\](.*)\[\/media\]/i",$body,$media_matches);
$my_files = array_merge($media_matches[1], $my_files);

// add by Cindy Li. 
// This resolves the problem introduced by [media] tag: when [media] is 
// parsed into <object>, same resource appears a few times in <object> with different 
// format to cater for different browsers or players. This way creates problem that different
// formats in <object> are all parsed and considered as different resource. array_unique()
// call solves this problem. But, it introduces the new problem that when a same resource
// appears at different places in the content and users do want to have them with different
// alternatives. With this solution, this same resource only shows up once at "adapt content"
// and only can have one alternative associated with. Table and scripts need to be re-designed
// to solve this problem, for example, include line number in table. 
$my_files = array_unique($my_files);

/* handle @import */
$import_files 	= get_import_files($body);
			
if (count($import_files) > 0) $my_files = array_merge($my_files, $import_files);

$i=0;

foreach ($my_files as $file) {
	/* filter out full urls */
	$url_parts = @parse_url($file);
//	if (isset($url_parts['scheme']) && substr($file, 0, strlen(TR_BASE_HREF)) != TR_BASE_HREF) {
//		continue;
//	}

	/* file should be relative to content. let's double check */
	if ((substr($file, 0, 1) == '/')) {
		continue;
	}
	
    // The URL of the movie from youtube.com has been converted above in embed_media().
	// For example:  http://www.youtube.com/watch?v=a0ryB0m0MiM is converted to
	// http://www.youtube.com/v/a0ryB0m0MiM to make it playable. This creates the problem
	// that the parsed-out url (http://www.youtube.com/v/a0ryB0m0MiM) does not match with
	// the URL saved in content table (http://www.youtube.com/watch?v=a0ryB0m0MiM).
	// The code below is to convert the URL back to original.
	$file = preg_replace("/(http:\/\/[a-z0-9\.]*)?youtube.com\/v\/(.*)/",
	                     "\\1youtube.com/watch?v=\\2", $file);
	
	$resources[$i] = $file;
	$i++;
}
?>