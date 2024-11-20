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
$tblDefault = new rsTable("archivio_newsletter");
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);
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
//-->
</script>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("newsletter", "") ?>
			<div id="body">
				<div class="container">
					<?php 
          $rs2=getTable("users","","");
          $rs3=$rs2;
          $rs2 = $objUsers->getGestione($_SESSION["user_id"],$rs2,"users");
          
          $sqlwh="";
          while (list($key, $row) = each($rs2)) {
            $sqlwh.="id='".$row['id']."'";
            if($key<count($rs2)-1) $sqlwh.=" OR ";  
          }
          
          if(count($rs2)==0) $sqlwh="id='-1'"; 
          if($rs2===$rs3) $sqlwh=""; 
          
          $tblDefault->_print($sqlwh,"","","","","2"); 
          ?>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>