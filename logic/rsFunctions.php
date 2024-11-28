<?php
include 'rsFunctions_Anagrafica.php';

if(!function_exists('mb_ucfirst') && function_exists('mb_substr')) {
  function mb_ucfirst($string) {
    $string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    return $string;
  }
}

if(!function_exists('mb_ucwords')) {
  function mb_ucwords($str) {
    return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
  }
}

function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    }
    else {
        exec($cmd . " > /dev/null &");  
    }
} 

function controllaCF($cf){
     if($cf=='') return false;

     if(strlen($cf)!= 16) return false;

     $cf=strtoupper($cf);
     if(!preg_match("/[A-Z0-9]+$/", $cf)) return false;
     $s = 0;
     
     for($i=1; $i<=13; $i+=2){
      	$c=$cf[$i];
      	if('0'<=$c and $c<='9')
      	   $s+=ord($c)-ord('0');
      	else
      	   $s+=ord($c)-ord('A');
     }

     for($i=0; $i<=14; $i+=2){
      	$c=$cf[$i];
      	switch($c){
          case '0':  $s += 1;  break;
          case '1':  $s += 0;  break;
          case '2':  $s += 5;  break;
          case '3':  $s += 7;  break;
          case '4':  $s += 9;  break;
          case '5':  $s += 13;  break;
          case '6':  $s += 15;  break;
          case '7':  $s += 17;  break;
          case '8':  $s += 19;  break;
          case '9':  $s += 21;  break;
          case 'A':  $s += 1;  break;
          case 'B':  $s += 0;  break;
          case 'C':  $s += 5;  break;
          case 'D':  $s += 7;  break;
          case 'E':  $s += 9;  break;
          case 'F':  $s += 13;  break;
          case 'G':  $s += 15;  break;
          case 'H':  $s += 17;  break;
          case 'I':  $s += 19;  break;
          case 'J':  $s += 21;  break;
          case 'K':  $s += 2;  break;
          case 'L':  $s += 4;  break;
          case 'M':  $s += 18;  break;
          case 'N':  $s += 20;  break;
          case 'O':  $s += 11;  break;
          case 'P':  $s += 3;  break;
          case 'Q':  $s += 6;  break;
          case 'R':  $s += 8;  break;
          case 'S':  $s += 12;  break;
          case 'T':  $s += 14;  break;
          case 'U':  $s += 16;  break;
          case 'V':  $s += 10;  break;
          case 'W':  $s += 22;  break;
          case 'X':  $s += 25;  break;
          case 'Y':  $s += 24;  break;
          case 'Z':  $s += 23;  break;
	     }
    }

    if( chr($s%26+ord('A'))!=$cf[15] ) return false;

    return true;
}

function controllaPIVA($variabile){
	if($variabile=='') return false;

	//la p.iva deve essere lunga 11 caratteri
	if(strlen($variabile)!=11) return false;

	//la p.iva deve avere solo cifre
	if(!ereg("^[0-9]+$", $variabile)) return false;

	$primo=0;
	for($i=0; $i<=9; $i+=2) $primo+= ord($variabile[$i])-ord('0');

	for($i=1; $i<=9; $i+=2 ){
		$secondo=2*( ord($variabile[$i])-ord('0') );

		if($secondo>9) $secondo=$secondo-9;
		
    $primo+=$secondo;
	}
	
  if( (10-$primo%10)%10 != ord($variabile[10])-ord('0') ) return false;

	return true;
}

function controllaEmail($variabile){
	// se la stringa � vuota sicuramente non � una mail
	if(trim($variabile)=="") return false;

	// controllo che ci sia una sola @ nella stringa
	$num_at=count(explode( '@', $variabile ))-1;

	if($num_at != 1) return false;

	// controllo la presenza di ulteriori caratteri "pericolosi":
	if(strpos($variabile, ';') || strpos($variabile, ',') || strpos($variabile, ' ')) return false;

	if(preg_match('/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $variabile)) return true; else return false;
}

function run_process($cmd, $outputFile = '/dev/null', $append = false){
    $pid=0;
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {//'This is a server using Windows!';
            $cmd = 'wmic process call create "'.$cmd.'" | find "ProcessId"';
            $handle = popen("start /B ". $cmd, "r");
            $read = fread($handle, 200); //Read the output 
            $pid=substr($read,strpos($read,'=')+1);
            $pid=substr($pid,0,strpos($pid,';') );
            $pid = (int)$pid;
            pclose($handle); //Close
    }else{
        $pid = (int)shell_exec(sprintf('%s %s %s 2>&1 & echo $!', $cmd, ($append) ? '>>' : '>', $outputFile));
    }
        return $pid;
}

function is_process_running($pid){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {//'This is a server using Windows!';
        //tasklist /FI "PID eq 6480"
        $result = shell_exec('tasklist /FI "PID eq '.$pid.'"' );
        if (count(preg_split("/\n/", $result)) > 0 && !preg_match('/No tasks/', $result) && !preg_match('/nessuna attivit/', $result)) {
            return true;
        }
    }else{
        $result = shell_exec(sprintf('ps %d 2>&1', $pid));
        if (count(preg_split("/\n/", $result)) > 2 && !preg_match('/ERROR: Process ID out of range/', $result)) {
            return true;
        }
    }
    return false;
}

function stop_process($pid){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {//'This is a server using Windows!';
        $result = shell_exec('taskkill /PID '.$pid.' /F' );
        if (count(preg_split("/\n/", $result)) > 0 && !preg_match('/No tasks/', $result) && !preg_match('/nessuna attivit/', $result)) {
            return true;
        }
    }else{
        $result = shell_exec(sprintf('kill %d 2>&1', $pid));
        if (!preg_match('/No such process/', $result)) {
            return true;
        }
    }
    return false;
}

function _bot_detected() {

  return (
    isset($_SERVER['HTTP_USER_AGENT'])
    && preg_match('/bot|crawl|slurp|spider|mediapartners|Google|Chrome-Lighthouse/i', $_SERVER['HTTP_USER_AGENT'])
  );
}
 
function serverRedir(){
  $host=strtolower($_SERVER['SERVER_NAME']);
  $host=str_replace("www.", "", $host);
  $q=$_SERVER['QUERY_STRING'];
  
  if(($host!="upload.heroesprint.eu" && $host!="sandbox.heroesprint.eu" && $host!="localhost" && $host!="localdemo.heroesprint.eu") && (strpos($q,"menid=2090")!==FALSE || strpos($q,"url=invio-file")!==FALSE || strpos($q,"url=send-files")!==FALSE || strpos($q,"url=envoi-des-fichiers")!==FALSE || strpos($q,"url=ubermittlung-der-dateien")!==FALSE || strpos($q,"url=envio-de-los-archivos")!==FALSE)){
    $pageURL = "https://upload.heroesprint.eu".$_SERVER["REQUEST_URI"];
    header("Location: ".$pageURL);
    die();    
  }elseif($host=="upload.heroesprint.eu" && (strpos($q,"menid=2090")===FALSE && strpos($q,"url=invio-file")===FALSE) && strpos($q,"url=send-files")===FALSE && strpos($q,"url=envoi-des-fichiers")===FALSE && strpos($q,"url=ubermittlung-der-dateien")===FALSE && strpos($q,"url=envio-de-los-archivos")===FALSE) {
    $pageURL = "https://heroesprint.eu".$_SERVER["REQUEST_URI"];
    header("Location: ".$pageURL);
    die();    
  }
}

if(!function_exists('mb_wordwrap')) {
  function mb_wordwrap($str, $width=74, $break="\r\n") {
      // Return short or empty strings untouched
      if(empty($str) || mb_strlen($str, 'UTF-8') <= $width)
          return $str;
     
      $br_width  = mb_strlen($break, 'UTF-8');
      $str_width = mb_strlen($str, 'UTF-8');
      $return = '';
      $last_space = false;
      
      for($i=0, $count=0; $i < $str_width; $i++, $count++)
      {
          // If we're at a break
          if (mb_substr($str, $i, $br_width, 'UTF-8') == $break)
          {
              $count = 0;
              $return .= mb_substr($str, $i, $br_width, 'UTF-8');
              $i += $br_width - 1;
              continue;
          }
  
          // Keep a track of the most recent possible break point
          if(mb_substr($str, $i, 1, 'UTF-8') == " ")
          {
              $last_space = $i;
          }
  
          // It's time to wrap
          if ($count > $width)
          {
              // There are no spaces to break on!  Going to truncate :(
              if(!$last_space)
              {
                  $return .= $break;
                  $count = 0;
              }
              else
              {
                  // Work out how far back the last space was
                  $drop = $i - $last_space;
  
                  // Cutting zero chars results in an empty string, so don't do that
                  if($drop > 0)
                  {
                      $return = mb_substr($return, 0, -$drop);
                  }
                  
                  // Add a break
                  $return .= $break;
  
                  // Update pointers
                  $i = $last_space + ($br_width - 1);
                  $last_space = false;
                  $count = 0;
              }
          }
  
          // Add character from the input string to the output
          $return .= mb_substr($str, $i, 1, 'UTF-8');
      }
      return $return;
  }
}

if(!function_exists('mb_ucfirst') && function_exists('mb_substr')) {
  function mb_ucfirst($string) {
    $string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    return $string;
  }
}

function rip_tags($string) { 
    // ----- remove HTML TAGs ----- 
    $string = preg_replace ('/<[^>]*>/', ' ', $string); 
    
    // ----- remove control characters ----- 
    $string = str_replace("\r", '', $string);    // --- replace with empty space
    $string = str_replace("\n", ' ', $string);   // --- replace with space
    $string = str_replace("\t", ' ', $string);   // --- replace with space
    
    // ----- remove multiple spaces ----- 
    $string = trim(preg_replace('/ {2,}/', ' ', $string));
    
    return $string; 
}

function fc($str){
  //FPDF char conv
  return iconv('UTF-8', 'windows-1252', $str);
}

function ln($str,$ret=0,$lan=0) {
	global $config_table_prefix;
	//return $str;
  if($ret==1) return $str;
  
  if($lan==0) {
    if(isset($_GET['lan'])) $_SESSION['lan']=$_GET['lan'];
    if($_SESSION['lan']==0 || $_SESSION['lan']=="") return $str;
    $lan=$_SESSION['lan'];
  }
  
  if($_SESSION["pred_lan"]==$_SESSION['lan'] && ($lan==0 || $lan==$_SESSION['lan'])) return $str;
  
  $ret_str=$str;
  if($str!="" && !is_numeric($str)) {
    $rs=getTable("dizionario","","(testo_editor='".addslashes($str)."')");
    
    if(count($rs)==0 && !($_SESSION["user_id"]>0)){
    	$insStr=addslashes($ret_str);
    	
    	$sql="INSERT INTO `".$config_table_prefix."dizionario` (testo_editor) VALUES ('".$insStr."')";
    	
    	mysql_query($sql);
    	return $ret_str;
    }
    
    $last=count($rs)-1;
    $row=$rs[$last];
    $rs2=Table2ByTable1("dizionario","traduzioni",$row['id'],"id_lingue=".$lan,"");
    
    while (list($key2, $row2) = each($rs2)) {
      if($row2['testo_tradotto_editor']!="") {
        $ret_str=$row2['testo_tradotto_editor'];  
        
        if(substr($ret_str, 0, 3)=="<p>" && substr($ret_str, strlen($ret_str)-4,strlen($ret_str))=="</p>") {
          $ret_str=substr($ret_str, 3, strlen($ret_str)-7);
        }
      }
    }
  }
  
  return $ret_str;
}

function ExistTraduction($str) {
  global $config_table_prefix;                                
  
  $lingue=getTable("lingue","","attivo='1'");
  if(count($lingue)==0) return false;
  
  $ret_lan=array();
  $str=$str;
  if($str!="" && !is_numeric($str)) {
    $rs=getTable("dizionario","","(testo_editor='".addslashes($str)."')");
    if(count($rs)>0){
      $last=count($rs)-1;
      $row=$rs[$last];
      while (list($key1, $row1) = each($lingue)) {    
        $rs2=Table2ByTable1("dizionario","traduzioni",$row['id'],"id_lingue=".$row1['id'],"");
        if(count($rs2)>0) array_push($ret_lan, $rs2[0]);
      }
    }else{
      return false;
    } 
  }

  if(count($ret_lan)==0) $ret_lan=false;
  
  return $ret_lan;
}

function isInDizionario($str) {
  global $config_table_prefix;
  
  $str=$str;
  if($str!="" && !is_numeric($str)) {
    $rs=getTable("dizionario","","(testo_editor='".addslashes($str)."')");
    if(count($rs)>0){
      $last=count($rs)-1;
      $row=$rs[$last];
      return $row['id'];
    }else{
      return false;
    }
  }
  
  return false;
}


function currencyITA($str) {
  $str=trim($str);
  if($str=="") return;
  $str=str_replace(",", ".", $str);
  if(strpos(".", $str)===FALSE) $str.=".00";
  if(strpos(".", $str)==0 && strpos(".", $str)!==FALSE) $str="0".$str;
    
  $str=number_format($str, 2, ',', '.');
  return $str;
}

function stampaMenuLingue($txtIta="",$imgIta="",$noLabel="") {
  if(isset($_GET['lan'])) $_SESSION['lan']=$_GET['lan'];
  
  $i=0;
  $rs=getTable("lingue","Ordinamento ASC","attivo='1'");
  
  $str="";
  $q=$_SERVER['QUERY_STRING'];
  if(!(strpos($q, "lan=")===FALSE)) {
    $str=substr($q, strpos($q, "lan="), 6);
    $q=str_replace($str,"", $q);
  }
  
  if($q!="") {
    $url=basename($_SERVER['SCRIPT_FILENAME'])."?".$q."&"."lan=0";
  } else {
    $url=basename($_SERVER['SCRIPT_FILENAME'])."?lan=0";
  }
  
  if($_SESSION['lan']==0) $sel="selected";
  
  $url=str_replace("&&", "&", $url);
  if($noLabel==1) {$tit=$txtIta;$txtIta="&nbsp;";}
  echo "<div id='lingue'><ul><li class='li$i'><a class='$sel' title='$tit' href='$url'><span class='$sel' style='background-image: url(".$imgIta.");'>".$txtIta."</span></a></li>";
  
  while (list($key, $row) = each($rs)) {
    $i++;
    $sel="";
    $url="";
    
    if($_SESSION['lan']==$row['id']) $sel="selected";
    
    if($q!="") {
      $url=basename($_SERVER['SCRIPT_FILENAME'])."?".$q."&"."lan=".$row['id'];
    } else {
      $url=basename($_SERVER['SCRIPT_FILENAME'])."?lan=".$row['id'];
    }
    
    $url=str_replace("&&", "&", $url);
    if($noLabel==1) {$tit=$row['nome'];$row['nome']="&nbsp;";}
    echo "<li class='li$i'><a class='$sel' title='$tit' href='$url'><span class='$sel' style='background-image: url(".retFile($row['immagine_file']).");'>".$row['nome']."</span></a></li>";
  }
  
  echo "</ul></div>";
}

function getUrlLan($id){
	$objUtility=new Utility();

	$str="";
	$q=$_SERVER['QUERY_STRING'];
	if(!(strpos($q, "lan=")===FALSE)) {
		$str=substr($q, strpos($q, "lan="), 6);
		$q=str_replace($str,"", $q);
	}

	if($q!="") {
		$url=basename($_SERVER['SCRIPT_FILENAME'])."?".$q."&"."lan=".$id;
	} else {
		$url=basename($_SERVER['SCRIPT_FILENAME'])."?lan=".$id;
	}

	$url=str_replace("&&", "&", $url);

	return $objUtility->getPathRoot().$url;
}

function getCurLan(){
	if(!($_SESSION['lan']>0) && !($_GET['lan']>0)) {
		$pred=getTable("lingue","","predefinita=1");
		if(count($pred)>0){
			$_SESSION['lan']=$pred[0]["id"];
		}else{
			return false; 
		}
	}

	if(isset($_GET['lan'])) $_SESSION['lan']=$_GET['lan'];
	$id=$_SESSION['lan'];

	$rs=retRow("lingue",$id);

	return $rs;
}

function getCurLanClass(){
	if(!($_SESSION['lan']>0) && !($_GET['lan']>0)) {
		$pred=getTable("lingue","","predefinita=1");
		if(count($pred)>0){
			$_SESSION['lan']=$pred[0]["id"];
		}else{
			return false; 
		}
	}

	if(isset($_GET['lan'])) $_SESSION['lan']=$_GET['lan'];
	$id=$_SESSION['lan'];

	$rs=retRow("lingue",$id);

	return $rs['classe'];
}

function getPadre($id) {
  $id_cat=$id;
  $padre=false;
  $rs=getTable("categorie#categorie_nm","","id_categorie_self='$id_cat'");
  $id_padre=$rs[0]['id_categorie'];
  if($id_padre!="") {
    $padre=retRow("categorie",$id_padre);
  }
  return $padre;
}

function getFigliFull($id){
  	$rs=Table2ByTable1("categorie","categorie",$id,"attivo=1","Ordinamento ASC");
  	
	$figli=array();
	while (list($key, $row) = each($rs)) {
		$id_figlio=$row['id'];
		
		if($id_figlio!="") {
			$figlio=retRow("categorie",$id_figlio);
			array_push($figli, $figlio);
		}
	}
	
	return $figli;
}

function getPadreFull($id){
	$rs=getTable("categorie#categorie_nm","","id_categorie_self='$id'");
  $id_padre=$rs[0]['id_categorie'];
	
	if($id_padre!="") {
    	$padre=retRow("categorie",$id_padre);
    	return $padre;
  	}else{
  		return false;
  	}
}

function getFratelliFull($struttura,$id){
	$padre=getPadreFull($id);
	
	if($padre!==FALSE){
		$fratelli=getFigliFull($padre["id"]);
		
		return $fratelli;	
	}else{
		$struttura=getTable("strutture","","nome='".addslashes("$struttura")."'");
		$struttura_id=$struttura[0]["id"];
    
    $figli=Table2ByTable1("strutture", "categorie", $struttura_id,"attivo=1","Ordinamento ASC");
		
		if(count($figli)>0) {
			return $figli;
		}else{
			return false;
		}
	}
}

function stampaStruttura3($struttura,$id="",$htmlFigli=""){
	if($id!=""){
    $nodo=retRow("categorie",$id);
  	$padre=getPadreFull($id);
  	$figli=getFigliFull($id);
  	$fratelli=getFratelliFull($struttura,$id);
	}else{
    $struttura=getTable("strutture","","nome='".addslashes("$struttura")."'");
		$struttura_id=$struttura[0]["id"];
    
    $figli=Table2ByTable1("strutture", "categorie", $struttura_id,"attivo=1","Ordinamento ASC");
    $htmlFigli="<ul>";
		while (list($key, $row) = each($figli)) {
			$htmlFigli.="<li><a>".$row["nome"]."</a></li>";
		}
		$htmlFigli.="</ul>";
    
    return $htmlFigli;  
  }
  
	if($htmlFigli=="" && $figli!==FALSE){
		$htmlFigli="<ul>";
		while (list($key, $row) = each($figli)) {
			$htmlFigli.="<li><a>".$row["nome"]."</a></li>";
		}
		$htmlFigli.="</ul>";
	}
	
	if($fratelli!==FALSE) {
		$htmlNodo="<ul>";
		while (list($key, $row) = each($fratelli)) {
			if($row["id"]==$id){
				$htmlNodo.="<li><a>".$row["nome"]."</a>".$htmlFigli."</li>";
			}else{
				$htmlNodo.="<li><a>".$row["nome"]."</a></li>";
			}
		}
		$htmlNodo.="</ul>";
	}
	
	if($padre===FALSE) {
		return $htmlNodo;
	}
	
	$htmlNodo=stampaStruttura3($struttura,$padre["id"],$htmlNodo);
	 
	return $htmlNodo;
}

function setPadre($id,$newPadre) {
  $padre=getPadre($id);
  $rs=retRow("categorie",$newPadre);
  
  if($padre==false || $rs['id']=="") return;

  $nodo=getTable("categorie#categorie_nm","","id_categorie_self='$id'");
  $nodo=$nodo[0];
  $sql="UPDATE `".$config_table_prefix."categorie#categorie_nm` SET id_categorie='$newPadre' WHERE id_categorie_self='$id' ";
  mysql_query($sql);
}

function deleteFileFromTable($table,$intId,$addPrefix="") {
  $objUtility = new Utility;
  global $config_table_prefix;
  if($addPrefix=="") $table=$config_table_prefix.$table;
  
  $query = mysql_query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME='$table' ");
  while($arr=mysql_fetch_array($query)) {
    if(strpos($arr['COLUMN_NAME'], "_file")) {
      $query2 = mysql_query("SELECT ".$arr['COLUMN_NAME']." AS fid FROM `$table` WHERE id='$intId' ");
      $arr2=mysql_fetch_array($query2);
      
      $query3 = mysql_query("SELECT * FROM `".$config_table_prefix."oggetti` WHERE id='".$arr2['fid']."' ");
      if($arr3=mysql_fetch_array($query3)) {
        if(is_file($objUtility->getPathResourcesDynamicAbsolute().$arr3['nome'].".".$arr3['ext'])) unlink($objUtility->getPathResourcesDynamicAbsolute().$arr3['nome'].".".$arr3['ext']); 
        $sql4="DELETE FROM `".$config_table_prefix."oggetti` WHERE id='".$arr2['fid']."'";
        $query4 = mysql_query($sql4);
      } 
    }
  }  
}

function cercaInTabella ($table,$str,$where="") {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  if(trim($where)=="") $where="1";
  
  $sql="SHOW COLUMNS FROM `".$config_table_prefix.$table."`";
  $rs=mysql_query($sql);
  $cols=$objUtility->buildRecordset($rs);
  
  $strArr=explode(" ", $str);
  
  $sql="";
  while (list($key, $row) = each($cols)) {
    reset($strArr);
    $sql=$sql."(";
    while (list($key1, $row1) = each($strArr)) {
      $sql.="`".$row['Field']."` LIKE '%".$row1."%' AND ";
    }
    $sql=$sql.")";
    $sql=str_replace("AND )", ")", $sql);
    $sql.=" OR ";      
  } 
  $sql="(".$sql.")";
  $sql=str_replace("OR )", ")", $sql);
  $sql="SELECT * FROM `".$config_table_prefix.$table."` WHERE (".$sql." AND ".$where.")"; 
  $q=mysql_query($sql);
  //echo $sql."<br>";
  $rs=$objUtility->buildRecordset($q);
  
  return $rs;        
}

function cercaInStruttura($struttura,$strToSearch,$where="",$where_recursive="") {
  global $config_table_prefix;
  $objUtility = new Utility;
  $nmtab="categorie";
  
  if($where=="") $where="1";
  if($where_recursive=="") $where_recursive="1";
  $rs=getStrutturaNM($nmtab,-1);
  $myArr=getStrutturaFull($struttura);
  $result=array();
  
  $tmpres=cercaInTabella($nmtab,$strToSearch);
  
  if(count($tmpres)>0) {
    while (list($key, $row) = each($tmpres)) {
      $tmpres[$key]['rsStruttureID']=$row['id'];  
    }
    $result=array_merge($result,$tmpres);
  }
  
  while (list($key, $row) = each($rs)) {
    $tmpTab=$row;
    reset($myArr);
    while (list($key1, $row1) = each($myArr)) { 
      $t=$rs2=Table2ByTable1($nmtab,$tmpTab,$row1['id'],"",""); 
      if(count($t)>0) {
        $wh="";
        while (list($key2, $row2) = each($t)) { 
          $wh.="id='".$row2['id']."' AND ";
        }  
        $wh="(".$wh.")";
        $wh=str_replace("AND )", ")", $wh);
        $wh="(".$wh." AND ".$where.")";
        $tmpres=cercaInTabelleNM($tmpTab,$strToSearch,$wh,$where_recursive);
        if(count($tmpres)>0) {
          while (list($key3, $row3) = each($tmpres)) {
            $tmpres[$key3]['rsStruttureID']=$row1['id'];  
          }
          $result=array_merge($result,$tmpres);
        }
      }  
    }
  }
  
  return $result;  
}

function cercaInTabelleNM($nmtab,$strToSearch,$where="",$where_recursive="") {
  global $config_table_prefix;
  $objUtility = new Utility;

  if($where=="") $where="1";
  if($where_recursive=="") $where_recursive="1"; 
  $rs=getStrutturaNM($nmtab,-1); 
  $myArr=getTable($nmtab,"",$where);
  $result=array();
  
  if(count($rs)>0) {
    while (list($key, $row) = each($rs)) {
      $tmpTab=$row;
      reset($myArr);
      while (list($key1, $row1) = each($myArr)) { 
        $t=$rs2=Table2ByTable1($nmtab,$tmpTab,$row1['id'],"","");
        if(count($t)>0) {
          $wh="";  
          while (list($key2, $row2) = each($t)) {
            $wh.="id='".$row2['id']."' AND ";
          }
          $wh="(".$wh.")";
          $wh=str_replace("AND )", ")", $wh);
          $wh="(".$wh." AND ".$where_recursive.")";
          $tmpres=cercaInTabelleNM($tmpTab,$strToSearch,$wh,$where_recursive);
          if(count($tmpres)>0) $result=array_merge($result,$tmpres); 
        }
      }
    }
    $tmpres=cercaInTabella ($nmtab,$strToSearch,$where);
    if(count($tmpres)>0) $result=array_merge($result,$tmpres);
  } else {
    $result=cercaInTabella ($nmtab,$strToSearch,$where);   
  }
  
  return $result;  
}

function getStrutturaNM($nmtab,$recursive="") {
  global $config_table_prefix;
  $objConfig = new ConfigTool();
  $db_dbname = $objConfig->get("db-dbname");
  $objUtility = new Utility;
  $categorie=$config_table_prefix.$nmtab;
  $result=array();
  
  if($recursive=="") $recursive=1;
  
  $search="%".$categorie."#%";
  $sql="SHOW TABLES LIKE '".$search."'";
  $q=mysql_query($sql);
  $rs=$objUtility->buildRecordset($q);
  $str="Tables_in_".$db_dbname." (".$search.")";  
  
  while (list($key, $row) = each($rs)) {
    $tmpTab=str_replace($categorie."#", "", $row[$str]);
    $tmpTab=left($tmpTab,strlen($tmpTab)-strlen("_nm"));
    if($tmpTab!="categorie") { 
      array_push($result, $tmpTab);
      if($recursive==1){
        $res2=getStrutturaNM($tmpTab,$recursive);
        $result=array_merge($result, $res2);
      }
    }
  }
  
  return $result;
}

function cancellaNodoStruttura ($intId) {
  global $config_table_prefix;
  $table=$config_table_prefix."categorie";
  
  $myArr=getStruttura("",$intId);
  array_push($myArr, $intId);
  
  for($j=0;$j<count($myArr);$j++) {
    $tdel=true;
    if(function_exists(str_replace($config_table_prefix,"", $table)."_before_delete")) eval("\$tdel=".str_replace($config_table_prefix,"", $table)."_before_delete('".$myArr[$j]."');");
	  
    if($tdel) {
      delete($myArr[$j]);
      $sql="DELETE FROM `".$config_table_prefix."categorie#categorie_nm` WHERE id_categorie='".$myArr[$j]."'";
      mysql_query($sql);
      $sql="DELETE FROM `".$config_table_prefix."strutture#categorie_nm` WHERE id_categorie='".$myArr[$j]."'";
      mysql_query($sql);
      
      $rs=Table2ByTable1("categorie","contenuti",$myArr[$j],"","");
      while (list($key, $row) = each($rs)) {
        $rs2=Table2ByTable1("contenuti","1_intestazioni",$row['id'],"","");
        while (list($key2, $row2) = each($rs2)) {
          deleteFileFromTable("1_intestazioni",$row2['id']);
          $sql="DELETE FROM `".$config_table_prefix."1_intestazioni` WHERE id='".$row2['id']."'";
          mysql_query($sql);
        }
        
        $rs=Table2ByTable1("categorie","magazzino_articoli",$myArr[$j],"","");
        while (list($key, $row) = each($rs)) {
          $sql="DELETE FROM `".$config_table_prefix."magazzino_articoli` WHERE id='".$row['id']."'";
          mysql_query($sql);
        }
        
        $rs=Table2ByTable1("categorie","magazzino_articoli_collegati",$myArr[$j],"","");
        while (list($key, $row) = each($rs)) {
          $sql="DELETE FROM `".$config_table_prefix."magazzino_articoli_collegati` WHERE id='".$row['id']."'";
          mysql_query($sql);
        }
        
        $rs2=Table2ByTable1("contenuti","2_titoli_h1_h2_h3",$row['id'],"","");
        while (list($key2, $row2) = each($rs2)) {
          deleteFileFromTable("2_titoli_h1_h2_h3",$row2['id']);
          $sql="DELETE FROM `".$config_table_prefix."2_titoli_h1_h2_h3` WHERE id='".$row2['id']."'";
          mysql_query($sql);
        }
        
        $rs2=Table2ByTable1("contenuti","3_testi",$row['id'],"","");
        while (list($key2, $row2) = each($rs2)) {
          deleteFileFromTable("3_testi",$row2['id']);
          $sql="DELETE FROM `".$config_table_prefix."3_testi` WHERE id='".$row2['id']."'";
          mysql_query($sql);
        }
        
        $rs2=Table2ByTable1("contenuti","4_mappa_google",$row['id'],"","");
        while (list($key2, $row2) = each($rs2)) {
          deleteFileFromTable("4_mappa_google",$row2['id']);
          $sql="DELETE FROM `".$config_table_prefix."4_mappa_google` WHERE id='".$row2['id']."'";
          mysql_query($sql);
        }
        
        $rs2=Table2ByTable1("contenuti","5_forms",$row['id'],"","");
        while (list($key2, $row2) = each($rs2)) {
          deleteFileFromTable("5_forms",$row2['id']);
          $sql="DELETE FROM `".$config_table_prefix."5_forms` WHERE id='".$row2['id']."'";
          mysql_query($sql);
        }
        
        $rs2=Table2ByTable1("contenuti","oggetti",$row['id'],"","");
        while (list($key2, $row2) = each($rs2)) {
          $sql="DELETE FROM `".$config_table_prefix."oggetti` WHERE id='".$row2['id']."'";
          mysql_query($sql);
        }
        
        deleteFileFromTable("contenuti",$row['id']);
        $sql="DELETE FROM `".$config_table_prefix."contenuti` WHERE id='".$row['id']."'";
        mysql_query($sql);
      }
      
      $rs=Table2ByTable1("categorie","fotogallery",$myArr[$j],"","");
      while (list($key, $row) = each($rs)) {
        deleteFileFromTable("fotogallery",$row['id']);
        $sql="DELETE FROM `".$config_table_prefix."fotogallery` WHERE id='".$row['id']."'";
        mysql_query($sql);
      }
      
      $rs=Table2ByTable1("categorie","immagini_top",$myArr[$j],"","");
      while (list($key, $row) = each($rs)) {
        deleteFileFromTable("immagini_top",$row['id']);
        $sql="DELETE FROM `".$config_table_prefix."immagini_top` WHERE id='".$row['id']."'";
        mysql_query($sql);
      }
      
      $sql="DELETE FROM `".$config_table_prefix."categorie#contenuti_nm` WHERE id_categorie='".$myArr[$j]."'";
      mysql_query($sql);
      $sql="DELETE FROM `".$config_table_prefix."categorie#fotogallery_nm` WHERE id_categorie='".$myArr[$j]."'";
      mysql_query($sql);
      $sql="DELETE FROM `".$config_table_prefix."categorie#immagini_top_nm` WHERE id_categorie='".$myArr[$j]."'";
      mysql_query($sql);
    }
    
    if(function_exists(str_replace($config_table_prefix,"", $table)."_after_delete")) eval(str_replace($config_table_prefix,"", $table)."_after_delete('".$myArr[$j]."');");
  }
}

function stampaStruttura($nome,$selected="",$useLayout="",$cat="",$edit="",$urlRewrite="",$useAjax="",$flash="",$flashW="",$flashH="",$nmRel="",$sortable="1") {
  global $config_table_prefix;
  $objUtility = new Utility;
  $rs=getTable("strutture","","nome='$nome' AND attivo='1'");
  
  if($_SESSION["user_id"]=="") $where="attivo='1'";
  
  if($flashW=="") $flashW=0;
  if($flashH=="") $flashH=0;
  
  if($cat=="") {
    $rs2=Table2ByTable1("strutture","categorie",$rs[0]['id'],$where,"Ordinamento ASC");
  } elseif (is_array($cat)) {
    $rs2 = array();
    while (list($key, $row) = each($cat)) {
      $rs2=array_merge_recursive($rs2, Table2ByTable1("categorie","categorie",$row,$where,"Ordinamento ASC"));
    }
  } else {
    $rs2=Table2ByTable1("categorie","categorie",$cat,$where,"Ordinamento ASC");
  }
  
  echo "<div class='ez-wr rsStrutture rsStrutture-connected ".onlyreadables($nome)."' id='".$rs[0]['css']." ' rsName='".addslashes($nome)."' rsSortable='".$sortable."' rsEditable='".$edit."' rsAjax='".$useAjax."' rsNMRel='".$nmRel."'>";
  if($edit=="1") echo "<input type='button' class='rsStrutture-add' value='' title='".ln("Aggiungi una categoria")."' rsName='".addslashes($nome)."' />";
  $tmpArr=array();
  $ramo=getRamoEx($selected);
  $tmpArr=stampaStrutturaEx($rs2,1,$selected,$useLayout,$tmpArr,$edit,$urlRewrite,$nome,$useAjax,$flash,$flashW,$flashH,$nmRel,$ramo);
  echo "</ul></div>";
  
  return $tmpArr;
}

function stampaStrutturaEx($rs,$padre,$selected="",$useLayout="",$tmpArr="",$edit="",$urlRewrite="",$nome="",$useAjax="",$flash="",$flashW="",$flashH="",$nmRel="",$ramo) {
  global $config_table_prefix;
  $objUtility = new Utility;

  $i=0;
  if($padre==1) echo "<ul class='ul$padre rsStrutture-ul' >";
  if($padre!=1) echo "</ul><ul class='ul$padre rsStrutture-ul'>";

  $strnome=getStrutturaByNodo($rs[0]['id']);
  if($strnome['nome']=="magazzino") {
    $maglay=getTable("ecommerce_conf_layout","","attivo='1'");
  }
  while (list($key, $row) = each($rs)) {
    $i++;
    $sel="";$url="";
    if(!is_array($selected) && $selected==$row['id']) {$sel="selected";}
    elseif (is_array($selected)) {
      reset($selected);
      if(in_array($row['id'], $selected)) $sel="selected";
    } 

    if($row['url']!="" && $urlRewrite=="") {
      $url=$row['url'];
    }

    if($row['url']=="" || $urlRewrite!="") {
      if($useLayout=="") {
        $lay=retRow("gestione_layout",$row['id_gestione_layout']);
        
        if($lay['file']=="") {
          if($strnome['nome']=="magazzino") {
            $tmpff=Table2ByTable1("categorie","categorie",$row['id'],"","");
            if(count($tmpff)>0) {
              $lay=retRow("gestione_layout",$maglay[0]['directory']);  
            }else{
              $lay=retRow("gestione_layout",$maglay[0]['nodo']);  
            }  
          }      
        }
        
        $url=trim($lay['file'])."?menid=".trim($row['id']);
        if($url=="") $url=""; 
      }else{
        $url="?menid=".trim($row['id']);
      }
    }
    
    array_push($tmpArr, $row['id']);
    if($edit=="1") $att="";else $att="attivo='1'";
    
    $rs2=Table2ByTable1("categorie","categorie",$row['id'],$att,"Ordinamento ASC");
    /*
    if(count($rs2)>0) {
      if($selected!="" || is_array($selected)) {
        $tarr=array();
        $tarr=getStrutturaEx($rs2,$tarr,"",$edit,"",$nome);
      }
    }
    */
    
    $l_img="";
    if(count($rs2)>0) {
      if($selected!="" || is_array($selected)) {
        if(!is_array($selected) && (in_array($row['id'], $ramo) || $selected==$row['id'])) {       
          if($sel!="") $l_imm="rsStrutture-c-open-current";
          if($sel=="") $l_imm="rsStrutture-c-open";  
        }elseif(is_array($selected)){
          $tmpArr = array();
          reset($selected);
          while (list($key2, $row2) = each($selected)) {
            if(in_array($row2, $tarr) || $row2==$row['id']) {
              if($sel!="") $l_imm="rsStrutture-c-open-current";
              if($sel=="") $l_imm="rsStrutture-c-open";
            }else{
              $l_imm="rsStrutture-c-closed";  
            }
          }  
        } else {
          $l_imm="rsStrutture-c-closed";  
        }
      } else {
        $l_imm="rsStrutture-c-closed";  
      }
    } else {
      if($sel!="") $l_imm="rsStrutture-c-current";
      if($sel=="") $l_imm="rsStrutture-c-current-closed";
    }
    $l_img="<div class='ez-box ".$l_imm." rsStrutture-a-ico'></div>";
    
    $md="";$mu="";
    if($edit=="1") {
      if($row['attivo']=="1") {
        $pubbl_class="rsStrutture-edit-pubbl";
        $pubbl_tit=ln("Non pubblicare questo elemento");
      }else{
        $pubbl_class="rsStrutture-edit-not-pubbl";
        $pubbl_tit=ln("Pubblica questo elemento");  
      }
      //if($rs[$i]["id"]!="") $md="<input type='button' rsId='".$row["id"]."' rsName='".addslashes($nome)."' rsIdNext='".$rs[$i]["id"]."' class='rsStrutture-edit-movedodwn' title='".ln("sposta in basso")."' />";
      //if($rs[$i-2]["id"]!="") $mu="<input type='button' rsId='".$row["id"]."' rsName='".addslashes($nome)."' rsIdPrevious='".$rs[$i-2]["id"]."' class='rsStrutture-edit-moveup' title='".ln("sposta in alto")."' />";
      $subt="<input type='button' rsId='".$row["id"]."' rsName='".addslashes($nome)."'class='rsStrutture-edit-sub-table' title='".ln("elementi collegati")."' />";
      $dd="<input type='button' rsId='".$row["id"]."' rsName='".addslashes($nome)."'class='rsStrutture-edit-delete' title='".ln("elimina")."' />";
      $ins="<input type='button' rsId='".$row["id"]."' rsName='".addslashes($nome)."'class='rsStrutture-edit-addcat' title='".ln("inserisci sottolivello")."' />";
      $mod="<input type='button' rsId='".$row["id"]."' rsName='".addslashes($nome)."'class='rsStrutture-edit-mod' title='".ln("modifica nome")."' />";
      $pubbl="<input type='button' rsId='".$row["id"]."' rsName='".addslashes($nome)."' class='".$pubbl_class."' title='".$pubbl_tit."' />";
    }
    
    $link_class="";$link_sel="";$barred=0;
    if($row['attivo']!="1") $link_class="rsStrutture-a-deactivate";
    if($sel=="selected") $link_sel="rsStrutture-a-selected";
    if($row['attivo']=="0") $barred=1;
    $isusersys=isUserSystem();
    if(($row['is_system']=="1" && $isusersys) || $row['is_system']=="0" || $row['is_system']=="2") { ?>
      <li id="<?php echo $row['id']; ?>" class='li<?php echo $i; ?> <?php echo $sel; ?> id<?php echo $row['id']; ?> rsStrutture-li rsStrutture-barred-<?php echo $barred; ?>' rsName="<?php echo addslashes($nome); ?>" rsNMRel="<?php echo $nmRel; ?>" rsEditable="<?php echo $edit; ?>" rsAjax="<?php echo $useAjax; ?>">
        <!-- Module 2B -->
        <div class="ez-wr rsStrutture-li-content">
          <div class="ez-fr ez-negml ez-50 rsStrutture-li-content-r">
          <?php if($edit!="-1") { ?>  
              <!-- Module 5A -->
              <div class="ez-wr rsStrutture-edit">
              <?php if($row['is_system']!="2" || $isusersys) { ?>  
                <div class="ez-fl ez-negmx ez-16">
                  <div class="ez-box"><?php echo $md; ?></div> 
                </div> 
                <?php if($nmRel=="") { ?>
                  <div class="ez-fl ez-negmx ez-16">
                    <div class="ez-box"><?php echo $subt; ?></div> 
                  </div>
                <? } ?>
                <div class="ez-fl ez-negmr ez-16">
                  <div class="ez-box"><?php echo $dd; ?></div> 
                </div>
                <div class="ez-fl ez-negmr ez-16">
                  <div class="ez-box"><?php echo $ins; ?></div> 
                </div> 
                <div class="ez-fl ez-negmr ez-16">
                  <div class="ez-box"><?php echo $mod; ?></div> 
                </div>
                <div class="ez-last ez-oh">
                  <div class="ez-box"><?php echo $pubbl; ?></div> 
                </div>
              <? } ?>   
              </div>
          <? } ?>  
          </div>
          <div class="ez-last ez-oh rsStrutture-li-content-l">
            <?php if($flash=="" || $flashW==0 || $flashH==0) { ?>
              <a href='<?php echo $url; ?>' class='<?php echo $sel; ?> id<?php echo $row['id']; ?> rsStrutture-a' rsId="<?php echo $row['id']; ?>" rsName="<?php echo addslashes($nome); ?>">
                <!-- Module 2A -->
                <div class="ez-wr">
                  <div class="ez-fl ez-negmr ez-50 rsStrutture-a-l">
                    <?php echo $l_img; ?>
                  </div>
                  <div class="ez-last ez-oh rsStrutture-a-r">
                    <div class="ez-box rsStrutture-a-text <?php echo $link_class; ?> <?php echo $link_sel; ?>" rsId="<?php echo $row['id']; ?>" rsName="<?php echo addslashes($nome); ?>">
                      <?php echo ln(strip_tags($row['nome'])); ?>  
                    </div>
                  </div>
                </div>
              </a>
            <? }else{ ?>
              <div class="rsStrutture-flash">
                <object classid="classid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="<?php echo $flashW; ?>" height="<?php echo $flashH; ?>" style="width:<?php echo $flashW; ?>;height:<?php echo $flashH; ?>;">
                  <param name=movie value="<?php echo $flash; ?>">     
                  <param name=wmode value="transparent">
                  <param name=FlashVars value="testo=<?php echo ln(strip_tags($row['nome'])); ?>">
                  <param name=FlashVars value="path=<?php echo $url; ?>">
                  <param name=FlashVars value="csel=<?php echo $sel; ?>">
                  <embed src="<?php echo $flash; ?>" FlashVars="testo=<?php echo ln(strip_tags($row['nome'])); ?>&path=<?php echo $url; ?>&csel=<?php echo $sel; ?>" wmode="transparent" width="<?php echo $flashW; ?>" height="<?php echo $flashH; ?>" style="width:<?php echo $flashW; ?>;height:<?php echo $flashH; ?>;"></embed>
                </object>
              </div>
            <? } ?>
          </div>
        </div>
      </li>
      <?php
    }
    
    if(count($rs2)>0) {
      if($selected!="" || is_array($selected)) {
        if(!is_array($selected) && (in_array($row['id'], $ramo) || $selected==$row['id'])) {
          $tmpArr=stampaStrutturaEx($rs2,$padre+1,$selected,$useLayout,$tmpArr,$edit,$urlRewrite,$nome,$useAjax,$flash,$flashW,$flashH,$nmRel,$ramo);
        }elseif(is_array($selected)){
          $tmpArr = array();
          reset($selected);
          while (list($key2, $row2) = each($selected)) {
            if(in_array($row2, $tarr) || $row2==$row['id']) {
              $tmpArr=array_merge_recursive($tmpArr, stampaStrutturaEx($rs2,$padre+1,$selected,$useLayout,$tmpArr,$edit,$urlRewrite,$nome,$useAjax,$flash,$flashW,$flashH,$nmRel,$ramo));
              break;
            }
          }  
        }
      }
    }
  }
  if($padre!=1) echo "</ul><ul class='ul".($padre-1)." rsStrutture-ul'>";
  return $tmpArr;
}

function stampaStruttura2($nome,$selected="",$useLayout="",$cat="",$edit="",$urlRewrite="",$printSub="1") {
  global $config_table_prefix;
  $objUtility = new Utility;
  $rs=getTable("strutture","","nome='$nome' AND attivo='1'");
  $where="attivo='1'";
  
  if($cat=="") {
    $rs2=Table2ByTable1("strutture","categorie",$rs[0]['id'],$where,"Ordinamento ASC");
  } elseif (is_array($cat)) {
    $rs2 = array();
    while (list($key, $row) = each($cat)) {
      $rs2=array_merge_recursive($rs2, Table2ByTable1("categorie","categorie",$row,$where,"Ordinamento ASC"));
    }
  } else {
    $rs2=Table2ByTable1("categorie","categorie",$cat,$where,"Ordinamento ASC");
  }
  
  echo "<div id='".$rs[0]['css']."'>";
  $tmpArr=array();
  
  $ramo=getRamoEx($selected);
  
  $tmpArr=stampaStrutturaEx2($rs2,1,$selected,$useLayout,$tmpArr,$edit,$urlRewrite,$printSub,$nome,$ramo);
  echo "</ul></div>";
  return $tmpArr;
}

function stampaStrutturaEx2($rs,$padre,$selected="",$useLayout="",$tmpArr="",$edit="",$urlRewrite="",$printSub="1",$struttura="",$ramo) {
  global $objUtility;

  $i=0;
  if($padre==1) echo "<ul class='ul".$padre."'>";
  if($padre!=1) echo "</ul><ul class='ul".$padre."'>";

  while (list($key, $row) = each($rs)) {
    $i++;
    $sel="";
    $url="";
    if(!is_array($selected) && $selected==$row['id']) {$sel="selected";}
    elseif (is_array($selected)) {
      reset($selected);
      if(in_array($row['id'], $selected)) $sel="selected";
    } 

    if($row['url']!="" && $urlRewrite=="") {
      $url=$row['url'];
    }

    if($row['url']=="" || $urlRewrite!="") {
      if($useLayout=="") {
        $lay=retRow("gestione_layout",$row['id_gestione_layout']);
        $url=trim($lay['file'])."?menid=".trim($row['id']);
      }
      if($useLayout!="") $url="?menid=".trim($row['id']);
    }

    $link="";
    
    $where="attivo='1'";
    
    //$figli=getStruttura($struttura,$row['id']);
    $ord="";
    if($printSub==1) $ord="Ordinamento ASC";
    $figli=Table2ByTable1("categorie","categorie",$row['id'], $where, $ord);
    $clsLastChild="";
    if(count($figli)==0) $clsLastChild="last-child";
    
    $contenuti=Table2ByTable1("categorie","contenuti",$row['id'],"attivo='1'","");
    if(count($contenuti)>0) {
      $link="<a href='$url' class='$clsLastChild $sel id".$row['id']."'><span class='$sel'>".strip_tags(ln($row['nome']))."</span></a>";
    } else {
      $link="<a href='$url' class='$clsLastChild $sel id".$row['id']."'><span class='$sel'>".strip_tags(ln($row['nome']))."</span></a>";
    //$link="<span class='$sel' >".$row['nome']."</span>";
    }
    
    array_push($tmpArr, $row['id']);
    //if($printSub==1) $rs2=Table2ByTable1("categorie","categorie",$row['id'],$where,"Ordinamento ASC");
    
    /*
    if(count($figli)>0) {
      if($selected!="" || is_array($selected)) {
        $tarr=array();
        //$tarr=getStrutturaEx($figli,$tarr,"",$edit);
        
      }
    }
    */
    
    $tst="style='";
    $l_img="";
      if($row['attivo']!="1") $tst.="text-decoration:line-through;";
    $tst.="'";
    
    if($i==count($rs)) $last="last";else $last="";
    
    echo "<li class='li".$i." ".$sel." ".$last." id".$row['id']."' ".$tst." >".$l_img.$link."</li>";
    
    if(count($figli)>0) {
      if($selected!="" || is_array($selected)) {
        if(!is_array($selected) && (in_array($row['id'], $ramo) || $selected==$row['id'])) {
          $tmpArr=stampaStrutturaEx2($figli,$padre+1,$selected,$useLayout,$tmpArr,$edit,$urlRewrite,$printSub,$struttura,$ramo);
        }elseif(is_array($selected)){
          $tmpArr = array();
          reset($selected);
          while (list($key2, $row2) = each($selected)) {
            if(in_array($row2, $tarr) || $row2==$row['id']) {
              $tmpArr=array_merge_recursive($tmpArr, stampaStrutturaEx2($figli,$padre+1,$selected,$useLayout,$tmpArr,$edit,$urlRewrite,$printSub,$struttura,$ramo));
              break;
            }
          }  
        }
      }
    }
  }
  if($padre!=1) echo "</ul><ul class='ul".($padre-1)."'>";
  return $tmpArr;
}

function curPageURL($query="") {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  if($query=="") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  }elseif($query=="-1"){
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["PHP_SELF"];  
  }
 } else {
  if($query=="") {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }elseif($query=="-1"){
    $pageURL.=$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];  
  }
 }
 return $pageURL;
}

function curServerName() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    $pageURL .= $_SERVER["SERVER_NAME"]; // Aggiunge solo il nome del server senza porta
    return $pageURL;
}

function curPageName() {
 return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

function findStruttura($nome,$find) {
  $struttura=getStrutturaFull($nome);
  $ret=array();
  while (list($key, $row) = each($struttura)) {
    if($row['nome']==$find) array_push($ret,$row);    
  }
  
  return $ret;  
}

function getStruttura($nome,$cat="",$sub="",$edit="") {
  if($edit=="") $wh="attivo='1'";
  if($edit!="") $wh="attivo=attivo";
  
  if($cat=="") {
    $rs=getTable("strutture","","nome='$nome' AND $wh");
    $rs2=Table2ByTable1("strutture","categorie",$rs[0]['id'],$wh,"Ordinamento ASC");
  } else {
    $rs2=Table2ByTable1("categorie","categorie",$cat,$wh,"Ordinamento ASC");
  }

  $tmpArr=array();

  $tmpArr=getStrutturaEx($rs2,$tmpArr,$sub,$edit);
  return $tmpArr;
}

function getStrutturaEx($rs,$tmpArr,$sub="",$edit="") {
  $wh="";
  $i=0;
  if($edit=="") $wh="attivo='1'";
  
  while (list($key, $row) = each($rs)) {
    $i++;

    array_push($tmpArr, $row['id']);
    if($sub=="") {
      $rs2=Table2ByTable1("categorie","categorie",$row['id'],$wh,"Ordinamento ASC");
      if(count($rs2)>0) {
        $tmpArr=getStrutturaEx($rs2,$tmpArr,$sub);
      }
    }
  }
  return $tmpArr;
}

function getStrutturaFull($nome,$cat="",$sub="",$edit="") {
  $wh="";
  $wh2="";
  if($edit=="") $wh="attivo='1'";
  if($edit=="") $wh2="AND attivo='1'";
  
  $rs=getTable("strutture","","nome='".addslashes($nome)."' $wh2");

  if($cat=="") {
    $rs2=Table2ByTable1("strutture","categorie",$rs[0]['id'],$wh,"Ordinamento ASC");
  } else {
    $rs2=Table2ByTable1("categorie","categorie",$cat,$wh,"Ordinamento ASC");
  }

  $tmpArr=array();

  $tmpArr=getStrutturaFullEx($rs2,$tmpArr,$sub);
  return $tmpArr;
}

function getStrutturaFullEx($rs,$tmpArr,$sub,$edit="") {
  $wh="";
  $i=0;
  if($edit=="") $wh="attivo='1'";
  
  while (list($key, $row) = each($rs)) {
    $i++;

    array_push($tmpArr, $row);
    if($sub=="") {
      $rs2=Table2ByTable1("categorie","categorie",$row['id'],$wh,"Ordinamento ASC");
      if(count($rs2)>0) {
        $tmpArr=getStrutturaFullEx($rs2,$tmpArr,$sub);
      }
    }
  }
  return $tmpArr;
}

function getCategoria($struttura="") {
  $menid=$_GET['menid'];
  if($menid=="") {
    
    if($struttura!="") {
      $arr=getStrutturaFull($struttura);
      for($j=0;$j<count($arr);$j++) {
        if($arr[$j]['home_page']=='1') return $arr[$j]['id'];
      }
    }
    
    $rs=getTable("categorie","","home_page='1'");
    $menid=$rs[0]['id'];
  }
  return $menid;
}

function getContenuti($cat="") {
  $menid=getCategoria();
  if($cat!="") $menid=$cat;

  if($menid!="") {
    $rs=Table2ByTable1("categorie","contenuti",$menid,"attivo='1'","");
    $id=$rs[0]['id'];
    return $id;
  }
  return false;
}

function getIntestazioni($cat="") {
  $id=getContenuti($cat);
  if($id!=false) {
    $rs=Table2ByTable1("contenuti","1_intestazioni",$id,"attivo='1'","");
    return $rs;
  }
  return false;
}

function getTitoli($cat="") {
  $id=getContenuti($cat);
  if($id!=false) {
    $rs=Table2ByTable1("contenuti","2_titoli_h1_h2_h3",$id,"attivo='1'","");
    return $rs;
  }
  return false;
}

function getTesti ($cat="") {
  $menid=getCategoria();
  if($cat!="") $menid=$cat;
  
  if($menid!="") {
    $rs=Table2ByTable1("categorie","3_testi",$menid,"attivo='1'","");
    return $rs;
  }
  return false;
}

function location ($cat="") {
  $id=getCategoria();
  if($cat!="") $id=$cat;
  
  if($id!=false) {
    $rs=Table2ByTable1("categorie","4_mappa_google",$id,"attivo='1'","");
    $ret=array();
    while (list($key, $row) = each($rs)) {
      $rs2=retRow("gmap",$row['id_gmap']);
      if($rs2['Attivo']==1) array_push($ret,$rs2);  
    }
    if(count($ret)>0) return $ret; 
  }
  return false;
}

function getForms ($cat="") {
  $id=getContenuti($cat);
  if($id!=false) {
    $rs=Table2ByTable1("contenuti","5_forms",$id,"attivo='1'","");
    $rs2=retRow("lista_forms",$rs[0]['id_lista_forms']);
    if($rs2['Attivo']==1) return $rs2;
  }
  return "";
}

function getFooters($idusers="") {
  if($idusers!="") $idusers="AND id_users='$idusers'";
  $rs=getTable("footers",$ordinamento="",$where="attivo='1' $idusers");
  return $rs;
}

function box($testo,$css="") {
  $objUtility = new Utility;
  if($css=="") $css="box";
  $_SESSION['rsOpenBox']=1;
  ?>
  <div id="rsBox-alert" style="display:none;">
    <div id="<?=$css?>">
      <div class="ok">
          <?php echo $testo; ?>
      </div>
      <span><a class="box-close" href="#" > <?php echo ln("chiudi"); ?> <i class="fa fa-close"></i></a></span>
    </div>
  </div>
  <?
}

function confirm($testo,$btnYes,$btnNo,$res,$jsYes,$jsNo) { ?>
<div id="box" style="visibility:hidden;height:1px;">
  <div class="ok">
      <?=$testo?>
  </div>
    <span style="padding-right:15px;"><a href="#" onClick="mostra('box');<?=$res?>='1';<?=$jsYes?>;"><?=$btnYes?></a></span>
    <span><a href="#" onClick="mostra('box');<?=$res?>='-1';<?=$jsNo?>;"><?=$btnNo?></a></span>
</div>
<?
}

function permissionField($rs) {
  global $config_table_prefix;
  
  $single=false;
  $multiple=false;
  $isSystem=false;
  $isBackoffice=false;
  $isAreaRis=false;
  $isNotAreaRis=false;
  
  if(is_array($rs[0])) $multiple=true;
  if(is_array($rs)) $single=true;

  if(!$single && !$multiple) return $rs;
  
  $userid=$_SESSION["user_id"];
  if($userid=="") {
    $userid=$_SESSION["userris_id"];
    if($userid>0) $isAreaRis=true;else $isNotAreaRis=true; 
  }else{
    $isBackoffice=true;
    $roles=Table1ByTable2_pointed("roles","users_list","users",$userid,"","(".$config_table_prefix."roles.issystem='1')");
    if(count($roles)>0) $isSystem=true;    
  }

  if($multiple) {
    while (list($key, $row) = each($rs)) {
      if($isSystem) {
        $newField=Table2ByTable1("rstbl2_campi","rstbl2_campi_system",$row['id'],"","");
        if(count($newField)>0) {
          if(isset($rs[$key]['titolo_visualizzato'])) $rs[$key]['titolo_visualizzato']=$newField[0]['titolo_visualizzato'];
          if(isset($rs[$key]['rsPower'])) $rs[$key]['rsPower']=$newField[0]['rsPower'];
          if(isset($rs[$key]['id_rstbl2_gruppi'])) $rs[$key]['id_rstbl2_gruppi']=$newField[0]['id_rstbl2_gruppi'];
        } 
      }
      
      if($isBackoffice && !$isSystem) {
        $newField=Table2ByTable1("rstbl2_campi","rstbl2_campi_backoffice",$row['id'],"","");
        if(count($newField)>0) {
          if(isset($rs[$key]['titolo_visualizzato'])) $rs[$key]['titolo_visualizzato']=$newField[0]['titolo_visualizzato'];
          if(isset($rs[$key]['rsPower'])) $rs[$key]['rsPower']=$newField[0]['rsPower'];
          if(isset($rs[$key]['id_rstbl2_gruppi'])) $rs[$key]['id_rstbl2_gruppi']=$newField[0]['id_rstbl2_gruppi']; 
        }
      }
      
      if($isAreaRis) {
        $newField=Table2ByTable1("rstbl2_campi","rstbl2_campi_loggati",$row['id'],"","");
        if(count($newField)>0) {
          if(isset($rs[$key]['titolo_visualizzato'])) $rs[$key]['titolo_visualizzato']=$newField[0]['titolo_visualizzato'];
          if(isset($rs[$key]['rsPower'])) $rs[$key]['rsPower']=$newField[0]['rsPower'];
          if(isset($rs[$key]['id_rstbl2_gruppi'])) $rs[$key]['id_rstbl2_gruppi']=$newField[0]['id_rstbl2_gruppi'];
        } 
      }
      
      if($isNotAreaRis) {
        $newField=Table2ByTable1("rstbl2_campi","rstbl2_campi_non_loggati",$row['id'],"","");
        if(count($newField)>0) {
          if(isset($rs[$key]['titolo_visualizzato'])) $rs[$key]['titolo_visualizzato']=$newField[0]['titolo_visualizzato'];
          if(isset($rs[$key]['rsPower'])) $rs[$key]['rsPower']=$newField[0]['rsPower'];
          if(isset($rs[$key]['id_rstbl2_gruppi'])) $rs[$key]['id_rstbl2_gruppi']=$newField[0]['id_rstbl2_gruppi'];
        }
      } 
    }
  }elseif($single){
    if($isSystem) {
      $newField=Table2ByTable1("rstbl2_campi","rstbl2_campi_system",$rs['id'],"","");
      if(count($newField)>0) {
        if(isset($rs['titolo_visualizzato'])) $rs['titolo_visualizzato']=$newField[0]['titolo_visualizzato'];
        if(isset($rs['rsPower'])) $rs['rsPower']=$newField[0]['rsPower'];
        if(isset($rs['id_rstbl2_gruppi'])) $rs['id_rstbl2_gruppi']=$newField[0]['id_rstbl2_gruppi'];
      } 
    }
    
    if($isBackoffice && !$isSystem) {
      $newField=Table2ByTable1("rstbl2_campi","rstbl2_campi_backoffice",$rs['id'],"","");
      if(count($newField)>0) {
        if(isset($rs['titolo_visualizzato'])) $rs['titolo_visualizzato']=$newField[0]['titolo_visualizzato'];
        if(isset($rs['rsPower'])) $rs['rsPower']=$newField[0]['rsPower'];
        if(isset($rs['id_rstbl2_gruppi'])) $rs['id_rstbl2_gruppi']=$newField[0]['id_rstbl2_gruppi']; 
      }
    }
    
    if($isAreaRis) {
      $newField=Table2ByTable1("rstbl2_campi","rstbl2_campi_loggati",$rs['id'],"","");
      if(count($newField)>0) {
        if(isset($rs['titolo_visualizzato'])) $rs['titolo_visualizzato']=$newField[0]['titolo_visualizzato'];
        if(isset($rs['rsPower'])) $rs['rsPower']=$newField[0]['rsPower'];
        if(isset($rs['id_rstbl2_gruppi'])) $rs['id_rstbl2_gruppi']=$newField[0]['id_rstbl2_gruppi'];
      } 
    }
    
    if($isNotAreaRis) {
      $newField=Table2ByTable1("rstbl2_campi","rstbl2_campi_non_loggati",$rs['id'],"","");
      if(count($newField)>0) {
        if(isset($rs['titolo_visualizzato'])) $rs['titolo_visualizzato']=$newField[0]['titolo_visualizzato'];
        if(isset($rs['rsPower'])) $rs['rsPower']=$newField[0]['rsPower'];
        if(isset($rs['id_rstbl2_gruppi'])) $rs['id_rstbl2_gruppi']=$newField[0]['id_rstbl2_gruppi'];
      }
    }    
  }
  
  reset($rs);
  return $rs;
}                                 

function replaceEcomerceMarkers($str) {
  $objUtility = new Utility;
  $objConfig = new ConfigTool();
  $root=$_SERVER['SERVER_NAME'].$objUtility->getPathRoot();
  $rs=getTable("ecommerce_modalita_pagamenti","Ordinamento ASC","attivo='1'");
  $ecomm_user=getTable("ecommerce_anagrafica_predefinita","","attivo='1'");
  $ecomm_user=retRow("users",$ecomm_user[0]['id_users']);
  $ecomm_logo=convertToJpg(retFileAbsolute($ecomm_user['immagine_file'],150));
  $curlan=getCurLan();
  $curlanid=$curlan["id"];
  
  
  $str=str_replace("#SERVER_NAME#", $_SERVER['SERVER_NAME'], $str);
  $str=str_replace("#ecomm_ragionesociale#", $ecomm_user['ragionesociale'], $str);
  $str=str_replace("#ecomm_conto_postale#", $ecomm_user['conto_postale'], $str);
  $str=str_replace("#ecomm_contocorrente#", $ecomm_user['contocorrente'], $str);
  $str=str_replace("#ecomm_banca#", $ecomm_user['banca'], $str);
  $str=str_replace("#ecomm_iban#", $ecomm_user['iban'], $str);
  if(file_exists($ecomm_logo)) {
    $str=str_replace("#ecomm_immagine_file#", "<img src='".$ecomm_logo."' />", $str);
  }else{
    $str=str_replace("#ecomm_immagine_file#", "", $str);  
  }
  $str=str_replace("#ecomm_partitaiva#", $ecomm_user['partitaiva'], $str);
  $str=str_replace("#ecomm_indirizzo#", $ecomm_user['indirizzo'], $str);
  $str=str_replace("#ecomm_citta#", $ecomm_user['citta'], $str);
  $str=str_replace("#ecomm_provincia#", $ecomm_user['provincia'], $str);
  $str=str_replace("#ecomm_cap#", $ecomm_user['cap'], $str);
  $str=str_replace("#ecomm_comune#", $ecomm_user['comune'], $str);
  $str=str_replace("#ecomm_telefono#", $ecomm_user['telefono'], $str);
  $str=str_replace("#ecomm_cellulare#", $ecomm_user['cellulare'], $str);
  $str=str_replace("#ecomm_email#", $ecomm_user['email'], $str);
  $str=str_replace("#ecomm_fax#", $ecomm_user['fax'], $str);
  $str=str_replace("#ecomm_cap#", $ecomm_user['cap'], $str);
  $str=str_replace("#ecomm_comune#", $ecomm_user['comune'], $str);  
  
  $str=str_replace("#CURLAN#", $curlanid, $str);
  
  return $str;
}

function gmap() {
  $rs=location();
  
  if(!$rs) return; 
  
  while (list($key, $row) = each($rs)) {
    $or_name=onlyreadables($row['nome_della_mappa']);
    $coords=$row['Url'];
  }
  
  if($coords!=""){ ?>
      <div id="<?php echo $or_name; ?>" class="gmap"></div>
  <?php }
}

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

function gmapInfo($id) {
  $row=retRow("gmap",$id);
  $Logo=retFile($row['Logo_file'],90);
  $Denominazione=$row['Denominazione'];
  $Indirizzo=$row['Indirizzo'];
  $Localita=$row['Localita'];
  $Telefono=$row['Telefono'];
  $Email=$row['Email'];
  ob_start();
  ?>
  <div id='gmap-info'>
    <div class='gmap-logo'>
      <?php if($Logo) { ?><img src='<?php echo $Logo; ?>' /><? } ?>
    </div>
    <div>
      <span class='gmap-denominazione'><?=$Denominazione?></span>
      <br>
      <span class='gmap-indirizzo'><?=$Indirizzo?></span>
      <br>
      <span class='gmap-localita'><?=$Localita?></span>
      <br>
      <span class='gmap-telefono'><?=$Telefono?></span>
      <br>
      <span class='gmap-email'>
        <a href='mailto:<?=$Email?>'><?=$Email?></a>
      </span>
    </div>
  </div>
  <?php
  $info=ob_get_contents(); 
  ob_end_clean();
  
  return compressHtml($info);
}

function comboBox($table,$field1="",$field2="",$selected="",$multiple="",$onchange="",$echoId="",$nome="",$where="", $class="",$ordine="",$firstempty="",$translate="") {
  global $config_table_prefix;
  if($table=="") return;
  if($nome=="") $nome=$table;
  
  if($class=="") $class="default";
  
  if($field1=="") {
    $sql="SHOW FIELDS FROM ".$config_table_prefix.$table;
    $query = mysql_query ($sql);
    $utility = new Utility;
    $rs = $utility->buildRecordset($query);
    $field1=$rs[1]['Field'];
  }
  
  $pointer1="";
  $pointer2="";
  $struttura1="";
  $struttura2="";
  
  $tmpField=$field1;
  if(strpos($tmpField, "_str_")) {
    $struttura=substr($tmpField, strpos($tmpField, "_str_"), strlen($tmpField)-strpos($tmpField, "_str_"));
    $tmpField=str_replace($struttura,"",$tmpField);
    $struttura1=str_replace("_str_","",$struttura);
    $pointer1="categorie";
  }
  
  $tmpField=$field2;
  if(strpos($tmpField, "_str_")) {
    $struttura=substr($tmpField, strpos($tmpField, "_str_"), strlen($tmpField)-strpos($tmpField, "_str_"));
    $tmpField=str_replace($struttura,"",$tmpField);
    $struttura2=str_replace("_str_","",$struttura);
    $pointer2="categorie";
  }
  
  $tmpField=$field1;
  if(!(strpos($tmpField, "id_")===false) && $pointer1=="") {
    $pointer1=str_replace("id_", "", $tmpField);
  }
  
  $tmpField=$field2;
  if(!(strpos($tmpField, "id_")===false) && $pointer2=="") {
    $pointer2=str_replace("id_", "", $tmpField);
  }
  
  if($ordine=="") {
    $rs = getTable($table,"$field1 ASC",$where);
  } else {
    $rs = getTable($table,"$ordine ASC",$where);  
  }
  

  if (count($rs)) {
    ?>
    <select id="<?=$nome?>" name="<?=$nome?>" size="1" class="<?php echo $class; ?>" <?php if($multiple!="") { ?> multiple="multiple" style="height:100px;" <? } ?> <?php if($onchange!="") { ?> onchange="<?=$onchange?>" <? } ?> >
      <?php if($firstempty!="-1") { ?><option value=""></option><? } ?>
      <?php
      while (list($key, $rowTmp) = each($rs)) {
        if($pointer1!="") {
          $zrs=retRow($pointer1,$rowTmp[$field1]);
          $rowTmp[$field1]=$zrs[1];
        }
        
        if($pointer2!="") {
          $zrs=retRow($pointer2,$rowTmp[$field2]);
          $rowTmp[$field2]=$zrs[1];
        }
      ?>
        <option value="<?php if($echoId=="") {echo $rowTmp['id'];}else {echo $rowTmp[$field1];} ?>"<?php echo ($rowTmp['id']==$selected) ? " selected" : "" ?>><?php if($translate==1){echo ln($rowTmp[$field1])." ".ln($rowTmp[$field2]);}else{echo $rowTmp[$field1]." ".$rowTmp[$field2]; } ?></option>
      <? } ?>
    </select>
  <?php
  }
}

function retMese($mese) {
  if($mese==1) $strmese="gennaio";
  if($mese==2) $strmese="febbraio";
  if($mese==3) $strmese="marzo";
  if($mese==4) $strmese="aprile";
  if($mese==5) $strmese="maggio";
  if($mese==6) $strmese="giugno";
  if($mese==7) $strmese="luglio";
  if($mese==8) $strmese="agosto";
  if($mese==9) $strmese="settembre";
  if($mese==10) $strmese="ottobre";
  if($mese==11) $strmese="novembre";
  if($mese==12) $strmese="dicembre";

  return $strmese;
}

function FirstCharToUpper ($str) {
  $res=substr($str, 0, 1);
  $res=strtoupper($res).substr($str, 1);

  return $res;
}

function dataIta($data="") {
  if($data=="") $data=date("Y-m-d");
  
  $data=str_replace(",", " ", $data);
  $data=explode(" ", $data);
  $ora=$data[1];
  $data=$data[0];
  
  $data=explode("-", $data);
  $data=array_reverse($data);
  $data=implode("-", $data);

  if($ora!="") $data.=", ".left($ora,5); 
  return $data;
}

function dataItaString($data="") {
  if($data=="") $data=date("Y-m-d");
  
  $data=explode(" ", $data);
  $ora=$data[1];
  $data=$data[0];
  
  $mese = array('gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre','dicembre');  
  $giorno = array('domenica','lunedì','martedì','mercoledì','giovedì','venerdì','sabato');  
  $tmpdate=explode('-',$data); 
  $gg=$tmpdate[2];
  $mm=$tmpdate[1];
  $yy=$tmpdate[0];
  $ww=date("w", mktime(0, 0, 0, $mm, $gg, $yy));

  $data=$giorno[$ww]." ".$gg." ".$mese[$mm-1]." ".$yy;
  if($ora!="") $data.=", ".left($ora,5);

  return $data;
}  

function dataItaStringLow($data="") {
  if($data=="") $data=date("Y-m-d");
  
  $data=explode(" ", $data);
  $ora=$data[1];
  $data=$data[0];
  
  $mese = array('gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre','dicembre');  
  $giorno = array('domenica','lunedì','martedì','mercoledì','giovedì','venerdì','sabato');  
  $tmpdate=explode('-',$data); 
  $gg=$tmpdate[2];
  $mm=$tmpdate[1];
  $yy=$tmpdate[0];
  $ww=date("w", mktime(0, 0, 0, $mm, $gg, $yy));

  if(left($gg,1)=="0") $gg=substr($gg, 1, 1);

  $data=$gg." ".$mese[$mm-1];
  if($ora!="") $data.=", ".left($ora,5);

  return $data;
}

function innerJoin($table1, $table2, $id1, $id2, $ordinamento,$filterTheme=true) {
  global $config_table_prefix;
  $objUtility = new Utility;

  $sql = "SELECT * FROM ".$config_table_prefix.$table1." INNER JOIN ".$config_table_prefix.$table2." ON ".$config_table_prefix.$table1.".$id1=".$config_table_prefix.$table2.".$id2 ORDER BY ".$config_table_prefix.$ordinamento;
  $query = mysql_query ($sql);

  $utility = new Utility;
  $rs = $utility->buildRecordset($query);
  $rs=filterByTheme($table1, $rs,$filterTheme);
  return $rs;
}

function Table1ByTable2_pointed($table1,$table2,$table3,$table2_value,$order,$where,$filterTheme=true) {
  global $config_table_prefix;
  $objUtility = new Utility;

  if($where!="") $where="WHERE ".$where;
  if($order!="") $order="ORDER BY ".$order;

  $sql1 = "SELECT ".$config_table_prefix.$table2.".* FROM ".$config_table_prefix.$table2." INNER JOIN ".$config_table_prefix.$table3." ON ".$config_table_prefix.$table2.".id_".$table3."=".$config_table_prefix.$table3.".id WHERE ".$config_table_prefix.$table3.".id='$table2_value'";
  $sql2 = "SELECT `".$config_table_prefix.$table1."#".$table2."_nm`.* FROM `".$config_table_prefix.$table1."#".$table2."_nm` INNER JOIN ($sql1) AS T1 ON `".$config_table_prefix.$table1."#".$table2."_nm`.id_".$table2."=T1.id";
  $sql = "SELECT ".$config_table_prefix.$table1.".* FROM ".$config_table_prefix.$table1." INNER JOIN ($sql2) AS T2 ON ".$config_table_prefix.$table1.".id=T2.id_".$table1." ".$where." ".$order;
  $query = mysql_query ($sql);
  //echo $sql; 
  $utility = new Utility;
  $rs = $utility->buildRecordset($query);
  $rs=filterByTheme($table1, $rs,$filterTheme);
  return $rs;
}

function Table2ByTable1($table1,$table2,$table1_value,$table2_where,$order,$filterTheme=true) {
  global $config_table_prefix;
  $objUtility = new Utility;

  $tid=$table2;
  if($table1==$table2) $tid=$table2."_self";

  if($table2_where!="") $where="WHERE ".$table2_where;
  if($order!="") $order="ORDER BY ".$order;

  $sql = "SELECT ".$config_table_prefix.$table2.".* FROM ".$config_table_prefix.$table2." INNER JOIN (SELECT * FROM `".$config_table_prefix.$table1."#".$table2."_nm` WHERE id_".$table1."='$table1_value') AS T1 ON ".$config_table_prefix.$table2.".id=T1.id_".$tid." $where $order";
  //echo $sql;
  
  $query = mysql_query ($sql);

  $utility = new Utility;
  $rs = $utility->buildRecordset($query,$filterTheme);
  $rs=filterByTheme($table2, $rs);
  return $rs;
}

function Table1ByTable2($table1,$table2,$table2_value,$table1_where,$order,$filterTheme=true) {
  global $config_table_prefix;
  $objUtility = new Utility;

  $tid=$table1;
  if($table1==$table2) $tid=$table1."_self";

  if($table1_where!="") $where="WHERE ".$table1_where;
  if($order!="") $order="ORDER BY ".$order;

  if($table2_value!="") $wh2="WHERE id_".$table2."='$table2_value'";

  $sql = "SELECT ".$config_table_prefix.$table1.".* FROM ".$config_table_prefix.$table1." INNER JOIN (SELECT * FROM `".$config_table_prefix.$table1."#".$table2."_nm` $wh2) AS T1 ON ".$config_table_prefix.$table1.".id=T1.id_".$tid." $where $order";
  $query = mysql_query ($sql);

  $utility = new Utility;
  $rs = $utility->buildRecordset($query);
  $rs=filterByTheme($table1, $rs,$filterTheme);
  return $rs;
}

function query($sql) {
    $objUtility = new Utility;
    
    $query = mysql_query ($sql);
    if(!$query) return FALSE;
    
    $rs = $objUtility->buildRecordset($query);
    return $rs;
}

function getTable($table,$ordinamento="",$where="",$extra="",$filterTheme=true) {
  global $config_table_prefix;
  $objUtility = new Utility;

  if($table=="") return;

  if($ordinamento!="") $ord="ORDER BY $ordinamento";
  if($where!="") $wh="WHERE $where";

  $sql = "SELECT * FROM `".$config_table_prefix.$table."` ".$wh." ".$extra." ".$ord;
  
  $query = mysql_query ($sql);
  if(!$query) return FALSE;
  
  $rs = $objUtility->buildRecordset($query);
  $rs=filterByTheme($table, $rs,$filterTheme);
  return $rs;
}

function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

function filterByTheme($table, $rs,$filterTheme=true) {
  global $config_table_prefix;
  $objUtility = new Utility;
  $objConfig = new ConfigTool();
  
  $useTheme=$objConfig->get("use_themes");
  if($useTheme==1 && $filterTheme==true){
    $sql = "SELECT * FROM `".$config_table_prefix."themes_tables` WHERE (`nome`='".$table."' AND `attivo`='1')";
    $query = mysql_query ($sql);
    if(!$query) return $rs;
    $themes_tables = $objUtility->buildRecordset($query);
    
    if(count($themes_tables)==0 && $table!="magazzino_articoli") return $rs; 
    
    $rs_ret=array();
    
    $tema=getTema();
    $sql = "SELECT * FROM `".$config_table_prefix."storico_users` WHERE (`domain`='".$tema."' AND `table`='".$table."' AND `deleted` = 0)";
    
    $query = mysql_query ($sql);
    if(!$query) return $rs;
    $allowed_rows = $objUtility->buildRecordset($query);
    
    while (list($key, $row) = each($rs)) {
       $br=false;
       if($table=="magazzino_articoli"){
         $mag_art_tema=getTable("magazzino_articoli_tema","id DESC","id_magazzino_articoli='".$row['id']."'");
         if(count($mag_art_tema)>0){
           $row['Descr1']=$mag_art_tema[0]['descrizione_editor'];
           
           $sconto=$row['Prezzo_cry']-(($row['Prezzo_cry']*$row['sconto'])/100);
           $aumento=($sconto/100)*$mag_art_tema[0]['maggiorazione_utenti_non_registrati'];
           $prezzo_scontato=$sconto+$aumento;
           $aumento_perc=parseToFloat((($prezzo_scontato/$row['Prezzo_cry'])*100)-100);
           $row['sconto']=-$aumento_perc;
           
           $sconto_reg=$row['Prezzo_cry']-(($row['Prezzo_cry']*$row['sconto_reg'])/100);
           $aumento_reg=($sconto/100)*$mag_art_tema[0]['maggiorazione_utenti_registrati'];
           $prezzo_scontato_reg=$sconto_reg+$aumento_reg;
           $aumento_perc_reg=parseToFloat((($prezzo_scontato_reg/$row['Prezzo_cry'])*100)-100);
           $row['sconto_reg']=-$aumento_perc_reg;
           array_push($rs_ret, $row);
           $br=true;
         }else{
            return $rs;   
         }
       }elseif($table=="categorie" && $row['is_system']=="2") {
         array_push($rs_ret, $row);
         $br=true;
       }
       
       if($br==false){
         reset($allowed_rows);
         while (list($key1, $row1) = each($allowed_rows)) {    
            if($row['id']==$row1['row']) {
              array_push($rs_ret, $row);
              break;
            }     
         }
       }
    }    
  }else{
    return $rs;
  } 
  
  //$rs_ret=unique_multidim_array($rs_ret,"id");
  
  return $rs_ret;
}

function retVideo($id,$label="",$w="",$h="") {
  $objUtility = new Utility;
  
  if($w=="") $w=480;
  if($h=="") $w=280;
  
  $file=retFile($id);
  if($file) {
    ?><a class="rsMedia {width:<?php echo $w; ?>, height:<?php echo $h; ?>}" href="<?php echo $file; ?>"><?php echo $label; ?></a><?php
  }else{
    $arr_file=explode(".", $id);
    $arr_file=array_reverse($arr_file);
    $ext=$arr_file[0];
    if($ext!="mov" && $ext!="flv" && $ext!="swf" && $ext!="wmv" && $ext!="avi" && $ext!="mpg" && $ext!="3g2" && $ext!="ram") $ext=-1; 
    ?><a class="rsMedia {width:<?php echo $w; ?>, height:<?php echo $h; ?><?php if($ext==-1) echo ", type:'swf'"; ?> }" href="<?php echo str_replace("http://youtu.be/", "http://youtube.com/v/", $id); ?>"><?php echo $label; ?></a><?  
  } 
}

function cropImage($file,$x,$y,$width,$alte,$addObject="",$absolute=""){
  if($file=="") return false;
  
  global $config_table_prefix;
  $objUtility =& new Utility;
  
  if($absolute=="") $ppth=$objUtility->getPathResourcesDynamic();
  if($absolute=="1") $ppth=$objUtility->getPathResourcesDynamicAbsolute();
  
  if($width==0 && $alte==0) return false;

  $strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
  $strUnique = "crp_".$x."_".$y."_".$width."x".$alte."_".basename($file);
  
  if(file_exists($strDestDir.$strUnique)) return $ppth.$strUnique;
  
  $strDestFile = $strUnique;
  $arr=explode(".", $strDestFile);
  $arr=array_reverse($arr);
  $strExt = $arr[0];
  unset($arr[0]);
  $arr=array_reverse($arr);
  $strDestFile=implode(".", $arr);
  $dpath=$strDestDir.$strDestFile;

  $arr=explode(".", $file);
  $arr=array_reverse($arr);
  $strExt = $arr[0];
  
  list($w, $h) = getimagesize($file);
   
  if($w<$width || $h<$alte) return false;
  
  $cosa = getimagesize($file);

  $min  = imagecreatetruecolor($width, $alte);
  switch($cosa['mime']) {
    case 'image/png':
      $im = imagecreatefrompng($file);
      imagealphablending($min, false);
      imagesavealpha($min, true);
      imagecopyresampled($min, $im, 0, 0, $x, $y, $width, $alte, $width, $alte);
      imagepng($min,$dpath.".".$strExt);
      break;

    case 'image/gif':
      $im = imagecreatefromgif($file);
      imagealphablending($min, false);
      $colorTransparent = imagecolorallocatealpha($min, 0, 0, 0, 127);
      imagefill($min, 0, 0, $colorTransparent);
      imagecopyresampled($min, $im, 0, 0, $x, $y, $width, $alte, $width, $alte);
      imagesavealpha($min, true);
      imagegif($min,$dpath.".".$strExt);
      break;

    case 'image/jpeg':
    case 'image/jpg':
      $im = imagecreatefromjpeg($file);
      imagecopyresampled($min, $im, 0, 0, $x, $y, $width, $alte, $width, $alte);
      imagejpeg($min,$dpath.".".$strExt,100);
      break;
  }
  imagedestroy($min);

  if($addObject!="") {
    $strOggettoPath = $strDestDir;
    $strOggettoExt = $strExt;
    $strOggettoOriginalname = $file;

    $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,ext,path,originalname) VALUES ('$strDestFile','$strOggettoExt','$strOggettoPath','$strOggettoOriginalname') ";
    $query=mysql_query($sql);
    $id_oggetto=mysql_insert_id();
    return $id_oggetto;
  }
   
  return $ppth.$strUnique;    
}

function retFile($id,$w=0,$h=0) {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  $id=explode(";", $id);
  $id=$id[0];
  
  if($w>0 || $h>0) {  
    return imgResizeByID($id,$w,$h);
  }
  
  $sql = "SELECT * FROM ".$config_table_prefix."oggetti WHERE id='$id' ";
  $query = mysql_query ($sql);
  $rs=mysql_fetch_array($query);
  
  if($rs['nome']=="") return "";
  
  $fname1=$objUtility->getPathResourcesDynamicAbsolute().$rs['nome'].".".$rs['ext'];
  $fname2=$objUtility->getPathResourcesPrivateAbsolute().$rs['nome'].".".$rs['ext'];
  
  if(file_exists($fname1)) {
    return $objUtility->getPathResourcesDynamic().$rs['nome'].".".$rs['ext'];
  }elseif(file_exists($fname2)) {
    return $objUtility->getPathResourcesPrivate().$rs['nome'].".".$rs['ext'];
  } else {
    return false;
  }
  
}

function list_directory($dir, $type="") {
	if(!is_dir($dir)) return false;
  $ris=array();
  $handler = opendir($dir);
  while(false !== ($file = readdir($handler))){
	  $tipo = filetype($dir.'/'.$file);
	  
    if($type!="") {
      switch($tipo){
  	    case $type:
    	    if($file != '.' && $file != '..'){
    	      array_push($ris,$file);
    	    }
    	    break;
  	  } 
    }else {
      array_push($ris,$file);  
    }     
  }
  return $ris;  
}

function retFileAbsolute($id,$w=0,$h=0) {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  $id=explode(";", $id);
  $id=$id[0];
  
  if($w>0 || $h>0) {  
    return imgResizeByID($id,$w,$h,"",1);
  }

  $sql = "SELECT * FROM ".$config_table_prefix."oggetti WHERE id='$id' ";
  $query = mysql_query ($sql);
  $rs=mysql_fetch_array($query);

  if($rs['nome']=="") return "";
   
  $fname1=$objUtility->getPathResourcesDynamicAbsolute().$rs['nome'].".".$rs['ext'];
  $fname2=$objUtility->getPathResourcesPrivateAbsolute().$rs['nome'].".".$rs['ext'];
  
  if(file_exists($fname1)) { 
    return $fname1;
  }elseif(file_exists($fname2)) {
    return $fname2;
  } else {
    return false;
  }
}

function retRow($table,$id) {
  global $config_table_prefix;
  $objUtility = new Utility;

  $sql = "SELECT * FROM ".$config_table_prefix.$table." WHERE id='$id' ";
  $query = mysql_query ($sql);
  $rs=mysql_fetch_array($query);
  return $rs;
}

function retType($id) {
  global $config_table_prefix;
  $objUtility = new Utility;

  $sql = "SELECT * FROM ".$config_table_prefix."oggetti WHERE id='$id' ";
  $query = mysql_query ($sql);
  $rs=mysql_fetch_array($query);

  return($rs['ext']);
}

function retName($id) {
  global $config_table_prefix;
  $objUtility = new Utility;

  $sql = "SELECT * FROM ".$config_table_prefix."oggetti WHERE id='$id' ";
  $query = mysql_query ($sql);
  $rs=mysql_fetch_array($query);

  return($rs['originalname']);
}

if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array();
        foreach ($input as $row) {
            if (isset($row[$columnKey])) {
                if ($indexKey !== null && isset($row[$indexKey])) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                } else {
                    $result[] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

function convertToJpg($file,$addObject="",$absolute=""){
  if($file=="") return false;
  
  global $config_table_prefix;
  $objUtility =& new Utility;
  
  if($absolute=="") $ppth=$objUtility->getPathResourcesDynamic();
  if($absolute=="1") $ppth=$objUtility->getPathResourcesDynamicAbsolute();
 
  if(!file_exists($file)) return -1;

  $strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
  $strUnique = "jpg_".basename($file);
  if(file_exists($strDestDir.$strUnique)) return $ppth.$strUnique;

  $strDestFile = $strUnique;
  $arr=explode(".", $strDestFile);
  $arr=array_reverse($arr);
  $strExt = $arr[0];
  unset($arr[0]);
  $arr=array_reverse($arr);
  $strDestFile=implode(".", $arr);
  $dpath=$strDestDir.$strDestFile;

  $arr=explode(".", $file);
  $arr=array_reverse($arr);
  $strExt = $arr[0];
  
  $cosa = getimagesize($file);
  
  switch($cosa['mime']) {
    case 'image/png':
      $im = imagecreatefrompng($file);
      list($width, $height) = getimagesize($file); 
      $output = imagecreatetruecolor($width, $height); 
      $white = imagecolorallocate($output,  255, 255, 255); 
      imagefilledrectangle($output, 0, 0, $width, $height, $white); 
      imagecopy($output, $im, 0, 0, 0, 0, $width, $height);
      imagejpeg($output,$dpath.".jpg",100);
      break;

    case 'image/gif':
      $im = imagecreatefromgif($file);
      list($width, $height) = getimagesize($file); 
      $output = imagecreatetruecolor($width, $height); 
      $white = imagecolorallocate($output,  255, 255, 255); 
      imagefilledrectangle($output, 0, 0, $width, $height, $white); 
      imagecopy($output, $im, 0, 0, 0, 0, $width, $height);
      imagejpeg($im,$dpath.".jpg",100);
      break;

    case 'image/jpeg':
    case 'image/jpg':
      $im = imagecreatefromjpeg($file);
      imagejpeg($im,$dpath.".jpg",100);
      break;
  }
  imagedestroy($im);

  if($addObject!="") {
    $strOggettoPath = $strDestDir;
    $strOggettoExt = "jpg";
    $strOggettoOriginalname = $file;

    $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,ext,path,originalname) VALUES ('$strDestFile','$strOggettoExt','$strOggettoPath','$strOggettoOriginalname') ";
    $query=mysql_query($sql);
    $id_oggetto=mysql_insert_id();
    return $id_oggetto;
  }
   
  return $ppth.$strDestFile.".jpg";  
}

function imgResize ($file,$width=0,$alte=0,$addObject="",$absolute="") {
  if($file=="") return false;
  
  global $config_table_prefix;
  $objUtility =& new Utility;
  
  if($absolute=="") $ppth=$objUtility->getPathResourcesDynamic();
  if($absolute=="1") $ppth=$objUtility->getPathResourcesDynamicAbsolute();
  
  if($width==0 && $alte==0) return false;

  $strDestDir = $objUtility->getPathResourcesDynamicAbsolute();
  $strUnique = "thm_".$width."x".$alte."_".basename($file);
  if(file_exists($strDestDir.$strUnique)) return $ppth.$strUnique;

  $strDestFile = $strUnique;
  $arr=explode(".", $strDestFile);
  $arr=array_reverse($arr);
  $strExt = $arr[0];
  unset($arr[0]);
  $arr=array_reverse($arr);
  $strDestFile=implode(".", $arr);
  $dpath=$strDestDir.$strDestFile;

  $arr=explode(".", $file);
  $arr=array_reverse($arr);
  $strExt = $arr[0];
  
  list($w, $h) = getimagesize($file);
  
  if($width==0 || $alte==0) {
    // Constraints 
    $max_width = $width; 
    $max_height = $alte; 
     
    $ratioh = $max_height/$h; 
    $ratiow = $max_width/$w; 
    $ratio = max($ratioh, $ratiow); 
    // New dimensions 
    $width = intval($ratio*$w); 
    $alte = intval($ratio*$h); 
  }
   
  if($w<$width && $h<$alte) return false;
  
  $cosa = getimagesize($file);

  $min  = imagecreatetruecolor($width, $alte);
  switch($cosa['mime']) {
    case 'image/png':
      $im = imagecreatefrompng($file);
      imagealphablending($min, false);
      imagesavealpha($min, true);
      imagecopyresampled($min, $im, 0, 0, 0, 0, $width, $alte, imageSx($im), imageSy($im));
      imagepng($min,$dpath.".".$strExt);
      break;

    case 'image/gif':
      $im = imagecreatefromgif($file);
      imagealphablending($min, false);
      $colorTransparent = imagecolorallocatealpha($min, 0, 0, 0, 127);
      imagefill($min, 0, 0, $colorTransparent);
      imagecopyresampled($min, $im, 0, 0, 0, 0, $width, $alte, imageSx($im), imageSy($im));
      imagesavealpha($min, true);
      imagegif($min,$dpath.".".$strExt);
      break;

    case 'image/jpeg':
    case 'image/jpg':
      $im = imagecreatefromjpeg($file);
      imagecopyresampled($min, $im, 0, 0, 0, 0, $width, $alte, imageSx($im), imageSy($im));
      imagejpeg($min,$dpath.".".$strExt,100);
      break;
  }
  imagedestroy($min);

  if($addObject!="") {
    $strOggettoPath = $strDestDir;
    $strOggettoExt = $strExt;
    $strOggettoOriginalname = $file;

    $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,ext,path,originalname) VALUES ('$strDestFile','$strOggettoExt','$strOggettoPath','$strOggettoOriginalname') ";
    $query=mysql_query($sql);
    $id_oggetto=mysql_insert_id();
    return $id_oggetto;
  }
   
  return $ppth.$strUnique;
}

function imgResizeByID($IdObject,$width=0,$alte=0,$addObject="",$absolute="") {
  if($IdObject=="") return;
  
  $file=retFileAbsolute($IdObject);
  if(!$file) return false;
  
  $retFile=imgResize($file,$width,$alte,$addObject,$absolute);
  if(!$retFile) {
    if($absolute!="1") {
      $retFile=retFile($IdObject);
    }else{
      $retFile=retFileAbsolute($IdObject);  
    }
  }                     
  
  return $retFile;  
}

function printGallery($rs,$didascalia="",$rows="",$cols="",$jquery="",$shadow="",$res=160) {
  $objUtility = new Utility;
  $i=0;
  $n=count($rs);

  if($n==0) return;
  if($rows>0) $cols=ceil($n/$rows);
  if($rows==0 && $cols>0) $rows=ceil($n/$cols);

  $thumb="immagine_file";
  $zoom="immagine_file";

  if($jquery=="") { ?>
    <div id="fotogallery">
      <table cellpadding="0" cellspacing="0">
        <?php for($j=0;$j<$rows;$j++) { ?>
          <tr>
          <?php for($k=0;$k<$cols;$k++) { ?>
            <td>
              <?
              $file=retFileAbsolute($rs[$i][$thumb],$res);
              if(file_exists($file) && is_file($file)) {
                $size = getimagesize($file);
                $f_thumb=retFile($rs[$i][$thumb],$res);
                $f_zoom=retFile($rs[$i][$zoom],1024);
                ?>
                <a href="<?=$f_zoom?>" rel="lytebox[gallery]" title="<?=ln($rs[$i][$didascalia])?>">
                  <img class="fotogallery-foto" alt="<?=ln($rs[$i][$didascalia])?>" src="<?=$f_thumb?>">
                  <div class="fotogallery-overlayer"></div>
                </a>
                <?php if($shadow==1) { ?><img class="fotogallery-shadow" src="admin/resources/liftedshadow_medium.png"><? } ?>
              <? } ?>
            </td>
          <? $i++;} ?>
          </tr>
        <? } ?>
      </table>
    </div>
    <?
  } elseif($jquery=="1") {  
    $st=getTable("fotogallery_stile","","attivo='1'");
    $st=$st[0]; 
    
    if($st['barPosition']=="") {$st['barPosition']="null";} else {$st['barPosition']="'".$st['barPosition']."'";}
    if($st['easing']=="") {$st['easing']="'linear'";} else {$st['easing']="'".$st['easing']."'";}
    if($st['time_interval']=="") $st['time_interval']="4500";
    if($st['ratio']=="") $st['ratio']="0.35";
    if($st['showOverlay']=="") $st['showOverlay']="true";
    if($st['slideshow']=="") $st['slideshow']="true";
    if($st['thumbHeight']=="") $st['thumbHeight']="55";
    if($st['thumbWidth']=="") $st['thumbWidth']="55";
    if($st['toggleBar']=="") $st['toggleBar']="true";  
    $uni=$objUtility->getFilenameUnique();
    ?>
    <div id="<?=$uni?>" class="gallery">  
      <ul class="galleryBar">    
        <?php 
        for($i=0;$i<$n;$i++) { 
          $file=retFileAbsolute($rs[$i][$thumb],$res);
          $file2=retFileAbsolute($rs[$i][$zoom],1024);
          if(file_exists($file2) && is_file($file2)) $size2 = getimagesize($file2); 
          if(file_exists($file) && is_file($file)) {
            $size = getimagesize($file);
            $f_thumb=retFile($rs[$i][$thumb],$res);
            $f_zoom=retFile($rs[$i][$zoom],1024);
            if($st['sfondo_file']!="0") $f_zoom=retFile($st['sfondo_file']);
            ?>
            <li>
              <a href="<?=$f_zoom?>" <?php if($rs[$i]['descrizione_editor']!="") { ?> rel="fg_description<?=$i?>" <? } ?> title="<?=$rs[$i]['titolo']?>">
                <img width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>" src="<?=$f_thumb?>" title="<?=$rs[$i][$didascalia]?>" <?php if($st['useLytebox']=="1") { ?>onclick='window.setTimeout(function(){$("#<?=$uni.$i?>").click();},1)'<? } ?> />
              </a>
            </li>
            
            <?php if($rs[$i]['descrizione_editor']!="") { ?>
              <div id="fg_description<?=$i?>" style="display:none;">  
                <p><?=$rs[$i]['descrizione_editor']?></p>
              </div>
            <? } ?>
          <? } ?>  
        <? } ?>      
      </ul>
      
      <?php
      for($i=0;$i<$n;$i++) { 
        $file=retFileAbsolute($rs[$i][$thumb],$res);
        if(file_exists($file) && is_file($file)) {
          $f_zoom=retFile($rs[$i][$zoom],1024);
          if($st['useLytebox']=="1") { ?><a href="<?=$f_zoom?>" id="<?=$uni.$i?>" rel="lytebox[<?=$uni?>]" style="display:none;">Openlytebox()</a><? } ?>
        <? } ?>  
      <? } ?>
      
    </div>
    <?php 
    if($st['height']=="") $st['height']=$size2[1];
    if($st['width']=="") $st['width']=$size2[0];
    ?>
    <script>
      $(document).ready(function(){
        $('#<?=$uni?>').gallery({thumbHeight:<?=$st['thumbHeight']?>,thumbWidth:<?=$st['thumbWidth']?>,barPosition:<?=$st['barPosition']?>,easing:<?=$st['easing']?>,height:<?=$st['height']?>,interval:<?=$st['time_interval']?>,ratio:<?=$st['ratio']?>,showOverlay:<?=$st['showOverlay']?>,slideshow:<?=$st['slideshow']?>,toggleBar:<?=$st['toggleBar']?>,width:<?=$st['width']?>});
      });
    </script>
    <? 
  } 
}

function retCatLayout($id){
  $cat=retRow("categorie",$id);
  $struttura=getStrutturaByNodo($id);
  $struttura=$struttura['nome'];
  if($struttura=="magazzino") {
    $maglay=getTable("ecommerce_conf_layout","","attivo='1'");
  }
  
  $lay=retRow("gestione_layout",$cat['id_gestione_layout']);
  
  if($lay['file']=="") {
    if($struttura=="magazzino") {
      $tmpff=Table2ByTable1("categorie","categorie",$id,"","");
      if(count($tmpff)>0) {
        $lay=retRow("gestione_layout",$maglay[0]['directory']);  
      }else{
        $lay=retRow("gestione_layout",$maglay[0]['nodo']);  
      }  
    }      
  }
  
  return $lay;  
}

function printGalleryFromStruttura($struttura,$cat,$rows="",$cols="",$didascalie="",$width=0,$height=0,$lyte=0,$rs="") {
  $objUtility=new Utility;
  
  if($rs==""){
    if($cat>0){
      $rs=Table2ByTable1("categorie","categorie",$cat,"attivo='1'","Ordinamento ASC");  
    }else{
      $rs=getTable("strutture","","nome='$struttura' AND attivo='1'");
      $rs=Table2ByTable1("strutture","categorie",$rs[0]['id'],"attivo='1'","Ordinamento ASC");
    } 
  }
  
  $maxZoom=800;
  
  $thumb="immagine_file";
  $didascalia="nome";

  $i=0;
  $n=count($rs);

  if($rows>0) $cols=ceil($n/$rows);
  if($rows==0 && $cols>0) $rows=ceil($n/$cols);
  if($width==0 || $width=="") $width=300;
  if($height==0 || $height=="") $height=0;
  ?>
  <div id="fotogallery" class="fromstruttura">
    <table>
      <?php for($j=0;$j<$rows;$j++) { ?>
      <tr>
        <?php for($k=0;$k<$cols;$k++) { 
          $gallery=Table2ByTable1("categorie","fotogallery",$rs[$i]['id'],"attivo='1'","Ordinamento ASC LIMIT 1");
          $magazzino=Table2ByTable1("categorie","magazzino_articoli",$rs[$i]['id'],"del_hidden='0'","");
          $sconto=$magazzino[0]['sconto'];
          $codArt=$magazzino[0]['Codice'];
          $dipArt=$magazzino[0]['quantita'];
          if($_SESSION["userris_id"]>0) $sconto=$magazzino[0]['sconto_reg'];
          $imm_articolo=retFile($rs[$i][$thumb],$width,$height); 
          $imm_articoloZoom=retFile($rs[$i][$thumb],$maxZoom);
          $imm_articoloAbs=retFileAbsolute($rs[$i][$thumb],$width,$height);
          $size = @getimagesize($imm_articoloAbs);
          
          if(!$imm_articolo) {
            $imm_articolo=retFile($gallery[0]['immagine_file'],$width,$height);
            $imm_articoloZoom=retFile($gallery[0]['immagine_file'],$maxZoom);
            $imm_articoloAbs=retFileAbsolute($gallery[0]['immagine_file'],$width,$height);
            $size = @getimagesize($imm_articoloAbs);
          }
          
          if(!$imm_articolo) {
            $tstr=getStruttura($struttura,$rs[$i]['id'],-1);
            //shuffle($tstr); 
            if(count($tstr)>0) {
              $timm=Table2ByTable1("categorie","fotogallery",$tstr[0],"attivo='1'","Ordinamento ASC LIMIT 1");
              $imm_articolo=retFile($timm[0]['immagine_file'],$width,$height);
              $imm_articoloZoom=retFile($timm[0]['immagine_file'],$maxZoom);
            } 
          }
          
          if(!$imm_articolo) $imm_articolo=$objUtility->getPathBackofficeResources()."nofoto.jpg"; 
          if($rs[$i]['id']!="") { ?>
            <td class="foto <?php if(getCategoria()==$rs[$i]['id']) echo "selected"; ?> <?php echo "td".$rs[$i]['id']; ?>" ><?
              if($struttura=="magazzino") {
                $maglay=getTable("ecommerce_conf_layout","","attivo='1'");
              }
              
              $lay=retRow("gestione_layout",$rs[$i]['id_gestione_layout']);
              
              if($lay['file']=="") {
                if($struttura=="magazzino") {
                  $tmpff=Table2ByTable1("categorie","categorie",$rs[$i]['id'],"","");
                  if(count($tmpff)>0) {
                    $lay=retRow("gestione_layout",$maglay[0]['directory']);  
                  }else{
                    $lay=retRow("gestione_layout",$maglay[0]['nodo']);  
                  }  
                }      
              }
              
              if($rs[$i]['url']=="") {
                $f_zoom=trim($lay['file'])."?menid=".trim($rs[$i]['id']);
              }else{
                $f_zoom=trim($rs[$i]['url']);
              }
              
              $figli=getStruttura($struttura,$rs[$i]['id']);
              
              if($lyte==0) { ?>
                <a href="<?php echo $f_zoom; ?>" title="<?=ln($rs[$i][$didascalia])?>" <?php if(count($figli)==0) echo "class='last-child'"; ?>>
                  <img width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>" alt="<?=ln($rs[$i][$didascalia])?>" src="<?=$imm_articolo?>">
                  <div class="didascalia-container">
                    <?php if($codArt!="" && count($figli)==0) { ?><div class="dispart"><?php echo ln("Disponibilità"); ?>: <? echo intval($dipArt); ?></div><? } ?>
                    <?php if($codArt!="" && count($figli)==0) { ?><div class="codart"><? echo $codArt; ?></div><? } ?>
                    <?php if($didascalie=="") { ?><div class="didascalia"><? echo ln($rs[$i][$didascalia])?></div><? } ?>
                    <?php if($magazzino[0]['Prezzo_cry']>0 && $magazzino[0]['richiedi_quotazione']!="1") { ?><div class="<?php if($sconto>0) echo "fromstruttura-prezzo";else echo "fromstruttura-sconto";?>" style="<?php if($sconto>0) echo "text-decoration:line-through"; ?>">€ <?php echo currencyITA($magazzino[0]['Prezzo_cry']); ?></div><? } ?>
                    <?php if($sconto>0 && $magazzino[0]['Prezzo_cry']>0) { ?><div class="fromstruttura-sconto">€ <?php echo currencyITA(parseToFloat($magazzino[0]['Prezzo_cry'])-(parseToFloat($magazzino[0]['Prezzo_cry'])*parseToFloat($sconto))/100); ?></div><? } ?>
                  </div>
                </a>
              <? } else { ?>
                <a href="<?php echo $imm_articoloZoom; ?>" rel="shadowbox" title="<?=ln($rs[$i][$didascalia])?>" <?php if(count($figli)==0) echo "class='last-child'"; ?>>
                  <img width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>" alt="<?=ln($rs[$i][$didascalia])?>" src="<?=$imm_articolo?>">
                  <div class="didascalia-container">
                    <?php if($didascalie=="") { ?><div class="didascalia"><?=ln($rs[$i][$didascalia])?></div><? } ?>
                    <?php if($magazzino[0]['Prezzo_cry']>0) { ?><div class="fromstruttura-prezzo" style="<?php if($sconto>0) echo "text-decoration:line-through"; ?>">€ <?php echo currencyITA($magazzino[0]['Prezzo_cry']); ?></div><? } ?>
                    <?php if($sconto>0) { ?><div class="fromstruttura-sconto">€ <?php echo currencyITA(parseToFloat($magazzino[0]['Prezzo_cry'])-(parseToFloat($magazzino[0]['Prezzo_cry'])*parseToFloat($sconto))/100); ?></div><? } ?>
                  </div>
                </a>
              <? } ?>
            </td>
            <?php 
            $i++;
          }
        } ?>
      </tr>
      <? } ?>
    </table>
  </div>
  <?
}

function retArray($rs,$field) {
  $arrRs=array();
  while (list($key, $row) = each($rs)) {
    array_push($arrRs, $row[$field]);
  }
  return $arrRs;
}

function printSubTablesNav($table,$intId,$extra="") {
  if($intId=="") return;
   
  $objConfig = new ConfigTool();
  $dbname = $objConfig->get("db-dbname");
  global $config_table_prefix;

  $sql="SHOW TABLE STATUS FROM $dbname";
  $query = mysql_query($sql);

  $g_config_table_prefix=strtolower($config_table_prefix);
  $g_table=$table;
  $tmp_g_table=$g_table;
  if($table=="categorie") $sstr=getStrutturaByNodo($intId);

  $j=0;
  while($res=mysql_fetch_array($query)) {
    $sqlWhere="";
    if(!(strpos($res[0],$g_config_table_prefix.$tmp_g_table."#")===FALSE) && strpos($res[0],"_nm")!=FALSE) {
      $p_table=$res[0];

      $res[0]=str_replace($g_config_table_prefix, "", $res[0]);
      $res[0]=str_replace("_nm", "", $res[0]);
      $tmp_arr=explode("#", $res[0]);
      $res[0]=$tmp_arr[1];

      if($res[0]!="") {
        $g_table=$res[0];

        $result = mysql_query("SELECT * FROM `$p_table` ");
        $field1=mysql_field_name($result,1);
        $field2=mysql_field_name($result,2);

        $query1 = mysql_query("SELECT * FROM `$p_table` WHERE $field1='$intId' ");

        while($res1=mysql_fetch_array($query1)) {
          $sqlWhere.="id='".$res1[$field2]."' OR ";
        }

        $sqlWhere=substr($sqlWhere,0,strlen($sqlWhere)-3);

        if($sqlWhere!="") $sqlWhere="($sqlWhere)";
        if($sqlWhere=="") $sqlWhere="id='0'";

        if($g_table=="contenuti") {
          $trs=getTable("contenuti","",$sqlWhere." AND attivo='1'");
          $tid=$trs[0]['id'];
          if($tid=="") {
            $tsql="INSERT INTO `".$config_table_prefix."contenuti` (nome,attivo) VALUES ('default','1') ";
            $p_res=mysql_query($tsql);
            $tid=mysql_insert_id();

            $tsql="INSERT INTO `".$config_table_prefix."categorie#contenuti_nm` (id_categorie,id_contenuti) VALUES ('$intId','$tid') ";
            $p_res=mysql_query($tsql);
          }
          printSubTablesNav("contenuti",$tid,$extra);
        }

        $filter="";
        if($g_table=="categorie" && $extra=="") $filter="id,nome,immagine_file,id_gestione_layout,Ordinamento,attivo";
        
        if($extra!="" || ($g_table!="contenuti" && $g_table!="4_mappa_google" && $g_table!="2_titoli_h1_h2_h3" && $g_table!="categorie" && $g_table!="5_forms" && $g_table!="oggetti" && $g_table!="1_intestazioni")) {
          if($extra=="" || ($extra!=""  && $g_table!="categorie"  && $g_table!="oggetti" && $g_table!="contenuti" )) {
            $tblDefault[$j] = new rsTable($g_table);
            $subPrint="";
            if($g_table=="categorie") $subPrint="-1";
            
            $skip=false;
            if($table=="categorie") {
              $tmprs_roles=getTable("categorie_roles","","(attivo='1' AND id_strutture='".$sstr['id']."' AND tabella='$g_table')");
              if($tmprs_roles[0]['nascondi']=="1") $skip=true;
            }
            
            if(!$skip) $tblDefault[$j]->_print($sqlWhere,$p_table,$intId,$subPrint,$filter);
            
            $j++;
          }
        }
      }
    }
  }
}

function getSubTablesNav($table) {
  $objConfig = new ConfigTool();
  $dbname = $objConfig->get("db-dbname");
  global $config_table_prefix;

  $sql="SHOW TABLE STATUS FROM $dbname";
  $query = mysql_query($sql);

  $g_config_table_prefix=strtolower($config_table_prefix);
  $g_table=$table;
  $tmp_g_table=$g_table;
  $ret=array();

  $j=0;
  while($res=mysql_fetch_array($query)) {
    $sqlWhere="";
    if(!(strpos($res[0],$g_config_table_prefix.$tmp_g_table."#")===FALSE) && strpos($res[0],"_nm")!=FALSE) {
      $p_table=$res[0];

      $res[0]=str_replace($g_config_table_prefix, "", $res[0]);
      $res[0]=str_replace("_nm", "", $res[0]);
      $tmp_arr=explode("#", $res[0]);
      $res[0]=$tmp_arr[1];

      if($res[0]!="") {
        $g_table=$res[0];
        array_push($ret, $g_table);
      }
    }
  }
  
  return $ret;
}

function retLabelInsert($table, $campo) {
  global $config_table_prefix;
   
  $idTab=getTable("nomi_tabelle","","tabella='$table'");
  
  $rs=getTable("etichette",$ordinamento="",$where="id_nomi_tabelle='".$idTab[0]['id']."' AND campo='$campo'");
  
  if(count($rs)>0) {
    return $rs[0]['label'];
  } else {
    $tmpField=$campo;
    
    if(strpos($tmpField, "_thm")) {
      $substr=substr($tmpField, strpos($tmpField, "_thm"), strlen($tmpField)-strpos($tmpField, "_thm"));
      $tmpField=str_replace($substr,"",$tmpField);
    }

    if(strpos($tmpField, "_str_")) {
      $struttura=substr($tmpField, strpos($tmpField, "_str_"), strlen($tmpField)-strpos($tmpField, "_str_"));
      $tmpField=str_replace($struttura,"",$tmpField);
      $struttura=str_replace("_str_","",$struttura);
    }
    
    $tmpField=str_replace("_lst","",$tmpField);
    $tmpField=str_replace("_cry","",$tmpField);
    $tmpField=str_replace("_editor","",$tmpField);
    $tmpField=str_replace("_file","",$tmpField);
    $tmpField=str_replace("id_","",$tmpField);
    $tmpField=str_replace("_"," ",$tmpField);
    
    if($campo!="id") {
      $sql="INSERT INTO `".$config_table_prefix."etichette` (id_nomi_tabelle,campo,label) VALUES ('".$idTab[0]['id']."','$campo','$tmpField') ";
      $res=mysql_query($sql);
    }
    return $tmpField;
  }
}

function retTableLabelInsert($table) {
  global $config_table_prefix;
  
  $rs=getTable("nomi_tabelle",$ordinamento="",$where="tabella='$table'");
  
  if(count($rs)>0) {
    return $rs[0]['label_lst'];
  } else {
    $table2=str_replace($config_table_prefix, "", $table);
    $table2=str_replace("_"," ",strtoupper($table2));
    $sql="INSERT INTO `".$config_table_prefix."nomi_tabelle` (tabella,label_lst) VALUES ('$table','$table2') ";
    $res=mysql_query($sql);
    return $table2;
  }
}

function jstr($string) {
  return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
}

function lyteFrame($page,$text="",$title="",$class="",$style="",$w="700",$h="460") { ?>
  <a href='<?=$page?>' rel="lyteframe" title="<?=$title?>"  rev="width: <?php echo $w;?>px; height: <?php echo $h;?>px; scrolling: auto; frameborder: 0;" class="<?=$class?>" ><span style="<?=$style?>" ><?=$text?></span></a>
  <?
}

function lyteFrameVideo($id,$text="",$title="",$w="",$h="",$class="",$style="") {
  $objUtility = new Utility;
  if($w=="") $w="700";
  if($h=="") $h="460";
  ?>
  <a href='rsVideo.php?id=<?=urlencode($id)?>&w=<?=$w?>&h=<?=$h?>' rel="lyteframe" title="<?=$title?>" rev="width: <?=$w+10?>px; height: <?=$h+10?>px; scrolling: no;" class="<?=$class?>" ><span style="<?=$style?>" ><?=$text?></span></a>
  <?
}

function retTableFields($table) {
  global $config_table_prefix;
  
  $sql.="SELECT * FROM ".$config_table_prefix.$table;
  $result = mysql_query($sql);
  $fields_num = mysql_num_fields($result);
  
  $farr=array();
  for($i=0; $i<$fields_num; $i++) {
    array_push($farr, mysql_field_name($result,$i));
  }
  
  return $farr;
}

function printRecord($table,$id) {
  include ("_docroot.php");

  //include (SERVER_DOCROOT . "logic/class_config.php");
  $objConfig = new ConfigTool();
  $objDb = new Db;
  $objHtml = new Html;
  $objJs = new Js;
  $objMenu = new Menu;
  $objObjects = new Objects;
  $objUsers = new Users;
  $objUtility = new Utility;
  $conn = $objDb->connection($objConfig);
  $dbname = $objConfig->get("db-dbname");
  
  session_start();
                                   
  $objUsers->getCurrentUser($intIdutente, $strUsername);
  
  global $config_table_prefix;
  
  $cols="*";
  if($table=="users") $cols="login,nome,cognome,ragionesociale,codicefiscale,partitaiva,telefono,cellulare,fax,email,ultimoaccesso"; 
  
  $sql="SELECT $cols FROM ".$config_table_prefix.$table." WHERE id='$id' ";
  $result = mysql_query($sql);
  
  include('rsTable_print_record.php');
}

function checkRights() {
  $objConfig = new ConfigTool();
  $objDb = new Db;
  $objMenu = new Menu;
  $objObjects = new Objects;
  $objUsers = new Users;
  $objUtility = new Utility;
  $conn = $objDb->connection($objConfig);
  $dbname = $objConfig->get("db-dbname");
  
  session_start();
  $objUsers->getCurrentUser($intIdutente, $strUsername, false, 1);
  if (!$objMenu->checkRights($conn, $intIdutente,$objUtility->getPathBackoffice()."navigazione/in_place.php",false,1)) {
    return false;
	}
	 return true;
}

function startInPlace($table="",$field="",$id="") {
  include ("_docroot.php");
  
  //include (SERVER_DOCROOT . "logic/class_config.php");
  $objConfig = new ConfigTool();
  $objDb = new Db;
  $objHtml = new Html;
  $objJs = new Js;
  $objMenu = new Menu;
  $objObjects = new Objects;
  $objUsers = new Users;
  $objUtility = new Utility;
  $conn = $objDb->connection($objConfig);
  $dbname = $objConfig->get("db-dbname");
  
  session_start();
  $objUsers->getCurrentUser($intIdutente, $strUsername, false, 1);
  if (!$objMenu->checkRights($conn, $intIdutente,$objUtility->getPathBackoffice()."navigazione/in_place.php",false,1)) {
     echo $obj;
     return;
	}
	
	if($obj=="" && $table=="" && $field=="" && $id=="") {
    echo "</div>";
    return;
  }
	
  global $cc;
  $cc=$cc+1;
  
  if($field!="") {
    $field="id,".$field;
  }
  
  ?>
  <div id="<?="edit_".$cc?>" class="editBar" style="height:1px;"><a href ="<?=$objUtility->getPathBackofficeAdmin()?>navigazione/in_place_pag.php?table=<?=$table?>&field=<?=$field?>&id=<?=$id?>" target="showframe"><img src="<?php echo $objUtility->getPathBackofficeResources() ?>pen-edit.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>pen-edit.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>pen-edit.png";' alt="visualizza/modifica" title="visualizza/modifica" class="btnupd" /></a></div>
  <div id="editInPlace" onmouseover="mostraNoScroll('<?="edit_".$cc?>');" >
  <?
}

function endInPlace() {
  startInPlace(); 
}

function tinyBug($string) {
  $objUtility = new Utility;
  
  $string=str_replace("../../resourcesdyn", $objUtility->getPathResourcesDynamic(), $string);
  $string=str_replace("../resourcesdyn", $objUtility->getPathResourcesDynamic(), $string);
  
  $string=str_replace("../../UserFiles", $objUtility->getPathUserFiles(), $string);
  $string=str_replace("../UserFiles", $objUtility->getPathUserFiles(), $string);
  
  $string = preg_replace('/src="(.*)UserFiles\//', 'src="'.$objUtility->getPathUserFiles(), $string);
  
  return $string;
}

function tinyBugAbsolute($string) {
  global $objConfig;
  
  $string=str_replace("../../resourcesdyn", "http://".$_SERVER['SERVER_NAME']."/".$objConfig->get("path-resources-upload"), $string);
  $string=str_replace("../resourcesdyn", "http://".$_SERVER['SERVER_NAME']."/".$objConfig->get("path-resources-upload"), $string);
  
  $string=str_replace("../../UserFiles", "http://".$_SERVER['SERVER_NAME']."/".$objConfig->get("path-userfiles"), $string);
  $string=str_replace("../UserFiles", "http://".$_SERVER['SERVER_NAME']."/".$objConfig->get("path-userfiles"), $string);
  
  return $string;
}

function valLogin() {
  if($_POST["UserReg"]) return;
  if($_POST["pwdSend"]) return;
  if($_POST["pwdSendDo"]) return;
  if($_POST["rsPagin"]) return;
  
  $objConfig = new ConfigTool();
  $objDb = new Db;
  $objHtml = new Html;
  $objJs = new Js;
  $objMenu = new Menu;
  $objObjects = new Objects;
  $objUsers = new Users;
  $objUtility = new Utility;
  $objCarrello=new Carrello();
  $conn = $objDb->connection($objConfig);

  $utente = $objUtility->translateForSafe($_POST["utente"], 100);
  $pwd = $objUtility->translateForSafe($_POST["pwd"], 100);
  
  $exit = $_GET['logout'];
  $UseMD5validation=true;
  
  if($_GET['user'] && $_GET['pass']) {
    $tmp_user=getTable("users","","(id='".$_GET['user']."' AND pwd='".$_GET['pass']."')");  
    if(count($tmp_user)>0) {
      $utente=$tmp_user[0]['login'];
      $pwd=$tmp_user[0]['pwd'];
      $UseMD5validation = false;  
    }
  }
  
  if($exit && !$utente && !$pwd) {
    if(!isset($_SESSION["userris_id"])) return;
    unset($_SESSION["userris_id"]);
    unset($_SESSION["userris_login"]);
    unset($_SESSION["userris_lastaccess"]);
    unset($_SESSION["userris_lastpwdupdate"]);
    unset($_SESSION["userris_isreadonly"]);
    unset($_SESSION['docRaggruppa']);
    $_SESSION['ecomm'] = array();
    //session_destroy();
    
    box(ln("Sei uscito correttamente"));
    return;
  }
                  
  if(isset($_SESSION["userris_id"])) return;
  
  $idutente = 0;
  $username = "";
  $idroleAreariservata = $objConfig->get("role-areariservata");
  if($UseMD5validation) {
    $isAuthorized = $objUsers->checkLoginAreariservata($conn, $utente, $pwd, $idroleAreariservata, $idutente, $username, $dateLastAccess, $dateLastPwdupdate, $isActivated);
  }else{
    $isAuthorized = $objUsers->checkLoginAreariservataA($conn, $utente, $pwd, $idroleAreariservata, $idutente, $username, $dateLastAccess, $dateLastPwdupdate, $isActivated);  
  } 
  
  if ($isAuthorized) 
  { 
    $_SESSION["userris_id"] = $idutente;
  	$_SESSION["userris_login"] = $username;
  	$_SESSION["userris_lastaccess"] = $dateLastAccess;
  	$_SESSION["userris_lastpwdupdate"] = $dateLastPwdupdate;
  	$_SESSION["userris_isactivated"] = $isActivated;
  	
    $objSession=new Session();
  	$tmpsess=$objSession->retByUserID($idutente);
  	
  	unset($_SESSION["user_id"]);
		unset($_SESSION["user_login"]); 
  	
    if(is_array($tmpsess['ecomm'])) {
      if(!is_array($_SESSION['ecomm'])) $_SESSION['ecomm']=array();
      
      $tmp_ecomm=$_SESSION['ecomm'];
      $_SESSION=$tmpsess;
      $_SESSION['ecomm']=$tmp_ecomm;
      
      while (list($key, $prodotto) = each($tmpsess['ecomm'])) { 
        if(!is_array($_SESSION['ecomm'][$key])) {
          $_SESSION['ecomm'][$key]=$prodotto;  
        }else{
          while (list($key2, $variante) = each($prodotto)) { 
            if($key2!=="ecomm_buffer") {
              if(is_array($_SESSION['ecomm'][$key][$key2])){
                array_push($_SESSION['ecomm'][$key],$variante);
              }else{
                $_SESSION['ecomm'][$key][$key2]=$variante;  
              }
            } 
          }
        }
             
      }
  	}
  	
    if ($isActivated) {
  		//header("Location: arearis_documenti.php");
  		//header("Location: login.php");
      return "1";
  	} else {
  		//header("Location: arearis_activation.php");
  		//header("Location: login.php");
      box(ln("Benvenuto/a")." ".$username.",<br> ".ln("accesso effettuato con successo"));
      return "2";
  	}
  } else {
  	//header("Location: login_error.php");
    box(ln("Nome utente o password non validi"));
    return "0";
  }
}

function cambiaPassword($conn) {
  $objUsers = new Users;
  
  $idUsers = $_SESSION["userris_id"];
  
  $password = $_POST["password"];
	$password_conf = $_POST["password_conf"];

	$isError = false;
	If (!$idUsers) $isError = true;
	If ($password == "") $isError = true;
	If ($password != $password_conf) $isError = true;

	If ($isError) {
		//header ("Location: pwd_upd.php");
	} else {
		$strError = "";
		$objUsers->passwordUpdate($conn, $idUsers, $password);
		box("password cambiata correttamente");
  }
}

function printRegistrationForm() {
  if($_POST["UserReg"]) {
    $rsForm = new rsForm("UserReg","-1");
    $rsForm->_print();
    return true;
  }
  return false;
}

function retBriciole($addTesta="",$addCoda="",$cat="") { 
  if($cat!="") { 
    $padre=retRow("categorie",$cat);
  }else{
    $padre=retRow("categorie",getCategoria());
  }
  
  $briciole=array();
  while($padre!=false) {
    //$lay=retRow('gestione_layout',$padre['id_gestione_layout']);
    $lay=$padre["url"];
    array_push($briciole, "<a href='".$lay."'>".ln($padre['nome'])."</a>");
    $padre=getPadre($padre['id']);
  }
  $briciole=array_reverse($briciole);
  $cc=count($briciole)-1;
  $briciole[$cc]=strip_tags($briciole[$cc]);
                  
  if(is_array($addTesta)) {
    for($j=0;$j<count($addTesta);$j++) {
      $padre=retRow("categorie",$addTesta[$j]);
      $lay=$padre["url"];
      array_unshift($briciole, "<a href='".$lay."'>".ln($padre['nome'])."</a>");    
    }  
  }
  
  if(is_array($addCoda)) {
    for($j=0;$j<count($addCoda);$j++) {
      $padre=retRow("categorie",$addCoda[$j]);
      $lay=$padre["url"];
      array_push($briciole, "<a href='".$lay."'>".ln($padre['nome'])."</a>");    
    }  
  } 
  
  return $briciole; 
}

function stampaRichiediQuotazione() {
  global $config_table_prefix;

  $menid=$_GET['menid'];
  $personalizzazioni="";
  while (list($key3, $caratteristica) = each($_SESSION['ecomm'][$menid]['ecomm_buffer'])) {
    if(strpos($key3, "ecomm_")===FALSE) {
      $addbr=1;
      if(!is_array($caratteristica)) {
        if(strpos($key3, "id#")!==FALSE) {
          $tmp_id=explode("#", $key3);
          $tmp_id=$tmp_id[1];
          $tmp_nome1=retRow("ecommerce_caratteristiche",$tmp_id);
          $tmp_nome=retRow("ewiz_caratteristiche_list",$tmp_nome1['id_ewiz_caratteristiche_list']);
          $tmp_nome['id']=$tmp_nome1['id'];
          $tmp_tipo=$tmp_nome['id_ecommerce_tipologie']; 
          
          $key3=$tmp_nome['nome'];
          //echo $key3,$caratteristica,$tmp_tipo;
          if($tmp_tipo=="3" || $tmp_tipo=="6" || $tmp_tipo=="7") {
            $caratteristica=retRow("ecommerce_valori",$caratteristica);
            $caratteristica=$caratteristica['nome'];
          } elseif($tmp_tipo=="4") {
            $tmpcar=explode(";", $caratteristica);
            $tmparr=array();
            while(list($key6, $value2) = each($tmpcar)) {
              $tmpval=retRow("ecommerce_valori",$value2);
              array_push($tmparr, $tmpval['nome']);
            }
            $caratteristica=implode(", ",$tmparr);  
          }elseif($tmp_tipo=="2"){
            if($caratteristica=="true") $caratteristica=ln("sì",$zy);
            if($caratteristica=="false") $caratteristica=ln("no",$zy);  
          }
        }
        
        $tmpkey3=ln(str_replace("ecomm_","",$key3),$zy);
        $tmpkey3=str_replace("_","&nbsp;",$key3);
        if($caratteristica!="") $personalizzazioni.= '<div style="text-transform: lowercase;">'.$tmpkey3.':&nbsp;'.ln($caratteristica,$zy).'</div>';
      } else {
        while (list($key4, $value) = each($caratteristica)) {
          $cnome=retRow("ecommerce_valori",$value);
          $caratteristica[$key4]=ln($cnome['nome'],$zy);
        }
        $tmpkey3=ln(str_replace("ecomm_","",$key3),$zy);
        $tmpkey3=str_replace("_","&nbsp;",$key3);
        if(implode(", ",$caratteristica)!="") $personalizzazioni.= '<div style="text-transform: lowercase;">'.$tmpkey3.':&nbsp;'.implode(", ",$caratteristica).'</div>';
      } 
    }
  }                                            
                                              
  $personalizzazioni=htmlentitiesEx($personalizzazioni);
  
  ?>
  <script>
  var tblOptions={
    <?php 
    $tblOptions["container"]="div.ecommerce-quotazione";
    $tblOptions["table"]="ecommerce_quotazione";
    $tblOptions["insert"]=1;
    $tblOptions["insertId"]='';
    $tblOptions["colFilter"]='nome,cognome,email,telefono,richiesta,accetto,data';
    $tblOptions["permDel"]=-1;
          
    echo cryptOptions($tblOptions); 
    ?>
  };
  
  g_table=new rsTable2(tblOptions);
  g_table._insert(function(){
    <?php 
    $off_id_users=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_quotazione' AND campo_hidden='id_users')"));
    $off_id_cat=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_quotazione' AND campo_hidden='id_categorie_str_magazzino')")); 
    $off_id_imm=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_quotazione' AND campo_hidden='immagine_file')")); 
    $off_codice=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_quotazione' AND campo_hidden='codice_articolo')"));
    $off_pers=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_quotazione' AND campo_hidden='personalizzazioni')"));
    
    $art=retArticoloFromCat($_GET['menid']);
    $gallery=Table2ByTable1("categorie","fotogallery",$_GET['menid'],"attivo='1'","Ordinamento ASC");
    ?>
    $("form.rsTable2-insert-form-ecommerce_quotazione").append("<input type='hidden' value='<?php echo $_SESSION['userris_id']; ?>' name='<?php echo $off_id_users[0]['id']; ?>' />"); 
    $("form.rsTable2-insert-form-ecommerce_quotazione").append("<input type='hidden' value='<?php echo $_GET['menid']; ?>' name='<?php echo $off_id_cat[0]['id']; ?>' />"); 
    $("form.rsTable2-insert-form-ecommerce_quotazione").append("<input type='hidden' value='<?php echo $gallery[0]['immagine_file']; ?>' name='<?php echo $off_id_imm[0]['id']; ?>' />");
    $("form.rsTable2-insert-form-ecommerce_quotazione").append("<input type='hidden' value='<?php echo $art['Codice']; ?>' name='<?php echo $off_codice[0]['id']; ?>' />");
    $("form.rsTable2-insert-form-ecommerce_quotazione").append("<input type='hidden' value='<?php echo $personalizzazioni; ?>' name='<?php echo $off_pers[0]['id']; ?>' />");
    
  });
  </script>
  <div class="ez-wr ecommerce-segnala-titolo"><?php echo ln("Richiedi una quotazione per questo articolo"); ?></div>
  <!-- Plain box -->
  <div class="ez-wr ecommerce-quotazione"></div>				
  <?php
}

function stampaSegnalaAmici() { 
  global $config_table_prefix;
  ?>
  <script>
  var tblOptions={
    <?php 
    $tblOptions["container"]="div.ecommerce-segnala";
    $tblOptions["table"]="ecommerce_segnala";
    $tblOptions["insert"]=1;
    $tblOptions["insertId"]='';
    $tblOptions["colFilter"]='nome,email,email1,email2,email3,email4,email5,commento,iscrizione_newsletter,accetto';
    $tblOptions["permDel"]=-1;
    $tblOptions["submitLabel"]=ln("Invia");
          
    echo cryptOptions($tblOptions); 
    ?>
  };
  
  g_table=new rsTable2(tblOptions);
  g_table._insert(function(){
    <?php 
    $off_id_users=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_segnala' AND campo_hidden='id_users')"));
    $off_id_cat=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_segnala' AND campo_hidden='id_categorie_str_magazzino')")); 
    $off_id_imm=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_segnala' AND campo_hidden='immagine_file')")); 
    $off_id_idcat=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_segnala' AND campo_hidden='idcategoria')"));
    $off_id_prezzo=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_segnala' AND campo_hidden='prezzo_cry')"));
    
    $art=retArticoloFromCat($_GET['menid']);
    $gallery=Table2ByTable1("categorie","fotogallery",$_GET['menid'],"attivo='1'","Ordinamento ASC");
    ?>
    $("form.rsTable2-insert-form-ecommerce_segnala").append("<input type='hidden' value='<?php echo $_SESSION['userris_id']; ?>' name='<?php echo $off_id_users[0]['id']; ?>' />"); 
    $("form.rsTable2-insert-form-ecommerce_segnala").append("<input type='hidden' value='<?php echo $_GET['menid']; ?>' name='<?php echo $off_id_cat[0]['id']; ?>' />"); 
    $("form.rsTable2-insert-form-ecommerce_segnala").append("<input type='hidden' value='<?php echo $gallery[0]['immagine_file']; ?>' name='<?php echo $off_id_imm[0]['id']; ?>' />");
    $("form.rsTable2-insert-form-ecommerce_segnala").append("<input type='hidden' value='<?php echo $_GET['menid']; ?>' name='<?php echo $off_id_idcat[0]['id']; ?>' />");
    $("form.rsTable2-insert-form-ecommerce_segnala").append("<input type='hidden' value='<?php echo $art['Prezzo_cry']; ?>' name='<?php echo $off_id_prezzo[0]['id']; ?>' />");
  });
 
  </script>
  <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  			<h4 class="ez-wr ecommerce-segnala-titolo"><?php echo ln("Segnala questo articolo ai tuoi amici"); ?></h4>
  </div>
		  <!-- Plain box -->
	<div class="modal-body"><div class="ez-wr ecommerce-segnala"></div></div>
	
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo ln("Chiudi")?></button>
        <!--  <button type="button" class="btn btn-primary">Send message</button>-->
      </div>		  		
  <?php
}

function stampaTitoli($idcat,$share="") { 
  $objConfig = new ConfigTool();
  $objUtility = new Utility;
  $twitter_account = $objConfig->get("twitter-account");
  $art=retArticoloFromCat($idcat);
  $categorie=retRow("categorie",$idcat);
  ?>
  <!-- Layout 1 -->
  <div class="ez-wr briciole-share-container txt-display clearfix">
    <?php if(($art["Codice"]!="" && $share=="1") || $share=="1") { ?>
      <div class="ez-wr briciole-share">
        <!-- Module 3A -->                                                                                   
        <div class="ez-wr briciole-share-content">                                                                                                            
          <div class="ez-fl  ez-negmx ez-33 briciole-share-fb-container">                                                               
            <!-- Module 2A -->                                                                                                          
            <div class="ez-wr">
              <div class="ez-fl ez-negmr ez-50 briciole-share-fb">
                <div class="ez-box"><a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(curPageURL()); ?>&t=<?php echo stripslashes($categorie['nome']); ?>" target="_blank"><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/fb.gif" width="14"/></a></div>
              </div>
              <div class="ez-last ez-oh">
                <div class="ez-box share-label">Facebook</div>
              </div>
            </div>
          </div>
          <div class="ez-fl ez-negmr ez-33 briciole-share-twitter-container">             
            <div class="ez-wr">
              <div class="ez-fl ez-negmr ez-50 briciole-share-twitter">
                <div class="ez-box"><a href="http://twitter.com/share?original_referer=<?php echo urlencode(curPageURL()); ?>&source=tweetbutton&text=<?php echo urlencode(ln('Seguici su Twitter')); ?>&url=<?php echo urlencode(curPageURL()); ?>&via=<?php echo $twitter_account; ?>" class="twitter-share-button" data-text="<?php echo ln('Seguici su Twitter'); ?>" data-count="horizontal" data-via="<?php echo $twitter_account; ?>" target="_blank" ><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/twitter.gif" width="14"/></a></div>
              </div>
              <div class="ez-last ez-oh">
                <div class="ez-box share-label">Twitter</div>
              </div>
            </div>
          </div>
          
          <div class="ez-last ez-oh">
            <div class="ez-wr">
              <div class="ez-fl ez-negmr ez-50 briciole-share-segnala">
                <div class="ez-box"><a href="rsAction.php?ecommSegnala=1&amp;menid=2087" title=""  data-toggle="modal" data-target="#mdlSegnala" class=""><span style=""><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/ico_email.png" width="16"></span></a>
                </div>
              </div>
              <div class="ez-last ez-oh">                                 
                <div class="ez-box share-label"><?php echo ln("Segnala agli amici"); ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <? } ?>
    <div class="ez-wr ez-oh briciole-left shout">
      <div class="ez-box">
        <?php
        $briciole=retBriciole("","",$idcat);
        $strnome=getStrutturaByNodo($rs[0]['nome']);
        if($_GET['ecomm_riepilogo']!=1 && $strnome=="magazzino") {
          array_unshift($briciole,ln("Prodotti"));
        }elseif($_GET['ecomm_riepilogo']==1){
          $briciole[0]=ln("Carrello");  
        }
        
        $br_last=count($briciole)-1;
        $briciole[$br_last]="<span class='briciole-left-last'>".strip_tags($briciole[$br_last])."</span>";
        
        /*
        if($art["Codice"]!="" && $printArtCode!="-1") {
          $briciole[$br_last].=" <span class='briciole-left-codart'>(".$art["Codice"].")</span>";
        }
        */
        
        if(count($briciole)>1 && $_GET['ecomm_riepilogo']!=1) $briciole=implode(" Ã‚Â» ",$briciole); else $briciole=$briciole[0]; 
        echo $briciole;
        ?>  
      </div>
    </div>
  </div>
  <?php
}

function getRamo($id) { 
  $padre=retRow("categorie",$id);
  $briciole=array();
  while($padre!=false) {
    $lay=retRow('gestione_layout',$padre['id_gestione_layout']);
    array_push($briciole, $padre);
    $padre=getPadre($padre['id']);
  }
  $briciole=array_reverse($briciole);
  return $briciole; 
}

function getRamoEx($id) { 
  $padre=retRow("categorie",$id);
  $briciole=array();
  while($padre!=false) {
    array_push($briciole, $padre['id']);
    $padre=getPadre($padre['id']);
  }
  $briciole=array_reverse($briciole);
  return $briciole; 
}

function onlyreadables($string,$char="-") {
  if($char=="-") $string=strtolower($string);
  $string=str_replace("à","a",$string);
  $string=str_replace("è","e",$string);
  $string=str_replace("é","e",$string);
  $string=str_replace("ò","o",$string);
  $string=str_replace("ì","i",$string);
  $string=str_replace("ù","u",$string);
  $string=str_replace("&"," e ",$string);

  for ($i=0;$i<strlen($string);$i++) {
    $chr = $string{$i};
    $ord = ord($chr);
    if ( (($ord>64) and ($ord<91)) or (($ord>96) and ($ord<123)) or (($ord>47) and ($ord<58))) {
      $string{$i} = $chr;
    } else {
      $chr=$char;
      $string{$i} = $chr;
    }
  }
  
 $string=str_replace("----","-",$string);
 $string=str_replace("---","-",$string);
 $string=str_replace("--","-",$string);
  
  return $string;
}

function fname_onlyreadables($string,$char="-") {
  $string=strtolower($string);
  $string=str_replace("à","a",$string);
  $string=str_replace("è","e",$string);
  $string=str_replace("é","e",$string);
  $string=str_replace("ò","o",$string);
  $string=str_replace("ì","i",$string);
  $string=str_replace("ù","u",$string);
  $string=str_replace("&"," e ",$string);
  
  for ($i=0;$i<strlen($string);$i++) {
    $chr = $string{$i};
    $ord = ord($chr);
    if ( (($ord>64) and ($ord<91)) or (($ord>96) and ($ord<123)) or (($ord>47) and ($ord<58)) or ($ord==46) or ($ord==95)) {
      $string{$i} = $chr;
    } else {
      $chr=$char;
      $string{$i} = $chr;
    }
  }
  
  $char4=$char.$char.$char.$char;
  $char3=$char.$char.$char;
  $char2=$char.$char;
    
  $string=str_replace($char4,$char,$string);
  $string=str_replace($char3,$char,$string);
  $string=str_replace($char2,$char,$string);
    
  return $string;
}

function genPassword($lung_pass="") {
  // Imposto la lunghezza della password a 10 caratteri
  if($lung_pass=="") $lung_pass=10;
  
  // Creo un ciclo for che si ripete per il valore di $lung_pass
  for ($x=1; $x<=$lung_pass; $x++)
  {
    // Se $x è multiplo di 2...
    if ($x % 2){
  
      // Aggiungo una lettera casuale usando chr() in combinazione
      // con rand() che genera un valore numerico compreso tra 97
      // e 122, numeri che corrispondono alle lettere dell'alfabeto
      // nella tabella dei caratteri ASCII
      $mypass = $mypass . chr(rand(97,122));
  
    // Se $x non è multiplo di 2...
    }else{
  
      // Aggiungo alla password un numero compreso tra 0 e 9
      $mypass = $mypass . rand(0,9);
  
    }
  }
                                          
  // Stampo a video il risultato
  return $mypass;
}

function genLogin($str1,$str2) {
  global $config_table_prefix;
  
  $login=$str1.$str2;
  if(strlen($login)>5) $login=substr($login, 0, 5);
  
  $sql="SELECT MAX(id) FROM `".$config_table_prefix."users`";
  $rs=mysql_query($sql);
  $rs=mysql_fetch_array($rs);
  $login=trim($login).($rs[0]+1);
  $login=str_replace(" ", "-", $login);
  return $login;
}

function printToolBar() {
  $exit=false;
  if($_POST["toolbarPosX"]!="") {
    $_SESSION["toolbarPosX"]=$_POST["toolbarPosX"];
    $exit=true;
  }
  
  if($_POST["toolbarPosY"]!="") {
    $_SESSION["toolbarPosY"]=$_POST["toolbarPosY"];
    $exit=true;
  }
  
  if($_POST["toolbarPosH"]!="") {
    $_SESSION["toolbarPosH"]=$_POST["toolbarPosH"];
    $exit=true;
  }
  
  if($exit) exit;
  
  $rs=getTable("toolbar_stile","","attivo='1'");
  if(count($rs)==0) return;
  $bc=retFile($rs[0]['background_file']);
  $logo=retFile($rs[0]['logo_file']);
  $logo_hover=retFile($rs[0]['logo_hover_file']);
  $mess=$rs[0]['messaggio_al_click'];
  ?>
  <style>
  <?php if($bc!="") { ?>
    #FloatToolbar {background-image: url(<?=$bc?>);}
  <? } ?>
  
  <?php if($logo!="") { ?>
    #FloatToolbar .logo {background-image: url(<?=$logo?>);}
  <? } else { ?>
    #FloatToolbar .logo {display:none;}
  <? } ?>
  
  <?php if($logo_hover!="") { ?>
    #FloatToolbar .logo:hover {background-image: url(<?=$logo_hover?>);}
  <? } ?>
  </style>

  <div id="FloatToolbar">
    <form name="toolbarPos" id="toolbarPos" method="post" action="">
      <input type="hidden" id="toolbarPosX" name="toolbarPosX" value="">
      <input type="hidden" id="toolbarPosY" name="toolbarPosY" value="">
      <input type="hidden" id="toolbarPosH" name="toolbarPosH" value="">     
    </form>
    <div class="resize">-</div>
    <div class="logo"></div>
    <?php $rs=getTable("toolbar","Ordinamento ASC","attivo='1'"); ?>
    <table cellspacing="10" class="tab">
      <tr>
      <?php
      $i=0;
      while (list($key, $row) = each($rs)) {
        if($i%2==0) echo "</tr><tr>";
        $i++;
        ?><td><a href="<?=$row['url']?>" title="<?=$row['nome']?>"><img width="16" height="16" src="<?=retFile($row['icona_file'])?>" alt="<?=retFile($row['nome'])?>"></a></td><?    
      }
      ?>
      </tr>
    </table>
    <div style="height:20px;background-image: url(<?=$bc?>);background-position:0px -438px;background-repeat:no-repeat;"></div>
  </div>
  
  <script type="text/javascript"> 
  $(document).ready(function()
  { 
    function SetPosition(event,ui) {
      var Stoppos = $(this).position();
      
      $('#toolbarPos').ajaxForm();
      $('#toolbarPosX').val(Stoppos.left);
      $('#toolbarPosY').val(Stoppos.top);
      $('#toolbarPos').submit();  
    }
    
    <?php if(isset($_SESSION["toolbarPosX"])) { ?>$("#FloatToolbar").css("left",<?php echo $_SESSION["toolbarPosX"]; ?>);<? } ?>
    <?php if(isset($_SESSION["toolbarPosY"])) { ?>$("#FloatToolbar").css("top",<?php echo $_SESSION["toolbarPosY"]; ?>);<? } ?>  
    <?php if(isset($_SESSION["toolbarPosH"])) { ?>$("#FloatToolbar").css("height","<?php echo $_SESSION["toolbarPosH"]; ?>");<? } ?>
    
    $("#FloatToolbar").css("display","block");
    $("#FloatToolbar").draggable({opacity: 0.35,stop:SetPosition});
    $("#FloatToolbar img").hover(function(){$(this).animate({opacity:0.5},500);},function(){$(this).animate({opacity:1.0},1000);});
    $("#FloatToolbar .logo").click(function(){
      alert("<?=$mess?>");
    });
    
    var SetH=$("#FloatToolbar .tab").height()+20<?php if($logo!="") echo"+25"; ?>;
    SetH=SetH.toString()+"px";
    if($("#FloatToolbar").css("height")=="17px") {
      $("#FloatToolbar .resize").html("+");
    }else{
      $("#FloatToolbar").css("height",SetH);
      $("#FloatToolbar .resize").html("-");
    }
    
    $("#FloatToolbar .resize").click(function(){
      $('#toolbarPos').ajaxForm();
      var SetH=$("#FloatToolbar .tab").height()+20<?php if($logo!="") echo"+25"; ?>;
      SetH=SetH.toString()+"px";
      if($("#FloatToolbar").css("height")=="17px") {
        $("#FloatToolbar").animate({height:SetH},500);
        $('#toolbarPosH').val(SetH);
        $(this).html("-");
      }else{
        $("#FloatToolbar").animate({height:"17px"},500);
        $('#toolbarPosH').val("17px");
        $(this).html("+");
      }
      $('#toolbarPos').submit();  
    }); 
  });
  </script>
  <?
}

function logToFile($message, $filename) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message . "\n";
    file_put_contents($filename, $logMessage, FILE_APPEND);
}

function geografia($idComune="", $NameCapDest="", $NameComuneDest="", $NameProvinciaDest="",
                   $NameProvinciaEstesaDest="",$NamePrefissoTelDest="",$NameRegioneDest="",
                   $NameSiglaRegioneDest="",$NameZonaDest="",$NameCapoluogoDest="") 
{
  $exit=false;
  if($_POST["RsComune"]!="") {
    $comune=retRow("comuni",$_POST["RsComune"]);
    $provincia=retRow("province",$comune['id_province']);
    $regione=retRow("regioni",$provincia['id_regioni']);
    
    if($_POST["RsIdCapDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdCapDest"]?>]").val("<?=$comune['cap']?>");
        $("[name=<?=$_POST["RsIdCapDest"]?>]").attr("valueid","<?=$comune['id']?>");       
      });
      </script>
      <?
      $exit=true;
    }
    
    if($_POST["RsIdComuneDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdComuneDest"]?>]").val("<?=$comune['comune']?>");
        $("[name=<?=$_POST["RsIdComuneDest"]?>]").attr("valueid","<?=$comune['id']?>"); 
        $("[name=<?=$_POST["RsIdComuneDest"]?>]").trigger("change");      
      });
      </script>
      <?
      $exit=true;
    }
    
    if($_POST["RsIdProvinciaDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdProvinciaDest"]?>]").val("<?=$provincia['sigla']?>"); 
        $("[name=<?=$_POST["RsIdProvinciaDest"]?>]").attr("valueid","<?=$provincia['id']?>");
        $("[name=<?=$_POST["RsIdProvinciaDest"]?>]").trigger("change");         
      });
      </script>
      <?
      $exit=true;
    }
    
    if($_POST["RsIdProvinciaEstesaDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdProvinciaEstesaDest"]?>]").val("<?=$provincia['provincia']?>");
        $("[name=<?=$_POST["RsIdProvinciaEstesaDest"]?>]").attr("valueid","<?=$provincia['id']?>"); 
        $("[name=<?=$_POST["RsIdProvinciaEstesaDest"]?>]").trigger("change");         
      });
      </script>
      <?
      $exit=true;
    }
    
    if($_POST["RsIdPrefissoTelDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdPrefissoTelDest"]?>]").val("<?=$comune['prefisso_tel']?>");
        $("[name=<?=$_POST["RsIdPrefissoTelDest"]?>]").attr("valueid","<?=$comune['id']?>");
        $("[name=<?=$_POST["RsIdPrefissoTelDest"]?>]").trigger("change");          
      });
      </script>
      <?
      $exit=true;
    }
    
    if($_POST["RsIdRegioneDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdRegioneDest"]?>]").val("<?=$regione['regione']?>");
        $("[name=<?=$_POST["RsIdRegioneDest"]?>]").attr("valueid","<?=$regione['id']?>");
        $("[name=<?=$_POST["RsIdRegioneDest"]?>]").trigger("change");          
      });
      </script>
      <?
      $exit=true;
    }
    
    if($_POST["RsIdSiglaRegioneDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdSiglaRegioneDest"]?>]").val("<?=$regione['sigla_regione']?>");
        $("[name=<?=$_POST["RsIdSiglaRegioneDest"]?>]").attr("valueid","<?=$regione['id']?>");
        $("[name=<?=$_POST["RsIdSiglaRegioneDest"]?>]").trigger("change");          
      });
      </script>
      <?
      $exit=true;
    }
    
    if($_POST["RsIdZonaDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdZonaDest"]?>]").val("<?=$regione['zona']?>");
        $("[name=<?=$_POST["RsIdZonaDest"]?>]").attr("valueid","<?=$regione['id']?>");
        $("[name=<?=$_POST["RsIdZonaDest"]?>]").trigger("change");          
      });
      </script>
      <?
      $exit=true;
    }
    
    if($_POST["RsIdCapoluogoDest"]!="") {
      ?>
      <script>
      $(document).ready(function() { 
        $("[name=<?=$_POST["RsIdCapoluogoDest"]?>]").val("<?=$regione['capoluogo']?>");
        $("[name=<?=$_POST["RsIdCapoluogoDest"]?>]").attr("valueid","<?=$regione['id']?>"); 
        $("[name=<?=$_POST["RsIdCapoluogoDest"]?>]").trigger("change");         
      });
      </script>
      <?
      $exit=true;
    }
    
    if($exit) exit;
  }
  
  if($idComune=="" && $NameCapDest=="" && $NameComuneDest=="" && $NameProvinciaDest=="" &&
      $NameProvinciaEstesaDest=="" && $NamePrefissoTelDest=="" && $NameRegioneDest=="" &&
      $NameSiglaRegioneDest=="" && $NameZonaDest=="" && $NameCapoluogoDest=="") return;
     
  global $RsGeoNotPrint;
  if($RsGeoNotPrint!=true) { ?>
    <script>
    $(document).ready(function() { 
      <?php if($NameCapDest!="") { ?>if($("[name=<?=$NameCapDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NameCapDest?>" value="">');<? } ?>
      <?php if($NameComuneDest!="") { ?>if($("[name=<?=$NameComuneDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NameComuneDest?>" value="">');<? } ?>
      <?php if($NameProvinciaDest!="") { ?>if($("[name=<?=$NameProvinciaDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NameProvinciaDest?>" value="">');<? } ?>
      <?php if($NameProvinciaEstesaDest!="") { ?>if($("[name=<?=$NameProvinciaEstesaDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NameProvinciaEstesaDest?>" value="">');<? } ?>
      <?php if($NamePrefissoTelDest!="") { ?>if($("[name=<?=$NamePrefissoTelDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NamePrefissoTelDest?>" value="">');<? } ?>
      <?php if($NameRegioneDest!="") { ?>if($("[name=<?=$NameRegioneDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NameRegioneDest?>" value="">');<? } ?>
      <?php if($NameSiglaRegioneDest!="") { ?>if($("[name=<?=$NameSiglaRegioneDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NameSiglaRegioneDest?>" value="">');<? } ?>
      <?php if($NameZonaDest!="") { ?>if($("[name=<?=$NameZonaDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NameZonaDest?>" value="">');<? } ?>  
      <?php if($NameCapoluogoDest!="") { ?>if($("[name=<?=$NameCapoluogoDest?>]").length==0) $("#RsFrmGeografia").append('<input type="hidden" name="<?=$NameCapoluogoDest?>" value="">');<? } ?>        
    });
    </script>
    <form action="rsAction.php" id="RsFrmGeografia" name="RsFrmGeografia" method="post" >
      <input type="hidden" id="RsComune" name="RsComune" value="">
      
      <input type="hidden" id="RsIdCapDest" name="RsIdCapDest" value="">
      <input type="hidden" id="RsIdComuneDest" name="RsIdComuneDest" value="">
      <input type="hidden" id="RsIdProvinciaDest" name="RsIdProvinciaDest" value="">
      <input type="hidden" id="RsIdProvinciaEstesaDest" name="RsIdProvinciaEstesaDest" value="">
      <input type="hidden" id="RsIdPrefissoTelDest" name="RsIdPrefissoTelDest" value="">
      <input type="hidden" id="RsIdRegioneDest" name="RsIdRegioneDest" value="">
      <input type="hidden" id="RsIdSiglaRegioneDest" name="RsIdSiglaRegioneDest" value="">
      <input type="hidden" id="RsIdZonaDest" name="RsIdZonaDest" value="">
      <input type="hidden" id="RsIdCapoluogoDest" name="RsIdCapoluogoDest" value="">
    </form>
    <div id="RsRespGeografia" sttyle="display:none;"></div><? 
    
    $RsGeoNotPrint = true;
  } ?>
  <script>
  $(document).ready(function() { 
    var options = { 
      target: '#RsRespGeografia' 
    }; 
   
    $('#RsFrmGeografia').ajaxForm(options);
    
    $("#<?=$idComune?>").bind("change",function(){
      $("#RsComune").val($("#<?=$idComune?>").val());
      
      $("#RsIdCapDest").val("<?=$NameCapDest?>");
      $("#RsIdComuneDest").val("<?=$NameComuneDest?>");
      $("#RsIdProvinciaDest").val("<?=$NameProvinciaDest?>");
      $("#RsIdProvinciaEstesaDest").val("<?=$NameProvinciaEstesaDest?>");
      $("#RsIdPrefissoTelDest").val("<?=$NamePrefissoTelDest?>");
      $("#RsIdRegioneDest").val("<?=$NameRegioneDest?>");
      $("#RsIdSiglaRegioneDest").val("<?=$NameSiglaRegioneDest?>");
      $("#RsIdZonaDest").val("<?=$NameZonaDest?>");
      $("#RsIdCapoluogoDest").val("<?=$NameCapoluogoDest?>");
      $('#RsFrmGeografia').submit();
    }); 
  });
  </script>
  <?
}

function cdate() {
  $day = date("d",time()); 
  $month = date("m",time()); 
  $year = date("Y",time()); 
  
  $cdate = "$year-$month-$day";
  return $cdate; 
}

function dateAdd($date,$addY="",$addM="",$addD="") {
  $arr_date=explode("-", $date);
  
  $day = $arr_date[2]; 
  $month = $arr_date[1]; 
  $year = $arr_date[0];
  
  if($addY!="") $year=$year+($addY); 
  if($addM!="") $month=$month+($addM);
  if($addD!="") $day=$day+($addD);
  
  $cdate = "$year-$month-$day";
  return $cdate;   
}
                    
function bannerMerge($jpg_banner,$png_bg,$jpg_final="",$x=0,$y=0,$maskrgb="") { 
  $imgsrc = imagecreatefromjpeg($jpg_banner);
  $imgwm = imagecreatefrompng($png_bg);
  $imgsrc2 = imagecreatetruecolor(imagesx($imgwm), imagesy($imgwm));  
           
  imagecopy($imgsrc2, $imgsrc, $x,$y, 0, 0, imagesx($imgsrc), imagesy($imgsrc));
  imagecopy($imgsrc2, $imgwm, 0, 0, 0, 0, imagesx($imgwm), imagesy($imgwm));
  
  if(is_array($maskrgb)) {
    $transparent = imagecolorallocate($imgsrc2, $maskrgb[0], $maskrgb[1], $maskrgb[2]);
    imagecolortransparent($imgsrc2, $transparent);
  }
  
  if($jpg_final=="") $jpg_final="images/".md5($jpg_banner).".png";
  imagepng($imgsrc2,$jpg_final);
  imagedestroy($imgsrc2);
  return $jpg_final;
}

function addCatToMagazzino($cat) {
  global $config_table_prefix;
  
  $mag=getTable("strutture","","nome='magazzino'");
  $magid=$mag[0]['id'];
  
  if($magid>0){
    $sql="INSERT INTO `".$config_table_prefix."strutture#categorie_nm` (id_strutture,id_categorie) VALUES ('".$magid."','".$cat."')";
    mysql_query($sql);
  }  
}

function html2rgb($color){    if ($color[0] == '#')        $color = substr($color, 1);    if (strlen($color) == 6)        list($r, $g, $b) = array($color[0].$color[1],                                 $color[2].$color[3],                                 $color[4].$color[5]);    elseif (strlen($color) == 3)        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);    else        return false;    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);    return array($r, $g, $b);}
function rgb2html($r, $g=-1, $b=-1){    if (is_array($r) && sizeof($r) == 3)        list($r, $g, $b) = $r;    $r = intval($r); $g = intval($g);    $b = intval($b);    $r = dechex($r<0?0:($r>255?255:$r));    $g = dechex($g<0?0:($g>255?255:$g));    $b = dechex($b<0?0:($b>255?255:$b));    $color = (strlen($r) < 2?'0':'').$r;    $color .= (strlen($g) < 2?'0':'').$g;    $color .= (strlen($b) < 2?'0':'').$b;    return '#'.$color;}

function addCatToCat($nodo,$figlio) {
  global $config_table_prefix;
  
  $sql="INSERT INTO `".$config_table_prefix."categorie#categorie_nm` (id_categorie,id_categorie_self) VALUES ('".$nodo."','".$figlio."')";
  mysql_query($sql); 
}

function maxOrd($table){
  $maxord=getTable($table,"Ordinamento DESC","");
  $maxord=$maxord[0]['Ordinamento']+10;
  
  return $maxord;
}

function catExist($nome,$struttura) {
  $cat=getTable("categorie","","nome='".addslashes($nome)."'");
  while (list($key, $row) = each($cat)) {
    $str=getStrutturaByNodo($row['id']);
    if($str['nome']==$struttura) {
      return $str;
    } 
  }
  
  return false;  
}

function addArticoloFromCat($cat) {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  $table=$config_table_prefix."categorie"; 
  $mag=getStrutturaByNodo($cat);
  if($mag['nome']=="magazzino") { 
    $table2=$config_table_prefix."magazzino_articoli"; 
    $sql="SELECT MAX(id) FROM `".$table2."`";
    $rs=mysql_query($sql);
    $row=mysql_fetch_array($rs);
    
    $sql="INSERT INTO `".$table2."` (Codice,id_categorie_str_magazzino,aggiornato) VALUES ('".($row[0]+1)."','".$cat."',NOW())";
    $rs=mysql_query($sql);
    $nid=mysql_insert_id();
    $nm_table="categorie#magazzino_articoli_nm";
    $nm_sql1="(id_categorie,id_magazzino_articoli)";
    $nm_sql2="('".$cat."','".$nid."')";
    $sql="INSERT INTO `".$config_table_prefix.$nm_table."` ".$nm_sql1." VALUES ".$nm_sql2;
    mysql_query($sql);
    
    return $nid;
  }
}

function deleteArticoloFromCat($cat) {
  global $config_table_prefix;
  
  $mag=getStrutturaByNodo($cat);
  if($mag['nome']=="magazzino") {
    $magazzino_articoli=Table2ByTable1("categorie","magazzino_articoli",$cat,"","");
    while (list($key, $row) = each($magazzino_articoli)) {
      deleteArticolo($row['id']);
    }    
  }
}

function deleteArticolo($articoloid) {
  global $config_table_prefix;
  
  $sql="UPDATE ".$config_table_prefix."magazzino_articoli SET del_hidden='1' WHERE id='".$articoloid."'";
  mysql_query($sql);  
}

function isValidCodArt($articoloid,$newcod) {
  global $config_table_prefix;
  
  if($newcod!="") {
    $rs=getTable("magazzino_articoli","","(Codice='".addslashes($newcod)."' AND del_hidden='0' AND id<>'".$articoloid."')");
    if(count($rs)>0) {
      return false;
    }
  }else{
    return false;
  }
  
  return true;
}

function updateArticolo($articoloid) {
  global $config_table_prefix;
  
  $rs=retRow("magazzino_articoli",$articoloid);
  $max_cod=getTable("magazzino_articoli","id DESC LIMIT 1","(Codice='".addslashes($rs['Codice'])."')");
  $max_cod=$max_cod[0];
  
  $sql="INSERT INTO `".$config_table_prefix."magazzino_articoli` () VALUES ()";
  mysql_query($sql);
  $nid=mysql_insert_id();
  
  $nm_table="categorie#magazzino_articoli_nm";
  $nm_sql1="(id_categorie,id_magazzino_articoli)";
  $nm_sql2="('".$max_cod['id_categorie_str_magazzino']."','".$nid."')";
  $sql="INSERT INTO `".$config_table_prefix.$nm_table."` ".$nm_sql1." VALUES ".$nm_sql2;
  mysql_query($sql);
  
  $sql="UPDATE `".$config_table_prefix."magazzino_articoli` SET del_hidden='1',id_categorie_str_magazzino='".$max_cod['id_categorie_str_magazzino']."' WHERE id='".$max_cod['id']."'";
  mysql_query($sql);
  
  return $nid;
}

function retArticoloFromCat($cat) {
  $rs=getTable("magazzino_articoli","","(id_categorie_str_magazzino='$cat' AND del_hidden=0)");
  return $rs[0];  
}

function parseToFloat($val,$dec="") {
  $val=trim($val);
  if(strpos($val,",")!==FALSE) $val=str_replace(".", "", $val);
  $val=str_replace(",", ".", $val);
  $val=trim($val);
  $val=(float)$val;
  
  if($dec!="") $val= round($val, $dec);
  
  return $val;
}

function setJQueryUploader($id,$onComplete,$fileExt="") {
  $objUtility = new Utility;
  
  ?>
  $('#<?=$id?>').uploadify({
    'uploader': '<?php echo $objUtility->getPathBackofficeResources() ?>uploadify.swf',
    'script': '<?php echo $objUtility->getPathBackofficeResources() ?>uploadify.php',
    'folder': '<?php echo $objUtility->getPathUserFiles() . "Upload" ?>',
    'cancelImg': '<?php echo $objUtility->getPathBackofficeResources() ?>cancel.png',
    'onComplete': <?=$onComplete?>,
    'auto': true
    <?php if($fileExt!="") {?>
    ,'fileExt': '<?=$fileExt?>',
     'fileDesc': 'File ammessi:<?=$fileExt?>'
    <? } ?> 
  });
  <?
}

function EndsWith($FullStr, $EndStr)
{
  // Get the length of the end string
  $StrLen = strlen($EndStr);
  // Look at the end of FullStr for the substring the size of EndStr
  $FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
  // If it matches, it does end with EndStr
  return $FullStrEnd == $EndStr;
}

function getAbsoluteUrl($pageUrl,$imgSrc) 
{ 
    $imgInfo = parse_url($imgSrc); 
    if (! empty($imgInfo['host'])) { 
        //img src is already an absolute URL 
        return $imgSrc; 
    } 
    else { 
        $urlInfo = parse_url($pageUrl); 
        $base = $urlInfo['scheme'].'://'.$urlInfo['host']; 
        if (substr($imgSrc,0,1) == '/') { 
            //img src is relative from the root URL 
            return $base . $imgSrc; 
        } 
        else { 
            //img src is relative from the current directory 
               return  
                    $base 
                    . substr($urlInfo['path'],0,strrpos($urlInfo['path'],'/')) 
                    . '/' . $imgSrc; 
        } 
    } 
}

function getStrutturaByNodo($cat) {
  $ramo=getRamo($cat);
  $idstruttura=getTable("strutture#categorie_nm","","(id_categorie='".$ramo[0]['id']."')");
  $struttura=retRow("strutture",$idstruttura[0]["id_strutture"]);
  
  return $struttura;
}

function genPDFbyHTML_Images($html) {
  $objUtility = new Utility;
  $docroot=$objUtility->getPathRoot();
  
  $dom = new DOMDocument;

  //Parse the HTML. The @ is used to suppress any parsing errors
  //that will be thrown if the $html string isn't valid XHTML.
  @$dom->loadHTML($html);
   
  //Get all links. You could also use any other tag name here,
  //like 'img' or 'table', to extract other tags.
  $imgs = $dom->getElementsByTagName('img');
   
  //Iterate over the extracted links and display their URLs
  foreach ($imgs as $img){
  	//Extract and show the "href" attribute. 
  	$file=$img->getAttribute('src');
  	//$name=basename($file);
    $file="http://".$_SERVER['SERVER_NAME'].$file;
    $img->setAttribute('src',$file); 
  }
  
  $html=$dom->saveHTML();
  $html=stripslashes($html);
  $html=html_entity_decode($html);
  
  return $html;
}

function genPDFbyHTML($html,$private,$dest_nome="") {
  require_once (SERVER_DOCROOT."logic/html2pdf/html2pdf.class.php");
  
  global $config_table_prefix;
  $objUtility = new Utility;
  
  $html=genPDFbyHTML_Images($html);
  
  $nome=$objUtility->getFilenameUnique();
  $ext="pdf";
  if($private==1) {
    $path=$objUtility->getPathResourcesPrivateAbsolute();
  } else {
    $private="";
    $path=$objUtility->getPathResourcesDynamicAbsolute();  
  }
  $fname=$path.$nome.".".$ext;
              
  //$pdf= new HTML2FPDF();
  $pdf = new HTML2PDF();
  //$pdf->AddPage();
  $html=html_entity_decode($html);
  $html=str_replace("&rsquo;", "'", $html);
  
  $pdf->WriteHTML($html);
  $pdf->Output($fname,"F");
  
  if($dest_nome=="") $dest_nome=$nome.".".$ext; 
  
  $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,path,originalname,ext,isprivate) VALUES ('$nome','$path','$dest_nome','$ext','$private')";
  mysql_query($sql);
  $id=mysql_insert_id();
  
  return $id;
} 

function PasswordDimenticata() {
  if(isAjaxPost()) return;
  
  $objConfig = new ConfigTool();
  $objUtility = new Utility;
  $objMailing = new Mailing;
  
  $username=$_POST["sendUser"];
  $emailtmp=$_POST["sendEmail"];
  $email="";
  
  if($username!="") {
    $user=getTable("users","","(login='$username')");
    if(count($user)>0) {
      $email=$user[0]['email'];
      $id_users=$user[0]['id'];
      $pass=$user[0]['pwd'];
      $login=$user[0]['login'];        
    }
  }
  
  if($email=="" && $emailtmp!="") {
    $user=getTable("users","","(email='$emailtmp')");
    if(count($user)>0) {
      $email=$user[0]['email'];
      $id_users=$user[0]['id'];
      $pass=$user[0]['pwd'];
      $login=$user[0]['login'];        
    }      
  }
  
  if($email!="") {
    $objMailing->mmail($email,$objConfig->get("email-from"),ln("Dati area riservata"),ln("Gentile Utente, di seguito i dati per accedere all'area riservata del sito"). " http://".$_SERVER['SERVER_NAME'].":<br><br>username: $login<br><br><p><a href='http://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."index.php?documents=1&user=".$id_users."&pass=".$pass."' target='_blank'>".ln("clicca qui per accedere alla tua area riservata")."</a> ".ln("oppure copia e incolla il seguente indirizzo:")."<br><br>http://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."index.php?documents=1&user=".$id_users."&pass=".$pass."</p><br><br>".replaceEcomerceMarkers(ln("Cordiali saluti,<br>#ecomm_ragionesociale#")).".","","","");
    box(ln("Riceverai a breve una e-mail con le tue credenziali per accedere all'area riservata."));
  } else {
    box(ln("I dati inseriti non risultano corretti."));
  }
}

function htmlEDtiny ($str) {
  $str=str_replace("à", "&agrave;", $str);
  $str=str_replace("á", "&aacute;", $str);
  $str=str_replace("è", "&egrave;", $str);
  $str=str_replace("é", "&eacute;", $str);
  $str=str_replace("ì", "&igrave;", $str);
  $str=str_replace("í", "&iacute;", $str);
  $str=str_replace("ò", "&ograve;", $str);
  $str=str_replace("ó", "&oacute;", $str);
  $str=str_replace("ù", "&ugrave;", $str);
  $str=str_replace("ú", "&uacute;", $str);
  $str=str_replace("À", "&Agrave;", $str);
  $str=str_replace("Í", "&Aacute;", $str);
  $str=str_replace("È", "&Egrave;", $str);
  $str=str_replace("É", "&Eacute;", $str);
  $str=str_replace("Ì", "&Igrave;", $str);
  $str=str_replace("Á", "&Iacute;", $str);
  $str=str_replace("Ò", "&Ograve;", $str);
  $str=str_replace("Ó", "&Oacute;", $str);
  $str=str_replace("Ù", "&Ugrave;", $str);
  $str=str_replace("Ú", "&Uacute;", $str);
  
  return $str;
}

function htmlentitiesEx($str) {
  $str=str_replace("à", "&agrave;", $str);
  $str=str_replace("á", "&aacute;", $str);
  $str=str_replace("è", "&egrave;", $str);
  $str=str_replace("é", "&eacute;", $str);
  $str=str_replace("ì", "&igrave;", $str);
  $str=str_replace("í", "&iacute;", $str);
  $str=str_replace("ò", "&ograve;", $str);
  $str=str_replace("ó", "&oacute;", $str);
  $str=str_replace("ù", "&ugrave;", $str);
  $str=str_replace("ú", "&uacute;", $str);
  $str=str_replace("À", "&Agrave;", $str);
  $str=str_replace("Á", "&Aacute;", $str);
  $str=str_replace("È", "&Egrave;", $str);
  $str=str_replace("É", "&Eacute;", $str);
  $str=str_replace("Ì", "&Igrave;", $str);
  $str=str_replace("Í", "&Iacute;", $str);
  $str=str_replace("Ò", "&Ograve;", $str);
  $str=str_replace("Ó", "&Oacute;", $str);
  $str=str_replace("Ù", "&Ugrave;", $str);
  $str=str_replace("Ú", "&Uacute;", $str);
  $str=str_replace("’", "'", $str);
  $str=str_replace("“", "&ldquo;", $str);
  $str=str_replace("”", "&rdquo;", $str);
  $str=str_replace("€", "&euro;", $str);
  $str=str_replace("…", "&hellip;", $str);
  $str=str_replace("—", "&mdash;", $str);
  $str=str_replace("–", "&ndash;", $str);
  $str=str_replace("°", "&deg;", $str);
 
  $str=htmlentities($str,ENT_QUOTES,"UTF-8");
  return $str;
}

function genUrlRewriteByStruttura($struttura) {
  global $config_table_prefix;
  
  $objConfig=new ConfigTool();
  $keyword=$objConfig->get("keyword");
  
  $keyword=explode(",", $keyword);
  
  $myArr=getStrutturaFull($struttura);
  
  while (list($key, $row) = each($myArr)) {
    $ramo=getRamo($row['id']);
    $onlyrdb="";
    $sep="";
    while (list($key2, $row2) = each($ramo)) {
      $tmpk=onlyreadables(trim($row2['nome']));
      if(trim($tmpk)!="") {
        if($key2>0) $sep="-"; 
        $onlyrdb.=$sep.$tmpk;
      }  
    }
    
    if(is_array($keyword) && count($keyword)>0) {
      $c=count($keyword);
      if($c>5) $c=1; 
      $keyt=array_rand($keyword,$c); 
    }
    
    if(is_array($keyt)) {
      while (list($key1, $row1) = each($keyt)) {
        if($keyword[$row1]!="") $onlyrdb.="-".$keyword[$row1];  
      }
    } 
    
    $onlyrdb=str_ireplace("-.html", ".html", left($onlyrdb,120).".html");
    
    $sql="UPDATE `".$config_table_prefix."categorie` SET url='".addslashes($onlyrdb)."' WHERE id='".$row['id']."'";
    mysql_query($sql);  
  }
}

function array_merge_unique($dest,$from,$field="id") {
  $ret_arr=$dest;
  
  while (list($key, $row) = each($from)) {
    $from_id=$row[$field];
    
    $trovato=0;
    for($i=0;$i<count($dest);$i++) {
      $dest_id=$dest[$i][$field];
      if($from_id==$dest_id) $trovato=1;
    }
    
    if($trovato==0) {
      array_push($ret_arr, $row);  
    }  
  }
  
  return $ret_arr;     
}

function in_multi_array($needle, $haystack) { 
  $in_multi_array = false; 
  if(in_array($needle, $haystack)) 
  { 
      $in_multi_array = true; 
  } 
  else 
  {    
      for($i = 0; $i < sizeof($haystack); $i++) 
      { 
          if(is_array($haystack[$i])) 
          { 
              if(in_multi_array($needle, $haystack[$i])) 
              { 
                  $in_multi_array = true; 
                  break; 
              } 
          } 
      } 
  } 
  return $in_multi_array; 
}

function array_sort_func($a,$b=NULL) { 
   static $keys; 
   if($b===NULL) return $keys=$a; 
   foreach($keys as $k) { 
      if(@$k[0]=='!') { 
         $k=mb_substr($k,1); 
         if(mb_strtolower(@$a[$k])!==mb_strtolower(@$b[$k])) { 
            return strcmp(mb_strtolower(@$b[$k]),mb_strtolower(@$a[$k])); 
         } 
      } 
      else if(mb_strtolower(@$a[$k])!==mb_strtolower(@$b[$k])) { 
         return strcmp(mb_strtolower(@$a[$k]),mb_strtolower(@$b[$k])); 
      } 
   } 
   return 0; 
} 

function array_sort(&$array) { 
   if(!$array) return $keys; 
   $keys=func_get_args(); 
   array_shift($keys); 
   array_sort_func($keys); 
   usort($array,"array_sort_func");        
} 

function troncaTesto($testo, $caratteri=50) { 
  if(strlen($testo)<=$caratteri) return $testo; 
  
  $testo=strip_tags($testo);
  $nuovo = mb_wordwrap($testo, $caratteri, "|"); 
  $nuovotesto=explode("|",$nuovo); 
  return $nuovotesto[0]."&hellip;"; 
}

function endphp() {
  if(isset($_POST['phpss'])) {
    $objphpss = new Session($_POST['phpss']);
    $objphpss->save($_SESSION);  
  }
} 

function right($value, $count){
    return substr($value, ($count*-1));
}

function left($string, $count){
    return substr($string, 0, $count);  
}

function printVideo($rs,$default,$rows="0",$cols="0",$antW="100",$antH="0",$vidW="400",$vidH="300") {
  $objUtility = new Utility;
  
  $i=0;
  $n=count($rs);
  if($n==0) return;
  if($rows>0) $cols=ceil($n/$rows);
  if($rows==0 && $cols>0) $rows=ceil($n/$cols);
  if($default=="") $default=$objUtility->getPathBackofficeResourcesAbsolute()."video-icon.png"; 
  $default=imgResize ($default,$antW,$antH,$addObject="",$absolute="");
  if(!$default) $default=$objUtility->getPathBackofficeResourcesAbsolute()."video-icon.png";
   
  ?> 
  <div class="rsVideoGallery-container">
    <table>
      <?php for($j=0;$j<$rows;$j++) { ?>
        <tr>
        <?php for($k=0;$k<$cols;$k++) { ?>
          <td class="rsVideoGallery-td">
            <!-- Layout 2 -->
            <div class="ez-wr rsVideoGallery">
              <?php if($rs[$i]['nome']!="") { ?>
                <div class="ez-box rsVideoGallery-titolo"><?php echo $rs[$i]['nome']; ?></div>
              <? } ?>
              <!-- Module 2A -->
              <div class="ez-wr">
                <div class="ez-fl ez-negmr ez-50 rsVideoGallery-sx">
                  <div class="ez-box">
                    <?php
                      if($i<$n) {
                        $file = $rs[$i]['video_file'];
                        if (!$file) $file = $rs[$i]['link_youTube'];
                        
                        $anteprima = retFile($rs[$i]["anteprima_file"],$antW,$antH);
                        $img = "<img src=".$anteprima.">";
                        if(!$anteprima && file_exists($default)) {
                          $img = "<img src=".$objUtility->getPathBackofficeResources()."video-icon.png"." />";
                        } elseif (!file_exists($default)) {
                          $img = "";
                        }
                         
                        lyteFrameVideo($file,$img,$title="",$vidW,$vidH,$class="",$style="",40,40);
                      } 
                    ?>
                  </div>
                </div>
                <?php if($rs[$i]['descrizione']!="") { ?>
                  <div class="ez-last ez-oh rsVideoGallery-dx">
                    <div class="ez-box"><?php echo $rs[$i]['descrizione']; ?></div>
                  </div>
                <? } ?>
              </div>
            </div>
            <?php $i++; ?>
          </td>
        <? } ?>  
        </tr>
    <? } ?>
    </table>
  </div>
<? } 

function rs_encrypt($input,$sess="") {
  $objConfig = new ConfigTool();
  $objUtility = new Utility();
  
  if($sess!=""){
    $ct = session_id();
    
    $key = left(md5($objConfig->get("mcrypt-key").$ct),15);
    $sessname='rsToken_'.md5($sess);
    
    $_SESSION[$sessname] = $key;
  }else{
    $key = left(md5($objConfig->get("mcrypt-key")),15);
  }
  
  $td = mcrypt_module_open('tripledes', '', 'ecb', '');
  $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
  mcrypt_generic_init($td, $key, $iv);
  $encrypted_data = mcrypt_generic($td, $input);
  mcrypt_generic_deinit($td);
  mcrypt_module_close($td);
  
  return base64_encode($encrypted_data);
}

function rs_decrypt($input,$sess="") {
  $objConfig = new ConfigTool();
  
  if($sess!=""){
    $sessname='rsToken_'.md5($sess);
    if(isset($_SESSION[$sessname])) $key = $_SESSION[$sessname]; else return false;
  }else{
    $key = left(md5($objConfig->get("mcrypt-key")),15);
  }
  
  $td = mcrypt_module_open('tripledes', '', 'ecb', '');
  $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
  mcrypt_generic_init($td, $key, $iv);
  $tf=base64_decode($input);
  if($tf!="") $decrypted_data = mdecrypt_generic($td, $tf);
  
  mcrypt_generic_deinit($td);
  mcrypt_module_close($td);
  
  return rtrim($decrypted_data,"\0");
}

function retExt($filname) {
  $arr_file=explode(".", $filname);
  $arr_file=array_reverse($arr_file);
  $ext=trim($arr_file[0]);
  
  return $ext;
}

function retExtByID($id) {
  $f=retFile($id);
  $f=basename($f);
  $ext=retExt($f);
  return $ext;
}

function isImageByID($id) {
  $f=retFile($id);
  $f=basename($f);
  return isImage($f);  
}

function isImage($f) {
  $ext=strtolower(retExt($f));
  
  if($ext=="jpg" || $ext=="jpeg" || $ext=="bmp" || $ext=="gif" || $ext=="png" || $ext=="tif"){
    return true;
  }else{
    return false;
  }  
} 

function isAjaxPost(){
  if($_POST['rsPagin']!="") return true;
  
  return false;
}

function loadTbl($dbname) {
  global $config_table_prefix;
  $objUtility = new Utility;
  $objConfig = new ConfigTool();
  $objTable2 = new rsTable2();
  
  $dbname = $objConfig->get("db-dbname");
  $sql="SHOW TABLE STATUS FROM $dbname";
  $query = mysql_query($sql);

  while($res=mysql_fetch_array($query)) {
    $sql="SELECT id FROM `".$config_table_prefix."rstbl2_tabelle` WHERE tabella='".$res[0]."'";
    $query2 = mysql_query($sql);
    if(strpos($res[0],"_nm")===FALSE && strpos($res[0],$config_table_prefix)!==FALSE) {
      if(mysql_num_rows($query2)==0) {
        $tmptit=$objTable2->filterTabName($res[0]);
        $sql="INSERT INTO `".$config_table_prefix."rstbl2_tabelle` (tabella,titolo_visualizzato) VALUES ('".$res[0]."','".$tmptit."') ";
        $query3 = mysql_query($sql);
        $idtab=mysql_insert_id();
      }else{
        $tmp = $objUtility->buildRecordset($query2);
        $idtab=$tmp[0]['id'];    
      }
    
      $sql="SHOW COLUMNS FROM `".$res[0]."`";
      $cols=mysql_query($sql);
      $cols = $objUtility->buildRecordset($cols);
      
      $sql="SELECT id,campo_hidden FROM `".$config_table_prefix."rstbl2_campi` WHERE tabella_hidden='".$res[0]."'";
      $query2 = mysql_query($sql);
      $tver=$objUtility->buildRecordset($query2);
      while (list($tkey, $trow) = each($tver)) {
        $sql="SELECT ".$trow['campo_hidden']." FROM `".$res[0]."`";
        $tq=mysql_query($sql);
        if(!$tq) {
          $sql="DELETE FROM `".$config_table_prefix."rstbl2_campi` WHERE id='".$trow['id']."'";
          mysql_query($sql);    
        }    
      }
      
      reset($cols);
      while (list($key, $row) = each($cols)) {
        $field=$row['Field'];
        $sql="SELECT id,campo_hidden FROM `".$config_table_prefix."rstbl2_campi` WHERE (campo_hidden='".$field."' AND tabella_hidden='".$res[0]."')";
        $query2 = mysql_query($sql);

        if(mysql_num_rows($query2)==0) {
          $ptit=$objTable2->filterColName($field);
          if($ptit!="" && $ptit!="id"){
            $sql="SELECT Ordinamento FROM `".$config_table_prefix."rstbl2_campi` ORDER BY id DESC LIMIT 0,1";
            $query3 = mysql_query($sql);
            $pmax=mysql_fetch_array($query3);
            $pmax=$pmax['Ordinamento']+10;
            
            if(left($res[0],strlen($config_table_prefix))==$config_table_prefix) {
              $tab_filtered=substr($res[0], strlen($config_table_prefix), strlen($res[0])-strlen($config_table_prefix));  
            }
            
            $rsPower=$objTable2->filterFunName($field, $tab_filtered);
            
            $sql="INSERT INTO `".$config_table_prefix."rstbl2_campi` (tabella_hidden,campo_hidden,titolo_visualizzato,Ordinamento,rsPower) VALUES ('".$res[0]."','".$field."','".$ptit."','".$pmax."','".$rsPower."') ";
            $query3 = mysql_query($sql);
            $tmpid=mysql_insert_id();
            
            $sql="INSERT INTO `".$config_table_prefix."rstbl2_tabelle#rstbl2_campi_nm` (id_rstbl2_tabelle,id_rstbl2_campi) VALUES ('$idtab','$tmpid') ";
            $query3 = mysql_query($sql);
          }  
        }
      }
    }
  }
  
  $sql="SHOW TABLE STATUS FROM $dbname";
  $query = mysql_query($sql);
  while($res=mysql_fetch_array($query)) {
    $sql="SELECT id FROM `".$config_table_prefix."rstbl2_tabelle` WHERE tabella='".$res[0]."'";
    $query2 = mysql_query($sql);
    $idtab1 = mysql_fetch_array($query2);
    $tmp=Table2ByTable1("rstbl2_tabelle","rstbl2_tabelle_list",$idtab1['id'],"","","");
    while (list($key, $row) = each($tmp)) {
      $tmptabname1=retRow("rstbl2_tabelle",$row['id_rstbl2_tabelle']);
      $tmptabname1=right($tmptabname1['tabella'],strlen($tmptabname1['tabella_hidden'])-strlen($config_table_prefix));
      $tmptabname=$res[0]."#".$tmptabname1."_nm"; 
      $sql="SHOW TABLE STATUS FROM $dbname WHERE Name='$tmptabname'";
      $query2=mysql_query($sql);
      if(mysql_num_rows($query2)==0) {
        $sql="DELETE FROM `".$config_table_prefix."rstbl2_tabelle#rstbl2_tabelle_list_nm` WHERE (id_rstbl2_tabelle='".$idtab1['id']."' AND id_rstbl2_tabelle_list='".$row['id']."')";
        mysql_query($sql);
      }
    }
    
    if(strpos($res[0],"_nm")!==FALSE && strpos($res[0],"#")!==FALSE){
      $tmparr=explode("#",$res[0]);
      $tab1=$tmparr[0];
      $tab2=substr($tmparr[1], 0,strlen($tmparr[1])-3);
      $tab2=$config_table_prefix.$tab2;
       
      $sql="SHOW TABLE STATUS FROM $dbname WHERE (Name='$tab1' OR Name='$tab2')";
      $query2=mysql_query($sql);
      if(mysql_num_rows($query2)==2 || (mysql_num_rows($query2)==1 && $tab1==$tab2)) {
        $sql="SELECT id,tabella FROM `".$config_table_prefix."rstbl2_tabelle` WHERE (tabella='$tab1' OR tabella='$tab2')";
        $query2 = mysql_query($sql);
        $query2=$objUtility->buildRecordset($query2);
        while (list($key, $row) = each($query2)) {
          if($row['tabella']==$tab1) $idtab1=$row['id'];
          if($row['tabella']==$tab2) $idtab2=$row['id']; 
        }
        
        if($tab1==$tab2) $idtab2=$idtab1; 
        
        $tmp=Table2ByTable1("rstbl2_tabelle","rstbl2_tabelle_list",$idtab1,"","","");
        $trovato=0;
        while (list($key, $row) = each($tmp)) {
          if($row['id_rstbl2_tabelle']==$idtab2) $trovato=1;
            
        }
        
        if($trovato==0) {
          $sql="SELECT Ordinamento FROM `".$config_table_prefix."rstbl2_tabelle_list` ORDER BY id DESC LIMIT 0,1";
          $query3 = mysql_query($sql);
          $pmax=mysql_fetch_array($query3);
          $pmax=$pmax['Ordinamento']+10;
          $sql="INSERT INTO `".$config_table_prefix."rstbl2_tabelle_list` (id_rstbl2_tabelle, Ordinamento) VALUES ('$idtab2','$pmax')";
          $query3 = mysql_query($sql);
          $tmpid=mysql_insert_id();
          
          $sql="INSERT INTO `".$config_table_prefix."rstbl2_tabelle#rstbl2_tabelle_list_nm` (id_rstbl2_tabelle,id_rstbl2_tabelle_list) VALUES ('$idtab1','$tmpid') ";
          $query3 = mysql_query($sql);    
        }
      }
    }
  }
  
  $ret=getSubTablesNav("categorie");
  while (list($key, $row) = each($ret)) {
    $rs=getTable("categorie_roles","","tabella='$row'");
    if(count($rs)==0) {
      $sql="INSERT INTO ".$config_table_prefix."categorie_roles (tabella) VALUES ('$row')";
      mysql_query($sql);
    }  
  }
}

function initGroup($groupid) {
  global $config_table_prefix;
  
  if(isset($_SESSION["user_id"]) && isset($_SESSION["user_login"])) {
    $gua=getTable("gestione_utenti_autonoma","","id_users='".$_SESSION["user_id"]."'");
    $gua=$gua[0]['id'];
    
    $sql="INSERT INTO `".$config_table_prefix."roles_list` (id_roles) VALUES ('".$groupid."')";
    mysql_query($sql);
    $id_roles_list=mysql_insert_id();
    //TabRowToUser("roles_list",$id_roles_list);
    
    $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` (id_gestione_utenti_autonoma,id_roles_list) VALUES ('".$gua."','".$id_roles_list."')";
    mysql_query($sql);  
  }  
}

function initUser($userid) {
  global $config_table_prefix;
  
  $user=retRow("users",$userid);
  
  TabRowToUser("users",$userid);
  
  if($user['codicecliente']=="" || $user['codicecliente']=="0") {
    $sql="UPDATE `".$config_table_prefix."users` SET codicecliente='".$user['id']."' WHERE id='".$userid."'";
    mysql_query($sql);
  }
  
  if($user['login']=="") {
    $sql="UPDATE `".$config_table_prefix."users` SET login='".$user['email']."' WHERE id='".$userid."'";
    mysql_query($sql);
  }
  
  $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma` (id_users,vede_tutti) VALUES ('".$userid."','0')";
  mysql_query($sql);

  if(isset($_SESSION["user_id"]) && isset($_SESSION["user_login"])) {
    $gua=getTable("gestione_utenti_autonoma","","id_users='".$_SESSION["user_id"]."'");
    $gua=$gua[0]['id'];
    
    $sql="INSERT INTO `".$config_table_prefix."users_list` (id_users) VALUES ('".$userid."')";
    mysql_query($sql);
    $id_users_list=mysql_insert_id();
    //TabRowToUser("users_list",$id_users_list);
    
    $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma#users_list_nm` (id_gestione_utenti_autonoma,id_users_list) VALUES ('".$gua."','".$id_users_list."')";
    mysql_query($sql);  
  }
  
  $rol_def=getRolesByName("default");
  CombineUsersRoles($userid,$rol_def['id']);
  if($_SESSION["userris_id"]=="" && $_SESSION["user_id"]=="") {
    $rol_def=getRolesByName("areariservata");
    CombineUsersRoles($userid,$rol_def['id']);  
  }
}

function getRolesByUser($iduser) {
  $rs=Table2ByTable1("users","roles_list",$iduser,"","");
  if(count($rs)>0) {
    $roles=array();
    while (list($key, $row) = each($rs)) { 
      $rol=retRow("roles",$row['id_roles']);
      if($rol) array_push($roles,$rol);    
    }
    return $roles;
  }
  
  return false;
}

function getAllRoles() {
  $rs=getTable("roles","","");
  if(count($rs)>0) {
    $roles=array();
    while (list($key, $row) = each($rs)) { 
      if($row) array_push($roles,$row);    
    }
    return $roles;
  }
  
  return false;
}

function getAllUsers() {
  $rs=getTable("users","","");
  if(count($rs)>0) {
    $roles=array();
    while (list($key, $row) = each($rs)) { 
      if($row) array_push($roles,$row);    
    }
    return $roles;
  }
  
  return false;
}

function getAllUsersByUser($iduser) {
  $gestUtAut=getTable("gestione_utenti_autonoma","","(id_users='".$iduser."')");
  
  if($gestUtAut[0]['vede_tutti']==1) {
    return getAllUsers(); 
  }
  
  $rs=Table2ByTable1("gestione_utenti_autonoma","users_list",$gestUtAut[0]['id'],"","");
  if(count($rs)>0) {
    $roles=array();
    while (list($key, $row) = each($rs)) { 
      $rol=retRow("users",$row['id_users']);
      if($rol) array_push($roles,$rol);    
    }
    return $roles;
  }
  
  return false;
}

function getAllRolesByUser($iduser) {
  $gestUtAut=getTable("gestione_utenti_autonoma","","(id_users='".$iduser."')");
  
  if($gestUtAut[0]['vede_tutti']==1) {
    return getAllRoles(); 
  }
  
  $rs=Table2ByTable1("gestione_utenti_autonoma","roles_list",$gestUtAut[0]['id'],"","");
  if(count($rs)>0) {
    $roles=array();
    while (list($key, $row) = each($rs)) { 
      $rol=retRow("roles",$row['id_roles']);
      if($rol) array_push($roles,$rol);    
    }
    return $roles;
  }
  
  return false;
}

function getRolesByName($roles) {
  $rs=getTable("roles","","nome='".addslashes($roles)."'");
  return $rs[0];
}

function AddRolesToUsers($userid,$rolesid) {
  global $config_table_prefix;
  
  $nm_table="roles_users_nm";
  $nm_sql1="(idroles,idusers)";
  $nm_sql2="('".$rolesid."','".$userid."')";
  $sql="INSERT INTO `".$config_table_prefix.$nm_table."` ".$nm_sql1." VALUES ".$nm_sql2;
  mysql_query($sql);
  
  $sql="INSERT INTO `".$config_table_prefix."users_list` (id_users) VALUES ('".$userid."')";
  mysql_query($sql);
  $id_users_list=mysql_insert_id();
  
  TabRowToUser("users_list",$id_users_list);
  
  $nm_table="roles#users_list_nm";
  $nm_sql1="(id_roles,id_users_list)";
  $nm_sql2="('".$rolesid."','".$id_users_list."')";
  $sql="INSERT INTO `".$config_table_prefix.$nm_table."` ".$nm_sql1." VALUES ".$nm_sql2;
  mysql_query($sql);
  
  $id_gua=getTable("gestione_utenti_autonoma","","id_users='".$userid."'");
  $id_gua=$id_gua[0]['id'];
  
  $notAdd=false;
  $rs=Table2ByTable1("gestione_utenti_autonoma","roles_list",$id_gua,"","");
  while (list($key, $row) = each($rs)) {
    if($row['id_roles']==$rolesid) {
      $notAdd=true;
      break;
    }
  }
  
  if(!$notAdd) {
    $sql="INSERT INTO `".$config_table_prefix."roles_list` (id_roles) VALUES ('".$rolesid."')";
    mysql_query($sql);
    $id_roles_list=mysql_insert_id();
    
    //TabRowToUser("roles_list",$id_roles_list);
    
    $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` (id_gestione_utenti_autonoma,id_roles_list) VALUES ('".$id_gua."','".$id_roles_list."')";
    mysql_query($sql);
  }   
}

function AddUsersToRoles($userid,$rolesid) {
  global $config_table_prefix;
  
  $nm_table="roles_users_nm";
  $nm_sql1="(idroles,idusers)";
  $nm_sql2="('".$rolesid."','".$userid."')";
  $sql="INSERT INTO `".$config_table_prefix.$nm_table."` ".$nm_sql1." VALUES ".$nm_sql2;
  mysql_query($sql);
  
  $sql="INSERT INTO `".$config_table_prefix."roles_list` (id_roles) VALUES ('".$rolesid."')";
  mysql_query($sql);
  $id_roles_list=mysql_insert_id();
  
  TabRowToUser("roles_list",$id_roles_list);
  
  $nm_table="users#roles_list_nm";
  $nm_sql1="(id_users,id_roles_list)";
  $nm_sql2="('".$userid."','".$id_roles_list."')";
  $sql="INSERT INTO `".$config_table_prefix.$nm_table."` ".$nm_sql1." VALUES ".$nm_sql2;
  mysql_query($sql);
  
  $id_gua=getTable("gestione_utenti_autonoma","","id_users='".$userid."'");
  $id_gua=$id_gua[0]['id'];
  
  $notAdd=false;
  $rs=Table2ByTable1("gestione_utenti_autonoma","roles_list",$id_gua,"","");
  while (list($key, $row) = each($rs)) {
    if($row['id_roles']==$rolesid) {
      $notAdd=true;
      break;
    }
  }
  
  if(!$notAdd) {
    $sql="INSERT INTO `".$config_table_prefix."roles_list` (id_roles) VALUES ('".$rolesid."')";
    mysql_query($sql);
    $id_roles_list=mysql_insert_id();
    
    //TabRowToUser("roles_list",$id_roles_list);
    
    $sql="INSERT INTO `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` (id_gestione_utenti_autonoma,id_roles_list) VALUES ('".$id_gua."','".$id_roles_list."')";
    mysql_query($sql);
  }   
}

function CombineUsersRoles($userid,$rolesid) {
  AddUsersToRoles($userid,$rolesid);
  AddRolesToUsers($userid,$rolesid);  
}

function deleteUser($id) {
  global $config_table_prefix;
  
  $sql="DELETE FROM `".$config_table_prefix."users` WHERE id='".$id."'";
  mysql_query($sql);
  
  $roles_list=Table2ByTable1("users","roles_list",$id,"","");
  while (list($key, $row) = each($roles_list)) {
    $sql="DELETE FROM `".$config_table_prefix."roles_list` WHERE id='".$row['id']."'";
    mysql_query($sql); 
    
    DelRowToUser("roles_list",$row['id']); 
  }
  
  $sql="DELETE FROM `".$config_table_prefix."users#roles_list_nm` WHERE id_users='".$id."'";
  mysql_query($sql);
  
  $sql="DELETE FROM `".$config_table_prefix."roles_users_nm` WHERE idusers='".$id."'";
  mysql_query($sql);  
  
  $users_list=getTable("users_list","","id_users='".$id."'");
  while (list($key, $row) = each($users_list)) {
    $sql="DELETE FROM `".$config_table_prefix."roles#users_list_nm` WHERE id_users_list='".$row['id']."'";
    mysql_query($sql);  
  }  
  
  $gua=getTable("gestione_utenti_autonoma","","id_users='".$id."'");
  $gua=$gua[0]['id'];
  
  $sql="DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma` WHERE id_users='".$id."'";
  mysql_query($sql);
  
  $users_list=getTable("users_list","","id_users='".$id."'");
  while (list($key, $row) = each($users_list)) {
    $sql="DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma#users_list_nm` WHERE id_users_list='".$row['id']."'";
    mysql_query($sql);  
    
    DelRowToUser("users_list",$row['id']);
  }
  
  $sql="DELETE FROM `".$config_table_prefix."users_list` WHERE id_users='".$id."'";
  mysql_query($sql);
} 

function DelGestUtAut($id){
  global $config_table_prefix;
  
  $gua=$id;
  $roles_list=Table2ByTable1("gestione_utenti_autonoma","roles_list",$gua,"","");
  while (list($key2, $row2) = each($roles_list)) {
    $sql="DELETE FROM `".$config_table_prefix."roles_list` WHERE id='".$row2['id']."'";
    mysql_query($sql);  
  }
  
  $sql="DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` WHERE id_gestione_utenti_autonoma='".$gua."'";
  mysql_query($sql);
  
  $roles_list=Table2ByTable1("gestione_utenti_autonoma","users_list",$gua,"","");
  while (list($key2, $row2) = each($roles_list)) {
    $sql="DELETE FROM `".$config_table_prefix."users_list` WHERE id='".$row2['id']."'";
    mysql_query($sql);  
  }
  
  $sql="DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma#users_list_nm` WHERE id_gestione_utenti_autonoma='".$gua."'";
  mysql_query($sql);
}

function deleteGroup($id) {
  global $config_table_prefix;
  
  $sql="DELETE FROM `".$config_table_prefix."roles` WHERE id='".$id."'";
  mysql_query($sql);
  
  $users_list=Table2ByTable1("roles","users_list",$id,"","");
  while (list($key, $row) = each($users_list)) {
    $sql="DELETE FROM `".$config_table_prefix."users_list` WHERE id='".$row['id']."'";
    mysql_query($sql);  
    
    DelRowToUser("users_list",$row['id']);
  } 
  
  
  $sql="DELETE FROM `".$config_table_prefix."roles#users_list_nm` WHERE id_roles='".$id."'";
  mysql_query($sql);
  
  $sql="DELETE FROM `".$config_table_prefix."roles_users_nm` WHERE idroles='".$id."'";
  mysql_query($sql);
  
  $sql="DELETE FROM `".$config_table_prefix."roles_menu_nm` WHERE idroles='".$id."'";
  mysql_query($sql);
  
  $gua=array();
  $roles_list=getTable("roles_list","","id_roles='".$id."'");
  while (list($key, $row) = each($roles_list)) {
    $sql="DELETE FROM `".$config_table_prefix."users#roles_list_nm` WHERE id_roles_list='".$row['id']."'";
    mysql_query($sql);
    
    $sql="DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` WHERE id_roles_list='".$row['id']."'";
    mysql_query($sql);
    
    $sql="DELETE FROM `".$config_table_prefix."roles_list` WHERE id='".$row['id']."'";
    mysql_query($sql);
    
    DelRowToUser("roles_list",$row['id']);
  }
}

function isUserSystem($iduser="") {
  global $config_table_prefix;
  
  if($iduser=="") $iduser=$_SESSION["user_id"];
  if($iduser=="") return 0;
  
  $roles=Table1ByTable2_pointed("roles","users_list","users",$iduser,"","(".$config_table_prefix."roles.issystem='1')");
  if(count($roles)>0) $issystem=1;else $issystem=0;
  
  return $issystem;
}

function is_date($date) {
  $date=trim($date);
  $date=left($date,10);
  
  $date=explode("-", $date);
  if(count($date)!=3) return false;
  
  if($date[0]=="0000" && $date[1]=="00" && $date[2]=="00") return true;
  return checkdate(parseToFloat($date[1]), parseToFloat($date[2]), parseToFloat($date[0]));   
}

function printChat($url_contattaci=""){
  $objUtility=new Utility();
  
  $chat_conf=getTable("chat_conf","","attivo='1'");
  
  $liveOp=getTable("chat","","(isadmin='1' AND (TIME_TO_SEC(TIMEDIFF(now(),ping))<=10))");
  if(count($liveOp)>0) {
    $l_img=retFile($chat_conf[0]['live_support_file']);
    if(!$l_img) {
      $l_img=$objUtility->getPathBackofficeResources()."live-support.png";
      list($lw, $lh)=getimagesize($objUtility->getPathBackofficeResourcesAbsolute()."live-support.png");
    }else{
      list($lw, $lh)=getimagesize(retFileAbsolute($chat_conf[0]['live_support_file']));
    }
    $l_txt="Chatta col nostro servizio clienti.";
    $offline=0;
  }else{
    $l_img=retFile($chat_conf[0]['live_support2_file']);
    if(!$l_img) {
      $l_img=$objUtility->getPathBackofficeResources()."live-support-offline.png";
      list($lw, $lh)=getimagesize($objUtility->getPathBackofficeResourcesAbsolute()."live-support-offline.png");
    }else{
      list($lw, $lh)=getimagesize(retFileAbsolute($chat_conf[0]['live_support2_file']));  
    }
    $l_txt="Nessun operatore on-line.";
    $offline=1;  
  }
  ?><div class="ez-box"><?php if($offline==1) echo "<a href='".$url_contattaci."'>"; ?><img class="rsOpenChat" offline="<?php echo $offline; ?>" src="<?php echo $l_img; ?>" width="<?php echo $lw; ?>" height="<?php echo $lh; ?>" title="<?php echo ln($l_txt); ?>" /><?php if($offline) echo "</a>"; ?></div><?php
}

function retArticoloDett($idcat,$defImmID="",$imgW="",$imgH="") {
  if($defImmID=="") $defImmID=0;
  if($imgW=="") $imgW=0;
  if($imgH=="") $imgH=82;
  
  $articolo=retRow("categorie",$idcat);
  $layout=retCatLayout($idcat);
  $url=$layout['file']."?menid=".$articolo['id'];
  
  if($articolo['url']!="") $url=$articolo['url']; 
  
  $imm=retFile($defImmID,$imgW,$imgH);
  if(!$imm) {
    $imm=retFile($articolo['immagine_file'],$imgW,$imgH);
    
    if(!$imm) {
      $gallery=Table2ByTable1("categorie","fotogallery",$idcat,"attivo='1'","Ordinamento ASC");
      $imm=retFile($gallery[0]['immagine_file'],$imgW,$imgH); 
    }   
  }
  
  $ret=array();
  $ret['articolo']=$articolo;
  $ret['url']=$url;
  $ret['img']=$imm;
  
  return $ret;
} 

function checkRightsForTable($table,$tableId="",$tableParent=""){
  global $config_table_prefix;
  $objUtility = new Utility;
  $objUsers = new Users;
  $isAuthorized=false;
  $iduser=$_SESSION["user_id"];
  
  $rstbl2_tabelle=getTable("rstbl2_tabelle","","(tabella='".$config_table_prefix.$table."' AND public='1')");
  if(count($rstbl2_tabelle)>0){
    return true;
  }
  
  $isSystem=isUserSystem();
  
  if($isSystem) return true; 
  
  if($tableParent!="") {
    $strSql = "SELECT * FROM ".$config_table_prefix."roles_users_nm ru LEFT JOIN ".$config_table_prefix."roles r ON ru.idroles=r.id LEFT JOIN ".$config_table_prefix."roles_menu_nm rm ON rm.idroles=r.id LEFT JOIN ".$config_table_prefix."menu m ON rm.idmenu=m.id WHERE ru.idusers=" . $iduser . " AND m.path='navigazione/in_place.php'";
    $query = mysql_query ($strSql);
  	$rs = $objUtility->buildRecordset($query);
  	
    if(count($rs)) {
  		return true;
  	}else{
      exit;  
    }  
  }
  
  if($table=="categorie") {
    $cat=retRow("categorie",$tableId);
    $struttura=getStrutturaByNodo($tableId);
    $strutture=getTable("strutture","","nome='".$struttura['nome']."'");
    $strutture=$strutture[0];
    
    if($strutture['riservato']==1 && $isSystem==0) exit;
    if($strutture['id_users']>0 && $iduser!=$strutture['id_users']) exit;
    
    return true;
  }
  
  $strSql = "SELECT * FROM ".$config_table_prefix."roles_users_nm ru LEFT JOIN ".$config_table_prefix."roles r ON ru.idroles=r.id LEFT JOIN ".$config_table_prefix."roles_menu_nm rm ON rm.idroles=r.id LEFT JOIN ".$config_table_prefix."menu m ON rm.idmenu=m.id WHERE ru.idusers=" . $iduser . " AND m.tabella='" . $table . "'";
	//echo $strSql;
  $query = mysql_query ($strSql);
	$rs = $objUtility->buildRecordset($query);
	
  if(count($rs)) {
		return true;
	}else{
    exit;  
  }  
}

function repBOLD($str){
  $str=str_replace("<strong>", "<b>", $str);
  $str=str_replace("</strong>", "</b>", $str);
  
  return $str;
}

/*
    Converts an array of RGB data to CMYK data.
    
    Parameters:
        $rgb    -   an array of color information where
                    $rgb[0] == red value
                    $rgb[1] == green value
                    $rgb[2] == blue value
    Returns:
        array containing cmyk color information:
            $array[0] == cyan value
            $array[1] == magenta value
            $array[2] == yellow value
            $array[3] == black value
*/
function RGB_to_CMYK($rgb) {
    $cyan = 1 - ($rgb[0] / 255);
    $magenta = 1 - ($rgb[1] / 255);
    $yellow = 1 - ($rgb[2] / 255);

    $min = min($cyan, $magenta, $yellow);
    
    if ($min == 1)
        return array(0,0,0,1);

    $K = $min;
    $black = 1 - $K;

    return array
    (
        ($cyan - $K) / $black,
        ($magenta- $K) / $black,
        ($yellow - $K) / $black,
        $K
    );
}

function truncateNumber ($num, $digits = 0) {

  //provide the real number, and the number of
  //digits right of the decimal you want to keep.
  
  $shift = pow(10, $digits);
  return ((floor($num * $shift)) / $shift);
}

function mergeJs(){
  $objUtility = new Utility;
  
  ob_start();

  /* your js files */
  include($objUtility->getPathBackofficeResourcesAbsolute()."lytebox.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery-1.11.2.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery-migrate-1.1.1.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery-ui-1.8.17.full.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery.livequery.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery.form.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."urlEncode.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."swfobject.full.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery_timer.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery.elevatezoom.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery.uploadify.v2.1.4.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."uploadifive/jquery.uploadifive.min.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."php.min.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."changeContent.jquey.js");                                                                                                      
  include($objUtility->getPathBackofficeResourcesAbsolute()."rsFunctions.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."rsPaginazione.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."rsTable2.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."rsStrutture.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."rsCarrello.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."bootbox.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."bootstrap-notify.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."bootstrap/bootstrap.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."js.cookie.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."modernizr-2.8.3.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."respond.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jasny-bootstrap.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."jquery.flexslider.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."bootstrap-select.js");
  include($objUtility->getPathBackofficeResourcesAbsolute()."loadingoverlay.js");  
  
  $jscode=ob_get_contents(); 
  ob_end_clean();
  
  return $jscode; 
}

function array2csv(array &$array,$file) {
	if (count($array) == 0) {
		return null;
	}
	ob_start();
	$df = fopen($file, 'w');
	fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));
	fputcsv($df, array_keys(reset($array)), $delimiter = ';', $enclosure = '"');
	foreach ($array as $row) {
		fputcsv($df, $row, $delimiter = ';', $enclosure = '"');
	}
	fclose($df);
	return ob_get_clean();
}

function parse_csv($file, $separator=';', $meta=array()) {
	$count = 0;
	$data = array();
	if (($fp = fopen($file, 'r')) !== false ) {
		while (($row = fgetcsv($fp, 1024, $separator)) !== false) {
			if ($count === 0 && empty($meta) && @strpos($row, '#') !== false) {
				$meta = $row;
				$meta = array_map(create_function('$s', 'return str_replace("#", "", $s);'), $meta);
			}
			$data[] = empty($meta) ? $row : array_combine($meta, $row);
			$count++;
		}
		fclose($fp);
	}
	return $data;
}


function replaceQuotesForCSV($str){
	$str=str_replace('"', '""', $str);
	
	return $str;
}


function cmp($a,$b){
	return strcmp($a["ragione_sociale"], $b["ragione_sociale"]);
}

function updateBusinessUrl(){
	global $config_table_prefix;

	$bs_business=getTable("bs_business","","url=''");
	while (list($key, $row) = each($bs_business)) {
		$url=onlyreadables($row["brand_aziendale"]).".html";

		$ifexist=getTable("bs_business","","url='".addslashes($url)."'");
		if(count($ifexist)==0) {
			$sql="UPDATE `".$config_table_prefix."bs_business` SET url='".addslashes($url)."' WHERE id=".$row["id"];
			mysql_query($sql);
		}else{
			$url=onlyreadables($row["brand_aziendale"])."-".$row["id"].".html";
			$sql="UPDATE `".$config_table_prefix."bs_business` SET url='".addslashes($url)."' WHERE id=".$row["id"];
			mysql_query($sql);
		}
	}
}

function cruiseCompressJs($file,$js_code,$cache=true){
  $objConfig = new ConfigTool();
  $objUtility = new Utility;
  
  if($cache==1) $cache=true;
  
  $funique=$objUtility->getFilenameUnique().".js";
  
  if(file_exists($file) && $cache){
    
    $packed=file_get_contents($file);
    return $packed;  
  }
  
  $js_code=str_replace("<script>", "", $js_code);
  $js_code=str_replace("<SCRIPT>", "", $js_code);
  $js_code=str_replace("</SCRIPT>", "", $js_code);
  $js_code=str_replace("</script>", "", $js_code);
  
  if($objConfig->get("ccompiler_override")=="1" || !file_exists($objConfig->get("ccompiler"))){
    return compressJs($js_code); 
  }
  
  if($cache) {
    file_put_contents($funique, $js_code);
    exec("java -jar ".$objConfig->get("ccompiler")." --js ".$funique,$packed);
    $packed=implode("\n",$packed);
    if($cache) file_put_contents($file, $packed);
     
    @unlink($funique);
    /*
    if(!$cache) {
      @unlink($file);
      @unlink($funique);
    }
    */
    
    return $packed;
  }else{
    return $js_code;
  }  
}

function compressJs($buffer) {
  $packed = JSMin::minify($buffer);
  return $packed; 
}

function compressCss($css) {
  $css = preg_replace('!//[^\n\r]+!', '', $css);//comments<br />
  $css = preg_replace('/[\r\n\t\s]+/s', ' ', $css);//new lines, multiple spaces/tabs/newlines<br />
  $css = preg_replace('#/\*.*?\*/#', '', $css);//comments<br />
  $css = preg_replace('/[\s]*([\{\},;:])[\s]*/', '\1', $css);//spaces before and after marks<br />
  $css = preg_replace('/^\s+/', '', $css);//spaces on the begining<br />
  
  return $css;
}

function compressHtml($html){
  preg_match_all('!(&lt;(?:code|pre).*&gt;[^&lt;]+&lt;/(?:code|pre)&gt;)!',$html,$pre);//exclude pre or code tags<br />
  $html = preg_replace('!&lt;(?:code|pre).*&gt;[^&lt;]+&lt;/(?:code|pre)&gt;!', '#pre#', $html);//removing all pre or code tags<br />
  $html = preg_replace('#&lt;!--[^\[].+--&gt;#', '', $html);//removing HTML comments<br />
  $html = preg_replace('/[\r\n\t]+/', ' ', $html);//remove new lines, spaces, tabs<br />
  $html = preg_replace('/&gt;[\s]+&lt;/', '&gt;&lt;', $html);//remove new lines, spaces, tabs<br />
  $html = preg_replace('/[\s]+/', ' ', $html);//remove new lines, spaces, tabs<br />
  if(!empty($pre[0])) foreach($pre[0] as $tag);
    
  $html = preg_replace('!#pre#!', $tag, $html,1);//putting back pre|code tags<br />
  return $html;
}

function cryptOptions($arr){
  $objConfig = new ConfigTool();
  $ret="";
  
  foreach($arr as $key=>$value){
    $ret.= "'".$key."':'".$value."',"; 
  }  
  
  $token=rs_encrypt($ret,$arr['table']);
  $ret.= "'token':'".$token."'";
  
  return $ret;
}

function decryptOptions($arr){
  $objConfig = new ConfigTool();
  $ret=true;

  if(empty($arr['token']) || empty($arr['table'])) return false;
  
  $decData=rs_decrypt($arr['token'],$arr['table']);
  if($decData==false) return false; 
  
  $opt="";
  foreach($arr as $key=>$value){
    if($key!="token") $opt.= "'".$key."':'".$value."',"; 
  }

  if($opt!=$decData) $ret=false;
  
  return $ret;
} 

function microtime_float(&$dbgt)
{
  list($usec, $sec) = explode(" ", microtime());
  $curt=((float)$usec + (float)$sec);
  $ret=$curt-$dbgt;
  $dbgt=$curt;
  if($dbgt==$ret) return 0; else return $ret;
}

// Function to return the JavaScript representation of a TransactionData object.
function getTransactionJs(&$trans) {
  return <<<HTML
ga('ecommerce:addTransaction', {
  'id': '{$trans['id']}',
  'affiliation': '{$trans['affiliation']}',
  'revenue': '{$trans['revenue']}',
  'shipping': '{$trans['shipping']}',
  'tax': '{$trans['tax']}',
  'currency': 'EUR'
});
HTML;
}

// Function to return the JavaScript representation of an ItemData object.
function getItemJs(&$transId, &$item) {
  return <<<HTML
ga('ecommerce:addItem', {
  'id': '$transId',
  'name': '{$item['name']}',
  'sku': '{$item['sku']}',
  'category': '{$item['category']}',
  'price': '{$item['price']}',
  'quantity': '{$item['quantity']}',
  'currency': 'EUR'
});
HTML;
}

function ret_union(&$r1,&$r2,&$ret){
  $xarr=array();
  $yarr=array();
  
  array_push($xarr,$r1['x1'],$r1['x2'],$r2['x1'],$r2['x2']);
  array_push($yarr,$r1['y1'],$r1['y2'],$r2['y1'],$r2['y2']);
  
  $ret['x1']=min($xarr);
  $ret['y1']=min($yarr);
  
  $ret['x2']=max($xarr);
  $ret['y2']=max($yarr);
  
  $ret['rot']=0;
  
  $ret['arrID']=array();
  $ret['arrID']=array_merge($r1['arrID'],$r2['arrID']);
  $ret['id']=-1;
  
  DataRet($ret);
}

function ret_rotate($ret){
  $tempW=$ret['w'];
  $ret['w']=$ret['h']; 
  $ret['h']=$tempW;
  
  coordbydim($ret);
  $ret['rot']=1;
  
  return $ret; 
}

function gen_nos(&$set, &$results) {
  for($i=0; $i<count($set); $i++) {
    $results[] = $set[$i];
    $tempset = $set;
    array_splice($tempset, $i, 1);
    $tempresults = array();
    gen_nos($tempset, $tempresults);
    foreach($tempresults as $res) {
      $results[] = $set[$i] . $res;
    }
  }
}

function ret_gen_nos(&$set, &$results) {
  for($i=0; $i<count($set); $i++) {
    $set[$i]['s']=$set[$i]['w']*$set[$i]['h'];
    $set[$i]['rot']=0;
    coordbydim($set[$i]);
    
    $results[] = $set[$i];
    $tempset = $set;
    array_splice($tempset, $i, 1);
    $tempresults = array();
    ret_gen_nos($tempset, $tempresults);
    foreach($tempresults as $res) {
    
     $tmpres['arrID']=array_merge($set[$i]['arrID'],$res['arrID']);
     $tmpres[id]=-1;
     $results[]=$tmpres;
    }
  }
}

function coordbydim(&$ret){
  $ret['x1']=0;
  $ret['y1']=0;
  $ret['x2']=$ret['w'];
  $ret['y2']=$ret['h']; 
}

function DataRet(&$ret){
  if($ret['x2']!==NULL && $ret['x1']!==NULL) $ret['w']=$ret['x2']-$ret['x1'];
  if($ret['y2']!==NULL && $ret['y1']!==NULL) $ret['h']=$ret['y2']-$ret['y1'];
  if($ret['w']!==NULL && $ret['h']!==NULL) $ret['s']=$ret['w']*$ret['h'];
  if(!is_array($ret['arrID']) && $ret['id']!=-1) {
    $ret['arrID']=array();
    array_push($ret['arrID'],$ret['id']);
  } 
  
  GeometryRet($ret);
}

function GeometryRet(&$ret){
  $w=$ret['w'];
  $h=$ret['h'];
  
  if($h<$w) {
    $wTemp=$w;
    $w=$h;
    $h=$wTemp;
  }  
  
  if($h>0) $ret['gr']=$w/$h;
}

function canInsertRet(&$retSource, &$retDest){  
  if($retDest['gr']>=$retSource['gr'] && $retDest['s']>=$retSource['s']) {
    return true;  
  }else{
    return false;
  }    
}

function retPezzoByCombi(&$combi,&$lastra,&$pezzo){
  
}

function tetris(&$pezzi,$tipo,$spessore,$colore){
  $scartoW=1;
  $scartoH=15;
     
  $combi=array();
  ret_gen_nos($pezzi,$combi);
  
  $idTipo=getTable("lastre_tipi","","(nome='".$tipo."' AND attivo='1')");
  $idColore=getTable("lastre_colori","","(nome='".$colore."' AND attivo='1')");
  
  $lastre=getTable("lastre","s ASC","(spessore=".$spessore." AND id_lastre_tipi='".$idTipo[0]['id']."' AND id_lastre_colori='".$idColore[0]['id']."' AND attivo='1')");
  
  $scarto=-1;
  while (list($key, $row) = each($lastre)) {
    $lastra['w']=$row['w'];
    $lastra['h']=$row['h'];
    $lastra['s']=$lastra['w']*$lastra['h'];
    $lastra['rot']=0;
    coordbydim($lastra);
    
    reset($combi);
    while (list($key1, $row1) = each($combi)) {
      retPezzoByCombi($combi,$lastra,$pezzo);
      
      if(canInsertRet($pezzo,$lastra)){
        $scartoTemp=$lastra['s']-$pezzo['s'];
        
        if($scartoTemp<$scarto || $scarto==-1) {
          $scarto=$scartoTemp;  
        }  
      }    
    }   
  }    
}

function getTema() {
    global $config_table_prefix;

    $tema = $_SERVER['HTTP_HOST'];

    // Development use only
    //if($_SESSION["user_login"]=="heroesprint" || ($_SESSION["user_login"]=="3" && $tema=="localhost")) $tema="www.heroesprint.eu";

    $tema = strtolower($tema);
    if(substr($tema, 0, 4) == "www.") {
        $tema = substr($tema, 4);
    }
    $tema = onlyreadables($tema);

    // Controlla se esiste un alias nella tabella `themes_alias` per il tema.
    $sql = "SELECT alias FROM `" . $config_table_prefix . "themes_alias` WHERE tema='" . $tema . "' AND attivo=1";
    $query = mysql_query($sql);

    if ($query && mysql_num_rows($query) > 0) {
        $row = mysql_fetch_assoc($query);
        return $row['alias'];  // Restituisce l'alias se esiste.
    }

    return $tema;
}


function getTemaMD5($tema){
    $md5 =  md5($tema . "6683");
    return $md5; 
}

function retPrezzoScontato($articolo){
    $prezzo=$articolo["Prezzo_cry"];
    
    if(isset($_SESSION["userris_id"])){
        $prezzo=parseToFloat($articolo["Prezzo_cry"]-(($articolo["Prezzo_cry"]*$articolo["sconto_reg"])/100));    
    }else{
        $prezzo=parseToFloat($articolo["Prezzo_cry"]-(($articolo["Prezzo_cry"]*$articolo["sconto"])/100));    
    }
    
    return $prezzo;
}

function TabRowsToUser($table,$id_user){
    global $config_table_prefix;
    
    $user=retRow("users",$id_user); 
    $username=$user['login'];
    $tema=getTema();
    $rs=getTable($table,"","","",false);
    
    while (list($key, $row) = each($rs)) {
        $sql="INSERT INTO `".$config_table_prefix."storico_users` (`id_users`,`domain`,`username`,`table`,`row`,`deleted`) VALUES ('".$id_user."','".$tema."','".$username."','".$table."','".$row['id']."','0')";
    	mysql_query($sql);  
    }
}

function TabRowToUser($table,$id_row,$id_user=""){
    global $config_table_prefix;
    $objUsers = new Users;
    
    if($id_user=="") {
        $objUsers->getCurrentUser($id_user, $username, false, 1);
    }
    
    $tema=getTema();
    
    $sql="INSERT INTO `".$config_table_prefix."storico_users` (`id_users`,`domain`,`username`,`table`,`row`,`deleted`) VALUES ('".$id_user."','".$tema."','".$username."','".$table."','".$id_row."','0')";
  	mysql_query($sql);  
}

function DelRowToUser($table,$id_row){
    global $config_table_prefix;
    
    $sql="UPDATE ".$config_table_prefix."storico_users SET `deleted`= 1 , `ultimo_aggiornamento`= NOW() WHERE (`table`='".$table."' AND `row`='".$id_row."')";
  	mysql_query($sql);  
}

function retOrdineDett($cond="data >= (CURDATE()-INTERVAL 7 DAY)") {
    global $config_table_prefix;
    
    $objConfig = new ConfigTool();
    $objDb = new Db;
    $objUtility = new Utility;
    $objHtml = new Html;
  
    $isArt=false;
    $c=-1;
    
    $documents=getTable("ecommerce_ordini","id DESC", "((" .$cond . ") AND (id_ecommerce_stati=9 OR id_ecommerce_stati=5 OR id_ecommerce_stati=4 OR id_ecommerce_stati=7 OR id_ecommerce_stati=13))");
    
    $arr_fatt=array();
    while (list($key, $row) = each($documents)) {
      $fatt=array(); 
      $data = $row["riepilogoITA_editor"];
      
      $spesedisped="";
      $spedlabel="";
      
      $dom = new domDocument;
      
      @$dom->loadHTML($data);
      $dom->preserveWhiteSpace = false;
      $tables = $dom->getElementsByTagName('table');
      
      foreach ($tables as $table) {
        $innerTables=$table->getElementsByTagName('table');
        if($innerTables->length==0){
          $rows = $table->getElementsByTagName('tr');
          foreach ($rows as $row2) {
            $cols = $row2->getElementsByTagName('td');
            if($cols->length==2) {
              $col_name=$cols->item(0)->textContent;
              $col_name=onlyreadables($col_name);
              $col_val=trim($cols->item(1)->textContent);
              if($col_val=="") $col_val="-";
              
              $col_val=utf8_decode(htmlspecialchars($col_val, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
              
              if($col_name=="cod-articolo") {
                $c++;
                $isArt=true;
              }
              
              if($isArt){
                if($col_name=="quantit-") {
                  $col_val=str_replace("N. ", "", $col_val);
                  if(((float)$col_val)<=0 || $col_val=="") $col_val=1;
                  $fatt["articoli"][$c][$col_name]=parseToFloat($col_val);
                }elseif($col_name=="totale-articolo") { 
                  $col_val=str_replace("? ","",$col_val);
                  $fatt["articoli"][$c]["DescrTot"]=left($fatt["articoli"][$c]["DescrTot"],strlen($fatt["articoli"][$c]["DescrTot"])-2);
                  $isArt=false;
                  $fatt["articoli"][$c][$col_name]=parseToFloat($col_val);
                }else{
                  $fatt["articoli"][$c][$col_name]=$col_val;
                }
                
                if($col_name!="cod-articolo" && $col_name!="quantit-" && $col_name!="totale-articolo" && $col_name!="n-copie"){
                  if($col_name!="descrizione") $descr=str_replace("-", " ", utf8_decode(htmlspecialchars($col_name, ENT_XML1 | ENT_QUOTES, 'UTF-8'))).": "; else $descr="";
                  $fatt["articoli"][$c]["DescrTot"].=$descr.$col_val.", ";
                }
                
                if(!isset($fatt["articoli"][$c]["quantit-"]) && isset($fatt["articoli"][$c]["n-copie"])) $fatt["articoli"][$c]["quantit-"]=(float)$fatt["articoli"][$c]["n-copie"];
              }else{
                if(left($col_name,strlen("spese-di-spedizione"))=="spese-di-spedizione") { 
                  $spedlabel=utf8_decode(htmlspecialchars($cols->item(0)->textContent, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
                  $spesedisped=parseToFloat(str_replace("? ","",$col_val),2);
                } 
                
                if($fatt[$col_name]!="") $col_name=$col_name."-2";
                $fatt[$col_name]= utf8_encode($col_val);  
              } 
            }
          }
        }
      }
      //print_r($fatt);
      if($fatt["codice-destinatario"]=="" && $fatt["pec"]!="") {
        $fatt["codice-destinatario"]=$fatt["pec"];
      }
      
      $fatt['data']=$row["data"];
      $fatt['id']=$row["id"];
      $fatt['totale_cry']=$row["totale_cry"];
      $fatt['id_ecommerce_stati']=$row["id_ecommerce_stati"];
      $fatt['spese_di_sped']=$spesedisped;
      $fatt['spese_di_sped_label']=$spedlabel;
      
      array_push($arr_fatt, $fatt);
    }
    
    return $arr_fatt;
}

?>
