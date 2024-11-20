function rsPaginazioneInitAutoSlide(obj) {
  var name=obj.attr("pagname");
  var mydest=obj.attr("dest");
  var autoslide=obj.attr("autoslide"); 
  
  if(autoslide>0) {
    var tmptimer=$.timer(autoslide, function() {
      if($(mydest+":visible").length>0 ) {
        if($("div.paginazione[name="+name+"] input.page.dopo").length>0) {
          rsPaginazionePageClick($("div.paginazione[name="+name+"] input.page.dopo"));
        }else if($("div.paginazione[name="+name+"] input.page[value='1i']").length>0){
          rsPaginazionePageClick($("div.paginazione[name="+name+"] input.page[value='1i']"));      
        }
      }
    });
    
    $(mydest).live("mouseover",function(){
      tmptimer.stop();
    });
    
    $(mydest).live("mouseout",function(){
      tmptimer.reset(autoslide);
    }); 
  } 
}

function rsPaginazioneGetPage(obj,fun) {
  var url=obj.attr("url");
  var getstr=obj.attr("getstr");
  var name=obj.attr("name");
  
  if(getstr!="") getstr="&"+getstr;
  $.ajax({
     type: "POST",                       
     url: url,
     data: "rsPagin=1&"+name+"=1"+getstr, 
     success: fun,
     error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
            }
  });  
}

function onPagChangeEx(fun,obj){
  obj.parents("div.paginazione").find("input").show();
  obj.parents("div.paginazione .rsPag-wait").remove();
  
  if(function_exists(fun)) eval(fun+"(obj);");
  return;    
}

function rsPaginazionePageClick(obj) {
  var name=obj.attr("pagname");
  var mydest=obj.attr("dest");
  var jsevents=explode(";",obj.attr("jsevents"));
  var autoslide=obj.attr("autoslide");
  var jsevents=obj.attr("jsevents");
  var onPagChange=obj.attr("onPagChange");
  var animate=obj.attr("animate");
  
  if(jsevents!="") jsevents=explode("#_#",jsevents);
  
  rsPaginazioneGetPage(obj,function(msg){
	var $mydom = $("<div></div>").hide().appendTo("body").html(msg);
    var dest=$mydom.find(mydest).html();
    var paginazione=$mydom.find("div.paginazione[name="+name+"]").html();
    
    msg="";
    $mydom.remove();
    
    if(paginazione!=null) $("div.paginazione[name="+name+"]").html(paginazione);
    $(mydest).fadeOut("slow",function(){
      if(dest!=null) {$(mydest).html(dest);}
      $(mydest).fadeIn("slow",function(){
        if($("#lbOverlay:visible").length==0){
          initLytebox();
        }
         
        for(var jj=0;jj<count(jsevents);jj++) {
          var func=substr(jsevents[jj], 0, strpos(jsevents[jj], "("));
          if(function_exists(func)) eval(jsevents[jj]); 
        }
        
        obj=$("div.paginazione[name="+name+"]");
        onPagChangeEx(onPagChange,obj);
        
        if(autoslide==0 && animate=="") {
          $(".scroll-pane").jScrollPane({showArrows:true});
          $('html, body').animate({scrollTop: $(mydest).offset().top}, 1000);
        } 
      });  
    });
  });
}

$(".paginazione input.page").live("click",function(){
  var obj=$(this);
  obj.parents("div.paginazione").find("input").hide();
  obj.parents("div.paginazione").prepend("<div class='rsPag-wait'>Loading...</div>");
  
  var name=obj.attr("pagname");
  var mydest=obj.attr("dest");
  var autoslide=obj.attr("autoslide");

  if(mydest==undefined) return true;

  rsPaginazionePageClick(obj);
  return false;         
});