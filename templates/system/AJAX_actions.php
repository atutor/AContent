<?php

######################################
#	THEMES
######################################

// enable lesson theme

if(isset($_POST['dnd_request'])){

	$config = parse_ini_file('config.ini');

	if($_POST['dnd_request'] == '759e647ad85438ed2669dbabfb77a602')
		$config['apply_to_the_lesson'] = '1';
	elseif($_POST['dnd_request'] == 'c1388816ccd2cc64905595c526ca678b')
		$config['apply_to_the_lesson'] = '0';

	writeINIfile($config);
}

function writeINIfile($config = 0){

	$fp = fopen('config.ini','w');

	foreach($config as $key => $value){
		fwrite($fp, $key.' = '.$value.';');
	}

	fclose($fp);

	return;
}

######################################
#	page_template
######################################      
if(isset($_POST['cid'], $_POST['action'], $_POST['text'])){
	include_once('Page_template.class.php');

	$contentID = htmlentities($_POST['cid']);
	$mod = new Page_template('');
	$text = $_POST['text'];

	$mod->applyPageTemplate($contentID,$text);
	return;
}

if(isset($_POST['control'])){
	$mod = new Page_template('');
	$res = $mod->control();
}

######################################
#	Get model internal structure (pure HTML)
######################################

if(isset($_POST['mID'])){

	include_once('Page_template.class.php');

	$pageTempalteID = htmlentities($_POST['mID']);

	$mod = new Page_template('');

	$res = $mod->getpage_templatetructure($pageTempalteID);
	
	echo $res;

	return;
}

//CM function that extracts content from the db
if(isset($_POST['content']))
{
	include_once('Layout.class.php');
	$cid=$_POST['content'];
	$con = new Layout('');

	$result=$con->content_text($cid);

	echo $result;
}
?>