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
 
global $_base_href;
 
// header
?>
<div id="error">
	<h4><?php echo _AT('the_follow_errors_occurred'); ?></h4>
	<?php if (is_array($this->item)) : ?>
		<ul>
		<?php foreach($this->item as $e) : ?>
			<li><?php echo $e; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>