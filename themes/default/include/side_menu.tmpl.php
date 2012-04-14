<?php 
if (!defined('TR_INCLUDE_PATH')) { exit; } 

if (!is_array($this->side_menu)) return;

foreach ($this->side_menu as $dropdown_file){
	
	if (file_exists($dropdown_file)) { require($dropdown_file); } 
}

?>
<div style="position:absolute; bottom:0px;">&nbsp;</div>
