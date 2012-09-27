<script type="text/javascript">

document.getElementById('subnavlistcontainer').style.display = 'none';



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



</script>