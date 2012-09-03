<?php

	class Themes{


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
		private $uniq		= 'dnd';

		// folders and documents to be excluded from the list of the themes
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
			if(isset($_POST['listatemi'], $_POST['applicaTemaCorso_btn']))
				$this->applicaTemaCorso();
			elseif(isset($_POST['listatemi'], $_POST['applicaTemaLezione_btn']))
				$this->applicaTemaLezione();

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

		public function getListaTemi(){

			$listaTemi	= array();
			$dir		= array();

			// read the list of available themes
			$dir		= scandir($this->mod_path['themes_dir_int']);

			// subtract files to be excluded from the list of available themes
			$dir		= array_diff($dir, $this->except);

			// call the function that validates the available themes
			$listaTemi	= $this->temaConforme($dir);

			return $listaTemi;
		}

		/*
		 * 	The following function reads from the filesystem existing themes and validates them
		 * 	according to pre-set criteria (eg comparison between version of the theme and core)
		 * 	and returns an array of available and valid themes.
		 *	input:	$dir[]			list of available themes
		 * 	output:	list of available themes skimmed according to the compatibility of each theme
		 * 
		 * */
		
		private function temaConforme($dir = array()){
			
			// scan all existing themes
		
			foreach($dir as $item){
			
				$isdir	= $this->mod_path['themes_dir_int'].$item;
			
				// checking if the element is a directory
				if(is_dir($isdir)){
			
					// check if exists the .info file and parse it
					
					//$isfile	= $isdir.'/theme.info';
			
					$xml_file = $isdir.'/layout.xml';
					if(is_file($xml_file)) {
						$xml = simplexml_load_file($xml_file);
						
						foreach($xml->children() as $child) {
							$name = $child->getName();
							if($name == "release") {
								$info['core'] = $child->version;
								
							}
							$info[$name] = $child;
						}
						
						
					//}
					
					
					//if(is_file($isfile)){

						//$info	= parse_ini_file($isdir.'/theme.info');
						
						// if you did not specify a name, use the folder name
						if(!$info['name'])
							$info['name'] = $item;

						// check the "core"
						if(!$info['core'])
							
							continue;
						else {

							$vfile	= explode('.', $info['core']);
							$vcore	= explode('.', VERSION);
			
							// cursory check for version compatibility
							// stopping the cycle to the first incompatibility found
							if($vfile[0] < $vcore[0])
								// not compatible!
								continue;
							elseif(strtolower($vfile[1]) != 'x' and $vfile[1] < $vcore[1]) 
								// not compatible!
								continue;
							
						}
		
						// put the info of the current theme into an array
						$temi[$item] = $info;
						
					}
				}
			}
		
			return $temi;
		}


		/*
		 * 	The following function provides for the generation of a form
		 *	to graphically show the user the list of available themes.
		 * 	The form is returned by the function and, then,
		 * 	integrated the output of this module.
		 *	input:	$listaTemi[]	list of available themes
		 *	output:	none 
		 * */

		public function createUI($listaTemi){

			$ui		= '';
		
			//$ui		.= '<form action="" id="dnd_themod" onsubmit="return false" method="post" style="display: none">';
			$ui		.= '<form action="'.$_SERVER['REQUEST_URI'].'" id="dnd_themod" method="post" style="display: none">';
		
			// select
		
			$ui		.= '<label for="listatemi">'._AT('theme_select').'</label>';
			$ui		.= '<select name="listatemi" id="listatemi">';
		
			// default option (null)
			$ui		.= '<option selected="selected">';
			$ui		.= ' - ';
			$ui		.= '</option>';
			
			// put all the available themes into the dropdown menu
			foreach($listaTemi as $tname => $tval){
		
				$ui	.= '<option value="'.$tname.'">';
					$ui	.= $tval['name'];
				$ui	.= '</option>';
			
			}
			
			$ui		.= '</select>';
			
			// fine select
			
			$ui		.= '<div>';
		
			$ui		.= '<div><img src="" alt="Screenshot" desc="Screenshot" title="Screenshot" id="themeScreenshot" /></div>';
		
			$ui		.= '<div><input type="submit" value="'._AT('theme_course_apply').'" id="applicaTemaCorso_btn" name="applicaTemaCorso_btn" /></div>';

			// add this option only if you have set it
			if($this->config['apply_to_the_lesson'] == 0)
				$display = 'display:none';

			$ui		.= '<div><input type="submit" style="'.$display.'" value="'._AT('theme_lesson_apply').'" id="applicaTemaLezione_btn" name="applicaTemaLezione_btn" /></div>';
		
			$ui		.= '</div>';
			
			$ui		.= '</form>';
			
			$ui		.= '<noscript><div>'._AT('no_js').'</div></noscript>';
		
			return $ui;
		}


		/*
		 *
		 */

		private function applicaTemaCorso(){

			define("TR_INCLUDE_PATH", "../../include/");

			require_once(TR_INCLUDE_PATH.'vitals.inc.php');
			require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

			$tema_selezionato	= (isset($_POST['listatemi']) ? htmlentities($_POST['listatemi']) : '-');

			// theme reset
			if($tema_selezionato == '-'){
				$theme_name		= '';
			}else{
				// new theme
				$theme_name		= $tema_selezionato;
			}


			$contentDAO = new ContentDAO();

			$lezioni	= $contentDAO->getContentByCourseID($this->course_id);

			// for each lesson with that code of course, set / override the style of lessons

			for($i = 0; $i < count($lezioni); $i++){

				$cid		= $lezioni[$i]['content_id'];
				$text		= $this->textFixPHP($lezioni[$i]['text']);

				if(strstr($text, '<div id="content">')){
					$text = str_replace('<div id="content">','',$text, $count);
				}
	
				$text = strrev($text);
	
				for($j=0; $j<$count; $j++)
					$text = str_replace('>vid/<','',$text);
				
				$text = strrev($text);
	
				$text		= '<div id="content">'.$text.'</div>';
					
				// clean up the text from <div id="dnd" and add it:
				// it does not mean that all classes have the tag,
				// then, take it off to all the add it again (more safe even if most "expensive")
	
				// clean up the text from the tag
				$text		= $this->clearCon
	
				// write on db
				$contentDAO->UpdateField($cid, 'text', $text);
				$contentDAO->UpdateField($cid, 'theme', $theme_name);
			}
			
			// page redirect
			echo '<script type="text/javascript">';
				echo 'window.location = "'.$_SERVER['REQUEST_URI'].'";';
			echo '</script>';

		}

		/*
		 * 
		 */

		private function applicaTemaLezione(){

			define("TR_INCLUDE_PATH", "../../include/");

			require_once(TR_INCLUDE_PATH.'vitals.inc.php');
			require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

			$tema_selezionato	= (isset($_POST['listatemi']) ? htmlentities($_POST['listatemi']) : '-');

			// theme reset
			if($tema_selezionato == '-'){
				$theme_name		= '';
			}else{
				// new theme
				$theme_name		= $tema_selezionato;
			}



			$contentDAO = new ContentDAO();

			$lezioni	= $contentDAO->get($this->content_id);

			$text		= $this->textFixPHP($lezioni['text']);

			if(strstr($text, '<div id="content">')){
				$text = str_replace('<div id="content">','',$text, $count);
			}

			$text = strrev($text);

			for($i=0; $i<$count; $i++)
				$text = str_replace('>vid/<','',$text);
			
			$text = strrev($text);

			$text		= '<div id="content">'.$text.'</div>';

			// clean up the text from <div id="dnd" and add it:
			// it does not mean that all classes have the tag,
			// then, take it off to all the add it again (more safe even if most "expensive")

			// clean up the text from the tag
			$text		= $this->clearContent($text);

			// write on db
			$contentDAO->UpdateField($this->content_id, 'text', $text);
			$contentDAO->UpdateField($this->content_id, 'theme', $theme_name);

			// page redirect
			echo '<script type="text/javascript">';
				echo 'window.location = "'.$_SERVER['REQUEST_URI'].'";';
			echo '</script>';
		}

		/*
		 *	Function that cleans the content passed as a parameter.
		 *	Cleaning is the removal of the block <div id="dnd"> <div id="anteprima-footer"> </ div> built by theme
		 */
	
		private function clearContent($content = ''){
	
			// delete the div
			$content	= str_replace('<div id="'.$this->uniq.'">','', $content);
	
			// completely delete the footer from the text
			$content	= preg_replace('/<div id="anteprima-footer">(.*)<\/div><\/div>/Uis', '', $content);
	
			return $content;
		}

		/*
		 *	exaggeration
		 *	TinyMCE is not precise with the carriage return, then, I try to repair
		 *	the display differences between TinyMCE and AContent preview.
		 *	text	= text to clean up
		*/
	
		private function textFixPHP($text = ''){
	
			// JUMP

			/*
			$text	= str_replace('<p>&nbsp;</p>', "<br />", $text);
			$text	= str_replace('<p></p>', "<br />", $text);
			$text	= str_replace('<br>', "<br />", $text);
			$text	= str_replace('<p>', "<div>", $text);
			$text	= str_replace('</p>', "</div>", $text);
			*/
	
			return $text;		
		}


		public function exportTheme($_content_id = '', $_course_id = ''){
			die('Ho bloccato Themes.class.php perch&#232; va parametrizzata!');
			$stylesheet	= '';
			
			$stylesheet = file_get_contents('../../dnd_themod/themes/unibo/unibo.css');
			
			$stylesheet	= str_replace('#'.$this->uniq, 'body', $stylesheet);
			
			//var_dump($stylesheet);
/*
			if($_content_id == '')
				echo 'tratto il caso del corso intero';
			else
				echo 'tratto il caso della lezione';
 */
/*
			echo '<pre>';
				print_r(get_defined_vars());
			echo '</pre>';
*/
			//die();


			return $stylesheet;
		}

		public function appendStyle($rows, $zipfile, $_content_id = ''){

			// $_content_id		determinates if packing the lesson or the entire course
			// $row				complete lessons list for a specific course

			$styles			= array();
			$stylesheet		= '';
			/*
			echo $_content_id;
			var_dump($rows);
			die();
			*/
			for($i=0; $i < count($rows); $i++){

				if($rows[$i]['theme'] != ''){
					// In another version, AContent requires 'commoncartridge' as folder
					$rows[$i]['head']					= '<link rel="stylesheet" href="commoncartridge/'.$rows[$i]['theme'].'.css" type="text/css" />'.$rows[$i]['head'];
					//$rows[$i]['head']					= '<link rel="stylesheet" href="'.$rows[$i]['theme'].'.css" type="text/css" />'.$rows[$i]['head'];
					$rows[$i]['use_customized_head']	= '1';

					// create image folder

						/*
						echo $src;
						echo '<br />';
						echo $dst;
						echo '<br />';
						*/

						/*
						$dir = opendir($src);
						while(false !== ( $file = readdir($dir)) ) { 
					        if (( $file != '.' ) && ( $file != '..' )) {
					            //copy($src . '/' . $file, $dst . '/' . $file);
								$zipfile->add_file($src . '/' . $file, $dst . '/' . $file);
						    } 
						}
						closedir($dir);
						*/
					/*
					echo '<hr />';
					echo 'content_id = '.$_content_id;
					echo '<br />';
					echo '$rows[$i][\'content_id\'] = '.$rows[$i]['content_id'];
					echo '<br />';
					echo 'styles = ';
					print_r($styles);
					echo '<br />';
					*/

					// if it's a new style to add
					if(($_content_id != '' AND $_content_id == $rows[$i]['content_id']) OR $_content_id == ''){
						//if(!in_array($rows[$i]['theme'], $styles)){
						
						//echo '<div>FIRST STEP</div>';

						$styles[]		= $rows[$i]['theme'];

						//if(($_content_id != '' AND $_content_id == $rows[$i]['content_id']) OR $_content_id == ''){
						if(in_array($rows[$i]['theme'], $styles)){
							//echo '<div>-SECOND STEP</div>';

							if($stylesheet = file_get_contents('../../dnd_themod/themes/'.$rows[$i]['theme'].'/'.$rows[$i]['theme'].'.css')){
								
								//echo '<div>THIRD (LAST) STEP</div>';
								$stylesheet	= str_replace('#'.$this->uniq, 'body', $stylesheet);
								$zipfile->add_file($stylesheet, 'resources/commoncartridge/'.$rows[$i]['theme'].'.css');

								// add images folder
								$src	= '../../dnd_themod/themes/'.$rows[$i]['theme'].'/'.$rows[$i]['theme'].'/';
								//$dst	= 'resources/commoncartridge/'.$rows[$i]['theme'].'/';
								$dst	= 'resources/commoncartridge/'.$rows[$i]['theme'].'/';
		
								$zipfile->create_dir('resources/commoncartridge/'.$rows[$i]['theme'].'/');
								$zipfile->add_dir($src, $dst);
							}
						}
					}//else{
						//echo '<div>BACK STEP</div>';
					//}
				}
			}
			//die('END');

			return $rows;
		}

	}
?>
