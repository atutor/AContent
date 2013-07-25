<div id="subnavlistcontainer">
    <div id="sub-navigation">
        <ul id="subnavlist">
            <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>"><?php echo _AT('create_template'); ?></a></li>
            <?php 
                if($this->template_type=="layout") echo '<li class="active"><b>'. _AT('layout') . '</b></li>';
                else echo '<li><a href="'. $_SERVER['PHP_SELF'] . '?tab=layout">'. _AT('layout') . '</a></li>';
                if($this->template_type=="structure") echo '<li class="active"><b>'. _AT('structures') . '</b></li>';
                else echo '<li><a href="'. $_SERVER['PHP_SELF'] . '?tab=structures">'. _AT('structures') . '</a></li>';
                if($this->template_type=="page") echo '<li class="active"><b>'. _AT('pages') . '</b></li>';
                else echo '<li><a href="'. $_SERVER['PHP_SELF'] . '?tab=pages">'. _AT('pages') . '</a></li>';
            ?>
        </ul>
    </div>
</div>
<div class="input-form">
    <fieldset class="group_form">
        <legend class="group_form"><?php echo _AT('available_templates'); ?></legend>
        <div class="results" style="float:left; width:240px; ">
            <div style="font-weight:bold"><?php echo _AT($this->template_type); ?></div>
            <ol class="remove-margin-left">
            <?php
            $editor_url;
            if($this->template_type=="layout") $editor_url="edit_layout.php";
            elseif($this->template_type=="structures") $editor_url="edit_structure.php";
            elseif($this->template_type=="pages") $editor_url="edit_page.php";

            foreach( $this->template_list as $template=>$name){
                echo "<li><a href='template_editor/".$editor_url."?temp=".$template."' class='courseName'>";
                echo $name;
                echo "</a></li>";
            } 
            ?>
            </ol>
        </div>
    </fieldset>
</div>

