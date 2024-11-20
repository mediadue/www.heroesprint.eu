<script language="JavaScript" type="text/javascript">

function bookmarksite(title, url){
if (document.all)
window.external.AddFavorite(url, title);
else if (window.sidebar)
window.sidebar.addPanel(title, url, "")
}

function selCh1() {
	var selIdx 		= document.level1.cat.selectedIndex;
	var newSel 		= document.level1.cat.options[selIdx].text;
	var selection	= document.level1.cat.value;
	self.location.replace("?add=1&start=<?php echo $_GET['start']; ?>&id_cat=" + selection);
}

function selCh2() {
	var selIdx 		= document.level2.subcat.selectedIndex;
	var newSel 		= document.level2.subcat.options[selIdx].text;
	var selection	= document.level2.subcat.value;
	self.location.replace("?add=1&start=<?php echo $_GET['start']; ?>&id_subcat=" + selection);
}

function selCh3() {
	var selIdx 		= document.level3.subcat2.selectedIndex;
	var newSel 		= document.level3.subcat2.options[selIdx].text;
	var selection	= document.level3.subcat2.value;
	self.location.replace("?add=1&start=<?php echo $_GET['start']; ?>&id_subcat2=" + selection);
}

function selCh4() {
	var selIdx 		= document.level1.cat.selectedIndex;
	var newSel 		= document.level1.cat.options[selIdx].text;
	var selection	= document.level1.cat.value;
	self.location.replace("?mod=1&start=<?php echo $_GET['start']; ?>&id=<? echo $_GET['id']; ?>&id_cat=" + selection);
}

function selCh5() {
	var selIdx 		= document.level2.subcat.selectedIndex;
	var newSel 		= document.level2.subcat.options[selIdx].text;
	var selection	= document.level2.subcat.value;
	self.location.replace("?mod=1&start=<?php echo $_GET['start']; ?>&id=<? echo $_GET['id']; ?>&id_subcat=" + selection);
}

function selCh6() {
	var selIdx 		= document.level3.subcat2.selectedIndex;
	var newSel 		= document.level3.subcat2.options[selIdx].text;
	var selection	= document.level3.subcat2.value;
	self.location.replace("?mod=1&start=<?php echo $_GET['start']; ?>&id=<? echo $_GET['id']; ?>&id_subcat2=" + selection);
}

function modpay() {
	var selIdx 		= document.pay.type_pay.selectedIndex;
	var newSel 		= document.pay.type_pay.options[selIdx].text;
	var selection	= document.pay.type_pay.value;
	self.location.replace("?type=" + newSel + "&pay_type=" + selection + "&task=select");
}

function apri(url) { 
newin = window.open(url,'titolo','scrollbars=yes, resizable=yes, width=700, height=330, status=no, location=no, toolbar=no'); 
}

function POSBy(url) { 
newin = window.open(url,'titolo','scrollbars=yes, resizable=yes, width=590, height=550, status=no, location=no, toolbar=no'); 
}
function openWindow(url) {
newin = window.open(url,'titolo','scrollbars=no, resizable=no, width=500, height=280, status=no, location=no, toolbar=no'); 
}

function apri_products_imgs(url) { 
newin = window.open(url,'titolo','scrollbars=yes, resizable=yes, width=<?php echo ($max_width  + 60); ?>,height=550,status=no,location=no,toolbar=no'); 
}

//=========================================== CALENDARIO ===========================================================

function show_calendar(str_target, str_datetime) {
	var arr_months = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno",
		"Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
	var week_days = ["Do", "Lu", "Ma", "Me", "Gi", "Ve", "Sa"];
	var n_weekstart = 1; // day week starts from (normally 0 or 1)

	var dt_datetime = (str_datetime == null || str_datetime =="" ?  new Date() : str2dt(str_datetime));
	var dt_prev_month = new Date(dt_datetime);
	dt_prev_month.setMonth(dt_datetime.getMonth()-1);
	var dt_next_month = new Date(dt_datetime);
	dt_next_month.setMonth(dt_datetime.getMonth()+1);
	var dt_firstday = new Date(dt_datetime);
	dt_firstday.setDate(1);
	dt_firstday.setDate(1-(7+dt_firstday.getDay()-n_weekstart)%7);
	var dt_lastday = new Date(dt_next_month);
	dt_lastday.setDate(0);
	
	// html generation (feel free to tune it for your particular application)
	// print calendar header
	var str_buffer = new String (
		"<html>\n"+
		"<head>\n"+
		"	<title>Calendar</title>\n"+
		"</head>\n"+
		"<body bgcolor=\"White\">\n"+
		"<table class=\"clsOTable\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n"+
		"<tr><td bgcolor=\"#006600\">\n"+
		"<table cellspacing=\"1\" cellpadding=\"3\" border=\"0\" width=\"100%\">\n"+
		"<tr>\n	<td bgcolor=\"#006600\">&nbsp;</td>\n"+
		"	<td bgcolor=\"#006600\" colspan=\"5\">"+
		"<font color=\"white\" face=\"tahoma, verdana\" size=\"1\">"
		+arr_months[dt_datetime.getMonth()]+" "+dt_datetime.getFullYear()+"</font></td>\n"+
		"	<td bgcolor=\"#006600\" align=\"right\">&nbsp;</td>\n</tr>\n"
	);

	var dt_current_day = new Date(dt_firstday);
	// print weekdays titles
	str_buffer += "<tr>\n";
	for (var n=0; n<7; n++)
		str_buffer += "	<td bgcolor=\"#666600\">"+
		"<font color=\"white\" face=\"tahoma, verdana\" size=\"1\">"+
		week_days[(n_weekstart+n)%7]+"</font></td>\n";
	// print calendar table
	str_buffer += "</tr>\n";
	while (dt_current_day.getMonth() == dt_datetime.getMonth() ||
		dt_current_day.getMonth() == dt_firstday.getMonth()) {
		// print row heder
		str_buffer += "<tr>\n";
		for (var n_current_wday=0; n_current_wday<7; n_current_wday++) {
				if (dt_current_day.getDate() == dt_datetime.getDate() &&
					dt_current_day.getMonth() == dt_datetime.getMonth())
					// print current date
					str_buffer += "	<td bgcolor=\"#FF4444\" align=\"right\">";
				else if (dt_current_day.getDay() == 0 || dt_current_day.getDay() == 6)
					// weekend days
					str_buffer += "	<td bgcolor=\"#d4f8c0\" align=\"right\">";
				else
					// print working days of current month
					str_buffer += "	<td bgcolor=\"white\" align=\"right\">";

				if (dt_current_day.getMonth() == dt_datetime.getMonth())
					// print days of current month
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'+document.cal.time.value; window.close();\">"+
					"<font color=\"black\" face=\"tahoma, verdana\" size=\"1\">";
				else
					// print days of other months
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'+document.cal.time.value; window.close();\">"+
					"<font color=\"gray\" face=\"tahoma, verdana\" size=\"1\">";
				str_buffer += dt_current_day.getDate()+"</font></a></td>\n";
				dt_current_day.setDate(dt_current_day.getDate()+1);
		}
		// print row footer
		str_buffer += "</tr>\n";
	}
	// print calendar footer
	str_buffer +=
		"</td>\n</table>\n" +
		"</body>\n" +
		"</html>\n";




document.getElementById('fava').innerHTML = str_buffer;

/*	var vWinCal = window.open("", "Calendar",
		"width=200,height=250,status=no,resizable=yes,top=200,left=200");
	vWinCal.opener = self;
	var calc_doc = vWinCal.document;
	calc_doc.write (str_buffer);
	calc_doc.close();*/
}
// datetime parsing and formatting routimes. modify them if you wish other datetime format
function str2dt (str_datetime) {
	var re_date = /^(\d+)\-(\d+)\-(\d+)\s+(\d+)\:(\d+)\:(\d+)$/;
	if (!re_date.exec(str_datetime))
		return alert("Invalid Datetime format: "+ str_datetime);
	return (new Date (RegExp.$3, RegExp.$2-1, RegExp.$1, RegExp.$4, RegExp.$5, RegExp.$6));
}
function dt2dtstr (dt_datetime) {
	return (new String (
			dt_datetime.getDate()+"-"+(dt_datetime.getMonth()+1)+"-"+dt_datetime.getFullYear()+" "));
}
function dt2tmstr (dt_datetime) {
	return (new String (
			dt_datetime.getHours()+":"+dt_datetime.getMinutes()+":"+dt_datetime.getSeconds()));
}


</script>
<script type="text/JavaScript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
