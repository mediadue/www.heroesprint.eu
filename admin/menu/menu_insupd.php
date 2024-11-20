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
$intIdmen = $objUtility->sessionVarRead("idmen");
switch ($strParam) {
	case "ins":
	case "upd":
		if ($strParam == "upd") {
			$rs = $objMenu->getMenuDetails($conn, $intIdmen);
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
				<?php $objJs->checkField("nome", "text", "NOME", "nome") ?>
				<?php //$objJs->checkField("path", "text", "PATH", "path") ?>
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
					<?php $objHtml->adminPageTitle("Menu", "") ?>
					<div id="body">
						<div class="inputdata">
							<form action="action.php" id="frm" name="frm" method="post" enctype="multipart/form-data" onsubmit="return checkForm()"/>
							<input type="hidden" name="idmoduli" value="<?php echo $objUtility->sessionVarRead("idmod") ?>"/>
							<div class="elemento">
								<div class="label"><label for="nome">nome </label>*</div>
								<div class="value"><input type="text" name="nome" id="nome" maxlength="50" class="text" value="<?php echo $row["nome"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="nomeen">path </label></div>
								<div class="value"><input type="text" name="path" id="path" maxlength="50" class="text" value="<?php echo $row["path"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="nomeen">tabella </label></div>
								<div class="value"><input type="text" name="tabella" id="tabella" maxlength="200" class="text" value="<?php echo $row["tabella"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="testo">icona</label></div>
								<div class="value"><input type="file" name="icona" id="icona" class="text" /></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="testo">desktop</label></div>
								<div class="value"><input type="checkbox" name="desktop" id="desktop" class="check" value="1" <?php if($row["desktop"]=="1") echo "checked" ?> /></div>
							</div>
							<div class="elemento">
								<div class="value"><input type="submit" name="act_MENU-INSUPD-DO" value="Salva" class="btn"/></div>
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