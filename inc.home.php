<?php
    $tmpl_btn_product=getTable("tmpl_btn_dettagli","","attivo=1");
    $tmpl_btn_product=$tmpl_btn_product[0];
?>

<section id="offersArea" class="offers-area container  spacing-normal clearfix">
  <div class="row">
    <?php 
	if(is_array($magazzino)){
        reset($magazzino);    
     
        $k=-1;
    	while (list($key, $row) = each($magazzino)) { 
        	$k++;
        	
            $tmpl_products=getTable("tmpl_products","attivo=1","id_categorie_str_magazzino='".$row["id"]."'");
            $tmpl_products=$tmpl_products[0];
            
            $articolo=retArticoloFromCat($row["id"]);
        	
            $prezzo=retPrezzoScontato($articolo);
            
            $gallery=Table2ByTable1("categorie", "fotogallery", $row["id"], "attivo=1", "Ordinamento ASC");
            
            $bg_class1="style='background-color: ".$tmpl_products['Colore_Titolo'].";'";
            $bg_class2="style='background-color: ".$tmpl_products['Colore_Testo'].";'";
    	    ?>
    	   	<div class="col-sm-4  col-md-4" >
              <div class="box  box--offer">
                
                <header <?php echo $bg_class1; ?> class="box__header txt-display">
                  <div class="box__pricetag  bg-shout"><?php echo $prezzo; ?> 
                    <div class="pricetag">
                      <p class="pricetag-text"><?php if($prezzo>80 || $prezzo==0.01 || $row["nome"]=="PICCOLO FORMATO") echo "NEW"; elseif($row["nome"]=="ROLL UP") echo "26,50 €<br>AL PEZZO"; else echo currencyITA($prezzo)." €<br>"; if($prezzo<80 && $row["nome"]!="PICCOLO FORMATO" && $row["nome"]!="ROLL UP" && $prezzo!=0.01) echo ln("AL MQ"); ?></p>
                    </div>
                  </div>
                  <H<?php if($k<6) echo $k+1; else echo "1"; ?> class="box__headline"><?php echo ln($tmpl_products['Titolo']); ?></H<?php if($k<6) echo $k+1; else echo "1"; ?>>
                </header>
                
                <div <?php echo $bg_class2; ?> class="box__content">
                  <div class="box__media"><a href="<?php echo $objUtility->getPathRoot().$clName."/".ln($row["url"]); ?>"><img class="img-responsive img-prd-home" src="<?php echo retFile($gallery[0]["immagine_file"],0,200); ?>" alt=""></a></div>
    	            <div class="box__copy box__copy--bottom">
    	              <?php echo troncaTesto(ln($articolo["Descr1"]),300); ?>
    	            </div>
                </div>
                
                <div class="box__footer">
                  <a href="<?php echo $objUtility->getPathRoot().$clName."/".ln($row["url"]); ?>" class="btn btn-block btn-lg <?php echo $tmpl_btn_product['class']; ?> btn-flow" role="button"><i class="icon  icon--big  fa <?php echo $tmpl_btn_product['icon']; ?>"></i><?php echo ln($tmpl_btn_product['text']);?></a>
                </div>
    
              </div>
            </div>	
        <? } ?>
    <? } ?>
  </div><!-- /#offersArea>row -->

</section><!-- /#offersArea -->

<section id="featuresArea" class="container spacing-normal clearfix">
  <div class="row features-area ">
    <div class="row-sm-height">
      <div class="col-sm-4 col-md-4 col-sm-height col-sm-top">
        <div class="box box--mini inside inside-full-height">
          <div class="media">
            <div class="media-left">
              <a class="media__link" href="#">
                <i class="media-object  media-object--features  fa fa-rocket" ></i>
              </a>
            </div>
            <div class="media-body  media-body--features">
              <p class="media-heading  media-heading--features"><?php echo ln("Consegna 24/48h"); ?></p>
              <p><?php echo ln("Grazie al nostro servizio"); ?> <span class="txt-display-footer"><strong>PrintExpress&copy;</strong></span> <?php echo ln("avrai le tue stampe in un lampo"); ?></p>
            </div>
          </div>
        </div>
      </div>

       <div class="col-sm-4 col-md-4 col-sm-height col-sm-top">
         <div class="box box--mini inside inside-full-height">
            <div class="media">
              <div class="media-left">
                <a class="media__link" href="#">
                  <i class="media-object  media-object--features  fa fa-credit-card" ></i>
                </a>
              </div>
              <div class="media-body  media-body--features">
                <p class="media-heading  media-heading--features"><?php echo ln("Pagamenti sicuri"); ?></p>
                <p><?php echo ln("Con PayPal potrai pagare in maniera"); ?> <span class="txt-display-footer"><strong><?php echo ln("rapida e senza rischi"); ?></strong></span>, <?php echo ln("usando le più diffuse carte di credito e prepagate"); ?></p>
              </div>
            </div>
          </div>
       </div>
       <div class="col-sm-4 col-md-4 col-sm-height col-sm-top">
         <div class="box box--mini inside inside-full-height">
            <div class="media">
              <div class="media-left">
                <a class="media__link" href="#">
                  <i class="media-object  media-object--features  fa fa-thumbs-o-up" ></i>
                </a>
              </div>
              <div class="media-body  media-body--features">
                <p class="media-heading  media-heading--features"><?php echo ln("Qualità Certificata"); ?></p>
                <p><?php echo ln("I materiali e le tecniche di stampa ti assicurano"); ?> <span class="txt-display-footer"><strong><?php echo ln("prestazioni da supereroe"); ?></strong></span>, <?php echo ln("anche in condizioni atmosferiche estreme"); ?></p>
              </div>
            </div>
          </div>
       </div>  
    </div>
       
  </div><!-- /#featuresArea>row -->
</section><!-- /#featuresArea -->