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

// Parse returned data from ajax php script and display messages in div
// with id "rtn-msg".
// @parameter: data   - array
//  data array structure
//  data['status'] = 'fail';  // fail or success
//  data['num_of_errors'] = 3;
//  data['num_of_feedbacks'] = 2;
//  data['num_of_warnings'] = 1;
//  data['error'][] = 'error 1';
//  data['error'][] = 'error 2';
//  data['error'][] = 'error 3';
//
//  data['feedback'][] = 'feedback 1';
//  data['feedback'][] = 'feedback 2';
//
//  data['warning'][] = 'warning 1';
function handleResponse(data)
{
	var msg='';
	
	if (data == null) return;
	
	if (typeof(data.status) == "undefined" || data.status == "success") 
	{
		jQuery('#rtn-msg').empty();
		return;
	}
	
	// data.status == "fail", handle messages
	if (typeof(data.error) != "undefined")
	{
		msg += template_error_prefix;
		for(i=0; i<data.error.length; i++)
		{
			if (data.error[i] != "") msg += "<li>"+data.error[i]+"</li>";
		}
		msg += template_suffix;
	}

	if (typeof(data.feedback) != "undefined")
	{
		msg += template_feedback_prefix;
		for(i=0; i<data.feedback.length; i++)
		{
			if (data.feedback[i] != "") msg += "<li>"+data.feedback[i]+"</li>";
		}
		msg += template_suffix;
	}
	
	if (typeof(data.warning) != "undefined")
	{
		msg += template_warning_prefix;
		for(i=0; i<data.warning.length; i++)
		{
			if (data.warning[i] != "") msg += "<li>"+data.warning[i]+"</li>";
		}
		msg += template_suffix;
	}
	
	jQuery('#rtn-msg').html(msg);
}

function addslashes(str)
{
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	str=str.replace(/\|/g,'\\|');
	
	return str;
}
// templates for message boxes
var template_error_prefix = '\
	<div id="error"> \
	<h4>The following errors occurred:</h4> \
	<ul> \
';

var template_warning_prefix = '\
	<div id="warning"> \
	<ul> \
';

var template_feedback_prefix = '\
	<div id="feedback"> \
	<ul> \
';


var template_suffix = '	</ul>\
	</div> \
';