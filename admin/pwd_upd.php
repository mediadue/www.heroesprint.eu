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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script type="text/javascript">
function checkForm() {
	var theform = document.frm;
	<?php $objJs->checkField("password", "password", "PASSWORD", "password") ?>
	if (theform.password.value != theform.password_conf.value) {
		alert('Le password non coincidono');
		theform.password.focus();
		return false;
	}
	return true;
}
</script>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("Modifica password", "") ?>
			<div id="body">
				<div class="inputdata">
					<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()">
					<div class="elemento">
						<div class="label"><label for="password">password </label></div>
						<div class="value"><input type="password" name="password" id="password" size="10" class="text" value=""/></div>
					</div>
					<div class="elemento">
						<div class="label"><label for="password_conf">conferma password </label></div>
						<div class="value"><input type="password" name="password_conf" id="password_conf" size="10" class="text" value=""/></div>
					</div>
					<div class="elemento">
						<div class="value"><input type="submit" value="Salva" name="ACT_PWDUPD-DO" class="btn"/></div>
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