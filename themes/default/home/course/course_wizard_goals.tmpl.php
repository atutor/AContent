<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('TR_INCLUDE_PATH')) { exit; } 
global $onload;
$onload = 'document.form.title.focus();';
global $languageManager;
require_once(TR_INCLUDE_PATH.'classes/CoursesUtility.class.php');
require_once(TR_INCLUDE_PATH.'../home/classes/GoalsManager.class.php');
//die($_SERVER['PHP_SELF']);// /AContent/AContent/home/course/course_strat.php
?>
<form action="<?php echo $_SERVER['PHP_SELF'].'?'; if ($this->cid > 0) echo '_cid='.$this->cid; else if ($this->pid > 0) echo 'pid='.$this->pid.SEP.'_course_id='.$this->course_id; else echo '_course_id='.$this->course_id;?>" method="post" name="form"> 
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
<div style=" weight: 10%; margin: 10px;"><?php echo _AT('create_content_4'); ?></div>




<fieldset>
    <legend>Choose the lesson's goals</legend>
<table>
<?php 
$goals_manager = new GoalsManager();

$goals = $goals_manager->getGoals();
$support=1;  
    echo '<tr>';
    foreach ($goals as $goal) {
        if($support<=4){
            echo '<td>';
                echo '<input id="'.$goal.'" type="checkbox" name="'.$goal.'"></input>';
                echo '<label style="padding-right:10px;" for="'.$goal.'">'.$goal.'</label>';
            echo '</td>';
            $support++;
        }else{
            echo '</tr>';
            $support=1;
            echo '<tr>';
                echo '<td>';
                    echo '<input id="'.$goal.'" type="checkbox" name="'.$goal.'"></input>';
                    echo '<label style="padding-right:10px;" for="'.$goal.'">'.$goal.'</label>';
            echo '</td>';
            $support++;
        }
    }
   

?>
</table>
</fieldset>
<div style="margin:10px;"></div>
<fieldset style="width: 250px;">
    <legend>Enable accessibility check</legend>
    <table>
        <tr>
            <td>
                 <input id="true_acc" type="checkbox" name="true"></input><label>True</label>
                 <input id="false_acc" type="checkbox" name="false"></input><label>False</label>
            </td>
        </tr>
    </table>
</fieldset>
<div style="margin:10px;"></div>
<fieldset style="width: 250px;">
    <legend>Enable compyright check</legend>
    <table>
        <tr>
            <td>
                 <input id="true_comp" type="checkbox" name="true"></input><label>True</label>
                 <input id="false_comp" type="checkbox" name="false"></input><label>False</label>
            </td>
        </tr>
    </table>
</fieldset>

<div class="row buttons">
<p class="submit_button" style="margin:10px;">
        <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" class="submit" />
        <!--<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="submit" />-->
</p>
</div>




</form>

<script>
    $('#server-msg').css('display','none');
</script>

