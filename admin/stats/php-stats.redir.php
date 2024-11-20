<?php
if(!defined('__PHP_STATS_PATH__'))
	die('undefined __PHP_STATS_PATH__');

$GLOBALS['php_stats_appendVarJs']=$GLOBALS['php_stats_sendVarJs']=$GLOBALS['php_stats_script_url']=$GLOBALS['php_stats_full_recn']='';
require(__PHP_STATS_PATH__.'php-stats.recphp.php');

if((($GLOBALS['php_stats_appendVarJs']!='' && $GLOBALS['php_stats_sendVarJs']==1) || $GLOBALS['php_stats_full_recn']) && $php_stats_ok==1)
{
	echo '<!-- dbg1 --><script type="text/javascript" src="'.$GLOBALS['php_stats_script_url'].'/php-stats.phpjs.php?'.$GLOBALS['php_stats_appendVarJs'].'"></script>';
}
?>
