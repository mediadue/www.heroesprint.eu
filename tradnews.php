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
//mysql_query("SET character_set_results=utf8");
$tradlist=array();
$wordcount=array();

$lan=getTable("lingue","","(attivo=1 && predefinita<>1)");

$dizionario=getTable("dizionario","data_aggiornamento DESC","");
while (list($key, $row) = each($dizionario)) {
	$trad=getTable("dizionario#traduzioni_nm","","id_dizionario=".$row["id"]);
	if(count($trad)<count($lan)){
		$txt=html_entity_decode($row["testo_editor"],ENT_QUOTES,"UTF-8");
		
		array_push($tradlist, array("id"=>$row["id"],  "testo"=>$txt ));
		$wordcount=array_merge($wordcount,str_word_count($txt,1));
		//echo $row["testo_editor"]."<br><br>";
	}
}

$wordcount=array_unique($wordcount);
print_r($wordcount);

/*
$sql="SELECT heroesprint_dizionario.id, heroesprint_dizionario.testo_editor, heroesprint_dizionario.data_aggiornamento, heroesprint_traduzioni.testo_tradotto_editor, heroesprint_lingue.nome
FROM ((`heroesprint_dizionario#traduzioni_nm` as dt INNER JOIN heroesprint_dizionario ON dt.id_dizionario = heroesprint_dizionario.id) INNER JOIN heroesprint_traduzioni ON dt.id_traduzioni = heroesprint_traduzioni.id) INNER JOIN heroesprint_lingue ON heroesprint_traduzioni.id_lingue = heroesprint_lingue.id
ORDER BY heroesprint_dizionario.id DESC , heroesprint_dizionario.data_aggiornamento DESC;";

$query=mysql_query($sql);
$tradlist=$objUtility->buildRecordset($query);
*/

array2csv($tradlist,"tradnews.csv");

echo "Aggiornamento Effettuato con successo!";

exit;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php //print_r($tradlist); exit; ?>
</body>
</html>
?>