<?php
Class rsWinMod {
  function rsWinMod() {
    return true;
  }

  function action() {
    global $config_table_prefix;
    $objUtility = new Utility;
    
    if($_POST['rsWinMod']==1) {
      if($_POST['type']=="getField"){
        $tmptable=new rsTable2();
        
        $field=$_POST['field'];
        $tmptab=retRow($_POST['table'],$_POST['id']);
        $rsPower=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$_POST['table']."' AND campo_hidden='".$_POST['field']."')"));
        $rsPower=$rsPower[0]['rsPower'];  
        $tarr=explode(";",$rsPower);
        
        echo $rsPower."#rsSEP#";
        
        if(($fun=$tmptable->rsValidate($tarr,"_str_"))!==FALSE){
          $struttura=str_replace("_str_", "", $fun);
          
          stampaStruttura($struttura,$selected=$tmptab[$field],$useLayout="",$cat="",$edit="",$urlRewrite="");
          exit;   
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"id_"))!==FALSE){
          $filter=$tmptable->rsValidate($tarr,"_filter#");
          if($filter) {
            $filter=right($filter,strlen($filter)-strlen("_filter#"));
          }else{
            $filter="";
          }
          $tmpTable=new rsTable2($_POST['table']);
          $tmpTable->printIdTD($field,$fun,$tmptab[$field],$_POST['id'],$filter);
          exit;   
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"_suggest#"))!==FALSE){
          $pointer=explode("_suggest#", $fun);
          $pointer=$pointer[1];
          $tmpTable=new rsTable2($_POST['table']);
          ?>
          <div class="rsTable2-suggest">
            <?php $tmpTable->printIdTD($field."_suggest","id_".$pointer,"","-1","",""); ?>
            <input name="<?php echo $field; ?>" class='rsTable2-suggest-input' type='text' value='<?php echo $tmptab[$field]; ?>' readonly='readonly' />
          </div>
          <?php
          exit;   
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"_date_small"))!==FALSE){
          $tmpTable=new rsTable2($_POST['table']);
          $currYear=date("Y", time());
          $yy=$currYear-10;
          $tmpTable->formdata($field,'','','','',$tmptab[$field],$yy);
          exit;   
        }elseif(($fun=$tmptable->rsValidate($tarr,"_date"))!==FALSE) {
          $tmpTable=new rsTable2($_POST['table']);
          $tmpTable->formdata($field,'','','','',$tmptab[$field],'1900');
          exit;  
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"_boolean"))!==FALSE){
          if($tmptab[$field]!=0) {
            $checked="checked";      
          }else{
            $checked="";  
          }
          ?><div class="ez-wr rstbl2-input-checkbox-container"><input name="<?php echo $field; ?>" type="checkbox" class="rstbl2-input-checkbox" value="1" <?php echo $checked; ?> /></div><?php
          exit;   
        }
        
        echo $tmptab[$field];
      }
      
      if($_POST['type']=="setField"){
        $tmptable=new rsTable2();
        $tblParent=$_POST['tableparent'];
        $parentId=$_POST['parentid'];
        $tmptable->g_table=$_POST['table'];
        $tmptable->g_tblParent=$tblParent;
        $tmptable->g_parentId=$parentId;
        $field_lan=array();
        $fname_lan=array();
        $field=$_POST['field'];
        
        $tmptab=retRow($_POST['table'],$_POST['id']);
        $rsPower=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$_POST['table']."' AND campo_hidden='".$field."')"));
        $fname=$rsPower[0]['titolo_visualizzato'];
        $rsPower=$rsPower[0]['rsPower'];  
        $tarr=explode(";",$rsPower);
        $nval=rawurldecode($_POST['newval']);
        $oldval=retRow($_POST['table'],$_POST['id']);
        
        if(($fun=$tmptable->rsValidate($tarr,"_cry"))!==FALSE){
          if(!$tmptable->elaborateCurrency($nval)) {
            echo "<div class='rsWinMod-not-unique' title='".ln("Il valore deve essere di tipo monetario.")."'>".$oldval[$field]."</div>";
            exit;  
          }else{
            $nval=str_replace(",", ".", $nval);
          }
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"_number"))!==FALSE){
          if(!$tmptable->elaborateCurrency($nval)) {
            echo "<div class='rsWinMod-not-unique' title='".ln("Il valore deve essere di tipo numerico.")."'>".$oldval[$field]."</div>";
            exit;  
          }else{
            $nval=str_replace(",", ".", $nval);
          }
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"_date"))!==FALSE || ($fun=$tmptable->rsValidate($tarr,"_date_small"))!==FALSE){
          $isdate=true;
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"_obligatory"))!==FALSE){
          if($isdate){
            if($nval=="0000-00-00") $nval="";
          }

          if($tmptable->rsValidate($rsfunArr,"_str_")!==FALSE) {
            if($nval=="0") $nval="";
            if($nval=="-1") $nval=""; 
          }
          
          if($tmptable->rsValidate($rsfunArr,"id_")!==FALSE) {
            if($nval=="0") $nval="";
            if($nval=="-1") $nval=""; 
          }
          
          if($nval==""){
            echo "<div class='rsWinMod-not-unique' title='".ln("Questo campo è obbligatorio.")."'>(*) ".$oldval[$field]."</div>";
            exit;
          }
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"_unique"))!==FALSE){
          if($parentId>0 && $tblParent!="") {
            $q=Table2ByTable1($tblParent,$_POST['table'],$parentId,"(`".$config_table_prefix.$_POST['table']."`.".$field."='".$nval."' AND `".$config_table_prefix.$_POST['table']."`.id<>'".$_POST['id']."')","");
          }else{
            $q=getTable($_POST['table'],"","(".$field."='".$nval."' AND id<>'".$_POST['id']."')");
          }
          
          if(count($q)>0) {
            echo "<div class='rsWinMod-not-unique' title='".ln("Questo campo è univoco.")."'>(**) ".$oldval[$field]."</div>";
            exit;
          } 
        }

        $sql="UPDATE `".$config_table_prefix.$_POST['table']."` SET `".$_POST['field']."`='".$nval."' WHERE id='".$_POST['id']."'";
        rsTable2_BeforeUpdate($_POST['table'],$_POST['id'],$tblParent,$parentId,$sql,$sql,$sql);
        mysql_query($sql);
        //echo "RSI#".$oldval[$field]."#_#".$str_mod_translate."#_#".$str_lan_col."#_#".$str_lan_field."#_#".ln(" hanno delle traduzioni abbinate, si desidera apportare modifiche anche alle rispettive traduzioni?")."#RSF"; 
        $exist_lan=ExistTraduction($oldval[$field]);
        if($exist_lan!=false){
          array_push($field_lan, $field); 
          array_push($fname_lan, $fname);
          $val_lan[$field]['trad']=$exist_lan;
          $val_lan[$field]['newval']=$nval; 
          
          $str_lan_col=implode(",", $field_lan);
          $str_lan_field=implode(",", $fname_lan);
          $tmptable->addTranslate($val_lan);
          $str_mod_translate=$tmptable->modTranslate($_POST['id'],$field_lan);
          echo "RSI#".ln("I campi ")."#_#".$str_mod_translate."#_#".$str_lan_col."#_#".$str_lan_field."#_#".ln(" hanno delle traduzioni abbinate, si desidera apportare modifiche anche alle rispettive traduzioni?")."#RSF";
        }
        rsTable2_AfterUpdate($_POST['table'],$_POST['id'],$tblParent,$parentId,$oldval);
        
        if(($fun=$tmptable->rsValidate($tarr,"_cry"))!==FALSE) {
          echo "€&nbsp;";
          $nval=str_replace(",", ".", $nval);
        }
        
        if(($fun=$tmptable->rsValidate($tarr,"_number"))!==FALSE) {
          $nval=str_replace(",", ".", $nval);
        }
        
        echo $nval;
      }
      
      if($_POST['type']=="setFile"){
        $f=rawurldecode($_POST['newval']);
        if($f!="0"){  
          $basename=basename($f);
          $path=$objUtility->getPathResourcesDynamicAbsolute()."uploaded/";
          $ext=retExt($f);
          $funique=$objUtility->getFilenameUnique();
          
          rename($path.$basename,$objUtility->getPathResourcesDynamicAbsolute().$funique.".".$ext);
          
          $sql="INSERT INTO ".$config_table_prefix."oggetti (nome,path,originalname,ext,isprivate) VALUES ('".$funique."','".$objUtility->getPathResourcesDynamic()."','".$basename."','".$ext."',NULL)";
          mysql_query($sql);
          $idoggetti=mysql_insert_id();
          if($idoggetti>0){
            $field=$_POST['field'];
            $old=retRow($_POST['table'],$_POST['id']);
            $oldf=retFileAbsolute($old[$field]);
            if($oldf) unlink($oldf);
            
            $sql="UPDATE `".$config_table_prefix.$_POST['table']."` SET `".$_POST['field']."`='".$idoggetti."' WHERE id='".$_POST['id']."'";
            mysql_query($sql);
            echo $idoggetti;  
          }
        }else{
          //ELIMINA IL FILE
          $field=$_POST['field'];
          $f=retRow($_POST['table'],$_POST['id']);
          $tf=retFileAbsolute($f[$field]);
          if($tf) unlink($tf);
          
          $sql="DELETE FROM `".$config_table_prefix."oggetti` WHERE id='".$f[$field]."'";
          mysql_query($sql);
          
          $sql="UPDATE `".$config_table_prefix.$_POST['table']."` SET `".$_POST['field']."`='0' WHERE id='".$_POST['id']."'";
          mysql_query($sql);
          
          echo "0";  
        }  
      }
      exit;  
    }
  }
}
?>