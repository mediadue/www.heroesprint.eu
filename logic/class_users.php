<?php
class Users 
{

// ******************************************************************************************
function getRolesUsers($conn, $intId="") {
	global $config_table_prefix;
	
  if($intId=="") $intId="idroles";
	
  $strSql = "SELECT DISTINCT idusers id FROM ".$config_table_prefix."roles_users_nm WHERE idroles=" . $intId;
  $query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getRicerca($conn, $strUsername, $strNome, $strCognome, $strEmail, $idroles) 
{
	global $config_table_prefix;
	$strSqlWhere = "";
	if ($idroles) {
		$rs = $this->getRolesUsers($conn, $idroles);
		if (count($rs)) {
			$strSqlWhere .= "(";
			while (list($key, $rowTmp) = each($rs)) { 
				$strSqlWhere .= "id=" . $rowTmp["id"] . " OR ";
			}
		} else {
			$strSqlWhere .= "(id=0 OR ";
		}
		$strSqlWhere = substr($strSqlWhere, 0, strlen($strSqlWhere)-4) . ") AND ";
	}
	if ($strUsername) $strSqlWhere .= "login LIKE '%" . addslashes($strUsername) . "%' AND ";
	If ($strNome) $strSqlWhere .= "nome LIKE '%" . addslashes($strNome) . "%' AND ";
	If ($strCognome) $strSqlWhere .= "cognome LIKE '%" . addslashes($strCognome) . "%' AND ";
	If ($strEmail) $strSqlWhere .= "email LIKE '%" . addslashes($strEmail) . "%' AND ";
	If ($strSqlWhere) $strSqlWhere = " WHERE " . substr($strSqlWhere, 0, strlen($strSqlWhere)-5);
	$strSql = "SELECT * FROM ".$config_table_prefix."users" . $strSqlWhere . " ORDER BY login ASC";

	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

function checkLogin($conn, $strLogin, $strPwd, &$intIdutente, &$strUsername, &$dateLastAccess, &$dateLastPwdupdate, &$isReadonly) 
{
	global $config_table_prefix;
	$strSql = "SELECT id, login, ultimoaccesso, ultimopwdmod, isreadonly FROM ".$config_table_prefix."users WHERE login='" . $strLogin . "'  AND pwd=MD5('" . $strPwd . "') AND (isdisabled<>1 OR isdisabled IS NULL) AND isbackoffice=1";
	$query = mysql_query ($strSql, $conn);
	if (mysql_num_rows($query) > 0) 
	{
		list($intIdutente, $strUsername, $dateLastAccess, $dateLastPwdupdate, $isReadonly) = mysql_fetch_row($query);
		//aggiorno la data di ultimo accesso
		$strSql = "UPDATE ".$config_table_prefix."users SET ultimoaccesso=CURRENT_TIMESTAMP WHERE id=" . $intIdutente;
		mysql_query ($strSql, $conn);
		return 1;
	} else 
	{
		$intIdutente = 0;
		$strUsername = "";
		$dateLastAccess = "";
		return 0;
	}
}

function checkLoginA($conn, $strLogin, $strPwd, &$intIdutente, &$strUsername, &$dateLastAccess, &$dateLastPwdupdate, &$isReadonly) 
{
	global $config_table_prefix;
	$strSql = "SELECT id, login, ultimoaccesso, ultimopwdmod, isreadonly FROM ".$config_table_prefix."users WHERE login='" . $strLogin . "'  AND pwd='" . $strPwd . "' AND (isdisabled<>1 OR isdisabled IS NULL) AND isbackoffice=1";
  
  $query = mysql_query ($strSql, $conn);
	if (mysql_num_rows($query) > 0) 
	{
		list($intIdutente, $strUsername, $dateLastAccess, $dateLastPwdupdate, $isReadonly) = mysql_fetch_row($query);
		//aggiorno la data di ultimo accesso
		$strSql = "UPDATE ".$config_table_prefix."users SET ultimoaccesso=CURRENT_TIMESTAMP WHERE id=" . $intIdutente;
		mysql_query ($strSql, $conn);
		return 1;
	} else 
	{
		$intIdutente = 0;
		$strUsername = "";
		$dateLastAccess = "";
		return 0;
	}
}

/**
******************************************************************************************
* autenticazione
* @access public        
* @param $conn
* @param $login
* @param $pwd
* @param $idrole
* @param $idutente
* @param $username
* @param $dateLastAccess
* @param $dateLastPwdupdate
* @param $isReadonly
* @return bool
*/
function checkLoginAreariservata($conn, $login, $pwd, $idrole, &$idutente, &$username, &$dateLastAccess, &$dateLastPwdupdate, &$isActivated) 
{
	global $config_table_prefix;
	$sql = "SELECT id, login, ultimoaccesso, ultimopwdmod FROM ".$config_table_prefix."users u LEFT JOIN ".$config_table_prefix."roles_users_nm nm ON nm.idusers=u.id WHERE u.login='" . $login . "'  AND u.pwd=MD5('" . $pwd . "') AND (u.isdisabled<>1 OR u.isdisabled IS NULL) AND nm.idroles=".$idrole;
  $query = mysql_query ($sql, $conn);
	if (mysql_num_rows($query) > 0) 
	{
		list($idutente, $username, $dateLastAccess, $dateLastPwdupdate, $isActivated) = mysql_fetch_row($query);
		//aggiorno la data di ultimo accesso
		$sql = "UPDATE ".$config_table_prefix."users SET ultimoaccesso=CURRENT_TIMESTAMP WHERE id=" . $idutente;
		mysql_query ($sql, $conn);
		return true;
	}
	else 
	{
		$idutente = 0;
		$username = "";
		$dateLastAccess = "";
		return false;
	}
}

function checkLoginAreariservataA($conn, $login, $pwd, $idrole, &$idutente, &$username, &$dateLastAccess, &$dateLastPwdupdate, &$isActivated) 
{
	global $config_table_prefix;
	$sql = "SELECT id, login, ultimoaccesso, ultimopwdmod FROM ".$config_table_prefix."users u LEFT JOIN ".$config_table_prefix."roles_users_nm nm ON nm.idusers=u.id WHERE u.login='" . $login . "'  AND u.pwd='" . $pwd . "' AND (u.isdisabled<>1 OR u.isdisabled IS NULL) AND nm.idroles=".$idrole;
  $query = mysql_query ($sql, $conn);
	if (mysql_num_rows($query) > 0) 
	{
		list($idutente, $username, $dateLastAccess, $dateLastPwdupdate, $isActivated) = mysql_fetch_row($query);
		//aggiorno la data di ultimo accesso
		$sql = "UPDATE ".$config_table_prefix."users SET ultimoaccesso=CURRENT_TIMESTAMP WHERE id=" . $idutente;
		mysql_query ($sql, $conn);
		return true;
	}
	else 
	{
		$idutente = 0;
		$username = "";
		$dateLastAccess = "";
		return false;
	}
}

// ******************************************************************************************
function checkUsernameExists($conn, $strLogin) 
{
	global $config_table_prefix;
	$strSql = "SELECT id FROM ".$config_table_prefix."users WHERE login='" . $strLogin . "'";
	$query = mysql_query ($strSql, $conn);
	if (mysql_num_rows($query) > 0) {
		list($idutente) = mysql_fetch_row($query);
		return $idutente;
	} else {
		return 0;
	}
}

// ******************************************************************************************
function getCurrentUser(&$intIdutente, &$strUsername, $strUrl=false, $exit="") 
{
    global $config_table_prefix, $objUtility;
	if (isset($_SESSION["user_id"]) && isset($_SESSION["user_login"])) {
		$intIdutente = $_SESSION["user_id"];
		$strUsername = $_SESSION["user_login"];
	} else {
    if($exit=="") {
      if (!$strUrl)
  			$strUrl = $objUtility->getPathBackoffice() . "logout.php";
  		header ("Location: " . $strUrl);
  		exit();
		}
	}
}

/**
******************************************************************************************
* restituisce true se l'utente ha il permesso di scaricare il file il cui id  passato come parametro
* @access public        
* @param $conn
* @param int $idusers
* @param int $iddocuments
* @return bool
*/
function checkDocumentsRights($conn, $idusers, $iddocuments) 
{
	global $config_table_prefix, $objUtility;

	$isAuthorized = false;
	$sqlWhere = "WHERE nm.idusers=".$idusers." AND d.id=".$iddocuments." AND ((d.ishidden<>1) OR (d.ishidden IS NULL)) ";
	$sql = "SELECT DISTINCT d.*, o.nome, o.ext, o.path, o.originalname ";
	$sql .= "FROM ".$config_table_prefix."documents d ";
	$sql .= "INNER JOIN ".$config_table_prefix."oggetti o ON o.id=d.idoggetti ";
	$sql .= "INNER JOIN ".$config_table_prefix."roles r ON r.id=d.idroles ";
	$sql .= "INNER JOIN ".$config_table_prefix."roles_users_nm nm ON nm.idroles=r.id ";
	$sql .= $sqlWhere;

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	if (count($rs))
		$isAuthorized = true;

	return $isAuthorized;
}

// ******************************************************************************************
// USERS

// ******************************************************************************************
function getGestione($iduser,$rs,$type) {
  $objConfig = new ConfigTool();
  $objDb = new Db;
  $conn = $objDb->connection($objConfig);
  
  $this->getCurrentUser($intIdutente, $strUsername);
  $isSystem=$this->isSystem($conn, $intIdutente);
  
  $gestione_autonoma=getTable("gestione_utenti_autonoma","","id_users='$iduser'");
  $gestione_autonoma=$gestione_autonoma[0];    
  if($gestione_autonoma['vede_tutti']!=1){
    $on_users=Table2ByTable1("gestione_utenti_autonoma",$type."_list",$gestione_autonoma['id'],"","");
    $newrs=array();
    
    while (list($key, $row) = each($on_users)) {
      reset($rs);
      while (list($key1, $row1) = each($rs)) {
        if($row['id_'.$type]==$row1['id']) {
          if($isSystem || $row1["nome"]!="default") {
            array_push($newrs, $row1);
          }
        }
      }
    }
    return $newrs; 
  }
  return $rs;
}

function getSearch($conn,$ragionesociale, $username, $nome, $cognome, $email, $idroles, $isfull=false) 
{
	global $config_table_prefix, $objUtility;
	
  if ($idroles) {
		$rs = $this->rolesGetUsers($conn, $idroles);
		if (count($rs)) 
		{
			$sqlWhere .= "(";
			while (list($key, $rowTmp) = each($rs)) 
			{ 
				$sqlWhere .= "u.id=" . $rowTmp["id"] . " OR ";
			}
		} 
		else 
		{
			$sqlWhere .= "(u.id=0 OR ";
		}
		$sqlWhere = substr($sqlWhere, 0, strlen($sqlWhere)-4) . ") AND ";
	}
	if ($ragionesociale) $sqlWhere .= "u.ragionesociale LIKE '%" . addslashes($ragionesociale) . "%' AND ";
  if ($username) $sqlWhere .= "u.login LIKE '%" . addslashes($username) . "%' AND ";
	if ($nome) $sqlWhere .= "u.nome LIKE '%" . addslashes($nome) . "%' AND ";
	if ($cognome) $sqlWhere .= "u.cognome LIKE '%" . addslashes($cognome) . "%' AND ";
	if ($email) $sqlWhere .= "u.email LIKE '%" . addslashes($email) . "%' AND ";
	if (!$isfull)
	{
		$sqlWhere .= "((r.issystem<>1) OR (r.issystem IS NULL)) AND ";
		if ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
		$sql = "SELECT DISTINCT u.* FROM ".$config_table_prefix."roles_users_nm nm LEFT JOIN ".$config_table_prefix."roles r ON nm.idroles=r.id LEFT JOIN ".$config_table_prefix."users u ON nm.idusers=u.id ".$sqlWhere;
	}
	else 
	{
		if ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
		$sql = "SELECT * FROM ".$config_table_prefix."users u " . $sqlWhere . " ORDER BY login ASC";
	}

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getSearchAdmin($conn, $ragionesociale, $nome, $cognome, $email) 
{
	global $config_table_prefix, $objUtility;
	$sqlWhere = "";
	if ($ragionesociale) $sqlWhere .= "u.ragionesociale LIKE '%" . addslashes($ragionesociale) . "%' AND ";
	if ($nome) $sqlWhere .= "u.nome LIKE '%" . addslashes($nome) . "%' AND ";
	if ($cognome) $sqlWhere .= "u.cognome LIKE '%" . addslashes($cognome) . "%' AND ";
	if ($email) $sqlWhere .= "u.email LIKE '%" . addslashes($email) . "%' AND ";
	$sqlWhere .= "((r.issystem<>1) OR (r.issystem IS NULL)) AND ";
	if ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT DISTINCT u.* FROM ".$config_table_prefix."users u LEFT JOIN ".$config_table_prefix."roles_users_nm nm ON nm.idusers=u.id LEFT JOIN ".$config_table_prefix."roles r ON nm.idroles=r.id ".$sqlWhere;

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getDetails($conn, $intId) 
{
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."users WHERE id=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function activateWithCode($conn, $intId, $activationcode, $idrolesold, $idrolesnew) {
	global $config_table_prefix;
	$iserror = true;
	// verifico che esista un utente con quell'id e quell'activationcode
	// e che si sia registrato non piu' di 24 ore prima
	$strSql = "SELECT * FROM ".$config_table_prefix."users WHERE id=" . $intId . " AND activationcode='" . $activationcode . "' AND TIMESTAMPDIFF(HOUR, datecreation, now())<=24";
	$query = mysql_query ($strSql, $conn);
	if (mysql_num_rows($query) > 0) {
		//sposto l'utente dal ruolo dei registrati a quello degli attivati
		$strSql = "UPDATE ".$config_table_prefix."roles_users_nm SET idroles=" . $idrolesnew . " WHERE idusers=" . $intId . " AND idroles=" . $idrolesold;
		$query = mysql_query ($strSql, $conn);

		if ($query) {

			//aggiorno la activationdate e azzero l'activationcode
			$strSql = "UPDATE ".$config_table_prefix."users SET isdisabled=0, activationcode=NULL, activationdate=NOW() WHERE id=" . $intId;
			$query = mysql_query ($strSql, $conn);
		
			if ($query) {
				$iserror = false;
			}
		}
	}
	if ($iserror) {
		return 0;
	} else {
		return 1;
	}
}

// ******************************************************************************************
function activate($conn, $idusers, &$errorMsg) 
{
	global $config_table_prefix, $objUtility;

	$sql = "UPDATE ".$config_table_prefix."users SET isactivated=1,activationdate=NOW() WHERE id=".$idusers;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$_SESSION["userris_isactivated"] = 1;
}

/**
******************************************************************************************
* invia una mail quando un cliente si attiva
* @access public        
* @param $conn
* @param int $idusers
* @param string $errorMsg
* @return 
*/	
function activateSend($conn, $idusers, &$errorMsg)
{
	global $objConfig, $objUsers;

	$rs = $objUsers->getDetails($conn, $idusers);
	if (count($rs))
		list($key, $row) = each($rs);
		
	$message = "La informiamo che il seguente cliente ha appena attivato la propria utenza\n\n";
	$message .= "------------------------------------------------------------\n";
	$message .= "Ragione sociale: " . $row["ragionesociale"] . "\n";
	$message .= "Codice cliente: " . $row["codicecliente"] . "\n";
	$message .= "Email: " . $row["email"] . "\n";
	$message .= "Telefono: " . $row["telefono"] . "\n";
	$message .= "\n";
	
	$objMail = new PHPMailer();
	$objMail->From = $objConfig->get("email-from");
	$objMail->FromName = $objConfig->get("email-fromname");
	$objMail->AddAddress($objConfig->get("email-from"));
	$objMail->Subject = "Attivazione [www.ceuimpianti.it]";
	$objMail->Body = $message;
	$esito = $objMail->Send();
	if (!$esito)
		$errorMsg .= "errore durante l'invio dell'email al cliente";	
}

// ******************************************************************************************
function insert2($conn, &$id, $username, $pwd, $nome, $cognome, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $isdisabled, $isbackoffice, $activationcode, &$errorMsg) {
	global $config_table_prefix;
	$objUtility = new Utility;

	$usernameSql = $objUtility->translateForDb($username, "string");
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$cognomeSql = $objUtility->translateForDb($cognome, "string");
	$indirizzoSql = $objUtility->translateForDb($indirizzo, "string");
	$cittaSql = $objUtility->translateForDb($citta, "string");
	$capSql = $objUtility->translateForDb($cap, "string");
	$provinciaSql = $objUtility->translateForDb($provincia, "string");
	$nazioneSql = $objUtility->translateForDb($nazione, "string");
	$telefonoSql = $objUtility->translateForDb($telefono, "string");
	$faxSql = $objUtility->translateForDb($fax, "string");
	$emailSql = $objUtility->translateForDb($email, "string");
  $noteSql = $objUtility->translateForDb($note, "string");
	$isdisabledSql = $objUtility->translateForDb($isdisabled, "int", "", false, false);
	$isbackofficeSql = $objUtility->translateForDb($isbackoffice, "int", "", false, false);
	$activationcodeSql = $objUtility->translateForDb($activationcode, "string");

	$sql = "INSERT INTO ".$config_table_prefix."users (login, pwd, nome, cognome, indirizzo, citta, cap, provincia, nazione, telefono, fax, email, note, isdisabled, isbackoffice, activationcode, datecreation)";
	$sql .= " VALUES (" . $usernameSql . ", MD5('".$pwd."')," . $nomeSql."," . $cognomeSql."," . $indirizzoSql."," . $cittaSql."," . $capSql."," . $provinciaSql."," . $nazioneSql."," . $telefonoSql."," . $faxSql."," . $emailSql . "," . $noteSql."," . $isdisabledSql."," . $isbackofficeSql."," . $activationcodeSql.",NOW())";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn)) {
		$id = mysql_insert_id($conn);
	}
}

// ******************************************************************************************
function update2($conn, &$id, $username, $pwd, $nome, $cognome, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $isdisabled, $isbackoffice, $activationcode, &$strError) 
{
	global $config_table_prefix;
	$objUtility = new Utility;
	$usernameSql = $objUtility->translateForDb($username, "string");
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$cognomeSql = $objUtility->translateForDb($cognome, "string");
	$indirizzoSql = $objUtility->translateForDb($indirizzo, "string");
	$cittaSql = $objUtility->translateForDb($citta, "string");
	$capSql = $objUtility->translateForDb($cap, "string");
	$provinciaSql = $objUtility->translateForDb($provincia, "string");
	$nazioneSql = $objUtility->translateForDb($nazione, "string");
	$telefonoSql = $objUtility->translateForDb($telefono, "string");
	$faxSql = $objUtility->translateForDb(fax, "string");
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
	$strSql .= "nome=" . $nomeSql . ", ";
	$strSql .= "cognome=" . $cognomeSql . ", ";
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
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}



// ******************************************************************************************
function insert($conn, &$id, $img, $username, $pwd, $ragionesociale, $partitaiva, $codicefiscale, $nome, $cognome, $indirizzo, $citta, $cap, $provincia, $nazione, $comune, $regione_estera, $telefono, $cellulare, $fax, $email, $sito, $note, $isdisabled, $isbackoffice, $activationcode, $data_di_nascita, $provincia_di_nascita, $sesso, $professione, $stato_civile, $nato_a, $hobby1, $hobby2, $hobby3, $regione, $titolo_di_studio, $nucleo_familiare, &$errorMsg) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$imgSql = $objUtility->translateForDb($img, "int");
  $usernameSql = $objUtility->translateForDb($username, "string");
	$ragionesocialeSql = $objUtility->translateForDb($ragionesociale, "string");
	$partitaivaSql = $objUtility->translateForDb($partitaiva, "string");
	$codicefiscaleSql = $objUtility->translateForDb($codicefiscale, "string");
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$cognomeSql = $objUtility->translateForDb($cognome, "string");
	$indirizzoSql = $objUtility->translateForDb($indirizzo, "string");
	$cittaSql = $objUtility->translateForDb($citta, "string");
	$capSql = $objUtility->translateForDb($cap, "string");
	$provinciaSql = $objUtility->translateForDb($provincia, "string");
	$nazioneSql = $objUtility->translateForDb($nazione, "string");
	$telefonoSql = $objUtility->translateForDb($telefono, "string");
	$cellulareSql = $objUtility->translateForDb($cellulare, "string");
	$faxSql = $objUtility->translateForDb($fax, "string");
	$emailSql = $objUtility->translateForDb($email, "string");
	$sitoSql = $objUtility->translateForDb($sito, "string");
  $noteSql = $objUtility->translateForDb($note, "string");
	$isdisabledSql = $objUtility->translateForDb($isdisabled, "int", "", false, false);
	$isbackofficeSql = $objUtility->translateForDb($isbackoffice, "int", "", false, false);
	$activationcodeSql = $objUtility->translateForDb($activationcode, "string");
  $comuneSql = $objUtility->translateForDb($comune, "string");
  $regione_esteraSql = $objUtility->translateForDb($regione_estera, "string"); 

  $data_di_nascitaSql = $objUtility->translateForDb($data_di_nascita, "string");
	$provincia_di_nascitaSql = $objUtility->translateForDb($provincia_di_nascita, "string");
	$sessoSql = $objUtility->translateForDb($sesso, "int");
	$professioneSql = $objUtility->translateForDb($professione, "int");
	$stato_civileSql = $objUtility->translateForDb($stato_civile, "int");
	$nato_aSql = $objUtility->translateForDb($nato_a, "string");
	$hobby1Sql = $objUtility->translateForDb($hobby1, "int");
	$hobby2Sql = $objUtility->translateForDb($hobby2, "int");
	$hobby3Sql = $objUtility->translateForDb($hobby3, "int");
	$regioneSql = $objUtility->translateForDb($regione, "string");
	$titolo_di_studioSql = $objUtility->translateForDb($titolo_di_studio, "int");
  $nucleo_familiareSql = $objUtility->translateForDb($nucleo_familiare, "string");

	$sql = "INSERT INTO ".$config_table_prefix."users (immagine_file, login, pwd, ragionesociale, partitaiva, codicefiscale, nome, cognome, indirizzo, citta, cap, provincia, nazione, comune, regione_estera, telefono, cellulare, fax, email, sito, note, isdisabled, isbackoffice, activationcode, datecreation, data_di_nascita, provincia_di_nascita, nato_a, id_sesso, id_professione, id_stato_civile, id_hobby1, id_hobby2, id_hobby3, regione, id_titolo_di_studio, nucleo_familiare)";
	$sql .= " VALUES (".$imgSql.",".$usernameSql.", MD5('".$pwd."'),".$ragionesocialeSql.",".$partitaivaSql.",".$codicefiscaleSql.",".$nomeSql.",".$cognomeSql.",".$indirizzoSql.",".$cittaSql.",".$capSql.",".$provinciaSql.",".$nazioneSql.",".$comuneSql.",".$regione_esteraSql.",".$telefonoSql.",".$cellulareSql.",".$faxSql.",".$emailSql.",".$sitoSql.",".$noteSql.",".$isdisabledSql.",".$isbackofficeSql.",".$activationcodeSql.",NOW(),".$data_di_nascitaSql.",".$provincia_di_nascitaSql.",".$nato_aSql.",".$sessoSql.",".$professioneSql.",".$stato_civileSql.",".$hobby1Sql.",".$hobby2Sql.",".$hobby3Sql.",".$regioneSql.",".$titolo_di_studioSql.",".$nucleo_familiareSql.")";
	
	//echo $sql;exit;
	
  mysql_query($sql, $conn);
  $errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
  if (!mysql_errno($conn) && !mysql_error($conn)) {
		$id = mysql_insert_id($conn);
    
    $gestid=getTable("gestione_utenti_autonoma","","id_users='".$_SESSION['user_id']."'");
    $gestid=$gestid[0]['id'];
    if($gestid==""){
      $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma` (id_users, vede_tutti) VALUES ('".$_SESSION['user_id']."', '0')";
      mysql_query($sql, $conn);
      $gestid=mysql_insert_id($conn);  
    }
    
    $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma` (id_users, vede_tutti) VALUES ('$id', 0)";
    mysql_query($sql, $conn);
    $refgest=mysql_insert_id($conn);
    
    $sql="INSERT INTO `".$config_table_prefix."users_list` (id_users) VALUES ('$id')";
    mysql_query($sql, $conn);
    $iduserslist=mysql_insert_id($conn);
    
    $rs=getTable("roles","","nome='default'");
    $sql="INSERT INTO `".$config_table_prefix."roles_list` (id_roles) VALUES ('".$rs[0]['id']."')";
    mysql_query($sql, $conn);
    $idDefaultRol=mysql_insert_id($conn);
    
    $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` (id_gestione_utenti_autonoma,id_roles_list) VALUES ('$refgest', '$idDefaultRol')";
    mysql_query($sql, $conn);
    
    $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma#users_list_nm` (id_gestione_utenti_autonoma,id_users_list) VALUES ('$gestid', '$iduserslist')";
    mysql_query($sql, $conn);     
	}
}

// ******************************************************************************************
function insertRegistration($conn, &$id, $username, $pwd, $email, $telefono, $referente, $ragionesociale, $provincia, &$errorMsg) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$note = "Referente: ".$referente;
	$usernameSql = $objUtility->translateForDb($username, "string");
	$emailSql = $objUtility->translateForDb($email, "string");
	$telefonoSql = $objUtility->translateForDb($telefono, "string");
	$noteSql = $objUtility->translateForDb($note, "string");
	$ragionesocialeSql = $objUtility->translateForDb($ragionesociale, "string");
	$provinciaSql = $objUtility->translateForDb($provincia, "string");
	$isdisabled=0;
	$isreadonly=0;
	$isbackoffice=0;
	
	$sql = "INSERT INTO ".$config_table_prefix."users (login,pwd,email,telefono,ragionesociale,provincia,note,isdisabled,isreadonly,isbackoffice,datecreation)";
	$sql .= " VALUES (".$usernameSql.",MD5('".$pwd."'),".$emailSql.",".$telefonoSql.",".$ragionesocialeSql.",".$provinciaSql.",".$noteSql.",".$isdisabled.",".$isreadonly.",".$isbackoffice.",NOW())";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn)) {
		$id = mysql_insert_id($conn);
	}
}

/**
******************************************************************************************
* invia una mail relativa al form "Contattaci"
* @access public        
* @param $conn
* @param string $nome
* @param string $email
* @param string $msg
* @return 
*/	
function registrationSend($conn, $idusers, &$errorMsg)
{
	global $objConfig;

	$rs = $this->getDetails($conn, $idusers);
	if (count($rs) > 0)
		list($key, $row) = each($rs);
	
	$message = "La informiamo che la seguente richiesta di registrazione e' stata inoltrata\n\n";
	$message .= "------------------------------------------------------------\n";
	$message .= "Email: " . $row["email"] . "\n";
	$message .= "Telefono: " . $row["telefono"] . "\n";
	$message .= $row["note"] . "\n";
	$message .= "Ragione sociale: " . $row["ragionesociale"] . "\n";
	$message .= "Provincia: " . $row["provincia"] . "\n";
	$message .= "\n";
	
	$objMail = new PHPMailer();
	$objMail->From = $objConfig->get("email-from");
	$objMail->FromName = $objConfig->get("email-fromname");
	$objMail->AddAddress("info@ceuimpianti.it");
	$objMail->Subject = "Registrazione [www.ceuimpianti.it]";
	$objMail->Body = $message;
	$esito = $objMail->Send();
	if (!$esito)
		$errorMsg .= "errore durante l'invio dell'email al cliente";	
}

// ******************************************************************************************
function update($conn, &$id, $img, $username, $pwd, $ragionesociale, $partitaiva, $codicefiscale, $nome, $cognome, $indirizzo, $citta, $cap, $provincia, $nazione, $comune, $regione_estera, $telefono, $cellulare, $fax, $email, $sito, $note, $isdisabled, $isbackoffice, $activationcode, $data_di_nascita, $provincia_di_nascita, $sesso, $professione, $stato_civile, $nato_a, $hobby1, $hobby2, $hobby3, $regione, $titolo_di_studio, $nucleo_familiare, &$strError) 
{
	global $config_table_prefix, $objUtility;
	$imgSql = $objUtility->translateForDb($img, "int");
  $usernameSql = $objUtility->translateForDb($username, "string");
	$ragionesocialeSql = $objUtility->translateForDb($ragionesociale, "string");
	$partitaivaSql = $objUtility->translateForDb($partitaiva, "string");
	$codicefiscaleSql = $objUtility->translateForDb($codicefiscale, "string");
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$cognomeSql = $objUtility->translateForDb($cognome, "string");
	$indirizzoSql = $objUtility->translateForDb($indirizzo, "string");
	$cittaSql = $objUtility->translateForDb($citta, "string");
	$capSql = $objUtility->translateForDb($cap, "string");
	$provinciaSql = $objUtility->translateForDb($provincia, "string");
	$nazioneSql = $objUtility->translateForDb($nazione, "string");
	$telefonoSql = $objUtility->translateForDb($telefono, "string");
	$cellulareSql = $objUtility->translateForDb($cellulare, "string");
	$faxSql = $objUtility->translateForDb(fax, "string");
	$emailSql = $objUtility->translateForDb($email, "string");
	$sitoSql = $objUtility->translateForDb($sito, "string");
  $noteSql = $objUtility->translateForDb($note, "string");
	$isdisabledSql = $objUtility->translateForDb($isdisabled, "int");
	$isbackofficeSql = $objUtility->translateForDb($isbackoffice, "int");
	$activationcodeSql = $objUtility->translateForDb($activationcode, "string");
	$comuneSql = $objUtility->translateForDb($comune, "string");
  $regione_esteraSql = $objUtility->translateForDb($regione_estera, "string");
	
	
	$data_di_nascitaSql = $objUtility->translateForDb($data_di_nascita, "string");
	$provincia_di_nascitaSql = $objUtility->translateForDb($provincia_di_nascita, "string");
	$sessoSql = $objUtility->translateForDb($sesso, "int");
	$professioneSql = $objUtility->translateForDb($professione, "int");
	$stato_civileSql = $objUtility->translateForDb($stato_civile, "int");
	$nato_aSql = $objUtility->translateForDb($nato_a, "string");
	$hobby1Sql = $objUtility->translateForDb($hobby1, "int");
	$hobby2Sql = $objUtility->translateForDb($hobby2, "int");
	$hobby3Sql = $objUtility->translateForDb($hobby3, "int");
	$regioneSql = $objUtility->translateForDb($regione, "string");
	$titolo_di_studioSql = $objUtility->translateForDb($titolo_di_studio, "int");
	$nucleo_familiareSql = $objUtility->translateForDb($nucleo_familiare, "string");
	
	$strSql = "UPDATE ".$config_table_prefix."users SET ";
	if ($username)
		$strSql .= "login=".$usernameSql.", ";
	if ($pwd)
		$strSql .= "pwd=MD5('".$pwd."'), ";
	$strSql .= "ragionesociale=" . $ragionesocialeSql . ", ";
	$strSql .= "partitaiva=" . $partitaivaSql . ", ";
	$strSql .= "codicefiscale=" . $codicefiscaleSql . ", ";
	$strSql .= "nome=" . $nomeSql . ", ";
	$strSql .= "cognome=" . $cognomeSql . ", ";
	$strSql .= "indirizzo=" . $indirizzoSql . ", ";
	$strSql .= "citta=" . $cittaSql . ", ";
	$strSql .= "cap=" . $capSql . ", ";
	$strSql .= "provincia=" . $provinciaSql . ", ";
	$strSql .= "nazione=" . $nazioneSql . ", ";
	$strSql .= "comune=" . $comuneSql . ", ";
	$strSql .= "regione_estera=" . $regione_esteraSql . ", ";
	$strSql .= "telefono=" . $telefonoSql . ", ";
	$strSql .= "cellulare=" . $cellulareSql . ", ";
  $strSql .= "fax=" . $faxSql . ", ";
	$strSql .= "email=" . $emailSql . ", ";
	$strSql .= "sito=" . $sitoSql . ", ";
  $strSql .= "note=" . $noteSql . ", ";
	$strSql .= "isdisabled=" . $isdisabled . ", ";
	if($imgSql>0) $strSql .= "immagine_file=" . $imgSql . ", ";
	$strSql .= "data_di_nascita=" . $data_di_nascitaSql . ", ";
	$strSql .= "provincia_di_nascita=" . $provincia_di_nascitaSql . ", ";
	$strSql .= "nato_a=" . $nato_aSql . ", ";
	$strSql .= "id_sesso=" . $sessoSql . ", ";
	$strSql .= "id_professione=" . $professioneSql . ", ";
  $strSql .= "id_stato_civile=" . $stato_civileSql . ", ";
	$strSql .= "id_hobby1=" . $hobby1Sql . ", ";
	$strSql .= "id_hobby2=" . $hobby2Sql . ", ";
	$strSql .= "id_hobby3=" . $hobby3Sql . ", ";
	$strSql .= "regione=" . $regioneSql . ", ";
	$strSql .= "id_titolo_di_studio=" . $titolo_di_studioSql . ", ";
	$strSql .= "nucleo_familiare=" . $nucleo_familiareSql . ", ";
	
	$strSql .= "isbackoffice=" . $isbackoffice;
	
	$strSql .= " WHERE id=" . $id;

	mysql_query($strSql, $conn);
	$sqlError .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

// ******************************************************************************************
function delete($conn, $id, &$strError) 
{
	global $config_table_prefix;
	$strError = false;
	mysql_query ("DELETE FROM ".$config_table_prefix."roles_users_nm WHERE idusers=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
	mysql_query ("DELETE FROM ".$config_table_prefix."users WHERE id=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
	
	$gestione_utenti=getTable("gestione_utenti_autonoma","","id_users='$id'");
	mysql_query ("DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma` WHERE id='" . $gestione_utenti[0]['id'] . "'", $conn);
	$rs=getTable("users_list","","id_users='$id'");
  while (list($key, $row) = each($rs)) {
    mysql_query ("DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma#users_list_nm` WHERE id='".$row['id']."'", $conn);    
  }
  mysql_query ("DELETE FROM `".$config_table_prefix."users_list` WHERE id_users='$id'", $conn);
	
  if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
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

// ******************************************************************************************
function usersGetRoles($conn, $idusers, $isfull=false)  
{
	global $config_table_prefix, $objUtility;
	if (!$isfull) $sqlWhere = " AND ((r.issystem<>1) OR (r.issystem IS NULL))";
	$strSql = "SELECT r.* FROM ".$config_table_prefix."roles_users_nm nm LEFT JOIN ".$config_table_prefix."roles r ON nm.idroles=r.id WHERE nm.idusers=".$idusers . $sqlWhere;
  $query = mysql_query ($strSql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

function usersGetRolesEx($conn, $idusers, $isfull=false) {
	global $config_table_prefix;
	$objUtility = new Utility;
	
  if (!$isfull) $sqlWhere = " AND ((r.issystem<>1) OR (r.issystem IS NULL))";

  $strSql = "SELECT * FROM ".$config_table_prefix."roles INNER JOIN (SELECT * FROM `".$config_table_prefix."roles_users_nm` WHERE idusers='$idusers') AS T1 ON ".$config_table_prefix."roles.id=T1.idroles ";	
  
  $query = mysql_query ($strSql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

function isSystem($conn, $idusers) {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  $rs=$this->usersGetRolesEx($conn, $idusers);
  while (list($key, $row) = each($rs)) {  
    if($row['issystem']=='1') return true;
  }
  return false;
}

function rolesByTable($table,$table1_value) {
  global $config_table_prefix;
  $objUtility = new Utility;

  $table1="tabelle";
  $table2="black_list";
  $tid="roles";

  $sql = "SELECT ".$config_table_prefix.$table2.".* FROM ".$config_table_prefix.$table2." INNER JOIN (SELECT * FROM `".$config_table_prefix.$table1."#".$table2."_nm` WHERE id_".$table1."='$table1_value') AS T1 ON ".$config_table_prefix.$table2.".id=T1.id_".$tid." $where $order";
  
  $query = mysql_query ($sql);

  $utility = new Utility;
  $rs = $utility->buildRecordset($query);
  return $rs;
}

function tablesByRole($role) {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  $sql1 = "SELECT ".$config_table_prefix."black_list.* FROM ".$config_table_prefix."black_list INNER JOIN ".$config_table_prefix."roles ON ".$config_table_prefix."black_list.id_roles=".$config_table_prefix."roles.id WHERE ".$config_table_prefix."roles.id='$role'";
  $sql2 = "SELECT `".$config_table_prefix."tabelle#black_list_nm`.* FROM `".$config_table_prefix."tabelle#black_list_nm` INNER JOIN ($sql1) AS T1 ON `".$config_table_prefix."tabelle#black_list_nm`.id_roles=T1.id";
  $sql = "SELECT ".$config_table_prefix."tabelle.* FROM ".$config_table_prefix."tabelle INNER JOIN ($sql2) AS T2 ON ".$config_table_prefix."tabelle.id=T2.id_tabelle  ";	
  $query = mysql_query ($sql);
  
  $utility = new Utility;
  $rs = $utility->buildRecordset($query);
  return $rs;
}

/**
******************************************************************************************
* invia una mail relativa al form "Contattaci"
* @access public        
* @param $conn
* @param int $idusers
* @param bool $isfull: se false vengono restituiti solo i ruoli non di sistema
* @return 
*/	
function usersGetRolesAvailable($conn, $idusers, $isfull=false) 
{
	global $config_table_prefix, $objUtility;

	$sqlWhere = "";
	$rsTmp = $this->usersGetRoles($conn, $idusers);
	if (count($rsTmp) > 0) 
	{
		$sqlWhere .= "(";
		while (list($key, $rowTmp) = each($rsTmp))
		{
			$sqlWhere .= "r.id<>" . $rowTmp["id"] . " AND ";
		}
		$sqlWhere = substr($sqlWhere, 0, strlen($sqlWhere)-5) . ") AND ";
	}
	if (!$isfull) $sqlWhere .= "((r.issystem<>1) OR (r.issystem IS NULL)) AND ";
	if ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);

	$sql = "SELECT r.* FROM ".$config_table_prefix."roles r ".$sqlWhere;

	$query = mysql_query ($sql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function usersRolesDelete($conn, $idusers, $idroles, &$errorMsg)
{
	global $config_table_prefix, $objUtility;
	$sql = "DELETE FROM ".$config_table_prefix."roles_users_nm WHERE idusers=".$idusers." AND idroles=".$idroles;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

// ******************************************************************************************
function usersRolesIns($conn, $idusers, $idroles, &$errorMsg) 
{
	global $config_table_prefix;
	$objUtility = new Utility;
	$sql = "INSERT INTO ".$config_table_prefix."roles_users_nm (idroles, idusers) VALUES (" . $idroles . ", " . $idusers . ")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

// ******************************************************************************************
function passwordUpdate($conn, $intIdutente, $strPwd, $errorMsg="")
{
	global $config_table_prefix, $objUtility;
	$strSql = "UPDATE ".$config_table_prefix."users SET pwd=MD5('" . $strPwd . "') WHERE id=" . $intIdutente;
	$query = mysql_query($strSql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
	{
		//aggiorno la data di ultimo cambio password
		$strSql = "UPDATE ".$config_table_prefix."users SET ultimopwdmod=CURRENT_TIMESTAMP WHERE id=" . $intIdutente;
		mysql_query($strSql, $conn);
	}
}

// ******************************************************************************************
function generateRandomPassword() 
{
	$string = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,:;#@";

	srand((double)microtime()*1000000);
	$pwd = "";
	for ($i=1; $i<=8; $i++) {
		$pos = rand(0, strlen($string));
		$char = substr ($string, $pos, 1);
		$pwd .= $char;
	}
	return $pwd;
}

/**
******************************************************************************************
* spedisce la mail con la notifica della nuova password
* @access public        
* @param $conn
* @param int $idusers
* @param string $password
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function sendMailPwd($conn, $idusers, $password, &$errorMsg)
{
	global $objUsers, $objUtility, $objConfig;

	$rs = $objUsers->getDetails($conn, $idusers);
	if (count($rs))
		list($key, $rowUsers) = each($rs);

	$message = "Di seguito le credenziali per accedere all'area riservata di: http://www.ceuimpianti.it\n\n";
	$message .= "username: " . $rowUsers["login"] . "\n";
	$message .= "password: " . $password . "\n\n";
	$message .= "Una volta effettuato il primo accesso all'area riservata, Le consigliamo di modificare la Sua password cliccando sulla voce 'modifica password', e appuntarla in modo sicuro.\n\n";
	$message .= "Buon lavoro.\n\n";

	$objMail = new PHPMailer();
	$objMail->From = $objConfig->get("email-from");
	$objMail->FromName = $objConfig->get("email-fromname");
	$objMail->AddAddress($rowUsers["email"]);
	$objMail->Subject = "Nuova password [www.ceuimpianti.it]";
	$objMail->Body = $message;
	$esito = $objMail->Send();
	if (!$esito)
		$errorMsg .= "errore durante l'invio dell'email al cliente";			
}

// ******************************************************************************************
function generateActivationCode () 
{
	$strChars = date("YmdHis", time());
	srand((double)microtime()*1000000);
	for ($i=1; $i<=3; $i++) {
		$intPos = rand(0, strlen($this->letters));
		$chrChar = substr ($this->letters, $intPos, 1);
		$strChars .= $chrChar;
	}
	return md5($strChars);
}

// ******************************************************************************************
// ROLES

// ******************************************************************************************
function rolesGetSearch($conn, $nome, $isfull=false)
{
	global $config_table_prefix, $objUtility;

	$sqlWhere = "";
	If ($nome) $sqlWhere .= "nome LIKE '%" . addslashes($nome) . "%' AND ";
	if (!$isfull) $sqlWhere .= "((issystem<>1) OR (issystem IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT * FROM ".$config_table_prefix."roles" . $sqlWhere . " ORDER BY nome ASC";
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function rolesGetDetails($conn, $intId) 
{
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."roles WHERE id=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function rolesInsert($conn, &$id, $nome, $issystem, &$strError) 
{
	global $config_table_prefix, $objUtility;

	$nomeSql = $objUtility->translateForDb($nome, "string");
	$issystemSql = $objUtility->translateForDb($issystem, "int");

	$strSql = "INSERT INTO ".$config_table_prefix."roles (nome,issystem)";
	$strSql .= " VALUES (".$nomeSql.",".$issystemSql.")";
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	} else {
		$id = mysql_insert_id($conn);
		
		$gestid=getTable("gestione_utenti_autonoma","","id_users='".$_SESSION['user_id']."'");
    $gestid=$gestid[0]['id'];
    if($gestid==""){
      $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma` (id_users, vede_tutti) VALUES ('".$_SESSION['user_id']."', 0)";
      mysql_query($sql, $conn);
      $gestid=mysql_insert_id($conn);   
    }
    
    $sql="INSERT INTO `".$config_table_prefix."roles_list` (id_roles) VALUES ('$id')";
    mysql_query($sql, $conn);
    $idroleslist=mysql_insert_id($conn);
    
    $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` (id_gestione_utenti_autonoma,id_roles_list) VALUES ('$gestid', '$idroleslist')";
    mysql_query($sql, $conn);
	}
}

// ******************************************************************************************
function rolesUpdate($conn, $id, $nome, $issystem, &$strError) 
{
	global $config_table_prefix, $objUtility;

	$nomeSql = $objUtility->translateForDb($nome, "string");
	$issystemSql = $objUtility->translateForDb($issystem, "int");

	$sql = "UPDATE ".$config_table_prefix."roles SET ";
	$sql .= "nome=".$nomeSql.", ";
	$sql .= "issystem=".$issystemSql;
	$sql .= " WHERE id=".$id;

	mysql_query($sql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function rolesDelete($conn, $id, &$strError) 
{
	global $config_table_prefix;
	$strError = false;
	mysql_query ("DELETE FROM ".$config_table_prefix."roles_users_nm WHERE idroles=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
	mysql_query ("DELETE FROM ".$config_table_prefix."roles_menu_nm WHERE idroles=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
	mysql_query ("DELETE FROM ".$config_table_prefix."roles WHERE id=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
	
	$rs=getTable("roles_list","","id_roles='$id'");
  while (list($key, $row) = each($rs)) {
    mysql_query ("DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` WHERE id='".$row['id']."'", $conn);    
  }
  mysql_query ("DELETE FROM `".$config_table_prefix."roles_list` WHERE id_roles='$id'", $conn);
}

// ******************************************************************************************
function getRolesMenu($conn, $intId) 
{
	global $config_table_prefix;
	$strSql = "SELECT idmenu id FROM ".$config_table_prefix."roles_menu_nm WHERE idroles=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function rolesMenuDelete($conn, $idroles, &$strError) 
{
	global $config_table_prefix;
	$strSql = "DELETE FROM ".$config_table_prefix."roles_menu_nm WHERE idroles=" . $idroles;
	mysql_query ($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function rolesMenuIns($conn, $idroles, $idmen, &$strError) 
{
	global $config_table_prefix;
	$strSql = "INSERT INTO ".$config_table_prefix."roles_menu_nm (idmenu, idroles) VALUES (" . $idmen . ", " . $idroles . ")";
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function rolesGetUsers($conn, $intId) 
{
	global $config_table_prefix;
	$strSql = "SELECT idusers id FROM ".$config_table_prefix."roles_users_nm WHERE idroles=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function rolesUsersDelete($conn, $idroles, &$strError) 
{
	global $config_table_prefix;
	$strSql = "DELETE FROM ".$config_table_prefix."roles_users_nm WHERE idroles=" . $idroles;
	mysql_query ($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function rolesUsersIns($conn, $idroles, $idusers, &$strError) 
{
	global $config_table_prefix;
	$strSql = "INSERT INTO ".$config_table_prefix."roles_users_nm (idroles, idusers) VALUES (" . $idroles . ", " . $idusers . ")";
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
// CONTATTACI

/**
******************************************************************************************
* invia una mail relativa al form "Contattaci"
* @access public        
* @param $conn
* @param string $nome
* @param string $email
* @param string $msg
* @return 
*/	
function contactSend($conn, $nome, $email, $msg, &$errorMsg)
{
	global $objConfig;

	$message = "La informiamo che la seguente richiesta di informazioni e' stata inoltrata\n\n";
	$message .= "------------------------------------------------------------\n";
	$message .= "Nome: " . $nome . "\n";
	$message .= "Email: " . $email . "\n";
	$message .= "Messaggio: " . $msg . "\n";
	$message .= "\n";
	
	$objMail = new PHPMailer();
	$objMail->From = $objConfig->get("email-from");
	$objMail->FromName = $objConfig->get("email-fromname");
	$objMail->AddAddress($objConfig->get("email-from"));
	$objMail->Subject = "Contatti [www.castagnoligiuseppe.it]";
	$objMail->Body = $message;
	$esito = $objMail->Send();
	if (!$esito)
		$errorMsg .= "errore durante l'invio dell'email al cliente";	
}

/**
******************************************************************************************
* aggiunge l'utente e lo associa al ruolo dei contatti, considera come login l'email (se esiste gi un utente con quell'email, non lo inserisce)
* @access public        
* @param $conn
* @param int $id: l'id del record inserito
* @param string $nome
* @param string $email
* @param string $message
* @param string $strError: in caso d'errore mysql, restituisce il dettaglio errore
* @return 
*/	
function contactInsert($conn, &$id, $nome, $email, $message, $idrole, &$errorMsg)
{
	global $config_table_prefix, $objConfig, $objUtility;
	$rs = $this->getSearch($conn, false, false, false, $email, false, false);
	if (!count($rs)) 
	{
		$isdisabled = 1;
		$isbackoffice = 0;
		$activationcode = false;
		$this->insert($conn, $id, $email, $email, false, false, false, $nome, false, false, false, false, false, false, false, false, $email, $message, $isdisabled, $isbackoffice, $activationcode, $errorMsg);
		$this->usersRolesIns($conn, $id, $idrole, $errorMsg);
	}
}

}
?><?php //#rs-enc-module123;# ?>