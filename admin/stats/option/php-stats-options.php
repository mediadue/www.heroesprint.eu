<?php
if(!defined('IN_PHPSTATS')) die("Php-Stats internal file.");
error_reporting(E_ERROR);
ignore_user_abort(true);

$option=Array(
'host'=>'localhost',
'database'=>'heroesprint',
'user_db'=>'heroesprint',
'pass_db'=>'media198386',
'script_url'=>'http://www.heroesprint.eu/admin/stats',
'prefix'=>'heroesprint_phpstats_',
'callviaimg'=>0,
'persistent_conn'=>0,
'autorefresh'=>5,
'show_server_details'=>1,
'show_average_user'=>1,
'short_url'=>1,
'ext_whois'=>'',
'online_timeout'=>5,
'page_title'=>1,
'refresh_page_title'=>1,
'log_host'=>1,
'clear_cache'=>0,
'full_recn'=>0,
'logerrors'=>1,
'check_new_version'=>1,
'bcap_auto_update'=>1,
'www_trunc'=>0,
'ip-zone'=>0,
'down_mode'=>1,
'check_links'=>1,
'ip_spam_filter'=>0,
'stats_disabled'=>0,
'language'=>'it',
'server_url'=>'http://www.tuosito.it',
'admin_pass'=>'ed22d174a0cea7592c1070d3c99527ef35a42a8f',
'use_pass'=>0,
'cifre'=>8,
'stile'=>1,
'timezone'=>0,
'template'=>'default',
'startvisits'=>0,
'starthits'=>0,
'nomesito'=>'Tuo Sito o link',
'user_mail'=>'tuonome@tuoserver.it',
'user_pass_new'=>'',
'user_pass_key'=>'',
'prune_0_on'=>0,
'prune_0_value'=>48,
'prune_1_on'=>0,
'prune_1_value'=>500,
'prune_2_on'=>0,
'prune_2_value'=>200,
'prune_3_on'=>0,
'prune_3_value'=>200,
'prune_4_on'=>0,
'prune_4_value'=>200,
'prune_5_on'=>0,
'prune_5_value'=>200,
'phpstats_ver'=>'1.4',
'inadm_lastcache_time'=>0,
'ip_timeout'=>4,
'page_timeout'=>1200,
'report_w_on'=>0,
'report_w_day'=>0,
'instat_report_w'=>0,
'auto_optimize'=>0,
'auto_opt_every'=>500,
'exc_fol'=>'',
'exc_sip'=>'',
'exc_dip'=>''
);

$modulo=Array(1,2,1,2,2,2,1,1,1,1,1,1);

$unlockedPages=Array(
''
);

$serverUrl=Array(
'http://www.tuosito.it'
);
$countServerUrl=1;

$countExcFol=0;

$countExcSip=0;

$countExcDip=0;

$default_pages=Array(
'/',
'/index.htm',
'/index.html',
'/default.htm',
'/index.php',
'/index.asp',
'/default.asp');
?>