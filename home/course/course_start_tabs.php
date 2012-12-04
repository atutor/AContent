<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }

function get_tabs() {
//these are the _AT(x) variable names and their include file
	/* tabs[tab_id] = array(tab_name, file_name,                accesskey) */
    
    /* DEFAULT PROF
	$tabs[0] = array('manually',       		'manually.inc.php',          'm');
	$tabs[1] = array('structure',    		'structure.inc.php',    's');
        $tabs[2] = array('wizard', 'wizard.inc.php',  'w');
     */

/* Check whether the template_structure is enabled or disabled */
        include_once(TR_INCLUDE_PATH.'classes/DAO/DAO.class.php');
        $dao = new DAO();
        
        $inc=0;
        $tabs[$inc] = array('manually',       		'manually.inc.php',          'm');    
/* I do not see the tab if the table structure ac_config the value is 0 (disable) */
        $sql="SELECT value FROM ".TABLE_PREFIX."config WHERE name='enable_template_structure'";
        $result=$dao->execute($sql);
        if(is_array($result))
        {
            foreach ($result as $support) {
                if($support['value']==TR_STATUS_ENABLED)
                    $tabs[++$inc] = array('structure',    		'structure.inc.php',    's');
            }  
        }
        
        //future feature
        //$tabs[++$inc] = array('wizard', 'wizard.inc.php',  'w');
       
	return $tabs;
}

function output_tabs($current_tab) {
	global $_base_path;
	$tabs = get_tabs();
	$num_tabs = count($tabs);
?>
	<table class="etabbed-table" border="0" cellpadding="0" cellspacing="0" width="95%">
	<tr>		
		<?php 
		for ($i=0; $i < $num_tabs; $i++): 
			if ($current_tab == $i):?>
				<td class="editor_tab_selected">
					
					<?php echo _AT($tabs[$i][0]); ?>
				</td>
				<td class="tab-spacer">&nbsp;</td>
			<?php else: ?>
				<td class="editor_tab">
					

					<?php echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="editor_buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'pointer\';" '.$clickEvent.' />'; ?>
				</td>
				<td class="tab-spacer">&nbsp;</td>
			<?php endif; ?>
		<?php endfor; ?>
		<td >&nbsp;</td>
	</tr>
	</table>
<?php }




?>