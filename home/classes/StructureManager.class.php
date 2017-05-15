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



if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ForumsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ForumsCoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentForumsAssocDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/TestsDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');


class StructureManager
{
	
	//array
	var $page_temp;
	
	var $tests;
	
	var $forums;
	
	var $name;
	
	var $path;
	
	var $info;
	

	
	/* constructor	*/
	function StructureManager($name) {
	
		$this->path = realpath(TR_INCLUDE_PATH		. '../templates').'/structures/' . $name;
		if(!is_dir($this->path)) 
			//throw new Exception(_"Error: the name of the struct doesn't corrispond to a dir");
			throw new Exception(_AT('error_no_structure'));
		
		$this->name = $name;
		
		$this->setInfo();
	
	}
	
	function dislayTest($name, $i) {
		echo '<div id="folder_'.$name.$i.'" style="margin-left: 15px;">';
		echo '<img class="img-size-tree" width="16" height="16" border="0" src="'.TR_BASE_HREF.'/images/tree/tree_space.gif" alt="">';
		echo '<img class="img-size-tree" width="16" height="16" border="0" src="'.TR_BASE_HREF.'/images/tree/tree_space.gif" alt="">';
		echo '<img class="img-size-tree" border="0" alt="" src="'.TR_BASE_HREF.'/images/tree/tree_end.gif">';
		echo '<img class="img-size-tree" width="16" height="16" border="0" alt="" src="'.TR_BASE_HREF.'/images/tree/tree_horizontal.gif">';
		echo '<img alt="test" title="test" src="'.TR_BASE_HREF.'/images/check.gif">';
		echo '<img class="img-size-tree" width="16" height="16" border="0" src="'.TR_BASE_HREF.'/images/clr.gif" alt="">';
		echo '<span>'.$name.'</span>';
		echo '</div>';
	}
	
	
	function getBody($page) {
		
		$content = $this->getContent($page);
		$path_page = realpath(TR_INCLUDE_PATH		. '../templates').'/page_templates/';
		if(count($content)==1) {	
 			$file = $path_page . $content[0] .'/'.$content[0].'.html';
			if(is_file($file)) {
				
				$text = file_get_contents($file);
				$find =  strpos($text, 'src="dnd_image"');
				
				if($find) 
						 //$resp[] = str_replace('src="dnd_image', 'src="/templates/system/page_template_image.png"');
						return str_replace('src="dnd_image', 'src="'.TR_BASE_HREF.'templates/system/page_template_image.png"', $text);
					 else 	
						return $text;
			}
			
		} else if(count($content)>1){
		    foreach($content as $template){
		        $file = $path_page . $template .'/'.$template.'.html';
			    if(is_file($file)) {
				    $text .= file_get_contents($file);
				    $find =  strpos($text, 'src="dnd_image"');
				
				    if($find) 
						return str_replace('src="dnd_image', 'src="'.TR_BASE_HREF.'templates/system/page_template_image.png"', $text);
			    }
		    }
		    return $text;
		}else if($this->hasForum($page)) {
			//return 'At this content is associated a forum';
			return _AT('forum_associated');
		} else if($this->hasTest($page)){
			//return 'At this content is associated a test';
			return _AT('test_associated');
		}
		//else {
			//return 'null';
		//}
	}
	
	
	/*function getTitle($page) {
		if($this->hasForum($page) || $this->hasTest($page))
			return $page;
			//return 'content with '.$page;
		else 
			return $page;
	} */
	
	function hasForum($page) {
		
		if($page->forum)
			return true;
		
		return false;
	}

	function hasTest($page) {
		if (!isset($page) || !isset($page->tests)) return false;
		
		if(count($page->tests->children()) > 0) 
			return true;
		
		return false;
	}
	
	function isFolder($page) {
		
		if($page->page || $page->folder)
			return true;
		else 
			return false;
	}
	
	
	function getName() {
		return $this->name;
	}
	
	
	/* Access: PRIVATE */
	function setInfo() {
		
	
		if(is_dir($this->path)) {
			
			//$file	= $this->path.'/structure.info';
			$file = $this->path.'/content.xml';
			//$xml = simplexml_load_file($file);
			if(is_file($file)){
				//$this->info	= parse_ini_file($file);
				$xml = simplexml_load_file($file);
				
				
				foreach($xml->children() as $child) {
						#$attrs = $child->attributes();
						$this->page_temp[] =  $child;//$attrs[2];
				}
			
			 	
			}
			
		}
		
	}
	
	/*function isPageTemp($page) {
		foreach ($this->page_temp as $p)
			if($page == $p)
				return true;
		
		return false;	
		
	}*/
	
	function printPreview($flag_button, $structs) {
		
		echo '<div>'."\n";
		//echo '<img class="img-size-tree" width="16" height="16" border="0" src="http://localhost/AContentEdu/images/tree/tree_space.gif" alt="">
		echo '<img class="img-size-tree" height="16" width="16" border="0" alt="" src="'.TR_BASE_HREF.'images/tree/tree_space.gif"/>';
		
		echo '<p style="display:inline;">'.$this->name.'</p>';
		//echo '<script type="text/javascript" src="../templates/system/Struct.js"></script>';
		
		echo '<strong>';
		
		$this->printStruct(null, -1, $this->name);
		
		
		
		if($flag_button) {
			echo '<form action="home/course/course_property.php" method="get">';
			echo '<input type="hidden" name="_struct_name" value="'.$structs.'" />';
		
			echo '<input type="submit" value="'._AT('create_this_structure').'" style="margin: 50px; margin-right: 100px; float: right;" />';
		//Create lesson with this structure
		}
		
		echo '</strong>';
		
		echo '</div>';
		
	}
	
	
	
	function printStruct($array, $folder, $tname) {
				
		global $_base_path;
		
		if($array == null) 
			$array = $this->page_temp;
			
		
		foreach ($array as $page) {
			
			$name = $page['name'];
			$min = $page['min'];
			$max = $page['max'];
			if($max == 'x' || $max == 'n')
				$max = $min + 1;
			
			for($i=0; $i<$max; $i++) {
			
				echo '<div>';
				echo '<img class="img-size-tree" height="16" width="16" border="0" alt="" src="'.TR_BASE_HREF.'images/tree/tree_space.gif"/>';
				echo '<img class="img-size-tree" height="16" width="16" border="0" alt="" src="'.TR_BASE_HREF.'images/tree/tree_space.gif"/>';
				if($i == ($max-1))
					echo '<img class="img-size-tree" height="16" width="16" border="0" alt="" src="'.TR_BASE_HREF.'images/tree/tree_end.gif"/>';
				else 
					echo '<img class="img-size-tree" width="16" height="16" border="0" alt="" src="'.TR_BASE_HREF.'images/tree/tree_split.gif">';
					
					
				if($this->isFolder($page)) 
					$this->insertToogle($name, $i, 'expand', $tname);
				else if($this->hasTest($page))
					$this->insertToogle($name, $i, 'collapse', $tname);
				else 
					echo '<img class="img-size-tree" style="margin-left: 1px;" width="16" height="16" border="0" alt="" src="'.TR_BASE_HREF.'images/tree/tree_horizontal.gif">';
				
				echo '<span style="margin-left:0.3cm; margin-right:0.2cm">';
				echo $name.' ';
			
				
				if($folder != -1 && !$this->hasTest($page) && !$this->hasForum($page))
					echo ($folder+1).'.'. ($i+1);
				else if($this->isFolder($page) && $max != 1)
					echo ($i+1);
				
					
				echo '</span>';
				
				$this->insertIcons($min, $i);
				
				if($this->isFolder($page)) {
					$name = preg_replace('/\s/', '_', $name);  // To accommodate folder names with spaces
					echo '<div style="margin-left: 15px; display: none;" id="folder_'.$name.$i.$tname.'" >';
					$this->printStruct($page->children(), $i);
									
					echo '</div>';
				} else if($this->hasTest($page)) {
					$this->dislayTest($name, $i);
				} 
		
				echo '</div>';
				
				
			}
				
		}
		
		
	}  
        function insertToogle($page,$i,$value,$tname ) {
		
		$tree_expand_icon = TR_BASE_HREF.'images/tree/tree_expand.gif';
		$tree_collapse_icon = TR_BASE_HREF.'images/tree/tree_collapse.gif';
		
		
		echo '
		<script>
		function initContentMenu() {
  			var tree_collapse_icon = "'.TR_BASE_HREF.'images/tree/tree_collapse.gif";
  			var tree_expand_icon = "'.TR_BASE_HREF.'images/tree/tree_expand.gif";
			
		};
		</script>
		';
		$page = preg_replace('/\s/', '_', $page);  // To accommodate folder names with spaces
		echo '<a href="javascript:void(0)" onclick="javascript: trans.utility.toggleFolderStruct(\''.$i.$tname.'\', \''.$page.'\', \''._AT('expand').'\', \''._AT('collapse').'\', \''.$tree_expand_icon.'\', \''.$tree_collapse_icon.'\' ); ">';
		echo '<img id="tree_icon_'.$page.$i.$tname.'" style="margin-left: 1px;" class="img-size-tree" width="16" height="16" border="0" title="'.$value.'" alt="'.$value.'" src="'.TR_BASE_HREF.'images/tree/tree_'.$value.'.gif">';
		echo '</a>';
	}
	
	function get_page_temp() {
		
		return $this->page_temp;
	}
	

	/** 
	 * **/
	function getPageTemplatesItem($title) {
		$file = $this->path.'/content.xml';
		if(is_file($file)){
			$xml = simplexml_load_file($file);
			$pages = $xml->xpath('//page');
			while(list( , $node) = each($pages)) {
   				if($node['name'] == $title) {
   					return $node->children();
   				}
			}
				
		}
		return null;
	}
	

	function getContent($page) {
		//die('gC');
		if($this->isFolder($page))
			return null;
      
		$content = array();
		$children = $page->page_templates->children();
		$json = json_encode($children);
        $array = json_decode($json,TRUE);
        // this only works when pages are in folders
		if(is_array($array) && isset($array['page_template'])){
            foreach($array['page_template'] as $child => $attributes) {
                if(is_array( $attributes) && isset($attributes['@attributes'])){
                    foreach($attributes['@attributes'] as $property => $name){
                        $content[] = $name;
                    }
                }
            }
		}
		return $content;
	}
	
	
	function createStruct($page_temp, $id_folder, $course_id) {
		
		$contentDAO = new ContentDAO();
		$coursesDAO = new CoursesDAO();
		
		foreach ($page_temp as $page) {
		
			//ToDo change here
			$min = $page['min'];
			$max = $page['max'];
			if($max == 'x' || $max == 'n') $max = $min + 1;
			
			for($i=0; $i<$max; $i++) {
			
				// if $opt = '1' the page is optional
			    // else the page is mandatory
				$opt = 	($i < $min) ? 0 : 1; 
			
				$content_type = 0;
				if($this->isFolder($page))
					$content_type = 1;
				
				$body = $this->getBody($page);
				
				$title = htmlspecialchars($page['name']);
					
				if($id_folder == -1) {
					$content_id = $contentDAO->Create($course_id, 0, 1, 0, 1, null, null, $title, $body, null, 0, null, $content_type);
					
				} else {
					$content_id = $contentDAO->Create($course_id, 0, 1, 0, 1, null, null, $title, $body, null, 0, null, $content_type);
					$contentDAO->UpdateField($content_id, 'content_parent_id', $id_folder);
				}
				
				//update the field 'optional'
				$contentDAO->UpdateField($content_id, 'optional', $opt);
				//update the field 'structure'
				$contentDAO->UpdateField($content_id, 'structure', $this->getName());
			
				if($this->hasForum($page)) {
					$forums_dao = new ForumsDAO();
					$forum_course = new ForumsCoursesDAO();
					$forum_content = new ContentForumsAssocDAO();
					
					$forum_id = $forums_dao->Create($page['name'], _AT('forums_description'));
					
					$forum_content->Create($content_id, $forum_id);
					
					$forum_course->Create($forum_id, $course_id);
					
				} else if($this->hasTest($page)) {

				    $count = 0;
				    foreach($page->tests as $test){
                        //global $msg;
                        //$msg->addFeedback($count);
                        $testsDAO = new TestsDAO();
                        $test_ass_cont = new ContentTestsAssocDAO();
                        $test_id = $testsDAO->Create($course_id, $page['name'].$count, _AT('tests_description'));
                        $test_ass_cont->Create($content_id, $test_id);
                        $count++;
					}
					
				} else if($content_type == 1) {
					//the content is a folder
					$child = $this->getChild($page);
				
					
					$this->createStruct($child, $content_id, $course_id);
					
					
				} 
				
			}
		
		}
		
	}
	
	
	function getChild($folder) {
		return $folder->children();
	}
	
	
	
	/* Access: PRIVATE */
	/*function getSrcImgPage($page) {
		
		
		$pages_template = $this->getContent($page);
		$previews = array();
		
		foreach ($pages_template as $page_template) {
			$img = TR_BASE_HREF . 'templates/page_template/' . $page_template . '/screenshot.png';
			$previews[] = $img;
		}
	
		
		return $previews;
		
	}*/
	
	/* Access: PRIVATE */
	function insertIcons($min, $i){

		if($i < $min)
			echo '<img title="'._AT('page_mandatory').'" border="0" alt="" src="'.TR_BASE_HREF.'images/must.png" class="mandatory_ex" alt="'._AT('mandatory_content').'"/>';
		else 
			echo '<img title="'._AT('page_deletable').'" height="14" width="14" border="0" alt="'._AT('delete').'"  src="'.TR_BASE_HREF.'images/bad.gif" class="delete_ex"/>';
		
		
	}

	
}

?>