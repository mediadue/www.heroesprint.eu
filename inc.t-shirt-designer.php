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
$objCarrello1 = new Carrello();
$objCarrello2 = new Carrello();

//if($_POST['current']=="2152") $objCarrello1->action();
//if($_POST['current']=="2151") $objCarrello2->action();

$objCarrello1->setOptions(false,0,$objUtility->getPathRoot()."index.php",0);
$combi1="";
$combi2="";

if($_GET['menid']=="2152") $combi1=$_GET['ecomm_combi'];
if($_GET['menid']=="2151") $combi2=$_GET['ecomm_combi'];

$objCarrello1->setCurrent("2152", $combi1);

$objCarrello2->setOptions(false,0,$objUtility->getPathRoot()."index.php",0);
$objCarrello2->setCurrent("2151", $combi2);


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

/*
global $config_table_prefix;

$ordini=getTable("ecommerce_ordini","","");
while (list($key, $row) = each($ordini)) {
    $sql="INSERT INTO `".$config_table_prefix."storico_users` (id_users,`domain`,`username`,`table`,`row`,deleted,data_creazione,ultimo_aggiornamento) VALUES ('1','heroesprint-eu','mediadue','ecommerce_ordini','".$row['id']."',0, NOW(), NOW() )";
    $query=mysql_query($sql);  
}
exit;
*/

//print_r($_SESSION['ecomm']);exit;

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
    $objCarrello1->CarrelloAjax();
    $objCarrello2->CarrelloAjax();
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
    <div class="loading-overlay"><div></div></div>
    <span class="master-container tshirt-designer" style="display:none;">
      <div class="alert alert-warning alert-dismissible cookie-alert clearfix" role="alert">
        <p>
          <?php echo ln("Questo sito utilizza cookie di profilazione propri o di terze parti, per saperne di più"); ?>  
          <a class="cookie-alert__link" href="<?php echo $objUtility->getPathRoot().$clName."/"."privacy.html"; ?>"><?php echo ln("clicca qui"); ?>.</a>
          <button class="btn btn-success btn-inline-dx" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><?php echo ln("Accetto"); ?></span></button>
        </p>
        
      </div>
      <header id="masthead" class="tshirt-container masthead">
  
        <div id="masteadBar" class="row masthead-bar no-spacing">&nbsp;</div>
        </header>
        <div class="tshirt-container div-tshirt-container" style="margin-bottom:0px;">
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
                    
                    <div class="tshirt-ul-2">
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
                    </div>
                    
                    <div class="tshirt-ul-1">
                      <img class="pimpmyprint-logo" src="<?php echo $objUtility->getPathImages();  ?>logo-pimpmyprint-180.png" />
                      <ul class="nav  navbar-nav    nav-main">
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
                    </div>                              
                    
                    
                    
                  </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
              </nav>
            </div>
            
          </div><!-- /hero-wrap -->
        </div>
        
        <?
    	if(isset($UserReg)) $tit=ln("I Tuoi Dati");
    	if($_GET['documents']=="1") $tit=ln("I Tuoi Documenti");
    	if($_GET["HRlogin"]==1 && !isset($_SESSION["userris_id"])) $tit=ln("Inserisci le tue credenziali");
    	if($_GET["HRlogin"]==1 && isset($_SESSION["userris_id"])) $tit=ln("Il Tuo Account");
    	if($_GET["HRreg"]==1 || isset($_POST['UserReg'])) $tit=ln("Diventa anche tu un Eroe!");
    	?>
    	
    	<section id="contentArea" class="content-area tshirt-container  spacing-normal clearfix">
          <div class="row">
            <div class="tshirt-designer-col col-sm-12 content clearfix">
              
              <?php 
              /*
              $testi=getTesti();
              echo ln($testi[0]['testo_editor']); 
              */
              ?>
              
              <!-- Module 2A -->
              <div class="ez-wr tshirt-editor-container">
                <div class="ez-box tshirt-bg"></div>
                
                <div class="ez-fl ez-negmr ez-50 tshirt-editor-container-l">
                    <div id="tabs">
                      <ul>
                        <li><a href="#tabs-1">Uomo</a></li>
                        <li><a href="#tabs-2">Donna</a></li>
                      </ul>
                      <div id="tabs-1">
                        <?php
                        if($arrStr["nome"]=="magazzino" || $_GET["ecomm_riepilogo"]==1 || $_GET["ecomm_combi"]>0){
                             $objCarrello1->stampaCarrello();  
                        }
                        ?>  
                      </div>
                      <div id="tabs-2">
                        <?php
                        if($arrStr["nome"]=="magazzino" || $_GET["ecomm_riepilogo"]==1 || $_GET["ecomm_combi"]>0){
                             $objCarrello2->stampaCarrello();  
                        }
                        ?>  
                      </div>
                    </div>
                </div>
                
                <div class="ez-last ez-oh tshirt-editor-container-r">
                  <div class="ez-box">
                  <?php 
                  if($arrStr["nome"]=="magazzino" || $_GET["ecomm_riepilogo"]==1 || $_GET["ecomm_combi"]>0){
                      ?><img class="tshirt-mockup" src="" /><?php  
                  }
                  ?>
                  <!-- <img class="tshirt-mockup" src="/css/images/tshirt-mockup.png" /> -->
                  </div>
                </div>
                
              </div>
    						
              
            </div>
    
          </div>
        </section><!-- /#ocontentArea -->      
  
        <footer class="container-fluid footer">
          <div class="tshirt-container">
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