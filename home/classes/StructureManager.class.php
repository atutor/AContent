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

class StructureManager
{
	
	//array
	var $page_temp;
	
	var $tests;
	
	var $forums;
	
	var $name;
	
	var $path;
	
	var $info;

	//constant
	var $pageTemplName = 'pageTemplName';
	
	var $testName = 'testName';
	
	var $forumName = 'forumName';
	
	

	
	/* constructor	*/
	function StructureManager($name) {
	
		$this->path = realpath(TR_INCLUDE_PATH		. '../dnd_themod').'/structures/' . $name;
		if(!is_dir($this->path)) 
			throw new Exception("Error: the name of the struct doesn't corrispond to a dir");
		
		$this->name = $name;
		
		$this->setInfo();
	
	}
	
	function dislayTest($page, $i) {
		echo '<div id="folder_'.$page.$i.'" style="margin-left: 15px;">';
		echo '<img class="img-size-tree" width="16" height="16" border="0" src="'.TR_BASE_HREF.'/images/clr.gif" alt="">';
		echo '<img class="img-size-tree" border="0" alt="" src="'.TR_BASE_HREF.'/images/tree/tree_end.gif">';
		echo '<img class="img-size-tree" width="16" height="16" border="0" alt="" src="'.TR_BASE_HREF.'/images/tree/tree_horizontal.gif">';
		echo '<img alt="test" title="test" src="'.TR_BASE_HREF.'/images/check.gif">';
		echo '<img class="img-size-tree" width="16" height="16" border="0" src="'.TR_BASE_HREF.'/images/clr.gif" alt="">';
		echo '<span>'.$page.' test</span>';
		echo '</div>';
	}
	
	
	function getBody($page) {
		
		$contents = $this->getContent($page);
		if(count($contents)==1) {
			$path_page = realpath(TR_INCLUDE_PATH		. '../dnd_themod').'/models/';;
		
			$file = $path_page . $contents[0] .'/'.$contents[0].'.html';
			if(is_file($file)) {
				$text = file_get_contents($file);
				$find =  strpos($text, 'src="dnd_image"');
				
				if($find) 
						 //$resp[] = str_replace('src="dnd_image', 'src="/dnd_themod/system/model_image.png"');
						return str_replace('src="dnd_image', 'src="'.TR_BASE_HREF.'dnd_themod/system/model_image.png"', $text);
					 else 	
						return $text;
				
			}
		} else if($this->isForum($page)) 
			return 'At this content is associated a forum';
		
		else
			return 'Choose the page template';
		
	}
	
	
	function getTitle($page) {
		if($this->isForum($page) || $this->isTest($page))
			return 'content with '.$page;
		else 
			return $page;
	} 
	
	function isForum($page) {
		
		foreach ($this->forums as $forum) {
			if($page == $forum) {
				return true;
				
			} 
		}
		
		return false;
	}

	function isTest($page) {
		
		
		foreach ($this->tests as $test) {
			
			if($page == $test) 
				return true;
				
		}
		
		return false;
	}
	
	function isFolder($page) {
		
		if($this->info[$page] != null)
			return true;
		else 
			return false;
	}
	
	function getMin($page) {
		return $this->info['min_'.$page];
	}
	
	function getMax($page) {
		$max = $this->info['max_'.$page];
		if($max == null || $max == 'x' || $max == '')
			$max = $this->info['min_'.$page]+ 1;
			
		return $max;
		
		
	}
	
	function getName() {
		return $this->name;
	}
	
	
	/* Access: PRIVATE */
	function setInfo() {
		
	
		if(is_dir($this->path)) {
			
			$file	= $this->path.'/structure.info';
			if(is_file($file)){
				$this->info	= parse_ini_file($file);
				
				$this->page_temp = $this->info[$this->pageTemplName];
				$this->tests = $this->info[$this->testName];
				$this->forums = $this->info[$this->forumName];
			 	
			}
			
		}
		
	}
	
	
	function printPreview() {
		
		
		
		echo '<div>'."\n";
		//echo '<script type="text/javascript" src="../dnd_themod/system/Struct.js"></script>';
		
		echo '<strong>';
		
		$this->printStruct(null, -1);
		
		
		
		
		echo '<form action="home/course/course_property.php" method="get">';
		echo '<input type="hidden" name="_struct_name" value="'.$this->name.'" />';
		echo '<input type="submit" value="Create course with this structure" style="margin: 50px; margin-right: 100px; float: right;"></input> ';
		//Create lesson with this structure
		echo '</input>';
		echo '</strong>';
		
		echo '</div>';
		
		
	}
	
	
	
	function printStruct($array, $folder) {
				
		global $_base_path;
		
		
			
		
		
		if($array == null) 
			$array = $this->page_temp;
	
		
		foreach ($array as $page) {
			
			$max = $this->info['max_'.$page];
			$min = $this->info['min_'.$page];
			
			if($max == 'x')
				$max = $min + 1;
			
			for($i=0; $i<$max; $i++) {
			
				echo '<div>';
				echo '<img class="img-size-tree" height="16" width="16" border="0" alt="" src="'.TR_BASE_HREF.'images/clr.gif"/>';
				echo '<img class="img-size-tree" height="16" width="16" border="0" alt="" src="'.TR_BASE_HREF.'images/tree/tree_end.gif"/>';
				
				if($this->isFolder($page)) 
					$this->insertToogle($page, $i, 'expand');
				else if($this->isTest($page))
					$this->insertToogle($page, $i, 'collapse');
				else 
					echo '<img class="img-size-tree" style="margin-left: 1px;" width="16" height="16" border="0" alt="" src="'.TR_BASE_HREF.'images/tree/tree_horizontal.gif">';
				
				
				//echo '<img class="img-size-tree" height="16" width="16" border="0" alt="" src="/AContent_BEAT/images/clr.gif">';
				
				
				echo '<span style="margin-left:0.3cm; margin-right:0.2cm">';
				
				if($this->isTest($page) || $this->isForum($page))
					echo 'content with ';
				
				echo $page.' ';
			
				
				if($folder != -1 && !$this->isTest($page) && !$this->isForum($page))
					echo ($folder+1).'.'. ($i+1);
				else if($this->isFolder($page))
					echo ($i+1);
				
					
				echo '</span>';
				
				$this->insertIcons($page, $min, $i);
				
				if($this->isFolder($page)) {
					$child = $this->getChild($page);
					//$child = $this->info[$page];
								
							echo '<div style="margin-left: 15px; display: none;" id="folder_'.$page.$i.'" >';
							$this->printStruct($child, $i);
									
							echo '</div>';
				} else if($this->isTest($page)) {
					$this->dislayTest($page, $i);
				} else {
					$this->createPreview($page, $i); 
				}
				
					
				
					
					
					
				echo '</div>';
				
				
			}
				
		}
		
		
	}
	
	function insertToogle($page,$i,$value ) {
		
		$tree_expand_icon = $_base_path.'images/tree/tree_expand.gif';
		$tree_collapse_icon = $_base_path.'images/tree/tree_collapse.gif';
		
		
		echo '
		<script>
		function initContentMenu() {
  			var tree_collapse_icon = "'.$_base_path.'images/tree/tree_collapse.gif";
  			var tree_expand_icon = "'.$_base_path.'images/tree/tree_expand.gif";
			
		};
		</script>
		';
		
		echo '<a href="javascript:void(0)" onclick="javascript: trans.utility.toggleFolderStruct(\''.$i.'\', \''.$page.'\', \''._AT('expand').'\', \''._AT('collapse').'\', \''.$tree_expand_icon.'\', \''.$tree_collapse_icon.'\' ); ">';
		echo '<img id="tree_icon_'.$page.$i.'" style="margin-left: 1px;" class="img-size-tree" width="16" height="16" border="0" title="'.$value.'" alt="'.$value.'" src="'.$_base_path.'images/tree/tree_'.$value.'.gif">';
		echo '</a>';
	}
	
	
	function createPreview($page, $i) {
		echo '<input id="prev-inp-'.$page.$i.'" type="image" onclick="openPrev(\''.$page.$i.'\');" title="preview of page template" style="margin-left:0.2cm;" height="16" width="16" border="0" alt="preview of page template" src="'.TR_BASE_HREF.'images/preview.png"/>';
		echo '<input id="hide-prev-inp-'.$page.$i.'" type="image" onclick="closePrev(\''.$page.$i.'\');" title="hide preview of page template" style="margin-left:0.2cm; display: none;" height="16" width="16" border="0" alt="hide preview of page template" src="'.TR_BASE_HREF.'images/hidePreview.png"/>';
		echo '<div id="prev-'.$page.$i.'" style="margin: 10px; margin-left: 30px;display: none ;border-style: solid; border-width:1px; border-color: grey;">'; //display: none

		$res = $this->getSrcImgPage($page);
			
		if(count($res) == 0) {
			echo '<p style="color: red;">Preview not found</p>';
		}

			
		foreach ($res as $prewiew) {

			echo '<img style="margin: 10px;" width="70" height="70" src="'.$prewiew.'" title="preview of the page template: '.$page.'" >';
			echo '</img>';

		}
		echo '</div>';
	}
	
	function get_page_temp() {
		
		return $this->page_temp;
	}
	
	
	
	/* Access: PRIVATE */
	function getContent($page) {
		$filename = realpath(TR_INCLUDE_PATH. '../dnd_themod').'/system/struct_page_ass.info';
		if(is_file($filename)) {
			$struct_page_ass = parse_ini_file($filename);
			return $struct_page_ass[$page];
		} else	
			return null;
	
	}
	
	
	function getChild($folder) {
		 return $this->info[$folder];
	}
	
	
	
	/* Access: PRIVATE */
	function getSrcImgPage($page) {
		
		$pages_template = $this->getContent($page);
		
		$previews = array();
		
		foreach ($pages_template as $page_template) {
			$img = TR_BASE_HREF . 'dnd_themod/models/' . $page_template . '/screenshot.png';
			$previews[] = $img;
		}
		
		
		/*$file_headers = @get_headers($file);
		if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		    return "";
		}
		else {
   			 return $file;
		}*/
		
		return $previews;
		
		
	}
	
	/* Access: PRIVATE */
	function insertIcons($page, $min, $i){

		
		//$min = $this->info['min_'.$page];
		//$max = $this->info['max_'.$page];
		
		//if($min == 1 && $max == 1)
		if($i < $min)
			echo '<img title="the page is mandatory" border="0" alt="" src="'.TR_BASE_HREF.'images/must.jpeg"/>';
		else 
			echo '<img title="the page can be deleted" height="14" width="14" border="0" alt="" src="'.TR_BASE_HREF.'images/bad.gif"/>';
		//else {
			//if($max == null)
				//$max = 'x';
			//echo '<span style="margin-top: 4px; margin-bottom: 4px; padding-left: 2px; padding-right: 2px;  border-style:dotted; border-width:1px" title="the page must be insert at least '.$min.' times and at most '. $max.' times">';
			//echo '<span style="margin-right:0.1cm; font-family:Courier New; font-size: 85%;">min='. $min .'</span>';
			//if($max != 'x' )
				//echo '<span style="font-family:Courier New; font-size: 85%;">max='. $max .'</span>';
			//echo '</span>';
			//echo '(min='.$min.', max='.$max.')';
		//}
		
	}

	
	
	
	
	
	




}

?>