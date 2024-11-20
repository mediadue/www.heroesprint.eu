<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

function report($manualsendreport=FALSE){//spostare a funzione di email in admin_func.inc.php
        global $db,$option,$style,$varie,$modulo;

        if(!$manualsendreport){
                include("lang/$option[language]/domains_lang.php");
                include('inc/admin_func.inc.php');
        }
        include("lang/$option[language]/main_lang.inc");

        // PREPARO LA MAIL
        $site=explode("\n",$option['server_url']);
        $site_url=str_replace(Array('http://','https://'),'',$site[0]);
        $bcc='';
        $user_email=explode("\n",$option['user_mail']);
        if(count($user_email)>1) $bcc='BCC: '.implode(',',array_slice($user_email,1))."\r\n";
        $user_email=$user_email[0];
        $return_path='error@useless.it';

        $mail_soggetto="Report settimanale statistiche su $site_url";
        $intestazioni=
        "From: Php-Stats at $site_url<$user_email>\r\n".
        $bcc. //altri indirizzi
        "X-Sender: <$user_email>\r\n".
        "X-Mailer: PHP-STATS\r\n". // mailer
        "X-Priority: 1\r\n". // Messaggio urgente!
        "Return-Path: <$return_path>\r\n";  // Indirizzo di ritorno per errori
        $ver=$option['phpstats_ver'];

        // VISITATORI E VISITE TOTALI
        $hits_this_week=$visite_this_week=0;
        // Contatori
        $result=sql_query("SELECT * FROM $option[prefix]_counters");
        list($hits,$visits)=mysql_fetch_row($result);
        $hits_totali=$hits+$option['starthits'];
        $visite_totali=$visits+$option['startvisits'];

        $date=time()-$option['timezone']*3600;
        list($date_G,$date_i,$date_m,$date_d,$date_Y,$date_w)=explode('-',date('G-i-m-d-Y-w',$date));
        $mese_oggi=$date_Y.'-'.$date_m; // Y-m

        // VISITATORI E PAGINE VISITATE NEGLI ULTIMI 7 GIORNI IN DETTAGLIO
        $dettagli="\n";
        for($i=0;$i<=7;++$i){
                $max=$lista_accessi[$i]=$lista_visite[$i]=0;
                $giorno=date('Y-m-d',mktime($date_G,$date_i,0,$date_m,$date_d-$i-1,$date_Y));
                $lista_giorni[$i]=$giorno;
                $result=sql_query("SELECT data,hits,visits,no_count_hits,no_count_visits FROM $option[prefix]_daily where data='$giorno'");
                while($row=mysql_fetch_row($result)){
                        list($daily_data,$daily_hits,$daily_visits,$daily_no_count_hits,$daily_no_count_visits)=$row;
                        $lista_accessi[$i]=$daily_hits-$daily_no_count_hits;
                        $lista_visite[$i]=$daily_visits-$daily_no_count_visits;
                        $hits_this_week+=$daily_hits-$daily_no_count_hits;
                        $visite_this_week+=$daily_visits-$daily_no_count_visits;
                        if(($daily_hits-$daily_no_count_hits)>$max) $max=$daily_hits-$daily_no_count_hits;
                }
        }
        for($i=0;$i<=7;++$i){
                $data=explode('-',$lista_giorni[$i]);
                $giorno=str_replace(Array('%mount%','%day%','%year%'),Array(formatmount($data[1]),$data[2],$data[0]),$varie['date_format']);
                $dettagli.=
                str_pad($giorno,35,' ',STR_PAD_LEFT).
                str_pad($lista_accessi[$i],15,' ',STR_PAD_LEFT).
                str_pad($lista_visite[$i],15,' ',STR_PAD_LEFT).
                "\n";
        }

        // REFERER (TOP 25)
        $site_referers='';
        if($modulo[4]==2) $result=sql_query("SELECT data,visits FROM $option[prefix]_referer WHERE mese='$mese_oggi' ORDER BY visits DESC LIMIT 25");
        else $result=sql_query("SELECT data,visits FROM $option[prefix]_referer ORDER BY visits DESC LIMIT 25");
        while($row=mysql_fetch_row($result)){
                list($referer_data,$referer_visits)=$row;
                $site_referers.="$referer_data ($referer_visits)\n";
        }

        // MOTORI DI RICERCA (TOP 25)
        $site_engines='';
        if($modulo[4]==2) $result=sql_query("SELECT data,engine,domain,visits FROM $option[prefix]_query WHERE mese='$mese_oggi' ORDER BY visits DESC LIMIT 25");
        else $result=sql_query("SELECT data,engine,domain,visits FROM $option[prefix]_query ORDER BY visits DESC LIMIT 25");
        while($row=mysql_fetch_row($result)){
                list($query_data,$query_engine,$query_domain,$query_visits)=$row;
                $site_engines.="$query_data ($query_visits, $query_engine {$domain_name[$query_domain]})\n";
        }

        // COMPILO IL TEMPLATE
        eval("\$mail_messaggio=\"".gettemplate("lang/$option[language]/report_weekly.tpl")."\";");

        // SPEDISCO LA MAIL
        $ok=FALSE;
        $ok=mail($user_email,$mail_soggetto,$mail_messaggio,$intestazioni);

        if($ok==FALSE) $ok=mail($user_email,$mail_soggetto,$mail_messaggio);

        if($manualsendreport) return $ok;
        if($ok==TRUE){
                // SE L'INVIO E' OK PROGRAMMO IL DATABASE PER IL PROSSIMO INVIO
                $next=mktime(0,0,0,$date_m,$date_d-$date_w+$option['report_w_day']+7,$date_Y);
                $oggi=time()-($option['timezone']*3600);
                if($next-$oggi>604800) $next=$next-604800;
                sql_query("UPDATE $option[prefix]_config SET value='$next' WHERE name='instat_report_w'");
//                if($NowritableServer===1) sql_query("UPDATE $option[prefix]_options SET option_value='$next' WHERE option_name='instat_report_w'");
        }
        else logerrors('Weekly Report|'.date('d/m/Y H:i:s').'|FAILED');
}
?>