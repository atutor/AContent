<?php

	class Models{


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

		// cartelle e documenti da escludere dalla lista dei temi presenti
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

			/*
			echo '<script type="text/javascript">';
				echo 'alert("Mauro Donadioioioioio!");';
			echo '</script>';
			*/

			//
			if(isset($_POST['cid'], $_POST['action'], $_POST['text']) AND htmlentities($_POST['action']) == 'saveModelContent'){
				$this->applicaModelloContenuto();
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

		public function getListaModelli(){

			$listaModelli	= array();
			$dir			= array();

			// leggo la lista dei modelli disponibili
			$dir		= scandir($this->mod_path['models_dir_int']);

			// sottraggo i file da escludere dalla lista dei modelli disponibili
			$dir		= array_diff($dir, $this->except);

			$dir		= array_merge(array(),$dir);

			// chiamo la funzione che valida i modelli disponibili
			$listaModelli	= $this->modelloConforme($dir);

			return $listaModelli;
		}

		/*
		 * 	La seguente funzione legge dal filesystem i modelli esistenti e li valida
		 * 	secondo criteri preimpostati (es. confronto tra versione del modello e del core)
		 * 	restituendo un vettore di modelli validi e disponibili.
		 *	input:	$dir[]			lista dei modelli disponibili
		 * 	output:	lista dei modelli disponibili scremata in base alla compatiblità di ogni modello
		 * 
		 * */
		
		function modelloConforme($dir = array()){
			
			// scandisco tutti i modelli esistenti
		
			foreach($dir as $item){

				$isdir	= $this->mod_path['models_dir_int'].$item;
			
				// controllo che l'elemento sia una directory
				if(is_dir($isdir)){
			
					// controllo esista il file .info e lo parso
					$isfile	= $isdir.'/model.info';
			
					if(is_file($isfile)){

						$info	= parse_ini_file($isdir.'/model.info');
		
						// se non è stato specificato un nome, utilizzo quello della cartella
						if(!$info['name'])
							$info['name'] = $item;
						
						// riduco il nome ad un numero di caratteri accettabile
						$limit	= 15;
						if(strlen($info['name']) >= $limit){
							$info['name']	= substr($info['name'], 0, ($limit-2));
							$info['name']	.= '..';
						}

						// controllo il "core"
						if(!$info['core'])
							continue;
						else{

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
		
						// inserisco le info del modello corrente all'interno di un vettore
						$modelli[$item] = $info;
					}
				}
			}
		
			return $modelli;
		}


		/*
		 * 	La seguente funzione provvede alla generazione di un form per mostrare
		 *	graficamente all'utente la lista di temi a disposizione.
		 * 	Il form viene restituito dalla funzione e, successivamente, integrato
		 * 	all'output del presente modulo.
		 *	input:	$listaTemi[]	lista dei temi disponibili verificati
		 *	output:	none 
		 * */

		public function createUI(){


			$ui		= '';

			$ui		.= '<form action="" onsubmit="return false" method="post" style="display: none" id="dnd_moduli">';

			$ui		.= '<div>';

			$ui		.= '<div><input type="checkbox" value="'._AT('activate_models').'" id="attivaModelli_btn" />';
			$ui		.= '<label for="attivaModelli_btn"> '._AT('activate_models').'</label></div>';
	
			$ui		.= '<div><input type="checkbox" value="'._AT('arrange_models').'" id="ordinaModelli_btn" />';
			$ui		.= '<label for="ordinaModelli_btn"> '._AT('arrange_models').'</label></div>';

			$ui		.= '</div>';

			$ui		.= '</form>';

			$ui		.= '<noscript><div>'._AT('no_js').'</div></noscript>';
		
			return $ui;
		}


		private function applicaModelloContenuto(){

			$cid	= htmlentities($_POST['cid']);
			$text	= $this->textFixPHP($_POST['text']);

			if(strlen($text) == 0)
				return;

			define("TR_INCLUDE_PATH", "../../include/");

			require_once(TR_INCLUDE_PATH.'vitals.inc.php');
			require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

			$contentDAO = new ContentDAO();

			// scrivo sul db
			$contentDAO->UpdateField($cid, "text", $text);

			// page redirect
			echo '<script type="text/javascript">';
				echo 'window.location = "'.$_SERVER['REQUEST_URI'].'";';
			echo '</script>';

			return;
		}

		public function getModelStructure($modelID = ''){
			$struct	= '';

			$file	= '../../dnd_themod/models/'.$modelID.'/'.$modelID.'.html';

			if(file_exists($file))
				$struct	= file_get_contents($file);

			return $struct;
		}

				/*
		 *	exaggeration
		 *	TinyMCE non è preciso con i carriage return, quindi, cerco di riparare
		 *	alle differenze di visualizzazione tra TinyMCE e l'anteprima di AContent.
		 *	text	= testo da ripulire
		*/
	
		private function textFixPHP($text = ''){

			/*
			$text	= str_replace('<p>&nbsp;</p>', "<br />", $text);
			$text	= str_replace('<p></p>', "<br />", $text);
			$text	= str_replace('<br>', "<br />", $text);
			$text	= str_replace('<p>', "<div>", $text);
			$text	= str_replace('</p>', "</div>", $text);
			*/
			
			// rimuovo i doppi header che si vanno a creare
			
	
			return $text;		
		}

	}
?>
