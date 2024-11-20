<?php
  session_start();
  
  
  header('Content-type: text/css');
  
  include ("_docroot.php");
  include (SERVER_DOCROOT."logic/class_config.php");
  $objUtility = new Utility;
  
  ob_start("compressCss");

  /* your css files */
  include($objUtility->getPathBackofficeResourcesAbsolute()."lytebox.css");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery-ui-1.8.17.custom.css");
  include($objUtility->getPathBackofficeResourcesAbsolute()."uploadify.css");
  include($objUtility->getPathBackofficeResourcesAbsolute()."uploadifive/uploadifive.css");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jqzoom.css");
  include($objUtility->getPathBackofficeResourcesAbsolute()."rsChat.css");
  include($objUtility->getPathBackofficeResourcesAbsolute()."tables.css");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery.alerts.css");
  
  ob_end_flush();
?>