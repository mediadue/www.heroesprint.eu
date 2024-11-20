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

$objUsers->getCurrentUser($intIdutente, $strUsername);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead(); ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente); ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("Home", ""); ?>
			<div id="body">
				<?php $objHtml->adminDesktop(); ?>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter(); ?>
</div>
</body>
</html>