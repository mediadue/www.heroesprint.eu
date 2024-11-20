<?php
  session_start();
  
  
  header('Content-type: text/css');
  
  include ("_docroot.php");
  include (SERVER_DOCROOT."logic/class_config.php");
  $objUtility = new Utility;
  
  include($objUtility->getPathCssAbsolute() . "bootstrap.min.css");  
  include($objUtility->getPathCssAbsolute() . "bootstrap-theme.min.css");
  include($objUtility->getPathCssAbsolute() . "jasny-bootstrap.min.css");
  include($objUtility->getPathCssAbsolute() . "col-sh.css");
  include($objUtility->getPathCssAbsolute() . "animate.css");
  include($objUtility->getPathCssAbsolute() . "languages.min.css");
  include($objUtility->getPathCssAbsolute() . "flexslider.css");
  include($objUtility->getPathCssAbsolute() . "bootstrap-select.css");
  
  ob_start("compressCss");
    /* your css files */
    //include("../css/gmap.css");
    include($objUtility->getPathCssAbsolute() . "gestione_documenti.css");
    //include("../css/ecommerce.css");
    //include("../css/jquery.gallery.css");
    //include("../css/jquery.betterTooltip.css");
    //include("../css/jquery.jscrollpane.css");
    //include("../css/jquery.jscrollpane.lozenge.css");
    //include("../css/cloud-zoom.css");
    //include("../css/rsForm-reset.css");
    //include("../css/ez.css");
    //include("../css/reset.css"); 
    //include("../css/rsEcommerce-pers.css");
    //include("../css/rsStrutture-reset.css");
    //include("../css/style2.css");
  ob_end_flush();
  
  //include("../css/flatWeatherPlugin.css");
  //include("../css/ekko-lightbox.min.css");
  //include("../css/layout.css");
  include($objUtility->getPathCssAbsolute() . "layout.css");
  include($objUtility->getPathCssAbsolute() . "rsStyle.css");
  
?>