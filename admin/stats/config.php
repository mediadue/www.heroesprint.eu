<?php
/*  ___ _  _ ___       ___ _____ _ _____ ___ 
 * | _ \ || | _ \_____/ __|_   _/_\_   _/ __|
 * |  _/ __ |  _/_____\__ \ | |/ _ \| | \__ \
 * |_| |_||_|_|       |___/ |_/_/ \_\_| |___/
 *
 * Bugfix and
 * update v1.x: Roberto Bizzarri
 *
 * Author:		Roberto Valsania - Webmaster76
 * Staff:		Matrix - Massimiliano Coppola
 *				PaoDJ - Paolo Antonio Tremadio
 *				Fabry - Fabrizio Tomasoni
 *				theCAS - Carlo Alberto Siti
 */
include_once ("_docroot.php");
include_once (SERVER_DOCROOT . "logic/class_config.php");
$objConfig = new ConfigTool();
$objUtility = new Utility;

/////////////////////
// CONFIGURAZIONE  //
/////////////////////
      $option['host']=$objConfig->get("db-hostname");						// Indirizzo server MySQL o IP (di solito è localhost)
  $option['database']=$objConfig->get("db-dbname");					// Nome database
   $option['user_db']=$objConfig->get("db-username");						// Utente
   $option['pass_db']=$objConfig->get("db-password");							// Password
$option['script_url']='http://'.$_SERVER['SERVER_NAME'].$objUtility->getPathRoot().'admin/stats';		// Indirizzo di installazione di Php-Stats

////////////////////////
// VARIABILI AVANZATE //
////////////////////////
$option['prefix']=$objConfig->get("db-table-prefix").'phpstats_'; // Prefisso per le tabelle di Php-Stats (default php_stats)
$option['callviaimg']='0'; // 1: richiama Php-Stats con immagine trasparente 1x1 pixel - 0: con javascript
$option['persistent_conn']='0'; // 1: connessione persistente a MySQL - 0: connessione normale
$option['autorefresh']='5'; // Valore in MINUTI, per aggiornamento pagine dell'admin
$option['show_server_details']='1'; // 1: mostra dettagli server nella pagina principale - 0: No
$option['show_average_user']='1'; // 1: mostra l'utente medio nella pagina principale - 0: No
$option['short_url']='1'; // 1: mostra url brevi quando possibile - 0: No
$option['ext_whois']=''; // Nel caso di connessioni esterne bloccate scrivi: - <i>http://www.yourwhois.com/adress?ip=%IP%</i> - Cambia solo yourwhois.com - NON CAMBIARE %IP%
$option['online_timeout']='5'; // Valore in MINUTI timeout utenti online - 0: conteggio dinamico
$option['page_title']='1'; // 1: memorizza i titoli delle pagine - 0: No
$option['refresh_page_title']='1'; // 1: aggiorna i titoli delle pagine - 0: No
$option['log_host']='1'; // 1: registra l'hostname tra i dettagli - 0: No
$option['clear_cache']='0'; // 1: ricoscimento cache continuo (ATTENZIONE: LENTO!) - 0: No
$option['full_recn']='0'; // 1: motori e refers riconosciuti ad ogni pagina visitata (ATTENZIONE: LENTO!) - 0: No
$option['logerrors']='1'; // 1: registra gli eventi nel file php-stats.log (deve avere i permessi in scrittura) - 0: No
$option['check_new_version']='1'; // 1: effettua verifica nuova versione php-stats - 0: No
$option['bcap_auto_update']='1'; // 1: aggiorna automaticamente database browser e sistemi (browscap) - 0: No
$option['www_trunc']='0'; // 1: trasforma http://www. in http:// - 0: No
$option['ip-zone']='0'; // 0: disattiva riconoscimento nazione da IP - 1: riconoscimento tramite Database (da installare) - 2: riconoscimento tramite File (da installare)
$option['down_mode']='1'; // 0: redirect - 1: forza download file - 2: forza download file altervista
$option['check_links']='1'; // 1: controlla il link - 0: No
$option['ip_spam_filter']='0'; // 1: attiva filtro IP spam tramite Database (da installare) - 0: No

$default_pages=array('/','/index.htm','/index.html','/default.htm','/index.php','/index.asp','/default.asp'); // Pagine di default del server, troncate considerate come la stessa

/////////////////////////////////////////////////
// NON MODIFICARE NULLA DA QUESTO PUNTO IN POI //
/////////////////////////////////////////////////

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' && substr($option['script_url'],0,5)==='http:') $option['script_url']='https:'.substr($option['script_url'],5);
if(substr($option['script_url'],-1)==='/') $option['script_url']=substr($option['script_url'],0,-1);

ini_set('display_errors', false);
error_reporting(E_ERROR);
ignore_user_abort(true);
?>
