<?php

	class Page_template {


		/**
		 * Update an existing course record
		 * @access  public
		 * @param   courseID: course ID
		 *          fieldName: the name of the table field to update
		 *          fieldValue: the value to update
		 * @return  true if successful
		 *          error message array if failed; false if update db failed
		 * @author  Mauro Donadio
		 */

		/*
		 * Variables declarations / definitions
		 * 
		 * */

		private $mod_path	= array();
		private $config		= array();
		private $content_id	= '';
		private $course_id	= '';
		private $uniq		= 'templates';

		// folders and documents to be excluded from the list of the page_template
		private $except		= array('.', '..', '.DS_Store', 'desktop.ini', 'Thumbs.db');


		/**
		 * Constructor: sets the main variables used (paths, ..)
		 * @access  public
		 * @param   mod_path: associative array containing the paths list
		 * @return  none
		 * @author  Mauro Donadio
		 */

		public function __construct($mod_path){

			global $_course_id, $_content_id;

			/* content id of an optional chapter */
			$this->content_id	= (isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : $_content_id);
			$this->course_id	= (isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : $_course_id);

			//
			if(isset($_POST['cid'], $_POST['action'], $_POST['text']) AND htmlentities($_POST['action']) == 'savePageTemplateContent'){
				$this->applyPageTemplateToContent();
			}

			$this->mod_path		= $mod_path;

			if($this->mod_path != '')
				$this->config		= parse_ini_file($this->mod_path['syspath'].'config.ini');

			return;
		}

		/*
		 * Open the configuration file reading the parameters
		 * input:	none
		 * output:	none
		 * 
		 * */

		public function getConfig(){
			return $this->config;
		}

		/*
		 * Read loaded themes creating a list of available themes
		 * input:	none
		 * output:	none
		 * 
		 * */

		public function getPageTemplateList(){

			$page_template_list	= array();
			$dir			= array();

			// read the list of available themes
			$dir		= scandir($this->mod_path['page_template_dir_int']);

			// subtract files to be excluded from the list of available themes
			$dir		= array_diff($dir, $this->except);

			$dir		= array_merge(array(),$dir);

			// call the function that validates the available themes
			$page_template_list	= $this->validatedPageTemplate($dir);
			return $page_template_list;
		}

		
		function getPageTemplates($item) {
			$pages = array();
			foreach ($item->children() as $child) {
				 $name = (string)$child['name'];
				 $pages[$name] = $this->checkPageTemplate($child['name']);
			}

			return $pages;
		}
		
		function checkPageTemplate($name) {
			$info = null;
			$isdir = $this->mod_path['models_dir_int'].$name;
			
				// checking if the element is a directory
				if(is_dir($isdir)){
					// check if exists the .info file and parse it
					$xml_file = $isdir.'/page_template.xml';
					if(is_file($xml_file)) {
						$xml = simplexml_load_file($xml_file);
						
						foreach($xml->children() as $child) {
							$name = $child->getName();
							if($name == "release") 
								$info['core'] = trim($child->version);
							else
								$info[$name] = trim($child);
						}
						
						// if you did not specify a name, use the folder name
						if(!$info['name'])
							$info['name'] = trim($item);
						
						// reduce the name length to 15 characters
						$limit	= 15;
						if(strlen($info['name']) >= $limit){
							$info['name']	= substr($info['name'], 0, ($limit-2));
							$info['name']	.= '..';
						}

						// check the "core"
						if(!$info['core'])
							continue;
						else{

							$vfile	= explode('.', $info['core']);
							$vcore	= explode('.', VERSION);
			
							// cursory check for version compatibility
							// stopping the cycle to the first incompatibility found
							if($vfile[0] < $vcore[0])
								// not compatible!
								continue;
							elseif(strtolower($vfile[1]) != 'x' AND $vfile[1] < $vcore[1])
								// not compatible!
								continue;
						}
		
						// put the info of the current model into an array
						//$modelli[$item] = $info;
					}
				}
			
			/*echo("qui");
			foreach ($info as $key=>$value) {
				echo ("a ".$key ." b ". $value);
			}*/
			return $info;
		}
		
		
		
		
		/*
		 * 	The following function reads from the filesystem existing page_template and validates them
		 * 	according to pre-set criteria (eg comparison between version of the model and core)
		 * 	and returns an array of available and valid page_template.
		 *	input:	$dir[]			list of available page_template
		 * 	output:	list of available page_template skimmed according to the compatibility of each model
		 * 
		 * */
		
		function validatedPageTemplate($dir = array()){
			
			// scan all existing themes
			$page_template = array();
			foreach($dir as $item)  {				
				
                            $page_template[$item] = $this->checkPageTemplate($item);
                            
                        }
				
			return $page_template;
		}


		/*
		 * 	The following function provides for the generation of a form
		 *	to graphically show the user the list of available page_template.
		 * 	The form is returned by the function and, then,
		 * 	integrated the output of this model.
		 *	input:	$pageTemplateList[]	list of available page_template
		 *	output:	none 
		 * */

		public function createUI(){

			
			$ui		= '';
			//style="display: none"
			$ui		.= '<form action="" onsubmit="return false" method="post" id="page_template_box"  >';

			$ui		.= '<div>';

			$ui		.= '<div><input type="checkbox" value="'._AT('activate_page_template').'" id="activate_page_template" />';
			$ui		.= '<label for="activate_page_template"> '._AT('activate_page_template').'</label></div>';
	
			$ui		.= '<div><input type="checkbox" value="'._AT('arrange_page_template').'" id="orderPageTemplate" />';
			$ui		.= '<label for="orderPageTemplate"> '._AT('arrange_page_template').'</label></div>';

			$ui		.= '</div>';

			$ui		.= '</form>';

			$ui		.= '<noscript><div>'._AT('no_js').'</div></noscript>';
		
			
			return $ui;
		}


		private function applyPageTemplateToContent(){

			$cid	= htmlentities($_POST['cid']);
			$text	= $this->textFixPHP($_POST['text']);

			if(strlen($text) == 0)
				return;

			define("TR_INCLUDE_PATH", "../../include/");

			require_once(TR_INCLUDE_PATH.'vitals.inc.php');
			require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

			$contentDAO = new ContentDAO();

			// write on db
			$contentDAO->UpdateField($cid, "text", $text);

			// page redirect
			echo '<script type="text/javascript">';
				echo 'window.location = "'.$_SERVER['REQUEST_URI'].'";';
			echo '</script>';

			return;
		}

		public function getpage_templatetructure($pageTemplateID = ''){
			$struct	= '';

			$file	= '../../templates/page_template/'.$pageTemplateID.'/'.$pageTemplateID.'.html';

			if(file_exists($file))
				$struct	= file_get_contents($file);

			return $struct;
		}


		/*
		 *	exaggeration
		 *	TinyMCE is not precise with the carriage return, then, I try to repair
		 *	the display differences between TinyMCE and AContent preview.
		 *	text	= text to clean up
		*/
	
		private function textFixPHP($text = ''){
		
			return $text;		
		}

	}
?>
