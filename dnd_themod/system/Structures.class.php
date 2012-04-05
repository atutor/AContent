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

		// cartelle e documenti da escludere dalla lista dei temi presenti
		private $except		= array('.', '..', '.DS_Store', 'desktop.ini', 'Thumbs.db');



		/**
		 * Constructor: sets the main variables used (paths, ..)
		 * @access  public
		 * @param   mod_path: associative array containing the paths list
		 * @return  none
		 * @author  Mauro Donadio
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

			// leggo la lista dei temi disponibili
			$dir		= scandir($this->mod_path['structs_dir_int']);

			// sottraggo i file da escludere dalla lista dei temi disponibili
			$dir		= array_diff($dir, $this->except);
			
			// chiamo la funzione che valida le strutture disponibili
			$structsList	= $this->checkStructs($dir);
			
			return $structsList;
		}

		/*
		 * 	La seguente funzione legge dal filesystem i temi esistenti e li valida
		 * 	secondo criteri preimpostati (es. confronto tra versione del tema e del core)
		 * 	restituendo un vettore di temi validi e disponibili.
		 *	input:	$dir[]			lista dei temi disponibili
		 * 	output:	lista dei temi disponibili scremata in base alla compatiblità di ogni tema
		 * 
		 * */
		
		private function checkStructs($dir = array()) {
		
			foreach($dir as $item){
			
				$isdir	= $this->mod_path['structs_dir_int'].$item;
				
				
				
				// controllo che l'elemento sia una directory
				if(is_dir($isdir)){
			
					// controllo esista il file .info e lo parso
					$isfile	= $isdir.'/structure.info';
			
					if(is_file($isfile)){

						$info	= parse_ini_file($isdir.'/structure.info');
						
						// se non è stato specificato un nome, utilizzo quello della cartella
						if(!$info['name'] || $info['name'] != $item)
							$info['name'] = $item;

						// controllo il "core"
						if(!$info['core'])
							continue;
						else {

							$vfile	= explode('.', $info['core']);
							$vcore	= explode('.', VERSION);
			
							// controllo superficiale per la compatibilità della versione
							// bloccando il ciclo alla prima incompatibilità trovata
							if($vfile[0] < $vcore[0])
								// non compatibile!
								continue;
							elseif(strtolower($vfile[1]) != 'x' AND $vfile[1] < $vcore[1])
								// non compatibile!
								continue;
						}
						
		
						
						// inserisco le info del tema corrente all'interno di un vettore
						$structs[$item] = $info;
						
						
					}
				}
			}
		
			
		
			
			return $structs;
		}



	}
		
 

?>
