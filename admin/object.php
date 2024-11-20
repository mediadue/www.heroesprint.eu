<?php



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

$objUsers->getCurrentUser($intIdutente, $strUsername);
if ($intIdutente) 
{
	$id = (int) $_GET["id"];
	$strSql = "SELECT * FROM ".$config_table_prefix."oggetti WHERE id=".$id;
	$query = mysql_query($strSql, $conn);
	if (!mysql_errno() && !mysql_error()) 
	{
		$strPath = $objUtility->getPathResourcesDynamicAbsolute();
		$rs = $objUtility->buildRecordset($query);
		if (count($rs) > 0) 
		{
			list($key, $row) = each($rs);
			switch (strtolower($row["ext"])) 
			{
				case "jpg":
					header("Content-type: image/jpg");
					readfile($strPath . $row["path"]);
					break;
			}
		}
	}
}
?>