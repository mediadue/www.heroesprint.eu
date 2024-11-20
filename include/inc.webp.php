<?php
session_start();
include ("_docroot.php");

$or_image=$_GET["or_img_to_webp"];
$host=strtolower($_SERVER['SERVER_NAME']);

$tmpArr=array_reverse(explode(".", $or_image));
$tExt=strtolower($tmpArr[0]);

if( strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false && $host!="localhost" && $host!="localdemo.heroesprint.eu" ) {
  $tmpArr[0]="webp";
  $toWebp=implode(".", array_reverse($tmpArr));

  if(!file_exists(SERVER_DOCROOT . $toWebp)){
    exec("cwebp -q 80 ". SERVER_DOCROOT . $or_image ." -o ". SERVER_DOCROOT . $toWebp);
  }
  
  header('Content-Type: image/webp');
  readfile(SERVER_DOCROOT . $toWebp);
  exit;
}else{
  header('Content-Type: image/'.$tExt);
  readfile(SERVER_DOCROOT . $or_image);
  exit;    
}

?>