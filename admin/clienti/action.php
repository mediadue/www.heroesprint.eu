<?php



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
$objClienti = new Clienti;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);

$objUtility->getAction($strAct, $intId);
$id = (int) $_POST["id"];

switch ($strAct) {

	// ******************************************************************************************
	// CLIENTI

	case "CLIENTI-PAGE-GOTO":
		header ("Location: clienti.php?page=" . $intId);
		break;

	case "CLIENTI-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idutenti", $intId);
		header ("Location: clienti_insupd.php");
		break;

	case "CLIENTI-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		header ("Location: clienti_insupd.php");
		break;

	case "CLIENTI-DEL-DO":
		$strError = "";
		$objClienti->delete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("clienti.php", $strEsito, "");
		break;

	case "CLIENTI-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idutenti");

		$username = $_POST["username"];
		$pwd = $_POST["password"];
		$codicecliente = $_POST["codicecliente"];
		$ragionesociale = $_POST["ragionesociale"];
		$partitaiva = $_POST["partitaiva"];
		$codicefiscale = $_POST["codicefiscale"];
		$indirizzo = $_POST["indirizzo"];
		$citta = $_POST["citta"];
		$cap = $_POST["cap"];
		$provincia = $_POST["provincia"];
		$nazione = $_POST["nazione"];
		$telefono = $_POST["telefono"];
		$fax = $_POST["fax"];
		$email = $_POST["email"];
		$note = $_POST["note"];
		$isdisabled = ($_POST["isdisabled"]=="1") ? 1 : 0;
		$issendpwd = ($_POST["issendpwd"]=="1") ? 1 : 0;

		//$isbackoffice = ($_POST["isbackoffice"]=="1") ? 1 : 0;
		$isbackoffice = 0;

		$strError = "";
		switch ($action) {
			case "ins":
				$idroleAreariservata = $objConfig->get("role-areariservata");
				$objClienti->insert($conn, $idroleAreariservata, $id, $username, $pwd, $codicecliente, $ragionesociale, $partitaiva, $codicefiscale, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $strError);
				break;
			case "upd":
				$objClienti->update($conn, $id, $username, $pwd, $codicecliente, $ragionesociale, $partitaiva, $codicefiscale, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $isdisabled, $isbackoffice, $activationcode, $strError);
				break;
		}
		if ($issendpwd)
		{
			$objClienti->sendMailPwd($conn, $id, $pwd, $strError);
		}
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/><br/>".$strError;
			$objHtml->adminPageRedirect("clienti.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("clienti.php", $strEsito, "");
		}
		break;

}
?>