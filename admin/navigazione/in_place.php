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
$tblDefault = new rsTable($_GET['table']);
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
//$objMenu->checkRights($conn, $intIdutente);
?>
<html>
<frameset rows="40,100%" framespacing="0"" frameborder="NO" border="0">
  <frame src="in_place_top.php" name="backoffice" noresize scrolling=no>
  <frame src="<?=$objConfig->get("path-virtual-root")?>" name="web">
</frameset>
</html>
