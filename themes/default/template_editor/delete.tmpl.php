<div id="subnavlistcontainer">
    <div id="sub-navigation">
    <span style="width:3em; float:left;margin-left:2em;margin-right:-2em;">
    <a href="template_editor/index.php?tab=<?php echo $this->tab; ?>"><img src="themes/default/images/previous.png" alt="<?php echo _AT('back'); ?>"></a>
    </span>
        <ul id="subnavlist">
            <?php 
                if($this->template_type == "page_templates"){
                    $app_type = "page";
                }else if($this->template_type == "layouts")  { 
                    $app_type = "layout"; 
                }else if($this->template_type == "structures")  { 
                    $app_type = "structure"; 
                }
                echo '<li><a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_'. $app_type.'.php?temp='. $this->template_dir.'"><strong>'. _AT('edit_template') . '</strong></a></li>';
                echo '<li><a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_meta.php?type='.$this->template_type.SEP.'temp='.$this->template_dir.'"><strong>'. _AT('edit_metadata') . '</strong></a></li>';
                //echo '<li  class="active"><strong>'. _AT('delete') . '</strong></li>';
                echo '<li><span style="font-weight:bold; text-decoration:none;">'. _AT('delete') . '</span></li>';
            ?>
        </ul>
    </div>
</div>
<div class="input-form">
    <div class="row">
        <?php echo _AT('confirm_template_delete', $this->template_name); ?>
    </div>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?type=<?php echo $this->template_type; ?>&temp=<?php echo $this->template_dir; ?>">
        <div class="row buttons">
            <input type="submit" name="submit" value="<?php echo _AT('yes'); ?>">
            <input type="submit" name="cancel" value="<?php echo _AT('no'); ?>">
        </div>
    </form>
</div>