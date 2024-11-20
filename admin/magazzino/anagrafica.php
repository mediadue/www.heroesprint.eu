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
$tblDefault = new rsTable("magazzino_articoli");
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

$menid=$_GET['menid'];

$strutture=getTable("strutture","","nome='magazzino'");
$css=$strutture[0]['css'];
$strutture=$strutture[0]['nome'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>

<style>

#<?=$css?> ul li a .selected {color:white;padding-left:2px;padding-right:3px;font-weight: bold;}

#<?=$css?> ul li .selected {background-color: rgb(96,127,188);}
#<?=$css?> ul li {list-style: none inside url();padding-bottom:0px;}
#<?=$css?> ul li:hover {padding-bottom:0px;background-color: white;}

#<?=$css?> .ul1 li {padding-left:0px}
#<?=$css?> .ul2 li {padding-left:10px}
#<?=$css?> .ul3 li {padding-left:20px}
#<?=$css?> .ul4 li {padding-left:30px}
#<?=$css?> .ul5 li {padding-left:40px}

#<?=$css?> ul li {padding-top:2px;}                 
#<?=$css?> ul li a {color:black;text-decoration: none;text-transform: lowercase;float:left;}
#<?=$css?> ul li a:hover {text-decoration: underline;}

.combonav {
  width: 200px;
}
</style>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("magazzino", "anagrafica articoli") ?>
			<div id="body">
				<div class="container">
          <div><br><br></div>
          <div style="padding-bottom:10px;float:left;width:30%;border:0px red solid;">
            <?php stampaStruttura("magazzino",$menid,"-1","","1",-1); ?>
          </div>
          <div style="padding-top:0px;border-left:1px gray dashed;width:68%;float:right;padding-left:1%">
            <?php $_SESSION['anagrafica_menid']=$_GET['menid']; ?>
  					<?php if($menid!="") $tblDefault->_print("id_categorie_str_magazzino='$menid' AND del_hidden='0'","","","","id,Codice,Descr1,Descr2,IVA,Fornitore,Um1,Um2,Confez,Costo_cry,Prezzo_cry,Note,aggiornato","1"); ?>
				  </div>
        </div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>