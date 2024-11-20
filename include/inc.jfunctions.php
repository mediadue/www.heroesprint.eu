<?php
/**
 * Funzioni personalizzate
 * 
 * AGGIUNGERE QUI LE FUNZIONI PERSONALIZZATE DEL SITO
 * 
 * Nicola Carmentano
 * 
**/

?> 
<script>
function alert_error(){
  $.notify({
  	// options
  	message: '<?php echo addslashes(html_entity_decode(ln('Si è verificato un errore durante l\'operazione richiesta, ripetere l\'operazione'),ENT_QUOTES, 'UTF-8')); ?>',
    icon: 'glyphicon glyphicon-warning-sign', 
  },{
  	// settings
  	type: 'danger',
    allow_dismiss: true,
  	newest_on_top: false,
  	showProgressbar: false,
    delay: 5000,
  	placement: {
  		from: "bottom",
  		align: "center"
  	}
  });
}

function heroes_sendData(type, id, fun) {
	$.ajax({
	  type: "POST",                       
	  url: "<?php echo $objUtility->getPathRoot(); ?>rsActionBoot.php",
	  data: "rsUPDHeroes=1&type="+type+"&id="+id, 
	  success: fun,
	  error: function(XMLHttpRequest, textStatus, errorThrown) {
		  				alert_error(); 
	         }
	});  
}

// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.

$(window).scroll(function (event) {
		
    var scroll = $(window).scrollTop();
    if (scroll > 450) {
        $('.back-to-top').fadeIn(300);
    } else {
        $('.back-to-top').fadeOut(300);
    }

});

$(window).load(function() {
    $('.flexslider').flexslider({
  		animation: "slide",
  		direction: "vertical",
  		controlNav: false,
  		directionNav: false
	});
  $("div.loading-overlay").hide();
  $("span.master-container").show();
  
  $(".tshirt-editor-container").height($(".tshirt-bg").outerHeight(true));
});

$(window).resize(function() {
  $(".tshirt-editor-container").height($(".tshirt-bg").outerHeight(true));
});

$(document).ready(function(){

  var menid="<?php echo $menid ?>";
  var tabSel="";
  if(menid=="2150") tabSel=0;
  if(menid=="2151") tabSel=1;
  
  $( function() {
    $("#tabs").tabs({selected:tabSel});
  } );

    $(".btn-newsletter").click(function(){
		var email=$("input.txt-newsletter").val();
		heroes_sendData("hrs_newsletter",email,function(msg){
			$.notify({
      	// options
      	message: msg,
        icon: 'glyphicon glyphicon-info-sign', 
      },{
      	// settings
      	type: 'info',
        allow_dismiss: true,
      	newest_on_top: false,
      	showProgressbar: false,
        delay: 5000,
      	placement: {
      		from: "bottom",
      		align: "center"
      	}
      });
		});	
	});

  function salvaNote(){
    var objNote=new Array();
    var i=0; 
     
    $("#invio-file-note, .invio-note-tutte").LoadingOverlay("show", {
        image       : "",
        text        : "<?php echo addslashes(html_entity_decode(ln('Sto salvando quello che hai scritto'),ENT_QUOTES, 'UTF-8')); ?>",
        textClass   : "upload_elab_file_wait"
    });
    
    $(".invio-note-tutte").each(function(index) {
      i++;
      objNote[i]=new Array();
      
      objNote[i]['id']=$(this).attr("id");
      objNote[i]['text']=addslashes($(this).val());
    });
    
    objNote['note']=addslashes($("#invio-file-note").val());
    
    var serData=urlencode(serialize(objNote));
                 
    $.ajax({
       type: "POST",
       url: getPathRoot+"verifica_file.php",
       data: "hrsNoteSave=1&note="+serData+"&ordine="+$("input.invio-file-ordine").val(),
       success: function(msg){
									$("#invio-file-note, .invio-note-tutte").LoadingOverlay("hide");
                  
                  $.notify({
                  	// options
                  	message: msg,
                    icon: 'glyphicon glyphicon-ok-sign', 
                  },{
                  	// settings
                  	type: 'success',
                    allow_dismiss: true,
                  	newest_on_top: false,
                  	showProgressbar: false,
                    delay: 5000,
                  	placement: {
                  		from: "bottom",
                  		align: "center"
                  	}
                  });
									$("input.btn-invio-file-submit").trigger("click");
  							},
       error: function(XMLHttpRequest, textStatus, errorThrown) {
    	   				 $("#invio-file-note").LoadingOverlay("hide");
                 alert_error();
              }
     });  
  }

	$("input.btn-invio-file-note-save").click(function(){
    salvaNote();   	
	});

  $(".invio-note-tutte").live("blur",function(){
    salvaNote();      
  });

	$("input.btn-invio-file-submit").click(function(){
	    $("div.rsLoading").show();   
	    $.ajax({
	       type: "POST",
	       url: getPathRoot+"verifica_file.php",
	       data: "email="+$("input.invio-file-email").val()+"&ordine="+$("input.invio-file-ordine").val(),
	       success: invio_file,
	       error: function(XMLHttpRequest, textStatus, errorThrown) {
	    	   alert_error(); 
	       }
	     });
	  });
	   
    <?php if(isset($_GET['idord']) && isset($_GET['email'])) echo '$("input.btn-invio-file-submit").trigger("click");'; ?>
       
	  $("input.invio-file-email,input.invio-file-ordine").live("keypress",function(event){
	    if(event.keyCode == '13') $("input.btn-invio-file-submit").trigger("click");
	  });
	  
    $("button.ecomm_file_approved").live("click",function(){
      var myObj=$(this); 
      var id=myObj.attr("id");
      
      bootbox.confirm({
          title: 'HeroesPrint.eu',
          message: '<?php echo addslashes(html_entity_decode(ln('Sei sicuro di voler approvare questo file?'),ENT_QUOTES, 'UTF-8')); ?>',
          buttons: {
              cancel: {
                  label: '<i class="fa fa-times"></i> <?php echo addslashes(html_entity_decode(ln('Annulla'),ENT_QUOTES, 'UTF-8')); ?>'
              },
              confirm: {
                  label: '<i class="fa fa-check"></i> <?php echo addslashes(html_entity_decode(ln('Conferma'),ENT_QUOTES, 'UTF-8')); ?>'
              }
          },
          callback: function (result) {
              if(result==true){
                myObj.parents(".alert").replaceWith("<div class='alert alert-info'><span class='glyphicon glyphicon-cog'></span> <strong><?php echo addslashes(html_entity_decode(ln('Preparazione dei Files'),ENT_QUOTES, 'UTF-8')); ?></strong><hr class='message-inner-separator'><p><?php echo addslashes(html_entity_decode(ln('Sto convertendo i tuoi files nel giusto formato di stampa, questa operazione puo\' richiedere diversi minuti. Puoi chiudere il browser o spegnere il computer e continuare a lavorare. Per controllare lo stato di avanzamento puoi aggiornare questa pagina di tanto in tanto premendo il tatso F5'),ENT_QUOTES, 'UTF-8')); ?>.</p></div>");
                
          	    $.ajax({
          	       type: "POST",
          	       url: getPathRoot+"verifica_file.php",
          	       data: "ecomm_approved="+id,
                   async: true,
          	       success: function(msg){
          	                  //myObj.LoadingOverlay("hide");
                              //$("input.btn-invio-file-submit").trigger("click");    
          	                },
          	       error: function(XMLHttpRequest, textStatus, errorThrown) {
          	                //myObj.LoadingOverlay("hide");
                            //alert_error(); 
          	              }
        	     });
             }
          }
       }); 
    });
    
    $("#pri_sel").live("change",function(){
      var myObj=$(this);
      myObj.LoadingOverlay("show");
      var id=myObj.attr("idfile");
      
      if(!(id>0)) return;
      
      $.ajax({
  	       type: "POST",
  	       url: getPathRoot+"verifica_file.php",
  	       data: "priority="+myObj.val()+"&idfile="+id,
  	       success: function(msg){
  	                  myObj.LoadingOverlay("hide");
                      if(msg=='1'){
                        salvaNote();
                      }else{
                        myObj.LoadingOverlay("hide");
                        alert_error();  
                      }  
  	                },
  	       error: function(XMLHttpRequest, textStatus, errorThrown) {
  	                myObj.LoadingOverlay("hide");
                    alert_error(); 
  	              }
	     });
    });
    
    $(".ecomm-file-icc").live("change",function(){
      var myObj=$(this);
      var parent=myObj.parents(".alert");
      parent.LoadingOverlay("show");
      $(".invio-file-table-del-row").LoadingOverlay("show");
      var id=myObj.attr("idfile");

      if(!(id>0)) return;
      
      $.ajax({
  	       type: "POST",
  	       url: getPathRoot+"verifica_file.php",
  	       data: "setIcc="+myObj.val()+"&idfile="+id,
  	       success: function(msg){
  	                  parent.LoadingOverlay("hide");
                      $(".invio-file-table-del-row").LoadingOverlay("hide");
                      if(msg=='1'){
                        $.notify({
                        	// options
                        	message: '<?php echo addslashes(html_entity_decode(ln('Correzione colore eseguita'),ENT_QUOTES, 'UTF-8')); ?>',
                          icon: 'glyphicon glyphicon-ok-sign', 
                        },{
                        	// settings
                        	type: 'success',
                          allow_dismiss: true,
                        	newest_on_top: false,
                        	showProgressbar: false,
                          delay: 5000,
                        	placement: {
                        		from: "bottom",
                        		align: "center"
                        	}
                        });
                        
                        $("input.btn-invio-file-submit").trigger("click");    
                      }else{
                        parent.LoadingOverlay("hide");
                        $(".invio-file-table-del-row").LoadingOverlay("hide");
                        alert_error();  
                      }  
  	                },
  	       error: function(XMLHttpRequest, textStatus, errorThrown) {
  	                parent.LoadingOverlay("hide");
                    $(".invio-file-table-del-row").LoadingOverlay("hide");
                    alert_error(); 
  	              }
	     });  
    });
    
	  $("img.invio-file-table-del-row").live("click",function(){
	    var myObj=$(this);
      
      bootbox.confirm({
          title: 'HeroesPrint.eu',
          message: '<?php echo addslashes(html_entity_decode(ln('Procedere con l\'eliminazione?'),ENT_QUOTES, 'UTF-8')); ?>',
          buttons: {
              cancel: {
                  label: '<i class="fa fa-times"></i> <?php echo addslashes(html_entity_decode(ln('Annulla'),ENT_QUOTES, 'UTF-8')); ?>'
              },
              confirm: {
                  label: '<i class="fa fa-check"></i> <?php echo addslashes(html_entity_decode(ln('Conferma'),ENT_QUOTES, 'UTF-8')); ?>'
              }
          },
          callback: function (result) {
              if(result==true){
                myObj.LoadingOverlay("show");
          	    $.ajax({
          	       type: "POST",
          	       url: getPathRoot+"verifica_file.php",
          	       data: "email="+$("input.invio-file-email").val()+"&del="+myObj.attr("id"),
          	       success: function(msg){
          	                  myObj.LoadingOverlay("hide");
                              if(msg=='1'){
                                $.notify({
                                	// options
                                	message: '<?php echo addslashes(html_entity_decode(ln('File eliminato correttamente'),ENT_QUOTES, 'UTF-8')); ?>',
                                  icon: 'glyphicon glyphicon-ok-sign', 
                                },{
                                	// settings
                                	type: 'success',
                                  allow_dismiss: true,
                                	newest_on_top: false,
                                	showProgressbar: false,
                                  delay: 5000,
                                	placement: {
                                		from: "bottom",
                                		align: "center"
                                	}
                                });
                                
                                $("input.btn-invio-file-submit").trigger("click");
                              }else{
                                $.notify({
                                	// options
                                	message: '<?php echo addslashes(html_entity_decode(ln('Dopo l\'approvazione non è più possibile eliminare il file. Contattare l\'Assistenza Clienti'),ENT_QUOTES, 'UTF-8')); ?>',
                                  icon: 'glyphicon glyphicon-warning-sign', 
                                },{
                                	// settings
                                	type: 'danger',
                                  allow_dismiss: true,
                                	newest_on_top: false,
                                	showProgressbar: false,
                                  delay: 5000,
                                	placement: {
                                		from: "bottom",
                                		align: "center"
                                	}
                                });  
                              }  
          	                },
          	       error: function(XMLHttpRequest, textStatus, errorThrown) {
          	                myObj.LoadingOverlay("hide");
                            alert_error(); 
          	              }
        	     });
             }
          }
       });
	  });
    
	  function invio_file(msg){
	    var arrFiles = [];
      if(msg!="null") arrFiles=json_decode(msg);
	    if(msg!="-1") {
	      $("span.invio-file-table-title-num").html($("input.invio-file-ordine").val()+":");
	      $("div.invio-file-table-title").show();
				$("div.invio-file-table").show();
	      $("div.invio-file-text").show();
	      $("div.invio-file-upload-right").show();
	      
	      
        var table="";
        var im_dir="";
        
        table+="<table class='table table-striped ecomm-table-upload'>";
        table+="  <thead>";
        table+="    <tr>";
        table+="      <th> </th>";
        table+="      <th> </th>";
        table+="      <th><?php echo addslashes(html_entity_decode(ln('Informazioni'),ENT_QUOTES, 'UTF-8')); ?></th>";
        table+="      <th><?php echo addslashes(html_entity_decode(ln('Anteprima'),ENT_QUOTES, 'UTF-8')); ?></th>";
        
        table+="    </tr>";
        table+="  </thead>";  
                
        $.each(arrFiles,function(key, row){     
          if(key!="note"){
            im_dir=getPathRoot+"resourcesdyn/users_file/"+row['id_ecommerce_ordini']+"/preview/"+basename(row['prevFile']);
            var sel1="";
            var sel2="";
            var sel3="";
            var sel4="";
            var corrCol1="";
            var corrCol2="";
            var corrCol3="";
            var corrCol4="";
            var icc="";
            
            if(row['icc']=="" && row['colorspace']=="") corrCol1="selected";
            if(strpos(row['icc'],"GRACoL2006_Coated1v2.icc")>0 && row['colorspace']=="CMYK") {corrCol2="selected"; icc="GRACoL2006_Coated1v2.icc"};
            if(strpos(row['icc'],"AdobeRGB1998.icc")>0 && row['colorspace']=="RGB") {corrCol3="selected"; icc="AdobeRGB1998.icc"}
            if(strpos(row['icc'],"sRGB-IEC61966-2.1.icc")>0 && row['colorspace']=="sRGB") {corrCol4="selected"; icc="sRGB-IEC61966-2.1.icc"}
            
            if(row['priority']==1) sel1="selected";
            if(row['priority']==2) sel2="selected";
            if(row['priority']==3) sel3="selected";
            if(row['priority']==4) sel4="selected"; 
            
            var extOrFile = retExt(row['orFileEx']);
            var nomeOrFile = basename(retFileNameWhitoutExt(row['orFile']) + "." + extOrFile);
            
            table+="  <tbody>";
            table+="    <tr>";
            table+="      <td><img title='<?php echo addslashes(html_entity_decode(ln('Elimina'),ENT_QUOTES, 'UTF-8')); ?>' class='invio-file-table-del-row' id='"+row['idconc']+"' src='"+getPathRoot+"resources/1296807289_DeleteRed.png' /></td>";
            table+="      <td><img src='"+getPathRoot+"resources/ico_file_"+extOrFile+".png' /></td>";
            table+="      <td title='"+nomeOrFile+"'><div style='color:red; font-weight:bold;'>"+troncaTesto(nomeOrFile,35)+"</div>";
            table+="        <div style='font-weight:bold;'><?php echo addslashes(html_entity_decode(ln('Pagine:'),ENT_QUOTES, 'UTF-8')); ?> "+row['npags']+"</div>";
            
            if(row['npags']=="1") {
              table+="        <div style='font-weight:bold;'><?php echo addslashes(html_entity_decode(ln('Dim. in MM:'),ENT_QUOTES, 'UTF-8')); ?> "+row['w_mm']+"X"+row['h_mm']+"</div>";
              table+="        <div style='font-weight:bold;'><?php echo ln("DPI"); ?>: "+row['dpi']+"</div>";
              if(row['icc']!="" && row['colorspace']!="") table+="        <div style='font-weight:bold;'><?php echo addslashes(html_entity_decode(ln('ColorSpace'),ENT_QUOTES, 'UTF-8')); ?>: "+row['colorspace']+", "+icc+"</div>";
            }
            
            table+="        <div style='font-weight:bold;'>("+row['file_size']+" MB)</div>";
            table+="        <a href='"+im_dir+"' target='_blank' download type='button' class='btn btn-info ecomm_prev_file_download'><i class='fa fa-download'></i><br><?php echo addslashes(html_entity_decode(ln('SCARICA ANTEPRIMA <BR> PER VEDERE I COLORI CMYK <BR> CORRETTI'),ENT_QUOTES, 'UTF-8')); ?></a>";
            table+="      </td>";    
            table+="      <td>";
            
            if(row['approved']=='1' && row['converting']=='1') table+="        <div class='alert alert-info'><span class='glyphicon glyphicon-cog'></span> <strong><?php echo addslashes(html_entity_decode(ln('Preparazione dei Files'),ENT_QUOTES, 'UTF-8')); ?></strong><hr class='message-inner-separator'><p><?php echo addslashes(html_entity_decode(ln('Sto convertendo i tuoi files nel giusto formato di stampa, questa operazione puo\' richiedere diversi minuti. Puoi chiudere il browser o spegnere il computer e continuare a lavorare. Per controllare lo stato di avanzamento puoi aggiornare questa pagina di tanto in tanto premendo il tatso F5'),ENT_QUOTES, 'UTF-8')); ?>.</p></div>";
            if(row['approved']=='1' && row['converting']=='-1') table+="        <div class='alert alert-warning'><span class='glyphicon glyphicon-warning-sign'></span> <strong><?php echo addslashes(html_entity_decode(ln('Approvazione Fallita'),ENT_QUOTES, 'UTF-8')); ?></strong><hr class='message-inner-separator'><p><?php echo addslashes(html_entity_decode(ln('Abbiamo riscontrato dei problemi durante la preparazione dei tuoi file. Ti chiediamo di verificare i file da te caricati e di riprovare. File troppo grandi, in scala 1:1 o con un numero eccessivo di vettori possono dare problemi di questo tipo. Se il problema persiste contatta l\'Assistenza Tecnica'),ENT_QUOTES, 'UTF-8')); ?>.</p><button id='"+row['idconc']+"' type='button' class='btn btn-warning ecomm_file_approved'><?php echo addslashes(html_entity_decode(ln('RIPROVA'),ENT_QUOTES, 'UTF-8')); ?></button></div>";
            if(row['approved']=='1' && row['converting']=='2') table+="        <div class='alert alert-success'><span class='glyphicon glyphicon-ok'></span> <strong><?php echo addslashes(html_entity_decode(ln('Stampa approvata'),ENT_QUOTES, 'UTF-8')); ?></strong><hr class='message-inner-separator'><p><?php echo addslashes(html_entity_decode(ln('Hai confermato di aver controllato l\'anteprima di stampa e che questa risulta essere conforme. Il file risulta quindi essere in lavorazione e non è più possibile annullare il lavoro. Ti ricordiamo che quello che vedi nell\'anteprima di stampa sarà esattamente quanto prodotto dalle nostre stampanti. Ti ricordiamo inoltre che i colori stampati potrebbero risultare leggermente differenti da quanto visualizzato sul tuo monitor in base alla marca e modello di quest\'ultimo'),ENT_QUOTES, 'UTF-8')); ?>.</p></div>";
            if(row['approved']=='0') table+="        <div class='alert alert-danger'><span class='glyphicon glyphicon-warning-sign'></span> <strong><?php echo addslashes(html_entity_decode(ln('Stampa non ancora approvata'),ENT_QUOTES, 'UTF-8')); ?></strong><hr class='message-inner-separator'><p><?php echo addslashes(html_entity_decode(ln('Attenzione! Per poter dare il via alla stampa, devi approvare l\'anteprima generato dal sistema. Usa il mouse per ispezionare l\'immagine. Puoi ingrandire e ridurre l\'immagine usando la rotella del mouse. Ti ricordiamo che quello che vedi nell\'anteprima di stampa sarà esattamente quanto prodotto dalle nostre stampanti. Ti ricordiamo inoltre che i colori stampati potrebbero risultare leggermente differenti da quanto visualizzato sul tuo monitor in base alla marca e modello di quest\'ultimo. Una volta che avrai approvato l\'anteprima, avrà inizio la produzione delle tue stampe. Eventuali errori o difetti presenti nell\'anteprima e approvati non potranno essere oggetto di contestazione. Ti invitiamo pertanto a controllare con attenzione prima di approvare'),ENT_QUOTES, 'UTF-8')); ?>.</p><br><!--<div class='form-group' style='float:left;margin-right:10px;'><select idfile='"+row['id']+"' class='ecomm-file-icc' data-style='btn-info'><option title='<?php echo addslashes(html_entity_decode(ln('I colori non sono corretti?'),ENT_QUOTES, 'UTF-8')); ?>' value='1' "+corrCol1+"><?php echo addslashes(html_entity_decode(ln('Nessuna correzione (se quello che vedi va già bene)'),ENT_QUOTES, 'UTF-8')); ?></option><option title='<?php echo addslashes(html_entity_decode(ln('I colori non sono corretti?'),ENT_QUOTES, 'UTF-8')); ?>' value='2' "+corrCol2+"><?php echo addslashes(html_entity_decode(ln('Correzione CMYK (se vedi colori troppo vivaci)'),ENT_QUOTES, 'UTF-8')); ?></option><option title='<?php echo addslashes(html_entity_decode(ln('I colori non sono corretti?'),ENT_QUOTES, 'UTF-8')); ?>' value='3' "+corrCol3+"><?php echo addslashes(html_entity_decode(ln('Correzione RGB (se vedi colori troppo scuri)'),ENT_QUOTES, 'UTF-8')); ?></option><option title='<?php echo addslashes(html_entity_decode(ln('I colori non sono corretti?'),ENT_QUOTES, 'UTF-8')); ?>' value='4' "+corrCol4+"><?php echo addslashes(html_entity_decode(ln('Correzione sRGB (per ottenere sfumature più morbide)'),ENT_QUOTES, 'UTF-8')); ?></option></select></div>--><div><button id='"+row['idconc']+"' type='button' class='btn btn-success ecomm_file_approved'><?php echo addslashes(html_entity_decode(ln('APPROVA'),ENT_QUOTES, 'UTF-8')); ?></button></div></div>";
            
            table+="        <div><?php echo addslashes(html_entity_decode(ln('Indicazioni per questo file (esigenze particolari, data di consegna ecc..)'),ENT_QUOTES, 'UTF-8')); ?>:<textarea class='form-control spacing-xs invio-note-tutte' rows='4' cols='50' id='"+row['id']+"'>"+stripslashes(row['note_text'])+"</textarea></div><div class='form-group'><label for='pri_sel'><?php echo addslashes(html_entity_decode(ln('Priorità'),ENT_QUOTES, 'UTF-8')); ?>:</label><select class='form-control' id='pri_sel' idfile='"+row['id']+"'><option "+sel1+" value='1' ><?php echo addslashes(html_entity_decode(ln('Bassa'),ENT_QUOTES, 'UTF-8')); ?></option><option "+sel2+" value='2'><?php echo addslashes(html_entity_decode(ln('Media'),ENT_QUOTES, 'UTF-8')); ?></option><option "+sel3+" value='3'><?php echo addslashes(html_entity_decode(ln('Alta'),ENT_QUOTES, 'UTF-8')); ?></option><option "+sel4+" value='4'><?php echo addslashes(html_entity_decode(ln('Urgente'),ENT_QUOTES, 'UTF-8')); ?></option></select></div><img src='"+im_dir+"' itemprop='image' class='img-responsive ecomm-img-upload' style='max-height:1024px;' />";     
            table+="    </td></tr>";
            table+="  </tbody>";
          }else{
            $("#invio-file-note").val(stripslashes(row));
          }
        });
               
        table+="</table>";
        
	      $("div.invio-file-table").html(table);
        $(".ecomm-img-upload").elevateZoom({zoomWindowPosition: 13,scrollZoom : true,zoomWindowWidth : 340,zoomWindowHeight : 300,});
        $(".ecomm-file-icc").selectpicker({
          style: 'btn-info',
          size: 10
        });
        
	      var email=urlencode($("input.invio-file-email").val());
	      var ordine=urlencode($("input.invio-file-ordine").val());

				<?php $timestamp = time(); ?>
        
				$(function() {
					$('#file_upload').uploadifive({
						'auto'             : true,
						'removeCompleted'		: true,
					  'fileType'     : false,
				    'fileDesc'     : 'Documents (.pdf, .jpg, .jpeg, .tiff, .tif, .png, .ai, .psd, .eps, .svg)',
				    'buttonClass'  : 'file-upload-btn',
				    'width'        : 100,
			      'height'       : 30,
			      'buttonText'   : '',
						'formData'     : {
      											   'timestamp' : '<?php echo $timestamp; ?>',
      											   'token'     : '<?php echo md5('pippo83' . $timestamp); ?>',
      											   'email1'    : email,
      											   'ordine1'   : ordine,
      											   'fileext'   : '*.pdf;*.jpg;*.jpeg;*.tiff;*.tif;*.png;*.ai;*.psd;*.eps;*.svg;'
						                 },
						'queueID'      : 'queue',
						'uploadScript' : getPathRoot+'verifica_file.php',
                        'onInit' : function(){
                            $("#uploadifive-file_upload").width("100%");
                            $("#uploadifive-file_upload").height("100%");
                            $("#uploadifive-file_upload #uploadifive-file_upload").css("display","none");
                        },
            'onProgress'   : function(file, e) {
                    if (e.lengthComputable) {
                        var percent = Math.round((e.loaded / e.total) * 100);
                        
                        if(percent==100) {
                        $("#hrs_file_upload").LoadingOverlay("show", {
                            image       : "",
                            text        : "<?php echo addslashes(html_entity_decode(ln('Attendere! Sto elaborando i tuoi file.'),ENT_QUOTES, 'UTF-8')); ?>",
                            textClass   : "upload_elab_file_wait"
                        });  
                      };
                    }                       
                  },                                
						'onQueueComplete' : 
							function(uploads){
				            if(uploads.successful>0){
                      $("#hrs_file_upload").LoadingOverlay("hide");
                      $.notify({
                      	// options
                      	message: uploads.successful + ' ' + '<?php echo addslashes(html_entity_decode(ln('File caricato correttamente!'),ENT_QUOTES, 'UTF-8')); ?>',
                        icon: 'glyphicon glyphicon-ok-sign', 
                      },{
                      	// settings
                      	type: 'success',
                        allow_dismiss: true,
                      	newest_on_top: false,
                      	showProgressbar: false,
                        delay: 5000,
                      	placement: {
                      		from: "bottom",
                      		align: "center"
                      	}
                      });
                      $("input.btn-invio-file-submit").trigger("click");
				            }else{
                      $.notify({
                      	// options
                      	message: '<?php echo addslashes(html_entity_decode(ln('L\'archivio caricato risulta danneggiato, vuoto o non valido. Si prega di ripetere l\'upload. Se il problema persiste, contattare l\'assistenza'),ENT_QUOTES, 'UTF-8')); ?>.',
                        icon: 'glyphicon glyphicon-warning-sign', 
                      },{
                      	// settings
                      	type: 'danger',
                        allow_dismiss: true,
                      	newest_on_top: false,
                      	showProgressbar: false,
                        delay: 5000,
                      	placement: {
                      		from: "bottom",
                      		align: "center"
                      	}
                      });
                      $("#hrs_file_upload").LoadingOverlay("hide");
                      $('#file_upload').uploadifive('cancel', file);
				            }
				          },
				      'onUploadComplete':
					      function(file,data){
                  if(data==-1) alert_error();
                  if(data==-2) {
                    $.notify({
                      	// options
                      	message: '<?php echo addslashes(html_entity_decode(ln('Il file caricato contiene più di 20 pagine e non puo\' essere accettato dal nostro sistema. Ti preghiamo di ridurre il numero di pagine e riprovare'),ENT_QUOTES, 'UTF-8')); ?>.',
                        icon: 'glyphicon glyphicon-warning-sign', 
                      },{
                      	// settings
                      	type: 'danger',
                        allow_dismiss: true,
                      	newest_on_top: false,
                      	showProgressbar: false,
                        delay: 30000,
                      	placement: {
                      		from: "bottom",
                      		align: "center"
                      	}
                      });  
                  }
                  
                  $("#hrs_file_upload").LoadingOverlay("hide");
					      },
              'onAddQueueItem' : function(file){
                   var fileName = file.name;
                   var ext = strtolower(fileName.substring(fileName.lastIndexOf(".")+1,fileName.length)); // Extract EXT
                    switch (ext) {
                      case 'pdf':
                      case 'jpg':
                      case 'jpeg':
                      case 'tiff':
                      case 'tif':
                      case 'ai':
                      case 'psd':
                      case 'eps':
                      case 'png':
                      case 'svg':
                      break;
                      default:
                         $.notify({
                        	// options
                        	message: '*.'+ext.toUpperCase()+' : <?php echo addslashes(html_entity_decode(ln('Formato di file non valido'),ENT_QUOTES, 'UTF-8')); ?>. OK : (.pdf, .jpg, .jpeg, .tiff, .png)',
                          icon: 'glyphicon glyphicon-warning-sign', 
                        },{
                        	// settings
                        	type: 'danger',
                          allow_dismiss: true,
                        	newest_on_top: false,
                        	showProgressbar: false,
                          delay: 5000,
                        	placement: {
                        		from: "bottom",
                        		align: "center"
                        	}
                        });
                        $('#file_upload').uploadifive('cancel', file);
                      break;
                   }
                   
                   email=urlencode($("input.invio-file-email").val());
	                 ordine=urlencode($("input.invio-file-ordine").val());                               
                   $.ajax({
            	       type: "POST",
            	       url: getPathRoot+"verifica_file.php",
            	       data: "do_upload="+file.name+"&idord="+ordine, 
                     async: false,                     
            	       success: function(msg) {
                                if(msg=="-1") {
                                  $.notify({
                                  	// options
                                  	message: '<?php echo addslashes(html_entity_decode(ln('Esiste già un file caricato precedentemente con lo stesso nome'),ENT_QUOTES, 'UTF-8')); ?>',
                                    icon: 'glyphicon glyphicon-warning-sign', 
                                  },{
                                  	// settings
                                  	type: 'danger',
                                    allow_dismiss: true,
                                  	newest_on_top: false,
                                  	showProgressbar: false,
                                    delay: 5000,
                                  	placement: {
                                  		from: "bottom",
                                  		align: "center"
                                  	}
                                  });
                                  $('#file_upload').uploadifive('cancel', file);
                                }  
            	                },
            	       error: function(XMLHttpRequest, textStatus, errorThrown) {
                              alert_error();
                              $('#file_upload').uploadifive('cancel', file); 
            	              }
            	     });                                     
                }
					});
				});
	    }else{
	      $("div.invio-file-table-title").hide();
	      $("div.invio-file-upload-right").hide();
	      $("div.invio-file-text").hide();
	      $("div.invio-file-table").html("");
	      $("div.rsLoading").hide();
        $.notify({
        	// options
        	message: '<?php echo addslashes(html_entity_decode(ln('I dati inseriti non risultano corretti. Controllare e riprovare'),ENT_QUOTES, 'UTF-8')); ?>.',
          icon: 'glyphicon glyphicon-warning-sign', 
        },{
        	// settings
        	type: 'danger',
          allow_dismiss: true,
        	newest_on_top: false,
        	showProgressbar: false,
          delay: 5000,
        	placement: {
        		from: "bottom",
        		align: "center"
        	}
        });  
	    }
	    
	    $("div.rsLoading").hide();
	  }

	
  /* BACK TO TOP BUTTON */
	$(".back-to-top").click(function(event) {
	    event.preventDefault();
	    $('html, body').animate({scrollTop: 0}, 300);
	    return false;
	})

  /* COOKIES LAW MANDATORY ALERT */
  //Cookies.remove('cookie-alert');//for testing purpose only
  a = Cookies.get('cookie-alert'); 
  if(  a !== 'accepted' ){
    $('.cookie-alert').css("display","table");
  }
  $('.cookie-alert button').click(function( e ){
      e.preventDefault(); // Do not perform default action when button is clicked      
       Cookies.set('cookie-alert', 'accepted', { expires: 365, path: '/' });
  });

  $('#prodDetailsModal').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget) // Button that triggered the modal
    var product = button.data('prod') // Extract info from data-* attributes
    var modal = $(this)
    
    /*
    if (product == "carta") { modal.find('.modal-title').text('Stampa manifesti Carta ');}
    if (product == "pvc") { modal.find('.modal-title').text('Stampa su PVC ');}
    if (product == "banner") { modal.find('.modal-title').text('Stampa Banner ');}
    */
    
  });
  
  //$("div.crt-caratteristiche-container").appendTo("div.ecomm_carrello");
  //$("div.crt-prezzi-container").appendTo("div.ecomm_carrello");
  
  $("#LoginAreaRis .theInput").live("click",function(){
    $(this).attr("style","color:#000000;");
  });
});
</script>  
