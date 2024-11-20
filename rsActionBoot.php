<?php
session_start();

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
$objMailing = new Mailing;

$conn = $objDb->connection($objConfig);
$dbname = $objConfig->get("db-dbname");

global $config_table_prefix;

if($_POST['rsUPDanalitycs']=="1"){ 
	$id=$_POST['id'];
	
	$sql="UPDATE `".$config_table_prefix."ecommerce_ordini` SET analitycs_sent=1 WHERE id=".$id;
  mysql_query($sql);
	
	echo "1";
	
	exit;
}

if($_POST['type']=="hrs_newsletter"){ 
	$email=$_POST['id'];
	
	$sql="INSERT INTO `".$config_table_prefix."newsletter_list` (email,attivo)
			  VALUES ('".$email."',1)";
	
	mysql_query($sql);
	
  $objMailing->mmail("subscribe@mediadue.net",$email,"Subscribe","Subscribe","","","");
  
	echo ln("Grazie per la tua adesione! Ti terremo aggiornato sulle nostre Super Offerte e Promozioni");
	
	exit;
}

if($_GET['type']=="hrs_updTrad") {
	$lingue=getTable("lingue","","(attivo=1 AND predefinita<>1)");
	while (list($key, $row) = each($lingue)) {
		$tmpLan=$row["classe"];
		$id_lan= $row["id"];
		$filename="tradnews_".$tmpLan.".csv";
		
		if(file_exists($filename)){
			$arrTrad=parse_csv($filename);
			print_r($arrTrad);
			while (list($key1, $row1) = each($arrTrad)) {
				$kk=array_keys($row1);
				$k1=$kk[0];
				$k2=$kk[1];
				
				$id_dizionario=$row1[$k1];
				$testo=$row1[$k2];
				
				if($id_dizionario>0){
					$sql="INSERT INTO `".$config_table_prefix."traduzioni` (id_lingue,testo_tradotto_editor) VALUES ('".$id_lan."','".addslashes($testo)."')";
					mysql_query($sql);
					$id_traduzioni=mysql_insert_id();
					
					if($id_traduzioni>0){
						$sql="INSERT INTO `".$config_table_prefix."dizionario#traduzioni_nm` (id_dizionario,id_traduzioni) VALUES ('".$id_dizionario."','".$id_traduzioni."')";
						mysql_query($sql);
					}
				}
			}
		}
	}
	exit;
}