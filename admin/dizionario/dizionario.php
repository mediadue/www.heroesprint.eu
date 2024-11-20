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
$tblDefault = new rsTable("dizionario");
$conn = $objDb->connection($objConfig);
$dbname = $objConfig->get("db-dbname");

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

function loadDizionario($dbname) {
  global $config_table_prefix;
  
  $n=strlen($config_table_prefix);
  
  $sql="SELECT COLUMN_NAME,TABLE_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$dbname' AND LEFT(TABLE_NAME, ".strlen($config_table_prefix).")='$config_table_prefix' AND (DATA_TYPE='varchar' OR DATA_TYPE='longtext' OR DATA_TYPE='mediumtext') AND TABLE_NAME<>'".$config_table_prefix."traduzioni' AND TABLE_NAME<>'".$config_table_prefix."dizionario'";
  $query = mysql_query($sql);
  
  while($res=mysql_fetch_array($query)) {
    $table=$res['TABLE_NAME'];
    $column=$res['COLUMN_NAME'];
    
    $sql2="SELECT $column FROM `$table` ";
    $query2 = mysql_query($sql2);
    
    while($res2=mysql_fetch_array($query2)) {
      if(trim($res2[0])!="" && !is_numeric($res2[0])) {
        $tmpstr=addslashes($res2[0]);
        $sql3="SELECT testo_editor FROM `".$config_table_prefix."dizionario` WHERE testo_editor='".$tmpstr."' ";
        $query3 = mysql_query($sql3);
        
        if(mysql_num_rows($query3)==0) {
          $sql4="INSERT INTO `".$config_table_prefix."dizionario` (testo_editor) VALUES ('".$tmpstr."') ";
          $query4 = mysql_query($sql4);
        }
      }
    }
  }
}

if($_POST['loadDizionario']) loadDizionario($dbname);

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
	if (!(confirm("L'\operazione potrebbe richiedere più di 30 sec \n sicuro di voler procedere?"))) {
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
			<?php $objHtml->adminPageTitle("dizionario", "") ?>
			<div id="body">
				<form action="" id="frm" name="frm" method="post" onsubmit="return confirmMenDelete()"/>
        <div class="inputdata">
          <div class="elemento">
  					<div class="value"><input type="submit" name="loadDizionario" value="Aggiorna..." class="btn"/></div>
  				</div>
				</div>
				</form>
        
        <div class="container">
          <?php $tblDefault->_print(); ?>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>