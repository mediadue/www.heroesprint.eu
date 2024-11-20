<?php
session_start();

header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: Private");
header("Cache-Control: no-cache, must-revalidate");

include ("_docroot.php");
include (SERVER_DOCROOT . "logic/class_config.php");

$objConfig = new ConfigTool();
$objDb = new Db;
$conn = $objDb->connection($objConfig);
$objUtility = new Utility;
$objCarrello = new Carrello;
$objHtml = new Html;

if(!isset($_GET['idordine'])) {
  $objHtml->adminPageRedirect("http://".$_SERVER['SERVER_NAME'],"");
  exit;
}

$id=$_GET['idordine'];
$ordine=retRow("ecommerce_ordini",$id);

$account=$objConfig->get("paypal-account");

$sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET id_ecommerce_stati='11' WHERE id='$id'";
mysql_query($sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title></title>
    <!-- #EndEditable -->
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <meta name="author" content=""/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta http-equiv="imagetoolbar" content="no" /> 
    <?php include(SERVER_DOCROOT . 'include/inc.functions.php'); ?>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $objUtility->getPathRoot(); ?>css/inc.rsStyle.css"/>
    <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources(); ?>inc.rsJavaScript.js"></script>
    <link href="<?php echo $objUtility->getPathRoot(); ?>css/style2.css" media="screen" rel="stylesheet" title="CSS" type="text/css" />            
  </head>
  <body>
    <!-- https://www.sandbox.paypal.com/cgi-bin/webscr -->  
    <form id="paypal" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
      <input type="hidden" name="cmd" value="_xclick">
      <input type="hidden" name="business" value="<?php echo $objConfig->get("paypal-account"); ?>">
      <input type="hidden" name="item_name" value="<?php echo ln("Acquisti effettuati su ".$_SERVER['SERVER_NAME']); ?>">
      <input type="hidden" name="item_number" value="1">
      <input type="hidden" name="amount" value="<?php echo ParseToFloat($ordine['totale_cry'])?>">
      <input type="hidden" name="shipping" value="0.00">
      <input type="hidden" name="no_shipping" value="1">
      <input type="hidden" name="return" value="<?php echo "http://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."index.php?paypal_return=$id"; ?>">
      <input type="hidden" name="rm" value="2">
      <input type="hidden" name="notify_url" value="<?php echo "http://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."rsAction.php"; ?>">
      <input type="hidden" name="cancel_return" value="<?php echo "http://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."index.php?paypal_cancel_return=$id"; ?>">
      <input type="hidden" name="no_note" value="1">
      <input type="hidden" name="currency_code" value="EUR">
      <input type="hidden" name="tax" value="0.00">
      <input type="hidden" name="lc" value="IT">
      <input type="hidden" name="bn" value="PP-BuyNowBF">
  		<input type="hidden" name="on0" value="<?php echo ln("Riferimento numero ordine"); ?>">
      <input type="hidden" name="os0" value="<?=$ordine['codice_vendita']?>">
		</form>
		
		<div align="center"><img src='<?php echo $objUtility->getPathBackofficeResources(); ?>paypal.jpg'></div>
		<br>
    <p align="center"><font size="2" face="Tahoma">Reindirizzamento sul sito sicuro</font></p>
		<p align="center"><font size="2" face="Tahoma"><strong>Attendere prego...</strong></font></p>
		
    <script> 
    $(document).ready(function(){
      $("#paypal").submit();
    });
    </script>
  </body>
</html>