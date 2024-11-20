<?php
class Documents extends Objects {

// ******************************************************************************************
// TAGS
	
// ******************************************************************************************
function tagsGetRicerca($conn, $nome)
{
	global $config_table_prefix;
	$sqlWhere = "";
	if ($nome) $sqlWhere .= "nome LIKE '%" . addslashes($nome) . "%' AND ";
	If ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT * FROM ".$config_table_prefix."documents_tags " . $sqlWhere . " ORDER BY Ordinamento DESC, nome ASC";

	$query = mysql_query ($sql, $conn);
	$utility = new Utility;
	$rs = $utility->buildRecordset($query);
	return $rs;
}
/**
******************************************************************************************
* restituisce l'elenco dei tag associati ad un documento
* @access public        
* @param $conn
* @param int $iddocuments: 
* @return array
*/
function tagsGetByDocument($conn, $iddocuments)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "SELECT t.* FROM ".$config_table_prefix."documents_tags_nm nm LEFT JOIN ".$config_table_prefix."documents_tags t ON nm.idtags=t.id WHERE nm.iddocuments=".$iddocuments." ORDER BY t.nome ASC";
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
/**
******************************************************************************************
* restituisce l'elenco dei tag associati ad un documento
* @access public        
* @param $conn
* @param int $iddocuments: 
* @return array
*/
function tagsGetByAnno($conn, $anno, $idusers, $isfull=true)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sqlWhere = "";
	if ($anno && $anno>0) $sqlWhere .= "d.anno=" . $anno . " AND ";
	if ($idusers) $sqlWhere .= "d.idusers=" . $idusers . " AND ";
	if (!$isfull) $sqlWhere .= "((d.ishidden<>1) OR (d.ishidden IS NULL)) AND ";
	if ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT DISTINCT t.* FROM ".$config_table_prefix."documents_tags t INNER JOIN ".$config_table_prefix."documents_tags_nm nm ON nm.idtags=t.id INNER JOIN ".$config_table_prefix."documents d ON nm.iddocuments=d.id" . $sqlWhere . " ORDER BY t.Ordinamento DESC, nome ASC";
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	
	$sqlWhereAnno = "";
  if ($anno && $anno>0) $sqlWhereAnno .= "AND YEAR(Data)='$anno'";
  $rs2=getTable("form_archivio_offerte","Data DESC","(idfornitore_hidden='$idusers' $sqlWhereAnno)");
	
	if($rs2) {
    while (list($key, $row) = each($rs2)) {
       $i=count($rs);
       $rs[$i]['nome'] = "Richiesta quotazione del ".dataITA($row['Data']);
       $rs[$i]['id'] = "-1";
       $rs[$i]['richiesta'] = $row['id_archivio_richieste_offerta'];  
  	}
	}
	
  return $rs;
}
// ******************************************************************************************
function tagsGetDetails($conn, $id)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "SELECT * FROM ".$config_table_prefix."documents_tags WHERE id=" . $id;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}
// ******************************************************************************************
function tagsInsert($conn, &$id, $nome, $importanza, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$nomeSql = $objUtility->translateForDb($nome, "string");
	$importanzaSql = $objUtility->translateForDb($importanza, "int", "50");

	$sql = "INSERT INTO ".$config_table_prefix."documents_tags (nome, importanza)";
	$sql .= " VALUES (".$nomeSql.",".$importanzaSql.")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}
// ******************************************************************************************
function tagsUpdate($conn, $id, $nome, $importanza, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;
	$nomeSql = $objUtility->translateForDb($nome, "string");
	$importanzaSql = $objUtility->translateForDb($importanza, "int", "50");

	$sql = "UPDATE ".$config_table_prefix."documents_tags SET ";
	$sql .= "nome=" . $nomeSql . ",";
	$sql .= "Ordinamento=" . $importanzaSql;
	$sql .= " WHERE id=" . $id;
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}
// ******************************************************************************************
function tagsDelete($conn, $id, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "DELETE FROM ".$config_table_prefix."documents_tags_nm WHERE idtags=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);

	$sql = "DELETE FROM ".$config_table_prefix."documents_tags WHERE id=" . $id;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

// ******************************************************************************************
// DOCUMENTS

/**
* ******************************************************************************************
* restituisce tutti gli anni inseriti
* @access public        
* @param $conn
* @param int $idusers
* @param bool $isfull: se false restituisce solo gli anni dei documenti non nascosti
* @return array
*/
function getAnni($conn, $idusers, $isfull=true) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sqlWhere = "";
	if ($idusers) $sqlWhere .= "idusers=" . $idusers . " AND ";
	if (!$isfull) $sqlWhere .= "((ishidden<>1) OR (ishidden IS NULL)) AND ";
	if ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT DISTINCT anno FROM ".$config_table_prefix."documents" . $sqlWhere . " ORDER BY anno DESC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	
	$sql = "SELECT DISTINCT YEAR(Data) as anno FROM ".$config_table_prefix."form_archivio_offerte WHERE idfornitore_hidden='$idusers'";
	$query = mysql_query ($sql, $conn);
	$rs2 = $objUtility->buildRecordset($query);
	        
	$arr1=array();
  for($i=0;$i<count($rs);$i++) {
    array_push($arr1, $rs[$i]['anno']);    
  }
  
  $arr2=array();
  for($i=0;$i<count($rs2);$i++) {
    array_push($arr2, $rs2[$i]['anno']);    
  }
	
	$ret_arr=array_merge($arr1, $arr2);
	$ret_arr=array_unique($ret_arr);
	sort($ret_arr);
	$ret_arr=array_reverse($ret_arr);
	
	$rs=array();
  for($i=0;$i<count($ret_arr);$i++) {
    $c=count($rs);
    $rs[$c]['anno']=$ret_arr[$i];    
  }
	
  return $rs;
}
/**
******************************************************************************************
* restituisce l'elenco dei documenti associati ad un tag
* @access public        
* @param $conn
* @param int $idtags: 
* @return array
*/
function getByTag($conn, $idtags)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "SELECT d.* FROM ".$config_table_prefix."documents_tags_nm nm LEFT JOIN ".$config_table_prefix."documents d ON nm.iddocuments=d.id WHERE nm.idtags=".$idtags." ORDER BY d.inserimento_data DESC";
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* restituisce l'elenco dei documenti, filtrando in base a diversi parametri
* @access public        
* @param $conn
* @param bool $idcat
* @param bool $isfull: impostato a true restituisce anche le news nascoste e scadute
* @return array
*/
function getList($conn, $idusers, $anno, $idtags, $isfull=true) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sqlWhere = "";
	if ($idusers) $sqlWhere .= "d.idusers='" . $idusers . "' AND ";
	if ($anno && $anno>0) $sqlWhere .= "d.anno=" . $anno . " AND ";
	if ($idtags) {
		$rs = $this->getByTag($conn, $idtags);
		if (count($rs)) 
		{
			$sqlWhere .= "(";
			while (list($key, $rowTmp) = each($rs)) 
			{ 
				$sqlWhere .= "d.id=" . $rowTmp["id"] . " OR ";
			}
		} else {
			$sqlWhere .= "(d.id=0 OR ";
		}
		$sqlWhere = substr($sqlWhere, 0, strlen($sqlWhere)-4) . ") AND ";
	}
	if (!$isfull) $sqlWhere .= "((d.ishidden<>1) OR (d.ishidden IS NULL)) AND ";
	
	if ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT d.*, o.nome, o.ext, o.path, o.originalname, u.login, u.codicecliente, u.ragionesociale FROM ".$config_table_prefix."documents d LEFT JOIN ".$config_table_prefix."oggetti o ON o.id=d.idoggetti LEFT JOIN ".$config_table_prefix."users u ON u.id=d.idusers " . $sqlWhere . " ORDER BY d.inserimento_data DESC, u.ragionesociale ASC, o.originalname ASC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* restituisce i dettagli del documento
* @access public        
* @param $conn
* @param int $id
* @return array
*/
function getDetails($conn, $id) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "SELECT d.*, o.nome, o.ext, o.path, o.originalname, o.isprivate, u.login, u.codicecliente, u.ragionesociale FROM ".$config_table_prefix."documents d LEFT JOIN ".$config_table_prefix."oggetti o ON o.id=d.idoggetti LEFT JOIN ".$config_table_prefix."users u ON u.id=d.idusers WHERE d.id=".$id;
	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* inserisce un documento
* @access public        
* @param $conn
* @param int id
* @return void
*/
function insert($conn, &$id, $idusers, $anno, $ishidden, $insertIdusers, $insertUsername, &$errorMsg) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$idusersSql = $objUtility->translateForDb($idusers, "int");
	$annoSql = $objUtility->translateForDb($anno, "int");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");
	$insertIdusersSql = $objUtility->translateForDb($insertIdusers, "int");
	$insertUsernameSql = $objUtility->translateForDb($insertUsername, "string");
	
	$sql = "INSERT INTO ".$config_table_prefix."documents (idusers, anno, ishidden, inserimento_idusers, inserimento_username, inserimento_data)";
	$sql .= " VALUES (".$idusersSql.",".$annoSql.",".$ishiddenSql.",".$insertIdusersSql.",".$insertUsernameSql.",NOW())";

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}

/**
******************************************************************************************
* aggiorna i dati del documento
* @access public        
* @param $conn
* @return void
*/
function update($conn, $id, $idusers, $anno, $ishidden, &$errorMsg) 
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$idusersSql = $objUtility->translateForDb($idusers, "int");
	$annoSql = $objUtility->translateForDb($anno, "int");
	$ishiddenSql = $objUtility->translateForDb($ishidden, "int");

	$sql = "UPDATE ".$config_table_prefix."documents SET ";
	$sql .= "idusers=" . $idusersSql . ", ";
	$sql .= "anno=" . $annoSql . ", ";
	$sql .= "ishidden=" . $ishiddenSql;
	$sql .= " WHERE id=" . $id;

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* cancella il documento
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
	$rs = $this->getDetails($conn, $id);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
		parent::delete($conn, $row["idoggetti"], $errorMsg);

		$sql = "DELETE FROM ".$config_table_prefix."documents WHERE id=" . $id;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	
		$sql = "DELETE FROM ".$config_table_prefix."documents_tags_nm WHERE iddocuments=" . $id;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}

/**
******************************************************************************************
* aggiorna il file associato al documento
* @access public        
* @param $conn
* @param int $id
* @param string $path: il nome del file sul filesystem
* @param string $ext: estensione del file
* @param string $originalName
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function fileUpdate($conn, $id, $path, $ext, $originalName, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$rs = $this->getDetails($conn, $id);
	if (count($rs) > 0)
	{
		list($key, $row) = each($rs);
		parent::delete($conn, $row["idoggetti"], $errorMsg);
	}

	$idoggetti=0;
	parent::insert($conn, $idoggetti, $originalName, $ext, $path, $originalName, true, $errorMsg);
	if ($idoggetti)
	{
		$sql = "UPDATE ".$config_table_prefix."documents SET idoggetti=".$idoggetti." WHERE id=".$id;
		mysql_query($sql, $conn);
		$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	}
}

/**
******************************************************************************************
* cancella tutte le associazioni tra un documento ed i tags
* @access public        
* @param $conn
* @param int $iddocuments
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function docTagsDelete($conn, $iddocuments, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "DELETE FROM ".$config_table_prefix."documents_tags_nm WHERE iddocuments=".$iddocuments;
	mysql_query ($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* associa un documento ad un tag
* @access public        
* @param $conn
* @param int $iddocuments
* @param int $idtags
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function docTagsIns($conn, $iddocuments, $idtags, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$sql = "INSERT INTO ".$config_table_prefix."documents_tags_nm (iddocuments, idtags) VALUES (".$iddocuments.",".$idtags.")";
	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
}

/**
******************************************************************************************
* aggiunge una riga alla tabella della history
* @access public        
* @param $conn
* @param int $iddocuments
* @param string $subject
* @param string $testo
* @param int $idusers
* @param string $username
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function emailInsert($conn, $iddocuments, $subject, $testo, $idusers, $username, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$iddocumentsSql = $objUtility->translateForDb($iddocuments, "int");
	$subjectSql = $objUtility->translateForDb($subject, "string");
	$testoSql = $objUtility->translateForDb($testo, "string");
	$idusersSql = $objUtility->translateForDb($idusers, "int");
	$usernameSql = $objUtility->translateForDb($username, "string");
	
	$sql = "INSERT INTO ".$config_table_prefix."documents_emailsent (iddocuments, subject, testo, inserimento_idusers, inserimento_username, inserimento_data)";
	$sql .= " VALUES (" . $iddocumentsSql.",".$subjectSql.",".$testoSql.",".$idusersSql.",".$usernameSql.",NOW())";

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}

/**
******************************************************************************************
* aggiunge una riga alla tabella della history
* @access public        
* @param $conn
* @param int $iddocuments
* @param string $subject
* @param string $testo
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function emailSend($conn, $iddocuments, $subject, $message, &$errorMsg)
{
	global $objUsers, $objUtility, $objConfig;

  $objMailing = new Mailing;

	$rs = $this->getDetails($conn, $iddocuments);
	if (count($rs))
		list($key, $rowDoc) = each($rs);

	$rs = $objUsers->getDetails($conn, $rowDoc["idusers"]);
	if (count($rs))
		list($key, $rowUsers) = each($rs);

	$path = $objUtility->getPathResourcesDynamicAbsolute();
	if ($rowDoc["isprivate"])
		$path = $objUtility->getPathResourcesPrivateAbsolute();
	$documentPath = $path;
	
  $allegato=$documentPath.$rowDoc["nome"].".".$rowDoc["ext"];
	$allegato_name=$rowDoc["originalname"];
  
  $esito = $objMailing->mmail($rowUsers["email"],$objConfig->get("email-from"),ln($subject),ln($message),$allegato,$allegato_type,$allegato_name);
	if (!$esito)
		$errorMsg .= "errore durante l'invio dell'email al cliente";			
}

/**
******************************************************************************************
* restituisce tutti gli elementi della history filtrati per iddocumento 
* @access public        
* @param $conn
* @param int $iddocuments
* @return array
*/
function emailList($conn, $iddocuments) 
{
	global $config_table_prefix, $objUtility;

	$sqlWhere = "";
	if ($iddocuments) $sqlWhere .= "iddocuments=" . $iddocuments . " AND ";
	if ($sqlWhere) $sqlWhere = " WHERE " . substr($sqlWhere, 0, strlen($sqlWhere)-5);
	$sql = "SELECT h.*, u.login, u.nome, u.cognome FROM ".$config_table_prefix."documents_emailsent h LEFT JOIN ".$config_table_prefix."documents d ON h.iddocuments=d.id LEFT JOIN ".$config_table_prefix."users u ON u.id=d.idusers " . $sqlWhere . " ORDER BY h.inserimento_data DESC";

	$query = mysql_query ($sql, $conn);
	$rs = $objUtility->buildRecordset($query);
	return $rs;
}

/**
******************************************************************************************
* aggiunge una riga alla tabella della history
* @access public        
* @param $conn
* @param int $iddocuments
* @param int $idusers
* @param string $username
* @param string $errorMsg: stringa che viene aggiornata con i dettagli degli eventuali errori generati dal metodo
* @return 
*/
function downloadInsert($conn, $iddocuments, $idusers, $username, &$errorMsg)
{
	global $config_table_prefix;
	$objUtility = new Utility;

	$iddocumentsSql = $objUtility->translateForDb($iddocuments, "int");
	$idusersSql = $objUtility->translateForDb($idusers, "int");
	$usernameSql = $objUtility->translateForDb($username, "string");
	
	$sql = "INSERT INTO ".$config_table_prefix."documents_download (iddocuments, inserimento_idusers, inserimento_username, inserimento_data)";
	$sql .= " VALUES (" . $iddocumentsSql.",".$idusersSql.",".$usernameSql.",NOW())";

	mysql_query($sql, $conn);
	$errorMsg .= $objUtility->errorMsgFormat(mysql_errno($conn), mysql_error($conn), $sql);
	if (!mysql_errno($conn) && !mysql_error($conn))
		$id = mysql_insert_id($conn);
}

}
?>