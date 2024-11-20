<?php
/*
session_start();
require_once ("rsHeader.php");
require_once ("_docroot.php");
require_once (SERVER_DOCROOT."logic/class_config.php");

$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$objHtml = new Html;

global $config_table_prefix;


*/
session_start();
require_once ("rsHeader.php");
require_once ("_docroot.php");
require_once (SERVER_DOCROOT."logic/class_config.php");

$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$objHtml = new Html;

global $config_table_prefix;

$filename="tradnews_old.csv";

$arrTrad=parse_csv($filename);

while (list($key1, $row1) = each($arrTrad)) {
	$kk=array_keys($row1);
	$k1=$kk[0];
	$k2=$kk[1];
	
	$id_dizionario=$row1[$k1];
	$testo=$row1[$k2];
	
	$sql="INSERT INTO `".$config_table_prefix."dizionario` (id,testo_editor) VALUES ('".$id_dizionario."','".addslashes($testo)."')";
	mysql_query($sql);
}
?>