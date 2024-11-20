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

$menid=$_GET['menid'];

if($_POST['str']=="") $_POST['str']=$_SESSION['str'];

//echo $_SESSION['str'];

if($_POST['str']) {
  $_SESSION['str']=$_POST['str'];
  $strutture=retRow("strutture",$_POST['str']);
  $css=$strutture['css'];
  $strutture=$strutture['nome'];
}

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

<style>

#<?=$css?> ul li a .selected {font-weight: bold;color: black;}

#<?=$css?> ul {list-style: square inside url()}
#<?=$css?> .ul1 li {padding-left:10px;color:red;}
#<?=$css?> .ul2 li {padding-left:20px;color:orange;}
#<?=$css?> .ul3 li {padding-left:30px;color:green;}
#<?=$css?> .ul4 li {padding-left:40px;color:gray;}
#<?=$css?> .ul5 li {padding-left:50px;color:brown;}

#<?=$css?> .ul1 li a {color:red;}
#<?=$css?> .ul2 li a {color:orange;}
#<?=$css?> .ul3 li a {color:green;}
#<?=$css?> .ul4 li a {color:gray;}
#<?=$css?> .ul5 li a {color:brown;}

#<?=$css?> ul li a {text-decoration: none;}
#<?=$css?> ul li a:hover {text-decoration: underline;}

</style>

</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("navigazione", "anteprima strutture") ?>
			<div id="body">
				<div class="container">
          <form action="" method="post" name="frm">
          <?php comboBox("strutture",$field1="",$field2="",$selected=$_POST['str'],$multiple="",$onchange="document.frm.submit();",$echoId="",$nome="str"); ?>
          <br><br>
          <?php stampaStruttura($strutture,$menid,"-1"); ?>
          </form>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>