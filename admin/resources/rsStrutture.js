function rsStrutture_sendData(type, name, id, selected, newVal, editable,sortable,useAjax,rsNMRel, fun) {
  selected=rawurlencode(serialize(selected));
  $.ajax({
    type: "POST",                       
    url: "rsAction.php",
    data: "rsUPDStrutture=1&type="+type+"&name="+rawurlencode(name)+"&id="+id+"&selected="+selected+"&newval="+rawurlencode(newVal)+"&editable="+editable+"&useAjax="+useAjax+"&rsNMRel="+rsNMRel, 
    success: fun,
    error: function(XMLHttpRequest, textStatus, errorThrown) {
             alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
           }
  });  
}

function rsStrutture_init(){
  $("div.rsStrutture[rsEditable='1'][rsSortable='1'] ul").sortable({
    connectWith: "div.rsStrutture-connected ul",
    update: rsStrutture_sortUpdate
  });  
}

function rsStrutture_sortUpdate(event, ui) {
  
  var current=ui.item.attr("id");
  var ord=$(this).sortable("toArray");
  var struttura=ui.item.attr("rsName");
  var obj=ui.item.parents("div.rsStrutture[rsName='"+struttura+"']");
  var editable=obj.attr("rsEditable");
  var sortable=obj.attr("rsSortable");
  var useAjax=obj.attr("rsAjax");
  var rsNMRel=obj.attr("rsNMRel");
  
  var mLoad=$("body").find("div.rsLoading");
  if(mLoad.length==0) mLoad=$("body").append('<div class="rsLoading">Loading...</div>');
  mLoad.show();
  
  rsStrutture_sendData("rsstr-sort", struttura, current, current, rawurlencode(serialize(ord)),editable,sortable,useAjax,rsNMRel, function(msg){
    if(msg!="") {
      obj.replaceWith(msg);
      rsStrutture_init();
      $("body").find("div.rsLoading").hide();
    }      
  });      
}

function rsSortable_addLive() {
  $("div.rsStrutture[rsAjax!=-1] a.rsStrutture-a").live("click",function(){
    //if($(this).find("div.rsStrutture-a-text").hasClass("rsStrutture-a-deactivate")) return false; 
    
    var id=$(this).attr("rsId");
    var struttura=$(this).attr("rsName"); 
    var obj=$(this).parents("div.rsStrutture[rsName='"+struttura+"']");
    var editable=obj.attr("rsEditable");
    var sortable=obj.attr("rsSortable");
    var useAjax=obj.attr("rsAjax");
    var rsNMRel=obj.attr("rsNMRel");
    
    $("div.rsTable2-dialog-nm").remove();
    
    rsStrutture_sendData("rsstr-open",struttura,id,id,"",editable,sortable,useAjax,rsNMRel,function(msg){
      obj.parents(".rsTable2-insert-row-r").find("input.rsTable2-insert-strutture").val(id);
      obj.replaceWith(msg);
      rsStrutture_init();
      
    });
    
    return false;
  });
} 

$("div.rsStrutture ul li").live("mouseenter",function() {
  $(this).find(".rsStrutture-edit").fadeIn(100);
});

$("div.rsStrutture ul li").live("mouseleave",function() {
  var obj = $(this);
  $(this).find(".rsStrutture-edit").fadeOut(100,function(){
    obj.fadeOut(0);
    obj.fadeIn(0);    
  });
});

$("input.rsStrutture-add").live("click",function(){
  var struttura=$(this).attr("rsName");
  var obj=$(this).parents("div.rsStrutture[rsName='"+struttura+"']");
  var editable=obj.attr("rsEditable");
  var sortable=obj.attr("rsSortable");
  var useAjax=obj.attr("rsAjax");
  var rsNMRel=obj.attr("rsNMRel");
  
  obj.find("ul.rsStrutture-ul,input").hide();
  obj.prepend("<div class='rsStrutture-rsWait'>Loading...</div>");
  
  rsStrutture_sendData("rsstr-addcat",struttura,0,0,"",editable,sortable,useAjax,rsNMRel,function(msg){
    obj.replaceWith(msg);
    rsStrutture_init();
  });  
});

$("input.rsStrutture-a-text-mod-ok").live("click",function(){
  if(confirm("Salvare le modifiche?")) {
    var id=$(this).attr("rsId");
    var struttura=$(this).attr("rsName");
    var obj=$(this).parents("li[rsName='"+struttura+"']");
    var selected=new Array();
    var newVal=trim(obj.find("input.rsStrutture-a-text-mod").val());
    var editable=obj.attr("rsEditable");
    var sortable=obj.attr("rsSortable");
    var useAjax=obj.attr("rsAjax");
    var rsNMRel=obj.attr("rsNMRel");
    
    obj.find(".rsStrutture-a-selected").each(function(key){
      array_push(selected,$(this).attr("rsId"));  
    });
    
    
    rsStrutture_sendData("rsstr-modcat",struttura,id,selected,newVal,editable,sortable,useAjax,rsNMRel,function(msg){
      obj.parents("div.rsStrutture[rsName='"+struttura+"']").replaceWith(msg);
      rsStrutture_init();
    });
  } 
});

$("input.rsStrutture-a-text-mod-canc").live("click",function(){
  if(confirm("Sicuro di voler annullare le modifiche?")) {
    var id=$(this).attr("rsId");
    var struttura=$(this).attr("rsName");
    var obj=$(this).parents("li[rsName='"+struttura+"']");
    var selected=new Array();
    var newVal=trim(obj.find("input.rsStrutture-a-text-mod-old").val());
    var editable=obj.attr("rsEditable");
    var sortable=obj.attr("rsSortable");
    var useAjax=obj.attr("rsAjax");
    var rsNMRel=obj.attr("rsNMRel");
    
    obj.find(".rsStrutture-a-selected").each(function(key){
      array_push(selected,$(this).attr("rsId"));  
    });
    
    rsStrutture_sendData("rsstr-modcat",struttura,id,selected,newVal,editable,sortable,useAjax,rsNMRel,function(msg){
      obj.parents("div.rsStrutture[rsName='"+struttura+"']").replaceWith(msg);
      rsStrutture_init();
    });
  } 
});

$("input.rsStrutture-edit-sub-table").live("click",function(e){
  var id=$(this).attr("rsId");
  var struttura=$(this).attr("rsName");
  var obj=$(this).parents("li[rsName='"+struttura+"']");
  
  $.ajax({
    type: "POST",                       
    url: "rsAction.php",
    data: "rsTable2Action=1&getRelNM=1&table=categorie&id="+id, 
    success: function(msg){
              $("div.rsTable2-dialog-nm").remove();
              
              var relativeX = (e.pageX);
              var relativeY = (e.pageY);
              var $mydom = $("<div class='rsTable2-dialog-nm-container'></div>").appendTo("body").html(msg);
              $mydom.css("left",relativeX+"px");
              $mydom.css("top",relativeY+"px"); 
              //obj.find("div.rsStrutture-li-content-r").append(msg);  
             },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
             alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
           }
  });  
});

$("input.rsStrutture-edit-mod").live("click",function(){
  var id=$(this).attr("rsId");
  var struttura=$(this).attr("rsName");
  var obj=$(this).parents("li[rsName='"+struttura+"']");
  var editable=obj.attr("rsEditable");
  
  var oldHtml=trim(obj.find("div.rsStrutture-a-text[rsId='"+id+"']").html());
  obj.html("<input class='rsStrutture-a-text-mod-old' type='hidden' value='' rsId='"+id+"' rsName='"+struttura+"' /><input class='rsStrutture-a-text-mod' type='text' value='' /><input class='rsStrutture-a-text-mod-ok' type='button' value='' rsId='"+id+"' rsName='"+struttura+"' title='Salva' /><input class='rsStrutture-a-text-mod-canc' type='button' value='' rsId='"+id+"' rsName='"+struttura+"' title='Annulla' />");
  obj.find("input.rsStrutture-a-text-mod-old").attr("value",oldHtml);
  obj.find("input.rsStrutture-a-text-mod").attr("value",oldHtml);
});

$("input.rsStrutture-edit-delete").live("click",function(){
  if(confirm("Eliminare la voce e tutte le sue sottovoci?")) {
    $(this).parents("div.rsStrutture").find("ul.rsStrutture-ul,input").hide();
    $(this).parents("div.rsStrutture").prepend("<div class='rsStrutture-rsWait'>Loading...</div>");
    var id=$(this).attr("rsId");
    var struttura=$(this).attr("rsName");
    var obj=$(this).parents("li[rsName='"+struttura+"']");
    var mobj=$(this).parents("div.rsStrutture[rsName='"+struttura+"']");
    var selected=new Array();
    var editable=obj.attr("rsEditable");
    var sortable=obj.attr("rsSortable");
    var useAjax=obj.attr("rsAjax");
    var rsNMRel=obj.attr("rsNMRel");
    
    obj.find(".rsStrutture-a-selected").each(function(key){
      array_push(selected,$(this).attr("rsId"));
    });
    
    rsStrutture_sendData("rsstr-delete",struttura,id,selected,"",editable,sortable,useAjax,rsNMRel,function(msg){
      mobj.replaceWith(msg);
      rsStrutture_init();
      $(this).parents("div.rsStrutture").find("div.rsStrutture-rsWait").remove();
    });
  }  
});

$("input.rsStrutture-edit-addcat").live("click",function(){
  var id=$(this).attr("rsId");
  var struttura=$(this).attr("rsName");
  var obj=$(this).parents("li[rsName='"+struttura+"']");
  var mobj=$(this).parents("div.rsStrutture[rsName='"+struttura+"']");
  var selected=new Array();
  var editable=obj.attr("rsEditable");
  var sortable=obj.attr("rsSortable");
  var useAjax=obj.attr("rsAjax");
  var rsNMRel=obj.attr("rsNMRel");
  
  obj.find(".rsStrutture-a-selected").each(function(key){
    array_push(selected,$(this).attr("rsId"));  
  });
  
  rsStrutture_sendData("rsstr-addcat",struttura,id,selected,"",editable,sortable,useAjax,rsNMRel,function(msg){
    mobj.replaceWith(msg);
    rsStrutture_init();
  });  
});

$("input.rsStrutture-edit-pubbl").live("click",function(){
  var id=$(this).attr("rsId");
  var struttura=$(this).attr("rsName");
  var obj=$(this).parents("li[rsName='"+struttura+"']");
  var mobj=$(this).parents("div.rsStrutture[rsName='"+struttura+"']");
  var selected=new Array();
  var editable=obj.attr("rsEditable");
  var sortable=obj.attr("rsSortable");
  var useAjax=obj.attr("rsAjax");
  var rsNMRel=obj.attr("rsNMRel");
  
  obj.find(".rsStrutture-a-selected").each(function(key){
    array_push(selected,$(this).attr("rsId"));  
  });
  
  var mLoad=$("body").find("div.rsLoading");
  if(mLoad.length==0) $("body").append('<div class="rsLoading">Loading...</div>');
  mLoad=$("body").find("div.rsLoading");
  mLoad.show();
  rsStrutture_sendData("rsstr-notpubbl",struttura,id,selected,"",editable,sortable,useAjax,rsNMRel,function(msg){
    mobj.replaceWith(msg);
    rsStrutture_init();
    mLoad.hide();
  });    
});

$("input.rsStrutture-edit-not-pubbl").live("click",function(){
  var id=$(this).attr("rsId");
  var struttura=$(this).attr("rsName");
  var obj=$(this).parents("li[rsName='"+struttura+"']");
  var mobj=$(this).parents("div.rsStrutture[rsName='"+struttura+"']");
  var selected=new Array();
  var editable=obj.attr("rsEditable");
  var sortable=obj.attr("rsSortable");
  var useAjax=obj.attr("rsAjax");
  var rsNMRel=obj.attr("rsNMRel");
  
  obj.find(".rsStrutture-a-selected").each(function(key){
    array_push(selected,$(this).attr("rsId"));  
  });
  
  var mLoad=$("body").find("div.rsLoading");
  if(mLoad.length==0) $("body").append('<div class="rsLoading">Loading...</div>');
  mLoad=$("body").find("div.rsLoading");
  mLoad.show();
  rsStrutture_sendData("rsstr-pubbl",struttura,id,selected,"",editable,sortable,useAjax,rsNMRel,function(msg){
    mobj.replaceWith(msg);
    rsStrutture_init();
    mLoad.hide();
  });    
});

$(document).ready(function(){
  rsStrutture_init();
  rsSortable_addLive();
});