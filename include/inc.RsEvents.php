<?
function rowsDel() {
  global $config_table_prefix;
  global $table;
  global $currentPage;
  global $objHtml;
  
  $chSel=$_SESSION[$table."checkSel"];
  for($z=0;$z<count($chSel);$z++) {
    $intId=$chSel[$z];
    $tdel=true;
    if(function_exists(str_replace($config_table_prefix,"", $table)."_before_delete")) eval("\$tdel=".str_replace($config_table_prefix,"", $table)."_before_delete('$intId');");
    
    if($tdel) {
      delete($intId);
    }
    
    if(function_exists(str_replace($config_table_prefix,"", $table)."_after_delete")) eval(str_replace($config_table_prefix,"", $table)."_after_delete('$intId');");
	}
	
  if ($strError) {
		$strEsito = "Attenzione<br/><br/>Si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta";
		$objHtml->adminPageRedirect($currentPage, $strEsito, "");
	} else {
		$strEsito = "Operazione eseguita correttamente";
		header("location:$currentPage#$table");
	}
}

function users_acquistiFind() {
  global $config_table_prefix;
  global $currentPage;
  
  $objUtility = new Utility;

  $table=$config_table_prefix."users";
  $cpage = $currentPage;
  
  ?><script>location.href = "<?=$cpage?>";</script><?
}

function acquisti_after_insert ($id) {
  global $config_table_prefix;
  $table=$config_table_prefix."acquisti";
  $rs=retRow("acquisti",$id);
  $rs2=retRow("magazzino_articoli",$rs['id_magazzino_articoli']);
  if($rs['prezzo_scontato_cry']==0) $rs['prezzo_scontato_cry']=($rs2['Prezzo_cry']);
  $sql="UPDATE `".$table."` SET user_hidden='".$_SESSION['ac_users_id']."', prezzo_cry='".($rs2['Prezzo_cry'])."', sconto='".(round((1-($rs['prezzo_scontato_cry'])/($rs2['Prezzo_cry'])), 2)*100)."', prezzo_scontato_cry='".$rs['prezzo_scontato_cry']."' WHERE id='$id' ";
  mysql_query($sql);
  unset($_SESSION['ac_users_id']);
}

function acquisti_after_update ($id) {
  global $config_table_prefix;
  $table=$config_table_prefix."acquisti";
  $rs=retRow("acquisti",$id);
  $rs2=retRow("magazzino_articoli",$rs['id_magazzino_articoli']);
  if($rs['prezzo_scontato_cry']==0) $rs['prezzo_scontato_cry']=($rs2['Prezzo_cry']);
  $sql="UPDATE `".$table."` SET prezzo_cry='".($rs2['Prezzo_cry'])."', sconto='".(round((1-($rs['prezzo_scontato_cry'])/($rs2['Prezzo_cry'])), 2)*100)."', prezzo_scontato_cry='".$rs['prezzo_scontato_cry']."' WHERE id='$id' ";
  mysql_query($sql);
}

function acquisti_sconto_ins_before_print () {
  return false;
}

function acquisti_sconto_upd_before_print () {
  return false;
}

function acquisti_prezzo_cry_ins_before_print () {
  return false;
}

function acquisti_prezzo_cry_upd_before_print () {
  return false;
}

function acquisti_after_print_tbody($sql) {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  $limit=$objUtility->sessionVarRead($config_table_prefix."acquistilimit");
  $rs=$objUtility->buildRecordset(mysql_query($sql));
  $prezzo_cry=0;
  while (list($key, $row) = each($rs)) {
    $prezzo_cry=$prezzo_cry+$row['prezzo_cry']+$row['aggiunte_cry'];
    $prezzo_scontato_cry=$prezzo_scontato_cry+$row['prezzo_scontato_cry'];
  }
  
  if($limit!="") {
    $rs=$objUtility->buildRecordset(mysql_query($sql.$limit));
    $prezzop_cry=0;
    while (list($key, $row) = each($rs)) {
      $prezzop_cry=$prezzop_cry+$row['prezzo_cry']+$row['aggiunte_cry'];
      $prezzop_scontato_cry=$prezzop_scontato_cry+$row['prezzo_scontato_cry'];
    }
  }
  ?>
  <?php if($limit!="") { ?>
    <? //echo $limit ?> 
    <tr><th colspan="11" style="border:0;background-color:white;">&nbsp;</th></tr>
    <tr><th colspan="1"></th><th colspan="7" style="height:26px;font-weight:bold;font-size:12px;text-align:right;">Totale lordo pagina:</th><th style="font-weight:bold;font-size:12px;color:red;text-align:right;"><?php echo round(0); ?> punti</th><th align="right" style="text-align:right;">&euro; <?php echo currencyITA($prezzop_cry); ?></th></tr>
    <tr><th colspan="1"></th><th colspan="7" style="height:26px;font-weight:bold;font-size:12px;text-align:right;">Sconto pagina:</th><th style="font-weight:bold;font-size:12px;color:red;text-align:right;"><?php echo round(0); ?> punti</th><th align="right" style="text-align:right;">&euro; <?php echo currencyITA($prezzop_scontato_cry-$prezzop_cry); ?></th></tr>
    <tr><th colspan="1"></th><th colspan="7" style="height:26px;font-weight:bold;font-size:12px;text-align:right;">Totale netto pagina:</th><th style="font-weight:bold;font-size:12px;color:red;text-align:right;"><?php echo round(0); ?> punti</th><th align="right" style="text-align:right;">&euro; <?php echo currencyITA($prezzop_scontato_cry); ?></th></tr>
  <? } ?>
    <tr><th colspan="11" style="border:0;"><hr style="border:0;border-bottom:1px white dashed;"></th></tr>
    <tr><th colspan="1"></th><th colspan="7" style="height:26px;font-weight:bold;font-size:12px;text-align:right;">Totale lordo complessivo:</th><th style="font-weight:bold;font-size:12px;color:red;text-align:right;"><?php echo round(0); ?> punti</th><th align="right" style="text-align:right;">&euro; <?php echo currencyITA($prezzo_cry); ?></th></tr>
    <tr><th colspan="1"></th><th colspan="7" style="height:26px;font-weight:bold;font-size:12px;text-align:right;">Sconto complessivo:</th><th style="font-weight:bold;font-size:12px;color:red;text-align:right;"><?php echo round(0); ?> punti</th><th align="right" style="text-align:right;">&euro; <?php echo currencyITA($prezzo_scontato_cry-$prezzo_cry); ?></th></tr>
    <tr><th colspan="1"></th><th colspan="7" style="height:26px;font-weight:bold;font-size:12px;text-align:right;">Totale netto complessivo:</th><th style="font-weight:bold;font-size:12px;color:red;text-align:right;"><?php echo round(0); ?> punti</th><th align="right" style="text-align:right;">&euro; <?php echo currencyITA($prezzo_scontato_cry); ?></th></tr>
    <?
}

function acquisti_before_print_cell ($key,$id) {
  if($key=="codice_vendita") {
    global $cell;
    
    $ordini=getTable("ecommerce_ordini","","codice_vendita='$cell'");
    
    $cell="<a href='rs_exec_functions.php?fun=printRecord&param1=ecommerce_ordini&param2=".$ordini[0]['id']."' rel='lyteframe' title='dettaglio ordine' rev='width: 700px; height: 460px; scrolling: auto; border: no;'>$cell</a>";
  }
  
  if($key=="id_magazzino_articoli") {
    $rs=retRow("acquisti",$id);
    $rs2=retRow("magazzino_articoli",$rs['id_magazzino_articoli']);
    
    $rs3=getTable("magazzino_articoli","","del_hidden=0 AND Codice='".$rs2['Codice']."'");
    
    if($rs2['del_hidden']=="1" && count($rs3)==0) return "text-decoration:line-through;color:red;";
    if($rs2['del_hidden']=="1" && count($rs3)>0) return "color:orange;";
    if($rs2['del_hidden']=="0" && count($rs3)>0) return "color:green;";
  }
  return "";
}

function acquisti_id_magazzino_articoli_ins_before_print () {
  ?><div class="label"><label for="articoli">articoli </label></div><?
  comboBox("magazzino_articoli",$field1="id_categorie_str_magazzino",$field2="",$selected="",$multiple="",$onchange="",$echoId="",$nome="id_magazzino_articoli",$where="(del_hidden='0'  AND Prezzo_cry<>'0.00')", $class="");
  return false;
}

function acquisti_id_magazzino_articoli_upd_before_print () {
  global $config_table_prefix;
  $objUtility = new Utility;
  $id=$objUtility->sessionVarRead("idmod");
  $rs=retRow("acquisti",$id);
  $rs2=retRow("magazzino_articoli",$rs['id_magazzino_articoli']);
  if($rs2['del_hidden']=="0") $sel=$rs['id_magazzino_articoli'];
  
  ?><div class="label"><label for="articoli">articoli </label></div><?
  comboBox("magazzino_articoli",$field1="id_categorie_str_magazzino",$field2="",$selected=$sel,$multiple="",$onchange="",$echoId="",$nome="id_magazzino_articoli",$where="(del_hidden='0' AND Prezzo_cry<>'0.00')", $class="");
  return false;
}

function acquisti_id_magazzino_articoli_search_before_print () {
  ?><div class="label"><label for="articoli">articoli </label></div><?
  comboBox("magazzino_articoli",$field1="id_categorie_str_magazzino",$field2="",$selected="",$multiple="",$onchange="",$echoId="",$nome="id_magazzino_articoli",$where="(del_hidden='0'  AND Prezzo_cry<>'0.00')", $class="");
  return false;
}

function users_selected_bar() {
  global $config_table_prefix;
  global $currentPage;
  
  $objUtility = new Utility;
  
  $table=$config_table_prefix."users";
  $cpage = $currentPage;
  
  ?><input type="image" name="act_BTN-CLICK_utentiEmail" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_email.png" alt="invia email/sms" title="invia email/sms" class="icoins"/><?php
  if(!(strpos($cpage, "acquisti_e_punteggio/acquisti.php")===false) || !(strpos($cpage, "acquisti_e_punteggio/analisi_vendite.php")===false)) {
    ?>
    <input type="image" name="act_BTN-CLICK_acquistiFind" src="<?php echo $objUtility->getPathBackofficeResources() ?>sales.png" alt="visualizza le vendite" title="visualizza le vendite" class="icoins"/>
    <?
  }
}

function users_utentiEmail() {
  global $config_table_prefix;
  global $currentPage;
  $objUtility = new Utility;
  $table=$config_table_prefix."users";
  $cpage = $currentPage;
  
  $chSel=$_SESSION[$config_table_prefix."userscheckSel"];
  if(count($chSel)>0) {
    ?>
    <form name="frm" id="frm" action="<?php echo $objUtility->getPathBackofficeAdmin(); ?>newsletter/newsletter_mail.php" method="post">
      <?php for($z=0;$z<count($chSel);$z++) { ?>
        <input type="hidden" name="cid[]" value="<?=$chSel[$z]?>" />
    	<? } ?>
      <?php $objUtility->sessionVarUpdate("newsletter_iduserslist", implode(";", $chSel));?>
  	</form>
  	<script>setTimeout("document.getElementById('frm').submit()",100);</script>
  	<?php
	} else {
    ?>
    <script>location.href = "<?=$cpage?>";</script>
    <?
  }
}

function ecommerce_ordini_codice_vendita_upd_before_print () {
  global $cell;
  ?><input type="hidden" value="<?=$cell?>" name="codice_vendita" readonly/><?
  
  return false;
}

function ecommerce_ordini_data_upd_before_print () {
  global $cell;
  $dd=explode("-", $cell);
  ?><input type="hidden" value="<?=$dd[0]?>" name="annodata" readonly/><?
  ?><input type="hidden" value="<?=$dd[1]?>" name="mesedata" readonly/><?
  ?><input type="hidden" value="<?=$dd[2]?>" name="giornodata" readonly/><?
  
  return false;
}

function ecommerce_ordini_ora_upd_before_print () {
  global $cell;
  ?><input type="hidden" value="<?=$cell?>" name="ora" readonly/><?
  
  return false;
}

function ecommerce_ordini_riepilogo_editor_upd_before_print () {
  global $cell;                       
  ?><textarea name="riepilogo_editor" readonly style="width:100%;height:300px;display:none" ><?=html_entity_decode($cell)?></textarea>
  <div style="text-align:left;height:300px;overflow:auto;padding-left:130px;"><?=$cell?></div>
  <?
  
  return false;
}

function ecommerce_ordini_before_print_cell ($key,$id) {
  global $cell;
  if($key=="id_ecommerce_stati") {
    $rs=retRow("ecommerce_stati",$cell);
    $cell="<div style='color:".$rs['colore'].";text-align:center;font-weight:bold;'>".$rs['stato']."</div>";
  }  
}

function magazzino_articoli_after_insert ($id) {
  session_start();
  global $config_table_prefix;
  $table=$config_table_prefix."magazzino_articoli";
  $sql="UPDATE `".$table."` SET id_categorie_str_magazzino='".$_SESSION['anagrafica_menid']."', aggiornato=CURDATE() WHERE id='$id' ";
  mysql_query($sql);
  unset($_SESSION['anagrafica_menid']);
}

function magazzino_articoli_id_categorie_str_magazzino_ins_before_print () {
  $objUtility = new Utility;
  $id = $objUtility->sessionVarRead("idmod");
  $rs=retRow("magazzino_articoli",$id);
  ?>
  <input type="hidden" name="id_categorie_str_magazzino" value="<?=$rs['id_categorie_str_magazzino']?>" />
  <?
  return false;
}

function magazzino_articoli_id_categorie_str_magazzino_upd_before_print () {
  $objUtility = new Utility;
  $id = $objUtility->sessionVarRead("idmod");
  $rs=retRow("magazzino_articoli",$id);
  ?>
  <input type="hidden" name="id_categorie_str_magazzino" value="<?=$rs['id_categorie_str_magazzino']?>" />
  <?
  return false;
}

function magazzino_articoli_before_delete($id) {
  global $config_table_prefix;
  $table=$config_table_prefix."magazzino_articoli";
  $sql="UPDATE `".$table."` SET del_hidden='1' WHERE id='$id' ";
  mysql_query($sql);
  return false;
}

function magazzino_articoli_before_update_db (&$id) {
  session_start();
  global $config_table_prefix;
  global $currentPage;
  global $objHtml;
  $table=$config_table_prefix."magazzino_articoli";
  
  /*
  if($_POST['Codice']) {
    $rs=getTable("magazzino_articoli","","(Codice='".$_POST['Codice']."' AND del_hidden='0' AND id<>'$id')");
    if(count($rs)>0) {
      $objHtml->adminPageRedirect($currentPage, "Codice articolo già esistente in magazzino", "");
      exit;
    }
  }
  */
  
  $rs=retRow("magazzino_articoli",$id);
  $sql="INSERT INTO `".$table."` (Codice,id_categorie_str_magazzino,Descr1,Descr2,IVA,Um1,Um2,Confez,Costo_cry,Prezzo_cry,Note,del_hidden,aggiornato,Fornitore) 
        VALUES ('".addslashes($rs['Codice'])."','".$rs['id_categorie_str_magazzino']."','".addslashes($rs['Descr1'])."','".addslashes($rs['Descr2'])."','".$rs['IVA']."','".addslashes($rs['Um1'])."',
                '".addslashes($rs['Um2'])."','".addslashes($rs['Confez'])."','".$rs['Costo_cry']."','".$rs['Prezzo_cry']."','".addslashes($rs['Note'])."','".$rs['del_hidden']."',CURDATE(),'".$rs['Fornitore']."')";

  $rs=retRow("magazzino_articoli",$id);
  $rs2=retRow("categorie",$rs['id_categorie_str_magazzino']);
  $sql1="UPDATE `".$table."` SET del_hidden='1' WHERE id='$id' ";
  mysql_query($sql1);
  
  
  $query=mysql_query($sql);
  
  $sql2="SELECT MAX(id) FROM `$table`";
  $rs=mysql_query($sql2);
  $row=mysql_fetch_array($rs);
  $nid=$row[0];
  
  $id=$nid;
  
  return true;
}

function magazzino_articoli_after_update ($id) {
  global $config_table_prefix;
  $table=$config_table_prefix."magazzino_articoli";
  
  $sql="UPDATE `".$table."` SET aggiornato=CURDATE() WHERE id='$id' ";
  mysql_query($sql);  
}

function magazzino_articoli_aggiornato_upd_before_print () {
  return false;
}

function magazzino_articoli_aggiornato_ins_before_print () {
  return false;
}

function categorie_before_delete($id) {
  global $config_table_prefix;
  global $table;
  global $currentPage;
  $objUtility = new Utility;
  $table=$config_table_prefix."categorie"; 
  $mag=getStruttura("magazzino");
  if(in_array($id, $mag)!==false) {
    $rs=retRow("categorie",$id);
    $table2=$config_table_prefix."magazzino_articoli"; 
    $sql="UPDATE `".$table2."` SET del_hidden='1',Codice=Codice WHERE id_categorie_str_magazzino='$id' ";
    mysql_query($sql);
  }
  
  return true;
}

function categorie_after_insert ($id) {
  global $config_table_prefix;
  global $table;
  global $currentPage;
  $objUtility = new Utility;
  $table=$config_table_prefix."categorie"; 
  $mag=getStruttura("magazzino");
  if(in_array($id, $mag)!==false) {
    $table2=$config_table_prefix."magazzino_articoli"; 
    $sql2="SELECT MAX(id) FROM `$table2`";
    $rs=mysql_query($sql2);
    $row=mysql_fetch_array($rs);
    
    $sql2="INSERT INTO `$table2` (Codice,id_categorie_str_magazzino,aggiornato) VALUES ('".($row[0]+1)."','$id',CURDATE())";
    $rs=mysql_query($sql2);
  }
}

function form_archivio_offerte_before_print_cell($key,$id) {
  if($key=="fornitore") {
    global $cell;
    
    $rs=retRow("form_archivio_offerte",$id);
    $iduser=$rs['idfornitore_hidden'];
    $rs2=getTable("users","","id='$iduser'");
    if(count($rs2)>0) {
      $cell="<a href='rs_exec_functions.php?fun=printRecord&param1=users&param2=$iduser' rel='lyteframe' title='Scheda fornitore' rev='width: 700px; height: 460px; scrolling: auto; border: no;'>$cell</a>";
    } else {
      $cell="<span style='color:red;text-decoration: line-through;'>$cell</span>"; 
    }
  } 
}

function ecommerce_caratteristiche_after_print_hidden_input($insupd) {
  $objUtility = new Utility;
  
  if($insupd==0) {
    $objUtility->sessionVarUpdate("ecomm_cat", $_GET['menid']); 
    ?><input name="menid" type="hidden" value="<?php echo $_GET['menid']; ?>" /><?
  } 
  
  if($insupd==1) {
    $tmpcat=$objUtility->sessionVarRead("ecomm_cat"); 
    ?><input name="menid" type="hidden" value="<?php echo $tmpcat; ?>" /><?
  } 
}

function ecommerce_caratteristiche_after_insert($id) {
  global $config_table_prefix;
  
  $tmpcat=$_POST['menid'];
  $sql="UPDATE ".$config_table_prefix."ecommerce_caratteristiche SET idcategorie_hidden='$tmpcat' WHERE id='$id'";
  mysql_query($sql);
}

function ecommerce_caratteristiche_before_print_cell($key,$rowid) {
  global $cell;
  if($key=="nome") {
    $cell="<a href='?menid=".$_GET['menid']."&idcaratt=".$rowid."' >$cell</a>";
  }
}

function ecommerce_valori_after_print_hidden_input($insupd) {
  $objUtility = new Utility;
  
  if($insupd==0) {
    $objUtility->sessionVarUpdate("ecomm_idcaratt", $_GET['idcaratt']); 
    ?><input name="idcaratt" type="hidden" value="<?php echo $_GET['idcaratt']; ?>" /><?
  } 
  
  if($insupd==1) {
    $tmpcat=$objUtility->sessionVarRead("ecomm_idcaratt"); 
    ?><input name="idcaratt" type="hidden" value="<?php echo $tmpcat; ?>" /><?
  } 
}

function ecommerce_valori_after_insert($id) {
  global $config_table_prefix;
  
  $tmpcat=$_POST['idcaratt'];
  $sql="UPDATE ".$config_table_prefix."ecommerce_valori SET idcaratteristiche_hidden='$tmpcat' WHERE id='$id'";
  
  mysql_query($sql);
}

function rsTable2_BeforeDelete(&$table,&$id,&$tblParent,&$parentId) {
  global $config_table_prefix;
  
  if($table=="magazzino_articoli") {
    deleteArticolo($id);
    $id="-1"; 
  } 
  
  if($table=="categorie") {
    deleteArticoloFromCat($id);  
  } 
  
  if($table=="roles") {
    $objUsers = new Users;
    $objConfig = new ConfigTool();
    $objDb = new Db;
    $iduser=$_SESSION["user_id"];
    
    $conn = $objDb->connection($objConfig);
    $isSystem=$objUsers->isSystem($conn, $iduser);
    
    $roles=retRow("roles",$id);
    
    $users_list=$objUsers->rolesGetUsers($conn,$id);
    while (list($key, $row) = each($users_list)) {
        if($row['id']==$iduser) {
            $id="-1";
            echo ln("L'utente corrente appartiene a questo gruppo. Impossibile eliminarlo.");
            exit;    
        }   
    }
    
    if($roles['issystem']!="0" && !$isSystem) {
      $id="-1";
      echo ln("Il gruppo selezionato è di sistema. Impossibile eliminarlo.");
      exit;
    }  
  }
}

function rsTable2_AfterDelete($table,$old,$tblParent,$parentId) {
  global $config_table_prefix;
  $objUtility = new Utility;
  $objUsers = new Users;
  
  $objUsers->getCurrentUser($intIdutente, $strUsername);
  if($intIdutente>0){
    $sql="UPDATE ".$config_table_prefix."storico_users SET `deleted`= 1 , `ultimo_aggiornamento`= NOW() WHERE (`table`='".$table."' AND `row`='".$old['id']."')"; 
    mysql_query($sql);
    $nid=mysql_insert_id();
  }
  
  if($table=="users") {
    deleteUser($old['id']); 
  }
  
  if($table=="roles") {
    deleteGroup($old['id']); 
  }
  
  if($table=="users_list") {
    if($parentId>0 && $tblParent=="roles") {
      $gua=getTable("gestione_utenti_autonoma","","id_users='".$old['id_users']."'");
      $gua=$gua[0]['id'];
      
      $sql="DELETE FROM `".$config_table_prefix."roles#users_list_nm` WHERE (id_roles='".$parentId."' AND id_users_list='".$old['id']."')";
      mysql_query($sql);
      
      $roles_list2=getTable("roles_list","","id_roles='".$parentId."'");
      while (list($key, $row) = each($roles_list2)) {
        $sql="DELETE FROM `".$config_table_prefix."users#roles_list_nm` WHERE (id_users='".$old['id_users']."' AND id_roles_list='".$row['id']."')";
        mysql_query($sql);     
      }
      
      $roles_list=Table2ByTable1("gestione_utenti_autonoma","roles_list",$gua,"","");
      while (list($key, $row) = each($roles_list)) {
        if($row['id_roles']==$parentId){
          $sql="DELETE FROM `".$config_table_prefix."roles_list` WHERE id='".$row['id']."'";
          mysql_query($sql);
          
          $sql="DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` WHERE id_roles_list='".$row['id']."'";
          mysql_query($sql);
        }  
      }    
    }
  }
  
  if($table=="roles_list") {
    if($parentId>0 && $tblParent=="users") {
      $gua=getTable("gestione_utenti_autonoma","","id_users='".$parentId."'");
      $gua=$gua[0]['id'];
      
      $sql="DELETE FROM `".$config_table_prefix."users#roles_list_nm` WHERE (id_users='".$parentId."' AND id_roles_list='".$old['id']."')";
      mysql_query($sql);
      
      $users_list2=getTable("users_list","","id_users='".$parentId."'");
      while (list($key, $row) = each($users_list2)) {
        $sql="DELETE FROM `".$config_table_prefix."roles#users_list_nm` WHERE (id_roles='".$old['id_roles']."' AND id_users_list='".$row['id']."')";
        mysql_query($sql);     
      }
      
      $roles_list=Table2ByTable1("gestione_utenti_autonoma","roles_list",$gua,"","");
      while (list($key, $row) = each($roles_list)) {
        if($row['id_roles']==$old['id_roles']){
          $sql="DELETE FROM `".$config_table_prefix."roles_list` WHERE id='".$row['id']."'";
          mysql_query($sql);
          
          $sql="DELETE FROM `".$config_table_prefix."gestione_utenti_autonoma#roles_list_nm` WHERE id_roles_list='".$row['id']."'";
          mysql_query($sql);
        }  
      }    
    }
  }
  
  if($table=="ecommerce_abbinamenti") {
    if($parentId>0 && $tblParent=="ecommerce_valori") {
      $valori=retRow("ecommerce_valori",$parentId);
      $caratt=$valori['idcaratteristiche_hidden'];
      
      $abb=getTable("ecommerce_abbinamenti","","(id_ecommerce_valori='".$parentId."' AND id_ecommerce_caratteristiche='".$caratt."' AND id_categorie='".$old['id_categorie']."')");
      $sql="DELETE FROM `".$config_table_prefix."ecommerce_abbinamenti` WHERE id='".$abb[0]['id']."'";
      mysql_query($sql);
      
      $sql="DELETE FROM `".$config_table_prefix."ecommerce_valori#ecommerce_abbinamenti_nm` WHERE id_ecommerce_abbinamenti='".$abb[0]['id']."'";
      mysql_query($sql);
    }    
  }
  
  if($table=="magazzino_articoli_collegati") {
    if($parentId>0 && $tblParent=="categorie") {
      $nm=Table2ByTable1("categorie","magazzino_articoli_collegati",$old['id_categorie_str_magazzino'],"","");
      while (list($key, $row) = each($nm)) {
        if($row['id_categorie_str_magazzino']==$parentId) {
          $sql="DELETE FROM `".$config_table_prefix."magazzino_articoli_collegati` WHERE id='".$row['id']."'";
          mysql_query($sql);
          
          $sql="DELETE FROM `".$config_table_prefix."categorie#magazzino_articoli_collegati_nm` WHERE id_magazzino_articoli_collegati='".$row['id']."'";
          mysql_query($sql);
        }  
      }
    }  
  }    
}

function rsTable2_BeforePrintTable($name,&$table) {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  if($name=="magazzino_articoli") {
    $table2=array();
    while (list($key, $row) = each($table)) {
      if($row['del_hidden']=="0") {
        array_push($table2, $row);
        break;
      }  
    }
    $table=$table2;
    reset($table);  
  }
  
  if($name=="users") {
    if(isset($_SESSION["user_id"]) && isset($_SESSION["user_login"])) {
      $table2=array();
      $gua=getTable("gestione_utenti_autonoma","","id_users='".$_SESSION["user_id"]."'");
      if($gua[0]['vede_tutti']==0) {
        $gua=$gua[0]['id'];
        $users_list=Table2ByTable1("gestione_utenti_autonoma","users_list",$gua,"","");
        while (list($key, $row) = each($table)) {
          reset($users_list);
          while (list($key1, $row1) = each($users_list)) {
            if($row1['id_users']==$row['id']) {
              array_push($table2, $row);
              break;
            }  
          }      
        }
        $table=$table2;
      }
    }else{
      $table=array();
    }
    
    reset($table);
  }
  
  if($name=="roles") {
    if(isset($_SESSION["user_id"]) && isset($_SESSION["user_login"])) {
      $table2=array();
      $gua=getTable("gestione_utenti_autonoma","","id_users='".$_SESSION["user_id"]."'");
      if($gua[0]['vede_tutti']==0) {
        $gua=$gua[0]['id'];
        $roles_list=Table2ByTable1("gestione_utenti_autonoma","roles_list",$gua,"","");
        while (list($key, $row) = each($table)) {
          reset($roles_list);
          while (list($key1, $row1) = each($roles_list)) {
            if($row1['id_roles']==$row['id']) {
              array_push($table2, $row);
              break;
            }  
          }      
        }
        $table=$table2;
      }
    }else{
      $table=array();
    }
    
    reset($table);
  }    
}

function rsTable2_BeforeProcess(&$table,&$tableId,&$tblParent,&$parentId,&$rowid) {
  global $config_table_prefix;
  
  if($table=="users" && !($rowid>0)) {
    $login=getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$table."' AND campo_hidden='login')");
    $login=permissionField($login);
    
    $login=$login[0]['id'];
    $login=$_POST[$login];
    
    $nome=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$table."' AND campo_hidden='nome')"));
    $nome=$nome[0]['id'];
    $nome=$_POST[$nome];
    
    $cognome=getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$table."' AND campo_hidden='cognome')");
    $cognome=permissionField($cognome);
    
    $cognome=$cognome[0]['id'];
    $cognome=$_POST[$cognome];
    
    $email=getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$table."' AND campo_hidden='email')");
    $email=permissionField($email);
    $email=$email[0]['id'];
    $email=$_POST[$email];      
    
    $rs=getTable("users","","login='$login' AND nome='$nome' AND cognome='$cognome' AND (ultimoaccesso IS NULL OR ultimoaccesso='0000-00-00 00:00:00')");
    if(count($rs)>0) {
      deleteUser($rs[0]['id']);
    }
    
    $rs=getTable("users","","email='$email' AND (ultimoaccesso IS NULL OR ultimoaccesso='0000-00-00 00:00:00')");
    if(count($rs)>0) {
      deleteUser($rs[0]['id']);
    }
  }    
}

function rsTable2_BeforeUpdate(&$table,&$rowid,&$tblParent,&$parentId,&$sqlupd,&$sql1,&$sql2) {
  global $config_table_prefix;
  
  if($table=="magazzino_articoli") { 
    $campi=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix."magazzino_articoli' AND campo_hidden='Codice')"));
    $k=$campi[0]['id'];
    
    /*
    if(!isValidCodArt($rowid,$_POST[$k])) {
      $objTable2=new rsTable2();
      $objTable2->msgbox_error("<div class='rsTable2-obligatory' rsField='".$k."'>".ln("'Il CODICE scelto non è disponibile. Si prega di scegliere un altro valore.")."</div>");
      exit;
    }
    */
    
    $rowid=updateArticolo($rowid);
  }     
}

function rsTable2_AfterUpdate($table,$rowid,$tblParent,$parentId,$old) {
  global $config_table_prefix;
  $objConfig = new ConfigTool();
  $objUtility = new Utility;
  $objMailing = new Mailing; 
  $objUsers = new Users;
  
  $objUsers->getCurrentUser($intIdutente, $strUsername);
  if($intIdutente>0){
    $domain=getTema();
    //$sql="UPDATE ".$config_table_prefix."storico_users SET `ultimo_aggiornamento`= NOW() WHERE (`table`='".$table."' AND `row`='".$rowid."')"; 
    $sql="INSERT INTO ".$config_table_prefix."storico_users (`id_users`,`domain`,`username`,`table`,`row`,`deleted`,`data_creazione`,`ultimo_aggiornamento`) VALUES ('".$intIdutente."','".$domain."','".$strUsername."','".$table."','".$rowid."',0,NOW(),NOW()) ON DUPLICATE KEY UPDATE `id_users`= '".$intIdutente."',`domain`= '".$domain."',`username`= '".$strUsername."',`table`= '".$table."',`row`= '".$rowid."',`deleted`= 0,`ultimo_aggiornamento`= NOW();";
    mysql_query($sql);
    $nid=mysql_insert_id();
  }
  
  if($table=="dizionario") {
    $str=retRow($table,$rowid);
    $txtStr=rip_tags(html2text($str['testo_editor']));
    $sql="UPDATE ".$config_table_prefix."dizionario SET testo_editor='".addslashes($txtStr)."', kmd5='".md5($txtStr)."' WHERE id='".$rowid."'";
    mysql_query($sql);
  }
  
  if($table=="ecommerce_ordini") {
    $ordine=retRow("ecommerce_ordini",$rowid);
    if($ordine['id_ecommerce_stati']!=$old['id_ecommerce_stati']){
      $stato=retRow("ecommerce_stati",$ordine['id_ecommerce_stati']);
      $user=retRow("users",$ordine['user_hidden']);
      $email=$user['email'];
      
      $doc=retRow("documents",$ordine["documents_hidden"]);
      $oggetto=retRow("oggetti",$doc["idoggetti"]);
      if($oggetto){
        $newname=strtoupper($stato['stato']." - ".$oggetto['originalname']);
        $sql="UPDATE ".$config_table_prefix."oggetti SET originalname='".addslashes($newname)."' WHERE id='".$oggetto['id']."'";
        mysql_query($sql);
      }
      
      if($email!="") {
        $mess=getTable("ecommerce_testi","","(nome='messaggio aggiornamento stato ordine' AND attivo='1')");
        $mess=$mess[0]['testo_editor'];
        $mess=str_replace("#STATO#", strtoupper($stato['stato']), $mess);
        $mess=str_replace("#ORDINE#", $ordine['codice_vendita'], $mess);
        $mess=replaceEcomerceMarkers($mess);
        
        $objMailing->mmail($email,$objConfig->get("email-from"),ln("Cambio STATO dell'ordine n. ").$ordine['codice_vendita'],$mess,"","","");
      }
    } 
  }
  
  if($table=="magazzino_articoli_collegati") {
    if($parentId>0 && $tblParent=="categorie") {
      $newVal=retRow("magazzino_articoli_collegati",$rowid);
      $nm=Table2ByTable1("categorie","magazzino_articoli_collegati",$old['id_categorie_str_magazzino'],"","");
      while (list($key, $row) = each($nm)) {
        if($row['id_categorie_str_magazzino']==$parentId) {
          $sql="UPDATE `".$config_table_prefix."categorie#magazzino_articoli_collegati_nm` SET id_categorie='".$newVal['id_categorie_str_magazzino']."' WHERE id_magazzino_articoli_collegati='".$row['id']."'";
          mysql_query($sql);
        }  
      }
    }  
  }
  
  if(function_exists(rs_AfterUpdate)) rs_AfterUpdate($table,$rowid,$tblParent,$parentId,$old);        
}

function rsTable2_BeforeInsert(&$table,&$sql1,&$sql2) {
  return;  
}

function rsTable2_AfterInsert($table,$insert_id,$parentId,$tblParent) {
  global $config_table_prefix;
  $objUtility = new Utility;
  $objUsers = new Users;
  
  
  $objUsers->getCurrentUser($intIdutente, $strUsername);
  if($intIdutente>0){
    $domain=getTema();
    $sql="INSERT INTO ".$config_table_prefix."storico_users (`id_users`,`username`,`table`,`domain`,`row`,`ultimo_aggiornamento`,`deleted`) VALUES ('".$intIdutente."','".$strUsername."','".$table."','".$domain."','".$insert_id."', NOW(), 0)"; 
    mysql_query($sql);
    $nid=mysql_insert_id();
  }
  
  
  if($table=="dizionario") {
    $str=retRow($table,$insert_id);
    $txtStr=rip_tags(html2text($str['testo_editor']));
    $sql="UPDATE ".$config_table_prefix."dizionario SET testo_editor='".addslashes($txtStr)."', kmd5='".md5($txtStr)."' WHERE id='".$insert_id."'";
    mysql_query($sql);
  }
  
  if($table=="categorie") {
    addArticoloFromCat($insert_id);
  }
  
  if($table=="ecommerce_caratteristiche") {
    $mag=getStrutturaByNodo($parentId);
    if($tblParent=="categorie" && $mag['nome']=="magazzino") {
      $sql="UPDATE ".$config_table_prefix."ecommerce_caratteristiche SET idcategorie_hidden='$parentId' WHERE id='$insert_id'";
      mysql_query($sql);
    }
  }
  
  if($table=="ecommerce_valori") {
    if($tblParent=="ecommerce_caratteristiche") {
      $sql="UPDATE ".$config_table_prefix."ecommerce_valori SET idcaratteristiche_hidden='$parentId' WHERE id='$insert_id'";
      mysql_query($sql);
    }
  } 
  
  if($table=="users") {
    initUser($insert_id); 
  }
  
  if($table=="roles") {
    initGroup($insert_id);
  }
  
  if($table=="users_list") {
    if($parentId>0 && $tblParent=="roles") {
      $users=retRow("users_list",$insert_id);
      AddUsersToRoles($users['id_users'],$parentId);    
    }
  }
  
  if($table=="roles_list") {
    if($parentId>0 && $tblParent=="users") {
      $roles=retRow("roles_list",$insert_id);
      AddRolesToUsers($parentId,$roles['id_roles']);    
    }
  }
  
  if($table=="ecommerce_abbinamenti") {
    if($parentId>0 && $tblParent=="ecommerce_valori") {
      $valori=retRow("ecommerce_valori",$parentId);
      $caratt=$valori['idcaratteristiche_hidden'];
      
      $row=retRow("ecommerce_abbinamenti",$insert_id);
      
      $sql="INSERT INTO `".$config_table_prefix."ecommerce_abbinamenti` (id_categorie,id_ecommerce_caratteristiche,id_ecommerce_valori,attivo) VALUES ('".$row['id_categorie']."','".$caratt."','".$parentId."',1)"; 
      mysql_query($sql);
      $nid=mysql_insert_id();
      
      $sql="INSERT INTO `".$config_table_prefix."ecommerce_valori#ecommerce_abbinamenti_nm` (id_ecommerce_valori,id_ecommerce_abbinamenti) VALUES ('".$row['id_ecommerce_valori']."','".$nid."')";
      mysql_query($sql);
    }    
  }
  
  if($table=="magazzino_articoli_collegati") {
    if($parentId>0 && $tblParent=="categorie") {
      $coll=retRow("magazzino_articoli_collegati",$insert_id);
      $maxord=getTable("magazzino_articoli_collegati","Ordinamento DESC","");
      $sql="INSERT INTO `".$config_table_prefix."magazzino_articoli_collegati` (id_categorie_str_magazzino,Ordinamento,attivo) VALUES ('".$parentId."','".($maxord[0]['Ordinamento']+10)."',1)";        
      mysql_query($sql);
      $nid=mysql_insert_id();
      
      $sql="INSERT INTO `".$config_table_prefix."categorie#magazzino_articoli_collegati_nm` (id_categorie,id_magazzino_articoli_collegati) VALUES ('".$coll['id_categorie_str_magazzino']."','".$nid."')";
      mysql_query($sql);
    }  
  }
  
  if(function_exists(rs_AfterInsert)) rs_AfterInsert($table,$insert_id); 
}

function rsTable2_BeforePrintLinkNM(&$table,&$tableId,&$nm_table,&$nm_link,&$key) {
  if($table=="categorie") {
    $mag=getStrutturaByNodo($tableId);
    if($mag['nome']!="magazzino" && $nm_table=="magazzino_articoli") {
      $nm_link="";  
    }
    
    if($mag['nome']!="magazzino" && $nm_table=="ecommerce_caratteristiche") {
      $nm_link="";  
    }
  }
  
  if(function_exists(rs_BeforePrintLinkNM)) rs_BeforePrintLinkNM($table,$tableId,$nm_table,$nm_link,$key);  
}

function rsTable2_BeforePrintTD(&$table,&$fun,&$field,&$col,&$tid,&$mod,&$modrap) {
  global $config_table_prefix;
  $objUtility = new Utility;
  
  if($table=="roles") {
    $objUsers = new Users;
    $objConfig = new ConfigTool();
    $objDb = new Db;
    $iduser=$_SESSION["user_id"];
    
    $conn = $objDb->connection($objConfig);
    $isSystem=$objUsers->isSystem($conn, $iduser);
    
    $roles=retRow("roles",$tid);
    
    $users_list=$objUsers->rolesGetUsers($conn,$tid);
    while (list($key, $row) = each($users_list)) {
        if($row['id']==$iduser) {
            $mod=0;
            $modrap=0;   
        }   
    }
    
    if($roles['issystem']!="0" && !$isSystem) {
      $mod=0;
      $modrap=0;      
    }  
  }
  
  if($table=="ecommerce_ordini" && $col=="id_ecommerce_stati") {
    $stato=retRow("ecommerce_stati",$field);
    $field="<div style='font-weight:bold;color:".$stato['colore'].";text-align:center;'>".$stato['stato']."</div>";
    $fun=false;
  }
  
  if($table=="ecommerce_abbinamenti" && $col=="id_categorie") {
    $art=retRow("categorie",$field);
    $field="<div style='font-weight:bold;color:red;text-align:center;'>".$art['nome']."</div>";
    $fun=false;  
  }
  
  if($table=="ecommerce_caratteristiche" && $col=="id_ewiz_caratteristiche_list") {
    $art=retRow("ewiz_caratteristiche_list",$field);
    $field="<div class='ewiz-caratteristica' id='".$tid."' rsLabel='".addslashes($art['nome'])."'>".$art['nome']."</div>";
    $fun=false;  
  }
  
  if($table=="ecommerce_valori" && $col=="nome") {
    $art=retRow("ewiz_caratteristiche_list",$field);
    $field="<div class='ewiz-valore-nome' id='".$tid."' rsLabel='".addslashes($field)."'>".$field."</div>";
    $fun=false;  
  }
  
  if($table=="magazzino_articoli_collegati" && $col=="id_categorie_str_magazzino") {
    $art=retRow("categorie",$field);
    $field="<div style='font-weight:bold;color:green;text-align:center;'>".$art['nome']."</div>";
    $fun=false;  
  }
}

function rsTable2_BeforePrintInsertRow($table,&$fun,&$arr_col,&$col_title,&$col_id,&$obbl,&$unique,&$defValue,&$rowid,$parentid) {
  if($table=="categorie" && $arr_col['campo_hidden']=="id_gestione_layout") { 
    $objUsers = new Users;
    $objConfig = new ConfigTool();
    $objDb = new Db;
    $iduser=$_SESSION["user_id"];
    
    $conn = $objDb->connection($objConfig);
    $isSystem=$objUsers->isSystem($conn, $iduser);
    
    $wh="";
    if(!$isSystem) $wh="id_users='".$iduser."'";
    
    $layout=getTable("gestione_layout","",$wh);
    ?>
    <!-- Module 2A -->
    <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $col_id; ?>">
      <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
        <div class="ez-box"><label title="<?php echo $col_title; ?>"><?php echo ln($col_title); ?></label></div>
      </div>
      <div class="ez-last ez-oh rsTable2-insert-row-r">
        <div class="ez-box">
          <select name="<?php echo $col_id; ?>[]" rsFilter="" rsRowId="<?php echo $rowid; ?>" rsTable="<?php echo $table; ?>">
      			<option value="-1"></option>							
      			<?php                                          
            while (list($key, $row) = each($layout)) { ?>
              <option value="<?php echo $row['id'] ?>" <?php if($defValue==$row['id']) echo "SELECTED"; ?>>
                <?php echo $row['nome']; ?>
              </option>
            <? } ?>
          </select>  
        </div>
      </div>
    </div>
    <?php
    $fun=-1;   
  }

  if($table=="ecommerce_abbinamenti" && $arr_col['campo_hidden']=="id_categorie") {
    $caratteristiche=Table1ByTable2("ecommerce_caratteristiche","ecommerce_valori",$parentid,"","");
    $categorie=Table1ByTable2("categorie","ecommerce_caratteristiche",$caratteristiche[0]['id'],"","");
    $defValue=$categorie[0]['id'];
    
    ?>
    <script>
    $(document).ready(function(){
      <?php if($rowid=="0") { ?>
        $("select[name='<?php echo $col_id; ?>[]']").trigger("change");
      <? } ?>
      $("select[name='<?php echo $col_id; ?>[]']").parents("div.rsTable2-insert-row").hide();  
    });
    </script>
    <?php
  }  
}
?>