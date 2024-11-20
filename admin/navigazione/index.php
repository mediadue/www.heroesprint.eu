<?php

include ("_docroot.php");
include (SERVER_DOCROOT . "logic/class_config.php");

$objConfig = new ConfigTool();
$objUtility = new Utility;

header ("Location: " . $objUtility->getPathBackoffice());
?>