<?php
if(!isAjaxPost()) { ?>
  <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $objUtility->getPathCss(); ?>font-awesome.min.css">
  <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $objUtility->getPathCss(); ?>google_fonts.css">
  
  <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $objUtility->getPathRoot(); ?>css/inc.rsStyle.css" />
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources(); ?>inc.rsJavaScript.js"></script>
  <script src="https://www.paypalobjects.com/api/checkout.min.js"></script>
  
  <?php ob_start(); ?>
  <script>
  $(document).ready(function() {
    $.ajax({
     type: "POST",
     url: "inc.menu.php",
     data: "",
     success: function(msg){
        $("ul.nav-service").replaceWith(msg);
     },
     error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
            }
    });
  });
  </script>
  
  <?php 
  if($_SESSION['rsOpenBox']==1){ ?>
    <script>
    $(document).ready(function() {
        $("body").append($("#rsBox-alert"));
  
        $("#rsBox-alert").show();
        $("#rsBox-alert").animate({opacity: 1.0},500 );
        $("#rsBox-alert").animate({opacity: 0.9},500 ); 
        
        $("#rsBox-alert .box-close").click(function() {
            $("#rsBox-alert").fadeOut(500);
            return false;
          });
      });
      </script>
    <? 
    $_SESSION['rsOpenBox']="";
  }
  
  if($_SESSION["userris_login"]) { 
    $objJs = new Js; ?>
    <script type="text/javascript"> 
    function rsCheckFormCmbPwd() {
    	var theform = document.cmbpwd;
    	<?php $objJs->checkField("password", "password", "PASSWORD", "password") ?>
    	if (theform.password.value != theform.password_conf.value) {
    		alert('<?php echo addslashes(ln("Le password non coincidono"));?>');
    		theform.password.focus();
    		return false;
    	}
    	return true;
    }
    
    $(document).ready(function() {
      $("#InfoAreaRis .cmbpwdvis").bind("click",function() {
        if($("#cmbpwd").css("display")=="none") $("#cmbpwd").show("slow"); else $("#cmbpwd").hide("slow");
      }); 
      
      $("#InfoAreaRis .theClose").click(function() {
        if($("#cmbpwd").css("display")=="none") $("#cmbpwd").show("slow"); else $("#cmbpwd").hide("slow");
      }); 
    });
    </script>
  <? }
  include(SERVER_DOCROOT . 'include/inc.jfunctions.php');
  $tjscode=ob_get_contents();
  ob_end_clean();
  if(isset($objCarrello)) $tjscode.=$objCarrello->g_jsCode;
  if(isset($objHtml)) $tjscode.=$objHtml->g_jsCode;
  $compressedJs=compressJs($tjscode);
  //$compressedJs=$tjscode;
  
  echo $compressedJs; 
}
?>

