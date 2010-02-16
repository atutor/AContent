<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; } ?>

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

<div id="content-text">
	<?php echo $this->body; ?>
</div>

<?php if (!empty($this->test_ids)): ?>
<div id="content-test" class="input-form">
	<ol>
		<strong><?php echo _AT('tests') . ':' ; ?></strong>
		<li class="top-tool"><?php echo $this->test_message; ?></li>
		<ul class="tools">
		<?php 
			foreach ($this->test_ids as $id => $test_obj){
//				echo '<li><a href="'.url_rewrite('tools/test_intro.php?tid='.$test_obj['test_id'], AT_PRETTY_URL_IS_HEADER).'">'.
				echo '<li><a href="tools/test_intro.php?tid='.$test_obj['test_id'].'">'.
				AT_print($test_obj['title'], 'tests.title').'</a><br /></li>';
			}
		?>
		</ul>
	</li></ol>
</div>
<?php endif; ?>

<div id="content-info">
	<?php echo $this->content_info; ?>

</div>