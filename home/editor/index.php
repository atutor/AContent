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

/** Commented by Cindy Li on Apr 27, 2010
 * Modified from ATutor home/editor/*, SVN revision 9807
 */

define('TR_INCLUDE_PATH', '../../include/');
define('TR_HTMLPurifier_PATH', '../../protection/xss/htmlpurifier/library/');
require (TR_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = 'Blank Page';

require (TR_INCLUDE_PATH.'header.inc.php');

?>
blank page
<?php
require (TR_INCLUDE_PATH.'footer.inc.php');
?>
