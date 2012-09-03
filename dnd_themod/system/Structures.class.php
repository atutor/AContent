<?php

	class Structures {

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
		 * @author  Catia Prandi
		 */

		public function __construct($mod_path) {

			//global $_course_id, $_content_id;

			
			
			/* content id of an optional chapter */
			//$this->content_id	= (isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : $_content_id);
			//$this->course_id	= (isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : $_course_id);

			//
			/*if(isset($_POST['listatemi'], $_POST['applicaTemaCorso_btn']))
				$this->applicaTemaCorso();
			elseif(isset($_POST['listatemi'], $_POST['applicaTemaLezione_btn']))
				$this->applicaTemaLezione();
			*/
			
			$this->mod_path		= $mod_path;

			/*if($this->mod_path != '')
				$this->config		= parse_ini_file($this->mod_path['syspath'].'config.ini');
			*/
			
			return;
		}

		/*
		 * Open the configuration file reading the parameters
		 * input:	none
		 * output:	none
		 * 
		 * */

		/*public function getConfig(){
			return $this->config;
		}*/

		/*
		 * Read loaded themes creating a list of available themes
		 * input:	none
		 * output:	none
		 * 
		 * */

		public function getStructsList(){

			$structsList	= array();
			$dir		= array();

			// read the list of available structures
			$dir		= scandir($this->mod_path['structs_dir_int']);

			// subtract files to be excluded from the list of available structures
			$dir		= array_diff($dir, $this->except);
			
			// call the function that validates the available structures
			$structsList	= $this->checkStructs($dir);
			
			return $structsList;
		}

		/*
		 * 	The following function reads from the filesystem existing structures and validates them
		 * 	according to pre-set criteria (eg comparison between version of the theme and core)
		 * 	and returns an array of available and valid structures.
		 *	input:	$dir[]			list of available structures
		 * 	output:	list of available structures skimmed according to the compatibility of each structure
		 * 
		 * */
		
		private function checkStructs($dir = array()) {
		
			foreach($dir as $item){
			
				$isdir	= $this->mod_path['structs_dir_int'].$item;
				
				
				
				// checking if the element is a directory
				if(is_dir($isdir)){
			
					// check if exists the .info file and parse it
					//$isfile	= $isdir.'/structure.info';
					$xml_file = $isdir.'/structure.xml';
					if(is_file($xml_file)) {
						$xml = simplexml_load_file($xml_file);
						
						foreach($xml->children() as $child) {
							$name = $child->getName();
							if($name == "release") {
								$info['core'] = $child->version;
								
							}
							$info[$name] = $child;
						}
					//if(is_file($isfile)){

						//$info	= parse_ini_file($isdir.'/structure.info');
						
						$info['short_name'] = $item;
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
							elseif(strtolower($vfile[1]) != 'x' AND $vfile[1] < $vcore[1])
								// not compatible!
								continue;
						}
						
		
						
						// put the info of the current structure into an array
						$structs[$item] = $info;
						
						
					}
				}
			}
		
			
		
			
			return $structs;
		}



	}
		
 

?>
