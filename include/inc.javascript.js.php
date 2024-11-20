<?php
  session_start();
  
  /*
  header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  header("Accept-Encoding: gzip, deflate, x-gzip");
  */
  
  include ("_docroot.php");
  include (SERVER_DOCROOT."logic/class_config.php");
  $objConfig = new ConfigTool();
  $objUtility = new Utility;
	
  $curLan=getCurLan();
  $clName=$curLan["classe"];
  
  $dir=SERVER_DOCROOT . "include/compiled/".$clName;
  if (!file_exists($dir) && $objConfig->get("use_cache")==1) {
  	mkdir($dir, 0777, true);
  }
  
  $file=$dir."/rsJava.js";
  if(!file_exists($file) || $objConfig->get("ccompiler_override")=="1" || $objConfig->get("use_cache")=="0"){
    $merged=mergeJs();  
  }
  
  ob_start(); ?>
    var getPathBackoffice = '<?php echo $objUtility->getPathBackoffice(); ?>';
    var getPathBackofficeResources = '<?php echo $objUtility->getPathBackofficeResources(); ?>';
    var getPathResourcesDynamic = '<?php echo $objUtility->getPathResourcesDynamic(); ?>';
    var getPathRoot = '<?php echo $objUtility->getPathRoot(); ?>'; 
  <?php
   
  $tjscode=ob_get_contents(); 
  ob_end_clean();
  echo compressJs($tjscode);
  echo cruiseCompressJs($file,$merged,$objConfig->get("use_cache"));
  include SERVER_DOCROOT."include/inc.javascript-nocache.js.php";
?>