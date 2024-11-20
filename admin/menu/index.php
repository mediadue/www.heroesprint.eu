<?php
include ("_docroot.php");
include (SERVER_DOCROOT . "logic/class_config.php");
$objUtility = new Utility;
header ("Location: " . $objUtility->getPathBackoffice());
?>
