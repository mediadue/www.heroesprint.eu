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
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);

$objUtility->getAction($strAct, $intId);

switch ($strAct) {
	case "OGGETTI-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idoggetti", $intId);
		header ("Location: oggetti_insupd.php");
		break;

	case "OGGETTI-DEL-DO":
		$strError = "";
		$objObjects->delete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("oggetti.php", $strEsito, "");
		break;

	case "OGGETTI-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idoggetti");

		$nome = $_POST["nome"];

		$strUnique = $objUtility->getFilenameUnique();
		$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
		$strDestFileTmp = "temp";

		// 
		$isUploadOk = false;
		$strDestFile = $strUnique;
		if ($_FILES["oggetto"]["name"]) {
			$strExt = $objUtility->getExtFromMime($_FILES["oggetto"]["type"]);
//			if ($strExt == "jpg") {
				$isUploadOk = move_uploaded_file($_FILES["oggetto"]["tmp_name"], $strDestDir . $strDestFile . "." . $strExt);
//			}
			if ($isUploadOk) {
				chmod($strDestDir . $strDestFile . "." . $strExt, 0644);
				$strOggettoPath = $strDestFile . "." . $strExt;
				$strOggettoExt = $strExt;
				$strOggettoOriginalname = $_FILES["oggetto"]["name"];
			}
		}

		//
		$strError = "";
		switch ($action) {
			case "upd":
				$objObjects->update($conn, $id, $nome, $strError);
				break;
		}
		if ($isUploadOk) {
			$objObjects->updateImage($conn, $id, $strOggettoPath, $strOggettoExt, $strOggettoOriginalname, $strError);
		}
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("oggetti.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("oggetti.php", $strEsito, "");
		}
		break;
}
?>