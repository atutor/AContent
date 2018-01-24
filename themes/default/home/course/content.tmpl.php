<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; } ?>

<?php if ($this->has_text_alternative || $this->has_audio_alternative || $this->has_visual_alternative || $this->has_sign_lang_alternative): ?>
<div id="alternatives_shortcuts">
<?php if ($this->has_text_alternative) :?>
  <a href="<?php echo $_SERVER['PHP_SELF'].'?_cid='.$this->cid.(($_GET['alternative'] == 3) ? '' : htmlentities_utf8(SEP).'alternative=3'); ?>">
    <img src="<?php echo TR_BASE_HREF; ?>images/<?php echo (($_GET['alternative'] == 3) ? 'pause.png' : 'text_alternative.png'); ?>" 
      alt="<?php echo (($_GET['alternative'] == 3) ? _AT('stop_apply_text_alternatives') : _AT('apply_text_alternatives')); ?>" 
      title="<?php echo (($_GET['alternative'] == 3) ? _AT('stop_apply_text_alternatives') : _AT('apply_text_alternatives')); ?>" 
      border="0" class="img1616" />
  </a>
<?php endif; // END OF has text alternative?>
<?php if ($this->has_audio_alternative) :?>
  <a href="<?php echo $_SERVER['PHP_SELF'].'?_cid='.$this->cid.(($_GET['alternative'] == 1) ? '' : htmlentities_utf8(SEP).'alternative=1'); ?>">
    <img src="<?php echo TR_BASE_HREF; ?>images/<?php echo (($_GET['alternative'] == 1) ? 'pause.png' : 'audio_alternative.png'); ?>" 
      alt="<?php echo (($_GET['alternative'] == 1) ? _AT('stop_apply_audio_alternatives') : _AT('apply_audio_alternatives')); ?>" 
      title="<?php echo (($_GET['alternative'] == 1) ? _AT('stop_apply_audio_alternatives') : _AT('apply_audio_alternatives')); ?>" 
      border="0" class="img1616" />
  </a>
<?php endif; // END OF has audio alternative?>
<?php if ($this->has_visual_alternative) :?>
  <a href="<?php echo $_SERVER['PHP_SELF'].'?_cid='.$this->cid.(($_GET['alternative'] == 4) ? '' : htmlentities_utf8(SEP).'alternative=4'); ?>">
    <img src="<?php echo TR_BASE_HREF; ?>images/<?php echo (($_GET['alternative'] == 4) ? 'pause.png' : 'visual_alternative.png'); ?>" 
      alt="<?php echo (($_GET['alternative'] == 4) ? _AT('stop_apply_visual_alternatives') : _AT('apply_visual_alternatives')); ?>" 
      title="<?php echo (($_GET['alternative'] == 4) ? _AT('stop_apply_visual_alternatives') : _AT('apply_visual_alternatives')); ?>" 
      border="0" class="img1616" />
  </a>
<?php endif; // END OF has visual alternative?>
<?php if ($this->has_sign_lang_alternative) :?>
  <a href="<?php echo $_SERVER['PHP_SELF'].'?_cid='.$this->cid.(($_GET['alternative'] == 2) ? '' : htmlentities_utf8(SEP).'alternative=2'); ?>">
    <img src="<?php echo TR_BASE_HREF; ?>images/<?php echo (($_GET['alternative'] == 2) ? 'pause.png' : 'sign_lang_alternative.png'); ?>" 
      alt="<?php echo (($_GET['alternative'] == 2) ? _AT('stop_apply_sign_lang_alternatives') : _AT('apply_sign_lang_alternatives')); ?>" 
      title="<?php echo (($_GET['alternative'] == 2) ? _AT('stop_apply_sign_lang_alternatives') : _AT('apply_sign_lang_alternatives')); ?>" 
      border="0" class="img1616" />
  </a>
<?php endif; // END OF has sign language alternative?>
</div>
<?php endif; // END OF displaying alternative shortcut icons?>

<?php if ($this->shortcuts): ?>
<input type="hidden" name="course_id" value="<?php echo $this->course_id; ?>" />

<fieldset id="shortcuts"><legend><?php echo _AT('shortcuts'); ?></legend>
	<ul>
		<?php foreach ($this->shortcuts as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
</fieldset>
<?php endif; ?>

<?php 
if ($_SESSION["prefs"]["PREF_SHOW_CONTENTS"] && $this->content_table <> "") 
	echo $this->content_table;
?>



    <!-- old
    <div id="content-text">
    -->
<?php
// code for the selection of chosen by layout
define('TR_INCLUDE_PATH', '../../include/');
global $associated_forum, $_course_id, $_content_id;
include_once(TR_INCLUDE_PATH.'classes/DAO/DAO.class.php');
require_once(TR_INCLUDE_PATH.'lib/tinymce.inc.php');
require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');

$dao = new DAO();
$cid = $_content_id;
$sql="SELECT layout FROM ".TABLE_PREFIX."content WHERE content_id=?";
$values = $cid;
$types = "i";
$result=$dao->execute($sql, $values, $types);
    if(is_array($result))
    {
        foreach ($result as $support) {
           //echo $support['head'];
           $choice_layout=$support['layout'];
           break;
        }  
    }
    if($choice_layout!=''){
        ?>
        <div id="content">
        <link  rel="stylesheet" href="<?php echo TR_BASE_HREF; ?>templates/layouts/<?php echo $choice_layout; ?>/<?php echo $choice_layout; ?>.css"  type="text/css" />
                    <?php echo $this->body; ?>
        </div>
        <?php
    }else{ ?>
       <div id="content">
       <?php echo $this->body; ?>
          
       </div>
<?php   } ?>


<?php if (!empty($this->test_ids)): ?>
<div id="content-test" class="input-form">
		<strong><?php echo _AT('tests') . ':' ; ?></strong>
		<p class="top-tool"><?php echo $this->test_message; ?></p>
		<ul class="tools">
		<?php 
			foreach ($this->test_ids as $id => $test_obj){
//				echo '<li><a href="'.url_rewrite('tools/test_intro.php?tid='.$test_obj['test_id'], AT_PRETTY_URL_IS_HEADER).'">'.
				echo '<li><a href="'.TR_BASE_HREF.'tests/preview.php?tid='.$test_obj['test_id'].'&_cid='.$this->cid.'">'.
				AT_print($test_obj['title'], 'tests.title').'</a><br /></li>';
			}
		?>
		</ul>
</div>
<?php endif; ?>

<?php if (is_array($this->forum_ids)): ?>
<div id="content-test" class="input-form">
    <ol>
        <li style="list-style-type: none;"><strong><?php echo _AT('forums') . ':' ; ?></strong>
            <ul class="tools">
                <?php
                foreach ($this->forum_ids as $id => $forum_obj) {
                    echo '<li>'.AT_print($forum_obj['title'], 'forums.title').'<br /></li>';
                }
                ?>
            </ul>
        </li>
    </ol>
</div>
<?php endif; ?>
<!-- <div id="sequence-links2">
    <?php 
    if ($this->sequence_links['resume']): ?>
    <a style="color:white;" href="<?php echo $this->sequence_links['resume']['url']; ?>" accesskey="."><img src="<?php echo $this->base_path.'themes/'.$this->theme; ?>/images/resume.png" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?> Alt+." alt="<?php echo $this->sequence_links['resume']['title']; ?> Alt+." class="shortcut_icon" /></a>
    <?php else:
          if ($this->sequence_links['previous']): ?>
    <a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," accesskey=","> <img src="<?php echo $this->base_path.'themes/'.$this->theme; ?>/images/previous.png" alt="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," class="shortcut_icon" /> <?php echo _AT('previous_topic'); ?></a>
    <?php endif;
          if ($this->sequence_links['next']): ?>
    <a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." accesskey="."><?php echo _AT('next_topic'); ?> <img src="<?php echo $this->base_path.'themes/'.$this->theme; ?>/images/next.png" alt="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." class="shortcut_icon" /></a>
    <?php endif; ?>
    <?php endif; ?>
    &nbsp;
  </div> -->
<div id="content-info">
	<?php echo $this->content_info; ?>

</div>