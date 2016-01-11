<div id="subnavlistcontainer">
    <div id="sub-navigation">
        <ul id="subnavlist">
            <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>"><?php echo _AT('create_template'); ?></a></li>
            <?php 
                if($this->template_type=="layout") echo '<li class="active"><b>'. _AT('layout') . '</b></li>';
                else echo '<li><a href="'. $_SERVER['PHP_SELF'] . '?tab=layout">'. _AT('layout') . '</a></li>';
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
            if($this->template_type=="layout") $editor_url="edit_layout.php";
            elseif($this->template_type=="structures") $editor_url="edit_structure.php";
            elseif($this->template_type=="pages") $editor_url="edit_page.php";

            foreach( $this->template_list as $template=>$name){
                echo "<li><a href='template_editor/".$editor_url."?temp=".$template."' class='courseName'>";
                if($this->template_type == "layout"){
                    echo '<img src="templates/layout/'.$template.'/screenshot-'.$template.'.png" class="template_thumb"/><br />';
                 }else if($this->template_type == "pages"){
                    echo '<img src="templates/page_template/'.$template.'/screenshot.png" class="template_thumb"/><br />';
                 } else if($this->template_type == "structures"){
                 //debug($template);
                 //debug($this->template_list);
                    ?>
           <!--         
                    <script type="text/javascript">
                        this.this_template  ='';
                        this.this_template = '<?php echo $template; ?>';
                        this.this_structures = '<?php echo $this->template_list; ?>';
                        //alert(this.this_template);
                    </script>
                    <script type="text/javascript" src="template_editor/js/structure.js"></script>
                      -->
                    <?php
                    /*
                    $xmlpath=realpath("../templates/structures")."/". $template."/content.xml";
                    $xmlDoc = new DOMDocument();
                    $xmlDoc->load($xmlpath);
                    $x = $xmlDoc->documentElement;
                    $this->xml_script = $xmlDoc->saveXML($xmlDoc->documentElement);
                  
                    echo '<textarea  id="xml_text_'.$template.'" name="xml_text" rows="35" cols="60"  style="border:1px solid #cccccc; resize: none;background-color:#ffffff; min-height:400px; display:none;">'.$this->xml_script.'</textarea>';
                    echo "<div id='tree_preview_".$template."' style='width:24em; height:300px; margin:2px;'></div>";
                    */
                    
                 }
                 
               echo "<br /><span style='float:right;'>".$name."<span>";
                echo '</a><a href="template_editor/delete.php?temp='.$template.SEP.'type='.$this->template_type.'"><img src="themes/default/images/delete.gif" alt="'._AT('delete').'" title="'._AT('delete').'"/></a></li>';
            } 
            ?>
            </ol>
        </div>
    </fieldset>
</div>

