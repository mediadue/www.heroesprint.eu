<?php
session_start();

header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");

require_once("_docroot.php");
require_once(SERVER_DOCROOT."/logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$objMailing = new Mailing;
$conn = $objDb->connection($objConfig);

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objUtility->getAction($strAct, $intId);
$id = (int) $_POST["id"]; 

switch ($strAct) {

	// ******************************************************************************************
	// UTENTI

	case "UTENTI-PAGE-GOTO":
		header ("Location: utenti.php?page=" . $intId);
		break;

	case "UTENTI-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idutenti", $intId);
		header ("Location: utenti_insupd.php");
		break;

	case "UTENTI-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		header ("Location: utenti_insupd.php");
		break;

	case "UTENTI-DEL-DO":
		$strError = "";
		$objUsers->delete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("utenti.php", $strEsito, "");
		break;

  case "USER-EXIST":
    global $config_table_prefix;
    $action = strtolower($objUtility->sessionVarRead("action"));
    $id = $objUtility->sessionVarRead("idutenti");
		if($action=="ins") $id="";
		
    $username = $_POST["username_ver"];
    $sql="SELECT login FROM ".$config_table_prefix."users WHERE id<>'$id' AND login='$username'";
    $res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
      ?>false<?
    } else {
      ?>true<?  
    }
    break;

  case "DELIMG-DO":
    $action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idutenti");
    $usr=retRow("users",$id);
    $immagine=retFileAbsolute($usr['immagine_file']);
    unlink($immagine);
    $sql="DELETE FROM `".$config_table_prefix."oggetti` WHERE id='".$usr['immagine_file']."'";
    mysql_query($sql);
    
    if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("utenti.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("utenti.php", $strEsito, "");
		}
    
    break;
  
  case "UTENTI-INSUPD-DO":
    $action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idutenti");

		$username = $_POST["username"];
    $pwd = $_POST["password"];
		$ragionesociale = $_POST["ragionesociale"];
		$partitaiva = $_POST["partitaiva"];
		$codicefiscale = $_POST["codicefiscale"];
		$nome = $_POST["nome"];
		$cognome = $_POST["cognome"];
		$indirizzo = $_POST["indirizzo"];
		$citta = $_POST["localita"];
		$comune=$_POST["citta"];
		$regione_estera=$_POST["regione_estera"];
    $cap = $_POST["cap"];
		$provincia = $_POST["provincia"];
		$nazione = $_POST["nazione"];
		$telefono = $_POST["telefono"];
		$cellulare = $_POST["cellulare"];
		$fax = $_POST["fax"];
		$email = $_POST["email"];
		$sito = $_POST["sito"];
    $note = $_POST["note"];
		
		$data_di_nascita = $_POST["annodata_di_nascita"]."-".$_POST["mesedata_di_nascita"]."-".$_POST["giornodata_di_nascita"];
		$provincia_di_nascita = $_POST["provincia_di_nascita"];
		$sesso = $_POST["sesso"];
		$professione = $_POST["professione"];
		$stato_civile = $_POST["stato_civile"];
		$nato_a = $_POST["nato_a"];
		$hobby1 = $_POST["hobby1"];
		$hobby2 = $_POST["hobby2"];
		$hobby3 = $_POST["hobby3"];
		$regione = $_POST["regione"];
		$titolo_di_studio = $_POST["titolo_di_studio"];
		$nucleo_familiare = $_POST["nucleo_familiare"];
    
    $isdisabled = ($_POST["isdisabled"]=="1") ? 1 : 0;
		$isbackoffice = ($_POST["isbackoffice"]=="1") ? 1 : 0;
		$activationcode = "";
		
    $immagine=$_FILES['immagine'];
		
		$img=false; 
    if(isset($immagine)) {
      $target_path = $objUtility->getPathResourcesPrivateAbsolute();
      $ext=explode(".", basename($immagine['name']));
      $ext=array_reverse($ext);
      $ext=$ext[0];
      $target_name = $objUtility->getFilenameUnique();
      $target_path = $target_path . $target_name . ".$ext";

      if(move_uploaded_file($immagine['tmp_name'], $target_path)) {
        $sql="INSERT INTO `".$config_table_prefix."oggetti` ( nome, path, originalname, ext, isprivate ) VALUES ( '$target_name', '".$objUtility->getPathResourcesPrivateAbsolute()."', '".basename($immagine['name'])."', '$ext', 1 )";    
        mysql_query($sql);
        $img=mysql_insert_id();
      }
    }
     
    $strError = "";
		switch ($action) {
			case "ins":
				$objUsers->insert($conn, $id, $img, $username, $pwd, $ragionesociale, $partitaiva, $codicefiscale, $nome, $cognome, $indirizzo, $citta, $cap, $provincia, $nazione, $comune, $regione_estera, $telefono, $cellulare, $fax, $email, $sito, $note, $isdisabled, $isbackoffice, $activationcode, $data_di_nascita, $provincia_di_nascita, $sesso, $professione, $stato_civile, $nato_a, $hobby1, $hobby2, $hobby3, $regione, $titolo_di_studio, $nucleo_familiare, $strError);
				if($email!="" && $_POST['issendpwd']=="1") $objMailing->mmail($email,$objConfig->get("email-from"),"Dati area riservata","Gentile Utente, di seguito i dati per accedere all'area riservata del sito http://".$_SERVER['SERVER_NAME'].":<br><br>username: $username<br>password: $pwd<br><br>Cordiali saluti.","","","");
        $rs=getTable("roles","","nome='default'");
        $objUsers->usersRolesIns($conn, $id, $rs[0]['id'],$err);
        break;
			case "upd":
        $objUsers->update($conn, $id, $img, $username, $pwd, $ragionesociale, $partitaiva, $codicefiscale, $nome, $cognome, $indirizzo, $citta, $cap, $provincia, $nazione, $comune, $regione_estera, $telefono, $cellulare, $fax, $email, $sito, $note, $isdisabled, $isbackoffice, $activationcode, $data_di_nascita, $provincia_di_nascita, $sesso, $professione, $stato_civile, $nato_a, $hobby1, $hobby2, $hobby3, $regione, $titolo_di_studio, $nucleo_familiare, $strError);
        if($email!="" && $_POST['issendpwd']=="1") $objMailing->mmail($email,$objConfig->get("email-from"),"Modifica dei dati d'accesso","Gentile Utente, di seguito i nuovi dati per accedere all'area riservata del sito http://".$_SERVER['SERVER_NAME'].":<br><br>username: $username<br>password: $pwd<br><br>Cordiali saluti.","","","");
        break;
		}
		
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("utenti.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("utenti.php", $strEsito, "");
		}
		break;

	case "UTENTI-ROLES-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idutenti", $intId);
		header ("Location: utenti_roles.php");
		break;
		
	case "UTENTI-EMAIL-GOTO":
		?>
    <form name="frm" id="frm" action="<?php echo $objUtility->getPathBackofficeAdmin(); ?>newsletter/newsletter_mail.php" method="post">
      <input type="hidden" class="check" name="cid[]" value="<?=$intId?>" />
    	<?php $objUtility->sessionVarUpdate("newsletter_iduserslist", $intId);?>
  	</form>
  	<script>setTimeout("document.getElementById('frm').submit()",100);</script>
  	<?php
		break;

	case "UTENTI-ROLES-DEL-DO":
		$errorMsg = "";
		$idusers = $objUtility->sessionVarRead("idutenti");
		$idroles = $intId;
		$objUsers->usersRolesDelete($conn, $idusers, $idroles, $errorMsg);
		if ($errorMsg) {
			$esitoMsg = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$esitoMsg = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("utenti_roles.php", $esitoMsg, "");
		break;

	case "UTENTI-ROLES-INS-GOTO":
		header ("Location: utenti_roles_insupd.php");
		break;

	case "UTENTI-ROLES-INS-DO":
		$idusers = $objUtility->sessionVarRead("idutenti");

		$strError = "";

		$tot = $_POST["id_tot"];
		for ($i=1; $i<=$tot; $i++) {
			$id = $_POST["id_" . $i];
			if ($id) 
				$objUsers->usersRolesIns($conn, $idusers, $id, $strError);
		}

		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("utenti_roles.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("utenti_roles.php", $strEsito, "");
		}
		break;

	// ******************************************************************************************
	// ROLES

	case "ROLES-PAGE-GOTO":
		header ("Location: roles.php?page=" . $intId);
		break;

	case "ROLES-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idroles", $intId);
		header ("Location: roles_insupd.php");
		break;

	case "ROLES-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		header ("Location: roles_insupd.php");
		break;

	case "ROLES-DEL-DO":
		$strError = "";
		$objUsers->rolesDelete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("roles.php", $strEsito, "");
		break;

	case "ROLES-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idroles");

		$nome = $_POST["nome"];
		$issystem = $_POST["issystem"];

		$strError = "";
		switch ($action) {
			case "ins":
				$objUsers->rolesInsert($conn, $id, $nome, $issystem, $strError);
				break;
			case "upd":
				$objUsers->rolesUpdate($conn, $id, $nome, $issystem, $strError);
				break;
		}
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("roles.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("roles.php", $strEsito, "");
		}
		break;

	case "ROLES-USERS-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idroles", $intId);
		header ("Location: roles_users.php");
		break;

	case "ROLES-USERS-DEL-DO":
		$errorMsg = "";
		$idroles = $objUtility->sessionVarRead("idroles");
		$idusers = $intId;
		$objUsers->usersRolesDelete($conn, $idusers, $idroles, $errorMsg);
		if ($errorMsg) {
			$esitoMsg = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$esitoMsg = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("roles_users.php", $esitoMsg, "");
		break;

	case "ROLES-UTENTI-INS-GOTO":
		$objUtility->sessionVarUpdate("idutenti", $intId);
		header ("Location: utenti_roles_insupd.php");
		break;

	case "ROLES-MENU-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idroles", $intId);
		header ("Location: roles_menu.php");
		break;

	case "ROLES-MENU-DO":
		$idroles = $objUtility->sessionVarRead("idroles");

		$strError = "";
		$objUsers->rolesMenuDelete($conn, $idroles, $strError);

		$tot = $_POST["id_tot"];
		for ($i=1; $i<=$tot; $i++) {
			$id = $_POST["id_" . $i];
			if ($id) {
				$objUsers->rolesMenuIns($conn, $idroles, $id, $strError);
			}
		}

		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("roles.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("roles.php", $strEsito, "");
		}
		break;

}
?>