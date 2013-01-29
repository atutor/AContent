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
require_once(TR_INCLUDE_PATH.'classes/DAO/LangCodesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');

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

function createShortCutIcon($file_name, $title) {
    $title = htmlspecialchars(_AT($title));
    return sprintf('<img src="%s" alt="%s" title="%s" class="shortcut_icon"/>', Utility::getThemeImagePath($file_name), $title, $title);
}

function addDescriptionLine($value, $message, $breakAfter = FALSE, $breakAfterDescription = FALSE) {
    if ($value == '') {
        return '';
    }
    
    $htmlBreak1 = ($breakAfterDescription) ? '<br />' : '';
    $htmlBreak2 = ($breakAfter) ? '<br />' : '';
    
    // Some different variations of markup for specific fields
    if ($message == 'author') {
        return sprintf('<div><strong>%s/%s:</strong>%s %s</div>%s', _AT('author'), _AT('course_owner'), $htmlBreak1, $value, $htmlBreak2);
    }
    
    return sprintf('<div><strong>%s:</strong>%s %s</div>%s', _AT($message), $htmlBreak1, $value, $htmlBreak2);
}

if (is_array($this->courses)) {
    global $_current_user;
    $userCoursesDAO = new UserCoursesDAO();
    $langCodesDAO = new LangCodesDAO();
    $usersDAO = new UsersDAO();

    // Get theme for the page
    $theme = $this->theme;
    $htmlSeparator = '&nbsp;&nbsp;&nbsp;';
    
    echo '<div class="results">';

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
        if ($session_user_id) {
            echo sprintf('<span style="float: right">%s%s%s</span>', createShortCutIcon('my_own_course.gif', 'my_authoring_course'), $htmlSeparator, _AT('authoring_img_info'));
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
        $course_title = $row['title'];
        $created_date = $row['created_date'];
        $modified_date = $row['modified_date'];
        $primary_language = $row['primary_language'];
        $access = $row['access'];
        $author = $row['user_id'];
        $course_link = sprintf('home/course/index.php?_course_id=%d', $course_id);
        
        $created_date = ($created_date != NULL && $created_date != '0000-00-00 00:00:00') ? $created_date : '';
        $modified_date = ($modified_date != NULL && $modified_date != '0000-00-00 00:00:00') ? $modified_date : '';
        $primary_language = ($primary_language != NULL) ? $primary_language : '';
        $course_description = ($course_description != NULL) ? $course_description : '';
        $access = ($access != NULL) ? $access : '';
        
        $primary_language = $langCodesDAO->getLanguageByCode($primary_language);
        $author = $usersDAO->getUserByID($author);
        $author = sprintf('%s %s', $author['first_name'], $author['last_name']);
        
        // find whether the current user is the author of this course
        $user_role = isset($session_user_id) ? $userCoursesDAO->get($session_user_id, $course_id) : NULL;
        $user_role = isset($user_role) ? $user_role['role'] : NULL;
        
        $course_description = Utility::highlightKeywords($course_description, $keywords);

        echo '<li class="course area">';
            echo '<div class="topRow">';
                // An icon on the left of the topic name to indicate if course belongs to the current user
                if ($user_role == TR_USERROLE_AUTHOR) {
                    $file_name = 'my_own_course.gif'; $title = 'my_authoring_course';
                } else {
                    $file_name = 'others_course.png'; $title = 'others_course';
                }
                echo createShortCutIcon($file_name, $title);
                
                // Course name
                echo sprintf('%s<a href="%s" class="courseName">%s</a>', $htmlSeparator, $course_link, Utility::highlightKeywords($course_title, $keywords));
                
                echo sprintf('%s<a href="#" class="collapsible_link">', $htmlSeparator);
                    echo sprintf('<span class="showLabel">%s...</span>', _AT('show'));
                    echo sprintf('<span class="hideLabel" style="display: none">%s...</span>', _AT('hide'));
                    echo sprintf('<span style="visibility:hidden">%s</span>', _AT('more_course_information', $course_title));
                echo sprintf('</a>');
            echo '</div>';
            echo '<div class="collapsible" style="display: none;">';
                echo '&nbsp;<div class="courseIcons">';
                    // -- set of icons set For Adding removal of the course from My Lessons. Book icon
                    $showIcon = false;
                    if ($user_role == TR_USERROLE_VIEWER) {
                        $showIcon = true; $action = 'remove'; $file_name = 'bookmark_remove.png'; $title = 'remove_from_list';
                    }
                    if ($user_role == NULL && $session_user_id > 0) {
                        $showIcon = true; $action = 'add'; $file_name = 'bookmark_add.png'; $title = 'add_into_list';
                    }
                    if ($showIcon) {
                        echo sprintf('%s<a href="home/%saction=%s%scid=%d">%s</a>', $htmlSeparator, $caller_url, $action, SEP, $course_id, createShortCutIcon($file_name, $title));
                    }
                    // end of set
                    
                    // Yellow icon for Download Content Package
                    echo sprintf('%s<a href="home/ims/ims_export.php?course_id=%d">%s</a>', $htmlSeparator, $course_id, createShortCutIcon('export.png', 'download_content_package'));
                    
                    // A DB icon for Download Common Cartridge
                    echo sprintf('%s<a href="home/imscc/ims_export.php?course_id=%d">%s</a>', $htmlSeparator, $course_id, createShortCutIcon('export_cc.png', 'download_common_cartridge'));
                    
                    // Delete button markup
                    if(isset($session_user_id)) {
                        // If user is an Admin or an Author of the course then display a delete icon
                        if($_current_user->isAdmin($session_user_id) == 1 || $user_role == TR_USERROLE_AUTHOR) {
                            echo sprintf('%s<a href="home/course/del_course.php?_course_id=%d">%s</a>', $htmlSeparator, $course_id, createShortCutIcon('delete.gif', 'del_course'));
                        }
                    }
                echo '</div>';
                // Extra information about the course goes here
                echo '<div class="description">';
                    echo addDescriptionLine($author, 'author', TRUE, FALSE);
                    echo addDescriptionLine($created_date, 'date_created');
                    echo addDescriptionLine($modified_date, 'last_modified');
                    echo addDescriptionLine($primary_language, 'primary_language');
                    echo addDescriptionLine($access, 'access', TRUE);
                    echo addDescriptionLine($course_description, 'description', TRUE, TRUE);
                    
                    echo sprintf('<a href="%s" class="courseName">%s</a>', $course_link, _AT('goto_course', $course_title));
                    echo '<br />&nbsp;';
                echo '</div>';
                // End of extra information
            echo '</div>';
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
