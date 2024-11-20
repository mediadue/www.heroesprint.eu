<?php
class Users {

// ******************************************************************************************
function checkLogin($conn, $strLogin, $strPwd, &$intIdutente, &$strUsername, &$dateLastAccess, &$dateLastPwdupdate, &$isReadonly) {
	global $config_table_prefix;
	$strSql = "SELECT id, login, ultimoaccesso, ultimopwdmod, isreadonly FROM ".$config_table_prefix."users WHERE login='" . $strLogin . "'  AND pwd=MD5('" . $strPwd . "') AND isdisabled=0 AND isbackoffice=1";
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

// ******************************************************************************************
function checkLoginAreariservata($conn, $strLogin, $strPwd, &$intIdutente, &$strUsername) {
	global $config_table_prefix;
	$strSql = "SELECT id, login, ultimoaccesso, ultimopwdmod, isreadonly FROM ".$config_table_prefix."users u LEFT JOIN ".$config_table_prefix."roles_users_nm nm ON nm.idusers=u.id WHERE login='" . $strLogin . "'  AND pwd=MD5('" . $strPwd . "') AND isdisabled=0 AND nm.idroles=4";
	$query = mysql_query ($strSql, $conn);
	if (mysql_num_rows($query) > 0) {
		list($intIdutente, $strUsername, $dateLastAccess, $dateLastPwdupdate, $isReadonly) = mysql_fetch_row($query);
		//aggiorno la data di ultimo accesso
		$strSql = "UPDATE ".$config_table_prefix."users SET ultimoaccesso=CURRENT_TIMESTAMP WHERE id=" . $intIdutente;
		mysql_query ($strSql, $conn);
		return 1;
	} else {
		$intIdutente = 0;
		$strUsername = "";
		$dateLastAccess = "";
		return 0;
	}
}

// ******************************************************************************************
function checkUsernameExists($conn, $strLogin) {
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
function getCurrentUser(&$intIdutente, &$strUsername, $strUrl=false) {
	global $config_table_prefix, $objUtility;
	if (isset($_SESSION["user_id"]) && isset($_SESSION["user_login"])) {
		$intIdutente = $_SESSION["user_id"];
		$strUsername = $_SESSION["user_login"];
	} else {
		if (!$strUrl)
			$strUrl = $objUtility->getPathBackoffice() . "logout.php";
		header ("Location: " . $strUrl);
		exit();
	}
}

// ******************************************************************************************
// USERS

// ******************************************************************************************
function getRicerca($conn, $strUsername, $strNome, $strCognome, $strEmail, $idroles) {
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

// ******************************************************************************************
function getDetails($conn, $intId) {
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."users WHERE id=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function activate($conn, $intId, $activationcode, $idrolesold, $idrolesnew) {
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
function insert($conn, &$id, $username, $pwd, $nome, $cognome, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $isdisabled, $isbackoffice, $activationcode, &$strError) {
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

	$strSql = "INSERT INTO ".$config_table_prefix."users (login, pwd, codice, nome, cognome, indirizzo, citta, cap, provincia, nazione, telefono, fax, email, note, isdisabled, isbackoffice, activationcode, datecreation)";
	$strSql .= " VALUES (" . $usernameSql . ", MD5('".$pwd."')," . $codiceSql."," . $nomeSql."," . $cognomeSql."," . $indirizzoSql."," . $cittaSql."," . $capSql."," . $provinciaSql."," . $nazioneSql."," . $telefonoSql."," . $faxSql."," . $emailSql."," . $noteSql."," . $isdisabledSql."," . $isbackofficeSql."," . $activationcodeSql.", NOW())";

	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	} else {
		$id = mysql_insert_id($conn);
	}
}

// ******************************************************************************************
function update($conn, &$id, $username, $pwd, $nome, $cognome, $indirizzo, $citta, $cap, $provincia, $nazione, $telefono, $fax, $email, $note, $isdisabled, $isbackoffice, $activationcode, &$strError) {
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
function delete($conn, $id, &$strError) {
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
}

// ******************************************************************************************
function getUsersRoles($conn, $intId) {
	global $config_table_prefix;
	$strSql = "SELECT idroles id FROM ".$config_table_prefix."roles_users_nm WHERE idusers=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function usersRolesDelete($conn, $idusers, &$strError) {
	global $config_table_prefix;
	$strSql = "DELETE FROM ".$config_table_prefix."roles_users_nm WHERE idusers=" . $idusers;
	mysql_query ($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function usersRolesIns($conn, $idusers, $idroles, &$strError) {
	global $config_table_prefix;
	$strSql = "INSERT INTO ".$config_table_prefix."roles_users_nm (idroles, idusers) VALUES (" . $idroles . ", " . $idusers . ")";
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function passwordUpdate($conn, $intIdutente, $strPwd) {
	global $config_table_prefix;
	$strSql = "UPDATE ".$config_table_prefix."users SET pwd=MD5('" . $strPwd . "') WHERE id=" . $intIdutente;
	$query = mysql_query ($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	} else {
		//aggiorno la data di ultimo cambio password
		$strSql = "UPDATE ".$config_table_prefix."users SET ultimopwdmod=CURRENT_TIMESTAMP WHERE id=" . $intIdutente;
		mysql_query ($strSql, $conn);
	}
}

// ******************************************************************************************
function generateRandomPassword() {
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

// ******************************************************************************************
function generateActivationCode () {
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
function getRolesList($conn) {
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."roles ORDER BY nome ASC";

	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getRolesRicerca($conn, $strNome) {
	global $config_table_prefix;
	$strSqlWhere = "";
	If ($strNome) $strSqlWhere .= "nome LIKE '%" . addslashes($strNome) . "%' AND ";
	If ($strSqlWhere) $strSqlWhere = " WHERE " . substr($strSqlWhere, 0, strlen($strSqlWhere)-5);
	$strSql = "SELECT * FROM ".$config_table_prefix."roles" . $strSqlWhere . " ORDER BY nome ASC";

	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getRolesDetails($conn, $intId) {
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."roles WHERE id=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function rolesInsert($conn, &$id, $nome, &$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;
	$nomeSql = $objUtility->translateForDb($nome, "string");

	$strSql = "INSERT INTO ".$config_table_prefix."roles (nome)";
	$strSql .= " VALUES (" . $nomeSql . ")";
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	} else {
		$id = mysql_insert_id($conn);
	}
}

// ******************************************************************************************
function rolesUpdate($conn, $id, $nome, &$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;
	$nomeSql = $objUtility->translateForDb($nome, "string");

	$strSql = "UPDATE ".$config_table_prefix."roles SET ";
	$strSql .= "nome=" . $nomeSql;
	$strSql .= " WHERE id=" . $id;

	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function rolesDelete($conn, $id, &$strError) {
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
}

// ******************************************************************************************
function getRolesMenu($conn, $intId) {
	global $config_table_prefix;
	$strSql = "SELECT idmenu id FROM ".$config_table_prefix."roles_menu_nm WHERE idroles=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function rolesMenuDelete($conn, $idroles, &$strError) {
	global $config_table_prefix;
	$strSql = "DELETE FROM ".$config_table_prefix."roles_menu_nm WHERE idroles=" . $idroles;
	mysql_query ($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function rolesMenuIns($conn, $idroles, $idmen, &$strError) {
	global $config_table_prefix;
	$strSql = "INSERT INTO ".$config_table_prefix."roles_menu_nm (idmenu, idroles) VALUES (" . $idmen . ", " . $idroles . ")";
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function getRolesUsers($conn, $intId) {
	global $config_table_prefix;
	$strSql = "SELECT idusers id FROM ".$config_table_prefix."roles_users_nm WHERE idroles=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function rolesUsersDelete($conn, $idroles, &$strError) {
	global $config_table_prefix;
	$strSql = "DELETE FROM ".$config_table_prefix."roles_users_nm WHERE idroles=" . $idroles;
	mysql_query ($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function rolesUsersIns($conn, $idroles, $idusers, &$strError) {
	global $config_table_prefix;
	$strSql = "INSERT INTO ".$config_table_prefix."roles_users_nm (idroles, idusers) VALUES (" . $idroles . ", " . $idusers . ")";
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

}
?>