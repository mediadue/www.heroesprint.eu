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
$conn = $objDb->connection($objConfig);

session_start();

global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

$menid=str_replace("?", "", $_GET['menid']);
$objUtility->sessionVarUpdate("menid",$menid);

$strutture=getTable("strutture","","nome='magazzino'");
$css=$strutture[0]['css'];
$strutture=$strutture[0]['nome'];
$tblAcquisti = new rsTable("acquisti");

if(!isset($_GET['menid'])) $_SESSION[$config_table_prefix."userscheckSel"]=array();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<?php $objHtml->adminHead() ?>
<script type="text/javascript">
$(document).ready(function() {       
    if($("#casadc_acquisti").attr('id') != null) $('html, body').animate({scrollTop: $("#casadc_acquisti").offset().top}, 500);
    return false;
}); 
</script> 
</head>
<body>
<div id="site">
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("Analisi vendite", "") ?>
			<div id="body">
				<!-- Module 2A -->
        <div class="ez-wr">
          <div class="ez-fl ez-negmr ez-50" style="width:300px;">
            <div class="ez-box"><?php stampaStruttura("magazzino",$menid,"-1","","1",-1); ?></div>
          </div>
          <?php 
          $tipo=$menid;
          
          if($tipo!="") {
            $figli=getStruttura("magazzino",$tipo);
            array_unshift($figli, $tipo);
            $strWh="";
            while (list($key, $value) = each($figli)) {
              $strWh=$strWh."id_categorie_str_magazzino='".$value."' OR ";
            } 
            $strWh=substr($strWh, 0, strlen($strWh)-3);
            $magazzino_articoli=getTable("magazzino_articoli","",$strWh);
            
            $strWh="";
            while (list($key, $value) = each($magazzino_articoli)) {
              $strWh=$strWh."id_magazzino_articoli='".$value['id']."' OR ";
            }
            $strWh=substr($strWh, 0, strlen($strWh)-3);
            $strWhMG=$strWh;
            $acquisti=getTable("acquisti","",$strWh);
            
            $strWh="";
            while (list($key, $value) = each($acquisti)) {
              $strWh=$strWh."id='".$value['user_hidden']."' OR ";
            }
            $strWh=substr($strWh, 0, strlen($strWh)-3); 
            if($strWh=="") {
              $strWh="id='-1'";
              $_SESSION[$config_table_prefix."userscheckSel"]=array();
            } 
            ?>
            <div class="ez-last ez-oh" style="border-left:1px gray dashed;padding-left:10px;">
              <!-- Layout 1 -->
              <div class="ez-wr">
                <div class="ez-box">&nbsp;</div>
                <div class="ez-box"><?php $tblUsers->_print($strWh,"","","","id,nome,cognome,ragionesociale,indirizzo,citta,telefono,cellulare,fax,email","2"); ?></div>
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
                          if($_GET['id']!="") $tblAcquisti->_print("(user_hidden='".$_GET['id']."') AND ($strWhMG)","","","","","2");
                        } 
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <? } ?>
        </div>
      </div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>