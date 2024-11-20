<?php



include ("_docroot.php");

include (SERVER_DOCROOT . "logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$conn = $objDb->connection($objConfig);
$tblStrutture = new rsTable("strutture");
$tblStrutture_rol = new rsTable("categorie_roles");

session_start();

global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

$isSystem = $objUsers->isSystem($conn, $intIdutente);

$menid=$_GET['menid'];

if($_POST['str']=="") $_POST['str']=$_SESSION['str'];

//echo $_SESSION['str'];

if($_POST['str']) {
  $_SESSION['str']=$_POST['str'];
  $strutture=retRow("strutture",$_POST['str']);
  $css=$strutture['css'];
  $strutture=$strutture['nome'];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>

</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("gestione sito web", "") ?>
			<div id="body">
				<div class="container">
          <?php
          $wh="";
          if(!$isSystem) $wh="id_users='$intIdutente'";
          $tblStrutture->_print($wh,"","","-1");
          $ret=getSubTablesNav("categorie");
          while (list($key, $row) = each($ret)) {
            $rs=getTable("categorie_roles","","tabella='$row'");
            if(count($rs)==0) {
              $sql="INSERT INTO ".$config_table_prefix."categorie_roles (tabella) VALUES ('$row')";
              mysql_query($sql);
            }  
          } 
          
          $tblStrutture_rol->_print("","","","","",1);
          ?>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>