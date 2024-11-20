<?php

/**
 *  ___ _  _ ___       ___ _____ _ _____ ___ 
 * | _ \ || | _ \_____/ __|_   _/_\_   _/ __|
 * |  _/ __ |  _/_____\__ \ | |/ _ \| | \__ \
 * |_| |_||_|_|0.1.9.2|___/ |_/_/ \_\_| |___/
 *
 * Author:     Roberto Valsania - Webmaster76
 *
 * Staff:      Matrix - Massimiliano Coppola
 *             Viewsource
 *             PaoDJ - Paolo Antonio Tremadio
 *             Fabry - Fabrizio Tomasoni
 *             theCAS - Carlo Alberto Siti
 *
 * Version:    0.1.9.2
 *
 * Site:       http://php-stats.com/
 *             http://phpstats.net/
 *
 **/

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

echo '<br><br>Creazione database in corso...';

$fp=fopen('ip-to-country.csv','r');
if($fp==false)
  {
  die('<br><br><b>ERRORE: il file ip-to-country.csv non è stato trovato trovato!!!</b>');
  }
else
  {
  mysql_query('DROP TABLE /*!32300 IF EXISTS*/ '.$option['prefix'].'_ip_zone;');
  mysql_query('CREATE TABLE '.$option['prefix']."_ip_zone (ip_from int(10) unsigned NOT NULL default 0, ip_to int(10) unsigned NOT NULL default 0, tld char(2) NOT NULL default '', PRIMARY KEY (ip_from,ip_to)) TYPE=MyISAM;");

  while(!feof($fp))
    {
    $content=trim(fgets($fp, 4096)); // Leggo 1 riga
    $content=str_replace('"','',$content);
    if($content!='')
      {
      list($ip_from,$ip_to,$tld,$dummy1,$dummy2)=explode(',',$content);
      $tld=strtolower($tld);
      mysql_query('INSERT INTO '.$option['prefix']."_ip_zone VALUES('$ip_from','$ip_to','$tld')");
      }
    }
    echo 'Fine.';
  }
?>