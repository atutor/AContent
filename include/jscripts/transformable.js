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

var trans = trans || {};
trans.utility = trans.utility || {};
trans.course = trans.course || {};
trans.editor = trans.editor || {};
trans.newwindow;
trans.utility.selected;

(function() {
  /**
   * pops up a 600 * 600 new window
   */
  trans.utility.poptastic = function (url) {
    var newwindow=window.open(url,'popup','height=600,width=600,scrollbars=yes,resizable=yes');
    if (window.focus) {newwindow.focus()}
  };
//	ATutor.poptastic = function (url) {
//		var newwindow=window.open(url,'popup','height=600,width=600,scrollbars=yes,resizable=yes');
//		if (window.focus) {
//			newwindow.focus();
//		}
//	};

  trans.utility.getexpirydate = function (nodays){
    var UTCstring;
    Today = new Date();
    nomilli=Date.parse(Today);
    Today.setTime(nomilli+nodays*24*60*60*1000);
    UTCstring = Today.toUTCString();
    return UTCstring;
  };
  
  /**
   * set cookie value and expiry
   */
  trans.utility.setcookie = function (name,value,duration){
    cookiestring=name+"="+escape(value)+";path=/;expires="+trans.utility.getexpirydate(duration);
    document.cookie=cookiestring;
    if(!trans.utility.getcookie(name)){
      return false;
    } else {
      return true;
    }
  };
  
  /**
   * get cookie value
   */
  trans.utility.getcookie = function (cookiename) {
    var cookiestring=""+document.cookie;
    var index1=cookiestring.indexOf(cookiename);
    if (index1==-1 || cookiename=="") return ""; 
    var index2=cookiestring.indexOf(';',index1);
    if (index2==-1) index2=cookiestring.length; 
    return unescape(cookiestring.substring(index1+cookiename.length+1,index2));
  };
  
  trans.utility.setDisplay = function (objId) {
    var toc = document.getElementById(objId);
  
    var state = trans.utility.getcookie(objId);
    if (document.getElementById(objId) && state && (state == 'none')) {
      trans.utility.toggleToc(objId);
    }
  };
/*  
  trans.utility.setstates = function () {
    return;
    var objId = "side-menu";
    var state = trans.utility.getcookie(objId);
    if (document.getElementById(objId) && state && (state == 'none')) {
      trans.utility.toggleToc(objId);
    }
  
    var objId = "toccontent";
    var state = trans.utility.getcookie(objId);
    if (document.getElementById(objId) && state && (state == 'none')) {
      trans.utility.toggleToc(objId);
    }
  };
*/  
  trans.utility.showTocToggle = function (objId, show, hide, key, selected) {
    if(document.getElementById) {
      if (key) {
        var accesskey = " accesskey='" + key + "' title='"+ show + "/" + hide + " Alt - "+ key +"'";
      } else {
        var accesskey = "";
      }
  
      if (selected == 'hide') {
        document.writeln('<a href="javascript:trans.utility.toggleToc(\'' + objId + '\')" ' + accesskey + '>' +
        '<span id="' + objId + 'showlink" style="display:none;">' + show + '</span>' +
        '<span id="' + objId + 'hidelink">' + hide + '</span>'  + '</a>');
      } else {
        document.writeln('<a href="javascript:trans.utility.toggleToc(\'' + objId + '\')" ' + accesskey + '>' +
        '<span id="' + objId + 'showlink">' + show + '</span>' +
        '<span id="' + objId + 'hidelink" style="display:none;">' + hide + '</span>'  + '</a>');
      }
    }
  };
  
  trans.utility.toggleToc = function (objId) {
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
    trans.utility.setcookie(objId, hidelink.style.display, 1);
  };
  
  // toggle a div, for example "create user group" => "add privileges" section
  trans.utility.toggleDiv = function (objId) {
    var toc = document.getElementById(objId);
    if (toc == null) return;
  
    if (toc.style.display == 'none')
    {
      toc.style.display = '';
      document.getElementById("toggle_image").src = "images/arrow-open.png";
      document.getElementById("toggle_image").alt = "Collapse";
      document.getElementById("toggle_image").title = "Collapse";
    }
    else
    {
      toc.style.display = 'none';
      document.getElementById("toggle_image").src = "images/arrow-closed.png";
      document.getElementById("toggle_image").alt = "Expand";
      document.getElementById("toggle_image").title = "Expand";
    }
  };
  
  
  //catia
  //toogle structure outline  (in create course)
  trans.utility.toggleOutline = function (struct_name, expand_text, collapse_text)
  {
	  if (jQuery("#a_outline_"+struct_name).attr("title") == "outline_collapsed") {
		  jQuery("#a_outline_"+struct_name).attr("title", "outline_expanded");
		  jQuery("#a_outline_"+struct_name).text(expand_text);
		  
	    }
	    else {
	      jQuery("#a_outline_"+struct_name).attr("title", "outline_collapsed");
	      jQuery("#a_outline_"+struct_name).text(collapse_text);
	    }
	    
	    jQuery("#div_outline_"+struct_name).slideToggle();
	  
  };
  
  // toggle content folder in side menu "content navigation"
  trans.utility.toggleFolder = function (cid, expand_text, collapse_text, course_id)
  {
	  
    if (jQuery("#tree_icon"+cid).attr("src") == tree_collapse_icon) {
      jQuery("#tree_icon"+cid).attr("src", tree_expand_icon);
      jQuery("#tree_icon"+cid).attr("alt", expand_text);
      jQuery("#tree_icon"+cid).attr("title", expand_text);
      trans.utility.setcookie("t.c"+course_id+"_"+cid, null, 1);
    }
    else {
      jQuery("#tree_icon"+cid).attr("src", tree_collapse_icon);
      jQuery("#tree_icon"+cid).attr("alt", collapse_text);
      jQuery("#tree_icon"+cid).attr("title", collapse_text);
      trans.utility.setcookie("t.c"+course_id+"_"+cid, "1", 1);
    }
    
    jQuery("#folder"+cid).slideToggle();
  };
  
  trans.utility.toggleFolderStruct = function (count, pageid, expand_text, collapse_text, tree_collapse_icon, tree_expand_icon)
  {
	 
	 
	  if (jQuery("#tree_icon_"+pageid+count).attr("src") == tree_collapse_icon) {
		  
		  jQuery("#tree_icon_"+pageid+count).attr("src", tree_expand_icon);
	      jQuery("#tree_icon_"+pageid+count).attr("alt", expand_text);
	      jQuery("#tree_icon_"+pageid+count).attr("title", expand_text);
	  } else {
		  
		  jQuery("#tree_icon_"+pageid+count).attr("src", tree_collapse_icon);
	      jQuery("#tree_icon_"+pageid+count).attr("alt", collapse_text);
	      jQuery("#tree_icon_"+pageid+count).attr("title", collapse_text);
	  }
	  
	  jQuery("#folder_"+pageid+count).slideToggle();
  };
  
  // toggle elements in side menu
  trans.utility.elementToggle = function (elem, title, compact_title, base_path, show_text, hide_text)
  {
    element_collapse_icon = base_path+"images/mswitch_minus.png";
    element_expand_icon = base_path+"images/mswitch_plus.png";
    
    if (jQuery(elem).attr("src") == element_collapse_icon) {
      jQuery(elem).attr("src", element_expand_icon);
      jQuery(elem).attr("alt", show_text + " "+ title);
      jQuery(elem).attr("title", show_text + " "+ title);
      trans.utility.setcookie("m_"+compact_title, 0, 1);
    }
    else {
      jQuery(elem).attr("src", element_collapse_icon);
      jQuery(elem).attr("alt", hide_text + " "+ title);
      jQuery(elem).attr("title", hide_text + " "+ title);
      trans.utility.setcookie("m_"+compact_title, 1, 1);;
    }
    
    jQuery(elem).parent().next().slideToggle();
  };
  
  trans.utility.printSubmenuHeader = function (title, compact_title, base_path, show_text, hide_text, default_value)
  {
	cookie_value = trans.utility.getcookie("m_"+compact_title);
	
	if (cookie_value == "0" || (cookie_value == "" && default_value == "hide"))
    {
      image = base_path + "images/mswitch_plus.png";
      alt_text = show_text + " " + title;
    }
    else
    {
      image = base_path+"images/mswitch_minus.png";
      alt_text = hide_text + " " + title;
    }
    
    document.writeln('<h4 class="box">'+
    '  <input src="'+image+'"' + 
    '         onclick="trans.utility.elementToggle(this, \''+title+'\', \''+compact_title+'\', \''+base_path+'\', \''+show_text+'\', \''+hide_text+'\'); return false;"' +
    '         alt="'+ alt_text + '" ' +
    '         title="'+ alt_text + '"' +
    '         style="float:right" type="image" class="toggle_switch"/> '+ title +
    '</h4>');
  };
  
  trans.utility.rowselect = function (obj) {
    obj.className = 'selected';
    if (trans.utility.selected && trans.utility.selected != obj.id)
      document.getElementById(trans.utility.selected).className = '';
    trans.utility.selected = obj.id;
  };
  
  trans.utility.rowselectbox = function (obj, checked, handler) {
    var functionDemo = new Function(handler + ";");
    functionDemo();
  
    if (checked)
      obj.className = 'selected';
    else
      obj.className = '';
  };

})();
