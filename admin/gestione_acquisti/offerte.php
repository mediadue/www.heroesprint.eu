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

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

$rs=$objUsers->usersGetRolesEx($conn,$intIdutente);

$colfilter="id,id_archivio_richieste_offerta,Data,id_ga_prodotti,articolo_richiesto,fornitore,idfornitore_hidden,data_offerta,ga_prezzo_cry,descrizione,Errori";
while (list($key, $row) = each($rs)) {
  if($row['nome']=="fornitori della gestione acquisti") {
    $filter="idfornitore_hidden='$intIdutente'";
    $colfilter="id,id_archivio_richieste_offerta,Data,idga_prodotti,articolo_richiesto,fornitore,idfornitore_hidden,data_offerta,ga_prezzo_cry,descrizione";
  }
}

$tblTable = new rsTable("form_archivio_offerte");

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
			<?php $objHtml->adminPageTitle("gestione acquisti", "") ?>
			<div id="body">
				<div class="container">
					<?php $tblTable->_print($filter,"","","",$colfilter); ?>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>