<?php
session_start();

header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: Private");
header("Cache-Control: no-cache, must-revalidate");

include ("_docroot.php");
include (SERVER_DOCROOT . "logic/class_config.php");
global $config_table_prefix;
$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$conn = $objDb->connection($objConfig);
$objHtml = new Html;

$id=urldecode($_GET['id']);
$w=$_GET['w'];
$h=$_GET['h'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <!-- #BeginEditable "doctitle" -->
    <title></title>
    <!-- #EndEditable -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content=""/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta http-equiv="imagetoolbar" content="no" />
    <link href="<?php echo $objUtility->getPathRoot(); ?>css/style2.css" media="screen" rel="stylesheet" title="CSS" type="text/css" />
    <?php include(SERVER_DOCROOT . 'include/inc.functions.php'); ?>                                                
  </head>
                   
  <?php //include('include/inc.css_admin.php'); ?>

  <body>
    <center><?php echo retVideo($id,"",$w,$h);?></center>
  </body>
</html>