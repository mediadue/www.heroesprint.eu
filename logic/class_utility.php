<?php
#
# Inclusion for Utility::dictionary() method
#
include_once('dictionary.php');

Class Utility {

var $numbers = "0123456789";
var $letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
var $objConfig;
var $objObjects;
var $tema = "";

function Utility(){
  $this->objConfig=new ConfigTool();
  $this->objObjects= new Objects();
  
  $this->tema = $this->retTema();
}
// ******************************************************************************************
function getPathBackofficeAdmin() 
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . $objConfig->get("path-admin") . "/");
}
// ******************************************************************************************
function getPathBackofficeAdminAbsolute() 
{
	$objConfig=$this->objConfig;
	return trim(SERVER_DOCROOT . $objConfig->get("path-admin") . "/");
}
// ******************************************************************************************
function getPathResourcesStatic()
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root").$objConfig->get("path-resources-static") . "/");
}
// ******************************************************************************************
function getPathResourcesStaticAbsolute()
{
	$objConfig=$this->objConfig;
	return trim(SERVER_DOCROOT . $objConfig->get("path-resources-static") . "/");
}
// ******************************************************************************************
function getPathResourcesDynamic()
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . $this->tema . $objConfig->get("path-resources-upload") . "/");
}
// ******************************************************************************************
function getPathUserFiles()
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . $this->tema . $objConfig->get("path-userfiles") . "/");
}
// ******************************************************************************************
function getPathUserFilesAbsolute()
{
	$objConfig=$this->objConfig;
	return trim(SERVER_DOCROOT . $this->tema . $objConfig->get("path-userfiles") . "/");
}
// ******************************************************************************************
function getPathResourcesDynamicAbsolute()
{
	$objConfig=$this->objConfig; 
    return trim(SERVER_DOCROOT . $this->tema . $objConfig->get("path-resources-upload") . "/");
}
// ******************************************************************************************
function getPathResourcesPrivateAbsolute()
{
	$objConfig=$this->objConfig;
	return trim(SERVER_DOCROOT . $this->tema . $objConfig->get("path-resources-private") . "/");
} 
// ******************************************************************************************
function getPathResourcesPrivate()
{	
  $objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . $this->tema . $objConfig->get("path-resources-private") . "/");
}
// ******************************************************************************************
function getPathBackoffice()
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . $objConfig->get("path-admin") . "/");
}
// ******************************************************************************************
function getPathRoot()
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . "");
}
// ******************************************************************************************
function getPathImages()
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . $this->tema . $objConfig->get("path-resources-images") . "/");
}
// ******************************************************************************************
function getPathImagesAbsolute()
{
	$objConfig=$this->objConfig;
	return trim(SERVER_DOCROOT . $this->tema . $objConfig->get("path-resources-images") . "/");
}
// ******************************************************************************************
function getPathFonts()
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . $this->tema . $objConfig->get("path-resources-fonts") . "/");
}
// ******************************************************************************************
function getPathFontsAbsolute()
{
	$objConfig=$this->objConfig;
	return trim(SERVER_DOCROOT . $this->tema . $objConfig->get("path-resources-fonts") . "/");
}
// ******************************************************************************************
function getPathCss()
{
	$objConfig=$this->objConfig;
	return trim($objConfig->get("path-virtual-root") . $this->tema . $objConfig->get("path-resources-css") . "/");
}
// ******************************************************************************************
function getPathCssAbsolute()
{
	$objConfig=$this->objConfig;
	return trim(SERVER_DOCROOT . $this->tema . $objConfig->get("path-resources-css") . "/");
}
// ******************************************************************************************

function getPathBackofficeResources() 
{
	$objConfig=$this->objConfig;
	return trim($this->getPathBackoffice() . $objConfig->get("path-admin-resources-static") . "/");
}
// ******************************************************************************************
function getPathBackofficeResourcesAbsolute() 
{
	$objConfig=$this->objConfig;
	return trim(SERVER_DOCROOT . $objConfig->get("path-admin") . "/" . $objConfig->get("path-admin-resources-static") . "/");
}
// ******************************************************************************************
function getAction (&$strAct, &$intId) {
	$strAct = $_POST["ACT"];
	If (!$strAct) {
		while (list ($key, $val) = each ($_POST)) {
			If (strtoupper(substr($key, 0, 4)) == "ACT_") {
				list (, $strAct, $intId) = explode('_', $key);
			}
		}
	}
	$strAct = strtoupper($strAct);
	reset($_POST);
}

// ******************************************************************************************
function sessionVarUpdate($strName, $strValue) {
	$trans = array ("|" => "", "§" => "");
	$strName = strtr($strName, $trans);
	$strValue = strtr($strValue, $trans);
	$strSessionVar = $_SESSION["sessionvar"];
	$isUpdate = false;
	$strSessionVarNew = "";
	if ($strSessionVar) {
		$arrVariable = explode("|", $strSessionVar);
		if (is_array($arrVariable)) {
			for ($i=0; $i<count($arrVariable)-1; $i++) {
				list($key, $val) = explode("§", $arrVariable[$i]);
				if ($key == $strName) {
					//aggiorna il valore della variabile
					$isUpdate = true;
					$val = $strValue;
				}
				$strSessionVarNew .= $key . "§" . $val . "|";
			}
		}
	}
	If (!$isUpdate) {
		//inserisce la variabile
		$strSessionVarNew  .= $strName . "§" . $strValue . "|";
	}
	$_SESSION["sessionvar"] = $strSessionVarNew;
}

// ******************************************************************************************
function sessionVarDelete($strName) {
	$strSessionVar = $_SESSION["sessionvar"];
	$strSessionVarNew = "";
	if ($strSessionVar) {
		$arrVariable = explode("|", $strSessionVar);
		if (is_array($arrVariable)) {
			for ($i=0; $i<count($arrVariable)-1; $i++) {
				list($key, $val) = explode("§", $arrVariable[$i]);
				if ($key != $strName) {
					$strSessionVarNew  .= $key . "§" . $val . "|";
				}
			}
		}
	}
	$_SESSION["sessionvar"] = $strSessionVarNew;
}

// ******************************************************************************************
function sessionVarRead($strName) {
	$strSessionVar = $_SESSION["sessionvar"];
	if ($strSessionVar) {
		$arrVariable = explode("|", $strSessionVar);
		if (is_array($arrVariable)) {
			for ($i=0; $i<count($arrVariable)-1; $i++) {
				list($key, $val) = explode("§", $arrVariable[$i]);
				if ($key == $strName) {
					return $val;
				}
			}
		}
	}
}

// ******************************************************************************************
function getLn()
{
	$ln = strtolower($_GET["ln"]);
	$ln = ($_GET["ln"] || ($_GET["ln"] === "")) ? $_GET["ln"] : $this->sessionVarRead("ln");
	switch ($ln) {
		case "en":
		case "fr":
		case "es":
			break;
		default:
			$ln="";
			break;
	}
	$this->SessionVarUpdate("ln", $ln);
	return $ln;
}

// ******************************************************************************************
function getLnOff($ln)
{
	switch ($ln) 
	{
		case "en":
			$lnoff="2";
			break;
		case "fr":
			$lnoff="3";
			break;
		case "es":
			$lnoff="4";
			break;
		default:
			$lnoff="1";
			break;
	}
	return $lnoff;
}

// ******************************************************************************************
function showObject($conn, $idOggetto)
{
	$objObjects=$this->objObjects;
	$rs = $objObjects->getDetails($conn, $idOggetto);
	if (count($rs) > 0) 
	{
		list($key, $row) = each($rs);
		$dir = $this->getPathResourcesDynamic();
		switch (strtolower($row["ext"])) {
			case "jpg" OR "png" OR "gif":
				?>
				<img src="<?php echo $dir . $row["path"] ?>" alt=""/>
				<?php
				break;
			default:
				?>
				<a href="<?php echo $dir . $row["path"] ?>" target="_blank"><?php echo $row["originalname"] ?></a><br/>
				<?php
				break;
		}
	}
}

// ******************************************************************************************
function showObjectPath($conn, $idOggetto)
{
	$objObjects=$this->objObjects;
	$rs = $objObjects->getDetails($conn, $idOggetto);
	if (count($rs) > 0) 
	{
		list($key, $row) = each($rs);
		$dir = $this->getPathResourcesDynamic();
		if (!$row["isprivate"])
			return $dir . $row["path"];
	}
}

// ******************************************************************************************
function showObjectPathAbsolute($conn, $idOggetto)
{
	$objObjects=$this->objObjects;
	$rs = $objObjects->getDetails($conn, $idOggetto);
	if (count($rs) > 0) 
	{
		list($key, $row) = each($rs);
		$dir = $this->getPathResourcesDynamicAbsolute();
		if ($row["isprivate"])
			$dir = $objUtility->getPathResourcesPrivateAbsolute();
		return $dir . $row["path"];
	}
}

// ******************************************************************************************
function showObjectFile($conn, $idOggetto)
{
	$this->config_table_prefix;
	$rs = $objObjects->getDetails($conn, $idOggetto);
	if (count($rs) > 0) 
	{
		list($key, $row) = each($rs);
		return $row["path"];
	}
}

// ******************************************************************************************
function getFileSizeKb($strFilePath) 
{
	return @round(filesize($strFilePath) / 1024);
}

// ******************************************************************************************
function errorMsgFormat($errNumber, $errDescription, $sql) 
{
	if ($errNumber || $errDescription)
		return $errNumber."|".$sql."|".$errDescription."<br/>";
	else 
		return "";
}

// ******************************************************************************************
function buildRecordset($query) {
    if(!$query) return;
    
    $rs = array();
    while ($row = mysql_fetch_array ($query, MYSQL_ASSOC)) {
  		array_push($rs, $row);
  	}
	return $rs;
}

// ******************************************************************************************
function getMonthName($monthNumber, $ln="") {
	switch ($ln) {
		case "":
			$monthNameArr = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
			break;
		case "en":
			$monthNameArr = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "Dicember");
			break;
	}
	return $monthNameArr[$monthNumber - 1];
}

// ******************************************************************************************
function dateShow($date, $monthFormat, $ln="", $separator=" ")
{
	//$datetime deve essere nella forma YYYY-MM-DD
	$date = substr($date, 0, 10); //elimino l'eventuale ora
	$day = substr($date, 8, 2);
	$monthNumber = substr($date, 5, 2);
	$month = $this->getMonthName($monthNumber, $ln);
	if ($monthFormat=="short") $month = substr($month, 0, 3);
	$year = substr($date, 0, 4);
	return number_format($day, 0) . $separator . $month . $separator . $year;
}

// ******************************************************************************************
function datetimeShow($datetime, $monthFormat, $ln="", $separator = " ") {
	//$datetime deve essere nella forma YYYY-MM-DD HH:MM:SS
	$year = substr($datetime, 0, 4);
	$monthNumber = substr($datetime, 5, 2);
	$day = substr($datetime, 8, 2);
	$hour = substr($datetime, 11, 2);
	$min = substr($datetime, 14, 2);
	$sec = substr($datetime, 17, 2);
	$month = $this->getMonthName($monthNumber, $ln);
	if ($monthFormat=="short") $month = substr($month, 0, 3);
	return number_format($day, 0) . $separator . $month . $separator . $year . ", " . $hour . ":" . $min;
}

// ******************************************************************************************
function dateCalculate ($myDate, $dayDiff, $monthDiff=0) {
	//$mydate deve essere nella forma YYYY-MM-DD
	$resultDate = mktime (0, 0, 0, substr($myDate, 5, 2) + $monthDiff, (substr($myDate, 8, 2) + $dayDiff), substr($myDate, 0, 4));
	$finalDate = date ("Y-m-d", $resultDate);
	return $finalDate;
}

// ******************************************************************************************
function tagImg ($fileName) {
	if (file_exists($fileName)) {
		$myarray = getimagesize ($fileName);
		echo "<img src=\"" . $fileName . "\" " . $myarray[3] . " border=\"0\" alt=\"\">";
	}
}

// ******************************************************************************************
function getExtIfExists ($fileName, $extList) {
	$extArr = explode ("|", $extList);
	for ($i=0; $i<count($extArr); $i++) {
		if (file_exists($fileName . "." . $extArr[$i])) {
			return $extArr[$i];
		}
	}
	return false;
}

// ******************************************************************************************
function getExt($fileName) {
	//strrpos restituisce l'ultimo carattere '.' presente in $filename
	$pos = strrpos ($fileName, ".");
	//viene restituito dal punto (escluso) in poi
	return strtolower(substr($fileName, $pos + 1));
}

// ******************************************************************************************
function getExtFromMime($mimeType) {
	$mimeType = strtolower($mimeType);
	$tempExt = "";
	if ($mimeType == "image/gif") {
		$tempExt = "gif";
	} else if ($mimeType == "image/jpg") {
		$tempExt = "jpg";
	} else if ($mimeType == "image/jpeg") {
		$tempExt = "jpg";
	} else if ($mimeType == "image/pjpeg") {
		$tempExt = "jpg";
	} else if ($mimeType == "application/pdf") {
		$tempExt = "pdf";
	} 
	return $tempExt;
}

// ******************************************************************************************
function deleteFile ($filename) {
	if (is_file($filename)) {
		return unlink($filename);
	} else {
		return false;
	}
}

// ******************************************************************************************
function deleteFiles ($fileName, $listaExt) {
	$ext = explode ("|", $listaExt);
	for ($i=0; $i<count($ext); $i++) {
		$fullFileName = $fileName . "." . $ext[$i];
		if (is_file($fullFileName)) {
			unlink($fullFileName);
		}
	}
}

// ******************************************************************************************
function getFilenameUnique () {
	$strChars = date("YmdHis", time());
	srand((double)microtime()*1000000); 
	for ($i=1; $i<=3; $i++) {
		$intPos = rand(0, strlen($this->letters));
		$chrChar = substr ($this->letters, $intPos, 1);
		$strChars .= $chrChar;
	}
	return md5($strChars);
}

// ******************************************************************************************
function getDateNormalized ($d, $m, $y) {
	$strChars = $y . substr("00" . $m, -2) . substr("00" . $d, -2);
	return $strChars;
}

// ******************************************************************************************
function showPrice ($text)
{
	if (is_numeric($text))
	{
		$text = number_format ($text, 2 , ",", ".") . " Euro";
	}
	return $text;
}

// ******************************************************************************************
function showPriceShort ($text)
{
	if (is_numeric($text))
		$text = number_format ($text, 2 , ",", ".");
	if ($text)
		$text = "€  " . $text;
	return $text;
}

// ******************************************************************************************
function showCurrency($text)
{
	if (is_numeric($text))
		$text = number_format ($text, 2 , ",", ".");
	return $text;
}

// ******************************************************************************************
function imageAdapt ($fileSource, $fileDesti, $finalw, $finalh) {
	$finalprop = $finalh / $finalw;
	$myarray = getimagesize ($fileSource);
	$actualw = $myarray[0];
	$actualh = $myarray[1];
	$actualprop = $actualh / $actualw;
	if ($finalprop != $actualprop) {
		//la proporzione tra height e width non e' uguale: devo fare il crop
		$idealh = $actualw * $finalprop;
		$idealw = $actualh / $finalprop;
		if ($idealh < $actualh) {
			$cropw = $actualw;
			$croph = $idealh;
			$cropx = 0;
			$cropy = number_format(($actualh - $idealh) / 2);
		} else {
			$cropw = $idealw;
			$croph = $actualh;
			$cropx = number_format(($actualw - $idealw) / 2);
			$cropy = 0;
		}
	} else {
		//non devo fare il crop ma solo il ridimensionamento (forse)
		$cropw = $actualw;
		$croph = $actualh;
		$cropx = 0;
		$cropy = 0;
	}
	$finalw = (int) $finalw;
	$finalh = (int) $finalh;

	//GDlibrary
	$imageSource = imagecreatefromjpeg($fileSource);
	$imageDestination = imagecreatetruecolor($finalw, $finalh);
	imagecopyresized($imageDestination, $imageSource, 0, 0, $cropx, $cropy, $finalw, $finalh, $cropw, $croph);
	imagejpeg($imageDestination, $fileDesti, 80);
	imagedestroy($imageSource);
	imagedestroy($imageDestination);
}

// ******************************************************************************************
//ridimensiona in proporzione, considera finalw e finalh come dimensioni massime raggiungibili
function imageResize ($fileSource, $fileDesti, $finalw, $finalh) {
	$myarray = @getimagesize($fileSource);
	$actualw = $myarray[0];
	$actualh = $myarray[1];
	$actualprop = $actualh / $actualw;
	$redim = false;
	if (($finalw != 0) && ($finalh == 0)) {
		//ridimensiono solo in base alla larghezza (se e' piu' grande)
		if ($actualw > $finalw) {
			$finalh = $finalw * $actualprop;
			$redim = true;
		}
	} else {
		if (($finalw == 0) && ($finalh != 0)) {
			//ridimensiono solo in base alla lunghezza (se e' piu' grande)
			if ($actualh > $finalh) {
				$finalw = $finalh / $actualprop;
				$redim = true;
			}
		} else {
			if (($finalw != 0) && ($finalh != 0)) {
				//ridimensiono sia in base alla larghezza che alla lunghezza (se una delle due e' piu' grande)
				if (($actualw > $finalw) || ($actualh > $finalh)) {
					$finalprop = $finalh / $finalw;
					if ($finalprop < $actualprop) {
						$finalw = $finalh / $actualprop;
					} else {
						$finalh = $finalw * $actualprop;
					}
					$redim = true;
				}
			}
		}
	}
	if (!$redim) {
		$finalw = $actualw;
		$finalh = $actualh;
	}
	$finalw = (int) $finalw;
	$finalh = (int) $finalh;

	//GDlibrary
	$imageSource = imagecreatefromjpeg($fileSource);
	$imageDestination = imagecreatetruecolor($finalw, $finalh);
	imagecopyresized($imageDestination, $imageSource, 0, 0, 0, 0, $finalw, $finalh, $actualw, $actualh);
	imagejpeg($imageDestination, $fileDesti, 80);
	imagedestroy($imageSource);
	imagedestroy($imageDestination);
}

/**
******************************************************************************************
* returns a string containing the JSON representation of value
* @access public        
* @param string $a: the value  being encoded
* @return string
*/
function json_encode($a=false)
{
	if (function_exists('json_encode'))
	{
		json_encode($a);
	}
	else 
	{
	    if (is_null($a)) return 'null';
	    if ($a === false) return 'false';
	    if ($a === true) return 'true';
	    if (is_scalar($a))
	    {
	      if (is_float($a))
	      {
	        // Always use "." for floats.
	        return floatval(str_replace(",", ".", strval($a)));
	      }
	
	      if (is_string($a))
	      {
	        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
	        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
	      }
	      else
	        return $a;
	    }
	    $isList = true;
	    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
	    {
	      if (key($a) !== $i)
	      {
	        $isList = false;
	        break;
	      }
	    }
	    $result = array();
	    if ($isList)
	    {
	      foreach ($a as $v) $result[] = json_encode($v);
	      return '[' . join(',', $result) . ']';
	    }
	    else
	    {
	      foreach ($a as $k => $v) $result[] = $this->json_encode($k).':'.$this->json_encode($v);
	      return '{' . join(',', $result) . '}';
	    }
	}
}

// ******************************************************************************************
function textExtract($text, $numberOfChars) {
	$text = strip_tags($text);
	$len = strlen($text);
	if ($len > $numberOfChars) {
		$text = substr($text, 0, $numberOfChars - 4);
		$pos = strrpos($text, " ");
		if (!($pos === false)) $text = substr($text, 0, $pos);
		$text .= " ...";
	}
	return $text;
}

// ******************************************************************************************
function translateForDb($chars, $type, $defaultValue="", $convertEmptyToNull=true, $convertZeroToNull=true) {
	If (!$chars && !($defaultValue===false)) {$chars = $defaultValue;}
	switch ($type)
	{
		case "string":
		case "date":
		   	$chars = trim($chars); 
		    if(get_magic_quotes_gpc())
		          $chars = stripslashes($chars);
		    if(function_exists("mysql_real_escape_string"))
		          $chars = mysql_real_escape_string($chars);
		    else
		          $chars = addslashes($chars);
			$chars = $this->translateForHtml($chars);
			$separator = "'";
			if ((!$chars) && $convertEmptyToNull)
				$chars = "NULL";
			else
				$chars = $separator . $chars . $separator;
			break;
		case "int":
			if (($chars===0) && $convertZeroToNull)
				$chars = "NULL";
			elseif (($chars==="" || ($chars===false)) && $convertEmptyToNull)
				$chars = "NULL";
			else
				$chars = (int) $chars;
			break;
		case "decimal":
			if (($chars===0) && $convertZeroToNull)				
				$chars = "NULL";
			elseif (($chars==="" || ($chars===false)) && $convertEmptyToNull)
				$chars = "NULL";
			else
				$chars = (double) $chars;
			break;
	}
	return $chars;
}

// ******************************************************************************************
function translateForHtml($chars) {
	$chars = str_replace("\n", "<br/>", $chars);
	$chars = str_replace(chr(13), "", $chars);
	$chars = str_replace(chr(10), "", $chars);
	$trans = Array("&" => "&amp;");
	//$trans = Array("\"" => "&quot;", "&" => "&amp;");
	//entities : "160,nbsp,38,amp,34,quot,162,cent,8364,euro,163,pound,165,yen,169,copy,174,reg,8482,trade,8240,permil,60,lt,62,gt,8804,le,8805,ge,176,deg,8722,minus",
	$chars = strtr($chars, $trans);
	return $chars;
}

// ******************************************************************************************
function translateForSafe($chars, $maxlength=false) 
{
	if ($maxlength > 0)
		$chars = substr($chars, 0, $maxlength);
	return strip_tags($chars);
}

// ******************************************************************************************
function translateForTextarea($chars) {
	$trans = array ("<br/>" => "\n", "<br/>" => "\n", "<br />" => "\n");
	return strtr($chars, $trans);
}

// ******************************************************************************************
function translateForPdf($text)
{
	$trans = array ("<br/>" => "\n", "<br/>" => "\n", "<br />" => "\n", "<p>" => "", "</p>" => "\n");
	$text = strtr($text, $trans);
	$text = strip_tags($text);
	return $text;
}


// ******************************************************************************************
function translateForHidden($chars) 
{
	$trans = array ("\'" => "'");
	return strtr($chars, $trans);
}

// ******************************************************************************************
function translateForDisplay($chars) 
{
	$trans = array ("\'" => "'", "\\\"" => "\"");
	return strtr($chars, $trans);
}

// ******************************************************************************************
function dictionary($_ = array('word'=>'','mode'=>'ARRAY')) {
	
	$this->dictionaryWords;
	
	switch ($_['mode']) {
		case 'STRING':
			return $dictionaryWords[$_['word']][$this->getLn()];
		break;
		default:
			return $dictionaryWords[$_['word']];
	
	}
	
}

// ******************************************************************************************
function retTema(){
  global $config_table_prefix;
  
  $objConfig=$this->objConfig;
  if($objConfig->get("use_themes")=="1"){
    $tema=getTema();
    $md5=getTemaMD5($tema);
    
    $sql = "SELECT id FROM `".$config_table_prefix."themes` WHERE md5='".$md5."'";
    $query = mysql_query ($sql);
    
    if($query){
      $temi = $this->buildRecordset($query);
      
      if(count($temi)==0){
          $sql="INSERT INTO `".$config_table_prefix."themes` (nome,md5,attivo) VALUES ('$tema','$md5','1')";
          $query=mysql_query($sql);
          $id_oggetto=mysql_insert_id();
      } 
    }
    
    $tema="themes/" . $md5 . "/";
  }else{
    $tema="";  
  }
  
  return $tema;
}

}
?><?php //#rs-enc-module123;# ?>