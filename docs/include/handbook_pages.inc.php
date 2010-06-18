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

$handbook_pages = array(
		'home/index.php',
               'register.php',
               'login.php',
               'password_reminder.php',
               'profile/index.php' =>   array(
                                        'profile/change_password.php',
                                        'profile/change_email.php'
                                        ),
               'user/index.php' =>      array(
                                        'user/user_create_edit.php',
                                        'user/user_password.php',
                                        'user/user_group.php',
                                        'user/user_group_create_edit.php'   
                                        ),
		'home/create_course.php' => array('home/course/course_property.php'),
		'tests/index.php' => array('tests/create_test.php',
					   'tests/edit_test.php',
					   'tests/questions.php',
					   'tests/question_db.php',
					   'tests/question_cats.php'),

		'home/editor/edit_content.php' => array('home/editor/edit_content_folder.php',
					  'home/editor/arrange_content.php',
					  'home/editor/import_export_content.php',
					  'home/editor/delete_content.php',
					  'file_manager/index.php'),
               'language/index.php' =>  array(
                                        'language/language_add_edit.php'
                                        ),
               'translation/index.php' => array(),

               'updater/index.php' => array('updater/patch_create.php')
);

?>