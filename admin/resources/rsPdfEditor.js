function genClick(id) {
  $("#"+id).click();
}

function rsPdfEditor(options) {
  var g_rsPdfEditor=this;
  var g_obj;
  var g_options;
  var g_sel="";
  var box_id=0;
  var id_jp=0;
  
  var g_defaults = {
	 'id': getFilenameUnique(),
   'container': 'body',
   'table': ""	
	};
  
  g_options = $.extend(g_defaults, options);
  
  this._print=function() {
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsPdfEditAction=1&rsInitEditor=1&options="+rawurlencode(serialize(g_options)), 
       success: g_rsPdfEditor.rsInitPdfEditor,
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
     });  
  }
  
  this.rsInitPdfEditor=function(msg){
    var $mydom = $("<div></div>").appendTo(g_options.container).html(msg);
    g_rsPdfEditor.addEvents();
    initLytebox();
  }
  
  this.saveTemplate=function(name,html,fun) {
    $.ajax({
      type: "POST",                       
      url: "rsAction.php",
      data: "rsPdfEditAction=1&rsSaveTemplate=1&html="+rawurlencode(html)+"&name="+rawurlencode(name), 
      success: fun,
      error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
             }
    });  
  }
  
  this.getTemplate=function(name,fun) {
    $.ajax({
      type: "POST",                       
      url: "rsAction.php",
      data: "rsPdfEditAction=1&rsGetTemplate=1&name="+rawurlencode(name), 
      success: fun,
      error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
             }
    });
  }
  
  this.delTemplate=function(name,fun) {
    $.ajax({
      type: "POST",                       
      url: "rsAction.php",
      data: "rsPdfEditAction=1&rsDelTemplate=1&name="+rawurlencode(name), 
      success: fun,
      error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
             }
    });
  }
  
  this.existTemplate=function(name,fun) {
    $.ajax({
      type: "POST",                       
      url: "rsAction.php",
      data: "rsPdfEditAction=1&rsExistTemplate=1&name="+rawurlencode(name), 
      success: fun,
      error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
             }
    });  
  }
  
  this.dragstop=function(event,ui) {
    var pos=$(this).position();
    $(this).attr("rsX",pos.left);
    $(this).attr("rsY",pos.top);
  }
  
  this.resizestop=function(event,ui) {
    var h=$(this).height();
    var w=$(this).width();
    
    $(this).attr("rsW",w);
    $(this).attr("rsH",h);
  }
  
  this.refreshTemplates=function() {
    $.ajax({
      type: "POST",                       
      url: "rsAction.php",
      data: "rsPdfEditAction=1&rsRefreshTemplate=1", 
      success: function(msg){
                 $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_template").replaceWith(trim(msg));
               },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
             }
    });  
  }
  
  this.addEvents=function() {
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon").officebar();
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon").draggable({axis:'y'});
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon div.off_button").click(
      function(){
        return false;  
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .rsPdfSaveDoc").click(function(){
      var obj=$(this).parents("div.rsPdfEditor-container").find("#lettera");
      var template=obj.attr("rsTemplate");
      var html=obj.parents("div.rsPdfLettera-container").html();
      var newdoc=false;
      
      if(template=="") { 
        newdoc=true;
        template = prompt('Inserire un nome per il template', '');
        if(template==null) return false;
      }
      
      g_rsPdfEditor.existTemplate(template,function(msg){
        var c=false;
        if(msg!="-1" && newdoc) {
          if(confirm(msg)) c=true;     
        }else{
          c=true;  
        }
        
        if(c) {
          g_rsPdfEditor.saveTemplate(template,html,function(msg){
            obj.attr("rsTemplate",template);
            g_rsPdfEditor.refreshTemplates();
            //alert(msg);  
          });  
        }  
      });   
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .rsPdfSaveDocAs").click(function(){
      var obj=$(this).parents("div.rsPdfEditor-container").find("#lettera");
      var html=obj.parents("div.rsPdfLettera-container").html();
      
      template = prompt('Inserire un nome per il template', '');  
      if(template==null) return false;
      
      g_rsPdfEditor.existTemplate(template,function(msg){
        var c=false;
        if(msg!="-1") {
          if(confirm(msg)) c=true;     
        }else{
          c=true;  
        }
        
        if(c) {
          g_rsPdfEditor.saveTemplate(template,html,function(msg){
            obj.attr("rsTemplate",template);
            g_rsPdfEditor.refreshTemplates();
            //alert(msg);  
          });  
        }  
      });   
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon #rsDeleteTemplate").click(function(){
      var sel=$(this).parents("div.rsPdfEditor-container").find("select[name=off_template]");
      var template=sel.find("option:selected").html();
      if(template=="" || template==null) return false;
      if(confirm("Il template corrente verrà eliminato. Continuare?")){
        box_id=0;
        var t=$(this);
        var obj=$(this).parents("div.rsPdfEditor-container").find("#lettera");
        
        g_rsPdfEditor.delTemplate(template,function(msg){
          if(msg=="1") {
            g_rsPdfEditor.refreshTemplates();
          }
        });
      }
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon #rsLoadTemplate").click(function(){
      var sel=$(this).parents("div.rsPdfEditor-container").find("select[name=off_template]");
      var template=sel.find("option:selected").html();
      if(template=="" || template==null) return false;
      if(confirm("Il documento corrente verrà eliminato. Continuare?")){
        box_id=0;
        var t=$(this);
        var obj=$(this).parents("div.rsPdfEditor-container").find("#lettera");
        
        g_rsPdfEditor.getTemplate(template,function(html){
          var $mydom = $("<div></div>").hide().appendTo("body").html(html);
          var dest=trim($mydom.html());
          obj.replaceWith(dest);
          obj=t.parents("div.rsPdfEditor-container").find("#lettera");
          html="";
          $mydom.remove();
          
          obj.attr("rsTemplate",template);
          t.parents("div.rsPdfEditor-container").find("span.rsPdf-CurrTemplate").html(template);
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] div.lettera_box").each(function(key){
            var x=$(this).attr("rsX");
            var y=$(this).attr("rsY");
            var h=$(this).attr("rsH");
            var w=$(this).attr("rsW");
            var tid=str_replace("box","",$(this).attr("id"));
            if(tid>box_id)box_id=tid;
            
            $(this).draggable({containment: 'parent', opacity: 0.35, stop: g_rsPdfEditor.dragstop});
            $(this).resizable({stop: g_rsPdfEditor.resizestop});
          });  
        });   
      }  
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .rsPdf-selColor").jPicker({
      window: { 
        position: {x: 'screenCenter',y: 0},
        expandable: true
      },
      color: {
        active: new $.jPicker.Color({hex: '#000000'})
      },
      liveUpdate: true
    },
    function(color, context) {
      var rgb = color.val('rgb');
      if(g_sel!="") {
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).attr("rsColor",rgb.r+","+rgb.g+","+rgb.b);
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("color","rgb("+rgb.r+","+rgb.g+","+rgb.b+")");
      } else {
        //$("#testoLettera").html("");  
      }
    });
    
    id_jp=($.jPicker.List.length-1);
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .rsPdfNewDoc").click(function(){
      if(confirm("Tutti i dati non salvati andranno persi. Continuare?")){
        var obj=$(this).parents("div.rsPdfEditor-container").find("#lettera");
        obj.html("");
        obj.attr("style","");
        obj.attr("rsTemplate",""); 
        $(this).parents("div.rsPdfEditor-container").find("span.rsPdf-CurrTemplate").html("Template");
        box_id=0;    
      }  
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .elimina_testo").click(
      function(){
        if(g_sel!="") {
          if(confirm("Sicuro di voler procedere?")) $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).remove();
          g_sel="";
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .grassetto").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .corsivo").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .sottolineato").removeClass("selected");    
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #14px").attr("selected","selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_FontFamily option:contains('Times New Roman')").attr("selected","selected");
        }  
      }
    );
  
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_FontSize").change(
      function(){
        if(g_sel!="") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-size", $(this).val()+"px"  );
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } 
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_FontFamily").change(function(){
      if(g_sel!="") {
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-family", $(this).val());
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
      } 
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .grassetto").click(function(){
      if(g_sel!="") {
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
        if($("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-weight")=="400" || $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-weight")=="normal") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-weight", "bold");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .grassetto").addClass("selected");
        } else {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-weight", "normal");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .grassetto").removeClass("selected");  
        }
        
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
      } 
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .corsivo").click(
      function(){
        if(g_sel!="") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          if($("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-style")=="normal") {
            $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-style", "italic"  );
            $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .corsivo").addClass("selected");
          } else {
            $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("font-style", "normal"  );
            $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .corsivo").removeClass("selected");  
          }
  
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } 
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .sottolineato").click(
      function(){
        if(g_sel!="") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          if($("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("text-decoration")=="none") {
            $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("text-decoration", "underline"  );
            $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .sottolineato").addClass("selected");
          } else {
            $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("text-decoration", "none"  );
            $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .sottolineato").removeClass("selected");  
          }
          
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } 
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").click(
      function(){
        if(g_sel!="") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("text-align", "center"  );
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").addClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").removeClass("selected"); 
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } 
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").click(
      function(){
        if(g_sel!="") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("text-align", "left"  );
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").addClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").removeClass("selected"); 
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } 
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").click(
      function(){
        if(g_sel!="") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("text-align", "right"  );
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").addClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").removeClass("selected"); 
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } 
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").click(
      function(){
        if(g_sel!="") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).css("text-align", "justify"  );
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").addClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").removeClass("selected");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").removeClass("selected"); 
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } 
      }
    );
  
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #c_mod").click(function(){
        if(g_sel!="") {
          if($("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).hasClass("lettera-box-img")) return false;
          
          var msg=$("div.rsPdfEditor-container[rsId="+g_options.id+"] #testoLettera").val();
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).html(msg);
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } else {
          //$("#testoLettera").html("");  
        }  
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #c_mod_db").click(
      function(){
        if(g_sel!="") {
          if($("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).hasClass("lettera-box-img")) return false;
          
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable('destroy');
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).html( "#`"+$("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_tables option:selected").val()+"`."+$("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_fields option:selected").val()+"#" );
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #"+g_sel).resizable({stop: g_rsPdfEditor.resizestop});
        } else {
          //$("#testoLettera").html("");  
        }  
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .stampaPdf").click(function(){
      var pos_i="";
      var x_i="";
      var y_i="";
      var pos="";
      var x="";
      var y="";
      var w="";
      var h="";
      var tmp="";
      
      pos_i = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #lettera").position();
      x_i = pos_i.left+2;
      y_i = pos_i.top+2;
      
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] #stampaLettera input[name='html[]']").remove();
      for(var i=0;i<(box_id+1);i++) {
        if($("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).html()!=null) {
          pos = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).position();
          x = pos.left-x_i;
          y = pos.top-y_i;      
          w=$("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).width();
          h=$("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).height();

          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).resizable('destroy');
          
          var grassetto = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).css("font-weight"); 
          var corsivo = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).css("font-style");
          var textAlign = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).css("text-align");
          var underline = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).css("text-decoration");
          var fontSize = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).css("font-size");
          var fontFamily = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).css("font-family");
          fontFamily=str_replace("'","",fontFamily);
          var rgb = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).attr("rsColor");
          
          var grafica = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #lettera").css("background-image");
          var img = $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i+" img").attr("src"); 
          
          tmp = x+"#_#"+y+"#_#"+w+"#_#"+h+"#_#"+grassetto+"#_#";
          tmp += corsivo+"#_#"+textAlign+"#_#"+underline+"#_#"+fontSize+"#_#"+fontFamily+"#_#";
          tmp += rgb+"#_#"+grafica+"#_#"+img+"#_#"+$("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).html();
          
          //alert(tmp); 
          tmp=rawurlencode(tmp);
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #stampaLettera").append("<input type='hidden' name='html[]' value='"+tmp+"' />");
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+i).resizable({stop: g_rsPdfEditor.resizestop});
        }  
      }
      
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] #stampaLettera").append("<input type='hidden' name='rsPdfEditAction' value='1' />");
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] #stampaLettera").append("<input type='hidden' name='rsTable' value='"+g_options.table+"' />");
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] #stampaLettera").append("<input type='hidden' name='PRINT-PDF-DO' value='1' />");
      
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] #stampaLettera").submit();
      $('[name=html[]]').each(function(){
          $(this).remove();
      })    
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_UploadedFile"+g_options.id).click(function(){
        box_id++;
        var retFileID=$(this).val();
        retFileID = retFileID.split(";");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #lettera").append("<div id='box"+box_id+"' class='lettera_box lettera-box-img'><img style='width:100%;height:auto;max-width:910px;' src='"+getPathResourcesDynamic+retFileID[0]+"' /></div>");
        
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+box_id).draggable({containment: 'parent', opacity: 0.35, stop: g_rsPdfEditor.dragstop});
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+box_id).resizable({stop: g_rsPdfEditor.resizestop});
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon div.nuova_immagine").click(function(){
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] .off_UploadImage").trigger("click");
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .immagine_sfondo").click(
      function(){
        if($("div.rsPdfEditor-container[rsId="+g_options.id+"] #lettera").css("background-image")=="none") {
          $("div.rsPdfEditor-container[rsId="+g_options.id+"] .off_UploadImageSfondo").click();       
        } else {
          if(confirm('Rimuovere la grafica corrente?')) $("div.rsPdfEditor-container[rsId="+g_options.id+"] #lettera").css("background-image","none");
        }
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_UploadedFileSfondo"+g_options.id).click(
      function(){
        var retFileID=$(this).val();
        retFileID = retFileID.split(";");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #lettera").css("background-image","url("+getPathResourcesDynamic+retFileID[0]+")");
        //$("div.rsPdfEditor-container[rsId="+g_options.id+"] #lettera").append("<img style='width:100%;height:100%;z-index:-9999' src='"+getPathResourcesDynamic+retFileID[0]+"' />");
      }
    );
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_tables").change(function(){
      $.ajax({
        type: "POST",                       
        url: "rsAction.php",
        data: "rsPdfEditAction=1&getFields=1&id="+$(this).find("option:selected").attr("id"), 
        success: function(msg){
                   $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_fields").replaceWith(msg);
                 },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
      });
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] div.lettera_box").live("click",function(){
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] .lettera_box").css("border","0px");
      if(g_sel!=$(this).attr("id")) {
        var nval="";
        var rgb=$(this).attr("rsColor");
        
        if(rgb!=undefined){
          rgb=explode(",",rgb);
          $.jPicker.List[id_jp].color.active.val('rgb', { r: rgb['0'], g: rgb['1'], b: rgb['2'] });
        }
        
        $(this).css("border","2px green solid");
        g_sel=$(this).attr("id");
        
        
        $(this).resizable('destroy');
        if(!$(this).hasClass("lettera-box-img")) nval=$(this).html();
        
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testoLettera").val(nval);
        
        var grassetto = $(this).css("font-weight"); 
        var corsivo = $(this).css("font-style");
        var textAlign = $(this).css("text-align");
        var underline = $(this).css("text-decoration");
        var fontSize = $(this).css("font-size");
        var fontFamily= $(this).css("font-family");
        fontFamily=str_replace("'","",fontFamily);
        
        if(grassetto=="700") $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .grassetto").addClass("selected"); else $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .grassetto").removeClass("selected");
        if(corsivo=="italic") $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .corsivo").addClass("selected"); else $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .corsivo").removeClass("selected");
        if(textAlign=="left") $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").addClass("selected"); else $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").removeClass("selected");
        if(textAlign=="center") $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").addClass("selected"); else $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").removeClass("selected");
        if(textAlign=="right") $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").addClass("selected"); else $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").removeClass("selected");
        if(textAlign=="justify") $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").addClass("selected"); else $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").removeClass("selected");
        if(underline=="underline") $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .sottolineato").addClass("selected"); else $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .sottolineato").removeClass("selected");    
        $("#"+fontSize).attr("selected","selected");
        $("#off_FontFamily option:contains('"+fontFamily+"')").attr("selected","selected");
        $(this).resizable({stop: g_rsPdfEditor.resizestop});
      } else {
        g_sel="";
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testoLettera").html("");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .grassetto").removeClass("selected");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .corsivo").removeClass("selected");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_sinistra").removeClass("selected");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .centrato").removeClass("selected");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .all_destra").removeClass("selected");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .giustificato").removeClass("selected");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon .sottolineato").removeClass("selected");    
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #14px").attr("selected","selected");
        $("div.rsPdfEditor-container[rsId="+g_options.id+"] #off_FontFamily option:contains('Times New Roman')").attr("selected","selected");  
      }
    });
    
    $("div.rsPdfEditor-container[rsId="+g_options.id+"] #testRibbon div.nuovo_testo").click(function(){
      box_id++;
      var rgb=$.jPicker.List[id_jp].color.active.val("rgb");
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] #lettera").append("<div id='box"+box_id+"' rsColor='"+rgb.r+","+rgb.g+","+rgb.b+"' class='lettera_box' style='color:rgb("+rgb.r+","+rgb.g+","+rgb.b+");'>Box"+box_id+"</div>");
      
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+box_id+"").draggable({containment: 'parent' , opacity: 0.35, stop: g_rsPdfEditor.dragstop});
      $("div.rsPdfEditor-container[rsId="+g_options.id+"] #box"+box_id+"").resizable({stop: g_rsPdfEditor.resizestop});
    });
  }
}