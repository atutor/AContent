<div id="subnavlistcontainer">
    <div id="sub-navigation">
        <ul id="subnavlist">
            <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>?create=true">Create Template</a></li>
        </ul>
    </div>
</div>
<div class="input-form">
    <fieldset class="group_form">
        <legend class="group_form">Available Templates</legend>
        <div class="results" style="float:left; width:240px; ">
            <div style="font-weight:bold">Layout Templates</div>
            <ol class="remove-margin-left">
            <?php
            foreach( $this->layout_list as $template=>$name){
                echo "<li><a href='template_editor/edit_layout.php?temp=".$template."' class='courseName'>";
                echo $name;
                echo "</a></li>";
            } 
            ?>
            </ol>
        </div>
        <div class="results" style="float:left; width:240px;">
            <div style="font-weight:bold">Structure Templates</div>
            <ol class="remove-margin-left">
            <?php
            foreach( $this->structure_list as $template=>$name){
                echo "<li ><a href='template_editor/edit_structure.php?temp=".$template."' class='courseName'>";
                echo $name;
                echo "</a> </li>";
            }
            ?>
            </ol>
        </div>
        <div class="results" style="float:left; width:240px;">
            <div style="font-weight:bold">Page Templates</div>
            <ol class="remove-margin-left">
            <?php
            foreach( $this->pgtemp_list as $template=>$name){
                echo "<li ><a href='template_editor/edit_page.php?temp=".$template."' class='courseName'>";
                echo $name;
                echo "</a> </li>";
            }
            ?>
            </ol>
        </div>
    </fieldset>
</div>

