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
$conn = $objDb->connection($objConfig);
$dbname = $objConfig->get("db-dbname");

$objUsers->getCurrentUser($intIdutente, $strUsername);
//$objMenu->checkRights($conn, $intIdutente);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script language="JavaScript" type="text/javascript">
  function confirmDelete() {
  	if (!(confirm("Sei sicuro di voler procedere?"))) {
  		return false;
  	}
  }
  
  function copia(area) {
    var appoggio=area;
    appoggio.focus();
    appoggio.select();
    intervallo=appoggio.createTextRange();
    intervallo.execCommand("Copy");
  }
  
  function cambiaTD (element,txt,c,rid) {
    if(rid==1) {
      element.innerHTML=txt.substr(0,200)+" ...<span id='cambiatd' onclick=\"cambiaTD(getElementById('"+element.id+"'),testo"+c+","+c+","+0+");\">(continua)</span>";
    } else {
      element.innerHTML=txt+" ...<span id='cambiatd' onclick=\"cambiaTD(getElementById('"+element.id+"'),testostrip"+c+","+c+","+1+");\">(riduci)</span>";;
    }
  }
</script>
</head>
<body>
<div id="pr-site">
	<?php //$objHtml->adminHead() ?>
	<div id="content" >
		<?php //$objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="pr-area" >
			<?php //$objHtml->adminPageTitle("dizionario", "lingue") ?>
			<div id="body">
				<div class="container">
					<?
          $fun=$_GET['fun'];
          $paramArray=array();
          
          for($i=1;$i<51;$i++) {
            $p="param".$i;
            if($_GET[$p]) array_push($paramArray,$_GET[$p]);
          }
          
          if(function_exists($fun)) {
            call_user_func_array($fun, $paramArray);
          }
          ?>
				</div>
			</div>
		</div>
	</div>
	<?php //$objHtml->adminFooter() ?>
</div>
</body>
</html>
