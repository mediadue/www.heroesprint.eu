<?php

Class Objects {

// ******************************************************************************************
function getRicerca($conn, $strNome, $strExt, $strFilename) {
	global $config_table_prefix;
	$strSqlWhere = "";
	if ($strNome) $strSqlWhere .= "nome LIKE '%" . addslashes($strNome) . "%' AND ";
	If ($strExt) $strSqlWhere .= "ext='" . addslashes($strExt) . "' AND ";
	If ($strFilename) $strSqlWhere .= "path LIKE '%" . addslashes($strFilename) . "%' AND ";
	If ($strSqlWhere) $strSqlWhere = " WHERE " . substr($strSqlWhere, 0, strlen($strSqlWhere)-5);
	$strSql = "SELECT * FROM ".$config_table_prefix."oggetti" . $strSqlWhere . " ORDER BY nome ASC, ext ASC";
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getDetails($conn, $intId) {
	global $config_table_prefix;
	$strSql = "SELECT * FROM ".$config_table_prefix."oggetti WHERE id=" . $intId;
  $query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getImagePath($conn, $strTable, $strField, $intId) {
	global $config_table_prefix;
	$strSql = "SELECT " . $strField . " AS nome FROM ".$config_table_prefix. $strTable . " WHERE id=" . $intId;
	$query = mysql_query ($strSql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* inserisce una news
* @access public        
* @param $conn
* @param int $id
* @param string $nome
* @param string $ext: estensione del file
* @param string $path: nome del file sul filesystem
* @param string $originalName: nome originale del file uplodato (prima di essere rinominato dal sistema)
* @param string $isprivate: se true, il file è stato salvato in una cartella non pubblica
* @param string $errorMsg
* @return 
*/
function insert($conn, &$id, $nome, $ext, $path, $originalName, $isprivate, &$errorMsg) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$nomeSql = $objUtility->translateForDb($nome, "string");
	$extSql = $objUtility->translateForDb($ext, "string");
	$pathSql = $objUtility->translateForDb($path, "string");
	$originalNameSql = $objUtility->translateForDb($originalName, "string");
	$isprivateSql = $objUtility->translateForDb($isprivate, "int");
	
	$sql = "INSERT INTO ".$config_table_prefix."oggetti (nome, ext, path, originalname, isprivate)";
	$sql .= " VALUES (" . $nomeSql.",".$extSql.",".$pathSql.",".$originalNameSql.",".$isprivateSql.")";

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}

// ******************************************************************************************
function update($conn, $id, $nome, &$strError)
{
	global $config_table_prefix;
	$objUtility = new Utility;
	$nomeSql = $objUtility->translateForDb($nome, "string");

	$strSql = "UPDATE ".$config_table_prefix."oggetti SET nome=" . $nomeSql . " WHERE id=" . $id;
	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function delete($conn, $id, &$errorMsg) {
	global $config_table_prefix;
	$objUtility = new Utility;

	if (!empty($id))
	{
		$rs = getTable("oggetti","","id='$id'");
		if (count($rs) > 0)
			list($key, $row) = each($rs);
		
		//cancello il file dal filesystem
		$fileDir = $objUtility->getPathResourcesDynamicAbsolute();
		if ($row["isprivate"])
			$fileDir = $objUtility->getPathResourcesPrivateAbsolute();
		$filePath = $row["nome"].".".$row["ext"];
		$objUtility->deleteFile($fileDir . $filePath);

		//aggiorno la tabella oggetti
		$sql = "DELETE FROM ".$config_table_prefix."oggetti WHERE id=" . $id;
		

		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}

// ******************************************************************************************
function updateImage($conn, $id, $strOggettoPath, $strOggettoExt, $strOggettoOriginalname, &$strError) {
	global $config_table_prefix;
	$objUtility = new Utility;

	$strOggettoPathSql = $objUtility->translateForDb($strOggettoPath, "string");
	$strOggettoExtSql = $objUtility->translateForDb($strOggettoExt, "string");
	$strOggettoOriginalnameSql = $objUtility->translateForDb($strOggettoOriginalname, "string");

	$strSql = "UPDATE ".$config_table_prefix."oggetti SET ";
	$strSql .= "path=" . $strOggettoPathSql . ", ";
	$strSql .= "ext=" . $strOggettoExtSql . ", ";
	$strSql .= "originalname=" . $strOggettoOriginalnameSql;
	$strSql .= " WHERE id=" . $id;

	mysql_query($strSql, $conn);
	if (mysql_errno() || mysql_error()) {
		$strError .= "Non e' stato possibile aggiornare il database.<br/>";
	}
}

// ******************************************************************************************
function getContentNews($conn, $id) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "SELECT n.* FROM ".$config_table_prefix."news n WHERE n.idimgthumb=".$id . " OR n.idimgzoom=".$id;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

// ******************************************************************************************
function getContentDocuments($conn, $id) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "SELECT d.*, o.nome, o.ext, o.path, o.originalname, u.login, u.nome, u.cognome FROM ".$config_table_prefix."documents d LEFT JOIN ".$config_table_prefix."oggetti o ON o.id=d.idoggetti LEFT JOIN ".$config_table_prefix."users u ON u.id=d.idusers WHERE d.idoggetti=".$id;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

}
?><?php //#rs-enc-module123;# ?>