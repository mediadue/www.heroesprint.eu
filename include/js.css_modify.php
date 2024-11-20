<script language="javascript">
	
	function mostra(liv){
		if(document.getElementById(liv).style.visibility=="") document.getElementById(liv).style.visibility='hidden';
		
		if(document.getElementById(liv).style.visibility=='hidden') {
			document.getElementById(liv).style.visibility='visible';
			document.getElementById(liv).style.zIndex='9999';
		} else {
			document.getElementById(liv).style.visibility='hidden';
		}
	}
	
	function nascondi(liv){
		document.getElementById(liv).style.visibility='hidden';
	}

</script>
