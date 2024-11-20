<?php
session_start();

include ("_docroot.php");

include (SERVER_DOCROOT . "logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$conn = $objDb->connection($objConfig);

global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);

if(isUserSystem()) $sortable="1"; else $sortable="-1";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<?php $objHtml->adminHead() ?>

<script>
$("document").ready(function(){  
  $("div.ewiz .ewiz-valore-nome").live("click",function(){
    $("div.ewiz-caratt-valori-fotogallery").html("");
    var rsId=$(this).attr("id");
    var rsLabel=$(this).attr("rsLabel");
    var rsCarNome=$("div.ewiz-bottom2 .ewiz-caratt-valori-content .ewiz-title").html();
    
    $("div.ewiz-caratt-abbinate").html("");
    
    $("div.ewiz-bottom2 .ewiz-caratt-valori-fotogallery-content .ewiz-title").html("Photogallery per '"+rsLabel+"'");
    $("div.ewiz-bottom2 .ewiz-caratt-abbinate-container .ewiz-title").html(rsCarNome+" "+rsLabel+" NON disponibile per:");
    
    var tblOptions2={
      'container': "div.ewiz-caratt-valori-fotogallery",
      'table': "fotogallery",
      'insert': '1',
      'tableParent': 'ecommerce_valori',
      'tableParentId': rsId,
      'colFilter': 'id,immagine_file,Ordinamento'
    };
    
    g_table2=new rsTable2(tblOptions2);
    g_table2._print(function(){return;});
    
    var tblOptions3={
      'container': "div.ewiz-caratt-abbinate",
      'table': "ecommerce_abbinamenti",
      'insert': '1',
      'tableParent': 'ecommerce_valori',
      'tableParentId': rsId,
      'colFilter': ''
    };
    
    g_table3=new rsTable2(tblOptions3);
    g_table3._print(function(){return;});
  });
  
  $("div.ewiz .ewiz-caratteristica").live("click",function(){
    $("div.ewiz-caratt-valori").html("");
    $("div.ewiz-caratt-valori-fotogallery").html("");
    $("div.ewiz-bottom2 .ewiz-title").html("");
    $("div.ewiz-caratt-abbinate").html("");
    
    var rsId=$(this).attr("id");
    var rsLabel=$(this).attr("rsLabel");
    
    $("div.ewiz-bottom2 .ewiz-caratt-valori-content .ewiz-title:first").html(rsLabel);
    
    var tblOptions3={
      'container': "div.ewiz-caratt-valori",
      'table': "ecommerce_valori",
      'insert': '1',
      'tableParent': 'ecommerce_caratteristiche',
      'tableParentId': rsId,
      'colFilter': '',
      'colLimit': 15
    };
    
    g_table3=new rsTable2(tblOptions3);
    g_table3._print(function(){return;});
  });
  
  $("div.ewiz div.rsStrutture[rsAjax!=-1] a.rsStrutture-a").live("mouseover",function(){
    if($(".rsStrutture-open").length==0) {
      $("div.ewiz .rsStrutture-edit").prepend("<div class='ez-fl ez-negmx ez-20'><div class='ez-box'><input class='rsStrutture-open' type='button' value='' title='apri scheda' /></div></div>");  
    }
  });
  
  $("div.ewiz div.rsStrutture[rsAjax!=-1] a.rsStrutture-a").live("click",function(){
    var rsId=$(this).attr("rsId");
    $("span.mod-briciole").remove();
    
    $.ajax({
     type: "POST",                       
     url: "rsAction.php",
     data: "rsGetBriciole=1&id="+rsId, 
     success: 
      function(msg){
        $("#modulo-titolo .modulo").append(" <span class='mod-briciole'>> "+"<b>"+msg+"</b></span>");
        $("div.ewiz-right").hide();    
      },
     error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
            }
    });
  });
  
  $("div.ewiz .rsStrutture-open").live("click",function(){
    var rsId=$(this).parents("li").attr("id");
    $("div.ewiz div.rsStrutture[rsAjax!=-1] div.rsStrutture-a-selected").removeClass("rsStrutture-a-selected");
    $("div.ewiz div.rsStrutture[rsAjax!=-1] li[id='"+rsId+"'] div.rsStrutture-a-text").addClass("rsStrutture-a-selected");
    
    $("div.ewiz-articolo").html("");
    $("div.ewiz-fotogallery").html("");
    $("div.ewiz-caratteristiche-lst").html("");
    $("div.ewiz-articoli-collegati").html("");
    $("div.ewiz-bottom2 .ewiz-title").html("");
    $("div.ewiz-caratt-valori").html("");
    $("div.ewiz-caratt-valori-fotogallery").html("");
    $("div.ewiz-caratt-abbinate").html("");
    
    var isLimitedUser=false;
    
    $.ajax({
     type: "POST",                       
     url: "rsAction.php",
     data: "rsGetMagazzinoArticoli=1&tableId="+rsId, 
     success: 
      function(msg){
        msg=explode(";",msg);
        if(msg[0]==2){
          var tblOptions={
            'container': "div.ewiz-articolo",
            'table': "magazzino_articoli",
            'insert': 1,
            'tableParent': 'categorie',
            'tableParentId': rsId,
            'insertId': msg[1],
            'colFilter': 'id,Codice,Descr1,Prezzo_cry,Ordine_minimo_cry,peso,sconto,sconto_reg,del_hidden,id_categorie_str_magazzino,aggiornato,id_ecommerce_caratt_list,articolo_ingombrante'
          };
        }else if(msg[0]==1){
            isLimitedUser=true;
            var tblOptions={
            'container': "div.ewiz-articolo",
            'table': "magazzino_articoli_tema",
            'insert': 1,
            'insertId': msg[1],
            'colFilter': 'id,id_magazzino_articoli,maggiorazione_utenti_registrati,maggiorazione_utenti_non_registrati,descrizione_editor,ultima_modifica'
          };
        }
        
        g_table=new rsTable2(tblOptions);
        g_table._insert(function(){return;});
        
        var tblOptions2={
          'container': "div.ewiz-fotogallery",
          'table': "fotogallery",
          'insert': '1',
          'tableParent': 'categorie',
          'tableParentId': rsId,
          'colFilter': 'id,immagine_file,Ordinamento'
        };
        
        g_table2=new rsTable2(tblOptions2);
        g_table2._print(function(){return;});
        
        var tblOptions3={
          'container': "div.ewiz-caratteristiche-lst",
          'table': "ecommerce_caratteristiche",
          'insert': '1',
          'tableParent': 'categorie',
          'tableParentId': rsId,
          'colFilter': ''
        };
        
        if(isLimitedUser==false){
          g_table3=new rsTable2(tblOptions3);
          g_table3._print(function(){return;});
        }
        
        var tblOptions4={
          'container': "div.ewiz-articoli-collegati",
          'table': "magazzino_articoli_collegati",
          'insert': '1',
          'tableParent': 'categorie',
          'tableParentId': rsId,
          'colFilter': 'id,id_categorie_str_magazzino,Ordinamento'
        };
        
        if(isLimitedUser==false){
          g_table4=new rsTable2(tblOptions4);
          g_table4._print(function(){return;});
        }  
        
        $("div.ewiz-right").show();
        
        $("span.mod-briciole").remove();
    
        $.ajax({
         type: "POST",                       
         url: "rsAction.php",
         data: "rsGetBriciole=1&id="+rsId, 
         success: 
          function(msg){
            $("#modulo-titolo .modulo").append(" <span class='mod-briciole'>> "+"<b>"+msg+"</b></span>");    
          },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
                }
        });  
      },
     error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
            }
    });
  });  
});
</script>
</head>
<body>
<div id="site">
	<div id="content">
		<?php $objHtml->adminLeft($conn, $intIdutente); ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("", ""); ?>
			<div id="body" style="padding:4px;padding-top:0px;padding-left:10px;">
			  <!-- Module 2A -->
        <div class="ez-wr ewiz">
          <div class="ez-fl ez-negmr ez-50 ewiz-magazzino">
            <div class="ez-box"><?php stampaStruttura("magazzino",$selected="",$useLayout="",$cat="",$edit="1",$urlRewrite="",$useAjax="",$flash="",$flashW="",$flashH="",$nmRel="-1",$sortable); ?></div>
          </div>
          <div class="ez-last ez-oh ewiz-right">
            <!-- Module 2A -->
            <div class="ez-wr">
              <div class="ez-fl ez-negmr ez-50 ewiz-articolo-container">
                <div class="ez-box ewiz-title">Generali</div>
                <div class="ez-box ewiz-articolo"></div>
              </div>
              <div class="ez-last ez-oh ewiz-fotogallery-container">
                <div class="ez-box ewiz-title">Photogallery</div>
                <div class="ez-box ewiz-fotogallery"></div>
              </div>
            </div>
            
            <!-- Module 2A -->
            <div class="ez-wr ewiz-bottom">
              <div class="ez-fl ez-negmr ez-50 ewiz-caratteristiche-container">
                <div class="ez-box ewiz-title">Caratteristiche</div>
                <div class="ez-box ewiz-caratteristiche-lst"></div>
              </div>
              <div class="ez-last ez-oh ewiz-articoli-collegati-container">
                <div class="ez-box ewiz-title">Articoli consigliati</div>
                <div class="ez-box ewiz-articoli-collegati"></div>
              </div>
            </div>
            
            <!-- Module 2A -->
            <div class="ez-wr ewiz-caratt-valori-container ewiz-bottom2">
              <div class="ez-fl ez-negmr ez-50 ewiz-caratt-valori-content">
                <div class="ez-box ewiz-title"></div>
                <div class="ez-box ewiz-caratt-valori"></div>
                <!-- Plain box -->
                <div class="ez-wr ewiz-caratt-abbinate-container">
                  <div class="ez-box ewiz-title"></div>
                  <div class="ez-box ewiz-caratt-abbinate"></div> 
                </div>
              </div>
              <div class="ez-last ez-oh ewiz-caratt-valori-fotogallery-content">
                <div class="ez-box ewiz-title">Photogallery</div>
                <div class="ez-box ewiz-caratt-valori-fotogallery"></div>
              </div>
            </div>
          </div>
        </div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>