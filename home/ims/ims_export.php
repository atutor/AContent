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

/** Commented by Cindy Li on Feb 2, 2010
 * Modified from ATutor mods/_core/imscp/ims_export.php, SVN revision 9126
 */

define('TR_INCLUDE_PATH', '../../include/');

if (isset($_REQUEST['to_tile']) && !isset($_POST['cancel'])) {
	/* for TILE */

	/* redirect to TILE import servlet */
	if (!authenticate(TR_PRIV_ADMIN, TR_PRIV_RETURN)) {
		/* user can't be authenticated */
		header('HTTP/1.1 404 Not Found');
		echo 'Document not found.';
		exit;
	}

	$m = md5(DB_PASSWORD . 'x' . ADMIN_PASSWORD . 'x' . $_SERVER['SERVER_ADDR'] . 'x' . $cid . 'x' . $_SESSION['course_id'] . 'x' . date('Ymd'));

	header('Location: '.TR_TILE_IMPORT. '?cp='.urlencode(TR_BASE_HREF. 'home/ims/ims_export.php?cid='.$cid.'&c='.$_SESSION['course_id'].'&m='.$m));
	exit;
} else if (isset($_GET['m'])) {
	/* for TILE */

	/* request (hopefully) coming from a TILE server, send the content package */

	$_user_location = 'public';
	require_once(TR_INCLUDE_PATH.'vitals.inc.php');
	$m = md5(DB_PASSWORD . 'x' . ADMIN_PASSWORD . 'x' . $_SERVER['SERVER_ADDR'] . 'x' . $cid . 'x' . $c . 'x' . date('Ymd'));
	if (($m != $_GET['m']) || !$c) {
		header('HTTP/1.1 404 Not Found');
		echo 'Document not found.';
		exit;
	}
	
	$course_id = $c;

} else {
	$use_a4a = false;
	if (isset($_REQUEST['to_a4a'])){
		$use_a4a = true;
	}
	require_once(TR_INCLUDE_PATH.'vitals.inc.php');
	global $_course_id, $_content_id;

	$_course_id = $course_id = (isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : $_course_id);
	$_content_id = $cid = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : $_content_id; /* content id of an optional chapter */
	$c   = isset($_REQUEST['c'])   ? intval($_REQUEST['c'])   : 0;
}

if ($course_id == 0 && $cid == 0)
{
	$msg->addError('MISSING_COURSE_ID');
	header('Location: ../index.php');
	exit;	
}

require_once(TR_INCLUDE_PATH.'../home/classes/ContentManager.class.php');  /* content management class */
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');

//load the following after vitals is included
require(TR_INCLUDE_PATH.'classes/testQuestions.class.php');
require(TR_INCLUDE_PATH.'classes/A4a/A4aExport.class.php');

$coursesDAO = new CoursesDAO();
$course_row = $coursesDAO->get($course_id);

$contentManager = new ContentManager($course_id);

$instructor_id   = $course_row['user_id'];
$course_desc     = htmlspecialchars($course_row['description'], ENT_QUOTES, 'UTF-8');
$course_title    = htmlspecialchars($course_row['title'], ENT_QUOTES, 'UTF-8');
$course_language = $course_row['primary_language'];

$courseLanguage = $languageManager->getLanguage($course_language);
//If course language cannot be found, use UTF-8 English
//@author harris, Oct 30,2008
if (!isset($courseLanguage) || $courseLanguage == null){
	$courseLanguage =& $languageManager->getLanguage(DEFAULT_LANGUAGE_CODE);
}

$course_language_charset = $courseLanguage->getCharacterSet();
$course_language_code = $courseLanguage->getCode();

require(TR_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
require(TR_INCLUDE_PATH.'classes/vcard.php');						/* for vcard */
require(TR_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
require(TR_INCLUDE_PATH.'../home/ims/include/ims_template.inc.php');/* for ims templates + print_organizations() */

if (isset($_POST['cancel'])) {
	$msg->addFeedback('EXPORT_CANCELLED');
	header('Location: ../index.php');
	exit;
}


$zipfile = new zipfile();
$zipfile->create_dir('resources/');

/*
	the following resources are to be identified:
	even if some of these can't be images, they can still be files in the content dir.
	theoretically the only urls we wouldn't deal with would be for a <!DOCTYPE and <form>

	img		=> src
	a		=> href				// ignore if href doesn't exist (ie. <a name>)
	object	=> data | classid	// probably only want data
	applet	=> classid | archive			// whatever these two are should double check to see if it's a valid file (not a dir)
	link	=> href
	script	=> src
	form	=> action
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
							'link'		=> 'href',
							'script'	=> 'src',
							'form'		=> 'action',
							'input'		=> 'src',
							'iframe'	=> 'src',
							'embed'		=> 'src',
							'param'		=> 'value');
	
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
$content = array();
$paths	 = array();
$top_content_parent_id = 0;

$handler=new MyHandler();
$parser = new XML_HTMLSax();
$parser->set_object($handler);
$parser->set_element_handler('openHandler','closeHandler');

$contentDAO = new ContentDAO();
$rows = $contentDAO->getContentByCourseID($course_id);



/***************************************
 * templates
 * add the layout, if present
 * donadiomauro@gmail.com
 * 
 * */
include_once(TR_INCLUDE_PATH . '../templates/system/Layout.class.php');

$templates_theme	= new Layout('');

// Array containing content and properties (such as content_id, course_id, layout ..)
// the 'layout' property is required to add the proper content into the manifest file
$rows				= $templates_theme->appendStyle($rows, $zipfile, $_content_id);

/***************************************/

//if (authenticate(TR_PRIV_CONTENT, TR_PRIV_RETURN)) {
//	$sql = "SELECT *, UNIX_TIMESTAMP(last_modified) AS u_ts FROM ".TABLE_PREFIX."content WHERE course_id=$course_id ORDER BY content_parent_id, ordering";
//} else {
//	$sql = "SELECT *, UNIX_TIMESTAMP(last_modified) AS u_ts FROM ".TABLE_PREFIX."content WHERE course_id=$course_id ORDER BY content_parent_id, ordering";
//}
//$result = mysql_query($sql, $db);
//while ($row = mysql_fetch_assoc($result)) {
//	if (authenticate(TR_PRIV_CONTENT, TR_PRIV_RETURN) || $contentManager->isReleased($row['content_id']) === TRUE) {

if (is_array($rows)) {
	foreach ($rows as $row) {
		$content[$row['content_parent_id']][] = $row;
		if ($cid == $row['content_id']) {
			$top_content = $row;
			$top_content_parent_id = $row['content_parent_id'];
		}
	}
}

if ($cid) {
	/* filter out the top level sections that we don't want */
	$top_level = $content[$top_content_parent_id];
	foreach($top_level as $page) {
		if ($page['content_id'] == $cid) {
			$content[$top_content_parent_id] = array($page);
		} else {
			/* this is a page we don't want, so might as well remove it's children too */
			unset($content[$page['content_id']]);
		}
	}
	$ims_course_title = $course_title . ' - ' . $content[$top_content_parent_id][0]['title'];
} else {
	$ims_course_title = $course_title;
}


/* generate the imsmanifest.xml header attributes */
$imsmanifest_xml = str_replace(array('{COURSE_TITLE}', '{COURSE_DESCRIPTION}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}', '{COURSE_PRIMARY_LANGUAGE_CODE}'), 
							  array($ims_course_title, $course_desc, $course_language_charset, $course_language_code),
							  $ims_template_xml['header']);

/* get the first content page to default the body frame to */
$first = $content[$top_content_parent_id][0];

$test_ids = array();	//global array to store all the test ids
//if ($my_files == null) 
//$my_files = array();

/* generate the IMS QTI resource and files */
/*
/* in print_organizations
foreach ($content[0] as $content_box){
	$content_test_rs = $contentManager->getContentTestsAssoc($content_box['content_id']);	
	while ($content_test_row = mysql_fetch_assoc($content_test_rs)){
		//export
		$test_ids[] = $content_test_row['test_id'];
		//the 'added_files' is for adding into the manifest file in this zip
		$added_files = test_qti_export($content_test_row['test_id'], '', $zipfile);

		//Save all the xml files in this array, and then print_organizations will add it to the manifest file.
		foreach($added_files as $filename=>$file_array){
			$my_files[] = $filename;
			foreach ($file_array as $garbage=>$filename2){
				$my_files[] = $filename2;
			}
		}
	}
}
*/

ob_start();
print_organizations($top_content_parent_id, $content, 0, '', array(), $toc_html);
$organizations_str = ob_get_contents();
ob_end_clean();

// Modified by Cindy Qi Li on Jun 3, 2010
// Transformable does not support glossary
/* generate the resources and save the HTML files */
/*
$used_glossary_terms = array();
if (count($used_glossary_terms)) {
	$used_glossary_terms = array_unique($used_glossary_terms);
	sort($used_glossary_terms);
	reset($used_glossary_terms);

	$terms_xml = '';
	foreach ($used_glossary_terms as $term) {
		$term_key = urlencode($term);
		$glossary[$term_key] = htmlentities($glossary[$term_key], ENT_QUOTES, 'UTF-8');
		$glossary[$term_key] = str_replace('&', '&amp;', $glossary[$term_key]);
		$escaped_term = str_replace('&', '&amp;', $term);
		$terms_xml .= str_replace(	array('{TERM}', '{DEFINITION}'),
									array($escaped_term, $glossary[$term_key]),
									$glossary_term_xml);

		$terms_html .= str_replace(	array('{ENCODED_TERM}', '{TERM}', '{DEFINITION}'),
									array($term_key, $term, $glossary[$term_key]),
									$glossary_term_html);
	}

	$glossary_body_html = str_replace('{BODY}', $terms_html, $glossary_body_html);

	$glossary_xml = str_replace(array('{GLOSSARY_TERMS}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}'),
							    array($terms_xml, $course_language_charset),
								$glossary_xml);
	$glossary_html = str_replace(	array('{CONTENT}', '{KEYWORDS}', '{TITLE}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}', '{COURSE_PRIMARY_LANGUAGE_CODE}'),
									array($glossary_body_html, '', 'Glossary', $course_language_charset, $course_language_code),
									$html_template);
	$toc_html .= '<ul><li><a href="glossary.html" target="body">'._AT('glossary').'</a></li></ul>';
} else {
	unset($glossary_xml);
}
*/
// END OF Modified by Cindy Qi Li on Jun 3, 2010

$toc_html = str_replace(array('{TOC}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}', '{COURSE_PRIMARY_LANGUAGE_CODE}'),
					    array($toc_html, $course_language_charset, $course_language_code),
						$html_toc);

if ($first['content_path']) {
	$first['content_path'] .= '/';
}
$frame = str_replace(	array('{COURSE_TITLE}',		'{FIRST_ID}', '{PATH}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}', '{COURSE_PRIMARY_LANGUAGE_CODE}'),
						array($ims_course_title, $first['content_id'], $first['content_path'], $course_language_charset, $course_language_code),
						$html_frame);

$html_mainheader = str_replace(array('{COURSE_TITLE}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}', '{COURSE_PRIMARY_LANGUAGE_CODE}'),
							   array($ims_course_title, $course_language_charset, $course_language_code),
							   $html_mainheader);


/***************************************
 * templates
 * add content into the manifest file
 * donadiomauro@gmail.com
 * 
 * */

$mnf	= '';
$flag = false;
	// take all .css documents in "commoncartridge" folder
	$css	= array();
	for($i=0; $i < count($rows); $i++){
		if(!in_array($rows[$i]['layout'], $css) AND $rows[$i]['layout'] != null AND $templates_theme->exist_layout($rows[$i]['layout'])) {
                       
                    
                        if(!$flag) {
                            
                                $mnf	.= "<resource identifier=\"MANIFEST01_RESOURCE".rand()."\" type=\"webcontent\">\n";
                                $mnf	.= "<metadata/>\n";
                                $flag = true;
                        }
                        
                        if ($cid) {
                            if($rows[$i]['content_id'] != $cid) {
                                
                                //I must insert only the layout of the content $cid
                                break;
                                
                            }
                           
                        }
                        
			$css[]	= $rows[$i]['layout'];

			// add the .css file
			$mnf	.= "\n<file href=\"resources/commoncartridge/".$rows[$i]['layout'].".css\"/>\n";

			// add all the style folder content
				// get all layout images
				$images = glob("../../templates/layout/".$rows[$i]['layout']."/".$rows[$i]['layout']."/*.*");

				for($j=0; $j<count($images); $j++){
					$mnf	.= "<file href=\"resources/commoncartridge/".$rows[$i]['layout']."/".basename($images[$j])."\"/>\n";
				}
		}
	}
        
if($flag)
        $mnf	.= "\n</resource>";        


$resources .= $mnf;


/***************************************/

/* append the Organizations and Resources to the imsmanifest */
$imsmanifest_xml .= str_replace(	array('{ORGANIZATIONS}',	'{RESOURCES}', '{COURSE_TITLE}'),
									array($organizations_str,	$resources, $ims_course_title),
									$ims_template_xml['final']);

/* generate the vcard for the instructor/author */
//$sql = "SELECT first_name, last_name, email, website, login, phone FROM ".TABLE_PREFIX."members WHERE member_id=$instructor_id";
//$result = mysql_query($sql, $db);
$usersDAO = new UsersDAO();
$row = $usersDAO->getUserByID($instructor_id);
									
$vcard = new vCard();
if (isset($row)) {
	$vcard->setName($row['last_name'], $row['first_name'], $row['login']);
	$vcard->setEmail($row['email']);
	$vcard->setNote('Originated from an AContent at '.TR_BASE_HREF.'. See ATutor.ca for additional information.');
	$vcard->setURL($row['website']);

	$imsmanifest_xml = str_replace('{VCARD}', $vcard->getVCard(), $imsmanifest_xml);
} else {
	$imsmanifest_xml = str_replace('{VCARD}', '', $imsmanifest_xml);
}

/* save the imsmanifest.xml file */

$zipfile->add_file($frame,			 'index.html');
$zipfile->add_file($toc_html,		 'toc.html');
$zipfile->add_file($imsmanifest_xml, 'imsmanifest.xml');
$zipfile->add_file($html_mainheader, 'header.html');

// Modified by Cindy Qi Li on Jun 3, 2010
// AContent does not support glossary
/*
if ($glossary_xml) {
	$zipfile->add_file($glossary_xml,  'glossary.xml');
	$zipfile->add_file($glossary_html, 'glossary.html');
}
*/
// END OF Modified by Cindy Qi Li on Jun 3, 2010
$zipfile->add_file(file_get_contents(TR_INCLUDE_PATH.'../home/ims/include/adlcp_rootv1p2.xsd'), 'adlcp_rootv1p2.xsd');
$zipfile->add_file(file_get_contents(TR_INCLUDE_PATH.'../home/ims/include/ims_xml.xsd'), 'ims_xml.xsd');
$zipfile->add_file(file_get_contents(TR_INCLUDE_PATH.'../home/ims/include/imscp_rootv1p1p2.xsd'), 'imscp_rootv1p1p2.xsd');
$zipfile->add_file(file_get_contents(TR_INCLUDE_PATH.'../home/ims/include/imsmd_rootv1p2p1.xsd'), 'imsmd_rootv1p2p1.xsd');
$zipfile->add_file(file_get_contents(TR_INCLUDE_PATH.'../home/ims/include/ims.css'), 'ims.css');
$zipfile->add_file(file_get_contents(TR_INCLUDE_PATH.'../home/ims/include/footer.html'), 'footer.html');
$zipfile->add_file(file_get_contents('../../images/logo.png'), 'logo.png');

$zipfile->close(); // this is optional, since send_file() closes it anyway

$ims_course_title = str_replace(array(' ', ':'), '_', $ims_course_title);
/**
 * A problem here with the preg_replace below.
 * Originally was designed to remove all werid symbols to avoid file corruptions.
 * In UTF-8, all non-english chars are considered to be 'werid symbols'
 * We can still replace it as is, or add fileid to the filename to avoid these problems
 * Well then again people won't be able to tell what this file is about
 * If we are going to take out the preg_replace, some OS might not be able to understand
 * these characters and will have problems importing.
 */
$ims_course_title = preg_replace("{[^a-zA-Z0-9._-]}","", trim($ims_course_title));
$zipfile->send_file($ims_course_title . '_ims');

exit;
?>