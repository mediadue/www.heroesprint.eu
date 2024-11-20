<?php
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

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."gestione_acquisti.invio_richieste/newsletter.php");

$idusersList = $objUtility->sessionVarRead("ga_iduserslist");
if (!$idusersList)
{
	header("Location: index.php");
	exit();
}

$allowed=$HTTP_POST_VARS['cid'];
$prodotto=$_POST['prodotto'];
$email=$_POST['email'];

if($prodotto!="") $prs=getTable("ga_prodotti","","id='$prodotto'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<?php $objHtml->adminHtmlEditor() ?>
<script type="text/javascript">
<!--
function checkForm(theform) 
{
	<?php $objJs->checkField("subject", "text", "OGGETTO", "subject") ?>
	return true;
}

function select_onclick(id){
  currentElement = document.createElement("input");
  currentElement.setAttribute("type", "hidden");
  currentElement.setAttribute("name", "prodotto");
  currentElement.setAttribute("value", id);
  document.refresh.appendChild(currentElement);
  
  currentElement = document.createElement("input");
  currentElement.setAttribute("type", "hidden");
  currentElement.setAttribute("name", "email");
  currentElement.setAttribute("value", "<?=$email?>");
  document.refresh.appendChild(currentElement);
  
  document.refresh.submit();
  return true;
}

function select_onclick2(id){
  currentElement = document.createElement("input");
  currentElement.setAttribute("type", "hidden");
  currentElement.setAttribute("name", "prodotto");
  currentElement.setAttribute("value", "<?=$prodotto?>");
  document.refresh.appendChild(currentElement);
  
  currentElement = document.createElement("input");
  currentElement.setAttribute("type", "hidden");
  currentElement.setAttribute("name", "email");
  currentElement.setAttribute("value", id);
  document.refresh.appendChild(currentElement);
  
  document.refresh.submit();
  return true;
}
//-->
</script>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("Acquisti", "Invio richiesta d'offerta a fornitori","1") ?>
			<div id="body">
				<div class="riepilogo-header">Utenti selezionati</div>
				<?php
				$arrUsers = explode(";", $idusersList);
				
        if(is_array($allowed)) {
          $arrUsers2=array();
          while (list($key, $row) = each($arrUsers)) {
            if(!(array_search($row, $allowed)===FALSE)) array_push($arrUsers2, $row);
  				}
  				$arrUsers=$arrUsers2;
  				$idusersList = implode(";", $arrUsers);
		      $objUtility->sessionVarUpdate("ga_iduserslist", $idusersList);
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
                    <?php if($ultima_email['data']!="") { ?><div class="detail" style="color:<?=$color_email?>;">Ultima email inviata il <?php echo dataITA($ultima_email['data']); ?> (<?=$differenza_email?> giorni fa)</div><? } ?>
                    <?php if($ultimo_sms['data']!="") { ?><div class="detail" style="color:<?=$color_sms?>;">Ultimo sms inviato il <?php echo dataITA($ultimo_sms['data']); ?> (<?=$differenza_sms?> giorni fa)</div><? } ?>
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
        <div class="inputdata">
          <form action="action.php" id="frm" name="frm" method="post" enctype="multipart/form-data" onsubmit="return checkForm(this)"/>
					<div class="elemento">
						<div class="label"><label for="subject">Prodotto</label></div>
  						<div class="value">
  						  <?php comboBox("ga_prodotti","codice","nome",$prodotto,"","select_onclick(this.options[this.selectedIndex].value);"); ?>
              </div>
					</div>
					<div class="elemento">
						<div class="label"><label for="subject">modello e-mail</label></div>
  						<div class="value">
  						  <?php comboBox("ga_testi_email","nome","",$email,"","select_onclick2(this.options[this.selectedIndex].value);",$echoId="",$nome="",$where="attivo='1'"); ?>
              </div>
					</div>
          <div class="elemento">
						<div class="label"><label for="subject">oggetto</label> * </div>
						<div class="value"><input type="text" name="subject" id="subject" maxlength="128" class="text" value="<?php if(trim($prs[0]['oggetto'])!="") {echo $prs[0]['oggetto'];} else { echo "Richiesta quotazione"; } ?>" /></div>
					</div>
					<div class="elemento">
						<div class="label"><label for="testo">testo </label> </div>
						<div class="value">
							<?php 
                $rs=getTable("ga_testi_email","","id='$email'");
                $rs=$rs[0];
              ?>
              <textarea name="testo" id="testo" rows="15" cols="40" class="textEditor"><?=tinybug($rs['prima_parte_editor'])."<table border='1' cellpadding='20' style='margin:10px;'><tr><th>cod. articolo</th><th>descrizione articolo</th><th>u.m.</th><th>quantità</th></tr><tr><td>".$prs[0]['codice']."</td><td>".$prs[0]['descrizione']."</td><td>".$prs[0]['unità_di_misura']."</td><td>".$prs[0]['quantità']."</td></tr></table>".tinybug($prs[0]['messaggio_editor'])."<br>".tinybug($rs['seconda_parte_editor'])?></textarea>
						</div>
					</div>
					<div class="elemento">
						<div class="label"><label for="file">allegato </label></div>
						<div class="value">
							<input type="file" name="file" maxlength="100" class="file"/>
						</div>
					</div>
					<div class="elemento">
						<div class="label"><label for="file">&nbsp;</label></div>
            <div class="value"><input type="submit" name="act_NEWSLETTER-SEND-DO" value="Invia Mail" class="btn" onclick="return confirm('Confermi l\'invio?');" /></div>
					</div>
					<input type="hidden" name="prodotto" value="<?=$prodotto?>" />
          </form>
					
					<form action="ga_mail.php" id="frm" name="refresh" method="post" enctype="multipart/form-data" />
					   <?php while (list($key, $row) = each($allowed)) { ?>
                <input type="hidden" name="cid[]" value="<?=$row?>" />
             <? } ?>
					</form>
				</div>
				<? } ?>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>
