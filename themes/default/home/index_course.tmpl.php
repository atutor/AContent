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

// determine the from url is search.php or index.php
global $_base_path;
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');

// This template is called by home/index.php and home/search.php
// Find out the caller URL and direct the page back to the caller 
// after adding/removing the course from "My Courses"
list($caller_url, $url_param) = Utility::getRefererURLAndParams();

// Get search text for the page
$search_text = $this->search_text;

$keywords = isset($search_text) ? explode(' ', $search_text) : NULL;
$session_user_id = $_SESSION['user_id'];
?>


<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo $this->title; ?></legend>

<?php 
if (is_array($this->courses)) {

    // Get theme for the page
    $theme = $this->theme;
    
    echo '<div class="results">';

    if (isset($session_user_id)) {
        $userCoursesDAO = new UserCoursesDAO();
    }

    $num_results = count($this->courses);
    print_paginator($this->curr_page_num, $num_results, $url_param, RESULTS_PER_PAGE, 5, '1');
    // if the requested page number exceeds the max number of pages, set the current page to the last page
    $num_pages = ceil($num_results / RESULTS_PER_PAGE);
    $this->curr_page_num = ($this->curr_page_num > $num_pages) ? $num_pages : $this->curr_page_num;

    $start_num = ($this->curr_page_num - 1) * RESULTS_PER_PAGE;
    $end_num = min($this->curr_page_num * RESULTS_PER_PAGE, $num_results);

    echo '<ol class="remove-margin-left">';
    
    // Markup for the "Lessons 1-6 of 6"
    echo '<li class="course" style="font-weight:bold"><div>';
    echo sprintf('%s %d-%d %s %d %s', 
                        strstr($caller_script, 'search.php') ? _AT('results') : _AT('lessons'),
                        ($start_num+1), $end_num, _AT('of'), $num_results,
                        $search_text <> '' ? sprintf('%s "<em>%s</em>"', _AT('for'), $search_text) : ''
    );
    
    // My lessons marker for articles which belong to the currently logged in author
    if($session_user_id) {
        echo sprintf('<span style="float: right"><img src="%s" alt="%s" title="%s" class="shortcut_icon"/>&nbsp;&nbsp;&nbsp;%s</span>', 
                        Utility::getThemeImagePath('my_own_course.gif'), _AT('my_authoring_course'), _AT('my_authoring_course'), _AT('authoring_img_info')
        );
    }
    echo '</div></li>';
    // end of markup

    // Max length for the course description
    $len_description = 330;
    
    for ($i = $start_num; $i < $end_num; $i++) {
        // only display the first 200 character of course description
        $row = $this->courses[$i];
        $course_id = $row['course_id'];
        $course_description = $row['description'];
        
        // find whether the current user is the author of this course
        $user_role = isset($session_user_id) ? $userCoursesDAO->get($session_user_id, $course_id) : NULL;
        $user_role = isset($user_role) ? $user_role['role'] : NULL;
        
        if (strlen($course_description) > $len_description) {
            $description = Utility::highlightKeywords(substr($course_description, 0, $len_description), $keywords).' ...';
        } else {
            $description = Utility::highlightKeywords($course_description, $keywords);
        }

        echo '<li class="course">';
    
            // An icon on the left of the topic name to indicate if course belongs to the current user
            if ($user_role == TR_USERROLE_AUTHOR) {
                echo sprintf('<img src="%s" alt="%s" title="%s" class="shortcut_icon"/>', 
                                Utility::getThemeImagePath('my_own_course.gif'), _AT('my_authoring_course'), _AT('my_authoring_course')
                );
            } else {
                echo sprintf('<img src="%s" alt="%s" title="%s" class="shortcut_icon"/>', 
                                Utility::getThemeImagePath('others_course.gif'), _AT('others_course'), _AT('others_course')
                );
            }
            
            // Course name
            echo sprintf('<a href="%shome/course/index.php?_course_id=%d">%s</a>', 
                                TR_BASE_HREF, $course_id, Utility::highlightKeywords($row['title'], $keywords)
            );
    
            // -- set of icons set For Adding removal of the course from My Lessons. Book icon
            if ($user_role == TR_USERROLE_VIEWER) {
                echo sprintf('<a href="%shome/%saction=remove%scid=%d">', 
                                TR_BASE_HREF, $caller_url, SEP, $course_id
                );
                echo sprintf('<img src="%s" alt="%s" title="%s" border="0" class="shortcut_icon"/>', 
                                Utility::getThemeImagePath('bookmark_remove.png'), htmlspecialchars(_AT('remove_from_list')), htmlspecialchars(_AT('remove_from_list'))
                );
                echo '</a>';
            }
            if ($user_role == NULL && $session_user_id > 0) {
                echo sprintf('<a href="%shome/%saction=add%scid=%d">', 
                                TR_BASE_HREF, $caller_url, SEP, $course_id
                );
                echo sprintf('<img src="%s" alt="%s" title="%s" border="0" class="shortcut_icon" />', 
                                Utility::getThemeImagePath('bookmark_add.png'), htmlspecialchars(_AT('add_into_list')), htmlspecialchars(_AT('add_into_list'))
                );
                echo '</a>';
            }
            // end of set
            
            // Yellow icon for Download Content Package
            echo sprintf('<a href="%shome/ims/ims_export.php?course_id=%d">', 
                                TR_BASE_HREF, $course_id
            );
            echo sprintf('<img src="%s" alt="%s" title="%s" border="0" class="shortcut_icon"/>', 
                                Utility::getThemeImagePath('export.png'), _AT('download_content_package'), _AT('download_content_package')
            );
            echo '</a>';
            
            // A DB icon for Download Common Cartridge
            echo sprintf('<a href="%shome/imscc/ims_export.php?course_id=%d">', 
                                TR_BASE_HREF, $course_id
            );
            echo sprintf('<img src="%s" alt="%s" title="%s" border="0" class="shortcut_icon"/>', 
                                Utility::getThemeImagePath('export_cc.png'), _AT('download_common_cartridge'), _AT('download_common_cartridge')
            );
            echo '</a>';
            
            // If a user logged in
            global $_current_user;
            if(isset($session_user_id)) {
                // If user is an Admin or an Author of the course then display a delete icon
                if($_current_user->isAdmin($session_user_id) == 1 || $user_role == TR_USERROLE_AUTHOR) {
                    echo sprintf('<a href="%shome/course/del_course.php?_course_id=%d">', 
                                TR_BASE_HREF, $course_id
                    );
                    echo sprintf('<img src="%s" title="%s" alt="%s" border="0" class="shortcut_icon"/>', 
                                Utility::getThemeImagePath('delete.gif'), _AT('del_course'), _AT('del_course')
                    );
                    echo "</a>";
                }
            }
            
            // Description for the course
            echo sprintf('<div>%s</div>', $description);
        echo '</li>';
    } // End of the course row
    echo '</ol>';
    
    print_paginator($this->curr_page_num, $num_results, $url_param, RESULTS_PER_PAGE, 5, '2');
    echo '</div>'; // Closing for div=results
} else {
    echo _AT("none_found");
}
?>
</fieldset>
</div>
