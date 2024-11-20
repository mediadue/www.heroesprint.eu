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
$objDocuments = new Documents;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);

$objUtility->getAction($strAct, $intId);
$id = (int) $_POST["id"];

switch ($strAct) {

	// ******************************************************************************************
	// TAGS

	case "TAGS-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("iddoctags", $intId);
		header ("Location: tags_insupd.php");
		break;

	case "TAGS-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		$objUtility->sessionVarUpdate("iddoctags", $intId);
		header ("Location: tags_insupd.php");
		break;

	case "TAGS-DEL-GOTO":
		$errorMsg = "";
		$objDocuments->tagsDelete($conn, $intId, $errorMsg);
		if ($errorMsg)
		{
			$errorMsg = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		}
		else
		{
			$errorMsg = "Cancellazione effettuata";
			$objUtility->sessionVarUpdate("iddoctags", "");
		}
		$objHtml->adminPageRedirect("tags.php", $errorMsg, "");
		break;

	case "TAGS-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("iddoctags");

		$nome = $_POST["nome"];
		$importanza = $_POST["importanza"];

		$errorMsg = "";
		switch ($action)
		{
			case "ins":
				$objDocuments->tagsInsert($conn, $id, $nome, $importanza, $errorMsg);
				break;
			case "upd":
				$objDocuments->tagsUpdate($conn, $id, $nome, $importanza, $errorMsg);
				break;
		}
			
		if (!empty($errorMsg))
		{
			$esitoMsg = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("tags.php", $esitoMsg, "");
		}
		else
		{
			$esitoMsg = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("tags.php", $esitoMsg, "");
		}
		break;

	// ******************************************************************************************
	// DOCUMENTS

	case "DOCUMENTS-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("iddoc", $intId);
		header ("Location: documents_insupd.php");
		break;

	case "DOCUMENTS-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		$objUtility->sessionVarUpdate("iddoc", $intId);
		header ("Location: documents_insupd.php");
		break;

	case "DOCUMENTS-DEL-DO":
		$esitoMsg = "";
		$objDocuments->delete($conn, $intId, $esitoMsg);
		if ($esitoMsg) {
			$errorMsg = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato<br/><br/><br/>".$esitoMsg;
		} else {
			$errorMsg = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("documents.php", $errorMsg, "");
		break;

	case "DOCUMENTS-INSUPD-DO":
    $action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("iddoc");
		$objUtility->sessionVarUpdate("iddoc", "");
		$idusersArr = $_POST["idusers"];
		$anno = $_POST["anno"];
		$ishidden = $_POST["ishidden"];
    $idtags = $_POST["idtags"];
    
		$esitoMsg = "";
		
    switch ($action) 
		{
			case "ins":
				$id = 0;
				if(is_array($_SESSION['tmp_arrOggetti'])) {
          foreach($_SESSION['tmp_arrOggetti'] as $key)  
  				{
            reset($idusersArr); 
            foreach($idusersArr as $idusers) {
              $sql="INSERT INTO `".$config_table_prefix."documents` (idoggetti,idusers,anno,ishidden,inserimento_idusers,inserimento_username,inserimento_data) VALUES ('$key','$idusers','$anno','$ishidden','$intIdutente','$strUsername',NOW() ) ";
              $query=mysql_query($sql);
              $iddocuments=mysql_insert_id();
              
              $sql="INSERT INTO `".$config_table_prefix."documents_tags_nm` (iddocuments,idtags) VALUES ('$iddocuments','$idtags') ";
              $query=mysql_query($sql);
            }
          }
        }
				break;
			case "upd":
				$v=0;
        if(is_array($_SESSION['tmp_arrOggetti'])) {
          foreach($_SESSION['tmp_arrOggetti'] as $key)  
  				{
            reset($idusersArr);
            foreach($idusersArr as $idusers) {
              $v=1;
              $sql="UPDATE `".$config_table_prefix."documents` SET idoggetti='$key',idusers='$idusers',anno='$anno',ishidden='$ishidden',inserimento_idusers='$intIdutente',inserimento_username='$strUsername',inserimento_data=NOW() WHERE id='$id' ";
              $query=mysql_query($sql);
              
              $sql="UPDATE `".$config_table_prefix."documents_tags_nm` SET idtags='$idtags' WHERE iddocuments='$id'";
              $query=mysql_query($sql);
            }
          }
        }
        if($v==0) {
          reset($idusersArr);
          foreach($idusersArr as $idusers) {
            $sql="UPDATE `".$config_table_prefix."documents` SET idusers='$idusers',anno='$anno',ishidden='$ishidden' WHERE id='$id' ";
            $query=mysql_query($sql);
            
            $sql="UPDATE `".$config_table_prefix."documents_tags_nm` SET idtags='$idtags' WHERE iddocuments='$id'";
            $query=mysql_query($sql);
          }
        }
				break;
		}

		if ($esitoMsg)
		{
			$errorMsg = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/><br/><br/>".$esitoMsg;
			$objHtml->adminPageRedirect("documents.php", $errorMsg, "");
		} 
		else
		{
			$errorMsg = "Operazione eseguita correttamente";
			switch ($action)
			{
				case "ins":
					$_SESSION['tmp_arrOggetti']=array();
          $objUtility->sessionVarUpdate("iddoc", $iddocuments);
					$objHtml->adminPageRedirect("documents.php", $errorMsg, "");
					break;
				case "upd":
					$_SESSION['tmp_arrOggetti']=array();
          $objHtml->adminPageRedirect("documents.php", $errorMsg, "");
					break;
			}
		}
		break;

	case "DOCUMENTS-EMAIL-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("iddoc", $intId);
		header ("Location: documents_email.php");
		break;
				
	case "DOCUMENTS-EMAIL-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("iddoc");

		$subject = $_POST["subject"];
		$testo = $_POST["testo"];

		$errorMsg = "";
		$objDocuments->emailSend($conn, $id, $subject, $testo, $errorMsg);
			
		if (!empty($errorMsg))
		{
			$esitoMsg = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/><br/><br/><br/>".$errorMsg;
			$objHtml->adminPageRedirect("documents.php", $esitoMsg, "");
		}
		else
		{
			$objDocuments->emailInsert($conn, $id, $subject, $testo, $intIdutente, $strUsername, $errorMsg);
      $esitoMsg = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("documents.php", $esitoMsg, "");
		}
		break;
}
?>