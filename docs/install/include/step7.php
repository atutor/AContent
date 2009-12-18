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

if (!defined('TR_INCLUDE_PATH')) { exit; }

print_progress($step);

?>
<p><strong>Congratulations on your installation of Transformable <?php echo $new_version; ?><i>!</i></strong></p>

<p>For security reasons once you have confirmed that Transformable has installed correctly, you should delete the <kbd>install/</kbd> directory,
and reset the permissions on the config.inc.php file to read only.</p>

<br />

<form method="get" action="../index.php">
	<div align="center">
		<input type="submit" name="submit" value="&raquo; Go To Transformable!" class="button" />
	</div>
</form>