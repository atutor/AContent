<?php
global $lay;

if (!defined('TR_INCLUDE_PATH')) exit;

class Layout{
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

	// folders and documents to be excluded from the list of the layout
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

		if(isset($_POST['apply_layout_to_course']))
			$this->applyLayoutToCourse();
		elseif(isset($_POST['apply_layout_to_content']))
			$this->applyLayoutToContent();

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
	 * Read loaded layout creating a list of available layout
	 * input:	none
	 * output:	none
	 * 
	 * */

	public function getLayoutList(){

		$layout_list	= array();
		$dir		= array();

		// read the list of available layout
		$dir = scandir($this->mod_path['layout_dir_int']);

		// subtract files to be excluded from the list of available layout
		$dir		= array_diff($dir, $this->except);
		// call the function that validates the available layout
		$layout_list	= $this->validated_layout($dir);

		return $layout_list;
	}

	/*
	 * 	The following function reads from the filesystem existing layout and validates them
	 * 	according to pre-set criteria (eg comparison between version of the layout and core)
	 * 	and returns an array of available and valid layout.
	 *	input:	$dir[]			list of available layout
	 * 	output:	list of available layout skimmed according to the compatibility of each layout
	 * 
	 * */
	
	private function validated_layout($dir = array()){
		
		// scan all existing layout
		$layouts = array();

		foreach($dir as $item){
	
			$isdir	= $this->mod_path['layout_dir_int'].$item;
		
			// checking if the element is a directory
			if(is_dir($isdir)){
		
				// check if exists the .info file and parse it
		
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
	
					// put the info of the current layout into an array
					$layouts[$item] = $info;
					
				}
			}
		}
	
		return $layouts;
	}

	/*
	 * 	The following function provides for the generation of a form
	 *	to graphically show the user the list of available layout.
	 * 	The form is returned by the function and, then,
	 * 	integrated the output of this module.
	 *	input:	$layout_list[]	list of available layout
	 *	output:	none 
	 * */

	public function createUI($layout_list,$_content_id){
		$IDcontent=$_content_id;

		$ui		= '';
		$ui		.= '<form action="'.$_SERVER['REQUEST_URI'].'" id="templates" method="post" style="display: none">';
	
		// select
		$ui .= '<div style="margin: 5px;">';

		// NUOVA POSIZIONE DEI PULSANTI     
		$ui .= '<input type="submit" style="width:250px;" value="'._AT('layout_content_apply').'" id="apply_layout_to_content" name="apply_layout_to_content" />';

		// Spacing of the buttons
		//$ui .='<div style="padding:5px;"></div>';
		$ui .= '<input type="submit" style="width:250px; margin-left:15px;" value="'._AT('layout_course_apply').'" id="apply_layout_to_course" name="apply_layout_to_course" />';

		$ui .= '<div style="margin: 10px;">';
		$ui .= '<table class="data" rules="cols" summary="">';
		$ui .= '<thead>
                    <tr>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">'._AT('name').'</th>
                        <th scope="col">'._AT('description').'</th>
                        <th scope="col">'._AT('thumbnail').'</th>
                    </tr>
                </thead>';

		$ui .= '<tbody>';
		$ui .= '<tr onclick="preview(\'nothing\');">';
		$ui .= '<td id="radio_nothing"  name="'.$_content_id.'" title="'._AT('nothing').'"><input id="radio-nothing" mouseseup="this.checked=!this.checked" type="radio" name="radio_layout" value="nothing"></td>';
		$ui .= '<td><label for="radio-nothing" style="cursor:pointer;">'._AT('nothing').'</label></td>';
		$ui .= '<td>'._AT('nothing_description').'</td>';

		$ui .= '<td><div><img class="layout_img_small"  src="'.TR_BASE_HREF.'/templates/system/nolayout.png" src=""  desc="Nothing Screenshot" title="'._AT('img_layout_icon','nothing').'" /></td></div>';       
		$ui .= '</tr>'; 
		$ui .= '</tr>';

		foreach($layout_list as $tname => $tval){
			$ui .= '<tr onclick="preview(\''.$tname.'\');">';

			$ui .= '<td id="radio_'.$tname.'" name="'.$_content_id.'" title="'.$tname.'">
<input id="radio-'.$tname.'" mouseseup="this.checked=!this.checked" type="radio" name="radio_layout" value="'.$tname.'">
</td>';

			$ui .= '<td><label style="cursor:pointer;" for="radio-'.$tname.'">'.$tval['name'].'</label></td>';
			$ui .= '<td>'.$tval['description'].'</td>';

			if($tname!='seti' && $tname!='windows'&& $tname!='unibo') {    
				$ui .= '<td><div><img class="layout_img_small" src="'.TR_BASE_HREF.'/templates/layout/'.$tname.'/screenshot-'.$tname.'.png" alt="'._AT('img_layout_icon',$tname).'"  title="'._AT('img_layout_icon',$tname).'"  /></td></div>';       
				$ui .= '</tr>'; 
			} elseif($tname != unibo) {
				$ui .= '<td><div><img  class="layout_img_small" src="'.TR_BASE_HREF.'/templates/layout/'.$tname.'/screenshot-'.$tname.'.png" alt="'._AT('img_layout_icon',$tname).'"  title="'._AT('img_layout_icon',$tname).'" /></td></div>';       
				$ui .= '</tr>'; 
			} else {
				$ui .= '<td><div><img  class="layout_img_small" src="'.TR_BASE_HREF.'/templates/layout/'.$tname.'/screenshot-'.$tname.'.png" alt="'._AT('img_layout_icon',$tname).'" title="'._AT('img_layout_icon',$tname).'"  /></td></div>';       
				$ui .= '</tr>'; 
			}
		}

		$ui .= '</tbody>';
		$ui .= '</table>';
		//$ui		.= '</select>';
		$ui .= '</div>';
		// end select
		
		$ui .= '<div id="content">';
		$ui .= '</div>';

		//POSIZIONE VECCHIA DEI PULSANTI SOTTO LA TABELLA
		$ui	.= '</form>';  

		$ui .= '<noscript><div>'._AT('no_js').'</div></noscript>'. "\n";
		
		// Define language variables needed by javascript file
		$ui .= '<script language="javascript" type="text/javascript">'. "\n";
		$ui .= '//<!--'. "\n";
		$ui .= '    var trans = trans || {};'. "\n";
		$ui .= '    trans.templates = trans.templates || {};'. "\n";
		$ui .= '    trans.templates.preview = "' . htmlentities_utf8(_AT('preview')) . '";'. "\n";
		$ui .= '    trans.templates.title = "' . htmlentities_utf8(_AT('title')) . '";'. "\n";
		$ui .= '    trans.templates.document_body = "' . htmlentities_utf8(_AT('document_body')) . '";'. "\n";
		$ui .= '    trans.templates.devoid_content = "' . htmlentities_utf8(_AT('devoid_content')) . '";'. "\n";
		$ui .= '//-->'. "\n";
		$ui .= '</script>'. "\n";
		
		$ui .='<script type="text/javascript" src="'.TR_BASE_HREF.'templates/system/Layout.js"></script>'. "\n";

		return $ui;
	}

	private function applyLayoutToCourse(){
		global $msg;

		require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

		$contentDAO = new ContentDAO();

		$content	= $contentDAO->getContentByCourseID($this->course_id);

		// for each lesson with that code of course, set / override the style of lessons

		for($i = 0; $i < count($content); $i++){

			$cid		= $content[$i]['content_id'];
			$text		= $this->textFixPHP($content[$i]['text']);

			$text = strrev($text);

			for($j=0; $j<$count; $j++)
				$text = str_replace('>vid/<','',$text);
			
			$text = strrev($text);

			//$text		= '<div id="content">'.$text.'</div>';
				
			// clean up the text from <div id="dnd" and add it:
			// it does not mean that all classes have the tag,
			// then, take it off to all the add it again (more safe even if most "expensive")

			// clean up the text from the tag
			$text    = $this->clearContent($text);

			// insert the value entered by the user in the radio button to choose
			$layout_name=$_POST['radio_layout'];

			// write on db
			$contentDAO->UpdateField($cid, 'layout', $layout_name);
		}
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		// page redirect
		echo '<script type="text/javascript">';
			echo 'window.location = "'.$_SERVER['REQUEST_URI'].'";';
		echo '</script>';

	}

	/*
	 * 
	 */

	private function applyLayoutToContent(){
		global $msg;
		
		require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

		$selected_layout	= (isset($_POST['layout_list']) ? htmlentities($_POST['layout_list']) : '-');

		$contentDAO = new ContentDAO();

		$content	= $contentDAO->get($this->content_id);

		$text		= $this->textFixPHP($content['text']);

		$text = strrev($text);

		for($i=0; $i<$count; $i++)
			$text = str_replace('>vid/<','',$text);
		
		$text = strrev($text);

		//$text		= '<div id="content">'.$text.'</div>';

		// clean up the text from <div id="dnd" and add it:
		// it does not mean that all classes have the tag,
		// then, take it off to all the add it again (more safe even if most "expensive")

		// clean up the text from the tag
		$text		= $this->clearContent($text);

		// insert the value entered by the user in the radio button to choose
		$layout_name= $_POST['radio_layout'];

		// write on db
		$contentDAO->UpdateField($this->content_id, 'layout', $layout_name);
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		// page redirect
		echo '<script type="text/javascript">';
			echo 'window.location = "'.$_SERVER['REQUEST_URI'].'";';
		echo '</script>';
	}

	/*
	 *	Function that cleans the content passed as a parameter.
	 *	Cleaning is the removal of the block <div id="dnd"> <div id="anteprima-footer"> </ div> built by layout
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
		return $text;
	}

	public function content_text($cid){
		require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');

		$contentDAO = new ContentDAO();

		$sql="SELECT text FROM ".TABLE_PREFIX."content WHERE content_id=".$cid."";
		$result=$contentDAO->execute($sql);
		if(is_array($result))
		{
			foreach ($result as $support) {
				$text=$support['text'];
				break;
			}  
		}    

		return $text;
	}

	public function appendStyle($rows, $zipfile, $_content_id = ''){

		// $_content_id		determinates if packing the lesson or the entire course
		// $row				complete lessons list for a specific course

		$styles			= array();
		$stylesheet		= '';
		
		for($i=0; $i < count($rows); $i++){

			if($rows[$i]['layout'] != ''){
				// In another version, AContent requires 'commoncartridge' as folder
				//$rows[$i]['head']					= '<link rel="stylesheet" href="commoncartridge/'.$rows[$i]['layout'].'.css" type="text/css" />'.$rows[$i]['head'];
				//$rows[$i]['head']					= '<link rel="stylesheet" href="'.$rows[$i]['layout'].'.css" type="text/css" />'.$rows[$i]['head'];
				$rows[$i]['use_customized_head']	= '1';

				// create image folder
				// if it's a new style to add
				if(($_content_id != '' AND $_content_id == $rows[$i]['content_id']) OR $_content_id == ''){
					

					$styles[]		= $rows[$i]['layout'];

					if(in_array($rows[$i]['layout'], $styles)){
						

						if($stylesheet = file_get_contents('../../templates/layout/'.$rows[$i]['layout'].'/'.$rows[$i]['layout'].'.css')){
							
							$stylesheet	= str_replace('#'.$this->uniq, 'body', $stylesheet);
							$zipfile->add_file($stylesheet, 'resources/commoncartridge/'.$rows[$i]['layout'].'.css');

							// add images folder
							$src	= '../../templates/layout/'.$rows[$i]['layout'].'/'.$rows[$i]['layout'].'/';
							$dst	= 'resources/commoncartridge/'.$rows[$i]['layout'].'/';
	
							$zipfile->create_dir('resources/commoncartridge/'.$rows[$i]['layout'].'/');
							$zipfile->add_dir($src, $dst);
						}
					}
				}
			}
		}
		

		return $rows;
	}

}
?>
