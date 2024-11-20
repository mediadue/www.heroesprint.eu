<?php
session_start();

header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");

require_once("_docroot.php");
require_once(SERVER_DOCROOT."/logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$conn = $objDb->connection($objConfig);

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."utenti/roles.php");
$isSystem=$objUsers->isSystem($conn, $intIdutente);

$param = strtolower($objUtility->sessionVarRead("action"));
$id = $objUtility->sessionVarRead("idroles");
switch ($param) {
	case "ins":
	case "upd":
		if ($param == "upd") {
			$rs = $objUsers->rolesGetDetails($conn, $id);
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
				theform.submit();
			}
			//-->
			</script>
		</head>
		<body>
		<div id="site">
			<?php $objHtml->adminHead() ?>
			<div id="content">
				<?php $objHtml->adminLeft($conn, $intIdutente,"utenti/roles.php") ?>
				<div id="area">
					<?php $objHtml->adminPageTitle("Gestione Gruppi", "") ?>
					<div id="body">
						<div class="inputdata">
							<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()"/>
							<div class="elemento">
								<div class="label"><label for="nome">nome </label></div>
								<div class="value"><input type="text" name="nome" id="nome" maxlength="100" class="text" value="<?php echo $row["nome"] ?>"/></div>
							</div>
							<?php if($isSystem) { ?>
              <div class="elemento">
								<div class="label"><label for="issystem">system </label></div>
								<div class="value"><input type="checkbox" name="issystem" value="1"<?php echo ($row["issystem"]) ? " checked=\"yes\"" : "" ?>"/></div>
							</div>
              <? } ?>	
							<div class="elemento">
								<div class="value"><input type="submit" name="act_ROLES-INSUPD-DO" value="Salva" class="btn"/></div>
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