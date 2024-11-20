<?php



include ("_docroot.php");
include (SERVER_DOCROOT . "/logic/class_config.php");
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
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."menu/menu.php");

$strParam = strtolower($objUtility->sessionVarRead("action"));
$intIdmod = $objUtility->sessionVarRead("idmod1");
switch ($strParam) {
	case "ins":
	case "upd":
		if ($strParam == "upd") {
			$rs = $objMenu->getMenuModuli1Details($conn, $intIdmod);
			if (count($rs) > 0) {
				list($key, $row) = each($rs);
			}
		}
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
		<head>
			<?php $objHtml->adminHeadsection() ?>
			<script type="text/javascript">
			<!--
			function checkForm() {
				var theform = document.frm;
				<?php $objJs->checkField("titolo", "text", "TITOLO", "titolo") ?>
				<?php $objJs->checkField("testo", "text", "TITOLO VISUALIZZATO", "testo") ?>
				theform.submit();
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
					<?php $objHtml->adminPageTitle("Menu", "Categorie Proncipali") ?>
					<div id="body">
						<div class="inputdata">
							<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()"/>
              <div class="elemento">
								<div class="label"><label for="titolo">titolo </label>*</div>
								<div class="value"><input type="text" name="titolo" id="titolo" maxlength="50" class="text" value="<?php echo $row["titolo"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="testo">titolo visualizzato</label>*</div>
								<div class="value"><input type="text" name="testo" id="testo" maxlength="50" class="text" value="<?php echo $row["testo"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label">&#160;</div>
								<div class="value"><input type="submit" name="act_MODULI1-INSUPD-DO" value="Salva" class="btn"/></div>
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
		<?php
		break;
}
?>