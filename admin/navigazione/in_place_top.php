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
<style>
  body {background: rgb(239,239,239) url(<?php echo $objUtility->getPathBackofficeResources() ?>dark-bg.gif) repeat-x scroll left top}
  
  #top {text-align: center;font-family: arial;color: black;font-weight: bold;}
  #top a {color:black;}
  #top a:hover {color:gray;text-decoration: none;}
</style>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<body>
  <div id="top">
  <a href="<?=$objUtility->getPathBackofficeAdmin()?>" target="_top">TORNA AL BACKOFFICE</a>
  </div>
</body>
</html>