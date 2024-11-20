<script>
$(window).load(function(){
  function initWizard(){
    $("div.ez-wr div.mywiz-lay-resize-h").each(function(){
      if($(this).children("div.ez-wr").children("div.ez-wr div.mywiz-lay-resize-h").length>0){
        var html = $('<div>').append($(this).clone()).remove().html();
        var newHtml = $(this).html();
        var $mydom = $(newHtml).attr("oldParent",html);
        
        $(this).replaceWith($mydom);
      }
    });
    
    $("div.mywiz-lay-resize-h").not(".mywiz-initialized").resizable({
      maxHeight:400,
      minHeight:30,
      autoHide: true,
      resize: function(event, ui) {
        if($(this).parent("div.ez-wr.wiz-mod").length>0 && $(this).parent(".wiz-mod").parent(".mywiz-lay").length==0){
          var ph=$(this).parent("div.ez-wr.wiz-mod").height();
          var b=ph;
          $(this).parent("div.ez-wr.wiz-mod").find("div.ez-wr:not(.wiz-mod)").each(function(){
            b=b-$(this).height();  
          });
                                
          var h2=$(this).height();
          var h3=$(this).siblings('div:last').height();
          var h1=ph-(h2+h3);
          
          var h = h2+b;
          ui.element.resizable({maxHeight: h});
        }
        
        $(this).css("width","100%");
        ui.size.width = ui.originalSize.width;
        
        $("div.wiz-text").each(function(){
          var p=$(this).parent("div.ez-box");
          $(this).height(p.height()-20);
          $(this).width(p.width()-20);
        });
      } 
    });
    
    $("div.mywiz-lay-resize-w").not(".mywiz-initialized").each(function(){ 
      var w = $(this).width()+$(this).siblings('div:last').width()-20;
      $(this).resizable({
        maxWidth: w,
        minWidth:20,
        autoHide: true,
        resize: function(event, ui) {
          var w = $(this).width()+$(this).siblings('div:last').width()-20;
          ui.element.resizable({maxWidth: w});
          $(this).css("height","100%");
          
          $("div.wiz-text").each(function(){
            var p=$(this).parent("div.ez-box");
            $(this).height(p.height()-20);
            $(this).width(p.width()-20);
          });
        } 
      });
    });
    
    $("div.mywiz-lay-resize-w,div.mywiz-lay-resize-h,div.mywiz-lay-resize").not(".mywiz-initialized").addClass("mywiz-initialized");
  }
  
  $("div.mywiz-lay div.ez-box").live("click",function(){
    $("div.mywiz-box-sel").removeClass("mywiz-box-sel");
    $("div.mywiz-wr-sel").removeClass("mywiz-wr-sel");
    
    $(this).addClass("mywiz-box-sel");
    $(this).parents(".wiz-mod:first").addClass("mywiz-wr-sel");
    if($("#wiz-opacity").length>0){
      if($("img.wiz-selectable[wiz-border=1]").length>0){
        var v=$("img.wiz-selectable[wiz-border=1]").css("opacity");  
      }else{
        var v=$("div.mywiz-box-sel").css("opacity");     
      }
      
      v=v*100;
      $("#wiz-opacity").slider({value:v});
    }
  });
  
  $("img.wiz-modules:not(.wiz-modules-del)").live("click",function(){
    var block=$(this).attr("html");
    var $mydom = $(block).html(block);
    var dest=$mydom.find(".wiz-mod:first");
    var oldHtml=$('<div>').append($("div.mywiz-box-sel").clone()).remove().html();
    var tmpHtml=$("div.mywiz-box-sel").attr("oldHtml");
    
    if(typeof tmpHtml !== 'undefined' && tmpHtml !== false){
      oldHtml=tmpHtml;  
    }
    
    dest.attr("oldHtml",oldHtml);
    
    if($("div.mywiz-box-sel").parent("div.mywiz-lay-resize-h").length>0 && dest.find("div.mywiz-lay-resize-h").length>0){
      $("div.mywiz-box-sel").parent("div.mywiz-lay-resize-h").replaceWith(dest.html());
    }else{
      $("div.mywiz-box-sel").replaceWith(dest);  
    }
    
    initWizard();
  });
  
  $("img.wiz-modules-del").live("click",function(){
    if($("div.mywiz-wr-sel").length==0) return;
    
    var tmpHtml=$("div.mywiz-wr-sel").attr("oldHtml");
    var tmpParent=$("div.mywiz-wr-sel").attr("oldParent");
    
    if(typeof tmpParent !== 'undefined' && tmpParent !== false){
      $("div.mywiz-wr-sel").replaceWith($(tmpParent).html(tmpHtml).removeClass("mywiz-initialized"));    
    }else{
      $("div.mywiz-wr-sel").replaceWith(tmpHtml);
    }
    
    initWizard();
  });
  
  $("div.rsStrutture[rsAjax!=-1] a.rsStrutture-a").live("click",function(){
    var id=$(this).attr("rsId");
    var struttura=$(this).attr("rsName"); 
    var obj=$(this).parents("div.rsStrutture[rsName='"+struttura+"']");
    var editable=obj.attr("rsEditable");
    var useAjax=obj.attr("rsAjax");
    var rsNMRel=obj.attr("rsNMRel");
    
    $.ajax({
      type: "POST",                       
      url: "wizard-action.php",
      data: "wizGetFotogallery=1&wizId="+id, 
      success: wizAddContent,
      error: function(XMLHttpRequest, textStatus, errorThrown) {
               alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
             }
    });
  });
  
  function wizAddContent(msg) {
    if(msg!="-1") {
      $("div.wiz-right-col-2").html(msg);
    }else{
      $("div.wiz-right-col-2").html("");  
    }
  }
  
  $(".wiz-gallery").live("click",function(){
    var id=$(this).attr("wizId");
    var wizRootId=$(this).attr("wizRootId");
    var image=$(this).attr("src");
    var origImage=$(this).attr("origImage");
    var AbsoluteOrigImage=$(this).attr("AbsoluteOrigImage");
    
    //TESTI
    if(wizRootId==44){
      $("div.mywiz-box-sel div.wiz-text").remove();
      var html=$("div.mywiz-box-sel").html();
      if(html==".1." || html==".2." || html==".3." || html==".4." || html==".1." || html==".header." || html==".body." || html==".footer." || html==".content.") {
        $("div.mywiz-box-sel").html("");  
      }
      
      $("div.mywiz-box-sel").append("<div class='wiz-text' style=\"background-image:url('"+origImage+"');\" bg='"+AbsoluteOrigImage+"' ></div>");
      $("div.mywiz-box-sel div.wiz-text").width($("div.mywiz-box-sel").width()-20);
      $("div.mywiz-box-sel div.wiz-text").height($("div.mywiz-box-sel").height()-20);    
    }
    
    //SFONDI
    if(wizRootId==43) {
      var r = true;
      
      if($("div.mywiz-wr-sel").length>0) {
        jConfirm("Applicare lo sfondo solo al box arancione selezionato?","wizard",function(r){
          wizApplyBg(r,origImage,null);  
        });
      }else{
        wizApplyBg(r,origImage,null);  
      }    
    }
    
    //FOTO
    if(wizRootId==45) {
      var html=$("div.mywiz-box-sel").html();
      if(html==".1." || html==".2." || html==".3." || html==".4." || html==".1." || html==".header." || html==".body." || html==".footer." || html==".content.") {
        $("div.mywiz-box-sel").html("");  
      }
      
      $("div.mywiz-box-sel").append($("<img src="+$(this).attr("src")+" class='wiz-foto wiz-selectable' wiz-border='0' style='position:absolute;z-index:999;padding:10px;' />").draggable({containment: 'parent', opacity: 0.35, stop: null})); 
      refreshBgColorIMG($("div.mywiz-box-sel"));   
    }
  });
  
  function wizApplyBgIMG (msg){
    var obj;
    var arr=explode("S#_#Q",msg);
    var r=arr[0];
    var imgbg=arr[1];
    
    if(r=="1"){
      obj=$("div.mywiz-box-sel");
    }else{
      obj=$("div.mywiz-wr-sel");
    } 
    
    obj.css("background-image","url('"+imgbg+"')");
  }
  
  function refreshBgColorIMG(obj){
    var o;
    obj.find("img.wiz-foto").each(function(){
        o=$(this).parents("div[wiz-bgcolor]:first");
        if(o.length>0){
          $(this).css("background-color",o.attr("wiz-bgcolor"));   
        }else{
          $(this).removeCss("background-color");  
        }
    }); 
  }
  
  function wizApplyBg(r,origImage,color){
    var obj;
    var html=$("div.mywiz-box-sel").html();
    
    if(html==".1." || html==".2." || html==".3." || html==".4." || html==".1." || html==".header." || html==".body." || html==".footer." || html==".content.") {
      $("div.mywiz-box-sel").html("");  
    }
    
    if(r){
      obj=$("div.mywiz-box-sel");
    }else{
      obj=$("div.mywiz-wr-sel");
    } 
    
    var bgimage=obj.attr("wiz-bgimage");
    var bgcolor=obj.attr("wiz-bgcolor");
    
    if(bgimage!=null && color!=null) {
      var rr;
      obj.css("background-color",color);
      obj.attr("wiz-bgcolor",color);
      refreshBgColorIMG(obj);
      
      if(r==true) rr="1";else rr="-1";
      $.ajax({
        type: "POST",                       
        url: "wizard-action.php",
        data: "wizBGColor=1&wizImg="+bgimage+"&wizColor="+color+"&r="+rr, 
        success: wizApplyBgIMG,
        error: function(XMLHttpRequest, textStatus, errorThrown) {
                 alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
               }
      });  
    }else if(bgcolor!=null && origImage!=null) {
      var rr;
      obj.attr("wiz-bgimage",origImage);
      if(r==true) rr="1";else rr="-1";
      $.ajax({
        type: "POST",                       
        url: "wizard-action.php",
        data: "wizBGColor=1&wizImg="+origImage+"&wizColor="+bgcolor+"&r="+rr, 
        success: wizApplyBgIMG,
        error: function(XMLHttpRequest, textStatus, errorThrown) {
                 alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
               }
      });  
    }else{
      obj.removeCss('background-image');
      obj.removeCss('background-color');
      obj.removeAttr('wiz-bgimage');
      obj.removeAttr('wiz-bgcolor');
      
      if(origImage!=null) {
        obj.css("background-image","url('"+origImage+"')");
        obj.attr("wiz-bgimage",origImage);
      } 
      
      if(color!=null) {
        obj.css("background-color",color);
        obj.attr("wiz-bgcolor",color);
      }
      
      refreshBgColorIMG(obj);  
    }   
  }
  
  $("table.wiz-testi-colore div.wiz-colors-del").live("click",function(){
    if($("img.wiz-selectable[wiz-border=1]").length>0){
      $("img.wiz-selectable[wiz-border=1]").remove();  
    }else{
      $("div.mywiz-box-sel div.wiz-text").remove();    
    } 
  });
  
  $("table.wiz-testi-colore div.wiz-colors").live("click",function(){
    var nam = $(this).attr("title");
    var newColor = $(this).css("background-color");
    var id = $(this).attr("wizId");
    var affini=$(this).hasClass("wiz-colors-affini");
    
    if($("img.wiz-selectable[wiz-border=1]").length>0){
      var img = $("img.wiz-selectable[wiz-border=1]").attr("AbsoluteOrigImage");
    }else{
      if($("div.mywiz-box-sel").length==0) return;
      var img = $("div.mywiz-box-sel div.wiz-text").attr("bg");
    }
    
    $.ajax({
      type: "POST",                       
      url: "wizard-action.php",
      data: "wizTextColor=1&wizImg="+img+"&wizName="+nam+"&wizNewColor="+newColor+"&wizId="+id+"&wizAffini="+affini, 
      success: wizTextColor,
      error: function(XMLHttpRequest, textStatus, errorThrown) {
               alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
             }
    });  
  });
  
  $("div.wiz-right-col-2 div.wiz-colors").live("click",function(){
    var col = $(this).attr("wizCol");
    var r = true;
    var msg="";
    
    if($(this).hasClass("wiz-colors-del")){
      msg="Rimuovere lo sfondo solo dal box arancione selezionato?";
    }else{
      msg="Applicare lo sfondo solo al box arancione selezionato?";  
    }
    
    if($("div.mywiz-wr-sel").length>0) {
      jConfirm(msg,"wizard",function(r){
        wizApplyBg(r,null,col);  
      });
    }else{
      wizApplyBg(r,null,col);  
    }   
  });
  
  $("div.wiz-shadows").live("click",function(){
    var st=$(this).parent("div[wizStyle]").attr("wizStyle");
    var r = true;
    var msg="";
    
    if($(this).hasClass("wiz-shadows-del")){
      msg="Rimuovere le ombre solo dal box arancione selezionato?";
    }else{
      msg="Applicare le ombre solo al box arancione selezionato?";  
    }
    
    if($("div.mywiz-wr-sel").length>0) {
      jConfirm(msg,"wizard",function(r){
        if(r){
          var st2=$("div.mywiz-box-sel").attr("style");
          st2=st2+";"+st;
          $("div.mywiz-box-sel").attr("style",st2);
          
          if(st==null) {
            $("div.mywiz-box-sel").css("box-shadow","");
          }else{
            $("div.mywiz-box-sel").attr("style",st2);  
          } 
        }else{
          var st2=$("div.mywiz-wr-sel").attr("style");
          st2=st2+";"+st;
          $("div.mywiz-wr-sel").attr("style",st2);
          
          if(st==null) {
            $("div.mywiz-wr-sel").css("box-shadow","");
          }else{
            $("div.mywiz-wr-sel").attr("style",st2);  
          }  
        }  
      });
    }else{
      var st2=$("div.mywiz-box-sel").attr("style");
      st2=st2+";"+st;
       
      if(st==null) {
        $("div.mywiz-box-sel").css("box-shadow","");
      }else{
        $("div.mywiz-box-sel").attr("style",st2);  
      } 
    } 
  });
  
  $("div.wiz-rounded").live("click",function(){
    var st=$(this).parent("div[wizStyle]").attr("wizStyle");
    var r = true;
    var msg="";
    
    if($(this).hasClass("wiz-rounded-del")){
      msg="Rimuovere l'arrotondamento solo dal box arancione selezionato?";
    }else{
      msg="Applicare l'arrotondamento solo al box arancione selezionato?";  
    }
    
    if($("div.mywiz-wr-sel").length>0) {
      jConfirm(msg,"wizard",function(r){
        if(r){
          if(st==null) {
            $("div.mywiz-box-sel").css("border-radius","");
          }else{
            var st2=$("div.mywiz-box-sel").attr("style");
            st2=st2+";"+st;
            $("div.mywiz-box-sel").attr("style",st2);
            $("div.mywiz-box-sel").attr("wizRounded",st);  
          } 
        }else{
          if(st==null) {
            $("div.mywiz-wr-sel").css("border-radius","");
          }else{
            var st2=$("div.mywiz-wr-sel").attr("style");
            st2=st2+";"+st;
            $("div.mywiz-wr-sel").attr("style",st2);
            $("div.mywiz-wr-sel").attr("wizRounded",st); 
          }  
        }  
      });
    }else{
      if(st==null) {
        $("div.mywiz-box-sel").css("border-radius","");
      }else{
        var st2=$("div.mywiz-box-sel").attr("style");
        st2=st2+";"+st;
        $("div.mywiz-box-sel").attr("style",st2);
        $("div.mywiz-box-sel").attr("wizRounded",st); 
      } 
    } 
  });
  
  $("img.wiz-brushes").live("click",function(){
    var origImage=$(this).attr("origImage");
    $("div.mywiz-box-sel").css("-moz-border-image","url("+origImage+") 1 1 1 1 stretch stretch");
  });
  
  $("div.wiz-brushes-del").live("click",function(){
    $("div.mywiz-box-sel").css("-moz-border-image","");
  });
  
  $("img.wiz-title").live("click",function(){
    var html=$("div.mywiz-box-sel").html();
    if(html==".1." || html==".2." || html==".3." || html==".4." || html==".1." || html==".header." || html==".body." || html==".footer." || html==".content.") {
      $("div.mywiz-box-sel").html("");  
    }
    
    $("div.mywiz-box-sel").append($("<img src='"+$(this).attr("src")+"' class='wiz-titoli wiz-selectable' wiz-border='0' style='position:absolute;z-index:999;' AbsoluteOrigImage='"+$(this).attr("AbsoluteOrigImage")+"' />").draggable({containment: 'parent', opacity: 0.35, stop: null}));  
  });
  
  $("img.wiz-selectable").live("click",function(){
    var sel=$(this).attr("wiz-border");
    
    $("img.wiz-selectable").css("border","0px");
    $("img.wiz-selectable").attr("wiz-border","0");
    
    if(sel=="0" || sel==""){
      $(this).css("border","2px green solid");
      $(this).attr("wiz-border","1");
    }
  });
  
  $("div.wiz-title-del").live("click",function(){
    $("img.wiz-selectable[wiz-border=1]").remove();
  });
  
  function wizTextColor(newImg){
    var arr=explode("S#_#Q",newImg);
    
    if($("img.wiz-selectable[wiz-border=1]").length>0){
      $("img.wiz-selectable[wiz-border=1]").attr("src",arr[0]);  
    }else{
      var arr=explode("S#_#Q",newImg);
      $("div.mywiz-box-sel div.wiz-text").css("background-image","url('"+arr[0]+"')");
      if(arr[1]!=-1){
        $("div.wiz-testi-colore-affini").html(arr[1]);
      } 
    }
  }
  
  $("img.wiz-webforms").live("click",function(){
    var html=$("div.mywiz-box-sel").html();
    if(html==".1." || html==".2." || html==".3." || html==".4." || html==".1." || html==".header." || html==".body." || html==".footer." || html==".content.") {
      $("div.mywiz-box-sel").html("");  
    }
    
    $("div.mywiz-box-sel").append($("<img src='"+$(this).attr("src")+"' class='wiz-titoli wiz-selectable' wiz-border='0' style='position:absolute;z-index:999;' AbsoluteOrigImage='"+$(this).attr("AbsoluteOrigImage")+"' />").draggable({containment: 'parent', opacity: 0.35, stop: null})); 
  });
  
  $("body").keypress(function(event) {
    if ( event.keyCode == 46 ) {
      if($("img.wiz-selectable[wiz-border=1]").length>0){
        $("img.wiz-selectable[wiz-border=1]").remove();  
      }else{
        $("div.mywiz-box-sel div.wiz-text").remove();    
      }     
    }
  });
  
  initWizard();
  
});
</script>