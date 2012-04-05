<script type="text/javascript">

document.getElementById('subnavlistcontainer').style.display = 'none';


function mioAlert(name) {
	
	alert(name);
}


function openPrev (pageName) {

	document.getElementById('prev-'+pageName).style.display = 'block';
	document.getElementById('prev-inp-'+pageName).style.display = 'none';
	document.getElementById('hide-prev-inp-'+pageName).style.display = 'inline';
	
	
}

function closePrev (pageName) {
	document.getElementById('prev-'+pageName).style.display = 'none';
	document.getElementById('prev-inp-'+pageName).style.display = 'inline';
	document.getElementById('hide-prev-inp-'+pageName).style.display = 'none';
}






//function showStructPreview(pageName) {
//	
//	
//	if (window.XMLHttpRequest)
//	  {// code for IE7+, Firefox, Chrome, Opera, Safari
//	  xmlhttp=new XMLHttpRequest();
//	  }
//	else
//	  {// code for IE6, IE5
//	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
//	  }
//	xmlhttp.onreadystatechange=function()
//	  {
//	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
//	    {
//	    document.getElementById("prev-"+pageName).innerHTML=xmlhttp.responseText;
//	    }
//	  }
//	xmlhttp.open("GET","home/structs/preview.php?prev="+pageName,true);
//	xmlhttp.send();
//	}
	


</script>