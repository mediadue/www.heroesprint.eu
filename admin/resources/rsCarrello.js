function CarrelloAjax(current,combi,strProdAdd,strProdMod,strProdCons) {
  var g_obj = this;
  var g_Current= current; 
  var g_Combi= combi; 
  var g_ProdAggiunto = strProdAdd; 
  var g_ProdMod = strProdMod;
  var g_ProdCons = strProdCons;
  var g_Init = false;
  var g_rsCarrelloBuffer = [];
  
  this.rsCarrelloBeforeSend = function(jqXHR, settings) {
    $("div.ecomm_carrello .crt-prezzo-scontato,div.ecomm_carrello .crt-aggiungi-al-carrello, div.ecomm_carrello .crt-aggiorna-il-carrello").hide();
    $("div.ecomm_carrello .crt-loading").show();
    
    var ProcID=getFilenameUnique();
    g_rsCarrelloBuffer.push(ProcID);
    sleep(25); 
    
    return true; 
  }
  
  this.rsCarrelloCompleteSend = function(jqXHR, textStatus) {
    g_rsCarrelloBuffer.shift();
    
    if(g_rsCarrelloBuffer.length==0) {
      $("div.ecomm_carrello .crt-loading").hide();
      $("div.ecomm_carrello .crt-prezzo-scontato,div.ecomm_carrello .crt-aggiungi-al-carrello, div.ecomm_carrello .crt-aggiorna-il-carrello").show();
    }
  }
  
  this.sendData=function(varname,data,fun) {
    $.ajax({
     type: "POST",
     url: getPathRoot+"logic/rsAction.php",
     data: "myRsCarrello="+varname+"&value="+data+"&current="+g_Current+"&combi="+g_Combi,
     success: fun,
     error: function(XMLHttpRequest, textStatus, errorThrown) {
              g_obj.sendData(varname,data,fun); 
            },
     beforeSend: g_obj.rsCarrelloBeforeSend,
     complete: g_obj.rsCarrelloCompleteSend,
     async:true
   });
  }
  
  this.getData=function(varname,fun) {
    $.ajax({
       type: "POST",
       url: getPathRoot+"logic/rsAction.php",
       data: "myRsCarrelloget="+varname+"&current="+g_Current+"&combi="+g_Combi,
       success: fun,
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                g_obj.getData(varname,fun); 
              },
       beforeSend: g_obj.rsCarrelloBeforeSend,
       complete: g_obj.rsCarrelloCompleteSend,
       async:true
     }); 
  }
  
  this.refreshCaratteristiche=function(){
    $("div.ecomm_carrello input[caratteristica], select[caratteristica], div[inputcaratt]").each(function() {
          g_obj.crtInputAction($(this));
    });
  }
  
  this.crtInputAction=function(self){
		if(self.hasClass("crt-upd-dim")) {
          var vv1=$("input.crt-upd-dim1").val();
          var vv2=$("input.crt-upd-dim2").val();
          
          var max1=toFloat($("input.crt-upd-dim1").attr("max"));
          var max2=toFloat($("input.crt-upd-dim2").attr("max"));
          var tmax;
          
          if(vv1>max1) {
            tmax=max1;
            max1=max2;
            max2=tmax; 
          }
          
          if(vv1>max1 && max1>0) {$("input.crt-upd-dim1").val(max1);vv1=max1}
          if(vv2>max2 && max2>0) {$("input.crt-upd-dim2").val(max2);vv2=max2}
          
          if(vv1==0) vv1=1;
          if(vv2==0) vv2=1;
          
          var res=toFloat(vv1) +  "x" + toFloat(vv2);
          if(res!="0x0") {
            self.val(res);
          }else{
            var str=explode("x",self.val());
            $("input.crt-upd-dim1").val(str[0]);
            $("input.crt-upd-dim2").val(str[1]);
          }
        }
        
        //$("div.ecomm_carrello .crt-aggiungi-al-carrello, div.ecomm_carrello .crt-aggiorna-il-carrello").hide();
        
        
        var caratteristica=self.attr("caratteristica");
        var auto=self.attr("auto");
        var id = new Array();
        var isCheck=false;
        var isQuantita=false;
        
        if(self.attr("type")=="checkbox") {
          if(self.is(':checked')) id=true;else id=false;
          isCheck=true;
        }else if(self.hasClass("select")){
        	self.children("option:selected").each(function() {
            if(self.attr("value")!="") id.push(self.attr("value"));
          });
          
          id=id.join(";"); 
          id=caratteristica+"#"+id;
          
          if(auto=="1") id=id+"#auto";
        }else if(self.hasClass("crtp-caratteristiche-aff")){
          self.find(".crt-caratteristiche-aff-sel").each(function() {
            if($(this).attr("value")!="") id.push($(this).attr("value"));
          });
          
          id=id.join(";"); 
          id=caratteristica+"#"+id;
          
          if(auto=="1") id=id+"#auto";  
        }else{
          id=self.val();  
        }
        
        if(self.hasClass("crt-upd-q") || self.hasClass("crt-upd-dim")) isQuantita=true; 
        
        if(self.hasClass("crt-upload-cmyk")){
          var cmyk=explode("*",id);
          if(count(cmyk)>1){
            $("div.crt-upload-cmyk-res-c-val").html(cmyk[0]);
            $("div.crt-upload-cmyk-res-m-val").html(cmyk[1]);
            $("div.crt-upload-cmyk-res-y-val").html(cmyk[2]);
            $("div.crt-upload-cmyk-res-k-val").html(cmyk[3]);
            
            $("img.crt-upload-cmyk-res-img-val").attr("src",getPathResourcesDynamic+"uploaded/"+cmyk[4]);
            $("img.crt-upload-cmyk-res-img-val").attr("title",cmyk[4]);
            $("div.crt-upload-cmyk-res").show("slow");
          }else{
            $("div.crt-upload-cmyk-res").hide("slow"); 
          }
        }
        
        if(!self.hasClass("select") && !self.hasClass("select-aff")) id=caratteristica+"#"+id+"#auto";
        
        g_obj.sendData("setCaratteristiche",id, function(msg) {
          var retmsg=msg.split("#AA134#");
          
          var gallery=retmsg[1].split("#AA#");
          var abbinamenti=unserialize(rawurldecode(retmsg[3]));
          var quotazione=retmsg[4];

          var pSenzaSconto = retmsg[5];
          var pFinale = retmsg[6];
          var pSconto = retmsg[7];

          if(quotazione==1){
            var c=$("div.crt-richiedi-quotazione").attr("c");
            c=c+caratteristica+";";
            
            $("div.crt-richiedi-quotazione").attr("c",c);
            $("div.crt-prezzi-container").hide();
            $("div.crt-richiedi-quotazione").show();
          }else{
            var c=$("div.crt-richiedi-quotazione").attr("c");
            c=explode(";",c);
            for(var ii=0;ii<count(c);ii++) {
              if(c[ii]==caratteristica) c.splice(ii, 1); 
            }
            c=implode(";",c);
            
            $("div.crt-richiedi-quotazione").attr("c",c);
            if(c=="" || c==";") {
              $("div.crt-richiedi-quotazione").hide();
              $("div.crt-prezzi-container").show();
            }  
          }
          
          $("div.ecomm_carrello [ecomm-disCaratt]").each(function(){
            var tmparr=explode(";",$(this).attr("ecomm-disCaratt"));
            var tmparr2=new Array();
            if(tmparr!=null) {
              for(var ii=0;ii<count(tmparr);ii++) {
                if(tmparr[ii]!=caratteristica) tmparr2.push(tmparr[ii]);   
              } 
              
              $(this).attr("ecomm-disCaratt",implode(";",tmparr2));
            }  
          });
          
          for(var ii=0;ii<count(abbinamenti);ii++) {
            var tobj2=$("div.ecomm_carrello .crt-caratteristica[caratteristica='"+abbinamenti[ii]['id_ecommerce_caratteristiche']+"'] [value='"+abbinamenti[ii]['id_ecommerce_valori']+"']");
            var tmparr=explode(";",tobj2.attr("ecomm-disCaratt"));
            
            if(tmparr==null) { 
              tmparr=new Array();
            }
            
            if(!in_array(caratteristica,tmparr)) tmparr.push(caratteristica);
            tobj2.attr("ecomm-disCaratt",implode(";",tmparr));  
          }
          
          $("div.ecomm_carrello [ecomm-disCaratt]").each(function(){
            var tmparr=$(this).attr("ecomm-disCaratt");
            if(tmparr==null) tmparr="";
            
            if(tmparr!="") {
              $(this).addClass("ecomm-values-disabled");
              $(this).attr("disabled", "disabled");
              $(this).parents(".crt-fotogallery-thm").append("<div class='ecomm-values-disabled-color'></div>");
              if(!$(this).attr("zoom")) $(this).css("opacity","0.3");
            }
          
            if(tmparr=="") {
              $(this).parents(".crt-fotogallery-thm").find(".ecomm-values-disabled-color").remove();
              $(this).removeClass("ecomm-values-disabled");
              $(this).attr("disabled", false);
              $(this).css("opacity","1.0");
            }
          });
          
          var src="";
          
          if(retmsg[0]!="0,00") {
            $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").html("€ "+retmsg[0]);
            $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-input").css("width","80%");
            $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").show();
          } else {
            if(isCheck) {
              $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-input").css("width","18px");
            }else{
              $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-input").css("width","80%");  
            } 
            $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").hide(); 
          }
          
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-info").css("visibility","hidden");
          var fumetto=trim(strtolower($("<div></div>").html(retmsg[2]).find("div.fumetto").html()));
          fumetto=strip_tags(fumetto);
          if(fumetto!="") {
            $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-info").css("visibility","visible");
            $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .tTip").attr("title",retmsg[2]);
          }
          
          
          $("div.ecomm_carrello .crt-fotogallery div img[idcaratteristica="+caratteristica+"]").remove(); 
          for(var i=0;i<gallery.length;i++){
            $("div.ecomm_carrello .crt-fotogallery div").append(gallery[i]);
          }
          $("div.ecomm_carrello .crt-fotogallery div img:hidden").fadeIn("slow");
          
          $("a.crt-not-zoom, a.crt-fotogallery-thm").live("click",function(){
            var newim=$(this).find("img");
            $("img.crt-foto-articolo-img").attr("src",newim.attr("zoom"));
            return false
          });
          
          
          //$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
          g_obj.refreshPrezzi(pSenzaSconto,pFinale,pSconto);
        });
  }
  
  this.refreshPrezzi=function(pSenzaSconto,pFinale,pSconto) { 
    var prezzo=pSenzaSconto;
    var fprezzo=toFloat(pSenzaSconto);
    var roundFprezzo=roundNumber(fprezzo,2);
    $("div.crt-prezzo").html("€ "+roundFprezzo);
     
    var prezzoScontato=toFloat(pFinale);
    if(prezzoScontato>0) {
      $("div.crt-prezzo-scontato").html("€ "+roundNumber(prezzoScontato,2));
    }

    var sconto=pSconto;
    if(toFloat(sconto)>0) {
      if(Math.round(toFloat(sconto))==toFloat(sconto)) {
        $("div.crt-sconto-perc").html("<span style='padding-right:20px;'>sconto</span> - "+roundNumber(toFloat(sconto),2)+" %");
      }
    }

    $("div.rsLoading").hide();
    $("div.ecomm_carrello").css("visibility","visible");
    $("div.ecomm_carrello .crt-aggiungi-al-carrello, div.ecomm_carrello .crt-aggiorna-il-carrello").show();
  }
  
  if(g_ProdCons=='1') {
    if($("div.ecomm_carrello .crt-articoli-successivi").length>0) {
      $("div.ecomm_carrello .crt-articoli-successivi .dragme").draggable({axis:'y',opacity:0.35,cursor:'move'});
      var successivi_h= $(".crt-col-two").height()-70;
      if(successivi_h>200) $("div.ecomm_carrello .crt-articoli-successivi .dragmeParent").height(successivi_h);
      
      if(g_Current!==false && g_Current!="") { 
        if($("div.ecomm_carrello .crt-articoli-successivi td.td"+g_Current).length>0) {
          var mypos=$("div.ecomm_carrello .crt-articoli-successivi td.td"+g_Current).position();
          $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 0.25,"margin-top": '-='+(mypos.top-50)}, 1000,"linear",function(){
            $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 1},500);  
          });
        }    
      }
      
      $("div.ecomm_carrello .crt-articoli-successivi .arrow-left").click(function(){
        $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 0.25,"margin-top": '+=100'}, 1000,"linear",function(){
          $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 1},500);  
        });  
      });
      
      $("div.ecomm_carrello .crt-articoli-successivi .arrow-right").click(function(){
        $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 0.25,"margin-top": '-=100'}, 1000,"linear",function(){
          $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 1},500);  
        });  
      });
    }
	}else if(g_ProdCons=='2') {
	  if($("div.ecomm_carrello .crt-articoli-successivi").length>0) {
      $("div.ecomm_carrello .crt-articoli-successivi .dragme").draggable({axis:'x',opacity:0.35,cursor:'move'});
      var successivi_h= $(".crt-col-two").width()-70;
      if(successivi_h>200) $("div.ecomm_carrello .crt-articoli-successivi .dragmeParent").width(successivi_h);
      
      if(g_Current!==false && g_Current!="") { 
        if($("div.ecomm_carrello .crt-articoli-successivi td.td"+g_Current).length>0) {
          var mypos=$("div.ecomm_carrello .crt-articoli-successivi td.td"+g_Current).position();
          $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 0.25,"margin-left": '-='+(mypos.left-50)}, 1000,"linear",function(){
            $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 1},500);  
          });
        }    
      } 
      
      $("div.ecomm_carrello .crt-articoli-successivi .arrow-left").click(function(){
        $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 0.25,"margin-left": '+=100'}, 1000,"linear",function(){
          $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 1},500);  
        });  
      });
      
      $("div.ecomm_carrello .crt-articoli-successivi .arrow-right").click(function(){
        $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 0.25,"margin-left": '-=100'}, 1000,"linear",function(){
          $("div.ecomm_carrello .crt-articoli-successivi .dragme").animate({opacity: 1},500);  
        });  
      });
    }    
  }
  
  this.initAjax=function() {
    $(".crt-foto-articolo-zoom").css("opacity","0.6");
    
    $("img.crt-upload-cmyk-cancel").live("click",function(){
      if($("input.crt-upload-cmyk").val()!=""){
        if(confirm("Eliminare il calcolo corrente?")) {
          $("input.crt-upload-cmyk").val("");
          g_obj.crtInputAction($("input.crt-upload-cmyk"));
        } 
      }
    });
    
    $("#crt-upload-cmyk").uploadify({
      'uploader': getPathBackofficeResources+'uploadify.swf',
      'script': getPathBackofficeResources+'uploadify_cmyk.php',
      'folder': getPathResourcesDynamic+'uploaded',
      'cancelImg': getPathBackofficeResources+'cancel.png',
      'buttonImg': getPathBackofficeResources+'upload.png',
      'wmode': 'transparent',
      'width':88,
      'height':20,
      'multi': false,
      'fileExt'     : '*.jpg;*.jpeg',
      'fileDesc'    : 'JPG Files',
      'onComplete': 
        function(event,queueID,fileObj,response,data,uploaderid){
          var cmyk=response.split("*");
          if(count(cmyk)>1){
            $("input.crt-upload-cmyk").val(response);
            //$("input.crt-upload-cmyk").trigger("keyup");
            g_obj.crtInputAction($("input.crt-upload-cmyk"));
          }else{
            alert(response);
          }
        }, 
      'auto': true 
    });

    $("div.ecomm_carrello .crt-fotogallery div img").livequery("click",function(){
      $("a.crt-foto-articolo-zoom-a").attr("href",$(this).attr("zoom"));
      $("a.crt-foto-articolo-zoom-a").attr("title",$(this).attr("title"));
    });

    $("div.ecomm_carrello .crt-fotogallery div img:hidden").fadeIn("slow");
    
  	$("div.ecomm_carrello .crt-foto-articolo a img").hover(function(){
      $("div.ecomm_carrello .crt-articolo-descrizione").hide();
    },function(){
      $("div.ecomm_carrello .crt-articolo-descrizione").show();
    });
  	
    $("div.ecomm_carrello .crt-aggiungi-al-carrello").click(function() {
      var myreturn=false;
     
      var disp=toFloat($(".crt-articolo-disp b").html());
      var q=1;
      
      if($("input.crt-upd-q").length>0) q=toFloat($("input.crt-upd-q").val());
      if($("select.crt-upd-q").length>0) q=toFloat($("select.crt-upd-q option:selected").text());
      
      if(q>disp && disp!=-1){
        alert("<?php echo ln("La disponibilità per questo articolo non è sufficiente. Ridurre la quantità ad un massimo di"); ?> "+disp);
        return;
      }
      
      $(".crt-caratteristica").each(function(){
        if(myreturn) return;
        
        var boolInputText=false;
        if($(this).find("input.text").length>0){
          boolInputText=$(this).find("input.text").val()=="";    
        }
        
        var boolOptionSelect=false;
        if($(this).find("option").length>0){
          boolOptionSelect=$(this).find("option:selected").index()==0;    
        }
        
        var boolColorSel=false;
        if($(this).find(".crt-caratteristiche-color").length>0){
          boolColorSel=$(this).find(".crt-caratteristiche-color-sel").length==0;    
        }
        
        var boolAffSel=false;
        if($(this).find(".crt-caratteristiche-aff").length>0){
          boolAffSel=$(this).find(".crt-caratteristiche-aff-sel").length==0;    
        }
        
        if($(this).find(".ecomm-values-disabled.crt-caratteristiche-color-sel").length>0 || $(this).find(".ecomm-values-disabled.crt-caratteristiche-aff-sel").length>0 || $(this).find("option.ecomm-values-disabled:selected").length>0) {
          alert("<?php echo ln("Attenzione. La scelta fatta per"); ?> '"+$(this).attr("nome")+"' <?php echo ln("non è disponibile nella configuarazione corrente"); ?>.");
          myreturn=true;
        }else if($(this).attr("obblig")=="1"){
          if(boolColorSel || boolAffSel || boolInputText || boolOptionSelect) {
            alert("<?php echo ln("La scelta di"); ?> '"+$(this).attr("nome")+"' <?php echo ln("è obbligatoria"); ?>.");
            myreturn=true;
          }
        }
      });
      
      if(myreturn) return;
      
      var m_btn=$(this);
      m_btn.hide();
      
      g_obj.sendData("addCurrentToCart","-1", function(msg) {
    	$("div.ecomm_viewcart").replaceWith(msg);
        $("div.ecomm_carrello .crt-result").fadeOut(function(){
          $("div.ecomm_carrello .crt-result-text").html(g_ProdAggiunto);
         
          m_btn.show();
          
          var crtcount=toFloat($(".crt-cart-count").html());
          //var updq=toFloat($("input.crt-upd-q").val());
          
          $(".crt-cart-count").html(crtcount+1);
          $("div.rsLoading").hide();
          $("div.ecomm_carrello .crt-result").fadeIn("slow");
        });
      });    
    });
    
    $("div.ecomm_carrello .crt-aggiorna-il-carrello").click(function() {
      var myreturn=false;
      
      var disp=toFloat($(".crt-articolo-disp b").html());
      var q=1;
      
      if($("input.crt-upd-q").length>0) q=toFloat($("input.crt-upd-q").val());
      if($("select.crt-upd-q").length>0) q=toFloat($("select.crt-upd-q option:selected").text());
      
      if(q>disp && disp!=-1){
        alert("<?php echo ln("La disponibilità per questo articolo non è sufficiente. Ridurre la quantità ad un massimo di"); ?> "+disp);
        return;
      }
      
      $(".crt-caratteristica").each(function(){
        if(myreturn) return;
        
        if($(this).find(".ecomm-values-disabled.crt-caratteristiche-color-sel").length>0 || $(this).find(".ecomm-values-disabled.crt-caratteristiche-aff-sel").length>0 || $(this).find("option.ecomm-values-disabled:selected").length>0) {
          alert("Attenzione. La scelta fatta per '"+$(this).attr("nome")+"' non è disponibile nella configuarazione corrente.");
          myreturn=true;
        }else if($(this).attr("obblig")=="1"){
          if($(this).find(".crt-caratteristiche-color-sel").length==0 && $(this).find(".crt-caratteristiche-aff-sel").length==0 && $(this).find("option:selected").index()==0) {
            alert("<?php echo ln("La scelta di"); ?> '"+$(this).attr("nome")+"' <?php echo ln("è obbligatoria"); ?>.");
            myreturn=true;
          } 
        }
      });
      
      if(myreturn) return;
      
      var m_btn=$(this);
      m_btn.hide();
      
      g_obj.sendData("updateCurrent","-1", function(msg) {
        $("div.ecomm_viewcart").replaceWith(msg);
        $("div.ecomm_carrello .crt-result").fadeOut(function(){
          $("div.ecomm_carrello .crt-result-text").html(g_ProdMod);
          
          m_btn.show();
          $("div.rsLoading").hide();
          $("div.ecomm_carrello .crt-result").fadeIn("slow");
        });
      });    
    });
    
    $("div.ecomm_carrello .crt-result-close").click(function(){
      $("div.ecomm_carrello .crt-result").fadeOut("slow");     
    });
    
    $("div.ecomm_carrello a.crt-result-close-a").live("click",function(){
      $("div.ecomm_carrello .crt-result-close").trigger("click");
      return false;      
    });
    
    $("img.crt-caratteristiche-color:first").css("margin-left","0px");
    $("div.crt-caratteristiche-aff:first").css("margin-left","0px");
    
    $("div.crt-caratteristiche-aff").bind("click",function(){
      var notSel=$(this).hasClass("crt-caratteristiche-aff-sel");
      var caratteristica=$(this).attr("caratteristica");      
                              
      $("a.crt-foto-articolo-zoom-a").attr("href",$(this).attr("zoom"));
      $("a.crt-foto-articolo-zoom-a").attr("title",$(this).attr("crtTitle"));
      
      $("div.crt-caratteristiche-aff-arrow").hide();
            
      $("div.crt-caratteristiche-aff-sel[caratteristica='"+caratteristica+"']").removeClass("crt-caratteristiche-aff-sel");
      if(notSel==false) $(this).addClass("crt-caratteristiche-aff-sel");
      $(this).next("div.crt-caratteristiche-aff-arrow").show();      
            
      //$("div.ecomm_carrello .crt-aggiungi-al-carrello").hide();
      //$("div.ecomm_carrello .crt-aggiorna-il-carrello").hide();
      
      var auto=$(this).attr("auto");
      var id = new Array();
      
      if($(this).attr("value")!="" && notSel==false) id.push($(this).attr("value"));
            
      id=id.join(";"); 
      id=caratteristica+"#"+id;
      
      if(auto=="1") id=id+"#auto";      
            
      g_obj.sendData("setCaratteristiche",id, function(msg) {
        var retmsg=msg.split("#AA134#");
        var gallery=retmsg[1].split("#AA#");
        var abbinamenti=unserialize(rawurldecode(retmsg[3]));
        var quotazione=retmsg[4];        
                        
        var pSenzaSconto = retmsg[5];
        var pFinale = retmsg[6];
        var pSconto = retmsg[7];
        
        if(quotazione==1){
          var c=$("div.crt-richiedi-quotazione").attr("c");
          c=c+caratteristica+";";
          
          $("div.crt-richiedi-quotazione").attr("c",c);
          $("div.crt-prezzi-container").hide();
          $("div.crt-richiedi-quotazione").show();
        }else{
          var c=$("div.crt-richiedi-quotazione").attr("c");
          c=explode(";",c);
          for(var ii=0;ii<count(c);ii++) {
            if(c[ii]==caratteristica) c.splice(ii, 1); 
          }
          c=implode(";",c);
          
          $("div.crt-richiedi-quotazione").attr("c",c);
          if(c=="" || c==";") {
            $("div.crt-richiedi-quotazione").hide();
            $("div.crt-prezzi-container").show();
          }  
        }
        
        $("div.ecomm_carrello [ecomm-disCaratt]").each(function(){
          var tmparr=explode(";",$(this).attr("ecomm-disCaratt"));
          var tmparr2=new Array();
          if(tmparr!=null) {
            for(var ii=0;ii<count(tmparr);ii++) {
              if(tmparr[ii]!=caratteristica) tmparr2.push(tmparr[ii]);   
            } 
            
            $(this).attr("ecomm-disCaratt",implode(";",tmparr2));
          }  
        });
        
        for(var ii=0;ii<count(abbinamenti);ii++) {
          var tobj2=$("div.ecomm_carrello .crt-caratteristica[caratteristica='"+abbinamenti[ii]['id_ecommerce_caratteristiche']+"'] [value='"+abbinamenti[ii]['id_ecommerce_valori']+"']");
          var tmparr=explode(";",tobj2.attr("ecomm-disCaratt"));
          if(tmparr==null) { 
            tmparr=new Array();
          }
          
          if(!in_array(caratteristica,tmparr)) tmparr.push(caratteristica);
          tobj2.attr("ecomm-disCaratt",implode(";",tmparr));  
        }
        
        $("div.ecomm_carrello [ecomm-disCaratt]").each(function(){
          var tmparr=$(this).attr("ecomm-disCaratt");
          if(tmparr==null) tmparr="";
          
          if(tmparr!="") {
            $(this).addClass("ecomm-values-disabled");
            $(this).attr("disabled", "disabled");
            $(this).parents(".crt-fotogallery-thm").append("<div class='ecomm-values-disabled-color'></div>");
            if(!$(this).attr("zoom")) $(this).css("opacity","0.3");
          }
        
          if(tmparr=="") {
            $(this).parents(".crt-fotogallery-thm").find(".ecomm-values-disabled-color").remove();
            $(this).removeClass("ecomm-values-disabled");
            $(this).attr("disabled", false);
            $(this).css("opacity","1.0");
          }
        });
        
        var src="";
        
        if(retmsg[0]!="0,00") {
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").html("€ "+retmsg[0]);
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-input").css("width","80%");
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").show();
        } else {
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-input").css("width","80%");
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").hide(); 
        }
        
        $("div.ecomm_carrello .crt-fotogallery div img[idcaratteristica="+caratteristica+"]").remove(); 
        for(var i=1;i<gallery.length;i++){
          $("div.ecomm_carrello .crt-fotogallery div").append(gallery[i]);
        }
        $("div.ecomm_carrello .crt-fotogallery div img:hidden").fadeIn("slow");
        //$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
        g_obj.refreshPrezzi(pSenzaSconto,pFinale,pSconto);
      });
    });
    
    $("a.crt-caratteristiche-color-a").live("click",function(){
      var aobj=$(this).find("img.crt-caratteristiche-color");
      $("a.crt-foto-articolo-zoom-a").attr("href",aobj.attr("zoom"));
      $("a.crt-foto-articolo-zoom-a").attr("title",aobj.attr("crtTitle"));
      
      $("img.crt-caratteristiche-color-sel").removeClass("crt-caratteristiche-color-sel");
      $("div.crt-caratteristiche-color-arrow").hide();
      aobj.addClass("crt-caratteristiche-color-sel");
      $(this).next("div.crt-caratteristiche-color-arrow").show();
      
      //$("div.ecomm_carrello .crt-aggiungi-al-carrello").hide();
      //$("div.ecomm_carrello .crt-aggiorna-il-carrello").hide();
      
      
      var caratteristica=aobj.attr("caratteristica");
      var auto=aobj.attr("auto");
      var id = new Array();
      
      if(aobj.attr("value")!="") id.push(aobj.attr("value"));
      
      id=id.join(";"); 
      id=caratteristica+"#"+id;
      
      if(auto=="1") id=id+"#auto";
      
      g_obj.sendData("setCaratteristiche",id, function(msg) {
        var retmsg=msg.split("#AA134#");
        var gallery=retmsg[1].split("#AA#");
        var abbinamenti=unserialize(rawurldecode(retmsg[3]));
        var quotazione=retmsg[4];
        
        var pSenzaSconto = retmsg[5];
        var pFinale = retmsg[6];
        var pSconto = retmsg[7];
        
        if(quotazione==1){
          var c=$("div.crt-richiedi-quotazione").attr("c");
          c=c+caratteristica+";";
          
          $("div.crt-richiedi-quotazione").attr("c",c);
          $("div.crt-prezzi-container").hide();
          $("div.crt-richiedi-quotazione").show();
        }else{
          var c=$("div.crt-richiedi-quotazione").attr("c");
          c=explode(";",c);
          for(var ii=0;ii<count(c);ii++) {
            if(c[ii]==caratteristica) c.splice(ii, 1); 
          }
          c=implode(";",c);
          
          $("div.crt-richiedi-quotazione").attr("c",c);
          if(c=="" || c==";") {
            $("div.crt-richiedi-quotazione").hide();
            $("div.crt-prezzi-container").show();
          }  
        }
        
        $("div.ecomm_carrello [ecomm-disCaratt]").each(function(){
          var tmparr=explode(";",$(this).attr("ecomm-disCaratt"));
          var tmparr2=new Array();
          if(tmparr!=null) {
            for(var ii=0;ii<count(tmparr);ii++) {
              if(tmparr[ii]!=caratteristica) tmparr2.push(tmparr[ii]);   
            } 
            
            $(this).attr("ecomm-disCaratt",implode(";",tmparr2));
          }  
        });
        
        for(var ii=0;ii<count(abbinamenti);ii++) {
          var tobj2=$("div.ecomm_carrello .crt-caratteristica[caratteristica='"+abbinamenti[ii]['id_ecommerce_caratteristiche']+"'] [value='"+abbinamenti[ii]['id_ecommerce_valori']+"']");
          var tmparr=explode(";",tobj2.attr("ecomm-disCaratt"));
          if(tmparr==null) { 
            tmparr=new Array();
          }
          
          if(!in_array(caratteristica,tmparr)) tmparr.push(caratteristica);
          tobj2.attr("ecomm-disCaratt",implode(";",tmparr));  
        }
        
        $("div.ecomm_carrello [ecomm-disCaratt]").each(function(){
          var tmparr=$(this).attr("ecomm-disCaratt");
          if(tmparr==null) tmparr="";
          
          if(tmparr!="") {
            $(this).addClass("ecomm-values-disabled");
            $(this).attr("disabled", "disabled");
            $(this).parents(".crt-fotogallery-thm").append("<div class='ecomm-values-disabled-color'></div>");
            if(!$(this).attr("zoom")) $(this).css("opacity","0.3");
          }
        
          if(tmparr=="") {
            $(this).parents(".crt-fotogallery-thm").find(".ecomm-values-disabled-color").remove();
            $(this).removeClass("ecomm-values-disabled");
            $(this).attr("disabled", false);
            $(this).css("opacity","1.0");
          }
        });
        
        var src="";
        
        if(retmsg[0]!="0,00") {
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").html("€ "+retmsg[0]);
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-input").css("width","80%");
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").show();
        } else {
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-input").css("width","80%");
          $("div.ecomm_carrello .crt-caratteristica[caratteristica="+caratteristica+"] .crt-caratteristiche-aggiunta").hide(); 
        }
        
        $("div.ecomm_carrello .crt-fotogallery div img[idcaratteristica="+caratteristica+"]").remove(); 
        for(var i=1;i<gallery.length;i++){
          $("div.ecomm_carrello .crt-fotogallery div").append(gallery[i]);
        }
        $("div.ecomm_carrello .crt-fotogallery div img:hidden").fadeIn("slow");
        //$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
        g_obj.refreshPrezzi(pSenzaSconto,pFinale,pSconto);
      });
      
      return false;
    });
    
    $("a.crt-caratteristiche-color-a").mouseover(function(){
      var aobj=$(this).find("img.crt-caratteristiche-color");
      var caratteristica=aobj.attr("caratteristica");
      $("div.crt-color-container[caratteristica='"+caratteristica+"'] .crt-color-zoom").attr("src",aobj.attr("crtSmall"));
      $("div.crt-color-container[caratteristica='"+caratteristica+"'] .crt-color-nome").html(aobj.attr("crtTitle"));
      $("div.crt-color-container[caratteristica='"+caratteristica+"'] .crt-color-descr").html(strip_tags(aobj.attr("crtDescr")));
      $("div.crt-color-container[caratteristica='"+caratteristica+"']").show();
    }).mouseout(function(){ 
      var aobj=$(this).find("img.crt-caratteristiche-color");
      var caratteristica=aobj.attr("caratteristica");
      $(".crt-color-container[caratteristica='"+caratteristica+"']").hide(); 
    });
    
    $(".ecomm-values-disabled-color").live("mouseover",function(){
      $(this).prev(".crt-caratteristiche-color").css("border","2px #AB2F41 solid");  
    })
    
    $(".ecomm-values-disabled-color").live("mouseout",function(){ 
      $(this).prev(".crt-caratteristiche-color").css("border",""); 
    });
    
    $("div.ecomm_carrello .crt-caratteristiche-input select").live("change",function() {
    	g_obj.refreshCaratteristiche();
    });
    
    $("div.ecomm_carrello .crt-caratteristiche-input input:checkbox").live("click",function() {
    	g_obj.refreshCaratteristiche();
    });
    
    $("div.ecomm_carrello .crt-caratteristiche-input input").live("keyup",function() {
    	typewatch(function(){
    		g_obj.refreshCaratteristiche();
    	},650);
    });
    
    $("div.ecomm_carrello .crt-caratteristiche-input a.crt-fotogallery-thm").live("click",function() {
      return false;
    });
    
    g_obj.refreshCaratteristiche(); 
  }
}