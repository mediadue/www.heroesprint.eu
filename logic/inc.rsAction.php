<?php
session_start();



include ("_docroot.php");
include (SERVER_DOCROOT . "logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$objCarrello = new Carrello;
$objWindows = new rsWindows;
$objTable2 = new rsTable2;
$objWinMod = new rsWinMod;
$objStrutture = new rsStrutture;
$ObjrsPdfEditor=new rsPdfEditor();
$ObjChat=new rsChat();

$conn = $objDb->connection($objConfig);
$dbname = $objConfig->get("db-dbname");

global $config_table_prefix;

$objWindows->action();
$objTable2->action();
$objCarrello->action();
$objWinMod->action();
$objStrutture->action();
$ObjrsPdfEditor->action();
$ObjChat->action();

geografia();
      
if($_GET['ecommSegnala']=="1") {
  require_once "include/inc.RsFunctions.php";
  ?>
  <style>
  div {text-align: left;}
  </style>
  <?php
  stampaSegnalaAmici();
  exit;
}

if($_GET['ecommQuotazione']=="1") {
  require_once "include/inc.RsFunctions.php";
  ?>
  <style>
  div {text-align: left;}
  </style>
  <?php
  stampaRichiediQuotazione();
  exit;
}

if(isset($_GET['ecomm_riepilogo'])) exit;

if(!isset($_SESSION["userris_id"])) $objUsers->getCurrentUser($intIdutente, $strUsername,false,-1);

$objUtility->getAction($strAct, $intId);

$table=$_POST['table'];
$currentPage=$_POST['currentPage'];
$parent=$_POST['parent'];
$tblparent=$_POST['tblparent'];
$subPrint=$_POST['subPrint'];
$colfilter=$_POST['colfilter'];
$filter=urldecode($_POST['filter']);

if($_POST['rsGetMagazzinoArticoli']=="1") {
  $tableId=$_POST['tableId'];
  $cat=retRow("categorie",$tableId);
  $magazzino_articoli=Table2ByTable1("categorie","magazzino_articoli",$tableId,"(`".$config_table_prefix."magazzino_articoli`.del_hidden='0')","`".$config_table_prefix."magazzino_articoli`.id DESC");
  $isusersys=isUserSystem();
  
  if($cat["is_system"]==2 && !$isusersys){
    $magazzino_articoli_tema=getTable("magazzino_articoli_tema","id DESC","id_magazzino_articoli='".$magazzino_articoli[0]['id']."'");
    if(count($magazzino_articoli_tema)==0){
        $sql="INSERT INTO `".$config_table_prefix."magazzino_articoli_tema` (id_magazzino_articoli) VALUES ('".$magazzino_articoli[0]['id']."')";
        mysql_query($sql);  
        $id = mysql_insert_id();
        
        if($id>0) rsTable2_AfterInsert("magazzino_articoli_tema",$id,0,0);
        
        $magazzino_articoli_tema=retRow("magazzino_articoli_tema",$id); 
        echo "1;" . $magazzino_articoli_tema['id'];
    }else{
        echo "1;" . $magazzino_articoli_tema[0]['id'];
    }
     
  }else{
    echo "2;" . $magazzino_articoli[0]['id'];
  }
  
  exit;
}

if($_POST['rsGetBriciole']=="1") {
  $rsId=$_POST['id'];
  $briciole=retBriciole("","",$rsId);
  $briciole=implode(" > ",$briciole);
  echo strip_tags($briciole);
  exit;
}

$exit=false;
if($_POST["idComune"]!="") {
  $exit=true;
}

if($exit) exit;

refreshChecks($table);

function refreshChecks($table) {
  if(!is_array($_SESSION[$table."checkSel"])) $_SESSION[$table."checkSel"]=array();
  $chSel=$_SESSION[$table."checkSel"];
  if($_POST["checkSelV"]) {
    for($z=0;$z<count($_POST["checkSelV"]);$z++) {
      $vv=explode("_", $_POST["checkSelV"][$z]);
      if(in_array($vv[0], $chSel)) {
        if($vv[1]=="false") {
          for($j=0;$j<count($chSel);$j++) {
            if($chSel[$j]==$vv[0]) $chSel[$j]="y";  
          }
        }
      } else {
        if($vv[1]=="true") {
          array_push($chSel, (int) $vv[0]);
        }  
      }
    }
    $chSel=array_filter($chSel, "is_numeric");
    $chSel=array_values($chSel);
    
    $_SESSION[$table."checkSel"]=$chSel;
    $_SESSION["rsTable2_".$table."_selection"]=$chSel;
  }
}
                 
function addFile($id="",$id1,$table,$index="") {
	global $config_table_prefix;
	
  $parent=$_POST['parent'];
  $tblparent=$_POST['tblparent'];
	
  $objUtility =& new Utility;
	
	$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
	
	$isUploadOk = false;
	$strUnique = $objUtility->getFilenameUnique();
	$strDestFile = $strUnique;
  
	if($index=="") {
    $post_name=$_FILES["allegato"]["name"];
		$post_type=$_FILES["allegato"]["type"];
		$post_tmpname=$_FILES["allegato"]["tmp_name"];
	} else {
    $post_name=$_FILES["allegato"]["name"][$index];
		$post_type=$_FILES["allegato"]["type"][$index];
		$post_tmpname=$_FILES["allegato"]["tmp_name"][$index];			
	}
	
	if ($post_name) 
	{
		//$strExt = $objUtility->getExtFromMime($post_type);
		
		if($id!="y") {
    	$query = mysql_query("SELECT * FROM `".$config_table_prefix."oggetti` WHERE id='$id' ");
      if($arr=mysql_fetch_array($query)) {
        unlink($arr['path'].$arr['nome'].".".$arr['ext']);
        $strDestFile=$arr['nome'];
      }
  	}
    
    $arr=explode(".", $post_name);
    $arr=array_reverse($arr);
    $strExt = $arr[0];
		
		$isUploadOk = move_uploaded_file($post_tmpname, $strDestDir.$strDestFile.".".$strExt);
		
		if ($isUploadOk)
		{
			chmod($strDestDir.$strDestFile.".".$strExt, 0644);
			$strOggettoPath = $strDestDir;
			$strOggettoExt = $strExt;
			$strOggettoOriginalname = $post_name;

			if($id!="y") {
			  $sql="UPDATE `".$config_table_prefix."oggetti` SET ext='$strOggettoExt', originalname='$strOggettoOriginalname' WHERE id='$id'  ";
        $query=mysql_query($sql);
			} else {
        $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,ext,path,originalname) VALUES ('$strDestFile','$strOggettoExt','$strOggettoPath','$strOggettoOriginalname') ";
        $query=mysql_query($sql);
  			$id_oggetto=mysql_insert_id();
      }
      
			$result = mysql_query("SELECT * FROM `$table` ");
      $field1=mysql_field_name($result,1);
      $field2=mysql_field_name($result,2);
			
			if($id=="y") {
        $sql="INSERT INTO `$table` ($field1,$field2) VALUES ('$id1', '$id_oggetto') ";
  			$p_res=mysql_query($sql);
      }
      
      $tmptbl=str_replace("id_", "", $field1);
      $objUtility->sessionVarUpdate("table", $config_table_prefix.$tmptbl);
      $objUtility->sessionVarUpdate("idmod", $parent);
      $objUtility->sessionVarUpdate("action", "upd");	
		}
	}
}

function addFiles($id="",$index="") {
	global $config_table_prefix;
  $objUtility =& new Utility;
	$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
	
	$isUploadOk = false;
	$strUnique = $objUtility->getFilenameUnique();
	$strDestFile = $strUnique;

	if($index=="") {
    $post_name=$_FILES["allegatos"]["name"];
		$post_type=$_FILES["allegatos"]["type"];
		$post_tmpname=$_FILES["allegatos"]["tmp_name"];
	} else {
		$index=$index-1;
    $post_name=$_FILES["allegatos"]["name"][$index];
		$post_type=$_FILES["allegatos"]["type"][$index];
		$post_tmpname=$_FILES["allegatos"]["tmp_name"][$index];			
	}
	
	if ($post_name) 
	{
		//$strExt = $objUtility->getExtFromMime($post_type);
    
    if($id!="y" && $id!="0") {
    	$query = mysql_query("SELECT * FROM `".$config_table_prefix."oggetti` WHERE id='$id' ");
      if($arr=mysql_fetch_array($query)) {
        unlink($arr['path'].$arr['nome'].".".$arr['ext']);
        $strDestFile=$arr['nome'];
        //echo "pp";
        //$strDestFile=$objUtility->getFilenameUnique();
        //$query = mysql_query("UPDATE `".$config_table_prefix."oggetti` SET nome='$strDestFile' WHERE id='$id' ");
      }
  	}
    
    $arr=explode(".", $post_name);
    $arr=array_reverse($arr);
    $strExt = $arr[0];
		
		$isUploadOk = move_uploaded_file($post_tmpname, $strDestDir.$strDestFile.".".$strExt);
		
		if ($isUploadOk)
		{
			chmod($strDestDir.$strDestFile.".".$strExt, 0644);
			$strOggettoPath = $strDestDir;
			$strOggettoExt = $strExt;
			$strOggettoOriginalname = $post_name;
      
			if($id!="y" && $id!="0") {
			  $sql="UPDATE `".$config_table_prefix."oggetti` SET ext='$strOggettoExt', originalname='$strOggettoOriginalname' WHERE id='$id'  ";
        $query=mysql_query($sql);
        
        return $id;
			} else {
        $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,ext,path,originalname) VALUES ('$strDestFile','$strOggettoExt','$strOggettoPath','$strOggettoOriginalname') ";
        $query=mysql_query($sql);
  			$id_oggetto=mysql_insert_id();
  			return $id_oggetto;
      }
		}
	}
}

function delete($intId) {
  $objUtility = new Utility;
  global $config_table_prefix;
   
  $table=$_POST['table'];
  $currentPage=$_POST['currentPage'];
  $parent=$_POST['parent'];
  $tblparent=$_POST['tblparent'];

  $strError = "";
  $objUtility->sessionVarUpdate("idmod", $intId);
  $objUtility->sessionVarUpdate("table", $table);
  $objUtility->sessionVarUpdate("currentPage", $currentPage);
  $objUtility->sessionVarUpdate($table."parent", $parent);
  $objUtility->sessionVarUpdate($table."tblparent", $tblparent);
  
  if($table==$config_table_prefix."oggetti") {
    $query = mysql_query("SELECT * FROM `$table` WHERE id='$intId' ");
    if($arr=mysql_fetch_array($query)) unlink($arr['path'].$arr['nome'].".".$arr['ext']);
  }
  
  deleteFileFromTable($table,$intId,"-1");

  $sql="DELETE FROM `$table` WHERE id='$intId' ";
  mysql_query($sql); 
  
  $tget="id".$table;
  $objUtility->sessionVarUpdate($tget, "");
  
  if($parent!="") {
    $result = mysql_query("SELECT * FROM `$tblparent` ");
    $field1=mysql_field_name($result,1);
    $field2=mysql_field_name($result,2);
    
    $sql="DELETE FROM `$tblparent` WHERE ($field1='$parent' AND $field2='$intId')"; 
    $p_res=mysql_query($sql);
    
    $tmptbl=str_replace("id_", "", $field1);
    $objUtility->sessionVarUpdate("table", $config_table_prefix.$tmptbl);
    $objUtility->sessionVarUpdate("idmod", $parent);
  }
}

function deletes($intId) {
  $objUtility = new Utility;
  global $config_table_prefix;

  $table=$_POST['table'];
  $currentPage=$_POST['currentPage'];
  $parent=$_POST['parent'];
  $tblparent=$_POST['tblparent'];
  
  $strError = "";
  $objUtility->sessionVarUpdate("idmod", $intId);
  $objUtility->sessionVarUpdate("table", $table);
  $objUtility->sessionVarUpdate("currentPage", $currentPage);
  $objUtility->sessionVarUpdate($table."parent", $parent);
  $objUtility->sessionVarUpdate($table."tblparent", $tblparent);
  
  $query = mysql_query("SELECT * FROM ".$config_table_prefix."oggetti WHERE id='$intId' ");
 
  if($arr=mysql_fetch_array($query)){
    unlink($objUtility->getPathResourcesDynamicAbsolute().$arr['nome'].".".$arr['ext']);
  } 
  
  $sql="DELETE FROM ".$config_table_prefix."oggetti WHERE id='$intId' ";
  mysql_query($sql);
  
  $targ="allegatodel".$intId;
  $targ2="rowid".$intId;
  $sql="SELECT * FROM `$table` WHERE id='".$_POST[$targ2]."' ";
  $query=mysql_query($sql);
  $arr=mysql_fetch_array($query);
  
  if(!(strpos($arr[$_POST[$targ]], ";")===FALSE)){
    $arr=explode(";", $arr[$_POST[$targ]]);
  
    for($z=0;$z<count($arr);$z++) {
      if($arr[$z]==$intId) unset($arr[$z]);
    }
    
    $tarr=implode(";", $arr);
  }
  
  if(trim($tarr=="")) $tarr='0';
  if(trim($tarr==";")) $tarr='0';
  
  $sql="UPDATE `$table` SET ".$_POST[$targ]."='$tarr' WHERE id='".$_POST[$targ2]."' ";
  mysql_query($sql);
  
  $tget="id".$table;
  $objUtility->sessionVarUpdate($tget, "");
  
  if($parent!="") {
    $result = mysql_query("SELECT * FROM `$tblparent` ");
    $field1=mysql_field_name($result,1);
    $field2=mysql_field_name($result,2);
    
    $tmptbl=str_replace("id_", "", $field1);
    $objUtility->sessionVarUpdate("table", $config_table_prefix.$tmptbl);
    $objUtility->sessionVarUpdate("idmod", $parent);
  }
}

switch ($strAct) {

	// ******************************************************************************************
	// MODULI

	case "UPD-GOTO":
		$objUtility->sessionVarUpdate("action", "upd");
		$objUtility->sessionVarUpdate("idmod", $intId);
		$objUtility->sessionVarUpdate("parent", $parent);
		$objUtility->sessionVarUpdate("tblparent", $tblparent);
    $objUtility->sessionVarUpdate("table", $table);
    $objUtility->sessionVarUpdate($table."parent", $parent);
		$objUtility->sessionVarUpdate($table."tblparent", $tblparent);
		$objUtility->sessionVarUpdate($table."currentPage", $currentPage);
		$objUtility->sessionVarUpdate($table."subPrint", $subPrint);
		$objUtility->sessionVarUpdate($table."colfilter", $colfilter);
		$objUtility->sessionVarUpdate($table."filter", $filter);
		
    if(function_exists(str_replace($config_table_prefix,"", $table)."_before_update")) eval(str_replace($config_table_prefix,"", $table)."_before_update();");
    
    header ("Location: rsTable_insupd.php");
		break;

	case "INS-GOTO":
    $objUtility->sessionVarUpdate("action", "ins");
		$objUtility->sessionVarUpdate("idmod", $intId);
		$objUtility->sessionVarUpdate("table", $table);
    $objUtility->sessionVarUpdate($table."parent", $parent);
		$objUtility->sessionVarUpdate($table."tblparent", $tblparent);
		$objUtility->sessionVarUpdate($table."currentPage", $currentPage);
		$objUtility->sessionVarUpdate($table."colfilter", $colfilter);
		$objUtility->sessionVarUpdate($table."filter", $filter);
		
		if(function_exists(str_replace($config_table_prefix,"", $table)."_before_insert")) eval(str_replace($config_table_prefix,"", $table)."_before_insert();");
		
    header ("Location: rsTable_insupd.php");
		break;

	case "DEL-DO":		
		$tdel=true;
    if(function_exists(str_replace($config_table_prefix,"", $table)."_before_delete")) eval("\$tdel=".str_replace($config_table_prefix,"", $table)."_before_delete('$intId');");
	  
    if($tdel) {
      delete($intId);
    }
    
    if(function_exists(str_replace($config_table_prefix,"", $table)."_after_delete")) eval(str_replace($config_table_prefix,"", $table)."_after_delete('$intId');");

		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			header("location:$currentPage#$table");
		}
		break;
		
	case "DELS-DO":		
		cancellaNodoStruttura($intId);
    
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			header("location:$currentPage");
		}
		break;
		
	case "ALLEGATO-DEL-DO":		
		deletes($intId);
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
		}
		break;
  
  case "ANNULLA-DO":		
		if($parent!="") {
      $result = mysql_query("SELECT * FROM `$tblparent` ");
      $field1=mysql_field_name($result,1);
      $field2=mysql_field_name($result,2);
      
      $tmptbl=str_replace("id_", "", $field1);
      $objUtility->sessionVarUpdate("table", $config_table_prefix.$tmptbl);
      $objUtility->sessionVarUpdate("idmod", $parent);
      $objUtility->sessionVarUpdate("action", "upd");
    }
		
    if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			header("location:".$currentPage);
		}  
		break;
		
	case "ALLEGATO-DEL-DO":		
		deletes($intId);
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
		}
		break;
  
	case "INSUPD-DO":
    $strError = "";
    $action = strtolower($objUtility->sessionVarRead("action"));
		$table = $objUtility->sessionVarRead("table");
		$id = $objUtility->sessionVarRead("idmod");
		$objUtility->sessionVarUpdate($table."parent", $parent);
		$objUtility->sessionVarUpdate($table."tblparent", $tblparent);
		$objUtility->sessionVarUpdate($table."colfilter", $colfilter);
		
		if($table!=$config_table_prefix."oggetti") {
      switch ($action) {
  			case "ins":
  			  if($parent=="") $parent=$id;
          $result = mysql_query("SELECT Ordinamento FROM `".$table."` ORDER BY Ordinamento DESC ");
  			  
          if($result) {
            $r=mysql_fetch_array($result);
            $_POST['Ordinamento']=$r['Ordinamento']+10;
          }
  			  
          $result = mysql_query("SELECT $colfilter FROM `".$table."`");
          
          $j=0;
          $k=0;
          while ($j < mysql_num_fields($result)) {    
            $field = mysql_fetch_field($result);
            $key=$field->name;
            if($key!="id" && !strpos($key, "_hidden")) {
              if(mysql_field_type($result,$j)=="date") {
                $anno="anno".$key;
                $mese="mese".$key;
                $giorno="giorno".$key;
                
                $_POST[$key]=$_POST[$anno]."-".$_POST[$mese]."-".$_POST[$giorno];
                
                $sql1.=$key.",";
                $sql2.="'".$_POST[$key]."',";
              } else if(strpos($key, "_file")) {
                $k++;
          			
          			$substr="";
                if(strpos($key, "_thm")) {
                  $substr=substr($key, strpos($key, "_thm"), strlen($key)-strpos($key, "_thm"));
                }
          			
                if ($_FILES["allegatos"]["name"][($k-1)]) {
                  $id_oggetto=addFiles("0",$k);
                  $sql1.=$key.",";
                  $sql2.="'$id_oggetto',";
                  
                  if($substr!="") {
                    $substr=str_replace("_thm", "", $substr);
                    $dim=substr($substr, 0, strpos($substr, "_"));
                    $destf=str_replace($dim."_", "", $substr);
                    $dim=explode("X", $dim);
                    $thumb=imgResizeByID($id_oggetto,$dim[0],$dim[1],$addObject="1");
                    $sql1.=$destf.",";
                    $sql2.="'$thumb',";
                  }
                  
          			}
              } else {
                $sql1.=$key.",";
                $sql2.="'".$_POST[$key]."',";
              }
            }
            $j++;
          }
          $sql1="($sql1)";
          $sql2="($sql2)";
          
          $sql1=str_replace(",)", ")", $sql1);
          $sql2=str_replace(",)", ")", $sql2);
          
          $sql="INSERT INTO `$table` $sql1 VALUES $sql2 "; 
          $p_res=mysql_query($sql);
          
          $id=mysql_insert_id();
          
          $tget="id".$table;
      		$objUtility->sessionVarUpdate($tget, $id);
      		 
          if($parent!="" && $tblparent!="") {
            $result = mysql_query("SELECT * FROM `$tblparent` ");
            $field1=mysql_field_name($result,1);
            $field2=mysql_field_name($result,2);
            
            $sql="INSERT INTO `$tblparent` ($field1,$field2) VALUES ('$parent','$id') "; 
            $p_res=mysql_query($sql);
            
            $tmptbl=str_replace("id_", "", $field1);
            $objUtility->sessionVarUpdate("table", $config_table_prefix.$tmptbl);
            $objUtility->sessionVarUpdate("idmod", $parent);
            $objUtility->sessionVarUpdate("action", "upd");
          }
          
          if($table==$config_table_prefix."categorie")  {
            $mys=strpos($currentPage, "&menid=");
            if($mys!==FALSE) {
              $mye=strpos($currentPage, "&",$mys+1);
              if($mye===FALSE) $mye=strlen($currentPage);
              $tosubtr=substr($currentPage, $mys, $mye-$mys);
              $currentPage=str_replace($tosubtr, "", $currentPage);
            }
            
            $mys=strpos($currentPage, "?menid=");
            if($mys!==FALSE) {
              $mye=strpos($currentPage, "&",$mys);
              if($mye===FALSE) $mye=strlen($currentPage);
              $tosubtr=substr($currentPage, $mys, $mye-$mys);
              $currentPage=str_replace($tosubtr, "", $currentPage);
            }
            
            if(strpos($currentPage, "?")!==FALSE) {
              $currentPage=$currentPage."&menid=$id";
            } else {
              $currentPage=$currentPage."?menid=$id";    
            }      
          }
          
          if(function_exists(str_replace($config_table_prefix,"", $table)."_after_insert")) eval(str_replace($config_table_prefix,"", $table)."_after_insert('".$id."');");
  			break;
  			case "upd":          
          $doUpd=true;
          if(function_exists(str_replace($config_table_prefix,"", $table)."_before_update_db")) eval("\$doUpd=".str_replace($config_table_prefix,"", $table)."_before_update_db(\$id);");
  		    
  		    if($doUpd) {
            $result = mysql_query("SELECT $colfilter FROM `".$table."` WHERE id='$id'");
            $field = mysql_fetch_array($result,MYSQL_ASSOC);
            
            $j=0;
            $k=0;          
            while (list($key, $cell) = each($field)) {    
              if(function_exists(str_replace($config_table_prefix,"", $table)."_before_update_column")) eval(str_replace($config_table_prefix,"", $table)."_before_update_column(\$id,\$key);");
              
              if($key!="id" && !strpos($key, "_hidden") && $key!="Ordinamento") {
                if(mysql_field_type($result,$j)=="date") {
                  $anno="anno".$key;
                  $mese="mese".$key;
                  $giorno="giorno".$key;
                  
                  $_POST[$key]=$_POST[$anno]."-".$_POST[$mese]."-".$_POST[$giorno];
                  
                  $sql="UPDATE `$table` SET $key='".$_POST[$key]."' WHERE id='$id' ";
                  $p_res=mysql_query($sql);
                } else if(strpos($key, "_file")) {
                  $k++;
                  
                  $substr="";
                  if(strpos($key, "_thm")) {
                    $substr=substr($key, strpos($key, "_thm"), strlen($key)-strpos($key, "_thm"));
                  }
                  
                  if ($_FILES["allegatos"]["name"][($k-1)]) {
                    $id_oggetto=addFiles($cell,$k);
                    $sql="UPDATE `$table` SET $key='$id_oggetto' WHERE id='$id' ";
                    $p_res=mysql_query($sql);
                    
                    if($substr!="") {
                      $substr=str_replace("_thm", "", $substr);
                      $dim=substr($substr, 0, strpos($substr, "_"));
                      $destf=str_replace($dim."_", "", $substr);
                      $dim=explode("X", $dim);
                      $thumb=imgResizeByID($id_oggetto,$dim[0],$dim[1],$addObject="1");
                      $sql="UPDATE `$table` SET $destf='$thumb' WHERE id='$id' ";
                      $p_res=mysql_query($sql);
                    }
                    
            			}
                } else {
                  $sql="UPDATE `$table` SET $key='".$_POST[$key]."' WHERE id='$id' ";
                  $p_res=mysql_query($sql);
        			  }
              }
              $j++;
              if(function_exists(str_replace($config_table_prefix,"", $table)."_after_update_column")) eval(str_replace($config_table_prefix,"", $table)."_after_update_column(\$id,\$key);");
            }
            $tget="id".$table;
        		$objUtility->sessionVarUpdate($tget, $id);
        		                                                                                                                                            
        		if($parent!="") {
              $result = mysql_query("SELECT * FROM `$tblparent` ");
              $field1=mysql_field_name($result,1);
              $field2=mysql_field_name($result,2);
              
              $tmptbl=str_replace("id_", "", $field1);
              $objUtility->sessionVarUpdate("table", $config_table_prefix.$tmptbl);
              $objUtility->sessionVarUpdate("idmod", $parent);
            }
    				break;
    		}
      }
  		
  		for($j=0;$j<count($_FILES["allegato"]["name"]);$j++) {
  			if ($_FILES["allegato"]["name"][$j]) {
  				addFile($id,$parent,$tblparent,$j);
  			}
  		}
  		
  		if($parent!="" && $tblparent!="") {
        $result = mysql_query("SELECT * FROM `$tblparent` ");
        $field1=mysql_field_name($result,1);
        $field2=mysql_field_name($result,2);
        
        $tmptbl=str_replace("id_", "", $field1);
        $objUtility->sessionVarUpdate("table", $config_table_prefix.$tmptbl);
        $objUtility->sessionVarUpdate("idmod", $parent);
        $objUtility->sessionVarUpdate("action", "upd");
      }
      
      if(function_exists(str_replace($config_table_prefix,"", $table)."_after_update")) eval(str_replace($config_table_prefix,"", $table)."_after_update('".$id."');");
      
  		if ($strError) {
  			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
  			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
  		} else {
  			$strEsito = "Operazione eseguita correttamente";
        $objHtml->adminPageRedirect($currentPage, $strEsito, "");
  		}
		}
    break;
		
    case "CERCA-DO":
    $strError = "";
    
		$objUtility->sessionVarUpdate($table."parent", $parent);
		$objUtility->sessionVarUpdate($table."tblparent", $tblparent);
    $objUtility->sessionVarUpdate($table."colfilter", $colfilter);
    
    if(function_exists(str_replace($config_table_prefix,"", $table)."_before_search")) eval(str_replace($config_table_prefix,"", $table)."_before_search();");

	  $result = mysql_query("SELECT $colfilter FROM `".$table."`");
    
    $j=0;
    echo $_POST['url'];
    while ($j < mysql_num_fields($result)) {    
      $field = mysql_fetch_field($result);
      $key=$field->name;
      $op="op_".$key;
      $op=$_POST[$op];
      if($op!="" || $_POST[$key]!="") {
        if($op=="") $op="=";
        if($op=="LIKE") $_POST[$key]="%".$_POST[$key]."%";
        
        if($key!="id" && !strpos($key, "_hidden")) {
          if(mysql_field_type($result,$j)=="date") {
            $anno="anno".$key;
            $mese="mese".$key;
            $giorno="giorno".$key;
            
            $anno2="anno".$key."_ZZZ";
            $mese2="mese".$key."_ZZZ";
            $giorno2="giorno".$key."_ZZZ";
            $op2="op2_".$key;
            $op2=$_POST[$op2];
            
            $_POST[$key]=$_POST[$anno]."-".$_POST[$mese]."-".$_POST[$giorno];
            $data2=$_POST[$anno2]."-".$_POST[$mese2]."-".$_POST[$giorno2];
            
            if($_POST[$key]!="0000-00-00") $sql.="(".$key." ".$op." '".$_POST[$key]."' OR ".$key." ".$op." '".htmlEDtiny($_POST[$key])."') AND ";
            if($data2!="0000-00-00") $sql.=$key." ".$op2." '".$data2."' AND ";
          } else if(strpos($key, "_file") && $_POST[$key]!="") {
            if($_POST[$key]=='0') $sql.=$key." = '0' AND ";
            if($_POST[$key]=='1') $sql.=$key." <> '0' AND ";
          } else {
            $sql.="(".$key." ".$op." '".$_POST[$key]."' OR ".$key." ".$op." '".htmlEDtiny($_POST[$key])."') AND ";
          }
        }
      }
      $j++;
    }
    $sql="($sql)";
    $sql=str_replace(" AND )", ")", $sql); 

    //echo $sql;exit;

    $objUtility->sessionVarUpdate($table."srcWhere",$sql);
    
		if($parent!="") {
      $result = mysql_query("SELECT * FROM `$tblparent` ");
      $field1=mysql_field_name($result,1);
      $field2=mysql_field_name($result,2);
      
      $tmptbl=str_replace("id_", "", $field1);
      $objUtility->sessionVarUpdate("table", $config_table_prefix.$tmptbl);
      $objUtility->sessionVarUpdate("idmod", $parent);
      $objUtility->sessionVarUpdate("action", "upd");
    }
		
		if(function_exists(str_replace($config_table_prefix,"", $table)."_after_search")) eval(str_replace($config_table_prefix,"", $table)."_after_search();");
		
		if ($strError) {
			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
		} else {
			$strEsito = "Operazione eseguita correttamente";
			?><script>location.href = "<?php echo $currentPage."#".$table; ?>";</script><?
		}
		break;
		
		case "CATEGORIE-MOVEUP-DO":
  	  $arr_intid=explode("#", $intId);
		  $next=$arr_intid[1];
		  $intId=$arr_intid[0];

      $sql="SELECT Ordinamento FROM `".$table."` WHERE id='$intId'";
      $result = mysql_query($sql);
      $row=mysql_fetch_array($result);
  		
  		$id1=$intId;
      $ord1=$row['Ordinamento'];
  		
  		$sql="SELECT id,Ordinamento FROM `".$table."` WHERE id='$next' ORDER BY Ordinamento DESC" ;
      $result = mysql_query($sql);
      $row=mysql_fetch_array($result);
      
      $id2=$row['id'];
      $ord2=$row['Ordinamento'];
      
      $sql="UPDATE `".$table."` SET Ordinamento='$ord2' WHERE id='$id1'" ;
      $result = mysql_query($sql);
      
      $sql="UPDATE `".$table."` SET Ordinamento='$ord1' WHERE id='$id2'" ;
      $result = mysql_query($sql);
      		
      if ($strError) {
  			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
  			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
  		} else {
  			$strEsito = "Operazione eseguita correttamente";
  			?><script>location.href = "<?php echo $currentPage."#".$table; ?>";</script><?
  			//echo $sql;
  		}  
		break;
		
		case "CATEGORIE-MOVEDOWN-DO":
		  $arr_intid=explode("#", $intId);
		  $next=$arr_intid[1];
		  $intId=$arr_intid[0];
		  
      $sql="SELECT Ordinamento FROM `".$table."` WHERE id='$intId'";
      $result = mysql_query($sql);
      $row=mysql_fetch_array($result);
  		
  		$id1=$intId;
      $ord1=$row['Ordinamento'];
  		
  		$sql="SELECT id,Ordinamento FROM `".$table."` WHERE id='$next' ORDER BY Ordinamento ASC" ;
      $result = mysql_query($sql);
      $row=mysql_fetch_array($result);
      
      $id2=$row['id'];
      $ord2=$row['Ordinamento'];
      
      $sql="UPDATE `".$table."` SET Ordinamento='$ord2' WHERE id='$id1'" ;
      $result = mysql_query($sql);
      
      $sql="UPDATE `".$table."` SET Ordinamento='$ord1' WHERE id='$id2'" ;
      $result = mysql_query($sql);
      		
      if ($strError) {
  			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
  			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
  		} else {
  			$strEsito = "Operazione eseguita correttamente";
  			?><script>location.href = "<?php echo $currentPage."#".$table; ?>";</script><?
  			//echo $sql;
  		}  
		break;
		
		case "ORDER-DO":
      
      if(function_exists(str_replace($config_table_prefix,"", $table)."_before_order")) eval(str_replace($config_table_prefix,"", $table)."_before_order();");
      
      $t="";
      if($_SESSION[('tmp_order'.$table)]==str_replace("***","_",$intId)) $t=$_SESSION[('tmp_ordert'.$table)];
      
      $_SESSION[('tmp_order'.$table)]=str_replace("***","_",$intId);
      
      if($t=="") $t1="ASC";
      if($t=="ASC") $t1="DESC";
      if($t=="DESC") {
        $t1="";
        $_SESSION[('tmp_order'.$table)]="id DESC";
      }
      
      $_SESSION[('tmp_ordert'.$table)]=$t1;
      
      if(function_exists(str_replace($config_table_prefix,"", $table)."_after_order")) eval(str_replace($config_table_prefix,"", $table)."_after_order();");
      
      if ($strError) {
  			$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
  			$objHtml->adminPageRedirect($currentPage, $strEsito, "");
  		} else {
  			$strEsito = "Operazione eseguita correttamente";
  			?><script>location.href = "<?php echo $currentPage."#".$table; ?>";</script><?
  			//echo $sql;
  		}  
		break;
		
		case "PAGE-GOTO":
    if($intId!="s" && $intId!="ns") {
      $objUtility->sessionVarUpdate($table."currpag", $intId);
      //$objUtility->sessionVarUpdate($table."currpagSel", "0");
    } else {
      $objUtility->sessionVarUpdate($table."currpagSel", "1");
    }
    
    if($intId=="ns") $objUtility->sessionVarUpdate($table."currpagSel", "0");
     
    ?><script>location.href = "<?php echo $currentPage."#".$table; ?>";</script><?
		exit;
    break;
		
		case "BTN-CLICK":
      if(function_exists(str_replace($config_table_prefix,"", $table)."_$intId")) eval(str_replace($config_table_prefix,"", $table)."_$intId();");
      break;
      
    case "BTN-CLICK-FUN":
      if(function_exists($intId)) eval($intId."(\$table);");
      break;
		
	// ******************************************************************************************
	
	
		
}	

?>