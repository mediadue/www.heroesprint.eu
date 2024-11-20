<?php

Class Db {
	
	// ******************************************************************************************
	function connection(&$objConfig) {
		$hostname = $objConfig->get("db-hostname");
		$username = $objConfig->get("db-username");
		$password = $objConfig->get("db-password");
		$dbname = $objConfig->get("db-dbname");
		
		$conn = mysql_connect($hostname, $username, $password);
		$db = mysql_select_db($dbname, $conn);
		//mysql_query("SET time_zone = '+00:00';", $conn);
		//mysql_query("SET NAMES utf8;");
		//mysql_query("SET CHARACTER_SET utf8;");
    return $conn;
	}

}

?>