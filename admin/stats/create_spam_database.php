<?php
// Per ragioni di sicurezza i file inclusi avranno un controllo di provenienza
define('IN_PHPSTATS', true);

set_time_limit(1200);

// inclusione delle principali funzioni esterne
if(!include('config.php')) die('<b>ERRORE</b>: File config.php non accessibile.');
if(!include('inc/main_func.inc.php')) die('<b>ERRORE</b>: File main_func.inc.php non accessibile.');
if(!include('inc/admin_func.inc.php')) die('<b>ERRORE</b>: File admin_func.inc.php non accessibile.');

if($option['prefix']=='') $option['prefix']='php_stats';

// Connessione a MySQL e selezione database
db_connect();

$flist = glob('spam-ip.com*.csv');
$fname = end($flist);
if ( empty($fname) )
	die('<br><br><b>ERRORE: il file .csv non è stato trovato trovato!!!</b>');

echo "<br><br>Creazione database in corso (file: $fname)...";

//$fp=fsockopen('spam-ip.com/csv_dump/spam-ip.com_06-28-2011.csv', 80, $errno, $errstr, 30);
$fp=fopen($fname,'r');

if($fp==false)
  {
  die('<br><br><b>ERRORE: il file .csv non è stato trovato trovato!!!</b>');
  }
else
  {
  mysql_query('DROP TABLE /*!32300 IF EXISTS*/ '.$option['prefix'].'_ip_spam;');
  mysql_query('CREATE TABLE '.$option['prefix']."_ip_spam (ip int(10) unsigned NOT NULL default 0, PRIMARY KEY (ip)) TYPE=MyISAM;");

  fgets($fp, 4096);		// Salta la prima linea di intestazioni
  while(!feof($fp))
    {
    $content=fgets($fp, 4096); // Leggo 1 riga
//    $content=str_replace('"','',$content);
//    if($content!='')
//      {
      list($dummy1,$ip,$dummy2,$dummy3,$dummy4)=explode(',',$content);
//      echo "$ip\n";
      $ip = ip2long(trim($ip));
      mysql_query('INSERT INTO '.$option['prefix']."_ip_spam VALUES('$ip')");
//      }
    }
    echo 'Fine.';
  }
?>