<?php



include ("_docroot.php");
include (SERVER_DOCROOT . "/logic/class_config.php");
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
global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);

$objUtility->getAction($strAct, $intId);
$id = (int) $_POST["id"];

switch ($strAct) {

	// ******************************************************************************************
	// MODULI

	case "MODULI-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idmod", $intId);
		header ("Location: moduli_insupd.php");
		break;

	case "MODULI-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		header ("Location: moduli_insupd.php");
		break;

	case "MODULI-DEL-GOTO":
		$strError = "";
		$id = $objUtility->sessionVarRead("idmod");
    $intIdmod1 = $objUtility->sessionVarRead("idmod1");
		$objMenu->moduliDelete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("menu.php?idmod=".$id."&idmod1=".$intIdmod1, $strEsito, "");
		break;

  case "MENU-DELICO-GOTO":
    $f=retRow("menu",$intId);
    unlink(retFileAbsolute($f['icona_file']));

    $sql="UPDATE ".$config_table_prefix."menu SET icona_file='0' WHERE id='$intId'";
    mysql_query($sql);
    
    $sql="DELETE FROM ".$config_table_prefix."oggetti WHERE id='".$f['icona_file']."'";
    mysql_query($sql);
    
    $strError = "";
    if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("menu.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("menu.php?idmod=".$id."&idmod1=".$intIdmod1, $strEsito, "");
		}
    break;
    
  case "MODULI-DELICO-GOTO":
    $f=retRow("menu_moduli",$intId);
    unlink(retFileAbsolute($f['icona_file']));
    
    $sql="UPDATE ".$config_table_prefix."menu_moduli SET icona_file='0' WHERE id='$intId'";
    mysql_query($sql);
    
    $sql="DELETE FROM ".$config_table_prefix."oggetti WHERE id='".$f['icona_file']."'";
    mysql_query($sql);
    
    $strError = "";
    if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("menu.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("menu.php?idmod=".$id."&idmod1=".$intIdmod1, $strEsito, "");
		}
    break;

	case "MODULI-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idmod");
    $intIdmod1 = $objUtility->sessionVarRead("idmod1");
    
		$titolo = $_POST["titolo"];
		$testo = $_POST["testo"];
		//$ordine = $_POST["ordine"];
    
    $id_oggetto=0;
    if($_FILES["icona"]["tmp_name"]){
      $dest_file=$objUtility->getFilenameUnique();
      $post_name=$_FILES["icona"]["name"];
  		$post_type=$_FILES["icona"]["type"];
  		$post_tmpname=$_FILES["icona"]["tmp_name"];
      
      $arr=explode(".", $post_name);
      $arr=array_reverse($arr);
      $strExt = $arr[0];
      
      $res=move_uploaded_file($_FILES["icona"]["tmp_name"], $objUtility->getPathResourcesDynamicAbsolute().$dest_file.".".$strExt);
      if($res) {
        $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,ext,path,originalname) VALUES ('$dest_file','$strExt','".$objUtility->getPathResourcesDynamicAbsolute()."','$post_name') ";
        $query=mysql_query($sql);
    		$id_oggetto=mysql_insert_id();
      }
    }

		$strError = "";
		switch ($action) {
			case "ins":
				$sql="SELECT MAX(ordine) FROM ".$config_table_prefix."menu_moduli";
    		$ordine=mysql_query($sql);
        $ordine=mysql_fetch_array($ordine);
        $ordine=$ordine[0]+10;
				
        $objMenu->moduliInsert($conn, $id, $intIdmod1, $titolo, $testo, $ordine,$id_oggetto, $strError);
				break;
			case "upd":
				$sql="SELECT ordine FROM ".$config_table_prefix."menu_moduli WHERE id='$id'";
        $ordine=mysql_query($sql);
        $ordine=mysql_fetch_array($ordine);
        $ordine=$ordine[0];
				
        $objMenu->moduliUpdate($conn, $id, $titolo, $testo, $ordine,$id_oggetto, $strError);
				break;
		}
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("menu.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("menu.php?idmod=".$id."&idmod1=".$intIdmod1, $strEsito, "");
		}
		break;
		
		case "MODULI1-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idmod1", $intId);
		header ("Location: moduli1_insupd.php");
		break;

	case "MODULI1-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		header ("Location: moduli1_insupd.php");
		break;

	case "MODULI1-DEL-GOTO":
		$strError = "";
		$id = $objUtility->sessionVarRead("idmod");
    $intIdmod1 = $objUtility->sessionVarRead("idmod1");
		
    $objMenu->moduli1Delete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("menu.php?idmod=".$id."&idmod1=".$intIdmod1, $strEsito, "");
		break;

	case "MODULI1-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$id = $objUtility->sessionVarRead("idmod1");

		$idmod1 = $_POST["idmod1"];
    $titolo = $_POST["titolo"];
		$testo = $_POST["testo"];
		//$ordine = $_POST["ordine"];

		$strError = "";
		switch ($action) {
			case "ins":
				$sql="SELECT MAX(ordine) FROM ".$config_table_prefix."menu_categorie";
    		$ordine=mysql_query($sql);
        $ordine=mysql_fetch_array($ordine);
        $ordine=$ordine[0]+10;
				
        $objMenu->moduli1Insert($conn, $id, $titolo, $testo, $ordine, $strError);
				break;
			case "upd":
				$sql="SELECT ordine FROM ".$config_table_prefix."menu_categorie WHERE id='$id'";
        $ordine=mysql_query($sql);
        $ordine=mysql_fetch_array($ordine);
        $ordine=$ordine[0];
        $objMenu->moduli1Update($conn, $id, $titolo, $testo, $ordine, $strError);
				break;
		}
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("menu.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("menu.php?idmod1=".$id, $strEsito, "");
		}
		break;

	// ******************************************************************************************
	// MENU

	case "MENU-UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idmen", $intId);
		header ("Location: menu_insupd.php");
		break;

	case "MENU-INS-GOTO":
		$objUtility->sessionVarUpdate("action", "ins");
		header ("Location: menu_insupd.php");
		break;

	case "MENU-DEL-GOTO":
		$strError = "";
		$objMenu->menuDelete($conn, $intId, $strError);
		if ($strError) {
			$strEsito = "Attenzione<br><br>Non è stato possibile cancellare l'elemento selezionato";
		} else {
			$strEsito = "Cancellazione effettuata";
		}
		$objHtml->adminPageRedirect("menu.php", $strEsito, "");
		break;

	case "MENU-INSUPD-DO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$idmen = $objUtility->sessionVarRead("idmen");

		$idmod = $_POST["idmoduli"];
		$nome = $_POST["nome"];
		$path = $_POST["path"];
		$ttab = $_POST["tabella"];
    $desk = $_POST["desktop"];
    if(!$desk) $desk="0";

    $id_oggetto=0;
    if($_FILES["icona"]["tmp_name"]){
      $dest_file=$objUtility->getFilenameUnique();
      $post_name=$_FILES["icona"]["name"];
  		$post_type=$_FILES["icona"]["type"];
  		$post_tmpname=$_FILES["icona"]["tmp_name"];
      
      $arr=explode(".", $post_name);
      $arr=array_reverse($arr);
      $strExt = $arr[0];
      
      $res=move_uploaded_file($_FILES["icona"]["tmp_name"], $objUtility->getPathResourcesDynamicAbsolute().$dest_file.".".$strExt);
      if($res) {
        $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,ext,path,originalname) VALUES ('".$dest_file."','$strExt','".$objUtility->getPathResourcesDynamicAbsolute()."','$post_name') ";
        $query=mysql_query($sql);
    		$id_oggetto=mysql_insert_id();
      }
    }

		$strError = "";
		switch ($action) {
			case "ins":
				$sql="SELECT MAX(ordine) FROM ".$config_table_prefix."menu WHERE idmoduli='$idmod'";
    		$ordine=mysql_query($sql);
        $ordine=mysql_fetch_array($ordine);
        $ordine=$ordine[0]+10;
				
        $objMenu->menuInsert($conn, $idmen, $idmod, $nome, $path, $ttab, $ordine,$id_oggetto,$desk, $strError);
				break;
			case "upd":
				$rs=retRow("menu",$idmen);
        $idmod=$rs['idmoduli'];
        
        $sql="SELECT ordine FROM ".$config_table_prefix."menu WHERE id='$idmen'";
    		$ordine=mysql_query($sql);
        $ordine=mysql_fetch_array($ordine);
        $ordine=$ordine[0];
				
        $objMenu->menuUpdate($conn, $idmen, $idmod, $nome, $path, $ttab, $ordine,$id_oggetto,$desk, $strError);
				break;
		}
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("menu.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("menu.php?idmod=".$idmod, $strEsito, "");
		}
		break;

	case "MENU-ROLES-GOTO":
		$objUtility->sessionVarUpdate("idmen", $intId);
		header ("Location: menu_roles.php");
		break;

	case "MENU-ROLES-DO":
		$idmen = $objUtility->sessionVarRead("idmen");

		$strError = "";
		$objMenu->menuRolesDelete($conn, $idmen, $strError);

		$tot = $_POST["id_tot"];
		for ($i=1; $i<=$tot; $i++) {
			$id = $_POST["id_" . $i];
			if ($id) {
				$objMenu->menuRolesIns($conn, $idmen, $id, $strError);
			}
		}

		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect("menu.php", $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect("menu.php", $strEsito, "");
		}
		
    case "MODULI1-CATEGORIE-MOVEUP-DO":
  	 CategorieMoveUp($config_table_prefix."menu_categorie",$intId);   
		break;
		
		case "MODULI1-CATEGORIE-MOVEDOWN-DO":
		  CategorieMoveDown($config_table_prefix."menu_categorie",$intId);    
		break;
		
		case "MODULI-CATEGORIE-MOVEUP-DO":
  	 CategorieMoveUp($config_table_prefix."menu_moduli",$intId);   
		break;
		
		case "MODULI-CATEGORIE-MOVEDOWN-DO":
		  CategorieMoveDown($config_table_prefix."menu_moduli",$intId);    
		break;
		
		case "MENU-CATEGORIE-MOVEUP-DO":
  	 CategorieMoveUp($config_table_prefix."menu",$intId);   
		break;
		
		case "MENU-CATEGORIE-MOVEDOWN-DO":
		  CategorieMoveDown($config_table_prefix."menu",$intId);    
		break;

}

function CategorieMoveUp ($table,$intId) {	  
  $arr_intid=explode("#", $intId);
  $next=$arr_intid[1];
  $intId=$arr_intid[0];
  
  $sql="SELECT ordine FROM `".$table."` WHERE id='$intId'";
  $result = mysql_query($sql);
  $row=mysql_fetch_array($result);
  
  $id1=$intId;
  $ord1=$row['ordine'];
  
  $sql="SELECT id,ordine FROM `".$table."` WHERE id='$next' ORDER BY ordine DESC" ;
  $result = mysql_query($sql);
  $row=mysql_fetch_array($result);
  
  $id2=$row['id'];
  $ord2=$row['ordine'];
  
  $sql="UPDATE `".$table."` SET ordine='$ord2' WHERE id='$id1'" ;
  $result = mysql_query($sql);
  
  $sql="UPDATE `".$table."` SET ordine='$ord1' WHERE id='$id2'" ;
  $result = mysql_query($sql);
  		
  if ($strError) {
  	$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
  	$objHtml->adminPageRedirect($currentPage, $strEsito, "");
  } else {
  	$strEsito = "Operazione eseguita correttamente";
    ?><script>location.href = "menu.php";</script><?
  	
  } 
}

function CategorieMoveDown ($table,$intId) {	  	  
  $arr_intid=explode("#", $intId);
  $next=$arr_intid[1];
  $intId=$arr_intid[0];
  
  $sql="SELECT ordine FROM `".$table."` WHERE id='$intId'";
  $result = mysql_query($sql);
  $row=mysql_fetch_array($result);
	
	$id1=$intId;
  $ord1=$row['ordine'];
	
	$sql="SELECT id,ordine FROM `".$table."` WHERE id='$next' ORDER BY ordine ASC" ;
  $result = mysql_query($sql);
  $row=mysql_fetch_array($result);
  
  $id2=$row['id'];
  $ord2=$row['ordine'];
  
  $sql="UPDATE `".$table."` SET ordine='$ord2' WHERE id='$id1'" ;
  $result = mysql_query($sql);
  
  $sql="UPDATE `".$table."` SET ordine='$ord1' WHERE id='$id2'" ;
  $result = mysql_query($sql);
  		
  if ($strError) {
		$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
		$objHtml->adminPageRedirect($currentPage, $strEsito, "");
	} else {
		$strEsito = "Operazione eseguita correttamente";
		?><script>location.href = "menu.php";</script><?
		//echo $sql;
	} 
}
?>