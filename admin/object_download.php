<?php
header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: Private");
header("Cache-Control: no-cache, must-revalidate");

require_once("_docroot.php");
require_once(SERVER_DOCROOT."/logic/class_config.php");
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

$intIdutente=$_SESSION["userris_id"];
if(!$intIdutente) $objUsers->getCurrentUser($intIdutente, $strUsername);

//$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."documents/documents.php");


$id = (int) $_GET["id"];

$isBackOffice=retRow("users",$intIdutente);

if($isBackOffice['isbackoffice']==0) {
  $rs=getTable("documents","","idoggetti='$id'");
  if($rs[0]['idusers']!=$intIdutente) exit;
}

$rs = $objObjects->getDetails($conn, $id);
if (count($rs) > 0) 
{
	list($key, $row) = each($rs);

	$path = $objUtility->getPathResourcesDynamicAbsolute();
	if ($row["isprivate"])
		$path = $objUtility->getPathResourcesPrivateAbsolute();

	header("Content-type: Application/octet-stream");
	header("Content-Disposition: attachment; filename=\"".$row["originalname"]."\"");
	header("Content-Description:".$row["originalname"]);
	header("Content-Length: " . (string)(filesize($path . $row["nome"].".".$row["ext"])));
	
	readfile($path . $row["nome"].".".$row["ext"]);
}
else 
{
	header ("Location: " . $objUtility->getPathBackoffice() . "logout.php");
}
?>