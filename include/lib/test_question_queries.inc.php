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

//Question for multiple choice.
define('TR_SQL_QUESTION_MULTI', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, ?, ?, 1, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							'', '', '', '', '', '', '', '', '', '', 5, 0)");

//Question for True/False
define('TR_SQL_QUESTION_TRUEFALSE', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, ?, ?, 2, ?, ?, 
							'', '', '', '', '', '', '', '', '', '', 
							?, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
							'', '', '', '', '', '', '', '', '', '', 5, 0)");


//Question for Open ended
define('TR_SQL_QUESTION_LONG', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, ?, ?, 3, ?, ?, 
							'', '', '', '', '', '', '', '', '', '', 
							0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
							'', '', '', '', '', '', '', '', '', '', ?, 0)");

//Question for Likert
define('TR_SQL_QUESTION_LIKERT', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, ?, ?, 4, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");

//Question for Ordering
define('TR_SQL_QUESTION_ORDERING', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, ?, ?, 6, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");
//Question for MultiAnswer
define('TR_SQL_QUESTION_MULTIANSWER', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, ?, ?, 7, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");
//Question for Matching Simple
define('TR_SQL_QUESTION_MATCHING', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (NULL, ?, ?, 5, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)");
//Question for Matching Graphical
define('TR_SQL_QUESTION_MATCHINGDD', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (NULL, ?, ?, 8, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)");
define('TR_SQL_TEST', "INSERT INTO ".TABLE_PREFIX."tests " .
					   "(test_id,
					 course_id,
					 title,
					 description,
					 `format`,
					 start_date,
					 end_date,
					 randomize_order,
					 num_questions,
					 instructions,
					 content_id,
					 passscore,
					 passpercent,
					 passfeedback,
					 failfeedback,
					 result_release,
					 random,
					 difficulty,
					 num_takes,
					 anonymous,
					 out_of,
					 guests,
					 display) " .
					   "VALUES 
						(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', ?, ?)");

?>
