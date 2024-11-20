function rsChat_get(type,id,fromid,msg,fun) {
  $.ajax({
     type: "POST",                       
     url: getPathRoot+"rsAction.php",
     data: "rsChat_type="+type+"&rsChatID="+id+"&rsChatID2="+fromid+"&rsChatMSG="+rawurlencode(msg), 
     success: fun,
     error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
            }
  });
}

function rsChat_open(id,sound) {
  if($("div.rsChatPriv-container[id="+id+"]").length>0) return false;
  
  rsChat_get("printChat",id,0,"",function(msg){
    var $mydom = $("<div></div>").appendTo("body").html(msg);
    $mydom.find("div.rsChatPriv-container").draggable({addClasses: true, containment: 'document', cursor: 'move', stop: rsChat_stopDrag, handle: $mydom.find("div.rsChatPeople-title").disableSelection() });
    var mbodyc=$mydom.find("div.rsChatPriv-body-content");
    
    var y=mbodyc.height();
    mbodyc.parents("div.rsChatPriv-body").scrollTop(y);
    
    $("input.rsChatPriv-input").focus();
    if(sound==true) {
      if($("div.rsChat-container").length>0) {
        $.fn.soundPlay({url: getPathBackofficeResources+'alt_ring2.mid', playerId: 'embed_player', command: 'play'});
      }
    }
  });  
}

function rsChat_stopDrag(event, ui) {
  rsChat_get("rsChatDrag",ui['position']['left'],ui['position']['top'],$(this).attr("id"),function(msg){
    return;
  });    
}

function rsChat_sendMsg(pobj) {
  var tmsg=trim(pobj.val());
  var fromid=pobj.attr("fromid");
  var toid=pobj.attr("toid");
  var obj=pobj.parents("div.rsChatPriv-container");
  var inp=pobj;
  var mbody=obj.find("div.rsChatPriv-body");
  
  if(tmsg=="") return false;
  inp.attr('disabled', 'disabled');
  rsChat_get("rsChatSend",fromid,toid,tmsg,function(msg){
    var mbodyc=mbody.find("div.rsChatPriv-body-content");
    mbodyc.append(msg);
    inp.val("");
    
    var y=mbodyc.height();
    mbody.scrollTop(y); 
    inp.removeAttr('disabled');   
  });
}

$("div.rsChat-container").live("click",function(){
  if($("div.rsChatPeople-container").length==0) {
    rsChat_get("printChatPeople",0,0,"",function(msg){
      var $mydom = $("<div></div>").appendTo("body").html(msg);
    });
  }else{
    $("div.rsChatPeople-container").remove();  
  }  
});

$("div.rsChatPriv-send").live("click",function(){
  rsChat_sendMsg($("input.rsChatPriv-input"));  
});

$("div.rsChatPeople-container div.rsChatPeople-row").live("click",function(){
  var id=$(this).attr("id");
  rsChat_open(id,false);  
});

$("div.rsChatPriv-container .rsChatPeople-close").live("click",function(){
  if(confirm("Sicuro di voler chiudere la chat?")) {
    var obj=$(this);
    var id=obj.attr("id");
    
    rsChat_get("rsChat_close",id,0,"",function(msg){
      if(msg=="1") obj.parents("div.rsChatPriv-container").remove();
    });
  }
});

$("input.rsChatPriv-input").live("keypress",function(e){
  if(e.keyCode==13){
    rsChat_sendMsg($(this));    
  }  
});

$(".rsOpenChat[offline='0']").live("click",function(){
  rsChat_open(-1,false);
  return false;
});

$(document).ready(function(){
  $("div.rsChatPriv-container").each(function(){
    var obj=$(this);                   
    var mbody=obj.find("div.rsChatPriv-body");
    var mbodyc=mbody.find("div.rsChatPriv-body-content");
    var y=mbodyc.height();
    mbody.scrollTop(y);
    obj.draggable({addClasses: true, containment: 'document', cursor: 'move', stop: rsChat_stopDrag, handle: obj.find("div.rsChatPeople-title").disableSelection() });  
    $("input.rsChatPriv-input").focus();
  }); 
  
  $.timer(3000, function() {
    rsChat_get("rsChat_getNewMess",1,0,"",function(msg){
      var ret1=explode("#_rsCHAT_SEP_#",msg);
      for(var i=0;i<count(ret1);i++){
        var ret=explode("#_rsCHAT_#",ret1[i]);
        if(count(ret)==2) {
          var mbodyc=$("div.rsChatPriv-body-content[id="+ret[0]+"]");
          if(mbodyc.length>0) {
            mbodyc.append(ret[1]);
            var y=mbodyc.height();
            mbodyc.parents("div.rsChatPriv-body").scrollTop(y);  
          }else{
            rsChat_open(ret[0],true);  
          }
        }
      }
      
      if($("div.rsChat-container").length>0) {
        var oldv=$("div.rsChat-count").text();
        oldv=oldv.replace("Chat (","");
        oldv=oldv.replace(")","");
        oldv=$.trim(oldv);
        
        var pchat=$("div.rsChatPeople-container").length;
        if(pchat>0) {
          var sct=$("div.rsChatPeople-container-scroll").scrollTop();
          $("div.rsChatPeople-container").remove();
        }
        var $mydom = $("<div></div>").hide().appendTo("body").html(ret1[0]);
        var newv=$mydom.find("div.rsChatPeople-row").length;
        $("div.rsChat-count").html("Chat ("+newv+")");
        
        if(newv>oldv) $.fn.soundPlay({url: getPathBackofficeResources+'dream-harp-01.mp3', playerId: 'embed_player', command: 'play'});
        
        if(pchat>0) {
          $mydom.show();
          $mydom.find("div.rsChatPeople-container-scroll").scrollTop(sct);
        }else{
          $mydom.remove();
        }
      }   
    });    
  });  
});
