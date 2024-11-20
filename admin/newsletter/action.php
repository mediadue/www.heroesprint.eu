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
		$idusersList = $objUtility->sessionVarRead("newsletter_iduserslist");
		
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
			
			$idusersList = $objUtility->sessionVarRead("newsletter_iduserslist");
    	if ($idusersList) 
    	{
    		$arrUsers = explode(";", $idusersList);
    		if (is_array($arrUsers)) 
    		{
    			echo "EMAIL INVIATE:<br><br>";
          for ($i=0; $i<count($arrUsers); $i++) 
    			{
    				$idusers = $arrUsers[$i];
    				if ($idusers)
    				{
    					$rsTmp = $objUsers->getDetails($conn, $idusers);
    					if (count($rsTmp))
    					{
    						while (list($key, $rowTmp) = each($rsTmp)) {  
                  $sql = "INSERT INTO ".$config_table_prefix."archivio_newsletter (nome, cognome, id_users, oggetto, messaggio, destinatario, allegato_file, inserimento_idusers_hidden, inserimento_username_hidden, data, EMAIL, errori)";
        	        $sql .= " VALUES ('".$rowTmp["nome"]."','".$rowTmp["cognome"]."','".$rowTmp["id"]."', '".$row["subject"]."','".$row["testo"]."','".$rowTmp["email"]."','".$row["idoggetti"]."','".$row["inserimento_idusers"]."','".$row["inserimento_username"]."',NOW(), '1', '".!$esito."')";
                  mysql_query($sql);
                  $tid=mysql_insert_id();
                  
                  $row['testo']=tinyBugAbsolute($row['testo']);
                  
                  $esito=$objMailing->mmail($rowTmp['email'],$objConfig->get("email-from"),$row['subject'],$row['testo'],$objUtility->getPathResourcesDynamicAbsolute().$oggettoPath,$oggettoExt,$oggettoOriginalname);
        				  if($esito) echo ($i+1).") ".date ("d-m-Y H:i:s", mktime(12,13,7,1,1,2007)).": ".$rowTmp['nome']." ".$rowTmp['cognome']." (".$rowTmp['email']."): <span style='color:green;'>inviato correttamente</span><br>";
        				  if(!$esito) echo ($i+1).") ".date ("d-m-Y H:i:s", mktime(12,13,7,1,1,2007)).": ".$rowTmp['nome']." ".$rowTmp['cognome']." (".$rowTmp['email']."): <span style='color:red;'>errori durante l'invio</span><br>";
                  
                  if($errorMsg!=TRUE) $errorMsg=!$esito;
        				  
        				  $sql="UPDATE ".$config_table_prefix."archivio_newsletter SET errori='".!$esito."' WHERE id='$tid'";
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
			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
			//$objHtml->adminPageRedirect("newsletter.php", $esitoMsg, "");
			echo $esitoMsg;
		} 
		else
		{
			//$objUtility->sessionVarDelete("newsletter_iduserslist");
			$esitoMsg = "Operazione eseguita correttamente";
			echo $esitoMsg;
      //$objHtml->adminPageRedirect("newsletter.php", $esitoMsg, "");
		}
		break;
	
	case "SMS-SEND-DO":
    $action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idusato");
		$objUtility->sessionVarUpdate("idusato", ""); 
    
    $subject=$_POST["Mit"];
    $tipo=$_POST["Tipo"];
		if($tipo=="true") $subject="Anonimo";
    $testo = $_POST["Mes"];
		$idusersList = $objUtility->sessionVarRead("newsletter_iduserslist");

		$errorMsg = "";
		$objMailing->insert($conn, $id, $subject, $testo, $idusersList, $intIdutente, $strUsername, $errorMsg);
		if ($id)
		{
			$idmailing = $id;
			$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();

			$rs = $objMailing->getDetails($conn, $id);
    	if (count($rs)) list($key, $row) = each($rs);
			
			$idusersList = $objUtility->sessionVarRead("newsletter_iduserslist");
    	if ($idusersList) 
    	{
    		$arrUsers = explode(";", $idusersList);
    		if (is_array($arrUsers)) 
    		{
    			echo "SMS INVIATI:<br><br>";
          for ($i=0; $i<count($arrUsers); $i++) 
    			{
    				$idusers = $arrUsers[$i];
    				if ($idusers)
    				{
    					$rsTmp = $objUsers->getDetails($conn, $idusers);
    					if (count($rsTmp))
    					{
                // Imposto le variabili per l'autenticazione
                $username = $objConfig->get("TotalConnect-username");
                $password = $objConfig->get("TotalConnect-password");
                // imposto altri dati
                $type_user = "admin";
                if($tipo=="true") $route = "GW1"; // GW2 se si vuole il mittente personalizzato
                if($tipo!="true") $route = "GW2";
                //$time = urlencode("2009-12-31 23:20:01"); // per esempio
                $time = urlencode("");
                // Recupero delle variabili del form
                $mittente = urlencode($subject);
                $messaggio = urlencode($testo);
                
                while (list($key, $rowTmp) = each($rsTmp)) {  
                  $sql = "INSERT INTO ".$config_table_prefix."archivio_newsletter (nome, cognome, id_users, oggetto, messaggio, destinatario, allegato_file, inserimento_idusers_hidden, inserimento_username_hidden, data, SMS, errori)";
        	        $sql .= " VALUES ('".$rowTmp["nome"]."','".$rowTmp["cognome"]."','".$rowTmp["id"]."', '$subject','$testo','".$rowTmp["cellulare"]."','','".$row["inserimento_idusers"]."','".$row["inserimento_username"]."',NOW(), '1', '".!$esito."')";
                  mysql_query($sql);
                  $tid=mysql_insert_id();
                  
                  $cell=$rowTmp['cellulare'];
                  $cell=str_replace(".", "", $cell);
                  $cell=str_replace("-", "", $cell);
                  $cell=str_replace("/", "", $cell);
                  $cell=str_replace("\\", "", $cell);
                  $cell=str_replace("+39", "", $cell);
                  $cell=str_replace(" ", "", $cell);
                  $cell=trim($cell);
                  if($cell!="") {
                    $destinatario = urlencode("+39".$cell);
                    // Compongo la url per l'invio del messaggio
                    $stringa = "http://www.totalconnect.it/send_sms/register.php?username=".$username;
                    $stringa .= "&password=".$password;
                    $stringa .= "&route=".$route."&message=".$messaggio."&to=".$destinatario;
                    $stringa .= "&from=".$mittente."&time=".$time;
                    
                    $contents = file($stringa);
                    if(is_array($contents)) $risposta = trim(implode("",$contents));
                  } else {
                    $esito=false;
                  }
                  
                  if(substr($risposta, 0, 2)=="OK") {$esito=true; echo ($i+1).") ".date ("d-m-Y H:i:s", mktime(12,13,7,1,1,2007)).": ".$rowTmp['nome']." ".$rowTmp['cognome']." (".$rowTmp['cellulare']."): <span style='color:green;'>inviato correttamente ($risposta)</span><br>";}
        				  if(substr($risposta, 0, 2)!="OK") {$esito=false; echo ($i+1).") ".date ("d-m-Y H:i:s", mktime(12,13,7,1,1,2007)).": ".$rowTmp['nome']." ".$rowTmp['cognome']." (".$rowTmp['cellulare']."): <span style='color:red;'>errori durante l'invio ($risposta)</span><br>";}
                  
                  if($errorMsg!=TRUE) $errorMsg=!$esito;
        				  
        				  $sql="UPDATE ".$config_table_prefix."archivio_newsletter SET errori='".!$esito."' WHERE id='$tid'";
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
			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
			//$objHtml->adminPageRedirect("newsletter.php", $esitoMsg, "");
			echo $esitoMsg;
		} 
		else
		{
			//$objUtility->sessionVarDelete("newsletter_iduserslist");
			$esitoMsg = "Operazione eseguita correttamente";
			echo $esitoMsg;
      //$objHtml->adminPageRedirect("newsletter.php", $esitoMsg, "");
		}
		break;
    
  case "CSV-SEND-DO":
    $action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idusato");
		$objUtility->sessionVarUpdate("idusato", ""); 
    
		$idusersList = $objUtility->sessionVarRead("newsletter_iduserslist");
    	
		$errorMsg = "";
		$objMailing->insert($conn, $id, $subject, $testo, $idusersList, $intIdutente, $strUsername, $errorMsg);

    $idmailing = $id;
		$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();

		$rs = $objMailing->getDetails($conn, $id);
  	if (count($rs)) list($key, $row) = each($rs);
		
		$idusersList = $objUtility->sessionVarRead("newsletter_iduserslist");
  
    if ($idusersList) {
  		$arrUersEx = array();
      $arrUserTMP=array();
      $arrUserTMP['0']="NAME";
      $arrUserTMP['1']="NUMBER";
      array_push($arrUersEx, $arrUserTMP);
      
      $arrUsers = explode(";", $idusersList);
  		if (is_array($arrUsers)) {
        for ($i=0; $i<count($arrUsers); $i++) {
  				$idusers = $arrUsers[$i];
  				if ($idusers) {
  					$rsTmp = $objUsers->getDetails($conn, $idusers);
  					if (count($rsTmp)) {
              while (list($key, $rowTmp) = each($rsTmp)) {  
                $arrUserTMP['0']=$rowTmp["nome"]." ".$rowTmp["cognome"];
                $arrUserTMP['0']=utf8_decode($arrUserTMP['0']);
                $arrUserTMP['1']=$rowTmp["cellulare"];
                if($arrUserTMP['1']=="") {
                  if(is_valid_it_mobile_number($rowTmp["telefono"])) $arrUserTMP['1']=$rowTmp["telefono"];
                }
                
                if($arrUserTMP['1']!="") array_push($arrUersEx, $arrUserTMP);
                $sql = "INSERT INTO ".$config_table_prefix."archivio_newsletter (nome, cognome, id_users, oggetto, messaggio, destinatario, allegato_file, inserimento_idusers_hidden, inserimento_username_hidden, data, SMS, errori)";
      	        $sql .= " VALUES ('".$rowTmp["nome"]."','".$rowTmp["cognome"]."','".$rowTmp["id"]."', 'CSV','CSV','".$rowTmp["cellulare"]."','','".$row["inserimento_idusers"]."','".$row["inserimento_username"]."',NOW(), '1', '0')";
                mysql_query($sql);
                $tid=mysql_insert_id();
                
      				  $sql="UPDATE ".$config_table_prefix."archivio_newsletter SET errori='0' WHERE id='$tid'";
      				  mysql_query($sql);
              }
  					}
  				}
  			}
        
        array_to_csv_download($arrUersEx,"export.csv",";");
  		}
  	}	
		
		break;

	case "ARCHIVIO-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("newsletter_idarchivio", $intId);
		header ("Location: archivio_insupd.php");
		break;

	case "ARCHIVIO-DEL-DO":
		$strError = "";
		$objMailing->archivioDelete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
			$objUtility->sessionVarUpdate("newsletter_idarchivio", "");
		}
		$objHtml->adminPageRedirect("archivio.php", $strEsito, "");
		break;
		
	case "PRINT-PDF-DO":
    ?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html>
      <head>
        <?php $objHtml->adminHeadsection(); ?>
      </head>
    <body>
    <?php
		$html=$_POST['html'];
    
    require_once (SERVER_DOCROOT."logic/fpdf/fpdf_alpha.php");
    
    $pdf=new PDF_ImageAlpha('P','mm','A4');
		$pdf->SetMargins(0, 0, 0); 
    $pdf->SetAutoPageBreak(false, 0);
    
    $arr_users=explode(";", $_POST['off_users']);
		
		$sql="SELECT * FROM ".$config_table_prefix."users LIMIT 1";
    $rs_users=mysql_query($sql);
		$num_fields=mysql_num_fields($rs_users);
		 
		for($z=0;$z<count($arr_users);$z++) {
      $tusers=retRow("users",$arr_users[$z]);
      
      $pdf->AddPage();
      $p_grafica=false;
      
  		for($i=0;$i<count($html);$i++) {
        $text=explode("#_#", $html[$i]);    
        
        $x=$text[0];
        $y=$text[1];
        $w=$text[2];
        $h=$text[3]-3;
        
        //CONVERTO IN MILLIMETRI
        //echo "<span style='color:red;'>x=$x<br>y=$y<br>w=$w<br>h=$h<br></span>";
        
        $x=(210*$x)/908;
        $y=(210*$y)/908;
        $w=(210*$w)/908;
        $h=(210*$h)/908;

        //echo "<span style='color:red;'>x=$x<br>y=$y<br>w=$w<br>h=$h<br></span>";
        
        $grassetto=$text[4];
        $corsivo=$text[5];
        $textAlign=$text[6];
        $underline=$text[7];
        
        $align="";
        if($textAlign=="center") $align="C";
        if($textAlign=="left") $align="L";
        if($textAlign=="right") $align="R";
        if($textAlign=="justify") $align="J";
        
        $style="";
        if($grassetto=="700") $style=$style."B";
        if($corsivo=="italic") $style=$style."I";
        if($underline=="underline") $style=$style."U";
        
        $fontSize=$text[8];
        $fontSize=str_replace("px", "", $fontSize);
        $fontSize=$fontSize/1.5;
        $line_height=$fontSize/2.6;
        
        $fontFamily=$text[9];
        if($fontFamily=="Times New Roman") $fontFamily="Times";
        
        $grafica=$text[10];
        $img=$text[11];
        
        $txt=$text[12];
        
        for($k=0;$k<$num_fields;$k++) {
          $field=mysql_field_name($rs_users, $k);
          $txt=str_replace("$".$field."$", $tusers[$field], $txt);  
        } 
         
        if(!$p_grafica && $grafica!="none") {
          $p_grafica=true;
          $grafica=substr($grafica, strpos($grafica,$objConfig->get("path-resources-upload")."/"), strlen($grafica));
          $grafica=substr($grafica, strpos($grafica,"/")+1, strlen($grafica));
          $grafica=substr($grafica, 0, strlen($grafica)-3);  
          $pdf->Image($objUtility->getPathResourcesDynamicAbsolute().$grafica,0,0,210,297);  
        }
        //echo "ppp".$x;exit;
        if($x>=1) {
          $pdf->SetXY($x-1,$y);
        }else{
          $pdf->SetXY($x,$y);  
        }
         
        $pdf->SetFont($fontFamily,$style,$fontSize);
         
        if($img=="null") {
          $pdf->MultiCell($w+2.5,$line_height,$txt,0,$align);
        } else { 
          $img=substr($img, strpos($img,$objConfig->get("path-resources-upload")."/"), strlen($img));
          $img=substr($img, strpos($img,"/")+1, strlen($img));
          $pdf->Image($objUtility->getPathResourcesDynamicAbsolute().$img,$x,$y,$w,$h);  
        }
      }
    }
		
    $pdf->Output('pdf/doc.pdf','F');
    ?>
    
    <a id="d_pdf" href="pdf/doc.pdf">Download del PDF generato il <?php echo date("d/m/y"); ?> alle ore <?php echo date("H:i"); ?></a>
    </body>
    </html>
    <?
		break;
}
?>