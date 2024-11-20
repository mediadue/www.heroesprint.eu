<?php
//ini_set('xdebug.max_nesting_level', 500);
session_start();



if(isset($_SESSION["userris_id"]) && $_GET['menid']=="5") {
  header("location: index.php?documents=1"); 
  exit;
}
 
require_once ("rsHeader.php");
require_once ("_docroot.php");
require_once (SERVER_DOCROOT."logic/class_config.php");

$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$objHtml = new Html;

serverRedir();

if($_GET['menid']=="") $_GET['menid']=getCategoria();  
$objCarrello = new Carrello();
$objCarrello->action();
$objCarrello->setOptions(false,0,$objUtility->getPathRoot()."index.php",0);
$objCarrello->setCurrent(getCategoria(),$_GET['ecomm_combi']);


$menid=$_GET["menid"]; 
if($_GET["ecomm_riepilogo"]>0) $titriep="Carrello";

$layout=retCatLayout($menid);

$curLan=getCurLan();

$clName=$curLan["classe"];
$clID=$curLan["id"];

$arrStr=getStrutturaByNodo($_GET['menid']);

?>
<!doctype html>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "http://<?php echo $_SERVER['HTTP_HOST']; ?>",
  "logo": "http://<?php echo $_SERVER['HTTP_HOST'] . "/" . $objUtility->getPathImages();  ?>logo-heroesprint-hdr.png",
  "name": "HeroesPrint.eu",
  "image" : "http://<?php echo $_SERVER['HTTP_HOST'] . "/" . $objUtility->getPathResourcesDynamic(); ?>thm_1170x360_16ec534d006ce3632e9c0ef2f2c63540.jpg",
  "contactPoint": [{
    "@type": "ContactPoint",
    "telephone": "+39 075 88 600 33",
    "email": "info@heroesprint.eu",
    "contactType": "customer service",
    "availableLanguage": ["Italian","English"],
    "areaServed": ["IT","GB","DE","FR","ES"],
    "contactOption": "TollFree"
  }],
  "sameAs": [
    "https://www.facebook.com/heroesprint.eu",
    "https://twitter.com/Heroes_Print",
    "https://it.pinterest.com/heroesPrint"
  ],
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Via delle Industrie, 84D",
    "addressLocality" : "Foligno",
    "addressRegion" : "(PG)",
    "postalCode" : "06034"
  }
}
</script>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $clName; ?>"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="<?php echo $clName; ?>"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="<?php echo $clName; ?>"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js" lang="<?php echo $clName; ?>"> 
<!--<![endif]-->
  <head>
    <?php 
    $headTitle=$titriep; 
    if($cat['nome']!="Home" && $rs[0]['titolo']=="") $headTitle.= $cat['nome']." - ".$rs[0]['titolo'];else $headTitle.= $rs[0]['titolo'];
    ?>
    <title><?php echo ln($headTitle); ?>- HeroesPrint.eu</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="<?php echo ln($rs[0]['descrizione']); ?>"/>
    <meta name="keywords" content="<?php echo ln($key1.$rs[0]['keywords']); ?>"/>
    <meta NAME="copyright" CONTENT="<?php echo $objConfig->get("email-fromname"); ?>">
    <meta name="robots" content="<?php if($cpageRobots=="index.php" && $cparamRobots!="") echo "noindex";else echo "index"; ?>, follow">
    <meta name="google-site-verification" content="FYcz1fZGLCkkN_uDIuYBybyrAcIOXEN8-1FYxPcZIrs" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="<?php echo $objUtility->getPathImages(); ?>apple-touch-icon.png">
    <meta name="p:domain_verify" content="96ac519830db6b9730a9d3a5ba27f1b0"/>
    <link rel="shortcut icon" href="<?php echo $objUtility->getPathImages(); ?>favicon.ico" />
                    
    <?php 
    $langs=getTable("lingue","Ordinamento ASC","attivo=1"); 
    $cPage=explode("/",$_SERVER["REQUEST_URI"]);
    $cPage=array_reverse($cPage);
    $cPage=$cPage[0];
    $predUrl=$_GET["url"].".html";
    
    if($predUrl==".html") $predUrl="home.html";
    if($cPage=="") $cPage="home.html";
    
    while (list($key, $row) = each($langs)) { 
      if($row["classe"]=="en") $iden=$row["id"];
      if($row["id"]==$_GET["lan"]) { ?><link rel="canonical" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row["classe"]; ?><?php if($predUrl!="home.html") echo "/".ln($predUrl,0,$row["id"]); ?>" /><? }
    }
    
    reset($langs);
    while (list($key, $row) = each($langs)) { ?>
      <link rel="alternate" hreflang="<?php echo $row["hreflang"]; ?>" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row["classe"]; ?><?php if($predUrl!="home.html") echo "/".ln($predUrl,0,$row["id"]); ?>" />
    <? } ?>
    <link rel="alternate"  hreflang="x-default" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/en<?php if($predUrl!="home.html") echo "/".ln($predUrl,0,$iden); ?>" />
    
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php echo ln($headTitle); ?>- HeroesPrint.eu" />
    <meta property="og:description" content="<?php echo ln($rs[0]['descrizione']); ?>" />
    <meta property="og:url" content="http://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $clName; ?>/<?php echo $cPage; ?>" />
    <meta property="og:site_name" content="heroesprint.eu/<?php echo $clName.'/'; ?>" />
    <meta property="article:published_time" content="2013-06-02T15:32:12+00:00" />
    <meta property="article:modified_time" content="<?php echo str_replace("#","T", date("Y-m-d#H:i:s+00:00", filemtime("index.php"))); ?>" />
    
    
    <?php include(SERVER_DOCROOT . 'include/inc.functions.php');
    $objCarrello->CarrelloAjax();
    if($cat["home_page"]==1 && $_GET["documents"]!=1 && !isset($_REQUEST["UserReg"])) { ?>
    <? } ?>
    <style>
    div.loading-overlay {
      display:        table;
      position:       fixed;
      top:            0;
      left:           0;
      width:          100%;
      height:         100%;   
      opacity: 1;
    }
    div.loading-overlay > div {
      display:        table-cell;
      width:          100%;
      height:         100%;
      background: #D5E9FC;
      text-align:     center;
      vertical-align: middle;
      font-weight:bold;
      font-family: "Comic Sans MS";
      color: #C20000;      
      background-image: url(<?php echo $objUtility->getPathImages(); ?>gruppo.jpg);
      background-repeat: no-repeat;
      background-position: center center;
      background-size: auto 60%;
    }   
    </style>
 
    <script>var notawk=1;</script>                              
  </head>  
  <body>
    <span class="master-container tshirt-designer" >
      
      <header id="masthead" class="tshirt-container masthead"></header>
        
      <div class="tshirt-editor-container">  
        <?php
        if($arrStr["nome"]=="magazzino" || $_GET["ecomm_riepilogo"]==1 || $_GET["ecomm_combi"]>0){
             $objCarrello->stampaCarrello();  
        }
        ?>  
      </div> 

        <?php //include "inc.modal.php"; ?>
        <?php
if(!isAjaxPost()) { ?>
  <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $objUtility->getPathCss(); ?>font-awesome.min.css">
  <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $objUtility->getPathCss(); ?>google_fonts.css">
  
  <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $objUtility->getPathRoot(); ?>css/inc.rsStyle.css" />
  <script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources(); ?>inc.rsJavaScript.js"></script>
  
  <?php ob_start(); ?>
  <script>
  $(document).ready(function() {
    
  });
  </script>
  
  <?php 
  include(SERVER_DOCROOT . 'include/inc.jfunctions.php');
  $tjscode=ob_get_contents();
  ob_end_clean();
  if(isset($objCarrello)) $tjscode.=$objCarrello->g_jsCode;
  if(isset($objHtml)) $tjscode.=$objHtml->g_jsCode;
  $compressedJs=compressJs($tjscode);
  //$compressedJs=$tjscode;
  
  echo $compressedJs; 
}
?>		
        
    </span>							  			    		  			  							  					
  </body>
</html>						