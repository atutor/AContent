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
 * Modified from ATutor mods/_core/imscp/ims_import.php, SVN revision 9126
 */

define('TR_INCLUDE_PATH', '../../include/');

// Validate OAuth token and set $SESSION['user_id']
// Must come before require(vitals.inc.php) because vitals redirects to index page 
// when $SESSION['user_id'] is not set
$oauth_import = false;  // whether the import request is from oauth web service

// By default, enable the import of associated tests and a4a objects

if (!isset($_POST['allow_test_import'])) $_POST['allow_test_import'] = 1;
if (!isset($_POST['allow_a4a_import'])) $_POST['allow_a4a_import'] = 1;

// the import request is from oauth web service, find the user id from the given token
if (isset($_GET['oauth_token']))
{
	require_once(TR_INCLUDE_PATH.'config.inc.php');
	require_once(TR_INCLUDE_PATH.'constants.inc.php');
	
	if ($_GET['oauth_token'] == '')
	{
		echo "error=".urlencode('Empty OAuth token');
		exit;
	}
	else
	{
		$oauth_import = true;
		require_once(TR_INCLUDE_PATH.'classes/DAO/OAuthServerTokensDAO.class.php');
		$oAuthServerTokensDAO = new OAuthServerTokensDAO();
		$token_row = $oAuthServerTokensDAO->getByTokenAndType($_GET['oauth_token'], 'access');

		if (!is_array($token_row))
		{
			echo "error=".urlencode('Invalid OAuth token');
			exit;
		}
		else if ($oAuthServerTokensDAO->isTokenExpired($_GET['oauth_token']))
		{
			echo "error=".urlencode('OAuth token expired');
			exit;
		}
		
		$_user_id = $token_row[0]['user_id'];
	}
}

require(TR_INCLUDE_PATH.'vitals.inc.php');

require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'../home/classes/ContentUtility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsQuestionsAssocDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php'); /* for clr_dir() and preImportCallBack and dirsize() */

require_once(TR_INCLUDE_PATH.'lib/pclzip.lib.php');
require_once(TR_INCLUDE_PATH.'lib/pclzip_callback.lib.php');
require_once(TR_INCLUDE_PATH.'lib/qti.inc.php'); 
//require(TR_INCLUDE_PATH.'classes/QTI/QTIParser.class.php');	
require_once(TR_INCLUDE_PATH.'classes/QTI/QTIImport.class.php');
require_once(TR_INCLUDE_PATH.'classes/A4a/A4aImport.class.php');
require(TR_INCLUDE_PATH.'../home/ims/ns.inc.php');	//namespace, no longer needs, delete it after it's stable.
require_once(TR_INCLUDE_PATH.'classes/Weblinks/WeblinksParser.class.php');
require(TR_INCLUDE_PATH.'classes/DiscussionTools/DiscussionToolsParser.class.php');
require(TR_INCLUDE_PATH.'classes/DiscussionTools/DiscussionToolsImport.class.php');

// make sure the user has author privilege
Utility::authenticate(TR_PRIV_ISAUTHOR);

/* to avoid timing out on large files */
@set_time_limit(0);
$_SESSION['done'] = 1;

$html_head_tags = array("style", "script", "link");

$package_base_path = '';
$package_real_base_path = '';	//the path to save the contents
$all_package_base_path = array();
$xml_base_path = '';
$element_path = array();
$imported_glossary = array();
$character_data = '';
$test_message = '';
$test_title = '';
$content_type = '';
$skip_ims_validation = false;
$added_dt = array();	//the mapping of discussion tools that are added
$avail_dt = array();	//list of discussion tools that have not been handled

function check_available_size($course_id)
{
	global $coursesDAO, $MaxCourseSize, $import_path, $msg, $oauth_import;

	$q_row = $coursesDAO->get($course_id);
	
	//$sql	= "SELECT max_quota FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
	//$result = mysql_query($sql, $db);
	//$q_row	= mysql_fetch_assoc($result);
	
	if ($q_row['max_quota'] == TR_COURSESIZE_UNLIMITED) return;
	else $zip_size_limit = $MaxCourseSize;

	$totalBytes   = FileUtility::dirsize($import_path);
	
	$total_after  = $zip_size_limit - $totalBytes;
	
	if (is_dir(TR_CONTENT_DIR . $course_id.'/')) 
	{
		$course_total = FileUtility::dirsize(TR_CONTENT_DIR . $course_id.'/');
		$total_after  -= $course_total;
	}
	
	if ($total_after < 0) {
		/* remove the content dir, since there's no space for it */
		$errors = array('NO_CONTENT_SPACE', number_format(-1*($total_after/TR_KBYTE_SIZE), 2 ) );
		$msg->addError($errors);
		
		// Clean up import path and inserted course row
		FileUtility::clr_dir($import_path);
		$coursesDAO->Delete($course_id);

		if (isset($_GET['tile'])) {
			header('Location: '.$_base_path.'tools/tile/index.php');
		} 
		else if ($oauth_import) {
			echo "error=".urlencode('No space for the content.');
		}
		else {
			header('Location: '.$_SERVER['HTTP_REFERER']);
		}
		exit;
	}
}

/*
 * return the error messages represented by the given array 
 * @author	Mike A.
 * @ref		http://ca3.php.net/manual/en/domdocument.schemavalidate.php
 */
function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

/**
 * Validate all the XML in the package, including checking XSDs, missing data.
 * @param	string		the path of the directory that contains all the package files
 * @return	boolean		true if every file exists in the manifest, false if any is missing.
 */
function checkResources($import_path){
	global $items, $msg, $skip_ims_validation, $avail_dt;

	if (!is_dir($import_path)){
		return;
	}

	//if the package has access for all content, skip validation for now. 
	//todo: import the XSD into our validator
	if ($skip_ims_validation){
		return true;
	}

	//generate a file tree
	$data = rscandir($import_path);

	//check if every file is presented in the manifest
	foreach($data as $filepath){
		$filepath = substr($filepath, strlen($import_path));

		//validate xml via its xsd/dtds
		if (preg_match('/(.*)\.xml/', $filepath)){
			libxml_use_internal_errors(true);
			$dom = new DOMDocument();
			$dom->load(realpath($import_path.$filepath));
 			if (!@$dom->schemaValidate('main.xsd')){
				$errors = libxml_get_errors();
				foreach ($errors as $error) {
					//suppress warnings
					if ($error->level==LIBXML_ERR_WARNING){
						continue;
					}
					$msg->addError(array('IMPORT_CARTRIDGE_FAILED', libxml_display_error($error)));
				}
				libxml_clear_errors();
			}
		}
	}

	//Create an array that mimics the structure of the data array, based on the xml items
	$filearray = array();
	foreach($items as $name=>$fileinfo){
		if(isset($fileinfo['file']) && is_array($fileinfo['file']) && !empty($fileinfo['file'])){
			foreach($fileinfo['file'] as $fn){
				if (!in_array(realpath($import_path.$fn), $filearray)){
					//if url, skip
					if (preg_match('/^http[s]?\:/', $fn) == 0){
						$filearray[] = realpath($import_path. $fn);
					}					
				}
			}
		}

		//validate the xml by its schema
		if (preg_match('/imsqti\_(.*)/', $fileinfo['type'])){
			$qti = new QTIParser($fileinfo['type']);
			$xml_content = @file_get_contents($import_path . $fileinfo['href']);
			$qti->parse($xml_content); //will add error to $msg if failed			
		} 

		//add all dependent discussion tools to a list
		if(isset($fileinfo['dependency']) && !empty($fileinfo['dependency'])){
			$avail_dt = array_merge($avail_dt, $fileinfo['dependency']);
		}
	}

	//check if all files in the xml is presented in the archieve
	$result = array_diff($filearray, $data);
	//using sizeof because array_diff only 
	//returns an array containing all the entries from array1  that are not present in any of the 
	//other arrays. 
	//Using sizeof make sure it's not a subset of array2.
	//-1 on data because it always contain the imsmanifest.xml file
	if (!$skip_ims_validation){
		if (!empty($result) || sizeof($data)-1>sizeof($filearray)){
			$msg->addError(array('IMPORT_CARTRIDGE_FAILED', _AT('ims_missing_references')));
		}
	}
	return true;
}

/*
 * @example rscandir(dirname(__FILE__).'/'));
 * @param string $base
 * @param array $omit
 * @param array $data
 * @return array
 */
function rscandir($base='', &$data=array()) {
  $array = array_diff(scandir($base), array('.', '..')); # remove ' and .. from the array */
  foreach($array as $value) : /* loop through the array at the level of the supplied $base */
 
    if (is_dir($base.$value)) : /* if this is a directory */
//	  don't save the directory name
      $data = rscandir($base.$value.'/', $data); /* then make a recursive call with the
      current $value as the $base supplying the $data array to carry into the recursion */
     
    elseif (is_file($base.$value)) : /* else if the current $value is a file */
      $data[] = realpath($base.$value); /* just add the current $value to the $data array */
     
    endif;
   
  endforeach;
  return $data; // return the $data array
 
}

/**
 * Function to restructure the $items.  So that old import will merge the top page into its children, and
 * create a new folder on top of it
 */
function rehash($items){
	global $order;
	$parent_page_maps = array();	//old=>new
	$temp_popped_items = array();
	$rehashed_items = array();	//the reconstructed array
	foreach($items as $id => $content){
		$parent_obj = $items[$content['parent_content_id']];
		$rehashed_items[$id] = $content;	//copy
		if (isset($parent_page_maps[$content['parent_content_id']])){
			$rehashed_items [$id]['parent_content_id'] = $parent_page_maps[$content['parent_content_id']];
			$rehashed_items [$id]['ordering']++;
		} 
		//If its parent page is a top page and have an identiferref
		elseif (isset($parent_obj) && isset($parent_obj['href'])){			
			if (!isset($parent_obj['href'])){
				//check if this top page is already a folder, if so, next.
				continue;
			}
			//else, make its parent page to a folder
			$new_item['title'] = $parent_obj['title'];
			//check if this parent has been modified, if so, chnage it
			if (isset($parent_page_maps[$parent_obj['parent_content_id']])){
			    $new_item['parent_content_id'] = $parent_page_maps[$parent_obj['parent_content_id']];
			} else {
    			$new_item['parent_content_id'] = $parent_obj['parent_content_id'];
            }
			//all ordering needs to be +1 because we are creating a new folder on top of
			//everything, except the first page.
			$new_item['ordering'] = $parent_obj['ordering'];
			if ($new_item['parent_content_id']!='0'){
				$new_item['ordering']++;
			} 

    		//assign this new parent folder to the pending items array
			$new_item_name = $content['parent_content_id'].'_FOLDER';
			//a not so brilliant way to append the folder in its appropriate position
			$reordered_hashed_items = array();  //use to store the new rehashed item with the correct item order
			foreach($rehashed_items as $rh_id=>$rh_content){
			    if ($rh_id == $content['parent_content_id']){
			        //add the folder in before the parent subpage.
			        $reordered_hashed_items[$new_item_name] = $new_item;
			    }
			    $reordered_hashed_items[$rh_id] = $rh_content;  //clone
			}
			$rehashed_items = $reordered_hashed_items;  //replace it back
			unset($reordered_hashed_items);
			$parent_page_maps[$content['parent_content_id']] = $new_item_name;  //save this page on the hash map

			//reconstruct the parent
			$rehashed_items[$content['parent_content_id']]['parent_content_id'] = $parent_page_maps[$content['parent_content_id']];
			$rehashed_items[$content['parent_content_id']]['ordering'] = 0; //always the first one.

			//reconstruct itself
			$rehashed_items[$id]['parent_content_id'] = $parent_page_maps[$content['parent_content_id']];
			$rehashed_items[$id]['ordering']++;

		}
	}
	return $rehashed_items;
}

/**
 * Take out the common path within all $items['new_path'].
 * This allows import/export repeatedly without duplicating its path
 * @param   array   contains the breakdown of all resources in the XML
 */
function removeCommonPath($items){
    $common_path; 
    $quit = false;  //a flag that is set if it's not the first time being run.

    $filearray = array();
    //get all files listed in the manifest
    foreach($items as $name=>$fileinfo){
		if(isset($fileinfo['file']) && is_array($fileinfo['file']) && !empty($fileinfo['file'])){
			foreach($fileinfo['file'] as $fn){
				if (!in_array($fn, $filearray)){
					if (preg_match('/^http[s]?\:/', $fn) == 0){
						$filearray[] = $fn;
					}					
				}
			}
		}
	}

    foreach($filearray as $index=>$path){
        //hack
        //check if this is a XML file; if so, skip through, 
        //cause XML most likely isn't a content resource.
        $ext = substr($path, (strrpos($path, '.')+1));
        if($ext=='xml'){
            continue;
        }
        
        //if common path is empty, assign the first path to it.
        if ($common_path=='' && $quit==false){
            $common_path = $path;
            $quit = true;   //the next time common_path is empty, quit;
            continue;
        }
        //we use '/' here instead of DIRECTORY_SEPARATOR because php would
        //actually use '\' and return the whole string. 
        $common_array = explode('/', $common_path);
        $path_array = explode('/', $path);
        //convert path_array to absolute path
        //TODO: array_search is slow, build a faster search
        $pos=array_search('..', $path_array);
        while($pos=array_search('..', $path_array)){
            array_splice($path_array, $pos-1, 2);
        }
        $intersect_array = array_intersect($common_array, $path_array);
        $common_path = implode('/', $intersect_array);       
    }
    
    // If this path (including file extension) is in the filearray,
    // then this means there is only 1 file in the array, implies there
    // wasn't any intersection at all.  In this case, use the base root.
    if(in_array($common_path, $filearray)) {
        $common_path = '';
    }

    return $common_path;
}


/** 
 * This function will take the test accessment XML and add these to the database.
 * @param	string	The path of the XML, without the import_path.
 * @param	mixed	An item singleton.  Contains the info of this item, namely, the accessment details.
 *					The item must be an object created by the ims class.
 * @param	string	the import path
 * @return	mixed	An Array that contains all the question IDs that have been imported.
 */
 function addQuestions($xml, $item, $import_path){
	global $test_title;
	$qti_import = new QTIImport($import_path);
	$tests_xml = $import_path.$xml;
	
	//Mimic the array for now.
	$test_attributes['resource']['href'] = $item['href'];
	$test_attributes['resource']['type'] = preg_match('/imsqti_xmlv1p2/', $item['type'])==1?'imsqti_xmlv1p2':'imsqti_xmlv1p1';
	$test_attributes['resource']['file'] = $item['file'];

	//Get the XML file out and start importing them into our database.
	//TODO: See question_import.php 287-289.
	$qids = $qti_import->importQuestions($test_attributes);
	$test_title = $qti_import->title;

	return $qids;
 }


	/* called at the start of en element */
	/* builds the $path array which is the path from the root to the current element */
	function startElement($parser, $name, $attrs) {
		global $items, $path, $package_base_path, $all_package_base_path, $package_real_base_path;
		global $element_path, $import_path, $skip_ims_validation;
		global $xml_base_path, $test_message, $content_type;
		global $current_identifier, $msg, $ns, $ns_cp;
		global $course_primary_lang;
		
		// get language from CONTENT PACKAGE
		if (substr($element_path[count($element_path)-1], -6) == ':title' && substr($name, -11) == ':langstring') {
			$course_primary_lang = trim($attrs['xml:lang']);
		}
		
		//validate namespaces
		if(!$skip_ims_validation && isset($attrs['xsi:schemaLocation']) && $name=='manifest'){
			$schema_location = array();
			$split_location = preg_split('/[\r\n\s]+/', trim($attrs['xsi:schemaLocation']));

			//check if the namespace is actually right, have an array or some sort in IMS class
			if(sizeof($split_location)%2==1){
				//schema is not in the form of "The first URI reference in each pair is a namespace name,
				//and the second is the location of a schema that describes that namespace."
				//$msg->addError('MANIFEST_NOT_WELLFORM');
				$msg->addError(array('IMPORT_CARTRIDGE_FAILED', _AT('schema_error')));
			}

			//turn the xsi:schemaLocation URI into a schema that describe namespace.
			//name = url
			//http://msdn.microsoft.com/en-us/library/ms256100(VS.85).aspx
			//http://www.w3.org/TR/xmlschema-1/
			for($i=0; $i < sizeof($split_location);$i=$i+2){
				//if the key of the namespace is not defined. Throw error.
				if(!isset($ns[$split_location[$i]]) && !isset($ns_cp[$split_location[$i]])){
					$msg->addError(array('IMPORT_CARTRIDGE_FAILED', _AT('schema_error')));
				}
			}
		} else {
			//throw error		
		}

		if ($name == 'manifest' && isset($attrs['xml:base']) && $attrs['xml:base']) {
			$xml_base_path = $attrs['xml:base'];
		} else if ($name == 'file') {
			// check if it misses file references
			if(!$skip_ims_validation && (!isset($attrs['href']) || $attrs['href']=='')){
				//$msg->addError('MANIFEST_NOT_WELLFORM');
				$msg->addError(array('IMPORT_CARTRIDGE_FAILED', _AT('ims_missing_references')));
			}

			// special case for webCT content packages that don't specify the `href` attribute 
			// with the `<resource>` element.
			// we take the `href` from the first `<file>` element.
			if (isset($items[$current_identifier]) && ($items[$current_identifier]['href'] == '')) {
				$attrs['href'] = urldecode($attrs['href']);
				$items[$current_identifier]['href'] = $attrs['href'];
			}
			$temp_path = pathinfo($attrs['href']);
			if ($temp_path['extension'] == 'html') {
				$temp_path = explode('/', $temp_path['dirname']);
				if (empty($package_base_path)){
			   		$package_base_path = $temp_path;
            	}
				if ($all_package_base_path!='' && empty($all_package_base_path)){
					$all_package_base_path = $temp_path;
				}
				$package_base_path = array_intersect_assoc($package_base_path, $temp_path);
			}
			//calculate the depths of relative paths
			if ($all_package_base_path!=''){
				$no_relative_temp_path = $temp_path;
				foreach($no_relative_temp_path as $path_node){
					if ($path_node=='..'){
						array_pop($no_relative_temp_path);
						array_pop($no_relative_temp_path); //not a typo, have to pop twice, both itself('..'), and the one before.
					}
				}
				$all_package_base_path = array_intersect_assoc($all_package_base_path, $no_relative_temp_path);
				if (empty($all_package_base_path)){
					$all_package_base_path = '';	//unset it, there is no intersection.
				}
			}

			//save the actual content base path
			$sizeofrp = array();
			if (in_array('..', $temp_path)){
				$sizeofrp = array_count_values($temp_path);
			}

			//for IMSCC, assume that all resources lies in the same folder, except styles.css
			if ($items[$current_identifier]['type']=='webcontent' || $items[$current_identifier]['type']=='imsdt_xmlv1p0'){
				//find the intersection of each item's related files, then that intersection is the content_path
				if (isset($items[$current_identifier]['file'])){
					foreach ($items[$current_identifier]['file'] as $resource_path){
					    if (!strpos($resource_path, 'Share')) {
						    $temp_path = pathinfo($resource_path);
						    $temp_path = explode('/', $temp_path['dirname']);
						    $package_base_path = array_intersect_assoc($package_base_path, $temp_path);
					    }
					}
				}
			}

			//real content path
			if(array_key_exists('..', $sizeofrp) && $sizeofrp['..'] > 0 && !empty($all_package_base_path)){
				for ($i=0; $i<$sizeofrp['..']; $i++){
					array_pop($all_package_base_path);
				}
			}
			//if (count($package_base_path) > 0) {
			if (!empty($package_base_path)) {
				$items[$current_identifier]['new_path'] = implode('/', $package_base_path);
			}	

			if (isset($_POST['allow_test_import']) && isset($items[$current_identifier]) 
						&& preg_match('/((.*)\/)*tests\_[0-9]+\.xml$/', $attrs['href'])) {
				$items[$current_identifier]['tests'][] = $attrs['href'];
			} 
			if (isset($_POST['allow_a4a_import']) && isset($items[$current_identifier])) {
				$items[$current_identifier]['a4a_import_enabled'] = true;
			}
		} else if (($name == 'item') && ($attrs['identifierref'] != '')) {
			$path[] = $attrs['identifierref'];
		} else if (($name == 'item') && ($attrs['identifier'])) {
			$path[] = $attrs['identifier'];

		} else if (($name == 'resource')) {
			$current_identifier = $attrs['identifier'];
			$items[$current_identifier]['type'] = $attrs['type'];
			if ($attrs['href']) {
				$attrs['href'] = urldecode($attrs['href']);

				$items[$attrs['identifier']]['href'] = $attrs['href'];

				// href points to a remote url
				if (preg_match('/^http.*:\/\//', trim($attrs['href'])))
					$items[$attrs['identifier']]['new_path'] = '';
				else // href points to local file
				{
					$temp_path = pathinfo($attrs['href']);
					$temp_path = explode('/', $temp_path['dirname']);
					$package_base_path = $temp_path;

					$items[$attrs['identifier']]['new_path'] = implode('/', $temp_path);
				}
			}


		} else if ($name=='dependency' && $attrs['identifierref']!='') {
			//if there is a dependency, attach it to the item array['file']
			$items[$current_identifier]['dependency'][] = $attrs['identifierref'];
		}
		if (($name == 'item') && ($attrs['parameters'] != '')) {
			$items[$attrs['identifierref']]['test_message'] = $attrs['parameters'];
		}
		if ($name=='file'){
			if(!isset($items[$current_identifier]) && $attrs['href']!=''){
				$items[$current_identifier]['href']	 = $attrs['href'];
			}
			if (substr($attrs['href'], 0, 7) == 'http://' || substr($attrs['href'], 0, 8) == 'https://' || file_exists($import_path.$attrs['href']) || $skip_ims_validation){
				$items[$current_identifier]['file'][] = $attrs['href'];
			} else {
				//$msg->addError('');
				$msg->addError(array('IMPORT_CARTRIDGE_FAILED', _AT(array('ims_files_missing', $attrs['href']))));
			}
		}		
		if ($name=='cc:authorizations'){
			//don't have authorization setup.
			//$msg->addError('');
			$msg->addError('IMS_AUTHORIZATION_NOT_SUPPORT');
		}
		array_push($element_path, $name);
	}

	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		global $path, $element_path, $my_data, $items, $oauth_import;
		global $current_identifier, $skip_ims_validation;
		global $msg, $content_type;
		global $course_title, $course_description, $course_primary_lang;  // added by Cindy Li
		static $resource_num = 0;
		
		if ($name == 'item') {
			array_pop($path);
		} 

		// added by Cindy Li on Jan 10, 2010
		// Extract course title, description and primary language for a newly-created course
		if (substr($element_path[count($element_path)-2], -6) == ':title') {
			if (substr($element_path[count($element_path)-1], -7) == ':string' ||
			    substr($element_path[count($element_path)-1], -11) == ':langstring') {
				$course_title = trim($my_data);
			}
		}
		
		if (substr($element_path[count($element_path)-2], -12) == ':description') {
			if (substr($element_path[count($element_path)-1], -7) == ':string' ||
			    substr($element_path[count($element_path)-1], -11) == ':langstring') {
				$course_description = trim($my_data);
			}
		}
		
		// get language from COMMON CARTRIDGE
		
		if (substr($element_path[count($element_path)-1], -9) == ':language') {
			$course_primary_lang = trim($my_data);
			
		}
		// end of added by Cindy Li on Jan 10, 2010
		
		//check if this is a test import
		if ($name == 'schema'){
			if (trim($my_data)=='IMS Question and Test Interoperability'){
				if ($oauth_import) {
					echo "error=".urlencode('A test import');
				} else {
					$msg->addError('IMPORT_FAILED');
				}
			} 
			$content_type = trim($my_data);
		}

		//Handles A4a
		if ($current_identifier != ''){
			$my_data = trim($my_data);
			$last_file_name = $items[$current_identifier]['file'][(sizeof($items[$current_identifier]['file']))-1];

			if ($name=='originalAccessMode'){				
				if (in_array('accessModeStatement', $element_path)){
					$items[$current_identifier]['a4a'][$last_file_name][$resource_num]['access_stmt_originalAccessMode'][] = $my_data;
				} elseif (in_array('adaptationStatement', $element_path)){
					$items[$current_identifier]['a4a'][$last_file_name][$resource_num]['adapt_stmt_originalAccessMode'][] = $my_data;
				}			
			} elseif (($name=='language') && in_array('accessModeStatement', $element_path)){
				$items[$current_identifier]['a4a'][$last_file_name][$resource_num]['language'][] = $my_data;
			} elseif ($name=='hasAdaptation') {
				$items[$current_identifier]['a4a'][$last_file_name][$resource_num]['hasAdaptation'][] = $my_data;
			} elseif ($name=='isAdaptationOf'){
				$items[$current_identifier]['a4a'][$last_file_name][$resource_num]['isAdaptationOf'][] = $my_data;
			} elseif ($name=='accessForAllResource'){
				/* the head node of accessForAll Metadata, if this exists in the manifest. Skip XSD validation,
				 * because A4a doesn't have a xsd yet.  Our access for all is based on ISO which will not pass 
				 * the current IMS validation.  
				 * Also, since ATutor is the only one (as of Oct 21, 2009) that exports IMS with access for all
				 * content, we can almost assume that any ims access for all content is by us, and is valid. 
				 */
				$skip_ims_validation = true;
				$resource_num++;
			} elseif($name=='file'){
				$resource_num = 0;	//reset resournce number to 0 when the file tags ends
			}
		}

		if ($element_path === array('manifest', 'metadata', 'imsmd:lom', 'imsmd:general', 'imsmd:title', 'imsmd:langstring')) {
			global $package_base_name;
			$package_base_name = trim($my_data);
		}

		array_pop($element_path);
		$my_data = '';
	}

	/* called when there is character data within elements */
	/* constructs the $items array using the last entry in $path as the parent element */
	function characterData($parser, $data){
		global $path, $items, $order, $my_data, $element_path;
		global $current_identifier;

		$str_trimmed_data = trim($data);
		
		if (!empty($str_trimmed_data)) {
			$size = count($path);
			if ($size > 0) {
				$current_item_id = $path[$size-1];
				if ($size > 1) {
					$parent_item_id = $path[$size-2];
				} else {
					$parent_item_id = 0;
				}

				if (isset($items[$current_item_id]['parent_content_id']) && is_array($items[$current_item_id])) {

					/* this item already exists, append the title		*/
					/* this fixes {\n, \t, `, &} characters in elements */

					/* horible kludge to fix the <ns2:objectiveDesc xmlns:ns2="http://www.utoronto.ca/atrc/tile/xsd/tile_objective"> */
					/* from TILE */
					if (in_array('accessForAllResource', $element_path)){
						//skip this tag
					} elseif ($element_path[count($element_path)-1] != 'ns1:objectiveDesc') {
						$items[$current_item_id]['title'] .= $data;
					}
	
				} else {
					$order[$parent_item_id] ++;
					$item_tmpl = array(	'title'				=> $data,
										'parent_content_id' => $parent_item_id,
										'ordering'			=> $order[$parent_item_id]-1);
					//append other array values if it exists
					if (is_array($items[$current_item_id])){
						$items[$current_item_id] = array_merge($items[$current_item_id], $item_tmpl);
					} else {
						$items[$current_item_id] = $item_tmpl;
					}
				}
			}
		}

		$my_data .= $data;
	}

	/* glossary parser: */
	function glossaryStartElement($parser, $name, $attrs) {
		global $element_path;

		array_push($element_path, $name);
	}

	/* called when an element ends */
	/* removed the current element from the $path */
	function glossaryEndElement($parser, $name) {
		global $element_path, $my_data, $imported_glossary;
		static $current_term;

		if ($element_path === array('glossary', 'item', 'term') || 
			$element_path === array('glossary:glossary', 'item', 'term')) {
			$current_term = $my_data;

		} else if ($element_path === array('glossary', 'item', 'definition') || 
				   $element_path === array('glossary:glossary', 'item', 'definition')) {
			$imported_glossary[trim($current_term)] = trim($my_data);
		}

		array_pop($element_path);
		$my_data = '';
	}

	function glossaryCharacterData($parser, $data){
		global $my_data;

		$my_data .= $data;
	}

if (!isset($_POST['submit']) && !isset($_POST['cancel']) && !isset($_GET['oauth_token'])) {
	/* just a catch all */
	$msg->addError('NO_PRIV');
	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('IMPORT_CANCELLED');

	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
}

$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;

//If user chooses to ignore validation.
if(isset($_POST['ignore_validation']) && $_POST['ignore_validation']==1) {
	$skip_ims_validation = true;
}

if (isset($_REQUEST['url']) && ($_REQUEST['url'] != 'http://') ) {
	if ($content = @file_get_contents($_REQUEST['url'])) {
		$filename = substr(time(), -6). '.zip';
		$full_filename = TR_CONTENT_DIR . $filename;

		if (!$fp = fopen($full_filename, 'w+b')) {
			echo "Cannot open file ($filename)";
			exit;
		}

		if (fwrite($fp, $content, strlen($content) ) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
		fclose($fp);
	}	
	$_FILES['file']['name']     = $filename;
	$_FILES['file']['tmp_name'] = $full_filename;
	$_FILES['file']['size']     = strlen($content);
	unset($content);
//	$url_parts = pathinfo($_REQUEST['url']);
//	$package_base_name_url = $url_parts['basename'];
    $package_base_name_url = md5(time());
}
$ext = pathinfo($_FILES['file']['name']);
$ext = $ext['extension'];

if ($ext != 'zip') {
	$msg->addError('IMPORTDIR_IMS_NOTVALID');
} else if ($_FILES['file']['error'] == 1) {
	$errors = array('FILE_MAX_SIZE', ini_get('upload_max_filesize'));
	$msg->addError($errors);
} else if ( !$_FILES['file']['name'] || (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_REQUEST['url'])) {
	$msg->addError('FILE_NOT_SELECTED');
} else if ($_FILES['file']['size'] == 0) {
	$msg->addError('IMPORTFILE_EMPTY');
} 
$msg->printAll();
if ($msg->containsErrors()) {
	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else if ($oauth_import) {
		echo "error=".urlencode('Invalid imported file');
	} else {
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (file_exists($full_filename)) @unlink($full_filename);
	exit;
}

/* check if ../content/import/ exists */
$import_path = TR_CONTENT_DIR . 'import/';
$content_path = TR_CONTENT_DIR;

if (!is_dir($import_path)) {
	if (!@mkdir($import_path, 0700)) {
		$msg->addError('IMPORTDIR_FAILED');
	}
}

if (isset($_POST['_course_id'])) $import_path .= $_POST['_course_id'].'/';
else $import_path .= Utility::getRandomStr(16).'/';

if (is_dir($import_path)) {
	FileUtility::clr_dir($import_path);
}

if (!@mkdir($import_path, 0700)) {
	$msg->addError('IMPORTDIR_FAILED');
}

if ($msg->containsErrors()) {
	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else if ($oauth_import) {
		echo "error=".urlencode('Cannot create import directory');
	} else {
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (file_exists($full_filename)) @unlink($full_filename);
	exit;
}

/* extract the entire archive into TR_COURSE_CONTENT . import/$course using the call back function to filter out php files */
error_reporting(0);
$archive = new PclZip($_FILES['file']['tmp_name']);

if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
						PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
	if ($oauth_import) {
		echo "error=".urlencode('Cannot unzip the package');
	} else {
		$msg->addError('IMPORT_FAILED');
		echo 'Error : '.$archive->errorInfo(true);
	}
	FileUtility::clr_dir($import_path);
	header('Location: '.$_SERVER['HTTP_REFERER']);
	if (file_exists($full_filename)) @unlink($full_filename);
	exit;
}


/* initialize DAO objects */
$coursesDAO = new CoursesDAO();
$contentDAO = new ContentDAO();
$testsQuestionsAssocDAO = new TestsQuestionsAssocDAO();
$contentTestsAssocDAO = new ContentTestsAssocDAO();

// get the course's max_quota
if (isset($_POST['_course_id']))
{
	check_available_size($_POST['_course_id']);
}

$items = array(); /* all the content pages */
$order = array(); /* keeps track of the ordering for each content page */
$path  = array();  /* the hierarchy path taken in the menu to get to the current item in the manifest */
$dependency_files = array(); /* the file path for the dependency files */

/*
$items[content_id/resource_id] = array(
									'title'
									'real_content_id' // calculated after being inserted
									'parent_content_id'
									'href'
									'ordering'
									);
*/
$ims_manifest_xml = @file_get_contents($import_path.'imsmanifest.xml');

//scan for manifest xml if it's not on the top level.
if ($ims_manifest_xml === false){
	$data = rscandir($import_path);
	$manifest_array = array();
	foreach($data as $scanned_file){
		$scanned_file = realpath($scanned_file);
		//change the file string to an array
		$this_file_array = explode(DIRECTORY_SEPARATOR, $scanned_file);
		if(empty($manifest_array)){
			$manifest_array = $this_file_array;
		}
		$manifest_array = array_intersect_assoc($this_file_array, $manifest_array);

		if (strpos($scanned_file, 'imsmanifest')!==false){
			$ims_manifest_xml = @file_get_contents($scanned_file);
		}
	}
	if ($ims_manifest_xml !== false){
		$import_path = implode(DIRECTORY_SEPARATOR, $manifest_array) . DIRECTORY_SEPARATOR;
	}
}

//if no imsmanifest.xml found in the entire package, throw error.
if ($ims_manifest_xml === false) {
	$msg->addError('NO_IMSMANIFEST');

	if (file_exists($import_path . 'atutor_backup_version')) {
		$msg->addError('NO_IMS_BACKUP');
	}
	FileUtility::clr_dir($import_path);

	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else if ($oauth_import) {
		echo "error=".urlencode('IMS manifest file does not appear to be valid');
	} else {
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (file_exists($full_filename)) @unlink($full_filename);
	exit;
}

$xml_parser = xml_parser_create();

xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
xml_set_element_handler($xml_parser, 'startElement', 'endElement');
xml_set_character_data_handler($xml_parser, 'characterData');

if (!xml_parse($xml_parser, $ims_manifest_xml, true)) {
	die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)));
}
xml_parser_free($xml_parser);

// Check if all the files exists in the manifest, iff it's a IMS CC package.
if ($content_type == 'IMS Common Cartridge') {
	checkResources($import_path);
}

// Check if there are any errors during parsing.
if ($msg->containsErrors()) {
	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else if ($oauth_import) {
		echo "error=".urlencode('Error at parsing IMS manifest file');
	} else {
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (file_exists($full_filename)) @unlink($full_filename);
	exit;
}

// added by Cindy Li on Jan 10, 2010
// generate a course_id if the import is not into an existing course
if (!isset($_POST['_course_id']))
{
	if (isset($_POST['hide_course']))
		$access = 'private';
	else
		$access = 'public';
	
	if (isset($course_primary_lang) && $course_primary_lang != '')
	{
		$langcode_and_charset = explode('-', $course_primary_lang);
//		$course_primary_lang = Utility::get3LetterLangCode($langcode_and_charset[0]);
		$course_primary_lang = $langcode_and_charset[0];
	}else{
		$course_primary_lang = DEFAULT_LANGUAGE_CODE;
	}
	$_course_id = $coursesDAO->Create($_SESSION['user_id'], 'top', $access, $course_title, $course_description, 
	             '', '', '', '', $course_primary_lang, '', '');
	
	check_available_size($_course_id);

	// insert author role into table "user_courses"
	$userCoursesDAO = new UserCoursesDAO();
	$userCoursesDAO->Create($_SESSION['user_id'], $_course_id, TR_USERROLE_AUTHOR, 0);
}
else $_course_id = $_POST['_course_id'];

// end of added by Cindy Li on Jan 10, 2010

/* generate a unique new package base path based on the package file name and date as needed. */
/* the package name will be the dir where the content for this package will be put, as a result */
/* the 'content_path' field in the content table will be set to this path. */
/* $package_base_name_url comes from the URL file name (NOT the file name of the actual file we open)*/
if (!$package_base_name && $package_base_name_url) {
	$package_base_name = substr($package_base_name_url, -6);
} else if (!$package_base_name) {
	$package_base_name = substr($_FILES['file']['name'], 0, -4);
}

$package_base_name = strtolower($package_base_name);
$package_base_name = str_replace(array('\'', '"', ' ', '|', '\\', '/', '<', '>', ':'), '_' , $package_base_name);
$package_base_name = preg_replace("/[^A-Za-z0-9._\-]/", '', $package_base_name);

$course_dir = TR_CONTENT_DIR.$_course_id.'/';

if (is_dir($course_dir.$package_base_name)) {
	$package_base_name .= '_'.date('ymdHis');
}

if ($package_base_path) {
	$package_base_path = implode('/', $package_base_path);
} elseif (empty($package_base_path)){
	$package_base_path = '';
}

if ($xml_base_path) {
	$package_base_path = $xml_base_path . $package_base_path;

	mkdir($import_path.$xml_base_path);
	$package_base_name = $xml_base_path . $package_base_name;
}

/* get the top level content ordering offset */
$order_offset = $contentDAO->getMaxOrdering($_course_id, 0);
$lti_offset = array();	//since we don't need lti tools, the ordering needs to be subtracted

//reorder the items stack
$common_path = removeCommonPath($items);
$items = rehash($items);
//debug($items);exit;
foreach ($items as $item_id => $content_info) 
{	
	//formatting field, default 1
	$content_formatting = 1;	//CONTENT_TYPE_CONTENT

	//don't want to display glossary as a page
	if ($content_info['href']== $glossary_path . 'glossary.xml'){
		continue;
	}

	//if discussion tools, add it to the list of unhandled dts
	if ($content_info['type']=='imsdt_xmlv1p0'){
		//if it will be taken care after (has dependency), then move along.
		if (in_array($item_id, $avail_dt)){
			$lti_offset[$content_info['parent_content_id']]++;
			continue;
		}
	}

	//handle the special case of cc import, where there is no content association. The resource should
	//still be imported.
	if(!isset($content_info['parent_content_id'])){
		//if this is a question bank 
		if ($content_info['type']=="imsqti_xmlv1p2/imscc_xmlv1p0/question-bank"){
			addQuestions($content_info['href'], $content_info, $import_path);
		}
	}

	//if it has no title, most likely it is not a page but just a normal item, skip it
	if (!isset($content_info['title'])){
		continue;
	}
	
	//check dependency immediately, then handles it
	$head = '';
	if (is_array($content_info['dependency']) && !empty($content_info['dependency'])){
		foreach($content_info['dependency'] as $dependency_ref){
			//check if this is a discussion tool dependency
			if ($items[$dependency_ref]['type']=='imsdt_xmlv1p0'){
				$items[$item_id]['forum'][$dependency_ref] = $items[$dependency_ref]['href'];
			}
			//check if this is a QTI dependency
			if (strpos($items[$dependency_ref]['type'], 'imsqti_xmlv1p2/imscc_xmlv1p0') !== false){
				$items[$item_id]['tests'][$dependency_ref] = $items[$dependency_ref]['href'];
			}
		}
	}


	// remote href
	if (preg_match('/^http.*:\/\//', trim($content_info['href'])) )
	{
		$content = '<a href="'.$content_info['href'].'" target="_blank">'.$content_info['title'].'</a>';
	}
	else
	{
		if ($content_type == 'IMS Common Cartridge'){
			//to handle import with purely images but nothing else
			//don't need a content base path for it.
			$content_new_path = $content_info['new_path'];
			$content_info['new_path'] = '';
		}
		if (isset($content_info['href'], $xml_base_path)) {
			$content_info['href'] = $xml_base_path . $content_info['href'];
		}
		if (!isset($content_info['href'])) {
			// this item doesn't have an identifierref. so create an empty page.
			// what we called a folder according to v1.2 Content Packaging spec
			// Hop over
			$content = '';
			$ext = '';
			$last_modified = date('Y-m-d H:i:s');
		} else {
			//$file_info = @stat(TR_CONTENT_DIR . 'import/'.$_POST['_course_id'].'/'.$content_info['href']);
			$file_info = @stat($import_path.$content_info['href']);
			if ($file_info === false) {
				continue;
			}
		
			//$path_parts = pathinfo(TR_CONTENT_DIR . 'import/'.$_POST['_course_id'].'/'.$content_info['href']);
			$path_parts = pathinfo($import_path.$content_info['href']);
			$ext = strtolower($path_parts['extension']);

			$last_modified = date('Y-m-d H:i:s', $file_info['mtime']);
		}
		if (in_array($ext, array('gif', 'jpg', 'bmp', 'png', 'jpeg'))) {
			/* this is an image */
			$content = '<img src="'.$content_info['href'].'" alt="'.$content_info['title'].'" />';
		} else if ($ext == 'swf') {
			/* this is flash */
            /* Using default size of 550 x 400 */

			$content = '<object type="application/x-shockwave-flash" data="' . $content_info['href'] . '" width="550" height="400"><param name="movie" value="'. $content_info['href'] .'" /></object>';

		} else if ($ext == 'mov') {
			/* this is a quicktime movie  */
            /* Using default size of 550 x 400 */

			$content = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="550" height="400" codebase="http://www.apple.com/qtactivex/qtplugin.cab"><param name="src" value="'. $content_info['href'] . '" /><param name="autoplay" value="true" /><param name="controller" value="true" /><embed src="' . $content_info['href'] .'" width="550" height="400" controller="true" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>';

		} else if ($ext == 'mp3') {
			$content = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="200" height="15" codebase="http://www.apple.com/qtactivex/qtplugin.cab"><param name="src" value="'. $content_info['href'] . '" /><param name="autoplay" value="false" /><embed src="' . $content_info['href'] .'" width="200" height="15" autoplay="false" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>';
		} else if (in_array($ext, array('wav', 'au'))) {
			$content = '<embed SRC="'.$content_info['href'].'" autostart="false" width="145" height="60"><noembed><bgsound src="'.$content_info['href'].'"></noembed></embed>';

		} else if (in_array($ext, array('txt', 'css', 'html', 'htm', 'csv', 'asc', 'tsv', 'xml', 'xsl'))) {
			if ($content_type == 'IMS Common Cartridge'){
				$content_info['new_path'] = $content_new_path;
			}

			/* this is a plain text file */

			$content = file_get_contents($import_path.$content_info['href']);
			if ($content === false) {
				/* if we can't stat() it then we're unlikely to be able to read it */
				/* so we'll never get here. */
				continue;
			}

			// get the contents of the 'head' element
			$head .= ContentUtility::getHtmlHeadByTag($content, $html_head_tags);
			
			// Specifically handle eXe package
			// NOTE: THIS NEEDS WORK! TO FIND A WAY APPLY EXE .CSS FILES ONLY ON COURSE CONTENT PART.
			// NOW USE OUR OWN .CSS CREATED SOLELY FOR EXE
			$isExeContent = false;

			// check xml file in eXe package
			if (preg_match("/<organization[ ]*identifier=\"eXe*>*/", $ims_manifest_xml))
			{
				$isExeContent = true;
			}

			// use ATutor's eXe style sheet as the ones from eXe conflicts with ATutor's style sheets
			if ($isExeContent)
			{
				$head = preg_replace ('/(<style.*>)(.*)(<\/style>)/ms', '\\1@import url(/docs/exestyles.css);\\3', $head);
			}

			// end of specifically handle eXe package

			$content = ContentUtility::getHtmlBody($content);
			if ($contains_glossary_terms) 
			{
				// replace glossary content package links to real glossary mark-up using [?] [/?]
				// refer to bug 3641, edited by Harris
				$content = preg_replace('/<a href="([.\w\d\s]+[^"]+)" target="body" class="at-term">([.\w\d\s&;"]+|.*)<\/a>/i', '[?]\\2[/?]', $content);
			}

			/* potential security risk? */
			if ( strpos($content_info['href'], '..') === false && !preg_match('/((.*)\/)*tests\_[0-9]+\.xml$/', $content_info['href'])) {
//				@unlink(TR_CONTENT_DIR . 'import/'.$_POST['_course_id'].'/'.$content_info['href']);
			}

			// overwrite content if this is discussion tool.
			if ($content_info['type']=='imsdt_xmlv1p0'){
				$dt_parser = new DiscussionToolsParser();
				$xml_content = @file_get_contents($import_path . $content_info['href']);
				$dt_parser->parse($xml_content);
				$forum_obj = $dt_parser->getDt();
				$content = $forum_obj->getText();
				unset($forum_obj);
				$dt_parser->close();
			}
		} else if ($ext) {
			/* non text file, and can't embed (example: PDF files) */
			$content = '<a href="'.$content_info['href'].'">'.$content_info['title'].'</a>';
		}	
	}
	$content_parent_id = $cid;
	if ($content_info['parent_content_id'] !== 0) {
		$content_parent_id = $items[$content_info['parent_content_id']]['real_content_id'];
		//if it's not there, use $cid
		if (!$content_parent_id){
			$content_parent_id = $cid;
		}
	}

	$my_offset = 0;
	if ($content_parent_id == $cid) {
		$my_offset = $order_offset;
	}

	/* replace the old path greatest common denomiator with the new package path. */
	/* we don't use str_replace, b/c there's no knowing what the paths may be	  */
	/* we only want to replace the first part of the path.	
	*/
	if(is_array($all_package_base_path)){
		$all_package_base_path = implode('/', $all_package_base_path);
	}


	if ($common_path != '' 
          && ($content_info['new_path'] === $common_path
          || substr($content_info['new_path'], strlen($common_path)))) {
		$content_info['new_path'] = $package_base_name . substr($content_info['new_path'], strlen($common_path));
	} else {
		$content_info['new_path'] = $package_base_name . '/' . $content_info['new_path'];
	}

	//handles weblinks
	if ($content_info['type']=='imswl_xmlv1p0'){
		$weblinks_parser = new WeblinksParser();
		$xml_content = @file_get_contents($import_path . $content_info['href']);
		$weblinks_parser->parse($xml_content);
		$content_info['title'] = $weblinks_parser->getTitle();
		$content = $weblinks_parser->getUrl();
		$content_folder_type = CONTENT_TYPE_WEBLINK;
		$content_formatting = 2;
	}


	//if this file is a test_xml, create a blank page instead, for imscc.
	if (preg_match('/((.*)\/)*tests\_[0-9]+\.xml$/', $content_info['href']) 
		|| preg_match('/imsqti\_(.*)/', $content_info['type'])) {
		$content = ' ';
	} 


	//check for content_type
	if ($content_formatting!=CONTENT_TYPE_WEBLINK){
		$content_folder_type = (!isset($content_info['type'])?CONTENT_TYPE_FOLDER:CONTENT_TYPE_CONTENT);
	}
	
	$items[$item_id]['real_content_id'] = $contentDAO->Create($_course_id, intval($content_parent_id), 
	                    ($content_info['ordering'] + $my_offset - $lti_offset[$content_info['parent_content_id']] + 1),
	                    0, $content_formatting, "", $content_info['new_path'], $content_info['title'],
	                    $content, $head, 1, $content_info['test_message'], $content_folder_type);


	/* get the tests associated with this content */
	if (!empty($items[$item_id]['tests']) || strpos($items[$item_id]['type'], 'imsqti_xmlv1p2/imscc_xmlv1p0') !== false){
		$qti_import = new QTIImport($import_path);
		if (isset($items[$item_id]['tests'])){
			$loop_var = $items[$item_id]['tests'];
		} else {
			$loop_var = $items[$item_id]['file'];
		}

		foreach ($loop_var as $array_id => $test_xml_file){
			//check if this item is the qti item object, or it is the content item obj
			//switch it to qti obj if it's content item obj
			if ($items[$item_id]['type'] == 'webcontent'){
				$item_qti = $items[$array_id];
			} else {
				$item_qti = $items[$item_id];
			}
			//call subrountine to add the questions.
			$qids = addQuestions($test_xml_file, $item_qti, $import_path);

			//import test
			if ($test_title==''){
				$test_title = $content_info['title'];
			}

			$tid = $qti_import->importTest($test_title);

			//associate question and tests
			foreach ($qids as $order=>$qid){
				if (isset($qti_import->weights[$order])){
					$weight = round($qti_import->weights[$order]);
				} else {
					$weight = 0;
				}
				$new_order = $order + 1;
				$testsQuestionsAssocDAO->Create($tid, $qid, $weight, $new_order);

			}

			//associate content and test
			$contentTestsAssocDAO->Create($items[$item_id]['real_content_id'], $tid);


		}
	}

	/* get the a4a related xml */
	if (isset($items[$item_id]['a4a_import_enabled']) && isset($items[$item_id]['a4a']) && !empty($items[$item_id]['a4a'])) {
		$a4a_import = new A4aImport($items[$item_id]['real_content_id']);
		$a4a_import->setRelativePath($items[$item_id]['new_path']);
		$a4a_import->importA4a($items[$item_id]['a4a']);
	}

	// get the discussion tools (dependent to content)
	if (isset($items[$item_id]['forum']) && !empty($items[$item_id]['forum'])){
		foreach($items[$item_id]['forum'] as $forum_ref => $forum_link){
			$dt_parser = new DiscussionToolsParser();
			$dt_import = new DiscussionToolsImport();

			//if this forum has not been added, parse it and add it.
			if (!isset($added_dt[$forum_ref])){
				$xml_content = @file_get_contents($import_path . $forum_link);
				$dt_parser->parse($xml_content);
				$forum_obj = $dt_parser->getDt();
				$dt_import->import($forum_obj, $items[$item_id]['real_content_id'], $_course_id);
				$added_dt[$forum_ref] = $dt_import->getFid();				
			}

		}
	} elseif ($items[$item_id]['type']=='imsdt_xmlv1p0'){
		//optimize this, repeated codes as above
		$dt_parser = new DiscussionToolsParser();
		$dt_import = new DiscussionToolsImport();
		$xml_content = @file_get_contents($import_path . $content_info['href']);
		$dt_parser->parse($xml_content);
		$forum_obj = $dt_parser->getDt();
		$dt_import->import($forum_obj, $items[$item_id]['real_content_id'], $_course_id);
		$added_dt[$item_id] = $dt_import->getFid();

	}
}

//exit;//harris
if ($package_base_path == '.') {
	$package_base_path = '';
}

// create course directory
if (!is_dir($course_dir)) {
	if (!@mkdir($course_dir, 0700)) {
		$msg->addError('IMPORTDIR_FAILED');
	}
}



//--- harris edit for path thing
$file = $import_path.$common_path;
if (is_dir($file)) {
    rename($file, TR_CONTENT_DIR.$_course_id.DIRECTORY_SEPARATOR.$package_base_name);
}
//--- end
//takes care of the condition where the whole package doesn't have any contents but question banks
//also is the case of urls
if(is_array($all_package_base_path)){
	$all_package_base_path = implode('/', $all_package_base_path);

	if(strpos($all_package_base_path, 'http:/')===false){
		if (rename($import_path.$all_package_base_path, $course_dir.$package_base_name) === false) {
	        if (!$msg->containsErrors()) {
				if ($oauth_import) {
					echo "error=".urlencode('Cannot move lesson directory into content directory');
				} else {
					$msg->addError('IMPORT_FAILED');
				}
	        }
	    }
	}
}

FileUtility::clr_dir($import_path);

if (file_exists($full_filename)) @unlink($full_filename);

if ($oauth_import) {
	echo 'course_id='.$_course_id;
} else {
	if (!$msg->containsErrors()) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
	header('Location: ../course/index.php?_course_id='.$_course_id);
}
exit;

?>
