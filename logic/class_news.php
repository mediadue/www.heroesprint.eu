<?php

Class News {

// ******************************************************************************************
// CATEGORIE

/**
******************************************************************************************
* restituisce l'elenco delle categorie
* @access public        
* @param $conn
* @param bool $isfull: impostato a true restituisce anche le categorie nascoste
* @return 
*/
function categorieGetList($conn, $isfull=true) 
{
	global $config_table_prefix;
	$sqlWhere = "";
	if (!$isfull) $sqlWhere .= "((ishidden<>1) OR (ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT * FROM ".$config_table_prefix."news_categorie" . $sqlWhere . " ORDER BY importanza DESC, nome ASC";

	$query = mysql_query ($sql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* restituisce le informazioni di una categoria
* @access public        
* @param $conn
* @param int $id
* @return 
*/
function categorieGetDetails($conn, $id)
{
	global $config_table_prefix;
	$sql = "SELECT * FROM ".$config_table_prefix."news_categorie WHERE id=".$id;
	$query = mysql_query ($sql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* inserisce una nuova categoria
* @access public        
* @param $conn
* @param int $id: restituisce l'id della categoria inserita
* @param string $nome
* @param string $descrizione
* @param string $ishidden
* @param int $importanza
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieInsert($conn, &$id, $nome, $descrizione, $ishidden, $importanza, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$nomeSql = $objUtility->translateForDb($nome, "string");
	$descrizioneSql = $objUtility->translateForDb($descrizione, "string");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");
	$importanzaSql = $objUtility->translateForDb($importanza, "int", "50");

	$sql = "INSERT INTO ".$config_table_prefix."news_categorie (nome, descrizione, ishidden, importanza)";
	$sql .= " VALUES (".$nomeSql.",".$descrizioneSql.",".$ishiddenSql.",".$importanzaSql.")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}
/**
******************************************************************************************
* aggiorna i dati della categoria
* @access public        
* @param $conn
* @param int $id
* @param string $nome
* @param string $descrizione
* @param string $ishidden
* @param int $importanza
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieUpdate($conn, $id, $nome, $descrizione, $ishidden, $importanza, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$nomeSql = $objUtility->translateForDb($nome, "string");
	$descrizioneSql = $objUtility->translateForDb($descrizione, "string");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");
	$importanzaSql = $objUtility->translateForDb($importanza, "int", "50");

	$sql = "UPDATE ".$config_table_prefix."news_categorie SET ";
	$sql .= "nome=" . $nomeSql . ",";
	$sql .= "descrizione=" . $descrizioneSql . ",";
	$sql .= "ishidden=" . $ishiddenSql . ",";
	$sql .= "importanza=" . $importanzaSql;
	$sql .= " WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* cancella la categoria e le news associate
* @access public        
* @param $conn
* @param int $id
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function categorieDelete($conn, $id, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$rs = $this->getList($conn, $id, true);
	if (count($rs)) 
	{
		$sqlWhere .= "";
		while (list($key, $rowTmp) = each($rs)) 
			$this->delete($conn, $row["id"], $errorMsg);
	}

	$sql = "DELETE FROM ".$config_table_prefix."news_categorie WHERE id=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

// ******************************************************************************************
// NEWS

/**
******************************************************************************************
* restituisce l'elenco delle news
* @access public        
* @param $conn
* @param int $idcat
* @param bool $isfull: impostato a true restituisce anche le news nascoste e scadute
* @return array
*/
function getList($conn, $idcat, $isfull=true) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sqlWhere = "";
	if (!empty($idcat)) $sqlWhere .= "n.idcategorie=" . $idcat . " AND ";
	if (!$isfull) $sqlWhere .= "((n.ishidden<>1) OR (n.ishidden IS NULL)) AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT n.* FROM ".$config_table_prefix."news n " . $sqlWhere . " ORDER BY n.importanza DESC, inserimento_data DESC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* restituisce l'elenco delle sole news pubblicabili
* @access public        
* @param $conn
* @param int $idcat
* @return array
*/
function getListPublished($conn, $idcat) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sqlWhere = "WHERE ((n.ishidden<>1) OR (n.ishidden IS NULL)) AND ((datapubblicazione IS NULL) OR (datapubblicazione<=NOW())) AND ((datascadenza IS NULL) OR (datascadenza>=NOW()))";
	if (!empty($idcat)) $sqlWhere .= " AND n.idcategorie=".$idcat;
	$sql = "SELECT n.* FROM ".$config_table_prefix."news n " . $sqlWhere . " ORDER BY n.importanza DESC, inserimento_data DESC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* restituisce l'elenco delle news, filtrando in base a diversi parametri
* @access public        
* @param $conn
* @param bool $idcat
* @param bool $isfull: impostato a true restituisce anche le news nascoste e scadute
* @return 
*/
function getCategorieByNews($conn, $idnews)
{
	global $config_table_prefix;
	$sql = "SELECT DISTINCT idcategorie id FROM ".$config_table_prefix."news WHERE id=" . $idnews;
	
	$query = mysql_query ($sql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* inserisce una news
* @access public        
* @param $conn
* @return 
*/
function insert($conn, &$id, $idcategorie, $titolo, $titoloen, $titolofr, $titoloes, $abstract, $abstracten, $abstractfr, $abstractes, $testo, $testoen, $testofr, $testoes, $link, $datapubblicazione, $datascadenza, $ishidden, $importanza, $idusers, $username, &$errorMsg) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$idcategorieSql = $objUtility->translateForDb($idcategorie, "int");
	$titoloSql = $objUtility->translateForDb($titolo, "string");
	$titoloenSql = $objUtility->translateForDb($titoloen, "string");
	$titolofrSql = $objUtility->translateForDb($titolofr, "string");
	$titoloesSql = $objUtility->translateForDb($titoloes, "string");
	$abstractSql = $objUtility->translateForDb($abstract, "string");
	$abstractenSql = $objUtility->translateForDb($abstracten, "string");
	$abstractfrSql = $objUtility->translateForDb($abstractfr, "string");
	$abstractesSql = $objUtility->translateForDb($abstractes, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");
	$linkSql = $objUtility->translateForDb($link, "string");
	$datapubblicazioneSql = $objUtility->translateForDb($datapubblicazione, "date");
	$datascadenzaSql = $objUtility->translateForDb($datascadenza, "date");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");
	$importanzaSql = $this->getImportanzaNext($conn, $idcategorie, &$errorMsg);
	$idusersSql = $objUtility->translateForDb($idusers, "int");
	$usernameSql = $objUtility->translateForDb($username, "string");
	$sql = "INSERT INTO ".$config_table_prefix."news (idcategorie, titolo, titoloen, titolofr, titoloes, abstract, abstracten, abstractfr, abstractes, testo, testoen, testofr, testoes, link, datapubblicazione, datascadenza, ishidden, importanza, inserimento_idusers, inserimento_username, inserimento_data)";
	$sql .= " VALUES (".$idcategorieSql.",".$titoloSql.",".$titoloenSql.",".$titolofrSql.",".$titoloesSql.",".$abstractSql.",".$abstractenSql.",".$abstractfrSql.",".$abstractesSql.",".$testoSql.",".$testoenSql.",".$testofrSql.",".$testoesSql.",".$linkSql.",".$datapubblicazioneSql.",".$datascadenzaSql.",".$ishiddenSql.",".$importanzaSql.",".$idusersSql.",".$usernameSql.",NOW())";

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}

/**
******************************************************************************************
* aggiorna i dati della news
* @access public        
* @param $conn
* @return 
*/
function update($conn, $id, $idcategorie, $titolo, $titoloen, $titolofr, $titoloes, $abstract, $abstracten, $abstractfr, $abstractes, $testo, $testoen, $testofr, $testoes, $link, $datapubblicazione, $datascadenza, $ishidden, $importanza, $isimgthumbdelete, $isimgzoomdelete, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;
	$objObjects = new Objects;

	$idcategorieSql = $objUtility->translateForDb($idcategorie, "int");
	$titoloSql = $objUtility->translateForDb($titolo, "string");
	$titoloenSql = $objUtility->translateForDb($titoloen, "string");
	$titolofrSql = $objUtility->translateForDb($titolofr, "string");
	$titoloesSql = $objUtility->translateForDb($titoloes, "string");
	$abstractSql = $objUtility->translateForDb($abstract, "string");
	$abstractenSql = $objUtility->translateForDb($abstracten, "string");
	$abstractfrSql = $objUtility->translateForDb($abstractfr, "string");
	$abstractesSql = $objUtility->translateForDb($abstractes, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$testoenSql = $objUtility->translateForDb($testoen, "string");
	$testofrSql = $objUtility->translateForDb($testofr, "string");
	$testoesSql = $objUtility->translateForDb($testoes, "string");
	$linkSql = $objUtility->translateForDb($link, "string");
	$datapubblicazioneSql = $objUtility->translateForDb($datapubblicazione, "date");
	$datascadenzaSql = $objUtility->translateForDb($datascadenza, "date");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");
	$importanzaSql = $objUtility->translateForDb($importanza, "int");

	$sql = "UPDATE ".$config_table_prefix."news SET ";
	$sql .= "idcategorie=" . $idcategorieSql . ", ";
	$sql .= "titolo=" . $titoloSql . ", ";
	$sql .= "titoloen=" . $titoloenSql . ", ";
	$sql .= "titolofr=" . $titolofrSql . ", ";
	$sql .= "titoloes=" . $titoloesSql . ", ";
	$sql .= "abstract=" . $abstractSql . ", ";
	$sql .= "abstracten=" . $abstractenSql . ", ";
	$sql .= "abstractfr=" . $abstractfrSql . ", ";
	$sql .= "abstractes=" . $abstractesSql . ", ";
	$sql .= "testo=" . $testoSql . ", ";
	$sql .= "testoen=" . $testoenSql . ", ";
	$sql .= "testofr=" . $testofrSql . ", ";
	$sql .= "testoes=" . $testoesSql . ", ";
	$sql .= "link=" . $linkSql . ", ";
	$sql .= "datapubblicazione=" . $datapubblicazioneSql . ", ";
	$sql .= "datascadenza=" . $datascadenzaSql . ", ";
	$sql .= "ishidden=" . $ishiddenSql . ", ";
	$sql .= "importanza=" . $importanzaSql;
	$sql .= " WHERE id=" . $id;

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	if ($isimgthumbdelete)
	{
		$rs = $this->getDetails($conn, $id);
		if (count($rs) > 0)
		{
			list($key, $row) = each($rs);
			$objObjects->delete($conn, $row["idimgthumb"], $errorMsg);
		}
	}
	if ($isimgzoomdelete)
	{
		$rs = $this->getDetails($conn, $id);
		if (count($rs) > 0)
		{
			list($key, $row) = each($rs);
			$objObjects->delete($conn, $row["idimgzoom"], $errorMsg);
		}
	}
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
function getImportanzaNext($conn, $idcategorie, &$errorMsg)
{
	global $config_table_prefix, $objUtility;
	
	$importanza = 1;
	$sql = "SELECT MAX(importanza) importanza FROM ".$config_table_prefix."news WHERE idcategorie=".$idcategorie;
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
* aggiorna il campo importanza della news: se non viene passato il valore da scrivere, lo calcola
* @access public        
* @param $conn
* @param int $id
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function updateImportanza($conn, $idnews, $importanza, &$errorMsg)
{
	global $config_table_prefix, $objUtility;
	
	$idnewsSql = $objUtility->translateForDb($idnews, "int");
	$importanzaSql = $objUtility->translateForDb($importanza, "int");
		
	$sql = "UPDATE ".$config_table_prefix."news SET importanza=".$importanzaSql." WHERE id=".$idnews;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* scambia i valori del campo importanza tra le news con id id_source e id_dest
* @access public        
* @param $conn
* @param int $id_source: id di una delle news
* @param int $id_dest: id dell'altra news
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return void
*/
function swapImportanza($conn, $id_source, $id_dest, &$errorMsg)
{
	global $config_table_prefix, $objUtility;
	
	$sql = "SELECT importanza FROM ".$config_table_prefix."news WHERE id=".$id_source;
	$query = mysql_query($sql, $conn);
	list($importanza_source) = mysql_fetch_row($query);

	$sql = "SELECT importanza FROM ".$config_table_prefix."news WHERE id=".$id_dest;
	$query = mysql_query($sql, $conn);
	list($importanza_dest) = mysql_fetch_row($query);

	$sql = "UPDATE ".$config_table_prefix."news SET importanza=".$importanza_dest." WHERE id=".$id_source;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	$sql = "UPDATE ".$config_table_prefix."news SET importanza=".$importanza_source." WHERE id=".$id_dest;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* cancella la news e le immagini associate
* @access public        
* @param $conn
* @param int $id
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function delete($conn, $id, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;
	
	$objObjects = new Objects;
	
	$rs = $this->getDetails($conn, $id);

	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
#		$objObjects->delete($conn, "idimg", $errorMsg); // ?? idimg ??
		$objObjects->delete($conn, $row["idimgthumb"], $errorMsg);
		$objObjects->delete($conn, $row["idimgzoom"], $errorMsg);
	}
	
	$errorMsg = false;
	
	$sql = "DELETE FROM ".$config_table_prefix."news WHERE id=" . $id;

	mysql_query ($sql, $conn);

	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* restituisce le informazioni di una news
* @access public        
* @param $conn
* @param int $id
* @return 
*/
function getDetails($conn, $id) 
{
	global $config_table_prefix;
	$sql = "SELECT * FROM ".$config_table_prefix."news WHERE id=" . $id;
	$query = mysql_query ($sql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* aggiorna l'immagine thumb della news
* @access public        
* @param $conn
* @param int $id
* @param string $path: il nome del file sul filesystem
* @param string $ext: estensione del file
* @param string $originalName
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function imgThumbUpdate($conn, $id, $objectPath, $objectExt, $objectOriginalName, &$errorMsg)
{
	global $config_table_prefix;
	$objObjects = new Objects;

	$rs = $this->getDetails($conn, $id);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
		$objObjects->delete($conn, $row["idimgthumb"], $errorMsg);
	}
	$objUtility = new Utility;
	$objectPathSql = $objUtility->translateForDb($objectPath, "string");
	$objectExtSql = $objUtility->translateForDb($objectExt, "string");
	$objectOriginalNameSql = $objUtility->translateForDb($objectOriginalName, "string");

	$sql = "INSERT INTO ".$config_table_prefix."oggetti (nome, ext, path, originalname)";
	$sql .= " VALUES (" . $objectOriginalNameSql.",".$objectExtSql.",".$objectPathSql.",".$objectOriginalNameSql . ")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
	{
		$idOggetto = mysql_insert_id($conn);
		$sql = "UPDATE ".$config_table_prefix."news SET idimgthumb=".$idOggetto." WHERE id=".$id;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}

/**
******************************************************************************************
* aggiorna l'immagine zoom della news
* @access public        
* @param $conn
* @param int $id
* @param string $path: il nome del file sul filesystem
* @param string $ext: estensione del file
* @param string $originalName
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function imgZoomUpdate($conn, $id, $objectPath, $objectExt, $objectOriginalName, &$errorMsg)
{
	global $config_table_prefix;
	$objObjects = new Objects;

	$rs = $this->getDetails($conn, $id);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
		$objObjects->delete($conn, $row["idimgzoom"], $errorMsg);
	}
	$objUtility = new Utility;
	$objectPathSql = $objUtility->translateForDb($objectPath, "string");
	$objectExtSql = $objUtility->translateForDb($objectExt, "string");
	$objectOriginalNameSql = $objUtility->translateForDb($objectOriginalName, "string");

	$sql = "INSERT INTO ".$config_table_prefix."oggetti (nome, ext, path, originalname)";
	$sql .= " VALUES (" . $objectOriginalNameSql.",".$objectExtSql.",".$objectPathSql.",".$objectOriginalNameSql . ")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
	{
		$idOggetto = mysql_insert_id($conn);
		$sql = "UPDATE ".$config_table_prefix."news SET idimgzoom=".$idOggetto." WHERE id=".$id;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}

}
?>