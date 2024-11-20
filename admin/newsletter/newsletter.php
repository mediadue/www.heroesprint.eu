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
    $(function() {
        $('#container-1').tabs();
        $('#container-2').tabs(2);
        $('#container-3').tabs({ fxSlide: true });
        $('#container-4').tabs({ fxFade: true, fxSpeed: 'fast' });
        $('#container-5').tabs({ fxSlide: true, fxFade: true, fxSpeed: 'normal' });
        $('#container-6').tabs({
            fxFade: true,
            fxSpeed: 'fast',
            onClick: function() {
                alert('onClick');
            },
            onHide: function() {
                alert('onHide');
            },
            onShow: function() {
                alert('onShow');
            }
        });
        $('#container-7').tabs({ fxAutoHeight: true });
        $("#container-7").css("display","block");
        $('#container-8').tabs({ fxShow: { height: 'show', opacity: 'show' }, fxSpeed: 'normal' });
        $('#container-9').tabs({ remote: true });
        $('#container-10').tabs();
        $('#container-11').tabs({ disabled: [3] });

        $('<p><a href="#">Disable third tab<\/a><\/p>').prependTo('#fragment-28').find('a').click(function() {
            $(this).parents('div').eq(1).disableTab(3);
            return false;
        });
        $('<p><a href="#">Activate third tab<\/a><\/p>').prependTo('#fragment-28').find('a').click(function() {
            $(this).parents('div').eq(1).triggerTab(3);
            return false;
        });
        $('<p><a href="#">Enable third tab<\/a><\/p>').prependTo('#fragment-28').find('a').click(function() {
            $(this).parents('div').eq(1).enableTab(3);
            return false;
        });
    });
</script>


<script type="text/javascript">
$(document).ready(function() { 
    function successRequest(){
      $("div.news_wait").remove();
      $("input[name='news_submit']").show();
      $('#responsecontent').show('slow');
    }
    
    function showRequest(){
      $("input[name='news_submit']").hide();
      $("div.news_submit").prepend("<div class='news_wait'>Attendere prego...</div>");
      $('#responsecontent').hide('slow');
    }
    
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
  			<div id="container-7" style="display:none;">
          <ul>
              <li><a href="#fragment-1"><span>Ricerca singola</span></a></li>
              <li><a href="#fragment-2"><span>Ricerca per gruppi</span></a></li>
              
          </ul>
          <div id="fragment-1" style="height:2000px;">
            <?php
    				$rs = $objNewsletterGruppi->getList();
    				$rs = $objUsers->getGestione($_SESSION["user_id"],$rs,"roles");
            
            $rs2=getTable("users","","");
            $rs3=$rs2;
            $rs2 = $objUsers->getGestione($_SESSION["user_id"],$rs2,"users");
            $sqlwh="";
            
            while (list($key, $row) = each($rs2)) {
              $sqlwh.="id='".$row['id']."'";
              if($key<count($rs2)-1) $sqlwh.=" OR ";  
            }
            
            if(count($rs2)==0) $sqlwh="id='-1'";
            if($rs2===$rs3) $sqlwh="";
            
            $tblDefault->_print($sqlwh,"","","","id,login,nome,cognome,ragionesociale,indirizzo,citta,cap,provincia,regione,nazione,telefono,cellulare,fax,email,note,id_sesso,data_di_nascita,nucleo_familiare,id_professione,id_stato_civile,id_hobby1","2"); 
            ?>    
          </div>
          <div id="fragment-2">
            <b style="font-size:14px;">Ricerca per gruppi:<br><br></b>
					
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
  							<div class="value news_submit"><input name="news_submit" type="submit" value="Procedi" class="btn"/></div>
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
