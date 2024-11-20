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
$tblUsers = new rsTable("users");
$tblAcquisti = new rsTable("acquisti");
$conn = $objDb->connection($objConfig);

session_start();

global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script type="text/javascript">
$(document).ready(function() { 
  if($("#casadc_acquisti").attr('id')!=null) $('html, body').animate({scrollTop: $("#casadc_acquisti").offset().top}, 500);
  return false;
}); 
</script>

<style>
a {color:gray;}
</style>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("Acquisti e punteggio", "") ?>
			<div id="body">
			<!-- Layout 1 -->
      <div class="ez-wr">
        <div class="ez-box">&nbsp;</div>
        <div class="ez-box"><?php $tblUsers->_print("","","","","id,nome,cognome,ragionesociale,indirizzo,citta,telefono,cellulare,fax,email","2"); ?></div>
        <!-- Module 2A -->
        <div class="ez-wr">
          <div class="ez-fl ez-negmr ez-50" style="width:150px;padding-top:20px;">
            <div class="ez-box">
            <?
            for($z=0;$z<count($_SESSION[$config_table_prefix."userscheckSel"]);$z++) {
              $intId=$_SESSION[$config_table_prefix."userscheckSel"][$z];
              $_SERVER["PHP_SELF"]="acquisti.php";
              ?><a href="?id=<?=$intId?>&menid=<?=$_GET['menid']?>" style="font-size:10px;<?php if($_GET['id']==$intId) echo 'font-weight: bold;color:blue;'; ?>"><?php $rs=retRow('users',$intId);if($rs['ragionesociale']!="") {echo $rs['ragionesociale']." ";};echo $rs['nome']." ".$rs['cognome']; ?></a><br><?
            }
            ?>
            </div>
          </div>
          <div class="ez-last ez-oh" style="border-left:1px gray dashed;padding-left:10px;">
            <div class="ez-box">
              <?php
              if(is_array($_SESSION[$config_table_prefix."userscheckSel"])) {
                if(in_array($_GET['id'],$_SESSION[$config_table_prefix."userscheckSel"])) { 
                  if($_GET['id']!="") $tblAcquisti->_print("(user_hidden='".$_GET['id']."')","","","","","2");
                } 
              }
              ?>
            </div>
          </div>
        </div>
      </div>
							

      </div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>