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

define('TR_INCLUDE_PATH', '../../include/');

require(TR_INCLUDE_PATH.'vitals.inc.php');


require_once(TR_INCLUDE_PATH.'classes/DAO/DAO.class.php');
require_once(TR_INCLUDE_PATH."classes/DAO/ForumsDAO.class.php");
require_once(TR_INCLUDE_PATH.'classes/DAO/ForumsCoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentForumsAssocDAO.class.php');



global $_course_id, $_content_id, $db;

$forums_courses_dao = new ForumsCoursesDAO();
$cont_for_ass_dao = new ContentForumsAssocDAO();
$forums_dao = new ForumsDAO();

Utility::authenticate(TR_PRIV_ISAUTHOR);

$cid = $_content_id;
$crid = $_course_id;


$popup = intval($_GET['popup']);

if ($cid == 0) {
	require(TR_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('NO_PAGE_CONTENT');
	//require (TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}




require(TR_INCLUDE_PATH.'header.inc.php');



?>


<!-- echo TR_BASE_HREF;?>home/editor/add_forum.php?popup=1" -->
<!-- echo $_SERVER['PHP_SELF'];?>?popup=1" -->
<form action="<?php echo TR_BASE_HREF;?>home/editor/add_forum.php?popup=1" method="post" name="create">
<div class="input-form">
	
		Create a link at new forum in this content
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
			<input type="text" name="title" size="40" id="title" />
		</div>
		<div class="row">
			<label for="body"><?php echo _AT('description'); ?></label><br />
			<textarea type="text" name="body" cols="45" rows="2" id="body" wrap="wrap"></textarea>
		</div>
		
		<input type="hidden" value="<?php echo $cid;?>" name="cid" />
		<input type="hidden" value="<?php echo $crid;?>" name="crid" />
		<input class="button" type="submit" value="Create" name="create_forum" />
		
		
	
</div>
</form>

<br/>
<form action="<?php echo TR_BASE_HREF;?>home/editor/add_forum.php?popup=1" method="post" name="save">
<div class="input-form">
Or choose one of these forum associated with this course
<br/>
<br/>
<table class="data static" border="0" style="width: 90%" rules="groups" summary="">
	<thead>
		
			<tr>
				<th scope="col"> </th>
				<th scope="col">Forum name</th>
				<th scope="col">Description</th>
				<th scope="col">Date</th>
			</tr>
	</thead>
	<tbody>
	
	<?php 
		
		$rows_forums = $forums_courses_dao->getByCourse($crid);
//debug($cid);
		$rows = $cont_for_ass_dao->getByContent($cid);
//debug($rows);		
		//debug($rows_forums);
		
		foreach ($rows_forums as $row_forum) { ?>
			<tr>
				<td>
					<?php 
						
						foreach ($rows as $row) {
							
							if($row_forum['forum_id'] == $row['forum_id'])  {
								$checked = 'checked';
								break;
							} else {
                           		$checked='';
                        	}
						}	
						
						$forum_info =  $forums_dao->get($row_forum['forum_id']);

						
					?>
					
					<input type="checkbox" <?php echo $checked;?> name="check[]" value="<?php echo $row_forum['forum_id']?>"/>
				</td>
				<td>
					<?php echo $forum_info['title']; ?>
				</td>
				<td>
					<?php echo  $forum_info['description'];?>
				</td>
				<td>
					<?php echo $forum_info['created_date'];?>
				</td>
				
			</tr>
		
		<?php 
		}
		?>
	
	</tbody>
	<tfoot>
		
		<tr>
			<td colspan="5">
				<input type="hidden" value="<?php echo $cid;?>" name="cid" />
				<input type="hidden" value="<?php echo $crid;?>" name="crid" />
				<input type="submit" value="Save" name="save">
				
			</td>
		</tr>
		
	</tfoot>

</table>
</div>
</form>