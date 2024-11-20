<?php
$filename = "mediaplayer.swf";
header('Content-type: application/x-shockwave-flash');header("Expires: Thu, 01 Jan 1970 00:00:00 GMT, -1 ");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
$content = file_get_contents($filename);
echo $content;
?>
