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
 
global $_base_href; ?>

<div id="info">
	<?php if (is_array($this->item)) : ?>
		<ul>
		<?php foreach($this->item as $i) : ?>
			<li><?php echo $i; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>