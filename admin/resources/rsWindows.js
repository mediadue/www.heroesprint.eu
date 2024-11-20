function rsWindows(options) {
  var g_rsWindows=this;
  var g_obj;
  var g_options;
  var g_table;
  
  var g_defaults = {
		'str':  "",
    'strutture': "",
    'table': "",
    'tableParent': "",
    'tableParentId': "",
    'maxButton': true,
    'colFilter': "",
    'where': "",
    'order': "",
    'title': "",
    'width': 901,
    'height': 537,
    'template': "",
    'resizable': "1",
    'tag': "",
    'hwnd': getFilenameUnique(),
    'onClose': function() {},
    'onLoad': function() {}
	};
  
  g_options = $.extend(g_defaults, options);
  
  this.open=function () {
    $.ajax({
       type: "POST",                       
       url: "rsAction.php",
       data: "rsOpenW=1&options="+rawurlencode(serialize(g_options)), 
       success: this.rsInitWindows,
       error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Si è verificato un errore durante l'operazione richiesta, ripetere l'operazione."); 
              }
     });  
  }
  
  this.rsInitWindows=function (msg) {
    var $mydom = $("<div></div>").appendTo("body").html(msg);
    g_rsWindows.rsWindowsDraggable();
    g_rsWindows.rsWindowsResizable();
    
    if(g_options.table!="") {
      var tblOptions={
        'container': "div.rs-windows[hwnd="+g_options.hwnd+"] .rs-windows-content-container",
        'table': g_options.table,
        'insert': g_options.insert,
        'insertId': g_options.insertId, 
        'tableParent':   g_options.tableParent,
        'tableParentId':   g_options.tableParentId,
        'where': g_options.where,
        'order': g_options.order,
        'title': g_options.title,
        'colFilter': g_options.colFilter
      };
      g_table=new rsTable2(tblOptions);
      if(g_options.insert=="1"){
        g_table._insert();      
      }else{
        g_table._print();
      }   
    }
    
    if(g_options.strutture!="") rsStrutture_init();
    
    var obj=$('div.rs-windows[hwnd='+g_options.hwnd+']');
    obj.css("width",g_options.width+"px");
    obj.css("height",g_options.height+"px");
    obj.find("div.rs-windows-frame-bottom").width(obj.width());
    obj.find("div.rs-windows-frame-right").height(obj.height());
    initModWin();
    if(g_options.onLoad) g_options.onLoad(obj);
  }
  
  this.rsWindowsDraggable=function () {
    $('div.rs-windows[hwnd='+g_options.hwnd+']').draggable({addClasses: true, cursor: 'move', handle: $('div.rs-windows[hwnd='+g_options.hwnd+'] div.rs-windows-titolo-container').disableSelection() });
  }
  
  this.winres=function (obj) {
    var tw=obj.width()-240;
    var th=obj.height()-78;
    if(tw>0 && th>0) {
      obj.find("div.rs-windows-col-two").show();
      obj.find("div.rsTable2-container").css({'width': tw+'px', 'height': th+'px'});
    }else{
      obj.find("div.rs-windows-col-two").hide();
    }
    
    obj.find("div.rs-windows-frame-bottom").width(obj.width());
    obj.find("div.rs-windows-frame-right").height(obj.height());  
  }
  
  this.rsWindowsResizable=function () {
    var obj=$('div.rs-windows[hwnd='+g_options.hwnd+']');
    if(g_options.resizable==1) {
      obj.resizable({ 
        maxHeight: g_options.height,
        maxWidth: g_options.width,
        minHeight: 23,
        minWidth: 150,
        resize: function(event, ui) {g_rsWindows.winres(obj);}
      });
    }
  }
  
  $("div.rs-windows[hwnd="+g_defaults.hwnd+"] .rs-windows-close").live("click",function(){
    var rs_windows = $(this).parents("div.rs-windows");
    var obj=rs_windows;
    rs_windows.fadeOut("slow",function(){
      rs_windows.remove();
      if(g_options.onClose) g_options.onClose(obj);  
    });  
  });
  
  $("div.rs-windows[hwnd="+g_defaults.hwnd+"] .rs-windows-maximize").live("click",function(){
    var rs_windows = $(this).parents("div.rs-windows");
    
    rs_windows.find("div.rs-windows-col-two").show();
    rs_windows.css({"width":g_options.width+"px", "height":g_options.height+"px"});
    rs_windows.find("div.rsTable2-container").css({'width': (g_options.width-240)+'px', 'height': (g_options.height-78)+'px'});
    rs_windows.trigger("resize");
    g_rsWindows.winres(rs_windows);
  });
  
  $("div.rs-windows[hwnd="+g_defaults.hwnd+"] .rs-windows-minimize").live("click",function(){
    var rs_windows = $(this).parents("div.rs-windows");
    var state=rs_windows.attr("state");
    if(state==-1) return;
    
    var tmpleft=$("div.rs-windows[state=-1]").length*100;
    var wh=$(window).height()-20;
    var position = rs_windows.position();
    
    rs_windows.attr("state","-1");
    rs_windows.attr("posx",position.left);
    rs_windows.attr("posy",position.top);
    rs_windows.attr("ww",rs_windows.width());
    rs_windows.attr("hh",rs_windows.height());
    
    rs_windows.height(23);
    rs_windows.width(100);
    rs_windows.find(".rs-windows-top-right-bar").hide();
    rs_windows.find(".rs-windows-icon").hide(); 
    rs_windows.resizable("destroy");
    rs_windows.draggable("destroy");
    rs_windows.css("top",wh+"px");
    rs_windows.css("left",tmpleft+"px");   
  });
  
  $("div.rs-windows[hwnd="+g_defaults.hwnd+"]").live("dblclick",function(){
    var state=$(this).attr("state");
    if(state==-1) {
      var tposx=$(this).attr("posx");
      var tposy=$(this).attr("posy");
      var tw=$(this).attr("ww");
      var th=$(this).attr("hh");
  
      $(this).attr("state","0");
      $(this).css({'width': tw+'px', 'height': th+'px'});
      $(this).find("div.rs-windows-top-right-bar").show();
      $(this).find("div.rs-windows-icon").show(); 
      g_rsWindows.rsWindowsResizable($(this));
      g_rsWindows.rsWindowsDraggable($(this));
      $(this).css({"top":tposy+"px", "left":tposx+"px"});  
    }   
  });
}