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
$conn = $objDb->connection($objConfig);

session_start();

global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);

$objUtility->getAction($strAct, $intId);
switch ($strAct) 
{
	case "NEWSLETTER-SEND-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idusato");
		$objUtility->sessionVarUpdate("idusato", "");

		$subject = $_POST["subject"];
		$testo = $_POST["testo"];
		$prodotto=$_POST["prodotto"];
		
		if($prodotto!="") {
      $prs=getTable("ga_prodotti","","id='$prodotto'");
		  $prs=$prs[0];
		}
		
		$idusersList = $objUtility->sessionVarRead("ga_iduserslist");

		$errorMsg = "";
		$objMailing->insert($conn, $id, $subject, $testo, $idusersList, $intIdutente, $strUsername, $errorMsg);
		if ($id)
		{
			$idmailing = $id;
			$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
		
			$isUploadOk = false;
			$strUnique = $objUtility->getFilenameUnique();
			$strDestFile = $strUnique;
			
			if ($_FILES["file"]["name"]) 
			{
				$strExt = $objUtility->getExt($_FILES["file"]["name"]);
				$isUploadOk = move_uploaded_file($_FILES["file"]["tmp_name"], $strDestDir.$strDestFile.".".$strExt);
				if ($isUploadOk)
				{
					chmod($strDestDir.$strDestFile.".".$strExt, 0644);
					$oggettoPath = $strDestFile.".".$strExt;
					$oggettoExt = $strExt;
					$oggettoOriginalname = $_FILES["file"]["name"];
				}
			}
			if ($isUploadOk) $objMailing->fileUpdate($conn, $idmailing, $oggettoPath, $oggettoExt, $oggettoOriginalname, $errorMsg);

			//$objMailing->send($conn, $idmailing, $errorMsg);
			$rs = $objMailing->getDetails($conn, $id);
    	if (count($rs)) list($key, $row) = each($rs);
			
			$idusersList = $row["iduserslist"];
    	if ($idusersList) 
    	{
    		$arrUsers = explode(";", $idusersList);
    		if (is_array($arrUsers)) 
    		{
    			$sql = "INSERT INTO ".$config_table_prefix."archivio_richieste_offerta (codice, data, oggetto, messaggio)";
	        $sql .= " VALUES ('0', NOW(),'".$row["subject"]."','')";
          mysql_query($sql);
          $tid=mysql_insert_id();
          
          for ($i=0; $i<count($arrUsers); $i++) 
    			{
    				$idusers = $arrUsers[$i];
    				if ($idusers)
    				{
    					$rsTmp = $objUsers->getDetails($conn, $idusers);
    					if (count($rsTmp))
    					{
                while (list($key, $rowTmp) = each($rsTmp)) {  
                  $sql = "INSERT INTO ".$config_table_prefix."form_archivio_offerte (id_archivio_richieste_offerta,data,id_ga_prodotti,idga_prodotti,articolo_richiesto,fornitore,idfornitore_hidden,data_offerta,ga_prezzo_cry,Errori)";
        	        $sql .= " VALUES ('".$tid."', NOW(),'".$prs["id"]."','".$prs["codice"]."','".$prs["descrizione"]."','".$rowTmp["ragionesociale"]."','".$rowTmp["id"]."','','',0)";
                  mysql_query($sql);
                  $tid2=mysql_insert_id();
                  
                  $pass=retRow("users",$rowTmp['id']);
                  $tmptesto=addslashes($row['testo']."<br><p><a href='http://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."index.php?documents=1&richiesta=$tid&user=".$rowTmp['id']."&pass=".$pass['pwd']."' target='_blank'>clicca qui per accedere alla tua area riservata e fare la tua offerta</a></p><br>");
                  $esito=$objMailing->mmail($rowTmp['email'],$objConfig->get("email-from"),$row['subject'],$tmptesto,$objUtility->getPathResourcesDynamicAbsolute().$oggettoPath,$oggettoExt,$oggettoOriginalname);
        				  
        				  $sql="UPDATE ".$config_table_prefix."archivio_richieste_offerta SET messaggio='$tmptesto', codice='$tid' WHERE id='$tid'";
        				  mysql_query($sql);
        				  
        				  $sql="UPDATE ".$config_table_prefix."form_archivio_offerte SET Errori='".!$esito."' WHERE id='$tid2'";
        				  mysql_query($sql);
                }
    					}
    				}
    			}
    		}
    	}	
		}
		if ($errorMsg)
		{
			$esitoMsg = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/><br/><br/>".$errorMsg;
			$objHtml->adminPageRedirect("newsletter.php", $esitoMsg, "");
		} 
		else
		{
			$objUtility->sessionVarDelete("ga_iduserslist");
			$esitoMsg = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("newsletter.php", $esitoMsg, "");
		}
		break;

	case "ARCHIVIO-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("ga_idarchivio", $intId);
		header ("Location: archivio_insupd.php");
		break;

	case "ARCHIVIO-DEL-DO":
		$strError = "";
		$objMailing->archivioDelete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
			$objUtility->sessionVarUpdate("ga_idarchivio", "");
		}
		$objHtml->adminPageRedirect("archivio.php", $strEsito, "");
		break;
}
?>