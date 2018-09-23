<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }

function in_array_cin($strItem, $arItems)
{
   foreach ($arItems as $key => $strValue)
   {
       if (strtoupper($strItem) == strtoupper($strValue))
       {
		   return $key;
       }
   }
   return false;
} 


function get_tabs() {
/* Check if the page template_layout and are enabled or disabled */
        include_once(TR_INCLUDE_PATH.'classes/DAO/DAO.class.php');
        $dao = new DAO();
        
        $inc=0; 
        $tabs[$inc] = array('content',       		'edit.inc.php',          'n');
        
        $sql="SELECT value FROM ".TABLE_PREFIX."config WHERE name='enable_template_layout'";
        $result=$dao->execute($sql);
        if(is_array($result))
        {
            foreach ($result as $support) {
                if($support['value']==TR_STATUS_ENABLED)
                    $tabs[++$inc] = array('layouts', 'layout.inc.php', 'l');
            }  
        }
        $sql="SELECT value FROM ".TABLE_PREFIX."config WHERE name='enable_template_page'";
        $result=$dao->execute($sql);
        if(is_array($result))
        {
            foreach ($result as $support) {
                if($support['value']==TR_STATUS_ENABLED)
                    $tabs[++$inc] = array('page_templates', 'page_template.inc.php', 'g');
            }  
        }

	$tabs[++$inc] = array('metadata',    		'properties.inc.php',    'p');
	$tabs[++$inc] = array('alternative_content', 'alternatives.inc.php',  'a');	
	$tabs[++$inc] = array('tests',               'tests.inc.php',         't');
	return $tabs;
}


function output_tabs($current_tab, $changes) {
	global $_base_path;
	$tabs = get_tabs();
	$num_tabs = count($tabs);
?>
	<table class="etabbed-table">
	<tr>		
		<?php 
		for ($i=0; $i < $num_tabs; $i++): 
			if ($current_tab == $i):?>
				<td class="editor_tab_selected">
					<?php if ($changes[$i]): ?>
						<img src="<?php echo $_base_path; ?>images/changes_bullet.gif" alt="<?php echo _AT('usaved_changes_made'); ?>" height="12" width="15" />
					<?php endif; ?>
					<?php echo _AT($tabs[$i][0]); ?>
				</td>
				<td class="tab-spacer">&nbsp;</td>
			<?php else: ?>
				<td class="editor_tab">
					<?php if ($changes[$i]): ?>
						<img src="<?php echo $_base_path; ?>images/changes_bullet.gif" alt="<?php echo _AT('usaved_changes_made'); ?>" height="12" width="15" />
					<?php endif; ?>

					<?php echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="editor_buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'pointer\';" '.$clickEvent.' />'; ?>
				</td>
				<td class="tab-spacer">&nbsp;</td>
			<?php endif; ?>
		<?php endfor; ?>
		<td >&nbsp;</td>
	</tr>
	</table>
<?php }
/**
 * Strips all tags and encodes special characters in the URL
 * Returns false if the URL is invalid
 * 
 * @param string $url
 * @return mixed - returns a stripped and encoded URL or false if URL is invalid
 */
function isValidURL($url) {
    if (substr($url,0,4) === 'http') {
        return filter_var(filter_var($url, FILTER_SANITIZE_STRING), FILTER_VALIDATE_URL);
    }
    return false;
}

/*
 * Parse the primary resources out of the content and save into db.
 * Clean up the removed primary resources from db.
 * @param: $cid: content id
 * @param: $content
 * @return: none
 */
function populate_a4a($cid, $content, $formatting){
	global $my_files, $content_base_href, $contentManager;
	
	// Defining alternatives is only available for content type "html".
	// But don't clean up the a4a tables at other content types in case the user needs them back at html.
	
	
	if ($formatting <> 1) return;

	include_once(TR_INCLUDE_PATH.'classes/A4a/A4a.class.php');
	include_once(TR_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
	include_once(TR_INCLUDE_PATH.'classes/ContentOutputParser.class.php');	/* for parser */
	
	// initialize content_base_href; used in format_content
	if (!isset($content_base_href)) {
		$content_row = $contentManager->getContentPage($cid);
		// return if the cid is not found
		if (!is_array($content_row)) {
			return;
		}
		$content_base_href = $content_row["content_path"].'/';
	}
	
	$body = ContentUtility::formatContent($content, $formatting);
    
	$handler = new ContentOutputParser();
	$parser = new XML_HTMLSax();
	$parser->set_object($handler);
	$parser->set_element_handler('openHandler','closeHandler');
	
	$my_files 		= array();
	$parser->parse($body);
	$my_files = array_unique($my_files);
	
	foreach ($my_files as $file) {
		/* filter out full urls */
		$url_parts = @parse_url($file);
		
		// file should be relative to content
		if ((substr($file, 0, 1) == '/')) {
			continue;
		}
		
		// The URL of the movie from youtube.com has been converted above in embed_media().
		// For example:  http://www.youtube.com/watch?v=a0ryB0m0MiM is converted to
		// http://www.youtube.com/v/a0ryB0m0MiM to make it playable. This creates the problem
		// that the parsed-out url (http://www.youtube.com/v/a0ryB0m0MiM) does not match with
		// the URL saved in content table (http://www.youtube.com/watch?v=a0ryB0m0MiM).
		// The code below is to convert the URL back to original.
		$file = ContentUtility::convertYoutubePlayURLToWatchURL($file);
		
		$resources[] = convertAmp($file);  // converts & to &amp;
	}
    
    $a4a = new A4a($cid);
    $db_primary_resources = $a4a->getPrimaryResources();
    
    // clean up the removed resources
    foreach ($db_primary_resources  as $primary_rid=>$db_resource){
        //if this file from our table is not found in the $resource, then it's not used.
    	if(count($resources) == 0 || !in_array($db_resource['resource'], $resources)){
			// The following ends up deleting all original resourse type from the db
			// Why is it here?
        	//$a4a->deletePrimaryResource($primary_rid);
        }
    }
    
    if (count($resources) == 0) return;

	// insert the new resources
    foreach($resources as $primary_resource)
	{
		if (!$a4a->getPrimaryResourceByName($primary_resource)){
			$a4a->setPrimaryResource($cid, $primary_resource, $_SESSION['lang']);
		}
	}
}

// save all changes to the DB
function save_changes($redir, $current_tab) {
	global $contentManager, $msg, $_course_id, $_content_id;
	
	$_POST['pid']	= intval($_POST['pid']);
	$_POST['_cid']	= intval($_POST['_cid']);
	
	
	$_POST['alternatives'] = intval($_POST['alternatives']);
	
	$_POST['title'] = trim($_POST['title']);
	$_POST['head']	= trim($_POST['head']);
	$_POST['use_customized_head']	= isset($_POST['use_customized_head'])?$_POST['use_customized_head']:0;
	$_POST['body_text']	= stripslashes(trim($_POST['body_text']));
	$_POST['weblink_text'] = trim($_POST['weblink_text']);
	$_POST['formatting'] = intval($_POST['formatting']);
	$_POST['keywords']	= stripslashes(trim($_POST['keywords']));
	$_POST['test_message'] = trim($_POST['test_message']);

	//if weblink is selected, use it
	if ($_POST['formatting']==CONTENT_TYPE_WEBLINK) {
	    $url = $_POST['weblink_text'];
	    $validated_url = isValidURL($url);
        if (!validated_url || $validated_url !== $url) {
	       $msg->addError(array('INVALID_INPUT', _AT('weblink')));
	    } else {
		    $_POST['body_text'] = $url;
		    $content_type_pref = CONTENT_TYPE_WEBLINK;
	    }
	} else {
		$content_type_pref = CONTENT_TYPE_CONTENT;
	}

		// add or edit content
		if ($_POST['_cid']) {
			/* editing an existing page */
			$err = $contentManager->editContent($_POST['_cid'], $_POST['title'], $_POST['body_text'], 
			                                    $_POST['keywords'], $_POST['formatting'], 
			                                    $_POST['head'], $_POST['use_customized_head'], 
			                                    $_POST['test_message']);
    
                                           
			$cid = $_POST['_cid'];
		} else {
			/* insert new */
			$cid = $contentManager->addContent($_course_id,
												  $_POST['pid'],
												  $_POST['ordering'],
												  $_POST['title'],
												  $_POST['body_text'],
												  $_POST['keywords'],
												  $_POST['related'],
												  $_POST['formatting'],
												  $_POST['head'],
												  $_POST['use_customized_head'],
												  $_POST['test_message'],
												  $content_type_pref);
												  
			$_POST['_cid']    = $cid;
			$_REQUEST['_cid'] = $cid;
		}
		
		
        
        
		if ($cid == 0) return;
		
		// re-populate a4a tables based on the new content
		populate_a4a($cid, $orig_body_text, $_POST['formatting']);
		
		
	if (isset($_GET['tab'])) {
		$current_tab = intval($_GET['tab']);
	}
	if (isset($_POST['current_tab'])) {
		$current_tab = intval($_POST['current_tab']);
	}

	// adapted content: save primary content type
	if (isset($_POST['use_post_for_alt']))
	{
		include_once(TR_INCLUDE_PATH.'classes/DAO/PrimaryResourcesTypesDAO.class.php');
		$primaryResourcesTypesDAO = new PrimaryResourcesTypesDAO();
		
		// 1. delete old primary content type

		$sql = "DELETE FROM ".TABLE_PREFIX."primary_resources_types
		         WHERE primary_resource_id in 
		               (SELECT DISTINCT primary_resource_id 
		                  FROM ".TABLE_PREFIX."primary_resources
		                 WHERE content_id=?
		                   AND language_code=?)";
		$values=array($cid, $_SESSION['lang']);
		$types = "ii";
		$primaryResourcesTypesDAO->execute($sql, $values, $types);
		
		// 2. insert the new primary content type

		$sql = "SELECT pr.primary_resource_id, rt.type_id
		          FROM ".TABLE_PREFIX."primary_resources pr, ".
		                 TABLE_PREFIX."resource_types rt
		         WHERE pr.content_id = ?
		           AND pr.language_code = ?";
		$values = array($cid, $_SESSION['lang']);
		$types = "is";
		$all_types_rows = $primaryResourcesTypesDAO->execute($sql, $values, $types);
		
		if (is_array($all_types_rows)) {
			foreach ($all_types_rows as $type) {
				if (isset($_POST['alt_'.$type['primary_resource_id'].'_'.$type['type_id']]))
				{
					$primaryResourcesTypesDAO->Create($type['primary_resource_id'], $type['type_id']);
				}
			}
		}
	}
	
	include_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');
	$contentTestsAssocDAO = new ContentTestsAssocDAO();
	$test_rows = $contentTestsAssocDAO->getByContent($_POST['_cid']);
	$db_test_array = array();
	if (is_array($test_rows)) {
		foreach ($test_rows as $row) {
			$db_test_array[] = $row['test_id'];
		}
	}

	if (is_array($_POST['tid']) && sizeof($_POST['tid']) > 0){
		$toBeDeleted = array_diff($db_test_array, $_POST['tid']);
		$toBeAdded = array_diff($_POST['tid'], $db_test_array);
		//Delete entries
		if (!empty($toBeDeleted)){
			$num_of_ids = count($toBeDeleted);
			$sql = 'DELETE FROM '. TABLE_PREFIX .'content_tests_assoc WHERE content_id=? AND test_id IN ('.substr(str_repeat("? , ", $num_of_ids), 0, -2).')';
			$values = $toBeDeleted;
			$types = "i";
			$types .= str_pad("", $num_of_ids, "i");
			$contentTestsAssocDAO->execute($sql, $values, $types);
		}

		//Add entries
		if (!empty($toBeAdded)){
			foreach ($toBeAdded as $i => $tid){
				$tid = intval($tid);

				if ($contentTestsAssocDAO->Create($_POST['_cid'], $tid) === false){
					$msg->addError('DB_NOT_UPDATED');
				}
			}
		}
	} else {
		//All tests has been removed.
		$contentTestsAssocDAO->DeleteByContentID($_POST['_cid']);
	}
	//End Add test

	if (!$msg->containsErrors() && $redir) {
		$_SESSION['save_n_close'] = $_POST['save_n_close'];
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.basename($_SERVER['PHP_SELF']).'?_cid='.$cid.SEP.'close='.addslashes($_POST['save_n_close']).SEP.'tab='.addslashes($_POST['current_tab']).SEP.'displayhead='.addslashes($_POST['displayhead']).SEP.'alternatives='.addslashes($_POST['alternatives']));
		exit;
	} else {
		return;
	}
}

function check_for_changes($row, $row_alternatives) {
	global $contentManager, $cid, $glossary, $glossary_ids_related;

	$changes = array();

	if ($row && strcmp(trim(addslashes($_POST['title'])), addslashes($row['title']))) {
		$changes[0] = true;
	} else if (!$row && $_POST['title']) {
		$changes[0] = true;
	}

	if ($row && strcmp(addslashes(trim($_POST['head'])), trim(addslashes($row['head'])))) {
		$changes[0] = true;
	} else if (!$row && $_POST['head']) {
		$changes[0] = true;
	}

	if ($row && strcmp(addslashes(trim($_POST['body_text'])), trim(addslashes($row['text'])))) {
		$changes[0] = true;
	} else if (!$row && $_POST['body_text']) {
		$changes[0] = true;
	}
	
    if ($row && strcmp(addslashes(trim($_POST['weblink_text'])), trim(addslashes($row['text'])))) {
        $changes[0] = true;
    } else if (!$row && $_POST['weblink_text']) {
        $changes[0] = true;
    }

	/* use customized head: */
	if ($row && isset($_POST['use_customized_head']) && ($_POST['use_customized_head'] != $row['use_customized_head'])) {
		$changes[0] = true;
	}

	/* formatting: */
	if ($row && strcmp(trim($_POST['formatting']), $row['formatting'])) {
		$changes[0] = true;
	} else if (!$row && $_POST['formatting']) {
		$changes[0] = true;
	}

	/* keywords */
	if ($row && strcmp(trim($_POST['keywords']), $row['keywords'])) {
		$changes[1] = true;
	}  else if (!$row && $_POST['keywords']) {
		$changes[1] = true;
	}

	/* adapted content */
	if (isset($_POST['use_post_for_alt']))
	{
		foreach ($_POST as $alt_id => $alt_value) {
			if (substr($alt_id, 0 ,4) == 'alt_' && $alt_value != $row_alternatives[$alt_id]){
				$changes[2] = true;
				break;
			}
		}
	}
	
	/* test & survey */	
	if ($row && isset($_POST['test_message']) && $_POST['test_message'] != $row['test_message']){
		$changes[3] = true;
	}
	
	$content_tests = $contentManager->getContentTestsAssoc($cid);
	
	if (isset($_POST['visited_tests'])) {
		if (!is_array($content_tests) && is_array($_POST['tid'])) {
			$changes[3] = true;
		}
		if (is_array($content_tests)) {
			for ($i = 0; $i < count($content_tests); $i++) {
				if ($content_tests[$i]['test_id'] <> $_POST['tid'][$i]) {
					$changes[3] = true;
					break;
				}
			}
		}
	}

	return $changes;
}

function paste_from_file() {
	global $msg;
	
	include_once(TR_INCLUDE_PATH.'../home/classes/ContentUtility.class.php');
	if ($_FILES['uploadedfile_paste']['name'] == '')	{
		$msg->addError('FILE_NOT_SELECTED');
		return;
	}
	if ($_FILES['uploadedfile_paste']['name']
		&& (($_FILES['uploadedfile_paste']['type'] == 'text/plain')
			|| ($_FILES['uploadedfile_paste']['type'] == 'text/html')) )
		{

		$path_parts = pathinfo($_FILES['uploadedfile_paste']['name']);
		$ext = strtolower($path_parts['extension']);

		if (in_array($ext, array('html', 'htm'))) {
			$_POST['body_text'] = file_get_contents($_FILES['uploadedfile_paste']['tmp_name']);

			/* get the <title></title> of this page				*/

			$start_pos	= strpos(strtolower($_POST['body_text']), '<title>');
			$end_pos	= strpos(strtolower($_POST['body_text']), '</title>');

			if (($start_pos !== false) && ($end_pos !== false)) {
				$start_pos += strlen('<title>');
				$_POST['title'] = trim(substr($_POST['body_text'], $start_pos, $end_pos-$start_pos));
			}
			unset($start_pos);
			unset($end_pos);

			$_POST['head'] = ContentUtility::getHtmlHeadByTag($_POST['body_text'], array("link", "style", "script")); 
			if (strlen(trim($_POST['head'])) > 0)	
				$_POST['use_customized_head'] = 1;
			else
				$_POST['use_customized_head'] = 0;
			
			$_POST['body_text'] = ContentUtility::getHtmlBody($_POST['body_text']); 

			$msg->addFeedback('FILE_PASTED');
		} else if ($ext == 'txt') {
			$_POST['body_text'] = file_get_contents($_FILES['uploadedfile_paste']['tmp_name']);
			//LAW
			$msg->addFeedback('FILE_PASTED');

		}
	} else {
		$msg->addError('BAD_FILE_TYPE');
	}

	return;
}

//for accessibility checker
function write_temp_file() {
	global $_POST, $msg;

	if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
		$content_base = 'get.php/';
	} else {
		$content_base = 'content/' . $_SESSION['course_id'] . '/';
	}

	if ($_POST['content_path']) {
		$content_base .= $_POST['content_path'] . '/';
	}

	$file_name = $_POST['_cid'].'.html';

	if ($handle = fopen(TR_CONTENT_DIR . $file_name, 'wb+')) {
	
		if (!@fwrite($handle, stripslashes($_POST['body_text']))) {
			$msg->addError('FILE_NOT_SAVED');       
	   }
	} else {
		$msg->addError('FILE_NOT_SAVED');
	}
	$msg->printErrors();
}
?>
