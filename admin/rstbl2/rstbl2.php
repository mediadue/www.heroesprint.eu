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
//$tblDefault = new rsTable("rstbl2_tabelle");
$conn = $objDb->connection($objConfig);
$dbname = $objConfig->get("db-dbname");

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

loadTbl($dbname);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <?php $objHtml->adminHeadsection() ?>
    <script>
    $(document).ready(function(){
      var winOptions={
        'table': 'rstbl2_tabelle',
        'title': 'Tabelle'
      };
      var win=new rsWindows(winOptions);
      win.open();
    });
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
            <div class="container"></div>
          </div>
        </div>
      </div>
      <?php $objHtml->adminFooter() ?>
    </div>
  </body>
</html>