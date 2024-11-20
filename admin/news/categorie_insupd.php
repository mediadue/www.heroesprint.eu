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
$objNews = new News;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."news/news.php");

$strParam = strtolower($objUtility->sessionVarRead("action"));
$intIdcat = $objUtility->sessionVarRead("idnewscat");
switch ($strParam) {
	case "ins":
	case "upd":
		if ($strParam == "upd")
		{
			$rs = $objNews->categorieGetDetails($conn, $intIdcat);
			if (count($rs) > 0)
				list($key, $row) = each($rs);
		}
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
		<head>
			<?php $objHtml->adminHeadsection() ?>
			<script type="text/javascript">
			<!--
			function checkForm(theform) {
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
				<?php $objHtml->adminLeft($conn, $intIdutente) ?>
				<div id="area">
					<?php $objHtml->adminPageTitle("Prodotti", "Categorie") ?>
					<div id="body">
						<div class="inputdata">
							<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm(this)"/>
							<div class="elemento">
								<div class="label"><label for="nome">nome </label>* </div>
								<div class="value"><input type="text" name="nome" id="nome" maxlength="50" class="text" value="<?php echo $row["nome"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="descrizione">descrizione </label></div>
								<div class="value"><textarea name="descrizione" rows="5" cols="40" class="default"><?php echo $row["descrizione"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="ishidden">nascondi </label></div>
								<div class="value"><input type="checkbox" name="ishidden" id="ishidden" value="1"<?php echo ($row["ishidden"]) ? " checked=\"yes\"" : "" ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="importanza">importanza </label></div>
								<div class="value"><input type="text" name="importanza" id="ordine" maxlength="10" class="textsmall" value="<?php echo $row["importanza"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="value"><input type="submit" name="act_CATEGORIE-INSUPD-DO" value="Salva" class="btn" /></div>
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