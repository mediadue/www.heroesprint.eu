<?php 
if(!isAjaxPost()) { 
  if(isset($_SESSION['alert_box'])) {
    if($_SESSION['alert_box']!="") box($_SESSION['alert_box']);
    unset($_SESSION['alert_box']);
  }    
  
  if(file_exists(SERVER_DOCROOT . 'admin/stats/php-stats.lock')) {
    define('__PHP_STATS_PATH__',SERVER_DOCROOT . 'admin/stats/');
    include(__PHP_STATS_PATH__.'php-stats.redir.php');
  }
  
  //include(SERVER_DOCROOT . 'include/js.gmap.php');
  
  $tForm=getForms();
  if($tForm!="") {
    $rsForm = new rsForm($tForm['Nome']);
  }
  
  if(($_POST["utente"] && $_POST["pwd"]) || ($_GET['logout'] && !$_POST["password"] && !$_POST["password_conf"]) || ($_GET['user'] && $_GET['pass'] )) {
    $areaRiservata=valLogin();
  }
  
  if($_POST["password"] && $_POST["password_conf"]) {
    cambiaPassword($conn);
  }
  
  if(isset($_POST["pwdSendDo"])) {
    PasswordDimenticata();
  }
  
  if(location()) { ?>
    <script>
    $(window).load(function(){
      GLoad(); 
    });
    
    $(window).unload(function(){
      GUnload();  
    });
    </script>
  <? } 
  
  //$objChat=new rsChat;
  //$objChat->addToChat();
}
?>