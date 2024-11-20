<html><title>:: Php-Stats - MySQL Query Utility ::</title>
<body>
<br>
<?php
// Per ragioni di sicurezza i file inclusi avranno un controllo di provenienza
define('IN_PHPSTATS', true);

if(isset($_POST['pswd']))	$pswd = addslashes($_POST['pswd']);
else						$pswd = '';
if(isset($_POST['q']))		$query = $_POST['q'];
else						$query = 'SELECT * FROM table WHERE 1';

// inclusione delle principali funzioni esterne
require('config.php');
require('inc/main_func.inc.php');
require('inc/user_func.inc.php');

// Connessione a MySQL e selezione database
db_connect();

if (user_is_logged_in() || user_login(false, $pswd))
{
	echo 'PHP version: ' . phpversion();
	echo '<br>';
	echo 'mySQL version: ' . mysql_get_server_info();
	echo '<br><br><br>';

    echo
    '<form action="query.php" method="post">'.
    'QUERY: <input type="text" name="q" size="100" value="'.$query.'"><br><br>'.
    '<input type="submit" value="Query">';
	
	if(isset($_POST['q']))
	{
		$ret = mysql_query($query);
		echo '<br><br><br>';
		echo 'mysql_query() = '.var_export($ret, true);
		echo '<br><br>';
		$err = mysql_error();
		if ($err == '')
			echo 'OK';
		else
			echo $err;
	}
}
else
{
    echo
    '<center><form action="query.php" method="post">'.
    'Php-Stats Password: <input name="pswd" type="password" value=""><br><br>'.
    '<input type="submit" value="Invia - Send"></center>';
}
?>
</body>
</html>
