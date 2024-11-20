<?php



include ("_docroot.php");
include (SERVER_DOCROOT . "logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$objNews = new News;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objUtility->getAction($strAct, $intId);

$id = (int) $_POST["id"];

switch ($strAct) {

	// ******************************************************************************************
	// CATEGORIE

	case "CATEGORIE-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idnewscat", $intId);
		header ("Location: categorie_insupd.php");
		break;

	case "CATEGORIE-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		$objUtility->sessionVarUpdate("idnewscat", $intId);
		header ("Location: categorie_insupd.php");
		break;

	case "CATEGORIE-DEL-GOTO":
		$errorMsg = "";
		$objNews->categorieDelete($conn, $intId, $errorMsg);
		if ($errorMsg)
		{
			$errorMsg = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		}
		else
		{
			$errorMsg = "Cancellazione effettuata";
			$objUtility->sessionVarUpdate("idnewscat", "");
		}
		$objHtml->adminPageRedirect("news.php", $errorMsg, "");
		break;

	case "CATEGORIE-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idnewscat");

		$nome = $_POST["nome"];
		$descrizione = $_POST["descrizione"];
		$ishidden = $_POST["ishidden"];
		$importanza = $_POST["importanza"];

		$errorMsg = "";
		switch ($action)
		{
			case "ins":
				$objNews->categorieInsert($conn, $id, $nome, $descrizione, $ishidden, $importanza, $errorMsg);
				break;
			case "upd":
				$objNews->categorieUpdate($conn, $id, $nome, $descrizione, $ishidden, $importanza, $errorMsg);
				break;
		}
			
		if (!empty($errorMsg))
		{
			$esitoMsg = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("news.php", $esitoMsg, "");
		}
		else
		{
			$esitoMsg = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("news.php", $esitoMsg, "");
		}
		break;

	// ******************************************************************************************
	// NEWS

	case "NEWS-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idnews", $intId);
		header ("Location: news_insupd.php");
		break;

	case "NEWS-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		$objUtility->sessionVarUpdate("idnewscat", $intId);
		header ("Location: news_insupd.php");
		break;

	case "NEWS-DEL-DO":
		$esitoMsg = "";
		$objNews->delete($conn, $intId, $esitoMsg);
		if ($esitoMsg) {
			$errorMsg = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$errorMsg = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("news.php", $errorMsg, "");
		break;

	case "NEWS-MOVEUP-DO":
		$rowSource = $intId;
		$rowDest = $rowSource-1;
		$id_source = $_POST["id".$rowSource];
		$id_dest = $_POST["id".$rowDest];
		$objNews->swapImportanza($conn, $id_source, $id_dest, $errorMsg);
		header ("Location: news.php");
		break;

	case "NEWS-MOVEDOWN-DO":
		$rowSource = $intId;
		$rowDest = $rowSource+1;
		$id_source = $_POST["id".$rowSource];
		$id_dest = $_POST["id".$rowDest];
		$objNews->swapImportanza($conn, $id_source, $id_dest, $errorMsg);
		header ("Location: news.php");
		break;

	case "NEWS-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idnews");
		$objUtility->sessionVarUpdate("idnews", "");

		$idcategorie = $_POST["idcategorie"];
		$titolo = $_POST["titolo"];
		$titoloen = $_POST["titoloen"];
		$titolofr = $_POST["titolofr"];
		$titoloes = $_POST["titoloes"];
		$abstract = $_POST["abstract"];
		$abstracten = $_POST["abstracten"];
		$abstractfr = $_POST["abstractfr"];
		$abstractes = $_POST["abstractes"];
		$testo = $_POST["testo"];
		$testoen = $_POST["testoen"];
		$testofr = $_POST["testofr"];
		$testoes = $_POST["testoes"];
		$link = $_POST["link"];
		if (strpos($link, "http://") !== false) //se viene inserito http, lo tolgo
			$link = substr($link, 7);
		$datapubblicazione = $_POST["datapubblicazione"];
		$datascadenza = $_POST["datascadenza"];
		$ishidden = $_POST["ishidden"];
		$importanza = $_POST["importanza"];

		$isimgthumbdelete = ($_POST["imgthumb_del"]=="1") ? 1 : 0;
		$isimgzoomdelete = ($_POST["imgzoom_del"]=="1") ? 1 : 0;

		$esitoMsg = "";
		switch ($action) {
			case "ins":
				$objNews->insert($conn, $id, $idcategorie, $titolo, $titoloen, $titolofr, $titoloes, $abstract, $abstracten, $abstractfr, $abstractes, $testo, $testoen, $testofr, $testoes, $link, $datapubblicazione, $datascadenza, $ishidden, $importanza, $intIdutente, $strUsername, $esitoMsg);
				break;
			case "upd":
				$objNews->update($conn, $id, $idcategorie, $titolo, $titoloen, $titolofr, $titoloes, $abstract, $abstracten, $abstractfr, $abstractes, $testo, $testoen, $testofr, $testoes, $link, $datapubblicazione, $datascadenza, $ishidden, $importanza, $isimgthumbdelete, $isimgzoomdelete, $esitoMsg);
				break;
		}

		$idnews = $id;
		$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
		$strDestFileTmp = "temp";

		$isUploadOk = false;
		$strUnique = $objUtility->getFilenameUnique();
		$strDestFile = $strUnique;
		
		if ($_FILES["imgthumb"]["name"]) 
		{
			$strExt = $objUtility->getExtFromMime($_FILES["imgthumb"]["type"]);
			if ($strExt == "jpg")
				$isUploadOk = move_uploaded_file($_FILES["imgthumb"]["tmp_name"], $strDestDir.$strDestFileTmp.".".$strExt);
			if ($isUploadOk)
			{
				$objUtility->imageAdapt($strDestDir.$strDestFileTmp.".".$strExt, $strDestDir.$strDestFile.".".$strExt, 32, 32);
				chmod($strDestDir.$strDestFile.".".$strExt, 0644);
				$strOggettoPath = $strDestFile.".".$strExt;
				$strOggettoExt = $strExt;
				$strOggettoOriginalname = $_FILES["imgthumb"]["name"];
			}
		}
		if ($isUploadOk)
			$objNews->imgthumbUpdate($conn, $idnews, $strOggettoPath, $strOggettoExt, $strOggettoOriginalname, $esitoMsg);

		$isUploadOk = false;
		$strUnique = $objUtility->getFilenameUnique();
		$strDestFile = $strUnique;
		if ($_FILES["imgzoom"]["name"])
		{
			$strExt = $objUtility->getExtFromMime($_FILES["imgzoom"]["type"]);
			if ($strExt == "jpg")
				$isUploadOk = move_uploaded_file($_FILES["imgzoom"]["tmp_name"], $strDestDir.$strDestFile.".".$strExt);
			if ($isUploadOk)
			{
				//$objUtility->imageResize($strDestDir . $strDestFileTmp . "." . $strExt, $strDestDir . $strDestFile . "." . $strExt, 391, 281);
				chmod($strDestDir.$strDestFile.".".$strExt, 0644);
				$strOggettoPath = $strDestFile . "." . $strExt;
				$strOggettoExt = $strExt;
				$strOggettoOriginalname = $_FILES["imgzoom"]["name"];
			}
		}
		if ($isUploadOk)
			$objNews->imgzoomUpdate($conn, $idnews, $strOggettoPath, $strOggettoExt, $strOggettoOriginalname, $esitoMsg);

		if ($esitoMsg)
		{
			$errorMsg = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/><br/><br/>".$esitoMsg;
			$objHtml->adminPageRedirect("news.php", $errorMsg, "");
		} 
		else
		{
			$errorMsg = "Operazione eseguita correttamente";
			switch ($action)
			{
				case "ins":
					$objUtility->sessionVarUpdate("idnews", $idnews);
					$objHtml->adminPageRedirect("news.php", $errorMsg, "");
					break;
				case "upd":
					$objHtml->adminPageRedirect("news.php", $errorMsg, "");
					break;
			}
		}
		break;

}
?>