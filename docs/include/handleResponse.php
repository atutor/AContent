//************************************************************************/
//* Transformable                                                        */
//************************************************************************/
//* Copyright (c) 2009                                                   */
//* Adaptive Technology Resource Centre / University of Toronto          */
//*                                                                      */
//* This program is free software. You can redistribute it and/or        */
//* modify it under the terms of the GNU General Public License          */
//* as published by the Free Software Foundation.                        */
//************************************************************************/

function handleResponse(data)
{
	if (data.status != "fail") return;
	
	if (data.num_of_errors > 0)
	{
		for(i==0; i<data.error.length; i++)
		{
			alert(i);
		}
	}
}

// templates for message boxes
var template_error_prefix = '\
	<div id="error"> \
	<h4><?php echo _AT('the_follow_errors_occurred'); ?></h4> \
';

var template_warning_prefix = '\
	<div id="warning"> \
';

var template_feedback_prefix = '\
	<div id="feedback"> \
';


var template_suffix = '	</div>';