<?php
$expires = 60*60; //1 ora;
//header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: public, max-age=31536000");
//header("Cache-Control: post-check=0, pre-check=0", false); 
//header("Pragma: no-cache"); 
header("Accept-Encoding: br, gzip, deflate, x-gzip, identity, *");

/*
$expires = 60*60*24*14;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header ("Cache-Control: Public");
header("keep-alive: timeout=15, max=100");
*/
?>