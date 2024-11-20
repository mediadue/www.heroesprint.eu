<?php

Class Html {     

var $g_jsCode;

function Html() { 
  $this->g_jsCode="";
}

// ******************************************************************************************
function templateHeadsection()
{
	global $ln;
	$objUtility = new Utility;
	?>
	<meta http-equiv="Content-Type" content="text/xml;"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo "resources/" ?>style.css"/>
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo "resources/" ?>style_print.css" />
	<?php
}
// ******************************************************************************************
function adminIco($filename)
{
	$objUtility = new Utility;

	$ext = $objUtility->getExt($filename);
	$filenameico = "ico_file_".$ext.".png";
	$fileabs = $objUtility->getPathBackofficeResourcesAbsolute() . $filenameico;
	$filerel = $objUtility->getPathBackofficeResources() . $filenameico;
	if (!file_exists($fileabs))
		$filerel = $objUtility->getPathBackofficeResources() . "ico_file.png";
	  ?><img src="<?php echo $filerel ?>" alt="<?php echo $ext ?>"/><?php
}
// ******************************************************************************************
function templateHeader() 
{       
  global $ln;
	$objUtility = new Utility;
	?>
    <div id="header">
    	<div class="image">
    	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="508" height="259" id="testataZoom" align="middle">
      <param name="allowScriptAccess" value="sameDomain" />
      <param name="movie" value="<?php echo $objUtility->getPathResourcesStatic() ?>headimg<?php echo $ln?>.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#727272" /><embed src="<?php echo $objUtility->getPathResourcesStatic() ?>headimg<?php echo $ln?>.swf" quality="high" bgcolor="#727272" width="508" height="259" name="testataZoom" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
      </object>
    		
		<?php /*	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="508" height="259" id="home" align="middle">
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="movie" value="<?php echo $objUtility->getPathResourcesStatic() ?>home.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="<?php echo $objUtility->getPathResourcesStatic() ?>home.swf" quality="high" bgcolor="#ffffff" width="508" height="259" name="home" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object> */ ?>			
		</div>
        <ul>
          <li><a href="index.php?ln="><img src="<?php echo $objUtility->getPathResourcesStatic() ?>ico_ln_it.gif" alt="italiano"/><span>IT</span></a></li>
          <li><a href="index.php?ln=en"><img src="<?php echo $objUtility->getPathResourcesStatic() ?>ico_ln_en.gif" alt="english"/><span>EN</span></a></li>
          <li><a href="index.php?ln=fr"><img src="<?php echo $objUtility->getPathResourcesStatic() ?>ico_ln_fr.gif" alt="fran&ccedil;ais"/><span>FR</span></a></li>
          <li><a href="index.php?ln=es"><img src="<?php echo $objUtility->getPathResourcesStatic() ?>ico_ln_es.gif" alt="espa&ntilde;ol"/><span>ES</span></a></li>
        </ul>
    </div>
    <hr class="hide"/>
	<?php
}
                         
// ******************************************************************************************
function adminDesktop() {
  $objUsers = new Users;
  $objUsers->getCurrentUser($intIdutente, $strUsername);
  
  $roles=getRolesByUser($intIdutente);
  $desktop=getTable("menu","ordine ASC","desktop='1'");
  ?>
  <!-- Plain box -->
  <div class="ez-wr rsAdmin-desktop">
    <?php 
    while (list($key, $row) = each($desktop)) { 
      $aut=getTable("roles_menu_nm","","idmenu='".$row['id']."'");
      if(count($aut)>0) { ?>
        <div class="ez-box rsAdmin-desktop-icon"><a href="#" rel="rsOpenWindow" rsTable="<?php echo $row['tabella']; ?>" ><img src="<?php echo retFile($row['icona_file'],32); ?>" /><br><?php echo ln($row['nome']); ?></a></div> 
      <? } ?>
    <? } ?>
  </div>
  <?php    
}

// ******************************************************************************************
function templateLeft($currentpage, $idcat=false)
{
	global $conn, $ln, $objUtility, $objConfig, $objNews, $objProducts;

	$ln_title = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_nokia'
				));
	$ln_subtitle = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_SOTTOTIT'
				));
	$ln_home = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_HOME'
				));
	$ln_azienda = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_AZIENDA'
				));
				
	$ln_prodotti = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_PRODOTTI'
				));
				
	$ln_rassegnastampa = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_RASSTAMP'
				));
				
	$ln_contatti = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_CONTATTI'
				));
				
	$ln_areariservata = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_RESAREA'
				));
				
	$ln_newstitle = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_NEWSTIT'
				));
				
	$ln_newsempty = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_NONEWS'
				));
	
	$ln_continua = $objUtility -> dictionary(array(
					'word' => 'LN_TITLE_CONTINUE'
				));

	$ln_vernici = $objUtility -> dictionary(array(
					'word' => 'LN_PROD_VERNICI'
				));
	
	$ln_dovesiamo = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_DOVESIAMO'
				));
				
	$ln_listino = $objUtility -> dictionary(array(
					'word' => 'LN_MENU_LISTINO'
				));
/*
	
	$ln_title = array (""=>"nokia", "en"=>"nokia", "fr"=>"nokia", "es"=>"nokia");
	$ln_subtitle = array (""=>"porte in legno e accessori", "en"=>"porte in legno e accessori", "fr"=>"porte in legno e accessori", "es"=>"porte in legno e accessori");
	$ln_home = array (""=>"Home", "en"=>"Home", "fr"=>"Home", "es"=>"Home");
	$ln_azienda = array (""=>"L'Azienda", "en"=>"L'Azienda", "fr"=>"L'Azienda", "es"=>"L'Azienda");
	$ln_prodotti = array (""=>"Prodotti", "en"=>"Prodotti", "fr"=>"Prodotti", "es"=>"Prodotti");
	$ln_rassegnastampa = array (""=>"Rassegna stampa", "en"=>"Press", "fr"=>"Press", "es"=>"Press");
	$ln_contatti = array (""=>"Contatti", "en"=>"Contatti", "fr"=>"Contatti", "es"=>"Contatti");
	$ln_areariservata = array (""=>"Area riservata", "en"=>"Area riservata", "fr"=>"Area riservata", "es"=>"Area riservata");
	$ln_newstitle = array (""=>"News", "en"=>"News", "fr"=>"News", "es"=>"News");
	$ln_newsempty = array (""=>"al momento non ci sono news", "en"=>"no news", "fr"=>"no news", "es"=>"no news");
	$ln_continua = array (""=>"continua", "en"=>"more", "fr"=>"more", "es"=>"more");
	
*/	
	
	?>
	<div id="left">
		<div id="logo">
			<h1><?php echo $ln_title[$ln] ?></h1>
			<p><?php echo $ln_subtitle[$ln] ?></p>
		</div>
		<hr class="hide"/>
		<div id="nav">
			<ul>
				<li<?php echo (mb_strtolower($currentpage) == "home") ? " class=\"important\"" : "" ?>><a href="index.php"><?php echo $ln_home[$ln] ?></a></li>
				<li<?php echo (mb_strtolower($currentpage) == "azienda") ? " class=\"important\"" : "" ?>><a href="azienda.php"><?php echo $ln_azienda[$ln] ?></a></li>
				<li<?php echo (mb_strtolower($currentpage) == "prodotti") ? " class=\"important\"" : "" ?>><a href="prodotti.php"><?php echo $ln_prodotti[$ln] ?></a></li>
				<li<?php echo (mb_strtolower($currentpage) == "rassegna_stampa") ? " class=\"important\"" : "" ?>><a href="rassegna_stampa.php"><?php echo $ln_rassegnastampa[$ln] ?></a></li>
				
				<li<?php echo (mb_strtolower($currentpage) == "dove_siamo") ? " class=\"important\"" : "" ?>><a href="dove_siamo.php"><?php echo $ln_dovesiamo[$ln] ?></a></li>
				
				<li<?php echo (mb_strtolower($currentpage) == "contatti") ? " class=\"important\"" : "" ?>><a href="contatti.php"><?php echo $ln_contatti[$ln] ?></a></li>
				<li<?php echo (mb_strtolower($currentpage) == "area_riservata") ? " class=\"important\"" : "" ?>><a href="area_riservata.php"><?php echo $ln_areariservata[$ln] ?></a></li>
				<li<?php echo (mb_strtolower($currentpage) == "listino") ? " class=\"important\"" : "" ?>><a href="listino.php"><?php echo $ln_listino[$ln] ?></a></li>
				
			</ul>
		</div>
		<hr class="hide"/>
		<div id="news">
			<h2><?php echo $ln_newstitle[$ln] ?></h2>
			<?php
			$idNewsCat = $objConfig->get("idnewscat-ultime");
			$rs = $objNews->getListPublished($conn, $idNewsCat);
			if (count($rs)) 
			{
				while (list($key, $row) = each($rs)) 
				{
					?>
					<h3><?php echo $row["titolo".$ln] ?></h3>
					<p><?php echo $row["abstract".$ln] ?></p>
					<?php 
					if ($row["testo".$ln] || $row["link"])
					{
						?>
						<p class="more"><a href="<?php echo ($row["link"]) ? "http://".$row["link"] : "news.php?id=".$row["id"] ?>"<?php echo ($row["link"]) ? " target=\"_blank\"" : "" ?>"><?php echo $ln_continua[$ln] ?>&nbsp;&raquo;</a></p>
						<?php
					}
				}
			} 
			else 
			{
				?>
				<p><?php echo $ln_newsempty[$ln] ?></p>
				<?php
			}
			?>
		</div>
		<hr class="hide"/>
		<div id="support">
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="243" height="97" id="mercatino" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="resources/mercatino<?php echo $ln?>.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="resources/mercatino<?php echo $ln?>.swf" quality="high" bgcolor="#ffffff" width="243" height="97" name="mercatino" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object><br/>
<?php define('__PHP_STATS_PATH__','/web/htdocs/www.nokia.it/home/stats/');
include(__PHP_STATS_PATH__.'php-stats.redir.php');?>
<img src="resources/fsc.gif" alt=""/>
</div>
		<hr class="hide"/>
        <?php
		if ($idcat)
		{
			$rsImages = $objProducts->categorieImagesGetList($conn, $idcat);
			if (count($rsImages)) 
			{
				?>
				
			<!--	<div id="verniciate">
					<p><a href="prodotti_verniciate.php?idcat=<?php echo $idcat ?>"><?php echo $ln_vernici[$ln] ?>&nbsp;&raquo;</a></p>
				</div> -->
				<?php
			} 
		}
		?>
	</div>	
	<?php
}


// ******************************************************************************************
function templateContactsEmail() 
{
	global $ln, $objUtility;
	?>	
<script type="text/javascript" language="javascript">
function nomeaziHide()
{
	var tr = document.getElementById('nomeazi');
	if (tr==null) { return; }
	tr.style.display = 'none';
	document.form1.ragsoc.value='';
}
function nomeaziShow()
{
	var tr = document.getElementById('nomeazi');
	if (tr==null) { return; }
	tr.style.display = '';
	document.form1.ragsoc.focus();
}
</script>
<form name="form1" method="post" action="?task=sendmail">
<table width="400" border="0" cellspacing="4" cellpadding="0">
<tr><td coslpan='2'>&nbsp;</td></tr>
<tr align="left" valign="top"> <td ></td>
<td>
  <input type="radio" name="tipo" value='privato' onClick="nomeaziHide()" checked > 
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_PRIVATI','mode'=>'STRING')); ?> &nbsp;&nbsp;&nbsp;&nbsp; 
  <input type="radio" name="tipo" value='azienda' onClick="nomeaziShow()" <?php echo ($_REQUEST['tipo'] == 'azienda')?'checked':'' ?>> 
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_AZIENDE','mode'=>'STRING')); ?> &nbsp;&nbsp;&nbsp;&nbsp; 
</td>
</tr>
<tr id="nomeazi"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_NOME_AZIENDA','mode'=>'STRING')); ?>:<font color="red">*</font></td>
<td>
  <input type="text" name="ragsoc" size="25" maxlength="25" value="<?php echo @$_REQUEST['ragsoc']?>">
</td>
</tr>

<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_NOME_REF','mode'=>'STRING')); ?>:
<font color="red">*</font></td>
<td>
<input type="text" name="responsabile" size="25" maxlength="25" value="<?php echo @$_REQUEST['responsabile']?>">
</td>
</tr>

<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_COGN_REF','mode'=>'STRING')); ?>:
<font color="red">*</font></td>
<td>
<input type="text" name="cognome" size="25" maxlength="25" value="<?php echo @$_REQUEST['cognome']?>">
</td>
</tr>

<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_INDIRIZZO','mode'=>'STRING')); ?>:
</td>
<td>
<input type="text" name="indirizzo" size="25" value="<?php echo @$_REQUEST['indirizzo']?>">
</td>
</tr>
<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_CAP','mode'=>'STRING')); ?>:
<font color="red">*</font></td>

<td>
<input type="text" name="cap" size="25" maxlength='5' value="<?php echo @$_REQUEST['cap']?>">
</td>
</tr>
<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_CITTA','mode'=>'STRING')); ?>:
<font color="red">*</font></td>
<td>
<input type="text" name="citta" size="25" value="<?php echo @$_REQUEST['citta']?>">

</td>
</tr>
<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_PAESE','mode'=>'STRING')); ?>:
<font color="red">*</font></td>
<td>
<input type="text" name="prov" size="25" value="<?php echo @$_REQUEST['prov']?>">
</td>
</tr>

              
<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_TEL','mode'=>'STRING')); ?>:
</td>
<td>
<input type="text" name="tel" size="25" value="<?php echo @$_REQUEST['tel']?>">
</td>
</tr>
<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_FAX','mode'=>'STRING')); ?>:
</td>
<td>

<input type="text" name="fax" size="25" value="<?php echo @$_REQUEST['fax']?>">
</td>
</tr>
<tr align="left" valign="top"> 
<td>
   <?php echo $objUtility->dictionary(array('word'=>'CONTATTI_EMAIL','mode'=>'STRING')); ?>:
<font color="red">*</font></td>
<td>
<input type="text" name="email" size="25" value="<?php echo @$_REQUEST['email']?>">
</td>

</tr>
               
</table>
            
<br />
<table width="*" border="0" cellspacing="3" cellpadding="0">
<tr> 
<td>
<input type="checkbox" name="scontistica" value="si" class="check" <?php echo (@$_REQUEST['scontistica'])?'checked':''?>>
</td>
<?if($_REQUEST["scontistica"]){?>
<input type='hidden' name='id' value='<?=$_REQUEST["id"]?>'>
<?}?>
<td>
<?php echo $objUtility->dictionary(array('word'=>'CONTATTI_RICH_SCONTO','mode'=>'STRING')); ?>
</td>
</tr>
<tr> 
<td>
<input type="checkbox" name="agente" value="si" class="check" <?php echo (@$_REQUEST['agente'])?'checked':''?>>
</td>
<td>
<?php echo $objUtility->dictionary(array('word'=>'CONTATTI_RICH_AGENTE','mode'=>'STRING')); ?>
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="catalogo" value="si" class="check" <?php echo (@$_REQUEST['catalogo'])?'checked':''?>>
</td>
<td>
<?php echo $objUtility->dictionary(array('word'=>'CONTATTI_RICH_CATALOGO','mode'=>'STRING')); ?>
</td>
</tr>
<tr>

<tr>

<td></td>
<td> 
<?php echo $objUtility->dictionary(array('word'=>'CONTATTI_MESSAGGIO','mode'=>'STRING')); ?>: 
<textarea name="messaggio" rows="4" cols="30" ><?php echo @$_REQUEST['messaggio']?></textarea>
</td>
</tr>
</table>
</td>
<td align="left" valign="top"><br><br></td>
</tr>
</table>
</td>
</tr>
<!-- <tr>
<td align="left" valign="top" height="8" background="images/bk_div.gif"></td>
</tr> -->
<?php echo $objUtility->dictionary(array('word'=>'CONTATTI_DISCLAIMERNDSUBMIT','mode'=>'STRING')); ?>
     </table>
</form>

<script>
<?php
if ($_REQUEST['task'] == 'sendmail') {
	if ($_REQUEST['tipo'] == 'azienda') {
?>
	nomeaziShow();
<?php 
	} else {
?>
	nomeaziHide();
<?php 
	}
} else {
?>
	nomeaziHide();
<?php 
}
?>	
</script>	
<?php
}              

function sendMail($_=array()) {

	require_once("phpmailer/class.phpmailer.php");
	
	global $objUtility;


	$_checkedInputs = $this -> _sendMail_CheckInputsHelper();

	if($_checkedInputs['response']){

		$mail = new PHPMailer();
		$mail->IsSMTP(); // set mailer to use SMTP
		$mail->SMTPAuth = false; // turn off SMTP authentication
#		$mail->Host = "out.alice.it";  // specify main and backup server
		$mail->Host = "smtp.nokia.it";  // specify main and backup server
		//$mail->AddAddress("david.tavanti@gmail.com");
		$mail->AddAddress("nokia@nokia.it");
		$mail->From = $_REQUEST['email'];
		$mail->FromName = mb_ucfirst(trim($_REQUEST['responsabile']))." ".ucfirst(trim($_REQUEST['cognome']));
		$mail->WordWrap = 50; // set word wrap to 50 characters
		$mail->IsHTML(true); // set email format to HTML
		$mail->Subject = "Richiesta informazioni da www.nokia.it";
		
		
		
		
		$messaggio = 
"Mail inviata da: <br><br>

TIPO:       ".$_REQUEST['tipo']." <br>
AZIENDA:       ".$_REQUEST['ragsoc']." <br>
NOME: ".mb_ucfirst($_REQUEST['responsabile'])." <br>
COGNOME: ".mb_ucfirst($_REQUEST['cognome'])." <br>
INDIRIZZO:     ".$_REQUEST['indirizzo']." <br>
CAP:     ".$_REQUEST['cap']." <br>
CITTA':     ".$_REQUEST['citta']." <br>
PROVINCIA:     ".$_REQUEST['prov']." <br>
TELEFONO:      ".$_REQUEST['tel']." <br>
FAX:           ".$_REQUEST['fax']." <br>
E-MAIL:        ".$_REQUEST['email']." <br>
MESSAGGIO:  ".addslashes($_REQUEST['messaggio'])." <br>
RICHIESTA CATALOGO TECNICO PRODOTTI: ".$_REQUEST['catalogo']." <br>
RICHIESTA SCONTISTICA: ".$_REQUEST['scontistica']." <br>
RICHIESTA VISITA AGENTE: ".$_REQUEST['agente']." <br><br>
";

if($_REQUEST[id]){
		$queryprodotto="SELECT * FROM nokia_prodotti WHERE id='".$_REQUEST["id"]."'";
		$qpr=mysql_query($queryprodotto);
		$qpar=mysql_fetch_Array($qpr);
		$messaggio.="RICHIESTA INFORMAZIONI PER IL PRODOTTO:<b>".$qpar[codice]."</b>";
		}


		$mail->Body    = $messaggio;
		$mail->AltBody = $messaggio;
			

		if(!$mail->Send()){
			echo "<script>window.alert(\"".$objUtility->dictionary(array('word'=>'CONTATTI_MSGFAIL','mode'=>'STRING'))."\");</script>";
		} else {
			echo "<script>window.alert(\"".$objUtility->dictionary(array('word'=>'CONTATTI_MSGSEND','mode'=>'STRING'))."\");</script>";
			$_REQUEST = array();
		}

	}else{
		echo "<script>window.alert(\"".$objUtility->dictionary(array('word'=>'CONTATTI_BADINPUTS','mode'=>'STRING'))."\")</script>";
	}

}

function _sendMail_CheckInputsHelper($_=array()) {

    $_response = $_cognome = $_responsabile = $_email = $_citta = $_prov = $_cap = $_consenso = true;

	if (!$_REQUEST['cognome']     ) {$_cognome = $_response = false;}
	if (!$_REQUEST['responsabile']) {$_responsabile = $_response = false;}
	if (!$_REQUEST['email']       ) {$_email = $_response = false;}
	if (!$_REQUEST['citta']       ) {$_citta = $_response = false;}
	if (!$_REQUEST['prov']        ) {$_prov = $_response = false;}
	if (!$_REQUEST['cap']         ) {$_cap = $_response = false;}
	if (!$_REQUEST['consenso']    ) {$_consenso = $_response = false;}

	return array('response'     => $_response, 
	             'cognome'      => $_cognome,
				 'responsabile' => $_responsabile,
				 'email'        => $_email,
				 'citta'        => $_citta,
				 'prov'         => $_prov,
				 'cap'          => $_cap,
				 'consenso'     => $_consenso);
}

// ******************************************************************************************
function templateRight() 
{
	?>	
	<?php
}

// ******************************************************************************************
function templateNewsItem($row)
{
	global $conn, $objUtility;
	?>
	<li>
		<div class="img">
			<?php
			if ($row["idimgthumb"])
			{
				$objUtility->showObject($conn, $row["idimgthumb"], true);
			}
			else 
			{
				?>
				<img src="<?php echo $objUtility->getPathResourcesStatic() ?>logo-small.gif" alt="Studio Castagnoli"/>
				<?php
			}
			?>
		</div>
		<div class="title"><a href="<?php echo ($row["link"]) ? "http://".$row["link"] : "news.php?id=".$row["id"] ?>"<?php echo ($row["link"]) ? " target=\"_blank\"" : "" ?>><?php echo $row["titolo"] ?></a></div>
		<div class="text"><?php echo $row["abstract"] ?></div>
	</li>
<?php
}
// ******************************************************************************************
function templateFooter() 
{
	?>

	<div class="clear">&nbsp;</div>
	<div id="spacer">&nbsp;</div>
	<hr class="hide"/>
	<div id="footer">
		<div id="copyright">
			<address>nokia S.r.l. - Via G. Amendola, 123 - 06134 Ponte Pattoli (PG) - ITALY</address>
			Tel. +39 075.594.13.66 - Fax +39 075.594.13.67 - nokia@nokia.it - Part. I.V.A. 00551410541
		</div>
		<div id="footerline"></div>
	</div>
	<?php
}

// ******************************************************************************************
function adminHeadsection() {
  if(isAjaxPost()) return;
  
  $objUtility = new Utility;
	$objJs = new Js;
  //$objChat = new rsChat;

	?>
	<title><?php echo $_SERVER['SERVER_NAME']; ?> Admin - RsEngine by Mediadue</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="author" content=""/>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>css-default.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>css-struttura.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>clickmenu.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.tabs.css" media="print, projection, screen" />
  <link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.gallery.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.ribbon.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>jquery-ui-1.8.14.custom.css"/>
  <link href="<?php echo $objUtility->getPathBackofficeResources() ?>tables.css" media="screen" rel="stylesheet" title="CSS" type="text/css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>jScrollPane.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>ez.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.fixedheadertable.1.1.2.css"/>
  <link href="<?php echo $objUtility->getPathBackofficeResources() ?>uploadify.css" type="text/css" rel="stylesheet" />
  <link rel="Stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>css/jPicker-1.1.6.min.css" />
  <link rel="Stylesheet" type="text/css" href="<?php echo $objUtility->getPathBackofficeResources() ?>jPicker.css" />
  
   
  <script language="JavaScript" type="text/javascript">
     var getPathBackoffice = '<?php echo $objUtility->getPathBackoffice(); ?>';
     var getPathBackofficeResources = '<?php echo $objUtility->getPathBackofficeResources(); ?>';
	   var getPathResourcesDynamic = '<?php echo $objUtility->getPathResourcesDynamic(); ?>';
	</script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery-1.11.2.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery-migrate-1.1.1.js"></script>  
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jpicker-1.1.6.js"></script> 
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.metadata.v2.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.media.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>swfobject.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery-ui-1.8.22.full.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.livequery.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.clickmenu.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.form.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.gallery.0.3.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>urlEncode.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery_timer.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.jqzoom1.0.1.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>cloud-zoom.1.0.2.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.uploadify.v2.1.4.min.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>uploadifive/jquery.uploadifive.min.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.betterTooltip.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.corner.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jScrollPane-1.2.3.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.mousewheel.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>php.min.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>changeContent.jquey.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>rsFunctions.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>rsPaginazione.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.tools.min.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.tabs.pack.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.officebar.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.fixedheadertable.1.1.2.min.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.tablednd_0_5.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>rsWindows.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>rsTable2.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>rsWinMod.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>rsStrutture.js"></script>
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>rsPdfEditor.js"></script>
  
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.sound.js"></script>
  
  <!-- Additional IE/Win specific style sheet (Conditional Comments) -->
  <!--[if lte IE 7]>
  <link rel="stylesheet" href="<?php echo $objUtility->getPathBackofficeResources() ?>jquery.tabs-ie.css" type="text/css" media="projection, screen">
  <![endif]-->
  
  <script language="JavaScript" type="text/javascript">
     $.fn.jPicker.defaults.images.clientPath='<?php echo $objUtility->getPathBackofficeResources(); ?>images/';
     $(document).ready(function(){
       $(".scroll-pane").jScrollPane({showArrows:true});
       $("textarea.textEditor").each(function(){
        tinyMCE.execCommand('mceAddControl', false, $(this).attr("id")); 
       });
     });
	</script>
  <?php
  $this->adminHtmlEditor();
  $this->adminLytebox();
  $objJs->adminMenuML();
  
  if(isset($_SESSION['alert_box'])) {
    box($_SESSION['alert_box']);
    unset($_SESSION['alert_box']);
  } 
  
  /*
  $chat_conf=getTable("chat_conf","","attivo='1'");
  if(count($chat_conf)>0 && $_SESSION["user_id"]>0){
    $objChat->addToChat();
    $objChat->_print();
  }    
  */
}

// ******************************************************************************************
function adminHead() 
{
  global $config_table_prefix, $objUtility;
	$username = $_SESSION["user_login"];
	$dateLastAccess = $_SESSION["user_lastaccess"];
	$dateLastPwdUpdate = $_SESSION["user_lastpwdupdate"];
	$idUsers = $_SESSION["user_id"];
	
	if ($username && $idUsers) {
    $rs=getTable("loghi_backoffice","","attivo='1' AND id_users='$idUsers'");
  	$logo=retFile($rs[0]['logo_file']);
	}
	
	if($logo=="") $logo=$objUtility->getPathBackofficeResources()."logo.jpg";
	
  ?>
	<div id="header">
		<div id="header-logo"><img src="<?php echo $logo; ?>" alt="backoffice"/></div>
		<div id="header-box">
			<div id="header-content">
				<div id="header-info">
					<?php 
					if ($username) 
					{
						?>
						<p >Utente: <span><?php echo $username ?></span></p>
						<?php if ($dateLastAccess) { ?><p>Ultimo Accesso: <span><?php echo $objUtility->datetimeShow($dateLastAccess, "long") ?></span></p><?php } ?>
						<?php if ($dateLastPwdUpdate) { ?><p>Password modificata il: <span><?php echo $objUtility->datetimeShow($dateLastPwdUpdate, "long") ?></span></p><?php } ?>
						<ul>
              <li><a href="<?php echo $objUtility->getPathBackoffice() ?>pwd_upd.php">Cambia password</a></li>
							<li><a href="<?php echo $objUtility->getPathBackoffice() ?>logout.php" class="logout">Logout</a></li>
						</ul>
						<?php
					}
					?>
					&nbsp;
				</div>
			</div>
		</div>
	</div>
<?php
}

// ******************************************************************************************
function adminLeftOld($conn, $intIdutente, $strSelected="") {
	$objMenu = New Menu;
	$objUtility = new Utility;
	
  if($strSelected=="") $strSelected=$_SERVER['PHP_SELF'];
  ?>
	<div id="menu">
		<?php
		if ($conn) {
			$rs = $objMenu->getMenuModuli($conn, $intIdutente, false);
			$i=0;
			while (list($key, $row) = each($rs)) {
				$i++;
				?>
				<div class="module">
					<div class="title"><p><?php echo $row["testo"] ?></p></div>
					
					<?php
					$rsTwo = $objMenu->getMenu($conn, $row["id"], $intIdutente, false);
					if (count($rsTwo)) 
					{
						?>
						<ul>
							<?php
							$j=0;
							while (list($keyTwo, $rowTwo) = each($rsTwo)) {
								?>
								<li <? if(!(strpos($strSelected,$rowTwo["path"])===FALSE)) { echo "class='selected'"; } ?>><a href="<?php echo $objUtility->getPathBackoffice() . $rowTwo["path"] ?>"><?php echo $rowTwo["nome"] ?></a></li>
								<?php
							}
							?>
						</ul>
						<?php
					}
					?>
				</div>
				<?php
			}
		} else {
			?>
			<div class="module">
				<div class="title"><p>Admin</p></div>
				<ul>
					<li>nokia</li>
				</ul>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}

function adminLeft($conn, $intIdutente, $strSelected="") {
	$objMenu = New Menu;
	$objUtility = new Utility;
	
	printToolBar();
  
  if($strSelected=="") $strSelected=$_SERVER['PHP_SELF'];
  ?>
  <div id="stylishdiv">
		<ul id="stylishmenu"> 
    <?php
		if ($conn) {
		  $rs1 = $objMenu->getMenuModuli1($conn, $intIdutente, true);
			$i1=0;
      while (list($key1, $row1) = each($rs1)) {
        $rs = $objMenu->getMenuModuli($conn, $intIdutente, false, $row1["id"]);
        $i=0;
        if (count($rs)) {
          ?>
          <li><a href="" title="" ><?php echo ucfirst(mb_strtolower($row1["testo"],"UTF-8")); ?></a>
          <ul>
          <?
          if($row1["id"]=="2") {
            $sRs=getTable("strutture","nome ASC","riservato!='1' AND attivo='1'");
            while (list($sKey, $sRow) = each($sRs)) {
              $myicon=$objUtility->getPathBackofficeResources()."spacer.gif";?>
              <li><img class="pic" src="<?php echo $myicon; ?>" /><a rel="rsOpenWindow" rsStrutture="<?php echo $sRow['nome']; ?>" href="#"><?php echo ucfirst(mb_strtolower($sRow['nome'],"UTF-8")); ?></a><?php
            }
          }
          while (list($key, $row) = each($rs)) {
    				$i++;
    				$myicon=$objUtility->getPathBackofficeResources()."spacer.gif";
            $tmpicon=retFile($row['icona_file'],16);
    				if($tmpicon!="") $myicon=$tmpicon;
            $rsTwo = $objMenu->getMenu($conn, $row["id"], $intIdutente, false);  
            if (count($rsTwo)) {
              if($row["testo"]!="-") {
                ?><li style="color:black;"><img class="pic" src="<?php echo $myicon; ?>" /><?php echo ucfirst(mb_strtolower($row["testo"],"UTF-8")); ?><?php
    						?><ul><?php
  							
  							if($row["id"]=="5") {
                  $sRs=getTable("strutture","nome ASC","");
                  while (list($sKey, $sRow) = each($sRs)) {
                  ?><li><img class="pic" src="<?php echo $myicon; ?>" /><a rel="rsOpenWindow" rsStrutture="<?php echo $sRow['nome']; ?>" href="#"><?php echo ucfirst(mb_strtolower($sRow['nome'],"UTF-8")); ?></a></li><?php
                  }
                }
  							
                $j=0;
  							while (list($keyTwo, $rowTwo) = each($rsTwo)) {
  								$myicon=$objUtility->getPathBackofficeResources()."spacer.gif";
                  $tmpicon=retFile($rowTwo['icona_file'],16);
    				      if($tmpicon!="") $myicon=$tmpicon;
                  $rowTwo["path"]=$objUtility->getPathBackoffice().$rowTwo["path"];
                  if($rowTwo["path"]=="") $rowTwo["path"]="rsDefault/rsDefault.php?tbl=".$rowTwo["tabella"];
                  $winopen="";
                  if($rowTwo["tabella"]!="") {
                    $winopen='rel="rsOpenWindow" rsTable="'.$rowTwo['tabella'].'"';
                    $rowTwo["path"]="#";
                  }
                  ?><li><img class="pic" src="<?php echo $myicon; ?>" /><a <?php echo $winopen; ?> href="<?php echo $rowTwo["path"]; ?>"><?php echo ucfirst(mb_strtolower($rowTwo["nome"],"UTF-8")); ?></a></li><?php
  							}
    						?></ul></li><?php
              } else {
                $rowTwo=$rsTwo[0];
                
                $myicon=$objUtility->getPathBackofficeResources()."spacer.gif";
                $tmpicon=retFile($rowTwo['icona_file'],16);
        				if($tmpicon!="") $myicon=$tmpicon;
                $rowTwo["path"]=$objUtility->getPathBackoffice().$rowTwo["path"];
                if($rowTwo["path"]=="") $rowTwo["path"]="rsDefault/rsDefault.php?tbl=".$rowTwo["tabella"];
                $winopen="";
                if($rowTwo["tabella"]!="") {
                  $winopen='rel="rsOpenWindow" rsTable="'.$rowTwo['tabella'].'"';
                  $rowTwo["path"]="#";
                }
                ?><li><img class="pic" src="<?php echo $myicon; ?>" /><a <?php echo $winopen; ?> href="<?php echo $rowTwo["path"]; ?>"><?php echo ucfirst(mb_strtolower($rowTwo["nome"],"UTF-8")); ?></a></li><?php  
              }
            }
    			}
    		  ?></ul></li><?
        }
			}
			?>
			</ul>
			<?
		} else {
			?>
			<div class="module">
				<div class="title"><p>Admin</p></div>
				<ul>
					<li>MediaDue</li>
				</ul>
			</div>
			<?php
		}
		?>
	</div>
	<script>
  $(document).ready(function(){
    
    $("#stylishdiv").show();
    
  });
  </script>
	<?php
}

// ******************************************************************************************
function adminPageTitle($strModulo, $strMenu, $usePassed="") {
	if($usePassed=="") {
    $strMenu="";
    $objUtility = new Utility;
  	if($_GET['tbl']) {
      $menu=getTable("menu","","tabella='".$_GET['tbl']."'");
      $modulo=retRow("menu_moduli",$menu[0]['idmoduli']);
      $categoria=retRow("menu_categorie",$modulo['idcategorie']);  
    } else {
      $path=str_replace($objUtility->getPathBackoffice(), "", $_SERVER["PHP_SELF"]);
    
      $menu=getTable("menu","","path='$path'");
      $modulo=retRow("menu_moduli",$menu[0]['idmoduli']);
      $categoria=retRow("menu_categorie",$modulo['idcategorie']); 
    }  
    $strModulo=mb_ucfirst(mb_strtolower($categoria['testo']))." > ".mb_ucfirst(mb_strtolower($modulo['testo']))." > ".mb_ucfirst(mb_strtolower($menu[0]['nome']))."";
    $strModulo=str_replace(" >  > ","", $strModulo);
    $strModulo=str_replace("> - >"," > ", $strModulo);
  }
  
  ?>
	<div id="modulo-titolo">
		<p><span class="modulo"><?php echo $strModulo ?></span><?php if ($strMenu) { ?> &#187; <span class="menu"><?php echo $strMenu ?></span><?php } ?></p>
	</div>
	<?php
}

// ******************************************************************************************
function adminFooter() 
{
	$objUtility = new Utility;
	?>
	<div id="footer">
		<div id="footer-box">
			<div id="footer-content">
				<div id="footer-info"><a href="http://www.mediadue.net/" target="_blank"><img src="<?php echo $objUtility->getPathBackofficeResources() ?>logo-bottom.gif" alt="Mediadue"/></a></div>
			</div>
		</div>
	</div>
	<?php
}

// ******************************************************************************************
function adminLytebox() {
	$objUtility = new Utility;
	?>
	<link rel="stylesheet" href="<?php echo $objUtility->getPathBackofficeResources() ?>lytebox.css" type="text/css" media="screen" />
  <script type="text/javascript" language="javascript" src="<?php echo $objUtility->getPathBackofficeResources(); ?>lytebox.js"></script>
  <?
}

// ******************************************************************************************
function adminHtmlEditor() {
  $objUtility = new Utility;
	
  ?>
	<script language="javascript" type="text/javascript" src="<?php echo $objUtility->getPathBackoffice(); ?>tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
    editor_selector : "rs-none-selector-txt",
    height:250,
    width:470,
    convert_urls : false,
    relative_urls : false,
    remove_script_host : false,
    paste_auto_cleanup_on_paste : false,
    //save_enablewhendirty: true,           
    //save_onsavecallback : "rsTinySave",
    //save_oncancelcallback: "rsTinyCancel",
		
    // Theme options
		language : "it",
    theme_advanced_buttons1 : "newdocument,|,fontselect,fontsizeselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,forecolor,backcolor,|,undo,redo,|,link,unlink,code,|,insertdate,inserttime,preview",
		theme_advanced_buttons3 : "tablecontrols,|,sub,sup,|styleprops,charmap,hr,image,media",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		//theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : false,
    file_browser_callback : "fileBrowserCallBack",
		
    // Example content CSS (should be your site CSS)
		//content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",
    
		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
	<?php
}


// ******************************************************************************************
function adminPageRedirect($strUrl, $strText, $strVariables="", $strSeconds="0") {
	$_SESSION['alert_box']=$strText;
  ?>
  <script type="text/javascript">
	beginTimer();
  function beginTimer() {
		timerID = setTimeout(endTimer, <?php echo $strSeconds ?>000);
	}
	
  function endTimer() {
	  location.href = "<?=$strUrl?>";	
	}
	</script>
  <?
  exit;
}

// ******************************************************************************************
function dateModify($fieldName, $date, $useToday=false) {
	$objUtility = new Utility;
	$today = date("Ymd", time());
	if ($date != "") {
		$default = $date;
	} elseif ($useToday) {
		$default = $today;
	} else {
		$default = "";
	}
	?>
	<input type="text" name="<?php echo $fieldName ?>" value="<?php echo $default ?>" class="textsmall"/>
	<a href="javascript:cal_<?php echo $fieldName ?>.popup();"><img src="<?php echo $objUtility->getPathBackofficeResources() ?>cal.gif" alt="Clicca qui per modificare la data"/></a><br/>
	<script language="JavaScript" type="text/javascript">
	<!--
	var cal_<?php echo $fieldName ?> = new calendar3(document.forms[0].elements['<?php echo $fieldName ?>']);
	cal_<?php echo $fieldName ?>.year_scroll = true;
	cal_<?php echo $fieldName ?>.time_comp = false;
	 -->
	 </script>
	 <?php
}

// ******************************************************************************************
function layoutModify($label, $radioName, $default="?") {
	?>
	<td align="right" class="testo"><?php echo $label ?></td>
	<td>
		<table border="0" cellspacing="0" cellpadding="3">
			<tr>
				<?php writeOneLayout(1, $radioName, $default) ?>
				<?php writeOneLayout(2, $radioName, $default) ?>
				<?php writeOneLayout(3, $radioName, $default) ?>
				<?php writeOneLayout(4, $radioName, $default) ?>
				<?php writeOneLayout(5, $radioName, $default) ?>
				<?php writeOneLayout(6, $radioName, $default) ?>
			</tr>
		</table>
	</td>
	<?php
}

// ******************************************************************************************
function writeOneLayout($number, $radioName, $default) {
	$objUtility = new Utility;
	?>
	<td class="testo" align="center">
		<img src="<?php echo $objUtility->getPathBackofficeResources() ?>layout_<?php echo $number ?>.gif" alt="" width="60" height="60" border="0"/><br/>
		<input type="radio" name="<?php echo $radioName ?>" class="inputRadio" value="<?php echo $number ?>"<?php echo ($default==$number) ? " checked=\"yes\"" : "" ?>/>
	</td>
	<?php
}

// ******************************************************************************************
function flag($ln)
{
	$objUtility = new Utility;
	if (!$ln) $ln="it";
	?>
	<img src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ln_<?php echo $ln ?>.png" alt=""/>
	<?php
}

// ******************************************************************************************
function paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPageCurrent, $strAction, $intPageInterval=8,$mostra_tutti="",$selezionati="",$elimina_filtro="",$nascondi="",$precTxt="",$succTxt="") {
  if ($intPagesTot > 1) {
		if ($intPageCurrent <= $intPageInterval) {
			$intPageBegin=1;
		} else {
			$intPageBegin = ($intPageCurrent - $intPageInterval)+intval($intPageInterval/2);
			if($intPageBegin>$intPagesTot) $intPageBegin=$intPagesTot;
		}
		if (($intPageCurrent + $intPageInterval) > $intPagesTot) {
			$intPageEnd=$intPagesTot;
		} else {
			$intPageEnd = ($intPageCurrent + $intPageInterval);
		}
		
		$intPageIntervalEnd=($intPageBegin+$intPageInterval)-1;
		if($intPageIntervalEnd>$intPagesTot) $intPageIntervalEnd=$intPagesTot;
		if($precTxt=="") $precTxt="Â« Precedente";
		if($succTxt=="") $succTxt="Successiva Â»";
    ?>
    <div id="paginazione" class="paginazione" name="<?=$strAction?>" <?php if($nascondi==1) echo "style='display:none;'"; ?>>
			<input type="submit" name="act_<?php echo $strAction ?>_1i" value="1i" title="<?php echo ln("vai alla pagina 1");?>" class="page gopage" style="display:none;"/>
      <?php if ($intPageCurrent > 1) { ?>
				<input type="submit" name="act_<?php echo $strAction ?>_<?php echo ($intPageCurrent - 1) ?>" value="<?php echo ln($precTxt);?>" title="<?php echo ln("vai alla pagina precedente");?>" class="page prima"/>
			<?php } ?>
			<?php for ($i=$intPageBegin; $i<=$intPageIntervalEnd; $i++) { ?>
				<?php if ($intPageCurrent == $i) { ?>
					<input type="button" value="<?php echo $i ?>" class="pagesel"/>
				<?php } else { ?>
					<input type="submit" name="act_<?php echo $strAction ?>_<?php echo $i ?>" value="<?php echo $i ?>" title="<?php echo ln("vai alla pagina");?> <?php echo $i ?>" class="page gopage"/>
				<?php } ?>
			<?php } ?>
			<?php if ($intPageCurrent < $intPagesTot) { ?>
				&#160;&#160;<input type="submit" name="act_<?php echo $strAction ?>_<?php echo ($intPageCurrent + 1) ?>" value="<?php echo ln($succTxt);?>" title="<?php echo ln("vai alla pagina successiva");?>" class="page dopo"/>
			<?php } ?>
			<?php if ($intPageCurrent >0 ) { ?>
      <div class="testo"><?php echo ln("pagina");?> <?php echo $intPageCurrent ?> <?php echo ln("di");?> <?php echo $intPagesTot ?> <?php echo ln("su");?> <?php echo $intItemsTot ?> <?php echo ln("risultati");?>
        <?php if($mostra_tutti!="" || $selezionati!="" || $elimina_filtro!="") echo ""; ?>
        <?php if($mostra_tutti!="") { ?>
          <input type="submit" name="act_<?php echo $strAction ?>_y" value="mostra tutti" title="mostra tutti" class="page" style="padding-left:20px;"/>
        <? } ?>
        <?php if($selezionati!="") { ?>
        &#160;&#160;&#160;<input type="submit" name="act_<?php echo $strAction ?>_s" value="mostra selezionati" title="<?php echo ln("mostra selezionati");?>" class="page"/>
        <? } ?>
        <?php if($elimina_filtro!="") { ?>
           / <input type="submit" name="act_<?php echo $strAction ?>_ns" value="elimina filtro selezione" title="<?php echo ln("elimina filtro selezione");?>" class="page"/>
        <? } ?>
        <?php if($mostra_tutti!="" || $selezionati!="" || $elimina_filtro!="") echo ""; ?>
      </div>
		  <?php } ?>
    </div>
		<?php
	}
}

// ******************************************************************************************
function printInfoAreaRis() { 
  if(isAjaxPost()) return;
  
  $objJs = new Js;
  $objUtility = new Utility;
  global $config_table_prefix, $objUtility;
	$username = $_SESSION["userris_login"];
	$dateLastAccess = $_SESSION["userris_lastaccess"];
	$dateLastPwdUpdate = $_SESSION["userris_lastpwdupdate"];
	$idUsers = $_SESSION["userris_id"];
  $usr=retRow("users",$idUsers);
  
  if($username) { ?>
    <!-- Module 2A -->
    <div class="ez-wr">
      <?php if(retFile($usr['immagine_file'])) { ?>
        <div class="ez-fl ez-negmr ez-50 InfoAreaRis-img">
          <div class="ez-box"><img src="<?php echo retFile($usr['immagine_file'],35,35); ?>" /></div>
        </div>
      <? } ?>
      <div class="ez-last ez-oh">
        <div class="ez-box">
          <div id="InfoAreaRis">
            <h4 ><span><?php echo $username ?></span></h4>
          	<?php if ($dateLastAccess && $dateLastAccess!="0000-00-00 00:00:00") { ?><p class="InfoAreaRis_p1"><?php echo ln("Ultimo Accesso");?>: <span><?php echo $objUtility->datetimeShow($dateLastAccess, "long") ?></span></p><?php } ?>
          	<?php if ($dateLastPwdUpdate && $dateLastPwdUpdate!="0000-00-00 00:00:00") { ?><p class="InfoAreaRis_p2"><?php echo ln("Password modificata il");?>: <span><?php echo $objUtility->datetimeShow($dateLastPwdUpdate, "long") ?></span></p><?php } ?>
          	<div class="row">
	          	<ul class="nav col-sm-7 col-lg-5">
	          		<li class="InfoAreaRis-profilo <?php if($_GET['UserRegDo']==1) echo "InfoAreaRis-profilo-sel"; ?>"><a href="index.php?UserReg=1" ><i class="fa fa-user icon-inline"></i><?php echo ln("Profilo");?></a></li>
	              <li class="InfoAreaRis-documenti <?php if($_GET['documents']==1) echo "InfoAreaRis-documenti-sel"; ?>"><a href="index.php?documents=1" ><i class="fa fa-file-text icon-inline"></i><?php echo ln("Documenti");?></a></li>
	              <li><a class="cmbpwdvis" href="" onclick="return false;"><i class="fa fa-lock icon-inline"></i><?php echo ln("Cambia password");?></a></li>
	          		<li class="logout InfoAreaRis-logout"><a href="index.php?logout=1" ><i class="fa  fa-power-off icon-inline"></i>Logout</a></li>
	          	</ul>
          	</div>
          	<div class="row">
	          	<form  class="cmb-pwd col-sm-7 col-lg-5"  id="cmbpwd" name="cmbpwd" action='' method='post' onsubmit="return rsCheckFormCmbPwd()"> 
	             <div class="form-group"> <label for="password"> <?php echo ln("nuova password");?></label> <input type="password" class="theInput cmbpwd-password form-control" name="password" /></div> 
	              <div class="form-group"> <label for="password"> <?php echo ln("conferma password");?></label> <input type="password" class="theInput cmbpwd-nuova-password form-control" name="password_conf" /></div>
	              <div class="form-group clearfix">
	              <input type="submit" class="theSubmit btn btn-success btn-120" value="<?php echo ln("cambia");?>" name="ACT_PWDUPD-DO" /> 
	              <input type="submit" class="theClose btn btn-danger btn-120 pull-right" value="<?php echo ln("annulla");?>" name="Submit" onclick="return false;"/>
	              </div>
	            </form>
            </div>
        	</div>
        </div>
      </div>
    </div>
  	<?php
  	return true;
  }
  return false;  
}

// ******************************************************************************************
function printLoginAreaRis($reg="",$pwdsend="") {
  if(isset($_SESSION["userris_id"])) return;
  ?>
  <form id="LoginAreaRis" action='' method='post'> 
    <div class="loginarearis-username no-spacing"><?php echo ln("Nome Utente"); ?></div><input type="text" class="theInput form-control spacing-normal" name="utente" /> 
    <div class="loginarearis-password no-spacing">Password</div><input type="password" class="theInput  form-control spacing-normal" name="pwd" />
    <input type="submit" class="theSubmit arearis-login btn btn-success" value="<?php echo ln("Accedi");?>" name="Submit" />
    <?php if($reg!="") { ?><input type="submit" class="theSubmit registrati" value="<?php echo ln("registrati");?>" name="UserReg" /><? } ?>
    <?php if($pwdsend!="") { ?><div class="pwdSend-container"><input type="submit" class="theSubmit password-dimenticata" value="<?php echo ln("password dimenticata?");?>" name="pwdSend" /></div><? } ?> 
    <div class="rsPwdSend" style="display:none;">
      <div class="pwdSend-close"></div>
      <div class="pwdSend-istruzioni"><?php echo ln("Inserisci la User ID o la E-mail con cui ti sei registrato");?></div>
      <div class="loginarearis-username">User ID</div> <input type="text" class="theInput" name="sendUser" /> 
      <div class="loginarearis-password">E-mail</div> <input type="text" class="theInput" name="sendEmail" />
      <input type="submit" class="theSubmit btn btn-success" value="<?php echo ln("Invia");?>" name="pwdSendDo" />
    </div>
  </form>
  <?php
}

// ******************************************************************************************
function printUpdateProfilo($idusers){ 
  if(isAjaxPost()) return;
  
  $users=retRow("users",$idusers);
  $FormUserUpd = new rsForm("UserUpd","-1","");
  ?>
  <!-- Module 2A -->
  <div class="ez-wr rsUpdateProfilo">
    <div class="ez-fl ez-negmr ez-50 rsUpdateProfilo-left">
      <!-- Plain box -->
      <div class="ez-wr rsUpdateProfilo-content">
        <?php while (list($field, $row) = each($users)) {
          if(!($field>0) && $field!="id" && $field!="login" && $field!="codicecliente" && $field!="pwd" && $field!="ultimoaccesso" && $field!="ultimopwdmod" && $field!="isdisabled" && $field!="isreadonly" && $field!="isbackoffice" && $field!="datecreation") { 
            $retField=$FormUserUpd->retLabel($field);
            $value="";
            if($retField) {
              if(strpos($field,"_file")!==FALSE) {
                if(retFile($row,80,0)) $value="<img src='".retFile($row,80,0)."' />";
              } else {
                $value=$row;
              }
              if($value=="0000-00-00") $value="";
              ?>
              <!-- Module 2A -->
              <div class="ez-wr rsUpdateProfilo-row">
                <div class="ez-fl ez-negmr ez-50 rsUpdateProfilo-row-l">
                  <div class="ez-box"><?php echo $retField; ?>:</div>
                </div>
                <div class="ez-last ez-oh rsUpdateProfilo-row-r">
                  <div class="ez-box"><?php echo $value; ?></div>
                </div>
              </div>
            <? } ?>
          <? } ?>
        <? } ?>				 
      </div>
    </div>
    <div class="ez-last ez-oh rsUpdateProfilo-form">
      <div class="ez-box"><?php $FormUserUpd->_print(); ?></div>
    </div>
  </div><?
}

function printDocumenti($idusers,$anni="-1") {
  if(isAjaxPost()) return;
  
  global $conn;
  global $config_table_prefix;
  $objDocuments = new Documents;
  $objUtility = new Utility;
  
  $_SESSION['docRaggruppa']=$_POST['docRaggruppa'];  
  
  if($_SESSION['docRaggruppa']!="true") $anni="-1";
  if($_SESSION['docRaggruppa']=="true") $anni="";
  
  ?>
  
  <?php ob_start(); ?>
  <script type="text/javascript"> 
    $(document).ready(function()
    { 
      $("#documenti").css("display","");
      $("#doc_raggruppa").click(function() { 
        $("#doc_raggruppa_frm").submit(); 
      });
    });
  </script>
  <?php
  $this->g_jsCode.=ob_get_contents(); 
  ob_end_clean();
  ?>
  
  <div id="documenti" style="display:none;">
    <?php
    if($_GET['richiesta']>0) {
      $richiesta=$_GET['richiesta'];
      $rsForm = new rsForm("offerte");
      $offerte_rs=getTable("form_archivio_offerte","","id_archivio_richieste_offerta='".$richiesta."' AND idfornitore_hidden='".$idusers."'");
      
      if(count($offerte_rs)==0) {
        box(ln("Nessuna offerta con questo codice. Contattare l'assistenza tecnica."));
        return;
      }
      ?>
      <?php ob_start(); ?>
      <script type="text/javascript"> 
        $(document).ready(function()
        { 
          $("#dett_richiesta_<?=$richiesta?> a:contains('clicca qui per accedere alla tua area riservata')").remove();
          $("#dett_richiesta_<?=$richiesta?> a").attr("OnClick","return false");
        });
      </script>
      <?php
      $this->g_jsCode.=ob_get_contents(); 
      ob_end_clean();
                                             
      if($_POST['id_archivio_richieste_offerta_'.$richiesta]==$richiesta) { 
        if($offerte_rs[0]['ga_prezzo_cry']==0) {
          $sql="UPDATE ".$config_table_prefix."form_archivio_offerte SET ga_prezzo_cry='".$_POST['ga_prezzo']."',data_offerta=NOW(), descrizione='".addslashes($_POST['ga_offerta'])."' WHERE (id_archivio_richieste_offerta='".$richiesta."' AND idfornitore_hidden='".$idusers."')";
          mysql_query($sql);
          $esito=ln("Offerta inviata correttamente.");
        } else {
          $esito=ln("ATTENZIONE! Offerta già  inserita.");
        }
      }
      $offerte_rs=getTable("form_archivio_offerte","","id_archivio_richieste_offerta='".$richiesta."' AND idfornitore_hidden='".$idusers."'");

  		$offerte_rs2=getTable("archivio_richieste_offerta","","id='$richiesta'")
      ?>
      <p><b><?=$offerte_rs2[0]['oggetto']?></b></p>
      <div id="dett_richiesta_<?=$richiesta?>"><?=html_entity_decode($offerte_rs2[0]['messaggio'])?></div>
      <?php
      
      if($offerte_rs[0]['ga_prezzo_cry']!=0) {
        ?>
        <div style="color:red;border:1px gray dashed;">
          <?php echo ln("La tua quotazione per questa richiesta d'offerta è stata di"); ?> € <?php echo $offerte_rs[0]['ga_prezzo_cry']; ?>
          <br>
          <?php echo $offerte_rs[0]['descrizione']; ?>
        </div>
        <?
      } 
             
      if($offerte_rs[0]['ga_prezzo_cry']==0) { 
        $rsForm->_print("id_archivio_richieste_offerta_".$richiesta,$richiesta,"id_users",$idusers); 
      }
      if($esito!="") box($esito);
      return;                       
    }
    ?><div class="doc_raggruppa_frm_container"><form id="doc_raggruppa_frm" method="post" action=""><input type="hidden" name="documents" value="1" /><input type="checkbox" value="true" name="docRaggruppa" id="doc_raggruppa"  <?php if($_SESSION['docRaggruppa']=="true") echo "checked"; ?> /><label for="doc_raggruppa"><?php echo ln("Raggruppa per anno"); ?></label></form></div><?php
    $anno = (int) $_GET["anno"];

		$rs = $objDocuments->getAnni($conn, $idusers, false);
		if (count($rs) || $anni=="-1")
		{
			if($anni=="-1") {
        $rs=array();
        $rs[0]="-1";
      }
      ?>
      <ul class="anni">
				<?php
				while (list($key, $rowTmp) = each($rs))
				{
					ob_start(); ?>
          <script type="text/javascript"> 
          $(document).ready(function()
          { 
            $("#<?php echo $rowTmp["anno"]."anni"; ?>").click(
              function() {
                 if($("#<?php echo $rowTmp["anno"]."anni-tags"; ?>").css("display")=="none") $("#<?php echo $rowTmp["anno"]."anni-tags"; ?>").show("slow"); else $("#<?php echo $rowTmp["anno"]."anni-tags"; ?>").hide("slow");
              }
            ); 
          });
          </script>
          <?php
          $this->g_jsCode.=ob_get_contents(); 
          ob_end_clean();
          ?>
          
          <li>
						<?php if($anni=="") { ?>
            &raquo;&nbsp;<a href="" id="<?php echo $rowTmp["anno"]."anni"; ?>" onclick="return false"><?php echo $rowTmp["anno"] ?></a>
						<?php
						} else {
              ?>&nbsp;&nbsp;<?php  
            }
            //if ($anno == $rowTmp["anno"])
						//{
							$anno = $rowTmp["anno"];
              $rsTags = $objDocuments->tagsGetByAnno($conn, $anno, $idusers, false);
							if (count($rsTags))
							{
								$tmp_style="";
                if($anni=="") $tmp_style="display:none;";
                ?>
								<ul id="<?php echo $rowTmp["anno"]."anni-tags"; ?>" class="anni-tags" style="<?=$tmp_style?>">
									<?php
                  while (list($key, $rowTags) = each($rsTags))
									{
										?>
                    <li>
                    <?php
                    $idtags = $rowTags["id"];
      							if($idtags>0) {
                      ?>
  										<a href="" id="<?php echo $rowTags["id"]."tags".$anno; ?>" onclick="return false"><img src="<?php echo $objUtility->getPathResourcesStatic() ?>ico_folder.png" alt=""/><?php echo ln($rowTags["nome"]); ?></a>
                      <?php ob_start(); ?>
                      <script type="text/javascript"> 
                      $(document).ready(function()
                      { 
                        $("#<?php echo $rowTags["id"]."tags".$anno; ?>").click(
                          function() {
                             if($("#<?php echo $rowTags["id"]."files".$anno; ?>").css("display")=="none") $("#<?php echo $rowTags["id"]."files".$anno; ?>").show("slow"); else $("#<?php echo $rowTags["id"]."files".$anno; ?>").hide("slow");
                          }
                        ); 
                      });
                      </script>
                      <?php
                      $this->g_jsCode.=ob_get_contents(); 
                      ob_end_clean();
                      
                      $rs2 = $objDocuments->getList($conn, $idusers, $anno, $idtags, false);
        							if (count($rs2)) 
        							{
        								?>
        								<ul id="<?php echo $rowTags["id"]."files".$anno; ?>" class="files" style="display:none;">
        									<?php
        									while (list($key2, $row2) = each($rs2)) 
        									{ 
        										$tmpObj = retRow("oggetti",$row2["idoggetti"]);
        										
                            ?>
        										<li><?php $this->adminIco($row2["originalname"]) ?>&nbsp;<a href="<?php echo $objUtility->getPathBackoffice() ?>object_download.php?id=<?php echo $tmpObj["id"] ?>"><?php echo $tmpObj["originalname"] ?>&nbsp; (<?php echo $objUtility->getFileSizeKb($objUtility->getPathResourcesPrivateAbsolute() . $tmpObj["nome"].".".$tmpObj["ext"]) ?> Kb)</a></li>
        										<?php
        									}
        									?>
        								</ul>
        								<?php
        							}
      							} elseif($idtags==-1) {
      								$clr="";
                      $richiesta=$rowTags['richiesta'];
                      $rsForm = new rsForm("offerte");
                      $offerte_rs=getTable("form_archivio_offerte","","id_archivio_richieste_offerta='".$richiesta."' AND idfornitore_hidden='".$idusers."'");
                      
                      if($_POST['id_archivio_richieste_offerta_'.$richiesta]==$richiesta) { 
                        if($offerte_rs[0]['ga_prezzo_cry']==0) {
                          $sql="UPDATE ".$config_table_prefix."form_archivio_offerte SET ga_prezzo_cry='".$_POST['ga_prezzo']."',data_offerta=NOW(), descrizione='".addslashes($_POST['ga_offerta'])."' WHERE (id_archivio_richieste_offerta='".$richiesta."' AND idfornitore_hidden='".$idusers."')";
                          mysql_query($sql);
                          $esito="Offerta inviata correttamente.";
                        } else {
                          $esito="ATTENZIONE! Offerta già  inserita.";
                        }
                      }
                      $offerte_rs=getTable("form_archivio_offerte","","id_archivio_richieste_offerta='".$richiesta."' AND idfornitore_hidden='".$idusers."'");
                      if($offerte_rs[0]['ga_prezzo_cry']!=0) $clr="gray";else $clr="red"; 
                      ?>
  										<a href="" id="<?php echo $rowTags["richiesta"]."tags".$anno; ?>" onclick="return false" style="color:<?=$clr?>"><img src="<?php echo $objUtility->getPathResourcesStatic() ?>ico_email.png" alt=""/><?php echo $rowTags["nome"] ?></a>
                      <?php ob_start(); ?>
                      <script type="text/javascript"> 
                      $(document).ready(function()
                      {   
                        $("#dett_richiesta_<?=$richiesta?> a:contains('clicca qui per fare la tua offerta')").remove();
                        $("#dett_richiesta_<?=$richiesta?> a").attr("OnClick","return false");

                        if($("#documenti .richieste_<?=$anno?>").html()==null) {
                          $("#<?php echo $rowTmp["anno"]."anni-tags"; ?>").append("<li class='richieste_<?=$anno?>' ><a href='' onclick='return false'><img src='<?php echo $objUtility->getPathResourcesStatic() ?>ico_utenti.png' alt=''/><?php echo ln("Richieste quotazioni");?></a></li>");
                          $("#<?php echo $rowTags['richiesta'].'offerte'.$anno; ?>").parent().parent().find("a:contains('Richiesta quotazione')").each(function() {
                            $(this).parent().css("display","none");
                            $(this).remove();
                          });
                          
                          $("#documenti .richieste_<?=$anno?> a").click(
                            function() {
                              if($("#documenti .richieste_<?=$anno?>").attr("val")!=1) {
                                $("#documenti .richieste_<?=$anno?>").attr("val","1");
                                $("#documenti .richieste_<?=$anno?> ul.richieste_link_<?=$anno?>").show("slow");  
                              } else {
                                $("#documenti .richieste_<?=$anno?>").attr("val","0");
                                $("#documenti .richieste_<?=$anno?> ul").hide("slow");  
                              }
                            }
                          );
                        }
                        
                        $("#documenti .richieste_<?=$anno?>").append("<ul class='richieste_link_<?=$anno?>' style='display:none;'><li><a href='' id='<?php echo $rowTags['richiesta'].'tags'.$anno; ?>' onclick='return false' style='color:<?=$clr?>'><img src='<?php echo $objUtility->getPathResourcesStatic() ?>ico_email.png' alt=''/><?php echo $rowTags['nome'] ?></a>");
                        $("#documenti .richieste_<?=$anno?>").append($("#<?php echo $rowTags['richiesta'].'offerte'.$anno; ?>"));
                        $("#documenti .richieste_<?=$anno?>").append("</li></ul>");

                        $("#<?php echo $rowTags["richiesta"]."tags".$anno; ?>").click(
                          function() {
                             if($("#<?php echo $rowTags["richiesta"]."offerte".$anno; ?>").css("display")=="none") $("#<?php echo $rowTags["richiesta"]."offerte".$anno; ?>").show("slow"); else $("#<?php echo $rowTags["richiesta"]."offerte".$anno; ?>").hide("slow");
                          }
                        );
                      });
                      </script>
                      <?php
                      $this->g_jsCode.=ob_get_contents(); 
                      ob_end_clean();
                      ?>
                      
                      <ul id="<?php echo $rowTags["richiesta"]."offerte".$anno; ?>" class="files" style="display:none"><li>
      									<?php 
              					$offerte_rs2=getTable("archivio_richieste_offerta","","id='$richiesta'")
                        ?>
                        <p><b><?=$offerte_rs2[0]['oggetto']?></b></p>
                        <div id="dett_richiesta_<?=$richiesta?>"><?=html_entity_decode($offerte_rs2[0]['messaggio'])?></div>
                        <?php
                        
                        if($offerte_rs[0]['ga_prezzo_cry']!=0) {
                          ?>
                          <div style="color:red;border:1px gray dashed;">
                            <?php echo ln("La tua quotazione per questa richiesta d'offerta è stata di"); ?> € <?php echo $offerte_rs[0]['ga_prezzo_cry']; ?>
                            <br>
                            <?php echo $offerte_rs[0]['descrizione']; ?>
                          </div>
                          <?
                        } 
                        
                        if($offerte_rs[0]['ga_prezzo_cry']==0) { 
                          $rsForm->_print("id_archivio_richieste_offerta_".$richiesta,$richiesta,"id_users",$idusers); 
                        } 
                        ?>
      								</li></ul>
      								<?php    
                    }
      							?>
                    </li>
										<?php
									}
									?>
								</ul>
								<?php
							}
							else 
							{
                echo ln("Nessun documento disponibile."); ?>
								<?php ob_start(); ?>
                <script>         
								$("#doc_raggruppa_frm").parent().remove();
								</script>
                <?php
                $this->g_jsCode.=ob_get_contents(); 
                ob_end_clean();													
							}
						//}
						?>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
		else
		{
			echo ln("Nessun documento disponibile."); 
		}
		
    ?>
	</div>
  <?php 
  
  if($esito!="") box($esito); 

}

}
?>