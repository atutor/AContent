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

	private static function embedFLV($text) {
		global $content_base_href;
		
		// .flv - uses Flowplayer 3.0 from flowplayer.org (playing file via full URL)
		preg_match_all("#\[media[0-9a-z\|]*\]http://([\w\./-]+)\.flv\[/media\]#i",$text,$media_matches[0],PREG_SET_ORDER);
		$media_replace[0] ="<a class=\"flowplayerholder\"
		style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
		href=\"http://##MEDIA1##.flv\">
		</a>";
		
		// .flv - uses Flowplayer 3.0 from flowplayer.org (playing file from AT_content_dir)
		preg_match_all("#\[media[0-9a-z\|]*\]([\w\./-]+)\.flv\[/media\]#i",$text,$media_matches[1],PREG_SET_ORDER);
		$media_replace[1] ="<a class=\"flowplayerholder\"
		style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
		href=\"".TR_BASE_HREF."get.php/".$content_base_href."##MEDIA1##.flv\">
		</a>";
		
		$has_flv = false;
		// Executing the replace
		for ($i=0;$i<count($media_replace);$i++){
			foreach($media_matches[$i] as $media)
			{
				if (is_array($media)) $has_flv = true;
				
				//find width and height for each matched media
				if (preg_match("/\[media\|([0-9]*)\|([0-9]*)\]*/", $media[0], $matches)) 
				{
					$width = $matches[1];
					$height = $matches[2];
				}
				else
				{
					$width = 425;
					$height = 350;
				}
				
				//replace media tags with embedded media for each media tag
				$media_input = $media_replace[$i];
				$media_input = str_replace("##WIDTH##","$width",$media_input);
				$media_input = str_replace("##HEIGHT##","$height",$media_input);
				$media_input = str_replace("##MEDIA1##","$media[1]",$media_input);
				$media_input = str_replace("##MEDIA2##","$media[2]",$media_input);
				$text = str_replace($media[0],$media_input,$text);
			}
		}
		
		if ($has_flv)
		{
			$text .= '
			<script language="JavaScript">
				$f("a.flowplayerholder", "'.TR_BASE_HREF.'include/jscripts/flowplayer/flowplayer-3.1.2.swf", { 
				 	clip: { autoPlay: false },  		
			        plugins:  { 
				        controls: { 
				            all: false,  
				            play: true,  
				            scrubber: true 
				        }         
				    }
				});
			</script>
			';
		}
		
		return $text;		
	}
	
	public static function embedMedia($text) {
		if (preg_match("/\[media(\|[0-9]+\|[0-9]+)?\]*/", $text)==0){
			return $text;
		}
	
		$media_matches = Array();
		
		/*
			First, we search though the text for all different kinds of media defined by media tags and store the results in $media_matches.
			
			Then the different replacements for the different media tags are stored in $media_replace.
			
			Lastly, we loop through all $media_matches / $media_replaces. (We choose $media_replace as index because $media_matches is multi-dimensioned.) It is important that for each $media_matches there is a $media_replace with the same index. For each media match we check the width/height, or we use the default value of 425x350. We then replace the height/width/media1/media2 parameter placeholders in $media_replace with the correct ones, before running a str_replace on $text, replacing the given media with its correct replacement.
			
		*/
		
		// youtube videos
		preg_match_all("#\[media[0-9a-z\|]*\]http://([a-z0-9\.]*)?youtube.com/watch\?v=([a-z0-9_-]+)\[/media\]#i",$text,$media_matches[1],PREG_SET_ORDER);
		$media_replace[1] = '<object width="##WIDTH##" height="##HEIGHT##"><param name="movie" value="http://##MEDIA1##youtube.com/v/##MEDIA2##"></param><embed src="http://##MEDIA1##youtube.com/v/##MEDIA2##" type="application/x-shockwave-flash" width="##WIDTH##" height="##HEIGHT##"></embed></object>';
			
		// .mpg
		preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).mpg\[/media\]#i",$text,$media_matches[2],PREG_SET_ORDER);
		$media_replace[2] = "<object data=\"##MEDIA1##.mpg\" type=\"video/mpeg\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.mpg\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.mpg\">##MEDIA1##.mpg</a></object>";
		
		// .avi
		preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).avi\[/media\]#i",$text,$media_matches[3],PREG_SET_ORDER);
		$media_replace[3] = "<object data=\"##MEDIA1##.avi\" type=\"video/x-msvideo\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.avi\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.avi\">##MEDIA1##.avi</a></object>";
		
		// .wmv
		preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).wmv\[/media\]#i",$text,$media_matches[4],PREG_SET_ORDER);
		$media_replace[4] = "<object data=\"##MEDIA1##.wmv\" type=\"video/x-ms-wmv\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.wmv\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.wmv\">##MEDIA1##.wmv</a></object>";
		
		// .mov
		preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).mov\[/media\]#i",$text,$media_matches[5],PREG_SET_ORDER);
		$media_replace[5] = "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.mov\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><!--[if gte IE 7]> <!--><object type=\"video/quicktime\" data=\"##MEDIA1##.mov\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><a href=\"##MEDIA1##.mov\">##MEDIA1##.mov</a></object><!--<![endif]--><!--[if lt IE 7]><a href=\"##MEDIA1##.mov\">##MEDIA1##.mov</a><![endif]--></object>";
		
		// .swf
		preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).swf\[/media\]#i",$text,$media_matches[6],PREG_SET_ORDER);
		$media_replace[6] = "<object type=\"application/x-shockwave-flash\" data=\"##MEDIA1##.swf\" width=\"##WIDTH##\" height=\"##HEIGHT##\">  <param name=\"movie\" value=\"##MEDIA1##.swf\"><param name=\"loop\" value=\"false\"><a href=\"##MEDIA1##.swf\">##MEDIA1##.swf</a></object>";
		
		// .mp3
		preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+).mp3\[/media\]#i",$text,$media_matches[7],PREG_SET_ORDER);
		$media_replace[7] = "<object type=\"audio/mpeg\" data=\"##MEDIA1##.mp3\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.mp3\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.mp3\">##MEDIA1##.mp3</a></object>";
		
		// .wav
		preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+).wav\[/media\]#i",$text,$media_matches[8],PREG_SET_ORDER);
		$media_replace[8] ="<object type=\"audio/x-wav\" data=\"##MEDIA1##.wav\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.wav\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.wav\">##MEDIA1##.wav</a></object>";
		
		// .ogg
		preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+).ogg\[/media\]#i",$text,$media_matches[9],PREG_SET_ORDER);
		$media_replace[9] ="<object type=\"application/ogg\" data=\"##MEDIA1##.ogg\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.ogg\"><a href=\"##MEDIA1##.ogg\">##MEDIA1##.ogg</a></object>";
		
		// .mid
		preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+).mid\[/media\]#i",$text,$media_matches[10],PREG_SET_ORDER);
		$media_replace[10] ="<object type=\"application/x-midi\" data=\"##MEDIA1##.mid\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.mid\"><a href=\"##MEDIA1##.mid\">##MEDIA1##.mid</a></object>";
		
		$text = preg_replace("#\[media[0-9a-z\|]*\](.+[^\s\"]+).mid\[/media\]#i", "<object type=\"application/x-midi\" data=\"\\1.mid\" width=\"".$width."\" height=\"".$height."\"><param name=\"src\" value=\"\\1.mid\"><a href=\"\\1.mid\">\\1.mid</a></object>", $text);
	
		// Executing the replace
		for ($i=1;$i<=count($media_replace);$i++){
			foreach($media_matches[$i] as $media)
			{
				
				//find width and height for each matched media
				if (preg_match("/\[media\|([0-9]*)\|([0-9]*)\]*/", $media[0], $matches)) 
				{
					$width = $matches[1];
					$height = $matches[2];
				}
				else
				{
					$width = 425;
					$height = 350;
				}
				
				//replace media tags with embedded media for each media tag
				$media_input = $media_replace[$i];
				$media_input = str_replace("##WIDTH##","$width",$media_input);
				$media_input = str_replace("##HEIGHT##","$height",$media_input);
				$media_input = str_replace("##MEDIA1##","$media[1]",$media_input);
				$media_input = str_replace("##MEDIA2##","$media[2]",$media_input);
				$text = str_replace($media[0],$media_input,$text);
			}
		}
		
		$text = ContentUtility::embedFLV($text);
		
		return $text;
	}

	private static function makeClickable($text) {
		$text = ContentUtility::embedMedia($text);
	
	//	$text = eregi_replace("([[:space:]])(http[s]?)://([^[:space:]<]*)([[:alnum:]#?/&=])", "\\1<a href=\"\\2://\\3\\4\">\\3\\4</a>", $text);
	//
	//	$text = eregi_replace(	'([_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.
	//							'\@'.'[_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.'(\.[a-zA-Z]{1,6})+)',
	//							"<a href=\"mailto:\\1\">\\1</a>",
	//							$text);
	
		$text = preg_replace("/([\s])(http[s]?):\/\/(.*)(\s|\$|<br\s\/\>)(.*)/U", 
		                     "\\1<a href=\"\\2://\\3\">\\3</a>\\4\\5", $text);
		
		$text = preg_replace('/([_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.
							'\@'.'[_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.'(\.[a-zA-Z]{1,6})+)/i',
							"<a href=\"mailto:\\1\">\\1</a>",
							$text);
		return $text;
	}

	private static function myCodes($text, $html = false) {
		global $_base_path;
		global $HTTP_USER_AGENT;
	
		if (substr($HTTP_USER_AGENT,0,11) == 'Mozilla/4.7') {
			$text = str_replace('[quote]','</p><p class="block">',$text);
			$text = str_replace('[/quote]','</p><p>',$text);
	
			$text = str_replace('[reply]','</p><p class="block">',$text);
			$text = str_replace('[/reply]','</p><p>',$text);
		} else {
			$text = str_replace('[quote]','<blockquote>',$text);
			$text = str_replace('[/quote]','</blockquote><p>',$text);
	
			$text = str_replace('[reply]','</p><blockquote class="block"><p>',$text);
			$text = str_replace('[/reply]','</p></blockquote><p>',$text);
		}
	
		$text = str_replace('[b]','<strong>',$text);
		$text = str_replace('[/b]','</strong>',$text);
	
		$text = str_replace('[i]','<em>',$text);
		$text = str_replace('[/i]','</em>',$text);
	
		$text = str_replace('[u]','<u>',$text);
		$text = str_replace('[/u]','</u>',$text);
	
		$text = str_replace('[center]','<center>',$text);
		$text = str_replace('[/center]','</center><p>',$text);
	
		/* colours */
		$text = str_replace('[blue]','<span style="color: blue;">',$text);
		$text = str_replace('[/blue]','</span>',$text);
	
		$text = str_replace('[orange]','<span style="color: orange;">',$text);
		$text = str_replace('[/orange]','</span>',$text);
	
		$text = str_replace('[red]','<span style="color: red;">',$text);
		$text = str_replace('[/red]','</span>',$text);
	
		$text = str_replace('[purple]','<span style="color: purple;">',$text);
		$text = str_replace('[/purple]','</span>',$text);
	
		$text = str_replace('[green]','<span style="color: green;">',$text);
		$text = str_replace('[/green]','</span>',$text);
	
		$text = str_replace('[gray]','<span style="color: gray;">',$text);
		$text = str_replace('[/gray]','</span>',$text);
	
		$text = str_replace('[op]','<span class="bigspacer"></span> <a href="',$text);
		$text = str_replace('[/op]','">'._AT('view_entire_post').'</a>',$text);
	
		$text = str_replace('[head1]','<h2>',$text);
		$text = str_replace('[/head1]','</h2>',$text);
	
		$text = str_replace('[head2]','<h3>',$text);
		$text = str_replace('[/head2]','</h3>',$text);
	
		$text = str_replace('[cid]',$_base_path.'content.php?_cid='.$_SESSION['s_cid'],$text);
	
		global $sequence_links, $_course_id, $_content_id;
		if ($_course_id > 0 && !isset($sequence_links) && $_content_id > 0) {
			global $contentManager;
			$sequence_links = $contentManager->generateSequenceCrumbs($_content_id);
		}
		if (isset($sequence_links['previous']) && $sequence_links['previous']['url']) {
			$text = str_replace('[pid]', $sequence_links['previous']['url'], $text);
		}
		if (isset($sequence_links['next']) && $sequence_links['next']['url']) {
			$text = str_replace('[nid]', $sequence_links['next']['url'], $text);
		}
		if (isset($sequence_links['resume']) && $sequence_links['resume']['url']) {
			$text = str_replace('[nid]', $sequence_links['resume']['url'], $text);
		}
		if (isset($sequence_links['first']) && $sequence_links['first']['url']) {
			$text = str_replace('[fid]', $sequence_links['first']['url'], $text);
		}
	
		/* contributed by Thomas M. Duffey <tduffey at homeboyz.com> */
	    $html = !$html ? 0 : 1;
	    
		// little hack added by greg to add syntax highlighting without using <?php \?\>
		
		$text = str_replace("[code]","[code]<?php",$text);
		$text = str_replace("[/code]","?>[/code]",$text);
	
		$text = preg_replace("/\[code\]\s*(.*)\s*\[\\/code\]/Usei", "highlight_code(fix_quotes('\\1'), $html)", $text);
		// now remove the <?php added above and leave the syntax colour behind.
		$text = str_replace("&lt;?php", "", $text);
		$text = str_replace("?&gt;", "", $text);
	
		return $text;
	}

	private static function imageReplace($text) {
		/* image urls do not require http:// */
		
	//	$text = eregi_replace("\[image(\|)?([[:alnum:][:space:]]*)\]" .
	//						 "[:space:]*" .
	//						 "([[:alnum:]#?/&=:\"'_.-]+)" .
	//						 "[:space:]*" .
	//						 "((\[/image\])|(.*\[/image\]))",
	//				  "<img src=\"\\3\" alt=\"\\2\" />",
	//				  $text);
		 
		$text = preg_replace("/\[image(\|)?([a-zA-Z0-9\s]*)\]".
		                     "[\s]*".
		                     "([a-zA-Z0-9\#\?\/\&\=\:\\\"\'\_\.\-]+)[\s]*".
		                     "((\[\/image\])|(.*\[\/image\]))/i",
					  "<img src=\"\\3\" alt=\"\\2\" />",
					  $text);
					  
		return $text;
	}
	
	private static function formatFinalOutput($text, $nl2br = true) {
		global $_base_path;
	
		$text = str_replace('CONTENT_DIR/', '', $text);
		if ($nl2br) {
			return nl2br(ContentUtility::imageReplace(ContentUtility::makeClickable(ContentUtility::myCodes(' '.$text, false))));
		}
	
		return ContentUtility::imageReplace(ContentUtility::makeClickable(ContentUtility::myCodes(' '.$text, true)));
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
	
		if ($html) {
			$x = ContentUtility::formatFinalOutput($input, false);
			return $x;
		}
	
		$output = ContentUtility::formatFinalOutput($input);
	
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
	 * This function returns an array of content tools' shortcuts
	 * @access: public
	 * @param: $content_row: an array of the current content information
	 * @return: an array of all the tool short cuts that apply to the current content or content folder
	 */
	public static function getToolShortcuts($content_row)
	{
		global $_current_user, $_base_href, $contentManager, $_course_id;
		
		if (((!$content_row['content_parent_id'] && ($_SESSION['packaging'] == 'top'))
		      || ($_SESSION['packaging'] == 'all'))
			  || (isset($_current_user) && $_current_user->isAuthor($_course_id))) {
		
			$tool_shortcuts[] = array(
				  'title' => _AT('export_content'), 
				  'url' => $_base_href . 'home/imscc/ims_export.php?_cid='.$content_row['content_id'],
				  'icon' => $_base_href . 'images/download.png');
		}
		
		if (isset($_current_user) && $_current_user->isAuthor($_course_id)) {
			if ($content_row['content_type'] == CONTENT_TYPE_CONTENT) {
				$tool_shortcuts[] = array(
					  'title' => _AT('edit_this_page'),   
					   'url' => $_base_href . 'home/editor/edit_content.php?_cid='.$content_row['content_id'],
					  'icon' => $_base_href . 'images/medit.gif');
			}
		
			if ($contentManager->_menu_info[$content_row['content_id']]['content_parent_id']) {
				$tool_shortcuts[] = array(
				  'title' => _AT('add_sibling_folder'), 
				  'url' => $_base_href .
					'home/editor/edit_content_folder.php?pid='.$contentManager->_menu_info[$content_row['content_id']]['content_parent_id'].SEP.'_course_id='.$_course_id,
				   'icon' => $_base_href . 'images/add_sibling_folder.gif');
			}

			if ($content_row['content_type'] == CONTENT_TYPE_FOLDER) {
				$tool_shortcuts[] = array(
				  'title' => _AT('add_sub_folder'), 
				  'url' => $_base_href .
					'home/editor/edit_content_folder.php?_course_id='.$_course_id.SEP.'pid='.$content_row['content_id'],
				   'icon' => $_base_href . 'images/add_sub_folder.gif');
			}
			
			if ($contentManager->_menu_info[$content_row['content_id']]['content_parent_id']) {
				$tool_shortcuts[] = array(
				  'title' => _AT('add_sibling_page'), 
				  'url' => $_base_href .
					'home/editor/edit_content.php?pid='.$contentManager->_menu_info[$content_row['content_id']]['content_parent_id'].SEP.'_course_id='.$_course_id,
				  'icon' => $_base_href . 'images/add_sibling_page.gif');
			}
			
			if ($content_row['content_type'] == CONTENT_TYPE_CONTENT) {
				$tool_shortcuts[] = array(
				  'title' => _AT('delete_this_page'), 	
				  'url' => $_base_href . 'home/editor/delete_content.php?_cid='.$content_row['content_id'],
				  'icon' => $_base_href . 'images/page_delete.gif');
			}
			else if ($content_row['content_type'] == CONTENT_TYPE_FOLDER) {
				$tool_shortcuts[] = array(
				  'title' => _AT('add_sub_page'), 	
				  'url' => $_base_href . 'home/editor/edit_content.php?_course_id='.$_course_id.SEP.'pid='.$content_row['content_id'],
				  'icon' => $_base_href . 'images/add_sub_page.gif');
				
				$tool_shortcuts[] = array(
				  'title' => _AT('delete_this_folder'), 	
				  'url' => $_base_href . 'home/editor/delete_content.php?_cid='.$content_row['content_id'],
				  'icon' => $_base_href . 'images/page_delete.gif');
			}
		}
		return $tool_shortcuts;

//	if (isset($_current_user) && $_current_user->isAuthor($_course_id)) {
//		$shortcuts[] = array('title' => _AT('add_sub_folder'),   'url' => $_base_href . 'home/editor/edit_content_folder.php?_course_id='.$_course_id.'pid='.$cid);
//		
////		$shortcuts[] = array('title' => _AT('add_top_page'),     'url' => $_base_href . 'home/editor/edit_content.php?_course_id='.$_course_id, 'icon' => $_base_href . 'images/page_add.gif');
//		if ($contentManager->_menu_info[$cid]['content_parent_id']) {
//			$shortcuts[] = array('title' => _AT('add_sibling_page'), 'url' => $_base_href .
//				'home/editor/edit_content.php?_course_id='.$_course_id.SEP.'pid='.$contentManager->_menu_info[$cid]['content_parent_id'], 'icon' => $_base_href . 'images/page_add_sibling.gif');
//		}
//	
//		$shortcuts[] = array('title' => _AT('add_sub_page'),     'url' => $_base_href . 'home/editor/edit_content.php?_course_id='.$_course_id.SEP.'pid='.$cid);
//		$shortcuts[] = array('title' => _AT('delete_this_folder'), 'url' => $_base_href . 'home/editor/delete_content.php?_cid='.$cid, 'icon' => $_base_href . 'images/page_delete.gif');
//	}
	}
	
	/**
	* replace source object with alternatives according to user's preferences
	* @access	public
	* @param	$cid: 				content id.
	* @param	$content:	 		the original content page ($content_row['text'], from content.php).
	* @param    $info_only:         when "true", return the array of info (has_text_alternative, has_audio_alternative, has_visual_alternative, has_sign_lang_alternative)
	* @param    $only_on_secondary_type: 
	* @return	string				$content: the content page with the appropriated resources.
	* @see		$db			        from include/vitals.inc.php
	* @author	Cindy Qi Li
	*/
	public static function applyAlternatives($cid, $content, $info_only = false, $only_on_secondary_type = 0){
		global $db, $_course_id;
		
		include_once(TR_INCLUDE_PATH.'classes/DAO/DAO.class.php');
		$dao = new DAO();
		
		$video_exts = array("mpg", "avi", "wmv", "mov", "swf", "mp3", "wav", "ogg", "mid", "mp4", "flv");
		$txt_exts = array("txt", "html", "htm");
		$image_exts = array("gif", "bmp", "png", "jpg", "jpeg", "png", "tif");
		$only_on_secondary_type = intval($only_on_secondary_type);
				
		// intialize the 4 returned values when $info_only is on
		if ($info_only)
		{
			$has_text_alternative = false;
			$has_audio_alternative = false;
			$has_visual_alternative = false;
			$has_sign_lang_alternative = false;
		}

		if (!$info_only && !$only_on_secondary_type && 
		    ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==0) && 
		    ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO']==0) && 
		    ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL']==0)) 
		{
			//No user's preferences related to content format are declared
			if (!$info_only) {
				return $content;
			} else {
				return array($has_text_alternative, $has_audio_alternative, $has_visual_alternative, $has_sign_lang_alternative);
			}
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
			       AND sr.language_code='".$_SESSION['lang']."'
			       AND sr.secondary_resource_id = srt.secondary_resource_id
		           AND pr.content_id = c.content_id";
		if ($only_on_secondary_type > 0) {
			$sql .= " AND srt.type_id=".$only_on_secondary_type;
		}
		$sql .= " ORDER BY pr.primary_resource_id, prt.type_id";
		
		$rows = $dao->execute($sql);
	
		if (!is_array($rows)) return $content;
		
		foreach ($rows as $row) 
		{
			if ($info_only || $only_on_secondary_type ||
			    ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==1 && $row['primary_type']==3 &&
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
				if (in_array($ext, $video_exts))
					$target = '[media]'.$row['secondary_resource'].'[/media]';
				// a text primary to be replaced by a visual alternative 
				else if (in_array($ext, $txt_exts))
				{
					if ($row['content_path'] <> '') 
						$file_location = $row['content_path'].'/'.$row['secondary_resource'];
					else 
						$file_location = $row['secondary_resource'];
					
					$file = TR_CONTENT_DIR.$_SESSION['course_id'] . '/'.$file_location;
					$target = '<br />'.file_get_contents($file);
					
					// check whether html file
					if (preg_match('/.*\<html.*\<\/html\>.*/s', $target))
					{ // is a html file, use iframe to display
						// get real path to the text file
						if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
							$course_base_href = 'get.php/';
						} else {
							$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
						}
		
						$file = TR_BASE_HREF . $course_base_href.$file_location;
							
						$target = '<iframe width="100%" frameborder="0" class="autoHeight" scrolling="auto" src="'.$file.'"></iframe>';
					}
					else
					{ // is a text file, insert/replace into content
						$target = nl2br($target);
					}
				} 
				else if (in_array($ext, $image_exts))
					$target = '<img border="0" alt="'._AT('alternate_text').'" src="'.$row['secondary_resource'].'"/>';
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
	
				// *** Alternative replace/append starts from here ***
				$img_processed = false;    // The indicator to tell the source image is found (or not) 
				                           // and processed (or not) in an <img> tag. If found and processed, 
				                           // SKIP the found/process for <a> tag because the source is a image
				                           // and <a> is very likely the tag wrapping around <img>

				// append/replace target alternative to [media]source[/media]
				if (preg_match("/".preg_quote("[media").".*".preg_quote("]".$row['resource']."[/media]", "/")."/sU", $content))
				{
					if (!$info_only) {
						$content = preg_replace("/(.*)(".preg_quote("[media").".*".preg_quote("]".$row['resource']."[/media]", "/").")(.*)/sU", 
				             $pattern_replace_to, $content);
					} else {
						if ($row['secondary_type'] == 1) $has_audio_alternative = true;
						if ($row['secondary_type'] == 2) $has_sign_lang_alternative = true;
						if ($row['secondary_type'] == 3) $has_text_alternative = true;
						if ($row['secondary_type'] == 4) $has_visual_alternative = true;
					}
				}
				
				// append/replace target alternative to <img ... src="source" ...></a>
				if (preg_match("/\<img.*src=\"".preg_quote($row['resource'], "/")."\".*\/\>/sU", $content))
				{
					$img_processed = true;
					if (!$info_only) {
						$content = preg_replace("/(.*)(\<img.*src=\"".preg_quote($row['resource'], "/")."\".*\/\>)(.*)/sU", 
			                                $pattern_replace_to, $content);
					} else {
						if ($row['secondary_type'] == 1) $has_audio_alternative = true;
						if ($row['secondary_type'] == 2) $has_sign_lang_alternative = true;
						if ($row['secondary_type'] == 3) $has_text_alternative = true;
						if ($row['secondary_type'] == 4) $has_visual_alternative = true;
					}
				}
				
				// append/replace target alternative to <a>...source...</a> or <a ...source...>...</a>
				// skip this "if" when the source object has been processed in aboved <img> tag
				if (!$img_processed && preg_match("/\<a.*".preg_quote($row['resource'], "/").".*\<\/a\>/sU", $content))
				{
					if (!$info_only) {
						$content = preg_replace("/(.*)(\<a.*".preg_quote($row['resource'], "/").".*\<\/a\>)(.*)/sU", 
			                                $pattern_replace_to, $content);
					} else {
						if ($row['secondary_type'] == 1) $has_audio_alternative = true;
						if ($row['secondary_type'] == 2) $has_sign_lang_alternative = true;
						if ($row['secondary_type'] == 3) $has_text_alternative = true;
						if ($row['secondary_type'] == 4) $has_visual_alternative = true;
					}
				}
	
				// append/replace target alternative to <object ... source ...></object>
				if (preg_match("/\<object.*".preg_quote($row['resource'], "/").".*\<\/object\>/sU", $content))
				{
					if (!$info_only) {
						$content = preg_replace("/(.*)(\<object.*".preg_quote($row['resource'], "/").".*\<\/object\>)(.*)/sU", 
			                                $pattern_replace_to, $content);
					} else {
						if ($row['secondary_type'] == 1) $has_audio_alternative = true;
						if ($row['secondary_type'] == 2) $has_sign_lang_alternative = true;
						if ($row['secondary_type'] == 3) $has_text_alternative = true;
						if ($row['secondary_type'] == 4) $has_visual_alternative = true;
					}
				}
	
				// append/replace target alternative to <embed ... source ...>
				if (preg_match("/\<embed.*".preg_quote($row['resource'], "/").".*\>/sU", $content))
				{
					if (!$info_only) {
						$content = preg_replace("/(.*)(\<embed.*".preg_quote($row['resource'], "/").".*\>)(.*)/sU", 
			                                $pattern_replace_to, $content);
					} else {
						if ($row['secondary_type'] == 1) $has_audio_alternative = true;
						if ($row['secondary_type'] == 2) $has_sign_lang_alternative = true;
						if ($row['secondary_type'] == 3) $has_text_alternative = true;
						if ($row['secondary_type'] == 4) $has_visual_alternative = true;
					}
				}
			}
		}
		
		if (!$info_only) {
			return $content;
		} else {
			return array($has_text_alternative, $has_audio_alternative, $has_visual_alternative, $has_sign_lang_alternative);
		}
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
		
		if (!$content_id || !isset($_SESSION['user_id'])) return;
		
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