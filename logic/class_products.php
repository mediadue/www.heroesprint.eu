<?php

Class Products {

// ******************************************************************************************
// CATEGORIE

// ******************************************************************************************
function getCategorieList($conn, $intIdcatpadre, $isfull=true) 
{
	global $config_table_prefix;
	$sqlWhere = "";
	if ($intIdcat) $sqlWhere .= "id=" . $intIdcat . " AND ";
	if (!$isfull) $sqlWhere .= "((ishidden<>1) OR (ishidden IS NULL)) AND ";
	if ($intIdcatpadre) {
		$sqlWhere .= "(idpadre=" . $intIdcatpadre . ") AND ";
	} else {
		$sqlWhere .= "((idpadre IS NULL) OR (idpadre=0)) AND ";
	}
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_categorie" . $sqlWhere . " ORDER BY ordine ASC, nome ASC";

	$query = mysql_query ($sql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function getCategorieDetails($conn, $lngId)
{
	global $config_table_prefix;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_categorie WHERE id=" . $lngId;
	$query = mysql_query ($sql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function categorieInsert($conn, &$id, $idpadre, $nome, $nomeen, $nomefr, $nomees, $testo, $testoen, $testofr, $testoes, $ordine, $ishidden, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$idpadreSql = $objUtility->translateForDb($idpadre, "int");
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$nomeenSql = $objUtility->translateForDb($nomeen, "string");
	$nomefrSql = $objUtility->translateForDb($nomefr, "string");
	$nomeesSql = $objUtility->translateForDb($nomees, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", "50");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "INSERT INTO ".$config_table_prefix."prodotti_categorie (idpadre, nome, nomeen, nomefr, nomees, testo, testoen, testofr, testoes, ordine, ishidden)";
	$sql .= " VALUES (".$idpadreSql.",".$nomeSql.",".$nomeenSql.",".$nomefrSql.",".$nomeesSql.",".$testoSql.",".$testoenSql.",".$testofrSql.",".$testoesSql.",".$ordineSql.",".$ishiddenSql.")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}
// ******************************************************************************************
function categorieUpdate($conn, $id, $idpadre, $nome, $nomeen, $nomefr, $nomees, $testo, $testoen, $testofr, $testoes, $ordine, $ishidden, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$idpadreSql = $objUtility->translateForDb($idpadre, "int");
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$nomeenSql = $objUtility->translateForDb($nomeen, "string");
	$nomefrSql = $objUtility->translateForDb($nomefr, "string");
	$nomeesSql = $objUtility->translateForDb($nomees, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", "50");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "UPDATE ".$config_table_prefix."prodotti_categorie SET ";
	$sql .= "idpadre=" . $idpadreSql . ", ";
	$sql .= "nome=" . $nomeSql . ", ";
	$sql .= "nomeen=" . $nomeenSql . ", ";
	$sql .= "nomefr=" . $nomefrSql . ", ";
	$sql .= "nomees=" . $nomeesSql . ", ";
	$sql .= "testo=" . $testoSql . ", ";
	$sql .= "testoen=" . $testoenSql . ", ";
	$sql .= "testofr=" . $testofrSql . ", ";
	$sql .= "testoes=" . $testoesSql . ", ";
	$sql .= "ordine=" . $ordineSql . ", ";
	$sql .= "ishidden=" . $ishiddenSql;
	$sql .= " WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function categorieDelete($conn, $id, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$rs = $this->getCategorieList($conn, $id, true);
	if (count($rs)) {
		$sqlWhere .= "";
		while (list($key, $rowTmp) = each($rs)) { 
			$sqlWhere .= "idcategorie=" . $rowTmp["id"] . " OR ";
		}
		$sqlWhere = substr($sqlWhere, 0, strlen($sqlWhere)-4);

		$sql = "DELETE FROM ".$config_table_prefix."prodotti_categorie_nm WHERE " . $sqlWhere;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

		$sql = "DELETE FROM ".$config_table_prefix."prodotti_categorie_extra WHERE " . $sqlWhere;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}

	$sql = "DELETE FROM ".$config_table_prefix."prodotti_categorie WHERE idpadre=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	$sql = "DELETE FROM ".$config_table_prefix."prodotti_categorie WHERE id=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function getCategorieFullname($conn, $idcat)
{
	global $config_table_prefix, $objUtility;
    $nome = "nome".$objUtility->getLn();	
	
	$strText = "";
	while ($idcat) {
		$sql = "SELECT $nome, idpadre FROM ".$config_table_prefix."prodotti_categorie WHERE id=" . $idcat;
		$query = mysql_query ($sql, $conn);
		$rs = $objUtility->buildRecordset($query);
		if (count($rs) > 0)
		{
			list($key, $row) = each($rs);
			$strText .= $row[$nome] . "|";
			$idcat = $row["idpadre"];
		} 
		else 
		{
			$idcat = false;
		}
	}
	return $strText;
}
// ******************************************************************************************
function getCategoriaPadre($conn, $idcat)
{
	global $config_table_prefix, $objUtility;
	while ($idcat) {
		$idpadre = $idcat;
		$sql = "SELECT nome, idpadre FROM ".$config_table_prefix."prodotti_categorie WHERE id=" . $idcat;
		$query = mysql_query ($sql, $conn);
		$rs = $objUtility->buildRecordset($query);
		if (count($rs) > 0)
		{
			list($key, $row) = each($rs);
			$idcat = $row["idpadre"];
		}
	}
	return $idpadre;
}
// ******************************************************************************************
// CATEGORIE IMAGES

/**
******************************************************************************************
* restituisce l'elenco delle immagini associate alla news
* @access public        
* @param $conn
* @param int $idnews
* @return 
*/
function categorieImagesGetList($conn, $idcategorie) 
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_categorie_images WHERE idcategorie=".$idcategorie." ORDER BY importanza DESC, inserimento_data DESC";
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* restituisce il primo thumb
* @access public        
* @param $conn
* @param int $idnews
* @return 
*/
function categorieImagesGetFirst($conn, $idcategorie) 
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_categorie_images WHERE idcategorie=".$idcategorie." ORDER BY importanza DESC, inserimento_data DESC LIMIT 0,1";
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* restituisce le informazioni di una immagine della categoria
* @access public        
* @param $conn
* @param int $id
* @return 
*/
function categorieImagesGetDetails($conn, $id)
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_categorie_images WHERE id=".$id;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* inserisce una nuova immagine della categoria
* @access public        
* @param $conn
* @param int $id: restituisce l'id dell'immagine inserita
* @param string $nome
* @param string $descrizione
* @param string $ishidden
* @param int $importanza
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieImagesInsert($conn, $idcategorie, $idimgthumb, $idimgzoom, $testo, $testoen, $testofr, $testoes, &$errorMsg)
{
	global $config_table_prefix, $objUtility, $objObjects;

	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");
	
	$sql = "INSERT INTO ".$config_table_prefix."prodotti_categorie_images (idcategorie,idimgthumb,idimgzoom,testo,testoen,testofr,testoes,inserimento_data)";
	$sql .= " VALUES (".$idcategorie.",".$idimgthumb.",".$idimgzoom.",".$testoSql.",".$testoenSql.",".$testofrSql.",".$testoesSql.",NOW())";

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
	{
		$id = mysql_insert_id($conn);
		$importanza = $this->categorieImagesGetImportanzaNext($conn, $idcategorie, $errorMsg);
		$this->categorieImagesUpdateImportanza($conn, $id, $importanza, $errorMsg);
	}
}

/**
******************************************************************************************
* cancella l'immagine associata alla categoria (ed anche le didascalie relative)
* @access public        
* @param $conn
* @param int $id
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieImagesDelete($conn, $id, &$errorMsg)
{
	global $config_table_prefix, $objUtility, $objObjects;

	$rs = $this->categorieImagesGetDetails($conn, $id);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
		$objObjects->delete($conn, $row["idimgthumb"], $errorMsg);
		$objObjects->delete($conn, $row["idimgzoom"], $errorMsg);
	}

	$sql = "DELETE FROM ".$config_table_prefix."prodotti_categorie_images WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* restituisce il valore successivo da assegnare al campo importanza
* @access public        
* @param $conn
* @param int $idcategorie
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return int
*/
function categorieImagesGetImportanzaNext($conn, $idcategorie, &$errorMsg)
{
	global $config_table_prefix, $objUtility;
	
	$importanza = 1;
	$sql = "SELECT MAX(importanza) importanza FROM ".$config_table_prefix."prodotti_categorie_images WHERE idcategorie=".$idcategorie;
	$query = mysql_query($sql, $conn);
	if (!mysql_errno($conn) && !mysql_error($conn))
	{
		$rs = $objUtility->buildRecordset($query);
		if (count($rs) > 0) 
		{
			list($key, $row) = each($rs);
			if ($row["importanza"])
				$importanza = $row["importanza"] + 1;
			else
				$importanza = 1;
		}
	}
	return $importanza;
}

/**
******************************************************************************************
* aggiorna il campo importanza dell'immagine categoria
* @access public        
* @param $conn
* @param int $id
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieImagesUpdateImportanza($conn, $id, $importanza, &$errorMsg)
{
	global $config_table_prefix, $objUtility;
	$importanzaSql = $objUtility->translateForDb($importanza, "int");
		
	$sql = "UPDATE ".$config_table_prefix."prodotti_categorie_images SET importanza=".$importanzaSql." WHERE id=".$id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* scambia i valori del campo importanza tra le immagini con id id_source e id_dest
* @access public        
* @param $conn
* @param int $id_source: id di una delle news
* @param int $id_dest: id dell'altra news
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return void
*/
function categorieImagesSwapImportanza($conn, $id_source, $id_dest, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$sql = "SELECT importanza FROM ".$config_table_prefix."prodotti_categorie_images WHERE id=".$id_source;
	$query = mysql_query($sql, $conn);
	list($importanza_source) = mysql_fetch_row($query);

	$sql = "SELECT importanza FROM ".$config_table_prefix."prodotti_categorie_images WHERE id=".$id_dest;
	$query = mysql_query($sql, $conn);
	list($importanza_dest) = mysql_fetch_row($query);

    $this->categorieImagesUpdateImportanza($conn, $id_source, $importanza_dest, $errorMsg);
    $this->categorieImagesUpdateImportanza($conn, $id_dest, $importanza_source, $errorMsg);
}

/**
******************************************************************************************
* aggiorna il testo in italiano della didascalia
* @access public        
* @param $conn
* @param int $id
* @param string $testo
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieImagesUpdateText($conn, $id, $testo, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$testoSql = $objUtility->translateForDb($testo, "string");
	$sql = "UPDATE ".$config_table_prefix."prodotti_categorie_images SET testo=".$testoSql." WHERE id=".$id;

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* aggiorna il testo in italiano della didascalia
* @access public        
* @param $conn
* @param int $id
* @param string $testo
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieImagesUpdateTexten($conn, $id, $testo, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$testoSql = $objUtility->translateForDb($testo, "string");
	$sql = "UPDATE ".$config_table_prefix."prodotti_categorie_images SET testoen=".$testoSql." WHERE id=".$id;

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* aggiorna il testo in francese della didascalia
* @access public        
* @param $conn
* @param int $id
* @param string $testo
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieImagesUpdateTextfr($conn, $id, $testo, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$testoSql = $objUtility->translateForDb($testo, "string");
	$sql = "UPDATE ".$config_table_prefix."prodotti_categorie_images SET testofr=".$testoSql." WHERE id=".$id;

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* aggiorna il testo in spagnolo della didascalia
* @access public        
* @param $conn
* @param int $id
* @param string $testo
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieImagesUpdateTextes($conn, $id, $testo, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$testoSql = $objUtility->translateForDb($testo, "string");
	$sql = "UPDATE ".$config_table_prefix."prodotti_categorie_images SET testoes=".$testoSql." WHERE id=".$id;

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

// ******************************************************************************************
// EXTRA

// ******************************************************************************************
function extraGetRicerca($conn, $testo, $isfull=true)
{
	global $config_table_prefix, $objUtility;
	$sqlWhere = "";
	if ($testo) $sqlWhere .= "x.testo LIKE '%" . addslashes($testo) . "%' AND ";
	if (!$isfull) $sqlWhere .= "((x.ishidden=0) OR (x.ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT x.*, n.testo AS notetesto FROM ".$config_table_prefix."prodotti_extra x LEFT JOIN ".$config_table_prefix."prodotti_extra_note n ON x.idnote=n.id" . $sqlWhere . " ORDER BY x.testo ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function extraGetDetails($conn, $id)
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_extra WHERE id=" . $id;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function extraGetByCategorie($conn, $idcategorie, $isfull=true) 
{
	global $config_table_prefix, $objUtility;
	$sqlWhere = "";
	if ($idcategorie) 
		$sqlWhere .= "(xc.idcategorie=" . $idcategorie . ") AND ";
	else
		$sqlWhere .= "(xc.idcategorie=0) AND ";
	if (!$isfull) $sqlWhere .= "((x.ishidden=0) OR (x.ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT DISTINCT x.*, xcnm.prezzo FROM ".$config_table_prefix."prodotti_extra x INNER JOIN ".$config_table_prefix."prodotti_categorie_extra_nm xcnm ON xcnm.idextra=x.id LEFT JOIN ".$config_table_prefix."prodotti_categorie_extra xc ON xc.idcategorie=xcnm.idcategorie " . $sqlWhere . " ORDER BY xcnm.ordine ASC, xcnm.prezzo ASC, x.testo ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function extraInsert($conn, &$id, $idnote, $testo, $testoen, $testofr, $testoes, $ishidden, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$idnoteSql = $objUtility->translateForDb($idnote, "int");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "INSERT INTO ".$config_table_prefix."prodotti_extra (idnote, testo, testoen, testofr, testoes, ishidden)";
	$sql .= " VALUES (" . $idnoteSql . ", " . $testoSql . ", " . $testoenSql . ", " . $testofrSql . ", " . $testoesSql . ", " . $ishiddenSql . ")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}
// ******************************************************************************************
function extraUpdate($conn, $id, $idnote, $testo, $testoen, $testofr, $testoes, $ishidden, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$idnoteSql = $objUtility->translateForDb($idnote, "int");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "UPDATE ".$config_table_prefix."prodotti_extra SET ";
	$sql .= "idnote=" . $idnoteSql . ", ";
	$sql .= "testo=" . $testoSql . ", ";
	$sql .= "testoen=" . $testoenSql . ", ";
	$sql .= "testofr=" . $testofrSql . ", ";
	$sql .= "testoes=" . $testoesSql . ", ";
	$sql .= "ishidden=" . $ishiddenSql;
	$sql .= " WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function extraDelete($conn, $id, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	mysql_query ("DELETE FROM ".$config_table_prefix."prodotti_categorie_extra_nm WHERE idextra=" . $id, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	mysql_query ("DELETE FROM ".$config_table_prefix."prodotti_extra WHERE id=" . $id, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function extraGetDescription($conn, $idcategorie)
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_categorie_extra WHERE idcategorie=" . $idcategorie;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function extraGetValues($conn, $idcategorie, $idextra, $id, $isfull=true)
{
	global $config_table_prefix, $objUtility;
	$sqlWhere = "";
	if ($idcategorie) $sqlWhere .= "(ev.idcategorie=" . $idcategorie . ") AND ";
	if ($idextra) $sqlWhere .= "(ev.idextra=" . $idextra . ") AND ";
	if ($id) $sqlWhere .= "(ev.id=" . $id . ") AND ";
	if (!$isfull) $sqlWhere .= "((e.ishidden=0) OR (e.ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT ev.*, e.testo, e.testoen FROM ".$config_table_prefix."prodotti_categorie_extra_nm ev INNER JOIN ".$config_table_prefix."prodotti_extra e ON ev.idextra=e.id" . $sqlWhere . " ORDER BY e.testo ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function extraDescriptionUpdate($conn, $idcategorie, $testo, $testoen, $testofr, $testoes, &$errorMsg)
{
	global $config_table_prefix, $objUtility;
	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");

	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_categorie_extra WHERE idcategorie=".$idcategorie;
	$query = mysql_query($sql, $conn);
	$exists = true;
	if (mysql_num_rows($query) <= 0)
	{
		$sql = "INSERT INTO ".$config_table_prefix."prodotti_categorie_extra (idcategorie, testo, testoen, testofr, testoes)";
		$sql .= " VALUES (" . $idcategorie . ", " . $testoSql . ", " . $testoenSql . ", " . $testofrSql . ", " . $testoesSql . ")";
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
	else
	{
		$sql = "UPDATE ".$config_table_prefix."prodotti_categorie_extra SET testo=".$testoSql . ", testoen=".$testoenSql . ", testofr=".$testofrSql . ", testoes=".$testoesSql . " WHERE idcategorie=" . $idcategorie;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}
// ******************************************************************************************
function extraValueUpdate($conn, $idcategorie, $idextra, $checked, $prezzo, $ordine, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$prezzoSql = $objUtility->translateForDb($prezzo, "decimal", false, false);
	$ordineSql = $objUtility->translateForDb($ordine, "int");
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_categorie_extra_nm WHERE idcategorie=".$idcategorie . " AND idextra=".$idextra;
	$query = mysql_query($sql, $conn);
	$exists = true;
	if (mysql_num_rows($query) <= 0)
		$exists = false;

	if ($checked)
	{
		if (!$exists)
		{
			$sql = "INSERT INTO ".$config_table_prefix."prodotti_categorie_extra_nm (idcategorie, idextra, prezzo, ordine)";
			$sql .= " VALUES (" . $idcategorie . ", " . $idextra . ", " . $prezzoSql . ", " . $ordineSql . ")";
			mysql_query($sql, $conn);
			$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
		} 
		else 
		{
			$sql = "UPDATE ".$config_table_prefix."prodotti_categorie_extra_nm SET prezzo=".$prezzoSql . ", ordine=".$ordineSql . " WHERE idcategorie=".$idcategorie . " AND idextra=".$idextra;
			mysql_query($sql, $conn);
			$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
		}
	}
	else 
	{
		if ($exists)
		{
			$sql = "DELETE FROM ".$config_table_prefix."prodotti_categorie_extra_nm WHERE idcategorie=".$idcategorie . " AND idextra=".$idextra;
			mysql_query($sql, $conn);
			$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
		}

	}
}
// ******************************************************************************************
function extraCategorieUpdatePrezzi($conn, $idcategorie, $perc, &$errorMsg) 
{
	global $config_table_prefix, $objUtility;

	$perc = $objUtility->translateForDb($perc, "decimal");
	if ($perc && (strtoupper($perc) != "NULL"))
	{
		$percvalue = (100+$perc) / 100;
		$sql = "UPDATE ".$config_table_prefix."prodotti_categorie_extra_nm SET prezzo".$i."=ROUND((prezzo*" . $percvalue . "), 2) WHERE idcategorie=" . $idcategorie;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}
// ******************************************************************************************
function extranoteGetList($conn, $testo, $isfull=true)
{
	global $config_table_prefix, $objUtility;
	$sqlWhere = "";
	if ($testo) $sqlWhere .= "testo LIKE '%" . addslashes($testo) . "%' AND ";
	if (!$isfull) $sqlWhere .= "((ishidden=0) OR (ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_extra_note" . $sqlWhere . " ORDER BY testo ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function extranoteGetDetails($conn, $id)
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_extra_note WHERE id=".$id;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function extranoteGetByCategorie($conn, $idcategorie, $isfull=true) 
{
	global $config_table_prefix, $objUtility;
	$sqlWhere = "";
	if ($idcategorie) 
		$sqlWhere .= "(xc.idcategorie=" . $idcategorie . ") AND ";
	else
		$sqlWhere .= "(xc.idcategorie=0) AND ";
	if (!$isfull) $sqlWhere .= "((x.ishidden=0) OR (x.ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT DISTINCT n.* FROM ".$config_table_prefix."prodotti_extra_note n ";
	$sql .= "INNER JOIN ".$config_table_prefix."prodotti_extra x ON x.idnote=n.id ";
	$sql .= "INNER JOIN ".$config_table_prefix."prodotti_categorie_extra_nm xcnm ON xcnm.idextra=x.id ";
	$sql .= "LEFT JOIN ".$config_table_prefix."prodotti_categorie_extra xc ON xc.idcategorie=xcnm.idcategorie ";
	$sql .= $sqlWhere . " ORDER BY xcnm.prezzo ASC, x.testo ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function extranoteInsert($conn, &$id, $testo, $testoen, $testofr, $testoes, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");

	$sql = "INSERT INTO ".$config_table_prefix."prodotti_extra_note (testo, testoen, testofr, testoes)";
	$sql .= " VALUES (" . $testoSql . ", " . $testoenSql . ", " . $testofrSql . ", " . $testoesSql . ")";

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}
// ******************************************************************************************
function extranoteUpdate($conn, $id, $testo, $testoen, $testofr, $testoes, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");

	$sql = "UPDATE ".$config_table_prefix."prodotti_extra_note SET ";
	$sql .= "testo=" . $testoSql . ", ";
	$sql .= "testoen=" . $testoenSql . ", ";
	$sql .= "testofr=" . $testofrSql . ", ";
	$sql .= "testoes=" . $testoesSql;
	$sql .= " WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function extranoteDelete($conn, $id, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$sql = "UPDATE ".$config_table_prefix."prodotti_extra SET idnote=NULL WHERE idnote=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	$sql = "DELETE FROM ".$config_table_prefix."prodotti_extra_note WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
// ESSENZE

// ******************************************************************************************
function getEssenzeRicerca($conn, $nome, $isfull=true) 
{
	global $config_table_prefix, $objUtility;

	$sqlWhere = "";
	if ($nome) $sqlWhere .= "nome LIKE '%" . addslashes($nome) . "%' AND ";
	if (!$isfull) $sqlWhere .= "((ishidden=0) OR (ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_essenze" . $sqlWhere . " ORDER BY nome ASC";

	$query = mysql_query($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function getEssenzeByProdotti($conn, $idprodotti, $isfull=true) 
{
	global $config_table_prefix, $objUtility;
	$sqlWhere = "";
	if ($idprodotti) $sqlWhere .= "(ev.idprodotti=" . $idprodotti . ") AND ";
	if (!$isfull) $sqlWhere .= "((e.ishidden=0) OR (e.ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT ev.*, e.nome, e.nomeen, e.nomefr, e.nomees FROM ".$config_table_prefix."prodotti_essenze_values ev INNER JOIN ".$config_table_prefix."prodotti_essenze e ON ev.idessenze=e.id" . $sqlWhere . " ORDER BY ev.id ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function getEssenzeDetails($conn, $lngId) 
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_essenze WHERE id=" . $lngId;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function getEssenzeValues($conn, $idprodotti, $idessenze, $id, $isfull=true)
{
	global $config_table_prefix, $objUtility;

	$sqlWhere = "";
	if ($idprodotti) $sqlWhere .= "(ev.idprodotti=" . $idprodotti . ") AND ";
	if ($idessenze) $sqlWhere .= "(ev.idessenze=" . $idessenze . ") AND ";
	if ($id) $sqlWhere .= "(ev.id=" . $id . ") AND ";
	if (!$isfull) $sqlWhere .= "((e.ishidden=0) OR (e.ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT ev.*, e.nome, e.nomeen, e.nomefr, e.nomees FROM ".$config_table_prefix."prodotti_essenze_values ev INNER JOIN ".$config_table_prefix."prodotti_essenze e ON ev.idessenze=e.id" . $sqlWhere . " ORDER BY e.nome ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function essenzeInsert($conn, &$id, $nome, $nomeen, $nomefr, $nomees, $ishidden, &$errorMsg) 
{
	global $config_table_prefix, $objUtility;

	$nomeSql = $objUtility->translateForDb($nome, "string");
	$nomeenSql = $objUtility->translateForDb($nomeen, "string");
	$nomefrSql = $objUtility->translateForDb($nomefr, "string");
	$nomeesSql = $objUtility->translateForDb($nomees, "string");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "INSERT INTO ".$config_table_prefix."prodotti_essenze(nome,nomeen,nomefr,nomees,ishidden)";
	$sql .= " VALUES (" . $nomeSql . ", " . $nomeenSql . ", " . $nomefrSql . ", " . $nomeesSql . ", " . $ishiddenSql . ")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}
// ******************************************************************************************
function essenzeUpdate($conn, $id, $nome, $nomeen, $nomefr, $nomees, $ishidden, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$nomeSql = $objUtility->translateForDb($nome, "string");
	$nomeenSql = $objUtility->translateForDb($nomeen, "string");
	$nomefrSql = $objUtility->translateForDb($nomefr, "string");
	$nomeesSql = $objUtility->translateForDb($nomees, "string");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "UPDATE ".$config_table_prefix."prodotti_essenze SET ";
	$sql .= "nome=" . $nomeSql . ", ";
	$sql .= "nomeen=" . $nomeenSql . ", ";
	$sql .= "nomefr=" . $nomefrSql . ", ";
	$sql .= "nomees=" . $nomeesSql . ", ";
	$sql .= "ishidden=" . $ishiddenSql;
	$sql .= " WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function essenzeDelete($conn, $id, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$sql = "DELETE FROM ".$config_table_prefix."prodotti_essenze_values WHERE idessenze=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	$sql = "DELETE FROM ".$config_table_prefix."prodotti_essenze WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function essenzeDescriptionUpdate($conn, $idprodotti, $descrizione, $descrizioneen, $descrizionefr, $descrizionees, $desc1, $desc2, $desc3, $desc4, $desc5, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$descrizioneSql = $objUtility->translateForDb($descrizione, "string");
	$descrizioneenSql = $objUtility->translateForDb($descrizioneen, "string");
	$descrizionefrSql = $objUtility->translateForDb($descrizionefr, "string");
	$descrizioneesSql = $objUtility->translateForDb($descrizionees, "string");
	$desc1Sql = $objUtility->translateForDb($desc1, "string");
	$desc2Sql = $objUtility->translateForDb($desc2, "string");
	$desc3Sql = $objUtility->translateForDb($desc3, "string");
	$desc4Sql = $objUtility->translateForDb($desc4, "string");
	$desc5Sql = $objUtility->translateForDb($desc5, "string");

	$sql = "UPDATE ".$config_table_prefix."prodotti SET essenzedescrizione=".$descrizioneSql . ", essenzedescrizioneen=".$descrizioneenSql . ", essenzedescrizionefr=".$descrizionefrSql . ", essenzedescrizionees=".$descrizioneesSql . ", essenzedesc1=".$desc1Sql . ", essenzedesc2=".$desc2Sql . ", essenzedesc3=".$desc3Sql . ", essenzedesc4=".$desc4Sql . ", essenzedesc5=".$desc5Sql . " WHERE id=" . $idprodotti;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function essenzeValueUpdate($conn, $idprodotti, $idessenze, $checked, $prezzo1, $prezzo2, $prezzo3, $prezzo4, $prezzo5, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$prezzo1Sql = $objUtility->translateForDb($prezzo1, "decimal");
	$prezzo2Sql = $objUtility->translateForDb($prezzo2, "decimal");
	$prezzo3Sql = $objUtility->translateForDb($prezzo3, "decimal");
	$prezzo4Sql = $objUtility->translateForDb($prezzo4, "decimal");
	$prezzo5Sql = $objUtility->translateForDb($prezzo5, "decimal");
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_essenze_values WHERE idprodotti=".$idprodotti . " AND idessenze=".$idessenze;
	$query = mysql_query($sql, $conn);
	$exists = true;
	if (mysql_num_rows($query) <= 0)
		$exists = false;

	if ($checked)
	{
		if (!$exists)
		{
			$sql = "INSERT INTO ".$config_table_prefix."prodotti_essenze_values (idprodotti, idessenze, prezzo1, prezzo2, prezzo3, prezzo4, prezzo5)";
			$sql .= " VALUES (" . $idprodotti . ", " . $idessenze . ", " . $prezzo1Sql . ", " . $prezzo2Sql . ", " . $prezzo3Sql . ", " . $prezzo4Sql . ", " . $prezzo5Sql . ")";
			mysql_query($sql, $conn);
			$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
		}
		else 
		{
			$sql = "UPDATE ".$config_table_prefix."prodotti_essenze_values SET prezzo1=".$prezzo1Sql . ", prezzo2=".$prezzo2Sql . ", prezzo3=".$prezzo3Sql . ", prezzo4=".$prezzo4Sql . ", prezzo5=".$prezzo5Sql . " WHERE idprodotti=".$idprodotti . " AND idessenze=".$idessenze;
			mysql_query($sql, $conn);
			$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
		}
	}
	else 
	{
		if ($exists)
		{
			$sql = "DELETE FROM ".$config_table_prefix."prodotti_essenze_values WHERE idprodotti=".$idprodotti . " AND idessenze=".$idessenze;
			mysql_query($sql, $conn);
			if (mysql_errno() || mysql_error())
				$errorMsg = true;
		}

	}
}
// ******************************************************************************************
// PRODOTTI

// ******************************************************************************************
function getList($conn, $lngIdcat, $isfullcat, $isfullprod) 
{
	global $config_table_prefix, $objUtility;

	$sqlWhere = "";
	if ($lngIdcat) {
		$sql = "SELECT id FROM ".$config_table_prefix."prodotti_categorie WHERE ((id=" . $lngIdcat . ") OR (idpadre=" . $lngIdcat . "))";
		if (!$isfullcat) $sql .= " AND ((ishidden<>1) OR (ishidden IS NULL))";

		$query = mysql_query ($sql, $conn);
		$utility = new Utility;
		$rs = $utility->buildRecordset($query);
		if (count($rs)) {
			$sqlWhere .= "(";
			while (list($key, $rowTmp) = each($rs)) { 
				$sqlWhere .= "idcategorie=" . $rowTmp["id"] . " OR ";
			}
			$sqlWhere = substr($sqlWhere, 0, strlen($sqlWhere)-4) . ") AND ";
		}
	}
	if (!$isfullprod) $sqlWhere .= "((ishidden<>1) OR (ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT DISTINCT p.* FROM ".$config_table_prefix."prodotti_categorie_nm nm LEFT JOIN ".$config_table_prefix."prodotti p ON nm.idprodotti=p.id " . $sqlWhere . " ORDER BY p.ordine ASC, p.codice";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function getSearchBackoffice($conn, $idcat, $idscat, $codice, $testo) 
{
	global $config_table_prefix, $objUtility;

	$sqlWhere = "";
	if ($idscat) $idcat=$idscat;
	if ($idcat) {
		$rs = $this->getList($conn, $idcat, true, true);
		if (count($rs)) {
			$sqlWhere .= "(";
			while (list($key, $rowTmp) = each($rs)) { 
				$sqlWhere .= "p.id=" . $rowTmp["id"] . " OR ";
			}
		} else {
			$sqlWhere .= "(p.id=0 OR ";
		}
		$sqlWhere = substr($sqlWhere, 0, strlen($sqlWhere)-4) . ") AND ";
	}
	if ($codice) $sqlWhere .= "p.codice='" . $codice . "' AND ";
	if ($testo) $sqlWhere .= "p.descrizione LIKE '%" . $testo . "%' AND ";

	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT p.* FROM ".$config_table_prefix."prodotti p " . $sqlWhere . " ORDER BY p.ordine ASC, p.codice";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function getCategorieByProdotti($conn, $idprod) {
	global $config_table_prefix, $objUtility;
	$sql = "SELECT DISTINCT idcategorie id FROM ".$config_table_prefix."prodotti_categorie_nm WHERE idprodotti=" . $idprod;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function productsCategorieDelete($conn, $idprod, &$errorMsg) 
{
	global $config_table_prefix, $objUtility;
	$sql = "DELETE FROM ".$config_table_prefix."prodotti_categorie_nm WHERE idprodotti=" . $idprod;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function productsCategorieIns($conn, $idprod, $idcat, &$errorMsg) 
{
	global $config_table_prefix, $objUtility;
	$sql = "INSERT INTO ".$config_table_prefix."prodotti_categorie_nm (idprodotti, idcategorie) VALUES (" . $idprod . ", " . $idcat . ")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function insert($conn, &$id, $codice, $prezzo, $descrizione, $descrizioneen, $descrizionefr, $descrizionees, $note, $ordine, $ishidden, &$errorMsg) 
{
	global $config_table_prefix, $objUtility, $objObjects;

	$codiceSql = $objUtility->translateForDb($codice, "string");
	$prezzoSql = $objUtility->translateForDb($prezzo, "decimal");
	$descrizioneSql = $objUtility->translateForDb($descrizione, "string");
	$descrizioneenSql = $objUtility->translateForDb($descrizioneen, "string");
	$descrizionefrSql = $objUtility->translateForDb($descrizionefr, "string");
	$descrizioneesSql = $objUtility->translateForDb($descrizionees, "string");
	$noteSql = $objUtility->translateForDb($note, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", 50);
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "INSERT INTO ".$config_table_prefix."prodotti (codice, prezzo, descrizione, descrizioneen, descrizionefr, descrizionees, note, ordine, ishidden)";
	$sql .= " VALUES (" . $codiceSql . ", " . $prezzoSql . ", " . $descrizioneSql . ", " . $descrizioneenSql . ", " . $descrizionefrSql . ", " . $descrizioneesSql . ", " . $noteSql . ", " . $ordineSql . ", " . $ishiddenSql . ")";

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}
// ******************************************************************************************
function update($conn, $id, $codice, $prezzo, $descrizione, $descrizioneen, $descrizionefr, $descrizionees, $note, $ordine, $ishidden, $isimgthumb1delete, $isimgzoom1delete, $isimgcatalogodelete, &$errorMsg) 
{
	global $config_table_prefix, $objUtility, $objObjects;

	$codiceSql = $objUtility->translateForDb($codice, "string");
	$prezzoSql = $objUtility->translateForDb($prezzo, "decimal");
	$descrizioneSql = $objUtility->translateForDb($descrizione, "string");
	$descrizioneenSql = $objUtility->translateForDb($descrizioneen, "string");
	$descrizionefrSql = $objUtility->translateForDb($descrizionefr, "string");
	$descrizioneesSql = $objUtility->translateForDb($descrizionees, "string");
	$noteSql = $objUtility->translateForDb($note, "string");
	$ordineSql = $objUtility->translateForDb($ordine, "int", 50);
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "UPDATE ".$config_table_prefix."prodotti SET ";
	$sql .= "codice=" . $codiceSql . ", ";
	$sql .= "prezzo=" . $prezzoSql . ", ";
	$sql .= "descrizione=" . $descrizioneSql . ", ";
	$sql .= "descrizioneen=" . $descrizioneenSql . ", ";
	$sql .= "descrizionefr=" . $descrizionefrSql . ", ";
	$sql .= "descrizionees=" . $descrizioneesSql . ", ";
	$sql .= "note=" . $noteSql . ", ";
	$sql .= "ordine=" . $ordineSql . ", ";
	$sql .= "ishidden=" . $ishiddenSql;
	$sql .= " WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	if ($isimgthumb1delete || $isimgzoom1delete)
	{
		$rs = $this->getDetails($conn, $id);
		if (count($rs) > 0)
		{
			list($key, $row) = each($rs);
			if ($isimgthumb1delete)
				$objObjects->delete($conn, $row["idimgthumb1"], $errorMsg);
			if ($isimgzoom1delete)
				$objObjects->delete($conn, $row["idimgzoom1"], $errorMsg);
			if ($isimgcatalogodelete)
				$objObjects->delete($conn, $row["idimgcatalogo"], $errorMsg);
		}
	}
}
// ******************************************************************************************
function updateNote($conn, $id, $note, &$errorMsg) 
{
	global $config_table_prefix, $objUtility;

	$noteSql = $objUtility->translateForDb($note, "string");

	$sql = "UPDATE ".$config_table_prefix."prodotti SET ";
	$sql .= "note=" . $noteSql;
	$sql .= " WHERE id=" . $id;

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function updatePrezzo($conn, $id, $prezzo, &$errorMsg) 
{
	global $config_table_prefix, $objUtility;

	$prezzoSql = $objUtility->translateForDb($prezzo, "decimal");

	$sql = "UPDATE ".$config_table_prefix."prodotti SET ";
	$sql .= "prezzo=" . $prezzoSql;
	$sql .= " WHERE id=" . $id;

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function delete($conn, $id, &$errorMsg)
{
	global $config_table_prefix, $objUtility, $objObjects;

	$rs = $this->getDetails($conn, $id);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);

#		$objObjects->delete($conn, "idimgthumb1", $errorMsg);
#		$objObjects->delete($conn, "idimgzoom1", $errorMsg);
		
		$objObjects->delete($conn, $row["idimgthumb1"], $errorMsg);
		$objObjects->delete($conn, $row["idimgzoom1"], $errorMsg);
		$objObjects->delete($conn, $row["idimgcatalogo"], $errorMsg);
		
	}
	$errorMsg = false;
	$sql = "DELETE FROM ".$config_table_prefix."prodotti_categorie_nm WHERE idprodotti=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	$sql = "DELETE FROM ".$config_table_prefix."prodotti_essenze_values WHERE idprodotti=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	$sql = "DELETE FROM ".$config_table_prefix."prodotti WHERE id=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function getDetails($conn, $lngId) 
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT p.* FROM ".$config_table_prefix."prodotti p WHERE p.id=" . $lngId;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function imgthumb1Update($conn, $id, $strOggettoPath, $strOggettoExt, $strOggettoOriginalname, &$errorMsg)
{
	global $config_table_prefix, $objUtility, $objObjects;

	$rs = $this->getDetails($conn, $id);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
		$objObjects->delete($conn, $row["idimgthumb1"], $errorMsg);
	}

	$strOggettoPathSql = $objUtility->translateForDb($strOggettoPath, "string");
	$strOggettoExtSql = $objUtility->translateForDb($strOggettoExt, "string");
	$strOggettoOriginalnameSql = $objUtility->translateForDb($strOggettoOriginalname, "string");

	$sql = "INSERT INTO ".$config_table_prefix."oggetti (nome, ext, path, originalname)";
	$sql .= " VALUES (" . $strOggettoOriginalnameSql . ", " . $strOggettoExtSql . ", " . $strOggettoPathSql . ", " . $strOggettoOriginalnameSql . ")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
	{
		$idOggetto = mysql_insert_id($conn);
		$sql = "UPDATE ".$config_table_prefix."prodotti SET idimgthumb1=" . $idOggetto . " WHERE id=" . $id;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}
// ******************************************************************************************
function imgzoom1Update($conn, $id, $strOggettoPath, $strOggettoExt, $strOggettoOriginalname, &$errorMsg)
{
	global $config_table_prefix, $objUtility;

	$strOggettoPathSql = $objUtility->translateForDb($strOggettoPath, "string");
	$strOggettoExtSql = $objUtility->translateForDb($strOggettoExt, "string");
	$strOggettoOriginalnameSql = $objUtility->translateForDb($strOggettoOriginalname, "string");

	$sql = "INSERT INTO ".$config_table_prefix."oggetti (nome, ext, path, originalname)";
	$sql .= " VALUES (" . $strOggettoOriginalnameSql . ", " . $strOggettoExtSql . ", " . $strOggettoPathSql . ", " . $strOggettoOriginalnameSql . ")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
	{
		$idOggetto = mysql_insert_id($conn);
		$sql = "UPDATE ".$config_table_prefix."prodotti SET idimgzoom1=" . $idOggetto . " WHERE id=" . $id;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}
// ******************************************************************************************
function imgcatalogoUpdate($conn, $idprod, $path, $ext, $originalname, &$errorMsg)
{
	global $config_table_prefix, $objUtility, $objObjects;

	//cancello l'eventuale immagine precedentemente associata
	$rs = $this->getDetails($conn, $idprod);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
		$objObjects->delete($conn, $row["idimgcatalogo"], $errorMsg);
	}

	//inserisco l'oggetto
	$idobjects = "";
	$objObjects->insert($conn, $idobjects, $originalname, $ext, $path, $originalname, false, $errorMsg);
	if (!$errorMsg)
	{
		//associo il prodotto con l'oggetto appena inserito
		$sql = "UPDATE ".$config_table_prefix."prodotti SET idimgcatalogo=".$idobjects . " WHERE id=".$idprod;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}
// ******************************************************************************************
// CATALOGO

/**
******************************************************************************************
* aggiunge il pdf tra gli oggetti ed aggiorna l'history
* @access public        
* @param $conn
* @param int $idoggetti: restituisce l'id inserito sulla tabella oggetti
* @param int $idcategorie
* @param string $lingua
* @param string $filepdf: nome del file pdf creato sul filesystem
* @param int $idusers: id dell'utente che ha creato il file
* @param string $username: username dell'utente che ha creato il file
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return void
*/
function catalogoArchivioInsert($conn, &$idoggetti, $idcategorie, $lingua, $filepdf, $idusers, $username, &$errorMsg)
{
	global $config_table_prefix, $objObjects, $objUtility, $objUsers;
	$name = "nokia_catalogo_".date("Y-m-d").".pdf";
	$objObjects->insert($conn, $idoggetti, $filepdf, "pdf", $filepdf, $name, true, $errorMsg);
	if ($idoggetti)
	{
		$categoria = $this->getCategorieFullname($conn, $idcategorie);

		$idcategorieSql = $objUtility->translateForDb($idcategorie, "int");
		$categoriaSql = $objUtility->translateForDb($categoria, "string", false, false);
		$linguaSql = $objUtility->translateForDb($lingua, "string", false, false);
		$idoggettiSql = $objUtility->translateForDb($idoggetti, "int");
		$idusersSql = $objUtility->translateForDb($idusers, "int");
		$usernameSql = $objUtility->translateForDb($username, "string");
		
		$sql = "INSERT INTO ".$config_table_prefix."prodotti_catalogo_archivio (idcategorie, categoria, lingua, idoggetti, inserimento_idusers, inserimento_username, inserimento_data)";
		$sql .= " VALUES (".$idcategorieSql.",".$categoriaSql.",".$linguaSql.",".$idoggettiSql.",".$idusersSql.",".$usernameSql.",NOW())";
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}

// ******************************************************************************************
function catalogoArchivioGet($conn, $dataFrom, $dataTo)
{
	global $config_table_prefix, $objUtility;
	$sqlWhere = "";
	if ($dataFrom) $sqlWhere .= "(inserimento_data >= '" . $dataFrom . " 00:00:00') AND ";
	if ($dataTo) $sqlWhere .= "(inserimento_data <= '" . $dataTo . " 23:59:99') AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_catalogo_archivio " . $sqlWhere . " ORDER BY inserimento_data DESC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function catalogoArchivioGetDetails($conn, $id)
{
	global $config_table_prefix, $objUtility;
	$sql = "SELECT * FROM ".$config_table_prefix."prodotti_catalogo_archivio WHERE id=" . $id;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function catalogoArchivioDelete($conn, $id, &$errorMsg)
{
	global $config_table_prefix, $objUtility, $objObjects;
	
	$rs = $this->catalogoArchivioGetDetails($conn, $id);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
		$objObjects->delete($conn, $row["idoggetti"], $errorMsg);
		
		if ($row["filepdf"]) 
		{
			$strDir = $objUtility->getPathResourcesDynamicAbsolute();
			$strFile = $row["filepdf"];
			$objUtility->deleteFile ($strDir . $strFile); //cancello il file dal filesystem
		}
	}

	$sql = "DELETE FROM ".$config_table_prefix."prodotti_catalogo_archivio WHERE id=".$id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

}
?>