<?php
session_start();

require_once ("rsHeader.php");
require_once ("_docroot.php");
require_once (SERVER_DOCROOT."logic/class_config.php");

$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$objHtml = new Html;
 
if($_GET['menid']=="") $_GET['menid']=getCategoria();  
$objCarrello = new Carrello();
$objCarrello->setOptions(false,0,$objUtility->getPathRoot()."index.php",0);
?>

<ul class="nav navbar-nav  nav-service">
  <?php $c=$objCarrello->countCart();if($c=="") $c=0; ?>
  <li><a class="nav-service_link" href="<?php echo $objUtility->getPathRoot().getCurLanClass()."/"; ?>carrello.html"><i class="icon glyphicon glyphicon-shopping-cart"></i><?php echo ln("Carrello "); ?>(<div class="crt-cart-count"><?php echo $c; ?></div>)</a></li>
  <li><a class="nav-service_link" href="<?php echo $objUtility->getPathRoot(); ?>index.php?HRlogin=1"><?php if(!isset($_SESSION["userris_id"])) echo "Login";else echo ln("I tuoi dati"); ?></a></li>
  <!--<?php if(!isset($_SESSION["userris_id"])) { ?><li><a class="nav-service_link" href="<?php echo $objUtility->getPathRoot(); ?>index.php?HRreg=1"><?php echo ln("Registrati"); ?></a></li><? } ?>-->
  <?php if(isset($_SESSION["userris_id"])) { ?><li><a class="nav-service_link" href="<?php echo $objUtility->getPathRoot(); ?>index.php?logout=1"><?php echo "Logout"; ?></a></li><? } ?>
</ul>