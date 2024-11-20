<?php
session_start();
include ("_docroot.php");
include (SERVER_DOCROOT."logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$conn = $objDb->connection($objConfig);
global $config_table_prefix;
$dbname = $objConfig->get("db-dbname");

$comuni=getTable("comuni","",""); 

while (list($key, $row) = each($comuni)) {
  $c1=$row["comune"];
  $cat1=$row["codice_catastale"];
  $is1=$row["codice_istat"];
  $prov1=$row["provincia"];
  //$province=getTable("province","","sigla='".$prov1."'");
  $id_prov=$province[0]["id"];
  //$cap1=getTable("comuni_cap","","istat='".$is1."'");
  
  //$c2=getTable("comuni2","","codice_catastale='".$cat1."'");
  $c2=getTable("comuni2","","codice_catastale='".$cat1."'");
  
  /*
  if(count($c2)==0) {
    
    $sql="INSERT INTO `".$config_table_prefix."comuni` (comune,id_province,cap,prefisso_tel,codice_istat,codice_catastale) VALUES ('".addslashes($c1)."','".$id_prov."',".$cap1[0]["cap"].",'".$row["prefisso"]."','".$is1."','".$cat1."')";
    //$sql="UPDATE `".$config_table_prefix."comuni` SET comune='".addslashes($com2)."', id_province='".$id_prov."', cap='".$cap2[0]["cap"]."', codice_catastale='".$cat2."' WHERE id='".$row["id"]."'";
    mysql_query($sql);
  }
  */
  
  
  if(count($c2)==0) {
    $sql="delete from `".$config_table_prefix."comuni` where id=".$row["id"];
    mysql_query($sql);    
  }
  
          
}

?>