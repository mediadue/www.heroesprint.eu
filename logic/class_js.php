<?php

Class Js {

	// ******************************************************************************************
	function adminMenu() {
		?>
		<script type="text/JavaScript">
		<!--
		function initMenu() {
			var li_ = document.getElementsByTagName('li');
			for (var i=0;li = li_[i];i++)
				if(li.className && li.className=="more") {
					for (j=0; j<li.childNodes.length; j++) 
						if (li.childNodes[j].nodeName.toLowerCase()=="ul") li.subMenu = li.childNodes[j];
					li.subMenu.className = "subMenu-off";
					li.onmouseover = li.onactivate = li.onfocus = function() { this.subMenu.className = "subMenu-on" };
					li.onmouseout = li.ondeactivate = li.onblur = function() { this.subMenu.className = "subMenu-off" };
				}
		}
		window.onload = function(e) {
		if(document.getElementsByTagName('body')) initMenu() ;
		}
		
		/*
		(c) 2004 Gianluca Troiani < g.troiani@constile.org > some rights reserved.
		This code is licensed under Creative Commons Attribution-ShareAlike License < http://creativecommons.org/licenses/by-sa/2.0/ >
		*/
		//-->
		</script>
		<?php
	}
  
  function adminMenuML() {
    $objUtility = new Utility;
    ?>
  	<script type="text/javascript"> 
    <!--
    $(document).ready(function() {
    	//use another arrow - image from http://www.famfamfam.com/lab/icons/silk/
    	$('#stylishdiv').css("visibility","visible");
      $('#stylishmenu').clickMenu({arrowSrc:'<?php echo $objUtility->getPathBackofficeResources() ?>arrow_right.png', onClick:function(){
    		var a = $(this).find('>a');
    		if ( a.length ) {
          var op_url=$(a).attr("href");
          var tab=$(a).attr("rsTable");
          var where=$(a).attr("rsWhere");
          var ordinamento=$(a).attr("rsOrd");
          var txt=$(a).attr("rsTxt");
          var tit=$(a).attr("rsTit");
          var rsTableParent=$(a).attr("rsTableParent");
          var rsTableParentId=$(a).attr("rsTableParentId");
          var rsInsert=$(a).attr("rsInsert");
          var rsInsertId=$(a).attr("rsInsertId");
          var rsStrutture=$(a).attr("rsStrutture");
          
          
          if(tab!="" && tab!=undefined) {
            var winOptions = {
              'template':  op_url,
              'str':     txt,
              'table':   tab,
              'tableParent':   rsTableParent,
              'tableParentId':   rsTableParentId,
              'where':   where,
              'order':   ordinamento,
              'title':   tit,
              'insert': rsInsert,
              'insertId': rsInsertId 
            };
          }else if(rsStrutture!="" && (rsStrutture!=undefined)){
            var winOptions={
              'strutture': rsStrutture,
              'width': 300,
              'height': 600,
              'maxButton': true,
              'title': rsStrutture
            };
          }
          
          if(winOptions!=undefined) {
            $("div.rsTable2-dialog-nm").remove();
            win=new rsWindows(winOptions);
            win.open();
          }else{
            if(typeof $(a).attr('href')!='undefined') {
              window.location = $(a).attr('href');
              $('#stylishmenu').trigger('closemenu');
            }
          }
    		}
    		return false;
    	}}); 
    });
    -->
    </script>
	  <?
  }
  
	// ******************************************************************************************
	function checkField($name, $type, $desc, $focus) {
		switch ($type) {
			case "text";
			case "password";
			case "textarea";
			case "hidden":
				?>
	      		if (theform.<?php echo $name ?>.value == '') {
		      		alert('Inserire un valore per il campo "<?php echo $desc ?>"');
		      		theform.<?php echo $focus ?>.focus();
		      		return false;
	      		}
				<?php
				break;
			case "select":
				?>
            if (Math.floor(theform.<?php echo $name ?>.options[theform.<?php echo $name ?>.selectedIndex].value) == 0) {
		      		alert('Scegliere un valore per il campo "<?php echo $desc ?>"');
		      		theform.<?php echo $focus ?>.focus();
		      		return false;
	      		}
				<?php
				break;
			case "radio":
				?>
	      		almenouno = false;;
	      		for (i=0; i<theform.<?php echo $name ?>.length; i++) {
		      		if (theform.<?php echo $name ?>[i].checked) {
			      		almenouno = true;
		      		}
	      		}
	      		if (!almenouno) {
		      		alert('Scegliere un valore per il campo "<?php echo $desc ?>"');
		      		theform.<?php echo $focus ?>[0].focus();
		      		return false;
	      		}
				<?php
				break;
			case "data":
				?>
	      		if (!isValidDefDate(theform.<?php echo $name ?>.value)) {
		      		alert('Inserire una data valida (aaaa-mm-gg) per il campo "<?php echo $desc ?>"');
		      		theform.<?php echo $name ?>.focus();
		      		return false;
	      		}
				<?php
				break;
				case "checkbox":
				?>
	      		if (!theform.<?php echo $name ?>.checked) {
              alert('Spunta la casella "<?php echo $desc ?>"');
		      		theform.<?php echo $focus ?>.focus();
		      		return false;
		      	}
				<?php
				break;
			case "date":
        ?>
	      		var tmpdate = theform.anno<?php echo $name ?>.value+"-"+theform.mese<?php echo $name ?>.value+"-"+theform.giorno<?php echo $name ?>.value;
            if (!isValidDefDate(tmpdate)) {
		      		alert('Inserire una data valida (aaaa-mm-gg) per il campo "<?php echo $desc ?>"');
		      		theform.giorno<?php echo $name ?>.focus();
		      		return false;
	      		}
				<?php
				break;
				case "checkbox":
				?>
	      		if (!theform.<?php echo $name ?>.checked) {
              alert('Spunta la casella "<?php echo $desc ?>"');
		      		theform.<?php echo $focus ?>.focus();
		      		return false;
		      	}
				<?php
				break;
			case "img":
				?>
				var imgname = theform.<?php echo $name ?>.value;
				if (imgname.length <= 0) {
					ext = '???';
				} else {
					ppos = imgname.lastIndexOf('.');
					ext = ((ppos != -1) ? imgname.substring(ppos + 1) : 'no');
					ext = ext.toLowerCase();
				}
				if (allowed.indexOf(ext) == -1) {
					alert ('Inserire un file con la giusta estensione');
		      		theform.<?php echo $focus ?>.focus();
					return false;
				}
				<?php
				break;
	    }
	}

	// ******************************************************************************************
	function elementsHelper() {
		?>
		<script language="JavaScript" type="text/javascript">
		<!--
		function elementGetCrossBrowser(elementId) {
			var ns4 = document.layers;
			var ns6 = document.getElementById && !document.all;
			var ie4 = document.all;
			var elementCross;
			if (ns4) elementCross = eval("document." + elementId);
			else if(ns6) elementCross = document.getElementById(elementId);
			else if(ie4) elementCross = document.all(elementId);
			return elementCross;
		}
		function elementToggle(elementId) {
			var elementCross = elementGetCrossBrowser(elementId)
			if (elementCross.style.display == 'none') {
				elementShow(elementId);
			} else {
				elementHide(elementId);
			}
		}
		function elementShow(elementId) {
			var elementCross = elementGetCrossBrowser(elementId)
			elementCross.style.display = '';
		}
		function elementHide(elementId) {
			var elementCross = elementGetCrossBrowser(elementId)
			elementCross.style.display = 'none';
		}
		//-->
		</script>
		<?php
	}

	// ******************************************************************************************
	function dateHelper() {
		?>
		<script language="JavaScript" type="text/javascript">
		<!--
		function isValidSlashDate(date) {
			return isValidCustomDate(date, "/");
		}
		function isValidDefDate(date) {
      return isValidCustomDate(date, "-");
		}
		function isValidCustomDate(date, separator) {
      if(date=="0000-00-00") return false; 
      var dateArray = date.split(separator);
			return isValidDate(dateArray[2], dateArray[1], dateArray[0]);
		}
		function isValidDate (g, m, a) {
      if(a=="0000" || m=="00" || g=="00") return false;
      return true;
		}
		function getFromDate(date, tipo) {
			var dateArray = date.split("-");
			if (tipo=="g") {
				return dateArray[0]
			}
			if (tipo=="m") {
				return dateArray[1]
			}
			if (tipo=="a") {
				return dateArray[2]
			}
		}
		//-->
		</script>
		<?php
	}

	// ******************************************************************************************
	function cboHelper() {
		?>
		<script language="JavaScript" type="text/javascript">
		<!--
		function cboClearAll (cbo) {
		  lboClearAll(cbo);
		}
		function cboAdd (cbo, value, text) {
		  lboAdd (cbo, value, text);
		}
		function lboAddCbo(lbo, cbo) {
		  if ((cbo.selectedIndex) >=0) {
		    var value = cbo.value;
		    var text = cbo.options[cbo.selectedIndex].text;
		    lboAdd (lbo, value, text);
		  }
		}
		function lboAdd (lbo, value, text) {
		  var count = lbo.options.length;
		  //controllo che l'elemento non sia stato gia' inserito
		  var itemFound = false;
		  for (var i=0; i<count; i++) {
		    if (lbo.options[i].value == value) {
		      itemFound = true;
		      break;
		    }
		  }
		  if (itemFound) {
		    lbo.selectedIndex = i;
		  } else {
		    //inserisco l'elemento in modo ordinato
		    lbo.options[count] = new Option(text, value);
		    var i = count;
		    while ((i>=1) && (text < lbo.options[i-1].text)) {
		      tValue = lbo.options[i].value;
		      tText = lbo.options[i].text;
		      lbo.options[i].value = lbo.options[i-1].value;
		      lbo.options[i].text = lbo.options[i-1].text;
		      lbo.options[i-1].value = tValue;
		      lbo.options[i-1].text = tText;
		      i--;
		    }
		  lbo.selectedIndex = i;
		  }
		}
		function lboClearSelected(lbo) {
		  var itemSelected = lbo.selectedIndex;
		  if (itemSelected >= 0) {
		    lbo.options[itemSelected] = null;
		  }
		}
		function lboClearAll(lbo) {
		  var count = lbo.options.length;
		  for (var i=0; i<count; i++) {
		    lbo.options[0] = null;
		  }
		}
		function lboToString(lbo) {
		  var count = lbo.options.length;
		  var tempId = "";
		  for (var i=0; i<count; i++) {
		    tempId += (lbo.options[i].value + "|");
		  }
		  tempId = tempId.substring(0, tempId.length-1);
		  return tempId;
		}
		//-->
		</script>
		<?php
	}

}
?><?php //#rs-enc-module123;# ?>