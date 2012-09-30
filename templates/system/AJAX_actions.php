<?php

	######################################
	#	THEMES
	######################################

	// enable lesson theme

	if(isset($_POST['dnd_request'])){

		$config							= parse_ini_file('config.ini');

		if($_POST['dnd_request'] == '759e647ad85438ed2669dbabfb77a602')
			$config['apply_to_the_lesson']	= '1';
		elseif($_POST['dnd_request'] == 'c1388816ccd2cc64905595c526ca678b')
			$config['apply_to_the_lesson']	= '0';

		writeINIfile($config);
	}
	
	
	
	
	function writeINIfile($config = 0){

		$fp		= fopen('config.ini','w');
		//$fp		= fopen('http://localhost/AContent_1.2/templates/system/config.ini','w');

		foreach($config as $key => $value){
			fwrite($fp, $key.' = '.$value.';');
		}

		fclose($fp);

		return;
	}

	######################################
	#	page_template
	######################################
	
	if(isset($_POST['cid'], $_POST['action'], $_POST['text']) AND htmlentities($_POST['action']) == 'savePageTemplateContent'){

		include_once('Page_template.class.php');

		$contentID	= htmlentities($_POST['cid']);
		$mod		= new Page_template('');

		//$mod->getpage_templatetructure($contentID);

		return;
	}	
	
	
	
	######################################
	#	Get model internal structure (pure HTML)
	######################################
	
	if(isset($_POST['mID'])){

		include_once('Page_template.class.php');

		$pageTempalteID	= htmlentities($_POST['mID']);

		$mod		= new Page_template('');

		$res		= $mod->getpage_templatetructure($pageTempalteID);
		
		echo $res;

		return;
	}
?>