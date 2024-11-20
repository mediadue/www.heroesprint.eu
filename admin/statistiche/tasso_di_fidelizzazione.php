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
  $('#frm').ajaxForm(options); 
}); 
</script>

</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("statistiche", "Spesa a entrata") ?>
			<div id="body">
				<div class="container">
				  <div class="inputdata"> 
            <form action="action.php" id="frm" name="frm" method="post" enctype="multipart/form-data" />
              <div class="elemento">
        				<?php 
                $label="";
                $column="data";
                ?>
                <div class="label"><? echo $label; ?><label for="<? echo $label; ?>"> </label></div>
        				<div class="value">
                  <span style="padding-right:5px;font-weight:bold;">dal</span> <? formdata($column,'','','','',''); ?>
                  <span style="padding-right:5px;font-weight:bold;padding-left:5px;">al</span> <? formdata($column."_a",'','','','',''); ?>
                  &nbsp;&nbsp;<input type="submit" name="act_GEN-GRAPH-TASSO-FIDELIZZAZIONE" value="crea grafico" class="btn" />
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