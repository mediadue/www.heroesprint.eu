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
$dbname = $objConfig->get("db-dbname");

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>

<script>
$(document).ready(function() { 
  function showRequest() {
    $("#responseGraph").hide("slow");
  }
  
  function showResponse() {
    $("#responseGraph").show("slow");
  }
  
  var options = { 
      target:        '#responseGraph',   // target element(s) to be updated with server response 
      beforeSubmit:  showRequest,  // pre-submit callback 
      success:       showResponse  // post-submit callback 

      // other available options: 
      //url:       url         // override for form's 'action' attribute 
      //type:      type        // 'get' or 'post', override for form's 'method' attribute 
      //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
      //clearForm: true        // clear all form fields after successful submit 
      //resetForm: true        // reset the form after successful submit 

      // $.ajax options can be used here too, for example: 
      //timeout:   3000 
  }; 

  // bind form using 'ajaxForm' 
  $('#frm1').ajaxForm(options); 
}); 
</script>

</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("statistiche", "Quanti clienti acquistano") ?>
			<div id="body">
				<div class="container">
				  <div class="inputdata"> 
            <form action="action.php" id="frm1" name="frm1" method="post" enctype="multipart/form-data" />
              <div class="elemento">
        				<?php 
                $label="";
                $column="data";
                ?>
                <div class="label">Vendite per marchio:<label for="<? echo $label; ?>"> </label></div>
        				<div class="value">                                                   
                  <select name='fornitore'>
                    <option value = '' >
                    <?php 
                    $sql="SELECT Fornitore FROM `".$config_table_prefix."magazzino_articoli` GROUP BY Fornitore ORDER BY Fornitore ASC";
                    $result = mysql_query($sql);
                    while($row = mysql_fetch_array($result)){  
                      ?><option value='<?=$row['Fornitore']?>'><?=$row['Fornitore']?><? 
                    }
                    ?>
                  </select>
                  &nbsp;&nbsp;<input type="submit" name="act_GEN-GRAPH-CLIENTI-ACQUISTANO" value="crea grafico" class="btn" />
                </div>
                <div class="label">Vendite per categoria:<label for="<? echo $label; ?>"> </label></div>
                <div class="value" style="border:0px red solid;padding-left:160px;">
                  <?php
                  //if($_GET['menid']=="") $_GET['menid']=$cell;
                  stampaStruttura("magazzino",$_GET['menid'],"-1","","2",-1); 
                  ?>
                  <div class="value"><input type="hidden" name="categoria" value="<?=$_GET['menid']?>"/></div>
                  
                  &nbsp;&nbsp;<input type="submit" name="act_GEN-GRAPH-CLIENTI-ACQUISTANO-CAT" value="crea grafico" class="btn" />
                </div>
               </div>
               <div class="label">Invia comunicazioni:<label for="<? echo $label; ?>"> </label></div>
                <div class="value">
                  &nbsp;&nbsp;<input type="image" name="act_BTN-CLICK-UTENTI-MAIL" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_email.png" alt="invia comunicazioni" title="invia comunicazioni" style="position:absolute;margin-top:3px;" />
                </div>
               </div>
            </form>
    			</div>
          
          <div id="responseGraph"></div>	
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>