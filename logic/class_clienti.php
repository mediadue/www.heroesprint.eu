<?php
class Clienti extends Users {

// ******************************************************************************************
function getSearch($conn, $username, $codicecliente, $ragionesociale, $email) 
{
	global $config_table_prefix, $objUtility, $objConfig;
	$idroles = $objConfig->get("role-areariservata");
	$sqlWhere = "";
	if ($idroles) {
		$rs = parent::getRolesUsers($conn);
		if (count($rs)) {
			$sqlWhere .= "(";
			while (list($key, $rowTmp) = each($rs)) { 
				$sqlWhere .= "id=" . $rowTmp["id"] . " OR ";
			}
		} else {
			$sqlWhere .= "(id=0 OR ";
		}
		$sqlWhere = substr($sqlWhere, 0, strlen($sqlWhere)-4) . ") AND ";
	}
	if ($username) $sqlWhere .= "login LIKE '%" . addslashes($username) . "%' AND ";
	If ($codicecliente) $sqlWhere .= "codicecliente='" . addslashes($codicecliente) . "' AND ";
	If ($ragionesociale) $sqlWhere .= "ragionesociale LIKE '%" . addslashes($ragionesociale) . "%' AND ";
	If ($email) $sqlWhere .= "email LIKE '%" . addslashes($email) . "%' AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT * FROM ".$config_table_prefix."users" . $sqlWhere . " ORDER BY ragionesociale ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* restituisce true se l'utente ha il permesso di scaricare il file il cui id è passato come parametro
* @access public        
* @param $conn
* @param int $idusers
* @param int $iddocuments
* @return bool
*/
function checkRights($conn, $idusers, $iddocuments) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$isAuthorized = false;
	$sql = "SELECT * FROM ".$config_table_prefix."documents WHERE idusers=".$idusers." AND id=".$iddocuments;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	if (count($rs))
		$isAuthorized = true;

	return $isAuthorized;
}

/**
******************************************************************************************
* inserisce un agente
*
* @access public
* @return void
*/ 
function insert ($conn, $idroleAreariservata, &$id, $username, $pwd, $codicecliente, $ragionesociale, $partitaiva, $codicefiscale, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, &$sqlError) 
{
	$isdisabled = 0;
	$isbackoffice = 1;
	$activationcode = false;
	parent::insert2($conn, $id, $username, $pwd, false, false, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $isdisabled, $isbackoffice, $activationcode, $sqlError);
	$this->update($conn, $id, $username, false, $codicecliente, $ragionesociale, $partitaiva, $codicefiscale, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $isdisabled, $isbackoffice, $activationcode, $sqlError);
	parent::usersRolesIns($conn, $id, $idroleAreariservata, $sqlError);
}

// ******************************************************************************************
function update($conn, $id, $username, $pwd, $codicecliente, $ragionesociale, $partitaiva, $codicefiscale, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $isdisabled, $isbackoffice, $activationcode, &$sqlError) 
{
	global $config_table_prefix, $objUtility;
	$usernameSql = $objUtility->translateForDb($username, "string");
	$codiceclienteSql = $objUtility->translateForDb($codicecliente, "string");
	$ragionesocialeSql = $objUtility->translateForDb($ragionesociale, "string");
	$partitaivaSql = $objUtility->translateForDb($partitaiva, "string");
	$codicefiscaleSql = $objUtility->translateForDb($codicefiscale, "string");
	$indirizzoSql = $objUtility->translateForDb($indirizzo, "string");
	$cittaSql = $objUtility->translateForDb($citta, "string");
	$capSql = $objUtility->translateForDb($cap, "string");
	$provinciaSql = $objUtility->translateForDb($provincia, "string");
	$nazioneSql = $objUtility->translateForDb($nazione, "string");
	$telefonoSql = $objUtility->translateForDb($telefono, "string");
	$faxSql = $objUtility->translateForDb($fax, "string");
	$emailSql = $objUtility->translateForDb($email, "string");
	$noteSql = $objUtility->translateForDb($note, "string");
	$isdisabledSql = $objUtility->translateForDb($isdisabled, "int");
	$isbackofficeSql = $objUtility->translateForDb($isbackoffice, "int");
	$activationcodeSql = $objUtility->translateForDb($activationcode, "string");

	$strSql = "UPDATE ".$config_table_prefix."users SET ";
	$strSql .= "login=" . $usernameSql . ", ";
	if ($pwd) {
		$strSql .= "pwd=MD5('" . $pwd . "'), ";
	}
	$strSql .= "codicecliente=" . $codiceclienteSql . ", ";
	$strSql .= "ragionesociale=" . $ragionesocialeSql . ", ";
	$strSql .= "partitaiva=" . $partitaivaSql . ", ";
	$strSql .= "codicefiscale=" . $codicefiscaleSql . ", ";
	$strSql .= "indirizzo=" . $indirizzoSql . ", ";
	$strSql .= "citta=" . $cittaSql . ", ";
	$strSql .= "cap=" . $capSql . ", ";
	$strSql .= "provincia=" . $provinciaSql . ", ";
	$strSql .= "nazione=" . $nazioneSql . ", ";
	$strSql .= "telefono=" . $telefonoSql . ", ";
	$strSql .= "fax=" . $faxSql . ", ";
	$strSql .= "email=" . $emailSql . ", ";
	$strSql .= "note=" . $noteSql . ", ";
	$strSql .= "isdisabled=" . $isdisabled . ", ";
	$strSql .= "isbackoffice=" . $isbackoffice;
	$strSql .= " WHERE id=" . $id;

	mysql_query($strSql, $conn);
	$sqlError .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* aggiorna soltanto i dati modificabili dall'utente
*
* @access public
* @param $conn
* @param int $id
* @param string $partitaiva
* @param string $codicefiscale
* @param string $indirizzo
* @param string $citta
* @param string $cap
* @param string $provincia
* @param string $nazione
* @param string $telefono
* @param string $fax
* @param string $email
* @param string $sqlError: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return void
*/ 
function updateProfile($conn, $id, $partitaiva, $codicefiscale, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, &$sqlError) 
{
	global $config_table_prefix, $objUtility;
	$partitaivaSql = $objUtility->translateForDb($partitaiva, "string");
	$codicefiscaleSql = $objUtility->translateForDb($codicefiscale, "string");
	$indirizzoSql = $objUtility->translateForDb($indirizzo, "string");
	$cittaSql = $objUtility->translateForDb($citta, "string");
	$capSql = $objUtility->translateForDb($cap, "string");
	$provinciaSql = $objUtility->translateForDb($provincia, "string");
	$nazioneSql = $objUtility->translateForDb($nazione, "string");
	$telefonoSql = $objUtility->translateForDb($telefono, "string");
	$faxSql = $objUtility->translateForDb($fax, "string");
	$emailSql = $objUtility->translateForDb($email, "string");

	$sql = "UPDATE ".$config_table_prefix."users SET ";
	$sql .= "partitaiva=" . $partitaivaSql . ", ";
	$sql .= "codicefiscale=" . $codicefiscaleSql . ", ";
	$sql .= "indirizzo=" . $indirizzoSql . ", ";
	$sql .= "citta=" . $cittaSql . ", ";
	$sql .= "cap=" . $capSql . ", ";
	$sql .= "provincia=" . $provinciaSql . ", ";
	$sql .= "nazione=" . $nazioneSql . ", ";
	$sql .= "telefono=" . $telefonoSql . ", ";
	$sql .= "fax=" . $faxSql . ", ";
	$sql .= "email=" . $emailSql;
	$sql .= " WHERE id=" . $id;

	mysql_query($sql, $conn);
	$sqlError .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* cancella i dati che rigurdano solo i clienti, poi richiama il metodo del padre
*
* @access public
* @return void
*/ 
function delete($conn, $id, &$sqlError)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	parent::delete($conn, $id, $sqlError);
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
function sendMailPwd($conn, $idclienti, $password, &$errorMsg)
{
	global $objUsers, $objUtility, $objConfig;

	$rs = $objUsers->getDetails($conn, $idclienti);
	if (count($rs))
		list($key, $rowUsers) = each($rs);

	$message = "Di seguito le credenziali per accedere all'area riservata di: http://www.castagnoligiusepppe.it\n\n";
	$message .= "username: " . $rowUsers["login"] . "\n";
	$message .= "password: " . $password . "\n\n";
	$message .= "Una volta effettuato il primo accesso all'area riservata, Le consigliamo di modificare la Sua password cliccando sulla voce 'modifica password', e appuntarla in modo sicuro.\n\n";
	$message .= "Buon lavoro.\n\n";

	$objMail = new PHPMailer();
	$objMail->From = $objConfig->get("email-from");
	$objMail->FromName = $objConfig->get("email-fromname");
	$objMail->AddAddress($rowUsers["email"]);
	$objMail->Subject = "Nuova password [www.castagnoligiuseppe.it]";
	$objMail->Body = $message;
	$esito = $objMail->Send();
	if (!$esito)
		$errorMsg .= "errore durante l'invio dell'email al cliente";			
}

}
?>