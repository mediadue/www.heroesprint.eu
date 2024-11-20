<?php

Class Menu {

// ******************************************************************************************
function checkRights($conn, $intIdutente, $pathScript=false, $strUrl=false, $exit="") {
  global $config_table_prefix, $objUtility;
	$objUtility = new Utility;
	$objHtml = new Html;
  $pathScript = (!$pathScript) ? $_SERVER["SCRIPT_NAME"] : $pathScript;
	$pathBackoffice = $objUtility->getPathBackoffice();
  $isAuthorized = false;
	if (substr($pathScript, 0, strlen($pathBackoffice)) == $pathBackoffice)
	{
		$pathScriptRel = substr($pathScript, strlen($pathBackoffice));
		$strSql = "SELECT * FROM ".$config_table_prefix."roles_users_nm ru LEFT JOIN ".$config_table_prefix."roles r ON ru.idroles=r.id LEFT JOIN ".$config_table_prefix."roles_menu_nm rm ON rm.idroles=r.id LEFT JOIN ".$config_table_prefix."menu m ON rm.idmenu=m.id WHERE ru.idusers=" . $intIdutente . " AND m.path='" . $pathScriptRel . "'";
		//echo $strSql;
    $query = mysql_query ($strSql, $conn);
		$utility = new Utility;
		$rs = $utility->buildRecordset($query);
		if (count($rs)) {
			$isAuthorized = true;
		}
	}
	if (!$isAuthorized)
	{
		if($exit!="") return $isAuthorized;
    if (!$strUrl)
			//$strUrl = $objUtility->getPathBackoffice() . "logout.php";
      ?>
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html>
        <head>
          <?php $objHtml->adminHeadsection() ?>
        </head>
        <body>
          <?php 
          box("Non si hanno i permessi per accedere a questa funzionalità.<br><br><a href='".$objUtility->getPathBackofficeAdmin()."'>torna indietro</a>");
          //header ("Location: " . $strUrl);
          ?>
          <meta http-equiv="refresh" content="3;url=<?php echo $objUtility->getPathBackofficeAdmin(); ?>" />
        </body>
      </html>
      <?
		  exit();
	}
	return $isAuthorized;
}

// ******************************************************************************************
// MODULI

// ******************************************************************************************
function getMenuModuli($conn, $iduser, $isfull,$intIdmod="") {
	global $config_table_prefix;
	
  if ($isfull) {
		$sqlw="";
    if($intIdmod!="") $sqlw="WHERE idcategorie='$intIdmod'";
    $strSql = "SELECT * FROM ".$config_table_prefix."menu_moduli $sqlw ORDER BY ordine ASC, titolo ASC";
	} else {
	  $sqlw="";
    if($intIdmod!="") $sqlw="AND mm.idcategorie='$intIdmod'";
		$strSql = "SELECT DISTINCT mm.* FROM ".$config_table_prefix."roles_menu_nm AS rm LEFT JOIN ".$config_table_prefix."roles_users_nm ru ON ru.idroles=rm.idroles LEFT JOIN ".$config_table_prefix."menu m ON rm.idmenu=m.id LEFT JOIN ".$config_table_prefix."menu_moduli mm ON m.idmoduli=mm.id WHERE ru.idusers=" . $iduser . " $sqlw ORDER BY mm.ordine ASC, mm.titolo ASC";
	}
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getMenuModuli1($conn, $iduser, $isfull) {
	global $config_table_prefix;
	if ($isfull) {
		$strSql = "SELECT * FROM ".$config_table_prefix."menu_categorie ORDER BY ordine ASC, titolo ASC";
	} else {
		$strSql = "SELECT DISTINCT mm.* FROM ".$config_table_prefix."roles_menu_nm AS rm LEFT JOIN ".$config_table_prefix."roles_users_nm ru ON ru.idroles=rm.idroles LEFT JOIN ".$config_table_prefix."menu m ON rm.idmenu=m.id LEFT JOIN ".$config_table_prefix."menu_moduli mm ON m.idmoduli=mm.id WHERE ru.idusers=" . $iduser . " ORDER BY mm.ordine ASC, mm.titolo ASC";
	}
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getMenuModuliDetails($conn, $idmod) {
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."menu_moduli WHERE id=" . $idmod;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getMenuModuli1Details($conn, $idmod) {
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."menu_categorie WHERE id=" . $idmod;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function moduliInsert($conn, &$id, $idcategorie, $titolo, $testo, $ordine,$id_oggetto, &$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;
	$titoloSql = $objUtility->translateForDb($titolo, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", "50");
  $idcategorieSql = $objUtility->translateForDb($idcategorie, "int", "50");
  
	$strSql = "INSERT INTO ".$config_table_prefix."menu_moduli (idcategorie, titolo, testo, icona_file, ordine)";
	$strSql .= " VALUES (" . $idcategorieSql . ", " . $titoloSql . ", " . $testoSql . ", " . $id_oggetto . ", " . $ordineSql . ")";
  
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	} else {
		$id = mysql_insert_id($conn);
	}
}

// ******************************************************************************************
function moduliUpdate($conn, $id, $titolo, $testo, $ordine,$id_oggetto, &$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;
	$titoloSql = $objUtility->translateForDb($titolo, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", "50");

	$strSql = "UPDATE ".$config_table_prefix."menu_moduli SET ";
	$strSql .= "titolo=" . $titoloSql . ", ";
	$strSql .= "testo=" . $testoSql . ", ";
	if($id_oggetto!="0") $strSql .= "icona_file=" . $id_oggetto . ", ";
	$strSql .= "ordine=" . $ordineSql." ";
	$strSql .= " WHERE id=" . $id;
  
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function moduliDelete($conn, $id, &$strError) {
	global $config_table_prefix;
	$strError = false;
	$query = mysql_query ("SELECT id FROM ".$config_table_prefix."menu WHERE idmoduli=" . $id, $conn);
	while (list($idmenu) = mysql_fetch_row($query)) {
		mysql_query ("DELETE FROM ".$config_table_prefix."roles_menu_nm WHERE idmenu=" . $idmenu, $conn);
		if (mysql_errno() || mysql_error()) {
			$strError .= "Non e' stato possibile aggiornare il database.<br/>";
		}
	}
	mysql_query ("DELETE FROM ".$config_table_prefix."menu WHERE idmoduli=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
	mysql_query ("DELETE FROM ".$config_table_prefix."menu_moduli WHERE id=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function moduli1Insert($conn, &$id, $titolo, $testo, $ordine, &$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;
	$titoloSql = $objUtility->translateForDb($titolo, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", "50");

	$strSql = "INSERT INTO ".$config_table_prefix."menu_categorie (titolo, testo, ordine)";
	$strSql .= " VALUES (" . $titoloSql . ", " . $testoSql . ", " . $ordineSql . ")";
  
  
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= $strSql."Non e' stato possibile aggiornare il database.<br/>";
	} else {
		$id = mysql_insert_id($conn);
	}
}

// ******************************************************************************************
function moduli1Update($conn, $id, $titolo, $testo, $ordine, &$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;
	$titoloSql = $objUtility->translateForDb($titolo, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", "50");

	$strSql = "UPDATE ".$config_table_prefix."menu_categorie SET ";
	$strSql .= "titolo=" . $titoloSql . ", ";
	$strSql .= "testo=" . $testoSql . ", ";
	$strSql .= "ordine=" . $ordineSql;
	$strSql .= " WHERE id=" . $id;

	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function moduli1Delete($conn, $id, &$strError) {
	global $config_table_prefix;
	$strError = false;
	$query = mysql_query ("SELECT id FROM ".$config_table_prefix."menu WHERE idmoduli=" . $id, $conn);
	while (list($idmenu) = mysql_fetch_row($query)) {
		mysql_query ("DELETE FROM ".$config_table_prefix."roles_menu_nm WHERE idmenu=" . $idmenu, $conn);
		if (mysql_errno() || mysql_error()) {
			$strError .= "Non e' stato possibile aggiornare il database.<br/>";
		}
	}
	mysql_query ("DELETE FROM ".$config_table_prefix."menu WHERE idmoduli=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
	mysql_query ("DELETE FROM ".$config_table_prefix."menu_categorie WHERE id=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
// MENU

// ******************************************************************************************
function getMenu($conn, $idmoduli, $iduser, $isfull) {
	global $config_table_prefix;
	if ($isfull) {
		$strSql = "SELECT * FROM ".$config_table_prefix."menu WHERE idmoduli=" . $idmoduli . " ORDER BY ordine ASC, nome ASC";
	} else {
		$strSql = "SELECT DISTINCT m.* FROM ".$config_table_prefix."roles_menu_nm AS rm LEFT JOIN ".$config_table_prefix."roles_users_nm ru ON ru.idroles=rm.idroles LEFT JOIN ".$config_table_prefix."menu m ON rm.idmenu=m.id WHERE ru.idusers=" . $iduser . " AND m.idmoduli=" . $idmoduli . " ORDER BY m.ordine ASC";
	}
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getMenuDetails($conn, $idmen) {
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."menu WHERE id=" . $idmen;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function menuInsert($conn, &$id, $idmoduli, $nome, $path, $tabella, $ordine,$id_oggetto,$desk,&$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;
	$idmoduliSql = $objUtility->translateForDb($idmoduli, "int");
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$pathSql = $objUtility->translateForDb($path, "string");
	$tabSql = $objUtility->translateForDb($tabella, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", "50");

	$strSql = "INSERT INTO ".$config_table_prefix."menu (idmoduli, nome, path, tabella,icona_file,desktop, ordine)";
	$strSql .= " VALUES (" . $idmoduliSql . ", " . $nomeSql . ", " . $pathSql . ", " . $tabSql . ", " . $id_oggetto . ", " . $desk . ", " . $ordineSql . ")";
  
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	} else {
		$id = mysql_insert_id($conn);
	}
}

// ******************************************************************************************
function menuUpdate($conn, $idmen, $idmod, $nome, $path, $tabella, $ordine,$id_oggetto,$desk, &$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$pathSql = $objUtility->translateForDb($path, "string");
	$tabSql = $objUtility->translateForDb($tabella, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", "50");

	$strSql = "UPDATE ".$config_table_prefix."menu SET ";
	$strSql .= "idmoduli=" . $idmod . ", ";
	$strSql .= "nome=" . $nomeSql . ", ";
	$strSql .= "path=" . $pathSql . ", ";
	$strSql .= "tabella=" . $tabSql . ", ";
	if($id_oggetto!="0") $strSql .= "icona_file=" . $id_oggetto . ", ";
	$strSql .= "ordine=" . $ordineSql. ", ";
	$strSql .= "desktop=" . $desk;
  $strSql .= " WHERE id=" . $idmen; 

	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function menuDelete($conn, $id, &$strError) {
	global $config_table_prefix;
	$strError = false;
	mysql_query ("DELETE FROM ".$config_table_prefix."roles_menu_nm WHERE idmenu=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
	mysql_query ("DELETE FROM ".$config_table_prefix."menu WHERE id=" . $id, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function getMenuRoles($conn, $idmen) {
	global $config_table_prefix;
	$strSql = "SELECT idroles id FROM ".$config_table_prefix."roles_menu_nm WHERE idmenu=" . $idmen;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function menuRolesDelete($conn, $idmen, &$strError) {
	global $config_table_prefix;
	$strSql = "DELETE FROM ".$config_table_prefix."roles_menu_nm WHERE idmenu=" . $idmen;
	mysql_query ($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function menuRolesIns($conn, $idmen, $idroles, &$strError) {
	global $config_table_prefix;
	$strSql = "INSERT INTO ".$config_table_prefix."roles_menu_nm (idmenu, idroles) VALUES (" . $idmen . ", " . $idroles . ")";
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

}
?>
<?php //#rs-enc-module123;# ?>