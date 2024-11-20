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
$conn = $objDb->connection($objConfig);

session_start();

$objUtility->getAction($strAct, $intId);
$id = (int) $_POST["id"];


switch ($strAct) {

	case "LOGIN-DO":
		$strLogin = $_POST["utente"];
		$strPwd = $_POST["pwd"];
		
		$intIdutente = 0;
		$strUsername = "";
		$isAuthorized = $objUsers->checkLogin($conn, $strLogin, $strPwd, $intIdutente, $strUsername, $dateLastAccess, $dateLastPwdupdate, $isReadonly);
		
		if ($isAuthorized) {
			$_SESSION["user_id"] = $intIdutente;
			$_SESSION["user_login"] = $strUsername;
			$_SESSION["user_lastaccess"] = $dateLastAccess;
			$_SESSION["user_lastpwdupdate"] = $dateLastPwdupdate;
			$_SESSION["user_isreadonly"] = $isReadonly;
			$_SESSION["sessionvar"] = "";
			header("Location: home.php");
		} else {
			$objHtml->adminPageRedirect("login_alt.php", "Attenzione<br/><br/>Login o password errate", "");
		}
		break;
		
	case "LOGINA-DO":
		$strLogin = $_POST["utente"];
		$strPwd = $_POST["pwd"];
		
		$intIdutente = 0;
		$strUsername = "";
		$isAuthorized = $objUsers->checkLoginA($conn, $strLogin, $strPwd, $intIdutente, $strUsername, $dateLastAccess, $dateLastPwdupdate, $isReadonly);
		
		if ($isAuthorized) {
			$_SESSION["user_id"] = $intIdutente;
			$_SESSION["user_login"] = $strUsername;
			$_SESSION["user_lastaccess"] = $dateLastAccess;
			$_SESSION["user_lastpwdupdate"] = $dateLastPwdupdate;
			$_SESSION["user_isreadonly"] = $isReadonly;
			$_SESSION["sessionvar"] = "";
			header("Location: home.php");
		} else {
			$objHtml->adminPageRedirect("login.php", "Attenzione<br/><br/>Login o password errate", "");
		}
		break;

	case "PWDUPD-DO":
		$objUsers->getCurrentUser($intIdutente, $strUsername);

		$password = $_POST["password"];
		$password_conf = $_POST["password_conf"];

		$isError = false;
		If ($password == "") $isError = true;
		If ($password != $password_conf) $isError = true;

		If ($isError) {
			header ("Location: pwd_upd.php");
		} Else {
			$strError = "";
			$objUsers->passwordUpdate($conn, $intIdutente, $password);
			if ($strError) {
				$strEsito = "Attenzione<br><br>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
				$objHtml->adminPageRedirect("pwd_upd.php", $strEsito, "");
			} else {
				$strEsito = "Operazione eseguita correttamente";
				$objHtml->adminPageRedirect("logout.php", $strEsito, "");
			}
		}
		break;

}
?>