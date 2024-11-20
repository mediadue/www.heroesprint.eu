var rsTable2Arr=new Array();

function RETrsTable2ByID(id) {
  for(var i=0;i<count(rsTable2Arr);i++){
    var tmptbl=rsTable2Arr[i];
    if(tmptbl.getId()==id) return tmptbl; 
  } 
  
  return false; 
}

function rsTable2PageChange(obj) {
  var win=obj.parents("div.rs-windows-container");
  var id=win.attr("rsTableId");
  var tblname=obj.parents("div.rs-windows-container").attr("rsTable");
  var g_rsTable2=RETrsTable2ByID(id);
  
  if(g_rsTable2!=false) {
    var g_options=g_rsTable2.getOptions();
    g_obj=$("div.rsTable2-tab-"+g_options.table+"[rsTableId="+g_options.id+"]");
    
    
    g_rsTable2.Sort($("div.rsTable2-tab-"+g_options.table+" table[rsTableId="+g_options.id+"]"));
    g_rsTable2.Paginazione(g_options.table);
    initModWin();
    g_rsTable2.FixHeader(g_obj);
    
    var thObj=g_obj.find("div.fht_table_body");
    
    var winobj=win.parents("div.rs-windows");
    var winW=winobj.find("div.rs-windows-frame-bottom").width();
    var winH=winobj.find("div.rs-windows-frame-right").height();
    
    thObj.css("height",(winH-90)+"px");
    thObj.css("width",(winW-238)+"px");
  }  
}

function rsTable2(options) {
  var g_rsTable2=this;
  var g_obj;
  var g_options;
  
  var g_defaults = {
	'str':  "",
    'container': "body",
    'table': "",
    'tableParent': "",
    'tableParentId': "",
    'insertId': "",
    'where': "",
    'order': "",
    'colFilter': "",
    'title': "",
    'tag': "",
    'showAll': false,
    'id': getFilenameUnique(),
    'sort': Array(),
    'permDel': "",
    'token': "",
    'colLimit': 13
	};
  
  g_optionsSer = options; 
  g_options = $.extend(g_defaults, g_optionsSer); 
  
  this.getOptions=function() {
    return g_options;      
  }
  
  this.showAll=function(v) {
    g_options.showAll=v
    g_rsTable2.remove();
    g_rsTable2._print();    
  }

  this.delrow=function(table,id) {
    $.ajax({
     type: "POST",                       
     url: "rsAction.php",
     data: "rsTable2Action=1&table="+rawurlencode(table)+"&parent="+rawurlencode(g_options.tableParent)+"&parentid="+g_options.tableParentId+"&delrow="+id+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
     success: 
      function(msg){
        if(msg=="1") {
          $("div.rs-windows-container."+g_options.table+" tr.rsTable2-tr[rsTable2ID="+id+"]").fadeOut("slow",function(){
            $(this).remove();
            
          });  
        }else{
          alert(msg);
        }
      },
     error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
            }
    });
  }
  
  this.delSelectedRow=function(table) {
    if(confirm("Procedere con l'eliminazione?")) {
      var mLoad=$(g_options.container).find("div.rsLoading");
      if(mLoad.length==0) $(g_options.container).html('<div class="rsLoading">Loading...</div>');
      mLoad.show();
      $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&table="+rawurlencode(table)+"&delSelectedRow=1&parent="+rawurlencode(g_options.tableParent)+"&parentid="+g_options.tableParentId+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: 
        function(msg){
          if(msg=="1") {
            g_rsTable2.refreshAll();
            $(g_options.container).find("div.rsLoading").hide();  
          }else{
            alert(msg);
            $(g_options.container).find("div.rsLoading").hide();
          }
        },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
      });
    }
  }
  
  this.getId=function() {
    return g_options.id;
  }
  
  this._print=function(fun) {
    var mLoad=$(g_options.container).find("div.rsLoading");
    if(mLoad.length==0) $(g_options.container).html('<div class="rsLoading">Loading...</div>');
    mLoad.show();
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&rsInitTable=1&options="+rawurlencode(serialize(g_options))+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: function(msg){
                  g_rsTable2.rsInitTable(msg);
                  if(typeof(fun)!="undefined") eval(fun());  
                },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
     });  
  }
  
  this.remove=function() {
    $("div.rs-windows-container."+g_options.table+"[rsTableId="+g_options.id+"]").remove();
    $("div.rsTable2-insert-container."+g_options.table+"[rsTableId="+g_options.id+"]").remove();  
  }
  
  this.refreshAll=function(fun) {
    var id;
    var tmptbl;
    
    $("div.rs-windows-container[rsTable="+g_options.table+"]").each(function(key){
      id=$(this).attr("rsTableId");
      tmptbl=RETrsTable2ByID(id);
      tmptbl.remove();
      tmptbl._print(fun);  
    });
    
    $("div.rsTable2-insert-container[rsTable="+g_options.table+"]").each(function(key){
      id=$(this).attr("rsTableId");
      tmptbl=RETrsTable2ByID(id);
      tmptbl.remove();
      tmptbl._insert(fun);  
    });
  }
  
  this._insert=function(fun) {
    var mLoad=$(g_options.container).find("div.rsLoading2");
    if(mLoad.length==0) $(g_options.container).append('<div class="rsLoading2">Loading...</div>');
    mLoad.show();
    
    var mLoad=$(g_options.container).find("div.rsLoading");
    if(mLoad.length==0) $(g_options.container).append('<div class="rsLoading">Loading...</div>');
    mLoad.show();

    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&tabInsert=1&options="+rawurlencode(serialize(g_options))+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: function(msg){
                  g_rsTable2.initInsertForm(msg);
                  if(typeof(fun)!="undefined") eval(fun());
                  $(g_options.container).find("div.rsLoading2").remove();
                },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
    });             
  }
  
  this.initInsertForm=function(msg) {
    var $mydom = $("<div></div>").appendTo(g_options.container).html(msg);
    var obj=$("div.rsTable2-insert-container[rsTableId="+g_options.id+"]");
    var tinyid;
    var uploaderid;
    var rsmultple;
    var table=g_options.table;
    var tableId=g_options.id;
    var colfilter=g_options.colFilter;
    var tableParent=g_options.tableParent;
    var tableParentId=g_options.tableParentId;
    var rowid=g_options.insertId;
    var up_multi=true; 
    
    if(rowid>0) up_multi=false;
    
    var options = {
      beforeSubmit:
        function(){
          var mLoad=$(g_options.container).find("div.rsLoading");
          if(mLoad.length==0) $(g_options.container).append('<div class="rsLoading">Loading...</div>');
          mLoad.show();
        },
      success:  
        function(msg, statusText, xhr, $form){
          var tabName = xhr.children("input[name=tabName]").attr("value");
          msg=g_rsTable2.modTranslate(msg);
          
          $("div.rsTable2-insert-container[rsTable="+tabName+"]").find(".rsTable2-insert-submit-result").html(msg);
          
          if(xhr.find(".rsTable2-ok").length>0) {
            xhr.find(".rsTable2-insert-row").find(".rsTable2-insert-row-l").find("label").removeClass("rsTable2-insert-obligatory");
            if(!(rowid>0)){
              xhr.find("input.rsTable2-insert-uploader").val(0);
              xhr.find("div.rsTable2-insert-uploader-preview").html("");
              xhr.resetForm();
            }
            
            if(table=="magazzino_articoli" && tableParent=="categorie") {
              rowid=$("div.rsTable2-insert-container[rsTable="+tabName+"] .rsTable2-ok").find("a[rel=rsOpenWindow]").attr("rsRowId");
              g_options.insertId=rowid;  
            }
            
            g_rsTable2.refreshAll(function(){
              $("div.rsTable2-insert-container[rsTable="+tabName+"]").find(".rsTable2-insert-submit-result").html(msg);
              if(!(rowid>0)) $("div.rsTable2-insert-container[rsTable="+tabName+"] .rsTable2-ok").find("a[rel=rsOpenWindow]").show();
            
              setTimeout(function(){
                if(!(rowid>0)) {
                  $("div.rsTable2-insert-container[rsTable="+tabName+"] .rsTable2-ok").find(".rsTable2-timeout").hide("slow");
                }else{
                  $("div.rsTable2-insert-container[rsTable="+tabName+"] .rsTable2-ok").hide("slow");  
                }
              },3000);  
            }); 
          }
          
          if(xhr.find(".rsTable2-obligatory").length>0) {
            var field=xhr.find(".rsTable2-obligatory").attr("rsField");
            xhr.find(".rsTable2-insert-row").find("label").removeClass("rsTable2-insert-obligatory");
            xhr.find(".rsTable2-insert-row[rsField="+field+"]").find("label").addClass("rsTable2-insert-obligatory");
            xhr.find(".rsTable2-insert-row[rsField="+field+"] a:first").focus();
            xhr.find(".rsTable2-insert-row[rsField="+field+"] input:first").focus();
            xhr.find(".rsTable2-insert-row[rsField="+field+"] select:first").focus();
            xhr.find(".rsTable2-insert-row[rsField="+field+"] textarea:first").focus();
          }
          $(g_options.container).find("div.rsLoading").hide();
        } 
    };
    
    if(obj.find("textarea.rsTable2-insert-editor").length>0) {
     
      tinyMCE.init({
    		// General options
    		mode : "textareas",
    		theme : "advanced",
    		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
        editor_selector : "textEditor",
        height:250,
        width:470,
        convert_urls : false,
        relative_urls : false,
        remove_script_host : false,
        paste_auto_cleanup_on_paste : false,
        //save_enablewhendirty: true,
        //save_onsavecallback : "rsTinySave",
        //save_oncancelcallback: "rsTinyCancel",
    		
        // Theme options
    		language : "it",
        theme_advanced_buttons1 : "newdocument,|,fontselect,fontsizeselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist",
    		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,forecolor,backcolor,|,undo,redo,|,link,unlink,code,|,insertdate,inserttime,preview",
    		theme_advanced_buttons3 : "tablecontrols,|,sub,sup,|styleprops,charmap,hr,image,media",
    		theme_advanced_buttons4 : "",
    		theme_advanced_toolbar_location : "top",
    		theme_advanced_toolbar_align : "left",
    		//theme_advanced_statusbar_location : "bottom",
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
    	
    	
      obj.find("textarea.rsTable2-insert-editor").each(function(key){
        tinyid=$(this).attr("id");
        tinyMCE.execCommand('mceAddControl', false, tinyid);
      });
    }
    
    obj.find("div.rsTable2-insert-uploader").each(function(key){
      
      var uploaderid=$(this).attr("id");
      var rsmultple=$(this).attr("rsMultiple");
      var up_multi = true;
      if(rsmultple=="0" || g_defaults.insertId!="") up_multi = false; 
      
      $('#'+uploaderid).uploadifive({
        'auto'         : true,
        'uploadScript' : getPathBackofficeResources+'uploadify.php', 
        'dnd' : false,
        'itemTemplate' : '<div class="uploadifive-queue-item" ><span class="filename"></span><span class="fileinfo" ></span></div>',
        'buttonText' : '',
        'buttonClass'  : 'rsTable2-insert-uploader',
        'width':16,
        'height':16,
        'multi': up_multi,
        'formData' : {'folder' : getPathResourcesDynamic+'uploaded'},
        'onUploadComplete': 
          function(file, data){
            $("#uploadifive-"+uploaderid+"-queue").find("span").hide();
            
            var obj=$("#"+uploaderid).parents("div.rsTable2-insert-uploader-container");
            var filePath=getPathResourcesDynamic+'uploaded'+data;
            $.ajax({
             type: "POST",                       
             url: "rsAction.php",
             data: "rsTable2Action=1&newfile="+rawurlencode(filePath)+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
             success: 
              function(msg){
                var tarr=explode("#SEP#",msg);
                var v=obj.find(".rsTable2-insert-uploader").val();
                if(v=="0") v="";
                if(up_multi){
                  if(v!="") v=v+";";
                  obj.find(".rsTable2-insert-uploader").val(v+tarr[0]);
                  obj.find("div.rsTable2-insert-uploader-preview").append(tarr[1]);
                }else{
                  obj.find(".rsTable2-insert-uploader").val(tarr[0]);
                  obj.find("div.rsTable2-insert-uploader-preview").html(tarr[1]);  
                }
                obj.find(".rsTable2-insert-uploader-del-file").show();
                initLytebox();
              },
             error: function(XMLHttpRequest, textStatus, errorThrown) {
                      alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
                    }
           });
          }
      });
    });
    
    
    $("form.rsTable2-insert-form-"+table).ajaxForm(options);
    initLytebox();
    
    var tmptbl=RETrsTable2ByID(g_options.id);
    if(tmptbl==false) array_push(rsTable2Arr,g_rsTable2);
    $(g_options.container).find("div.rsLoading").hide();
  }
  
  this.rsInitTable=function(msg) {
    if(strpos(msg,"rsInsert#")==1) {
      var rowid=explode("#",msg);
      rowid=rowid[1];
      //alert(rowid);
      g_options.insert=1;
      g_options.insertId=rowid;
      g_rsTable2._insert();
      return false;  
    }
    
    var $mydom = $("<div></div>").appendTo(g_options.container).html(msg);
    g_obj=$("div.rsTable2-tab-"+g_options.table+"[rsTableId="+g_options.id+"]");
    
    g_rsTable2.FixHeader($("div.rsTable2-tab-"+g_options.table+"[rsTableId="+g_options.id+"]"));
    g_rsTable2.Sort($("div.rsTable2-tab-"+g_options.table+" table[rsTableId="+g_options.id+"]"));
    g_rsTable2.Paginazione(g_options.table);
                
    var options = {
      beforeSubmit:  g_rsTable2.searchBeforeSubmit,
      success:  g_rsTable2.searchResp 
    };
    
    $("form.rsTable2-search-form-"+g_options.table+"[rsTableId="+g_options.id+"]").ajaxForm(options);
    $("form.rsTable2-search-form-"+g_options.table+"[rsTableId="+g_options.id+"]").resetForm();
    
    initModWin();
    initLytebox();
    
    if(g_options.showAll==true) {
      $("div.rs-windows-container[rsTableId="+g_options.id+"] a.rsTable2-toolbar-3-pag-on").show();
      $("div.rs-windows-container[rsTableId="+g_options.id+"] a.rsTable2-toolbar-3-pag-off").hide();
    }else{
      $("div.rs-windows-container[rsTableId="+g_options.id+"] a.rsTable2-toolbar-3-pag-off").show();
      $("div.rs-windows-container[rsTableId="+g_options.id+"] a.rsTable2-toolbar-3-pag-on").hide();  
    }
    
    var tmptbl=RETrsTable2ByID(g_options.id);
    if(tmptbl==false) array_push(rsTable2Arr,g_rsTable2);
    $(g_options.container).find("div.rsLoading").hide();
  }

  this.FixHeader=function(obj) {
    obj.fixedHeaderTable({autoResize:true});
  }
  
  this.Sort=function(obj) {
    obj.tableDnD({
      onDragStart: g_rsTable2.DragStart, 
      onDrop: g_rsTable2.DoSort,
      dragHandle: "rsTable2-td-dragHandle",
      onDragClass: "rsTable2-tr-onDrag"
    });  
  }
  
  this.DragStart=function(table,row) {
    /*
    var rows = table.tBodies[0].rows;
    var tabname=rows[0].rsTable;
    var sortable=rows[0].rsSortable;
    var order= new Array();
    
    for (var i=0; i<rows.length; i++) {
      array_push(order,rows[i].rsTable2ID);
    }
    
    $("div.rs-windows-container."+tabname+" tr.rsTable2-tr").parents("table").attr("sortBuffer",urlencode(serialize(order))); 
    */ 
  }
  
  this.DoSort=function(table,row) {
    var rows = table.tBodies[0].rows;
    var tabname=rows[0].getAttribute('rsTable');
    var sortable=rows[0].getAttribute('rsSortable');
    var new_order= new Array();
    
    for (var i=0; i<rows.length; i++) {
      array_push(new_order,rows[i].getAttribute('rsTable2ID'));
    }
    
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&neworder="+rawurlencode(serialize(new_order))+"&table="+tabname+"&rowdropped="+row.getAttribute('rsTable2ID')+"&sortable="+sortable+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: g_rsTable2.RefreshOrder,
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
     });  
  }
  
  this.RefreshOrder=function(msg) {
    //if(msg!="") alert(msg);
  }
  
  this.AddToSelection=function(table) {
    var selection= new Array();
    
    $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").each(function(){
      if($(this).hasClass("rsTable2-selected")){
        array_push(selection,$(this).attr("rsTable2ID")+"_true");
      }else{
        array_push(selection,$(this).attr("rsTable2ID")+"_false");  
      }   
    });
    
    if(count(selection)==0) return;
    
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&selection="+urlencode(serialize(selection))+"&table="+table+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: g_rsTable2.RefreshSelection,
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
    });  
  }
  
  this.RefreshSelection=function(msg) {
    var arr=explode("#_RS_#",msg); 
    var table=arr[0];
    var selection=unserialize(stripslashes(urldecode(arr[1])));
    
    if(count(selection)==0) {
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").removeClass('rsTable2-selected');
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] td.rsTable2-td-selectable input").attr("checked",false); 
      return;
    }
    
    $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").each(function(){
      if(in_array($(this).attr("rsTable2ID"),selection)) {
        $(this).removeClass('rsTable2-hover');
        $(this).addClass('rsTable2-selected');
        $(this).find("td.rsTable2-td-selectable input").attr("checked",true);  
      }else{
        $(this).removeClass('rsTable2-selected');
        $(this).find("td.rsTable2-td-selectable input").attr("checked",false);  
      }   
    });
  }
  
  this.searchResp=function(msg, statusText, xhr, $form) {
    var tabName = xhr.children("input[name=tabName]").attr("value");
    var mydest = "div.rs-windows-container."+tabName+"[rsTableId="+g_options.id+"] .rs-windows-col-two";
    var $mydom = $("<div></div>").hide().appendTo("body").html(msg);
    var dest=$mydom.find(mydest).html();
      
    msg="";
    $mydom.remove();
    
    $(mydest).fadeOut("slow",function(){
      if(dest!=null) {$(mydest).html(dest);}
      $(mydest).fadeIn("slow",function(){
        if($("#lbOverlay:visible").length==0){
          initLytebox();
        }
        
        g_rsTable2.FixHeader($("div.rsTable2-tab-"+tabName+"[rsTableId="+g_options.id+"]"));
        g_rsTable2.Sort($("div.rsTable2-tab-"+tabName+" table[rsTableId="+g_options.id+"]"));
        g_rsTable2.Paginazione(tabName);
        initModWin(); 
      });  
    }); 
  }
  
  this.searchBeforeSubmit=function() {
    if(confirm("Sicuro di voler procedere?")){
      return true;    
    }else{
      return false;
    }  
  }
  
  this.Paginazione=function(table) {
    if($("div.rsTable2-container[rsTableId="+g_options.id+"] div.paginazione[name=rsTable2tab"+g_options.id+"]").length>0){
      $("div.rs-windows-container[rsTableId="+g_options.id+"] .rsTable2-toolbar-3").html($("div.rsTable2-container[rsTableId="+g_options.id+"] div.paginazione[name=rsTable2tab"+g_options.id+"]"));
      $("div.rs-windows-container[rsTableId="+g_options.id+"] div.paginazione[name=rsTable2tab"+g_options.id+"] input.pagesel").attr("title",$("div.rs-windows-container[rsTableId="+g_options.id+"] div.paginazione .testo").html());
      $("div.rs-windows-container[rsTableId="+g_options.id+"] div.paginazione[name=rsTable2tab"+g_options.id+"] input.prima").val("");
      $("div.rs-windows-container[rsTableId="+g_options.id+"] div.paginazione[name=rsTable2tab"+g_options.id+"] input.dopo").val("");
      $("div.rs-windows-container[rsTableId="+g_options.id+"] div.paginazione[name=rsTable2tab"+g_options.id+"] input.prima").show();
      $("div.rs-windows-container[rsTableId="+g_options.id+"] div.paginazione[name=rsTable2tab"+g_options.id+"] input.dopo").show();
      $("div.rsTable2-toolbar-3-container").css("visibility","visible");
    }else{
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] .rsTable2-toolbar-3").html("");
      $("div.rsTable2-toolbar-3-container").css("visibility","hidden");
      $("div.rsTable2-toolbar-3-container .rsTable2-toolbar-3-pag-on").css("visibility","visible");
        
    }
  }
  
  this.TroncaTesto=function(table,field,id,tblParent) {
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&rsTable2TroncaTesto=1&table="+table+"&field="+field+"&id="+id+"&tblParent="+tblParent+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: function(msg){
                  $("[rel=rsWinMod][rsTable="+table+"][rsField="+field+"][rsId="+id+"]").find("div.rsWinMod-container").html(stripslashes(msg));
                  initLytebox();
                  g_rsTable2.FixHeader($("div.rsTable2-tab-"+table+"[rsTableId="+g_options.id+"]"));
                },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
    });
  }
  
  this.UpdateStrutture=function(table,field,id,tblParent) {
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&rsTable2TD-ID=1&table="+table+"&field="+field+"&id="+id+"&tblParent="+tblParent+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: function(msg){
                  var obj=$("[rel=rsWinMod][rsTable="+table+"][rsField="+field+"][rsId="+id+"]");
                  obj.replaceWith(msg);
                  obj=$("[rel=rsWinMod][rsTable="+table+"][rsField="+field+"][rsId="+id+"]")
                  //initModWinEx(obj);
                  //rsModWin_addLive();
                  g_rsTable2.refreshAll();
                },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
    });
  }
  
  this.UpdateBoolean=function(table,field,id,tblParent) {
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&rsTable2TD-boolean=1&table="+table+"&field="+field+"&id="+id+"&tblParent="+tblParent+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: function(msg){
                  var obj=$("[rel=rsWinMod][rsTable="+table+"][rsField="+field+"][rsId="+id+"]").find("div.rsWinMod-container");
                  obj.html(msg);
                  //rsModWin_addLive();
                  g_rsTable2.refreshAll();
                },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
    });
  }
  
  this.updateFile=function(table,field,id,tblParent) {
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&rsTable2UpdateFile=1&table="+table+"&field="+field+"&id="+id+"&tblParent="+tblParent+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: function(msg){
                  $("[rel=rsWinMod][rsTable="+table+"][rsField="+field+"][rsId="+id+"]").find(".rsTable2-file").html(msg);
                  $("[rel=rsWinMod][rsTable="+table+"][rsField="+field+"][rsId="+id+"]").find(".rsTable2-file").hide();
                  $("[rel=rsWinMod][rsTable="+table+"][rsField="+field+"][rsId="+id+"]").find(".rsTable2-file").show();
                  //initLytebox();
                  g_rsTable2.refreshAll();
                },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
    });    
  }
  
  this.modTranslate=function(msg) {
    var lan_mess="";
    var lan_field="";
    var lan_mod="";
    var lan_messi=strpos(msg,"RSI#");
    var lan_messf=strpos(msg,"#RSF");
    
    if(lan_messi!==false && lan_messf!==false) {
      lan_mess=substr(msg, lan_messi+strlen("RSI#"), lan_messf-lan_messi-strlen("#RSF"));
      lan_mess=explode("#_#",lan_mess);
      lan_mod=lan_mess[1];
      lan_field=lan_mess[2];
      lan_mess=lan_mess[0]+" '"+lan_mess[3]+"' "+lan_mess[4];
      msg=substr(msg,lan_messf+strlen("#RSF"));
      if(confirm(lan_mess)) {
        var win_lan_Options = {
          'str':  lan_mod,
          'title': 'Traduzioni',
          'resizable': 1,
          'onLoad': 
            function(obj){
              tinyMCE.init({
            		// General options
            		mode : "textareas",
            		theme : "advanced",
            		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
                editor_selector : "textEditor",
                height:250,
                width:470,
                convert_urls : false,
                relative_urls : false,
                remove_script_host : false,
                paste_auto_cleanup_on_paste : false,
                //save_enablewhendirty: true,
                //save_onsavecallback : "rsTinySave",
                //save_oncancelcallback: "rsTinyCancel",
            		
                // Theme options
            		language : "it",
                theme_advanced_buttons1 : "newdocument,|,fontselect,fontsizeselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist",
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
              
              obj.find("textarea.rsTable2-modTranslate-editor").each(function(){
                tinyid=$(this).attr("id");
                tinyMCE.execCommand('mceAddControl', false, tinyid);  
              });
              
              var options2 = {
                beforeSubmit:
                  function(){
                    //
                  },
                success:  
                  function(msg, statusText, xhr, $form){
                    xhr.find(".rsTable2-modTranslate-submit-result").html(msg);
                    
                    if(xhr.find(".rsTable2-ok").length>0) {
                      setTimeout(function(){
                        xhr.find(".rsTable2-ok").hide("slow");
                      },3000);
                    }
                  }
              };
              
              obj.find("form.rsTable2-modTranslate-form").ajaxForm(options2);
            }
        };
  
        var win_lan=new rsWindows(win_lan_Options);
        win_lan.open();                  
      } 
    }
    return msg;
  }
  
  $("div.rs-windows-container[rsTableId="+g_options.id+"] input.rsTable2-delete-row-input").live("click",function(){
    var table=$(this).attr("rsTable");
    
    g_rsTable2.delSelectedRow(table);  
  });
  
  $("div.rs-windows-container[rsTableId="+g_options.id+"] input.rsTable2-printsel-row-input").live("click",function(){
    var table=$(this).attr("rsTable");
    
    var winOptions = {
      'str':  '',
      'title': 'Report per '+table,
      'width': '919',
      'height': '1460',
      'resizable': 0,
      'onClose':
        function(obj){
          return;
        },
      'onLoad': 
        function(obj){
          var hwnd=obj.attr('hwnd');
          
          var pdfOptions = {
            'container': 'div.rs-windows[hwnd='+hwnd+'] div.rs-windows-content-container',
            'table': table
          };
          
          var rsPdf=new rsPdfEditor(pdfOptions);
          rsPdf._print(function(){
            obj.css("position","absolute");  
          });
        }
    };
    
    var win=new rsWindows(winOptions);
    win.open();
  });
  
  $("div.rs-windows-container[rsTableId="+g_options.id+"] input.rsTable2-insert-doinsert, div.rs-windows-container[rsTableId="+g_options.id+"] div.rsTable2-modify").live("click",function(){
    var table=$(this).attr("rsTable");
    var tableId=$(this).attr("rsTableId");
    var colfilter=$(this).attr("rsColfilter");
    var tableParent=$(this).attr("rsTableParent");
    var tableParentId=$(this).attr("rsTableParentId");
    var rowid=$(this).attr("rsRow");
    
    var winOptions={
      'table': table,
      'tableParent': tableParent,
      'tableParentId': tableParentId,
      'insert': '1',
      'insertId': rowid,
      'colFilter': colfilter,
      'width': 520,
      'height': 513
    };
    
    var win=new rsWindows(winOptions);
    win.open();  
  });
  
  $("div.rs-windows-container[rsTableId="+g_options.id+"] div.rsTable2-div-sort").live("click",function(){
    var colonna=$(this).attr("rsField");
    var oldsort=$(this).attr("rsSort");
    
    if(oldsort=="") {
      $(this).removeClass("rsTable2-div-sort-none");
      $(this).addClass("rsTable2-div-sort-asc");
      $(this).attr("rsSort","ASC");  
    }else if(oldsort=="ASC") {
      $(this).removeClass("rsTable2-div-sort-asc");
      $(this).addClass("rsTable2-div-sort-desc"); 
      $(this).attr("rsSort","DESC");   
    }else if(oldsort=="DESC") {
      $(this).removeClass("rsTable2-div-sort-desc");
      $(this).addClass("rsTable2-div-sort-none");
      $(this).attr("rsSort",""); 
    }
    
    g_options.sort[colonna]=$(this).attr("rsSort");
    
    g_rsTable2.remove();
    g_rsTable2._print();
  });
  
  $("div.rsTable2-insert-container[rsTableId="+g_options.id+"] .rsTable2-insert-uploader-del-file").live("click",function(){
    if(confirm("Proseguire con l'eliminazione?")) {
      var objbtn=$(this);
      var obj=objbtn.parents("div.rsTable2-insert-uploader-container");
      var id=obj.find("input.rsTable2-insert-uploader").val();
      var name=obj.find("input.rsTable2-insert-uploader").attr("name");
      var rowid=obj.find("input.rsTable2-insert-uploader").attr("rsRowId");
      
      $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&delfile="+id+"&name="+name+"&rowid="+rowid+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: 
        function(msg){
          obj.find("input.rsTable2-insert-uploader").val("");
          obj.find("div.rsTable2-insert-uploader-preview").html("");
          objbtn.hide();
        },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
      });
    }else{
      return true;
    }    
  });
  
  $("div.rs-windows-container[rsTableId="+g_options.id+"] a.rsTable2-toolbar-3-pag-on").live("click",function(){
    g_rsTable2.showAll(false);
    return false;
  });
  
  $("div.rs-windows-container[rsTableId="+g_options.id+"] a.rsTable2-toolbar-3-pag-off").live("click",function(){
    g_rsTable2.showAll(true);
    return false;
  });
  
  $("div.rs-windows-container[rsTableId="+g_options.id+"] input.rsTable2-search-docancel").live("click", function(){
    if(confirm("Vuoi eliminare tutti i parametri della ricerca?")){
      //CODICE AZZERAMENTO RICERCA
      $(this).parents("form").find("select").each(function(){
        $(this).val($(this).find('option:first').val());
      });
      
      $(this).parents("form").find("input[type=text]").each(function(){
        $(this).val("");
      });
      
      $(this).parents("form").submit();
      return true;    
    }else{
      return false;
    }
  });
  
  /*
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-modify").live("mouseover",function() {
    $(this).find("div.rsTable2-modify").addClass('rsTable2-show-modify');
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-modify").live("mouseleave",function() {
    $(this).find("div.rsTable2-modify").removeClass('rsTable2-show-modify');
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-delete").live("mouseover",function() {
    $(this).find("div.rsTable2-delete").addClass('rsTable2-show-delete');
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-delete").live("mouseleave",function() {
    $(this).find("div.rsTable2-delete").removeClass('rsTable2-show-delete');
  });
  */
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-delete div.rsTable2-delete").live("click",function(){
    if(confirm("Procedere con l'eliminazione?")) {
      var table=$(this).attr("rsTable");
      var id=$(this).attr("rsTable2ID");
      
      g_rsTable2.delrow(table,id);
    }    
  });
  
  /*
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-dragHandle").live("mouseover",function() {
    $(this).find("div.rsTable2-sortable").addClass('rsTable2-showSortable');
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-dragHandle").live("mouseleave",function() {
    $(this).find("div.rsTable2-sortable").removeClass('rsTable2-showSortable');
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-nm").live("mouseover",function() {
    if($("div.rsTable2-dialog-nm").length==0) {
      $(this).find("div.rsTable2-nm").addClass('rsTable2-show-nm');
    }else{
      $(this).find("div.rsTable2-nm").removeClass('rsTable2-show-nm');
    }
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-nm").live("mouseleave",function() {
    $(this).find("div.rsTable2-nm").removeClass('rsTable2-show-nm');
  });
  */
    
  $("div.rsTable2-container[rsTableId="+g_options.id+"] table tr").live("click",function() {
    $("div.rsTable2-dialog-nm").remove();
  });

  $("div.rsTable2-container[rsTableId="+g_options.id+"] table tr").live("mouseover",function() {
    $(this).addClass('rsTable2-hover');
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] table tr").live("mouseleave",function() {
    $(this).removeClass('rsTable2-hover');
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] div.rsTable2-show-nm").live("click",function(e) {
    var td=$(this).parents("td.rsTable2-td-nm");
    var tabname=td.attr("rsTable");
    var id=td.attr("rsTable2ID");
    
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsTable2Action=1&getRelNM=1&table="+rawurlencode(tabname)+"&id="+id+"&titparent="+rawurlencode(g_options.title)+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
       success: function(msg){
                  //td.find("div.rsTable2-nm").removeClass('rsTable2-show-nm');
                  $("div.rsTable2-dialog-nm").remove();
              
                  var relativeX = (e.pageX);
                  var relativeY = (e.pageY);
                  var $mydom = $("<div class='rsTable2-dialog-nm-container'></div>").appendTo("body").html(msg);
                  $mydom.css("left",relativeX+"px");
                  $mydom.css("top",relativeY+"px");  
                },
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
     });
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] td.rsTable2-td-selectable input").live("click",function() {
    var tr=$(this).parents("tr");
    var table=tr.attr("rsTable");
    
    if($(this).is(":checked")){
      tr.removeClass('rsTable2-hover');
      tr.addClass('rsTable2-selected');
    }else{  
      tr.removeClass('rsTable2-selected');  
    }
    
    g_rsTable2.AddToSelection(table);
  });
  
  $("div.rs-windows-container[rsTableId="+g_options.id+"] div.rsTable2-tb-selectable input").live("click",function(){
    var tb=$(this).parents("div.rsTable2-tb-selectable");
    var table=tb.attr("rsTable");
    
    if($(this).is(":checked")){
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").removeClass('rsTable2-hover');
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").addClass('rsTable2-selected');
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] td.rsTable2-td-selectable input").attr("checked",true);
    }else{
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").removeClass('rsTable2-selected');
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] td.rsTable2-td-selectable input").attr("checked",false);  
    }
    
    g_rsTable2.AddToSelection(table);  
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] th.rsTable2-th-selectable input").live("click",function(){
    var th=$(this).parents("th");
    var table=th.attr("rsTable");
    
    if($(this).is(":checked")){
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").removeClass('rsTable2-hover');
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").addClass('rsTable2-selected');
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] td.rsTable2-td-selectable input").attr("checked",true);
    }else{
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] tr.rsTable2-tr").removeClass('rsTable2-selected');
      $("div.rs-windows-container."+table+"[rsTableId="+g_options.id+"] td.rsTable2-td-selectable input").attr("checked",false);  
    }
    
    g_rsTable2.AddToSelection(table);  
  });
  
  this.setFilter = function(obj,parent) {
    var name=obj.attr("name")
    var rsFilter=obj.attr("rsFilter");
    var rsTable=obj.attr("rsTable");
    var rsRowId=obj.attr("rsRowId");
    var selected=obj.find("option:selected").val()
    var namestr="";
    if(parent.hasClass("rsTable2-container")) namestr="1";
    
    $.ajax({
     type: "POST",                       
     url: "rsAction.php",
     data: "rsTable2Action=1&rsFilterFun=1&rsFilter="+rawurlencode(serialize(rsFilter))+"&rsTable="+rawurlencode(serialize(rsTable))+"&rsRowId="+rsRowId+"&name="+name+"&sel="+selected+"&namestr="+namestr+"&optionsSer="+rawurlencode(serialize(g_optionsSer)), 
     success: function(msg){
                var s=explode("#RSSEP#",msg);
                for(var i=0;i<count(s);i++) {
                  var $mydom=$("<div></div>").html(s[i]).find("select");
                  parent.find("select[name='"+$mydom.attr("name")+"']").replaceWith($mydom);
                }
              },
     error: function(XMLHttpRequest, textStatus, errorThrown) {
              //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
            }
   });
  }
  
  $("div.rsTable2-insert-container[rsTableId="+g_options.id+"] input.rsTable2-insert-row-l-boolean-check").live("change",function(){
    var name=$(this).attr("rsField");
    var val=$(this).is(":checked");
    if(val==true) val=1;else val=0;
    $("div.rsTable2-insert-container[rsTableId="+g_options.id+"] input[name="+name+"]").val(val);    
  });
  
  $("div.rsTable2-insert-container[rsTableId="+g_options.id+"] select[rsFilter!='']").live("change",function(){
    g_rsTable2.setFilter($(this),$("div.rsTable2-insert-container[rsTableId="+g_options.id+"]"));    
  });
  
  $("div.rsTable2-container[rsTableId="+g_options.id+"] select[rsFilter!='']").live("change",function(){
    g_rsTable2.setFilter($(this),$("div.rsTable2-container[rsTableId="+g_options.id+"]"));  
  });
  
  $("div.rsTable2-search-container[rsTableId="+g_options.id+"] select[rsFilter!='']").live("change",function(){
    g_rsTable2.setFilter($(this),$("div.rsTable2-search-container[rsTableId="+g_options.id+"]"));  
  });
  
  $("div.rsTable2-insert-container[rsTableId="+g_options.id+"] input.rsTable2-perc-input").live("change",function(){
    var id=$(this).attr("name");
    var defValue=toFloat($(this).val());
    var percVal=toFloat($(this).attr("pointer"));
    
    $("input.rsTable2-perc-input2[name='"+id+"_perc']").val(number_format(round(percVal-(percVal*defValue)/100,2), 2, ',', ''));
  });
  
  $("div.rsTable2-insert-container[rsTableId="+g_options.id+"] input.rsTable2-perc-input2").live("change",function(){
    var id=$(this).attr("name");
    id=str_replace("_perc", "", id);
    
    var defValue=toFloat($(this).val());
    var percVal=toFloat($(this).attr("pointer"));
    
    $("input.rsTable2-perc-input[name='"+id+"']").val(number_format(round(100-((defValue*100)/percVal),4), 4, ',', ''));
  });
}