<?php
if(base64_decode($_SERVER["HTTP_X_AUTHORIZATION"]) == "mediadue:media198386") {
  session_start();
  
  require_once ("rsHeader.php");
  require_once ("_docroot.php");
  require_once (SERVER_DOCROOT."logic/class_config.php");
  
  $objConfig = new ConfigTool();
  $objDb = new Db;
  $objUtility = new Utility;
  $objHtml = new Html;
    
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
  ?>
  <!-- File in formato Easyfatt-XML creato con Danea Easyfatt - www.danea.it/software/easyfatt -->
  <!-- Per importare o creare un file in formato Easyfatt-Xml, consultare la documentazione tecnica: www.danea.it/software/easyfatt/xml -->
  <EasyfattDocuments AppVersion="2" Creator="HeroesPrint.eu" CreatorUrl="http://www.heroesprint.eu/ordini.xml">
    <Company>
      <Name>Mediadue s.a.s.</Name>
      <Address>Via delle Industrie Snc - Loc. Portoni</Address>
      <Postcode>06034</Postcode>
      <City>Foligno</City>
      <Province>PG</Province>
      <Country>Italia</Country>
      <FiscalCode>03218780546</FiscalCode>
      <VatCode>03218780546</VatCode>
      <Tel>0742459072</Tel>
      <Fax>0742459072</Fax>
      <Email>info@mediadue.net</Email>
      <HomePage>http://www.mediadue.net</HomePage>
    </Company>
    <Documents>  
      <?php 
      $isArt=false;
      $c=-1;
      
      $documents=getTable("ecommerce_ordini","id DESC","((data >= (CURDATE()-INTERVAL 7 DAY)) AND (id_ecommerce_stati=9 OR id_ecommerce_stati=5 OR id_ecommerce_stati=4 OR id_ecommerce_stati=7 OR id_ecommerce_stati=13))");
      
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
        
        if(count($fatt["articoli"])>0) { ?>
          <Document>
            <CustomerCode></CustomerCode>
            <CustomerWebLogin></CustomerWebLogin>
            <CustomerName><?php if($fatt["ragione-sociale"]!="") echo $fatt["ragione-sociale"];else echo $fatt["cognome"]." ".$fatt["nome"]; ?></CustomerName>
            <CustomerAddress><?php echo $fatt["indirizzo"]; ?></CustomerAddress>
            <CustomerPostcode><?php echo $fatt["cap"]; ?></CustomerPostcode>
            <CustomerCity><?php echo $fatt["comune"]; ?></CustomerCity>
            <CustomerProvince><?php echo $fatt["provincia"]; ?></CustomerProvince>
            <CustomerCountry><?php echo $fatt["nazione"]; ?></CustomerCountry>
            <CustomerVatCode><?php echo $fatt["partita-iva"]; ?></CustomerVatCode>
            <CustomerFiscalCode><?php echo $fatt["codice-fiscale"]; ?></CustomerFiscalCode>
            <CustomerReference><?php echo $fatt["cognome"]." ".$fatt["nome"]; ?></CustomerReference>
            <CustomerTel><?php echo $fatt["telefono"]; ?></CustomerTel>
            <CustomerEmail><?php echo $fatt["e-mail"]; ?></CustomerEmail>
            <?php if($fatt["codice-destinatario"]!=""){ ?><CustomerEInvoiceDestCode><?php echo $fatt["codice-destinatario"]; ?></CustomerEInvoiceDestCode><? } ?>
            <?php if($fatt["pec"]!=""){ ?><CustomerPec><?php echo $fatt["pec"]; ?></CustomerPec><? } ?>
            <DocumentType>C</DocumentType>
            <Warehouse></Warehouse>
            <Date><?php echo $row["data"]; ?></Date>
            <Number><?php echo $row["id"]; ?></Number>
            <Numbering>/<?php echo left($row["data"],4); ?></Numbering>
            <CostDescription></CostDescription>
            <CostVatCode>22</CostVatCode>
            <CostAmount></CostAmount>
            <TotalWithoutTax></TotalWithoutTax>
            <VatAmount></VatAmount>
            <WithholdingTaxAmount>0</WithholdingTaxAmount>
            <Total><?php echo parseToFloat($row["totale_cry"]); ?></Total>
            <PriceList></PriceList>
            <PricesIncludeVat>false</PricesIncludeVat>
            <WithholdingTaxPerc>0</WithholdingTaxPerc>
            <PaymentName>Bonifico vista fattura</PaymentName>
            <PaymentBank>BCC Credito Cooperativo Spello e Bettona - IBAN IT 37 X 08871 38690 001000011419</PaymentBank>
            <Payments>
              <Payment>
                <Advance>false</Advance>
                <Date></Date>
                <Amount></Amount>
                <Paid><?php if($row["id_ecommerce_stati"]==9) echo "true";else echo "false"; ?></Paid>
              </Payment>
            </Payments>
            <InternalComment></InternalComment>
            <CustomField1></CustomField1>
            <CustomField2></CustomField2>
            <CustomField3></CustomField3>
            <CustomField4></CustomField4>
            <FootNotes></FootNotes>
            <SalesAgent></SalesAgent>
            <Rows>
                <?php foreach ($fatt["articoli"] as $art) { 
                  if(!isset($art["quantit-"]) && isset($art["n-copie"])) $art["quantit-"]=(float)$art["n-copie"]; 
                  ?>
                  <Row>
                    <Code><?php echo $art["cod-articolo"]; ?></Code>
                    <Description><?php echo $art["DescrTot"]; ?></Description>
                    <Qty><?php echo parseToFloat($art["quantit-"],2); ?></Qty>
                    <Um>pz</Um>
                    <Price><?php echo round($art["totale-articolo"]/$art["quantit-"],4); ?></Price>
                    <Discounts></Discounts>
                    <VatCode>22</VatCode>
                    <Total><?php echo $art["totale-articolo"]; ?></Total>
                    <Stock>false</Stock>
                  </Row>
                <? } 
                if($spesedisped>0) { ?>
                  <Row>
                    <Code></Code>
                    <Description><?php echo $spedlabel; ?></Description>
                    <Qty>1</Qty>
                    <Um></Um>
                    <Price><?php echo $spesedisped; ?></Price>
                    <Discounts></Discounts>
                    <VatCode>22</VatCode>
                    <Total><?php echo $spesedisped; ?></Total>
                    <Stock>false</Stock>
                  </Row>
                <? } ?>
            </Rows>
          </Document>
        <? } ?>
      <? } ?>
    </Documents>
  </EasyfattDocuments>
<?php  
} else {
	header ("HTTP/1.1 400 Bad request");
	echo "Utente o password non validi";
	exit;
}
?>
