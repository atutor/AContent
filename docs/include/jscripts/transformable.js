// ************************************************************************
// * Transformable                                                        *
// ************************************************************************
// * Copyright (c) 2009                                                   *
// * Adaptive Technology Resource Centre / University of Toronto          *
// *                                                                      *
// * This program is free software. You can redistribute it and/or        *
// * modify it under the terms of the GNU General Public License          *
// * as published by the Free Software Foundation.                        *
// ************************************************************************

var newwindow;

function poptastic(url) {
	newwindow=window.open(url,'popup','height=600,width=600,scrollbars=yes,resizable=yes');
	if (window.focus) {newwindow.focus()}
}

function getexpirydate(nodays){
	var UTCstring;
	Today = new Date();
	nomilli=Date.parse(Today);
	Today.setTime(nomilli+nodays*24*60*60*1000);
	UTCstring = Today.toUTCString();
	return UTCstring;
}

function setcookie(name,value,duration){
	cookiestring=name+"="+escape(value)+";path=/;expires="+getexpirydate(duration);
	document.cookie=cookiestring;
	if(!getcookie(name)){
		return false;
	} else {
		return true;
	}
}

function getcookie(cookiename) {
	var cookiestring=""+document.cookie;
	var index1=cookiestring.indexOf(cookiename);
	if (index1==-1 || cookiename=="") return ""; 
	var index2=cookiestring.indexOf(';',index1);
	if (index2==-1) index2=cookiestring.length; 
	return unescape(cookiestring.substring(index1+cookiename.length+1,index2));
}

function setDisplay(objId) {
	var toc = document.getElementById(objId);

	var state = getcookie(objId);
	if (document.getElementById(objId) && state && (state == 'none')) {
		toggleToc(objId);
	}
}


function setstates() {
	return;
	var objId = "side-menu";
	var state = getcookie(objId);
	if (document.getElementById(objId) && state && (state == 'none')) {
		toggleToc(objId);
	}

	var objId = "toccontent";
	var state = getcookie(objId);
	if (document.getElementById(objId) && state && (state == 'none')) {
		toggleToc(objId);
	}

}

function showTocToggle(objId, show, hide, key, selected) {
	if(document.getElementById) {
		if (key) {
			var accesskey = " accesskey='" + key + "' title='"+ show + "/" + hide + " Alt - "+ key +"'";
		} else {
			var accesskey = "";
		}

		if (selected == 'hide') {
			document.writeln('<a href="javascript:toggleToc(\'' + objId + '\')" ' + accesskey + '>' +
			'<span id="' + objId + 'showlink" style="display:none;">' + show + '</span>' +
			'<span id="' + objId + 'hidelink">' + hide + '</span>'	+ '</a>');
		} else {
			document.writeln('<a href="javascript:toggleToc(\'' + objId + '\')" ' + accesskey + '>' +
			'<span id="' + objId + 'showlink">' + show + '</span>' +
			'<span id="' + objId + 'hidelink" style="display:none;">' + hide + '</span>'	+ '</a>');
		}
	}
}

function toggleToc(objId) {
	var toc = document.getElementById(objId);
	if (toc == null) {
		return;
	}
	var showlink=document.getElementById(objId + 'showlink');
	var hidelink=document.getElementById(objId + 'hidelink');

	if (hidelink.style.display == 'none') {
		document.getElementById('contentcolumn').id="contentcolumn_shiftright";
		jQuery("[id="+objId+"]").slideDown("slow");
		hidelink.style.display='';
		showlink.style.display='none';
	} else {
		document.getElementById('contentcolumn_shiftright').id="contentcolumn";
		jQuery("[id="+objId+"]").slideUp("slow");
		hidelink.style.display='none';
		showlink.style.display='';
	}
	setcookie(objId, hidelink.style.display, 1);
}

// toggle content folder in side menu "content navigation"
function toggleFolder(cid, expand_text, collapse_text)
{
	if (jQuery("#tree_icon"+cid).attr("src") == tree_collapse_icon) {
		jQuery("#tree_icon"+cid).attr("src", tree_expand_icon);
		jQuery("#tree_icon"+cid).attr("alt", expand_text);
		jQuery("#tree_icon"+cid).attr("title", expand_text);
		setcookie("c<?php echo $this->course_id;?>_"+cid, null, 1);
	}
	else {
		jQuery("#tree_icon"+cid).attr("src", tree_collapse_icon);
		jQuery("#tree_icon"+cid).attr("alt", collapse_text);
		jQuery("#tree_icon"+cid).attr("title", collapse_text);
		setcookie("c<?php echo $this->course_id;?>_"+cid, "1", 1);
	}
	
	jQuery("#folder"+cid).slideToggle();
}

// toggle elements in side menu
function elementToggle(elem, title, base_path, show_text, hide_text)
{
	element_collapse_icon = base_path+"images/mswitch_minus.gif";
	element_expand_icon = base_path+"images/mswitch_plus.gif";
	
	if (jQuery(elem).attr("src") == element_collapse_icon) {
		jQuery(elem).attr("src", element_expand_icon);
		jQuery(elem).attr("alt", show_text + " "+ title);
		jQuery(elem).attr("title", show_text + " "+ title);
		setcookie("m_"+title, 0, 1);
	}
	else {
		jQuery(elem).attr("src", element_collapse_icon);
		jQuery(elem).attr("alt", hide_text + " "+ title);
		jQuery(elem).attr("title", hide_text + " "+ title);
		setcookie("m_"+title, null, 1);;
	}
	
	jQuery(elem).parent().next().slideToggle();
}

function printSubmenuHeader(title, base_path, show_text, hide_text)
{
	if (getcookie("m_"+title) == "0")
	{
		image = base_path + "images/mswitch_plus.gif";
		alt_text = show_text + title;
	}
	else
	{
		image = base_path+"images/mswitch_minus.gif";
		alt_text = hide_text + title;
	}
	
	document.writeln('<h4 class="box">'+
	'	<input src="'+image+'"' + 
	'	       onclick="elementToggle(this, \''+title+'\', \''+base_path+'\', \''+show_text+'\', \''+hide_text+'\'); return false;"' +
	'	       alt="'+ alt_text + '" ' +
	'	       title="'+ alt_text + '"' +
	'	       style="float:right" type="image" /> '+ title +
	'</h4>');
}

var selected;

function rowselect(obj) {
	obj.className = 'selected';
	if (selected && selected != obj.id)
		document.getElementById(selected).className = '';
	selected = obj.id;
}
function rowselectbox(obj, checked, handler) {
	var functionDemo = new Function(handler + ";");
	functionDemo();

	if (checked)
		obj.className = 'selected';
	else
		obj.className = '';
}

/**
 * Easy to define namespace.
 * Usage: namespace('Trans', 'Trans.course')
 */
namespace = function() { 
    var a=arguments, o=null, i, j, d; 
    for (i=0; i<a.length; i=i+1) { 
        d=a[i].split("."); 
        o=window; 
        for (j=0; j<d.length; j=j+1) { 
            o[d[j]]=o[d[j]] || {}; 
            o=o[d[j]]; 
        } 
    } 
    return o; 
}; 
