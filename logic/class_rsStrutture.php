<?php
  Class rsStrutture {
    var $g_config_table_prefix;
    
    function rsStrutture() {
      global $config_table_prefix;
    }
    
    function getSelected() {
      return unserialize(stripslashes(rawurldecode($_POST['selected']))); 
    }
    
    function action() {
      if($_POST['rsUPDStrutture']!="1") return false;
      
      global $config_table_prefix;
      $objUtility=new Utility;
      $objUsers=new Users;
      
      if($_POST['type']=="rsstr-open"){
        stampaStruttura(stripslashes(rawurldecode($_POST['name'])),$selected=$this->getSelected(),$useLayout="",$cat="",$edit=$_POST['editable'],$urlRewrite="",$_POST['useAjax'],"","","",$_POST['rsNMRel'],$_POST['sortable']);  
      }
      
      
      if($_POST['type']=="rsstr-delete"){
        $selected=getPadre($_POST['id']);
        cancellaNodoStruttura($_POST['id']);
        $sql="DELETE FROM `".$config_table_prefix."categorie` WHERE id='".$_POST['id']."'";
        mysql_query($sql);
        $sql="DELETE FROM `".$config_table_prefix."categorie#categorie_nm` WHERE id_categorie='".$_POST['id']."'";
        mysql_query($sql);
        
        $objUsers->getCurrentUser($intIdutente, $strUsername);
        if($intIdutente>0){
          $sql="UPDATE ".$config_table_prefix."storico_users SET `deleted`= 1 , `ultimo_aggiornamento`= NOW() WHERE (`table`='categorie' AND `row`='".$_POST['id']."')"; 
          mysql_query($sql);
          $nid=mysql_insert_id();
        }
        
        stampaStruttura(stripslashes(rawurldecode($_POST['name'])),$selected['id'],$useLayout="",$cat="",$edit=$_POST['editable'],$urlRewrite="",$_POST['useAjax'],"","","",$_POST['rsNMRel'],$_POST['sortable']);
      }
      
      if($_POST['type']=="rsstr-modcat"){
        $sql="UPDATE `".$config_table_prefix."categorie` SET nome='".rawurldecode($_POST['newval'])."' WHERE id='".$_POST['id']."'";
        mysql_query($sql);
        
        $selected=$this->getSelected();
        if(count($selected)==0) $selected=$_POST['id']; 
        
        stampaStruttura(stripslashes(rawurldecode($_POST['name'])),$selected,$useLayout="",$cat="",$edit=$_POST['editable'],$urlRewrite="",$_POST['useAjax'],"","","",$_POST['rsNMRel'],$_POST['sortable']);  
      }
      
      if($_POST['type']=="rsstr-pubbl"){
        $sql="UPDATE `".$config_table_prefix."categorie` SET attivo='1' WHERE id='".$_POST['id']."'";
        mysql_query($sql);
        
        $struttura=stripslashes(rawurldecode($_POST['name']));
        $ramo=getStruttura($struttura,$_POST['id'],"",1);
        while (list($key, $row) = each($ramo)) {
          $sql="UPDATE `".$config_table_prefix."categorie` SET attivo='1' WHERE id='".$row."'";
          mysql_query($sql);    
        }

        $selected=$_POST['id'];
        if(count($selected)==0) $selected=$_POST['id']; 
        
        stampaStruttura($struttura,$selected,$useLayout="",$cat="",$edit=$_POST['editable'],$urlRewrite="",$_POST['useAjax'],"","","",$_POST['rsNMRel'],$_POST['sortable']);  
      }
      
      if($_POST['type']=="rsstr-notpubbl"){
        $struttura=stripslashes(rawurldecode($_POST['name']));
        
        $ramo=getStruttura($struttura,$_POST['id'],"",1);
        while (list($key, $row) = each($ramo)) {
          $sql="UPDATE `".$config_table_prefix."categorie` SET attivo='0' WHERE id='".$row."'";
          mysql_query($sql);    
        }
        
        $sql="UPDATE `".$config_table_prefix."categorie` SET attivo='0' WHERE id='".$_POST['id']."'";
        mysql_query($sql);
        
        $selected=$_POST['id'];
        if(count($selected)==0) $selected=$_POST['id']; 
        
        stampaStruttura($struttura,$selected,$useLayout="",$cat="",$edit=$_POST['editable'],$urlRewrite="",$_POST['useAjax'],"","","",$_POST['rsNMRel'],$_POST['sortable']);  
      }
      
      if($_POST['type']=="rsstr-addcat"){
        $sql="SELECT MAX(Ordinamento) as ord, MAX(id) as id FROM `".$config_table_prefix."categorie`";
        $q=mysql_query($sql);
        $tmprs=$objUtility->buildRecordset($q);
        $table="categorie";
        $sql1="(nome, url, Ordinamento)";
        $sql2="('New category','".($tmprs[0]['id']+1).".html','".($tmprs[0]['ord']+10)."')";
        
        rsTable2_BeforeInsert($table,$sql1,$sql2);
        $sql="INSERT INTO `".$config_table_prefix.$table."` ".$sql1." VALUES ".$sql2;
        
        mysql_query($sql);
        $nid=mysql_insert_id();
        
        if($_POST['id']>0){
          $sql="INSERT INTO `".$config_table_prefix."categorie#categorie_nm` (id_categorie, id_categorie_self) VALUES ('".$_POST['id']."','".$nid."')";
          mysql_query($sql);
        }else{
          $strutt=getTable("strutture","","nome='".stripslashes(rawurldecode($_POST['name']))."'");
          if(count($strutt)>0) {
            $sql="INSERT INTO `".$config_table_prefix."strutture#categorie_nm` (id_strutture, id_categorie) VALUES ('".$strutt[0]['id']."','".$nid."')";
            mysql_query($sql);
          }  
        }
        
        rsTable2_AfterInsert($table,$nid,"","");
        stampaStruttura(stripslashes(rawurldecode($_POST['name'])),$selected=$nid,$useLayout="",$cat="",$edit=$_POST['editable'],$urlRewrite="",$_POST['useAjax'],"","","",$_POST['rsNMRel'],$_POST['sortable']);
      }
      
      if($_POST['type']=="rsstr-sort"){
        $table="categorie";
        $sortable="Ordinamento";
        $tmpTable2=new rsTable2($table);
        $neworder=unserialize(stripslashes(rawurldecode($_POST['newval'])));
        $dropped=$this->getSelected();
        $struttura=stripslashes(rawurldecode($_POST['name']));
        
        $curRow=retRow($table,$dropped);
        $isusersys=isUserSystem();
        if($curRow['is_system']=="2" && !$isusersys) {
            stampaStruttura($struttura,$dropped,$useLayout="",$cat="",$edit=$_POST['editable'],$urlRewrite="",$_POST['useAjax'],"","","",$_POST['rsNMRel'],$_POST['sortable']);
            exit;
        }
        
        $padre_dropped=getPadre($dropped);
        $dropped_struttura=getStrutturaByNodo($dropped);
        
        if(!in_array($dropped, $neworder) && $dropped_struttura['nome']==$struttura) exit;
        
        $tmpTable2->neworder($neworder,$dropped,$table,$sortable);
        
        $tmporder=array();
        for($i=0;$i<count($neworder);$i++) {
          if($neworder[$i]!=$dropped) array_push($tmporder, $neworder[$i]);
        }
        
        $testa=$tmporder[0];
        $rs=getTable("strutture#categorie_nm","","id_categorie='".$testa."'");
        
        if($padre_dropped!==FALSE) {
          $sql="DELETE FROM `".$config_table_prefix."categorie#categorie_nm` WHERE (id_categorie='".$padre_dropped['id']."' AND id_categorie_self='".$dropped."')";
          mysql_query($sql);
        }else{
          $sql="DELETE FROM `".$config_table_prefix."strutture#categorie_nm` WHERE (id_categorie='".$dropped."')";
          mysql_query($sql);  
        }
        
        if(count($rs)==0) {
          $id_padre=getPadre($testa);
          $sql="INSERT INTO `".$config_table_prefix."categorie#categorie_nm` (id_categorie,id_categorie_self) VALUES ('".$id_padre['id']."','".$dropped."')";
          mysql_query($sql);    
        }else{
          $id_struttura=getStrutturaByNodo($testa);
          $sql="INSERT INTO `".$config_table_prefix."strutture#categorie_nm` (id_strutture,id_categorie) VALUES ('".$id_struttura['id']."','".$dropped."')";
          mysql_query($sql);    
        }
        
        stampaStruttura($struttura,$dropped,$useLayout="",$cat="",$edit=$_POST['editable'],$urlRewrite="",$_POST['useAjax'],"","","",$_POST['rsNMRel'],$_POST['sortable']);  
      }
      
      exit;
    }                            
  }
?>