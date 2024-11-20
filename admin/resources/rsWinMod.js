function initModWin() {
  var k=0;
  $("[rel=rsWinMod]").each(function(key){
    if($(this).find("div.rsWinMod-container").length==0) { 
      k++;
      initModWinEx($(this));
    }
  }); 
  
  if(k>0) {
    rsModWin_addLive();
    initLytebox();
  }
}

function initModWinEx(obj) {
  var html=obj.html();
  var table=obj.attr("rsTable");
  var field=obj.attr("rsField");
  var id=obj.attr("rsId");
  var tableid=obj.parents("div.rsTable2-container").attr("rsTableId");
  var tblParent=obj.attr("rsTableParent");
  var parentId=obj.attr("rsTableParentID");
  obj.html("<div class='rsWinMod-container' rsTable='"+table+"' rsTableParent='"+tblParent+"' rsTableParentID='"+parentId+"' rsTableId='"+tableid+"' rsField='"+field+"' rsId='"+id+"'>"+html+"</div>");
}

function rsModWin_sendData(type, table, tblParent, parentId, field, id, newval, fun) {
  $.ajax({
    type: "POST",                       
    url: "rsAction.php",
    data: "rsWinMod=1&type="+type+"&table="+table+"&tableparent="+tblParent+"&parentid="+parentId+"&field="+field+"&id="+id+"&newval="+rawurlencode(newval), 
    success: fun,
    error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
           }
  });  
}

function rsModWin_addLive() {
  $("div.rsWinMod-container[rsWinModSel!=1]").bind("mouseenter",function(){
    var mypos=$(this).position();
    
    $("div.rsWinMod-ico").remove();
    $(this).append($("<div class='rsWinMod-ico'></div>").css({'margin-left': mypos.left+$(this).parents('.fht_table_body').scrollLeft(), 'margin-top': mypos.top+$(this).parents('.fht_table_body').scrollTop()}));
  }).mouseleave(function(){
    var myobj=$(this).find("div.rsWinMod-ico");
    setTimeout(function(){myobj.remove()},1500);    
  });
}

function rsTinySave(options) {
  var tinyid=options['id'];
  $("#"+tinyid).parents("form").trigger("submit"); 
}

function rsValidate(arr,fun) {
  if(!is_array(arr)) {
    arr=explode(";",arr);  
  }
  
  fun=strtolower(fun);
  for(var i=0;i<count(arr);i++) {
    pfield=strtolower(arr[i]); 
    
    str=fun;
    if(right(pfield,strlen(str))==str) {
      return pfield;    
    }
  
    if(left(pfield,strlen(str))==str) {
      return pfield; 
    }
    
    if(left(str,1)=="_" && right(str,1)=="_") {
      if(strpos(pfield, str)!==false) {
        return pfield; 
      }  
    }         
  }
  
  return false;
}

$("div.rsWinMod-ico").live("click",function(){
  var obj=$(this).parents("div.rsWinMod-container");
  var table=obj.attr("rsTable");
  var field=obj.attr("rsField");
  var id=obj.attr("rsId");
  var oldHtml=obj.html();
  var control="<input type='text' class='rsWinMod-text rsWinMod-control' rsType='text' value='' />";
  var prefix="_editor";
  var unid=getFilenameUnique();
  var tinyid="rsWinMod-textarea-editor_"+unid;
  var formid="rsWinMod-form-tiny_"+unid;
  var uploaderid="rsWinMod-uploader_"+unid;
  var tableid=obj.attr("rsTableId");
  var objtbl=RETrsTable2ByID(tableid);
  var tblParent=obj.attr("rsTableParent");
  var parentId=obj.attr("rsTableParentID");
  
  obj.attr("rsWinModSel","1");
  
  rsModWin_sendData("getField",table,tblParent,parentId,field,id,"",function(msg){
    var tarr=explode("#rsSEP#",msg);
    var rsPower=explode(";",tarr[0]);
    msg=tarr[1];
    
    if(rsValidate(rsPower,"_editor")!==false) {    
      control="<form id='"+formid+"' action='rsAction.php' method='post'><input type='hidden' name='rsWinMod' value='1' /><input type='hidden' name='type' value='setField' /><input type='hidden' name='table' value='"+table+"' /><input type='hidden' name='tblParent' value='"+tblParent+"' /><input type='hidden' name='field' value='"+field+"' /><input type='hidden' name='id' value='"+id+"' /><textarea id='"+tinyid+"' class='rsWinMod-textarea rsWinMod-control rsWinMod-textarea-editor' name='newval' rsType='textarea'>"+msg+"</textarea></form>";
      
      var winOptions = {
        'str':  control,
        'title': 'Modifica di '+strtoupper(table)+', colonna '+strtoupper(field),
        'tag': table+'#SEP#'+field+'#SEP#'+id+'#SEP#'+tableid,
        'resizable': 0,
        'onClose':
          function(obj){
            var tag=explode("#SEP#",obj.attr("rsTag"));
            var o=$("div.rsWinMod-container[rsTable="+tag[0]+"][rsField="+tag[1]+"][rsId="+tag[2]+"][rsTableId="+tag[3]+"]");
            o.attr("rsWinModSel","0");
            rsModWin_addLive();  
          },
        'onLoad': 
          function(obj){
            var tag=explode("#SEP#",obj.attr("rsTag"));
            $("div.rsWinMod-container[rsTable="+tag[0]+"][rsField="+tag[1]+"][rsId="+tag[2]+"][rsTableId="+tag[3]+"]").find("div.rsWinMod-ico").remove();
            rsModWin_addLive();
            $("#"+formid).ajaxForm(formOptions);
            
            tinyMCE.init({
          		// General options
          		mode : "textareas",
          		theme : "advanced",
          		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
              editor_selector : "textEditor",
              height:497,
              width:901,
              convert_urls : false,
              relative_urls : false,
              remove_script_host : false,
              paste_auto_cleanup_on_paste : false,
              //save_enablewhendirty: true,
              save_onsavecallback : "rsTinySave",
              //save_oncancelcallback: "rsTinyCancel",
          		
              // Theme options
          		language : "it",
              theme_advanced_buttons1 : "save,newdocument,|,fontselect,fontsizeselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist",
          		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,forecolor,backcolor,|,undo,redo,|,link,unlink,code,|,insertdate,inserttime,preview",
          		theme_advanced_buttons3 : "tablecontrols,|,sub,sup,|styleprops,charmap,hr,image,media",
          		theme_advanced_buttons4 : "",
          		theme_advanced_toolbar_location : "top",
          		theme_advanced_toolbar_align : "left",
          		theme_advanced_statusbar_location : "bottom",
          		theme_advanced_resizing : false,
              file_browser_callback : "fileBrowserCallBack",
          		
              // Example content CSS (should be your site CSS)
          		content_css : "css/content.css",
          
          		// Drop lists for link/image/media/template dialogs
          		template_external_list_url : "lists/template_list.js",
          		external_link_list_url : "lists/link_list.js",
          		external_image_list_url : "lists/image_list.js",
          		media_external_list_url : "lists/media_list.js",
          
          		// Replace values for the template plugin
          		template_replace_values : {
          			username : "Some User",
          			staffid : "991234"
          		}
          	});
            tinyMCE.execCommand('mceAddControl', false, tinyid);
          }
      };
      
      var formOptions = {
        beforeSubmit: 
          function(formData, jqForm, options){
            return(confirm("Sovrascrivere i cambiamenti?"));
          },
        success:  
          function(msg, statusText, xhr, $form){
            var table = xhr.children("input[name=table]").attr("value"); 
            var tblParent=xhr.children("input[name=tblParent]").attr("value");
            var field = xhr.children("input[name=field]").attr("value");
            var id = xhr.children("input[name=id]").attr("value");
            var obj=$("div.rsWinMod-container[rsTable="+table+"][rsField="+field+"][rsId="+id+"]"); 
            
            if(objtbl!=false) {
              objtbl.TroncaTesto(table,field,id,tblParent);  
            }else {
              obj.html(msg);
            }
            initLytebox();    
          } 
      };
      
      var win=new rsWindows(winOptions);
      win.open();
    }else if(rsValidate(rsPower,"_file")!==false) { 
      var alen=$("<div></div>").append(oldHtml).find("a").length; 
      var adis="";
      if(alen==0) {
        adis="style='display:none;'";  
      }
      obj.find("div.rsWinMod-ico").remove();
      obj.prepend("<div class='ez-wr rsWinMod-file-container'><div class='ez-fl  ez-negmx ez-33 rsWinMod-del-file'><div class='ez-box'><input type='button' value='' class='rsWinMod-del-file' title='elimina' "+adis+" /></div></div><div class='ez-fl ez-negmr ez-33 rsWinMod-uploader'><div class='ez-box'><div id='"+uploaderid+"'>&nbsp;</div></div></div><div class='ez-last ez-oh rsWinMod-file-cancel'><div class='ez-box'><input type='button' value='' class='rsWinMod-file-cancel' title='cancel' /></div></div></div>");
      obj.find("input.rsWinMod-old").val(oldHtml);
      
      /*
      obj.find('#'+uploaderid).uploadify({
        'uploader': getPathBackofficeResources+'uploadify.swf',
        'script': getPathBackofficeResources+'uploadify.php',
        'folder': getPathResourcesDynamic+'uploaded',
        'cancelImg': getPathBackofficeResources+'cancel.png',
        'buttonImg': getPathBackofficeResources+'file-explorer.png',
        'wmode': 'transparent',
        'width':16,
        'height':16,
        'onComplete': 
          function(event,queueID,fileObj,response,data,uploaderid){
            var uploaderid = event['target'].getAttribute("id");
            var obj=$("#"+uploaderid).parents("div.rsWinMod-container");
            var table=obj.attr("rsTable");
            var field=obj.attr("rsField");
            var id=obj.attr("rsId");
            var tblParent=obj.attr("rsTableParent");
            var parentId=obj.attr("rsTableParentID");
            
            rsModWin_sendData("setFile",table,tblParent,parentId,field,id,fileObj.filePath,function(msg){
              if(objtbl!=false) objtbl.updateFile(table,field,id,tblParent);
              initLytebox();  
            });
          },
        'auto': true 
      });
      */
      
      $('#'+uploaderid).uploadifive({
        'auto'         : true,
        'uploadScript' : getPathBackofficeResources+'uploadify.php', 
        'dnd' : false,
        'itemTemplate' : '<div class="uploadifive-queue-item" ><span class="filename"></span><span class="fileinfo" ></span></div>',
        'buttonText' : '',
        'buttonClass'  : 'rsWinMod-uploader',
        'width':16,
        'height':16,
        'formData' : {'folder' : getPathResourcesDynamic+'uploaded'},
        'multi': false,
        'onUploadComplete': 
          function(file, data){
            var filePath=getPathResourcesDynamic+'uploaded'+data;
            var obj=$("#"+uploaderid).parents("div.rsWinMod-container");
            var table=obj.attr("rsTable");
            var field=obj.attr("rsField");
            var id=obj.attr("rsId");
            var tblParent=obj.attr("rsTableParent");
            var parentId=obj.attr("rsTableParentID");
            
            rsModWin_sendData("setFile",table,tblParent,parentId,field,id,filePath,function(msg){
              if(objtbl!=false) objtbl.updateFile(table,field,id,tblParent);
              initLytebox();  
            });
          }
      });
            
    }else if(rsValidate(rsPower,"_str_")!==false){
      obj.html("<input type='hidden' value='' class='rsWinMod-control' rsType='_str_' /><input type='button' value='' class='rsWinMod-save rsWinMod-save-strutture' title='save' />"+msg);
      obj.find("input.rsWinMod-old").val(oldHtml);
      rsStrutture_init();
      //if(objtbl!=false) objtbl.FixHeader($("div.rsTable2-tab-"+table+"[rsTableId="+tableid+"]"));
    }else if(rsValidate(rsPower,"id_")!==false){
      obj.html("<input type='hidden' value='' class='rsWinMod-old' /><input type='hidden' value='' class='rsWinMod-control' rsType='id_' /><input type='button' value='' class='rsWinMod-save' title='save' /><input type='button' value='' class='rsWinMod-cancel' title='cancel' />"+msg);
      obj.find("input.rsWinMod-old").val(oldHtml);
      //if(objtbl!=false) objtbl.FixHeader($("div.rsTable2-tab-"+table+"[rsTableId="+tableid+"]"));
    }else if(rsValidate(rsPower,"_suggest#")!==false){
      obj.html("<input type='hidden' value='' class='rsWinMod-old' /><input type='hidden' value='' class='rsWinMod-control' rsType='_suggest#' /><input type='button' value='' class='rsWinMod-save' title='save' /><input type='button' value='' class='rsWinMod-cancel' title='cancel' />"+msg);
      obj.find("input.rsWinMod-old").val(oldHtml);
      //if(objtbl!=false) objtbl.FixHeader($("div.rsTable2-tab-"+table+"[rsTableId="+tableid+"]"));
    }else if(rsValidate(rsPower,"_date")!==false){
      obj.html("<input type='hidden' value='' class='rsWinMod-old' /><input type='hidden' value='' class='rsWinMod-control' rsType='_date' /><input type='button' value='' class='rsWinMod-save' title='save' /><input type='button' value='' class='rsWinMod-cancel' title='cancel' />"+msg);
      obj.find("input.rsWinMod-old").val(oldHtml);
      //if(objtbl!=false) objtbl.FixHeader($("div.rsTable2-tab-"+table+"[rsTableId=tableid]"));
    }else if(rsValidate(rsPower,"_boolean")!==false){
      obj.html("<input type='hidden' value='' class='rsWinMod-old' /><input type='hidden' value='' class='rsWinMod-control' rsType='_boolean' /><input type='button' value='' class='rsWinMod-save' title='save' /><input type='button' value='' class='rsWinMod-cancel' title='cancel' />"+msg);
      obj.find("input.rsWinMod-old").val(oldHtml);
      //if(objtbl!=false) objtbl.FixHeader($("div.rsTable2-tab-"+table+"[rsTableId="+tableid+"]"));
    }else{
      if(strlen(msg)>60) {
        control="<textarea class='rsWinMod-textarea rsWinMod-control' rsType='textarea'></textarea>";
      }
      
      obj.html("<input type='hidden' value='' class='rsWinMod-old' /><input type='button' value='' class='rsWinMod-save' title='save' /><input type='button' value='' class='rsWinMod-cancel' title='cancel' />"+control);
      obj.find("input.rsWinMod-old").val(oldHtml);
      obj.find("input.rsWinMod-text").val(msg);
      obj.find("textarea.rsWinMod-textarea").html(msg);
    }
    
    $("div.rsWinMod-container").unbind("mouseenter");
    rsModWin_addLive();  
  });
});

$("input.rsWinMod-del-file").live("click",function(){
  if(confirm("Sicuro di voler eliminare il file?")) {
    var obj=$(this).parents("div.rsWinMod-container");
    var table=obj.attr("rsTable");
    var field=obj.attr("rsField");
    var id=obj.attr("rsId");
    var tableid=obj.attr("rsTableId");
    var objtbl=RETrsTable2ByID(tableid);
    var tblParent=obj.attr("rsTableParent");
    var parentId=obj.attr("rsTableParentID");
    
    rsModWin_sendData("setFile",table,tblParent,parentId,field,id,0,function(msg){
      if(objtbl!=false) objtbl.updateFile(table,field,id,tblParent);  
    });
  }else{
    return true;
  }    
});

$("input.rsWinMod-cancel").live("click",function(){
  if(confirm("Sicuro di voler annullare le modifiche?")) {
    var obj=$(this).parents("div.rsWinMod-container");
    var table=obj.attr("rsTable");
    var field=obj.attr("rsField");
    var id=obj.attr("rsId");
    var oldHtml=obj.find("input.rsWinMod-old").val();
    var tableid=obj.attr("rsTableId");
    var objtbl=RETrsTable2ByID(tableid);
    
    obj.html(oldHtml);
    initLytebox();
    obj.attr("rsWinModSel","0");
    rsModWin_addLive();
    if(objtbl!=false) {
    //objtbl.FixHeader($("div.rsTable2-tab-"+table+"[rsTableId="+tableid+"]"));
    }
  }else{
    return true;
  }    
});

$("input.rsWinMod-file-cancel").live("click",function(){
  var obj=$(this).parents("div.rsWinMod-container");
  var table=obj.attr("rsTable");
  var field=obj.attr("rsField");
  var id=obj.attr("rsId");
  obj.find("div.rsWinMod-file-container").remove();
  obj.attr("rsWinModSel","0");
  rsModWin_addLive();
});

$("input.rsWinMod-save").live("click",function(){
  var obj=$(this).parents("div.rsWinMod-container");
  var control=obj.find(".rsWinMod-control");
  var type=control.attr("rsType");
  var conf="Salvare le modifiche?";
  var tableid=obj.attr("rsTableId");
  var objtbl=RETrsTable2ByID(tableid);
  var tblParent=obj.attr("rsTableParent");
  var parentId=obj.attr("rsTableParentID");
  
  if(type=="_str_") conf="Sicuri di voler terminare le modifiche?"; 
  
  if(confirm(conf)) {
    var table=obj.attr("rsTable");
    var field=obj.attr("rsField");
    var id=obj.attr("rsId"); 
    
    var newHtml="";
    
    if(type=="text") newHtml=control.val();
    if(type=="textarea") newHtml=control.val(); 
    if(type=="_str_") newHtml=obj.find(".rsStrutture-a-selected").attr("rsId");
    if(type=="id_") {
      newHtml=obj.find("select option:selected").val();
      if(newHtml<0) newHtml=0;
    }
    
    if(type=="_date") {
      var newYY=obj.find("select.rsTable2-formdata-aa option:selected").val();
      var newMM=obj.find("select.rsTable2-formdata-mm option:selected").val();
      var newGG=obj.find("select.rsTable2-formdata-gg option:selected").val();
      newHtml=newYY+"-"+newMM+"-"+newGG;
      if(newHtml=="" || newHtml==0) newHtml="0000-00-00";
    }
    if(type=="_boolean") {
      newHtml=obj.find("input.rstbl2-input-checkbox:checked").val();
      if(newHtml!=1) newHtml=0;
    }
    if(type=="_suggest#") {
      newHtml=obj.find("input.rsTable2-suggest-input").val();
    }
    
    rsModWin_sendData("setField",table,tblParent,parentId,field,id,newHtml,function(msg){
      if(type=="_str_" || type=="id_") {
        if(objtbl!=false) objtbl.UpdateStrutture(table,field,id,tblParent);  
      }else if(type=="_boolean"){
        if(objtbl!=false) objtbl.UpdateBoolean(table,field,id,tblParent);  
      }else{
        if(type=="_date") msg=dataIta(msg);
        if(msg=="00-00-0000") msg="";
        
        msg=objtbl.modTranslate(msg);
        var tmsg=$("<div></div>").append(msg).find(".rsWinMod-not-unique");
        if(tmsg.length>0) {
          alert(tmsg.attr("title"));
          return; 
        }
        
        obj.html(msg);
        
        obj.attr("rsWinModSel","0");
        rsModWin_addLive();
        
        if(objtbl!=false) {objtbl.refreshAll();}   
      }
      initLytebox();
      obj.attr("rsWinModSel","0");
      rsModWin_addLive();    
    });   
  }else{
    return false;
  }    
});

$(document).ready(function(){
  initModWin(); 
});