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
			<?php $objHtml->adminPageTitle("irc", "nuova tabella") ?>
			<div id="body">
				<div class="inputdata">
					<form action="action.php" id="frm" name="frm" method="post" enctype="multipart/form-data" onsubmit="return checkForm()"/>					
            <div class="elemento">
  						<div class="label"><label for="localita">Nome tabella </label></div>
  						<div class="value"><input type="text" name="nometabella" id="periodicitarevisione" maxlength="50" class="text" value="<?= $objAutomezzi->Get("periodicitarevisione"); ?>"/></div>
  					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>