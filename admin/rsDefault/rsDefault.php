<?php
session_start();



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

$tbl=$_GET['tbl'];
$tblDefault = new rsTable($tbl);

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."navigazione/in_place.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script language="JavaScript" type="text/javascript">
function confirmModDelete() {
	if (!(confirm("Cancellazione modulo.\n\nVerranno cancellate anche i menu relativi.\n\nSei sicuro di voler procedere?"))) {
		return false;
	}
}
function confirmMenDelete() {
	if (!(confirm("Cancellazione menu.\n\nSei sicuro di voler procedere?"))) {
		return false;
	}
}

$(document).ready(function(){
  var winOptions={
    'table': '<?php echo $tbl; ?>'
    //'title': '<?php echo $tbl; ?>'
  };
  
  var win=new rsWindows(winOptions);
  win.open();
});
//-->
</script> 
</head>
<body>
<div id="site">
  <?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("",""); ?>
			<div id="body">
				<div class="container">
					<?php 
          //cercaInTabella("3_testi","prova a prendermi");
          //cercaInStruttura("magazzino","prova");
          //$tblDefault->_print();
          
          //$win->addTable($tblDefault2);
          //$win->_print();
          ?>
          <?php $objHtml->adminDesktop(); ?>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>