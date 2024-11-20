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
$conn = false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <?php 
    $objHtml->adminHeadsection(); 
	
    unset($_SESSION["user_id"]);
    unset($_SESSION["user_login"]);
    unset($_SESSION["user_lastaccess"]);
    $_SESSION = array();
    
    session_destroy();
    ?>
  </head>
  <body onload="document.frm.utente.focus();">
    <div id="site">
      <?php $objHtml->adminHead("", "") ?>
      <div id="content">
        <?php //$objHtml->adminLeft($conn, $intIdutente) ?>
        <div id="area">
          <?php $objHtml->adminPageTitle("Login", "","1") ?>
          <div id="body">
            <div class="inputdata">
              <form action="action.php" method="post" name="frm">
                <div class="elemento">
                  <div class="label"><label for="user">username </label></div>
                  <div class="value"><input type="text" name="utente" id="utente" size="10" maxlength="20" class="text" value=""/></div>
                </div>
                <div class="elemento">
                  <div class="label"><label for="password">password </label></div>
                  <div class="value"><input type="password" name="pwd" id="pwd" size="10" maxlength="20" class="text" value=""/></div>
                </div>
                <div class="elemento">
                  <div class="label">&nbsp;</div>
                  <div class="value"><input type="submit" name="ACT_LOGIN-DO" value="Entra" class="btn"/></div>
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