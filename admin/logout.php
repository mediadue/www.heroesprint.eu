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

if(!empty($_SESSION["user_id"])) unset($_SESSION["user_id"]);
if(!empty($_SESSION["user_login"])) unset($_SESSION["user_login"]);
if(!empty($_SESSION["user_lastaccess"])) unset($_SESSION["user_lastaccess"]);
$_SESSION = array();

@session_destroy();
@header ("Location: " . $objUtility->getPathBackoffice() . "login.php");
?>