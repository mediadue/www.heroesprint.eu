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

session_start();

global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente,$objUtility->getPathBackoffice()."navigazione/in_place.php");

$isSystem = $objUsers->isSystem($conn, $intIdutente);
$wh="";
if(!$isSystem) $wh="id_users='$intIdutente'";

$menid=$_GET['menid'];

if($_REQUEST['str']=="") $_REQUEST['str']=$_SESSION['str'];

//echo $_SESSION['str'];

if($_REQUEST['str']) {
  $_SESSION['str']=$_REQUEST['str'];
  $strutture=retRow("strutture",$_REQUEST['str']);
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
			<?php $objHtml->adminPageTitle("Sviluppo > Elementi sito > <b>".mb_ucfirst($strutture)."</b>", "", "1") ?>
			<div id="body">
				<div class="container">
          <p style="padding-bottom:10px;">
            <?php //$tblStrutture->_print("","","","-1"); ?>
          </p>
          <div><br></div>
          <div style="padding-bottom:10px;float:left;width:30%;border:0px red solid;">
          <?php stampaStruttura($strutture,$menid,"-1","","1",-1); ?>
          </div>         
          
          <div style="padding-top:15px;border-left:1px gray dashed;width:68%;float:right;padding-left:1%">
          <?php 
            if($menid!="") {
              $ex=retRow("categorie",$menid);
              if($ex['id']!="") {
                if(strlen($ex['nome'])>40) $ex['nome']=substr($ex['nome'], 0, 40)."...";
                ?><div style="font-size:20px;color:#607FBC;font-weight:bold;text-align:center;padding-bottom:10px;"><?php echo $ex['nome'] ?></div><?php
                $filter="";
                $tblDefault = new rsTable("categorie");
                $tblDefault->_print("id='$menid'","","","-1",$filter,"1"); 
                printSubTablesNav("categorie",$menid,1); 
              }
            } 
          ?>
          </div>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>