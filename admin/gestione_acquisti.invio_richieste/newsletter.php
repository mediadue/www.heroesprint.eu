<?php
header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: Public");

require_once("_docroot.php");
require_once(SERVER_DOCROOT."logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$objMailing = new Mailing;
$objNewsletterGruppi = new NewsletterGruppi;
$tblDefault = new rsTable("users");
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script type="text/javascript">
$(document).ready(function() { 
    function successRequest(){$('#responsecontent').show('slow');}
    function showRequest(){$('#responsecontent').hide('slow');}
    var options = { 
        target:        '#responsecontent',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:        successRequest  // post-submit callback 
 
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
    $('#myform').ajaxForm(options);
    return false;
}); 
</script>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("newsletter", "invio") ?>
			<div id="body">
  			<div>
          
          <div>
            <?php $rs = $objNewsletterGruppi->getList(); ?>
            <b style="font-size:14px;">&nbsp;<br><br></b>
					
            <form action="newsletter_users.php" method="post" id="myform">
  					<table cellspacing="0" class="default">
  						<tr>
  							<th scope="col">&nbsp;</th>
  							<th scope="col" style="width:95%;">Gruppi</th>
  						</tr>
  						<?php
  
  						$i=0;
  						while (list($key, $row) = each($rs))
  						{
  							$i++;
  							?>
  							<tr>
  								<td><input type="checkbox" name="id_<?php echo $i ?>" value="<?php echo $row["id"] ?>"/></td>
  								<td><?php echo  $row["nome"]; ?></td>
  							</tr>
  							<?php
  						}
  						?>
  					</table>
  					<input type="hidden" name="id_tot" value="<?php echo $i ?>"/>
  					<div class="inputdata">
  						<div class="elemento">
  							<div class="value"><input type="submit" value="Procedi" class="btn"/></div>
  						</div>
  					</div>
  					</form>
  					<div id="responsecontent" class="responsecontent" style="display:none;"><!-- ajax-content --></div>
          </div>
      </div>	
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>
