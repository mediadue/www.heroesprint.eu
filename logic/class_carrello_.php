<?php
class Carrello {
  var $g_current;
  var $g_combi;
  var $g_ivainclusa;
  var $g_smallImgW;
  var $g_smallGalleryImgW;
  var $g_zoomImgW;
  var $g_jsCode;
   
  function Carrello() {
    $this->g_current="";
    $this->g_combi="";
    $this->g_ivainclusa=0;
    
    $this->g_smallImgW=350;
    $this->g_zoomImgW=1024;
    $this->g_smallGalleryImgW=80;
    
    $this->g_jsCode="";
    
    if(!isset($_POST['phpss']) && $_SESSION["userris_id"]>0) {
      $objphpss = new Session();
      $objphpss->create();
      $objphpss->save($_SESSION);  
    }
  }
  
  function ivaInclusa() {
    $rs=getTable("ecommerce_carrello","","attivo='1'");
    return $rs[0]['prezzi_iva_inclusa'];  
  }
  
  function countCart() {
    $c=0;
    if(!is_array($_SESSION['ecomm'])) return; 
    
    reset($_SESSION['ecomm']);
    while (list($key, $prodotto) = each($_SESSION['ecomm'])) { 
      $n=count($_SESSION['ecomm'][$key]);
      $magazzino=retRow("categorie",$key);
      if($magazzino!=false) {
        while (list($key2, $variante) = each($prodotto)) { 
          if($key2!=="ecomm_buffer") {
            $c++;
          } 
        } 
      } 
    }
    reset($_SESSION['ecomm']);
    return $c;  
  }
  
  function emptyCart() {
    if(!is_array($_SESSION['ecomm'])) return; 
    reset($_SESSION['ecomm']);
    while (list($key, $prodotto) = each($_SESSION['ecomm'])) { 
      $magazzino=retRow("categorie",$key);
      if($magazzino!=false) {
        unset($_SESSION['ecomm'][$key]);
        if(count($_SESSION['ecomm'][$key])==0) unset($_SESSION['ecomm'][$key]); 
      } 
    }
    reset($_SESSION['ecomm']);  
  }
 
  function appendElement($id,$appendid) {
    ?>
    <?php ob_start(); ?>
    <script>
    $(document).ready(function(){
      $("<?php echo $appendid; ?>").append($("<?php echo $id; ?>"));    
    });
    </script>
    <?php
    $this->g_jsCode.=ob_get_contents(); 
    ob_end_clean();  
  }
  
  function replaceElement($id,$replaceid) {
    ?>
    <?php ob_start(); ?>
    <script>
    $(document).ready(function(){
      $("<?php echo $replaceid; ?>").replaceWith($("<?php echo $id; ?>"));    
    });
    </script>
    <?php
    $this->g_jsCode.=ob_get_contents(); 
    ob_end_clean();  
  }
  
  function setOptions($p_totale=false,$p_vai_cassa=0,$url_vai_cassa="",$levelForName=0,$menid="-1"){
    $_SESSION['ecomm']['viewcart']['p_totale']=$p_totale;
    $_SESSION['ecomm']['viewcart']['p_vai_cassa']=$p_vai_cassa;
    $_SESSION['ecomm']['viewcart']['url_vai_cassa']=$url_vai_cassa;
    $_SESSION['ecomm']['viewcart']['levelForName']=$levelForName;
    $_SESSION['ecomm']['viewcart']['menid']=$menid;    
  }
  
  function viewCart($p_totale=false,$p_vai_cassa=0,$url_vai_cassa="",$levelForName=0,$menid="-1") {
    $objUtility=new Utility;
    
    if(!is_array($_SESSION['ecomm'])) $_SESSION['ecomm']=array();
    
    if($this->countCart()==0) {
      return false;
    }
    
    $_SESSION['ecomm']['viewcart']['p_totale']=$p_totale;
    $_SESSION['ecomm']['viewcart']['p_vai_cassa']=$p_vai_cassa;
    $_SESSION['ecomm']['viewcart']['url_vai_cassa']=$url_vai_cassa;
    $_SESSION['ecomm']['viewcart']['levelForName']=$levelForName;
    $_SESSION['ecomm']['viewcart']['menid']=$menid; 
    
    ?>
    <!-- Plain box -->
    <div class="ez-wr ecomm_viewcart">
      <?php ob_start(); ?>
      <script>
      $(document).ready(function(){
        var myqta=$('div.ecomm_viewcart input.qta');
        
        $('div.ecomm_viewcart a.delete').click(function(){
          if(!confirm("Confermare la rimozione dal carrello?")) return false;
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_prodotto' value='"+$(this).attr('tag1')+"' />");
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_variante' value='"+$(this).attr('tag2')+"' />");
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_del' value='1' />");
          $('#ecomm_form_viewcart').submit();
          return false;    
        });
        
        $('div.ecomm_viewcart a.refresh').click(function(){
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_prodotto' value='"+myqta.attr('tag1')+"' />");
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_variante' value='"+myqta.attr('tag2')+"' />");
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_updq' value='1' />");
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_qta' value='"+myqta.val()+"' />");
          $('#ecomm_form_viewcart').submit();
          return false;    
        });
        
        $('div.ecomm_viewcart input.qta').keypress(function(event){
          if(event.keyCode == '13') {
            $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_prodotto' value='"+$(this).attr('tag1')+"' />");
            $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_variante' value='"+$(this).attr('tag2')+"' />");
            $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_updq' value='1' />");
            $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_qta' value='"+$(this).val()+"' />");
            $('#ecomm_form_viewcart').submit();
          }   
        });            
      }); 
      </script> 
      <?php
      $this->g_jsCode.=ob_get_contents(); 
      ob_end_clean();
      ?>                  
      <FORM action="" method="post" id="ecomm_form_viewcart" name="ecomm_viewcart">
      <input type="hidden" name="ecomm_post" value="2" />
      <?php 
      $tot=0;
      while (list($key, $prodotto) = each($_SESSION['ecomm'])) { 
        $n=count($_SESSION['ecomm'][$key]);
  
        $magazzino=retRow("categorie",$key);
        if($magazzino!=false) { 
          $layout=retRow("gestione_layout",$magazzino['id_gestione_layout']);
          $nome_prodotto=ln($magazzino['nome']);
          $_SESSION['ecomm_levelforname']=$levelForName;
          for($nn=0;$nn<$levelForName;$nn++) {
            if($nn==0) $padre=getPadre($key);
            if($nn>0 && $padre!=false) $padre=getPadre($padre['id']);
            if($padre!=false) {
              $nome_prodotto=$padre['nome']." ".$nome_prodotto;    
            }
          }
          if($magazzino['immagine_file']!=0) {
            $foto_prodotto=retFile($magazzino['immagine_file']);
          }else{
            $foto_prodotto=$objUtility->getPathBackofficeResources()."nofoto.jpg";  
          }
          while (list($key2, $variante) = each($prodotto)) { 
            if($key2!=="ecomm_buffer") {
              $link=$variante['ecomm_link'];
              if($link=="") $link=$layout['file']."?menid=$key&ecomm_combi=$key2";
              
              $image=$variante['ecomm_image'];
              if($image!="") $foto_prodotto=$image;
              
              $quantita=$variante['ecomm_quantita'];
              $prezzo_finale=$variante['ecomm_prezzo_finale'];
              $tot=$tot+$prezzo_finale;
              
              ?>
              <!-- Module 2A -->
              <div class="ez-wr">
                <div class="ez-fl ez-negmr ez-50 vc-img-prd">
                  <div class="ez-box"><img src='<?=$foto_prodotto?>'></div>
                </div>
                <div class="ez-last ez-oh">
                  <!-- Layout 1 -->
                  <div class="ez-wr">
                    <div class="ez-box articolo"><a href="<?php echo $link; ?>" ><?=$nome_prodotto?></a></div>
                    <div class="ez-box quantita"><?php echo ln("Quantit&agrave;");?>: <input type ="text" class='qta' name='qta' value="<?=$quantita?>" tag1="<?=$key?>" tag2="<?=$key2?>" MAXLENGTH=7 ><a class='refresh'  href='' tag1="<?=$key?>" tag2="<?=$key2?>"><img src="<?php echo $objUtility->getPathBackofficeResources()."refresh.png"; ?>" title="<?php echo ln("aggiorna");?>" class="img_refr" /></a><a class='delete'  href='' tag1="<?=$key?>" tag2="<?=$key2?>"><img src="<?php echo $objUtility->getPathBackofficeResources()."delete.gif"; ?>" title="<?php echo ln("rimuovi il prodotto dal carrello");?>" class="img_del" /></a></div>
                    <!-- Module 2A -->
                    <div class="ez-wr box-prezzo">
                      <div class="ez-fl ez-negmr ez-50">
                        <div class="ez-box "><?php echo ln("Prezzo");?>:</div>
                      </div>
                      <div class="ez-last ez-oh prezzo">
                        <div class="ez-box">&euro; <?php echo currencyITA($prezzo_finale); ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Plain box -->
              <div class="ez-wr separator">
                <div class="ez-box"></div> 
              </div>
      			<? } ?>			
          <? } ?>
        <? } ?>
      <? } ?>
      
      <?php if($p_totale!=false) { ?>
      <!-- Plain box -->
      <div class="ez-wr">
        <!-- Module 2A -->
        <div class="ez-wr box-totale">
          <div class="ez-fl ez-negmr ez-50">
            <div class="ez-box prezzo-totale"><?php echo ln("Totale");?>:</div>
          </div>
          <div class="ez-last ez-oh">
            <div class="ez-box prezzo">&euro; <?php echo currencyITA($tot); ?></div>
          </div>
        </div>
      </div>
      <? } ?>
      <?php if($p_vai_cassa<$tot) { ?>
      <!-- Plain box -->
      <div class="ez-wr vai-alla-cassa">
        <!-- Module 2A -->
        <div class="ez-wr">
          <div class="ez-fl ez-negmr ez-50">
            <div class="ez-box" style="">&nbsp;&bull;&nbsp;&nbsp;<a href='<?php echo $url_vai_cassa;?>?ecomm_riepilogo=1&menid=<?=$menid?>'><?php echo ln("vai alla cassa");?></a></div>
          </div>
          <div class="ez-last ez-oh">
            <div class="ez-box" style="padding-top:2px;"><img src="<?php echo $objUtility->getPathBackofficeResources()."arrow_right.gif"; ?>" title="<?php echo ln("vai alla cassa");?>" class="img_go" /></div>
          </div>
        </div>
      </div>
      <? } ?> 
      </FORM>
    </div>
    <?
  }
  
  function riepilogoCarrello_acquista() {
    $objUtility=new Utility;
    ob_start(); ?>
    <script>
    function riepilogoCarrello_acquista() {
      var g_obj=this;
      
      this.checkTipo=function() {
      	var tipo = $("#userTipo").val();
        var naz = $("#nazione").val(); 
      	if (tipo == 'privato')
      	{
      		$('#ragpiva').css('display', 'none');
      		$('#referente').css('display', 'none');
      		$('#referente2').css('display', 'none');
      		$('#referentep').css('display', '');
      		$('#referente2p').css('display', '');
      		$('#codfis').css('display', 'none');
      		$('#codfisp').css('display', '');
          if(naz==106) {
            $('#coddest').val('0000000');
          }else{
            $('#coddest').val('YYYYYYY'); 
          }
          $('#coddest').attr('readonly','readonly');
          $('#coddest').removeClass('hidden');
          $("div.cart-user-register .lbl-codpec").html("<?php echo ln("codice destinatario"); ?>");
          $('#pec').addClass('hidden');
          $('#pec').val("");
          $('div.cart-user-register .rpg-nocoddest').addClass('hidden');
          $('div.cart-user-register .rpg-nopec').addClass('hidden'); 
      	}
      	else
      	{
      		$('#ragpiva').css('display', '');
      		$('#referente').css('display', '');	
      		$('#referente2').css('display', '');
      		$('#referentep').css('display', 'none');	
      		$('#referente2p').css('display', 'none');
      		$('#codfis').css('display', '');
      		$('#codfisp').css('display', 'none');
          if(naz==106) {
            $('div.cart-user-register .rpg-nocoddest').removeClass('hidden');
            $('div.cart-user-register .rpg-nopec').addClass('hidden'); 
            if($('#coddest').val()=='0000000' || $('#coddest').val()=='YYYYYYY') $('#coddest').val('');
            $('#coddest').removeAttr('readonly');
            $('#coddest').removeClass('hidden');
            $('#pec').addClass('hidden');
            $('#pec').val("");
          }else{
            $('#coddest').val('YYYYYYY');
            $('#coddest').attr('readonly','readonly');
            $('#coddest').removeClass('hidden');
            $("div.cart-user-register .lbl-codpec").html("<?php echo ln("codice destinatario"); ?>");
            $('#pec').addClass('hidden');
            $('#pec').val("");
            $('div.cart-user-register .rpg-nocoddest').addClass('hidden');
            $('div.cart-user-register .rpg-nopec').addClass('hidden');  
          }		
      	}	
      }
      
      $("#userTipo").change(function(){
        g_obj.checkTipo();  
      });
      
      $("#provincia").change(function(){
        var provincia=$(this).val();
        $("div.rsLoading3").show();
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "ecomm_sp_provincia2="+provincia,
         success: function(msg){
          if(msg==""){
            msg="<select id='comune' name='comune' class='input2 select form-control'><option value=''></option></select>"
          }
          
          $("#comune").replaceWith(msg);
          $("div.rsLoading3").hide();
         },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      });
      
      $("div.cart-user-register .rpg-nocoddest").live("click",function(){
        $("#coddest").addClass("hidden");
        $("#coddest").val("");
        $("div.cart-user-register .rpg-nocoddest").addClass("hidden"); 
        $("div.cart-user-register #pec").removeClass("hidden");
        $("div.cart-user-register .rpg-nopec").removeClass("hidden");
        
        $("div.cart-user-register .lbl-codpec").html("<?php echo ln("PEC"); ?>");
        
        return false;  
      });
      
      $("div.cart-user-register .rpg-nopec").live("click",function(){
        $("#coddest").removeClass("hidden"); 
        $("div.cart-user-register .rpg-nocoddest").removeClass("hidden");
        $("div.cart-user-register #pec").addClass("hidden");
        $("div.cart-user-register #pec").val("");
        $("div.cart-user-register .rpg-nopec").addClass("hidden");
        
        $("div.cart-user-register .lbl-codpec").html("<?php echo ln("codice destinatario"); ?>");
        
        return false;  
      });
      
      $("div.cart-user-register select[name=nazione]").change(function(){
        var naz = $(this).val();
        if(naz!=106) {
          $.ajax({
           type: "POST",
           url: "rsAction.php",
           data: "ecomm_sp_comune=-1&ecomm_sp_nazione="+naz+"&ecomm_sp_regione_estera=-2",
           success: function(msg){
                      $("div.cart-user-register .rpg-comune").hide();
                      $("div.cart-user-register .rpg-provincia").hide();
                      $("div.cart-user-register .rpg-provincia-estera").show();
                      $("div.cart-user-register .rpg-nocoddest").addClass("hidden");
                      $("div.cart-user-register .rpg-nopec").addClass("hidden");
                      $("#coddest").val("YYYYYYY");
                      $("#coddest").attr("readonly","readonly");
                      $("#coddest").removeClass("hidden");
                      $("div.cart-user-register .lbl-codpec").html("<?php echo ln("codice destinatario"); ?>");
                      $("#pec").addClass("hidden");
                      $("#pec").val(""); 
                      if($(msg).find("select").length>0) {
                        $("div.cart-user-register .rpg-regione-estera .value select").remove();
                        $("div.cart-user-register .rpg-regione-estera .value").append($(msg).find("select"));
                        $("div.cart-user-register .rpg-regione-estera .value select").addClass("input2");
                        $("div.cart-user-register .rpg-regione-estera").show();
                      }else{
                    	  $("div.cart-user-register .rpg-regione-estera .value select").val(0);
                    	  $("div.cart-user-register .rpg-regione-estera").hide();	
                      }
                    },
           error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                  }
          });
              
        }else{
          $("div.cart-user-register .rpg-comune").show();
          $("div.cart-user-register .rpg-provincia").show();
          $("div.cart-user-register .rpg-regione-estera .value select").val(0);
          $("div.cart-user-register .rpg-regione-estera").hide();
          $("div.cart-user-register .rpg-provincia-estera").hide(); 
          if($("#userTipo").val()=="privato") {
            $("div.cart-user-register .rpg-nocoddest").addClass("hidden");
            $("div.cart-user-register .rpg-nopec").addClass("hidden");
            $("#coddest").val("0000000");
            $("#coddest").attr("readonly","readonly");
            $("#coddest").removeClass("hidden");
            $("div.cart-user-register .lbl-codpec").html("<?php echo ln("codice destinatario"); ?>");
            $("#pec").addClass("hidden");
            $("#pec").val("");
          }else{
            $("div.cart-user-register .rpg-nocoddest").removeClass("hidden");
            $("div.cart-user-register .rpg-nopec").addClass("hidden");
            if($("#coddest").val()=="0000000" || $("#coddest").val()=="YYYYYYY") $("#coddest").val("");
            $("#coddest").removeAttr("readonly");
            $("#coddest").removeClass("hidden");
            $("div.cart-user-register .lbl-codpec").html("<?php echo ln("codice destinatario"); ?>"); 
            $("#pec").addClass("hidden");
            $("#pec").val(""); 
          }
        }
      });  
   
      $("#ecomm_riepilogo_acquista form").bind("submit",function() {
        var eform=$(this);
        
        var einp=$(this).find("input[name='mail']");
        var cfinp=$(this).find("input[name='cf']");
        var pivainp=$(this).find("input[name='piva']");
        var tipoinp=$(this).find("select[name='tipo']");
        var nazioneinp=$(this).find("select[name='nazione']");
        
        var verCampi={}
        verCampi["email"]=einp.val();
        verCampi["cf"]=cfinp.val();
        verCampi["piva"]=pivainp.val();
        verCampi["tipo"]=tipoinp.val();
        verCampi["nazione"]=nazioneinp.val();
        
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "ecomm_ver_email="+JSON.stringify(verCampi),
         success: function(msg){
                    var tret=true;
                    if(msg=="1") {
                      alert("<?php echo ln("L'indirizzo email inserito risulta già registrato"); ?>.");
                      einp.focus();
                      einp.css("background-color","orange");
                      
                      einp.blur(function() {
                        einp.css("background-color","");  
                      });
                      return false;
                    }else if(msg=="-1"){
                      alert("<?php echo ln("L'indirizzo email non è valido"); ?>.");
                      einp.focus();
                      einp.css("background-color","orange");
                      
                      einp.blur(function() {
                        einp.css("background-color","");  
                      });
                      return false;    
                    }else if(msg=="-2"){
                      alert("<?php echo ln("La Partita IVA non è corretta"); ?>.");
                      pivainp.focus();
                      pivainp.css("background-color","orange");
                      
                      pivainp.blur(function() {
                        pivainp.css("background-color","");  
                      });
                      return false;  
                    }else if(msg=="-3"){
                      alert("<?php echo ln("Il Codice Fiscale non è corretto"); ?>.");
                      cfinp.focus();
                      cfinp.css("background-color","orange");
                      
                      cfinp.blur(function() {
                        cfinp.css("background-color","");  
                      });
                      return false;   
                    }
                    
                    $("#ecomm_riepilogo_acquista form input:visible").each(function() {
                      if($(this).val()=="" || ($(this).attr('type')=="checkbox" && !$(this).is(":checked") ) ) {
                        if($(this).attr("name")!="procedi") {
                          if($(this).attr('type')!="checkbox") {
                            alert('<?php echo ln("Inserire un valore per il campo obbligatorio");?>');
                          } else {
                            alert('<?php echo addslashes(html_entity_decode(ln("Risulta obbligatorio leggere e accettare la PRIVACY POLICY e le CONDIZIONI DI VENDITA"),ENT_QUOTES, 'UTF-8')); ?>');
                          }
                          $(this).focus();
                          $(this).css("background-color","orange");
                          
                          $(this).blur(function() {
                            $(this).css("background-color","");  
                          });
                          tret=false;
                          return tret;
                        }
                      }
                    }); 
                    if(tret) {
                      eform.unbind("submit");
                      eform.submit();
                      return false;
                    }  
                  },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });

        return false;
      });
      
      g_obj.checkTipo();
    }
    
    if(typeof(rsRiepilogoCarrello_acquista)=="undefined") eval("var rsRiepilogoCarrello_acquista='';");
    
    function ecomm_initRiepilogoCarrello_acquista() {
      if(rsRiepilogoCarrello_acquista=="") {
        rsRiepilogoCarrello_acquista=1;
        var tmpCart=new riepilogoCarrello_acquista();
      }  
    }
    
    $(document).ready(function(){
      ecomm_initRiepilogoCarrello_acquista();  
    });
    </script>
    <?php
    $this->g_jsCode.=ob_get_contents(); 
    ob_end_clean();
    ?>
    <!--<div class="rsLoading">Loading...</div>-->
    <div id="ecomm_riepilogo_acquista" style="display:none;">
      <div class="ecomm_riepilogo_acquista-tit"><?php echo ln("INSERISCI I TUOI DATI"); ?></div>
      <form action="" method="post" >
      	<div class="cart-user-register  form-inline">
          <div class="row">
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("tipo");?></div>
      					<div class="value">
      						<select name="tipo" id="userTipo" class="input2 select form-control">
      							<option value="privato"><?php echo ln("privato");?></option>
      							<option value="azienda"><?php echo ln("azienda");?></option>
      							<option value="associazione"><?php echo ln("associazione");?></option>
      						</select>
      					</div>
      				</div>
      			</div>
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz">&nbsp;</div>
      					<div class="value">&nbsp;</div>
      				</div>
      			</div>
      		</div>
      		<div class="row" id="ragpiva">
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("ragione sociale");?></div>
      					<div class="value"><input name="rag" class="input2 form-control" maxlength="60"/></div>
      				</div>
      			</div>
      			
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("partita iva");?></div>
      					<div class="value"><input name="piva" class="input2  form-control" maxlength="11"/></div>
      				</div>
      			</div>
      		</div>
      		<div class="row">
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><div id='referentep'><?php echo ln("cognome");?></div><div id='referente'><?php echo ln("cognome");?>&nbsp;<?php echo ln("referente");?></div></div>
      					<div class="value"><input name="cognome" class="input2 form-control" maxlength="60"/></div>
      				</div>
      			</div>
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><div id='referente2p'><?php echo ln("nome");?></div><div id='referente2'><?php echo ln("nome");?>&nbsp;<?php echo ln("referente");?></div></div>
      					<div class="value"><input name="nome" class="input2 form-control" maxlength="60"/></div>
      				</div>
      			</div>
      		</div>
      	
      		<div class="row">
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><div id='codfisp'><?php echo ln("codice fiscale");?></div><div id='codfis'><?php echo ln("codice fiscale azienda");?></div></div>
      					<div class="value"><input name="cf" class="input2 form-control" maxlength="16"/></div>
      				</div>
      			</div>
      			
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("indirizzo");?></div>
      					<div class="value"><input name="indirizzo" class="input2 form-control" maxlength="48"/></div>
      				</div>
      			</div>
      		</div>
      		<div class="row">
      		<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("cap");?></div>
      					<div class="value"><input  name="capcap" class="input2 form-control" maxlength="12"/></div>
      				</div>
      			</div>
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("località");?></div>
      					<div class="value"><input name="citta" class="input2 form-control" maxlength="36"/></div>
      				</div>
      			</div>
      			
      		</div>
      		<div class="row">
      			<?php if($_SESSION['ecomm']['sp_nazione']=="") $_SESSION['ecomm']['sp_nazione']="106";  ?>
      			<div class="col-sm-6 rpg-provincia" <?php if($_SESSION['ecomm']['sp_nazione']!="106")  echo "style='display:none;'"; ?> >
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("provincia");?></div>
      					<div class="value">
                  <?php comboBox("province",$field1="sigla",$field2="",$selected="84",$multiple="",$onchange="",$echoId="",$nome="provincia",$where="", $class="input2 select form-control",$ordine=""); ?>
                </div>
      				</div>
      			</div>
      			<div class="col-sm-6 rpg-provincia-estera" <?php if($_SESSION['ecomm']['sp_nazione']=="106")  echo "style='display:none;'"; ?>>
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("provincia");?></div>
      					<div class="value"><input name="provincia_estera" class="input2 form-control" maxlength="6"/></div>
      				</div>
      			</div>
            <div class="col-sm-6 rpg-comune" <?php if($_SESSION['ecomm']['sp_nazione']!="106")  echo "style='display:none;'"; ?> >
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("comune");?></div>
      					<div class="value">
                  <?php comboBox("comuni",$field1="comune",$field2="",$selected="",$multiple="",$onchange="",$echoId="",$nome="comune",$where="id_province='84'", $class="input2 select form-control",$ordine=""); ?>
                </div>
      				</div>
      			</div>
      		</div>
      		<div class="row">
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("telefono");?></div>
      					<div class="value"><input name="telefono" class="input2 form-control" maxlength="24"/></div>
      				</div>
      			</div>
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("nazione");?></div>
      					<div class="value">
      						<?php comboBox("nazioni",$field1="naz_eng",$field2="",$selected=$_SESSION['ecomm']['sp_nazione'],$multiple="",$onchange="",$echoId="",$nome="nazione",$where="", $class="input2 select  form-control",$ordine="") ?>
      					</div>
      				</div>
      			</div>
      		</div>   
          
          <div class="row">
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz lbl-codpec"><?php echo ln("codice destinatario");?></div>
      					<div class="value">
                  <input id="coddest" name="coddest" class="input2 form-control" maxlength="7"/>
                  <input id="pec" name="pec" class="input2 form-control" maxlength="60"/>
                  <a href="#" class="rpg-nocoddest"><?php echo ln("Non ho il codice");?></a>
                  <a href="#" class="rpg-nopec"><?php echo ln("Non ho la PEC");?></a>
                </div>
      				</div>
      			</div>
            <div class="col-sm-6" style="display:none;">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("PEC");?></div>
                <div class="value"></div>
      				</div>
      			</div>
      		</div>
          
      		<div class="row">
      			<div class="col-sm-6">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("email");?></div>
      					<div class="value"><input name="mail" class="input2 form-control" maxlength="60"/></div>
      				</div>
      			</div>
            <div class="col-sm-6 rpg-regione-estera" style="display:none;">
      				<div class="elemento form-group">
      					<div class="labelz"><?php echo ln("regione estera");?></div>
                <div class="value"></div>
      				</div>
      			</div>
      		</div>
      	</div><br>
      	
      	<?php 
        $policy=getTable("ecommerce_testi","","nome='privacy policy' AND attivo='1'");
        $condizioni=getTable("ecommerce_testi","","nome='condizioni generali di vendita' AND attivo='1'");
        
        $policy[0]['testo_editor']=replaceEcomerceMarkers(ln($policy[0]['testo_editor']));
        $condizioni[0]['testo_editor']=replaceEcomerceMarkers(ln($condizioni[0]['testo_editor']));
        
        ?>
      	<div style="font-size:10px" >* <?php echo ln("tutti i campi sono obbligatori");?>.</div>
      	<br>
        <div class="cart-policy"><?php echo $policy[0]['testo_editor']; ?></div>
      	<div class="accept"><input type="checkbox" name="a1" value="1"/><?php echo ln("accetto");?></div>
        
        <div class="cart-policy"><?php echo $condizioni[0]['testo_editor']; ?></div>
      	<div class="accept"><input type="checkbox" name="a2" value="1"/><?php echo ln("accetto");?></div>
      	
      	<br>
      	<div style="text-align:center;">
      		<input type='image' src='<?php echo $objUtility->getPathBackofficeResources()."proc.png"; ?>' value='<?php echo ln("procedi");?>' title='<?php echo ln("procedi");?>' name='procedi'>
      	</div>
      </form>
    </div>
  <?
  }
  
  function validateRowIVA($id,$idnazione="",$idcomune="",$idregione_estera="") {
    $geo=$this->retGeografia($idnazione,$idcomune,$idregione_estera);
      
    $nazione=$geo['nazione'];
    $comune=$geo['comune'];
    $provincia=$geo['provincia'];
    $regione=$geo['regione'];
    $regione_estera=$geo['regione_estera'];
    $zona=$geo['zona'];
    
    $tab=getTable("ecommerce_iva_localita","","(id_regioni_estere='".$regione_estera['id']."')");
    if(count($tab)==0) {
      $tab=getTable("ecommerce_iva_localita","","(id_ecommerce_sp_zone='".$zona['id']."')");
      if(count($tab)==0) {
        $tab=getTable("ecommerce_iva_localita","","(id_comuni='".$comune['id']."')");
        if(count($tab)==0) {
          $tab=getTable("ecommerce_iva_localita","","(id_province='".$provincia['id']."')");
          if(count($tab)==0) {
            $tab=getTable("ecommerce_iva_localita","","(id_regioni='".$regione['id']."')");
            if(count($tab)==0) {
              $tab=getTable("ecommerce_iva_localita","","(id_nazioni='".$nazione['id']."')");  
            }  
          }  
        }
      }
    }                 
    
    while (list($key, $row) = each($tab)) {
      $tmprowiva=Table1ByTable2("ecommerce_iva","ecommerce_iva_localita",$row['id'],"","");
      if($tmprowiva[0]['id']==$id) return true;
    }
    
    return false;
  }
  
  function tabellaIVA($idnazione="",$idcomune="",$idregione_estera="") { 
    if($_SESSION['ecomm']['IVA']==0) $_SESSION['ecomm']['IVA']=22; 
    $rsiva=getTable("ecommerce_iva","Ordinamento ASC","attivo='1'");
    while (list($keyiva, $rowiva) = each($rsiva)) {
      $is_validate=$this->validateRowIVA($rowiva['id'],$idnazione,$idcomune,$idregione_estera);
      
      if($is_validate) {
        if($rowiva['intestazione']==1) { ?>
          <div class="ez-box iva-tab-row<?=$keyiva?>" <?php if($rowiva['visibile']!=1) echo "style='display:none;'"; ?> >
            <? echo tinybug(ln($rowiva['riga_editor'])); ?>
          </div><?
        } 
  
        if($rowiva['selezionabile']==1) { ?>
          <div class="ez-box iva-tab-row<?=$keyiva?>" <?php if($rowiva['visibile']!=1) echo "style='display:none;'"; ?> >
            <input type="radio" name="iva_option" id="<?=$rowiva['id']?>" value="<?=$rowiva['valore']?>" <?php if($rowiva['predefinito']==1 && $_SESSION['ecomm']['IVA']=="" ) echo "checked"; ?> <?php if($_SESSION['ecomm']['IVA']==$rowiva['valore'] ) echo "checked"; ?> >
      		  <label for="<?=$rowiva['id']?>" style="float:left;"><?php echo tinybug(ln($rowiva['riga_editor']))?></label>
          </div><?
        } 
         
        if($rowiva['descrittiva']==1) { ?>
          <div class="ez-box iva-tab-row<?=$keyiva?>" <?php if($rowiva['visibile']!=1) echo "style='display:none;'"; ?>>
            <? echo tinybug(ln($rowiva['riga_editor'])); ?>
          </div>
        <? } ?>
      <? } ?>
    <? } 
  }
  
  function riepilogoCarrello() {
    $objUtility=new Utility;
    $objHtml=new Html;
    $objConfig = new ConfigTool();    
    
    $useTheme=$objConfig->get("use_themes");    
        
    if(!is_array($_SESSION['ecomm'])) $_SESSION['ecomm']=array();
    
    if($this->countCart()==0) {
      ?><div id="carrello_vuoto"><?php echo ln("Il carrello è vuoto");?>.</div><?
      return false;
    }
     
    $cols_carrello=getTable("ecommerce_carrello","","attivo='1'"); 
    $cols_carrello=$cols_carrello[0];
    $cols_count=0;
    ?>
    <div  class="tbl-ecomm-riepilogo-cnt" id="ecomm_riepilogo" style="display:none;">
      <table class="table table-hover table-condensed tbl-ecomm-riepilogo" border='0' cellpadding='0' cellspacing='0'>	
      <thead>
        <tr class="head">
        <td style="width:10%;">
        <?php echo ln("Azioni"); ?>
        <?php /*
          <?php if($cols_carrello['modifica']==1) {$cols_count++; ?><td width='40' class="ecomm-td-mod-lbl"><?php echo ln("modifica"); ?></td><? } ?>
          <?php if($cols_carrello['clona']==1) {$cols_count++; ?><td width='40' class="ecomm-td-clona-lbl"><?php echo ln("clona"); ?></td><? } ?>
          <?php if($cols_carrello['elimina']==1) {$cols_count++; ?><td width='40' class="ecomm-td-del-lbl"><?php echo ln("elimina"); ?></td><? } ?>
          */ ?>
          <?php if($cols_carrello['immagine']==1) {$cols_count++; ?><td style="width:5%;">&nbsp;</td><? } ?>
          <?php if($cols_carrello['codice']==1) {$cols_count++; ?><td  style="width:5%;" class="ecomm-td-cod-lbl"><?php echo ln("cod. articolo"); ?></td><? } ?>
          <?php if($cols_carrello['nome']==1) {$cols_count++; ?><td  style="width:50%;" class="ecomm-td-descr-lbl"><?php echo ln("descrizione"); ?></td><? } ?>
          <?php if($cols_carrello['prezzo_unitario']==1) {$cols_count++; ?><td  style="width:5%;" class="ecomm-td-prz-lbl"><?php echo ln("prezzo un."); ?>&nbsp;&euro;</td><? } ?> 
          <?php if($cols_carrello['sconto']==1) {$cols_count++; ?><td  style="width:5%;" class="ecomm-td-sconto-lbl"><?php echo ln("sconto"); ?></td><? } ?>
          <?php if($cols_carrello['quantita']==1) {$cols_count++; ?><td  style="width:5%;" class="ecomm-td-quantita-lbl"><?php echo ln("quantità"); ?></td><? } ?>
          <?php if($cols_carrello['aggiunte_detrazioni']==1) {$cols_count++; ?><td  style="width:5%;" class="ecomm-td-agg-lbl"><?php echo ln("aggiunte/detrazioni"); ?>&nbsp;&euro;</td><? } ?>
            <td  style="width:8%;" class="ecomm-td-lbls">&nbsp;</td>
          <?php if($cols_carrello['totale']==1) {$cols_count++; ?><td  style="width:8%;" class="ecomm-td-tot-lbl text-right"><?php echo ln("totale"); ?>&nbsp;&euro;</td><? } ?>
        
        </td>
        </tr>	
        </thead>
        <tbody>
        <form action="" method="post" id="ecomm_form_viewcart" name="ecomm_viewcart"> 		  	 		
          <input type="hidden" name="ecomm_post" value="2" />
          <?php 
          $tot=0;
          $totPeso=0;
          $tot_sc=0;
          $ivaTot=0;
          $totali=$this->totale();
          reset($_SESSION['ecomm']);
          while (list($key, $prodotto) = each($_SESSION['ecomm'])) { 
            $n=count($_SESSION['ecomm'][$key]);
            $magazzino=retRow("categorie",$key);
            if($magazzino!=false) {
              $layout=retRow("gestione_layout",$magazzino['id_gestione_layout']);
              $nome_prodotto=ln($magazzino['nome']);
              /*
              for($nn=0;$nn<$_SESSION['ecomm_levelforname'];$nn++) {
                if($nn==0) $padre=getPadre($key);
                if($nn>0 && $padre!=false) $padre=getPadre($padre['id']);
                if($padre!=false) {
                  $nome_prodotto=$padre['nome']." ".$nome_prodotto;    
                }
              }
              */
              if($useTheme=="1"){
                $tmpl_products=getTable("tmpl_products","","attivo=1 AND id_categorie_str_magazzino='".$key."'");  
                $foto_prodotto=retFile($tmpl_products[0]['immagine_file'],35);                
              }
                            
              if(!$foto_prodotto) $foto_prodotto=retFile($magazzino['immagine_file'],35);
              if(!$foto_prodotto) {
                $gallery=Table2ByTable1("categorie","fotogallery",$key,"attivo='1'","Ordinamento ASC LIMIT 1");
                $foto_prodotto=retFile($gallery[0]['immagine_file'],35);
              }
              
              if(!$foto_prodotto) $foto_prodotto=$objUtility->getPathBackofficeResources()."nofoto.jpg";
              $ecommerce_generali=getTable("magazzino_articoli","","(id_categorie_str_magazzino='$key' AND Prezzo_cry>0 AND del_hidden='0')");
              $ord_min=$ecommerce_generali[0]['Ordine_minimo_cry'];
              $prezzo=$ecommerce_generali[0]['Prezzo_cry'];
              $peso=$ecommerce_generali[0]['peso'];
              $sconto=$ecommerce_generali[0]['sconto'];
              if($_SESSION["userris_id"]>0) {
                $sconto=$this->scontoUser($_SESSION["userris_id"],$key);
                if($sconto==0) $sconto=$ecommerce_generali[0]['sconto_reg']; 
              } 
              
              $codice=$ecommerce_generali[0]['Codice'];
              $iva_perc=$ecommerce_generali[0]['IVA'];
              if($iva_perc==0) $iva_perc=22;
              
              while (list($key2, $variante) = each($prodotto)) { 
                if($key2!=="ecomm_buffer") {
                  $link=$variante['ecomm_link'];
                  if($link=="") $link=$objUtility->getPathRoot().$layout['file']."?menid=$key&ecomm_combi=$key2";
                  
                  $image=$variante['ecomm_image'];
                  if($image!="") {
                    $foto_prodotto=$objUtility->getPathRoot().$image;
                  }
                  
                  if(isset($variante['ecomm_codice']) && $variante['ecomm_codice']!="") $codice=$variante['ecomm_codice'];
                  if(isset($variante['ecomm_prezzo']) && $variante['ecomm_prezzo']!="") $prezzo=$variante['ecomm_prezzo'];
                  //if(isset($variante['ecomm_sconto'])&& $variante['ecomm_sconto']!="") $sconto=$variante['ecomm_sconto'];
                  if(isset($variante['ecomm_peso'])&& $variante['ecomm_peso']!="") $peso=$variante['ecomm_peso'];
                  if(isset($variante['ecomm_iva_perc'])&& $variante['ecomm_iva_perc']!="") $iva_perc=$variante['ecomm_iva_perc'];
                  if(isset($variante['ecomm_nome']) && $variante['ecomm_nome']!="") $nome_prodotto=$variante['ecomm_nome'];
                  
                  
                  $prezzo=parseToFloat($prezzo);
                  
                  $quantita=$variante['ecomm_quantita'];
                  $dim=$variante['ecomm_dimensioni'];
                  $aggiunte=parseToFloat($variante['ecomm_aggiunte']);
                  $aggiunteF=parseToFloat($variante['ecomm_aggiunte_f']);
                  $aggiunteQ=parseToFloat($variante['ecomm_aggiunte_q']);
                  $aggiunte_p=parseToFloat($variante['ecomm_aggiunte_p']);
                  $aggiunteDim=parseToFloat($variante['ecomm_aggiunte_dim']);
                  $note_interne=$variante['ecomm_aggiunte_note'];
                  
                  $area=$this->retAreaByDim($dim);
                  if($area==0) $area=1;
                  
                  $_SESSION['ecomm'][$key][$key2]['ecomm_prezzo_finale']=$prezzo*$quantita*$area;
                  $variante['ecomm_prezzo_finale']=$_SESSION['ecomm'][$key][$key2]['ecomm_prezzo_finale'];
                  $prezzo_finale=parseToFloat($variante['ecomm_prezzo_finale']);
                  
                  $prezzo_scontato=$prezzo_finale+$aggiunteF+($aggiunte*$quantita*$area)+($aggiunteQ*$quantita);
                  $prezzo_scontato=$prezzo_scontato-(($prezzo_scontato*$sconto)/100);
                  
                  $prezzo_fin_senza_sconto=(($prezzo+$aggiunte)*$quantita*$area)+$aggiunteF+($aggiunteQ*$quantita);
                  
                  if($prezzo_scontato<$ord_min) {
                    $prezzo_fin_senza_sconto=$ord_min;
                    $prezzo_finale=$ord_min;
                    $prezzo_scontato=$ord_min;
                    $sconto=0;
                  }
                  
                  $tot=$tot+$prezzo_fin_senza_sconto;
                  $totPeso=$totPeso+(($peso+$aggiunte_p)*$quantita*$area);
                  
                  $tot_sc=$tot_sc+$prezzo_scontato;
                  $iva=($prezzo_scontato*$iva_perc)/100;
                  $ivaTot=$ivaTot+$iva;
                  ?>
                  <tr class="body">
                    <td class="actions" data-th="<?php echo ln("Azioni"); ?>">	 			 	 			
                    <?php if($cols_carrello['modifica']==1) { ?><a href="<?php echo $link ?>" <?php if($cols_carrello['modifica_fumetto_editor']!="") { ?>class="tTip btn btn-sm" title="<div class='fumetto'><?=str_replace("\"","'",tinybug(ln($cols_carrello['modifica_fumetto_editor'])));?></div>"<? }else{ ?>class="btn btn-sm"  title="<?php echo ln("modifica il prodotto");?>" <? } ?> ><img src="<?php echo $objUtility->getPathBackofficeResources()."pencil.png"; ?>" /></a><? } ?>
                    <?php if($cols_carrello['clona']==1) { ?><a href="<?php echo $link ?>" <?php if($cols_carrello['clona_fumetto_editor']!="") { ?>class="tTip btn btn-sm" title="<div class='fumetto'><?=str_replace("\"","'",tinybug(ln($cols_carrello['clona_fumetto_editor'])));?></div>"<? }else{ ?> class="btn btn-sm" title="<?php echo ln("clona il prodotto");?>" <? } ?> ><img src="<?php echo $objUtility->getPathBackofficeResources()."clone.png"; ?>" /></a><? } ?>
                    <?php if($cols_carrello['elimina']==1) { ?> 
                      <a class='delete btn btn-sm'  href='' tag1="<?=$key?>" tag2="<?=$key2?>"><img src="<?php echo $objUtility->getPathBackofficeResources()."delete.gif"; ?>" title="<?php echo ln("rimuovi il prodotto dal carrello");?>" class="img_del" /></a>
                    <? } ?>
                    </td>
                    <?php if($cols_carrello['immagine']==1) { ?><td class="img-articolo"><img  class="img-responsive " src='<?=$foto_prodotto?>'></td><? } ?>
                    <?php if($cols_carrello['codice']==1) { ?><td class="articolo" data-th="<?php echo ln("cod. articolo"); ?>"><?=$codice?></td><? } ?>
                    <?php if($cols_carrello['nome']==1) { ?>
                      <td class="articolo-descr" data-th="<?php echo ln("descrizione"); ?>">
                        <?php 
                        echo "<div class='articolo-descr-nome no-spacing'>".$nome_prodotto."</div>";
                        
                        ksort($variante);
                        while (list($key3, $caratteristica) = each($variante)) {
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
                              if($caratteristica!="") echo "<div style='text-transform: lowercase;'>".ln($tmpkey3).":&nbsp;".ln($caratteristica,$zy)."</div>";
                            } else {
                              while (list($key4, $value) = each($caratteristica)) {
                                $cnome=retRow("ecommerce_valori",$value);
                                $caratteristica[$key4]=ln($cnome['nome'],$zy);
                              }
                              $tmpkey3=ln(str_replace("ecomm_","",$key3),$zy);
                              $tmpkey3=str_replace("_","&nbsp;",$key3);
                              if(implode(", ",$caratteristica)!="") echo "<div style='font-style:italic;text-transform: lowercase;'>".$tmpkey3.":&nbsp;".implode(", ",$caratteristica)."</div>";
                            } 
                          }
                        }
                        ?>
                      </td>
                    <? } ?>	 			
                    <?php if($cols_carrello['prezzo_unitario']==1) { ?><td class="articolo-prz"  data-th="<?php echo ln("prezzo un."); ?>"><?php echo currencyITA($prezzo); ?></td><? } ?>
                    <?php if($cols_carrello['sconto']==1) { ?><td class="articolo-scn" data-th="<?php echo ln("sconto"); ?>"><?php if($sconto>0) { ?> - <?php echo round($sconto,2); ?>%<? } ?></td><? } ?>
                    <?php if($cols_carrello['quantita']==1) { ?><td class="input-articolo" data-th="<?php echo ln("quantità"); ?>"><input type ="text" class='qta' name='qta' value="<?php echo $quantita; ?>" tag1="<?=$key?>" tag2="<?=$key2?>" MAXLENGTH=7><a class='refresh'  href='' tag1="<?=$key?>" tag2="<?=$key2?>"><img src="<?php echo $objUtility->getPathBackofficeResources()."refresh.png"; ?>" title="<?php echo ln("aggiorna");?>" class="img_refr" /></a></td><? } ?>
                    <?php if($cols_carrello['aggiunte_detrazioni']==1) { ?><td class="articolo-aggiunte" data-th="<?php echo ln("aggiunte/detrazioni"); ?>"><?php if(($aggiunte!="" && $aggiunte!="0") || ($aggiunteF!="" && $aggiunteF!="0") || ($aggiunteQ!="" && $aggiunteQ!="0")) { ?><?php echo currencyITA(($aggiunte*$quantita*$area)+$aggiunteF+($aggiunteQ*$quantita)); ?><? }else{ echo "&nbsp;";} ?></td><? } ?>			
                    <td class="colonna_labels empty-xs"></td>
                    <?php if($cols_carrello['totale']==1) { ?><td class="colonna_prezzi tot-articolo  text-right" data-th="<?php echo ln("totale"); ?>"><span><?php echo currencyITA($prezzo_scontato); ?></span></td><? } ?>			
                  </tr>
                <? } ?>
              <? } ?>
            <? } ?>
          <? } ?>			
        </form>	
        </tbody>        
        <tfoot>
        <tr class="<?php if(($totali['sconto'])==0) { echo 'riga_prezzi_totale';} ?>">
          <td colspan='<?php echo ($cols_count);?>' class="empty-xs">&nbsp;</td>
          <td colspan='1' class="colonna_prezzi_label hide-xs totale text-right" ><?php echo ln("Totale");?></td>
          <td colspan='1' class="colonna_prezzi totale text-right" data-th="<?php echo ln("Totale");?>"> 
            <span <?php if(($totali['sconto'])>0) { ?>style="text-decoration:line-through;"<? } ?>><?php if($totali['sconto']>=0) {echo currencyITA($totali['totale_senza_sconto']);}elseif($totali['sconto']<0){echo currencyITA($totali['totale']);} ?></span>
          </td>
        </tr> 		 
        <?php if(($totali['sconto'])>0) { ?>
        <tr class="noborders">
          <td colspan='<?php echo ($cols_count);?>'class="empty-xs">&nbsp;</td>
          <td colspan='1' class="colonna_prezzi_label hide-xs complimenti_sconto totale text-right">-&nbsp;<?php echo ln("SCONTO");?></td>
          <td colspan='1' class="colonna_prezzi complimenti_sconto text-right" data-th="-&nbsp;<?php echo ln("SCONTO");?>">
            <span><?php echo currencyITA($totali['sconto']); ?></span>		 		 
          </td>   
        </tr>
        <!--
        <tr>                               
          <td colspan='<?php echo ($cols_count+1);?>' class=""><div style="border-bottom:1px gray solid;padding:2px;width:170px;float:right;"></div></td>
        </tr>   
        -->
        <tr class="riga_prezzi_totale">
          <td colspan='<?php echo ($cols_count);?>'class="empty-xs">&nbsp;</td>
          <td colspan='1' class="colonna_prezzi_label hide-xs totale text-right"><?php echo ln("Totale scontato");?></td>
          <td colspan='1' class="colonna_prezzi totale text-right" data-th="<?php echo ln("Totale scontato");?>"> 
            <span><?php echo currencyITA($totali['totale']); ?></span>
          </td>
        </tr>
        <? } ?>
        <tr>
          <td colspan='<?php echo ($cols_count+2);?>' style="border-bottom:none;">
            <div class="ez-wr">
              <div class="ez-fl ez-negmr ez-50 ritiro-in-sede">
                <div class="ez-box"><input type="checkbox" name="ritiro-in-sede" value="1" <?php if($_SESSION['ecomm']['ritiro_in_sede']==1) echo "checked"; ?> /></div>
              </div>
              <div class="ez-last ez-oh ritiro-in-sede-label">
                <div class="ez-box"><?php echo ln("ritiro in sede"); ?></div>
              </div>
            </div>
          </td>
        </tr>
        <tr class="noborders">
          <td colspan='<?php echo ($cols_count);?>' style="border-top: none;">
              <!-- Module 3A -->
              <div class="ez-box rsLoading3"><img src="<?php echo $objUtility->getPathBackofficeResources()."loading.gif"; ?>" />&nbsp;&nbsp;Loading...</div>
              <div class="ez-wr calcolo-spese-spedizione clearfix" style="visibility:hidden;">
                <div class="ez-fl  ez-negmx ez-33 spese-spedizione-check">
                  <div class="ez-wr">
                    <div class="ez-fl ez-negmr ez-50 spedizione-check">
                      <div class="ez-box"><input type="checkbox" name="spedizione-check" value="1" <?php if($_SESSION['ecomm']['ritiro_in_sede']!=1) echo "checked"; ?> /></div>
                    </div>
                    <div class="ez-last ez-oh spedizione-check-label">
                      <div class="ez-box"><?php echo ln("spedizione"); ?></div>
                    </div>
                  </div>
                </div>
                <div class="ez-fl ez-negmr ez-33 spese-spedizione-left form-block-inline">
                  <!-- Module 2A -->
                  <div class="ez-wr">
                    <div class="ez-fl ez-negmr ez-50 spedizione-nazione-label ecomm-label form-inline-el">
                      <div class="ez-box"><?php echo ln("nazione");?></div>
                    </div>
                    <div class="ez-last ez-oh spedizione-nazione form-inline-el">
                      <?php if($_SESSION['ecomm']['sp_nazione']=="") $_SESSION['ecomm']['sp_nazione']=106; ?>                                              
                      <div class="ez-box"><?php comboBox("nazioni",$field1="naz_eng",$field2="",$selected=$_SESSION['ecomm']['sp_nazione'],$multiple="",$onchange="",$echoId="",$nome="sel_nazione",$where="", $class="input2 sel-nazioni   select form-control"); ?></div>
                    </div>
                  </div>
                  <!-- Module 2A -->
                  <div class="ez-wr ecomm-spedizione-corriere">
                    <div class="ez-fl ez-negmr ez-50 spedizione-corriere-label ecomm-label form-inline-el">
                      <div class="ez-box"><?php echo ln("Corriere");?></div>
                    </div>
                    <div class="ez-last ez-oh spedizione-corriere form-inline-el">
                      <?php 
                      if($_SESSION['ecomm']['sp_nazione']=="106") {$addClassCorr="show";$addClassCorrInt="hide";}else{$addClassCorr="hide";$addClassCorrInt="show";}                                         
                      if(is_array($_SESSION['ecomm']['sp_corriere'])){ 
                        $corriereid=$_SESSION['ecomm']['sp_corriere']['id'];
                        $corriereFed=$_SESSION['ecomm']['sp_corriere']['feedback'];
                        $corriereFed=str_replace(".", "_", $corriereFed);    
                      }else{
                        $corriereid=""; 
                        $corriereFed=""; 
                      } 
                      ?>
                      <!-- Module 2A -->
                      <div class="ez-wr">
                        <div class="ez-fl ez-negmr ez-50 form-inline-el">
                          <div class="ez-box"><?php comboBox("corrieri",$field1="nome",$field2="tempi_consegna",$selected=$corriereid,$multiple="",$onchange="",$echoId="",$nome="sel_corriere",$where="attivo=1", $class="input2 sel-corrieri select form-control ".$addClassCorr,$ordine="Ordinamento"); ?></div>
                          <div class="ez-box"><?php comboBox("corrieri",$field1="nome",$field2="tempi_consegna_internazionale",$selected=$corriereid,$multiple="",$onchange="",$echoId="",$nome="sel_corriere_int",$where="(attivo=1 AND internazionale=1)", $class="input2 sel-corrieri-int select form-control ".$addClassCorrInt,$ordine="Ordinamento","",1); ?></div>
                        </div>
                        <div class="ez-last ez-oh feedback-corriere form-inline-el">
                          <div class="ez-box">
                            <img src="<?php echo $objUtility->getPathRoot();?>images/<?php echo $corriereFed."-stella.png"; ?>" class="corrieri_feedback cor-fed" <?php if($corriereFed=="" || $addClassCorr=="hide") echo "style='display:none;'"?> />
                            <img src="<?php echo $objUtility->getPathRoot();?>images/<?php echo $corriereFed."-stella.png"; ?>" class="corrieri_int_feedback cor-fed" <?php if($corriereFed=="" || $addClassCorrInt=="hide") echo "style='display:none;'"?> />
                          </div>  
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Module 2A -->
                  <div class="ez-wr ecomm-assicurazione <?php echo $addClassCorr; ?>">
                    <div class="ez-fl ez-negmr ez-50 spedizione-assicurazione-label ecomm-label form-inline-el">
                      <div class="ez-box"><?php echo ln("Assicurazione");?></div>
                    </div>
                    <div class="ez-last ez-oh spedizione-nazione form-inline-el">
                      <select id="sel_assicurazione" name="sel_assicurazione" size="1" class="input2 sel-assicurazione  select form-control">
                        <?php $selass=$this->retAssicurazione(); ?>
                        <option value="1" <?php if($selass==1) echo "selected"; ?> ><?php echo ln("sì") . " ( + 15,00 € )";?></option>
                        <option value="2" <?php if($selass==2 || $selass=="") echo "selected"; ?> ><?php echo ln("no");?></option>              
                      </select>
                    </div>
                  </div>
                </div>
                <div class="ez-last ez-oh spedizione-comune-container form-block-inline">
                  <!-- Module 2A -->
                  <div class="ez-wr spedizione-comune-container-element form-inline-el">
                    <div class="ez-box zona-non-raggiungibile"><?php echo ln("ATTENZIONE! Zona non raggiungibile."); ?></div>
                    
		                      <div class="ez-fl ez-negmr ez-50 spedizione-comune-label  form-inline-el" >
		                      	<div class="ez-box"><?php //if($_SESSION['ecomm']['sp_nazione']=="106") echo ln("");?></div>
		                    	</div>

                    <div class="ez-last ez-oh spedizione-comune form-inline-el" <?php if($_SESSION['ecomm']['sp_nazione']!="106") echo "style='display:none;'"; ?>>
                      <!-- Module 3A -->
                      <div class="ez-wr">
	                      <div class="sped-prov form-inline-el" >
	                         <div class="ez-fl ez-negmr ez-50 spedizione-provincia-label ecomm-label  form-inline-el"  >
		                      	<div class="ez-box"><?php if($_SESSION['ecomm']['sp_nazione']=="106") echo ln("provincia");?></div>
		                    	</div>
	                        <div class="ez-fl  ez-negmx ez-33 form-inline-el"  >
	                          <div class="ez-box"><?php comboBox("province",$field1="sigla",$field2="",$_SESSION['ecomm']['sp_provincia'],$multiple="",$onchange="",$echoId="",$nome="sel_provincia",$where="", $class="input2  select form-control"); ?></div>
	                        </div>
	                       </div>
	                      <div class="sped-comune form-inline-el" >
	                        <div class="ez-fl ez-negmr ez-33 form-inline-el sel-comune-label" >
	                          <div class="ez-box"><?php echo ln("comune"); ?></div>
	                        </div>
	                        <div class="ez-last ez-oh form-inline-el">
	                          <div class="ez-box">
	                          <?
	                          if($_SESSION['ecomm']['sp_provincia']>0) {
	                            comboBox("comuni",$field1="comune",$field2="",$selected=$_SESSION['ecomm']['sp_comune'],$multiple="",$onchange="",$echoId="",$nome="sel_comune",$where="id_province='".$_SESSION['ecomm']['sp_provincia']."'", $class="input2  select form-control");
	                          }else{ ?>
	                            <select id="sel_comune" name="sel_comune" class="select sel-comune form-control" style=""><option value=""></option></select>  
	                          <? } ?>
	                          </div>
	                        </div>
	                      </div>
                      </div>
                        
                    </div>          
                  </div>
                </div>
                <!--
                <div class="colonna_spese_spedizione clearfix">
                  <div class="label_spese_spedizione"><?php echo ln("spese di spedizione")." (".currencyITA($totPeso/1000)." kg)"; ?></div>
                  <div class="spese_spedizione"><?php echo currencyITA($_SESSION["ecomm"]["spese_spedizione"]); ?></div>
                </div>
              </div>
              -->
          </td>
        
          <td class="colonna_prezzi_label hide-xs spese_spedizione" colspan="1"><?php echo ln("spese di spedizione")." (".currencyITA($totPeso/1000)." kg)"; ?></td>
          <td class="colonna_prezzi spese_spedizione text-right" data-th="<?php echo ln("spese di spedizione")." (".currencyITA($totPeso/1000)." kg)"; ?>" colspan="1"><?php echo currencyITA($_SESSION["ecomm"]["spese_spedizione"]); ?></td>
          
        </tr>
        <tr>
          <td colspan='<?php echo ($cols_count+2);?>' class="separator">
            <!--<div>&nbsp;</div>-->
          </td>
        </tr>	
        <tr>
          <td colspan='<?php echo ($cols_count+2);?>' class='colonna_prezzi_label'>
            <!-- Layout 1 -->
            <div class="totali-cnt">
              <div class="ez-wr iva-tab"></div>
              <p><span class="totale-label"><?php echo ln("imponibile");?></span><span class='totalesmall carrello-imponibile'>&euro; <?php //echo currencyITA($totali['totale_imponibile']); ?></span></p>
              <p><span class="totale-label"><?php echo ln("I.V.A.");?></span><span class='totalesmall carrello-iva'>&euro; <?php //echo currencyITA($totali['iva']); ?></span></p>
              <p class="totali-totalone"><span class="totale-label"><?php echo ln("totale");?></span><span class='totale carrello-totale'>&euro; <?php //echo currencyITA($totali['totale']+$totali['iva']); ?></span></p>
            </div>
          </td>
        </tr>  
        </tfoot>	
      </table> 	 	
      <?php if(!isset($_SESSION["userris_id"]) && !isset($_POST['ecomm_reg'])) { ?>
        <div class="ecomm_registrazione row">	  
          <div class="col-xs-12 col-sm-4">
                  <form action="">     
                    <input type=submit value="<?php echo ln("acquista e registrati"); ?>" name="ecomm_registrati" class="submit btn btn-success btn-block flat-bottom">
                  </form>
            <div class="ecomm_acquista_reg alert alert-info flat-top"><?php echo ln("Per effettuare i tuoi prossimi acquisti in maniera comoda e veloce. Riceverai via mail le credenziali di accesso."); ?> </div>
          </div>
          <div class="col-xs-12 col-sm-4">     
                  <form action="">
                    <input type=submit value="<?php echo ln("acquista senza registrarti"); ?>" name="ecomm_acquista"  class="submit btn btn-success btn-block flat-bottom">
                  </form>
                  <div class="ecomm_acquista_no_reg alert alert-info flat-top"><?php echo ln("Per un singolo acquisto semplice e rapido."); ?> </div>
          </div>
          <div class="col-xs-12 col-sm-4">
                  <form action="">     
                    <input type=submit value="<?php echo ln("accedi e acquista"); ?>" name="ecomm_accedi" class="submit  btn btn-success btn-block flat-bottom" >
                  </form>
                  
                  <div class="ecomm_acquista_no_reg alert alert-info flat-top"><?php echo ln("Se sei gia registrato, inserisci le tue credenziali."); ?> </div>
                  <div class="ecomm_area_ris">
                    <?php $objHtml->printLoginAreaRis(0); ?>    
                  </div>

          </div>
        </div>
      <? } ?>
    </div>
    <?php ob_start(); ?>
    <script>
    function riepilogoCarrello() {
      var g_obj=this;
      var myqta;
      
      this.getIVA=function(fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_get_iva=1",
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.getRitiroInSede=function(fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_get_ritiro_in_sede=1",
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.ritiroInSede=function(v,fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_ritiro_in_sede="+v,
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.setIva=function(newivaperc,fun) {
        g_obj.getSpeseSpedizione(function(spese_spedizione){
          var iva_perc=newivaperc;
          var imponibile=toFloat(<?php echo $totali['totale_imponibile']; ?>);
          //alert(imponibile);
          spese = toFloat(spese_spedizione);
          
          imponibile=toFloat(imponibile);
          var iva=(toFloat(imponibile)*iva_perc)/100;
          var tot=toFloat(imponibile)+toFloat(iva);
          tot=tot.toString();
          iva=iva.toString();
          imponibile=imponibile.toString();
          
          g_obj.setImponibile(imponibile,function(){
            g_obj.setIVAeuro(iva,function(){
              g_obj.setTotale(tot,function(){
                $("#ecomm_riepilogo .carrello-totale").html("&euro; "+roundNumber(tot,2));
                $("#ecomm_riepilogo .carrello-imponibile").html("&euro; "+roundNumber(imponibile,2));
                $("#ecomm_riepilogo .carrello-iva").html("&euro; "+roundNumber(iva,2));
                
                $.ajax({
                   type: "POST",
                   url: "rsAction.php",
                   data: "ecomm_newiva="+newivaperc,
                   success: fun,
                   error: function(XMLHttpRequest, textStatus, errorThrown) {
                            //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                          }
                 });
              });
            });  
          });   
        });
      }
      
      this.getDatiGeografici=function(fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_get_dati_geografici=1",
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.calcolaSpese=function() {
        g_obj.getDatiGeografici(function(dati){
          dati=dati.split("#_#");
          var g_nazione=dati[0];
          var g_comune=dati[1];
          var g_regione_estera=dati[2];
          
          g_obj.getRitiroInSede(function(v){
            if(v==1){
              g_obj.ritiroInSede(1,function(spese){
                $("#ecomm_riepilogo .calcolo-spese-spedizione").hide();
                $("#ecomm_riepilogo input[name=ritiro-in-sede]").attr("checked",true);
                $("#ecomm_riepilogo .spedizione-check input[type=checkbox]").attr("checked",false);
                g_obj.setSpeseSpedizione(spese);
              });
            } else {
              $("#ecomm_riepilogo input[name=ritiro-in-sede]").attr("checked",false);
              $("#ecomm_riepilogo .spedizione-check input[type=checkbox]").attr("checked",true);
              $("#ecomm_riepilogo .calcolo-spese-spedizione").show();
              $("input[name=procedi_first]").show();
              
              if(g_nazione==106) {
                $("#ecomm_riepilogo div.spedizione-provincia-label").html("<?php echo ln('provincia'); ?>");
                $("#ecomm_riepilogo div.spedizione-comune-label").html("").hide();
                $("#ecomm_riepilogo div.spedizione-regione-estera").remove();
                $("#ecomm_riepilogo input[name=ritiro-in-sede]").attr("checked",false);
                $("#ecomm_riepilogo .spedizione-check input[type=checkbox]").attr("checked",true);
                $("#ecomm_riepilogo div.spedizione-comune").show();
                $.ajax({
                 type: "POST",
                 url: "rsAction.php",
                 data: "ecomm_sp_comune="+g_comune+"&ecomm_sp_nazione="+g_nazione+"&ecomm_sp_regione_estera=",
                 success: g_obj.setSpeseSpedizione,
                 error: function(XMLHttpRequest, textStatus, errorThrown) {
                          //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                        }
                });
              } 
              
              if(g_nazione!=106) {
                $.ajax({
                 type: "POST",
                 url: "rsAction.php",
                 data: "ecomm_sp_comune=-1&ecomm_sp_nazione="+g_nazione+"&ecomm_sp_regione_estera=-1",
                 success: function(msg){
                            $("#ecomm_riepilogo div.spedizione-comune").hide();
                            $("#ecomm_riepilogo div.spedizione-comune-label").html("").show();
                            $("#ecomm_riepilogo div.spedizione-regione-estera").remove();
                            
                            g_obj.setIva(22,function(){
                              if($(msg).find("select").length>0) {
                                $("#ecomm_riepilogo div.spedizione-comune-label").html("<?php echo ln('regione'); ?>");
                                //$("#ecomm_riepilogo select[name=sel_regione_estera]").find('option:first').attr('selected', 'selected').parent('select');
                                $("#ecomm_riepilogo .spedizione-comune-container-element").append(msg);
                              } else {
                                g_regione_estera="";
                              }
                              
                              $.ajax({
                               type: "POST",
                               url: "rsAction.php",
                               data: "ecomm_sp_comune=-1&ecomm_sp_nazione="+g_nazione+"&ecomm_sp_regione_estera="+g_regione_estera,
                               success: g_obj.setSpeseSpedizione,
                               error: function(XMLHttpRequest, textStatus, errorThrown) {
                                        //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                                      }
                              });
                            });     
                          },
                 error: function(XMLHttpRequest, textStatus, errorThrown) {
                          //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                        }
                });
              }
            } 
          });
        });
      }
      
      this.getSpeseSpedizione=function(fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_get_spese_spedizione=1",
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.getImponibile=function(fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_get_imponibile=1",
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.setImponibile=function(newval,fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_set_imponibile="+newval,
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.getIVAeuro=function(fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_get_ivaeuro=1",
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.setIVAeuro=function(newval,fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_set_ivaeuro="+newval,
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.getTotale=function(fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_get_totale=1",
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.setTotale=function(newval,fun) {
        $.ajax({
         type: "POST",
         url:  "rsAction.php",
         data: "ecomm_set_totale="+newval,
         success: fun,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });  
      }
      
      this.setProvincia=function(msg) {
        if(msg==""){
          msg="<select id='sel_comune' name='sel_comune' class='input2 select form-control'><option value=''></option></select>"
        }
        
        $("#sel_comune").replaceWith(msg);
        $("div.rsLoading3").hide();
      }
      
      this.setSpeseSpedizioneEx=function(msg) {
        g_obj.getIVA(function(iva_perc){
          if(strpos(msg,"#_#")==0) msg=msg+"#_#"; 
          msg=msg.split("#_#");
          tmpmsg=trim(msg[0]);
          
          var spese = tmpmsg;
          var tabellaIVA=trim(msg[1]);
          //var spese = msg;
          var spese_sess=spese;
          var imponibile;
          g_obj.getImponibile(function(msgimp){
            imponibile=msgimp;
            //alert(imponibile);
          
            if(tmpmsg!="") {
              $("#ecomm_riepilogo td.colonna_prezzi.spese_spedizione").html(roundNumber(spese,2));
              if(spese>0) {
                $("#ecomm_riepilogo td.colonna_prezzi_label.spese_spedizione").show(); 
                $("#ecomm_riepilogo td.colonna_prezzi.spese_spedizione").show();
              }else{
                $("#ecomm_riepilogo td.colonna_prezzi_label.spese_spedizione").hide();
                $("#ecomm_riepilogo td.colonna_prezzi.spese_spedizione").hide();  
              }
              $("#ecomm_riepilogo_acquista").hide();
              $("#ecomm_riepilogo .ecomm_registrazione").show();
              $("#ecomm_riepilogo_dati").show();
              $("#ecomm_riepilogo .ecomm-spedizione-corriere").show();
              $("#ecomm_riepilogo .ecomm-assicurazione").show();
              $("#ecomm_riepilogo .zona-non-raggiungibile").hide();
  
              //alert(toFloat(spese)/1.2);
              imponibile=toFloat(imponibile);
              var iva=(toFloat(imponibile)*iva_perc)/100;
              var tot=toFloat(imponibile)+toFloat(iva);
              tot=tot.toString();
              iva=iva.toString();
              imponibile=imponibile.toString();
              
              g_obj.setImponibile(imponibile,function(){
                g_obj.setIVAeuro(iva,function(){
                  g_obj.setTotale(tot,function(){
                    $("#ecomm_riepilogo .carrello-totale").html("&euro; "+roundNumber(tot,2));
                    $("#ecomm_riepilogo .carrello-imponibile").html("&euro; "+roundNumber(imponibile,2));
                    $("#ecomm_riepilogo .carrello-iva").html("&euro; "+roundNumber(iva,2));
                    
                    if(tabellaIVA!="") {
                      $("#ecomm_riepilogo .iva-tab").html(tabellaIVA);
                      $("#ecomm_riepilogo .iva-tab").show();
                    } else {
                      $("#ecomm_riepilogo .iva-tab").hide(); 
                    }
                    $("#ecomm_riepilogo .calcolo-spese-spedizione").css("visibility","visible");
                    $("input[name=procedi_first]").css("visibility","visible");
                    $("div.rsLoading3").hide();
                    return;
                  });
                });  
              });
            } else {
              $("#ecomm_riepilogo_acquista").hide();
              $("#ecomm_riepilogo .ecomm_registrazione").hide();
              //$("#ecomm_riepilogo_dati").hide();
              
              spese="";
              spese_sess="n/a";
              
              var imponibile=<?php echo $totali['totale_imponibile']; ?>;
              imponibile=toFloat(imponibile);
              var iva=(toFloat(imponibile)*iva_perc)/100;
              var tot=toFloat(imponibile)+toFloat(iva);
              tot=tot.toString();
              iva=iva.toString();
              imponibile=imponibile.toString();
              
              g_obj.setImponibile(imponibile,function(){
                g_obj.setIVAeuro(iva,function(){
                  g_obj.setTotale(tot,function(){
                    $("#ecomm_riepilogo .carrello-totale").html("&euro; "+roundNumber(tot,2));
                    $("#ecomm_riepilogo .carrello-imponibile").html("&euro; "+roundNumber(imponibile,2));
                    $("#ecomm_riepilogo .carrello-iva").html("&euro; "+roundNumber(iva,2));
                    
                    $("#ecomm_riepilogo .colonna_prezzi.spese_spedizione").html(roundNumber(spese,2));
                    if(spese>0) { 
                      $("#ecomm_riepilogo .colonna_prezzi_label.spese_spedizione").show();
                      $("#ecomm_riepilogo .colonna_prezzi.spese_spedizione").show();
                    }else{
                      $("#ecomm_riepilogo .colonna_prezzi_label.spese_spedizione").hide();
                      $("#ecomm_riepilogo .colonna_prezzi.spese_spedizione").hide();  
                    }
                    $("#ecomm_riepilogo .zona-non-raggiungibile").show();
                    $("#ecomm_riepilogo .ecomm-spedizione-corriere").hide();
                    $("#ecomm_riepilogo .ecomm-assicurazione").hide();
                    $("#ecomm_riepilogo .iva-tab").hide();
                    $("#ecomm_riepilogo  .calcolo-spese-spedizione").css("visibility","visible");
                    $("input[name=procedi_first]").css("visibility","visible");
                    $("div.rsLoading3").hide();
                    return;
                  });
                });  
              });  
            }    
          });
        });  
      }
      
      this.setSpeseSpedizione=function(msg) {
        $("div.rsLoading3").show();
        $("#ecomm_riepilogo .calcolo-spese-spedizione").css("visibility","hidden");
        $("input[name=procedi_first]").css("visibility","hidden");
        g_obj.getDatiGeografici(function(dati){
          dati=dati.split("#_#");
          var g_nazione=dati[0];
          var g_comune=dati[1];
          var g_regione_estera=dati[2];
          
          if(g_nazione!=106) {
            g_obj.getRitiroInSede(function(v){
              if(v==-1){
                g_obj.setIva(0,function(z){
                  g_obj.setSpeseSpedizioneEx(msg);
                });
              }else{
                g_obj.setSpeseSpedizioneEx(msg);  
              } 
            });
          }else{
            g_obj.setSpeseSpedizioneEx(msg);
          }
        });      
      }

      $('#ecomm_riepilogo a.delete').click(function(){
        if(!confirm("<?php echo ln("Confermare la rimozione dal carrello?");?>")) return false;
        $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_prodotto' value='"+$(this).attr('tag1')+"' />");
        $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_variante' value='"+$(this).attr('tag2')+"' />");
        $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_del' value='1' />");
        $('#ecomm_form_viewcart').submit();
        return false;    
      });
      
      $('#ecomm_riepilogo input.qta').blur(function(){
        g_obj.myqta=$(this);   
      });
      
      $('#ecomm_riepilogo a.refresh').click(function(){
        $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_prodotto' value='"+g_obj.myqta.attr('tag1')+"' />");
        $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_variante' value='"+g_obj.myqta.attr('tag2')+"' />");
        $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_updq' value='1' />");
        $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_qta' value='"+g_obj.myqta.val()+"' />");
        $('#ecomm_form_viewcart').submit();
        return false;    
      });
      
      $('#ecomm_riepilogo input.qta').keypress(function(event){
        if(event.keyCode == '13') {
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_prodotto' value='"+$(this).attr('tag1')+"' />");
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_variante' value='"+$(this).attr('tag2')+"' />");
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_updq' value='1' />");
          $('#ecomm_form_viewcart').append("<input type='hidden' name='ecomm_qta' value='"+$(this).val()+"' />");
          $('#ecomm_form_viewcart').submit();
        }   
      });
      
      $('[name=ecomm_registrati]').click(function(){
        if($("#ecomm_riepilogo td .calcolo-spese-spedizione .spedizione-comune select[name=sel_comune]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo ln("Attenzione! Selezionare un COMUNE per la spedizione."); ?>");
          $("#ecomm_riepilogo td .calcolo-spese-spedizione .spedizione-comune select[name=sel_comune]").focus();
          return false;
        }
        
        if($("#ecomm_riepilogo select[name=sel_corriere_int]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo html_entity_decode(ln("Attenzione! Selezionare un CORRIERE per la spedizione."),ENT_NOQUOTES,"UTF-8"); ?>");
          $("#ecomm_riepilogo select[name=sel_corriere_int]").focus();
          return false;
        }
        
        if($("#ecomm_riepilogo select[name=sel_corriere]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo html_entity_decode(ln("Attenzione! Selezionare un CORRIERE per la spedizione."),ENT_NOQUOTES,"UTF-8"); ?>");
          $("#ecomm_riepilogo select[name=sel_corriere]").focus();
          return false;
        }

        $('#ecomm_riepilogo .ecomm_registrazione').hide();
        $('#ecomm_riepilogo_acquista #nazione').trigger("change");
        $('#ecomm_riepilogo_acquista').show();
        $('#ecomm_riepilogo_acquista form').append("<input type='hidden' name='ecomm_reg' value='1' />");
        return false;      
      });
      
      $('[name=ecomm_acquista]').click(function(){
        if($("#ecomm_riepilogo td .calcolo-spese-spedizione .spedizione-comune select[name=sel_comune]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo ln("Attenzione! Selezionare un COMUNE per la spedizione."); ?>");
          $("#ecomm_riepilogo td .calcolo-spese-spedizione .spedizione-comune select[name=sel_comune]").focus();
          return false;
        }
        
        if($("#ecomm_riepilogo select[name=sel_corriere_int]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo html_entity_decode(ln("Attenzione! Selezionare un CORRIERE per la spedizione."),ENT_NOQUOTES,"UTF-8"); ?>");
          $("#ecomm_riepilogo select[name=sel_corriere_int]").focus();
          return false;
        }
        
        if($("#ecomm_riepilogo select[name=sel_corriere]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo html_entity_decode(ln("Attenzione! Selezionare un CORRIERE per la spedizione."),ENT_NOQUOTES,"UTF-8"); ?>");
          $("#ecomm_riepilogo select[name=sel_corriere]").focus();
          return false;
        }
        
        $('#ecomm_riepilogo .ecomm_registrazione').hide();
        $('#ecomm_riepilogo_acquista #nazione').trigger("change");
        $('#ecomm_riepilogo_acquista').show();
        $('#ecomm_riepilogo_acquista form').append("<input type='hidden' name='ecomm_reg' value='0' />");
        return false;      
      });
      
      $('[name=ecomm_accedi]').click(function(){
        if($("#ecomm_riepilogo td .calcolo-spese-spedizione .spedizione-comune select[name=sel_comune]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo ln("Attenzione! Selezionare un COMUNE per la spedizione."); ?>");
          $("#ecomm_riepilogo td .calcolo-spese-spedizione .spedizione-comune select[name=sel_comune]").focus();
          return false;
        }
        
        if($("#ecomm_riepilogo select[name=sel_corriere_int]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo html_entity_decode(ln("Attenzione! Selezionare un CORRIERE per la spedizione."),ENT_NOQUOTES,"UTF-8"); ?>");
          $("#ecomm_riepilogo select[name=sel_corriere_int]").focus();
          return false;
        }
        
        if($("#ecomm_riepilogo select[name=sel_corriere]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo html_entity_decode(ln("Attenzione! Selezionare un CORRIERE per la spedizione."),ENT_NOQUOTES,"UTF-8"); ?>");
          $("#ecomm_riepilogo select[name=sel_corriere]").focus();
          return false;
        }
        
        if($('#ecomm_riepilogo .ecomm_area_ris').css("display")=="none") {
          $('#ecomm_riepilogo .ecomm_area_ris').show();
          $('#ecomm_riepilogo .ecomm_area_ris input[name="utente"]').focus();
        }else{
          $('#ecomm_riepilogo .ecomm_area_ris').hide();
        }
        return false;      
      });
      
      $("#ecomm_riepilogo .iva-tab input").live("click",function(){
        g_obj.setIva($(this).val());
      });
      
      $("#ecomm_riepilogo .iva-tab input:checked").click();
      
      $("#ecomm_riepilogo input[name=ritiro-in-sede]").click(function() {
        var v;
        
        if($(this).is(':checked')) {
          v=1;
        }else{
          v=-1;
        } 
        
        g_obj.ritiroInSede(v,function(ret){
          g_obj.calcolaSpese();
        });     
      });
      
      //$("#ecomm_riepilogo_dati input[name=altra]").attr("checked",false);
      //$("#ecomm_riepilogo_dati .ecomm-altra-dest-container").hide();
      //$("#ecomm_riepilogo_dati .ecomm-altra-dest-container").css("visibility","visible");
                        
      $("#ecomm_riepilogo_dati input[name=altra]").change(function(){
        var myobj=$("#ecomm_riepilogo_dati .ecomm-altra-dest-container");
        
        if($(this).is(":checked")){
          myobj.show();
        }else{
          myobj.hide(); 
        }
      });
      
      $("#ecomm_riepilogo select[name=sel_provincia]").change(function() {
        var provincia=$(this).val();
        var nazione=$("#ecomm_riepilogo select[name=sel_nazione]").val();
        if($("#ecomm_riepilogo_dati input[name=altra_nazione]").length>0) {
          $("#ecomm_riepilogo_dati input[name=altra_prov]").attr("value",$(this).children("option:selected").html());
          if(nazione==106) {
            $("#ecomm_riepilogo_dati tr.altra_regione_estera").hide();
            $("#ecomm_riepilogo_dati tr.altra_prov").show();
            //if(provincia=="") {
              //$("#ecomm_riepilogo_dati").hide();
            //}
          }else{
            $("#ecomm_riepilogo_dati tr.altra_prov").hide();
            $("#ecomm_riepilogo_dati tr.altra_regione_estera").show(); 
          }  
        }else{
          if(provincia=="") $("#ecomm_riepilogo .ecomm_registrazione").hide();
        } 
        
        $("div.rsLoading3").show();
        
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "ecomm_sp_provincia="+provincia,
         success: g_obj.setProvincia,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });    
      });
      
      $("#ecomm_riepilogo select[name=sel_comune]").live("change",function() {
        var comune=$(this).val();
        var nazione=$("#ecomm_riepilogo select[name=sel_nazione]").val();
        if($("#ecomm_riepilogo_dati input[name=altra_nazione]").length>0) {
          $("#ecomm_riepilogo_dati input[name=altra_comune]").attr("value",$(this).children("option:selected").html());
          if(nazione==106) {
            $("#ecomm_riepilogo_dati tr.altra_regione_estera").hide();
            $("#ecomm_riepilogo_dati tr.altra_comune").show();
            //if(comune=="") {
              //$("#ecomm_riepilogo_dati").hide();
            //}
          }else{
            $("#ecomm_riepilogo_dati tr.altra_comune").hide();
            $("#ecomm_riepilogo_dati tr.altra_regione_estera").show(); 
          }  
        }else{
          if(comune=="") $("#ecomm_riepilogo .ecomm_registrazione").hide();
        }
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "ecomm_sp_comune="+comune+"&ecomm_sp_nazione="+nazione,
         success: g_obj.setSpeseSpedizione,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });    
      });
      
      $("#ecomm_riepilogo select[name=sel_corriere_int]").live("change",function() {
        $("div.rsLoading3").show();
        $("#ecomm_riepilogo .calcolo-spese-spedizione").css("visibility","hidden");
        $("input[name=procedi_first]").css("visibility","hidden");
        var corriere=$("#ecomm_riepilogo select[name=sel_corriere_int] option:selected").val(); 
        
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "setCorriereInt="+corriere,
         success: function(msg){
                    $("#ecomm_riepilogo img.corrieri_int_feedback").attr("src",getPathRoot+"images/"+msg+"-stella.png").show();
                    g_obj.calcolaSpese();
                  },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        }); 
      });
      
      $("#ecomm_riepilogo select[name=sel_assicurazione]").live("change",function() {
        $("div.rsLoading3").show();
        $("#ecomm_riepilogo .calcolo-spese-spedizione").css("visibility","hidden");
        $("input[name=procedi_first]").css("visibility","hidden");
        var ass=$("#ecomm_riepilogo select[name=sel_assicurazione] option:selected").val();
        
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "setAssicurazione="+ass,
         success: function(msg){
                    g_obj.calcolaSpese();
                  },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });    
      });
      
      $("#ecomm_riepilogo select[name=sel_corriere]").live("change",function() {
        $("div.rsLoading3").show();
        $("#ecomm_riepilogo .calcolo-spese-spedizione").css("visibility","hidden");
        $("input[name=procedi_first]").css("visibility","hidden");
        var corriere=$("#ecomm_riepilogo select[name=sel_corriere] option:selected").val();
        
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "setCorriere="+corriere,
         success: function(msg){
                    $("#ecomm_riepilogo img.corrieri_feedback").attr("src",getPathRoot+"images/"+msg+"-stella.png").show();
                    g_obj.calcolaSpese();
                  },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });    
      });
      
      $("#ecomm_riepilogo select[name=sel_nazione]").live("change",function() {
        $("div.rsLoading3").show();
        $("#ecomm_riepilogo .calcolo-spese-spedizione").css("visibility","hidden");
        $("input[name=procedi_first]").css("visibility","hidden");
        var nazione=$("#ecomm_riepilogo select[name=sel_nazione] option:selected").val();
        
        $("#ecomm_riepilogo select[name=sel_corriere_int]").removeClass("show hide");
        $("#ecomm_riepilogo select[name=sel_corriere]").removeClass("show hide");
        $("#ecomm_riepilogo .ecomm-assicurazione").removeClass("show hide");
        
        if(nazione==106) {
          $("#ecomm_riepilogo select[name=sel_corriere]").val($("#ecomm_riepilogo select[name=sel_corriere_int]").val());
          $("#ecomm_riepilogo select[name=sel_corriere_int]").hide();
          $("#ecomm_riepilogo select[name=sel_corriere]").show();
          
          $("#ecomm_riepilogo img.corrieri_int_feedback").hide();
          $("#ecomm_riepilogo img.corrieri_feedback").show();
          
          
          $("#ecomm_riepilogo .ecomm-assicurazione").addClass("show");
        }else{
          $("#ecomm_riepilogo select[name=sel_corriere_int]").val($("#ecomm_riepilogo select[name=sel_corriere]").val());
          $("#ecomm_riepilogo select[name=sel_corriere_int]").show();
          $("#ecomm_riepilogo select[name=sel_corriere]").hide();
          
          $("#ecomm_riepilogo img.corrieri_int_feedback").show();
          $("#ecomm_riepilogo img.corrieri_feedback").hide();
          
          $("#ecomm_riepilogo .sel-assicurazione").val(2);  
          $("#ecomm_riepilogo .ecomm-assicurazione").addClass("hide");
        }
        
        
        if($("#ecomm_riepilogo_acquista select[name=nazione]").length>0) {
        	$("#ecomm_riepilogo_acquista select[name=nazione]").val($("#ecomm_riepilogo select[name=sel_nazione]").val());
        	$("#ecomm_riepilogo_acquista select[name=nazione]").trigger("change");		
        }
				
        if($("#ecomm_riepilogo_dati input[name=altra_nazione]").length>0) {
          $("#ecomm_riepilogo_dati input[name=altra_nazione]").attr("value",$("#ecomm_riepilogo select[name=sel_nazione]").children("option:selected").html());
          if(nazione==106) {
        	  $("#ecomm_riepilogo_dati input[name=altra_prov]").attr("value",$("#ecomm_riepilogo select[name=sel_provincia]").children("option:selected").html());
        	  $("#ecomm_riepilogo_dati input[name=altra_prov]").attr("readonly","");
        	  $("#ecomm_riepilogo_dati td.lbl-altra-prov").attr("style","color:red;");
            $("#ecomm_riepilogo_dati tr.altra_regione_estera").hide();
            $("#ecomm_riepilogo_dati tr.altra_comune").show();
          }else{
        	  $("#ecomm_riepilogo_dati input[name=altra_prov]").attr("value","");
        	  $("#ecomm_riepilogo_dati input[name=altra_prov]").removeAttr("readonly");
        	  $("#ecomm_riepilogo_dati td.lbl-altra-prov").removeAttr("style");
            $("#ecomm_riepilogo_dati tr.altra_comune").hide();
            $("#ecomm_riepilogo_dati input[name=altra_regione_estera]").attr("value","");
            $("#ecomm_riepilogo_dati tr.altra_regione_estera").show();
          }  
        }
        
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "ecomm_sp_comune=-1&ecomm_sp_nazione="+nazione+"&ecomm_sp_regione_estera=-1&",
         success: function(msg){
                    g_obj.calcolaSpese();
                  },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });      
      });
      
      $("#ecomm_riepilogo select[name=sel_regione_estera]").live("change",function() {
        var regione=$(this).val();
        var nazione=$("#ecomm_riepilogo select[name=sel_nazione]").val();
        if($("#ecomm_riepilogo_dati input[name=altra_nazione]").length>0) {
          $("#ecomm_riepilogo_dati input[name=altra_regione_estera]").attr("value",$(this).children("option:selected").html());
          if(nazione==106) {
            $("#ecomm_riepilogo_dati tr.altra_regione_estera").hide();
            $("#ecomm_riepilogo_dati tr.altra_comune").show();
          }else{
            $("#ecomm_riepilogo_dati tr.altra_comune").hide();
            $("#ecomm_riepilogo_dati tr.altra_regione_estera").show(); 
          }  
        }
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: "ecomm_sp_comune=-1&ecomm_sp_nazione="+nazione+"&ecomm_sp_regione_estera="+regione,
         success: g_obj.setSpeseSpedizione,
         error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                }
        });    
      });
      
      $("#ecomm_riepilogo .spedizione-check input[type=checkbox]").click(function(){
        var v;
        
        if($(this).is(":checked")) {
          v=-1;
        }else{
          v=1;
        } 
        
        g_obj.ritiroInSede(v,function(ret){
          g_obj.calcolaSpese();
        });  
      });
      
      g_obj.calcolaSpese();
      
      $("#ecomm_riepilogo").show();
    }
    
    if(typeof(rsRiepilogoCarrello)=="undefined") eval("var rsRiepilogoCarrello='';");
    
    function ecomm_initRiepilogoCarrello() {
      if(rsRiepilogoCarrello=="") {
        rsRiepilogoCarrello=1;
        var tmpCart=new riepilogoCarrello();
      }  
    }
    
    $(document).ready(function(){
      ecomm_initRiepilogoCarrello();  
    });
    </script>
    <?php
    $this->g_jsCode.=ob_get_contents(); 
    ob_end_clean();
    
    if(!isset($_SESSION["userris_id"])){
      $this->riepilogoCarrello_acquista();
    }
    
    if(isset($_POST['ecomm_reg']) || isset($_SESSION["userris_id"])){
      $this->riepilogoDati();
    }    
  }
  
  function riepilogoDati() { 
    $objConfig = new ConfigTool();
    $objDb = new Db;
    $objUtility = new Utility;
    $objHtml = new Html;
    $conn = $objDb->connection($objConfig);
    $objMailing = new Mailing;
    
    global $config_table_prefix;
    
    if(isset($_SESSION["userris_id"])) {
      $id=$_SESSION["userris_id"];
      $user=retRow("users",$id);
      
      $RagioneSociale=$user['ragionesociale'];
      $pec=$user['pec'];
      $codice_destinatario=$user['codice_destinatario'];
      $cognome=$user['cognome'];
      $nome=$user['nome'];
      $codicefiscale=$user['codicefiscale'];
      $iva=$user['partitaiva'];
      $indirizzo=$user['indirizzo'];
      $cap=$user['cap'];
      $citta=$user['citta'];
      $comune=$user['comune'];
      $regione_estera=$user['regione_estera'];
      $provincia=$user['provincia'];
      $provincia_estera=$user['provincia_estera'];
      $nazione=$user['nazione'];
      $tmpnaz=getTable("nazioni", "", "(nazione='".addslashes($nazione)."' OR naz_eng='".addslashes($nazione)."')");
      if(count($tmpnaz)>0) $idnazione=$tmpnaz[0]["id"];
      
      $telefono=$user['telefono'];
      $email=$user['email'];
      $user['reg']=-1;
    } elseif(isset($_POST['ecomm_reg'])) {
      $user=$_POST;
      
      $user['codice_destinatario']=$user['coddest'];
      $user['ragionesociale']=$user['rag'];
      $user['codicefiscale']=$user['cf'];
      $user['partitaiva']=$user['piva'];
      $user['cap']=$user['capcap'];
      $user['email']=$user['mail'];
      
      $comune=$user['comune'];
      $tmp_comune=retRow("comuni",$comune);
      if(count($tmp_comune)>0){
      	$comune=$tmp_comune["comune"];
      }
      
      $regione_estera=$user['sel_regione_estera'];
      $regione_estera=retRow("regioni_estere",$regione_estera);
      $regione_estera=$regione_estera['regione_en'];
      $provincia_estera=$user['provincia_estera'];
      
      $RagioneSociale=$user['rag'];
      $pec=$user['pec'];
      $codice_destinatario=$user['codice_destinatario'];
      $cognome=$user['cognome'];
      $nome=$user['nome'];
      $codicefiscale=$user['cf'];
      $iva=$user['piva'];
      $indirizzo=$user['indirizzo'];
      $cap=$user['capcap'];
      $citta=$user['citta'];
      $provincia=$user['provincia'];
      $tmp_prov=retRow("province",$provincia);
      if(count($tmp_prov)>0){
	      $provincia=$tmp_prov["sigla"];
      }
      $idnazione=$user['nazione'];
      $nazione=retRow("nazioni",$idnazione);
      if($nazione['id']!=106) $comune="";
      $nazione=$nazione['naz_eng'];
      $telefono=$user['telefono'];
      $email=$user['mail'];
      
      if($_POST['ecomm_reg']=='1') $ecomm_grp='17';
      if($_POST['ecomm_reg']=='0') $ecomm_grp='18';
      
      $user['reg']=$_POST['ecomm_reg'];
      
      $login=$email;
      $password=genPassword();
      $pi=$iva;
      $cf=$codicefiscale;
      $tel=$telefono;
      
      $rs=getTable("users","","login='$login' AND nome='$nome' AND cognome='$cognome' AND ultimoaccesso IS NULL");
      if(count($rs)>0) {
        deleteUser($rs[0]['id']);
      }
      
      $rs=getTable("users","","email='$email' AND ultimoaccesso IS NULL");
      if(count($rs)>0) {
        deleteUser($rs[0]['id']);
      }else{
        $rs=getTable("users","","email='$email'");
        if(count($rs)>0) { 
          $tmpmess=ln("L'indirizzo email inserito risulta già registrato.");
          $objHtml->adminPageRedirect("",$tmpmess);
          exit;
        }
      }
      
      $sql="INSERT INTO `".$config_table_prefix."users` (nome,cognome,login,pwd,ragionesociale,indirizzo,cap,citta,provincia,provincia_estera,nazione,comune,regione_estera,partitaiva,codicefiscale,telefono,fax,email,codice_destinatario,pec,data_di_nascita,cellulare,autorizzo,datecreation) VALUES ( ";
      $sql.="'".($nome)."',";
      $sql.="'".($cognome)."',";
      $sql.="'".($login)."',";
      $sql.="MD5('".$password."'),";
      $sql.="'".($RagioneSociale)."',";
      $sql.="'".($indirizzo)."',";
      $sql.="'".($cap)."',";
      $sql.="'".($citta)."',";
      if($idnazione==106) $sql.="'".($provincia)."',";else $sql.="'',";
      $sql.="'".($provincia_estera)."',";
      $sql.="'".($nazione)."',";
      if($idnazione==106) $sql.="'".($comune)."',";else $sql.="'',";
      $sql.="'".($regione_estera)."',";
      $sql.="'".($pi)."',";
      $sql.="'".($cf)."',";
      $sql.="'".($tel)."',";
      $sql.="'".($fax)."',";
      $sql.="'".($email)."',";
      $sql.="'".($codice_destinatario)."',";
      $sql.="'".($pec)."',";
      $sql.="'".($data_nascita)."',";
      $sql.="'".($cellulare)."',";
      $sql.="'1',";
      $sql.="NOW() )";
      mysql_query($sql);
      $id_users=mysql_insert_id();
      $user['id']=$id_users;
      $user['login']=$login;
      if($id_users>0) {
        initUser($id_users);
        CombineUsersRoles($id_users,$ecomm_grp);
      }
      
      $tmpTable2=new rsTable2();
      $tmpTable2->g_table="users"; 
      
      $confirm=$tmpTable2->makeTemplateReplace($id_users);
      if($confirm) {
        $res=1;
      }
      
      if($ecomm_grp=="17") {
        if($confirm) {
          if($confirm['email_editor']!="" && $confirm['email_destinatari']!="") {
            $res=$objMailing->mmail($confirm['email_destinatari'],$objConfig->get("email-from"),$confirm['email_oggetto'],$confirm['email_editor'],$allegato,$allegato_type,$allegato_name);
          }
          if($res==0) $err=1; 
          
          $sql="INSERT INTO `".$config_table_prefix."rstbl2_invii` (destinatari,oggetto,messaggio,errori) VALUES ('".addslashes($confirm['email_destinatari'])."','".addslashes($confirm['email_oggetto'])."','".addslashes($confirm['email_editor'])."','".$res."')";
          mysql_query($sql);
          $eid=mysql_insert_id();
          
          $sql="INSERT INTO `".$config_table_prefix."rstbl2_email#rstbl2_invii_nm` (id_rstbl2_email,id_rstbl2_invii) VALUES ('".$confirm['id']."','".$eid."')";
          mysql_query($sql);
          
          if(stripslashes($confirm['messaggio_conferma_editor'])!="") $messaggio_conferma=$confirm['messaggio_conferma_editor'];
          if(stripslashes($confirm['messaggio_errore_editor'])!="") $messaggio_errore=$confirm['messaggio_errore_editor'];
        }
        
        if(stripslashes($messaggio_conferma)=="") $messaggio_conferma=ln("Grazie per esserti registrato. A breve riceverai una e-mail con i dati d'accesso.");
        if(stripslashes($messaggio_errore)=="") $messaggio_errore=ln("Si è verificato un errore durante l'operazione. Riprovare in un secondo momento.");
      }
    }
    
    $_SESSION['ecomm_user']=$user;  
    ob_start(); ?>
    <script> 
    $("input[name=ecomm_pagamento]").change(function(){
      $("#paypal-button").html(""); 
      $("input[name=procedi_first]").css("visibility","visible");
    });
     
    function ecomm_riepilogo_dati_form() {
      var g_obj=this;
      
      $("form[name=ecomm_riepilogo_dati_form]").submit(function(){
        if($("#ecomm_riepilogo td .calcolo-spese-spedizione .spedizione-comune select[name=sel_comune]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo ln("Attenzione! Selezionare un COMUNE per la spedizione."); ?>");
          $("#ecomm_riepilogo select[name=sel_comune]").focus();
          return false;  
        }
        
        if($("#ecomm_riepilogo select[name=sel_corriere_int]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo html_entity_decode(ln("Attenzione! Selezionare un CORRIERE per la spedizione."),ENT_NOQUOTES,"UTF-8"); ?>");
          $("#ecomm_riepilogo select[name=sel_corriere_int]").focus();
          return false;
        }
        
        if($("#ecomm_riepilogo select[name=sel_corriere]:visible").val()=="" && $("#ecomm_riepilogo td input[name=ritiro-in-sede]:checked").length == 0) {
          alert("<?php echo html_entity_decode(ln("Attenzione! Selezionare un CORRIERE per la spedizione."),ENT_NOQUOTES,"UTF-8"); ?>");
          $("#ecomm_riepilogo select[name=sel_corriere]").focus();
          return false;
        }
        
        var formData=$("form[name=ecomm_riepilogo_dati_form]").serialize();
        
        $("input[name=procedi_first]").css("visibility","hidden");
        
        $("input[name=spedizione-check]").attr("disabled", true);
        $("input[name=ritiro-in-sede]").attr("disabled", true);
        
        $("select[name=sel_nazione]").attr("disabled", true);
        $("select[name=sel_corriere]").attr("disabled", true);
        $("select[name=sel_assicurazione]").attr("disabled", true);
        $("select[name=sel_provincia]").attr("disabled", true);
        $("select[name=sel_comune]").attr("disabled", true);
        
        $("#ecomm-riep-altra").attr("disabled", true);
        
        $("input[name=ecomm_pagamento]").attr("disabled", true);
        $("input[name=altra_cognome]").attr("disabled", true);
        $("input[name=altra_nome]").attr("disabled", true);
        $("input[name=altra_indirizzo]").attr("disabled", true);
        $("input[name=altra_cap]").attr("disabled", true);
        $("input[name=altra_loc]").attr("disabled", true);
        $("input[name=altra_telefono]").attr("disabled", true);
        
        $("div[name=ecomm_order_loading]").show();
        
        $.ajax({
         type: "POST",
         url: "rsAction.php",
         data: formData,
         success: function(msg){
            $("div[name=ecomm_order_loading]").hide();
            
            if(msg=="-1"){
              //
            }else{
              var myord = msg.split(";");
            
              if(myord[0]=="PayPalEXP"){
                paypal.Button.render({
                    env: 'production', // sandbox | production
            
                    client: {
                      sandbox:    'ASKfxFOFboJLBLSoy-FE1nte5GnPqWe-NmY5Bp-tBPLrFmI_WC1oMtx51NPLpKeiUTTcynX5eQYD79zU',
                      production: 'AQbWkVzNbMSM4ooRhfz17jDdwfWQmLbTtQavlOVjOoTazYThxlZPO3PAHwYGE9w_67lRCcvBdopHhYmH'
                    },
            
                    // Show the buyer a 'Pay Now' button in the checkout flow
                    commit: true,
            
                    // payment() is called when the button is clicked
                    payment: function(data, actions) {
                        // Make a call to the REST API to set up the payment
                        return actions.payment.create({
                            payment: {
                                transactions: [{
                                  amount: { total: toFloat(roundNumber(myord[2],2)), currency: 'EUR' },
                                  description: '<?php echo ln("Riferimento numero ordine"); ?> ' + myord[1],
                                  custom: '<?php echo ln("Acquisti effettuati su ".$_SERVER['SERVER_NAME']); ?>',
                                  invoice_number: myord[1]        
                                }],
                                
                                redirect_urls: {
                                    return_url: '<?php echo "https://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."rsAction.php?paypal_return="; ?>'+myord[1],
                                    cancel_url: '<?php echo "https://".$_SERVER['SERVER_NAME'].$objUtility->getPathRoot()."rsAction.php?paypal_cancel_return="; ?>'+myord[1]
                                }
                                
                            }
                        });
                    },
            
                    // onAuthorize() is called when the buyer approves the payment
                    onAuthorize: function(data, actions) {
                        // Make a call to the REST API to execute the payment
                        return actions.payment.execute().then(function() {
                            actions.redirect();
                        });
                    },
                
                    onCancel: function(data, actions) {
                        $.ajax({
                         type: "GET",
                         url: "rsAction.php",
                         data: "paypal_cancel_return="+myord[1],
                         success: function(msg){
                            alert('<?php echo addslashes(html_entity_decode(ln("Operazione annullata o rifiutata. Non sono stati effettuati addebiti. Si prega di riprovare"),ENT_QUOTES, 'UTF-8')); ?>');
                            $("input[name=ecomm_pagamento]").removeAttr("disabled");    
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                                  //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
                                }
                        });
                        //actions.redirect();
                    }
              
                }, '#paypal-button');
              }else{
                msg=str_replace('<script type="text\/javascript">','',msg); 
                msg=str_replace('<\/script>','',msg);                
                eval(msg);
              } 
            }  
         },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
            //alert("<?php echo ln("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); ?>"); 
         }
        });
        
        return false;
      });
    }
    
    if(typeof(rsRiepilogoCarrelloForm)=="undefined") eval("var rsRiepilogoCarrelloForm='';");
    
    function ecomm_initRiepilogo_dati_form() {
      if(rsRiepilogoCarrelloForm=="") {
        rsRiepilogoCarrelloForm=1;
        var tmpCart=new ecomm_riepilogo_dati_form();
      }  
    }
    
    $(document).ready(function(){
      ecomm_initRiepilogo_dati_form();  
    });
    </script>
    <?php
    $this->g_jsCode.=ob_get_contents(); 
    ob_end_clean();
    ?>
    <div id="ecomm_riepilogo_dati">
      <form name="ecomm_riepilogo_dati_form" action='' method='post'>
        <input type="hidden" name="ecomm_pagamenti_do" value="1" />
        <!--<table><tr><td valign='top' width='18'>&nbsp;</td><td valign='top'>-->
      		<table  class="table table-striped noborders nopadding" border="0" cellspacing="0" style="table-layout:fixed;" >
      			<tr>
      				<td  align="left">
      				<div style="color:#B03A40;"><B><?php echo ln("RIEPILOGO DATI");?></B></div></td>
      				<td width= "78%" align="left">&nbsp;</td>
      			</tr>
      			<tr>
      				<td  align="left"></td>
      				<td align="left">&nbsp;</td>
      			</tr>
      			<?php if($RagioneSociale!="") { ?>
        			<tr>
        				<td  align="left">
        				<?php echo ln("ragione sociale");?></td>
        				<td align="left">&nbsp;<B><?=$RagioneSociale?></B></td>
        			</tr>
      			<? } ?>
            <?php if($cognome!="") { ?>
            <tr>
      				<td  align="left">
      				<?php echo ln("cognome");?></td>
      				<td align="left">&nbsp;<B><?=$cognome?></B></td>
      			</tr>
      			<? } ?>
      			<?php if($nome!="") { ?>
            <tr>
      				<td  align="left">
      				<?php echo ln("nome");?></td>
      				<td align="left">&nbsp;<B><?=$nome?></B></td>
      			</tr>
      			<? } ?>
      			<tr>
      				<td  align="left">
      				<?php echo ln("codice fiscale");?></td>
      				<td align="left">&nbsp;<B><?=$codicefiscale?></B></td>
      			</tr>
      			<?php if($iva!="") { ?>
              <tr>
        				<td  align="left">
        				<?php echo ln("partita IVA");?></td>
        				<td align="left">&nbsp;<B><?=$iva?></B></td>
        			</tr>
      			<? } ?>
            <tr>
      				<td  align="left">
      				<?php echo ln("indirizzo");?></td>
      				<td align="left">&nbsp;<B><?=$indirizzo?></B></td>
      			</tr>
      			<tr>
      				<td  align="left">
      				<?php echo ln("cap");?></td>
      				<td align="left">&nbsp;<B><?=$cap?></B></td>
      			</tr>
      			<tr>
      				<td  align="left">
      				<?php echo ln("località");?></td>
      				<td align="left">&nbsp;<B><?=$citta?></B></td>
      			</tr>
      			<tr>
      				<td  align="left">
      				<?php echo ln("provincia");?></td>
      				<td align="left">&nbsp;<B><?php if($idnazione==106) echo $provincia;else echo $provincia_estera; ?></B></td>
      			</tr>
      			<tr>
      				<td  align="left">
      				<?php echo ln("nazione");?></td>
      				<td align="left">&nbsp;<B><?=$nazione?></B></td>
      			</tr>
            <?php if($comune!="") { ?>
              <tr>
        				<td  align="left">
        				<?php echo ln("comune");?></td>
        				<td align="left">&nbsp;<B><?php echo $comune; ?></B></td>
        			</tr>
      			<? } else { ?>
      			<tr>
      				<td  align="left">
      				<?php echo ln("regione estera");?></td>
      				<td align="left">&nbsp;<B><?=$regione_estera?></B></td>
      			</tr>
      			<? } ?>
      			<tr>
      				<td  align="left">
      				<?php echo ln("telefono");?></td>
      				<td align="left">&nbsp;<B><?=$telefono?></B></td>
      			</tr>
      			<?php if($codice_destinatario!="") { ?>
              <tr>
        				<td  align="left">
        				<?php echo ln("codice destinatario");?></td>
        				<td align="left" class="td-wrapped">&nbsp;<b><?=$codice_destinatario?></b> </td>
        			</tr>
            <? } ?>
            <?php if($pec!="") { ?>
              <tr>
        				<td  align="left">
        				<?php echo ln("PEC");?></td>
        				<td align="left" class="td-wrapped">&nbsp;<b><?=$pec?></b> </td>
        			</tr>
            <? } ?>
            <tr>
      				<td  align="left">
      				<?php echo ln("email");?></td>
      				<td align="left" class="td-wrapped">&nbsp;<b><?=$email?></b> </td>
      			</tr>
      			
      			<tr>
      				<td ></td>
      				<td align="left">&nbsp;</td>
      			</tr>
      			<tr>
      				<td colspan="2">
                <table>
                  <tr>
                    <td style="vertical-align:top;line-height:10px;width:20px;">
                      <input type="checkbox" id="ecomm-riep-altra" name="altra" value="1"  style="line-height:10px;position:relative;" <?php if($idnazione!=$_SESSION["ecomm"]["sp_nazione"]) echo 'checked'; ?> />
                    </td>
                    <td style="vertical-align:middle;margin:0px;">
                      <label for="ecomm-riep-altra" style="margin:0px;"><b><?php echo ln("SELEZIONA PER RICEVERE LA MERCE PRESSO UN ALTRO INDIRIZZO");?></b></label>
                    </td>
                  </tr>
                </table>
      				</td>
      			</tr>
      			<tr>
      				<td colspan="2">
      				  &nbsp;
      				</td>
      			</tr>
      			</table>
      			<table class="ecomm-altra-dest-container table noborders nopadding" <?php if($idnazione!=$_SESSION["ecomm"]["sp_nazione"]) echo "style='display:block;'";else echo "style='display:none;'";  ?>>
              <tr>
        				<td class="ecomm-altra-dest-label"><?php echo ln("cognome");?></td>
        				<td align="left">&nbsp;<input name="altra_cognome" class="input2 form-control" maxlength="60"></td>
        			</tr>
        			
        			<tr>
        				<td class="ecomm-altra-dest-label"><?php echo ln("nome");?></td>
        				<td align="left">&nbsp;<input name="altra_nome" class="input2 form-control" maxlength="60"/></td>
        			</tr>
        			
        			<tr>
        				<td class="ecomm-altra-dest-label"><?php echo ln("indirizzo");?></td>
        				<td align="left">&nbsp;<input name="altra_indirizzo" class="input2 form-control" maxlength="48"></td>
        			</tr>
        			
        			<tr>
        				<td class="ecomm-altra-dest-label"><?php echo ln("cap");?></td>
        				<td align="left">&nbsp;<input name="altra_cap" class="input2  form-control" maxlength="12"></td>
        			</tr>
        			
        			<tr>
        				<td class="ecomm-altra-dest-label"><?php echo ln("località");?></td>
        				<td align="left">&nbsp;<input name="altra_loc" class="input2 form-control" maxlength="36"></td>
        			</tr>
        			<?php
              $tmpnazione=retRow("nazioni",$_SESSION["ecomm"]["sp_nazione"]);
              $tmpcomune=retRow("comuni",$_SESSION["ecomm"]["sp_comune"]);
              $tmpprovincia=retRow("province",$_SESSION["ecomm"]["sp_provincia"]);
              $tmpregione=retRow("regioni_estere",$_SESSION["ecomm"]["sp_regione_estera"]);
              ?>
        			<tr>
        				<td class="ecomm-altra-dest-label lbl-altra-prov" <?php if($idnazione==106) echo 'style="color:red;"'; ?> ><?php echo ln("provincia");?>*</td>
        				<td align="left">&nbsp;<input name="altra_prov" class="input2 form-control" maxlength="4" value="<?php if($_SESSION["ecomm"]["sp_nazione"]==106) echo $tmpprovincia['sigla'];else echo $provincia_estera; ?>" <?php if($_SESSION["ecomm"]["sp_nazione"]==106) echo "readonly"; ?> ></td>
        			</tr>
        			
        			<tr class="altra_nazione">
        				<td class="ecomm-altra-dest-label" style="color:red;"><?php echo ln("nazione");?>*</td>
        				<td align="left">&nbsp;<input name="altra_nazione" class="input2 form-control" maxlength="128" value="<?=$tmpnazione['naz_eng']?>" readonly></td>
        			</tr>
        			
        			<tr class="altra_comune" <?php if($tmpnazione['id']!="106") echo "style='display:none;'" ?>>
        				<td class="ecomm-altra-dest-label" style="color:red;"><?php echo ln("comune");?>*</td>
        				<td align="left">&nbsp;<input name="altra_comune" class="input2 form-control" maxlength="128" value="<?=$tmpcomune['comune']?>" readonly></td>
        			</tr>
        			
        			<tr class="altra_regione_estera" <?php if($tmpnazione['id']=="106") echo "style='display:none;'" ?> style="color:red;">
        				<td class="ecomm-altra-dest-label"><?php echo ln("regione estera");?>*</td>
        				<td align="left">&nbsp;<input name="altra_regione_estera" class="input2 form-control" maxlength="128" value="<?=$tmpregione['regione_en']?>" readonly></td>
        			</tr>
        			
        			<tr>
        				<td class="ecomm-altra-dest-label"><?php echo ln("telefono");?></td>
        				<td align="left">&nbsp;<input name="altra_telefono" class="input2 form-control" maxlength="24"></td>
        			</tr>
              <tr>
        				<td colspan="2" align="left" style="color:gray; padding: 20px 0;">(*) <?php echo ln("La modifica è possibile dai campi precedenti relativi alle spese di spedizione."); ?></td>
        			</tr>	
      			</table>

      			<!--</td></tr>
          </table>
          -->
          <BR><br>
          <div style="">
          	<div style="color:#B03A40;"><b><?php echo ln("Scegli la modalità di pagamento");?>:</b></div><br>
          	<?php  
            $rs=getTable("ecommerce_modalita_pagamenti","Ordinamento ASC","attivo='1'");
            while (list($key, $row) = each($rs)) { 
              $row['descrizione_editor']=replaceEcomerceMarkers(ln($row['descrizione_editor']));
              
              if($row['alert']!="") {
                ob_start(); ?>
                <script>
                $(document).ready(function() {
                  $("input[name=ecomm_pagamento][value=<?=$row['id']?>]").click(function(){
                    alert("<?php echo ln($row['alert']); ?>");      
                  });
                });
                </script>
                <?php
                $this->g_jsCode.=ob_get_contents(); 
                ob_end_clean();  
              } ?>
              <table>
                <tr>
                  <td valign='top' width='18'>
                    <input type="radio" name="ecomm_pagamento" value="<?=$row['id']?>" <? if($row['predefinito']=="1") echo "checked"; ?> />&nbsp;
                  </td>
                  <td valign='top' style="padding-top:5px;">
                    <b><?=ln($row['nome'])?></b>
                    <br>
                    <?php echo $row['descrizione_editor']; ?>  
                  </td>
                </tr>
              </table>
            <? } ?>    				
            <br>
            <div class="ez-box rsLoading3" name="ecomm_order_loading"><img src="<?php echo $objUtility->getPathBackofficeResources()."loading.gif"; ?>" />&nbsp;&nbsp;Loading...</div>
            <div align='center'>
              <div id="paypal-button"></div>
              <input name='procedi_first' type='image' src='<?php echo $objUtility->getPathBackofficeResources()."proc.png"; ?>' title='<?php echo ln("procedi");?>' style="visibility:hidden;" />
            </div>
          </div>
      </form>
    </div>
    <?
  }
  
  function resetBuffer() {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $_SESSION['ecomm'][$current][$combi]=array();  
  }
  
  function setAggiunta($id,$value) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte'])) $carrello['ecomm_arr_aggiunte']=array();
    
    $carrello['ecomm_arr_aggiunte'][$id]=$value;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function setAggiuntaF($id,$value) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte_f'])) $carrello['ecomm_arr_aggiunte_f']=array();
    
    $carrello['ecomm_arr_aggiunte_f'][$id]=$value;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function setAggiuntaPerim($id,$value) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte_perim'])) $carrello['ecomm_arr_aggiunte_perim']=array();
    
    $carrello['ecomm_arr_aggiunte_perim'][$id]=$value;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function setAggiuntaDim($id,$value) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte_dim'])) $carrello['ecomm_arr_aggiunte_dim']=array();
    
    $carrello['ecomm_arr_aggiunte_dim'][$id]=$value;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function setCarattNota($id,$value) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte_note'])) $carrello['ecomm_arr_aggiunte_note']=array();
    
    $carrello['ecomm_arr_aggiunte_note'][$id]=$value;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function setAggiuntaQ($id,$value) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte_q'])) $carrello['ecomm_arr_aggiunte_q']=array();
    
    $carrello['ecomm_arr_aggiunte_q'][$id]=$value;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function setAggiuntaPeso($id,$value) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte_p'])) $carrello['ecomm_arr_aggiunte_p']=array();
    
    $carrello['ecomm_arr_aggiunte_p'][$id]=$value;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function setQuantita($q) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte'])) $carrello['ecomm_arr_aggiunte']=array();
    if(!is_array($carrello['ecomm_arr_aggiunte_p'])) $carrello['ecomm_arr_aggiunte_p']=array();
    
    $carrello['ecomm_quantita']=$q;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function getQuantita() {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte'])) $carrello['ecomm_arr_aggiunte']=array();
    if(!is_array($carrello['ecomm_arr_aggiunte_p'])) $carrello['ecomm_arr_aggiunte_p']=array();
    
    if($carrello['ecomm_quantita']=="" || $carrello['ecomm_quantita']==0) $carrello['ecomm_quantita']=1;
    
    return $carrello['ecomm_quantita'];
  }
  
  function retAreaByDim ($strDim){
    $dim=explode("x",$strDim);
    $area=(parseToFloat($dim[0])/100)*(parseToFloat($dim[1])/100);
    
    return $area;  
  }
  
  function setDimensioni($strDim) {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    echo $strDim;
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte'])) $carrello['ecomm_arr_aggiunte']=array();
    if(!is_array($carrello['ecomm_arr_aggiunte_p'])) $carrello['ecomm_arr_aggiunte_p']=array();
    
    $carrello['ecomm_dimensioni']=$strDim;
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function getDimensioni() {
    $current=$this->getCurrent();
    if($current===FALSE) return "0x0";
    
    $combi="ecomm_buffer";
    
    $carrello=$_SESSION['ecomm'][$current][$combi];
    if(!is_array($carrello['ecomm_arr_aggiunte'])) $carrello['ecomm_arr_aggiunte']=array();
    if(!is_array($carrello['ecomm_arr_aggiunte_p'])) $carrello['ecomm_arr_aggiunte_p']=array();
    
    if($carrello['ecomm_dimensioni']=="" || $carrello['ecomm_dimensioni']=="0x0") $carrello['ecomm_dimensioni']="0x0";
    
    return $carrello['ecomm_dimensioni'];
  }
  
  function getArea() {
    return $this->retAreaByDim($this->getDimensioni());
  }
  
  function getAggiunte($combi="") {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    if($combi=="") $combi="ecomm_buffer";
    
    $aggiunte=$_SESSION['ecomm'][$current][$combi]['ecomm_arr_aggiunte'];
    if(!is_array($aggiunte)) return false;
    
    while (list($key, $row) = each($aggiunte)) {
      $totale=$totale+$row;
    }
    
    return $totale; 
  }
  
  function getAggiunteF($combi="") {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    if($combi=="") $combi="ecomm_buffer";
    
    $aggiunte=$_SESSION['ecomm'][$current][$combi]['ecomm_arr_aggiunte_f'];
    if(!is_array($aggiunte)) return false;
    
    while (list($key, $row) = each($aggiunte)) {
      $totale=$totale+$row;
    }
    
    return $totale; 
  }
  
  function getCarattNote($combi="") {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    if($combi=="") $combi="ecomm_buffer";
    
    $aggiunte=$_SESSION['ecomm'][$current][$combi]['ecomm_arr_aggiunte_note'];
    if(!is_array($aggiunte)) return false;
    
    while (list($key, $row) = each($aggiunte)) {
      $str=$str.$row."<br>";
    }
    
    return $str; 
  }
  
  function getAggiuntePerim($combi=""){
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    if($combi=="") $combi="ecomm_buffer";
    
    $aggiunte=$_SESSION['ecomm'][$current][$combi]['ecomm_arr_aggiunte_perim'];
    if(!is_array($aggiunte)) return false;
    
    while (list($key, $row) = each($aggiunte)) {
      $totale=$totale+$row;
    }
    
    return $totale;
  }
  
  function getAggiunteDim($combi="") {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    if($combi=="") $combi="ecomm_buffer";
    
    $aggiunte=$_SESSION['ecomm'][$current][$combi]['ecomm_arr_aggiunte_dim'];
    if(!is_array($aggiunte)) return false;
    
    $min=0;
    while (list($key, $row) = each($aggiunte)) {
      while (list($k, $kdim_disp) = each($row['dimArr'])) {
        if(strpos($kdim_disp,"+")!==FALSE){
          if($kdim_disp>$min) $min=$kdim_disp; 
        }
      }
    }
    
    if($min>0) $min=($min-1)/100;
    
    $q=$this->getQuantita();
    $totale=0;

    reset($aggiunte);
    while (list($key, $row) = each($aggiunte)) {
      $tmpAggiuntaDimm2=0;
      $tmpTotale=0;
      $c=-1;
      
      $dimArr=$row['dimArr'];
      
      if(is_array($dimArr)){
        $tw=$row['orDimW'];
        $th=$row['orDimH'];

        $diffPrez=$row['prezzo']-(($row['prezzo']*15)/100);
        $nomeCaratt=$row['orNomeCaratt'];
        
        $maxSize=max($dimArr)/100;
        
        if($tw>($maxSize-0.01) && $th<$maxSize && $th<$tw){
          $temptw=$tw;
          $tw=$th;
          $th=$temptw;
        } 
        
        /*
        if($tw>$maxSize && $th>$tw){
          $temptw=$tw;
          $tw=$th;
          $th=$temptw;
        }
        */
        
        $pann=0;
        $s=0.0;
        $st=0.0;
        $defSize=0;
        $defSc=0;
        
        reset($dimArr);
        if(($tw+$min)>=($maxSize-0.01) && ($th+$min)>=($maxSize-0.01)){
          while (list($k, $kdim_disp) = each($dimArr)) {
            if(strpos($kdim_disp,"+")===FALSE) {
              $dim_disp=$kdim_disp/100;
              
              $tw2 = $tw+$min;
              $th2 = $th+$min;
              $orizz=false;
              $strOrizz="verticali";
              
              $pannN=ceil($tw2/$dim_disp);
              
              $stAdd=0;
              if(strpos(strtolower($nomeCaratt), "banner")!==FALSE) {
                $stAdd=($pannN*$th2)*0.5;  
              }
              
              $st=($pannN*$dim_disp)+$stAdd;
              if(((int)($st*100)<=(int)($s*100) && $dim_disp>$defSize) || $defSize==0) {
                $s=$st;
                $defSize=$dim_disp;
              } 
            }  
          }
          
          if($defSize>0){
            $pannN=ceil($tw2/$defSize);
            $pann=(($pannN*$defSize)+($pannN*0.04))*$th2;
            $defSc=($pann*$diffPrez*$q)-($tw2*$th2*$diffPrez*$q);
            
            $this->setCarattNota($key, strtoupper($nomeCaratt).": N. ".$pannN." pannelli ".$strOrizz.", bobina H=".($defSize*100)." CM");  
          }
        }else{
          $def_dim_disp=0;
          $mW=0;
          while (list($k, $kdim_disp) = each($dimArr)) {
            $tmpAggiuntaDimm=0;
            if(strpos($kdim_disp,"+")===FALSE) {  
              $c++;
              
              $dim_disp=$kdim_disp/100;
              
              if(($tw+$min) <= ($dim_disp-0.01) ) {
                $mW = floor($dim_disp/$tw);
                if($mW>$q || $mW==0) $mW=$q;
                 
                $mH = ceil($q/$mW);
                
                $tw2 = $tw;
                $th2 = $th;
              }
              
              if($tw<$th && ($th+$min) <= ($dim_disp-0.01)) {
                $mW = floor($dim_disp/$th);
                if($mW>$q || $mW==0) $mW=$q;
                
                $mH = ceil($q/$mW);
                
                $tw2 = $th;
                $th2 = $tw;
              }
              
              if($mW>0){
                $mA = $tw*$th*$q;
                $tmpAggiuntaDimm = ($dim_disp*$mH*$th2) - $mA;
                
                if(($tmpAggiuntaDimm<$tmpAggiuntaDimm2) || $tmpAggiuntaDimm2==0) {
                  $tmpAggiuntaDimm2=$tmpAggiuntaDimm;
                  $tmpTotale=$tmpAggiuntaDimm*$diffPrez;
                  $def_dim_disp=$dim_disp;
                }
              }
            }
          }
          
          if($def_dim_disp>0){
            $this->setCarattNota($key, strtoupper($nomeCaratt).": bobina H=".($def_dim_disp*100)." CM");
          }
        } 
        $totale=$totale+$tmpTotale+$defSc;
      }
    }
    
    return $totale; 
  }
  
  function getAggiunteQ($combi="") {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    if($combi=="") $combi="ecomm_buffer";
    
    $aggiunte=$_SESSION['ecomm'][$current][$combi]['ecomm_arr_aggiunte_q'];
    if(!is_array($aggiunte)) return false;
    
    while (list($key, $row) = each($aggiunte)) {
      $totale=$totale+$row;
    }
    
    return $totale; 
  }
  
  function getAggiuntePeso($combi="") {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    if($combi=="") $combi="ecomm_buffer";
    
    $aggiunte=$_SESSION['ecomm'][$current][$combi]['ecomm_arr_aggiunte_p'];
    if(!is_array($aggiunte)) return false;
    
    while (list($key, $row) = each($aggiunte)) {
      $totale=$totale+$row;
    }
    
    return $totale; 
  }
  
  function addCurrentToCart() {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    $carrello=$_SESSION['ecomm'][$current][$combi];
    
    $articolo=retArticoloFromCat($current);
    $prezzo=$articolo['Prezzo_cry'];
    $sconto=$articolo['sconto'];
    if($_SESSION["userris_id"]>0) {
      $sconto=$this->scontoUser($_SESSION["userris_id"],$current);
      if($sconto==0) $sconto=$articolo['sconto_reg'];
    } 
    
    $aggiunte=$this->getAggiunte();
    $aggiunteF=$this->getAggiunteF();
    $aggiunteQ=$this->getAggiunteQ();
    $aggiunte_p=$this->getAggiuntePeso();
    $aggiunteDim=$this->getAggiunteDim();
    $aggiuntePerim=$this->getAggiuntePerim();
    $note_interne=$this->getCarattNote();
    
    $aggiunteF=$aggiunteF+$aggiunteDim;
    $aggiunteQ=$aggiunteQ+$aggiuntePerim;
    
    $prezzo_scontato=$prezzo-(($prezzo*$sconto)/100);
    
    $carrello['ecomm_prezzo']=parseToFloat($prezzo);
    $carrello['ecomm_sconto']=parseToFloat($sconto);
    if($carrello['ecomm_quantita']=="" || $carrello['ecomm_quantita']=="0") $carrello['ecomm_quantita']="1";
    $q=$carrello['ecomm_quantita'];
    $dim=$carrello['ecomm_dimensioni'];
    $area=$this->retAreaByDim($dim);
    if($area==0) $area=1;
    
    $prezzo_fin_senza_sconto=(($prezzo+$aggiunte)*$area*$q)+$aggiunteF+($aggiunteQ*$q);
    $prezzo_finale=(($prezzo_scontato+$aggiunte)*$area*$q)+$aggiunteF+($aggiunteQ*$q);
    
    if($prezzo_scontato<$articolo['Ordine_minimo_cry']) {
      $prezzo_fin_senza_sconto=$articolo['Ordine_minimo_cry'];
      $prezzo_finale=$articolo['Ordine_minimo_cry'];
      $prezzo_scontato=$articolo['Ordine_minimo_cry'];
      $sconto=0;
    }
    
    $carrello['ecomm_aggiunte']=parseToFloat($aggiunte);
    $carrello['ecomm_aggiunte_f']=parseToFloat($aggiunteF);
    $carrello['ecomm_aggiunte_q']=parseToFloat($aggiunteQ);
    $carrello['ecomm_aggiunte_p']=parseToFloat($aggiunte_p);
    $carrello['ecomm_aggiunte_dim']=parseToFloat($aggiunteDim);
    $carrello['ecomm_aggiunte_perim']=parseToFloat($aggiuntePerim);
    $carrello['ecomm_aggiunte_note']=$note_interne;
    
    $carrello['ecomm_prezzo_senza_sconto']=parseToFloat($prezzo_fin_senza_sconto);
    $carrello['ecomm_prezzo_finale']=parseToFloat($prezzo_finale);
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
    
    $combi=array_push($_SESSION['ecomm'][$current], $_SESSION['ecomm'][$current]['ecomm_buffer']);
  }
  
  function updateCurrent() {
    $current=$this->getCurrent();
    if($current===FALSE) return false;
    
    $combi="ecomm_buffer";
    $carrello=$_SESSION['ecomm'][$current][$combi];
    
    $articolo=retArticoloFromCat($current);
    $prezzo=$articolo['Prezzo_cry'];
    $sconto=$articolo['sconto'];
    if($_SESSION["userris_id"]>0) {
      $sconto=$this->scontoUser($_SESSION["userris_id"],$current);
      if($sconto==0) $sconto=$articolo['sconto_reg'];
    }
    $aggiunte=$this->getAggiunte();
    $aggiunteF=$this->getAggiunteF();
    $aggiunteQ=$this->getAggiunteQ();
    $aggiunte_p=$this->getAggiuntePeso();
    $aggiunteDim=$this->getAggiunteDim();
    $aggiuntePerim=$this->getAggiuntePerim();
    $note_interne=$this->getCarattNote();
    
    $aggiunteF=$aggiunteF+$aggiunteDim;
    $aggiunteQ=$aggiunteQ+$aggiuntePerim;
    
    $prezzo_scontato=$prezzo-(($prezzo*$sconto)/100);
    
    $carrello['ecomm_prezzo']=parseToFloat($prezzo);
    $carrello['ecomm_sconto']=parseToFloat($sconto);
    if($carrello['ecomm_quantita']=="" || $carrello['ecomm_quantita']=="0") $carrello['ecomm_quantita']="1";
    $q=$carrello['ecomm_quantita'];
    $dim=$carrello['ecomm_dimensioni'];
    $area=$this->retAreaByDim($dim);
    if($area==0) $area=1;
    
    $prezzo_fin_senza_sconto=(($prezzo+$aggiunte)*$area*$q)+$aggiunteF+($aggiunteQ*$q);
    $prezzo_finale=(($prezzo_scontato+$aggiunte)*$area*$q)+$aggiunteF+($aggiunteQ*$q);
    
    if($prezzo_scontato<$articolo['Ordine_minimo_cry']) {
      $prezzo_fin_senza_sconto=$articolo['Ordine_minimo_cry'];
      $prezzo_finale=$articolo['Ordine_minimo_cry'];
      $prezzo_scontato=$articolo['Ordine_minimo_cry'];
      $sconto=0;
    }
    
    $carrello['ecomm_aggiunte']=parseToFloat($aggiunte);
    $carrello['ecomm_aggiunte_p']=parseToFloat($aggiunte_p);
    $carrello['ecomm_aggiunte_f']=parseToFloat($aggiunteF);
    $carrello['ecomm_aggiunte_q']=parseToFloat($aggiunteQ);
    $carrello['ecomm_aggiunte_dim']=parseToFloat($aggiunteDim);
    $carrello['ecomm_aggiunte_perim']=parseToFloat($aggiuntePerim);
    $carrello['ecomm_aggiunte_note']=$note_interne;
    
    $carrello['ecomm_prezzo_senza_sconto']=parseToFloat($prezzo_fin_senza_sconto);
    $carrello['ecomm_prezzo_finale']=parseToFloat($prezzo_finale);
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
    $combi=$this->getCombi();
    $_SESSION['ecomm'][$current][$combi]=$carrello;
    unset($_SESSION['ecomm'][$current]['ecomm_buffer']);
  }
  
  function setCurrent($idarticolo,$combi="") {
    $this->g_current=$idarticolo;
    $this->g_combi=$combi;
  }
  
  function getCurrent() {
    $carrello=$_SESSION['ecomm'];
    if(!is_array($carrello)) $carrello=array();
    
    $current=$this->g_current;
    if($current=="") return false;
    
    if(!is_array($carrello[$current])) $carrello[$current]=array();
    $_SESSION['ecomm']=$carrello;
    
    return $current;
  }
  
  function getCombi() {
    $carrello=$_SESSION['ecomm'];
    if(!is_array($carrello)) $carrello=array();
    
    $combi=$this->g_combi;
    if($combi=="") return false;
    
    $current=$this->getCurrent();
    if(!is_array($carrello[$current][$combi])) $carrello[$current][$combi]=array();
    $_SESSION['ecomm']=$carrello;
    
    return $combi;
  }
  
  function verIVA($prz,$iva,$ver="") {
    if($ver==-1) return $prz;
    
    if($this->g_ivainclusa==0) $this->g_ivainclusa=$this->ivaInclusa();
    if($this->g_ivainclusa==1) $prz=$this->senzaIVA($prz,$iva);
    return $prz;    
  }
  
  function senzaIVA($prz,$iva) {
    $prz=parseToFloat($prz)/(1+(parseToFloat($iva)/100));
    return $prz;     
  }
  
  function conIVA($prz,$iva) {
    $prz=$prz*(1+($iva/100));
    return $prz;     
  }
  
  function scontoUser($iduser,$idcat){
    $sconto=0;
    $sconto_usr=getTable("sconti","sconto ASC","(attivo='1' AND id_users='".$iduser."' AND sconto_su_tutto='1')");
    if(count($sconto_usr)>0){
      $curdate = strtotime(date("Y-m-d"));
      if($sconto_usr[0]["data_inizio"]!="0000-00-00") $data_inizio = strtotime($sconto_usr[0]["data_inizio"]);else $data_inizio=false;
      if($sconto_usr[0]["data_fine"]!="0000-00-00") $data_fine = strtotime($sconto_usr[0]["data_fine"]);else $data_fine=false;
      
      if( ($data_inizio===FALSE || $data_inizio<=$curdate) && ($data_fine===FALSE || $data_fine>=$curdate)) $sconto=$sconto_usr[0]["sconto"];    
    }else{
      $sconto_usr=getTable("sconti","sconto ASC","(attivo='1' AND id_users='".$iduser."' AND id_categorie_str_magazzino='".$idcat."')");
      if(count($sconto_usr)==0){
        $scRamo=getRamo($idcat);
        $scRamo=array_reverse($scRamo);
        while (list($keyR, $rowR) = each($scRamo)) {
          $sconto_usr=getTable("sconti","sconto ASC","(attivo='1' AND id_users='".$iduser."' AND id_categorie_str_magazzino='".$rowR['id']."')");  
          if(count($sconto_usr)>0){
            $curdate = strtotime(date("Y-m-d"));
            if($sconto_usr[0]["data_inizio"]!="0000-00-00") $data_inizio = strtotime($sconto_usr[0]["data_inizio"]);else $data_inizio=false;
            if($sconto_usr[0]["data_fine"]!="0000-00-00") $data_fine = strtotime($sconto_usr[0]["data_fine"]);else $data_fine=false;
            
            if( ($data_inizio===FALSE || $data_inizio<=$curdate) && ($data_fine===FALSE || $data_fine>=$curdate)) $sconto=$sconto_usr[0]["sconto"];
              
            break;  
          }
        }    
      }else{
        $curdate = strtotime(date("Y-m-d"));
        if($sconto_usr[0]["data_inizio"]!="0000-00-00") $data_inizio = strtotime($sconto_usr[0]["data_inizio"]);else $data_inizio=false;
        if($sconto_usr[0]["data_fine"]!="0000-00-00") $data_fine = strtotime($sconto_usr[0]["data_fine"]);else $data_fine=false;
        
        if( ($data_inizio===FALSE || $data_inizio<=$curdate) && ($data_fine===FALSE || $data_fine>=$curdate)) $sconto=$sconto_usr[0]["sconto"];  
      }
    }
    
    return $sconto;
  }
  
  function totale($ver="") {
    $totali=array();
    if(!is_array($_SESSION['ecomm'])) return;
    reset($_SESSION['ecomm']);
    $totali['spese_spedizione']=parseToFloat($_SESSION["ecomm"]["spese_spedizione"]);
    $totali['pallet']=0;
    
    if($_SESSION['ecomm']['ritiro_in_sede']==1) $totali['spese_spedizione']=0;
          
    $iva_g=parseToFloat($_SESSION["ecomm"]["IVA"]);
    while (list($key, $current) = each($_SESSION['ecomm'])) {
      $rs=retRow("categorie",$key);
      if($rs!=false) {
        while (list($key2, $combi) = each($current)) {
          if($key2!=="ecomm_buffer") {
            $carrello=$combi;
            $articolo=retArticoloFromCat($key);
            $prezzo=$articolo['Prezzo_cry'];
            $peso=$articolo['peso'];
            $ingombro=$articolo['larghezza']*$articolo['altezza']*$articolo['profondita'];
            if($carrello['ecomm_peso']!="") $peso=parseToFloat($carrello['ecomm_peso']);
            if($carrello['ecomm_prezzo']!="") $prezzo=parseToFloat($carrello['ecomm_prezzo']); 
            $sconto=$articolo['sconto'];
            if($_SESSION["userris_id"]>0) {
              $sconto=$this->scontoUser($_SESSION["userris_id"],$key);
              if($sconto==0) $sconto=$articolo['sconto_reg'];
            }
            
            $_SESSION['ecomm'][$key][$key2]['ecomm_sconto']=$sconto;
            //if($carrello['ecomm_sconto']!="") $sconto=parseToFloat($carrello['ecomm_sconto']);
            $iva=$articolo['IVA'];
            if($carrello['ecomm_iva']!="") $iva=parseToFloat($carrello['ecomm_iva']);
            if($iva==0) $iva=$iva_g;
            if($iva==0) $iva=22; 
            
            $quantita=$carrello['ecomm_quantita'];
            $dim=$carrello['ecomm_dimensioni'];
            $aggiunte=parseToFloat($carrello['ecomm_aggiunte']);
            $aggiunteF=parseToFloat($carrello['ecomm_aggiunte_f']);
            $aggiunteQ=parseToFloat($carrello['ecomm_aggiunte_q']);
            $aggiunte_p=parseToFloat($carrello['ecomm_aggiunte_p']);
            $aggiunteDim=parseToFloat($carrello['ecomm_aggiunte_dim']);
            $aggiuntePerim=parseToFloat($carrello['ecomm_aggiunte_perim']);
            $note_interne=$carrello['ecomm_aggiunte_note'];
            
            $area=$this->retAreaByDim($dim);
            if($area==0) $area=1;
            
            $sconto_calc=(($prezzo+$aggiunte)*$sconto)/100;
            $sconto_aggiunteF=($aggiunteF*$sconto)/100;
            $sconto_aggiunteQ=($aggiunteQ*$sconto)/100;
            $prezzo_scontato=$prezzo+$aggiunte-$sconto_calc;
            
            $totale=($prezzo_scontato*$quantita*$area)+$aggiunteF-$sconto_aggiunteF+($aggiunteQ*$quantita)-($sconto_aggiunteQ*$quantita);
            $sconto_fin=($sconto_calc*$quantita*$area)+$sconto_aggiunteF+($sconto_aggiunteQ*$quantita);
            $aggiunte_fin=($aggiunte*$quantita*$area)+$aggiunteF-$sconto_aggiunteF+($aggiunteQ*$quantita)-($sconto_aggiunteQ*$quantita);
            
            if($totale<$articolo['Ordine_minimo_cry']) {
              $totale=$articolo['Ordine_minimo_cry'];
              $sconto_fin=0;
              $aggiunte_fin=0;
            }
            
            if($articolo['articolo_ingombrante']==1){
              $totali['pallet']=1;  
            }
            
            $totale_imponibile=$this->verIVA($totale,$iva);
            $iva_fin=($totale_imponibile*$iva)/100;
            
            $totali['totale']+=$totale;
            $totali['totale_imponibile']+=$totale_imponibile;
            $totali['somma_prezzi']+=$prezzo*$quantita*$area;
            $totali['iva']+=$iva_fin;
            $totali['sconto']+=$sconto_fin;
            $totali['aggiunte']+=$aggiunte_fin;
            //$totali['totale_senza_aggiunte']+=$prezzo_scontato*$quantita*$area;
            //$totali['totale_senza_sconto']+=(($prezzo+$aggiunte)*$quantita*$area)+$aggiunteF+($aggiunteQ*$quantita);
            $totali['totale_senza_aggiunte']+=$totale-$aggiunte_fin;
            $totali['totale_senza_sconto']+=$totale+$sconto_fin;
            $totali['peso']+=($peso+$aggiunte_p)*$quantita*$area;
            $totali['ingombro']+=($ingombro*$quantita);
          }
        }  
      }
    } 
    reset($_SESSION['ecomm']);
    $_SESSION['ecomm']['totale']=$totali['totale'];
    $totali['totale_imponibile']+=$this->verIVA($totali['spese_spedizione'],22);
    $_SESSION['ecomm']['imponibile']=$totali['totale_imponibile'];
    $_SESSION['ecomm']['ivaeuro']=$totali['iva'];
    $_SESSION['ecomm']['totale_imponibile']=$totali['totale_imponibile'];
    $_SESSION['ecomm']['spese_spedizione']=$totali['spese_spedizione'];
    return $totali;  
  }
  
  function updatePrezzi() {
    $current=$this->g_current;
    $combi="ecomm_buffer";
    $carrello=$_SESSION['ecomm'][$current][$combi];
    
    $articolo=retArticoloFromCat($current);
    $prezzo=$articolo['Prezzo_cry'];
    $sconto=$articolo['sconto'];
    if($_SESSION["userris_id"]>0) {
      $sconto=$this->scontoUser($_SESSION["userris_id"],$current);
      if($sconto==0) $sconto=$articolo['sconto_reg'];
    }
    
    $aggiunte=$this->getAggiunte();
    $aggiunteF=$this->getAggiunteF();
    $aggiunteQ=$this->getAggiunteQ();
    $aggiunte_p=$this->getAggiuntePeso();
    $aggiunteDim=$this->getAggiunteDim();
    $aggiuntePerim=$this->getAggiuntePerim();
    $note_interne=$this->getCarattNote(); 
    
    $aggiunteF=$aggiunteF+$aggiunteDim;
    $aggiunteQ=$aggiunteQ+$aggiuntePerim; 
    
    //$prezzo_scontato=$prezzo-(($prezzo*$sconto)/100);
    
    $carrello['ecomm_prezzo']=parseToFloat($prezzo);
    $carrello['ecomm_sconto']=parseToFloat($sconto);
    if($carrello['ecomm_quantita']=="" || $carrello['ecomm_quantita']=="0") $carrello['ecomm_quantita']="1";
    $q=$carrello['ecomm_quantita'];
    $dim=$carrello['ecomm_dimensioni'];
    
    $area=$this->retAreaByDim($dim);
    if($area==0) $area=1;
    
    $prezzo_fin_senza_sconto=(($prezzo+$aggiunte)*$area*$q)+$aggiunteF+($aggiunteQ*$q);
    $prezzo_scontato=$prezzo_fin_senza_sconto-(($prezzo_fin_senza_sconto*$sconto)/100);
    $prezzo_finale=(($prezzo_scontato+$aggiunte)*$area*$q)+$aggiunteF+($aggiunteQ*$q);
    
    if($prezzo_scontato<$articolo['Ordine_minimo_cry']) {
      $prezzo_fin_senza_sconto=$articolo['Ordine_minimo_cry'];
      $prezzo_finale=$articolo['Ordine_minimo_cry'];
      $prezzo_scontato=$articolo['Ordine_minimo_cry'];
      $sconto=0;
    }
    
    $carrello['ecomm_aggiunte']=parseToFloat($aggiunte);
    $carrello['ecomm_aggiunte_p']=parseToFloat($aggiunte_p);
    $carrello['ecomm_aggiunte_f']=parseToFloat($aggiunteF);
    $carrello['ecomm_aggiunte_q']=parseToFloat($aggiunteQ);
    $carrello['ecomm_aggiunte_dim']=parseToFloat($aggiunteDim);
    $carrello['ecomm_aggiunte_perim']=parseToFloat($aggiuntePerim);
    $carrello['ecomm_aggiunte_note']=$note_interne;
    
    $carrello['ecomm_prezzo_senza_sconto']=parseToFloat($prezzo_fin_senza_sconto);
    $carrello['ecomm_prezzo_finale']=parseToFloat($prezzo_scontato);
    
    $_SESSION['ecomm'][$current][$combi]=$carrello;
  }
  
  function setDataByVal($name,$val,$ord="") {
    $carrello=$_SESSION['ecomm'];
    
    $current=$this->getCurrent();
    if($current===false) return false;
    
    $carrello[$current]['ecomm_buffer'][$name]=$val; 
    $_SESSION['ecomm']=$carrello; 
    return $current; 
  }
  
  function getData($combi="") {
    $name=$_POST['myRsCarrelloget'];
    $current=$this->getCurrent();
    if($current===false) return false;
    
    $carrello=$_SESSION['ecomm'];
    if(!is_array($carrello)) return false;
    
    //Stampa le caratteristiche selezionate
    if($combi=="") $combi='ecomm_buffer'; 
    echo $carrello[$current][$combi][$name];  
  }
  
  function retData($name,$combi="") {
    $current=$this->getCurrent();
    if($current===false) return false;
    
    $carrello=$_SESSION['ecomm'];
    if(!is_array($carrello)) return false;
    
    //Stampa le caratteristiche selezionate
    if($combi=="") $combi='ecomm_buffer';
    return $carrello[$current][$combi][$name];  
  }
  
  function retGeografia($idnazione="",$idcomune="",$idregione_estera="") {
  	$nazione=array();
  	$comune=array();
  	$provincia=array();
  	$regione=array();
  	$regione_estera=array();
  	
  	if($idnazione>0) $nazione=retRow("nazioni",$idnazione);
    if($idcomune>0 && $idnazione==106) {
    	$comune=retRow("comuni",$idcomune);
	    $provincia=retRow("province",$comune['id_province']);
	    $regione=retRow("regioni",$provincia['id_regioni']);
    }
    if($idregione_estera>0) $regione_estera=retRow("regioni_estere",$idregione_estera);
    
    if($nazione['id']=="") $nazione['id']=-2;
    if($comune['id']=="") $comune['id']=-2;
    if($provincia['id']=="") $provincia['id']=-2;
    if($regione['id']=="") $regione['id']=-2; 
    if($regione_estera['id']=="") $regione_estera['id']=-2;
    
    if($nazione['id']!="106") {
      $comune['id']=-2;
      $provincia['id']=-2;
      $regione['id']=-2;
    }
    
    $localita=getTable("ecommerce_sp_localita","","(id_regioni_estere='".$regione_estera['id']."')");
    if(count($localita)==0) {
      $localita=getTable("ecommerce_sp_localita","","(id_comuni='".$comune['id']."')");
      if(count($localita)==0) {
        $localita=getTable("ecommerce_sp_localita","","(id_province='".$provincia['id']."')");
        if(count($localita)==0) {
          $localita=getTable("ecommerce_sp_localita","","(id_regioni='".$regione['id']."')");
          if(count($localita)==0) {
            $localita=getTable("ecommerce_sp_localita","","(id_nazioni='".$nazione['id']."')");  
          }  
        }  
      }
    }
    
    if(count($localita)>0) {
    	while (list($key, $row) = each($localita)) {
    		if($row["id"]>0) {
	        $zona=Table1ByTable2("ecommerce_sp_zone","ecommerce_sp_localita",$row['id'],"","");
	        if(count($zona)>0){
	        	$zona=$zona[0];
	        	break;
	        }
	      }
    	}
    }

    if($zona['id']=="") $zona['id']=-2;
    
    $geo=array();
    $geo['nazione']=$nazione;
    $geo['comune']=$comune;
    $geo['provincia']=$provincia;
    $geo['regione']=$regione;
    $geo['regione_estera']=$regione_estera;
    $geo['zona']=$zona;
    
    return $geo;
  }
  
  function retCorriere(){ 
    return $_SESSION['ecomm']['sp_corriere'];
  }
  
  function retAssicurazione(){ 
    return $_SESSION['ecomm']['sp_assicurazione'];
  }
  
  function retTariffa($idnazione="",$idcomune="",$idregione_estera="") {
    $geo=$this->retGeografia($idnazione,$idcomune,$idregione_estera);
    $corriere=$this->retCorriere();
    $assicurazione=$this->retAssicurazione();
      
    $nazione=$geo['nazione'];
    $comune=$geo['comune'];
    $provincia=$geo['provincia'];
    $regione=$geo['regione'];
    $regione_estera=$geo['regione_estera'];
    $zona=$geo['zona'];
    $corriere=$this->retCorriere();
    
    if(!($corriere["id"]>0)) return 0.1;
    
    $totali=$this->totale();
    
    $peso=$totali['peso'];
    $ingombro=$totali['ingombro']*pow(10, -9);
    
    $pallet=$totali['pallet'];
    
    $tariffa=Table1ByTable2_pointed("ecommerce_sp_tariffe","corrieri_list","corrieri",$corriere["id"],"","(id_regioni_estere='".$regione_estera['id']."')");
    if(count($tariffa)==0) {
      $tariffa=Table1ByTable2_pointed("ecommerce_sp_tariffe","corrieri_list","corrieri",$corriere["id"],"","(id_ecommerce_sp_zone='".$zona['id']."')");
      if(count($tariffa)==0) {
        $tariffa=Table1ByTable2_pointed("ecommerce_sp_tariffe","corrieri_list","corrieri",$corriere["id"],"","(id_comuni='".$comune['id']."')");
        if(count($tariffa)==0) {
          $tariffa=Table1ByTable2_pointed("ecommerce_sp_tariffe","corrieri_list","corrieri",$corriere["id"],"","(id_province='".$provincia['id']."')");
          if(count($tariffa)==0) {
            $tariffa=Table1ByTable2_pointed("ecommerce_sp_tariffe","corrieri_list","corrieri",$corriere["id"],"","(id_regioni='".$regione['id']."')");
            if(count($tariffa)==0) {
              $tariffa=Table1ByTable2_pointed("ecommerce_sp_tariffe","corrieri_list","corrieri",$corriere["id"],"","(id_nazioni='".$nazione['id']."')"); 
            }  
          }  
        }
      }
    }
    
    $tar=array();
    while (list($key, $row) = each($tariffa)) {
      $sp_peso=Table1ByTable2("ecommerce_sp_peso","ecommerce_sp_tariffe",$row['id'],"","");
      if(($sp_peso[0]['da']>0 || $sp_peso[0]['a']>0) && ($sp_peso[0]['ingombro_da']>0 || $sp_peso[0]['ingombro_a']>0) ) {
        if($sp_peso[0]['da']<=$peso && $sp_peso[0]['a']>=$peso && $sp_peso[0]['ingombro_da']<=$ingombro && $sp_peso[0]['ingombro_a']>=$ingombro) {
          array_push($tar, $row['tariffa_cry']+($sp_peso[0]['prezzo_pallet_cry']*$pallet));  
        }
      }
      
      if( ($sp_peso[0]['da']>0 || $sp_peso[0]['a']>0) && ($sp_peso[0]['ingombro_da']==0 && $sp_peso[0]['ingombro_a']==0) ) {
        if($sp_peso[0]['da']<=$peso && $sp_peso[0]['a']>=$peso) {
          array_push($tar, $row['tariffa_cry']+($sp_peso[0]['prezzo_pallet_cry']*$pallet));  
        }
      }
      
      if( ($sp_peso[0]['da']==0 && $sp_peso[0]['a']==0) && ($sp_peso[0]['ingombro_da']>0 || $sp_peso[0]['ingombro_a']>0) ) {
        if($sp_peso[0]['ingombro_da']<=$ingombro && $sp_peso[0]['ingombro_a']>=$ingombro) {
          array_push($tar, $row['tariffa_cry']+($sp_peso[0]['prezzo_pallet_cry']*$pallet));  
        }
      }
    }
    
    if(!(count($tar)>0)){
      $tariffa=getTable("ecommerce_sp_tariffe","","(id_regioni_estere='".$regione_estera['id']."')");
      if(count($tariffa)==0) {
        $tariffa=getTable("ecommerce_sp_tariffe","","(id_ecommerce_sp_zone='".$zona['id']."')");
        if(count($tariffa)==0) {
          $tariffa=getTable("ecommerce_sp_tariffe","","(id_comuni='".$comune['id']."')");
          if(count($tariffa)==0) {
            $tariffa=getTable("ecommerce_sp_tariffe","","(id_province='".$provincia['id']."')");
            if(count($tariffa)==0) {
              $tariffa=getTable("ecommerce_sp_tariffe","","(id_regioni='".$regione['id']."')");
              if(count($tariffa)==0) {
                $tariffa=getTable("ecommerce_sp_tariffe","","(id_nazioni='".$nazione['id']."')");  
              }  
            }  
          }
        }
      }
      
      while (list($key, $row) = each($tariffa)) {
        $sp_peso=Table1ByTable2("ecommerce_sp_peso","ecommerce_sp_tariffe",$row['id'],"","");
        if( ($sp_peso[0]['da']>0 || $sp_peso[0]['a']>0) && ($sp_peso[0]['ingombro_da']>0 || $sp_peso[0]['ingombro_a']>0) ) {
          if($sp_peso[0]['da']<=$peso && $sp_peso[0]['a']>=$peso && $sp_peso[0]['ingombro_da']<=$ingombro && $sp_peso[0]['ingombro_a']>=$ingombro) {
            array_push($tar, $row['tariffa_cry']+($sp_peso[0]['prezzo_pallet_cry']*$pallet));  
          }
        }
        
        if( ($sp_peso[0]['da']>0 || $sp_peso[0]['a']>0) && ($sp_peso[0]['ingombro_da']==0 && $sp_peso[0]['ingombro_a']==0) ) {
          if($sp_peso[0]['da']<=$peso && $sp_peso[0]['a']>=$peso) {
            array_push($tar, $row['tariffa_cry']+($sp_peso[0]['prezzo_pallet_cry']*$pallet));  
          }
        }
        
        if( ($sp_peso[0]['da']==0 && $sp_peso[0]['a']==0) && ($sp_peso[0]['ingombro_da']>0 || $sp_peso[0]['ingombro_a']>0) ) {
          if($sp_peso[0]['ingombro_da']<=$ingombro && $sp_peso[0]['ingombro_a']>=$ingombro) {
            array_push($tar, $row['tariffa_cry']+($sp_peso[0]['prezzo_pallet_cry']*$pallet));  
          }
        }
      }
    }
    
    if(count($tar)>0) {
      $maxtar=max($tar);
      //if($totali['totale']>49) $maxtar=0;
      
      if(is_array($corriere)){
        if($nazione["id"]!=106) $molt=$corriere['incremento_percentuale_internazionale'];
        if($nazione["id"]==106) $molt=$corriere['incremento_percentuale'];
        $maxtar=$maxtar+($maxtar*($molt/100));
      }
      
      if($assicurazione==1 && $_SESSION['ecomm']['sp_nazione']=="106") $maxtar=$maxtar+15;else $_SESSION['ecomm']['sp_assicurazione']=0; 
      
      return $maxtar;   
    }else {
      return false;  
    }
  }
  
  function UpdateOrder($esito) {
    global $config_table_prefix;
    $objMailing = new Mailing;
    
    $itemname = $_POST['item_name'];
    $idordine = $_POST['option_selection1'];
    if(isset($_GET['paypal_return'])) $idordine=$_GET['paypal_return'];
    if(isset($_GET['paypal_cancel_return'])) $idordine=$_GET['paypal_cancel_return'];
    
    $itemnum = $_POST['auth_id'];
    
    if(isset($_REQUEST['codTrans']) && isset($_REQUEST['esito'])) {
      $idordine = str_replace("00-", "", $_REQUEST['codTrans']);  
    }
    
    if($esito==-1) {
      $mess_conferma=getTable("ecommerce_testi","","(nome='messaggio errore pagamento' AND attivo='1')");
      $mess_conferma=$mess_conferma[0];
      
      $sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET id_ecommerce_stati='8' WHERE id='".$idordine."'";
      mysql_query($sql); 
    } elseif($esito==1) {
      $_SESSION['ecomm_trans']=$idordine; 
      $mess_conferma=getTable("ecommerce_testi","","(nome='messaggio conferma ordine' AND attivo='1')");
      $mess_conferma=$mess_conferma[0];
          
      $sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET id_ecommerce_stati='9' WHERE id='".$idordine."'";
      mysql_query($sql);
      $this->emptyCart();
      
      $email=getTable("ecommerce_ordini_email","","id_ecommerce_ordini='$idordine'");
      $email=$email[0];
      $objMailing->mmail($email['destinatario'],$email['mittente'],$email['oggetto'],$email['testo_editor'],retFileAbsolute($email['allegato_file']),"",$email['nome_allegato']);
    
    }
    
    return ln($mess_conferma['testo_editor']);  
  }
  
  function action() {  
    if(isset($_POST['ecomm_ver_email'])) {
      $arrCampi=json_decode(stripslashes($_POST['ecomm_ver_email']),true);
      
      $email=$arrCampi["email"];
      $pi=$arrCampi["piva"];
      $cf=$arrCampi["cf"];
      $tipo=$arrCampi["tipo"];
      $nazione=$arrCampi["nazione"];
      
      if(controllaEmail($email)==false) {echo "-1";exit;}
      if($nazione==106){
        if($tipo=="azienda" || $tipo=="associazione"){
          if(controllaPIVA($pi)==false) {echo "-2";exit;}
          if(controllaPIVA($cf)==false && controllaCF($cf)==false) {echo "-3";exit;}
        }else{
          if(controllaCF($cf)==false) {echo "-3";exit;}    
        }
      }
      
      $rs=getTable("users","","email='$email' AND ultimoaccesso IS NULL");
      if(count($rs)>0) {
        echo "0";
      }else{
        $rs=getTable("users","","email='$email'");
        if(count($rs)>0) { 
          echo "1";
        }
      }
      exit;
    }
    
    if(isset($_GET['keyclient_cancel_return'])) {
      global $config_table_prefix;

      $objHtml = new Html;
      $objUtility = new Utility;

      $sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET id_ecommerce_stati='10' WHERE id='".$_GET['keyclient_cancel_return']."'";
      mysql_query($sql);
      
      $objHtml->adminPageRedirect($objUtility->getPathRoot().getCurLanClass()."/pagamento-fallito.html","");
      exit;   
    }
    
    if(isset($_REQUEST['codTrans']) && isset($_REQUEST['esito'])) {
      $objHtml = new Html;
      $objUtility = new Utility;
      
      $id_ordini=str_replace("00-", "", $_REQUEST['codTrans']);
      
      $c=$this->valKeyClientCode($_REQUEST['codAut']);
      
      $ordine=getTable("ecommerce_keyclient","","code='".$_REQUEST['codAut']."'");
      $ordine=$ordine[0];
      if($ordine['pagamenti_riusciti']==1) {
        $mess_conferma=$this->UpdateOrder(1);
        $objHtml->adminPageRedirect($objUtility->getPathRoot().getCurLanClass()."/grazie.html",ln($mess_conferma));  
      } else {
        $mess_conferma=$this->UpdateOrder(-1);
        $objHtml->adminPageRedirect($objUtility->getPathRoot().getCurLanClass()."/pagamento-fallito.html",ln($mess_conferma));
      }
      exit;        
    }
    
    if(isset($_GET['paypal_return']) || isset($_GET['paypal_cancel_return']) ) {
      global $config_table_prefix;
      $objHtml = new Html;
      $objUtility = new Utility;
      
      if(isset($_GET['paypal_return'])) $esito=1;
      if(isset($_GET['paypal_cancel_return'])) $esito=-1;
      if(isset($_GET['paymentId'])) $this->valPayPalCode($_GET['paymentId'],$esito);
      
      $this->UpdateOrder($esito);
      
      $str="";

      if($esito==1){
        $this->emptyCart();
        $mess_conferma=getTable("ecommerce_testi","","(nome='messaggio conferma ordine' AND attivo='1')");
        $str=ln($mess_conferma[0]['testo_editor']);
        $dest_url="/grazie.html";
        
        $objHtml->adminPageRedirect($objUtility->getPathRoot().getCurLanClass().$dest_url,$str);
      }elseif($esito==-1){
        $mess_conferma=getTable("ecommerce_testi","","(nome='messaggio errore pagamento' AND attivo='1')");
        $str=ln($mess_conferma[0]['testo_editor']);
        //$dest_url="/pagamento-fallito.html";
        echo $str; 
      } 
      exit;
    } 
    
    if(isset($_POST['txn_id']) && !isset($_GET['paypal_return']) && !isset($_GET['paypal_cancel_return']) ) {
      $objHtml = new Html;
      $objUtility = new Utility;

      $this->valPayPalCode($_POST['txn_id']);
      $esito=$this->valPayPalIPN();
      $this->UpdateOrder($esito);
      exit;
    }
    
    if(isset($_POST['ecomm_set_totale'])) {
      $_SESSION["ecomm"]["totale"]=ParseToFloat($_POST['ecomm_set_totale']);
      exit;  
    }
    
    if(isset($_POST['ecomm_set_ivaeuro'])) {
      $_SESSION["ecomm"]["ivaeuro"]=ParseToFloat($_POST['ecomm_set_ivaeuro']);
      exit;  
    }
    
    if(isset($_POST['ecomm_set_imponibile'])) {
      $_SESSION["ecomm"]["imponibile"]=ParseToFloat($_POST['ecomm_set_imponibile']);
      exit;  
    }
    
    if($_POST['ecomm_get_totale']==1) {
      echo $_SESSION["ecomm"]["totale"];
      exit;  
    }
    
    if($_POST['ecomm_get_ivaeuro']==1) {
      echo $_SESSION["ecomm"]["ivaeuro"];
      exit;  
    }
    
    if($_POST['ecomm_get_imponibile']==1) {
      $totali=$this->totale();
      $_SESSION["ecomm"]["totale_imponibile"]-=$this->verIVA($totali['spese_spedizione'],22);
      echo $_SESSION["ecomm"]["imponibile"]; 
      exit;  
    }

    if($_POST['ecomm_get_ritiro_in_sede']==1) {
      echo $_SESSION["ecomm"]["ritiro_in_sede"];
      exit;  
    }
    
    if($_POST['ecomm_get_dati_geografici']==1) {
      echo $_SESSION["ecomm"]["sp_nazione"]."#_#";
      echo $_SESSION["ecomm"]["sp_comune"]."#_#";
      echo $_SESSION["ecomm"]["sp_regione_estera"];
      exit;
    }
    
    if(isset($_POST['ecomm_ritiro_in_sede'])) {
      $_SESSION["ecomm"]["ritiro_in_sede"]=$_POST['ecomm_ritiro_in_sede'];
      echo "0";
      echo "#_#";
      $this->tabellaIVA(106,-1,-1);
      exit;  
    }
    
    if($_POST['ecomm_get_iva']==1) {
      echo $_SESSION["ecomm"]["IVA"];
      exit;
    }
    
    if($_POST['ecomm_get_spese_spedizione']==1) {
      echo $_SESSION["ecomm"]["spese_spedizione"];
      exit;
    }
    
    if(isset($_POST['ecomm_newiva'])) {
      $_SESSION["ecomm"]["IVA"]=$_POST['ecomm_newiva'];
      exit;
    }
    
    if(isset($_POST['ecomm_spese_spedizione'])) {
      $_SESSION["ecomm"]["spese_spedizione"]=$_POST['ecomm_spese_spedizione'];
      exit;
    }
    
    if(isset($_POST['ecomm_sp_comune']) || isset($_POST['ecomm_sp_nazione'])){
      if(isset($_POST['ecomm_sp_nazione']) && $_POST['ecomm_sp_nazione']!="-1" && $_POST['ecomm_sp_regione_estera']!="-2") $_SESSION['ecomm']['sp_nazione']=$_POST['ecomm_sp_nazione'];
      if(isset($_POST['ecomm_sp_comune']) && $_POST['ecomm_sp_comune']!="-1" && $_POST['ecomm_sp_regione_estera']!="-2") $_SESSION['ecomm']['sp_comune']=$_POST['ecomm_sp_comune'];
      if(isset($_POST['ecomm_sp_regione_estera']) && $_POST['ecomm_sp_regione_estera']!="-1" && $_POST['ecomm_sp_regione_estera']!="-2") {
      	$_SESSION['ecomm']['sp_regione_estera']=$_POST['ecomm_sp_regione_estera'];
      }
      
      if($_POST['ecomm_sp_nazione']!=106 && ($_POST['ecomm_sp_regione_estera']=="-1" || $_POST['ecomm_sp_regione_estera']=="-2")) { ?>
        <div class="ez-last ez-oh spedizione-regione-estera form-inline-el">
          <div class="ez-box"><?php comboBox("regioni_estere",$field1="regione_en",$field2="",$selected=$_SESSION['ecomm']['sp_regione_estera'],$multiple="",$onchange="",$echoId="",$nome="sel_regione_estera",$where="id_nazioni='".$_POST['ecomm_sp_nazione']."'", $class="input2 select form-control sel-regione-estera form-inline-el"); ?></div>
        </div>
      <? exit;} ?>
      
      <?php
      $tariffa=$this->retTariffa($_POST['ecomm_sp_nazione'],$_POST['ecomm_sp_comune'],$_POST['ecomm_sp_regione_estera']);
      $_SESSION["ecomm"]["spese_spedizione"]=$tariffa;
      echo $tariffa;
      
      echo "#_#";
      //$this->tabellaIVA($_POST['ecomm_sp_nazione'],$_POST['ecomm_sp_comune'],$_POST['ecomm_sp_regione_estera']);
      $this->tabellaIVA(106,-1,-1);
      exit;  
    }
    
    if(isset($_POST['ecomm_sp_provincia'])){
      if($_POST['ecomm_sp_provincia']>0){
        $_SESSION['ecomm']['sp_provincia']=$_POST['ecomm_sp_provincia'];
        comboBox("comuni",$field1="comune",$field2="",$selected=$_SESSION['ecomm']['sp_comune'],$multiple="",$onchange="",$echoId="",$nome="sel_comune",$where="id_province='".$_POST['ecomm_sp_provincia']."'", $class="input2 select  form-control");
      }
      exit;
    }
    
    if(isset($_POST['ecomm_sp_provincia2'])){
      if($_POST['ecomm_sp_provincia2']>0){
        comboBox("comuni",$field1="comune",$field2="",$selected="",$multiple="",$onchange="",$echoId="",$nome="comune",$where="id_province='".$_POST['ecomm_sp_provincia2']."'", $class="input2 select form-control");
      }
      exit;
    }
    
    if(isset($_POST['setCorriere'])){
      if($_POST['setCorriere']>0){
        $corriere=retRow("corrieri",$_POST['setCorriere']);
        $corriere['sp_int']=0;
        $_SESSION['ecomm']['sp_corriere']=$corriere;
        echo str_replace(".", "_", $corriere['feedback']); 
      }
      exit;
    }
    
    if(isset($_POST['setAssicurazione'])){
      if($_POST['setAssicurazione']>0){
        $ass=$_POST['setAssicurazione'];
        $_SESSION['ecomm']['sp_assicurazione']=$ass;
      }
      exit;
    }
    
    if(isset($_POST['setCorriereInt'])){
      if($_POST['setCorriereInt']>0){
        $corriere=retRow("corrieri",$_POST['setCorriereInt']);
        $corriere['sp_int']=1;
        $_SESSION['ecomm']['sp_corriere']=$corriere;
        echo str_replace(".", "_", $corriere['feedback']); 
      }
      exit;
    }
    
    $current=$_POST['current'];
    $combi=$_POST['combi'];
    $this->setCurrent($current,$combi);
    
    if(isset($_POST['myRsCarrelloget'])) {
      $combi=$this->getCombi();
      $this->getData($combi);
      exit;    
    } 
      
    if(isset($_POST['myRsCarrello'])) {
      if($_POST['myRsCarrello']=="refreshPrezzi") {
        $this->updatePrezzi();
        echo $this->retData($_POST['value']);
        exit;
      }
       
      if($_POST['myRsCarrello']=="addCurrentToCart") {
        $this->addCurrentToCart();
        $p_totale=$_SESSION['ecomm']['viewcart']['p_totale'];
        $p_vai_cassa=$_SESSION['ecomm']['viewcart']['p_vai_cassa'];
        $url_vai_cassa=$_SESSION['ecomm']['viewcart']['url_vai_cassa'];
        $levelForName=$_SESSION['ecomm']['viewcart']['levelForName'];
        $menid=$_SESSION['ecomm']['viewcart']['menid'];
        $this->viewCart($p_totale,$p_vai_cassa,$url_vai_cassa,$levelForName,$menid);
        exit;
      }
        
      if($_POST['myRsCarrello']=="updateCurrent") {
        $this->updateCurrent();
        $p_totale=$_SESSION['ecomm']['viewcart']['p_totale'];
        $p_vai_cassa=$_SESSION['ecomm']['viewcart']['p_vai_cassa'];
        $url_vai_cassa=$_SESSION['ecomm']['viewcart']['url_vai_cassa'];
        $levelForName=$_SESSION['ecomm']['viewcart']['levelForName'];
        $menid=$_SESSION['ecomm']['viewcart']['menid'];
        $this->viewCart($p_totale,$p_vai_cassa,$url_vai_cassa,$levelForName,$menid);
        exit;
      }
             
      if($_POST['myRsCarrello']=="setCaratteristiche") {
        $tid=array();
        $id=$_POST['value'];
        
        if(strpos($id,"auto")===FALSE) {
          $id=explode("#", $id);
          $caratteristica=retRow("ecommerce_caratteristiche",$id[0]);
          $id=$id[1];
          $tid=explode(";", $id);
        } else {
          $id=explode("#", $id);
          $caratteristica=retRow("ecommerce_caratteristiche",$id[0]);
          $id=$id[1];
          if($id=="true") {
            $tid=getTable("ecommerce_valori","","idcaratteristiche_hidden='".$caratteristica['id']."'");
            $tid2=$tid[0]['id'];
            $tid=array();
            array_push($tid,$tid2);  
          }  
        }
        
        $ewiz_caratteristiche_list=retRow("ewiz_caratteristiche_list",$caratteristica['id_ewiz_caratteristiche_list']);
        $ecommerce_tipologie=retRow("ecommerce_tipologie",$ewiz_caratteristiche_list['id_ecommerce_tipologie']);
        $caratteristica_nome="id#".$caratteristica['id'];
        $this->setDataByVal($caratteristica_nome,$id); 
        
        $aggiunta=0;
        $aggiuntaf=0;
        $aggiuntaq=0;        
        $aggiunta_p=0;
        $gallery=array();
        $abbinamenti=array();
        
        if($ecommerce_tipologie['id']=="8"){
          $aggiunta=0;
          $aggiuntaf=0;
          $aggiuntaq=0;          
          
          $this->setQuantita($id);
          
          if($id>0) {
            $valori=getTable("ecommerce_valori","Ordinamento ASC","idcaratteristiche_hidden=".$caratteristica['id']);
  
            $v1=parseToFloat($valori[0]['nome']);
            $v2=parseToFloat($valori[1]['nome']);
            if($v2>$id) $v2=$id;
            
            $p1=(float)$valori[0]['differenza_prezzo_cry'];
            $p2=(float)$valori[1]['differenza_prezzo_cry'];
            
            $pf1=(float)$valori[0]['differenza_prezzo_fissa_cry'];
            $pf2=(float)$valori[1]['differenza_prezzo_fissa_cry'];
            
            $pq1=(float)$valori[0]['differenza_prezzo_quantita_cry'];
            $pq2=(float)$valori[1]['differenza_prezzo_quantita_cry'];                        
            
            $peso1=(float)$valori[0]['differenza_peso'];
            $peso2=(float)$valori[1]['differenza_peso'];
            
            if($v2-$v1!=0){
              $m=($p2-$p1)/($v2-$v1);
              $mf=($pf2-$pf1)/($v2-$v1);
              $mq=($pq2-$pq1)/($v2-$v1);
              $mp=($peso2-$peso1)/($v2-$v1);
              
              $aggiuntaf=(($id-$v1)*$mf)+$pf1;
              $aggiuntaq=(($id-$v1)*$mq)+$pq1;
              $aggiunta_p=(($id-$v1)*$mp)+$peso1;
              $aggiunta=(($id-$v1)*$m)+$p1;
            }
          }
        }
        
        if($ecommerce_tipologie['id']=="10"){
          $aggiunta=0;
          $aggiuntaf=0; 
          $aggiuntaq=0;          
          $aggiunta_p=0;
          
          $this->setQuantita($id);
          
          if($id>0) {
            $valori=getTable("ecommerce_valori","Ordinamento ASC","idcaratteristiche_hidden=".$caratteristica['id']);
  
            $v1=parseToFloat($valori[0]['nome']);
            $v2=parseToFloat($valori[1]['nome']);
            
            $p1=(float)$valori[0]['differenza_prezzo_cry'];
            $p2=(float)$valori[1]['differenza_prezzo_cry'];
            
            $pf1=(float)$valori[0]['differenza_prezzo_fissa_cry'];
            $pf2=(float)$valori[1]['differenza_prezzo_fissa_cry'];
            
            $pq1=(float)$valori[0]['differenza_prezzo_quantita_cry'];
            $pq2=(float)$valori[1]['differenza_prezzo_quantita_cry'];            
                        
            $peso1=(float)$valori[0]['differenza_peso'];
            $peso2=(float)$valori[1]['differenza_peso'];
            
            if($v2-$v1!=0) $m=($p2-$p1)/($v2-$v1);
            if($v2-$v1!=0) $m1=($peso2-$peso1)/($v2-$v1);
            
            if($v2-$v1!=0) $mf=($pf2-$pf1)/($v2-$v1);
            if($v2-$v1!=0) $mq=($pq2-$pq1)/($v2-$v1);
            
            $aggiuntaf=(($id-$v1)*$mf)+$pf1;
            $aggiuntaq=(($id-$v1)*$mq)+$pq1;
            $aggiunta=(($id-$v1)*$m)+$p1;
            $aggiunta_p=(($id-$v1)*$m1)+$peso1;
          }
        }
        
        if($ecommerce_tipologie['id']=="11"){
          $current=$this->getCurrent();
          $art=retArticoloFromCat($current);
          
          $aggiunta=0;
          $aggiuntaf=0;
          $aggiuntaq=0;
          $aggiunta_p=0;
          $aggiuntaDimm=0;
          $aggiuntaPerim=0;
          
          $this->setDimensioni($id);
          $area=$this->retAreaByDim($id);
          
          //$aggiunta=(parseToFloat($art['Prezzo_cry']))-parseToFloat($art['Prezzo_cry']);
          //$aggiunta_p=(parseToFloat($art['peso']))-parseToFloat($art['peso']);
          $aggiunta=0;
          $aggiunta_p=0;  
        }
        
        if($ecommerce_tipologie['id']=="9"){
          $current=$this->getCurrent();
          $art=retArticoloFromCat($current);
          
          $tmp_cmyk=str_replace("%", "", $id);
          $cmyk=explode("*",$tmp_cmyk);
          $copertura=($cmyk[0]+$cmyk[1]+$cmyk[2]+$cmyk[3])/4;
          
          $copertura=($copertura*100)/35;
          if($copertura>100) $copertura=100;
          $sc=(100-$copertura);
          if($sc<0) $sc=0; 
          if($sc>50) $sc=50;
          
          $aggiunta=($art['Prezzo_cry']*$sc)/100; 
          $aggiunta=-$aggiunta;  
        }
        
        if($ecommerce_tipologie['id']=="5"){
          if($id>0) {
            $valori=getTable("ecommerce_valori","Ordinamento ASC","idcaratteristiche_hidden=".$caratteristica['id']);
  
            $v1=parseToFloat($valori[0]['nome']);
            $v2=parseToFloat($valori[1]['nome']);
            
            $p1=(float)$valori[0]['differenza_prezzo_cry'];
            $p2=(float)$valori[1]['differenza_prezzo_cry'];
            
            $pf1=(float)$valori[0]['differenza_prezzo_fissa_cry'];
            $pf2=(float)$valori[1]['differenza_prezzo_fissa_cry'];
            
            $pq1=(float)$valori[0]['differenza_prezzo_quantita_cry'];
            $pq2=(float)$valori[1]['differenza_prezzo_quantita_cry'];
            
            $peso1=(float)$valori[0]['differenza_peso'];
            $peso2=(float)$valori[1]['differenza_peso'];
            
            if($v2-$v1!=0) $m=($p2-$p1)/($v2-$v1);
            if($v2-$v1!=0) $mf=($pf2-$pf1)/($v2-$v1);
            if($v2-$v1!=0) $mq=($pq2-$pq1)/($v2-$v1);
            if($v2-$v1!=0) $m1=($peso2-$peso1)/($v2-$v1);
            $aggiuntaf=(($id-$v1)*$mf)+$pf1;
            $aggiuntaq=(($id-$v1)*$mq)+$pq1;
            $aggiunta=(($id-$v1)*$m)+$p1;
            $aggiunta_p=(($id-$v1)*$m1)+$peso1;
          }
        }
        
        $current=$this->getCurrent();
        $art=retArticoloFromCat($current);
        $arrDim = explode("x", $this->getDimensioni());
        $tw=$arrDim[0]/100;
        $th=$arrDim[1]/100;
        
        while (list($key, $row) = each($tid)) {
          $tmpaggiunta=retRow("ecommerce_valori",$row);
          
          $aggiunta=$aggiunta+(float)$tmpaggiunta['differenza_prezzo_cry'];
          $aggiuntaf=$aggiuntaf+(float)$tmpaggiunta['differenza_prezzo_fissa_cry'];
          $aggiuntaq=$aggiuntaq+(float)$tmpaggiunta['differenza_prezzo_quantita_cry'];
          $aggiunta_p=$aggiunta_p+(float)$tmpaggiunta['differenza_peso'];
          
          if($tmpaggiunta['dimensioni_disponibili']!="") {
            $tdd=$tmpaggiunta['dimensioni_disponibili'];
            if(right($tdd,1)==";") $tdd=left($tdd,strlen($tdd)-1); 
            $tdimArr=explode(";",$tdd);
            
            $diffPrez=(float)$tmpaggiunta['differenza_prezzo_cry'];
            if($diffPrez==0) $diffPrez=(float)$art['Prezzo_cry'];
            
            $aggiuntaDimm=array();
            $aggiuntaDimm['prezzo']=$diffPrez;
            $aggiuntaDimm['dimArr']=$tdimArr;
            $aggiuntaDimm['orDimW']=$tw;
            $aggiuntaDimm['orDimH']=$th;
            $aggiuntaDimm['orNomeCaratt']=$tmpaggiunta['nome'];
          }
          
          if($tmpaggiunta['calcolo_perimetrale']=="1") {
            $perim=($tw*2)+($th*2);
            $aggiuntaPerim=$aggiunta*$perim;
            $aggiunta=0;        
          }
          
          $tmpfumetto.=tinybug(ln($tmpaggiunta['fumetto_editor']))."<br>";
          $tmpgallery=Table2ByTable1("ecommerce_valori","fotogallery",$tmpaggiunta['id'],"attivo='1'","Ordinamento ASC");
          $tmpabbinati=Table2ByTable1("ecommerce_valori","ecommerce_abbinamenti",$tmpaggiunta['id'],"attivo='1'","");
          
          array_unshift($tmpgallery, $tmpaggiunta);
          while (list($key2, $row2) = each($tmpgallery)) {
            if(retFile($row2['immagine_file'])) {
              $tmpgallery2="<a href='".retFile($row2['immagine_file'],$this->g_zoomImgW)."' class='crt-fotogallery-thm cloud-zoom-gallery' title='' rel=\"useZoom: 'crt-foto-articolo-zoom', smallImage: '".retFile($row2['immagine_file'],$this->g_smallImgW)."'\"><img src='".retFile($row2['immagine_file'],$this->g_smallGalleryImgW)."' zoom='".retFile($row2['immagine_file'],$this->g_zoomImgW)."' valore='$caratteristica_nome' idvalore='$id' idcaratteristica='".$caratteristica['id']."' title='".$tmpaggiunta['nome']."' /></a>";
              array_push($gallery, $tmpgallery2);
            }
          }
          
          while (list($key2, $row2) = each($tmpabbinati)) {
            array_push($abbinamenti, $row2);
          }
        }
        
        if($ecommerce_tipologie['id']!="11") {
          $tmpQ=$this->getArea();
        }else{
          $tmpQ=1; 
        }
        
        if($tmpQ==0) $tmpQ=1;
        
        $gallery=implode("#AA#", $gallery);
        $this->setAggiuntaPeso($caratteristica_nome,$aggiunta_p);
        $this->setAggiunta($caratteristica_nome,$aggiunta);
        $this->setAggiuntaF($caratteristica_nome,$aggiuntaf);
        $this->setAggiuntaQ($caratteristica_nome,$aggiuntaq);
        $this->setAggiuntaDim($caratteristica_nome,$aggiuntaDimm);
        $this->setAggiuntaPerim($caratteristica_nome,$aggiuntaPerim);
        
        $this->updatePrezzi();
        $newPrezzoSenzaSconto = $this->retData("ecomm_prezzo_senza_sconto");
        $newPrezzoFinale = $this->retData("ecomm_prezzo_finale");
        $newSconto = $this->retData("ecomm_sconto");
          
        echo currencyITA(($aggiunta*$tmpQ*$this->getQuantita())+$aggiuntaf+($aggiuntaq*$this->getQuantita()))."#AA134#".$gallery."#AA134#"."<div class='fumetto'>".$tmpfumetto."</div>"."#AA134#".rawurlencode(serialize($abbinamenti))."#AA134#".$tmpaggiunta['richiedi_quotazione']."#AA134#".$newPrezzoSenzaSconto."#AA134#".$newPrezzoFinale."#AA134#".$newSconto;
 
        exit;
      }
      
      if($_GET['ecomm_riepilogo']==1 && !isset($_POST['ecomm_pagamenti_do'])) {
        $this->riepilogoCarrello();
        return true;
      }
      
      exit;  
    }
    
    if($_POST['ecomm_post']=="1") {
        $voci_prodotto=$_POST;
        $combi=$_POST['ecomm_submit_aggiorna'];
        $p=$voci_prodotto['ecomm_prodotto'];
        if(isset($_POST['ecomm_submit_add'])) {
          if(!is_array($_SESSION['ecomm'][$p])) $_SESSION['ecomm'][$p]=array();
          array_push($_SESSION['ecomm'][$p], $voci_prodotto);
        } elseif($combi!="") {
          $_SESSION['ecomm'][$p][$combi]=$voci_prodotto;
        }
        return;
      }
      
      if($_POST['ecomm_post']=="2") {
        if(isset($_POST['ecomm_updq'])) {
          $prodotto=$_POST['ecomm_prodotto'];
          $combi=$_POST['ecomm_variante'];
          $quantita=parseToFloat($_POST['ecomm_qta']);
          if($quantita<=0)$quantita=1;
          
          $prezzo_finale=parseToFloat($_SESSION['ecomm'][$prodotto][$combi]['ecomm_prezzo_finale']);
          $quantita_old=parseToFloat($_SESSION['ecomm'][$prodotto][$combi]['ecomm_quantita']);
          $dim=$_SESSION['ecomm'][$prodotto][$combi]['ecomm_dimensioni'];
    
          $area=$this->retAreaByDim($dim);
          if($area==0) $area=1;
          
          $_SESSION['ecomm'][$prodotto][$combi]['ecomm_prezzo_finale']=($prezzo_finale/$quantita_old)*$quantita*$area;
          $_SESSION['ecomm'][$prodotto][$combi]['ecomm_quantita']=$quantita;
          return;
        }
        
        if(isset($_POST['ecomm_del'])) {
          $prodotto=$_POST['ecomm_prodotto'];
          $combi=$_POST['ecomm_variante'];
          unset($_SESSION['ecomm'][$prodotto][$combi]);
          if(count($_SESSION['ecomm'][$prodotto])==0) unset($_SESSION['ecomm'][$prodotto]);
          return;
        }
        exit;
      }
      
      if($_POST['ecomm_pagamenti_do']==1 && isset($_POST['ecomm_pagamento'])) {
        $objConfig = new ConfigTool();
        $objMailing = new Mailing;
        $objHtml = new Html;
        $objUtility = new Utility();
        
        global $config_table_prefix;
        
        $useTheme=$objConfig->get("use_themes");
        
        $mod=$_POST['ecomm_pagamento'];
        $pagamento=retRow("ecommerce_modalita_pagamenti",$mod);
        $email=$objConfig->get("email-from");
        $domain=substr($email, strpos($email,"@"));
        $user=$_SESSION['ecomm_user'];
        
        $totali=$this->totale();
        $imponibile=$totali['totale_imponibile'];
        if($imponibile==0) { echo "-1"; exit; }
        
        if($user['sel_regione_estera']!="") $user['regione_estera']=$user['sel_regione_estera'];
        
      	$idcitta=$user['comune'];
      	$tmpcitta=getTable("comuni", "", "(comune='".addslashes($idcitta)."')");
      	if(count($tmpcitta)>0) {
      		$idcitta=$tmpcitta[0]["id"];
      		$citta=$tmpcitta[0];
      	}else{
      		$citta=retRow("comuni",$idcitta);
      	}
      	
      	$idprovincia=$user['provincia'];
      	$tmpprovincia=getTable("province", "", "(sigla='".addslashes($idprovincia)."')");
      	if(count($tmpprovincia)>0) {
      		$idprovincia=$tmpprovincia[0]["id"];
      		$provincia=$tmpprovincia[0];
      	}else{
      		$provincia=retRow("province",$user['provincia']);
      	}
      	
      	$idregione_estera=$user['regione_estera'];
      	$tmpregione_estera=getTable("regioni_estere", "", "(regione='".addslashes($idregione_estera)."' OR regione_en='".addslashes($idregione_estera)."')");
      	if(count($tmpregione_estera)>0) {
      		$idregione_estera=$tmpregione_estera[0]["id"];
      		$regione_estera=$tmpregione_estera[0];
      	}else{
      		$regione_estera=retRow("regioni_estere",$idregione_estera);
      	}
      	
      	$idnazione=$user['nazione'];
      	$tmpnaz=getTable("nazioni", "", "(nazione='".addslashes($idnazione)."' OR naz_eng='".addslashes($idnazione)."')");
      	if(count($tmpnaz)>0) {
      		$idnazione=$tmpnaz[0]["id"];
      		$nazione=$tmpnaz[0];
      	}else{
      		$nazione=retRow("nazioni",$idnazione);
      	}
      	
        $sql="SELECT MAX(id) FROM `".$config_table_prefix."ecommerce_ordini`";
        $cod_vendita=mysql_query($sql);
        $cod_vendita=mysql_fetch_array($cod_vendita);
        $cod_vendita=$cod_vendita[0]+1;
        $_SESSION['ecomm_vendita']=$cod_vendita;
        
        for($zy=0;$zy<2;$zy++) { 
          $mess=""; 
          $mess_ut="";
      
          $tot=0;
          $totPeso=0;
          $tot_sc=0;
          $ivaTot=0; 
          $counter=0;                   
          $totali=$this->totale();
          $spese_spedizione=$totali['spese_spedizione'];
          reset($_SESSION['ecomm']);
          
          $mess.=ln("DATI CLIENTE",$zy).":";
          $mess.="<br><br>";
          
          $mess1="";
          if($user['reg']==-1) $mess1.="<span style='font-weight:bold;'>".ln("utente registrato",$zy)."</span><br>"; 
          if($user['reg']==0) $mess1.="<span style='font-weight:bold;'>".ln("utente non registrato",$zy)."</span><br>"; 
          if($user['reg']==1) $mess1.="<span style='font-weight:bold;'>".ln("utente appena registrato",$zy)."</span><br>"; 
          if($user['reg']==1) $mess1.="user: ".$user['login'];
          
          $mess1.="<br><br>";
          
          $mess=$mess1.$mess;
          
          if($user['ragionesociale']!="") $mess.="<table><tr><td width='120'>".ln("ragione sociale",$zy)."</td><td> <b>".$user['ragionesociale']."</b></td></tr></table>";
          if($user['cognome']!="") $mess.="<table><tr><td width='120'>".ln("cognome",$zy)."</td><td> <b>".$user['cognome']."</b></td></tr></table>";
          if($user['nome']!="") $mess.="<table><tr><td width='120'>".ln("nome",$zy)."</td><td> <b>".$user['nome']."</b></td></tr></table>";
          if($user['codicefiscale']!="") $mess.="<table><tr><td width='120'>".ln("codice fiscale",$zy)."</td><td> <b>".$user['codicefiscale']."</b></td></tr></table>";
          if($user['partitaiva']!="") $mess.="<table><tr><td width='120'>".ln("partita iva",$zy)."</td><td> <b>".$user['partitaiva']."</b></td></tr></table>";
          if($user['codice_destinatario']!="") $mess.="<table><tr><td width='120'>".ln("codice destinatario",$zy)."</td><td> <b>".$user['codice_destinatario']."</b></td></tr></table>";
          if($user['pec']!="") $mess.="<table><tr><td width='120'>".ln("PEC",$zy)."</td><td> <b>".$user['pec']."</b></td></tr></table>";
          if($user['indirizzo']!="") $mess.="<table><tr><td width='120'>".ln("indirizzo",$zy)."</td><td> <b>".$user['indirizzo']."</b></td></tr></table>";
          if($user['cap']!="") $mess.="<table><tr><td width='120'>".ln("cap",$zy)."</td><td> <b>".$user['cap']."</b></td></tr></table>";
          
          if($idnazione==106 && $citta['comune']!="") $mess.="<table><tr><td width='120'>".ln("comune",$zy)."</td><td> <b>".$citta['comune']."</b></td></tr></table>";
          if($user['citta']!="") $mess.="<table><tr><td width='120'>".ln("località",$zy)."</td><td> <b>".$user['citta']."</b></td></tr></table>";
          if($user['provincia']!="" && $idnazione==106) $mess.="<table><tr><td width='120'>".ln("provincia",$zy)."</td><td> <b>".$provincia['sigla']."</b></td></tr></table>";
          if($idnazione!=106) $mess.="<table><tr><td width='120'>".ln("provincia",$zy)."</td><td> <b>".$user['provincia_estera']."</b></td></tr></table>";
          if($nazione['nazione']!="") $mess.="<table><tr><td width='120'>".ln("nazione",$zy)."</td><td> <b>".$nazione['nazione']."</b></td></tr></table>";
          if($idnazione!=106 && $regione_estera['regione_en']!="") $mess.="<table><tr><td width='120'>".ln("regione estera",$zy)."</td><td> <b>".$regione_estera['regione_en']."</b></td></tr></table>";
          
          $mess.="<table><tr><td width='120'>".ln("telefono",$zy)."</td><td> <b>".$user['telefono']."</b></td></tr></table>";
          $mess.="<table><tr><td width='120'>".ln("e-mail",$zy)."</td><td> <b>".$user['email']."</b></td></tr></table>";
          $mess.="<br>";  
          
          if(isset($_POST['altra'])) {
            $mess.="<p style='color:red;'>";
            $mess.=ln("LA MERCE DOVRÀ ESSERE SPEDITA AL SEGUENTE INDIRIZZO",$zy).":";
            $mess.="<br><br>";
            if($_POST['altra_cognome']!="") $mess.="<table><tr><td width='140'>".ln("cognome",$zy)."</td><td> <b>".$_POST['altra_cognome']."</b></td></tr></table>";
            if($_POST['altra_nome']!="") $mess.="<table><tr><td width='140'>".ln("nome",$zy)."</td><td> <b>".$_POST['altra_nome']."</b></td></tr></table>";
            if($_POST['altra_indirizzo']!="") $mess.="<table><tr><td width='140'>".ln("indirizzo",$zy)."</td><td> <b>".$_POST['altra_indirizzo']."</b></td></tr></table>";
            if($_POST['altra_cap']!="") $mess.="<table><tr><td width='140'>".ln("cap",$zy)."</td><td> <b>".$_POST['altra_cap']."</b></td></tr></table>";
            if(strtolower($_POST['altra_nazione'])=="italy" && $_POST['altra_comune']!="") $mess.="<table><tr><td width='140'>".ln("comune",$zy)."</td><td> <b>".$_POST['altra_comune']."</b></td></tr></table>";
            if($_POST['altra_loc']!="") $mess.="<table><tr><td width='140'>".ln("località",$zy)."</td><td> <b>".$_POST['altra_loc']."</b></td></tr></table>";
            if($_POST['altra_prov']!="") $mess.="<table><tr><td width='140'>".ln("provincia",$zy)."</td><td> <b>".$_POST['altra_prov']."</b></td></tr></table>";
            if($_POST['altra_nazione']!="") $mess.="<table><tr><td width='140'>".ln("nazione",$zy)."</td><td> <b>".$_POST['altra_nazione']."</b></td></tr></table>";
            if(strtolower($_POST['altra_nazione'])!="italy" && $_POST['altra_regione_estera']!="") $mess.="<table><tr><td width='140'>".ln("regione estera",$zy)."</td><td> <b>".$_POST['altra_regione_estera']."</b></td></tr></table>";
            if($_POST['altra_telefono']!="") $mess.="<table><tr><td width='140'>".ln("telefono",$zy)."</td><td> <b>".$_POST['altra_telefono']."</b></td></tr></table>";
            $mess.="<br><br>";
            $mess.="</p>";
            
            $tmpcom=$_POST['altra_comune'];
            if(strtolower($_POST['altra_nazione'])!="italy") $tmpcom=""; 
            $sql="INSERT INTO ".$config_table_prefix."ecommerce_ordini_altra_destinazione (cognome,nome,indirizzo,cap,citta,provincia,paese,comune,regione_estera,telefono) VALUES (";
            $sql.="'".$_POST['altra_cognome']."',";
            $sql.="'".$_POST['altra_nome']."',";
            $sql.="'".$_POST['altra_indirizzo']."',";
            $sql.="'".$_POST['altra_cap']."',";
            $sql.="'".$_POST['altra_loc']."',";
            $sql.="'".$_POST['altra_prov']."',";
            $sql.="'".$_POST['altra_nazione']."',";
            $sql.="'".$tmpcom."',";
            $sql.="'".$_POST['altra_regione_estera']."',";
            $sql.="'".$_POST['altra_telefono']."')";
            if($zy==1) {
              mysql_query($sql);
              $idaltra_dest=mysql_insert_id();
            }
          }
          
          $mess.=ln("modalità di pagamento",$zy).": <b>".ln($pagamento['nome'],$zy)."</b><br>";
          $mess.="#INFO-DATI-PAGAMENTO#";
          $mess.="<br><br>";
          
          reset($_SESSION['ecomm']);
          while (list($key, $prodotto) = each($_SESSION['ecomm'])) { 
            $n=count($_SESSION['ecomm'][$key]);
            $magazzino=retRow("categorie",$key);
            $str=getStrutturaByNodo($key);
            if($magazzino!=false && $str['nome']=="magazzino") {
              $layout=retRow("gestione_layout",$magazzino['id_gestione_layout']);
              $nome_prodotto=$magazzino['nome'];
              
              if($magazzino['immagine_file']==0) {
                $gallery=Table2ByTable1("categorie","fotogallery",$key,"attivo='1'","Ordinamento ASC LIMIT 1");
                $magazzino['immagine_file']=$gallery[0]['immagine_file'];
              }
              
              if($useTheme=="1"){
                $tmpl_products=getTable("tmpl_products","","attivo=1 AND id_categorie_str_magazzino='".$key."'"); 
                $foto_prodotto=convertToJpg(retFileAbsolute($tmpl_products[0]['immagine_file'],400));               
              }
              
              if(!$foto_prodotto){ 
                if($magazzino['immagine_file']!=0) {
                  $foto_prodotto=convertToJpg(retFileAbsolute($magazzino['immagine_file'],400)); 
                }else{
                  $foto_prodotto=$objUtility->getPathBackofficeResources()."nofoto.jpg";  
                }
              }
              
              $ecommerce_generali=getTable("magazzino_articoli","","(id_categorie_str_magazzino='$key' AND Prezzo_cry>0 AND del_hidden='0')");
              $prezzo=$ecommerce_generali[0]['Prezzo_cry'];
              $peso=$ecommerce_generali[0]['peso'];
              $sconto=$ecommerce_generali[0]['sconto'];
              $ord_min=$ecommerce_generali[0]['Ordine_minimo_cry'];
              if($_SESSION["userris_id"]>0) {
                $sconto=$this->scontoUser($_SESSION["userris_id"],$key);
                if($sconto==0) $sconto=$ecommerce_generali[0]['sconto_reg'];
              }
              
              $codice=$ecommerce_generali[0]['Codice'];
              $iva_perc=$ecommerce_generali[0]['IVA'];
              $id=$ecommerce_generali[0]['id'];
              if($iva_perc==0) $iva_perc=22;
              
              while (list($key2, $variante) = each($prodotto)) { 
                if($key2!=="ecomm_buffer") {
                  $counter++;
                  $link=$variante['ecomm_link'];
                  if($link=="") $link=$layout['file']."?menid=$key&ecomm_combi=$key2";
                  
                  $image=$variante['ecomm_image'];
                  if($image!="") {
                    $foto_prodotto=$objUtility->getPathRoot().$image;
                  }
                  
                  if(isset($variante['ecomm_prezzo']) && $variante['ecomm_prezzo']!="") $prezzo=$variante['ecomm_prezzo'];
                  if(isset($variante['ecomm_sconto'])&& $variante['ecomm_sconto']!="") $sconto=$variante['ecomm_sconto'];
                  if(isset($variante['ecomm_peso'])&& $variante['ecomm_peso']!="") $peso=$variante['ecomm_peso'];
                  if(isset($variante['ecomm_iva_perc'])&& $variante['ecomm_iva_perc']!="") $iva_perc=$variante['ecomm_iva_perc'];
                  if(isset($_SESSION["ecomm"]["IVA"]) && $_SESSION["ecomm"]["IVA"]!="") $iva_perc=$_SESSION["ecomm"]["IVA"];  
                  if(isset($variante['ecomm_nome']) && $variante['ecomm_nome']!="") $nome_prodotto=$variante['ecomm_nome'];
                  
                  $prezzo=parseToFloat($prezzo);
                  
                  $quantita=$variante['ecomm_quantita'];
                  $dim=$variante['ecomm_dimensioni'];
                  $aggiunte=$variante['ecomm_aggiunte'];
                  $aggiunteF=$variante['ecomm_aggiunte_f'];
                  $aggiunteQ=$variante['ecomm_aggiunte_q'];
                  $aggiunte_p=$variante['ecomm_aggiunte_p'];
                  $aggiunteDim=$variante['ecomm_aggiunte_dim'];
                  $prezzo_finale=$variante['ecomm_prezzo_finale'];

                  if($zy==1 && $variante['ecomm_aggiunte_note']!="") $note_interne=$note_interne."<b>".strtoupper($nome_prodotto).":</b><br> Pz. ".$quantita."<br> Dim. ".$dim." CM<br> ".$variante['ecomm_aggiunte_note']."<br><br> ";

                  $area=$this->retAreaByDim($dim);
                  if($area==0) $area=1;

                  $tot=$tot+$prezzo_fin_senza_sconto;
                  $totPeso=$totPeso+(($peso+$aggiunte_p)*$quantita*$area);
                  $tot_sc=$tot_sc+$prezzo_finale;
                  $iva=($prezzo_finale*$iva_perc)/100;
                  $ivaTot=$ivaTot+$iva;

                  $sconto_calc=(($prezzo+$aggiunte)*$sconto)/100;
                  $sconto_aggiunte=($aggiunte*$sconto)/100;
                  $sconto_aggiunteF=($aggiunteF*$sconto)/100;
                  $sconto_aggiunteQ=($aggiunteQ*$sconto)/100;
                  $prezzo_scontato=$prezzo-(($prezzo*$sconto)/100);
                  
                  $prezzo_fin_senza_sconto=(($prezzo+$aggiunte)*$quantita*$area)+$aggiunteF+($aggiunteQ*$quantita);
                  
                  $prezzo_finale=($prezzo_scontato*$quantita*$area)+(($aggiunte-$sconto_aggiunte)*$quantita*$area)+($aggiunteF-$sconto_aggiunteF)+(($aggiunteQ-$sconto_aggiunteQ)*$quantita);
                                    
                  if($prezzo_finale<$ord_min) {
                    $prezzo_fin_senza_sconto=$ord_min;
                    $prezzo_finale=$ord_min;
                    $prezzo_scontato=$ord_min;
                    $sconto=0;
                  }
                  
                  if($codice!="") $tmpcodice=mb_strtoupper($codice)." - ";else $tmpcodice="";
                  
                  if($foto_prodotto!="" && $foto_prodotto!="0") {
                    $mess.="<table><tr><td><img src='".$foto_prodotto."' width=150 style='width:150px;border:1px #DDD solid;' /></td><td>";
                  }
                  
                  $mess.="<table><tr><td width='140'>".ln("Cod. articolo",$zy)."</td><td> <b>".$codice."</b></td></tr></table>";
                  $mess.="<table><tr><td width='140'>".ln("Descrizione",$zy)."</td><td> <b>".ln($nome_prodotto,$zy)."</b></td></tr></table>";
                  
                  $dett1="";
                  
                  ksort($variante);
                  $addbr=0;
                  while (list($key3, $caratteristica) = each($variante)) {
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
                          }elseif($tmp_tipo=="8"){
                            $tmp_qAdded=1;  
                          }
                        }
                        
                        $tmpkey3=str_replace("ecomm_","",$key3);
                        $tmpkey3=str_replace("_","&nbsp;",$key3);
                        if($caratteristica!="") $dett1.="<table><tr><td width='140'>".ln($tmpkey3,$zy)."</td><td> <b>".ln($caratteristica,$zy)."</b></td></tr></table>";
                      } else {
                        while (list($key4, $value) = each($caratteristica)) {
                          $cnome=retRow("ecommerce_valori",$value);
                          $caratteristica[$key4]=ln($cnome['nome'],$zy);
                        }
                        $tmpkey3=str_replace("ecomm_","",$key3);
                        $tmpkey3=str_replace("_","&nbsp;",$key3);
                        if(implode(", ",$caratteristica)!="") $dett1.="<table><tr><td width='140'>".ln($tmpkey3,$zy)."</td><td> <b>".implode(", ",$caratteristica)."</b></td></tr></table>";
                      } 
                    }
                  }         
                  
                  $mess.=$dett1;
                  
                  if($tmp_qAdded!=1) $mess.="<table><tr><td width='140'>".ln("Quantità",$zy)."</td> <td><b>N. ".$quantita."</b></td></tr></table>";
                  
                  $mess.="<table><tr><td width='140'>".ln("Totale articolo",$zy)."</td> <td><b>&euro; ".currencyITA($prezzo_finale)."</b></td></tr></table>";
                  $mess.="</td></tr></table>";
                  
                  
                  $sql="INSERT INTO ".$config_table_prefix."acquisti (user_hidden,codice_vendita,data,ora,id_magazzino_articoli,dettagli_editor,quantita,prezzo_cry,aggiunte_cry,sconto,prezzo_scontato_cry) VALUES (";
                  $sql.="'".$user['id']."',";
                  $sql.="'$cod_vendita',";
                  $sql.="CURDATE(),";
                  $sql.="CURTIME(),";
                  $sql.="'".$id."',";
                  $sql.="'".addslashes($dett1)."',";
                  $sql.="'".parseToFloat($quantita)."',";
                  $sql.="'".parseToFloat(($prezzo*$quantita*$area))."',";
                  $sql.="'".parseToFloat(($aggiunte*$quantita*$area)+$aggiunteF+($aggiunteQ*$quantita))."',";
                  $sql.="'".$sconto."',";
                  $sql.="'".parseToFloat($prezzo_scontato*$quantita*$area)."')";
                  if($zy==1) $ret=mysql_query($sql);
                }			
              }
            }
          }
          
          if($_SESSION['ecomm']['ritiro_in_sede']!=1) {
            $ritiro_in_sede=0;
            
            $tmpnazione=retRow("nazioni",$_SESSION["ecomm"]["sp_nazione"]);
            $tmpcomune=retRow("comuni",$_SESSION["ecomm"]["sp_comune"]);
            $tmpregione=retRow("regioni_estere",$_SESSION["ecomm"]["sp_regione_estera"]);
            
            $mess.="<br><br>"; 
          } else {
            $ritiro_in_sede=1;
          }
          
          $sql="INSERT INTO ".$config_table_prefix."ecommerce_ordini_spedizione (nazione,comune,regione_estera,ritiro_in_sede,prezzo_cry) VALUES (";
          $sql.="'".$tmpnazione['nazione']."',";
          $sql.="'".$tmpcomune["comune"]."',";
          $sql.="'".$tmpregione["regione_en"]."',";
          $sql.="'".$ritiro_in_sede."',";
          $sql.="'".ParseToFloat($spese_spedizione)."')";
          if($zy==1) {
            mysql_query($sql);
            $idspedizione=mysql_insert_id();
          }
          
          $imponibile=$totali['totale_imponibile'];
          
          $tot_iva=0;
	        $iva_sped=(ParseToFloat($this->verIVA($spese_spedizione,22))*22)/100;
	        $tot_iva=ParseToFloat($totali['iva'])+ParseToFloat($iva_sped);
          
          $tot_merce=ParseToFloat($totali['totale']);
          $tot_f=ParseToFloat($imponibile)+ParseToFloat($tot_iva);
          
          $mess.="<br><p style='color:red;'>";
          $mess.=ln("TOTALI",$zy)."</p>";
          $mess.="<br>";
          $mess.="<table><tr><td width='140'>".ln("Totale merce",$zy)."</td> <td><b>&euro; ".currencyITA($tot_merce)."</b></td></tr></table>";
          if(ParseToFloat($spese_spedizione)>0) {
            $corriere=$this->retCorriere();
            $assicurazione=$this->retAssicurazione();
            if($assicurazione==1) $assicurazione=ln("sì",$zy);else $assicurazione=ln("no",$zy);
            
            $mess.="<table><tr><td width='140'>".ln("Spese di spedizione",$zy)." <b>".$corriere["nome"]."</b> (";
            $mess.=$tmpnazione['naz_eng'];
            if($_SESSION["ecomm"]["sp_comune"]!="" && $_SESSION["ecomm"]["sp_nazione"]=="106") $mess.=", ".$tmpcomune["comune"];
            if($_SESSION["ecomm"]["sp_regione_estera"]!="") $mess.=", ".$tmpregione["regione_en"];
            $mess.=")";
            $mess.="</td> <td><b>&euro; ".currencyITA($spese_spedizione)."</b></td></tr></table>";
            
            $mess.="<table><tr><td width='140'>".ln("Assicurazione",$zy);
            $mess.="</td> <td><b> ".$assicurazione."</b></td></tr></table>";
          }
          $mess.="-----------------------------------------------------------------------------------------------------------------------------------------------------------------";
          $mess.="<table><tr><td width='140' style='font-size:14px;'>".ln("Totale Imponibile",$zy)."</td> <td width='140'><b>&euro; ".currencyITA(ParseToFloat($imponibile))."</b><td width='60'>".ln("IVA",$zy)."</td> <td width='140'><b>&euro; ".(currencyITA($tot_iva))."</b></td></td><td width='140'>".ln("Totale",$zy)."</td> <td><b>&euro; ".currencyITA($tot_f)."</b></td></tr></table>";
          $mess.="<br><br>";
          $mess="<style>table tr td {font-family:Arial;}</style><div style='font-family:Arial;'>".$mess."</div>";
          $mess_ut=$mess;
          $mess_ins=str_replace("#INFO-DATI-PAGAMENTO#", "", $mess);
          $mess_ins=str_replace("<style>table tr td {font-family:Arial;}</style>", "", $mess_ins);
          
          $sql="INSERT INTO ".$config_table_prefix."ecommerce_ordini (user_hidden,altra_destinazione_hidden,spedizione_hidden,documents_hidden,codice_vendita,riepilogo_editor,data,ora,id_ecommerce_stati,note_interne_text,totale_cry) VALUES (";
          $sql.="'".$user['id']."',";
          $sql.="'".$idaltra_dest."',";
          $sql.="'".$idspedizione."',";
          $sql.="'".$iddocs."',";
          $sql.="'$cod_vendita',";
          $sql.="'".addslashes($mess_ins)."',";
          $sql.="CURDATE(),";
          $sql.="CURTIME(),";
          $sql.="'5',";
          $sql.="'".addslashes($note_interne)."',";
          $sql.="'".ParseToFloat($tot_f)."')"; 
            
          if($zy==0) {
            mysql_query($sql);
            $idordine=mysql_insert_id();
            
            if($cod_vendita!=$idordine) {
              $sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET codice_vendita='$idordine' WHERE id='$idordine'";
              mysql_query($sql);
              $cod_vendita=$idordine;
              $_SESSION['ecomm_vendita']=$cod_vendita;  
            }
            
            $sql="UPDATE ".$config_table_prefix."ecommerce_ordini_email SET id_ecommerce_ordini='$idordine' WHERE id='$idemailutente'";
            mysql_query($sql);
          }
          
          $riep=getTable("ecommerce_testi","","(nome='email di riepilogo per acquirente' AND attivo='1')");
          $riep=$riep[0];
          
          $riep['testo_editor']=ln($riep['testo_editor'],$zy);
              
          $riep['testo_editor']=str_replace("#RIEPILOGO#", $mess, $riep['testo_editor']);
          $riep['testo_editor']=str_replace("#DATA#", dataIta(), $riep['testo_editor']);
          $riep['testo_editor']=str_replace("#ORDINE#", $idordine, $riep['testo_editor']);
          $riep['testo_editor']=str_replace("#ORDINE-EMAIL#", $user['email'], $riep['testo_editor']);
          
          $mess_ut=$riep['testo_editor'];
          $mess_ut=replaceEcomerceMarkers(str_replace($mess1, "", $mess_ut));
          
          $riep=getTable("ecommerce_testi","","(nome='email di riepilogo per venditore' AND attivo='1')");
          $riep=$riep[0];
          
          $riep['testo_editor']=str_replace("#RIEPILOGO#", $mess, $riep['testo_editor']);
          $riep['testo_editor']=str_replace("#DATA#", dataIta(), $riep['testo_editor']);
          $riep['testo_editor']=str_replace("#ORDINE#", $idordine, $riep['testo_editor']);
          $riep['testo_editor']=str_replace("#ORDINE-EMAIL#", $user['email'], $riep['testo_editor']);
          
          $mess=replaceEcomerceMarkers($riep['testo_editor']);
          
          if($zy==1) {
            $mess=str_replace("#INFO-DATI-PAGAMENTO#", "<p><b>NOTE:</b><br><br>".$note_interne."</p>", $mess); 
            
            $idpdf=genPDFbyHTML(utf8_decode($mess),0,ln("Ordine N. ",$zy).$idordine.ln(" DEL ",$zy).dataIta().".pdf");
            $sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET pdf_file='".$idpdf."', riepilogoITA_editor='".addslashes($mess)."' WHERE id='$idordine'";
            mysql_query($sql);
            $objMailing->mmail($email,"ecommerce".$domain,ln("Ordine N. ",$zy).$idordine.ln(" da ",$zy).$_SERVER['SERVER_NAME'],$mess,retFileAbsolute($idpdf),"",ln("Ordine N. ",$zy).$idordine.ln(" del ",$zy).dataIta().".pdf");
          }elseif($zy==0){
            $mess_ut=str_replace("#INFO-DATI-PAGAMENTO#", replaceEcomerceMarkers(ln($pagamento['descrizione_per_email_editor'],$zy))."<br>", $mess_ut);
            $idpdf=genPDFbyHTML(utf8_decode($mess_ut),1,ln("Ordine N. ",$zy).$idordine.ln(" del ",$zy).dataIta().".pdf");
            $sql="INSERT INTO ".$config_table_prefix."documents (idoggetti,idusers,anno,ishidden,inserimento_data) VALUES ('$idpdf','".$user['id']."',YEAR(CURDATE()),0,NOW())";
            mysql_query($sql);
            $iddocs=mysql_insert_id();
            if($iddocs>0){
              $sql="UPDATE ".$config_table_prefix."ecommerce_ordini SET documents_hidden='".$iddocs."' WHERE id='".$idordine."'";
              mysql_query($sql);
            }
            
            $idtags=getTable("ecommerce_carrello","","attivo='1'");
            $sql="INSERT INTO ".$config_table_prefix."documents_tags_nm (iddocuments,idtags) VALUES ('$iddocs','".$idtags[0]['id_documents_tags']."')";
            mysql_query($sql);
            
            $sql="INSERT INTO ".$config_table_prefix."ecommerce_ordini_email (id_ecommerce_ordini,mittente,destinatario,oggetto,testo_editor,allegato_file,nome_allegato) VALUES (";
            $sql.="'".$idordine."',";
            $sql.="'".$email."',";
            $sql.="'".$user['email']."',";
            $sql.="'".addslashes(ln("Ordine effettuato su ",$zy).$_SERVER['SERVER_NAME'])."',";
            $sql.="'".addslashes($mess_ut)."',";
            $sql.="'".$idpdf."',";
            $sql.="'".addslashes(ln("Ordine",$zy)." N. ".$idordine.ln(" del ",$zy).dataIta().".pdf")."')";
            mysql_query($sql);
            $idemailutente=mysql_insert_id();
          }
        }
        
        if(strpos(strtolower($pagamento['nome']), "paypal")!==FALSE ){
          echo "PayPalEXP;".$idordine.";".ParseToFloat($tot_f);
        }else{
          $connector=retFile($pagamento['connector_file']);
          if($connector) {
              $objHtml->adminPageRedirect($connector."?idordine=$idordine","");  
          } else {
            $_SESSION["ecomm_trans"]=$idordine;
            $mess_conferma=getTable("ecommerce_testi","","(nome='messaggio conferma ordine' AND attivo='1')");
            $mess_conferma=$mess_conferma[0]['testo_editor'];
            $this->emptyCart();
            
            $email=getTable("ecommerce_ordini_email","","id_ecommerce_ordini='$idordine'");
            $email=$email[0];
            $objMailing->mmail($email['destinatario'],$email['mittente'],$email['oggetto'],$email['testo_editor'],retFileAbsolute($email['allegato_file']),"",$email['nome_allegato']);
            
            $objHtml->adminPageRedirect($objUtility->getPathRoot().getCurLanClass()."/grazie.html",ln($mess_conferma));    
          }
        }
        return true;
      }
  }

  function valPayPalCode($code,$esito="") {
    global $config_table_prefix;
    
    $rs=getTable("ecommerce_paypal","","code='$code'");
    if(count($rs)>0) {
      return true;
    }else{
      $idordini=$_POST['option_selection1'];
      if($_GET['paypal_return']>0) $idordini=$_GET['paypal_return'];
      
      $sql="INSERT INTO ".$config_table_prefix."ecommerce_paypal (id_ordini,code,pagamenti_riusciti) VALUES ('".$idordini."','".$code."','".$esito."')";
      mysql_query($sql);
      return false;  
    }
  }
  
  function valKeyClientCode($code) {
    global $config_table_prefix;
    
    $id_ordini=str_replace("00-", "", $_REQUEST['codTrans']);
    if($id_ordini=="") return;
    
    if($_REQUEST['esito']=="OK"){ 
      $esito="1";
    }elseif($_REQUEST['esito']=="KO"){
      $esito="0";
    }
    
    $rs=getTable("ecommerce_keyclient","","code='$code'");
    if(count($rs)==0 || $code=="") {
      $sql="INSERT INTO ".$config_table_prefix."ecommerce_keyclient (id_ordini,code,pagamenti_riusciti) VALUES ('$id_ordini','$code','".$esito."')";
      mysql_query($sql);
      return true;  
    }
  }
  
  function valPayPalIPN() {
    global $config_table_prefix;
    $objUtility = new Utility;
    
    // CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
    // Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
    // Set this to 0 once you go live or don't require logging.
    define("DEBUG", 0);
    // Set to 0 once you're ready to go live
    define("USE_SANDBOX", 0);
    define("LOG_FILE", "ipn.log");
    // Read POST data
    // reading posted data directly from $_POST causes serialization
    // issues with array data in POST. Reading raw POST data from input stream instead.
    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    foreach ($raw_post_array as $keyval) {
    	$keyval = explode ('=', $keyval);
    	if (count($keyval) == 2)
    		$myPost[$keyval[0]] = urldecode($keyval[1]);
    }
    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';
    if(function_exists('get_magic_quotes_gpc')) {
    	$get_magic_quotes_exists = true;
    }
    foreach ($myPost as $key => $value) {
    	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
    		$value = urlencode(stripslashes($value));
    	} else {
    		$value = urlencode($value);
    	}
    	$req .= "&$key=$value";
    }
    // Post IPN data back to PayPal to validate the IPN data is genuine
    // Without this step anyone can fake IPN data
    if(USE_SANDBOX == true) {
    	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    } else {
    	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
    }
    $ch = curl_init($paypal_url);
    if ($ch == FALSE) {
      return FALSE;
    }
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    if(DEBUG == true) {
    	curl_setopt($ch, CURLOPT_HEADER, 1);
    	curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    }
    // CONFIG: Optional proxy configuration
    //curl_setopt($ch, CURLOPT_PROXY, $proxy);
    //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
    // Set TCP timeout to 30 seconds
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    // CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
    // of the certificate as shown below. Ensure the file is readable by the webserver.
    // This is mandatory for some environments.
    //$cert = $objUtility->getPathBackofficeResources() . "cacert.pem";
    //curl_setopt($ch, CURLOPT_CAINFO, $cert);
    $res = curl_exec($ch);
    if (curl_errno($ch) != 0) // cURL error
    	{
    	if(DEBUG == true) {	
    		error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
    	}
    	curl_close($ch);
      exit;
    } else {
    		// Log the entire HTTP response if debug is switched on.
    		if(DEBUG == true) {
    			error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
    			error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
    		}
    		curl_close($ch);
    }
    // Inspect IPN validation result and act accordingly
    // Split response headers and payload, a better way for strcmp
    $parts = explode("\r\n\r\n", trim($res));
    $res = trim(array_pop($parts));
    if (strcmp ($res, "VERIFIED") == 0) {
    	// check whether the payment_status is Completed
    	// check that txn_id has not been previously processed
    	// check that receiver_email is your PayPal email
    	// check that payment_amount/payment_currency are correct
    	// process payment and mark item as paid.
    	// assign posted variables to local variables
    	//$item_name = $_POST['item_name'];
    	//$item_number = $_POST['item_number'];
    	//$payment_status = $_POST['payment_status'];
    	//$payment_amount = $_POST['mc_gross'];
    	//$payment_currency = $_POST['mc_currency'];
    	//$txn_id = $_POST['txn_id'];
    	//$receiver_email = $_POST['receiver_email'];
    	//$payer_email = $_POST['payer_email'];
    	
      $sql="UPDATE ".$config_table_prefix."ecommerce_paypal SET pagamenti_riusciti='1' WHERE code='".$_POST['txn_id']."'";
      mysql_query($sql);
      
    	if(DEBUG == true) {
    		error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
    	}
      
      return 1;
    } else if (strcmp ($res, "INVALID") == 0) {
    	// log for manual investigation
    	// Add business logic here which deals with invalid IPN messages
      
      $sql="UPDATE ".$config_table_prefix."ecommerce_paypal SET pagamenti_riusciti='-1' WHERE code='".$_POST['txn_id']."'";
      mysql_query($sql);
      
    	if(DEBUG == true) {
    		error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
    	}
      
      return -1;
    }
  }

  function CarrelloAjax() {
    global $config_table_prefix;
    $objUtility = new Utility;
    
    $info_carello=getTable("ecommerce_carrello","","attivo='1'"); 
    ob_start(); 
    ?><script><?php
    
    $carrello=getTable("ecommerce_carrello","","attivo='1'"); 
    if($carrello[0]['suggerimenti_sui_prezzi']==1) { ?>
      var tblOptions={
        <?php 
        $tblOptions["container"]="div.ecomm-tua-offerta";
        $tblOptions["table"]="ecommerce_offerte";
        $tblOptions["insert"]=1;
        $tblOptions["insertId"]='';
        $tblOptions["colFilter"]='titolo,email,telefono,offerta_cry';
        $tblOptions["permDel"]=-1;
              
        echo cryptOptions($tblOptions); 
        ?>
      };
      
      g_table=new rsTable2(tblOptions);
      g_table._insert(function(){
        <?php 
        $art=retArticoloFromCat($_GET['menid']);
        $off_id_users=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_offerte' AND campo_hidden='id_users')"));
        $off_id_cat=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_offerte' AND campo_hidden='id_categorie_str_magazzino')")); 
        $off_id_cod=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_offerte' AND campo_hidden='cod_art')"));
        $off_id_url=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_offerte' AND campo_hidden='url')"));
        $off_id_prz=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."ecommerce_offerte' AND campo_hidden='prezzo_cry')"));
        ?>
        $("form.rsTable2-insert-form-ecommerce_offerte").append("<input type='hidden' value='<?php echo $_SESSION['userris_id']; ?>' name='<?php echo $off_id_users[0]['id']; ?>' />"); 
        $("form.rsTable2-insert-form-ecommerce_offerte").append("<input type='hidden' value='<?php echo $_GET['menid']; ?>' name='<?php echo $off_id_cat[0]['id']; ?>' />");
        $("form.rsTable2-insert-form-ecommerce_offerte").append("<input type='hidden' value='<?php echo $art['Codice']; ?>' name='<?php echo $off_id_cod[0]['id']; ?>' />"); 
        $("form.rsTable2-insert-form-ecommerce_offerte").append("<input type='hidden' value='<?php echo $art['Prezzo_cry']; ?>' name='<?php echo $off_id_prz[0]['id']; ?>' />");
        $("form.rsTable2-insert-form-ecommerce_offerte").append("<input type='hidden' value='<?php echo curPageURL(); ?>' name='<?php echo $off_id_url[0]['id']; ?>' />");
      });  
    <? } ?>

    if(typeof(rsCAjax)=="undefined") eval("var rsCAjax='';");
    
    function ecomm_initCartAjax() {
      if(rsCAjax=="") {
        rsCAjax=1;
        var tmpCart=new CarrelloAjax('<?php echo $this->getCurrent(); ?>','<?php echo $this->getCombi(); ?>',"<?php echo ln('Prodotto aggiunto al carrello'); ?>.<br><a href='carrello.html'><?php echo ln('Vai al carrello'); ?></a> <?php echo ln('oppure'); ?> <a class='crt-result-close-a' href='#'><?php echo ln('Continua lo shopping'); ?></a>","<?php echo ln('Modifiche salvate correttamente.'); ?><br><a href='<?php echo getCurLanClass(); ?>/carrello.html'><?php echo ln('Vai al carrello'); ?></a> <?php echo ln('oppure'); ?> <a class='crt-result-close-a' href='#'><?php echo ln('continua lo shopping'); ?></a>","<?php echo $info_carello[0]['prodotti_consigliati']; ?>");
        tmpCart.initAjax();
      }  
    }
    
    $(document).ready(function(){
      ecomm_initCartAjax();  
    }); 
    </script>
    <?php
    $this->g_jsCode.=ob_get_contents();  
    ob_end_clean(); 
  }
  
  function stampaCarrello() {
    $objUtility = new Utility;
    
    if($_GET['ecomm_riepilogo']==1) {
      $this->riepilogoCarrello();
      return;
    }
    
    $articolo=$this->g_current;
    $combi=$this->g_combi;
    
    $this->resetBuffer();
    
    $articolo_generali=retRow("categorie",$articolo);
    if($articolo_generali['attivo']==0) return;
    
    $articolo_testi=getTesti($articolo);
    $ecommerce_generali=getTable("magazzino_articoli","","(id_categorie_str_magazzino='$articolo' AND del_hidden='0')");
    if($ecommerce_generali[0]['Descr1']!="") $articolo_testi[0]['testo_editor']= tinyBug($ecommerce_generali[0]['Descr1']); 
    
    $ramo=getRamo($articolo);
    $ecommerce_caratteristiche=array();
    $mag_rs=array();
    while (list($key, $nodo) = each($ramo)) {
      $tmpmag_rs=Table2ByTable1("categorie","magazzino_articoli_collegati",$nodo['id'],"attivo='1'","Ordinamento ASC");
      $mag_rs=array_merge($tmpmag_rs, $mag_rs);
      
      $tmp_arr=getTable("ecommerce_caratteristiche","Ordinamento ASC","(idcategorie_hidden='".$nodo['id']."' AND attivo='1')");
      while (list($key2, $tmpcar) = each($tmp_arr)) {
        $car_list=retRow("ewiz_caratteristiche_list",$tmpcar['id_ewiz_caratteristiche_list']);
        $car_list['id']=$tmpcar['id'];
        array_push($ecommerce_caratteristiche, $car_list);
      }
    } 
    
    if(count($ecommerce_generali)==0) return false;
  
    $combi=$this->getCombi();
    if($combi!==FALSE) $combi_val=$_SESSION['ecomm'][$articolo][$combi];
    
    $info_carello=getTable("ecommerce_carrello","","attivo='1'");
    
    ?>
    <div class="rsLoading">Loading...</div>
    <!-- Plain box -->
    <div class="ez-wr ecomm_carrello ecomm-carrello container-fluid">
    	<div class="separated">
      <!-- Module 3A -->
      <div class="ez-wr row">
        <?php 
        $notZoom=true;
        $gallery=Table2ByTable1("categorie","fotogallery",$this->getCurrent(),"attivo='1'","Ordinamento ASC");
        //$imm_articolo=retFile($articolo_generali['immagine_file'],$this->g_zoomImgW); 
        //$imm_articolo2=retFile($articolo_generali['immagine_file'],$this->g_smallImgW);
        
        $immbool=$imm_articolo;
        if(!$imm_articolo) {
          $imm_art_or=retFileAbsolute($gallery[0]['immagine_file']);
          if($imm_art_or){
            list($fw, $fh) = getimagesize($imm_art_or);
            if($fw<600) $notZoom=true;
          } 
          
          $imm_articolo=retFile($gallery[0]['immagine_file'],$this->g_zoomImgW);
          $imm_articolo2=retFile($gallery[0]['immagine_file'],$this->g_smallImgW);
        }
        
        if(!$imm_articolo) {
          //$imm_articolo=$objUtility->getPathBackofficeResources()."nofoto.jpg";
          //$imm_articolo2=$objUtility->getPathBackofficeResources()."nofoto.jpg";
          $imm_articolo="";
          $imm_articolo2="";
          $notZoom=true;
        }
        
        if(count($gallery)>0 || $imm_articolo2!=""){ ?>
          <div class="ez-fl ez-negmx ez-33 crt-col-one col-sm-4">
            <div class="ez-wr crt-foto-container">
              <?php if($imm_articolo2!="") { ?>
                <!-- Layout 1 -->
                <div class="ez-wr">
                  <?php if(!$notZoom) { ?>
                    <div class="ez-box crt-foto-articolo-zoom-container">
                      <a href="<?php echo $imm_articolo; ?>" rel="lytebox" class="crt-foto-articolo-zoom-a"  ><div class="ez-box crt-foto-articolo-zoom" title="<?php echo ln("visualizza per esteso"); ?>"></div></a>
                    </div>
                  <? } ?>
                  <div class="ez-box crt-foto-articolo">
                    <?php if(!$notZoom) { ?><a href="<?php echo $imm_articolo; ?>" target="_blank" class="crt-foto-articolo-thm <?php if(!$notZoom) { ?>cloud-zoom<? } ?>" title="" id="crt-foto-articolo-zoom" rel="adjustX: 8, adjustY:-4"><? } ?>
                      <img class="crt-foto-articolo-img img-responsive" src="<?php echo $imm_articolo2; ?>" alt='' title="" />
                    <?php if(!$notZoom) { ?></a><? } ?>
                  </div>
                </div>
              <? } ?>
  
              <?php 
              if(count($gallery)>0) { ?>
                <!-- Plain box -->
                <div class="ez-wr crt-fotogallery">
                  <div class="ez-box">
                    <?php 
                    if(count($gallery)>0 && $immbool) { ?>
                      <a href="<?php echo $imm_articolo; ?>"  class="crt-fotogallery-thm <?php if($notZoom==false) { ?>cloud-zoom-gallery <?php } if($notZoom) echo 'crt-not-zoom'; ?>" title="" <?php if(!$notZoom) { ?>rel="useZoom: 'crt-foto-articolo-zoom', smallImage: '<?php echo $imm_articolo2; ?>'"<? } ?>>
                        <img src="<?php echo retFile($articolo_generali['immagine_file'],$this->g_smallGalleryImgW); ?>" zoom="<?=$imm_articolo?>" title="<?php echo $articolo_generali['nome'] ?>" />
                      </a>
                    <? } 
                     
                    if((count($gallery)>1 && !$immbool) || $immbool) {
                      while (list($key, $tmp_img) = each($gallery)) {
                        $f="immagine_file";
                        if(retFile($tmp_img[$f])) { ?>
                          <a href="<?php echo retFile($tmp_img[$f],$this->g_zoomImgW); ?>"  class="crt-fotogallery-thm <?php if($notZoom==false) { ?>cloud-zoom-gallery <?php } if($notZoom) echo 'crt-not-zoom'; ?>" title="" <?php if(!$notZoom) { ?>rel="useZoom: 'crt-foto-articolo-zoom', smallImage: '<?=retFile($tmp_img[$f],$this->g_smallImgW)?>'"<? } ?>>
                            <img src="<?php echo retFile($tmp_img[$f],$this->g_smallGalleryImgW); ?>" zoom="<?=retFile($tmp_img[$f],$this->g_zoomImgW)?>" title="<?=$tmp_img['titolo']?>" />
                          </a><?
                        } 
                      } 
                    }   
                    ?>
                  </div> 
                </div>
              <? } ?>
  						
              <!-- Module 2A -->
              <div class="ez-wr crt-share-container">
                <div class="ez-fl ez-negmr ez-50 crt-share">
                  <div class="ez-box tTip" tipType="click" title="<div class='fumetto'><?=str_replace("\"","'",tinybug(ln($info_carello[0]['info_condividi_editor'])));?></div>"></div>
                </div>
                <div class="ez-last ez-oh">
                  <div class="ez-box">&nbsp;</div>
                </div>
              </div>
            </div>
          </div>
        <? }else{$maxWtxt=true;} ?>
        <?php if($maxWtxt==true) { $col_class = "col-xs-12 col-sm-12"; } else { $col_class = "col-xs-12 col-sm-8"; } ?>
        <div class="ez-fl ez-negmr ez-33 crt-col-two crt-articolo-descrizione <?php echo $col_class; ?>"  >
          <!-- Layout 1 -->
          <div class="ez-wr">
            <div class="ez-box crt-articolo-nome"><?php echo $articolo_generali[0]['nome']; ?></div>
            
            <div class="ez-wr crt-articolo-descrizione">
              <div class="ez-box crt-articolo-cod"><?php echo ln("Cod. Articolo").": <b>".$ecommerce_generali[0]['Codice']."</b>"; ?></div>
              <div class="ez-box crt-articolo-disp"><?php echo ln("Disponibilità").": <b>".intval($ecommerce_generali[0]['quantita'])."</b>"; ?></div>
              <div class="ez-box"><?php echo ln($articolo_testi[0]['testo_editor']);?></div>
            </div>
            </div>
          </div>
        </div>
      </div>
            
            <!-- Plain box -->
            <div class="ez-wr crt-caratteristiche-container  separated">
              <?php
              while (list($key, $tmp_caratteristiche) = each($ecommerce_caratteristiche)) {
                $nome=ln($tmp_caratteristiche['nome']);
                $width=$tmp_caratteristiche['larghezza']."px";
                if($width=="0px" || $width=="px") $width="98%";
                $idnome=onlyreadables($nome);
                $combid="id#".$tmp_caratteristiche['id'];
                $tipo=$tmp_caratteristiche['id_ecommerce_tipologie'];
                $ecommerce_valori=getTable("ecommerce_valori","Ordinamento ASC","(idcaratteristiche_hidden ='".$tmp_caratteristiche['id']."' AND attivo='1')");
                //echo $tmp_caratteristiche['id'];
                ?>
                <!-- Module 2A -->
                <div class="ez-wr crt-caratteristica row crt-label-container-car-<?php echo $tmp_caratteristiche['id']; ?>" caratteristica="<?=$tmp_caratteristiche['id']?>" obblig="<?=$tmp_caratteristiche['obbligatorio']?>" nome="<?=$tmp_caratteristiche['nome']?>" >
                  <div class="ez-fl ez-negmr ez-50 crt-caratteristiche-nome col-sm-4">
                    <div class="ez-box bts-caratteristica-label"><?php echo $nome; ?></div>
                  </div>
                  <div class="ez-last ez-oh col-sm-8 ">
                    <!-- Module 3A -->
                    <div class="ez-wr">
                      <div class="ez-fl  ez-negmx ez-33 crt-caratteristiche-input bts-caratteristiche-input" style="width:80%">
                        <div class="ez-box" >
                          <?php
                          if($tipo=="1") {
                            ?><input style="width:<?php echo $width; ?>;" caratteristica="<?=$tmp_caratteristiche['id']?>" class="text" name="<?=$idnome?>" type="text" id="<?=$tmp_valori['id']?>" value="<?=$combi_val[$combid]?>" /><?  
                          }
                  
                          if($tipo=="2") {
                            ?><input caratteristica="<?=$tmp_caratteristiche['id']?>" class="check" name="<?=$idnome?>" type="checkbox" id="<?=$tmp_valori['id']?>" value="true" class="checkbox" <?php if($combi_val[$combid]=="true") echo "checked"; ?> /><?  
                          }
                          
                          if($tipo=="3" && count($ecommerce_valori)>0) { ?>
                            <select style="width:<?php echo $width; ?>;" caratteristica="<?=$tmp_caratteristiche['id']?>" name="<?=$idnome?>" class="select form-control">
                              <option value="" <?php if($combi_val[$combid]=="") echo "selected"; ?>>
                              <?php
                              while (list($key2, $tmp_valori) = each($ecommerce_valori)) {
                                ?><option value="<?=$tmp_valori['id']?>" <?php if($combi_val[$combid]==$tmp_valori['id']) echo "selected"; ?>><?=ln($tmp_valori['nome'])?>
                              <? } ?>
                            </select><?  
                          }
                          
                          if($tipo=="4" && count($ecommerce_valori)>0) { 
                            $combi_val[$combid]=explode(";", $combi_val[$combid]);
                            ?>
                            <select style="width:<?php echo $width; ?>;" caratteristica="<?=$tmp_caratteristiche['id']?>" name="<?=$idnome?>" class="select form-control" multiple>
                              <option value="" <?php if(count($combi_val[$combid])==0) {echo "selected";} ?>>
                              <?php
                              while (list($key2, $tmp_valori) = each($ecommerce_valori)) {
                                ?><option value="<?=$tmp_valori['id']?>" <?php if(in_array($tmp_valori['id'],$combi_val[$combid])) echo "selected"; ?>><?=ln($tmp_valori['nome'])?><?
                              }
                              ?>
                            </select><?
                          }
                          
                          if($tipo=="5" && count($ecommerce_valori)>0) {
                            $s=$ecommerce_valori['0']['nome'];
                            $tot=count($ecommerce_valori)-1;
                            $e=$ecommerce_valori[$tot]['nome'];
                            
                            $buffer=array();
                            for($i=$s;$i<$e+1;$i++) {
                              array_push($buffer, $i);  
                            }
                            
                            ?>
                            <select style="width:<?php echo $width; ?>;" caratteristica="<?=$tmp_caratteristiche['id']?>" auto="1" name="<?=$idnome?>" class="select form-control">
                              <option value="" <?php if($combi_val[$combid]=="") echo "selected"; ?>>
                              <?php
                              while (list($key2, $tmp_valori2) = each($buffer)) {
                                ?><option value="<?=$tmp_valori2?>" <?php if($combi_val[$combid]==$tmp_valori2) echo "selected"; ?>><?=$tmp_valori2?><?
                              }
                              ?>
                            </select>
                          <? }
                          
                          if($tipo=="6" && count($ecommerce_valori)>0) { 
                            $combi_val[$combid]=explode(";", $combi_val[$combid]);
                            ?>
                            <table caratteristica="<?=$tmp_caratteristiche['id']?>" auto="1" name="<?=$idnome?>">
                              <tr>
                                <td>
                                  <div class="ez-wr crt-color-container" caratteristica="<?=$tmp_caratteristiche['id']?>">
                                    <div class="ez-box"><img src="" class="crt-color-zoom" /></div>
                                    <div class="ez-box crt-color-nome"></div>
                                    <div class="ez-box crt-color-descr"></div>
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <?php
                                while (list($key2, $tmp_valori) = each($ecommerce_valori)) { 
                                  if($key2%6==0) echo "<tr></tr>"; ?>
                                  <td>
                                    <?php
                                    $f="immagine_file";
                                    $f2="immagine2_file";
                                    if(retFile($tmp_valori[$f])) { ?>
                                      <!-- Layout 1 -->
                                      <?php if(retFile($tmp_valori[$f2])) { ?>
                                        <a href="<?=retFile($tmp_valori[$f2],$this->g_zoomImgW)?>" class="crt-caratteristiche-color-a crt-fotogallery-thm <?php if($notZoom==false) { ?>cloud-zoom-gallery<? } ?>" title="" <?php if($notZoom==false) { ?>rel="useZoom: 'crt-foto-articolo-zoom', smallImage: '<?=retFile($tmp_valori[$f2],$this->g_smallImgW)?>'"<? } ?> >
                                      <? } ?>
                                        <img src="<?php echo retFile($tmp_valori[$f],$this->g_smallImgW); ?>" <?php if(retFile($tmp_valori[$f2],$this->g_zoomImgW)) { ?>zoom="<?php echo retFile($tmp_valori[$f2],$this->g_zoomImgW); ?>" <? } ?> crtTitle="<?php echo htmlentities($tmp_valori['nome']); ?>" value="<?=$tmp_valori['id'];?>" class="crt-caratteristiche-color <?php if(in_array($tmp_valori['id'],$combi_val[$combid])) echo "crt-caratteristiche-color-sel"; ?>" crtSmall="<?=retFile($tmp_valori[$f],216,130)?>" crtDescr="<?php echo htmlentities($tmp_valori['fumetto_editor']); ?>" caratteristica="<?=$tmp_caratteristiche['id']?>" name="<?=$idnome?>" />
                                      <?php if(retFile($tmp_valori[$f2])) { ?>
                                        </a>
                                      <? } ?>
                                      <div class="crt-caratteristiche-color-arrow"></div>
                                    <? } ?>                    
                                  </td>
                                <? } ?>
                              </tr>
                            </table>
                          <? }
                          
                          if($tipo=="7" && count($ecommerce_valori)>0) { 
                            $combi_val[$combid]=explode(";", $combi_val[$combid]); ?>
                            
                            <div class="row row-eq-height crtp-caratteristiche-aff select-aff" inputcaratt=1 caratteristica="<?=$tmp_caratteristiche['id']?>" name="<?=$idnome?>">
                              <?php
                              while (list($key2, $tmp_valori) = each($ecommerce_valori)) { 
                                $f="immagine_file";
                                $f2="immagine2_file";
                                if($tmp_valori['nome']!="") { ?>
                                <div class="col-xs-6 col-md-4">
                                  <?php if(retFile($tmp_valori[$f])) { ?>
                                    <div  src="<?php echo retFile($tmp_valori[$f],$this->g_smallImgW); ?>" <?php if(retFile($tmp_valori[$f],$this->g_zoomImgW)) { ?>zoom="<?php echo retFile($tmp_valori[$f],$this->g_zoomImgW); ?>" <? } ?> value="<?=$tmp_valori['id'];?>" class="thumbnail crt-caratteristiche-aff <?php if(in_array($tmp_valori['id'],$combi_val[$combid])) echo "crt-caratteristiche-aff-sel"; ?>" crtDescr="<?php echo htmlentities($tmp_valori['fumetto_editor']); ?>" caratteristica="<?php echo $tmp_caratteristiche['id']?>" name="<?php echo $idnome?>" crtTitle="<?php echo htmlentities($tmp_valori['nome']); ?>" >
                                      <a href="<?php echo retFile($tmp_valori[$f],$this->g_zoomImgW)?>" class="crt-fotogallery-thm <?php if($notZoom==false) { ?>cloud-zoom-gallery<? } ?>" title="" rel="useZoom: 'crt-foto-articolo-zoom', smallImage: '<?php echo retFile($tmp_valori[$f],$this->g_smallImgW)?>'">
                                        <img src="<?php echo retFile($tmp_valori[$f],$this->g_smallImgW); ?>" alt="<?php echo ln($tmp_valori['nome']); ?>" style="width:100%">
                                        <div class="caption">
                                          <p style="text-align:center;"><?php echo ln($tmp_valori['nome']); ?></p>
                                        </div>
                                      </a>
                                    </div>
                                  <? } ?>
                                </div>
                                <? } ?>  
                              <? } ?>
                            </div> 
                          <? } 
                          
                          if($tipo=="8") { 
                            if(!($combi_val[$combid]>0)) $combi_val[$combid]=1; ?>
                            <input caratteristica="<?=$tmp_caratteristiche['id']?>" class="text crt-upd-q form-control" name="<?=$idnome?>" type="text" id="<?=$tmp_valori['id']?>" value="<?=$combi_val[$combid]?>" />    
                          <? }
                          
                          if($tipo=="11") { 
                            $dimV=explode("x", $combi_val[$combid]);
                            $maxS=explode("x",$ecommerce_valori['0']['nome']);
                            ?>
                            <!-- Module 2A -->
                            <div class="ez-wr form-inline">
                              <div class="ez-fl ez-negmr ez-50 form-group">
                                <label for="<?=$idnome?>_1"><?php echo ln("Base"); ?></label><br><input class="text crt-upd-dim1 form-control" name="<?=$idnome?>_1" type="text" id="" value="<?=$dimV[0]?>" max="<?=$maxS[0]?>" />
                              </div>
                              <div class="ez-last ez-oh  form-group">
                                <label for="<?=$idnome?>_2"><?php echo ln("Altezza"); ?></label><br><input class="text crt-upd-dim2 form-control" name="<?=$idnome?>_2" type="text" id="" value="<?=$dimV[1]?>" max="<?=$maxS[1]?>" />
                              </div>
                            </div>
                            <input style="width:<?php echo $width; ?>; display:none;" caratteristica="<?=$tmp_caratteristiche['id']?>" class="text crt-upd-dim" name="<?=$idnome?>" type="text" id="<?=$tmp_valori['id']?>" value="<?=$combi_val[$combid]?>" />    
                          <? }
                          
                          if($tipo=="10" && count($ecommerce_valori)>0) {
                            $s=$ecommerce_valori['0']['nome'];
                            $tot=count($ecommerce_valori)-1;
                            $e=$ecommerce_valori[$tot]['nome'];
                            
                            $buffer=array();
                            for($i=$s;$i<$e+1;$i++) {
                              array_push($buffer, $i);  
                            }
                            
                            ?>
                            <select style="width:<?php echo $width; ?>;" class="crt-upd-q select form-control" caratteristica="<?=$tmp_caratteristiche['id']?>" auto="1" name="<?=$idnome?>">
                              <option value="" <?php if($combi_val[$combid]=="") echo "selected"; ?>>
                              <?php while (list($key2, $tmp_valori2) = each($buffer)) { ?>
                                <option value="<?=$tmp_valori2?>" <?php if($combi_val[$combid]==$tmp_valori2) echo "selected"; ?>><?=$tmp_valori2?>
                              <? } ?>
                            </select>
                          <? }
                          
                          if($tipo=="9") { ?>
                            <!-- Module 2A -->
                            <div class="ez-wr">
                              <div class="ez-fl ez-negmr ez-50" style="width:95px;">
                                <div class="ez-box">
                                  <input id="crt-upload-cmyk" type="upload" />
                                </div>
                              </div>
                              <div class="ez-last ez-oh">
                                <div class="ez-box">
                                  <img class="crt-upload-cmyk-cancel" src="images/upload_cancel1.png" width=60 height=20 />
                                </div>
                              </div>
                            </div>
                            <input type="hidden" caratteristica="<?=$tmp_caratteristiche['id']?>" class="text crt-upload-cmyk" name="<?=$idnome?>" id="<?=$tmp_valori['id']?>" value="<?=$combi_val[$combid]?>" />    
                            <div class="crt-upload-cmyk-res">
                              <!-- Module 2A -->
                              <div class="ez-wr">
                                <div class="ez-fl ez-negmr ez-50" style="width:60px;">
                                  <img class="crt-upload-cmyk-res-img-val" src="" />
                                </div>
                                <div class="ez-last ez-oh">
                                  <!-- Layout 1 -->
                                  <div class="ez-wr crt-upload-cmyk-res">
                                    <div class="ez-box">
                                      <div class="crt-upload-cmyk-res-color crt-upload-cmyk-res-c"></div>
                                      <div class="crt-upload-cmyk-res-c-val crt-upload-cmyk-res-val"></div>
                                    </div>
                                    <div class="ez-box">
                                      <div class="crt-upload-cmyk-res-color crt-upload-cmyk-res-m"></div>
                                      <div class="crt-upload-cmyk-res-m-val crt-upload-cmyk-res-val"></div>
                                    </div>
                                    <div class="ez-box">
                                      <div class="crt-upload-cmyk-res-color crt-upload-cmyk-res-y"></div>
                                      <div class="crt-upload-cmyk-res-y-val crt-upload-cmyk-res-val"></div>
                                    </div>
                                    <div class="ez-box">
                                      <div class="crt-upload-cmyk-res-color crt-upload-cmyk-res-k"></div>
                                      <div class="crt-upload-cmyk-res-k-val crt-upload-cmyk-res-val"></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <? } ?>
                        </div>
                      </div>
                      <div class="ez-fl ez-negmr ez-33 crt-caratteristiche-info bts-caratteristiche-info">
                        <div class="ez-box"><img class="tTip" src="<?php echo $objUtility->getPathRoot();?>css/images/info.png" title="" /></div>
                      </div>
                      <div class="ez-last ez-oh">
                        <!--  <div class="ez-box crt-caratteristiche-aggiunta bts-caratteristiche-aggiunta" style="<?php echo "visibility:hidden;"; ?>">0,00</div> -->
                      </div>
                    </div>
                  </div>
                </div>
              <? } ?> 
            </div>
          </div>                    
          <?php if($ecommerce_generali[0]['Prezzo_cry']>0 && $ecommerce_generali[0]['richiedi_quotazione']!="1") { ?>
            <!-- Layout 1 -->
            <div class="ez-wr crt-prezzi-container">
              <!-- Module 2A -->
              <div class="ez-wr row">
                <div class="ez-fl ez-negmr ez-50 crt-barcode">
                  <div class="ex-box crt-barcode-cod" title="<?php echo $ecommerce_generali[0]['Codice']; ?>" ><?php echo $ecommerce_generali[0]['Codice']; ?></div>
                </div>
                <div class="ez-last ez-oh">
                  <!-- Module 2A -->
                  <div class="ez-wr">
                    <div class="ez-fl ez-negmr ez-50 crt-addtocart-container col-xs-12 col-sm-4">
                      <?php if($this->getCombi()===false) { ?><div class="ez-box crt-aggiungi-al-carrello  btn btn-success btn-flow"><?php echo ln("AGGIUNGI AL CARRELLO"); ?></div><? } ?>
                      <?php if($this->getCombi()!==false) { ?><div class="ez-box crt-aggiorna-il-carrello btn btn-success btn-flow"><?php echo ln("SALVA MODIFICHE"); ?></div><? } ?>
                    </div>
                    <div class="ez-last ez-oh">
                      <!-- Module 2A -->
                      <div class="ez-wr">
                        <?php
                        $chat_conf=getTable("chat_conf","","attivo='1'");
                        if(count($chat_conf)>0 && $info_carello[0]['chat']=="1"){ ?>
                          <div class="ez-fl ez-negmr ez-50 crt-live-support">
                            <?php printChat(); ?>
                          </div>
                        <? } ?>
                        <div class="ez-last ez-oh col-xs-12 col-sm-8">
                          <!-- Module 2A -->
                          <div class="ez-wr">
                            <!-- 
                            <div class="ez-fl ez-negmr ez-50 crt-prezzo-nosconto-label">
                              <div class="ez-box">&nbsp;</div>  
                            </div>
                             -->
                            <div class="ez-last ez-oh">
                              <div class="ez-box crt-prezzo">&nbsp;</div>
                            </div>
                          </div>                                      
                          
                          <!-- Plain box -->
                          <div class="ez-wr crt-sconto-perc">
                            <div class="ez-box">&nbsp;</div> 
                          </div>
						
                          <!-- Module 2A -->
                          <div class="ez-wr">
                            <div class="ez-fl ez-negmr ez-50 crt-prezzo-label-container">
                              <!-- Module 2A -->
                              <div class="ez-wr">
                              <!--  
                                <div class="ez-fl ez-negmr ez-50 crt-prezzo-label">
                                  <div class="ez-box">&nbsp;</div>
                                </div>
                                 -->
                                <div class="ez-last ez-oh crt-prezzo-info">
                                  <div class="ez-box tTip" tipType="click" title="<div class='fumetto'><?=str_replace("\"","'",tinybug(ln($info_carello[0]['info_prezzo_editor'])));?></div>"></div>
                                </div>
                              </div>
                            </div>
                            <div class="ez-last ez-oh">
                              <div class="ez-box crt-prezzo-scontato">&nbsp;</div>
                              <div class="ez-box crt-loading" style="display:none;"><img src="<?php echo $objUtility->getPathBackofficeResources()."loading.gif"; ?>" />&nbsp;&nbsp;Loading...</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Layout 2 -->
              <div class="ez-wr crt-result alert alert-success ">
                <!-- Module 2B -->
                <div class="ez-wr crt-result-close-container" >
                  <div class="ez-fr ez-negml ez-50 crt-result-close">
                    <div class="ez-box"><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/dialog-close.png" /></div>
                  </div>
                  <div class="ez-last ez-oh">
                    <div class="ez-box">&nbsp;</div>
                  </div>
                </div>
                <!-- Module 2A -->
                <div class="ez-wr crt-result-msg-container">
                  <div class="ez-fl ez-negmr ez-50 crt-result-info">
                    <div class="ez-box"><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/ok.png" /></div>
                  </div>
                  <div class="ez-last ez-oh crt-result-text">
                    <div class="ez-box">&nbsp;</div>
                  </div>
                </div>
                <div class="ez-box">&nbsp;</div>
              </div>
              
              <?php if($ecommerce_generali[0]['box_prezzo_adeguato']==1) { ?>
              <!-- Plain box -->
              <div class="ez-wr ecomm-tua-offerta"></div>
              <? } ?>
              
              <?php if($info_carello[0]['prodotti_consigliati']==2) {
                $mag_rs_ret=array();
                if(count($mag_rs)>0 || $info_carello[0]['consiglia_stesso_livello']==1) { ?>
                  <!-- Plain box -->
                  <div class="ez-wr crt-articoli-successivi">
                    <!-- Plain box -->
                    <div class="ez-wr crt-articoli-successivi-label">
                      <div class="ez-box"><?php echo ln("Ti consigliamo anche..."); ?></div> 
                    </div>
                    <!-- Module 3A -->
                    <div class="ez-wr">
                      <div class="ez-fl  ez-negmx ez-33 arrow-left">
                        <div class="ez-box"><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/arrow-left.gif" /></div>
                      </div>
                      <div class="ez-fl ez-negmr ez-33 dragmeParent">
                        <div class="ez-box dragme">
                          <?php 
                          $struttura=getStrutturaByNodo($this->getCurrent());
                          while (list($mkey, $mrow) = each($mag_rs)) {
                            $tmag=getStrutturaFull($struttura['nome'],$mrow['id_categorie_str_magazzino']);
                            if(count($tmag)>0) {
                              while (list($mkey2, $mrow2) = each($tmag)) {
                                $tfigli=getStrutturaFull($struttura['nome'],$mrow2['id']);
                                if(count($tfigli)==0 && $mrow2['attivo']==1) array_push($mag_rs_ret, $mrow2);  
                              }
                            }else{
                              $tmag=retRow("categorie",$mrow['id_categorie_str_magazzino']);
                              if($tmag['attivo']==1) array_push($mag_rs_ret, $tmag); 
                            }
                          }

                          if($info_carello[0]['consiglia_stesso_livello']==1){
                            $mag_rs_ret2=array();
                            $padre=getPadre($this->getCurrent());
                            $tlev=getStrutturaFull($struttura['nome'],$padre['id']);
                            while (list($mkey, $mrow) = each($tlev)) {
                              $tfigli=getStrutturaFull($struttura['nome'],$mrow['id']);
                              if(count($tfigli)==0 && $mrow['attivo']==1 && $mrow['id']!=$this->getCurrent()) array_push($mag_rs_ret2, $mrow);  
                            }
                            
                            $mag_rs_ret=array_merge_unique($mag_rs_ret,$mag_rs_ret2);
                          }


                          if(count($mag_rs_ret)==0) $mag_rs_ret="";
                          printGalleryFromStruttura($struttura['nome'],$padre['id'],"1","0","",$this->g_smallImgW,0,0,$mag_rs_ret); 
                          ?>
                        </div> 
                      </div>  
                      <div class="ez-last ez-oh arrow-right">
                        <div class="ez-box"><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/arrow-right.gif" /></div>
                      </div>
                    </div>
                  </div>
                <? } ?>
              <? } ?>
            </div>
          <? } ?>
          
          <?php if($ecommerce_generali[0]['Prezzo_cry']>0) { ?>
            <!-- Layout 1 -->
            <div class="ez-wr crt-richiedi-quotazione" c="" <?php if($ecommerce_generali[0]['richiedi_quotazione']=="1") { ?>style="display:block"<? } ?>>
              <div class="ez-box">
                <!-- Module 2A -->
                <div class="ez-wr crt-quotazione-warning">
                  <div class="ez-fl ez-negmr ez-50 crt-quotazione-warning-ico">
                    <div class="ez-box"><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/warning.png" /></div>
                  </div>
                  <div class="ez-last ez-oh crt-quotazione-warning-txt">
                    <div class="ez-box">
                      <?php echo ln("Per la personalizzazione desiderata occorre richiedere una quotazione."); ?>
                      <?php lyteFrame("rsAction.php?ecommQuotazione=1&menid=".$articolo_generali['id'],'<b>'.ln("Richiedi quotazione").'</b>'); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <? } ?>
          
        
        <div class="ez-last ez-oh">
          <?php if($info_carello[0]['prodotti_consigliati']==1) { 
            $mag_rs_ret=array();
            if(count($mag_rs)>0 || $info_carello[0]['consiglia_stesso_livello']==1) { ?>
              <!-- Plain box -->
              <div class="ez-wr crt-articoli-successivi">
                <!-- Plain box -->
                <div class="ez-wr crt-articoli-successivi-label">
                  <div class="ez-box"><?php echo ln("Ti consigliamo anche..."); ?></div> 
                </div>
                <!-- Layout 1 -->
                <div class="ez-wr">
                  <div class="ez-box arrow-left"><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/arrow-up.gif" /></div>
                  <!-- Plain box -->
                  <div class="ez-wr dragmeParent">
                    <div class="ez-box dragme">
                      <?php 
                      $struttura=getStrutturaByNodo($this->getCurrent());
                      while (list($mkey, $mrow) = each($mag_rs)) {
                        $tmag=getStrutturaFull($struttura['nome'],$mrow['id_categorie_str_magazzino']);
                        if(count($tmag)>0) {
                          while (list($mkey2, $mrow2) = each($tmag)) {
                            $tfigli=getStrutturaFull($struttura['nome'],$mrow2['id']);
                            if(count($tfigli)==0 && $mrow2['attivo']==1) array_push($mag_rs_ret, $mrow2);  
                          }
                        }else{
                          $tmag=retRow("categorie",$mrow['id_categorie_str_magazzino']);
                          if($tmag['attivo']==1) array_push($mag_rs_ret, $tmag); 
                        }
                      }
  
                      if($info_carello[0]['consiglia_stesso_livello']==1){
                        $mag_rs_ret2=array();
                        $padre=getPadre($this->getCurrent());
                        $tlev=getStrutturaFull($struttura['nome'],$padre['id']);
                        while (list($mkey, $mrow) = each($tlev)) {
                          $tfigli=getStrutturaFull($struttura['nome'],$mrow['id']);
                          if(count($tfigli)==0 && $mrow['attivo']==1 && $mrow['id']!=$this->getCurrent()) array_push($mag_rs_ret2, $mrow);  
                        }
                        
                        $mag_rs_ret=array_merge_unique($mag_rs_ret,$mag_rs_ret2);
                      }
  
                      if(count($mag_rs_ret)==0) $mag_rs_ret="";
                      printGalleryFromStruttura($struttura['nome'],$padre['id'],"0","1","",$this->g_smallImgW,0,0,$mag_rs_ret); 
                      ?>
                    </div> 
                  </div>
                  <div class="ez-box arrow-right"><img src="<?php echo $objUtility->getPathRoot(); ?>css/images/arrow-down.gif" /></div>
                </div>
              </div>
            <? } ?>
          <? } ?>
        </div>			 
    </div>
    <?    
  }
}
?>