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
$tblDefault = new rsTable("tabelle");
$conn = $objDb->connection($objConfig);
$dbname = $objConfig->get("db-dbname");

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

function loadTbl($dbname) {
  global $config_table_prefix;

  $sql="SHOW TABLE STATUS FROM $dbname";
  $query = mysql_query($sql);

  while($res=mysql_fetch_array($query)) {
    $sql2="SELECT id FROM `".$config_table_prefix."tabelle` WHERE Nome='".$res[0]."' ";
    $query2 = mysql_query($sql2);

    if(mysql_num_rows($query2)==0) {
      if(strpos($res[0],"_nm")===FALSE && !(strpos($res[0],$config_table_prefix)===FALSE)) {
        $sql3="INSERT INTO `".$config_table_prefix."tabelle` (Nome, Modifica, Elimina, Aggiungi) VALUES ('".$res[0]."', 1, 1, 1) ";
        $query3 = mysql_query($sql3);
      }
    }
  }
}

loadTbl($dbname);

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
          <?php $objHtml->adminPageTitle("tabelle", "") ?>
          <div id="body">
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