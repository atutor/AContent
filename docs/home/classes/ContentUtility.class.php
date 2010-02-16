<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* Content Utility functions 
* @access	public
* @author	Cindy Qi Li
*/

if (!defined('TR_INCLUDE_PATH')) exit;

class ContentUtility {

	/**
	* This function cuts out html body
	* @access  public
	* @param   $text  html text
	* @author  Cindy Qi Li
	*/
	public static function getHtmlBody($text) {
		/* strip everything before <body> */
		$start_pos	= strpos(strtolower($text), '<body');
		if ($start_pos !== false) {
			$start_pos	+= strlen('<body');
			$end_pos	= strpos(strtolower($text), '>', $start_pos);
			$end_pos	+= strlen('>');
	
			$text = substr($text, $end_pos);
		}
	
		/* strip everything after </body> */
		$end_pos	= strpos(strtolower($text), '</body>');
		if ($end_pos !== false) {
			$text = trim(substr($text, 0, $end_pos));
		}
	
		return $text;
	}

	/**
	* This function cuts out requested tag information from html head
	* @access  public
	* @param   $text  html text
	* @param   $tags  a string or an array of requested tags
	* @author  Cindy Qi Li
	*/
	public static function getHtmlHeadByTag($text, $tags)
	{
		$head = ContentUtility::getHtmlHead($text);
		$rtn_text = "";
		
		if (!is_array($tags) && strlen(trim($tags)) > 0)
		{
			$tags = array(trim($tags));
		}
		foreach ($tags as $tag)
		{
			$tag = strtolower($tag);
	
			/* strip everything before <{tag}> */
			$start_pos	= stripos($head, '<'.$tag);
			$temp_head = $head;
			
			while ($start_pos !== false) 
			{
				$temp_text = substr($temp_head, $start_pos);
		
				/* strip everything after </{tag}> or />*/
				$end_pos	= stripos($temp_text, '</' . $tag . '>');
		
				if ($end_pos !== false) 
				{
					$end_pos += strlen('</' . $tag . '>');
					
					// add an empty line after each tag information
					$rtn_text .= trim(substr($temp_text, 0, $end_pos)) . '
		
	';
				}
				else  // match /> as ending tag if </tag> is not found
				{
					$end_pos	= stripos($temp_text, '/>');
					
					if($end_pos === false && stripos($temp_text, $tag.'>')===false){
						//if /> is not found, then this is not a valid XHTML
						//text iff it's not tag>
						$end_pos = stripos($temp_text, '>');
						$end_pos += strlen('>');
					} else {
						$end_pos += strlen('/>');
					}
					// add an empty line after each tag information
					$rtn_text .= trim(substr($temp_text, 0, $end_pos)) . '
		
	';
				}
				
				// initialize vars for next round of matching
				$temp_head = substr($temp_text, $end_pos);
				$start_pos = stripos($temp_head, '<'.$tag);
			}
		}
		return $rtn_text;
	}

	/**
	* This function cuts out html head
	* @access  private
	* @param   $text  html text
	* @author  Cindy Qi Li
	*/
	private static function getHtmlHead ($text) {
		/* strip everything before <head> */
		$start_pos	= stripos($text, '<head');
		if ($start_pos !== false) {
			$start_pos	+= strlen('<head');
			$end_pos	= stripos($text, '>', $start_pos);
			$end_pos	+= strlen('>');
	
			$text = substr($text, $end_pos);
		}
	
		/* strip everything after </head> */
		$end_pos	= stripos($text, '</head');
		if ($end_pos !== false) {
			$text = trim(substr($text, 0, $end_pos));
		}
		return $text;
	}

	/**
	* This function converts the input string into Transformable html content string 
	* @access  public
	* @param   $input: input string
	*          $html: whether the input is in html
	* @return  converted Transformable html content string
	* @author  Cindy Qi Li
	*/
	public static function formatContent($input, $html = 0) {
		global $_base_path, $_config;
	
		if (!$html) {
			$input = str_replace('<', '&lt;', $input);
			$input = str_replace('&lt;?php', '<?php', $input); // for bug #2087
		} elseif ($html==2) {
			$output = '<iframe width="100%" frameborder="0" id="content_frame" marginheight="0" marginwidth="0" src="'.$input.'"></iframe>';
			$output .=	'<script type="text/javascript">
						function resizeIframe() {
							var height = document.documentElement.clientHeight;
							
							// not sure how to get this dynamically
							height -= 20; /* whatever you set your body bottom margin/padding to be */
							
							document.getElementById(\'content_frame\').style.height = height +"px";
							
						};
						document.getElementById(\'content_frame\').onload = resizeIframe;
						window.onresize = resizeIframe;
						</script>';
			return $output;
		}
	
		/* Commented by Cindy Qi Li on Jan 12, 2010
		 * Transformable does not support glossary
		// do the glossary search and replace:
		if (is_array($glossary)) {
			foreach ($glossary as $k => $v) {
				$k = urldecode($k);
				$v = str_replace("\n", '<br />', $v);
				$v = str_replace("\r", '', $v);
	
				// escape special characters
				$k = preg_quote($k);
	
				$k = str_replace('&lt;', '<', $k);
				$k = str_replace('/', '\/', $k);
	
				$original_term = $k;
				$term = $original_term;
	
		 		$term = '(\s*'.$term.'\s*)';
				$term = str_replace(' ','((<br \/>)*\s*)', $term); 
	
				$def = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');		
				if ($simple) {
					$input = preg_replace
							("/(\[\?\])$term(\[\/\?\])/i",
							'<a href="'.$simple.'glossary.html#'.urlencode($original_term).'" target="body" class="at-term">\\2</a>',
							$input);
				} else {
					$input = preg_replace
							("/(\[\?\])$term(\[\/\?\])/i",
							'\\2<sup><a class="tooltip" href="'.$_base_path.'glossary/index.php?g_cid='.$_SESSION['s_cid'].htmlentities(SEP).'w='.urlencode($original_term).'#term" title="'.addslashes($original_term).': '.$def.'"><span style="color: blue; text-decoration: none;font-size:small; font-weight:bolder;">?</span></a></sup>',$input);
				}
			}
		} else if (!$user_glossary) {
			$input = str_replace(array('[?]','[/?]'), '', $input);
		}
		*/

		
		$input = str_replace('CONTENT_DIR', '', $input);
	
		if (isset($_config['latex_server']) && $_config['latex_server']) {
			// see: http://www.forkosh.com/mimetex.html
			$input = preg_replace('/\[tex\](.*?)\[\/tex\]/sie', "'<img src=\"'.\$_config['latex_server'].rawurlencode('$1').'\" align=\"middle\">'", $input);
		}
	
		/* Commented by Cindy Qi Li on Jan 12, 2010
		if ($html) {
			$x = apply_customized_format(format_final_output($input, false));
			return $x;
		}
	
		$output = apply_customized_format(format_final_output($input));
		*/
	
		$output = '<p>'.$input.'</p>';
	
		return $output;
	}
	
	/**
	 * This function returns html string of "table of content"
	 * @access: public
	 * @param: $content: a string
	 * @return: a html string of "table of content"
	 */
	public static function getContentTable($content)
	{
		preg_match_all("/<(h[\d]+)[^>]*>(.*)<\/(\s*)\\1(\s*)>/i", $content, $found_headers, PREG_SET_ORDER);
		
		if (count($found_headers) == 0) return array("", $content);
		else
		{
			$num_of_headers = 0;
	
			for ($i = 0; $i < count($found_headers); $i++)
			{
				$div_id = "_header_" . $num_of_headers++;
				
				if ($i == 0)
				{
					$content_table = "<div id=\"toc\">\n<fieldset id=\"toc\"><legend>". _AT("table_of_contents")."</legend>\n";
				}
	
				$content = str_replace($found_headers[$i][0], '<div id="'.$div_id.'">'.$found_headers[$i][0].'</div>', $content);
				$content_table .= '<a href="'.$_SERVER["REQUEST_URI"].'#'.$div_id.'" class="'.$found_headers[$i][1].'">'. $found_headers[$i][2]."</a>\n";
	
				if ($i == count($found_headers) - 1)
				{
					$content_table .= "</fieldset></div><br />";
				}
			}
			return array($content_table, $content);
		}
	}
	
	/**
	* replace source object with alternatives according to user's preferences
	* @access	public
	* @param	$cid: 				content id.
	* @param	$content:	 		the original content page ($content_row['text'], from content.php).
	* @return	string				$content: the content page with the appropriated resources.
	* @see		$db			        from include/vitals.inc.php
	* @author	Cindy Qi Li
	*/
	public static function applyAlternatives($cid, $content){
		global $db;
		
		include_once(TR_INCLUDE_PATH.'classes/DAO/DAO.class.php');
		$dao = new DAO();
		
		$vidoe_exts = array("mpg", "avi", "wmv", "mov", "swf", "mp3", "wav", "ogg", "mid");
		$txt_exts = array("txt", "html", "htm");
		
		if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==0) && ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO']==0) && ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL']==0)) 
		{
			//No user's preferences related to content format are declared
			return $content;
		}
		
		// get all relations between primary resources and their alternatives
		$sql = "SELECT c.content_path, pr.resource, prt.type_id primary_type, sr.secondary_resource, srt.type_id secondary_type
		          FROM ".TABLE_PREFIX."primary_resources pr, ".
		                 TABLE_PREFIX."primary_resources_types prt,".
		                 TABLE_PREFIX."secondary_resources sr,".
		                 TABLE_PREFIX."secondary_resources_types srt,".
		                 TABLE_PREFIX."content c
		         WHERE pr.content_id=".$cid."
			       AND pr.primary_resource_id = prt.primary_resource_id
			       AND pr.primary_resource_id = sr.primary_resource_id
			       AND sr.language_code='".$_SESSION['prefs']['PREF_ALT_AUDIO_PREFER_LANG']."'
			       AND sr.secondary_resource_id = srt.secondary_resource_id
		           AND pr.content_id = c.content_id
			     ORDER BY pr.primary_resource_id, prt.type_id";
		
		$rows = $dao->execute($sql);
	
		if (!is_array($rows)) return $content;
		
		foreach ($rows as $row) 
		{
			if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==1 && $row['primary_type']==3 &&
			    ($_SESSION['prefs']['PREF_ALT_TO_TEXT']=="audio" && $row['secondary_type']==1 || 
			     $_SESSION['prefs']['PREF_ALT_TO_TEXT']=="visual" && $row['secondary_type']==4 || 
			     $_SESSION['prefs']['PREF_ALT_TO_TEXT']=="sign_lang" && $row['secondary_type']==2)) ||
			     
			     ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO']==1 && $row['primary_type']==1 &&
			     ($_SESSION['prefs']['PREF_ALT_TO_AUDIO']=="visual" && $row['secondary_type']==4 || 
			      $_SESSION['prefs']['PREF_ALT_TO_AUDIO']=="text" && $row['secondary_type']==3 || 
			      $_SESSION['prefs']['PREF_ALT_TO_AUDIO']=="sign_lang" && $row['secondary_type']==2)) ||
			      
			     ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL']==1 && $row['primary_type']==4 &&
			     ($_SESSION['prefs']['PREF_ALT_TO_VISUAL']=="audio" && $row['secondary_type']==1 || 
			      $_SESSION['prefs']['PREF_ALT_TO_VISUAL']=="text" && $row['secondary_type']==3 || 
			      $_SESSION['prefs']['PREF_ALT_TO_VISUAL']=="sign_lang" && $row['secondary_type']==2))
			    )
			{
				$ext = substr($row['secondary_resource'], strrpos($row['secondary_resource'], '.')+1);
				
				// alternative is video
				if (in_array($ext, $vidoe_exts))
					$target = '[media]'.$row['secondary_resource'].'[/media]';
				// a text primary to be replaced by a visual alternative 
				else if (in_array($ext, $txt_exts))
				{
					if (substr($row['secondary_resource'], 0, 2) == '..') 
						$file_location = substr($row['secondary_resource'], 3);
					else 
						$file_location = $row['secondary_resource'];
					$file .= $file_location;
					
					if ($row['content_path'] <> '') {
						$file = AT_CONTENT_DIR.$_SESSION['course_id'] . '/'.$row['content_path'].'/'.$file_location;
					}
					else {
						$file = AT_CONTENT_DIR.$_SESSION['course_id'] . '/'.$file_location;
					}
					$target = file_get_contents($file);
					
					// check whether html file
					if (preg_match('/.*\<html.*\<\/html\>.*/s', $target))
					{ // is a html file, use iframe to display
						// get real path to the text file
						if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
							$course_base_href = 'get.php/';
						} else {
							$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
						}
		
						$file = AT_BASE_HREF . $course_base_href.$file_location;
							
						$target = '<iframe width="100%" frameborder="0" class="autoHeight" scrolling="auto" src="'.$file.'"></iframe>';
					}
					else
					{ // is a text file, insert/replace into content
						$target = nl2br($target);
					}
				} 
				else if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==1 
				         && $_SESSION['prefs']['PREF_ALT_TO_TEXT']=="visual")
					$target = '<img border="0" alt="Alternate Text" src="'.$row['secondary_resource'].'"/>';
				// otherwise
				else
					$target = '<p><a href="'.$row['secondary_resource'].'">'.$row['secondary_resource'].'</a></p>';
				
				// replace or append the target alternative to the source
				if (($row['primary_type']==3 && $_SESSION['prefs']['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE'] == 'replace') ||
					($row['primary_type']==1 && $_SESSION['prefs']['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE']=='replace') ||
					($row['primary_type']==4 && $_SESSION['prefs']['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE']=='replace'))
					$pattern_replace_to = '${1}'.$target.'${3}';
				else
					$pattern_replace_to = '${1}${2}'.$target.'${3}';
					
				// append/replace target alternative to [media]source[/media]
				$content = preg_replace("/(.*)(".preg_quote("[media]".$row['resource']."[/media]", "/").")(.*)/s", 
				             $pattern_replace_to, $content);
				
				// append/replace target alternative to <a>...source...</a> or <a ...source...>...</a>
				if (preg_match("/\<a.*".preg_quote($row['resource'], "/").".*\<\/a\>/s", $content))
				{
					$content = preg_replace("/(.*)(\<a.*".preg_quote($row['resource'], "/").".*\<\/a\>)(.*)/s", 
			                                $pattern_replace_to, $content);
				}
	
				// append/replace target alternative to <img ... src="source" ...></a>
				if (preg_match("/\<img.*src=\"".preg_quote($row['resource'], "/")."\".*\/\>/s", $content))
				{
					$content = preg_replace("/(.*)(\<img.*src=\"".preg_quote($row['resource'], "/")."\".*\/\>)(.*)/s", 
			                                $pattern_replace_to, $content);
				}
				
				// append/replace target alternative to <object ... source ...></object>
				if (preg_match("/\<object.*".preg_quote($row['resource'], "/").".*\<\/object\>/s", $content))
				{
					$content = preg_replace("/(.*)(\<object.*".preg_quote($row['resource'], "/").".*\<\/object\>)(.*)/s", 
			                                $pattern_replace_to, $content);
				}
	
				// append/replace target alternative to <embed ... source ...>
				if (preg_match("/\<embed.*".preg_quote($row['resource'], "/").".*\>/s", $content))
				{
					$content = preg_replace("/(.*)(\<embed.*".preg_quote($row['resource'], "/").".*\>)(.*)/s", 
			                                $pattern_replace_to, $content);
				}
			}
		}
		return $content;
	}	
		
	/**
	 * This function save the last content_id accessed by current user on a course into db and set $_SESSION['s_cid']
	 * @access: public
	 * @param: $content_id
	 * @return: save $content_id, the last visited one of the current user, into db and session
	 */
	public static function saveLastCid($content_id)
	{
		global $_course_id;
		
		if (!$content_id) return;
		
		include_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');
		
		$userCoursesDAO = new UserCoursesDAO();
		if ($userCoursesDAO->isExist($_SESSION['user_id'], $_course_id))
		{
			$userCoursesDAO->UpdateLastCid($_SESSION['user_id'], $_course_id, $content_id);
			$_SESSION['s_cid'] = $content_id;
		}
	}
}
?>