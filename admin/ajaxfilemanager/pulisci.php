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

function eliminaFile($path,$file) {
  global $config_table_prefix;
  global $dbname;
  
  if($file=="") return false;
  
  $n=strlen($config_table_prefix);
  
  $sql="SELECT COLUMN_NAME,TABLE_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$dbname' AND LEFT(TABLE_NAME, ".strlen($config_table_prefix).")='$config_table_prefix' AND (DATA_TYPE='varchar' OR DATA_TYPE='longtext' OR DATA_TYPE='mediumtext')";
  $query = mysql_query($sql);
  
  $j=count($file);
  while($res=mysql_fetch_array($query)) {
    $table=$res['TABLE_NAME'];
    $column=$res['COLUMN_NAME'];

    for($i=0;$i<$j;$i++) {
      $sql2="SELECT $column FROM `$table` WHERE $column LIKE '%".$file[$i]."%'";
      $query2 = mysql_query($sql2);
      
      if($query2!==FALSE){
        if(mysql_num_rows($query2)>0) {
          $file[$i]=false;
        }
      }
      
      if($table==$config_table_prefix."oggetti") {
        $sql2="SELECT id FROM `".$config_table_prefix."oggetti` WHERE CONCAT(nome,'.',ext)='".$file[$i]."'";
        $query2 = mysql_query($sql2);  
      }
      
      if($query2!==FALSE){
        if(mysql_num_rows($query2)>0) {
          $file[$i]=false;
        }
      }
    }
  }
  

  $sql="SELECT id,CONCAT(nome,'.',ext) as fullname FROM `".$config_table_prefix."oggetti` ";
  $query = mysql_query($sql);
  while($res=mysql_fetch_array($query)) {
    if(!file_exists($path.$res['fullname'])) {
      $sql2="DELETE FROM `".$config_table_prefix."oggetti` WHERE id='".$res['id']."'";
      $query2 = mysql_query($sql2); 
    } 
  }

  return $file;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
</head>
<body>
<div id="site">
	<?php $objHtml->adminHead() ?>
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente) ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("dizionario", "lingue") ?>
			<div id="body">
				<div class="container">
				  <?php 
          $list=list_directory($objUtility->getPathResourcesDynamicAbsolute(),"file");
          $list=eliminaFile($objUtility->getPathResourcesDynamicAbsolute(),$list);
          $elim=0;

          while (list($key, $row) = each($list)) {
            if($row!=false && $row!="_docroot.php") {
              unlink($objUtility->getPathResourcesDynamicAbsolute().$row);
              $elim=1;
              echo $objUtility->getPathResourcesDynamic().$row.": <span style='color:red'>eliminato</span><br>";
            }
          }
          
          if($elim==0) {
            ?><span style='color:red;font-size:14px;'>Nessun file da eliminare</span><?
          }
          ?>  	
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>