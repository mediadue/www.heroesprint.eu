<?php
  
  
  
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
  $conn = $objDb->connection($objConfig);
  
  function addFile($index,$id="") {
  	global $config_table_prefix;
    $objUtility =& new Utility;
  	if($_GET['table']!="") {$strDestDir = $objUtility->getPathResourcesDynamicAbsolute();$isPrivate=0;}else{$strDestDir = $objUtility->getPathResourcesPrivateAbsolute();$isPrivate=1;}
  	
  	$isUploadOk = false;
  	$strUnique = $objUtility->getFilenameUnique();
  	$strDestFile = $strUnique;
  	
    $post_name=$_FILES[$index]["name"];
  	$post_type=$_FILES[$index]["type"];
  	$post_tmpname=$_FILES[$index]["tmp_name"];
  	
  	if ($post_name) 
  	{
  		//$strExt = $objUtility->getExtFromMime($post_type);
      
      if($id!="y" && $id!="0" && $id!="") {
      	$query = mysql_query("SELECT * FROM `".$config_table_prefix."oggetti` WHERE id='$id' ");
        if($arr=mysql_fetch_array($query)) {
          unlink($arr['path'].$arr['nome'].".".$arr['ext']);
          $strDestFile=$objUtility->getFilenameUnique();
          $query = mysql_query("UPDATE `".$config_table_prefix."oggetti` SET nome='$strDestFile' WHERE id='$id' ");
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
        
  			if($id!="y" && $id!="0" && $id!="") {
  			  $sql="UPDATE `".$config_table_prefix."oggetti` SET ext='$strOggettoExt', originalname='$strOggettoOriginalname' WHERE id='$id'  ";
          $query=mysql_query($sql);
          return $id;
  			} else {
          $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,ext,path,originalname,isprivate) VALUES ('$strDestFile','$strOggettoExt','$strOggettoPath','$strOggettoOriginalname', $isPrivate) ";
          $query=mysql_query($sql);
    			$id_oggetto=mysql_insert_id();
    			return $id_oggetto;
        }
  		}
  	}
  }
  
 	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}
	session_start();

	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		// Usually we'll only get an invalid upload if our PHP.INI upload sizes are smaller than the size of the file we allowed
		// to be uploaded.
		header("HTTP/1.1 500 File Upload Error");
		if (isset($_FILES["Filedata"])) {
			echo $_FILES["Filedata"]["error"];
		}
		exit(0);
	}
	//$target = $objUtility->getPathResourcesDynamicAbsolute()."uploaded/".basename($_FILES['Filedata']['name']) ;
	//move_uploaded_file($_FILES["Filedata"]["tmp_name"], $target);
  $myId=addFile("Filedata",$_GET['id']);
	
  if($_GET['table']!="") {
    if($_GET['id']!="0") {
      $query = mysql_query("SELECT `".$_GET['field']."` FROM `".$_GET['table']."` WHERE id='".$_GET['row']."' ");
      $arr=mysql_fetch_array($query);
      $arr=$arr[$_GET['field']];
      
      $arr=explode(";", $arr);
      
    	for($z=0;$z<count($arr);$z++) {
        if($arr[$z]==$myId) $noAdd=1;
      }
    	
    	$arr=implode(";", $arr);
    	
    	if($noAdd!=1) {
        if($arr!='0' && trim($arr)!="") {$arr=$arr.";".$myId;}else{$arr=$myId;}
      	
        $sql="UPDATE `".$_GET['table']."` SET `".$_GET['field']."`='$arr' WHERE id='".$_GET['row']."'  ";
        $query=mysql_query($sql);
    	}
    	
    	$key=$_GET['field'];
    	
      $substr="";
    	if(strpos($key, "_thm")) {
        $substr=substr($key, strpos($key, "_thm"), strlen($key)-strpos($key, "_thm"));
      }
    
      if($substr!="") {
        $substr=str_replace("_thm", "", $substr);
        $dim=substr($substr, 0, strpos($substr, "_"));
        $destf=str_replace($dim."_", "", $substr);
        $dim=explode("X", $dim);
        $thumb=imgResizeByID($myId,$dim[0],$dim[1],$addObject="1");
        $old_data="SELECT `$destf` FROM `".$_GET['table']."` WHERE id='".$_GET['row']."' ";
        $query=mysql_query($old_data);
        $old_data=mysql_fetch_array($query);
        if($old_data[0]!="" && $old_data[0]!="0") $thumb=$old_data[0].";".$thumb;
        $sql="UPDATE `".$_GET['table']."` SET `$destf`='$thumb' WHERE id='".$_GET['row']."'  ";
        $query=mysql_query($sql);
      }
  	}
  	
  	if($_GET['id']=="0") {
      if($_SESSION['tmp_uploader_n']=="1") {
        $query = mysql_query("INSERT INTO `".$_GET['table']."` (`".$_GET['field']."`) VALUES ('".$myId."')");
    	  $ttid=mysql_insert_id();
  
    	  $key=$_GET['field'];
      	
      	
        $result = mysql_query("SELECT Ordinamento FROM `".$_GET['table']."` ORDER BY Ordinamento DESC ");
  			  
        if($result) {
          $r=mysql_fetch_array($result);
          
          $result2 = mysql_query("SELECT Ordinamento FROM `".$_GET['table']."` WHERE id='$ttid' ");
          $r2=mysql_fetch_array($result2);
          
          if($r2['Ordinamento']==0) {
            $sql="UPDATE `".$_GET['table']."` SET Ordinamento='".($r['Ordinamento']+10)."' WHERE id='".$ttid."'  ";
            $query=mysql_query($sql);
          }
        }

        
        $substr="";
      	if(strpos($key, "_thm")) {
          $substr=substr($key, strpos($key, "_thm"), strlen($key)-strpos($key, "_thm"));
        }
      
        if($substr!="") {
          $substr=str_replace("_thm", "", $substr);
          $dim=substr($substr, 0, strpos($substr, "_"));
          $destf=str_replace($dim."_", "", $substr);
          $dim=explode("X", $dim);
          $thumb=imgResizeByID($myId,$dim[0],$dim[1],$addObject="1");
          $sql="UPDATE `".$_GET['table']."` SET `$destf`='$thumb' WHERE id='".$ttid."'  ";
          $query=mysql_query($sql);
        }
        
        $parent=$_GET['parent'];
        $tblparent=urldecode($_GET['tblparent']);
        if($parent!="" && $tblparent!="") {
          $result = mysql_query("SELECT * FROM `$tblparent` ");
          $field1=mysql_field_name($result,1);
          $field2=mysql_field_name($result,2);
          
          $sql="INSERT INTO `$tblparent` ($field1,$field2) VALUES ('$parent','$ttid') "; 
          $p_res=mysql_query($sql);
        }
      }
      
      if($_SESSION['tmp_uploader_n']=="") {
        $query = mysql_query("UPDATE `".$_GET['table']."` SET `".$_GET['field']."`='$myId' WHERE id='".$_GET['row']."'");
  
    	  $key=$_GET['field'];
      	
        $result = mysql_query("SELECT Ordinamento FROM `".$_GET['table']."` ORDER BY Ordinamento DESC ");
  			  
        if($result) {
          $r=mysql_fetch_array($result);
          
          $result2 = mysql_query("SELECT Ordinamento FROM `".$_GET['table']."` WHERE id='$ttid' ");
          $r2=mysql_fetch_array($result2);
          
          if($r2['Ordinamento']==0) {
            $sql="UPDATE `".$_GET['table']."` SET Ordinamento='".($r['Ordinamento']+10)."' WHERE id='".$ttid."'  ";
            $query=mysql_query($sql);
          }
        }
        
        
        $substr="";
      	if(strpos($key, "_thm")) {
          $substr=substr($key, strpos($key, "_thm"), strlen($key)-strpos($key, "_thm"));
        }
      
        if($substr!="") {
          $substr=str_replace("_thm", "", $substr);
          $dim=substr($substr, 0, strpos($substr, "_"));
          $destf=str_replace($dim."_", "", $substr);
          $dim=explode("X", $dim);
          $thumb=imgResizeByID($myId,$dim[0],$dim[1],$addObject="1");
          $sql="UPDATE `".$_GET['table']."` SET `$destf`='$thumb' WHERE id='".$_GET['row']."'  ";
          $query=mysql_query($sql);
        }
        
        $_SESSION['tmp_uploader_n']="1";
      }
  	}

  } else {
     array_push($_SESSION['tmp_arrOggetti'],$myId);
  }
  
  if($myId!="") {
    $myFile=retRow("oggetti",$myId);
    $myFile=$myFile['nome'].".".$myFile['ext'];
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>SWFUpload v2.0 Multi-Upload Demo</title>
</head>
<body>
  <p>#<?php echo $myFile;?>##</p>
</body>
</html>