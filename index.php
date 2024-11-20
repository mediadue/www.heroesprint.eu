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

$UserReg=$_REQUEST["UserReg"];
$_SESSION["UserReg"]=$_REQUEST["UserReg"];
 
if($_GET['menid']=="") $_GET['menid']=getCategoria();  
$objCarrello = new Carrello();
$objCarrello->action();
$objCarrello->setOptions(false,0,$objUtility->getPathRoot()."index.php",0);
$objCarrello->setCurrent(getCategoria(),$_GET['ecomm_combi']);

$tit=ln(str_replace(".html","",str_replace("-"," ",$tit['url'])));

if($tit=="") $tit="";
 
$keywordArr=explode(" ", $tit);
shuffle($keywordArr); 

$keywords=explode(",",$objConfig->get("keyword"));
//shuffle($keywords);  

$tit="";
$cat="";
$des="";
$key1="";
  
$cat=retRow("categorie",$_GET['menid']);
$des=" - ";
if($tit['titolo']!="") $tit['titolo']=$tit['titolo']." - ";

if($tit['titolo']=="") $tit['titolo']=$keywords[0];

$rs=getIntestazioni();
if($rs[0]['keywords']=="") $rs[0]['keywords']=implode(",",$keywords);
if($rs[0]['titolo']=="") $rs[0]['titolo']=$keywords[0];

$menid=$_GET["menid"]; 
if($_GET["ecomm_riepilogo"]>0) $titriep="Carrello";

$layout=retCatLayout($menid);

$curLan=getCurLan();

$clName=$curLan["classe"];
$clID=$curLan["id"];

$arrStr=getStrutturaByNodo($_GET['menid']);

$keyw=explode(",",$rs[0]['keywords']);

$cpageRobots=basename($_SERVER["PHP_SELF"]);
$cparamRobots=$_SERVER["QUERY_STRING"];

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
  </head>  
  <body>
  
  <?php 
  /*
  $r1['id']=1;
  $r1['w']=31;
  $r1['h']=41;
  
  $r2['id']=2;
  $r2['w']=12;
  $r2['h']=102;
  
  $r3['id']=3;
  $r3['w']=153;
  $r3['h']=63;
  
  $r4['id']=4;
  $r4['w']=94;
  $r4['h']=134;
  
  DataRet($r1);
  DataRet($r2);
  DataRet($r3);
  DataRet($r4);
  
  $dimArr = array();
  array_push($dimArr,$r1,$r2,$r3,$r4);
  tetris($dimArr,"forex",3,"bianco");
  
  exit; 
  */
  ?>
  
    <div class="loading-overlay"><div></div></div>
    <span class="master-container" style="display:none;">
      <div class="alert alert-warning alert-dismissible cookie-alert clearfix" role="alert">
        <p>
          <?php echo ln("Questo sito utilizza cookie di profilazione propri o di terze parti, per saperne di più"); ?>  
          <a class="cookie-alert__link" href="<?php echo $objUtility->getPathRoot().$clName."/"."privacy.html"; ?>"><?php echo ln("clicca qui"); ?>.</a>
          <button class="btn btn-success btn-inline-dx" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><?php echo ln("Accetto"); ?></span></button>
        </p>
        
      </div>
      <header id="masthead" class="container masthead">
  
        <div id="masteadBar" class="row masthead-bar no-spacing">&nbsp;</div>
        
          <div id="mastheadContent" class="row masthead-content clearfix">
            <div class="logo cell-md cell--v-aligned  spacing-normal">
              <a href="<?php echo $objUtility->getPathRoot().$clName; ?>"><img class="img-responsive logo_img block--h-aligned " src="<?php echo $objUtility->getPathImages() . ln("logo-heroesprint-hdr.png"); ?>" alt="<?php echo ln(trim($keyw[0])); ?>"></a>
            </div>
            <div class="masthead-banner cell-md cell--v-aligned  spacing-normal">
              <img class="img-responsive masthead-banner_img block--h-aligned" src="<?php echo $objUtility->getPathImages() . ln("masthead-banner.gif"); ?>" alt="">
            </div>
          </div>
        </header>
        <div class="container">
          <div class="row hero-wrap">
            <div id="navSection" class="nav-section clearfix">
              <nav class="navbar main-nav ">
                <div class="container-fluid no-spacing">
    
                  <div class="navbar navbar-fixed-top hidden-md hidden-lg hidden-sm hp-navbar-top">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="offcanvas" data-recalc="false"  data-canvas=".canvas" data-target="#mainNavBar" aria-expanded="false">
                      <span class="sr-only">Toggle navigation</span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                    </button>
                  </div>
    
                  <div id="mainNavBar" class="navmenu  navmenu-fixed-left  offcanvas-xs  navmenu-main">
                    <ul class="nav  navbar-nav  navbar-left  nav-main">
                      <?php 
      				    $mc=0;
                        $menu=getStrutturaFull("Gestione Sezioni","",-1);
                        $magazzino=getStrutturaFull("magazzino","",-1);
    					while (list($key, $row) = each($menu)) { 
                          $mc++;
                          if($mc==2){ ?>
                            <li class="dropdown">
                              <a href="#" class="dropdown-toggle nav-main_link shout" data-toggle="dropdown">
                                <?php echo ln("Prodotti"); ?>
                                <i class="glyphicon glyphicon-menu-down "></i>
                              </a>
                              
                              <ul class="dropdown-menu list-unstyled arrow-box">
                                 <?php 
    								while (list($key2, $row2) = each($magazzino)) { ?>
    						   		<li <?php if($menid==$row2["id"]) echo 'class="active"' ?>>
    							   		<a class="nav-main_link" href="<?php echo $objUtility->getPathRoot().$clName; if($row2["url"]!="home.html") echo "/".ln($row2["url"]); ?>"><?php echo ln($row2["nome"]); ?> 
    							   			<?php if($menid==$row2["id"]) { ?><span class="sr-only">(current)</span><? } ?>
    							   		</a>
    						   		</li>	
    						        <? } ?>
                              </ul>
                            </li>
                            <? } ?>
                          
                          <li <?php if($menid==$row["id"]) echo 'class="active"' ?>>
  					   		<a class="nav-main_link <?php if ($row["id"]==2090) echo "shout" ?>" href="<?php echo $objUtility->getPathRoot().$clName; if($row["url"]!="home.html") echo "/".ln($row["url"]); ?>"><?php echo ln($row["nome"]); ?> 
  					   			<?php if($menid==$row["id"]) { ?><span class="sr-only">(current)</span><? } ?>
  					   		</a>
  				   		  </li>	
				 		<? } ?>
                      
                      <li>
				   		<a href="tel:+390758860033" class="nav-tel_link" ><img height="32" src="<?php echo $objUtility->getPathImages()."phone.png"; ?>" />Tel. 075 88 600 33</a>
			   		  </li>	
                    </ul>                               
    
                    <ul class="nav navbar-nav navbar-right">
                      <li class="pull-left dropdown lang-sel">
                        <a href="#" class="dropdown-toggle lang-sel-link" data-toggle="dropdown"><span class="lang-xs" lang="<?php echo $curLan["classe"]; ?>"></span><i class="glyphicon glyphicon-menu-down button-indicator"></i></a>
                        
                          <ul class="dropdown-menu list-unstyled lang-sel-menu lang-sel-menu--right arrow-box">
                           <?php 
    					   reset($langs);
    					   while (list($key, $row) = each($langs)) { ?>
    					       <li><a href="http://<?php echo $_SERVER['HTTP_HOST'].$objUtility->getPathRoot().$row['classe']; ?>"><span class="lang-xs lang-lbl-full" lang="<?php echo $row['classe'] ?>"></span></a></li>
    					   <? } ?>
                          </ul>
                      </li>
                    </ul>
                    
                    <ul class="nav navbar-nav  nav-service"></ul>

                  </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
              </nav>
            </div>
            <section id="homeSlider" class="flexslider no-spacing">
                <ul class="slides">
                  <?php
                  $banner=getTable("banner","Ordinamento ASC","attivo=1");
    			  $ii=-1;
                  while (list($key, $row) = each($banner)) { 
                    $ii++; 
    				$tmpCat=retRow("categorie",$row["id_categorie_str_magazzino"]);
                    //if($tmpCat["url"]=="")  $tmpCat["url"]="home.html";
                    $banner_file=ln(retFile($row["banner_file"],1170,360));
                    if($banner_file!=false) { ?>
                        <li><a class="homeSlider-a" href="<?php echo $objUtility->getPathRoot().$clName."/".ln($tmpCat["url"]); ?>"><img src="<?php echo $banner_file; ?>" alt="<?php echo ln(trim($keyw[$ii])); ?>" /></a></li>
    		        <? } ?>
                  <? } ?>
                </ul>
            </section><!-- /#homeSlider -->
          </div><!-- /hero-wrap -->
        </div>
  
    	<?php 
    	if(($layout["nome"]=="home page" || $layout["nome"]=="") && $_GET["ecomm_riepilogo"]!=1 && !isset($_GET["ecomm_combi"]) && $_GET["HRlogin"]!=1 && $_GET["HRreg"]!=1 && $_GET['documents']!="1" && $_GET['UserReg']!="1"){ 
    	   include "inc.home.php"; 
    	}elseif ($layout["nome"]=="info" || $_GET["ecomm_riepilogo"]==1 || isset($_GET["ecomm_combi"]) || $_GET["HRlogin"]==1 || $_GET["HRreg"]==1 || $_GET['documents']=="1" || $_GET['UserReg']=="1"){ 
    	  	include "inc.info.php";	
        }elseif ($layout["nome"]=="faq"){ 
        	include "inc.faq.php";
        }elseif ($layout["nome"]=="contatti"){
        	include "inc.contatti.php";
        } ?>      
  
        <footer class="container-fluid footer">
          <div class="container">
            <div class="row footer__row">
  
              <div class="col-sm-6 col-md-4  footer__block">
                <!-- <img src="<?php echo $objUtility->getPathImages(); ?>logo-heroesprint-hdr.gif" alt="" class="footer__logo  img-responsive  spacing-xs">-->
                <h3 class="footer__headline txt-display-bottom">Heroesprint.eu</h3>
                <div class="footer__content footer__content--cpy">                
  
                  <ul class="nav nav-list footer-nav">
                    <?php 
				    $menu=getStrutturaFull("landing_pages","",-1);
					while (list($key, $row) = each($menu)) { ?>
    			   		<li <?php if($menid==$row["id"]) echo 'class="active"' ?>>
    				   		<a href="<?php echo $objUtility->getPathRoot().$clName."/".ln($row["url"]); ?>"> 
    				   			<span class="menu-text"><?php echo ln($row["nome"]); ?></span>
    				   		</a>
    			   		</li>	
			 		<? } ?>
                  </ul>
                </div>
              </div>
            
              <div class="col-sm-6 col-md-4  footer__block footer__block--center">
                <h3 class="footer__headline txt-display-bottom"><?php echo ln("Ricevi la Newsletter"); ?></h3>
                <section class="footer__obj nl-form ">
                  <div id="newsletter" class="input-group">
                    <input type="text" class="form-control txt-newsletter" placeholder="<?php echo ln("Inserisci la tua mail"); ?>...">
                    <span class="input-group-btn">
                      <button class="btn btn-default btn-success btn-newsletter" type="button"><?php echo ln("Ricevila ora"); ?></button>
                    </span>
                  </div><!-- /input-group -->
                </section>
              </div>          
  
              <div class="col-sm-6 col-md-4  footer__block">
                <h3 class="footer__headline txt-display-bottom"><?php echo ln("Seguici su"); ?>:</h3>
                <div class="sided-obj-box clearfix">
                  <?php
                  $loghi=getTable("loghi_social","Ordinamento ASC","attivo='1'");
                  while (list($key, $row) = each($loghi)) {
                    ?><div class="sided-obj footer__social-icon"><a href="<?php echo $row['link'] ?>" target="_blank"><img class="img-responsive" src="<?php echo retFile($row['logo_file']); ?>" alt="<?php echo ln($row['testo']) ?>"></a></div><?php  
                  }
                  ?>
                </div>
              </div>
            </div>
  
            <div class="row footer__row">
              <div class="col-md-4  footer__block">
                <h3 class="footer__headline footer__headline--darker txt-display-bottom"><i class="fa fa-rocket icon"></i><?php echo ln("Consegna 24/48h"); ?></h3>
              </div>
              
              <div class="col-md-4  footer__block footer__block--center ">
                <h3 class="footer__headline footer__headline--darker txt-display-bottom"><i class="fa fa-credit-card icon"></i><?php echo ln("Pagamenti sicuri"); ?></h3>
                <div class="footer__obj sided-obj-box pay-icons-list clearfix">
                  <div class="sided-obj pay-icons pay-icons--xs"><img class="img-responsive" src="<?php echo $objUtility->getPathImages(); ?>paypal.png" alt="Paypal"> </div>
                  <div class="sided-obj pay-icons pay-icons--xs"><img class="img-responsive" src="<?php echo $objUtility->getPathImages(); ?>visa02.png" alt="Visa"></div>
                  <div class="sided-obj pay-icons pay-icons--xs"><img class="img-responsive" src="<?php echo $objUtility->getPathImages(); ?>visaelectron.png" alt="Visa Electron"></div>
                  <div class="sided-obj pay-icons pay-icons--xs"><img class="img-responsive" src="<?php echo $objUtility->getPathImages(); ?>mastercard.png" alt="Mastercard"></div>
                  <div class="sided-obj pay-icons pay-icons--xs"><img class="img-responsive" src="<?php echo $objUtility->getPathImages(); ?>maestro.png" alt="Maestro"></div>
                </div>
              </div>
  
              <div class="col-md-4  footer__block">
                <h3 class="footer__headline footer__headline--darker txt-display-bottom"><i class="fa fa-thumbs-o-up icon"></i><?php echo ln("Qualità  certificata"); ?></h3>
              </div>
            </div>
          </div> <!-- /container -->
                    
          <div class="col-sm-12 col-xs-12">
              <p class="text-center"><span class="small"><strong>Heroesprint.eu</strong> <?php echo ln("è un marchio registrato | P.Iva IT03218780546"); ?>
              <br><a class="footer__link" href="mailto:info@heroesprint.eu">info@heroesprint.eu</a></span></p>
          </div>     
        </footer>
      
        <a href="#" class="back-to-top" title="Back to Top"><i class="glyphicon glyphicon-menu-up"></i></a> 
        
        <?php //include "inc.modal.php"; ?>
        <?php include "include/inc.bottom.php"; ?>		
        <div class="modal fade" id="mdlSegnala" tabindex="-1" role="dialog" aria-labelledby="">
          <div class="modal-dialog modal-lg" role="document">
      	    <div class="modal-content"></div>
      	  </div>	  
        </div>
    </span>							  			    		  			  							  					
  </body>
</html>						