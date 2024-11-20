<?php
session_start();
header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: Public");

require_once("_docroot.php");
require_once(SERVER_DOCROOT."logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$objMailing = new Mailing;
$objNewsletterUtenti = new NewsletterUtenti;
$conn = $objDb->connection($objConfig);

global $config_table_prefix;
$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."newsletter/newsletter.php");

$idusersList = $objUtility->sessionVarRead("newsletter_iduserslist");
if (!$idusersList)
{
	//header("Location: index.php");
	//exit();
}

$allowed=$_POST['cid'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<?php $objHtml->adminHtmlEditor() ?>

<script type="text/javascript">
function checkForm(theform)  {
	<?php $objJs->checkField("subject", "text", "OGGETTO", "subject") ?>
	return true;
}

function textCounter(field, countfield, maxlimit) {
	if (field.value.length > maxlimit) // if too long...trim it!
		field.value = field.value.substring(0, maxlimit);
		// otherwise, update 'characters left' counter
	else 
		countfield.value = field.value.length;
}

function Form_Validator(theForm) {                                                                   
  var checkOK = "0123456789-";
  var allValid = true;
  var validGroups = true;
  var decPoints = 0;
  var allNum = "";

  if (!allValid)
  {
    alert("Il campo \"Destinatario\" accetta solo numeri");
    theForm.To.focus();
    return (false);
  }

  if (theForm.Mes.value == "")
  {
    alert("Inserire un testo per il messaggio");
    theForm.Mes.focus();
    return (false);
  }

  if (theForm.Mes.value.length < 1)
  {
    alert("Inserire almeno un carattere nel campo \"Testo\" ");
    theForm.Mes.focus();
    return (false);
  }

  if (theForm.Mes.value.length > 160)
  {
    alert("Il messaggio può essere lungo al massimo 160 caratteri");
    theForm.Mes.focus();
    return (false);
  }

  var checkOK = "0123456789-";
  var checkStr = theForm.remLen.value;
  var allValid = true;
  var validGroups = true;
  var decPoints = 0;
  var allNum = "";
  for (i = 0;  i < checkStr.length;  i++)
  {
    ch = checkStr.charAt(i);
    for (j = 0;  j < checkOK.length;  j++)
      if (ch == checkOK.charAt(j))
        break;
    if (j == checkOK.length)
    {
      allValid = false;
      break;
    }
    allNum += ch;
  }
  if (!allValid)
  {
    alert("Inserire valori esatti");
    theForm.remLen.focus();
    return (false);
  }
  return (true);
}

$(document).ready(function() {
  var pdfOptions = {
    'container': '#fragment-0',
    'table': 'users'
  };
  
  var rsPdf=new rsPdfEditor(pdfOptions);
  rsPdf._print();
  
  $('#container-7').tabs({ fxAutoHeight: true });
  
  $('#templates').change(function() {
    if($(this).val()=="") return false;
    if(!confirm('Vuoi procedere con la sostituzione del contenuto corrente?')) return false;
    tinyMCE.get('testo').setContent($(this).val());
  });
  
  $('[name=NEWSLETTER-SEND]').click(function(){
    if(!confirm('Vuoi procedere con l\'invio?')) return false;
    if($("#subject").val()=="") {
      alert("inserire un valore per il campo oggetto");
      $("#subject").focus();
      return false;
    }
  });
  
  $('[name=SMS-SEND]').click(function(){
    if(!confirm('Vuoi procedere con l\'invio degli SMS?')) return false;
  });
  
  function rsNewsletter_successRequest(){
    alert('Invio effettuato.\nConsultare \'Report\' per maggiori informazioni ');
    $("div.send_mail_wait").remove();
    $("div.send_mail input").show();
  }
  
  function rsNewsletter_beforeSubmit(){
    $("div.send_mail input").hide();
    $("div.send_mail").prepend("<div class='send_mail_wait'><?php echo ln('Invio in corso...'); ?></div>");  
  }
  
  var options = { 
      target:        '#report-email',   // target element(s) to be updated with server response 
      beforeSubmit:   rsNewsletter_beforeSubmit,  // pre-submit callback 
      success:        rsNewsletter_successRequest  // post-submit callback 

      // other available options: 
      //url:       url         // override for form's 'action' attribute 
      //type:      type        // 'get' or 'post', override for form's 'method' attribute 
      //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
      //clearForm: true        // clear all form fields after successful submit 
      //resetForm: true        // reset the form after successful submit 

      // $.ajax options can be used here too, for example: 
      //timeout:   3000 
  }; 

  // bind form using 'ajaxForm' 
  $('#sendEmail').ajaxForm(options);
  
  function successRequestSMS(){
    alert('Invio effettuato.\nConsultare \'Report\' per maggiori informazioni ');
    $("div.send_sms_wait").remove();
    $("div.send_sms input").show();
  }
  
  function beforeSubmitSMS(){
    if($("#sendSms input[name='Tipo']:checked").length==0 && $("#sendSms input[name='Mit']").val()=="") {
      alert("<?php echo ln('Inserire un valore valido per il campo \'MITTENTE\''); ?>");
      $("#sendSms input[name='Mit']").focus();
      return false;
    }
    $("div.send_sms input").hide();
    $("div.send_sms").prepend("<div class='send_sms_wait'><?php echo ln('Invio in corso...'); ?></div>");  
  }
  
  var optionsSMS = { 
      target:        '#report-sms',   // target element(s) to be updated with server response 
      beforeSubmit:   beforeSubmitSMS,  // pre-submit callback 
      success:        successRequestSMS  // post-submit callback 

      // other available options: 
      //url:       url         // override for form's 'action' attribute 
      //type:      type        // 'get' or 'post', override for form's 'method' attribute 
      //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
      //clearForm: true        // clear all form fields after successful submit 
      //resetForm: true        // reset the form after successful submit 

      // $.ajax options can be used here too, for example: 
      //timeout:   3000 
  };

  // bind form using 'ajaxForm' 
  $('#sendSms').ajaxForm(optionsSMS);

  $("#ut_uomo").click( 
    function() {
      if($("#ut_donna").attr("checked")) $("#ut_donna").attr("checked", !$("#ut_uomo").attr("checked"));
    }
  );
  
  $("#ut_donna").click( 
    function() {
      if($("#ut_uomo").attr("checked")) $("#ut_uomo").attr("checked", !$("#ut_donna").attr("checked"));
    }
  );
  
  $("#ut_email").click( 
    function() {
      if($("#ut_email_senza").attr("checked")) $("#ut_email_senza").attr("checked", !$("#ut_email").attr("checked"));
    }
  );
  
  $("#ut_cell").click( 
    function() {
      if($("#ut_cell_senza").attr("checked")) $("#ut_cell_senza").attr("checked", !$("#ut_cell").attr("checked"));
    }
  );
  
  $("#ut_email_senza").click( 
    function() {
      if($("#ut_email").attr("checked")) $("#ut_email").attr("checked", !$("#ut_email_senza").attr("checked"));
    }
  );
  
  $("#ut_cell_senza").click( 
    function() {
      if($("#ut_cell").attr("checked")) $("#ut_cell").attr("checked", !$("#ut_cell_senza").attr("checked"));
    }
  );
      
}); 
</script>

<style>
  div.elemento .label {width: 90px;}
</style>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("Comunicazioni", "Invio",-1) ?>
			<div id="body">
        <form action="" id="frm2" name="frm2" method="post" enctype="multipart/form-data" />
          <?php 
          $chSel=$_POST['cid'];
          
          if(count($chSel)>0) {
            for($z=0;$z<count($chSel);$z++) { ?>
              <input type="hidden" name="cid[]" value="<?=$chSel[$z]?>" /><? 
            } 
            $objUtility->sessionVarUpdate("newsletter_iduserslist", implode(";", $chSel));
            $idusersList = $objUtility->sessionVarRead("newsletter_iduserslist"); 
        	}
          ?>
          <table id="newsletter-filtro" border="0">
            <tr>
              <th>Seleziona: </th>
              <td><div style="width:27px;"></div></td>
              <td><input type="checkbox" name="ut_email" id="ut_email" value="1" <?php if($_POST['ut_email']!="") echo "checked"; ?> /></td>
              <td>&nbsp;&nbsp;con e-mail</td>
              <td><div style="width:15px;"></div></td>
              
              <td><input type="checkbox" name="ut_email_senza" id="ut_email_senza" value="1" <?php if($_POST['ut_email_senza']!="") echo "checked"; ?> /></td>
              <td>&nbsp;&nbsp;senza e-mail</td>
              <td><div style="width:15px;"></div></td>
              
              <td><input type="checkbox" name="ut_cell" id="ut_cell" value="1" <?php if($_POST['ut_cell']!="") echo "checked"; ?> /></td>
              <td>&nbsp;&nbsp;con cellulare</td>
              <td><div style="width:15px;"></div></td>
              
              <td><input type="checkbox" name="ut_cell_senza" id="ut_cell_senza" value="1" <?php if($_POST['ut_cell_senza']!="") echo "checked"; ?> /></td>
              <td>&nbsp;&nbsp;senza cellulare</td>
              <td><div style="width:15px;"></div></td>
              
              <td><input type="checkbox" name="ut_compleanno" id="ut_compleanno" value="1" <?php if($_POST['ut_compleanno']!="") echo "checked"; ?> /></td>
              <td>&nbsp;&nbsp;che festeggiano oggi il compleanno</td>
              <td><div style="width:15px;"></div></td>
            </tr><tr>
              <td><div style="height:10px;"></div></td>
            </tr><tr>
              <th>Escludi: </th>
              <td><div style="width:25px;"></div></td>
              <td><input type="checkbox" name="ut_verdi" id="ut_verdi" value="1" <?php if($_POST['ut_verdi']!="") echo "checked"; ?> /></td>
              <td style="color:green;">&nbsp;&nbsp;utenti verdi</td>
              <td><div style="width:15px;"></div></td>
              
              <td><input type="checkbox" name="ut_gialli" id="ut_gialli" value="1" <?php if($_POST['ut_gialli']!="") echo "checked"; ?> /></td>
              <td  style="color:#FF9933;">&nbsp;&nbsp;utenti gialli</td>
              <td><div style="width:15px;"></div></td>
              
              <td><input type="checkbox" name="ut_rossi" id="ut_rossi" value="1" <?php if($_POST['ut_rossi']!="") echo "checked"; ?> /></td>
              <td  style="color:red;">&nbsp;&nbsp;utenti rossi</td>
              <td><div style="width:15px;"></div></td>
            </tr><tr>
              <td><div style="height:10px;"></div></td>
            </tr><tr>
              <th>Sesso: </th>
              <td><div style="width:25px;"></div></td>
              <td><input type="checkbox" name="ut_uomo" id="ut_uomo" value="1" <?php if($_POST['ut_uomo']!="") echo "checked"; ?> /></td>
              <td>&nbsp;&nbsp;uomo</td>
              <td><div style="width:15px;"></div></td>
              
              <td><input type="checkbox" name="ut_donna" id="ut_donna" value="1" <?php if($_POST['ut_donna']!="") echo "checked"; ?> /></td>
              <td>&nbsp;&nbsp;donna</td>
              <td><div style="width:15px;"></div></td>
              
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td><div style="width:15px;"></div></td>
            </tr><tr>
              <td><div style="height:10px;"></div></td>
            </tr><tr>
              <th>Età: </th>
              <td colspan="10">&nbsp;&nbsp;tra&nbsp;&nbsp;
                <input type="text" name="ut_eta1" id="ut_eta1" value="<?=$_POST['ut_eta1']?>" size="3" />
                &nbsp;&nbsp;e&nbsp;&nbsp;
                <input type="text" name="ut_eta2" id="ut_eta2" value="<?=$_POST['ut_eta2']?>" size="3" />
              </td>
            </tr><tr>
              <td><div style="height:10px;"></div></td>
            </tr><tr>
              <th>Nucleo familiare: </th>
              <td colspan="10">&nbsp;&nbsp;tra&nbsp;&nbsp;
                <input type="text" name="ut_nucleo1" id="ut_nucleo1" value="<?=$_POST['ut_nucleo1']?>" size="3" />
                &nbsp;&nbsp;e&nbsp;&nbsp;
                <input type="text" name="ut_nucleo2" id="ut_nucleo2" value="<?=$_POST['ut_nucleo2']?>" size="3" />
              </td>
            </tr><tr>
              <td><div style="height:10px;"></div></td>
            </tr><tr>
              <td colspan="11" style="padding-left:120px;">
                <input type="submit" name="ut_submit" id="ut_submit" value="applica" class="button" />
              </td>
            </tr>
          </table>
				</form>
				
        <div class="riepilogo-header">Utenti selezionati</div>
				<?php
				$arrUsers = explode(";", $idusersList);
        if(is_array($allowed)) {
          $arrUsers2=array();
          while (list($key, $row) = each($arrUsers)) {
            if(!(array_search($row, $allowed)===FALSE)) { 
              $tmpAdd=true;
              if($_POST['ut_uomo']!="") {
                $tmpUsr=retRow("users",$row); 
                if(trim($tmpUsr['id_sesso'])=="2") $tmpAdd=false;    
              }
              
              if($_POST['ut_donna']!="") {
                $tmpUsr=retRow("users",$row); 
                if(trim($tmpUsr['id_sesso'])=="1") $tmpAdd=false;    
              }
              
              if($_POST['ut_email']!="") {
                $tmpUsr=retRow("users",$row); 
                if(trim($tmpUsr['email'])=="") $tmpAdd=false;    
              }
              
              if($_POST['ut_email_senza']!="") {
                $tmpUsr=retRow("users",$row); 
                if(trim($tmpUsr['email'])!="") $tmpAdd=false;    
              }
              
              if($_POST['ut_cell']!="") {
                $tmpUsr=retRow("users",$row);
                if(trim($tmpUsr['cellulare'])=="") $tmpAdd=false;    
              }
              
              if($_POST['ut_cell_senza']!="") {
                $tmpUsr=retRow("users",$row);
                if(trim($tmpUsr['cellulare'])!="") $tmpAdd=false;    
              }
              
              if($_POST['ut_compleanno']!="") {
                $tmpUsr=retRow("users",$row);
                if(substr($tmpUsr['data_di_nascita'], 5, 6)!=substr(cdate(), 5, 6)) $tmpAdd=false;    
              }    
              
              if($_POST['ut_verdi']!="") {
                $tmpUsr=retRow("users",$row);
                $ultima_comunicazione=getTable("archivio_newsletter","data DESC","((destinatario='".$tmpUsr['email']."' OR destinatario='".$tmpUsr['cellulare']."') AND errori='0')");    
                $ultima_comunicazione=$ultima_comunicazione[0];
                 
                $cdate = cdate();
                $differenza_com=abs(strtotime($ultima_comunicazione['data']) - strtotime($cdate))/(86400);
                //if($differenza_com<7) $tmpAdd=false;
                //if($differenza_com>=7 && $differenza_com<30) $tmpAdd=false;
                if($differenza_com>=30) $tmpAdd=false;
              }
              
              if($_POST['ut_gialli']!="") {
                $tmpUsr=retRow("users",$row);
                $ultima_comunicazione=getTable("archivio_newsletter","data DESC","((destinatario='".$tmpUsr['email']."' OR destinatario='".$tmpUsr['cellulare']."') AND errori='0')");    
                $ultima_comunicazione=$ultima_comunicazione[0];
                
                $cdate = cdate();
                $differenza_com=abs(strtotime($ultima_comunicazione['data']) - strtotime($cdate))/(86400);
                //if($differenza_com<7) $tmpAdd=false;
                if($differenza_com>=7 && $differenza_com<30) $tmpAdd=false;
                //if($differenza_com>=30) $tmpAdd=false;
              }
              
              if($_POST['ut_rossi']!="") {
                $tmpUsr=retRow("users",$row);
                $ultima_comunicazione=getTable("archivio_newsletter","data DESC","((destinatario='".$tmpUsr['email']."' OR destinatario='".$tmpUsr['cellulare']."') AND errori='0')");    
                $ultima_comunicazione=$ultima_comunicazione[0];
                
                $cdate = cdate();
                $differenza_com=abs(strtotime($ultima_comunicazione['data']) - strtotime($cdate))/(86400);
                if($differenza_com<7) $tmpAdd=false;
                //if($differenza_com>=7 && $differenza_com<30) $tmpAdd=false;
                //if($differenza_com>=30) $tmpAdd=false;
              }
              
              if($_POST['ut_eta1']!="") {
                $ut_eta1=$_POST['ut_eta1'];
                $ut_eta2=$_POST['ut_eta2'];
                
                if($ut_eta2=="") $ut_eta2=($ut_eta1+1); 
                                
                $tmpUsr=getTable("users","","(id='$row' AND DATEDIFF(CURDATE(),data_di_nascita)>=".($ut_eta1*365)." AND  DATEDIFF(CURDATE(),data_di_nascita)<=".($ut_eta2*365)." )");
                if(count($tmpUsr)==0) $tmpAdd=false;
              }
              
              if($_POST['ut_nucleo1']!="") {
                $ut_nucleo1=$_POST['ut_nucleo1'];
                $ut_nucleo2=$_POST['ut_nucleo2'];
                
                if($ut_nucleo2=="") $ut_nucleo2=$ut_nucleo1; 
                                
                $tmpUsr=getTable("users","","(id='$row' AND nucleo_familiare>=".$ut_nucleo1." AND nucleo_familiare<=".$ut_nucleo2." )");
                if(count($tmpUsr)==0) $tmpAdd=false;
              }
              
              if($tmpAdd==true) array_push($arrUsers2, $row);
            }
  				}
  				$arrUsers=$arrUsers2;
  				$idusersList = implode(";", $arrUsers);
		      $objUtility->sessionVarUpdate("newsletter_iduserslist", $idusersList);
  			}
				
        if (is_array($arrUsers) && is_array($allowed)) 
				{
					?>
					<div class="riepilogo">
						<?php
						for ($i=0; $i<count($arrUsers); $i++) 
						{
							$idusers = $arrUsers[$i];
							if ($idusers)
							{
								$objNewsletterUtenti->setCurrentByID($idusers);
								
								if ($objNewsletterUtenti!=NULL)
								{
									?>
									<div class="item">
										<?php if($objNewsletterUtenti->Get("cognome")!="" && $objNewsletterUtenti->Get("nome")!="") { ?><div class="titolo">[<?php echo $i+1 ?>]&nbsp;<?php echo $objNewsletterUtenti->Get("cognome")." ".$objNewsletterUtenti->Get("nome"); ?></div><? } ?>
										<?php if($objNewsletterUtenti->Get("cognome")=="" && $objNewsletterUtenti->Get("nome")=="") { ?><div class="titolo">[<?php echo $i+1 ?>]&nbsp;<?php echo $objNewsletterUtenti->Get("ragionesociale"); ?></div><? } ?>
                    <?php 
                      if($objNewsletterUtenti->Get("email")!="") {
                        $tmp_email = $objNewsletterUtenti->Get("email");
                        $ultima_email=getTable("archivio_newsletter","data DESC","(destinatario='$tmp_email' AND EMAIL='1' AND errori='0')");
                        $ultima_email=$ultima_email[0];
                        
                        if($ultima_email['data']!="") {
                          $cdate = cdate();
                          $differenza_email=abs(strtotime($ultima_email['data']) - strtotime($cdate))/(86400);
                          if($differenza_email<7) $color_email="red";
                          if($differenza_email>=7 && $differenza_email<30) $color_email="#FF9933";
                          if($differenza_email>=30) $color_email="green";
                        } 
                      }
                      
                      if($objNewsletterUtenti->Get("cellulare")!="") {
                        $tmp_cell = $objNewsletterUtenti->Get("cellulare");
                        $ultimo_sms=getTable("archivio_newsletter","data DESC","(destinatario='$tmp_cell' AND SMS='1' AND errori='0')");
                        $ultimo_sms=$ultimo_sms[0];

                        if($ultimo_sms['data']!="") {
                          $cdate = cdate();
                          $differenza_sms=abs(strtotime($ultimo_sms['data']) - strtotime($cdate))/(86400);
                          if($differenza_sms<7) $color_sms="red";
                          if($differenza_sms>=7 && $differenza_sms<30) $color_sms="#FF9933";
                          if($differenza_sms>=30) $color_sms="green"; 
                        }  
                      }
                    ?>
                    <?php if($ultima_email['data']!="") { ?><div class="detail" style="color:<?=$color_email?>;">Ultima email inviata il <?php echo dataITA($ultima_email['data']); ?> (<?php if(round($differenza_email)>0) echo round($differenza_email)." giorni fa";else echo "oggi"; ?>)</div><? } ?>
                    <?php if($ultimo_sms['data']!="") { ?><div class="detail" style="color:<?=$color_sms?>;">Ultimo sms inviato il <?php echo dataITA($ultimo_sms['data']); ?> (<?php if(round($differenza_sms)>0) echo round($differenza_sms)." giorni fa";else echo "oggi"; ?>)</div><? } ?>
                    <?php if($objNewsletterUtenti->Get("email")!="") { ?><div class="detail">Email: <span class="value"><?php echo $objNewsletterUtenti->Get("email") ?></span></div><? } ?>
										<?php if($objNewsletterUtenti->Get("cellulare")!="") { ?><div class="detail">Cell: <span class="value"><?php echo $objNewsletterUtenti->Get("cellulare") ?></span></div><? } ?>
									</div>
									<?php
									$ultima_email="";
									$ultimo_sms="";
								}
							}
						}
						?>
					</div>
					<?php
				}
				?>
				<?php if (is_array($arrUsers) && is_array($allowed)) { ?>
        <div id="container-7">
            <ul>
                <li><a href="#fragment-0"><span>Stampa lettera</span></a></li>
                <li><a href="#fragment-1"><span>Scarica CSV</span></a></li>
                <li><a href="#fragment-2"><span>Invio e-mail</span></a></li>
                <li><a href="#fragment-3"><span>Invio sms</span></a></li>
                <li><a href="#fragment-4"><span>Report</span></a></li>
            </ul>
            <div id="fragment-0"></div>
            <div id="fragment-1">
              <div class="inputdata">
                <form action="action.php" id="downloadCSV" name="downloadCSV" method="post" />
        					<input type="hidden" name="act_CSV-SEND-DO" value="1" />
        					<div class="elemento">
        						<div class="label">&nbsp;</div>
                    <div class="value send_csv"><input type="submit" name="CSV-SEND" value="Download CSV" class="btn" /></div>
        					</div>
      					</form>
      				</div>
            </div>
            <div id="fragment-2">
                <div class="inputdata">
                  <form action="action.php" id="sendEmail" name="sendEmail" method="post" enctype="multipart/form-data" onsubmit="return checkForm(this)"/>
          					<input type="hidden" name="act_NEWSLETTER-SEND-DO" value="1" />
                    <div class="elemento">
          						<div class="label"><label for="subject">oggetto</label> * </div>
          						<div class="value"><input type="text" name="subject" id="subject" maxlength="128" class="text" style="width:670px;"/></div>
          					</div>
          					<div class="elemento">
        						<div class="label"><label for="subject">Modello</label></div>
        						<div class="value">
                      <select id="templates" name="templates">
                         <option  value="">&nbsp;</option>
                         <?php 
                         $tm=getTable("editor_templates","Ordinamento ASC","attivo='1'"); 
                         while (list($tm_key, $tm_row) = each($tm)) {
                            ?><option  value="<?php echo htmlentities($tm_row['testo_editor']); ?>"><?=$tm_row['nome']?></option><?
                         }
                         ?>
                      </select>
                    </div>
        					</div>
                    <div class="elemento">
          						<div class="label"><label for="testo">testo </label> </div>
          						<div class="value">
          							<textarea name="testo" id="testo" rows="15" cols="40" class="textEditor"></textarea>
          						</div>
          					</div>
          					<div class="elemento">
          						<div class="label"><label for="file">allegato </label></div>
          						<div class="value">
          							<input type="file" name="file" maxlength="100" class="file" style="width:675px;"/>
          						</div>
          					</div>
          					<div class="elemento">
          						<div class="label">&nbsp;</div>
                      <div class="value send_mail"><input type="submit" name="NEWSLETTER-SEND" value="Invia Mail" class="btn" /></div>
          					</div>
        					</form>
        				</div>
            </div>
            <div id="fragment-3">
            <div class="inputdata">
              <form action="action.php" id="sendSms" name="sendSms" method="post" onsubmit="return Form_Validator(this)" />
      					<input type="hidden" name="act_SMS-SEND-DO" value="1" />
                <div class="elemento">
      						<div class="label"><label for="subject">mittente </label> </div>
      						<div class="value"><input type="text" name="Mit" size="20"></div>
      					</div>
      					<div class="elemento">
      						<div class="label">mittente anonimo</div>
                  <input type="checkbox" name="Tipo" value="true">
      					</div>
      					<div class="elemento">
      						<div class="label"><label for="file">testo </label></div>
      						<div class="value">
                    <textarea name="Mes" rows="4" cols="40" onKeyDown="textCounter(this.form.Mes,this.form.remLen,160);" onKeyUp="textCounter(this.form.Mes,this.form.remLen,160);"></textarea>
      						  <input name="remLen" size=3 maxlength=3 value="0">
                  </div>
      					</div>
      					<div class="elemento">
      						<div class="label">&nbsp;</div>
                  <div class="value send_sms"><input type="submit" name="SMS-SEND" value="Invia Sms" class="btn" /></div>
      					</div>
      					<input type="hidden" name="Prefisso" value="+39">
    					</form>
    				</div>
            </div>
            <div id="fragment-4">
              <div id="report-email" style="border-bottom:1px gray dashed;padding-bottom:5px;"></div>
              <div id="report-sms" style="padding-top:5px;"></div>
            </div>
        </div>
        <? } ?>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>
