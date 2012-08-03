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

			// leggo la lista dei temi disponibili
			$dir		= scandir($this->mod_path['themes_dir_int']);

			// sottraggo i file da escludere dalla lista dei temi disponibili
			$dir		= array_diff($dir, $this->except);

			// chiamo la funzione che valida i temi disponibili
			$listaTemi	= $this->temaConforme($dir);

			return $listaTemi;
		}

		/*
		 * 	La seguente funzione legge dal filesystem i temi esistenti e li valida
		 * 	secondo criteri preimpostati (es. confronto tra versione del tema e del core)
		 * 	restituendo un vettore di temi validi e disponibili.
		 *	input:	$dir[]			lista dei temi disponibili
		 * 	output:	lista dei temi disponibili scremata in base alla compatiblità di ogni tema
		 * 
		 * */
		
		private function temaConforme($dir = array()){
			
			// scandisco tutti i temi esistenti
		
			foreach($dir as $item){
			
				$isdir	= $this->mod_path['themes_dir_int'].$item;
			
				// controllo che l'elemento sia una directory
				if(is_dir($isdir)){
			
					// controllo esista il file .info e lo parso
					
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
						
						// se non è stato specificato un nome, utilizzo quello della cartella
						if(!$info['name'])
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
							elseif(strtolower($vfile[1]) != 'x' and $vfile[1] < $vcore[1]) 
								// non compatibile!
								continue;
							
						}
		
						// inserisco le info del tema corrente all'interno di un vettore
						$temi[$item] = $info;
						
					}
				}
			}
		
			return $temi;
		}


		/*
		 * 	La seguente funzione provvede alla generazione di un form per mostrare
		 *	graficamente all'utente la lista di temi a disposizione.
		 * 	Il form viene restituito dalla funzione e, successivamente, integrato
		 * 	all'output del presente modulo.
		 *	input:	$listaTemi[]	lista dei temi disponibili verificati
		 *	output:	none 
		 * */

		public function createUI($listaTemi){

			$ui		= '';
		
			//$ui		.= '<form action="" id="dnd_themod" onsubmit="return false" method="post" style="display: none">';
			$ui		.= '<form action="'.$_SERVER['REQUEST_URI'].'" id="dnd_themod" method="post" style="display: none">';
		
			// select
		
			$ui		.= '<label for="listatemi">'._AT('theme_select').'</label>';
			$ui		.= '<select name="listatemi" id="listatemi">';
		
			// opzione di default (null)
			$ui		.= '<option selected="selected">';
			$ui		.= ' - ';
			$ui		.= '</option>';
			
			// inserisco fra le opzioni tutti i temi a disposizione
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

			// aggiungo questa opzione solo se è stata impostata
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

			// per ogni lezione con quel codice di corso, imposto / sovrascrivo lo stile delle lezioni

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
				/*
				echo '<div style="position:absolute">';
				echo '--> '.var_dump($text);
				echo '</div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
				*/
					
				// pulisco il testo dal <div id="dnd" e lo aggiungo:
				// non e' detto che tutte le lezioni abbiano il tag, quindi, lo tolgo a tutti e lo riaggiungo (piu' sicuro anche se piu' oneroso)
	
				// pulisco il testo dal tag
				$text		= $this->clearContent($text);

				// lo aggiungo
				/*
				if($theme_name != ''){
					$text		= '<div id="'.$this->uniq.'">'.$text.'<div id="anteprima-footer"> </div></div>';
				}*/
	
				// scrivo sul db
				$contentDAO->UpdateField($cid, 'text', $text);
				$contentDAO->UpdateField($cid, 'theme', $theme_name);
			}
			
			//die();

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

			// pulisco il testo dal <div id="dnd" e lo aggiungo:
			// non e' detto che tutte le lezioni abbiano il tag, quindi, lo tolgo a tutti e lo riaggiungo (piu' sicuro anche se piu' oneroso)

			// pulisco il testo dal tag
			$text		= $this->clearContent($text);

			// lo aggiungo
			/*
			if($theme_name != ''){
				//$text		= '<div id="'.$this->uniq.'">'.$text.'<div id="anteprima-footer"> </div></div>';
				$text		= '<div id="'.$this->uniq.'">'.$text.'<div id="anteprima-footer"> </div></div>';
			}*/

			// scrivo sul db
			$contentDAO->UpdateField($this->content_id, 'text', $text);
			$contentDAO->UpdateField($this->content_id, 'theme', $theme_name);

			// page redirect
			echo '<script type="text/javascript">';
				echo 'window.location = "'.$_SERVER['REQUEST_URI'].'";';
			echo '</script>';
		}

		/*
		 *	Funzione che pulisce il contenuto passato come parametro.
		 *	La pulizia consiste nella rimozione del blocco <div id="dnd"><div id="anteprima-footer"></div> incorporato dal tema
		 */
	
		private function clearContent($content = ''){
	
			// elimino il div
			$content	= str_replace('<div id="'.$this->uniq.'">','', $content);
	
			// elimino completamente il footer dal testo
			$content	= preg_replace('/<div id="anteprima-footer">(.*)<\/div><\/div>/Uis', '', $content);
	
			return $content;
		}

		/*
		 *	exaggeration
		 *	TinyMCE non è preciso con i carriage return, quindi, cerco di riparare
		 *	alle differenze di visualizzazione tra TinyMCE e l'anteprima di AContent.
		 *	text	= testo da ripulire
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
