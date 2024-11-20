$(document).ready(function(){
<?php if((isset($_POST['UserReg']) || $_GET["HRreg"]==1) && !isset($_SESSION["userris_id"])) { ?>
   var tblOptions={
      <?php 
      $tblOptions["container"]="div.container-r-reg";
      $tblOptions["table"]="users";
      $tblOptions["insert"]=1;
      $tblOptions["permDel"]=-1;
      $tblOptions["submitLabel"]=ln("Registrati");
            
      echo cryptOptions($tblOptions); 
      ?>
   };
    
   g_table=new rsTable2(tblOptions);
   g_table._insert();
 <? }else if($_SESSION["UserReg"]=="1" && isset($_SESSION["userris_id"])){ ?>
   var tblOptions={
      <?php 
      $tblOptions["container"]="div.container-r-reg";
      $tblOptions["table"]="users";
      $tblOptions["insert"]=1;
      $tblOptions["insertId"]=$_SESSION["userris_id"];
      $tblOptions["permDel"]=-1;
      $tblOptions["submitLabel"]=ln("Salva Modifiche");
            
      echo cryptOptions($tblOptions); 
      ?>
   };
    
   g_table=new rsTable2(tblOptions);
   g_table._insert(); 
 <? } ?> 
 
 var tblOptions={
    <?php 
    $tblOptions["container"]="div.form-contattaci";
    $tblOptions["table"]="contattaci";
    $tblOptions["insert"]=1;
    $tblOptions["permDel"]=-1;
    $tblOptions["submitLabel"]=ln("Invia Richiesta");
          
    echo cryptOptions($tblOptions); 
    ?>  
  };            
      
  g_table=new rsTable2(tblOptions);
  g_table._insert();
  
  <?php include_once("inc.analyticstracking.php"); ?>
  <?php include_once("inc.tawk.php"); ?>
  <?php include_once("inc.googletagmgr.php"); ?>
});