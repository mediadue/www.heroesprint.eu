<?php
  Class rsTable2 {
    var $g_table;
    var $g_id;
    var $g_config_table_prefix;
    var $cell;
    var $g_printSearch;
    var $g_objPag;
    var $g_nCharTD;
    var $g_nCharSearch;
    var $g_where;
    var $g_colFilter;
    var $g_tblParent;
    var $g_parentId;
    var $g_showAll;
    var $g_sort;
    var $g_permessi;
    var $obbl_label;
    var $unique_label;
    var $g_optionsSer;
    var $g_col_limit;
    
    function rsTable2($table="",$id="",$tblParent="",$parentId="",$sorting="",$colLimit=13) {
      global $config_table_prefix;
      
      $obbl_label=false;
      $unique_label=false;
      
      $this->g_nCharTD=180;
      if($table=="") return;
      
      $this->g_table=$table;
      $this->g_id=$id;
      $this->g_tblParent=$tblParent;
      $this->g_parentId=$parentId;            
      
      $this->g_col_limit=$colLimit;
                                            
      $this->g_objPag=new Paginazione();
      $this->g_sort=$sorting;
      if(!is_array($this->g_sort)) $this->g_sort=array();
                    
      $this->g_objPag->JSonPageChange("rsTable2PageChange");
      $this->g_objPag->setOptions("","rsTable2tab".$this->g_id,20,"div.rsTable2-tab-".$this->g_table."[rsTableId=".$this->g_id."]",-1,"rsAction.php?rsTable2Action=1&tabName=".rawurlencode($this->g_table)."&tabId=".$this->g_id."&tblParent=".rawurlencode($this->g_tblParent)."&parentId=".$this->g_parentId."&where=".rawurlencode($this->g_where)."&sorting=".rawurlencode(serialize($this->g_sort)));
      if($_POST['tabSearch']) $this->g_objPag->currentPage(1);
      if(!is_array($_SESSION["rsTable2_".$this->g_table."_selection"])) $_SESSION["rsTable2_".$this->g_table."_selection"]=array();
      
      $tmpTab=getTable("rstbl2_tabelle","","tabella='".$config_table_prefix.$this->g_table."'");
      $this->g_permessi=Table2ByTable1("rstbl2_tabelle","rstbl2_permessi",$tmpTab[0]['id'],"","");
      $this->g_permessi=$this->g_permessi[0];
      if($this->g_permessi['Inserimento']=="") $this->g_permessi['Inserimento']="1";
      if($this->g_permessi['Modifica']=="") $this->g_permessi['Modifica']="1";
      if($this->g_permessi['Modifica_rapida']=="") $this->g_permessi['Modifica_rapida']="1";
      if($this->g_permessi['Cancellazione']=="") $this->g_permessi['Cancellazione']="1";
      if($this->g_permessi['Relazioni']=="") $this->g_permessi['Relazioni']="1";
    }                            
    
    function refreshSelection($table,$selection) {
      global $config_table_prefix;
      
      if(!is_array($_SESSION[$table."checkSel"])) $_SESSION[$table."checkSel"]=array();
      $chSel=$_SESSION[$table."checkSel"];
      if($selection) {
        for($z=0;$z<count($selection);$z++) {
          $vv=explode("_", $selection[$z]);
          if(in_array($vv[0], $chSel)) {
            if($vv[1]=="false") {
              for($j=0;$j<count($chSel);$j++) {
                if($chSel[$j]==$vv[0]) $chSel[$j]="y";  
              }
            }
          } else {
            if($vv[1]=="true") {
              array_push($chSel, (int) $vv[0]);
            }  
          }
        }
        $chSel=array_filter($chSel, "is_numeric");
        $chSel=array_values($chSel);
        
        while (list($key, $row) = each($chSel)) {
          $sql="SELECT id FROM `".$config_table_prefix.$table."` WHERE id='".$chSel[$key]."'";
          $q=mysql_query($sql);  
          if(mysql_num_rows($q)==0) unset($chSel[$key]);
        }
        
        reset($chSel);
        $_SESSION[$table."checkSel"]=$chSel;
        $_SESSION["rsTable2_".$table."_selection"]=$chSel;
        echo $table."#_RS_#".urlencode(serialize($chSel));
      }
    }
    
    function dsort($i,$neworder,$dropped,$table,$sortable) {
      global $config_table_prefix;
      
      $next=$neworder[($i+1)];
      $next_ord=retRow($table,$next);
      $prev=$neworder[($i-1)];
      $prev_ord=retRow($table,$prev);
      $dropped_ord=retRow($table,$dropped);
      
      $oldorder_r=getTable($table,$sortable." ASC","(".$sortable.">='".$next_ord[$sortable]."' AND ".$sortable."<='".$dropped_ord[$sortable]."')");
       
      if(count($oldorder_r)>0 && isset($next_ord[$sortable])){
        $sql="UPDATE `".$config_table_prefix.$table."` SET ".$sortable."='".$next_ord[$sortable]."' WHERE id='".$dropped."'"; 
        mysql_query($sql);
        
        while (list($key, $row) = each($oldorder_r)) {
          if($row['id']!=$dropped) {
            $ordinamento=retRow($table,$row['id']);
            $sql="UPDATE `".$config_table_prefix.$table."` SET ".$sortable."='".($ordinamento[$sortable]+10)."' WHERE id='".$row['id']."'";
            
            mysql_query($sql);
          }    
        }  
      }elseif(count($oldorder_r)==0 || !isset($next_ord[$sortable])){
        $oldorder_r=getTable($table,$sortable." ASC","(".$sortable."<='".$prev_ord[$sortable]."' AND ".$sortable.">='".$dropped_ord[$sortable]."')");
        if(count($oldorder_r)>0){
          $sql="UPDATE `".$config_table_prefix.$table."` SET ".$sortable."='".$prev_ord[$sortable]."' WHERE id='".$dropped."'";
          mysql_query($sql);
          
          while (list($key, $row) = each($oldorder_r)) {
            if($row['id']!=$dropped) {
              $ordinamento=retRow($table,$row['id']);
              $sql="UPDATE `".$config_table_prefix.$table."` SET ".$sortable."='".($ordinamento[$sortable]-10)."' WHERE id='".$row['id']."'";
              mysql_query($sql);
            }    
          } 
        }else{
          return;
        } 
      }
    }
    
    function msgbox_error($str){ ?>
      <!-- Module 2A -->
      <div class="ez-wr rsTable2-error alert alert-danger">
        <div class="ez-fl ez-negmr ez-50 rsTable2-error-ico rsTable2-timeout">
          <div class="ez-box"></div>
        </div>
        <div class="ez-last ez-oh">
          <div class="ez-box"><?php echo $str; ?></div>
        </div>
      </div>
      <?php
    }
    
    function msgbox_confirm($str){ ?>
      <!-- Module 2A -->
      <div class="ez-wr rsTable2-ok alert alert-success">
        <div class="ez-fl ez-negmr ez-50 rsTable2-ok-ico rsTable2-timeout">
          <div class="ez-box"></div>
        </div>
        <div class="ez-last ez-oh">
          <div class="ez-box"><?php echo $str; ?></div>
        </div>
      </div>
      <?php
    }
    
    function action() {
      if(!isset($_REQUEST['rsTable2Action'])) return false;
      
      if(empty($_SESSION["user_id"]) && empty($_SESSION["user_login"])){
        $this->g_optionsSer=$_POST['optionsSer']; 
        $optionsSer=unserialize(rawurldecode(stripslashes($this->g_optionsSer)));
         
        if(!decryptOptions($optionsSer)) return false;
      }
      
      global $config_table_prefix;
      $objConfig = new ConfigTool();
      $objUtility=new Utility();
      
      if($_POST['rsFilterFun']=="1"){
        $rsFilter=unserialize(rawurldecode(stripslashes($_POST['rsFilter'])));
        $rsFilter=explode("#RSSEP#",$rsFilter);
        $rsTable=unserialize(rawurldecode(stripslashes($_POST['rsTable'])));
        //checkRightsForTable($rsTable,$_POST['rsRowId'],1);
        $rsRowId=$_POST['rsRowId'];
        $selected=$_POST['sel'];
        $this->g_table=$rsTable;
        
        $str="id_";
        while (list($key, $filter) = each($rsFilter)) { 
          $col=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$rsTable."' AND campo_hidden='".$filter."')"));
          if($_POST['namestr']=="1") $name=$col[0]['campo_hidden'];else $name=$col[0]['id']; 
          $rsfunArr=explode(";", $col[0]['rsPower']);
          if(($fun=$this->rsValidate($rsfunArr,$str))!==FALSE) {    
            if(($fun2=$this->rsValidate($rsfunArr,"_filter#"))!==FALSE) {
              $fun=$fun.";".$fun2;
            }
            
            if(strpos($fun, "_filter#")!==FALSE) {
              $filter=explode(";_filter#", $fun);
              $fun=$filter[0];
              $filter=$filter[1];
            }
            $this->printIdTD($name,$fun,"",$rsRowId,$filter,$selected);
            echo "#RSSEP#";  
          }
        }
        exit;
      }
      
      if($_POST['rsInitTable']=="1"){
        $options=unserialize(rawurldecode(stripslashes($_POST['options'])));
        //checkRightsForTable($options['table'],$options['id'],$options['tableParent']);
        
        $this->rsTable2($options['table'],$options['id'],$options['tableParent'],$options['tableParentId'],$options['sort']);
        $this->g_showAll=$options['showAll'];
        $this->g_colFilter=$options['colFilter'];
        $this->g_col_limit=$options['colLimit'];
        
        /*
        $this->g_table=$options['table'];
        $this->g_id=$options['id'];
        $this->g_tblParent=$options['tableParent'];
        $this->g_parentId=$options['tableParentId'];
        $this->g_sort=$options['sort'];
        $this->g_showAll=$options['showAll'];
        $this->g_colFilter=$options['colFilter'];
        */
        $this->_print($where=$options['where'],$tblParent=$options['tableParent'],$parentId=$options['tableParentId'],$subPrint="",$colfilter=$options['colFilter'],$onlyMod="");
        exit;
      }
      
      if($_POST['tabModTranslateDo']=="1") {
        $table=$_POST['rsTable'];
        //checkRightsForTable($table);
        $this->g_table=$table;
        $rowid=$_POST['rsRow'];
        $err=false;
        
        $cols=permissionField(getTable("rstbl2_campi","","tabella_hidden='".$config_table_prefix.$table."'"));
        while (list($key, $row) = each($cols)) {
          $field=$row['campo_hidden'];
          $str=$_POST[$field];
          if(isset($str)){
            $tradid=$_POST[$field."_tradid"];
            $sql="UPDATE `".$config_table_prefix."traduzioni` SET testo_tradotto_editor='".addslashes($str)."' WHERE id='".$tradid."'";   
            if(!mysql_query($sql)) $err=true;
          }  
        }
        
        if(!$err) {
          $this->msgbox_confirm('<div class="rsTable2-timeout">'.ln("Salvataggio avvenuto correttamente ").'</div>');
        }else{
          $this->msgbox_error(ln("Si è verificato un errore durante l'operazione."));
        }
        exit;
      }
      
      if($_POST['getRelNM']=="1"){
        $table=rawurldecode(stripslashes($_POST['table']));
        $tableId=$_POST['id'];
        //checkRightsForTable($table,$tableId);
        if($_POST['titparent']=="") {
          $tit_parent=getTable("rstbl2_tabelle","","(tabella='".$config_table_prefix.$table."')");
          $tit_parent=$tit_parent[0]['titolo_visualizzato'];
        }else{
          $tit_parent=rawurldecode(stripslashes($_POST['titparent']));
        }
        
        $ppp=retRow($table,$tableId);
        
        if($table=="categorie") {
          $cat=retRow("categorie",$tableId);
          $struttura=getStrutturaByNodo($tableId);
          $tit_parent=$struttura['nome']." > ".$cat['nome'];
          $briciole=strip_tags(implode(" > ",retBriciole("","",$tableId)));
          $tit_parent=$struttura['nome']." > ".$briciole;
          $tit_parent=htmlentities($tit_parent);  
        }
        
        $tmpTab=getTable("rstbl2_tabelle","","tabella='".$config_table_prefix.$table."'");
        if(count($tmpTab)>0) {
          $isSystem=isUserSystem();
          $wh="(attivo='1')";
          if($isSystem==0) $wh="(attivo='1' AND issystem='0')";
          $tbl_nm=Table2ByTable1("rstbl2_tabelle","rstbl2_tabelle_list",$tmpTab[0]['id'],$wh,"Ordinamento ASC");
          ?>
          <!-- Layout 1 -->
          <div class="ez-wr rsTable2-dialog-nm">
            <?php
            while (list($key, $row) = each($tbl_nm)) {
              $nm=retRow("rstbl2_tabelle",$row['id_rstbl2_tabelle']);
              $tit=$nm['titolo_visualizzato'];
              $ico=retFile($nm['icon_file'],16,16);
              $nm_table=right($nm['tabella'],(strlen($nm['tabella'])-strlen($config_table_prefix)));
              $nmcount=Table2ByTable1($table,$nm_table,$tableId,"","");
              $nmcount=count($nmcount);
              if(!$ico) $ico=$objUtility->getPathBackofficeResources()."ico_table.png";
              
              $roles=getTable("categorie_roles","","(attivo='1' AND tabella='".$nm_table."' AND id_strutture='".$struttura['id']."' AND (id_categorie='0' OR id_categorie='".$tableId."') AND nascondi='1')");
              
              if(!(count($roles)>0)) { 
                $str_insert="";
                
                if($nm_table=="magazzino_articoli" && $table=="categorie") {
                  $magazzino_articoli=Table2ByTable1("categorie","magazzino_articoli",$tableId,"(`".$config_table_prefix."magazzino_articoli`.del_hidden='0')","`".$config_table_prefix."magazzino_articoli`.id DESC");
                  
                  $magazzino_articoli=$magazzino_articoli[0];
                  $str_insert='rsInsert="1" rsInsertId="'.$magazzino_articoli['id'].'"';
                }
                
                if($nm_table=="categorie" && $table=="categorie") {
                  $str_insert='rsInsert="1" rsInsertId="'.$tableId.'"';
                }
                
                ob_start();
                ?><div class="rsTable2-dialog-nm-link" style="background-image:url(<?php echo $ico; ?>);"><a href="#" rel="rsOpenWindow" rsTable="<?php echo $nm_table; ?>" rsTableParent="<?php echo $table; ?>" rsTableParentId="<?php echo $tableId; ?>" rsWhere="" rsOrd="" rsTxt="" rsTit="<?php echo str_replace("&nbsp;"," ",$tit_parent." > ".$tit." (".left(trim(strip_tags($ppp[1])),20).")"); ?>" <?php echo $str_insert ; ?>><?php echo $tit; if($nmcount>0) echo " (".$nmcount.")"; ?></a></div><?php 
                $nm_link=ob_get_contents(); 
                ob_end_clean();
                rsTable2_BeforePrintLinkNM($table,$tableId,$nm_table,$nm_link,$key);
                echo $nm_link;
              }
            }
          ?></div><?php                         
        }
        exit;
      }
      
      if(isset($_POST['delrow'])) {
        if(empty($_SESSION["user_id"]) && empty($_SESSION["user_login"])){
          if($optionsSer['permDel']==-1) {
            echo "Access denied";
            exit;
          }  
        }
        
        $id=$_POST['delrow'];
        $table=stripslashes(rawurldecode($_POST['table']));
        $tblParent=stripslashes(rawurldecode($_POST['parent']));
        $parentId=$_POST['parentid'];
        $old=retRow($table,$id);
        //checkRightsForTable($table,$id,$tblParent);
        
        rsTable2_BeforeDelete($table,$id,$tblParent,$parentId);
        if($tblParent!="" && $parentId>0) {
          $sql="DELETE FROM `".$config_table_prefix.$tblParent."#".$table."_nm` WHERE (id_".$tblParent."='".$parentId."' AND id_".$table."='".$id."')";
          mysql_query($sql);
        }
        
        $sql="DELETE FROM `".$config_table_prefix.$table."` WHERE id='".$id."'";
        if(mysql_query($sql)) { 
          rsTable2_AfterDelete($table,$old,$tblParent,$parentId);
          echo "1";
        }else{
          echo ln("Errore durante la cancellazione");  
        }
        exit;
      }
      
      if($_POST['delSelectedRow']=="1") {
        if(empty($_SESSION["user_id"]) && empty($_SESSION["user_login"])){
          if($optionsSer['permDel']==-1) {
            echo "Access denied";
            exit;
          }  
        }
      
        $table=stripslashes(rawurldecode($_POST['table']));
        $tblParent=stripslashes(rawurldecode($_POST['parent']));
        $parentId=$_POST['parentid'];
        //checkRightsForTable($table,0,$tblParent);
        
        $err=0;
        if(!is_array($_SESSION[$table."checkSel"])) {
          echo "1";
          exit;
        }
        
        reset($_SESSION[$table."checkSel"]);
        while (list($key, $row) = each($_SESSION[$table."checkSel"])) {
          $old=retRow($table,$row);
          rsTable2_BeforeDelete($table,$row,$tblParent,$parentId);
          if($tblParent!="" && $parentId>0) {
            $sql="DELETE FROM `".$config_table_prefix.$tblParent."#".$table."_nm` WHERE (id_".$tblParent."='".$parentId."' AND id_".$table."='".$row."')";
            if(!mysql_query($sql)) $err=1;
          }
          
          $sql="DELETE FROM `".$config_table_prefix.$table."` WHERE id='".$row."'";
          
          if(!mysql_query($sql)){
            $err=1;
          }else{
            rsTable2_AfterDelete($table,$old,$tblParent,$parentId);
          }       
        }
        reset($_SESSION[$table."checkSel"]);
        if($err==0) {
          echo "1";
        }else{
          echo ln("Errori durante le cancellazioni");  
        } 
        
        exit;  
      }
      
      if(isset($_POST['tabInsertDo'])) {
        $objMailing = new Mailing;
        $table=stripslashes(rawurldecode($_POST['tabName']));
        $tableId=$_POST['tabId'];
        $colfilter=$_POST['colfilter'];
        $tblParent=stripslashes(rawurldecode($_POST['tblParent']));
        //checkRightsForTable($table,$tableId,$tblParent);
        
        $parentId=$_POST['parentId'];
        $rowid=$_POST['rowid'];
        
        $this->g_table=$table;
        
        $result = mysql_query("SELECT * FROM `".$config_table_prefix.$table."`"); 
        $j=0;
        $oldval=retRow($table,$rowid);
        $file_arr=array();
        $id_arr=array();
        $field_lan=array();
        $fname_lan=array();
        $val_lan=array();
        $usemd5=true;
        while ($j < mysql_num_fields($result)) {
          $field = mysql_fetch_field($result);
          $key=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$table."' AND campo_hidden='".$field->name."')"));
                             
          $rsfunArr=explode(";", $key[0]['rsPower']);
          $key=$key[0]['id'];
          
          if($this->rsValidate($rsfunArr,"_file")!==FALSE) {
            $farr=explode(";", $_POST[$key]);
            if(count($farr)>count($file_arr)) {
              $file_arr=$farr;
            }
          }
          
          if($this->rsValidate($rsfunArr,"id_")!==FALSE) {
            if(is_array($_POST[$key])) $idarr=$_POST[$key];
            if(count($idarr)>count($id_arr)) {
              $id_arr=$idarr;
            }
          }
          
          $j++;
        }
        
        if(count($file_arr)==0) array_push($file_arr, "0");
        if(count($id_arr)==0) array_push($id_arr, "0");
        
        $err=0;
        rsTable2_BeforeProcess($table,$tableId,$tblParent,$parentId,$rowid);
        while (list($k, $idfile) = each($file_arr)) {
          reset($id_arr);
          while (list($k2, $idarr) = each($id_arr)) {
            $j=0;
            $result = mysql_query("SELECT * FROM `".$config_table_prefix.$table."`");
            $sql1="";
            $sql2="";
            $notInsert=false;
            while ($j < mysql_num_fields($result)) {
              $field = mysql_fetch_field($result);
              $key=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$table."' AND campo_hidden='".$field->name."')"));
              $fname=$key[0]['titolo_visualizzato'];
              $fname=str_replace("&nbsp;", " ", $fname);
                                 
              $rsfunArr=explode(";", $key[0]['rsPower']);
              $key=$key[0]['id'];
              
              $post_buffer=$_POST[$key];
              
              $op="op_".$key;
              $op=$_POST[$op];
              $isdate=false;
              
              if($this->rsValidate($rsfunArr,"_date")!==FALSE) {
                $anno="anno".$key;
                $mese="mese".$key;
                $giorno="giorno".$key;
                
                $_POST[$key]=$_POST[$anno]."-".$_POST[$mese]."-".$_POST[$giorno];
                $isdate=true;
              }
              
              if($this->rsValidate($rsfunArr,"_file")!==FALSE) {
                $tmpfile=explode(";", $_POST[$key]);
                $_POST[$key]=$tmpfile[$k]; 
                if($_POST[$key]=="0") $_POST[$key]="";
                if($_POST[$key]=="0;") $_POST[$key]=""; 
                if($_POST[$key]==";") $_POST[$key]="";
              }
              
              if($this->rsValidate($rsfunArr,"_ordinamento")!==FALSE && !($rowid>0)) {
                $sql="SELECT MAX(".$field->name.") FROM `".$config_table_prefix.$table."`";
                $q=mysql_query($sql);
                $tmprs=$objUtility->buildRecordset($q);
                $_POST[$key]=$tmprs[0]['MAX('.$field->name.')']+10;
              } 
              
              if($this->rsValidate($rsfunArr,"id_")!==FALSE) {
                $tmpid=$_POST[$key];
                if(is_array($tmpid) && count($tmpid)>1) {
                  $_POST[$key]=$tmpid[$k2];
                }elseif(is_array($tmpid) && count($tmpid)==1){
                  $_POST[$key]=$tmpid[0];    
                } 
              }
              
              if($this->rsValidate($rsfunArr,"_cry")!==FALSE) {
                if(!$this->elaborateCurrency($_POST[$key])) {
                  $this->msgbox_error("<div class='rsTable2-obligatory' rsField='".$key."'>".ln("Il campo '").strtoupper($fname).ln("' deve contenere un valore monetario.")."</div>");
                  exit;  
                }else{
                  $_POST[$key]=str_replace(",", ".", $_POST[$key]);  
                }
              }
              
              if($this->rsValidate($rsfunArr,"_number")!==FALSE) {
                if(!$this->elaborateCurrency($_POST[$key])) {
                  $this->msgbox_error("<div class='rsTable2-obligatory' rsField='".$key."'>".ln("Il campo '").strtoupper($fname).ln("' deve contenere un valore numerico.")."</div>");
                  exit;  
                }else{
                  $_POST[$key]=str_replace(",", ".", $_POST[$key]);  
                }
              }
               
              if($this->rsValidate($rsfunArr,"_obligatory")!==FALSE) {
                if($isdate){
                  if($_POST[$anno]=="0000" || $_POST[$mese]=="00" || $_POST[$giorno]=="00") $_POST[$key]="";
                }
  
                if($this->rsValidate($rsfunArr,"_pwd")!==FALSE) {
                  if($_POST[$key]=="" && $_POST["c_".$key]=="") {
                    if($rowid>0) {
                      $usemd5=false;
                      $tmpf=$field->name;
                      $_POST[$key]=$oldval[$tmpf];
                      $_POST["c_".$key]=$oldval[$tmpf];  
                    } 
                  }
                }
  
                if($this->rsValidate($rsfunArr,"_str_")!==FALSE) {
                  if($_POST[$key]=="0") $_POST[$key]="";
                  if($_POST[$key]=="-1") $_POST[$key]=""; 
                }
                
                if($this->rsValidate($rsfunArr,"id_")!==FALSE || $this->rsValidate($rsfunArr,"_boolean")) {
                  if($_POST[$key]=="0") $_POST[$key]="";
                  if($_POST[$key]=="-1") $_POST[$key]=""; 
                }
                
                if($_POST[$key]=="" && (count($file_arr)>1 || count($id_arr)>1)){
                  $notInsert=true;
                }elseif($_POST[$key]==""){
                  $this->msgbox_error("<div class='rsTable2-obligatory' rsField='".$key."'>".ln("Il campo '").strtoupper(ln($fname)).ln("' è obbligatorio.")."</div>");
                  exit;
                }    
              }
              
              if($this->rsValidate($rsfunArr,"_pwd")!==FALSE) {
                if($_POST[$key]=="" && $_POST["c_".$key]=="") {
                  if(!($rowid>0)) {
                    $genpass=$objUtility->getFilenameUnique();
                    $genpass=left($genpass,8);
                    $_POST[$key]=$genpass;
                    $_POST["c_".$key]=$genpass;
                  }else{
                    $usemd5=false;
                    $tmpf=$field->name;
                    $_POST[$key]=$oldval[$tmpf];
                    $_POST["c_".$key]=$oldval[$tmpf];  
                  }
                }
                
                if($usemd5) {
                  if(strlen($_POST[$key])<8) {
                    $this->msgbox_error("<div class='rsTable2-obligatory' rsField='".$key."'>".ln("La password deve essere lunga almeno 8 caratteri.")."</div>");
                    exit;  
                  }
                }
                
                $conf=$_POST["c_".$key];
                if($conf!=$_POST[$key]) {
                  $this->msgbox_error("<div class='rsTable2-obligatory' rsField='".$key."'>".ln("I campi '").strtoupper($fname).ln("' devono essere uguali.")."</div>");
                  exit;  
                }else{
                  if($usemd5) $_POST[$key]=md5($_POST[$key]);  
                }  
              }
              
              if($this->rsValidate($rsfunArr,"_unique")!==FALSE) {
                if($this->rsValidate($rsfunArr,"_ordinamento")==FALSE && $this->rsValidate($rsfunArr,"_file")==FALSE && $this->rsValidate($rsfunArr,"id_")==FALSE && $this->rsValidate($rsfunArr,"_cry")==FALSE && $this->rsValidate($rsfunArr,"_date")==FALSE && $this->rsValidate($rsfunArr,"_number")==FALSE && $this->rsValidate($rsfunArr,"_suggest#")==FALSE && $this->rsValidate($rsfunArr,"_perc#")==FALSE) {
                  $max=getTable($table,"id DESC LIMIT 1","");
                  if($k>0) $_POST[$key].="_".$max[0]['id'];
                  if($k2>0) $_POST[$key].="_".$max[0]['id'];
                }
                
                if($parentId>0 && $tblParent!="") {
                  $q=Table2ByTable1($tblParent,$table,$parentId,"(`".$config_table_prefix.$table."`.".$field->name."='".$_POST[$key]."' AND `".$config_table_prefix.$table."`.id<>'".$rowid."')","");
                }else{
                  $q=getTable($table,"","(".$field->name."='".$_POST[$key]."' AND id<>'".$rowid."')");
                }
                
                if($this->rsValidate($rsfunArr,"id_")!==FALSE && (count($file_arr)>1 || count($id_arr)>1)){
                  if(count($q)>0) $notInsert=true;  
                }else{
                  if(count($q)>0) {
                    $this->msgbox_error("<div class='rsTable2-obligatory' rsField='".$key."'>".ln("'").strtoupper($fname).ln("' non disponibile. Scegliere un altro valore.")."</div>");
                    exit;
                  }  
                } 
              }
              
              if(isset($_POST[$key]) && $field->name!="id") {
                $tmpfieldlan=$field->name;
                $exist_lan=ExistTraduction($oldval[$tmpfieldlan]);
                if($exist_lan!=false){
                  array_push($field_lan, $tmpfieldlan); 
                  array_push($fname_lan, $fname);
                  $val_lan[$tmpfieldlan]['trad']=$exist_lan;
                  $val_lan[$tmpfieldlan]['newval']=$_POST[$key]; 
                } 
                
                $sqlupd.=$field->name."='".$_POST[$key]."',";
                $sql1.=$field->name.",";
                $sql2.="'".$_POST[$key]."',"; 
              }
              
              $_POST[$key]=$post_buffer;
              $j++; 
            }
          
            $sql1="(".$sql1.")";
            $sql2="(".$sql2.")";
            $sql1=str_replace(",)", ")", $sql1);
            $sql2=str_replace(",)", ")", $sql2);
            $sqlupd=left($sqlupd, strlen($sqlupd)-1);
          
            if($rowid>0) {
              $old=retRow($table,$rowid);
              rsTable2_BeforeUpdate($table,$rowid,$tblParent,$parentId,$sqlupd,$sql1,$sql2); 
              $sql="UPDATE `".$config_table_prefix.$table."` SET ".$sqlupd." WHERE id='".$rowid."'";
               
              if(mysql_query($sql)){
                $nid=$rowid;
                rsTable2_AfterUpdate($table,$rowid,$tblParent,$parentId,$old);
              }else{
                $err=1;
              }
            }elseif($notInsert==false){
              rsTable2_BeforeInsert($table,$sql1,$sql2);
              $sql="INSERT INTO `".$config_table_prefix.$table."` ".$sql1." VALUES ".$sql2;
              if(mysql_query($sql)){
                $nid=mysql_insert_id();
                if($tblParent!="" && $parentId>0) {
                  $nm_table=$tblParent."#".$table."_nm";
                  $nm_sql1="(id_".$tblParent.",id_".$table.")";
                  $nm_sql2="('".$parentId."','".$nid."')";
                  $sql="INSERT INTO `".$config_table_prefix.$nm_table."` ".$nm_sql1." VALUES ".$nm_sql2;
                  mysql_query($sql);
                  
                }
                
                rsTable2_AfterInsert($table,$nid,$parentId,$tblParent);
                
                $confirm=$this->makeTemplateReplace($nid);
                if($confirm['email_oggetto_amministrazione']=="") $confirm['email_oggetto_amministrazione']=ln("Dati inviati da ".$_SERVER['SERVER_NAME']);
                if($confirm) {
                  $res=1;
                  if($objConfig->get("email-from")!="" && $confirm['invio_bozza_amministrazione']==1) {
                    $objMailing->mmail($objConfig->get("email-from"),"form@".$_SERVER['SERVER_NAME'],ln($confirm['email_oggetto_amministrazione']),$confirm['email_gestore'],$allegato,$allegato_type,$allegato_name);
                  }
                  
                  if($confirm['email_editor']!="" && $confirm['email_destinatari']!="") {
                    $res=$objMailing->mmail($confirm['email_destinatari'],$objConfig->get("email-from"),$confirm['email_oggetto'],$confirm['email_editor'],$allegato,$allegato_type,$allegato_name);
                  }
                  if($res==0) $err=1; 
                  
                  $sql="INSERT INTO `".$config_table_prefix."rstbl2_invii` (destinatari,oggetto,messaggio,errori) VALUES ('".addslashes($confirm['email_destinatari'])."','".addslashes($confirm['email_oggetto'])."','".addslashes($confirm['email_editor'])."','".$res."')";
                  mysql_query($sql);
                  $eid=mysql_insert_id();
                  
                  $sql="INSERT INTO `".$config_table_prefix."rstbl2_email#rstbl2_invii_nm` (id_rstbl2_email,id_rstbl2_invii) VALUES ('".$confirm['id']."','".$eid."')";
                  mysql_query($sql);
                  
                  if(stripslashes($confirm['messaggio_conferma_editor'])!="") $messaggio_conferma=$confirm['messaggio_conferma_editor'];
                  if(stripslashes($confirm['messaggio_errore_editor'])!="") $messaggio_errore=$confirm['messaggio_errore_editor'];
                }
              }else{
                $err_Num=mysql_errno();
                if($err_Num==1062) $err=1;
              }                                                                                                                             
            }            
          }
        }
        
        if(stripslashes($messaggio_conferma)=="") $messaggio_conferma='<div class="rsTable2-timeout">'.ln("Salvataggio avvenuto correttamente ").'</div><a style="display:none;" href="" rel="rsOpenWindow" rsRowId="'.$nid.'" rsTable="'.$table.'" rsWhere="id=\''.$nid.'\'" rsOrd="" rsTxt="" rsTit="'.$table.'">'.ln("(clicca qui per vedere l'ultimo salvataggio fatto)").'</a>';
        if(stripslashes($messaggio_errore)=="") $messaggio_errore=ln("Si è verificato un errore durante l'operazione.");
        if($err_Num==1062 && $table=="users") $messaggio_errore=$err_str.ln("Questo Username non è disponibile. Sceglierne un altro");
        if($err_Num==1062) $messaggio_errore=ln("Questo Username non è disponibile. Sceglierne un altro");
        
        if($err==0) {                                       
          if(($rowid>0) && count($field_lan)>0) {
            $str_lan_col=implode(",", $field_lan);
            $str_lan_field=implode(",", $fname_lan);
            $this->addTranslate($val_lan);
            $str_mod_translate=$this->modTranslate($rowid,$field_lan); 
            echo "RSI#".ln("I campi ")."#_#".$str_mod_translate."#_#".$str_lan_col."#_#".$str_lan_field."#_#".ln(" hanno delle traduzioni abbinate, si desidera apportare modifiche anche alle rispettive traduzioni?")."#RSF";
          }
          
          $this->msgbox_confirm($messaggio_conferma);
        }else{
          $this->msgbox_error($messaggio_errore);
        }
        
        exit;
      }
      
      if($_POST['rsTable2TroncaTesto']==1){
        //checkRightsForTable($_POST['table'],$_POST['id'],$_POST['tblParent']);
        $html=retRow($_POST['table'],$_POST['id']);
        $field=$_POST['field'];
        $this->printSegueTD($html[$field],$_POST['table'],$_POST['field'],$_POST['id']);
        exit;
      }
      
      if($_POST['rsTable2TD-ID']==1){
        //checkRightsForTable($_POST['table'],$_POST['id'],$_POST['tblParent']);
        $this->g_table=$_POST['table'];
        $html=retRow($_POST['table'],$_POST['id']);
        $field=$_POST['field'];
        
        $cols=permissionField(getTable("rstbl2_campi","Ordinamento ASC","(tabella_hidden='".$config_table_prefix.$this->g_table."' AND campo_hidden='".$field."')"));
        $rsfunArr=explode(";",$cols[0]['rsPower']);
        
        $tmpTab=getTable("rstbl2_tabelle","","tabella='".$config_table_prefix.$this->g_table."'");
        $this->g_permessi=Table2ByTable1("rstbl2_tabelle","rstbl2_permessi",$tmpTab[0]['id'],"","");
        $this->g_permessi=$this->g_permessi[0];
        if($this->g_permessi['Inserimento']=="") $this->g_permessi['Inserimento']="1";
        if($this->g_permessi['Modifica']=="") $this->g_permessi['Modifica']="1";
        if($this->g_permessi['Modifica_rapida']=="") $this->g_permessi['Modifica_rapida']="1";
        if($this->g_permessi['Cancellazione']=="") $this->g_permessi['Cancellazione']="1";
        if($this->g_permessi['Relazioni']=="") $this->g_permessi['Relazioni']="1";
        
        if(($rsfun=$this->rsValidate($rsfunArr,"id_"))!==FALSE) { 
          $this->printTD($rsfun,$html[$field],$field,$_POST['id']);
        }
        exit;
      }
      
      if($_POST['rsTable2TD-boolean']==1){
        //checkRightsForTable($_POST['table'],$_POST['id'],$_POST['tblParent']);
        $html=retRow($_POST['table'],$_POST['id']);
        $field=$_POST['field'];
        $this->printBooleanTD($html[$field]);  
      }
      
      if($_POST['rsTable2UpdateFile']==1){
        //checkRightsForTable($_POST['table'],$_POST['id'],$_POST['tblParent']);
        $f=retRow($_POST['table'],$_POST['id']);
        $field=$_POST['field'];
        $this->printFileTD($f[$field]);
        exit;
      }
      
      if(isset($_POST['tabSearch'])){
        $table=stripslashes(rawurldecode($_POST['tabName']));
        $tableid=$_POST['tabId'];
        $colfilter=$_POST['colfilter'];
        $where=stripslashes(rawurldecode($_POST['where']));
        $tblParent=stripslashes(rawurldecode($_POST['tblParent']));
        //checkRightsForTable($table,$tableid,$tblParent);
        $parentId=$_POST['parentId'];
        
        $_SESSION["rsTable2_".$tableid."_search_arr"]=array();
        
        $result = mysql_query("SELECT $colfilter FROM `".$config_table_prefix.$table."`"); 
        $j=0;
        while ($j < mysql_num_fields($result)) {    
          $field = mysql_fetch_field($result);
          $key=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$table."' AND campo_hidden='".$field->name."')"));
          
          $rsfunArr=explode(";", $key[0]['rsPower']);
          $key=$key[0]['id'];
          
          $op="op_".$key;
          $op=$_POST[$op];
          $op2="op2_".$key;
          $op2=$_POST[$op2];
          $tmpSearch=$_POST[$key];
          if($op!="" || $tmpSearch!="") {
            $_SESSION["rsTable2_".$tableid."_search_arr"][$key]=$tmpSearch;
            $_SESSION["rsTable2_".$tableid."_search_arr"]["op_".$key]=$op;
            $_SESSION["rsTable2_".$tableid."_search_arr"]["op2_".$key]=$op2;
            
            if($op=="") $op="=";
            if($op2=="") $op2="=";
            if($op=="LIKE") $tmpSearch="%".$tmpSearch."%";
            
            if($this->rsValidate($rsfunArr,"_hidden")===FALSE) {
              if($this->rsValidate($rsfunArr,"_date")!==FALSE) {
                $anno="anno".$key;
                $mese="mese".$key;
                $giorno="giorno".$key;
                
                $anno2="anno".$key."_ZZZ";
                $mese2="mese".$key."_ZZZ";
                $giorno2="giorno".$key."_ZZZ";
                
                $tmpSearch=$_POST[$anno]."-".$_POST[$mese]."-".$_POST[$giorno];
                $data2=$_POST[$anno2]."-".$_POST[$mese2]."-".$_POST[$giorno2];
                
                $_SESSION["rsTable2_".$tableid."_search_arr"][$key."_date1"]=$tmpSearch;
                $_SESSION["rsTable2_".$tableid."_search_arr"][$key."_date2"]=$data2;
                
                if($tmpSearch!="0000-00-00") $sql.="`".$config_table_prefix.$table."`.".$field->name." ".$op." '".$tmpSearch."' AND ";
                if($data2!="0000-00-00") $sql.="`".$config_table_prefix.$table."`.".$field->name." ".$op2." '".$data2."' AND ";
              } elseif(($this->rsValidate($rsfunArr,"id_"))!==FALSE) { 
                //if(!$this->rsValidate($rsfunArr,"_str_")) {
                  while (list($key, $row) = each($tmpSearch)) {
                    if($row>-1) $sqltmp.="`".$config_table_prefix.$table."`.".$field->name." = '".$row."' OR "; 
                  }
                  $sqltmp="(".$sqltmp.")";
                  $sqltmp=str_replace(" OR )", ")", $sqltmp);
                  if($sqltmp=="()") $sqltmp="";
                  if($sqltmp!="") $sql.=$sqltmp." AND ";
                //}      
              } elseif($this->rsValidate($rsfunArr,"_file")!==FALSE && isset($tmpSearch)) {
                if($tmpSearch=='0') $sql.="("."`".$config_table_prefix.$table."`.".$field->name." = 0 OR "."`".$config_table_prefix.$table."`.".$field->name." IS NULL) AND ";
                if($tmpSearch=='1') $sql.="("."`".$config_table_prefix.$table."`.".$field->name." <> 0 AND "."`".$config_table_prefix.$table."`.".$field->name." IS NOT NULL) AND ";
              } elseif($this->rsValidate($rsfunArr,"_boolean")!==FALSE && isset($tmpSearch)) {
                if($tmpSearch=='0') $sql.="("."`".$config_table_prefix.$table."`.".$field->name." = '0' OR "."`".$config_table_prefix.$table."`.".$field->name." IS NULL) AND ";
                if($tmpSearch=='1') $sql.="`".$config_table_prefix.$table."`.".$field->name." = '1' AND ";
              } else {
                $sql.="("."`".$config_table_prefix.$table."`.".$field->name." ".$op." '".$tmpSearch."' OR "."`".$config_table_prefix.$table."`.".$field->name." ".$op." '".htmlEDtiny($tmpSearch)."') AND ";
              }
            }
          }
          $j++;
        }
        $sql="($sql)";
        $sql=str_replace(" AND )", ")", $sql);
        if($sql=="()") $sql="1";
         
        $_SESSION["rsTable2_".$tableid."_search"]=$sql;
        
        $sort=unserialize(stripslashes(rawurldecode($_GET['sorting'])));
        $this->rsTable2($table,$tableid,$tblParent,$parentId,$sort);
        $this->g_showAll=$_POST['showAll'];
        
        /*
        $this->g_table=$options['table'];
        $this->g_id=$options['id'];
        $this->g_tblParent=$options['tableParent'];
        $this->g_parentId=$options['tableParentId'];
        $this->g_sort=$options['sort'];
        $this->g_showAll=$_POST['showAll'];
        */
        $this->_print($where,$tblParent,$parentId);
        
        exit;
      }
      
      if(isset($_POST['newfile'])) {
        $f=rawurldecode($_POST['newfile']);
        if($f!=""){  
          $basename=basename($f);
          $path=$objUtility->getPathResourcesDynamicAbsolute()."uploaded/";
          $ext=retExt($f);
          $funique=$objUtility->getFilenameUnique();
          
          rename($path.$basename,$objUtility->getPathResourcesDynamicAbsolute().$funique.".".$ext);
          
          $sql="INSERT INTO `".$config_table_prefix."oggetti` (nome,path,originalname,ext,isprivate) VALUES ('".$funique."','".$objUtility->getPathResourcesDynamic()."','".$basename."','".$ext."',NULL)";
          mysql_query($sql);
          $idoggetti=mysql_insert_id();
          
          echo $idoggetti;
          echo "#SEP#";
          $this->printFileTD($idoggetti);  
        }else{
          echo "0";  
        }
        exit;
      }
      
      if(isset($_POST['delfile'])) {
        $arrid=explode(";", $_POST['delfile']);
        while (list($key, $id) = each($arrid)) {
          $tf=retFileAbsolute($id);
          
          if($tf) unlink($tf);
          
          $sql="DELETE FROM `".$config_table_prefix."oggetti` WHERE id='".$id."'";
          mysql_query($sql);
        }
        
        $rs=permissionField(retRow("rstbl2_campi",$_POST['name']));
        $sql="UPDATE `".$rs['tabella_hidden']."` SET ".$rs['campo_hidden']."='0' WHERE id='".$_POST['rowid']."'";
        mysql_query($sql);
        
        exit;
      }
      
      if(isset($_POST['tabInsert'])){
        $options=unserialize(rawurldecode(stripslashes($_POST['options']))); 
        //checkRightsForTable($options['table'],$options['id'],$options['tableParent']);
        $this->rsTable2($options['table'],$options['id'],$options['tableParent'],$options['tableParentId'],"");
        
        /*
        $this->g_table=$options['table'];
        $this->g_id=$options['id'];
        $this->g_tblParent=$options['tableParent'];
        $this->g_parentId=$options['tableParentId'];
        */
        
        $this->_insert($options['colFilter'],$options['submitLabel'],"",$options['insertId']);
        
        exit;
      }
      
      if(isAjaxPost()) {
        //checkRightsForTable($_GET['tabName'],$_GET['tabId'],stripslashes(rawurldecode($_GET['tblParent'])));
        $sort=unserialize(stripslashes(rawurldecode($_GET['sorting'])));
        $this->rsTable2(stripslashes(rawurldecode($_GET['tabName'])),$_GET['tabId'],stripslashes(rawurldecode($_GET['tblParent'])),$_GET['parentId'],$sort); 
        
        /*$this->g_table=stripslashes(rawurldecode($_GET['tabName']));
        $this->g_id=$_GET['tabId'];
        $this->g_tblParent=stripslashes(rawurldecode($_GET['tblParent']));
        $this->g_parentId=$_GET['parentId'];
        $this->g_sort=$sort;
        */
        
        $this->_print(stripslashes(rawurldecode($_GET['where'])),stripslashes(rawurldecode($_GET['tblParent'])),$_GET['parentId']); 
        exit;  
      }
      
      if(isset($_POST['neworder'])) {
        $table=$_POST['table'];
        //checkRightsForTable($table,1,1);
        $sortable=$_POST['sortable'];
        $neworder=unserialize(stripslashes(rawurldecode($_POST['neworder'])));
        $dropped=$_POST['rowdropped'];
        
        $this->neworder($neworder,$dropped,$table,$sortable);
        
        echo ln("Operazione eseguita correttamente.");
        exit;  
      }
      
      if(isset($_POST['selection'])) {
        //checkRightsForTable($_POST['table'],1,1);
        $selection=unserialize(stripslashes(urldecode($_POST['selection'])));
        $this->refreshSelection($_POST['table'],$selection);
        exit;
      }
      
      if($_REQUEST['segue']==1) {
        //checkRightsForTable($_REQUEST['tab'],1,1);
        $col=$_REQUEST['col'];
        $tab=retRow($_REQUEST['tab'],$_REQUEST['id']);
        $txt=$tab[$col];
        ?>
        <!-- Plain box -->
        <link href="<?php echo $objUtility->getPathBackofficeResources(); ?>tables.css" media="screen" rel="stylesheet" title="CSS" type="text/css" />
        <div class="ez-wr rsTable2-lyteframe">
          <div class="ez-box"><?php echo $txt; ?></div> 
        </div>	
        <?php
        exit;
      }  
    }
    
    function makeTemplateReplace($id) {
      global $config_table_prefix;
      $objConfig = new ConfigTool();
      $objUtility = new Utility;
      
      $ecomm_user=getTable("ecommerce_anagrafica_predefinita","","attivo='1'");
      $ecomm_user=retRow("users",$ecomm_user[0]['id_users']);
      $ecomm_logo=retFile($ecomm_user['immagine_file'],150);
      
      $root=$_SERVER['SERVER_NAME'].$objUtility->getPathRoot();
      $userid=$_SESSION["user_id"];
      if($userid=="") $userid=$_SESSION["userris_id"];
      if($userid=="") $loggato=0;else $loggato=1;
      if($userid!="" && $_SESSION["user_id"]!="") {
        $roles=Table1ByTable2_pointed("roles","users_list","users",$userid,"","(".$config_table_prefix."roles.issystem='1')");
        if(count($roles)>0) $issystem=1;else $issystem=0;   
      }else{
        $issystem=0;
      } 
      
      $wh="";
      if($issystem==1) $wh.=$config_table_prefix."rstbl2_email.system='1' OR ";
      
      if($loggato==1 && $issystem==1) $wh.=$config_table_prefix."rstbl2_email.loggati='1' OR ";
      if($loggato==1 && $issystem!=1) $wh.="(".$config_table_prefix."rstbl2_email.loggati='1' AND ".$config_table_prefix."rstbl2_email.system='0') OR ";
      if(!$loggato==1) $wh.=$config_table_prefix."rstbl2_email.non_loggati='1' OR "; 
      
      if(right($wh,3)=="OR ") $wh=substr($wh, 0, strlen($wh)-3);
      if(trim($wh)=="") $wh="1";
      
      $email_conf=getTable("rstbl2_tabelle","","(tabella='".$config_table_prefix.$this->g_table."')");
      $email_conf=Table2ByTable1("rstbl2_tabelle","rstbl2_email",$email_conf[0]['id'],"(".$config_table_prefix."rstbl2_email.attivo='1' AND (".$wh."))","");
      if(count($email_conf)==0) return false;
      $email_conf=$email_conf[0];
      
      $email_conf["messaggio_conferma_editor"]=ln($email_conf["messaggio_conferma_editor"]);
      $email_conf["messaggio_errore_editor"]=ln($email_conf["messaggio_errore_editor"]);
      $email_conf["email_oggetto"]=ln($email_conf["email_oggetto"]);
      $email_conf["email_editor"]=ln($email_conf["email_editor"]);
      
      $cols=getTable("rstbl2_campi","","tabella_hidden='".$config_table_prefix.$this->g_table."'");
      $tmpuser=retRow($this->g_table,$id);
      while (list($key, $row) = each($cols)) {
        $field=$row['campo_hidden'];
        $tit=$row['titolo_visualizzato'];
        $hidden=false;
        
        if($this->rsValidate($row['rsPower'],"_pwd")!==FALSE) $hidden=true;
        if($this->rsValidate($row['rsPower'],"_hidden")!==FALSE) $hidden=true;
        if($this->rsValidate($row['rsPower'],"_system")!==FALSE) $hidden=true; 
        
        if($this->rsValidate($row['rsPower'],"_file")!==FALSE) {
          $oggetti=retRow("oggetti",$tmpuser[$field]);
          if(retFile($tmpuser[$field])){
            if(isImageByID($tmpuser[$field])) {
              $tmpuser[$field]="<img src='".retFile($tmpuser[$field],250)."' title='".addslashes($oggetti['originalname'])."' />";    
            }else{
              $tmpuser[$field]="<a href='http://".$_SERVER['SERVER_NAME'].retFile($tmpuser[$field])."' >".addslashes($oggetti['originalname'])."</a>";      
            }
          }else{
            $tmpuser[$field]="";  
          }  
        }elseif(($fun=$this->rsValidate($row['rsPower'],"id_"))!==FALSE) {
          $str="id_";
          $field1=$tmpuser[$field];
          if(!($field1>-1) || $field1=="NULL") {
            $field1=0;
          }
          $tab=substr($fun, strlen($str), strlen($fun)-strlen($str));
          if($tab=="") {
            $tmpuser[$field]="";  
          }
          
          $cols1=getTable("rstbl2_campi","Ordinamento ASC","tabella_hidden='".$config_table_prefix.$tab."'");
          if(!$cols1) {
            $tmpuser[$field]="";  
          }
          
          $pointed=array();
          $str1="_lst";
          while (list($key1, $row1) = each($cols1)) {
            $rsfunArr=explode(";", $row1['rsPower']);
            if($this->rsValidate($rsfunArr,$str1)!==FALSE) array_push($pointed, $row1['campo_hidden']);     
          }
          
          if(count($pointed)==0) array_push($pointed, $cols1[1]['campo_hidden']);
          $ptab=retRow($tab,$field1);             
          while (list($key1, $row1) = each($pointed)) { 
            $tmpuser[$field]=ln(str_replace(" ", "&nbsp;", $ptab[$row1])); 
          } 
        }elseif(($fun=$this->rsValidate($row['rsPower'],"_date"))!==FALSE) {
          $tmpuser[$field]=dataITA($tmpuser[$field]);
          if($tmpuser[$field]=="00-00-0000") $tmpuser[$field]="";
          if($tmpuser[$field]=="00-00-0000, 00:00") $tmpuser[$field]="";  
        }elseif(($fun=$this->rsValidate($row['rsPower'],"_boolean"))!==FALSE) {
          if($tmpuser[$field]=="1") $tmpuser[$field]=ln("sì");
          if($tmpuser[$field]=="1") $tmpuser[$field]=ln("no");  
        }
        
        $tmpuser[$field]=trim($tmpuser[$field]);
        
        if($tmpuser[$field]!="" && !$hidden) $email_conf["email_gestore"].=$tit.": <b>".$tmpuser[$field]."</b><br>";
        
        reset($email_conf);
        while (list($ekey, $erow) = each($email_conf)) {   
          $email_conf[$ekey]=str_replace("#SERVER_ROOT#", $root, $email_conf[$ekey]);
          
          $email_conf[$ekey]=str_replace("#".$field."#", $tmpuser[$field], $email_conf[$ekey]);
          $email_conf[$ekey]=replaceEcomerceMarkers($email_conf[$ekey]);
        }
      } 
      
      $email_conf["email_gestore"]="<div style='font-family:arial;'>".$email_conf["email_gestore"]."</div>";
      
      return $email_conf;
    }
    
    function elaborateCurrency($curr) {
      if($curr!="") {
        $curr=str_replace(",", ".", $curr);
        if(strpos($curr, ".")!==FALSE){
          $tarr=explode(".",$curr);
          $tl=count($tarr)-1;
          $tlast=$tarr[$tl];
          unset($tarr[$tl]);
          $curr=implode("", $tarr).".".$tlast;
        }
        if(!preg_match( '/^[\-+]?[0-9]*\.*\,?[0-9]+$/', $curr)) {
          return false;      
        }
      }
      return true;
    }
    
    function addTranslate($val_lan) {
      global $config_table_prefix;
      
      while (list($tk, $trow) = each($val_lan)) {
        if(isInDizionario($trow['newval'])==false) { 
          $txtStr=rip_tags(html2text($trow['newval']));
          $sql="INSERT INTO `".$config_table_prefix."dizionario` (testo_editor) VALUES ('".addslashes($txtStr)."')";    
          mysql_query($sql);
          $diz_id=mysql_insert_id();
          
          while (list($tk1, $trow1) = each($trow['trad'])) {
            $sql="INSERT INTO `".$config_table_prefix."traduzioni` (id_lingue,testo_tradotto_editor) VALUES ('".$trow1['id_lingue']."','".addslashes($trow1['testo_tradotto_editor'])."')";    
            mysql_query($sql);
            $trad_id=mysql_insert_id();
            
            $sql="INSERT INTO `".$config_table_prefix."dizionario#traduzioni_nm` (id_dizionario,id_traduzioni) VALUES ('".$diz_id."','".$trad_id."')";
            mysql_query($sql);
          }
        }
      }
    }
    
    function modTranslate ($rowid,$arr_field) {
      global $config_table_prefix;
      $objUtility=new Utility();
      
      if(!is_array($arr_field)) return false;
      $rs=retRow($this->g_table,$rowid);
      if(!$rs) return false;
      ob_start();
      ?>
      <div class='ez-wr rsTable2-modTranslate'>
        <form name='rsTable2-modTranslate-form' class='rsTable2-modTranslate-form' action='rsAction.php' method='post'>
          <input name="rsTable2Action" type="hidden" value="1">  
          <input name="tabModTranslateDo" type="hidden" value="1">
          <input name="rsTable" type="hidden" value="<?php echo $this->g_table; ?>">
          <input name="rsRow" type="hidden" value="<?php echo $rowid; ?>">
          
          <div class='ez-wr rsTable2-modTranslate-container'>
            <?php
            while (list($key, $field) = each($arr_field)) {
              $str=$rs[$field];
              $trad=ExistTraduction($str);
              $tmpf=permissionField(getTable("rstbl2_campi","","(campo_hidden='".$field."' AND tabella_hidden='".$config_table_prefix.$this->g_table."')"));
              if($trad) {
                while (list($k, $row) = each($trad)) {
                  $unique=$objUtility->getFilenameUnique();
                  $lan=retRow("lingue",$row['id_lingue']);
                  ?> 
                  <div class='ez-wr rsTable2-modTranslate-header'>
                    <div class='ez-fl ez-negmr ez-50 rsTable2-modTranslate-image'>
                      <div class='ez-box'>
                        <img title='<?php echo ln($lan['nome']); ?>' src='<?php echo retFile($lan['immagine_file'],16); ?>' />
                      </div>
                    </div>
                    
                    <div class='ez-last ez-oh rsTable2-modTranslate-field'>
                      <div class='ez-box'>
                        <?php echo $tmpf[0]['titolo_visualizzato']; ?>
                      </div>
                    </div>
                  </div>
                  
                  <div class='ez-wr rsTable2-modTranslate-text'>
                    <div class='ez-box'>
                      <textarea id='<?php echo $unique; ?>' name='<?php echo $field; ?>' class='rsTable2-modTranslate-editor'><?php echo $row['testo_tradotto_editor']; ?></textarea>
                      <input name="<?php echo $field."_tradid"; ?>" type="hidden" value="<?php echo $row['id']; ?>">
                    </div> 
                  </div> 
                  <?php   
                }
              }          
            }
            ?>
          </div>
          <!-- Module 2A -->
          <div class="ez-wr rsTable2-modTranslate-submit">
            <div class="ez-fl ez-negmr ez-50 rsTable2-modTranslate-submit-button">
              <div class="ez-box"><input type="submit" class="rsTable2-modTranslate-save" title="<?php echo ln("Salva le traduzioni"); ?>" value="<?php echo $button_label; ?>" /></div>
            </div>
            <div class="ez-last ez-oh">
              <!-- Module 2B -->
              <div class="ez-wr">
                <div class="ez-fr ez-negml ez-50 rsTable2-modTranslate-submit-note">&nbsp;</div>
                <div class="ez-last ez-oh rsTable2-modTranslate-submit-result">
                  <div class="ez-box">&nbsp;</div>
                </div>
              </div>
            </div>
          </div> 
        </form>
      </div>
      <?php
      $ret=ob_get_contents(); 
      ob_end_clean();
      return $ret; 
    }
    
    function neworder($neworder,$dropped,$table,$sortable) {
      for ($i=0; $i<count($neworder); $i++) {
        if($neworder[$i]==$dropped) {
          $this->dsort($i,$neworder,$dropped,$table,$sortable);
          break;  
        }
      }  
    }
    
    function filterTabName($tab){
      global $config_table_prefix;
      
      $pref_len=strlen($config_table_prefix);
      $ptab=strtolower($tab);
      $ptab=substr($ptab,$pref_len,strlen($ptab)-$pref_len);
      $ptab=str_replace("_", "&nbsp;", $ptab);
      return $ptab;    
    }
    
    function rsValidate($arr,$fun) {
      if(!is_array($arr)) {
        $arr=explode(";",$arr);  
      }
      
      if(count($arr)==0) return false;
      
      //$fun=strtolower($fun);
      for($i=0;$i<count($arr);$i++) {
        $pfield=$arr[$i]; 
        
        $str=$fun;
        if(right($pfield,strlen($str))==$str) {
          return $pfield;    
        }
      
        if(left($pfield,strlen($str))==$str) {
          return $pfield; 
        }
        
        if(left($str,1)=="_" && right($str,1)=="_") {
          if(strpos($pfield, $str)!==FALSE) {
            return $pfield; 
          }  
        }         
      }
      
      return false;
    }
    
    function filterFunName($field,$tab="") {
      global $config_table_prefix;
      
      $pfield=$field; 
      $arrFun=array();
      
      if($tab!="") {
        $result = mysql_query("SELECT * FROM ".$config_table_prefix.$tab);
        $fields = mysql_num_fields($result);
        for ($i=0; $i < $fields; $i++) {
          $name  = mysql_field_name($result, $i);
          if($field==$name){
            $type  = mysql_field_type($result, $i);
            $len   = mysql_field_len($result, $i);
            $flags = mysql_field_flags($result, $i);
            
            if($type=="date") array_push($arrFun,"_date");
            if($type=="int" && $len==1) array_push($arrFun,"_boolean");
            
            if($field!="Ordinamento" && $field!="id" && right($pfield,strlen("_file"))!="_file" && left($pfield,strlen("id_"))!="id_" && right($pfield,strlen("_cry"))!="_cry" && strpos($pfield, "_str_")===FALSE) {
              if($type=="int" && $len>1) array_push($arrFun,"_number");
              if($type=="long" && $len>1) array_push($arrFun,"_number");
              if($type=="double" && $len>1) array_push($arrFun,"_number");
              if($type=="decimal" && $len>1) array_push($arrFun,"_number");
              //if($type=="int" && $len>1 && $len<16) array_push($arrFun,"_int"); 
            }
          }
        }        
      }
      
      $str="Ordinamento";
      if($field==$str) {
        array_push($arrFun,"_ordinamento");    
      }
      
      $str="id";
      if($pfield==$str) {
        array_push($arrFun,"_hidden");    
      }
      
      $str="_str_";
      if(strpos($pfield, $str)!==FALSE) {
        $strprfx=substr($pfield, strpos($pfield, $str), strlen($pfield)-strpos($pfield, $str));
        $pfield=str_replace($strprfx, "", $pfield);
        array_push($arrFun,$strprfx);
      }
      
      $str="_file";
      if(right($pfield,strlen($str))==$str) {
        array_push($arrFun,$str);    
      }
      
      $str="id_";
      if(left($pfield,strlen($str))==$str) {
        array_push($arrFun,$pfield);  
      }
      
      $str="_lst";
      if(right($pfield,strlen($str))==$str) {
        array_push($arrFun,$str);    
      }
      
      $str="_editor";
      if(right($pfield,strlen($str))==$str) {
        array_push($arrFun,$str);    
      }
      
      $str="_hidden";
      if(right($pfield,strlen($str))==$str) {
        array_push($arrFun,$str);    
      }
      
      $str="_cry";
      if(right($pfield,strlen($str))==$str) {
        array_push($arrFun,$str);    
      }
      
      $str="_number";
      if(right($pfield,strlen($str))==$str) {
        array_push($arrFun,$str);    
      }
      
      $str="_suggest#";
      if(strpos($pfield, $str)!==FALSE) {
        $strprfx=substr($pfield, strpos($pfield, $str), strlen($pfield)-strpos($pfield, $str));
        $pfield=str_replace($strprfx, "", $pfield);
        array_push($arrFun,$strprfx);
      }
      
      $str="_perc#";
      if(strpos($pfield, $str)!==FALSE) {
        $strprfx=substr($pfield, strpos($pfield, $str), strlen($pfield)-strpos($pfield, $str));
        $pfield=str_replace($strprfx, "", $pfield);
        array_push($arrFun,$strprfx);
      }
      
      return implode(";", $arrFun);  
    }
    
    function filterColName($field) {
      $pfield=$field; 
      
      $str="Ordinamento";
      if($str==$field) {
        $pfield="&nbsp;";   
      }
      
      $str="_str_";
      if(strpos($pfield, $str)!==FALSE) {
        $strprfx=substr($pfield, strpos($pfield, $str), strlen($pfield)-strpos($pfield, $str));
        $pfield=str_replace($strprfx,"",$pfield);
      }
      
      $str="_file";
      if(right($pfield,strlen($str))==$str) {
        $pfield=substr($pfield, 0, strlen($pfield)-strlen($str));    
      }
      
      $str="id_";
      if(left($pfield,strlen($str))==$str) {
        $pfield=substr($pfield, strlen($str), strlen($pfield)-strlen($str));  
      }
      
      $str="_lst";
      if(right($pfield,strlen($str))==$str) {
        $pfield=substr($pfield, 0, strlen($pfield)-strlen($str));    
      }
      
      $str="_editor";
      if(right($pfield,strlen($str))==$str) {
        $pfield=substr($pfield, 0, strlen($pfield)-strlen($str));    
      }
      
      $str="_hidden";
      if(right($pfield,strlen($str))==$str) {
        $pfield=substr($pfield, 0, strlen($pfield)-strlen($str));    
      }
      
      $str="_cry";
      if(right($pfield,strlen($str))==$str) {
        $pfield=substr($pfield, 0, strlen($pfield)-strlen($str));    
      }
      
      $str="_number";
      if(right($pfield,strlen($str))==$str) {
        $pfield=substr($pfield, 0, strlen($pfield)-strlen($str));    
      }
      
      $str="_suggest#";
      if(strpos($pfield, $str)!==FALSE) {
        $strprfx=substr($pfield, strpos($pfield, $str), strlen($pfield)-strpos($pfield, $str));
        $pfield=str_replace($strprfx,"",$pfield);
      }
      
      $str="_perc#";
      if(strpos($pfield, $str)!==FALSE) {
        $strprfx=substr($pfield, strpos($pfield, $str), strlen($pfield)-strpos($pfield, $str));
        $pfield=str_replace($strprfx,"",$pfield);
      }
      
      if(strpos($pfield, "_")!==FALSE) {
        $tarr=explode("_", $pfield);
        for($i=0;$i<count($tarr);$i++) {
          $tarr[$i]=strtolower($tarr[$i]);
          $tarr[$i]=mb_ucfirst($tarr[$i]); 
        }
        $pfield=implode("_", $tarr);
      }else{
        $pfield=strtolower($pfield);
        $pfield=mb_ucfirst($pfield);  
      }
      
      $pfield=str_replace("_", "&nbsp;", $pfield);
      return $pfield;
    }
    
    function getTitolo($tab,$col="") {
      global $config_table_prefix;
      
      if($col!="") {
        $rs=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$tab."' AND campo_hidden='$col')")); 
        return $rs[0]['titolo_visualizzato'];
      }else{
        $rs=getTable("rstbl2_tabelle","","tabella='".$config_table_prefix.$tab."'"); 
        return $rs[0]['titolo_visualizzato'];  
      } 
    }
    
    function operator($field,$prefix="",$selected="") {
      if($prefix=="") $prefix="op_"; ?>
      <select name="<?=$prefix.$field?>" class="rsTable2-operator" size="1">
        <option value=""></option>
        <option value="LIKE" <?php if($selected=="LIKE") echo "SELECTED" ?>><?php echo ln("contiene il testo"); ?></option>
        <option value=">=" <?php if($selected==">=") echo "SELECTED" ?>><?php echo ln("a partire da"); ?></option>
        <option value="<=" <?php if($selected=="<=") echo "SELECTED" ?>><?php echo ln("fino a"); ?></option>
        <option value="=" <?php if($selected=="=") echo "SELECTED" ?>><?php echo ln("è uguale a"); ?></option>
        <option value=">" <?php if($selected==">") echo "SELECTED" ?>><?php echo ln("è maggiore di"); ?></option>
        <option value="<" <?php if($selected=="<") echo "SELECTED" ?>><?php echo ln("è minore di"); ?></option>
        <option value="<>" <?php if($selected=="<>") echo "SELECTED" ?>><?php echo ln("è diverso da"); ?></option>
      </select>
      <?
    }
    
    function formdata($var, $style, $id, $table, $js, $dateselected=false, $start="", $end=""){
     if($start=="") $start=date("Y")-6;
     if($end=="") $end=date("Y")+3; 
     if ($dateselected) {
    	$arraydatak=explode("-",$dateselected);
    	if(strlen($arraydatak[0])==4) {
        $giorno=$arraydatak[2];
      	$mese=$arraydatak[1];
      	$anno=$arraydatak[0];
      }else{
        $giorno=$arraydatak[0];
      	$mese=$arraydatak[1];
      	$anno=$arraydatak[2];
      }
     }else{
      if($id!="" AND $table!=""){
    	  $querydata="SELECT data".$var." FROM ".$table."  WHERE id=".$id;
    		$querydatares=mysql_query($querydata);
    		$arraydata=mysql_fetch_array($querydatares);
    		$arraydatak=explode("-",$arraydata[0]);
    		$giorno=$arraydatak[2];
    		$mese=$arraydatak[1];
    		$anno=$arraydatak[0];
    	}
     }
     ?>
    	<div class="rsTable2-formdata">
        <select name="giorno<?php echo $var; ?>" class='<?php echo $style; ?>-giorno rsTable2-formdata rsTable2-formdata-gg select form-control'  <?php echo $js; ?>>
      	<option value='00'>--</option>
    			<?php 
          $n=1; 
    			while ($n<=31) {?>
            <option value='<?php echo $n; ?>' <?php if($n==$giorno){echo"selected";}?>><?php echo $n; ?></option><?
    				$n++;
          }
    			?>
        </select>				
      	<select name="mese<?php echo $var; ?>"  class='<?php echo $style; ?>-mese rsTable2-formdata rsTable2-formdata-mm select form-control' <?php echo $js; ?>>
    			<option value='00'>--</option>
    			<?php 
          $n=1; 
    			while ($n<=12) { ?>
            <option value='<?=$n?>'  <?if($n==$mese){echo"selected";}?>><?=$n?></option><?
    				$n++;
          }
    			?>
        </select>		
      	<select name="anno<?php echo $var; ?>"  class='<?php echo $style; ?>-anno rsTable2-formdata rsTable2-formdata-aa select form-control'<?php echo $js; ?>>
    			<option value='0000'>----</option>
    			<?php 
          $n=$start; 
    			while ($n<=$end) {
    			?><option value='<?php echo $n?>'  <?php if($n==$anno){echo"selected";}?>><?php echo $n; ?></option><?
    			$n++;
          }
    			?>
        </select>
      </div><?
    }
    
    function printInsertRow($fun,$col,$tid,$obbl="",$unique="",$defValue="",$rowid="") {
      global $config_table_prefix;
      $objUtility=new Utility;
      
      $col=str_replace("&nbsp;", " ", $col);
      $col=ln($col);
      
      if($obbl=="1") {
        $col=$col." (*)";
      }
       
      if($unique=="1") {
        //$col=$col." (**)";
      } 
      $tmpf=permissionField(retRow("rstbl2_campi",$tid));
      
      if($rowid=="") $rowid="0";
      
      if($defValue=="CURRENT_TIMESTAMP") return true;
      if($this->rsValidate($tmpf['rsPower'],"_hidden")!==FALSE) $fun="_hidden";
      
      rsTable2_BeforePrintInsertRow($this->g_table,$fun,$tmpf,$col,$tid,$obbl,$unique,$defValue,$rowid,$this->g_parentId);
      
      if($fun===FALSE) { ?>
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo  left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box">
              <?php if($this->rsValidate($tmpf['rsPower'],"_textarea")!==FALSE) { ?>
                <textarea class="form-control " name="<?php echo $tid; ?>"><?php echo $defValue; ?></textarea>
              <? }else{ ?>
                <input class="form-control " name="<?php echo $tid; ?>" type="text" value="<?php echo $defValue; ?>" />  
              <? } ?>
            </div>
          </div>
        </div>
        <?                                                     
        return true;
      }
      
      $str="_label";
      if(left($fun,strlen($str))==$str) { 
        if($defValue!="" && $defValue!="0000-00-00 00:00:00" && $defValue!="0000-00-00") { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box"><label name="<?php echo $tid; ?>">
            <?php
            if(is_date($defValue)) {
              if($defValue!="0000-00-00 00:00:00" && $defValue!="0000-00-00") echo dataITA($defValue);
            }else{
              echo $defValue;
            }
            ?>
            </label></div>
          </div>
        </div>
        <?php
        }
        return true;
      }
      
      $str="_hidden";
      if(left($fun,strlen($str))==$str) { 
        if($this->rsValidate($tmpf['rsPower'],"_pwd")!==FALSE) { ?>
          <input type="hidden" name="<?php echo $tid; ?>" value="">
          <input type="hidden" name="<?php echo "c_".$tid; ?>" value="">
        <? }else{ ?>
          <input type="hidden" name="<?php echo $tid; ?>" value="<?php echo $defValue; ?>">
        <? } 
        return true;
      }
      
      $str="id_";
      if(left($fun,strlen($str))==$str) {   
        if(strpos($fun, "_filter#")!==FALSE) {
          $filter=explode(";_filter#", $fun);
          $fun=$filter[0];
          $filter=$filter[1];
        }
        ?>
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box">
              <?php $this->printIdTD($tid,$fun,$defValue,$rowid,$filter); ?>
            </div>
          </div>
        </div>
        <?
        return true;
      }
      
      $str="_str_";
      if(left($fun,strlen($str))==$str) { 
        $struttura=right($fun,strlen($fun)-strlen($str));
        ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box">
              <?php if($defValue=="") $defValue="0";  ?>
              <input type="hidden" name="<?php echo $tid; ?>" class="rsTable2-insert-strutture" value="<?php echo $defValue; ?>" />
              <?php stampaStruttura($struttura,$defValue); ?>
            </div>
          </div>
        </div>
        <?
        return true;
      }
      
      $str="_file";
      if(right($fun,strlen($str))==$str) {  
        $multiple=1;
        if($this->rsValidate($tmpf['rsPower'],"_notmultiple")!==FALSE) $multiple=0; 
        ?>
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-wr rsTable2-insert-uploader-container">
              <?php $tmpun=$objUtility->getFilenameUnique(); ?>
              <input type='button' value='' class='rsTable2-insert-uploader-del-file' title='<?php echo ln("elimina"); ?>' <?php if($defValue=="" || $defValue=="0") echo "style='display:none;'" ?>  />
              <div type="file" id="rsTable2-insert-uploader_<?php echo $tmpun; ?>" class="rsTable2-insert-uploader" rsMultiple=<?php echo $multiple; ?>>&nbsp;</div>
              <input type="hidden" name="<?php echo $tid; ?>" rsRowId="<?php echo $rowid; ?>" class="rsTable2-insert-uploader" value="<?php echo $defValue; ?>" />
              <div class="ex-box rsTable2-insert-uploader-preview">
                <?php if(retFile($defValue)) $this->printFileTD($defValue); ?>
              </div>
            </div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_boolean";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <!-- Module 2A -->
          <div class="ez-wr rsTable2-insert-row-boolean">
            <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l-boolean">
              <div class="ez-box checkbox">
                <input type="checkbox" class="rsTable2-insert-row-l-boolean-check" name="" rsField="<?php echo $tid; ?>" id="" value="1" <?php if($defValue=="1") echo "checked"; ?> />  
                <input type="hidden" name="<?php echo $tid; ?>" id="<?php echo $tid; ?>" value="<?php echo $defValue; ?>" />
              </div>
            </div>
            <div class="ez-last ez-oh">
              <div class="ez-box">
                <label for="<?php echo $tid; ?>" title="<?php echo $col; ?>">
                  <?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?>
                </label>
              </div>
            </div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_date";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box">
              <?php $this->formdata($tid,'','','','',$defValue,'1900'); ?>
            </div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_date_small";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box">
              <?php 
              $currYear=date("Y", time());
              $yy=$currYear-10;
              $this->formdata($tid,'','','','',$defValue,$yy); 
              ?>
            </div>
          </div>
        </div><? 
        return true;       
      }
                             
      $str="_suggest#";
      if(left($fun,strlen($str))==$str) { 
        $pointer=explode("_suggest#", $fun);
        $pointer=$pointer[1];
        ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r rsTable2-suggest">
            <div class="ez-box">
              <?php  
              $this->printIdTD($tid."_suggest","id_".$pointer,"","-1","","");
              ?>
              <input class="form-control rsTable2-suggest-input" name="<?php echo $tid; ?>" type="text" value="<?php echo $defValue; ?>" readonly="readonly" />
            </div>
          </div>
        </div>
        <?
        return true;
      }
      
      $str="_perc#";
      if(left($fun,strlen($str))==$str) { 
        $pointer=explode("_perc#", $fun);
        $pointer=$pointer[1]; 
        $percVal=retRow($this->g_table,$rowid);
        $percVal=$percVal[$pointer];
        ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <!-- Module 2A -->
            <div class="ez-wr rsTable2-perc">
              <div class="ez-fl ez-negmr ez-50 rsTable2-perc-sx">
                <div class="ez-box">
                  <input class="form-control " name="<?php echo $tid; ?>" class="rsTable2-perc-input" pointer="<?php echo $percVal; ?>" type="text" value="<?php if($defValue!="" && $defValue>0) echo number_format($defValue, 4, ',', ''); ?>" style="text-align:right;" />
                </div>
              </div>
              <div class="ez-last ez-oh rsTable2-perc-dx">
                <div class="ez-box">
                  <input class="form-control rsTable2-perc-input2" pointer="<?php echo $percVal; ?>" name="<?php echo $tid; ?>_perc" type="text" value="<?php $tvv=round($percVal-($percVal*$defValue)/100,2); if($tvv>0) echo number_format($tvv, 2, ',', ''); ?>" style="text-align:right;" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <?
        return true;
      }
      
      $str="_cry";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box"><input class="form-control " name="<?php echo $tid; ?>" type="text" value="<?php if($defValue!="") echo number_format($defValue, 2, ',', ''); ?>" style="text-align:right;" /></div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_number";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box"><input name="<?php echo $tid; ?>" type="text" value="<?php echo str_replace(".", ",", $defValue); ?>" style="text-align:right;" /></div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_pwd";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box"><input class="form-control" name="<?php echo $tid; ?>" type="password" value="" /></div>
          </div>
        </div>
        
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo ln("Conferma ").$col; ?>"><?php echo ln("Conferma ").left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box"><input class="form-control" name="<?php echo "c_".$tid; ?>" type="password" value="" /></div>
          </div>
        </div>
        <? 
        return true;       
      }
      
      $str="_editor";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div class="ez-fl ez-negmr ez-50 rsTable2-insert-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-insert-row-r">
            <div class="ez-box"><textarea id="rsTable2-insert-editor-<?php echo $objUtility->getFilenameUnique(); ?>" class="rsTable2-insert-editor" name="<?php echo $tid; ?>"><?php echo tinyBug($defValue); ?></textarea></div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_ordinamento";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-insert-row <?php echo $tmpf['campo_hidden']; ?>" rsField="<?php echo $tid; ?>">
          <div><input name="<?php echo $tid; ?>" type="hidden" value="<?php echo $defValue; ?>" /></div>
        </div><? 
        return true;       
      }
    }
    
    function printIdTD($name,$fun,$selected,$rowid="",$filter="",$fval="") {
      global $config_table_prefix;
      
      $ttable=$this->g_table;
      
      if($rowid=="") $rowid="0";
      $addFilter=array();
      
      if($name>0) {
        $fun2=permissionField(retRow("rstbl2_campi",$name));
      }else{
        $fun2=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$ttable."' AND campo_hidden='".$name."')"));  
        $fun2=$fun2[0];
      }
      
      $nomultiple=false;
      if($this->rsValidate($fun2['rsPower'],"_notmultiple")!==FALSE) $nomultiple=true;
      
      $fieldn=$fun2['campo_hidden'];
      $struttura="";
      if(($fun2=$this->rsValidate($fun2['rsPower'],"_str_"))!==FALSE){
        $struttura=right($fun2,strlen($fun2)-strlen("_str_"));
      }
      
      $str="id_";
      $tab=substr($fun, strlen($str), strlen($fun)-strlen($str));
      if($filter!="") {
        $ftab=substr($filter, strlen($str), strlen($filter)-strlen($str));
        if($fval=="") {
          $fval=retRow($this->g_table,$rowid);
          $fval=$fval[$filter];
        }
      }
        
      if($tab=="") return false;
      
      $cols=permissionField(getTable("rstbl2_campi","Ordinamento ASC","tabella_hidden='".$config_table_prefix.$ttable."'"));
      $str2="_filter#";
      while (list($key, $row) = each($cols)) {
        $rsfunArr=explode(";", $row['rsPower']);
        if(($tmpfun=$this->rsValidate($rsfunArr,$str2))!==FALSE) {
          $tmpfilter=right($tmpfun,strlen($tmpfun)-strlen($str2));
          if($tmpfilter==$fieldn) array_push($addFilter,$row['campo_hidden']); 
        }     
      }
      
      $cols=permissionField(getTable("rstbl2_campi","Ordinamento ASC","tabella_hidden='".$config_table_prefix.$tab."'"));
      if(!$cols) return false;
      
      $pointed=array();
      $str1="_lst";
      while (list($key, $row) = each($cols)) {
        $rsfunArr=explode(";", $row['rsPower']);
        if($this->rsValidate($rsfunArr,$str1)!==FALSE) array_push($pointed, $row['campo_hidden']);    
      }
      
      if(count($pointed)==0) array_push($pointed, $cols[1]['campo_hidden']);
      if($struttura=="") {
        if($filter=="") {
          $ptab=getTable($tab,$pointed[0]." ASC","");
        }elseif($filter!="" && $fval>0){
          $ptab=Table2ByTable1($ftab,$tab,$fval,"",$pointed[0]." ASC"); 
        }elseif($filter!="" && !($fval>0)){
          $ptab=getTable($tab,"","id='-1'");  
        }
      }else{
        $ptab=getStrutturaFull($struttura); 
        array_sort($ptab,$pointed[0]); 
      } 
      
      
      $str="id_";
      $tt_col=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$tab."' AND campo_hidden='".$pointed[0]."')"));
      $tt_rsfunArr=explode(";", $tt_col[0]['rsPower']);
      if(($tt_fun=$this->rsValidate($tt_rsfunArr,$str))!==FALSE) {
        $tt_tab=substr($tt_fun, strlen($str), strlen($tt_fun)-strlen($str));
        
        $tt_cols=permissionField(getTable("rstbl2_campi","Ordinamento ASC","tabella_hidden='".$config_table_prefix.$tt_tab."'"));
        
        $tt_pointed=array();
        $str1="_lst";
        while (list($key, $row) = each($tt_cols)) {
          $rsfunArr=explode(";", $row['rsPower']);
          if($this->rsValidate($rsfunArr,$str1)!==FALSE) array_push($tt_pointed, $row['campo_hidden']);    
        }
        
        if(count($tt_pointed)==0) array_push($tt_pointed, $tt_cols[1]['campo_hidden']);
      }
      ?>
      <select name="<?php echo $name; ?>[]" class="rsTable2-select select form-control" <?php if($rowid=="0" && count($addFilter)==0 && !$nomultiple) echo "MULTIPLE"; ?> rsFilter="<?php echo implode("#RSSEP#",$addFilter); ?>" rsRowId="<?php echo $rowid; ?>" rsTable="<?php echo $this->g_table; ?>">
  			<option value="-1"></option>							
  			<?php                                          
        $tmp_find=-1;
        if($this->g_table=="roles_list") {
          $objUsers = new Users;
          $objUsers->getCurrentUser($intIdutente, $strUsername);
          if(isUserSystem($intIdutente)==0) $rls2=getAllRolesByUser($intIdutente); else $rls2=getAllRoles();
        }
        
        if($this->g_table=="users_list") {
          $objUsers = new Users;
          $objUsers->getCurrentUser($intIdutente, $strUsername);
          if(isUserSystem($intIdutente)==0) $rls2=getAllUsersByUser($intIdutente); else $rls2=getAllUsers();
        }
        
        while (list($key, $row) = each($ptab)) {
          if($this->g_table=="roles_list") {
            $tmp_find=0;
            for ($i=0; $i<count($rls2); $i++) {
							if($rls2[$i]['id']==$row['id']) $tmp_find=1;
						}
          }
          
          if($this->g_table=="users_list") {
            $tmp_find=0;
            for ($i=0; $i<count($rls2); $i++) {
							if($rls2[$i]['id']==$row['id']) $tmp_find=1;
						}
          }
          
          
          if($tmp_find==-1 || $tmp_find==1){
              ?><option value="<?php echo $row['id'] ?>" <?php if($selected==$row['id']) echo "SELECTED"; ?>><?php
          
              reset($pointed);
              while (list($key2, $row2) = each($pointed)) {
                if($tt_tab=="") {
                  echo $row[$row2]."&nbsp;";
                }else{
                  $tt_opt=retRow($tt_tab,$row[$row2]);
                  reset($tt_pointed);
                  while (list($key3, $row3) = each($tt_pointed)) {
                    echo $tt_opt[$row3]."&nbsp;";  
                  }
                }
              }
            ?>
          </option>
        <? }} ?>
      </select>
      <?
      return true;
    }
    
    function printSearchRow($fun,$col,$tid) {
      global $config_table_prefix;
      
      $col=str_replace("&nbsp;", " ", $col);
      
      if($fun===FALSE) { ?>
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-search-row">
          <div class="ez-fl ez-negmr ez-50 rsTable2-search-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo  left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-search-row-r">
            <div class="ez-box"><?php $this->operator($tid,"",$_SESSION["rsTable2_".$this->g_id."_search_arr"]["op_".$tid]); ?><input name="<?php echo $tid; ?>" type="text" value="<?php echo $_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid]; ?>" /></div>
          </div>
        </div>
        <?                                                     
        return true;
      }
      
      $str="id_";
      if(left($fun,strlen($str))==$str) { 
        if(strpos($fun, "_filter#")!==FALSE) {
          $filter=explode(";_filter#", $fun);
          $fun=$filter[0];
          $filter=$filter[1];
        }
        
        $fun2=permissionField(retRow("rstbl2_campi",$tid));
        $struttura="";
        if(($fun2=$this->rsValidate($fun2['rsPower'],"_str_"))!==FALSE){
          $col=right($fun2,strlen($fun2)-strlen("_str_"));
          $col=str_replace("&nbsp;", " ", $col);
          $col=mb_ucfirst($col);
        }
        echo $filter;
        ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-search-row">
          <div class="ez-fl ez-negmr ez-50 rsTable2-search-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-search-row-r">
            <div class="ez-box">
              <?php $this->printIdTD($tid,$fun,$_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid],"",$filter); ?>
            </div>
          </div>
        </div>
        <?
        return true;
      }
      
      $str="_file";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-search-row">
          <div class="ez-fl ez-negmr ez-50 rsTable2-search-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-search-row-r">
            <div class="ez-box">
              <select name="<?php echo $tid; ?>">
    						<option value="-1"></option>							
    							<option value="1" <?php if($_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid]==1) echo "SELECTED"; ?>>presente</option>
    							<option value="0" <?php if($_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid]==0 && isset($_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid])) echo "SELECTED"; ?>>non presente</option>
  		        </select>
            </div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_boolean";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-search-row">
          <div class="ez-fl ez-negmr ez-50 rsTable2-search-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-search-row-r">
            <div class="ez-box">
              <select name="<?php echo $tid; ?>">
    						<option value="-1"></option>							
    							<option value="1" <?php if($_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid]==1) echo "SELECTED"; ?>>selezionato</option>
    							<option value="0" <?php if($_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid]==0 && isset($_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid])) echo "SELECTED"; ?>>non selezionato</option>
  		        </select>
            </div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_date";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-search-row">
          <div class="ez-fl ez-negmr ez-50 rsTable2-search-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-search-row-r">
            <div class="ez-box">
            <?php 
            $this->operator($tid,"",$_SESSION["rsTable2_".$this->g_id."_search_arr"]["op_".$tid]);
            $this->formdata($tid,'','','','',$_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid."_date1"],'1900');
            $this->operator($tid,"op2_",$_SESSION["rsTable2_".$this->g_id."_search_arr"]["op2_".$tid]);
            $this->formdata($tid."_ZZZ",'','','','',$_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid."_date2"],'1900'); 
            ?>
            </div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_date_small";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-search-row">
          <div class="ez-fl ez-negmr ez-50 rsTable2-search-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-search-row-r">
            <div class="ez-box">
            <?php 
            $currYear=date("Y", time());
            $yy=$currYear-10;
            $this->operator($tid,"",$_SESSION["rsTable2_".$this->g_id."_search_arr"]["op_".$tid]);
            $this->formdata($tid,'','','','',$_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid."_date1"],$yy);
            $this->operator($tid,"op2_",$_SESSION["rsTable2_".$this->g_id."_search_arr"]["op2_".$tid]);
            $this->formdata($tid."_ZZZ",'','','','',$_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid."_date2"],$yy); 
            ?>
            </div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_cry";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-search-row">
          <div class="ez-fl ez-negmr ez-50 rsTable2-search-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-search-row-r">
            <div class="ez-box"><?php $this->operator($tid,"",$_SESSION["rsTable2_".$this->g_id."_search_arr"]["op_".$tid]); ?><input name="<?php echo $tid; ?>" type="text" value="<?php echo $_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid] ?>" /></div>
          </div>
        </div><? 
        return true;       
      }
      
      $str="_number";
      if(right($fun,strlen($str))==$str) { ?>  
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-search-row">
          <div class="ez-fl ez-negmr ez-50 rsTable2-search-row-l">
            <div class="ez-box"><label title="<?php echo $col; ?>"><?php echo left($col,$this->g_nCharTD);if(strlen($col)>$this->g_nCharTD) echo "&hellip;"; ?></label></div>
          </div>
          <div class="ez-last ez-oh rsTable2-search-row-r">
            <div class="ez-box"><?php $this->operator($tid,"",$_SESSION["rsTable2_".$this->g_id."_search_arr"]["op_".$tid]); ?><input name="<?php echo $tid; ?>" type="text" value="<?php echo $_SESSION["rsTable2_".$this->g_id."_search_arr"][$tid] ?>" /></div>
          </div>
        </div><? 
        return true;       
      }
    }
    
    function emptyTD() {
      ?><td class="rsTable2-td">&nbsp;</td><?php  
    }
    
    function printFileTD($field){
      $objUtility=new Utility();
      
      if($field>0) { 
        if(isImageByID($field)) { 
          list($width, $height)=getimagesize(retFileAbsolute($field,0,70));
          ?><a href="<?php echo retFile($field,1024); ?>" rel="lytebox" target="_blank"><img class="rsTable2-file-img" src="<?php echo retFile($field,0,70); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" /></a><? 
        }else{ 
          $ext=strtolower(retExtByID($field));
          $imgsrc="";
          $tmp_file=retRow("oggetti",$field);
          if($ext=="pdf") $imgsrc="ico_file_pdf.png";
          if($ext=="xls") $imgsrc="ico_file_xls.png";           
          if($ext=="mov") $imgsrc="ico_file_mov.png";
          if($ext=="doc" || $ext=="docx") $imgsrc="ico_file_doc.png";
          if($ext=="swf" || $ext=="fla") $imgsrc="ico_file_flash.png";
          if($imgsrc=="") $imgsrc="ico_file.png";
          ?>
          <!-- Module 2A -->
          <div class="ez-wr rsTable2-file-not-image-container">
            <div class="ez-fl ez-negmr ez-50 rsTable2-file-not-image-ico">
              <div class="ez-box"><img src="<?php echo $objUtility->getPathBackofficeResources().$imgsrc; ?>" width="16" height="16" /></div>
            </div>
            <div class="ez-last ez-oh rsTable2-file-not-image-name">
              <div class="ez-box">
                <a href="<?php echo retFile($field); ?>" target="_blank"><?php echo trim(str_replace(" ", "&nbsp;", $tmp_file['originalname'])); ?></a>
              </div>
            </div>
          </div><? 
        } 
      }
    }
    
    function printSegueTD($field,$table,$col,$tid){
      ?>
      <div class="ez-wr rsTable2-trocaTesto">
        <?php 
        if(is_date($field)) {
          if($field!="0000-00-00 00:00:00" && $field!="0000-00-00") echo str_replace(" ", "&nbsp;", dataITA($field));
        }else{
          echo troncaTesto($field,$this->g_nCharTD);
        }
        ?>
      </div><?php
      if(strlen($field)>$this->g_nCharTD) { ?>
        <!-- Plain box -->
        <div class="ez-wr rsTable2-segue-container">
          <?php lyteFrame("rsAction.php?rsTable2Action=1&segue=1&tab=".$table."&col=".$col."&id=".$tid, $text="segue"); ?>
        </div><? 
      }  
    }
    
    function printBooleanTD($field) { 
      ?>
      <!-- Plain box -->
      <div class="ez-wr rsTable2-boolean-container">
        <div class="ez-box rsTable2-boolean">
          <?php 
          if($field!=0) {
            $checked="checked";      
          }else{
            $checked="unchecked";  
          } 
          ?>  
          <!-- Plain box -->
          <div class="ez-wr rstbl2-checkbox-container">
            <div class="ez-box rstbl2-checkbox <?php echo $checked; ?>" rsSel="<?php echo $checked; ?>"></div> 
          </div>
        </div> 
      </div>
      <?
    }
    
    function printTD($fun,$field,$col,$tid){
      global $config_table_prefix;
      $objUtility=new Utility();
      
      rsTable2_BeforePrintTD($this->g_table,$fun,$field,$col,$tid,$this->g_permessi['Modifica'],$this->g_permessi['Modifica_rapida']);
      if($this->g_permessi['Modifica_rapida']=="1") $rel="rsWinMod";
      
      if($fun===FALSE) { ?>
        <td class="rsTable2-td" rel="<?php echo $rel; ?>" rsTable="<?php echo $this->g_table; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentID="<?php echo $this->g_parentId; ?>" rsField="<?php echo $col; ?>" rsId="<?php echo $tid; ?>">
          <?php $this->printSegueTD($field,$this->g_table,$col,$tid) ?>  
        </td><?
        return true;
      }
      
      $str="id_";
      if(left($fun,strlen($str))==$str) { 
        if(!($field>-1) || $field=="NULL") {
          $field=0;
        }
        $tab=substr($fun, strlen($str), strlen($fun)-strlen($str));
        if($tab=="") {
          $this->emptyTD();
          return false;
        }
        
        $cols=permissionField(getTable("rstbl2_campi","Ordinamento ASC","tabella_hidden='".$config_table_prefix.$tab."'"));
        if(!$cols) {
          $this->emptyTD();
          return false;
        }
        
        $pointed=array();
        $str1="_lst";
        while (list($key, $row) = each($cols)) {
          $rsfunArr=explode(";", $row['rsPower']);
          if($this->rsValidate($rsfunArr,$str1)!==FALSE) array_push($pointed, $row['campo_hidden']);     
        }
        
        if(count($pointed)==0) array_push($pointed, $cols[1]['campo_hidden']);
        $ptab=retRow($tab,$field);
        
        $str="id_";
        $tt_col=permissionField(getTable("rstbl2_campi","","(tabella_hidden='".$config_table_prefix.$tab."' AND campo_hidden='".$pointed[0]."')"));
        $tt_rsfunArr=explode(";", $tt_col[0]['rsPower']);
        if(($tt_fun=$this->rsValidate($tt_rsfunArr,$str))!==FALSE) {
          $tt_tab=substr($tt_fun, strlen($str), strlen($tt_fun)-strlen($str));
          
          $tt_cols=permissionField(getTable("rstbl2_campi","Ordinamento ASC","tabella_hidden='".$config_table_prefix.$tt_tab."'"));
          
          $tt_pointed=array();
          $str1="_lst";
          while (list($key, $row) = each($tt_cols)) {
            $rsfunArr=explode(";", $row['rsPower']);
            if($this->rsValidate($rsfunArr,$str1)!==FALSE) array_push($tt_pointed, $row['campo_hidden']);    
          }
          
          if(count($tt_pointed)==0) array_push($tt_pointed, $tt_cols[1]['campo_hidden']);
        }
        ?><td class="rsTable2-td-id" rel="<?php echo $rel; ?>" rsTable="<?php echo $this->g_table; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentID="<?php echo $this->g_parentId; ?>" rsField="<?php echo $col; ?>" rsId="<?php echo $tid; ?>">
          <!-- Plain box -->
          <div class="ez-wr rsTable2-puntatore-container">
            <div class="ez-box rsTable2-puntatore"><?php                  
              while (list($key, $row) = each($pointed)) { 
                  if($tt_tab=="") { ?>
                    <a href="" rel="rsOpenWindow_" rsTable="<?php echo $tab; ?>" rsWhere="id='<?php echo $field; ?>'" rsOrd="" rsTxt="" rsTit="<?php echo $tab; ?>" onClick="return false;"><?php echo ln(str_replace(" ", "&nbsp;", $ptab[$row])); ?></a> 
                  <? }else{ 
                    $tt_val=retRow($tt_tab,$ptab[$row]);?>
                    <a href="" rel="rsOpenWindow_" rsTable="<?php echo $tab; ?>" rsWhere="id='<?php echo $field; ?>'" rsOrd="" rsTxt="" rsTit="<?php echo $tab; ?>" onClick="return false;"><?php echo ln(str_replace(" ", "&nbsp;", $tt_val[$tt_pointed[0]])); ?></a>
                  <? } ?>
              <? } ?>
            </div>              
          </div>
        </td><?
        return true;
      }
      
      $str="_label";
      if(right($fun,strlen($str))==$str) { ?>  
        <td class="rsTable2-td" rsTable="<?php echo $this->g_table; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentID="<?php echo $this->g_parentId; ?>" rsField="<?php echo $col; ?>" rsId="<?php echo $tid; ?>">
          <?php $this->printSegueTD($field,$this->g_table,$col,$tid) ?>  
        </td><?
        return true;       
      }
      
      $str="_file";
      if(right($fun,strlen($str))==$str) { ?>  
        <td class="rsTable2-td-file" rel="<?php echo $rel; ?>" rsTable="<?php echo $this->g_table; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentID="<?php echo $this->g_parentId; ?>" rsField="<?php echo $col; ?>" rsId="<?php echo $tid; ?>">
          <!-- Plain box -->
          <div class="ez-wr rsTable2-file">
            <?php $this->printFileTD($field); ?>
          </div>
        </td><? 
        return true;       
      }
      
      $str="_boolean";
      if(right($fun,strlen($str))==$str) { ?>  
        <td class="rsTable2-td rsTable2-td-boolean" rel="<?php echo $rel; ?>" rsTable="<?php echo $this->g_table; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentID="<?php echo $this->g_parentId; ?>" rsField="<?php echo $col; ?>" rsId="<?php echo $tid; ?>">
          <?php $this->printBooleanTD($field); ?>
        </td><? 
        return true;       
      }
      
      $str="_date";
      $str1="_date_small";
      if((right($fun,strlen($str))==$str) || (right($fun,strlen($str1))==$str1)) { ?>  
        <td class="rsTable2-td" rel="<?php echo $rel; ?>" rsTable="<?php echo $this->g_table; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentID="<?php echo $this->g_parentId; ?>" rsField="<?php echo $col; ?>" rsId="<?php echo $tid; ?>">
          <!-- Plain box -->
          <div class="ez-wr rsTable2-date-container">
            <div class="ez-box rsTable2-date">
              <?php if($field!="0000-00-00") echo dataITA($field); ?>
            </div> 
          </div>
        </td><? 
        return true;       
      }
      
      $str="_cry";
      if(right($fun,strlen($str))==$str) { ?>  
        <td class="rsTable2-td" rel="<?php echo $rel; ?>" rsTable="<?php echo $this->g_table; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentID="<?php echo $this->g_parentId; ?>" rsField="<?php echo $col; ?>" rsId="<?php echo $tid; ?>">
          <!-- Plain box -->
          <div class="ez-wr rsTable2-currency-container">
            <div class="ez-box rsTable2-currency">
              <?php if($field!="0.00" && $field!="" && $field!="0") echo "&euro;&nbsp;".currencyITA($field); ?>
            </div> 
          </div>
        </td><? 
        return true;       
      }
      
      $str="_number";
      if(right($fun,strlen($str))==$str) { ?>  
        <td class="rsTable2-td" rel="<?php echo $rel; ?>" rsTable="<?php echo $this->g_table; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentID="<?php echo $this->g_parentId; ?>" rsField="<?php echo $col; ?>" rsId="<?php echo $tid; ?>">
          <!-- Plain box -->
          <div class="ez-wr rsTable2-currency-container">
            <div class="ez-box rsTable2-currency">
              <?php if($field!="0.00" && $field!="" && $field!="0") echo currencyITA($field); ?>
            </div> 
          </div>
        </td><? 
        return true;       
      }
      
      $str="_ordinamento";
      if(right($fun,strlen($str))==$str) { ?>  
        <td class="rsTable2-td rsTable2-td-dragHandle">
          <!-- Plain box -->
          <div class="ez-wr rsTable2-sortable-container">
            <div class="ez-box rsTable2-sortable rsTable2-showSortable"></div> 
          </div>
        </td><? 
        return true;       
      }
    }
    
    function where($str) {
      $this->g_where=$str;
    }
    
    function _insert($colfilter,$button_label="",$len_field="",$rowid="") {
      global $config_table_prefix;
      $objHtml=new Html();
      $objUtility=new Utility();
      
      if(!($rowid>0)) $rowid="";
      if($colfilter=="" && $this->g_colFilter=="") {
        $colfilter="*";
        $this->g_colFilter=$colfilter;
      }elseif($colfilter!="") {
        $this->g_colFilter=$colfilter;
      }
      
      if($len_field=="") $len_field=60;
      
      $table_tit=$this->getTitolo($this->g_table);
      //$sql="SELECT id,id_rstbl2_gruppi FROM `".$config_table_prefix."rstbl2_campi` WHERE tabella_hidden='".$config_table_prefix.$this->g_table."' ORDER BY Ordinamento ASC";
      //$q=mysql_query($sql);
      //$groups=$objUtility->buildRecordset($q);
      $groups=permissionField(getTable("rstbl2_campi","Ordinamento ASC","tabella_hidden='".$config_table_prefix.$this->g_table."'"));
      
      $where="";
      $added=array();
      while (list($key, $row) = each($groups)) {     
        if(!in_array($row['id_rstbl2_gruppi'],$added)) {
          $where.="id='".$row['id_rstbl2_gruppi']."' OR ";
          array_push($added, $row['id_rstbl2_gruppi']);
        }    
      }

      $where = "((".$where."1) AND attivo='1')";
      $where=str_replace("OR 1", "", $where);
      $groups=getTable("rstbl2_gruppi","Ordinamento ASC",$where);
      
      $lr=array();
      $lr['id']=0;
      $lr['nome']="";
      $lr['label']="";
      $lr['id_rstbl2_column']=1;
      array_push($groups, $lr);
      ?>
      <div class="ez-wr rsTable2-insert-container <?php echo $this->g_table; ?>" rsTable="<?php echo $this->g_table; ?>" rsTableId="<?php echo $this->g_id; ?>">
        <form id="rsTable2-insert-form" class="rsTable2-insert-form-<?php echo $this->g_table; ?>" action="rsAction.php" method="post">
          <input name="rsTable2Action" type="hidden" value="1">  
          <input name="tabInsertDo" type="hidden" value="1">
          <input name="curPageUrl" type="hidden" value="<?php echo curPageURL(); ?>">
          <input name="tabName" type="hidden" value="<?php echo rawurlencode($this->g_table); ?>">
          <input name="tabId" type="hidden" value="<?php echo $this->g_id; ?>">
          <input name="colfilter" type="hidden" value="<?php echo $colfilter; ?>">
          <input name="where" type="hidden" value="<?php echo rawurlencode($this->g_where); ?>">
          <input name="tblParent" type="hidden" value="<?php echo rawurlencode($this->g_tblParent); ?>">
          <input name="parentId" type="hidden" value="<?php echo $this->g_parentId; ?>">
          <input name="rowid" type="hidden" value="<?php echo $rowid; ?>">
          <input name="optionsSer" type="hidden" value='<?php echo rawurldecode(stripslashes($this->g_optionsSer)); ?>'>
          
          <!-- Module 2A -->
          <div class="ez-wr rsTable2-insert-groups-container clearfix">
            <!-- Plain box -->
            <div class="ez-wr rsTable2-insert-groups-container-1">
              <?php 
              reset($groups);
              while (list($key1, $row1) = each($groups)) {
                if($row1['id_rstbl2_column']==1) $this->printGroup($row1,$len_field,$rowid);
              } 
              ?> 
            </div>
            <div class="ez-wr clearfix">
              <div class="ez-fl ez-negmr ez-50 rsTable2-insert-groups-container-2 col-sm-6">
                <?php 
                reset($groups);
                while (list($key1, $row1) = each($groups)) {
                  if($row1['id_rstbl2_column']==2) $this->printGroup($row1,$len_field,$rowid);
                } 
                ?> 
              </div>
              <div class="ez-last ez-oh rsTable2-insert-groups-container-3 col-sm-6">
                <?php 
                reset($groups);
                while (list($key1, $row1) = each($groups)) {
                  if($row1['id_rstbl2_column']==3) $this->printGroup($row1,$len_field,$rowid);
                } 
                ?>
              </div>
            </div>
            <!-- Plain box -->
            <div class="ez-wr rsTable2-insert-groups-container-4 col-sm-12">
              <?php 
              reset($groups);
              while (list($key1, $row1) = each($groups)) {
                if($row1['id_rstbl2_column']==4) $this->printGroup($row1,$len_field,$rowid);
              } 
              ?> 
            </div>
          </div>
          <!-- Module 2A -->
          <div class="ez-wr rsTable2-insert-submit clearfix">
            <div class="ez-fl ez-negmr ez-50 rsTable2-insert-submit-button col-sm-6 col-xs-12">
              <div class="ez-box"><input type="submit" class="rsTable2-insert-save btn btn-success btn-block" title="<?php if($button_label=="") echo ln("Salva/Invia la scheda corrente");else echo $button_label; ?>" value="<?php echo $button_label; ?>" /></div>
            </div>
            <div class="ez-last ez-oh">
              <!-- Module 2B -->
              <div class="ez-wr">
                <div class="ez-fr ez-negml ez-50 rsTable2-insert-submit-note col-sm-6 col-xs-12">
                  <?php if($this->obbl_label==true) { ?><div class="ez-box rsTable2-insert-submit-note-text"><?php echo "(*) ".ln("Campi obbligatori"); ?></div><?php } ?>
                  <?php if($this->unique_label==true) { ?><div class="ez-box"><?php //echo "(**) ".ln("Campi univoci"); ?></div><?php } ?>
                </div>
                <div class="ez-last ez-oh rsTable2-insert-submit-result">
                  <div class="ez-box">&nbsp;</div>
                </div>
              </div>
            </div>
          </div>	
        </form>
      </div>
      <?php  
    }
    
    function printGroup($group,$len_field="",$rowid="") {
      global $config_table_prefix;
      $objUtility=new Utility();
      $objUsers = new Users;
      $objConfig = new ConfigTool();
      $objDb = new Db;
      
      $conn = $objDb->connection($objConfig);
      $userid=$_SESSION["user_id"];
      if($userid=="") $userid=$_SESSION["userris_id"];
      $isSystem=$objUsers->isSystem($conn, $userid);
      
      if($len_field=="") $len_field=25;
      $cols=permissionField(getTable("rstbl2_campi","Ordinamento ASC","(tabella_hidden='".$config_table_prefix.$this->g_table."')"));
      
      $countcols=0;
      while (list($key, $row) = each($cols)) {
        if($row['id_rstbl2_gruppi']==$group['id']) {
          $countcols++;    
        }    
      }
      
      $notempty=false;
      reset($cols);
      while (list($key, $row) = each($cols)) {
        if($row['id_rstbl2_gruppi']==$group['id']) {
          $col=$row['campo_hidden'];
          $obbl=0;
          $unique=0;
          
          $rsfunArr=explode(";", $row['rsPower']);
          
          if($this->rsValidate($rsfunArr,"_obligatory")) $obbl=1;
          if($this->rsValidate($rsfunArr,"_unique")) $unique=1;
          
          $hide=true;
          $tfilter=explode(",",$this->g_colFilter);
          if($this->g_colFilter=="*") $hide=false; 
          while (list($key1, $row1) = each($tfilter)) {
            $row1=trim($row1);
            if($row1==$col) $hide=false;   
          }
          
          if($this->rsValidate($rsfunArr,"_system")!==FALSE && !$isSystem) $hide=true;
          if($this->rsValidate($rsfunArr,"_hidden")!==FALSE) $hidden=$hidden+1;
           
          
          if($hide==false) {
            $notempty=true;
            break;
          }
        }
      }
      
      $hidden=0;
      reset($cols);
      while (list($key, $row) = each($cols)) { 
        if($row['id_rstbl2_gruppi']==$group['id']) {
          $rsfunArr=explode(";", $row['rsPower']);
          
          if($this->rsValidate($rsfunArr,"_hidden")!==FALSE) $hidden=$hidden+1;
        }
      }
      
      if($hidden==$countcols) $hidden=true;else $hidden=false; 
      
      reset($cols);
      if($countcols>0 && $notempty) { ?>
        <!-- Layout 1 -->
        <div class="ez-wr rsTable2-insert-groups rsTable2-grp<?php echo $group['id']; ?>" <?php if($hidden) echo "style='display:none;'"; ?>>
          <?php 
          $group['label']=ln($group['label']);
          $tmpTit=trim(replaceEcomerceMarkers($group['label'])); 
          if($tmpTit!="") {
          ?>
            <div class="ez-box rsTable2-insert-groups-titolo"><?php echo $tmpTit; ?></div><?php
          } 
          if($rowid>0) { 
            $tmpcurrval=retRow($this->g_table,$rowid);
          }else{
            $sql="SHOW COLUMNS FROM ".$config_table_prefix.$this->g_table;
            $col_info=mysql_query($sql);
            $col_info=$objUtility->buildRecordset($col_info);
          }
          
          
          while (list($key, $row) = each($cols)) {
            if($row['id_rstbl2_gruppi']==$group['id']) {
              $field=$row['titolo_visualizzato'];
              $col=$row['campo_hidden'];
              $field=str_replace("&nbsp;", " ", $field);
              
              $obbl=0;
              $unique=0;
              
              if($rowid>0) {
                $currval=$tmpcurrval[$col]; 
              }else{
                reset($col_info);
                while (list($key1, $row1) = each($col_info)) {
                  if($row1['Field']==$col) $currval=$row1['Default'];   
                }  
              }
               
              if(mb_strlen($field)>$len_field){                    
                $field=left($field,$len_field);
                $field.="&hellip;";
              }
              
              $rsfunArr=explode(";", $row['rsPower']);
              
              
              if($this->rsValidate($rsfunArr,"_obligatory")) {
                $obbl=1;
                $this->obbl_label=true;
              }
              
              if($this->rsValidate($rsfunArr,"_unique")) {
                $unique=1;
                $this->unique_label=true;
              }
              
              $hide=true; 
              $tfilter=explode(",",$this->g_colFilter);
              if($this->g_colFilter=="*") $hide=false; 
              while (list($key1, $row1) = each($tfilter)) {
                $row1=trim($row1);
                if($row1==$col) $hide=false;   
              }
              
              if($this->rsValidate($rsfunArr,"_system")!==FALSE && !$isSystem) $hide=true;
              
              if($hide){
                $this->printInsertRow("_hidden",$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_str_"))!==FALSE) { 
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"id_"))!==FALSE){
                if(($rsfun2=$this->rsValidate($rsfunArr,"_filter#"))!==FALSE) {
                  $this->printInsertRow($rsfun.";".$rsfun2,$field,$row['id'],$obbl,$unique,$currval,$rowid);
                 }else{
                  $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid); 
                 }   
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_hidden"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_file"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_label"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_boolean"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);    
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_date"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_cry"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_ordinamento"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_editor"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_pwd"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_suggest#"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_perc#"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              }elseif(($rsfun=$this->rsValidate($rsfunArr,"_number"))!==FALSE){
                $this->printInsertRow($rsfun,$field,$row['id'],$obbl,$unique,$currval,$rowid);
              } else { 
                $this->printInsertRow(FALSE,$field,$row['id'],$obbl,$unique,$currval,$rowid);           
              }        
            }
          }
        ?></div><?php
      }
    }
    
    function _print($where="",$tblParent="",$parentId="",$subPrint="",$colfilter="",$onlyMod="") {
      global $config_table_prefix;
      $objHtml=new Html();
      $objUtility=new Utility();
      $objUsers = new Users;
      $objConfig = new ConfigTool();
      $objDb = new Db;
      
      $collimit="";
      if($this->g_col_limit>0) $collimit="LIMIT ".$this->g_col_limit;
      
      $conn = $objDb->connection($objConfig);
      $userid=$_SESSION["user_id"];
      if($userid=="") $userid=$_SESSION["userris_id"];
      $isSystem=$objUsers->isSystem($conn, $userid);
      
      $search=trim($_SESSION["rsTable2_".$this->g_id."_search"]);
      $search=str_replace("%%", "%", $search); 
      if($search=="") $search="1";
      
      if($tblParent!="") $this->g_tblParent=$tblParent;
      if($parentId!="") $this->g_parentId=$parentId;
      if($where!="") $this->g_where=$where;
      if(trim($this->g_where)=="") $this->g_where="1"; 
      
      $cols=array();
      
      //echo "(".$this->g_where." AND ".$search.")";
      
      $table_tit=$this->getTitolo($this->g_table);
      $tmpTab=getTable("rstbl2_tabelle","","tabella='".$config_table_prefix.$this->g_table."'");
      if(count($tmpTab)>0) {
        
        $cols=permissionField(Table2ByTable1("rstbl2_tabelle","rstbl2_campi",$tmpTab[0]['id'],"","Ordinamento ASC ".$collimit));
        
        $arr_filter=explode(",", $colfilter);
        
        if(count($arr_filter)>0 && $colfilter!="") {
          $def_cols=array();
          while (list($key, $row) = each($arr_filter)) {
            $arr_filter[$key]=trim($arr_filter[$key]);  
          }
          
          
          while (list($key, $row) = each($cols)) {
            if(in_array(trim($row['campo_hidden']), $arr_filter)) array_push($def_cols, $row);    
          }
          
          
          $cols=$def_cols;
        }else{
          $colfilter="*";  
        }
        $tbl_nm=Table2ByTable1("rstbl2_tabelle","rstbl2_tabelle_list",$tmpTab[0]['id'],"","Ordinamento ASC"); 
        
      }
      
      $html="";
      $tmpsortable="";
      $sortable="";
      $sortByArrow=false;
      
      reset($cols);
      while (list($key, $row) = each($cols)) {
        $rsPower[$key]=explode(";", $row['rsPower']); 
        
        if(in_array("_ordinamento",$rsPower[$key])) {             
          $sortable=$row['campo_hidden'];
          $sortByArrow=true;
        }
      }
    
    
    ob_start();            
      reset($cols);
      while (list($key, $row) = each($cols)) {
        $field=$row['titolo_visualizzato'];
        $col_name=$row['campo_hidden'];
        $th_class="";
        $rsPower[$key]=explode(";", $row['rsPower']); 
        
        if(in_array("_ordinamento",$rsPower[$key])) { 
          $th_class="rsTable2-th-sortable";
          $field="";
        }
        
        $sortable_class="rsTable2-div-sort-none";
        if($this->g_sort[$col_name]=="ASC") $sortable_class="rsTable2-div-sort-asc";
        if($this->g_sort[$col_name]=="DESC") $sortable_class="rsTable2-div-sort-desc";
        if($this->g_sort[$col_name]=="") $sortable_class="rsTable2-div-sort-none"; 
        
        if(!($this->rsValidate($rsPower[$key],"_system")!==FALSE && !$isSystem)) {
          if(!in_array("_hidden",$rsPower[$key]) && !in_array("_pwd",$rsPower[$key])) {
            $struttura="";
            if(($fun2=$this->rsValidate($rsPower[$key],"_str_"))!==FALSE){
              $field=right($fun2,strlen($fun2)-strlen("_str_"));
              $field=str_replace("&nbsp;", " ", $field);
              $field=mb_ucfirst($field);
            }
            ?>
            <th class="<?php echo $th_class; ?>">
              <div class="<?php if(!$sortByArrow) echo "rsTable2-div-sort ".$sortable_class;else echo "rsTable2-th-sortable-ico"; ?>" rsSort="<?php echo $this->g_sort[$col_name]; ?>" rsField="<?php echo $col_name; ?>"><?php echo str_replace(" ","&nbsp;",$field); ?><?php if($sortByArrow && (str_replace(" ","&nbsp;",$field)=="&nbsp;" || str_replace(" ","&nbsp;",$field)=="")) echo ln("sposta"); ?></div>
            </th>
            <?php            
          }
        }
        
        if($this->g_sort[$col_name]!="") {
          $tmpsortable.="TRIM(LCASE(`".$config_table_prefix.$this->g_table."`.".$col_name.")) ".$this->g_sort[$col_name].",";
        }
      }
      
      $html = ob_get_contents();
      ob_clean();  
         
                  
      if($tmpsortable!="") {
        $sortable=left($tmpsortable,strlen($tmpsortable)-1);
        $sortable=trim($sortable);
      }
      
      if($sortable=="") $sortable="`".$config_table_prefix.$this->g_table."`."."id DESC";
      
      if($this->g_tblParent!="" && $this->g_parentId>0) {
        $table=Table2ByTable1($this->g_tblParent,$this->g_table,$this->g_parentId,"(".$this->g_where." AND ".$search.")", $sortable);
        rsTable2_BeforePrintTable($this->g_table,$table);
        $countTable=count($table);
        if(!$this->g_showAll) $table=$this->g_objPag->buildRs($table);                
      }else{ 
        $table=getTable($this->g_table, $sortable,"(".$this->g_where." AND ".$search.")");
        rsTable2_BeforePrintTable($this->g_table,$table);
        $countTable=count($table);
        if(!$this->g_showAll) $table=$this->g_objPag->buildRs($table);        
      }
      
      $tperm=Table2ByTable1("rstbl2_tabelle","rstbl2_permessi",$tmpTab[0]['id'],"","");
      $this->g_permessi=$tperm[0];
      if($this->g_permessi['Inserimento']=="") $this->g_permessi['Inserimento']="1";
      if($this->g_permessi['Modifica']=="") $this->g_permessi['Modifica']="1";
      if($this->g_permessi['Modifica_rapida']=="") $this->g_permessi['Modifica_rapida']="1";
      if($this->g_permessi['Cancellazione']=="") $this->g_permessi['Cancellazione']="1";
      if($this->g_permessi['Relazioni']=="") $this->g_permessi['Relazioni']="1";
      
      
      if($this->g_tblParent!="" && $this->g_parentId>0) {
        $rowid=Table2ByTable1($this->g_tblParent,$this->g_table,$this->g_parentId,"","id DESC");
      }else{ 
        $rowid=getTable($this->g_table,"id DESC","");
      }
      
      /*
      if(count($rowid)==0 || ($this->g_permessi['Inserimento']=="1" && $this->g_permessi['Modifica']!="1" && $this->g_permessi['Cancellazione']!="1" && $this->g_permessi['Relazioni']!="1")) {
        $rowid=$rowid[0]['id'];
        if($rowid=="") {
          $sql="INSERT INTO `".$config_table_prefix.$this->g_table."` () VALUES ()";
          mysql_query($sql);
          $rowid=mysql_insert_id();
          
          if($this->g_tblParent!="" && $this->g_parentId>0){
            $sql="INSERT INTO `".$config_table_prefix.$this->g_tblParent."#".$this->g_table."_nm` (id_".$this->g_tblParent.",id_".$this->g_table.") VALUES ('".$this->g_parentId."','".$rowid."')";
            mysql_query($sql);  
          }
        }    
        echo "_rsInsert#".$rowid."#";
        return false;  
      }
      */
      ?>	
      <!-- Module 2A -->
      <div class="ez-wr rs-windows-container <?php echo $this->g_table; ?>" rsTableId="<?php echo $this->g_id; ?>" rsTable="<?php echo $this->g_table; ?>">
        <!-- Module 2A -->
        <div class="ez-wr rsTable2-toolbar">
          <?php if($countTable>0) { ?>
            <div class="ez-fl ez-negmr ez-50 rsTable2-toolbar-left">
              <div class="ez-box"><?php echo ln("Numero record: ")."<b>".$countTable."</b>" ?></div>
            </div>
          <? } ?>
          <div class="ez-last ez-oh">
						<!-- Module 2A -->
            <div class="ez-wr rsTable2-toolbar-3-container">
              <div class="ez-fl ez-negmr ez-50" style="width:40px;">
                <div class="ez-box"><?php echo ln("Pagine"); ?></div>
              </div>
              <div class="ez-last ez-oh">
                <!-- Module 2B -->
                <div class="ez-wr">
                  <div class="ez-fr ez-negml ez-50" style="width:100px;text-align:right;">
                    <div class="ez-box"><a href="#" class="rsTable2-toolbar-3-label-a rsTable2-toolbar-3-pag-on" target="_blank"><?php echo ln("suddividi in pagine"); ?></a><a href="#" class="rsTable2-toolbar-3-label-a rsTable2-toolbar-3-pag-off" target="_blank"><?php echo ln("mostra tutti"); ?></a></div>
                  </div>
                  <div class="ez-last ez-oh rsTable2-toolbar-3">
                    <div class="ez-box"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="ez-fl ez-negmr ez-50 rs-windows-col-one">
          <form id="rsTable2-search-form" class="rsTable2-search-form-<?php echo $this->g_table; ?>" action="rsAction.php" method="post" rsTableId="<?php echo $this->g_id; ?>">
            <input name="rsTable2Action" type="hidden" value="1">  
            <input name="tabSearch" type="hidden" value="1">
            <input name="curPageUrl" type="hidden" value="<?php echo curPageURL(); ?>">
            <input name="tabName" type="hidden" value="<?php echo rawurlencode($this->g_table); ?>">
            <input name="tabId" type="hidden" value="<?php echo $this->g_id; ?>">
            <input name="colfilter" type="hidden" value="<?php echo $colfilter; ?>">
            <input name="where" type="hidden" value="<?php echo rawurlencode($this->g_where); ?>">
            <input name="tblParent" type="hidden" value="<?php echo rawurlencode($this->g_tblParent); ?>">
            <input name="parentId" type="hidden" value="<?php echo $this->g_parentId; ?>">
            <input name="showAll" type="hidden" value="<?php echo $this->g_showAll; ?>">
            <input name="optionsSer" type="hidden" value='<?php echo rawurldecode(stripslashes($this->g_optionsSer)); ?>'>
            
            <!-- Module 3A -->
            <div class="ez-wr rsTable2-search-title">
              <div class="ez-fl ez-negmx ez-33 rsTable2-search-label">
                <div class="ez-box"><?php echo ln("Ricerche"); ?></div>
              </div>
              <div class="ez-fl ez-negmr ez-33 rsTable2-search-apply">
                <div class="ez-box"><input type="submit"  class="form-control rsTable2-search-doapply" title="<?php echo ln("Applica la ricerca");?>" value="" /></div>
              </div>
              <div class="ez-last ez-oh rsTable2-search-cancel">
                <div class="ez-box"><input type="button" class="form-control rsTable2-search-docancel" title="<?php echo ln("Azzera la ricerca");?>" value="" /></div>
              </div>
            </div>
            
            <!-- Plain box -->
            <div class="ez-wr rsTable2-search-container" rsTableId="<?php echo $this->g_id; ?>">
              <?php
              reset($cols); 
              while (list($key, $row) = each($cols)) {
                $field=$row['titolo_visualizzato'];
                $rsfunArr=explode(";", $row['rsPower']);
                
                if(!($this->rsValidate($rsfunArr,"_system")!==FALSE && !$isSystem)) {
                  if($this->rsValidate($rsfunArr,"_hidden")===FALSE) {
                    if(($rsfun=$this->rsValidate($rsfunArr,"id_"))!==FALSE) { 
                      if(($rsfun2=$this->rsValidate($rsfunArr,"_filter#"))!==FALSE) {
                        $this->printSearchRow($rsfun.";".$rsfun2,$field,$row['id']);
                      }else{
                        $this->printSearchRow($rsfun,$field,$row['id']); 
                      }
                    }elseif(($rsfun=$this->rsValidate($rsfunArr,"_file"))!==FALSE){
                      $this->printSearchRow($rsfun,$field,$row['id']);
                    }elseif(($rsfun=$this->rsValidate($rsfunArr,"_boolean"))!==FALSE){
                      $this->printSearchRow($rsfun,$field,$row['id']);    
                    }elseif(($rsfun=$this->rsValidate($rsfunArr,"_date"))!==FALSE){
                      $this->printSearchRow($rsfun,$field,$row['id']);
                    }elseif(($rsfun=$this->rsValidate($rsfunArr,"_cry"))!==FALSE){
                      $this->printSearchRow($rsfun,$field,$row['id']);
                    }elseif(($rsfun=$this->rsValidate($rsfunArr,"_number"))!==FALSE){
                      $this->printSearchRow($rsfun,$field,$row['id']);
                    }elseif(($rsfun=$this->rsValidate($rsfunArr,"_ordinamento"))!==FALSE){
                      $this->printSearchRow($rsfun,$field,$row['id']);
                    } else { 
                      $this->printSearchRow(FALSE,$field,$row['id']);           
                    } 
                  }
                }       
              } 
              ?> 
            </div>
          </form>
        </div>
        <div class="ez-last ez-oh rs-windows-col-two" rsTableId="<?php echo $this->g_id; ?>">
          
          <!-- Module 4A -->
          <div class="ez-wr rsTable2-toolbar">
            <div class="ez-fl ez-negmx ez-25" style="display:none;">
              <div class="ez-box"></div> 
            </div> 
            <div class="ez-fl ez-negmx ez-25" style="width:95px;padding-left:10px;">
              <?php if($this->g_where=="1" && $this->g_permessi['Inserimento']=="1") { ?>
                <!-- Module 2A -->
                <div class="ez-wr">
                  <div class="ez-fl ez-negmr ez-50" style="width:25px;">
                    <div class="ez-box"><div class="ez-box"><input type="button" class="form-control rsTable2-insert-doinsert" title="<?php echo ln("Aggiungi");?>" value="" rsTable="<?php echo $this->g_table; ?>" rsTableId="<?php echo $this->g_id; ?>" rsColfilter="<?php echo $colfilter; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentId="<?php echo $this->g_parentId; ?>" /></div></div>
                  </div>
                  <div class="ez-last ez-oh">
                    <div class="ez-box"><?php echo ln("Aggiungi"); ?></div>
                  </div>
                </div>
  			  <? } ?>
            </div>
            <?php if($countTable>0) { ?>
              <div class="ez-fl ez-negmr ez-25" style="width:117px;">
                <!-- Module 2A -->
                <div class="ez-wr">
                  <div class="ez-fl ez-negmr ez-50" style="width:23px;">
                    <div class="ez-box"><div class="rsTable2-tb-selectable checkbox" rsTable="<?php echo $this->g_table; ?>"><input type="checkbox" /></div></div>
                  </div>
                  <div class="ez-last ez-oh">
                    <div class="ez-box"><?php echo ln("Seleziona tutti"); ?></div>
                  </div>
                </div>
                
              </div> 
            <? } ?>
            
            <?php if($this->g_permessi['Cancellazione']=="1" && $countTable>0) { ?>
              <div class="ez-last ez-oh">
                
                <!-- Module 2A -->
                <div class="ez-wr">
                  <div class="ez-fl ez-negmr ez-50" style="width:25px;">
                    <div class="ez-box"><input class="rsTable2-delete-row-input" type="button" value="" rsTable="<?php echo $this->g_table; ?>" title="<?php echo ln("Elimina selezionati"); ?>" /></div>
                  </div>
                  <div class="ez-last ez-oh">
                    <div class="ez-box"><?php echo ln("Elimina selezionati"); ?></div>
                  </div>
                </div>  
                 
              </div>
            <? } ?> 
          </div>
          
          <?php if($countTable>0) { ?>
            <!-- Plain box -->
            <div class="ez-wr rsTable2-container rsTable2-tab-<?php echo $this->g_table; ?>" rsTableId="<?php echo $this->g_id; ?>">	
              <table rsTableId="<?php echo $this->g_id; ?>"><thead><tr>
                <th class="rsTable2-th-selectable" rsTable="<?php echo $this->g_table; ?>"><!--<input type="checkbox" />-->&nbsp;</th>
                <?php if($this->g_permessi['Cancellazione']=="1") { ?>
                  <!--
                  <th class="rsTable2-th-delete" rsTable="<?php echo $this->g_table; ?>"><div class='rsTable2-th-delete-ico'></div></th>
                  -->
                <? } ?>
                <?php if($this->g_permessi['Modifica']=="1") { ?>
                  <th class="rsTable2-th-modify" rsTable="<?php echo $this->g_table; ?>"><div class='rsTable2-th-modify-ico'><?php echo ln("modifica"); ?></div></th>
                <? }else{ 
                    ?><th class="rsTable2-th-modify" style="width:15px;" rsTable="<?php echo $this->g_table; ?>">&nbsp;</th>
                <? }
                
                if(count($tbl_nm)>0 && $this->g_permessi['Relazioni']=="1") {
                  ?><th class="rsTable2-th-nm" rsTable="<?php echo $this->g_table; ?>"><div class='rsTable2-th-nm-ico'></div></th><?php  
                }else{ 
                    ?><?php
                }
                
                echo $html;
                
                if($tblParent!="") $this->g_tblParent=$tblParent;
                if($parentId!="") $this->g_parentId=$parentId;
                if($this->g_showAll==true) $this->g_objPag->g_perPag=1000;
                
                /*                
                if($this->g_tblParent!="" && $this->g_parentId>0) {
                  $table=Table2ByTable1($this->g_tblParent,$this->g_table,$this->g_parentId,"(".$this->g_where." AND ".$search.")",$sortable);
                  rsTable2_BeforePrintTable($this->g_table,$table);
                  $table=$this->g_objPag->buildRs($table);
                }else{ 
                  $table=getTable($this->g_table,$sortable,"(".$this->g_where." AND ".$search.")");
                  rsTable2_BeforePrintTable($this->g_table,$table);
                  $table=$this->g_objPag->buildRs($table);
                }
                */                
                
                if($sortByArrow) {          
                  $sql="SELECT MAX(".$sortable.") FROM `".$config_table_prefix.$this->g_table."`";
                  $query=mysql_query($sql);
                  $query=mysql_fetch_array($query);
                  if($query['MAX('.$sortable.')']==0) {
                    $arrTable=getTable($this->g_table,"id ASC","");
                    while (list($tkey, $trow) = each($arrTable)) {
                      $sql="SELECT MAX(".$sortable.") FROM `".$config_table_prefix.$this->g_table."`";
                      $tmax=mysql_query($sql);
                      $tmax=mysql_fetch_array($tmax);
                      $tmax=$tmax['MAX('.$sortable.')'];
                      
                      $sql="UPDATE `".$config_table_prefix.$this->g_table."` SET ".$sortable."='".($tmax+10)."' WHERE id='".$trow['id']."'";  
                      mysql_query($sql);
                    }  
                  }      
                }
                
                ?></tr></thead><tbody>
                <?php 
                $tmp_find=-1;
                if($this->g_table=="roles_list") {
                  $objUsers = new Users;
                  $objUsers->getCurrentUser($intIdutente, $strUsername);
                  if(isUserSystem($intIdutente)==0) $rls2=getAllRolesByUser($intIdutente); else $rls2=getAllRoles();
                }
                
                if($this->g_table=="users_list") {
                  $objUsers = new Users;
                  $objUsers->getCurrentUser($intIdutente, $strUsername);
                  if(isUserSystem($intIdutente)==0) $rls2=getAllUsersByUser($intIdutente); else $rls2=getAllUsers();
                }
                
                
                while (list($key, $row) = each($table)) { 
                  
                  if($this->g_table=="roles_list") {
                    $tmp_find=0;
                    $tmp_pid=retRow("roles",$row['id_roles']);
                    for ($i=0; $i<count($rls2); $i++) {
        		       if($rls2[$i]['id']==$tmp_pid['id']) $tmp_find=1;
        			}
                  }
                  
                  if($this->g_table=="users_list") {
                    $tmp_find=0;
                    $tmp_pid=retRow("users",$row['id_users']);
                    for ($i=0; $i<count($rls2); $i++) {
        			   if($rls2[$i]['id']==$tmp_pid['id']) $tmp_find=1;
        			}
                  }
                  
                  if($tmp_find==-1 || $tmp_find==1){
                  ?>
                  <tr class="<?php if($key%2==0) echo "rsTable2-tr-pari";else echo "rsTable2-tr-dispari"; ?> rsTable2-tr <?php if(in_array($row['id'],$_SESSION["rsTable2_".$this->g_table."_selection"])) echo "rsTable2-selected"; ?>" rsTable2ID="<?php echo $row['id']; ?>" rsTable="<?php echo $this->g_table; ?>" rsSortable="<?php echo $sortable; ?>">
                    <td class="rsTable2-td-selectable" rsTable2ID="<?php echo $row['id']; ?>"><input type="checkbox" <?php if(in_array($row['id'],$_SESSION["rsTable2_".$this->g_table."_selection"])) echo "checked"; ?> /></td>
                    
                    <?php
                    ob_start();
                    reset($cols);
                    while (list($key1, $row1) = each($cols)) {
                      $field=$row1['campo_hidden']; 
                      $rsfunArr=$rsPower[$key1];
                      
                      if(!($this->rsValidate($rsfunArr,"_system")!==FALSE && !$isSystem)) {
                        if($this->rsValidate($rsfunArr,"_hidden")===FALSE) {
                          if(($rsfun=$this->rsValidate($rsfunArr,"id_"))!==FALSE) { 
                            $this->printTD($rsfun,$row[$field],$field,$row['id']);      
                          }elseif(($rsfun=$this->rsValidate($rsfunArr,"_file"))!==FALSE){
                            $this->printTD($rsfun,$row[$field],$field,$row['id']);
                          }elseif(($rsfun=$this->rsValidate($rsfunArr,"_label"))!==FALSE){
                            $this->printTD($rsfun,$row[$field],$field,$row['id']);
                          }elseif(($rsfun=$this->rsValidate($rsfunArr,"_boolean"))!==FALSE){
                            $this->printTD($rsfun,$row[$field],$field,$row['id']);    
                          }elseif(($rsfun=$this->rsValidate($rsfunArr,"_date"))!==FALSE){
                            $this->printTD($rsfun,$row[$field],$field,$row['id']);
                          }elseif(($rsfun=$this->rsValidate($rsfunArr,"_cry"))!==FALSE){
                            $this->printTD($rsfun,$row[$field],$field,$row['id']);
                          }elseif(($rsfun=$this->rsValidate($rsfunArr,"_number"))!==FALSE){
                            $this->printTD($rsfun,$row[$field],$field,$row['id']);
                          }elseif(($rsfun=$this->rsValidate($rsfunArr,"_ordinamento"))!==FALSE){
                            $this->printTD($rsfun,$row[$field],$field,$row['id']);
                          }elseif(($rsfun=$this->rsValidate($rsfunArr,"_pwd"))!==FALSE){
                            //$this->printTD($rsfun,$row[$field],$field,$row['id']);
                          } else { 
                            $this->printTD(FALSE,$row[$field],$field,$row['id']);           
                          } 
                        }
                      }
                    }
                    $html2=ob_get_contents();
                    ob_clean();
                    
                    if($this->g_permessi['Cancellazione']=="1") { ?>
                      <!--
                      <td class="rsTable2-td rsTable2-td-delete" rsTable2ID="<?php echo $row['id']; ?>" rsTable="<?php echo $this->g_table; ?>">
                        <div class="ez-wr rsTable2-delete-container">
                          <div class="ez-box rsTable2-delete rsTable2-show-delete" title="<?php echo ln("Elimina questa riga"); ?>" rsTable2ID="<?php echo $row['id']; ?>" rsTable="<?php echo $this->g_table; ?>"></div> 
                        </div>
                      </td>
                      -->
                    <? } ?>
                    
                    <?php if($this->g_permessi['Modifica']=="1") { ?>
                      <td class="rsTable2-td rsTable2-td-modify" rsTable2ID="<?php echo $row['id']; ?>" rsTable="<?php echo $this->g_table; ?>">
                        
                        <!-- Plain box -->
                        <div class="ez-wr rsTable2-modify-container">
                          <div class="ez-box rsTable2-modify rsTable2-show-modify" title="<?php echo ln("Modifica questa riga"); ?>" rsTable="<?php echo $this->g_table; ?>" rsTableId="<?php echo $this->g_id; ?>" rsColfilter="<?php echo $colfilter; ?>" rsTableParent="<?php echo $this->g_tblParent; ?>" rsTableParentId="<?php echo $this->g_parentId; ?>" rsRow="<?php echo $row['id']; ?>"></div> 
                        </div>
                        
                      </td>
                    <? } else{ ?>
                        <td class="rsTable2-td rsTable2-td-modify-no" rsTable2ID="<?php echo $row['id']; ?>" rsTable="<?php echo $this->g_table; ?>">&nbsp;</td>    
                    <? } ?>
                    
                    <?php 
                    
                    if(count($tbl_nm)>0 && $this->g_permessi['Relazioni']=="1") { ?>
                      <td class="rsTable2-td rsTable2-td-nm" rsTable2ID="<?php echo $row['id']; ?>" rsTable="<?php echo $this->g_table; ?>">
                        <!-- Plain box -->
                        <div class="ez-wr rsTable2-nm-container">
                          <div class="ez-box rsTable2-nm rsTable2-show-nm"></div> 
                        </div>
                      </td>
                      <?php  
                    }else{ ?>
                            
                    <? }
                    
                    echo $html2;
                     
                  ?></tr>   
                <? }} ?>
              </tbody></table>
              <?php $this->g_objPag->_print(); ?>
            </div>
          <? }else{ ?>
            <!-- Plain box -->
            <div class="ez-wr rsTable2-empty-message">
              <div class="ez-box"><?php //echo ln("Non ci sono dati da visualizzare."); ?></div> 
            </div>
          <? } ?>
        </div>
      </div>
    <?php
    }
  }
?>