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

$account=$objConfig->get("keyclient-account");
$key=$objConfig->get("keyclient-key");

$user=retRow("users",$ordine['user_hidden']);

$sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET id_ecommerce_stati='12' WHERE id='$id'";
mysql_query($sql);

$importo=str_replace(".","",currencyITA($ordine['totale_cry']));
$importo=str_replace(",","",$importo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title></title>
    <!-- #EndEditable -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content=""/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta http-equiv="imagetoolbar" content="no" /> 
    <?php include(SERVER_DOCROOT . 'include/inc.functions.php'); ?>
    <link href="<?php echo $objUtility->getPathRoot(); ?>css/style2.css" media="screen" rel="stylesheet" title="CSS" type="text/css" />            
    <script> 
    $(document).ready(function(){
      $("#keyclient").submit();
    });
    </script>
    
  </head>
  <body>  
    <form id="keyclient" action="https://ecommerce.cim-italia.it/ecomm/DispatcherServlet" method="post">
      <input type="hidden" name="alias" value="<?php echo $account; ?>" /> 
      <!--<input type="hidden" name="alias" value="payment_testm_urlmac" />-->
      <input type="hidden" name="importo" value="<?php echo $importo; ?>" /> 
      <!-- <input type="hidden" name="importo" value="1" /> -->
      <input type="hidden" name="divisa" value="EUR" />
      <input type="hidden" name="codTrans" value="00-<?=$ordine['codice_vendita']?>" />
      <input type="hidden" name="mail" value="<?php echo $user['email']; ?>" />
      <input type="hidden" name="url" value="<?php echo "http://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."rsAction.php"; ?>" />
      <input type="hidden" name="url_back" value="<?php echo "http://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."index.php?keyclient_cancel_return=$id"; ?>" />
      <input type="hidden" name="languageId" value="ITA" />
      <input type="hidden" name="mac" value="<?php echo  urlencode(base64_encode(md5("codTrans=00-".$ordine['codice_vendita']."divisa=EURimporto=".$importo.$key))); ?>" /> 
		  <!--<input type="hidden" name="mac" value="<?php echo  urlencode(base64_encode(md5("codTrans=00-".$ordine['codice_vendita']."divisa=EURimporto=1esempiodicalcolomac"))); ?>" /> -->
    </form>
		
		<p align="center"><img src='<?php echo $objUtility->getPathBackofficeResources(); ?>logoKeyClient.JPG'></p>
		<p align="center"><font size="2" face="Tahoma">Reindirizzamento sul sito sicuro</font></p>
		<p align="center"><font size="2" face="Tahoma"><strong>Attendere prego...</strong></font></p>
		
  </body>
</html>