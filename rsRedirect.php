<?php
session_start();

require_once ("_docroot.php");
require_once (SERVER_DOCROOT . "/logic/class_config.php");
//if($_GET['url']=="googleecb4d4ca48a72b9c") {echo "google-site-verification: googleecb4d4ca48a72b9c.html";exit;}

global $config_table_prefix;
$objConfig = new ConfigTool();
$objUtility = new Utility;
$var="menid";

serverRedir();

$query=mysql_query("SELECT id FROM ".$config_table_prefix."lingue WHERE predefinita=1");
if($query) {
	$lan_pred = mysql_fetch_array($query);
	$lingue[0]["id"]=$lan_pred["id"];
  $_SESSION["pred_lan"]=$lingue[0]["id"];
}

if($_GET['lan']=="" && ($_SESSION['lan']=="" || $_SESSION['lan']==0)) {
  $_GET['lan']=$lingue[0]["id"]; 
  $_SESSION['lan']=$_GET['lan'];
} 

if(!isset($_GET['lan']) || $_GET['lan']==""){ 
  $_GET['lan']=$_SESSION['lan'];
}

if(isset($_GET['lan']) && !($_GET['lan']>0)) {
	$query=mysql_query("SELECT * FROM ".$config_table_prefix."lingue WHERE classe='".$_GET['lan']."'");
	if($query) {
		$lan = mysql_fetch_array($query);
		$id=$lan['id'];
		$_GET['lan']=$id;
    $_SESSION['lan']=$_GET['lan'];
	}
}


if($_GET['url2']=="home" || $_GET['url2']=="home_base"){
  
  /*
  if($_SESSION["country_red"]!=1 && $_SERVER['HTTP_HOST']!="localhost" && !_bot_detected()) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $country_det = json_decode(file_get_contents("http://api.ipstack.com/".$ip."?access_key=2a6e1fea524e869668150732e3429e8e"));
    
    if($country_det->country_code=="FR") {$_SESSION["country_red"]=1;header("location: fr/");exit;}
    if($country_det->country_code=="DE") {$_SESSION["country_red"]=1;header("location: de/");exit;}
    if($country_det->country_code=="ES") {$_SESSION["country_red"]=1;header("location: es/");exit;}
    if($country_det->country_code=="EN" || $country_det->country_code=="US") {$_SESSION["country_red"]=1;header("location: en/");exit;}
  }  
  */
  
	if($_GET['url2']=="home"){
    if(right($_SERVER['REQUEST_URI'],1)=="/"){
  		$_SERVER['REQUEST_URI']=left($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1);
  	}
  	
  	$uri=$_SERVER['REQUEST_URI'];
  	
  	$arrUri=explode("/",$uri);
  	$arrUri=array_reverse($arrUri);
  	$tlan=$arrUri[0];
     
  	$query=mysql_query("SELECT id FROM ".$config_table_prefix."lingue WHERE classe='".$tlan."'");
  	if($query) {
  		$lan = mysql_fetch_array($query);
  		if($lan['id']>0){
  			$id=$lan['id'];
  			$_GET['lan']=$id;
        $_SESSION['lan']=$_GET['lan'];
  		}
  	}
  }elseif($_GET['url2']=="home_base"){
    $query=mysql_query("SELECT classe FROM ".$config_table_prefix."lingue WHERE id='".$_GET['lan']."'");
    if($query) {
  		$lan = mysql_fetch_array($query);
			$tlan=$lan['classe'];
  	}  
  }
  
  $fname = SERVER_DOCROOT ."cache/".$_SERVER['HTTP_HOST']."/".$tlan."/home.html";
  if(file_exists($fname) && $objConfig->get("use_cache")=="1"){
    if(isset($_SESSION['alert_box'])) {
      include("index.php");
    }else{
      include($fname);
      //include("index.php");
    }    
  }else{
    ob_start();
  
  	include("index.php");
    
    $html=ob_get_contents(); 
    ob_end_clean();
    if($objConfig->get("use_cache")=="1") {
      @mkdir(SERVER_DOCROOT ."cache/".$_SERVER['HTTP_HOST']);
      @mkdir(SERVER_DOCROOT ."cache/".$_SERVER['HTTP_HOST']."/".$tlan);
      file_put_contents($fname, $html);
    }
    
    echo $html;
  }
  
	exit;
}

if($_GET['url']) {
  if($lan['id']>0 && $lan['predefinita']!=1){
    $query=mysql_query("SELECT id from ".$config_table_prefix."traduzioni WHERE (testo_tradotto_editor='".$_GET['url'].".html' OR testo_tradotto_editor='<p>".$_GET['url'].".html</p>')");
    if($query) {
  		$rs = mysql_fetch_array($query);
      if($rs['id']>0){
        $rs2=Table1ByTable2("dizionario","traduzioni",$rs['id'],"","id DESC");
        while (list($key2, $row2) = each($rs2)) {
          if($row2['testo_editor']!="") {
            $_GET['url']=$row2['testo_editor']; 
            
            if(substr($_GET['url'], 0, 3)=="<p>" && substr($_GET['url'], strlen($_GET['url'])-4,strlen($_GET['url']))=="</p>") {
              $_GET['url']=substr($_GET['url'], 3, strlen($_GET['url'])-7);
            }
            
            $_GET['url']=str_replace(".html", "", $_GET['url']);
          }
        }
      }
    }  
  }
  
  $query=mysql_query("SELECT * FROM ".$config_table_prefix."categorie WHERE url='".$_GET['url'].".html'");
	
	if($query) {
		$rs = mysql_fetch_array($query);
	
		$id=$rs['id'];
		$layout=retCatLayout($id);
		$_GET[$var]=$id;
	
		if($layout['file']=="") $layout['file']="index.php";
		
    $tlan=$lan["classe"];
    $fname = SERVER_DOCROOT ."cache/".$_SERVER['HTTP_HOST']."/".$tlan."/".$_GET['url'].".html";
    if(file_exists($fname) && $objConfig->get("use_cache")=="1"){
      if(isset($_SESSION['alert_box'])) {
        include("index.php");
      }else{
        include($fname);
        //include("index.php");
      }    
    }else{
      ob_start();
    
    	include($layout['file']);
      
      $html=ob_get_contents(); 
      ob_end_clean();
      if($objConfig->get("use_cache")=="1") {
        @mkdir(SERVER_DOCROOT ."cache/".$_SERVER['HTTP_HOST']);
        @mkdir(SERVER_DOCROOT ."cache/".$_SERVER['HTTP_HOST']."/".$tlan);
        file_put_contents($fname, $html);
      }
      
      echo $html;
    }
    
		exit;
	}else{
		header("Location: http://".$_SERVER['HTTP_HOST']);
		exit;
	}
}
?>