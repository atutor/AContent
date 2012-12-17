<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2012                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) exit;
require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class AContent_LiveContentLinkDAO {

	// DAO instance pointer
	private static $_singleton	= null;
	// dom pointer
	private $_dom				= null;

	/**
	 * Singleton Design Pattern
	 * Return the instance of the class DAO.class.php
	 * @access  private
	 * @return  DAO class instance
	 * @author  Mauro Donadio
	 */
	private static function _getInstance(){
		if (AContent_LiveContentLinkDAO::$_singleton == null){ 
			AContent_LiveContentLinkDAO::$_singleton = new DAO();
		}
	
		return AContent_LiveContentLinkDAO::$_singleton;
	}

	/**
	 * Return content information by given content id
	 * @access  public
	 * @param   content id, type of content: course = 1, lesson = 0
	 * @return  XML data about the requested content
	 * @author  Mauro Donadio
	 */
	public function getContent($v_id, $course)
	{

		$v_id = intval($v_id);
		
		// create doctype
		$this->_dom = new DOMDocument("1.0");
		//set the document encoding
		$this->_dom->encoding = 'utf-8';
		//set xml version 
		$this->_dom->xmlVersion = '1.0'; 

		// create the main ROOT element
		$root = $this->_dom->createElement("AContent_LiveContentLink");
		$this->_dom->appendChild($root);

		if($course)
			$sql = 'SELECT * FROM '.TABLE_PREFIX.'content WHERE course_id='.$v_id.' AND content_parent_id=0 ORDER BY ordering ASC';
		else
			$sql = 'SELECT * FROM '.TABLE_PREFIX.'content WHERE content_id='.$v_id;

		$DAO = self::_getInstance();

		if ($rows = $DAO->execute($sql))
		{
			for($i = 0; $i < count($rows); $i++){

				$content_id = self::_xmlAddContentID($rows[$i]['content_id'], $root);

				self::_xmlFillFields($rows[$i], $content_id);

				// check if the selected content is a folder or just a page
				// just a pge
				if($rows[$i]['content_type'] == 1){
	
					// this is a folder!
					// I have to return folder data and folder content recursively
					// define the root
					//$ret[$rows[0]['content_id']]['children']	= self::_recursiveFolderScan($rows[0]['content_id'], $content_id);
					self::_recursiveFolderScan($rows[$i]['content_id'], $content_id);
				}
			}

			//return $ret;
			// save and display tree
			return htmlentities($this->_dom->saveXML());

		}
		else
			return false;
	}

	/**
	 * Return recursively content information
	 * @access  private
	 * @param   parent content id
	 * @return  content row
	 * @author  Mauro Donadio
	 */
	private function _recursiveFolderScan($parentID, $root){
		global $addslashes;
		$parentID = intval($parentID);

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'content WHERE content_parent_id='.$parentID.' ORDER BY ordering ASC';
		$DAO = self::_getInstance();

		if ($rows = $DAO->execute($sql))
		{
			// for each child
			for($i = 0; $i < count($rows); $i++){

				$content_id = self::_xmlAddContentID($rows[$i]['content_id'], $root);

				self::_xmlFillFields($rows[$i], $content_id);

				// checks for subfolders
				if($rows[$i]['content_type'] == 1){
					self::_recursiveFolderScan($rows[$i]['content_id'], $content_id);
				}

			}
		}

		return $ret;
	}

	/**
	 * Add content_id to the XML document
	 * @access  private
	 * @param   content_id name, xml root pointer, tag name
	 * @return  xml content_id pointer
	 * @author  Mauro Donadio
	 */
	private function _xmlAddContentID($id, $root, $tagName = 'content_id'){
		$id = intval($id);
		
		$content_id = $this->_dom->createElement($tagName);
		$root->appendChild($content_id);
			$attribute = $this->_dom->createAttribute("id");
			$content_id->appendChild($attribute);
				$value = $this->_dom->createTextNode($id);
				$attribute->appendChild($value);

		return $content_id;
	}

	/**
	 * Creates xml elements for each database extracted item
	 * @access  private
	 * @param   data to insert, xml root pointer
	 * @return  void
	 * @author  Mauro Donadio
	 */
	private function _xmlFillFields($content, $root){

		$i=0;

		foreach($content as $key => $value){

			// first element is the content_id already specified as parent tag
			// we want to ease the XML!
			if($i==0){
				$i++;
				continue;
			}

			// avoid to import the 'theme' preference
			if($key == 'theme')
				continue;

			// $_aContentURL
			// Replace the text with the page address
			if($key ==  'text'){

				$f	= explode('/',$_SERVER['PHP_SELF']);
				$value	= 'http://'.$_SERVER['SERVER_NAME'] . '/' . $f[1] . '/' . 'home/course/content.php?_cid=' . $content['content_id'];
			}

			// FOR EACH DATABASE FIELD OF THE CONTENT ID
			$field = $this->_dom->createElement($key);
			$root->appendChild($field);
				$value = $this->_dom->createTextNode($value);

			$field->appendChild($value);
		}
		
		return;
	}

}
?>