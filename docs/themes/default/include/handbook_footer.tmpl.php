<?php 
/************************************************************************/
/* AFrame                                                               */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

?>

<div class="seq">
	<?php if (isset($this->prev_page)): ?>
		<?php echo _AT('previous_chapter'); ?>: <a href="frame_content.php?p=<?php echo $this->prev_page; ?>" accesskey="," title="<?php echo _AT($this->pages[$this->prev_page]['title_var']); ?> Alt+,"><?php echo _AT($this->pages[$this->prev_page]['title_var']); ?></a><br />
	<?php endif; ?>

	<?php if (isset($this->next_page)): ?>
		<?php echo _AT('next_chapter'); ?>: <a href="frame_content.php?p=<?php echo $this->next_page; ?>" accesskey="," title="<?php echo _AT($this->pages[$this->next_page]['title_var']); ?> Alt+,"><?php echo _AT($this->pages[$this->next_page]['title_var']); ?></a><br />
	<?php endif; ?>
</div>

<div class="tag">
	All text is available under the terms of the GNU Free Documentation License. 
</div>
</body>
</html>