<?php if (!_bot_detected()) { ?>
    
    function analitycs_sendData(id, fun) {
    	$.ajax({
    	  type: "POST",                       
    	  url: "<?php echo $objUtility->getPathRoot(); ?>rsActionBoot.php",
    	  data: "rsUPDanalitycs=1&id="+id, 
    	  success: fun,
    	  error: function(XMLHttpRequest, textStatus, errorThrown) {
    		  				//alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione"); ?>."); 
    	         }
    	});  
    }
    
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
  
    ga('create', 'UA-79250023-1', 'auto');
    ga('send', 'pageview');
     
    <?php 
    $ordine=getTable("ecommerce_ordini","","(analitycs_sent=0 AND id_ecommerce_stati=9)");
    if(count($ordine)>0) { ?>
      ga('require', 'ecommerce');
      ga('ecommerce:clear');
      <?php
    
      while (list($key1, $row1) = each($ordine)) {
        $acquisti=getTable("acquisti","","(codice_vendita='".addslashes($row1["codice_vendita"])."')");
        $tot=0;
        while (list($key, $row) = each($acquisti)) {
          $sconto_aggiunte=($row["aggiunte_cry"]*$row["sconto"])/100;
          $tot+=round(($row["prezzo_scontato_cry"]+$row["aggiunte_cry"]-$sconto_aggiunte),2);
        }
        
        $imponibile=round($row1["totale_cry"]/1.22,2);
        $shipping=round($imponibile-$tot,2);
        $tax=round(($imponibile*22)/100,2);
        
        $trans = array('id'=>$row1["codice_vendita"], 'affiliation'=>'Mediadue.net','revenue'=>$imponibile, 'shipping'=>$shipping, 'tax'=>$tax);
        
        $items = array();
        reset($acquisti);
        while (list($key, $row) = each($acquisti)) {
          $mag_art=retRow("magazzino_articoli",$row["id_magazzino_articoli"]);
          $cat=retRow("categorie",$mag_art["id_categorie_str_magazzino"]);
          if($row["quantita"]>0){
            $prezzo_row=round(($row["prezzo_scontato_cry"]+$row["aggiunte_cry"])/$row["quantita"],2);
            array_push($items, array('sku'=>$mag_art["Codice"], 'name'=>$cat["nome"], 'category'=>'', 'price'=>$prezzo_row, 'quantity'=>$row["quantita"]));
          }
        }
        
        if(count($items)>0) {
          echo getTransactionJs($trans);
          
          foreach ($items as &$item) {
            echo getItemJs($row1["codice_vendita"], $item);
          }
        }
        
        ?>
        analitycs_sendData(<?php echo $row1["id"]; ?>,function(msg){
    			//alert(msg);
    		});
        <?php
      }
       
      echo "ga('ecommerce:send');";
    } 
} 
?>