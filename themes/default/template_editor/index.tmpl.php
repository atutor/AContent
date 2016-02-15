<div id="subnavlistcontainer">
    <div id="sub-navigation">
        <ul id="subnavlist">
            <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>"><?php echo _AT('create_template'); ?></a></li>
            <?php 
                if($this->template_type=="layouts") echo '<li class="active"><b>'. _AT('layout') . '</b></li>';
                else echo '<li><a href="'. $_SERVER['PHP_SELF'] . '?tab=layouts">'. _AT('layout') . '</a></li>';
                if($this->template_type=="structures") echo '<li class="active"><b>'. _AT('structures') . '</b></li>';
                else echo '<li><a href="'. $_SERVER['PHP_SELF'] . '?tab=structures">'. _AT('structures') . '</a></li>';
                if($this->template_type=="pages") echo '<li class="active"><b>'. _AT('pages') . '</b></li>';
                else echo '<li><a href="'. $_SERVER['PHP_SELF'] . '?tab=pages">'. _AT('pages') . '</a></li>';
            ?>
        </ul>
    </div>
</div>
<div class="input-form">
    <fieldset class="group_form">
        <legend class="group_form"><?php echo _AT('available_templates'); ?></legend>
        <div class="results ">
            <div style="font-weight:bold"><?php echo _AT($this->template_type); ?></div>
            <ol class="remove-margin-left" id="layout_list">
            <?php
            global $_base_href;
            $editor_url;
            //if($this->template_type=="layouts") $editor_url="edit_layout.php";
            if($this->template_type=="layouts") $editor_url="edit_layout.php";
            elseif($this->template_type=="structures") $editor_url="edit_structure.php";
            elseif($this->template_type=="pages") $editor_url="edit_page.php";

                    $mod_path['templates']		= realpath(TR_BASE_HREF	. 'templates').'/';
			        $mod_path['templates_int']	= realpath(TR_INCLUDE_PATH. '../templates').'/';
			        $mod_path['templates_sys']	= $mod_path['templates_int']	. 'system/';
			        $mod_path['structs_dir']		= $mod_path['templates']. 'structures/';
			        $mod_path['structs_dir_int']	= $mod_path['templates_int']	. 'structures/';
                    include_once('../templates/system/Structures.class.php');
                    $structs	= new Structures($mod_path);
                    $structsList = $structs->getStructsList();

            foreach( $this->template_list as $template=>$name){
                echo "<li style='float:left;text-align:top;'><a href='template_editor/".$editor_url."?temp=".$template."' class='courseName'>";
                if($this->template_type == "layouts"){
                    echo '<img src="templates/layouts/'.$template.'/screenshot-'.$template.'.png" class="template_thumb" alt=""/><br />';
                 }else if($this->template_type == "pages"){
                    if(file_exists('../templates/page_templates/'.$template.'/screenshot.png')){
                        echo '<img src="templates/page_templates/'.$template.'/screenshot.png" class="template_thumb" alt="" /><br />';
                    }else{
                        echo '<img src="images/page_placeholder.jpg" class="template_thumb" alt="" style="height:11em; width:11em;text-align:center;border:1px black dashed;margin:.1em;"/><br />';
                    }
                 } else if($this->template_type == "structures"){
                   
                 }
                 
                echo "<br /><span style='float:right;'>".$name."<span>";
                echo '</a><!--<a href="template_editor/delete.php?temp='.$template.SEP.'type='.$this->template_type.'"><img src="themes/default/images/delete.gif" alt="'._AT('delete').'" title="'._AT('delete').'"/></a> -->';
                 if($this->template_type == "structures"){
                    foreach ($structsList as $struct) {
                    
                 	    if($struct['short_name'] == $template){
                 	    ?>
                        <div style=" margin-bottom: 10px; <?php if($check) echo 'border: 2px #cccccc dotted;';?> ">
                            <div style="font-size:95%; margin-left: 10px;">
                                    <div style="display: inline;" id="div_outline_<?php echo $struct['short_name'];?>">
                                        <?php   
                                                $struc_manag = new StructureManager($struct['short_name']);
                                                $struc_manag->printPreview(false, $struct['short_name']); 
                                        ?>
                                    </div>
                            </div>
                        </div>
                <?php
                 	    }
                 	} 
                }
                echo '</li>';
            
            } 
            ?>
            </ol>
        </div>
    </fieldset>
</div>

