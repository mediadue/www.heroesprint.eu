<?php
session_start();




function checkRef($referer, $host, $defaulturl) {
  $host = "http://" . $host;
  $vabene = (strpos($referer, $host) === 0);
  if (!$vabene) {
    header ("Location: " . $defaulturl);
    exit();
  }
}

checkRef ($_SERVER["HTTP_REFERER"], "www.mediadue.net", "http://www.mediadue.net");

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

$utente=$_GET['utente'];
$password=$_GET['password'];

$strLogin = $utente;
$strPwd = $password;

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
  header("Location: http://www.mediadue.net");
}
