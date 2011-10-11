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

/**
 * Menu
 * 1. Generate main menu items based on login user
 * 2. Generate path in bread crumb
 * 3. Decide the page to display (redirect) based on login user's privilege.
 *    This page is set as current page
 * 4. Generate sub menus of current page
 * 5. Generate back to page of current page
 * @access	public
 * @author	Cindy Qi Li
 * @package	Menu
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/PrivilegesDAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');

class Menu {

	// all private
	var $pages;                               // top tab pages
	var $current_page;                        // current page
	var $root_page;                           // root page relative to current page
	var $breadcrumb_path = array();           // array of breadcrumb path
	var $sub_menus;                           // array of sub-menus of current page
	var $path;                                // array of all parent pages to current page, used for breadcrumb path and generating back to page
	var $back_to_page;                        // string of parent page to go back to

	/**
	 * Constructor: Initialize top pages (tab menu), all pages accessible by current user, current page.
	 * Generate top tab menu items based on session user_id. If no user login in (public view), use public menu
	 * @access  public
	 * @param   None
	 * @author  Cindy Qi Li
	 */
	function Menu()
	{
		$this->pages[TR_NAV_TOP] = array();        // top tab pages

		$this->init();           // Initialize $this->pages[TR_NAV_PUBLIC] & $this->pages
		$this->setTopPages();    // set top pages based on user id

		// decide current page.
		// if the page that user tries to access is from one of the public link
		// but not define in user's priviledge pages, re-direct to the first $this->pages[TR_NAV_TOP]
		$this->setCurrentPage();
		$this->sub_menus = $this->setSubMenus($this->current_page);   // loop recursively to set $this->submenus to the top parent of $this->current_page
		$this->root_page = $this->setRootPage($this->current_page);  
		$this->path = $this->setPath($this->current_page);
		$this->back_to_page = $this->setBackToPage();
	}

	/**
	 * initialize: public accessible items ($this->pages[TR_NAV_PUBLIC]); all accessible pages ($this->pages)
	 * @access  private
	 * @param   user id
	 * @return  true
	 * @author  Cindy Qi Li
	 */
	private function init()
	{
		// $_pages is defined in include/constants.inc.php
		global $_pages, $_base_path;

		// initialize $this->pages
		$this->pages = $_pages;
		// end of initializing $this->pages
		
		$priviledgesDAO = new PrivilegesDAO();
		$rows = $priviledgesDAO->getPublicPrivileges();

		if (is_array($rows))
		{
			foreach ($rows as $id => $row)
			{
				$this->pages[TR_NAV_PUBLIC][] = array($row['link'] => array('title_var'=>$row['title_var'], 'parent'=>TR_NAV_TOP));
			}
		}
		// end of initializing $this->pages[TR_NAV_PUBLIC]

		return true;
	}

	/**
	 * Set top pages array based on login user's priviledge. If there's no login user, use priviledges that are open to public.
	 * @access  private
	 * @param   none
	 * @return  true
	 * @author  Cindy Qi Li
	 */
	private function setTopPages()
	{
		global $_base_path, $_course_id, $_content_id;

		$priviledgesDAO = new PrivilegesDAO();

		if (isset($_SESSION['user_id']) && $_SESSION['user_id'] <> 0)
		{
			$rows = $priviledgesDAO->getUserPrivileges($_SESSION['user_id']);
		}
		else // public pages
		{
			$rows = $priviledgesDAO->getPublicPrivileges();
		}
		if (is_array($rows))
		{
			foreach ($rows as $id => $row)
			{
				// replace the required constants in link
				$row['link'] = Utility::replaceConstants($row['link']);
				list($url, $param) = Utility::separateURLAndParam($row['link']);
				if (Utility::authenticate(array_key_exists('user_requirement', $row) ? $row['user_requirement'] : NULL, false)) {
					$this->pages[TR_NAV_TOP][] = array('url' => $_base_path.$row['link'], 
				                                   'title' => _AT($row['title_var']),
					                               'param' => $param);
				}
				
				// add section pages if it has not been defined in $this->pages
				if (!isset($this->pages[$url]))
				{
				    $this->pages = array_merge($this->pages, 
				                           array($url => array('title_var'=>$row['title_var'], 'parent'=>TR_NAV_TOP, 'param' => $param)));
				}
				else
				{
					$this->pages[$url]['param'] = $param;
				}
			}
		}
		
		return true;
	}

	/**
	 * Decide current page.
	 * if the page that user tries to access is from one of the public link
	 * but not define in user's priviledge pages, re-direct to the first $this->pages[TR_NAV_TOP]
	 * @access  private
	 * @return  true
	 * @author  Cindy Qi Li
	 */
	private function setCurrentPage()
	{
		global $_base_path, $_base_href, $msg;

		$this->current_page = substr($_SERVER['PHP_SELF'], strlen($_base_path));

		if (!isset($this->pages[$this->current_page]))
		{
			if (!$this->isPublicLink($this->current_page))  // report error if the link is not from a public link
			{
				$msg->addError(array('PAGE_NOT_FOUND', $_base_href.$this->current_page));
			}

			// re-direct to first $_pages URL
			foreach ($this->pages[TR_NAV_TOP] as $page)
			{
//				debug($_base_path.$this->current_page);debug($page);
				if ($_base_path.$this->current_page != $page['url'])
				{
					header('Location: '.$page['url']);
						
					// reset current_page after re-direction
					$this->current_page = substr($_SERVER['PHP_SELF'], strlen($_base_path));
						
					// Note: must exit. otherwise, the rest of includeheader.inc.php proceeds and prints out all messages
					// which is not going to be displayed at re-directed page.
					exit;
				}
			}
		}
	}

	/**
	* Set sub-menus of current page by $_pages[$current_page]['children']
	* @access  private
	* @return  true
	* @author  Cindy Qi Li
	*/
	private function setSubMenus($page) {
		global $_base_path, $_course_id;

		if (isset($page) && defined($page)) 
		{
			// reached the top
			return array();
		} 
		else if (isset($this->pages[$page]['children'])) 
		{
			$param = $this->getParam($page);
//			$sub_menus[] = array('url' => $_base_path . $page.$param, 'title' => $this->getPageTitle($page), 'param' => $param);

			foreach ($this->pages[$page]['children'] as $child) 
			{
				$this->pages[$child]['param'] = $param;
				$sub_menus[] = array('url' => $this->addUrlParam($_base_path . $child, $param), 
				                    'title' => $this->getPageTitle($child), 
				                    'has_children' => isset($this->pages[$child]['children']),
				                    'param' => $param);
			}
		} 
		else if (isset($this->pages[$page]['parent'])) 
		{
			// no children
			return $this->setSubMenus($this->pages[$page]['parent']);
		}

		return $sub_menus;
	}

	/**
	* Set the back to page of $this->current_page
	* @access  private
	* @return  true
	* @author  Cindy Qi Li
	*/
	private function setBackToPage() 
	{
		$back_to_page = '';
		
		unset($this->path[0]);
		if (isset($this->path[2]['url'], $this->sub_menus[0]['url']) && $this->path[2]['url'] == $this->sub_menus[0]['url']) {
			$back_to_page = $this->path[3];
		} 
		else if (isset($this->path[1]['url'], $this->sub_menus[0]['url']) && $this->path[1]['url'] == $this->sub_menus[0]['url']) {
			$back_to_page = isset($this->path[2]) ? $this->path[2] : null;
		} 
		else if (isset($this->path[1])) {
			$back_to_page = $this->path[1];
		}
		
		return $back_to_page;
	}
	
	/**
	 * Check if the given link is a pre-defined public link
	 * @access  private
	 * @param   $page
	 * @return  true  if is a pre-defined public link
	 *          false if not a pre-defined public link
	 * @author  Cindy Qi Li
	 */
	private function isPublicLink($url)
	{
		foreach ($this->pages[TR_NAV_PUBLIC] as $page => $garbage)
		{
			if ($page == $url) return true;
		}

		return false;
	}

	/**
	 * Return the page title of given page
	 * @access  private
	 * @param   $page
	 * @return  page title
	 *          empty if page is not defined
	 * @author  Cindy Qi Li
	 */
	private function getPageTitle($page)
	{
		if (isset($this->pages[$page]['title'])) 
		{
			$page_title = $this->pages[$page]['title'];
		} 
		else 
		{
			$page_title = _AT($this->pages[$page]['title_var']);
		}
		
		return $page_title;
	}
	
	/**
	 * Return the URL with the given parameter attached. $param can have multiple parameters.
	 * The repetitive parameter is skipped
	 * @access  private
	 * @param   $url: URL, can be completed or not completed. For example: tests/index.php?a=1
	 *          $param: URL parameters. For example: ?a=3&b=4
	 * @return  the URL with the given parameter attached at the end. The repetitive parameter is skipped.
	 * @author  Cindy Qi Li
	 */
	private function addUrlParam($url, $param)
	{
		// remove '?'
		$param = str_replace('?', '', trim($param));
		if ($param == '') return $url;
		
		$has_question_mark = false;
		if (strpos($url, '?') > 0) $has_question_mark = true;
		else $counter = 0;
		
		$all_params = explode('&', $param);
		if (is_array($all_params)) {
			foreach ($all_params as $each_param)
			{
				$pair = explode('=', $each_param);
				// check if the parameter is already in the url
				if (strpos($url, $pair[0].'=') > 0) continue;
				else {
					if ($has_question_mark || $counter > 0)
						$url .= '&'.$each_param;
					else {
						$url .= '?'.$each_param;
						$counter++;
					}
				}
			}
		}
		
		return $url;
	}
	
	/**
	 * Return all pages array
	 * @access  public
	 * @return  all pages array
	 * @author  Cindy Qi Li
	 */
	public function getAllPages()
	{
		return $this->pages;
	}

	/**
	 * Return top tab menu item array
	 * @access  public
	 * @return  top tab menu item array
	 * @author  Cindy Qi Li
	 */
	public function getTopPages()
	{
		return $this->pages[TR_NAV_TOP];
	}

	/**
	 * Return top tab menu item array
	 * @access  public
	 * @return  top tab menu item array
	 * @author  Cindy Qi Li
	 */
	public function getCurrentPage()
	{
		return $this->current_page;
	}

	/**
	 * Return sub menus of current page
	 * @access  public
	 * @return  top tab menu item array
	 * @author  Cindy Qi Li
	 */
	public function getSubMenus()
	{
		return $this->sub_menus;
	}

	/**
	 * Return back to page of current page
	 * @access  public
	 * @return  back to page array
	 * @author  Cindy Qi Li
	 */
	public function getBackToPage()
	{
		return $this->back_to_page;
	}
	
	/**
	 * Set root page relative to the current page
	 * @access  public
	 * @return  root page
	 * @author  Cindy Qi Li
	 */
	private function setRootPage($page)
	{
		global $_base_path;

		$parent_page = $this->pages[$page]['parent'];

		if (isset($parent_page) && defined($parent_page)) // check if $parent_page is
		{
			return $_base_path . $page;
		}
		else if (isset($parent_page))
		{
			return $this->setRootPage($parent_page);
		}
		else
		{
			return $_base_path . $page;
		}
	}

	/**
	 * Return root page relative to the current page
	 * @access  public
	 * @return  root page
	 * @author  Cindy Qi Li
	 */
	public function getRootPage()
	{
		return $this->root_page;
	}
	
	/**
	 * Return array of all parent items path to current page
	 * this array is used to determine back to page 
	 * @access  private
	 * @return  array of breadcrumb path
	 * @author  Cindy Qi Li
	 */
	public function setPath($page)
	{
		global $_base_path;

		// all children pages inherit URL parameter of the parent page
		$parent_page = $this->pages[$page]['parent'];
		$parent_page_param = $this->getParam($page);
		
		if (stripos($page, str_replace(array(SEP, '?'), array('', ''), $parent_page_param)) > 0) {
			$page_url = $page;
		} else {
			$page_url = $page.$parent_page_param;
		}

		$page_title = $this->getPageTitle($page);

		if (isset($parent_page) && defined($parent_page))
		{
			$path[] = array('url' => $_base_path . $page_url, 'title' => $page_title, 'param' => $parent_page_param);
		}
		else if (isset($parent_page))
		{
			$path[] = array('url' => $_base_path . $page_url, 'title' => $page_title, 'param' => $parent_page_param);
			$path = array_merge((array) $path, $this->setPath($parent_page));
		} else {
			$path[] = array('url' => $_base_path . $page_url, 'title' => $page_title, 'param' => $parent_page_param);
		}

		return $path;
	}

	/**
	 * Return breadcrumb path
	 * @access  public
	 * @return  root page
	 * @author  Cindy Qi Li
	 */
	public function getPath()
	{
		return $this->path;
	}
	
	/**
	 * return "param" element of the given page or the parents of the given page
	 * @param $page
	 * @return "param" element value
	 */
	private function getParam($page)
	{
		if (isset($this->pages[$page]['param'])) return $this->pages[$page]['param'];
		
		if ($page == TR_NAV_TOP || $page == TR_NAV_PUBLIC) return '';
		
		if (isset($this->pages[$this->pages[$page]['parent']]['param']))
			return $this->pages[$this->pages[$page]['parent']]['param'];
		else
			return $this->getParam($this->pages[$page]['parent']);
	}
}
?>
