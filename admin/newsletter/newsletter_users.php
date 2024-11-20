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
$objNewsletterGruppi = new NewsletterGruppi;
$objNewsletterUtenti = new NewsletterUtenti;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."newsletter/newsletter.php");

//recupero l'elenco degli utenti selezionati
$arrId = array();
$arrEmail = array();
$arrRagionesociale = array();
$tot = $_POST["id_tot"];
for ($i=1; $i<=$tot; $i++) 
{
	$idroles = $_POST["id_" . $i];
  if ($idroles!="") 
	{
		$objNewsletterGruppi->setCurrentByID($idroles);
    $rsUsers = $objNewsletterGruppi->getUserList();
    
		if (count($rsUsers))
		{
			while (list($key, $rowUsers) = each($rsUsers))
			{
        $rs=getTable("users","","id='".$rowUsers["idusers"]."'");
        $rs=$objUsers->getGestione($_SESSION["user_id"],$rs,"users");
        if(count($rs)==1) {
          $objNewsletterUtenti->setCurrentByID($rowUsers["idusers"]);
          
          if ($objNewsletterUtenti!=NULL) {
						$is_add=FALSE;
            if($is_add==FALSE) {
              array_push($arrId, $objNewsletterUtenti->Get("id"));
  						array_push($arrEmail, $objNewsletterUtenti->Get("email"));
  						if($objNewsletterUtenti->Get("cognome")!="" && $objNewsletterUtenti->Get("nome")!="") {
                array_push($arrRagionesociale, $objNewsletterUtenti->Get("cognome")." ".$objNewsletterUtenti->Get("nome"));
  						}elseif($objNewsletterUtenti->Get("cognome")=="" && $objNewsletterUtenti->Get("nome")=="" && $objNewsletterUtenti->Get("ragionesociale")!="") {
                array_push($arrRagionesociale,$objNewsletterUtenti->Get("ragionesociale"));  
						  }elseif($objNewsletterUtenti->Get("cognome")!="" && $objNewsletterUtenti->Get("nome")=="" && $objNewsletterUtenti->Get("ragionesociale")=="") {
                array_push($arrRagionesociale,$objNewsletterUtenti->Get("cognome"));
						  }elseif($objNewsletterUtenti->Get("cognome")=="" && $objNewsletterUtenti->Get("nome")!="" && $objNewsletterUtenti->Get("ragionesociale")=="") {
                array_push($arrRagionesociale,$objNewsletterUtenti->Get("nome"));
              }else{
                array_push($arrRagionesociale,"");  
              }
            }
  				}
				}
			}
		}	
	}
}
if (!count($arrId)) { ?>
	<p class="error">Attenzione!<br/><br/>Nessuna utente selezionato</p>
<? } else { ?>
	<form action="newsletter_mail.php" method="post">
  <div class="riepilogo-header">Seleziona utenti</div>
	<div class="riepilogo">
		<?php
    array_multisort($arrRagionesociale, SORT_ASC, $arrEmail, SORT_ASC, $arrId, SORT_ASC);
		for ($i=0; $i<count($arrId); $i++)
		{
			?>
			<div class="item">
				<div class="titolo"><input type="checkbox" class="check" name="cid[]" value="<?=$arrId[$i]?>" checked />[<?php echo $i+1 ?>]&nbsp;<?php echo $arrRagionesociale[$i] ?></div>
				<div class="detail">Email: <span class="value"><?php echo $arrEmail[$i] ?></span></div>
			</div>
			<?php
		}
		$idusersList = implode(";", $arrId);
		$objUtility->sessionVarUpdate("newsletter_iduserslist", $idusersList);
		?>
	</div>
	
	<div class="inputdata">
		<div class="elemento">
			<div class="value"><input type="submit" value="avanti" class="btn" /></div>
		</div>
	</div>
	</form>
	<?php

}