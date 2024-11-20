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

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);
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
			<?php $objHtml->adminPageTitle("statistiche", "installazione statistiche") ?>
			<div id="body">
				<div class="container">
          <?php
            global $config_table_prefix;
            
            /*
            $hostname = $objConfig->get("db-hostname");
        		$username = $objConfig->get("db-username");
        		$password = $objConfig->get("db-password");
        		$dbname = $objConfig->get("db-dbname");
            
            $file = 'standard.php';
            // Open the file to get existing content
            $current = file_get_contents($file);
            // Append a new person to the file
            $current = str_replace("option['host']= ''", "option['host']= '$hostname'", $current);
            $current = str_replace("option['database']= ''", "option['database']= '$dbname'", $current);
            $current = str_replace("option['user_db']= ''", "option['user_db']= '$username'", $current);
            $current = str_replace("option['pass_db']= ''", "option['pass_db']= '$password'", $current);
            $current = str_replace("option['script_url']=''", "option['script_url']='".'http://'.$_SERVER['SERVER_NAME'].$objUtility->getPathBackoffice()."stats"."'", $current);
            $current = str_replace("option['exc_pass']=''", "option['exc_pass']='MediaDue8386'", $current);
            $current = str_replace("option['prefix']=''", "option['prefix']='".$config_table_prefix."phpstats_"."'", $current);
            // Write the contents back to the file
            $fp = fopen('modified.php', 'w');
            fwrite($fp, $current);
            fclose($fp);
            */
          ?>
          <a href="setup.php" target="_blank">Installazione guidata di php-stats</a>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>