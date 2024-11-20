var tmpid="";

jQuery.fn.extend
({
    removeCss: function(cssName) {
        return this.each(function() {
            var curDom = $(this);
            jQuery.grep(cssName.split(","),
                    function(cssToBeRemoved) {
                        curDom.css(cssToBeRemoved, '');
                    });
            return curDom;
        });
    }
});

var typewatch = function(){
  var timer = 0;
  return function(callback, ms){
      clearTimeout (timer);
      timer = setTimeout(callback, ms);
  }  
}();

function retExt(filname) {
  var arr_file=explode(".", filname);
  arr_file=array_reverse(arr_file);
  var ext=trim(arr_file[0]);
  
  return ext;
}

function retFileNameWhitoutExt(filname) {
  var ultimoPunto = filname.lastIndexOf(".");
  
  if (ultimoPunto === -1) {
    return filname;
  }

  var retNome = filname.slice(0, ultimoPunto);

  return retNome;
}

function replaceAll(str,f,r) {
  var intIndexOfMatch = str.indexOf(f);
  
  while (intIndexOfMatch != -1){
    // Relace out the current instance.
    str=str.replace(f, r);

    // Get the index of any next matching substring.
    intIndexOfMatch = str.indexOf(f);
  }
  
  return str;
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function toFloat(str) {
  str=str.toString();
  str=replaceAll(str,"€","");
  str=replaceAll(str,"&euro;","");
  str=replaceAll(str,"EUR","");
  str=replaceAll(str,"eur","");
  if(str.indexOf(",")!=-1) str=replaceAll(str,".","");
  str=replaceAll(str,",",".");
  str=str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
  
  str=parseFloat(str);
  if(isNaN(str)) str=0;
  return str;
}

function roundNumber(num, dec) {
  if(isNaN(num)) num=parseFloat(0.00);
  var result = parseFloat(Math.round(num*Math.pow(10,dec))/Math.pow(10,dec));
  var str=result.toFixed(2);
  str=str.toString();
  str=str.replace(".",",");
  
  var conta=false;
  var c=0;
  var output = '';
  for(var i=str.length-1;i>-1;i--) {
   if(conta==true) c++;
   if(c==4) {
    output+=".";
    c=1;
   } 
   if(str.charAt(i)==",") conta=true;
   output+=str.charAt(i);
  }
  
  var newString = "";  
  var counter = output.length; 

  for (counter  ;counter > 0 ;counter -- ) { 
     newString += output.substring(counter-1, counter); 
  }
  
  return newString;
}

function mostra(id) {
  var height=document.getElementById(id).style.height;
  document.getElementById(id).style.overflow='hidden';
  
  if(height=="") {
    document.getElementById(id).style.height='1px';
    document.getElementById(id).style.visibility='hidden';
  } else {
    document.getElementById(id).style.height='';
    document.getElementById(id).style.visibility='visible';
    window.scrollTo(0,0);
  }
}

function mostra2(id) {
  var height=document.getElementById(id).style.height;
  document.getElementById(id).style.overflow='hidden';
  
  if(height=="") {
    document.getElementById(id).style.height='1px';
    document.getElementById(id).style.visibility='hidden';
  } else {
    document.getElementById(id).style.height='';
    document.getElementById(id).style.visibility='visible';
  }
}

function mostraNoScroll(id) {
  if(tmpid!="") mostra2(tmpid);
  
  var height=document.getElementById(id).style.height;
  document.getElementById(id).style.overflow='hidden';
  
  if(height=="") {
    document.getElementById(id).style.height='1px';
    document.getElementById(id).style.visibility='hidden';
    tmpid="";
  } else {
    document.getElementById(id).style.height='';
    document.getElementById(id).style.visibility='visible';
    tmpid=id;
  }
}

function troncaTesto(testo, caratteri) { 
  if(!caratteri) caratteri=50;
  if(strlen(testo)<=caratteri) return testo; 
  
  testo=strip_tags(testo);
  var nuovo = wordwrap(testo, caratteri, "|", true);
  var nuovotesto=explode("|",nuovo); 
  return nuovotesto[0]+"&hellip;";
}

function getFilenameUnique() {
  var numbers = "0123456789";
  var letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
  strChars = date("YmdHis", time());
	//srand((double)microtime()*1000000);
	for (var i=1; i<=3; i++) {
		intPos = mt_rand(0, strlen(letters));
    chrChar = substr (letters, intPos, 1);
		strChars += chrChar;
	}
	return md5(strChars);
}

function addFlash(mydest,flash,w,h,ver) {
  var ww=w;
  var hh=h;
  
  $(mydest).each(function(n){
    w=ww;
    h=hh;
    
    if(isNaN(w) || w==0) w=$(this).outerWidth();
    if(isNaN(h) || h==0) h=$(this).outerHeight();
    if(isNaN(ver)) ver="9.0.0";
    var str=$(this).html();
    
    if($(this).attr("id")=="") {
      iddest=getFilenameUnique();
      $(this).attr("id",iddest); 
    }else{
      iddest=$(this).attr("id");    
    }
    
    swfobject.embedSWF(flash, iddest, w, h, ver, "expressInstall.swf", {mycontent:urlencode(str)},{wmode:'transparent'});  
  });  
}

function right(value, count){
  return substr(value, (count*-1));
}

function left(string, count){
  return substr(string, 0, count);
}

function dataIta(data) {
  data=explode("-", data);
  
  if(data[1].length==1) data[1]="0"+data[1];
  if(data[2].length==1) data[2]="0"+data[2];
  
  data=data[2]+"-"+data[1]+"-"+data[0];
  return data;
}

function gmap_loadMarker(map,x,y,info) {
  var infowindow = new google.maps.InfoWindow({
      content: info
  });

  var marker = new google.maps.Marker({
      position: new google.maps.LatLng(y, x),
      map: map
  });
  
  google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map,marker);
  });
    
}

function gmap_load(x,y,name,zoom) {
  var myLatlng = new google.maps.LatLng(y,x);
  var mapOptions = {
    zoom: zoom,
    center: myLatlng
  };

  var map = new google.maps.Map(document.getElementById(name), mapOptions);

  return map;
}

function include(jsFile) {
  document.write('<script type="text/javascript" src="'+ jsFile + '"></scr' + 'ipt>');
}

function fileBrowserCallBack(field_name, url, type, win) {
	var connector = getPathBackoffice+"filemanager/browser.html?Connector=connectors/php/connector.php";
	var enableAutoTypeSelection = true;
	
	var cType;
	tinyfck_field = field_name;
	tinyfck = win;
	
	switch (type) {
		case "image":
			cType = "Image";
			break;
		case "flash":
			cType = "Flash";
			break;
		case "file":
			cType = "File";
			break;
		case "media":
			cType = "Media";
			break;
	}
	
	if (enableAutoTypeSelection && cType) {
		connector += "&Type=" + cType;
	}
	
	window.open(connector, "tinyfck", "modal,width=800,height=600");
}

$("a[rel=rsOpenWindow]").live("click",function(){
  var op_url=$(this).attr("href");
  var tab=$(this).attr("rsTable");
  var where=$(this).attr("rsWhere");
  var ordinamento=$(this).attr("rsOrd");
  var txt=$(this).attr("rsTxt");
  var tit=$(this).attr("rsTit");
  var rsTableParent=$(this).attr("rsTableParent");
  var rsTableParentId=$(this).attr("rsTableParentId");
  var rsInsert=$(this).attr("rsInsert");
  var rsInsertId=$(this).attr("rsInsertId");
  var rsStrutture=$(this).attr("rsStrutture");
  
  if(rsStrutture=="" || (rsStrutture==undefined)) {
    var winOptions = {
      'template':     op_url,
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
  }else{
    var winOptions={
      'strutture': rsStrutture,
      'width': 300,
      'height': 600,
      'maxButton': true,
      'title': rsStrutture
    };
  }
  
  $("div.rsTable2-dialog-nm").remove();
  win=new rsWindows(winOptions);
  win.open();
  
  return false;
});

$("div.rs-windows").live("click",function(){
  $("div.rsTable2-dialog-nm").remove();
});

$("div.rsTable2-dialog-nm").live("click",function(){
  $("div.rsTable2-dialog-nm").remove();
});

$("input.password-dimenticata").live("click",function(){
  var frmLogin=$(this).parents("form#LoginAreaRis");
  if(frmLogin.find(".rsPwdSend:visible").length==0) {
    frmLogin.find(".rsPwdSend").show();
  }else{
    frmLogin.find(".rsPwdSend").hide();  
  }
  return false;    
});

$("div.pwdSend-close").live("click",function(){
  $("div.rsPwdSend").hide();
});

$("div.rsTable2-suggest select").live("change",function(){
  var html=$(this).find("option:selected").html();
  html=trim(html_entity_decode(html));
  $(this).next("input").val(html);  
});

$("#tabs-2").live("click",function(){
  var strVal="";
  var tmpVal;
  
  $("[ecomm_is_val='1'][ecomm_is_for_mockup!='']").each(function(i){
      if($(this).is("input")) {
          tmpVal=$(this).val();
          if(tmpVal!=undefined && tmpVal!="") strVal = strVal + $(this).val() + ";";    
      } else if($(this).is("select")){
          tmpVal=$(this).val();
          if(tmpVal!=undefined && tmpVal!="") strVal = strVal + $(this).val() + ";";         
      } else if($(this).is("div")){
          tmpVal=$(this).filter(".crt-caratteristiche-aff-sel").attr("value");
          if(tmpVal!=undefined && tmpVal!="") strVal = strVal + $(this).filter(".crt-caratteristiche-aff-sel").attr("value") + ";"; 
      }
  });   
  
  console.log(strVal);
   
});
