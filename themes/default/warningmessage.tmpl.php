<?php  
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
 
global $_base_href;
 
?>

<div id="warning" role="alert">
	<?php if (is_array($this->item)) : ?>
		<ul>
		<?php foreach($this->item as $e) : ?>
			<li><?php echo $e; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>