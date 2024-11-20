<?php

Class Mailing 
{

  /**
  ******************************************************************************************
  * aggiunge il pdf tra gli oggetti ed aggiorna l'history
  * @access public        
  * @param $conn
  * @param int $idoggetti: restituisce l'id inserito sulla tabella oggetti
  * @param int $idcategorie
  * @param string $lingua
  * @param string $filepdf: nome del file pdf creato sul filesystem
  * @param int $idusers: id dell'utente che ha creato il file
  * @param string $username: username dell'utente che ha creato il file
  * @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
  * @return void
  */
  function insert($conn, &$id, $subject, $testo, $iduserslist, $idusers, $username, &$errorMsg)
  {
  	global $config_table_prefix, $objUtility;
  
  	$subjectSql = $objUtility->translateForDb($subject, "string");
  	$testoSql = $objUtility->translateForDb($testo, "string");
  	$iduserslistSql = $objUtility->translateForDb($iduserslist, "string");
  	$idusersSql = $objUtility->translateForDb($idusers, "int");
  	$usernameSql = $objUtility->translateForDb($username, "string");
  	
  	$sql = "INSERT INTO ".$config_table_prefix."newsletter_archivio (subject, testo, iduserslist, inserimento_idusers, inserimento_username, inserimento_data)";
  	$sql .= " VALUES (" . $subjectSql.",".$testoSql.",".$iduserslistSql.",".$idusersSql.",".$usernameSql.",NOW())";
  
  	mysql_query($sql, $conn);
  	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

    if (!mysql_errno($conn) && !mysql_error($conn))
  		$id = mysql_insert_id($conn);
  }
  
  // ******************************************************************************************
  function get($conn, $dataFrom, $dataTo)
  {
  	global $config_table_prefix, $objUtility;
  	$sqlWhere = "";
  	if ($dataFrom) $sqlWhere .= "(inserimento_data >= '" . $dataFrom . " 00:00:00') AND ";
  	if ($dataTo) $sqlWhere .= "(inserimento_data <= '" . $dataTo . " 23:59:99') AND ";
  	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
  	$sql = "SELECT * FROM ".$config_table_prefix."newsletter_archivio " . $sqlWhere . " ORDER BY inserimento_data DESC";
  
  	$query = mysql_query ($sql, $conn);
  	$rs = $objUtility->buildRecordset($query);
  	return $rs;
  }
  // ******************************************************************************************
  function getDetails($conn, $id)
  {
  	global $config_table_prefix, $objUtility;
  	$sql = "SELECT * FROM ".$config_table_prefix."newsletter_archivio WHERE id=" . $id;
  	$query = mysql_query ($sql, $conn);
  	$rs = $objUtility->buildRecordset($query);
  	return $rs;
  }
  
  // ******************************************************************************************
  function delete($conn, $id, &$errorMsg)
  {
  	global $config_table_prefix, $objUtility, $objObjects;
  	
  	$rs = $this->catalogoArchivioGetDetails($conn, $id);
  	if (count($rs) > 0)
  	{
  		list($key, $row) = each($rs);
  		$objObjects->delete($conn, $row["idoggetti"], $errorMsg);
  	}
  
  	$sql = "DELETE FROM ".$config_table_prefix."newsletter_archivio WHERE id=".$id;
  	mysql_query ($sql, $conn);
  	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
  }
  
  /**
  ******************************************************************************************
  * aggiorna il file associato alla newsletter
  * @access public        
  * @param $conn
  * @param int $id
  * @param string $path: il nome del file sul filesystem
  * @param string $ext: estensione del file
  * @param string $originalName
  * @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
  * @return 
  */
  function fileUpdate($conn, $id, $path, $ext, $originalName, &$errorMsg)
  {
  	global $config_table_prefix, $objUtility, $objObjects;
  
  	$rs = $this->getDetails($conn, $id);
  	if (count($rs) > 0)
  	{
  		list($key, $row) = each($rs);
  		$objObjects->delete($conn, $row["idoggetti"], $errorMsg);
  	}
  	$idoggetti=0;
  	$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
  	$path=str_replace(".".$ext, "", $path);
    $objObjects->insert($conn, $idoggetti, $path, $ext, $strDestDir, $originalName, "NULL", $errorMsg);
  	if ($idoggetti)
  	{
  		$sql = "UPDATE ".$config_table_prefix."newsletter_archivio SET idoggetti=".$idoggetti." WHERE id=".$id;
  		mysql_query($sql, $conn);
  		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
  	}
  }
  
  /**
  ******************************************************************************************
  * aggiunge una riga alla tabella della history
  * @access public        
  * @param $conn
  * @param int $iddocuments
  * @param string $subject
  * @param string $testo
  * @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
  * @return 
  */
  function send($conn, $id, &$errorMsg)
  {
  	global $objUsers, $objUtility, $objObjects, $objConfig, $config_table_prefix;
  	$rs = $this->getDetails($conn, $id);
  	if (count($rs))
  		list($key, $row) = each($rs);
  
  	$objMail = new PHPMailer();
  	$objMail->From = $objConfig->get("email-from");
  	$objMail->FromName = $objConfig->get("email-fromname");
  	$objMail->Subject = $row["subject"];
  	$objMail->Body = $row["testo"];
  	$objMail->AddAttachment($pathObj, $rowObj["originalname"]);
  	$objMail->AddAddress($objConfig->get("email-from"));
  
  	//gestisco l'eventuale allegato
  	if ($row["idoggetti"])
  	{
  		$rs = $objObjects->getDetails($conn, $row["idoggetti"]);
  		if (count($rs) > 0) 
  		{
  			list($key, $rowObj) = each($rs);			
  			$path = $objUtility->getPathResourcesDynamicAbsolute();
  			$pathObj = $path.$rowObj["path"];
  			$objMail->AddAttachment($pathObj, $rowObj["originalname"]);
  		}
  	}
  
  	//spedisco la mail ai destinatari
  	$idusersList = $row["iduserslist"];
  	if ($idusersList) 
  	{
  		$arrUsers = explode(";", $idusersList);
  		if (is_array($arrUsers)) 
  		{
  			for ($i=0; $i<count($arrUsers); $i++) 
  			{
  				$idusers = $arrUsers[$i];
  				if ($idusers)
  				{
  					$rsTmp = $objUsers->getDetails($conn, $idusers);
  					if (count($rsTmp))
  					{
  						list($key, $rowTmp) = each($rsTmp);
  						$objMail->AddBCC($rowTmp["email"]);
  					}
  				}
  			}
  		}
  	}	
  	$esito = $objMail->Send();
  	
  	for ($i=0; $i<count($arrUsers); $i++) 
  	{
  		$idusers = $arrUsers[$i];
  		if ($idusers)
  		{
  			$rsTmp = $objUsers->getDetails($conn, $idusers);
  			if (count($rsTmp))
  			{
  				while (list($key, $rowTmp) = each($rsTmp)) {  
  				  $sql = "INSERT INTO ".$config_table_prefix."archivio_newsletter (oggetto, messaggio, destinatario, allegato_file, inserimento_idusers_hidden, inserimento_username_hidden, data, errori)";
  	        $sql .= " VALUES ('".$row["subject"]."','".$row["testo"]."','".$rowTmp["email"]."','".$row["idoggetti"]."','".$row["inserimento_idusers"]."','".$row["inserimento_username"]."',NOW(), '".!$esito."')";
            mysql_query($sql);
          }
  			}
  		}
  	}
  	
    if (!$esito)
  		$errorMsg .= $objMail->ErrorInfo;
  }
  
  // ******************************************************************************************
  function archivioGetList($conn, $dataFrom, $dataTo)
  {
  	global $config_table_prefix, $objUtility;
  	$sqlWhere = "";
  	if ($dataFrom) $sqlWhere .= "(inserimento_data >= '" . $dataFrom . " 00:00:00') AND ";
  	if ($dataTo) $sqlWhere .= "(inserimento_data <= '" . $dataTo . " 23:59:99') AND ";
  	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
  	$sql = "SELECT * FROM ".$config_table_prefix."newsletter_archivio " . $sqlWhere . " ORDER BY inserimento_data DESC";
  
  	$query = mysql_query ($sql, $conn);
  	$rs = $objUtility->buildRecordset($query);
  	return $rs;
  }
  // ******************************************************************************************
  function archivioGetDetails($conn, $id)
  {
  	global $config_table_prefix, $objUtility;
  	$sql = "SELECT * FROM ".$config_table_prefix."newsletter_archivio WHERE id=".$id;
  	$query = mysql_query ($sql, $conn);
  	$rs = $objUtility->buildRecordset($query);
  	return $rs;
  }
  
  // ******************************************************************************************
  function archivioDelete($conn, $id, &$errorMsg)
  {
  	global $config_table_prefix, $objUtility, $objObjects;
  	
  	$rs = $this->archivioGetDetails($conn, $id);
  	if (count($rs) > 0)
  	{
  		list($key, $row) = each($rs);
  		if ($row["idoggetti"])
  			$objObjects->delete($conn, $row["idoggetti"], $errorMsg);
  	}
  
  	$sql = "DELETE FROM ".$config_table_prefix."newsletter_archivio WHERE id=".$id;
  	mysql_query ($sql, $conn);
  	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
  }
  
  function convertToEmbedded($str,$prefix) {
    while(strpos($str, $prefix)!==FALSE) {
      $obj="";  
    }  
  }
  
  function makeEmbedded($html) {
    $objUtility = new Utility;
    $docroot=$objUtility->getPathRoot();
    $docroot=str_replace($docroot, "", SERVER_DOCROOT);
    
    $mail = new PHPMailer();
    $dom = new DOMDocument;
 
    //Parse the HTML. The @ is used to suppress any parsing errors
    //that will be thrown if the $html string isn't valid XHTML.
    @$dom->loadHTML($html);
     
    //Get all links. You could also use any other tag name here,
    //like 'img' or 'table', to extract other tags.
    $imgs = $dom->getElementsByTagName('img');
     
    //Iterate over the extracted links and display their URLs
    foreach ($imgs as $img){
    	//Extract and show the "href" attribute. 
    	$file=$img->getAttribute('src');
    	$name=basename($file);
      $file=$docroot.str_replace("http://".$_SERVER['SERVER_NAME']."/", "", $file);
      
      $ext=retExt($name);
      if($ext="jpg") $ext="jpeg";
      
      $mail->AddEmbeddedImage($file, $name, $name,"base64","image/".$ext);
      $img->setAttribute('src','cid:'.$name); 
    }
    
    $html=$dom->saveHTML();
    $html=stripslashes($html);
    $html=html_entity_decode($html);
    
    $mail->Body = $html;
    
    return $mail;
  }
  
  function mmail($destinatario,$mittente,$oggetto,$messaggio,$allegato,$allegato_type,$allegato_name) {
    global $objConfig;
    $messaggio=utf8_decode($messaggio);
    $mail = new PHPMailer();
    $mail=$this->makeEmbedded($messaggio);
    /*
    $mail->IsSMTP();  // send via SMTP
    $mail->Host     = "smtp.umbriaeventi.com"; // SMTP servers
    $mail->SMTPAuth = true;     // turn on SMTP authentication
    $mail->Username = "info@umbriaeventi.com";  // SMTP username
    $mail->Password = "New06Com"; // SMTP password
    */
    
    $arrDest=explode(";", $destinatario);
    if(count($arrDest)>0) {
      while (list($key, $row) = each($arrDest)) {
        $row=trim($row);
        if($row!="") $mail->AddAddress($row);    
      }
    }else{
      $mail->AddAddress($destinatario);  
    }
    
    $mail->From     = $mittente;
    $mail->FromName = $objConfig->get("email-fromname");
    
    $mail->AddReplyTo($mittente);
    
    $mail->WordWrap = 50;                              // set word wrap
    if($allegato!="" && file_exists($allegato) && is_file($allegato)) $mail->AddAttachment($allegato, $allegato_name);
    $mail->IsHTML(true);                               // send as HTML
    
    $mail->Subject  =  $oggetto;
    
    include("events/inc.before_sending.php");
    
    //$mail->AltBody  =  "";
    $ret=$mail->Send();
    
    include("events/inc.after_sending.php");
    
    if(!$ret)
    {
       return FALSE;
    }
    
    return TRUE;
  }
  
}
?>