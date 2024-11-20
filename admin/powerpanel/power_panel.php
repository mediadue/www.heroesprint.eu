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
$eventi = new rsTable("eventi");

$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."powerpanel/power_panel.php");

$power_panel=getTable("power_panel","","id_users='$intIdutente' AND attivo='1'");
 
while (list($key, $row) = each($power_panel)) {
  $sqlWhere.="id='".$row['id_eventi']."'";
  if($key<(count($power_panel)-1)) $sqlWhere.=" OR ";  
}

if($sqlWhere=="") $sqlWhere="id='-1'";

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
			<?php $objHtml->adminPageTitle("",""); ?>
			<div id="body">
				<div class="container">
					<?php $eventi->_print($sqlWhere,"","","fotogallery,pdf,servizi_offerti_list,video","id,immagine_file,immagine_anagrafica_file,nome1_lst,descrizione1_editor,id_comuni,indirizzo,localita,CAP,Telefono,Fax,Cellulare,email,Sito,data_inizio,data_fine,data_inizio2,data_fine2,pubblica",1); ?>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>