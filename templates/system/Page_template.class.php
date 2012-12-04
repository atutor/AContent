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
		 */

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
			if(isset($_POST['cid'], $_POST['text'])){
				$this->applyPageTemplateToContent();
			}

			if(isset($_POST['save_page_templates']))
			{
				$this->applyPageTemplateToContent();
			}

			$this->mod_path		= $mod_path;

			if($this->mod_path != '') {
				$this->config		= parse_ini_file($this->mod_path['syspath'].'config.ini');
			}
			return;
		}

		public function view_page_templates($whit_content)
		{
			$this->content_id	= (isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : $_content_id);
			$this->course_id	= (isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : $_course_id);

			$template="templates";

			if(isset($_POST['activate_page_template_php'])){
				// form if using save as button                        
				echo '<form action="'.$_SERVER['REQUEST_URI'].'" id="test" method="post" style="display: none;" onsubmit="return false">';

				// this gets the cid value from the hidden field added to createUI()
				$cid = $_POST['value_cid'];

				// added css for labels that become buttons       
				echo '<link rel="stylesheet" type="text/css" href="'.TR_BASE_HREF.'/themes/default/forms.css">';
				echo '<div style="text-align:left; margin: 10px; margin-top: 20px; margin-bottom: 15px;">';

				echo '<li id="deactivate_page_template" style="display: inline;">';
				echo '<label style="margin-right:61px; margin-left:2px;">'._AT('label_deactivate_page_template').'</label>';
				echo '<label class="label_button">'._AT('deactivate_page_template').'</label>';
				echo '</li>';

				echo '<li id="activate_page_template" style="display: none;">';
				echo '<label style="margin-right:34px; margin-left:2px;">'._AT('label_active_page_template').'</label>';
				echo '<label class="label_button">'._AT('activate_page_template').'</label>';
				echo '</li>';

				// display the button if there is only arrange content
				if ($whit_content!=0) {
					echo '<li id="orderPageTemplate" style="display: inline;">';
					echo '<div style="padding:5px;"></div>';
					echo '<label style="margin-right:20px; padding-left:2px; padding-right:3px;">'._AT('label_arrange_page_template').'</label>';
					echo '<label class="label_button">'._AT('arrange_page_template').'</label>';
					echo '</li>';

				} else {
					echo '<li id="orderPageTemplate" style="display: none;">';
					echo '<div style="padding:5px;"></div>';
					echo '<label style="margin-right:20px; padding-left:2px; padding-right:3px;">'._AT('label_arrange_page_template').'</label>';
					echo '<label class="label_button">'._AT('arrange_page_template').'</label>';
					echo '</li>';
				}

				echo '<div style="padding:5px;"></div>';

				//  code for save (LABEL by DEFAULT /// this also code for the button)
				echo '<li id="savePageTemplate" style="display: inline;" name="'.$cid.'">';
				echo '<label id="label_save" name="'.$_SERVER['REQUEST_URI'].'" style="margin-right:61px; margin-left:2px;">'._AT('label_save_page_template').'</label>';
				echo '<label class="label_button">'._AT('save').'</label>';
				echo '</li>';                          
				echo '</div>';

				echo '<script type="text/javascript" src="'.TR_BASE_HREF.'/templates/system/Page_template_new.js"></script>';

				$pageTemplateList = array();
   
				// Db calls to get the values ​​of the structure and title
				define('TR_INCLUDE_PATH', '../../include/');
				include_once(TR_INCLUDE_PATH.'classes/DAO/DAO.class.php');
				require_once(TR_INCLUDE_PATH.'lib/tinymce.inc.php');
				require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');
				require_once(TR_INCLUDE_PATH.'../home/classes/StructureManager.class.php');
				Utility::authenticate(TR_PRIV_ISAUTHOR);
				$dao = new DAO();

				$sql="SELECT structure FROM ".TABLE_PREFIX."content WHERE content_id=".$cid."";
				$result=$dao->execute($sql);

				if(is_array($result))
				{
					foreach ($result as $support) {
						$content=$support['structure'];
						break;
					}  
				}

				$sql="SELECT title FROM ".TABLE_PREFIX."content WHERE content_id=".$cid."";
				$result=$dao->execute($sql);
				if (is_array($result)) {
					foreach ($result as $support) {
						$title=$support['title'];
						break;
					}
				}

				if ($content!='') {
					// Upload the array of default page template structure
					$structManager = new StructureManager($content);

					$item=$structManager->getPageTemplatesItem($title);
					$array = $structManager->getContent($item);

				}

				$pageTemplateList = $this->getPageTemplateList();

				echo '<link rel="stylesheet" href="'.TR_BASE_HREF.'/templates/system/page_template.css" type="text/css" />';
				// avoid the input when the array is empty
				if ($pageTemplateList != null) {
					echo '<div class="boxTotal">';    
					echo '<div class="boxPageTemplate" style="display: block;" >';
					echo '<ul>';
					foreach ($pageTemplateList as $key => $value)
					{
						//Check if there is a structure and search if the page template belongs
						// scanned array of predefined structure
						if ($content!='') {
							if (in_array($key,$array)) {
								echo '<li>';
								echo '<table id="'.$key.'" >';
								echo '<tr>';
								echo '<td>';
								echo '<img style="padding:10px;" src="'.TR_BASE_HREF.'/templates/page_template/'.$key.'/screenshot.png" alt="ERRORE img" />';
								echo '</td>';
								echo '</tr>';
								echo '<tr>';
								echo '<td class="desc">';
								echo $value['name'];
								echo '</td>';
								echo '</tr>';
								echo '</table>';
								echo '</li>';  
							}
						} else {
							echo '<li>';
							echo '<table id="'.$key.'" >';
							echo '<tr>';
							echo '<td>';
							echo '<img style="padding:10px;" src="'.TR_BASE_HREF.'/templates/page_template/'.$key.'/screenshot.png" alt="ERRORE img" />';
							echo '</td>';
							echo '</tr>';
							echo '<tr>';
							echo '<td class="desc">';
							echo $value['name'];
							echo '</td>';
							echo '</tr>';
							echo '</table>';
							echo '</li>';  
						}

					}
					echo '</ul>';
					echo '</div>'; // div boxPageTemplate  

					// two button PASTE and COPY
					echo '<div class="boxPageTemplateTool">';
					echo '<ul>';
					echo '</ul>';

					echo '<ul>';
					echo '<li id="pageTemplatePaste" style="display: none;">';

					echo '<img alt="error paste" title="paste" src="'.TR_BASE_HREF.'/templates/system/paste.png">';
					echo _AT('paste_page_template');
					echo '</li>';
					echo '<li id="pageTemplateCopy">';
					echo '<img alt="error copy" title="copy" src="'.TR_BASE_HREF.'/templates/system/copy.png">';
					echo _AT('copy_page_template');
					echo '</li>';
					echo '</ul>';
					echo '</div>';
					echo '</div>'; // div boxTotal

					echo '<div id="content-text">';

					echo '</div>';

					echo '</form>';
				}
			}
		}

 		/*
		 * Open the configuration file reading the parameters
		 * input:	none
		 * output:	none
		 * 
		 */

		public function getConfig(){
			return $this->config;
		}

		/*
		 * Read loaded themes creating a list of available themes
		 * input:	none
		 * output:	none
		 * 
		 */

		public function getPageTemplateList(){
			$page_template_list	= array();
			$dir = array();

			// read the list of available themes
			$dir = scandir($this->mod_path['page_template_dir_int']);

			// subtract files to be excluded from the list of available themes
			$dir = array_diff($dir, $this->except);

			$dir = array_merge(array(),$dir);

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
			$isdir = $this->mod_path['page_template_dir_int'].$name;

			// checking if the element is a directory
			if(is_dir($isdir)) {
				// check if exists the .info file and parse it
				$xml_file = $isdir.'/page_template.xml';
				if(is_file($xml_file)) {
					$xml = simplexml_load_file($xml_file);

					foreach($xml->children() as $child) {
						$name = $child->getName();

						if($name == "release") {
							$info['core'] = trim($child->version);
						} else {
							$info[$name] = trim($child);
						}
					}

					// if you did not specify a name, use the folder name
					if(!$info['name'])
						$info['name'] = trim($item);
					
					// reduce the name length to 15 characters
					$limit	= 15;
					if(strlen($info['name']) >= $limit) {
						$info['name']	= substr($info['name'], 0, ($limit-2));
						$info['name']	.= '..';
					}

					// check the "core"
					if(!$info['core']) {
						continue;
					} else {
						$vfile	= explode('.', $info['core']);
						$vcore	= explode('.', VERSION);
			
						// cursory check for version compatibility
						// stopping the cycle to the first incompatibility found
						if($vfile[0] < $vcore[0]) {
							// not compatible!
							continue;
						} elseif(strtolower($vfile[1]) != 'x' AND $vfile[1] < $vcore[1]) {
							// not compatible!
							continue;
						}
					}
				}
			}	
			return $info;
		}
		
		/*
		 * 	The following function reads from the filesystem existing page_template and validates them
		 * 	according to pre-set criteria (eg comparison between version of the model and core)
		 * 	and returns an array of available and valid page_template.
		 *	input:	$dir[]			list of available page_template
		 * 	output:	list of available page_template skimmed according to the compatibility of each model
		 * 
		 */
		
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
		 */

		public function createUI($sup,$cid){
			$ui = '';
			$ui .='<form action="'.$_SERVER['REQUEST_URI'].'" id="templates" method="post" style="display: none" onsubmit="return false">';

			$ui .= '<div style="text-align:left; margin: 10px; margin-top: 20px; margin-bottom: 15px;">';
			$ui .= '<label id="label_act_page_template_php" style="margin-right:20px;">'._AT('label_active_first_part').'<br>'.
			       _AT('label_active_second_part').'</label><br><div style="padding:5px;"></div>'.
			       '<input type="submit" style="width:250px;" value="Active page template functions" id="activate_page_template_php" name="activate_page_template_php" />';
			$ui .= '<input name="value_cid" type="hidden" value="'.$cid.'" >';

			$ui .='</form>';
			$ui .= '<noscript><div>'._AT('no_js').'</div></noscript>';

			return $ui;
		}

		private function applyPageTemplateToContent(){
			if (isset($_POST['cid'])) {
				echo $_POST['body_text'];
			}
			$cid	= htmlentities($_POST['_cid']);
			$text	= $this->textFixPHP($_POST['body_text']);
			echo $cid;
			echo $text;

			if(strlen($text) == 0)
				return;

			define("TR_INCLUDE_PATH", "../../include/");

			require_once(TR_INCLUDE_PATH.'vitals.inc.php');
			require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

			$contentDAO = new ContentDAO();

			// write on db
			$contentDAO->UpdateField($cid, "text", $text);

			return;
		}

		public function control(){}

		public function applyPageTemplate($cid,$text){
			define("TR_INCLUDE_PATH", "../../include/");

			require_once(TR_INCLUDE_PATH.'vitals.inc.php');
			require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

			$contentDAO = new ContentDAO();

			// write on db
			$contentDAO->UpdateField($cid, "text", $text);


			return;
		}

		public function getpage_templatetructure($pageTemplateID = ''){
			$struct = '';

			$file = '../../templates/page_template/'.$pageTemplateID.'/'.$pageTemplateID.'.html';

			if (file_exists($file)) {
				$struct	= file_get_contents($file);
			}

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
<script>
$('.unsaved').css('display','none');
</script>