<?php

Class Paginazione {
  var $g_rs="";
  var $g_name="";
  var $g_perPag="";
  var $g_dest="";
  var $g_autoslide="";
  var $g_timer="";
  var $g_jsevents="";
  var $g_rawSession="";
  var $g_onPagChange="";
  var $g_precedenteText="";
  var $g_successivaText="";
  var $g_interval=8;
  
  function setOptions($rs,$name,$perPag,$dest,$autoslide="",$url="",$rawSession="",$animate="") {
    $objUtility = new Utility;
    
    if($autoslide=="") $autoslide=0;
    
    $this->g_rs=$rs;
    $this->g_name=$name;
    $this->g_perPag=$perPag;
    $this->g_dest=$dest;
    $this->g_autoslide=$autoslide;
    $this->g_rawSession=$rawSession;
    $this->g_timer=$name."_timer";
    $this->g_animate=$animate;
    
    if($url=="" || isAjaxPost()) {
      $this->g_url=curPageURL();
    }else{
      $this->g_url=$url;  
    }
    
    $this->action();    
  }

  function JSonPageChange($fun){
    $this->g_onPagChange=$fun;    
  }

  function buildRs($rs="") {
    if($rs!="" && is_array($rs)) {
      $this->g_rs=$rs;
      if(count($rs)<=$this->g_perPag) return $rs;
    }  
    
    $ret_rs=array();
    $rs=$this->g_rs;
    
    $currpage=$this->currentPage();
    $perPag=$this->g_perPag;
    
    if($currpage=="") $currpage=1;
    
    $pstart=($currpage-1)*$perPag;
    $pend=$perPag;
    
    while (list($key, $row) = each($rs)) {
      if($key>=$pstart && $key<($pstart+$pend) ) {
        array_push($ret_rs, $row);
      }
    }
    
    return $ret_rs;
  }

  function pagname() {
    $name=$this->g_name;
    $pagname='rsPagin_'.$name;
    
    return $pagname;  
  }
  
  function currentPage($pag="") {
    $pagname=$this->pagname();
    if(isset($_POST[$pagname])) return $_POST[$pagname]; 
    
    if($pag!="") $_SESSION[$pagname]=$pag;
    
    $currpage=$_SESSION[$pagname];
    if($currpage=="") $currpage=1;
    
    return $currpage;  
  }
  
  function action() {
    $objUtility = new Utility;
    
    $name=$this->g_name;
    $dest=$this->g_dest;
    $pagname=$this->pagname();
    $objUtility->getAction($strAct, $cpage);
    $objSession = new Session();
     
    if($strAct==strtoupper($name) && !isset($_POST["phpss"])) {
      if($cpage=="") $cpage=1;
      $this->currentPage($cpage);
      
      $mypost=$_POST;
      $act="act_".$name."_".$cpage;
      unset($mypost[$act]);
      $mypost[$pagname]=$cpage;  
      $mypost['phpss']=$objSession->retPost();

      if(is_array($mypost) && count($mypost)>0) {
        $postdata = http_build_query($mypost); 
        
        $opts = array('http' => 
            array( 
                'method'  => 'POST', 
                'header'  => 'Content-type: application/x-www-form-urlencoded', 
                'content' => $postdata 
            ) 
        ); 
         
        $context = stream_context_create($opts); 
        
        // create a new session
        $objSession->create();
       	$objSession->save($_SESSION); 
				
        $str = file_get_contents($this->g_url, false, $context);
        if($this->g_rawSession!="-1") $_SESSION=$objSession->raw();
      } else {
        $str = file_get_contents($this->g_url);
      }
      
      echo $str;
      exit;
    }elseif(isAjaxPost() && $_POST['phpss']!="") {
    	$objphpss = new Session($_POST['phpss']);
    	$_SESSION=$objphpss->raw();
    }
  }
  
  function bind_js($js) {
    $this->g_jsevents=$js;
    return;  
  }
  
  function _print() {
    $objHtml = new Html;
    
    $rs=$this->g_rs;
    $name=$this->g_name; 
    $perPag=$this->g_perPag;
    $dest=$this->g_dest;
    $autoslide=$this->g_autoslide;
    $url=$this->g_url;
    $timer=$this->g_timer;
    $jsevents=$this->g_jsevents;
    $animate=$this->g_animate;
    $m_onPagChange=$this->g_onPagChange;
    
    $pagname=$this->pagname();
    $currpage=$this->currentPage();
    
    $getstr="";
    if($_POST['rsOpenW']!="1" && $_POST['rsTable2Action']!="1") {
      foreach ($_POST as $postKey => $postValue) {
        if($postKey!="rsPagin" && $postKey!="rsPagin_pagRicerca" && $postKey!="phpss"){
          $getstr.=$postKey."=".$postValue."&";
        }
      }

      $getstr=substr($getstr, 0, strlen($getstr)-1);
    }
    
    $totres=count($rs);
    $npags=ceil($totres/$perPag);
    
    $nascondi="";
    if($autoslide>0) $nascondi=1;
    
    $objHtml->paginazione($perPag,$totres,$npags,$currpage,$name,$this->g_interval,"","","",$nascondi,$this->g_precedenteText,$this->g_successivaText);
    ?>
    <script> 
    if(typeof(rsPagin<?=$name?>)=="undefined") eval("var rsPagin<?=$name?>='';");
    
    function rsPaginazioneInit<?=$name?>() {
      if(<?=$autoslide?>>0) {
        $("div.paginazione[name=<?=$name?>]").hide();  
      }
      
      $("div.paginazione[name=<?=$name?>]").removeClass("<?=$name?>");
      
      $("div.paginazione[name=<?=$name?>] input.page").attr("pagname","<?=$name?>");
      $("div.paginazione[name=<?=$name?>] input.page").attr("perPag","<?=$perPag?>");
      $("div.paginazione[name=<?=$name?>] input.page").attr("dest","<?=$dest?>");
      $("div.paginazione[name=<?=$name?>] input.page").attr("autoslide","<?=$autoslide?>");
      $("div.paginazione[name=<?=$name?>] input.page").attr("url","<?=$url?>");
      $("div.paginazione[name=<?=$name?>] input.page").attr("getstr","<?=$getstr?>");
      $("div.paginazione[name=<?=$name?>] input.page").attr("jsevents","");
      $("div.paginazione[name=<?=$name?>] input.page").attr("onPagChange","<?=$m_onPagChange?>");
      $("div.paginazione[name=<?=$name?>] input.page").attr("animate","<?=$animate?>");
      <?php if(is_array($jsevents)) { ?> $(".paginazione[name=<?=$name?>] input.page").attr("jsevents","<?php echo implode('#_#',$jsevents); ?>");<? } ?>  
      if(<?=$autoslide?>>0 && rsPagin<?=$name?>=="") rsPaginazioneInitAutoSlide($(".paginazione[name=<?=$name?>] input.page"));
      rsPagin<?=$name?>="1";
    }
    
    $(document).ready(function(){
      rsPaginazioneInit<?=$name?>();  
    });
    </script>
    <?  
  }
}
?>