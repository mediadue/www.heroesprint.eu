<?php
session_start();
include ("_docroot.php");
include (SERVER_DOCROOT."logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$conn = $objDb->connection($objConfig);
global $config_table_prefix;
$dbname = $objConfig->get("db-dbname");

function get_os() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return 'Windows';
    } else {
        return 'Linux';
    }
}

if(get_os()=="Windows") {
    $identify="identify.exe";
    $convert="convertt.exe";
    $gs="gs.exe";
    $mogrify="mogrify.exe";
    $icc_path = SERVER_DOCROOT . "icc/";
}

if(get_os()=="Linux") {
    $identify="identify";
    $convert="convert";
    $gs="gs";
    $mogrify="mogrify";
    $icc_path="/var/www/vhosts/heroesprint.eu/upload.heroesprint.eu/icc/";
}

function get_file_extension($filename) {
    return  strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function removeFileExtension($filename) {
    $fileParts = pathinfo($filename);
    return $fileParts['filename'];
}

function findSmallestBox($boxTypes, $dimensions) {
    $smallestBox = null;
    $smallestBoxArea = PHP_INT_MAX;

    foreach ($boxTypes as $boxType) {
        if (!isset($dimensions[$boxType])) {
            continue;
        }

        $width = $dimensions[$boxType]['width'];
        $height = $dimensions[$boxType]['height'];
        $area = $width * $height;

        if ($area > 0 && $area < $smallestBoxArea) {
            $smallestBox = $boxType;
            $smallestBoxArea = $area;
        }
    }

    return $smallestBox;
}

function CronJob(){
  global $config_table_prefix; 
  global $objUtility;
  global $convert;
  
  $sql1="SELECT COUNT(id) as npags, GROUP_CONCAT(id SEPARATOR ';') as idconc, eu.* FROM ".$config_table_prefix."ecommerce_upload eu WHERE eu.converting=1 GROUP BY eu.`skey` ORDER BY eu.`priority` DESC, eu.`data` ASC";
  $q=mysql_query($sql1);
  $rs=$objUtility->buildRecordset($q);
  
  reset($rs);
  while (list($key, $row) = each($rs)) {
    $fexist=file_exists($row["convFile"]);
    if($row["converting"]==1 || ($row["converting"]==2 && !$fexist)) {
      $pid=$row["pid"];
      $pexist=is_process_running($pid);
      if($pexist==false && $fexist){
        $info = pathinfo($row["convFile"]);
        if($info['extension']!="pdf"){
          $pdfFile = $info['dirname'] ."/". $info['filename'] . "." . "pdf";
          //$cmd="convert -limit thread 12 ".$row["convFile"]." -quality 100 -background white -alpha remove -fuzz 10% -fill \"#CB2131\" -opaque \"#FE0000\" ".$pdfFile; 
          $cmd="$convert -limit thread 12 ". $row["convFile"] ." -quality 100 -background white -alpha remove ".$pdfFile;
          //$cmd="convert -limit thread 12 ".$row["convFile"]." -density ".$row["newDpi"]." -quality 100 -background white -alpha remove ".$pdfFile;          
          $pid2=run_process($cmd);
          
          $sql="UPDATE ".$config_table_prefix."ecommerce_upload SET converting='1', pid='".$pid2."', convFile='".$pdfFile."' WHERE id='".$row["id"]."'";
          mysql_query($sql);
        }else{
          $jpgFile = $info['dirname'] ."/". $info['filename'] . "." . "jpg";
          @unlink($jpgFile);
          
          $sql="UPDATE ".$config_table_prefix."ecommerce_upload SET converting='2' WHERE id='".$row["id"]."'"; 
          mysql_query($sql); 
        }
      }elseif($pexist==false && !$fexist){
        $sql="UPDATE ".$config_table_prefix."ecommerce_upload SET converting='-1' WHERE id='".$row["id"]."'";
        mysql_query($sql);     
      }
    }    
  } 
  
  return false; 
}


//$targetPath = $objUtility->getPathResourcesDynamicAbsolute() . "users_file" . "/" . "83" . "/";
/*
$icc_path = SERVER_DOCROOT . "icc/";
echo $icc_path;
exit;
*/


if(isset($_POST['hrsNoteSave'])) {
	$note=$_POST["note"];
  $idOrdine=$_POST['ordine'];
	
  $objNote=unserialize(stripslashes($note));
  
  if($objNote["note"]!="") $txtNote=stripslashes(utf8_decode($objNote["note"]))."\r\n\r\n";
  
  $sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET note_text = '".addslashes(utf8_decode($objNote["note"]))."' WHERE id='".$idOrdine."'";
  mysql_query($sql);
 
  while (list($key, $row) = each($objNote)) {
    if($key!="note"){
      $rs=retRow("ecommerce_upload",$row["id"]);
      
      $sql="UPDATE ".$config_table_prefix."ecommerce_upload SET note_text = '".addslashes(utf8_decode($row["text"]))."' WHERE id='".$row["id"]."'";
      mysql_query($sql);
    }  
  } 
  
    $sql1="SELECT COUNT(id) as npags, GROUP_CONCAT(id SEPARATOR ';') as idconc, eu.* FROM ".$config_table_prefix."ecommerce_upload eu WHERE eu.id_ecommerce_ordini='".$idOrdine."' GROUP BY eu.`skey` ORDER BY eu.`priority` DESC, eu.`data` ASC";
    $q=mysql_query($sql1);
    $rs=$objUtility->buildRecordset($q);
    
    reset($rs);
    while (list($key, $row) = each($rs)) {
      if($row["note_text"]!="") $txtNote.="- ".basename($row["orFile"]).": "."\r\n\r\n".stripslashes(utf8_decode($row["note_text"])). "\r\n\r\n\r\n\r\n";  
    }
  
	$user_dir=$objUtility->getPathResourcesDynamicAbsolute() . "users_file" . "/" . $idOrdine . "/";
	file_put_contents($user_dir.$idOrdine."_leggimi.txt",$txtNote);
		
	echo ln("Salvataggio avvenuto correttamente!");
	
	exit;
}

if(isset($_POST['priority']) && isset($_POST['idfile'])) {
  $pri=$_POST['priority'];
  $id=$_POST['idfile'];
  
  $sql="UPDATE ".$config_table_prefix."ecommerce_upload SET priority = '".$pri."' WHERE id='".$id."'";
  mysql_query($sql);
  
  echo "1";	
	
	exit;
}

if(isset($_POST['setIcc']) && isset($_POST['idfile'])) {
  $corr=$_POST['setIcc'];
  $id=$_POST['idfile'];
  
  $rs=retRow("ecommerce_upload",$id);
  $npag=$rs["tot_pag"];
  
  $oldIcc=$rs["icc"];
  $oldColorspace=$rs["colorspace"];
  $orcolorspace=$rs["orColorspace"];
  
  if($corr=="1") {
    $icc="";
    $colorspace="";  
  }elseif($corr=="2"){
    $iccF="GRACoL2006_Coated1v2.icc";
    $icc=SERVER_DOCROOT."icc/GRACoL2006_Coated1v2.icc";
    $colorspace="CMYK";    
  }elseif($corr=="3"){
    $iccF="AdobeRGB1998.icc";
    $icc=SERVER_DOCROOT."icc/AdobeRGB1998.icc";
    $colorspace="RGB";    
  }elseif($corr=="4"){
    $iccF="sRGB-IEC61966-2.1.icc";
    $icc=SERVER_DOCROOT."icc/sRGB-IEC61966-2.1.icc";
    $colorspace="sRGB";    
  }  
  
  if($oldIcc==$icc && $oldColorspace==$colorspace) {echo "1"; exit;}
  
  $addCs="";
  $addICC="";
  
  if($colorspace!="") $addCs= "-colorspace ".$colorspace;
  if($icc!="") {$addICC= "-profile ".$icc; $addStrip="-strip";}
  
  $targetFile=$rs["orFile"];
  $prevFile=str_replace("_CMYK_prev.jpg", "_prev.jpg", $rs["prevFile"]);
  $prevFile=str_replace("_sRGB_prev.jpg", "_prev.jpg", $prevFile);
  $prevFile=str_replace("_RGB_prev.jpg", "_prev.jpg", $prevFile);
  $prevFile=str_replace("_prev.jpg","_".$colorspace."_prev.jpg", $prevFile); 
  $prevFile=str_replace("__prev.jpg", "_prev.jpg", $prevFile);
  
  if(!file_exists($prevFile)){
    $density=72;
    $resol=1024;
    $append="";
    $addOldICC="";
    //if($colorspace=="RGB") $addCs=str_replace("RGB", "sRGB", $addCs);
    
    if($orcolorspace=="RGB") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/AdobeRGB1998.icc";
    }elseif($orcolorspace=="CMYK") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/GRACoL2006_Coated1v2.icc";    
    }elseif($orcolorspace=="SRGB") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/sRGB-IEC61966-2.1.icc";    
    }
    
    $addOldCs="-colorspace ".$orcolorspace;

    if($colorspace=="RGB") {
      $addOldCs="";
      $addCs="";
    }
    
    if($npag>1){
      $append="+append";
    }else{
      $append="";
      $resol=1024;  
    }
    //exec("magick convert ".$targetFile." ".$addStrip." ".$addCs." ".$addICC." -background white -flatten -alpha deactivate +append -density ".$density." -quality 100 -resize ".$resol." ".$prevFile);  
    //exec("magick convert -density ".$density." -quality 100 ".$targetFile." -alpha transparent -clip -alpha opaque +clip -resize ".$resol." ".$append." ".$addStrip." ".$addCs." ".$addICC." ".$prevFile);
    //exec("magick convert -density ".$density." ".$targetFile." ".$addStrip."  ".$addOldCs." -intent Relative -black-point-compensation ".$addOldICC." -background white -alpha remove -resize ".$resol." ".$addCs." -intent Relative -black-point-compensation ".$addICC." -quality 100 ".$append." ".$prevFile); 
    //exec("gs -sDEVICE=jpeg -sICCProfilesDir=\"/var/www/vhosts/heroesprint.eu/upload.heroesprint.eu/icc/\" -sDefaultRGBProfile=\"AdobeRGB1998.icc\" -sDefaultCMYKProfile=\"GRACoL2006_Coated1v2.icc\" -dOverrideICC=true -sOutputICCProfile=\"AdobeRGB1998.icc\" -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT -dMaxBitmap=500000000 -dAlignToPixels=0 -dGridFitTT=2 -dTextAlphaBits=4 -dGraphicsAlphaBits=1 -dJPEGQ=85 -r72 -sOutputFile=\"".$prevFile."\" ".$targetFile);
    $gs_prev_cmd = "$gs -sDEVICE=jpeg -sICCProfilesDir=\"".$icc_path."\" -sDefaultRGBProfile=\"AdobeRGB1998.icc\" -sDefaultCMYKProfile=\"GRACoL2006_Coated1v2.icc\" -dOverrideICC=true -sOutputICCProfile=\"AdobeRGB1998.icc\" -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT -dMaxBitmap=500000000 -dAlignToPixels=0 -dGridFitTT=2 -dTextAlphaBits=1 -dGraphicsAlphaBits=1 -dJPEGQ=85 -r72 -sOutputFile=\"".$prevFile."\" -f ".$targetFile;
    exec($gs_prev_cmd);
    logToFile($gs_prev_cmd,"log.txt");   
  }
  
  $sql="UPDATE ".$config_table_prefix."ecommerce_upload SET colorspace='".$colorspace."', icc='".$icc."', prevFile='".$prevFile."'  WHERE id='".$id."'";
  mysql_query($sql);
  
  echo "1";
  exit;
}

if(isset($_POST['del'])){
  $id=$_POST['del'];
  $arrid=explode(";",$id);
  if(count($arrid)==0) array_push($arrid, $id);
  
  $stopped="-1";
  while (list($key, $row) = each($arrid)) {
    $f=retRow("ecommerce_upload",$row);
    $p=$row["num_pag"]-1;
    
    if($f["approved"]=="1" && $f["converting"]=="2" && $_POST['email']!="mediadue") {
      echo "-1";
      exit;
    }
  
    if($f["converting"]=="1" && $f["pid"]!="-1" && $f["pid"]!=$stopped) {
      $stopped=$f["pid"];
      stop_process($f["pid"]);
      sleep(1);
    }
    
    $prevFile=$f["prevFile"];
    $prevFile=str_replace("_CMYK_prev.jpg", "_prev.jpg", $prevFile);
    $prevFile=str_replace("_sRGB_prev.jpg", "_prev.jpg", $prevFile);
    $prevFile=str_replace("_RGB_prev.jpg", "_prev.jpg", $prevFile);
    
    if(file_exists($prevFile)) @unlink($prevFile);
    
    $prevFileCMYK=str_replace("_prev.jpg", "_CMYK_prev.jpg", $prevFile);
    $prevFileSRGB=str_replace("_prev.jpg", "_sRGB_prev.jpg", $prevFile);
    $prevFileRGB=str_replace("_prev.jpg", "_RGB_prev.jpg", $prevFile);
    
    if(file_exists($prevFileCMYK)) @unlink($prevFileCMYK);
    if(file_exists($prevFileRGB)) @unlink($prevFileRGB);
    if(file_exists($prevFileSRGB)) @unlink($prevFileSRGB);
    
    if(file_exists($f["orFile"])) @unlink($f["orFile"]);
    if(file_exists($f["orFileEx"])) @unlink($f["orFileEx"]);
    if(file_exists($f["prevFile"])) @unlink($prevFile);
    if(file_exists($f["convFile"])) @unlink($f["convFile"]);
    
    $sql="DELETE FROM ".$config_table_prefix."ecommerce_upload WHERE id='".$row."'";
    mysql_query($sql);
  }
  
  echo "1";
  
  exit;
}

if(isset($_POST['ecomm_approved'])){
  $id=$_POST['ecomm_approved'];
  $arrid=explode(";",$id);
  if(count($arrid)==0) array_push($arrid, $id);
  
  $rs=retRow("ecommerce_upload",$arrid[0]);
  
  $orFile=$rs["orFile"];
  $prevFile=$rs["prevFile"];
  $targetPath = $objUtility->getPathResourcesDynamicAbsolute() . "users_file" . "/" . $rs['id_ecommerce_ordini'] . "/";
  $fname=basename($orFile); 
  
  if(!file_exists($targetPath)) {
    @mkdir(str_replace('//','/',$targetPath), 0755, true);  
  }
  
  $area=0;
  $pagc=-1; 
  while (list($key, $row) = each($arrid)) {
    $pagc++;
    
    $rs=retRow("ecommerce_upload",$row);
    $area=$rs["area_mq"]; 
    $np=$rs["num_pag"];
    $p="[".($rs["num_pag"]-1)."]";
    if(count($arrid)==1) $p="";
  
    $w=$rs["w_mm"];
    $h=$rs["w_mm"];
    
    $icc=$rs["icc"];
    $iccF=basename($icc);
    
    $orcolorspace=$rs["orColorspace"];
    $colorspace=$rs["colorspace"];
    
    
    $addCs="";
    $addICC="";
    $addStrip="";
    if($colorspace!="") $addCs= "-colorspace ".$colorspace;
    if($icc!="") {$addICC= "-profile ".$icc; $addStrip="-strip";}
    
    $addOldICC="";
    //if($colorspace=="RGB") $addCs=str_replace("RGB", "sRGB", $addCs);
    
    if($orcolorspace=="RGB") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/AdobeRGB1998.icc";
    }elseif($orcolorspace=="CMYK") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/GRACoL2006_Coated1v2.icc";    
    }elseif($orcolorspace=="SRGB") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/sRGB-IEC61966-2.1.icc";    
    }
    
    $addOldCs="-colorspace ".$orcolorspace;

    if($colorspace=="RGB") {
      $addOldCs="";
      $addCs="";
    }
    
    if($area<5) $dpi=300;
    if($area>=5) $dpi=150;
    if($area>20) $dpi=72;
    
    $ss="";
    if($w>5000 || $h>5000) {
      $sw=2500/$w;
      $sh=2500/$h;
      
      if($sw<$sh) $s=$sw; else $s=$sh;
      $s=floor($s*100);
      if($dpi>150) $dpi=150;
      
      $ss="-resize ".$s."%";
    }
    
    $maxMP=180;
    $mps=sqrt(($maxMP*1000000)/($rs["w_pixel"]*$rs["h_pixel"]));
    $newPxW=floor($rs["w_pixel"]*$mps);
    $newPxH = floor($rs["h_pixel"]*$mps);
    
    $newDPI=floor($newPxW/($w/25.4));
    
    $prevFile=$rs["prevFile"];
    $prevFile=str_replace("_CMYK_prev.jpg", "_prev.jpg", $prevFile);
    $prevFile=str_replace("_sRGB_prev.jpg", "_prev.jpg", $prevFile);
    $prevFile=str_replace("_RGB_prev.jpg", "_prev.jpg", $prevFile);
    
    if(file_exists($prevFile) && $prevFile!=$rs["prevFile"] && $pagc<1) @unlink($prevFile);
    
    $prevFileCMYK=str_replace("_prev.jpg", "_CMYK_prev.jpg", $prevFile);
    $prevFileRGB=str_replace("_prev.jpg", "_RGB_prev.jpg", $prevFile);
    $prevFileSRGB=str_replace("_prev.jpg", "_sRGB_prev.jpg", $prevFile);
    
    if(file_exists($prevFileCMYK) && $prevFileCMYK!=$rs["prevFile"] && $pagc<1) @unlink($prevFileCMYK);
    if(file_exists($prevFileRGB) && $prevFileRGB!=$rs["prevFile"] && $pagc<1) @unlink($prevFileRGB);
    if(file_exists($prevFileSRGB) && $prevFileSRGB!=$rs["prevFile"] && $pagc<1) @unlink($prevFileSRGB);
    
    //$approvedPath=$targetPath.$fname."_approved".$p.".pdf";
    $approvedPath=$targetPath. removeFileExtension($fname) ."_approved".$p.".jpg";
    //if($colorspace=="RGB") $addCs=str_replace("RGB", "sRGB", $addCs);
    //$cmd="magick convert ".$addStrip." -compress JPEG -density 300 -resize ".$newPxW." -quality 100 ".$addCs." ".$addICC." -background white -alpha remove -type TrueColor ".$orFile.$p." ".$approvedPath;
    //$cmd="magick convert ".$addStrip." -density 300 -quality 100 -resize ".$newPxW." ".$addCs." ".$addICC." -background white -alpha remove ".$orFile.$p." ".$approvedPath;
    //$cmd="magick convert ".$orFile.$p." ".$addStrip." ".$addCs." ".$addICC." -background white -flatten -alpha deactivate -density 300 -quality 100 -resize ".$newPxW." ".$approvedPath; 
    //$cmd="magick convert -density 300 -quality 100 ".$orFile.$p." -alpha transparent -clip -alpha opaque +clip -resize ".$newPxW." ".$addCs." ".$addStrip." ".$addICC." ".$approvedPath;
    //$cmd="magick convert -density 300 ".$orFile.$p." ".$addStrip." ".$addOldCs." -intent Relative -black-point-compensation ".$addOldICC." -background white -alpha remove -resize ".$newPxW." ".$addCs." -intent Relative -black-point-compensation ".$addICC." -quality 100 ".$approvedPath;
    
    //$cmd="$gs -sDEVICE=jpeg -sICCProfilesDir=\"".$icc_path."\" -sDefaultRGBProfile=\"AdobeRGB1998.icc\" -sDefaultCMYKProfile=\"GRACoL2006_Coated1v2.icc\" -dOverrideICC=true -sOutputICCProfile=\"AdobeRGB1998.icc\" -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT -dMaxBitmap=500000000 -dAlignToPixels=0 -dGridFitTT=2 -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=100 -g".$newPxW."x".$newPxH." -r".$newDPI." -sPageList=".$np." -sOutputFile=\"".$approvedPath."\" ".$orFile;
    $cmd="$gs -sDEVICE=jpeg -sICCProfilesDir=\"".$icc_path."\" -sDefaultRGBProfile=\"AdobeRGB1998.icc\" -sDefaultCMYKProfile=\"GRACoL2006_Coated1v2.icc\" -dOverrideICC=true -sOutputICCProfile=\"AdobeRGB1998.icc\" -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT -dMaxBitmap=500000000 -dAlignToPixels=0 -dGridFitTT=2 -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=100 -r".$newDPI." -dFirstPage=".$np." -dLastPage=".$np." -sOutputFile=\"".$approvedPath."\" -f ".$orFile;
    //exec("$gs -sDEVICE=jpeg -sICCProfilesDir=\"".$icc_path."\" -sDefaultRGBProfile=\"AdobeRGB1998.icc\" -sDefaultCMYKProfile=\"GRACoL2006_Coated1v2.icc\" -dOverrideICC=true -sOutputICCProfile=\"AdobeRGB1998.icc\" -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT -dMaxBitmap=500000000 -dAlignToPixels=0 -dGridFitTT=2 -dTextAlphaBits=4 -dGraphicsAlphaBits=1 -dJPEGQ=85 -r72 -sOutputFile=\"".$prevFile."\" -f ".$targetFile);
    //$cmd="gs -sDEVICE=pdfimage24 -dPDFSETTINGS=/printer -sICCProfilesDir=\"/var/www/vhosts/heroesprint.eu/upload.heroesprint.eu/icc/\" -sDefaultRGBProfile=\"AdobeRGB1998.icc\" -sDefaultCMYKProfile=\"GRACoL2006_Coated1v2.icc\" -dOverrideICC=true -sOutputICCProfile=\"AdobeRGB1998.icc\" -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT -dMaxBitmap=500000000 -dAlignToPixels=0 -dGridFitTT=2 -dTextAlphaBits=4 -dGraphicsAlphaBits=1 -dJPEGQ=100 -r".$newDPI." -sPageList=".$np."  -sOutputFile=\"".$approvedPath."\" ".$orFile;
   
    $pid=run_process($cmd); 
    
    $sql="UPDATE ".$config_table_prefix."ecommerce_upload SET convFile='".$approvedPath."', approved='1', converting='1', pid='".$pid."', start_conv=NOW(), cmd='".addslashes($cmd)."', newDpi='".$newDPI."' WHERE id='".$row."'";
    mysql_query($sql);
  }
  
  exit;
}

if(isset($_POST['do_upload']) && isset($_POST['idord'])){
  $f=fname_onlyreadables($_POST['do_upload'],"_");
  $id=$_POST['idord'];
  $user_dir=$objUtility->getPathResourcesDynamicAbsolute() . "users_file" . "/" . $id . "/";
  
  $key=md5($user_dir.$f);
  $find=getTable("ecommerce_upload","","(skey='".$key."')"); 
  
  
  if(file_exists($user_dir.$f) && count($find)>0) {
    echo "-1";
  }elseif(!file_exists($user_dir.$f) && count($find)>0) {
    while (list($key1, $row1) = each($find)) {
      $ideu=$row1["id"];
      
      if(file_exists($row1["orFile"])) @unlink($row1["orFile"]);
      if(file_exists($row1["prevFile"])) @unlink($row1["prevFile"]);
      if(file_exists($row1["convFile"])) @unlink($row1["convFile"]);
      
      $sql="DELETE FROM ".$config_table_prefix."ecommerce_upload WHERE id='".$ideu."'";
      mysql_query($sql);
    } 
    echo "1";
  }elseif(file_exists($user_dir.$f) && count($find)==0) { 
    @unlink($user_dir.$f);
    echo "1";  
  }else{
    echo "1";  
  }
  exit;
}

if($_GET['cronJob']==1){
  CronJob();
  exit;
}

if(isset($_POST['email']) && isset($_POST['ordine'])){ 
  CronJob();
  
  $sqlEmail="";
  if($_POST['email']!="mediadue" && $_POST['email']!="ospite") $sqlEmail="u.email='".addslashes($_POST['email'])."' AND";
  
  $sql="SELECT u.email,u.id,o.codice_vendita,note_text FROM ".$config_table_prefix."ecommerce_ordini o INNER JOIN ".$config_table_prefix."users u ON o.user_hidden=u.id WHERE ".$sqlEmail." o.codice_vendita='".addslashes($_POST['ordine'])."'";
  $q=mysql_query($sql);
  $rs=$objUtility->buildRecordset($q);
  if(count($rs)>0){
    $user_dir=$objUtility->getPathResourcesDynamicAbsolute() . "users_file" . "/" . $rs[0]['codice_vendita'] . "/";
    $ordNote=$rs[0]['note_text'];
    
    if(!file_exists($user_dir)) {
      @mkdir(str_replace('//','/',$user_dir), 0755, true);  
    }
    
    $sql1="SELECT COUNT(id) as npags, GROUP_CONCAT(id SEPARATOR ';') as idconc, GROUP_CONCAT(converting SEPARATOR ';') as convconc, eu.* FROM ".$config_table_prefix."ecommerce_upload eu WHERE eu.id_ecommerce_ordini='".$rs[0]['codice_vendita']."' GROUP BY eu.`skey` ORDER BY eu.`priority` DESC, eu.`data` ASC";
    $q1=mysql_query($sql1);
    $rs1=$objUtility->buildRecordset($q1);
    
    if($rs1[0]['npags']>1){
      $convconc=explode(";", $rs1[0]['convconc']);
      $minconvconc=min($convconc);
      if($minconvconc>0) $rs1[0]['converting']=$minconvconc;
    }
    
    $rs1['note']=$ordNote;
    
    echo str_replace('\\/', '/', json_encode($rs1, 0));
  }else {
    echo "-1";
  }
  
  exit;
}

if((!isset($_POST['email1']) && $_POST['email1']!="mediadue" && $_POST['email1']!="ospite") || !isset($_POST['ordine1'])) {echo ln("Errore durante il caricamento del file. Ripetere la procedura.");exit;}

$email=urldecode($_POST['email1']);
$ordine=urldecode($_POST['ordine1']);

$emailSqlstr="";
if($email!="mediadue" && $email!="ospite") $emailSqlstr="u.email='".addslashes($email)."' AND";

$sql="SELECT u.email,u.id,o.codice_vendita FROM ".$config_table_prefix."ecommerce_ordini o INNER JOIN ".$config_table_prefix."users u ON o.user_hidden=u.id WHERE ".$emailSqlstr." o.codice_vendita='".addslashes($ordine)."'";

$q=mysql_query($sql);
$rs=$objUtility->buildRecordset($q);
if(count($rs)==0) exit;

$verifyToken = md5('pippo83' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$tmpdir = $objUtility->getFilenameUnique();
	$targetPath = $objUtility->getPathResourcesDynamicAbsolute() . "users_file" . "/" . $rs[0]['codice_vendita'] . "/";
  $targetFile =  str_replace('//','/',$targetPath) . fname_onlyreadables($_FILES['Filedata']['name'],"_");
	
  $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
  $fileTypes  = str_replace(';','|',$fileTypes);
  $typesArray = split('\|',$fileTypes);
  $fileParts  = pathinfo($_FILES['Filedata']['name']);
  
  $ext=strtolower($fileParts['extension']);
  if($ext=="php") {echo "0"; exit;}  
  
  if (in_array($ext,$typesArray)) {
    // Uncomment the following line if you want to make the directory if it doesn't exist
    @mkdir(str_replace('//','/',$targetPath), 0755, true);
    
    /*
    if(strtolower($fileParts['extension'])=="zip"){
	    $za = new ZipArchive();
	    $za->open($tempFile);
	    if($za->numFiles>0){
	      move_uploaded_file($tempFile,$targetFile);
	      echo ln("Numero file presenti nell'archivio: ") . $za->numFiles;  
	    }else{
	      echo "-1" //Errore ZIP;
	    }
    }
    */
    move_uploaded_file($tempFile,$targetFile);
    
    $crop_pdf=true;
    
    $infoF = pathinfo($targetFile);
    if($infoF['extension']=="jpg" || $infoF['extension']=="jpeg" || $infoF['extension']=="tiff" || $infoF['extension']=="tif" || $infoF['extension']=="png" || $infoF['extension']=="svg" || $infoF['extension']=="psd"){
      $pdfFile = $infoF['dirname'] ."/". $infoF['filename'] . ".pdf";
      
      $conv_cmd = "$convert -limit thread 12 ".$targetFile." -flatten -quality 100 -background white -alpha remove ".$pdfFile;
      exec($conv_cmd);
      logToFile($conv_cmd,"log.txt");  
      
      $newOrig=$infoF['dirname'] ."/". $infoF['filename'] . "_orig." . $infoF['extension'];
      rename($targetFile, $newOrig);
      $targetFile=$pdfFile;
      $crop_pdf=false;
    }
    
    //exec("magick convert ".$targetFile." ".$addStrip." ".$addCs." ".$addICC." -background white -flatten -alpha deactivate +append -density ".$density." -quality 100 -resize ".$resol." ".$prevFile);  
    //exec("magick convert -density ".$density." -quality 100 ".$targetFile." -alpha transparent -clip -alpha opaque +clip -resize ".$resol." ".$append." ".$addStrip." ".$addCs." ".$addICC." ".$prevFile);
    //exec("magick convert -density ".$density." ".$targetFile." ".$addStrip." ".$addOldCs." -intent Relative -black-point-compensation ".$addOldICC." -background white -alpha remove -resize ".$resol." ".$addCs." -intent Relative -black-point-compensation ".$addICC." -quality 100 ".$append." ".$prevFile);
    
    if($crop_pdf){
      //$gs_cmd="$gs -q -dQUIET -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress -dAutoRotatePages=/None -dEmbedAllFonts=true -dSubsetFonts=false -dCompressFonts=false -dMaxSubsetPct=100 -dConvertCMYKImagesToRGB=false -dDownsampleColorImages=false -dDownsampleGrayImages=false -dDownsampleMonoImages=false -dColorImageFilter=/FlateEncode -dGrayImageFilter=/FlateEncode -dMonoImageFilter=/FlateEncode ";
      $gs_cmd="$gs -q -dQUIET -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dPDFSETTINGS=/printer -dAutoRotatePages=/None ";
      
      $pdfFile = $targetFile;
      $outputFile = $targetFile;
      
      $boxTypes = array('MediaBox','CropBox', 'BleedBox', 'TrimBox', 'ArtBox');
      
      $identifyOutput = array();
      
      for($k=0;$k<count($boxTypes);$k++){
          $tmp_box=$boxTypes[$k];
          
          //$identifyCmd = "$identify -format \"%[fx:(w/72)*2.54]x%[fx:(h/72)*2.54]#\" -define pdf:use-$tmp_box=true $pdfFile";
          $identifyCmd = "$identify -format \"%[fx:w]x%[fx:h]#\" -define pdf:use-$tmp_box=true $pdfFile";
          $tmp_identifyOutput = array();
          exec($identifyCmd, $tmp_identifyOutput);
          $tmp_identifyOutput=explode("#", $tmp_identifyOutput[0]);
          array_pop($tmp_identifyOutput);
          
          for ($i = 0; $i < count($tmp_identifyOutput); $i++) {
              $identifyOutput[$i][$tmp_box] = $tmp_identifyOutput[$i];
          }
      }
      
      $pageCount = count($identifyOutput);
      $gsCommands = array();
      
      for ($pageIndex = 1; $pageIndex <= $pageCount; $pageIndex++) {
          $pageDimensions = array();
            
          foreach ($boxTypes as $boxType) {
              $boxValue = $identifyOutput[$pageIndex - 1][$boxType];
              if ($boxValue !== '') {
                  $coordinates = sscanf($boxValue, '%fx%f');
                  $pageDimensions[$boxType] = array(
                      'width' => $coordinates[0],
                      'height' => $coordinates[1],
                  );
              }
          }
          
          $boxToKeep = findSmallestBox($boxTypes, $pageDimensions);
            
          $smallerBox = $pageDimensions[$boxToKeep];
          $smallerBoxWidth = $smallerBox['width'];
          $smallerBoxHeight = $smallerBox['height'];
          
          $mediaBox = $pageDimensions['MediaBox'];
          $mediaBoxWidth = $mediaBox['width'];
          $mediaBoxHeight = $mediaBox['height'];
          
          $offsetX=($mediaBoxWidth-$smallerBoxWidth)/2;
          $offsetY=($mediaBoxHeight-$smallerBoxHeight)/2;
          
          $temp_file = $targetPath . "temp_" . $pageIndex . "_" . $infoF['filename'].".pdf"; 
          
          $cmdKeepBox="";
          if($infoF['extension']=="pdf" || $infoF['extension']=="ai") $cmdKeepBox="-dUse".$boxToKeep;
          if($infoF['extension']=="eps") $cmdKeepBox="-dEPSCrop";
          
          array_push($gsCommands, $gs_cmd . "-dFirstPage=$pageIndex -dLastPage=$pageIndex -o $temp_file $cmdKeepBox -f $pdfFile");
      }
      
      foreach ($gsCommands as $gsCommand) {
          exec($gsCommand); 
          logToFile($gsCommand,"log.txt");   
      }
      
      $outputFile = $infoF['dirname'] ."/". $infoF['filename'] . ".pdf";
      
      $newOrig=$infoF['dirname'] ."/". $infoF['filename'] . "_orig." . $infoF['extension'];
      rename($targetFile, $newOrig);
      
      if($pageCount>1){
        // Combina tutti i file temporanei in un unico file di output
        $mergeCmd = $gs_cmd . '-sOutputFile=' . $outputFile . ' ';
        for ($pageIndex = 1; $pageIndex <= $pageCount; $pageIndex++) {
            $temp_file = $targetPath . "temp_" . $pageIndex . "_" . $infoF['filename'].".pdf";
            $mergeCmd .= $temp_file . " ";
        }
        $mergeCmd .= " -c .setpdfwrite";
        exec($mergeCmd);
        logToFile($mergeCmd,"log.txt");
        
      }else{
        rename($temp_file, $outputFile);
      }
      
      //Elimina i file temporanei
      for ($pageIndex = 1; $pageIndex <= $pageCount; $pageIndex++) {
          $temp_file = $targetPath . "temp_" . $pageIndex . "_" . $infoF['filename'].".pdf";
          unlink($temp_file);
      }
      
      $targetFile = $outputFile;
    }
    
    //Get Size of Image In MM
    exec("$identify -ping -format \"%rx%nx%wx%hx%xx%yx%[profile:icc]-\" ".$targetFile, $res); 
    if(count($res)==0) {@unlink($targetFile); echo -1; exit;}
    $info_pag=explode("-", $res[0]);
    $info=explode("x", $info_pag[0]);
    $colorspace=trim(str_replace("DirectClass", "", $info[0]));
    
    if(strtolower($colorspace)!="cmyk") $colorspace="RGB";else $colorspace="CMYK";
    
    $orcolorspace= strtoupper($colorspace);
    $oricc = $info[6];
    
    if($colorspace=="CMYK") {$iccF="GRACoL2006_Coated1v2.icc";$icc=SERVER_DOCROOT."icc/GRACoL2006_Coated1v2.icc";}
    if($colorspace=="RGB") {$iccF="AdobeRGB1998.icc";$icc=SERVER_DOCROOT."icc/AdobeRGB1998.icc";}
    
    //$colorspace="";
    //$icc="";
    if($colorspace!="CMYK" && $colorspace!="RGB") {
      $colorspace="RGB";
      $iccF="AdobeRGB1998.icc";
      $icc=SERVER_DOCROOT."icc/AdobeRGB1998.icc";
    }
    
    $addCs="";
    $addICC="";
    $addStrip="";
    if($colorspace!="") $addCs= "-colorspace ".$colorspace;
    if($icc!="") {$addICC= "-profile ".$icc; $addStrip="-strip";}
                                      
    
    $npag=$info[1];
    
    if($npag>40) {@unlink($targetFile); echo -2; exit;} 
    
    array_pop($info_pag);
    
    //Make Preview
    $f_path=dirname($targetFile);
    $f_file=basename($targetFile);
    $prevDir=$f_path."/preview/";
    
    if(!file_exists($prevDir)) {
      @mkdir(str_replace('//','/',$prevDir), 0755, true);  
    }
    
    $sql1="SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dbname."' AND TABLE_NAME = '".$config_table_prefix."ecommerce_upload'";
    $rsmax=mysql_query($sql1);
    $rsmax=mysql_fetch_array($rsmax);
    $idmax=$rsmax[0];
    
    $density=72;
    $resol=1024;
    $append="";
    $pd="";
    $addOldICC="";
    //if($colorspace=="RGB") $addCs=str_replace("RGB", "sRGB", $addCs);
    
    if($orcolorspace=="RGB") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/AdobeRGB1998.icc";
    }elseif($orcolorspace=="CMYK") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/GRACoL2006_Coated1v2.icc";    
    }elseif($orcolorspace=="SRGB") {
      $addOldICC="-profile " . SERVER_DOCROOT."icc/sRGB-IEC61966-2.1.icc";    
    }
    
    $addOldCs="-colorspace ".$orcolorspace;

    if($colorspace=="RGB") {
      $addOldCs="";
      $addCs="";
    }
    
    if($npag>1){
      $append="+append";
      $pd="-%d";
    }else{
      $append="";
      $pd="";
      $resol=1024;  
    }
    
    $prevFileDef=$prevDir.$f_file."-".$idmax."_".$colorspace."_prev.jpg";
    $prevFileDef=str_replace("__prev".$pd.".jpg", "_prev.jpg", $prevFileDef);
    
    if($pd!=""){
      $prevFileMon=$prevDir.$f_file."-".$idmax."_".$colorspace."_prev-*.jpg";
      $prevFileMon=str_replace("__prev".$pd.".jpg", "_prev-*.jpg", $prevFileMon); 
    }
    
    $i=0;
    while (list($key, $row) = each($info_pag)) {
      $i++;
      
      $info=explode("x", $row);
      
      $wPx=$info[2];
      $hPx=$info[3];
      $dpiX=$info[4];
      $dpiY=$info[5];
      $w=($wPx/$dpiX)*25.4;
      $h=($hPx/$dpiY)*25.4;
      $key=md5($targetFile);
      
      if($pd!="") $pd="-".$i;
      
      $prevFile=$prevDir.$f_file."-".$idmax."_".$colorspace."_prev".$pd.".jpg";
      $prevFile=str_replace("__prev".$pd.".jpg", "_prev".$pd.".jpg", $prevFile);
      
      if($npag>1){
        $maxMP=2;
      }else{
        $maxMP=4;  
      }
      
      $mps=sqrt(($maxMP*1000000)/($wPx*$hPx));
      $newPxW=floor($wPx*$mps);
      $newPxH = floor($hPx * $mps);
      
      $newDPI=floor($newPxW/($w/25.4));
      
      $exec_cmd="$gs -sDEVICE=jpeg -sICCProfilesDir=\"".$icc_path."\" -sDefaultRGBProfile=\"AdobeRGB1998.icc\" -sDefaultCMYKProfile=\"GRACoL2006_Coated1v2.icc\" -dOverrideICC=true -sOutputICCProfile=\"AdobeRGB1998.icc\" -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT -dMaxBitmap=500000000 -dAlignToPixels=0 -dGridFitTT=2 -dTextAlphaBits=4 -dGraphicsAlphaBits=1 -dJPEGQ=85 -g".$newPxW."x".$newPxH." -r".$newDPI." -dFirstPage=".$i." -dLastPage=".$i." -sOutputFile=\"".$prevFile."\" -f \"".$targetFile."\"";
      exec($exec_cmd);
      logToFile($exec_cmd,"log.txt"); 
       
      if(!file_exists($prevFile)) {@unlink($targetFile); echo -1; exit;}
      
      $sql="INSERT INTO `".$config_table_prefix."ecommerce_upload` (skey, id_ecommerce_ordini, orFileEx, orFile, prevFile, num_pag, tot_pag, w_pixel, h_pixel, w_mm, h_mm, dpi, newDpi, orColorspace, colorspace, orIcc, icc, area_mq, file_size, note_text, data, approved) VALUES ( ";
      $sql.="'".($key)."',";
      $sql.="'".($ordine)."',";
      $sql.="'".($newOrig)."',";
      $sql.="'".($targetFile)."',";
      $sql.="'".($prevFileDef)."',";
      $sql.="'".($i)."',";
      $sql.="'".($npag)."',";
      $sql.="'".($wPx)."',";
      $sql.="'".($hPx)."',";
      $sql.="'".($w)."',";
      $sql.="'".($h)."',";
      $sql.="'".(($dpiX+$dpiY)/2)."',";
      $sql.="'".$newDPI."',";
      $sql.="'".addslashes($orcolorspace)."',";
      $sql.="'".addslashes($colorspace)."',";
      $sql.="'".addslashes($oricc)."',";
      $sql.="'".addslashes($icc)."',";
      $sql.="'".round(($w/1000)*($h/1000),2)."', ";
      $sql.="'".str_replace(".",",",(round(filesize($targetFile)/pow(1024,2),2)))."',";
      $sql.="'',";
      $sql.="NOW(),";
      $sql.="'0' )";
      
      //echo $sql;
      
      mysql_query($sql);
      $retid=mysql_insert_id();
    }
    
    if($pd!="") {
      $cmd_exec="$convert ".$prevFileMon." -resize 2048 -density ".$newDPI." -append ".$prevFileDef;
      exec($cmd_exec);
      logToFile($cmd_exec,"log.txt"); 
      
      if(!file_exists($prevFile)) {@unlink($targetFile); echo -1; exit;}
      
      array_map('unlink', glob($prevFileMon));
    }
    
  }else{
    echo "0"; //Formato non valido;
  }
}
?>
