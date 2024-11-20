<?php
  Class rsTable {
    var $g_table;
    var $g_config_table_prefix;
    var $cell;
    var $g_printSearch;
              
    /*PREFISSI DA METTERE SUI NOMI DELLE CELLE IN MYSQL
    _hidden = 'colonna non visibile';
    _id_nometabella = '_id dichiara una cella puntata, _nometabella è il nome della tabella a cui punta la colonna'
    */
    
    function rsTable($table) {
      global $config_table_prefix;
      
      $this->g_config_table_prefix=$config_table_prefix;
      $this->g_table=$table;
      $this->g_printSearch=true;
    }
    
    function _print($filter="",$tblparent="",$parent="",$subPrint="",$colfilter="",$onlyMod="") {
      global $cell;
      
      $objUtility = new Utility;
      $objConfig = new ConfigTool();
      $objHtml = new Html; 
      
      $dbname = $objConfig->get("db-dbname");
      
      $g_table=$this->g_table;
      $g_config_table_prefix=$this->g_config_table_prefix;
      
      if($filter!="") $where="WHERE $filter"; 
      if($colfilter=="") $colfilter="*"; 
      
      $sql="SELECT $colfilter FROM `".$this->g_config_table_prefix.$this->g_table."` $where ";
      $rsql="";
      if(function_exists(str_replace($g_config_table_prefix,"", $g_table)."_before_print")) eval("\$rsql=".str_replace($g_config_table_prefix,"", $g_table)."_before_print(\"$sql\",\"$filter\",\"$tblparent\",\"$parent\",\"$subPrint\",\"$colfilter\",\"$onlyMod\");");
      if($rsql!="") $sql=$rsql;
      
      $objDb = new Db;
    $objUsers = new Users;
    $objConfig = new ConfigTool();
    $conn = $objDb->connection($objConfig);
    
    $objUsers->getCurrentUser($intIdutente, $strUsername);
    
    if($intIdutente!=0) { 
      $modifica="";
      $elimina="";
      $aggiungi="";
      
      $tmptabelle=getTable("tabelle","","Nome='".$g_config_table_prefix.$g_table."'");
      $rs=$objUsers->rolesByTable($g_config_table_prefix.$g_table,$tmptabelle[0]['id']);
      if(count($rs)>0) {
        $rs=$objUsers->usersGetRolesEx($conn, $intIdutente);
        while (list($key, $row) = each($rs)) {
          $role=$row['id'];
          $rs2=$objUsers->tablesByRole($role);
          while (list($key2, $row2) = each($rs2)) {
            if($row2['Nome']==$g_config_table_prefix.$g_table) {
              if($modifica!="1") $modifica=$row2['Modifica'];
              if($elimina!="1") $elimina=$row2['Elimina'];
              if($aggiungi!="1") $aggiungi=$row2['Aggiungi'];
              $notElimina=""; //disabilita l'eliminazione delle immaggini e files
            }
          }
        }
      }else{
        if($modifica!="1") $modifica=$tmptabelle[0]['Modifica'];
        if($elimina!="1") $elimina=$tmptabelle[0]['Elimina'];
        if($aggiungi!="1") $aggiungi=$tmptabelle[0]['Aggiungi'];
        $notElimina=""; //disabilita l'eliminazione delle immaggini e files  
      }
    }
    
    if($onlyMod!="") {
      if($onlyMod=="2") $modifica="0";
      $elimina="0";
      $aggiungi="0";
      $notElimina="1";  //abilita l'eliminazione delle immaggini e files
    }
    
    $srcWhere=$objUtility->sessionVarRead($g_config_table_prefix.$g_table."srcWhere");
    $currpag=$objUtility->sessionVarRead($g_config_table_prefix.$g_table."currpag");
    $currpagsel=$objUtility->sessionVarRead($g_config_table_prefix.$g_table."currpagSel");
    $currpag1=$currpag;
    if($currpag=="" || $currpag=="y") $currpag=1;
    
    $srcWhere=str_replace("()", "", $srcWhere);
    
    $ppsel="";
    $ppsel2="";
    if(count($_SESSION[$g_config_table_prefix.$g_table."checkSel"])>0 && $currpagsel!="1") $ppsel="1";
    if($currpagsel=="1") $ppsel2="1"; 
    
    if($currpagsel=="1" && is_array($_SESSION[$g_config_table_prefix.$g_table."checkSel"])) {
      if($srcWhere!="") $srcWhere.=" AND ";
      for($k=0;$k<count($_SESSION[$g_config_table_prefix.$g_table."checkSel"]);$k++) {
        $srcWhere.="id='".$_SESSION[$g_config_table_prefix.$g_table."checkSel"][$k]."'  OR ";
      }
      $srcWhere = substr($srcWhere, 0, strlen($srcWhere)-4);
    }
    
    if($srcWhere!="") {
      if(!strpos($sql, "WHERE")) {
        $sql.=" WHERE $srcWhere";
        
      } else {
        $sql.=" AND $srcWhere";
      }
    }
    if(!$_SESSION[('tmp_order'.$g_config_table_prefix.$g_table)]) {
      $sql2=$sql." ORDER BY id ASC";
    } else {
      //$sql2=$sql." ORDER BY id DESC";
      $sql2=$sql." ORDER BY ".$_SESSION[('tmp_order'.$g_config_table_prefix.$g_table)]." ".$_SESSION[('tmp_ordert'.$g_config_table_prefix.$g_table)];
      //echo $sql2;
    }
    
    $perpag="30";
    $totres=mysql_query($sql2); 
    $totres=mysql_num_rows($totres);
    $npags=ceil($totres/$perpag);
    
    if($currpag>$npags) $currpag=1;
    
    $pstart=($currpag-1)*$perpag;
    $pend=$perpag;
    
    $objUtility->sessionVarUpdate($g_config_table_prefix.$g_table."limit","");
    if($currpag1!="y") {
      $sql2=$sql2." LIMIT $pstart, $pend";
    }
    
    if($npags>1) $objUtility->sessionVarUpdate($g_config_table_prefix.$g_table."limit"," LIMIT $pstart, $pend");
    
    $result = mysql_query($sql2);
    global $config_table_prefix;
    
    $tget="id".$g_config_table_prefix.$g_table;
    $idmod=$objUtility->sessionVarRead($tget);
    ?>
    
    <script language="JavaScript" type="text/javascript">
      
      $(document).ready(function(){
       $("#<? echo $g_config_table_prefix.$g_table; ?>checkboxall").click(function()
        {
         var checked_status = this.checked;
         $("input[name='<? echo $g_config_table_prefix.$g_table; ?>checkSel[]']").each(function(){
          this.checked = checked_status;
          $("#checkSelV"+this.value).remove();
          $("<input type='hidden' id='checkSelV"+this.value+"' name='checkSelV[]' value='"+this.value+"_"+this.checked+"' />").appendTo("form#<? echo $g_config_table_prefix.$g_table; ?>");
         });
        });
        
        $("input[name='<? echo $g_config_table_prefix.$g_table; ?>checkSel[]']").click(function () {
          $("#checkSelV"+this.value).remove();
          $("<input type='hidden' id='checkSelV"+this.value+"' name='checkSelV[]' value='"+this.value+"_"+this.checked+"' />").appendTo("form#<? echo $g_config_table_prefix.$g_table; ?>");
        })
      });
      
      function confirmDelete() {
      	if (!(confirm("Sei sicuro di voler procedere?"))) {
      		return false;
      	}
      }
      
      function copia(area) {
        var appoggio=area;
        appoggio.focus();
        appoggio.select();
        intervallo=appoggio.createTextRange();
        intervallo.execCommand("Copy");
      }
      
      function cambiaTD (element,txt,c,rid) {
        if(rid==1) {
          element.innerHTML=txt.substr(0,200)+" ...<span id='cambiatd' onclick=\"cambiaTD(getElementById('"+element.id+"'),testo"+c+","+c+","+0+");\">(continua)</span>";
        } else {
          element.innerHTML=txt+" ...<span id='cambiatd' onclick=\"cambiaTD(getElementById('"+element.id+"'),testostrip"+c+","+c+","+1+");\">(riduci)</span>";;
        }
      }
    </script>
    <?php if($result) { ?>
      <?php if($currpag1=="y") $currpag="0";
      
      $table=$g_config_table_prefix.$g_table;
      $objUtility->sessionVarUpdate($table."currentPage", $_SERVER["PHP_SELF"]."?".$_SERVER['QUERY_STRING']);
      $objUtility->sessionVarUpdate($table."parent", $parent);
      $objUtility->sessionVarUpdate($table."tblparent", $tblparent);
      $objUtility->sessionVarUpdate($table."subPrint", $subPrint);
      $objUtility->sessionVarUpdate($table."colfilter", $colfilter);
      $objUtility->sessionVarUpdate($table."filter", $filter);
      
      global $currentPage;
      $currentPage=$_SERVER["PHP_SELF"]."?".$_SERVER['QUERY_STRING'];
      
      ?>
      <form name="<? echo $g_config_table_prefix.$g_table; ?>" id="<? echo $g_config_table_prefix.$g_table; ?>" action="rsAction.php" method="post">
  			<input type="hidden" name="table" value="<?php echo $g_config_table_prefix.$g_table; ?>"/>
  			<input type="hidden" name="currentPage" value="<?php echo $currentPage; ?>"/>
  			<input type="hidden" name="parent" value="<?=$parent?>" />
  			<input type="hidden" name="tblparent" value="<?=$tblparent?>" />
  			<input type="hidden" name="subPrint" value="<?=$subPrint?>" />
  			<input type="hidden" name="colfilter" value="<?=$colfilter?>" />
  			<input type="hidden" name="filter" value="<?=urlencode($filter)?>" />
  			
  			<?php if(function_exists(str_replace($g_config_table_prefix,"", $g_table)."_after_print_hidden_input")) eval(str_replace($g_config_table_prefix,"", $g_table)."_after_print_hidden_input(0);"); ?>
  			
        <?php if($this->g_printSearch==true) {
          $tblLabel=retTableLabelInsert($g_config_table_prefix.$g_table);
  ?>
  <div class="inputdata" style="clear:right">
    <div class="ricerca">
      <div class="elemento" style="float:left;">
				<div class="labelbig" ><label><b><?php echo $tblLabel; ?></b> </label></div>
			</div> 
			<div class="elemento" style="padding-right:17px;">
        <img style="cursor:pointer;" src="<?php echo $objUtility->getPathBackofficeResources() ?>cerca.gif" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>cerca_over.gif";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>cerca.gif";' alt="cerca.gif, 567B" title="ricerche"  onClick="ricerca('<?=$g_table?>search');">
		    <input type="image" name="act_CERCA-DO" src="<?php echo $objUtility->getPathBackofficeResources() ?>azzera.gif" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>azzera_over.gif";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>azzera.gif";' alt="azzera ricerche" title="azzera ricerche" onClick="return confirmDelete()" />
      </div>
    </div>
	 <div id="<?=$g_table?>search" style="height:0px;overflow:hidden;background-color: #CED3DD;border-bottom:0px #CCCCCC solid;margin-bottom:5px;padding-left:10px;margin-right:30px;" >
        <?php
        $j=0;
        while ($j < mysql_num_fields($result)) {    
          $field = mysql_fetch_field($result);
          $key=$field->name;
          $vprint=true;
          if(function_exists(str_replace($g_config_table_prefix,"", $g_table)."_".$key."_search_before_print")) eval("\$vprint=".str_replace($g_config_table_prefix,"", $g_table)."_".$key."_search_before_print();");
          
          if($vprint) {
            $tmpkey=$key;
            
            if(strpos($key, "_thm")) {
              $substr=substr($key, strpos($key, "_thm"), strlen($key)-strpos($key, "_thm"));
              $key=str_replace($substr,"",$key);
            }
            
            $strprfx="";
            if(strpos($key, "_str_")) {
              $struttura=substr($key, strpos($key, "_str_"), strlen($key)-strpos($key, "_str_"));
              $key=str_replace($struttura,"",$key);
              $struttura=str_replace("_str_","",$struttura);
              $strprfx="_str_".$struttura;
            }
            
            $tmpkey=retLabelInsert($g_config_table_prefix.$g_table, $key);
            
            if($g_table=="oggetti" && $key=="originalname") $tmpkey="Nome";
            if($g_table=="oggetti" && $key=="ext") $tmpkey="Estensione";
            
            if(($g_table!="oggetti" && $key!="id" && !strpos($key, "_hidden")) || ($g_table=="oggetti" && $key!="nome" && $key!="path" && $key!="isprivate" && $key!="id" && !strpos($key, "_hidden") )) {
              if(strpos($key, "_editor")) {
                ?>
                <div class="elemento">
          				<div class="label"><? echo $tmpkey; ?><label for="<? echo $tmpkey; ?>"> </label></div>
                  <div class="value"><? operator($key); ?><input type="text" name="<? echo $key; ?>" id="<? echo $key; ?>" maxlength="1024" class="text" value=""/></div>
          			</div>
          		  <? 
              } else if(mysql_field_type($result,$j)=="date") {
                ?>
                <div class="elemento">
          				<div class="label"><? echo $tmpkey; ?><label for="<? echo $tmpkey; ?>"> </label></div>
          				<div class="value">
                    <? operator($key); ?><? formdata($key,'','','','','','1900'); ?>
                    <? operator($key,"op2_"); ?><? formdata($key."_ZZZ",'','','','','','1900'); ?>
                  </div>
          			</div>
          		  <? 
              } else if(mysql_field_type($result,$j)=="int" && mysql_field_len($result,$j)==1) {
                ?>
                  <div class="elemento">
                    <div class="label"><? echo str_replace("_"," ",$key); ?><label for="<? echo str_replace("_"," ",$key); ?>"> </label></div>
                    <div class="value">
              					<select name="<?=$key?>">
              						<option value=""></option>							
              							<option value="1">selezionato</option>
              							<option value="0">non selezionato</option>
            		        </select>
            		    </div>
          		    </div>
          		<?php
          	  } else if(strpos($key, "_file")) {
                $tmpKey=str_replace("_file","",$key)
                ?>
                <div class="elemento">
                  <div class="label"><? echo str_replace("_"," ",$tmpKey); ?><label for="<? echo str_replace("_"," ",$tmpKey); ?>"> </label></div>
                  <div class="value">
            					<select name="<?=$key?>">
            						<option value=""></option>							
            							<option value="1">presente</option>
            							<option value="0">non prsente</option>
          		        </select>
          		    </div>
        		    </div>
          		  <?
          		} else if(!(strpos($key, "id_")===FALSE)) {
                  $tmp_table = str_replace("id_", "", $key);
                  $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix.$tmp_table);
                  $fnum = mysql_num_fields($tmprs);
                  ?>
                  <div class="elemento">
                    <div class="label"><? echo str_replace("_"," ",$tmpkey); ?><label for="<? echo str_replace("_"," ",$tmpkey); ?>"> </label></div>
                    <div class="value">
                      <select name="<?=$key.$strprfx?>">
            						<option value=""></option>							
            						<?php
            						$utility = new Utility;
          			        $rsTmp = $utility->buildRecordset($tmprs);
                        if($struttura!="") {
                          $arrstr=getStruttura("$struttura");
                        }
                        
                        $tfield="";
                        for($ii=0; $ii<$fnum; $ii++) {
                          $fld=mysql_field_name($tmprs,$ii);
                          if((strpos($fld, "_lst"))) $tfield=$fld;
                        }
                        
                        if($tfield=="") {
                          $fld=mysql_field_name($tmprs,1);
                          $tfield=$fld;
                        }

                        $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix.$tmp_table." ORDER BY $tfield ASC");
                        $rsTmp = $utility->buildRecordset($tmprs);

                        while (list($keyTmp, $rowTmp) = each($rsTmp)) {
                          $rcell=$rowTmp[$tfield];

                          if($struttura=="") $tmpAdd=true;
                          
                          if($struttura!="") {
                            $tmpAdd=false;
                            if(in_array($rowTmp["id"], $arrstr)) $tmpAdd=true;
                          }
                          if($tmpAdd) {
                            ?>
              							<option value="<?php echo $rowTmp["id"]; ?>"<?php echo ($cell==$rowTmp["id"]) ? " selected" : "" ?>><?php echo $rcell; ?></option>
              							<?php
            							}
            						}
          			      ?>
          		        </select>
          		    </div>
          		  </div>
          		<?php
              } else {
                ?>
                <div class="elemento">
          				<div class="label"><? echo $tmpkey; ?><label for="<? echo $tmpkey; ?>"> </label></div>
          				<div class="value"><? operator($key); ?><input type="text" name="<? echo $key; ?>" id="<? echo $key; ?>" maxlength="1024" class="text" value=""/></div>
          			</div>
          		  <?
              }
            }
          }
          $j++;
        }
        ?>
      <br>
      <div class="elemento">
        <div class="value"><input type="image" name="act_CERCA-DO" src="<?php echo $objUtility->getPathBackofficeResources() ?>vai.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>vai.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>vai.png";' alt="" title="" /></div>
       
        <input type="hidden" name="act_CERCA-DO" />
      </div>
    </div>
</div>


<script language="JavaScript">
  function ricerca(str) {
    var height=document.getElementById(str).style.height;
    document.getElementById(str).style.overflow='hidden';
    
    if(height=="") {
      document.getElementById(str).style.height='0px';
    } else {
      document.getElementById(str).style.height='';
    }
  }
</script>
        <? } ?>
        <div id="tmpp" class="column" style="width:100%;overflow:auto;margin-bottom:20px;">
          <?
          $objHtml->paginazione($perpag, $totres, $npags, $currpag, "PAGE-GOTO", 10,"1",$ppsel,$ppsel2);
          if($currpagsel=="1" && $npags<2) {?><div class="paginazione"><div class="testo"><input type="submit" name="act_PAGE-GOTO_ns" value="elimina filtro selezionati" title="elimina filtro selezionati" class="page"/></div></div><?}

          
          $fields_num = mysql_num_fields($result);
$rows_num = mysql_num_rows($result);

if($rows_num>0) {
?>
<table cellspacing="2" border="0" summary="Moduli" class="default" style="width:96%;"> 
  <thead>
    <tr>           
      <?php if($modifica!="0") { ?><th scope="col" abbr="" style="background-color:#EEEEEE;width:30px;padding-left:0px;padding-right:0px;padding-top:5px;padding-bottom:5px;">&#160;</th><? } ?>
      <?php if($elimina!="0") { ?><th scope="col" abbr="" style="background-color:#EEEEEE;width:30px;padding-left:0px;padding-right:0px;padding-top:5px;padding-bottom:5px;">&#160;</th><? } ?>
      <th name="<? echo $g_config_table_prefix.$g_table; ?>colsell[]" scope="col" abbr="" style="width:30px;padding-left:0px;padding-right:0px;text-align:center;"><input type="checkbox" id="<? echo $g_config_table_prefix.$g_table; ?>checkboxall" name="<? echo $g_config_table_prefix.$g_table; ?>checkboxall" title="seleziona tutti" /></th>
      <?php
      //preorderTH($g_table);

      $ordinamento=FALSE;
      for($i=0; $i<$fields_num; $i++) {
        $field=mysql_field_name($result,$i);
        if($field=="Ordinamento") $ordinamento=TRUE;
      }

      if($ordinamento==TRUE) {
        //echo $sql2;exit;
        $sql.=" ORDER BY Ordinamento ASC";
        if($currpag1!="y") {
          $sql=$sql." LIMIT $pstart, $pend";
        }
        $result = mysql_query($sql);
        $tres = mysql_query($sql);
      }

      $ncols=0;

      for($i=0; $i<$fields_num; $i++) {
        $ver="";
        $tmp_table="";
        $field=mysql_field_name($result,$i);
        if(strpos($field, "id_")===FALSE) $ver=1;

        $tmpField=$field;

        if($g_table=="oggetti" && $field=="originalname") $tmpField="Nome";
        if($g_table=="oggetti" && $field=="ext") $tmpField="Estensione";

        if(strpos($tmpField, "_thm")) {
          $substr=substr($tmpField, strpos($tmpField, "_thm"), strlen($tmpField)-strpos($tmpField, "_thm"));
          $tmpField=str_replace($substr,"",$tmpField);
        }

        if(strpos($tmpField, "_str_")) {
          $struttura=substr($tmpField, strpos($tmpField, "_str_"), strlen($tmpField)-strpos($tmpField, "_str_"));
          $tmpField=str_replace($struttura,"",$tmpField);
          $struttura=str_replace("_str_","",$struttura);
        }
        
        $tmpField=retLabelInsert($g_config_table_prefix.$g_table, $field);
        
        $ord_image="none.gif";
        $ord_tit="ordine per inserimento";
        if($_SESSION[('tmp_order'.$g_config_table_prefix.$g_table)]==$field) {
          $t=$_SESSION[('tmp_ordert'.$g_config_table_prefix.$g_table)];
      
          if($t=="") {$ord_image="none.gif"; $ord_tit="ordine per inserimento"; }
          if($t=="ASC") {$ord_image="desc.gif"; $ord_tit="ordine crescente";}
          if($t=="DESC") {$ord_image="asc.gif"; $ord_tit="ordine decrescente";}
        }
        if($ver!=1) {
          $tmp_table = str_replace("id_", "", $field);
          $ncols++;
          ?>
          <th scope="col" abbr="Moduli" <?php if($field=="Ordinamento") { ?>style="width:40px;"<? } ?> <?php if($ordinamento==FALSE) { ?> class="point" title="ordina" <? } ?> ><?php if($ordinamento==FALSE) { ?><table id="ordinamento"><tr><td><input type="image" name="act_ORDER-DO_<?php echo str_replace("_","***",$field); ?>" src="<?php echo $objUtility->getPathBackofficeResources().$ord_image ?>" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources().$ord_image ?>";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources().$ord_image ?>";' alt="<?=$ord_tit?>" title="<?=$ord_tit?>" class="btnupd" /></td><td><? } ?><? echo $tmpField; ?><?php if($ordinamento==FALSE) { ?></td></tr></table><? } ?></th>
          <?
        } else {
          if( ($g_table!="oggetti" && $field!="id" && !strpos($field, "_hidden")) || ($g_table=="oggetti" && $field!="nome" && $field!="path" && $field!="isprivate" && $field!="id" && !strpos($field, "_hidden") )) {
          ?>
            <?php $ncols++; ?>
            <th scope="col" abbr="Moduli" <?php if($field=="Ordinamento") { ?>style="width:40px;"<? } ?>  <?php if($ordinamento==FALSE) { ?> class="point" title="ordina" <? } ?>  ><?php if($ordinamento==FALSE) { ?><table id="ordinamento"><tr><td><input type="image" name="act_ORDER-DO_<?php echo str_replace("_","***",$field); ?>" src="<?php echo $objUtility->getPathBackofficeResources().$ord_image ?>" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources().$ord_image ?>";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources().$ord_image ?>";' alt="<?=$ord_tit?>" title="<?=$ord_tit?>" class="btnupd"/></td><td><? } ?><? echo $tmpField; ?><?php if($ordinamento==FALSE) { ?></td></tr></table><? } ?></th>
          <?
          }
        }
      }

      if($g_table=="oggetti") {
        ?>
      <th scope="col" abbr="Moduli" class="point"  title="ordina">Download</th>
      <?
      }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php
    if (count($result)) {
      $i=0;
      //$tres=$objUtility->buildRecordset($tres);

      while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
        $i++;
        ?>
    <tr>
      <?php if($modifica!="0") { ?><td <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>>&#160;<?php if($modifica!="0") { ?><input type="image" name="act_UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png";' alt="visualizza/modifica" title="visualizza/modifica" class="btnupd" <?php if($modifica=="0") echo "disabled='disabled'"; ?> /><? } ?></td><? } ?>
      <?php if($elimina!="0") { ?><td <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>>&#160;<?php if($elimina!="0") { ?><input type="image" name="act_DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_del_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png";'  alt="elimina" title="elimina" class="btndel" onClick="return confirmDelete()" <?php if($elimina=="0") echo "disabled='disabled'"; ?>/><? } ?></td><? } ?>
      <td name="<? echo $g_config_table_prefix.$g_table; ?>colsell[]" style="padding-left:0px;padding-rigth:0px;text-align:center;padding-left:5px;" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="checkbox" name="<? echo $g_config_table_prefix.$g_table; ?>checkSel[]" value="<?php echo $row["id"] ?>" title="seleziona" <?php if(is_array($_SESSION[$g_config_table_prefix.$g_table."checkSel"])) { if(in_array($row["id"], $_SESSION[$g_config_table_prefix.$g_table."checkSel"])) echo "checked="; } ?> ></td>

          <?
          $j=0; 
          while (list($key, $cell) = each($row)) {
            
            $m_style="";
            if(function_exists(str_replace($g_config_table_prefix,"", $g_table)."_before_print_cell")) eval("\$m_style=".str_replace($g_config_table_prefix,"", $g_table)."_before_print_cell(\"$key\",\"".$row["id"]."\");");
            
            if(strpos($key, "_str_")) {
              $struttura=substr($key, strpos($key, "_str_"), strlen($key)-strpos($key, "_str_"));
              $key=str_replace($struttura,"",$key);
              $struttura=str_replace("_str_","",$struttura);
            }

            if( ($g_table!="oggetti" && $key!="id" && !strpos($key, "_hidden")) || ($g_table=="oggetti" && $key!="nome" && $key!="path" && $key!="isprivate" && $key!="id" && !strpos($key, "_hidden") )) {
              if(mysql_field_type($result,$j)=="date") {
                ?>
      <td style="<?=$m_style?>" align='center' <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?> style="text-align:center;">
                  <?php
                  $cell=explode("-", $cell);
                  if($cell[0]!="0000") {
                    $cell=array_reverse($cell);
                    $cell=implode("-", $cell);
                  } else {
                    $cell="";
                  }
                  echo "<span style='display:block;width:60px;'>".$cell."</span>";
                  ?>&#160;
      </td>
              <?
              } else if(mysql_field_name($result,$j)=="Ordinamento") {
                  ?>
      <td style="<?=$m_style?>" align='center' <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?> style="text-align:center;">
                    <?php
                    if ($i>1 && ($rows_num!=1)) {
                      ?>
        <input type="image" name="act_CATEGORIE-MOVEUP-DO_<?php echo $row["id"]."#".mysql_result($tres,$i-2,'id'); ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png";' alt="sposta in alto" title="sposta in alto" class="btnupd"/>
                    <?php
                    }
                    else {
                      ?>
        <img src='<?php echo $objUtility->getPathBackofficeResources() ?>spacer.gif' width='25' border='0'>
                    <?php
                    }
                    ?>

                    <?php
                    if ($i < $rows_num) {
                      ?>
        <input type="image" name="act_CATEGORIE-MOVEDOWN-DO_<?php echo $row["id"]."#".mysql_result($tres,$i,'id'); ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png";' alt="sposta in basso" title="sposta in basso" class="btnupd"/>
                    <?php
                    }
                    else {
                      ?>
        <img src='<?php echo $objUtility->getPathBackofficeResources() ?>spacer.gif' width='25' border='0'>
                    <?php
                    }
                    ?>
      </td>
                <?
                } else if(strpos($key, "_cry")) {
                    ?>
      <td align="right" style="<?=$m_style?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;text-align:right;\"" : "" ?> style="text-align:right;">
          <? if($cell!="0.00") {?>&euro; <?php echo currencyITA($cell); ?><? } ?>&#160;
      </td>

                <?
                } else if(mysql_field_type($result,$j)=="int" && mysql_field_len($result,$j)==1) {
                    ?>
      <td style="<?=$m_style?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;text-align:center;\"" : "" ?> style="text-align:center;">
                      <?
                      if($cell==0) $cell="";
                      if($cell==1) $cell="checked";
                      ?>
        <center><input type="checkbox" name="" id="" disabled="disabled" class="checkbox" <?= $cell ?> value="1"/ >&#160;</center>
      </td>
                  <?
                  } else if(strpos($key, "_file")) {
                      //$tmprs = mysql_query("SELECT * FROM ".$config_table_prefix."oggetti WHERE id='$cell'" );
                      //$tmprow = mysql_fetch_array($tmprs);
                      ?>
      <td name="RsUpload" style="<?=$m_style?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
       <?php
        
        $tmprowArr=explode(";", $cell);
        $tmprowArr=array_reverse($tmprowArr);
        if((mysql_field_type($result,$j)=="int" && $tmprowArr[0]==0) || (mysql_field_type($result,$j)!="int")) {
          if(mysql_field_type($result,$j)=="int") {$spid="0";}else{$spid="";}
          if($modifica!="0") {lyteFrame($objUtility->getPathBackofficeAdmin()."upload/index.php?parent=$parent&tblparent=".urlencode($tblparent)."&row=".$row["id"]."&id=$spid&table=".$g_config_table_prefix.$g_table."&field=".$key,$text="<img src='".$objUtility->getPathBackofficeResources()."add_image.png' onmouseover='this.src = \"".$objUtility->getPathBackofficeResources()."add_image.png\";' onmouseout='this.src = \"".$objUtility->getPathBackofficeResources()."add_image.png\";' alt='inserisci' title='inserisci file/immagine' class='btnins' style='border:0;padding-left:3px;' />",$title="",$class="",$m_style);}
        }
        for($z=0;$z<count($tmprowArr);$z++) {
          $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix."oggetti WHERE id='".$tmprowArr[$z]."'" );
          $tmprow = mysql_fetch_array($tmprs);
          $tmprow['id']=$tmprowArr[$z];
          if($cell!="0" && $cell!="") { 
          ?>
          <hr style="border:1px #BBBBBB solid">
          <table cellspacing="0" cellpadding="0" border="0" style="padding:0;margin:0;"><tr><td style="background-color:transparent;border:0;vertical-align: top;padding:0;margin:0;padding-top:3px;padding-right:3px;">
                <input type="hidden" name="allegatodel<?=$tmprow["id"]?>" value="<?=$key?>" />
                <input type="hidden" name="rowid<?=$tmprow["id"]?>" value="<?=$row["id"]?>" />
                <div class="value" >&#160;<?php if($elimina!="0" || $notElimina=="1") { ?><input type="image" name="act_ALLEGATO-DEL-DO_<?php echo $tmprow["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>del_image.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>del_image.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>del_image.png";' alt="elimina" title="elimina" class="btndel" onClick="return confirmDelete()" <?php if($elimina=="0" && $notElimina!="1") echo "disabled='disabled'"; ?> /><? } ?></div>
                <?php lyteFrame($objUtility->getPathBackofficeAdmin()."upload/index.php?row=".$row["id"]."&id=".$tmprow["id"]."&table=".$g_config_table_prefix.$g_table."&field=".$key,$text="<img src='".$objUtility->getPathBackofficeResources()."edit_image.png' onmouseover='this.src = \"".$objUtility->getPathBackofficeResources()."edit_image.png\";' onmouseout='this.src = \"".$objUtility->getPathBackofficeResources()."edit_image.png\";' alt='sostituisci' title='sostituisci' class='btnupd' style='border:0;padding-left:3px;' />",$title="",$class="",$m_style); ?>
              </td><td style="background-color:transparent;border:0;vertical-align: top;padding:0;margin:0;padding-top:3px;padding-right:3px;">
              <? }
              if($tmprow['ext']=="jpg" || $tmprow['ext']=="gif" || $tmprow['ext']=="png" || $tmprow['ext']=="bmp") {
              echo "<a href=\"".$objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']."\" rel=\"lytebox[gallery]\" title=\"".$tmprow['originalname']."\"><img height='83' alt=\"".$tmprow['originalname']."\" src=\"".$objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']."\" style=\"border-top:1px solid #000000; border-left:1px solid #000000; border-bottom:1px solid #ffffff; border-right:1px solid #ffffff; \"></a>";
              ?>
              </td></tr><tr><td colspan="2"  style="background-color:transparent;border:0;vertical-align: top;padding:0;margin:0;padding-top:3px;padding-left:1px;padding-bottom:5px;">
                <div class="elemento" style="padding-left:6px;">
                  <div class="value">
                    <input style="margin-left:14px;" type="text" name="rsurl" id="urlcopy" maxlength="1024" class="text" value="<?php echo $objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']; ?>" READONLY  width="126" onclick="copia(this)" />
                  </div>
                </div>
                <? if($cell!="0" && $cell!="") { ?> </td></tr></table><? } ?>
                          <?
                          } else { ?>
          <div><a href="<?php echo ($objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']); ?>"><?=$tmprow['originalname']?></a></div>
                          <? if($cell!="0" && $cell!="") { ?> </td></tr></table><? }  ?>
                  <? }   } ?>
</td>
              <?
              } else if(!(strpos($key, "id_")===FALSE)) {
                  $tmp_table = str_replace("id_", "", $key);
                  $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix.$tmp_table." WHERE id='$cell'" );
                  if($tmprs) {
                    $tmprow=mysql_fetch_array($tmprs);
                    $fnum = mysql_num_fields($tmprs);
  
                    $rcell="";
                    for($ii=0; $ii<$fnum; $ii++) {
                      $fld=mysql_field_name($tmprs,$ii);
                      if((strpos($fld, "_lst"))) $rcell.=$tmprow[$ii];
                    }
                    if($rcell=="") {
                      $fld=mysql_field_name($tmprs,1);
                      $rcell=$tmprow[$fld];
                    }
                  }
                  ?>
<td style="<?=$m_style?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
  &#160; <?php if($tmprs) {lyteFrame("rs_exec_functions.php?fun=printRecord&param1=$tmp_table&param2=".$cell,$rcell,$rcell,$class="",$m_style);}else{echo $cell;} ?>
</td>
                <?
                } else {
                  ?>
<? 
global $c;
$c=$c+1; 
?>
<script>
var testo<?=$c?>="<?php echo jstr($cell); ?>";
var testostrip<?=$c?>="<?php echo jstr(strip_tags($cell)); ?>";
</script>
<td style="<?=$m_style?>" id="<?=$g_table.$c?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
                    <?php 
                    if(strlen($cell)>200) {
                      $cell=strip_tags($cell);
                      $cell=substr($cell,0,200)." ...<span id='cambiatd' onclick=\"cambiaTD(getElementById('".$g_table.$c."'),testo".$c.",".$c.",0);\">(continua)</span>";
                    }
                    $cell=trim($cell);
                    echo $cell; ?>
                    &#160;
                    <?php if(strlen($cell)==7 && substr($cell,0,1)=="#") { ?>
  <div style="margin:2px;width:50px;height:50px;background-color:<?=$cell?>"></div>
                    <? } ?>
</td>
                <?
                }
      }
      $j++;
    }
    if($g_table=="oggetti") {
      ?>
<td style="<?=$m_style?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
        <?php
        if($row['ext']=="jpg" || $row['ext']=="gif" || $row['ext']=="png" || $row['ext']=="bmp") {
          echo "<a href=\"".$objUtility->getPathResourcesDynamic().$row['nome'].".".$row['ext']."\" rel=\"lytebox[gallery]\" title=\"".$row['originalname']."\"><img width='120' alt=\"".$row['originalname']."\" src=\"".$objUtility->getPathResourcesDynamic().$row['nome'].".".$row['ext']."\" style=\"border:4px solid #eeeeee; \"></a>";
          ?>
  <div class="elemento">
    <div class="label">url:<label for="rsurl"></label></div>
    <div class="value"><input type="text" name="rsurl" id="url" maxlength="1024" class="text" value="<?php echo $objUtility->getPathResourcesDynamic().$row['nome'].".".$row['ext']; ?>" READONLY /></div>
  </div>
        <?
        } else { ?>
  <a href="<?php echo ($objUtility->getPathResourcesDynamic().$row['nome'].".".$row['ext']); ?>"><?=$row['originalname']?></a>
        <? } ?>
</td>
    <?
    }
    ?>
</tr>
  <?
  }
} else {
  ?>
<tr>
  <td>&#160;</td>
  <td>&#160;</td>
    <?
    while($row = mysql_fetch_row($result)) {
      foreach($row as $cell) { ?>
  <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
    (nessuno)
  </td>
      <?
      }
    }
    ?>
</tr>
<?php } ?>

<?php if(function_exists(str_replace($g_config_table_prefix,"", $g_table)."_after_print_tbody")) eval(str_replace($g_config_table_prefix,"", $g_table)."_after_print_tbody(\"$sql\");"); ?>
<tr>
  <?php
  $initNcols=3;
  if($elimina=="0") $initNcols=$initNcols-2;
  if($modifica=="0") $initNcols=$initNcols-1; 
  ?>
  <td colspan="<?=$initNcols?>" style="background-color:#EEEEEE">&#160;</td>
  <td colspan="<?=$ncols?>" id="sel_toolbar_btn" style="background-color:#EEEEEE"> 
    <span style="float:left;margin-right:18px;margin-left:2px;"><?php if($aggiungi!="0") { ?><input type="image" name="act_INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png";' alt="aggiungi" title="aggiungi" class="btnins" <?php if($aggiungi=="0") echo "disabled='disabled'"; ?> /><? } ?></span>
    <div id="<? echo $g_config_table_prefix.$g_table; ?>sel_toolbar">
      <span style="margin-top:0px;float:left;padding-right:5px;font-size:12px;">Se selezionati: </span>
      <?php if($elimina!="0") { ?><input type="image" name="act_BTN-CLICK-FUN_rowsDel" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="elimina" title="elimina" class="icoins" onClick="return confirmDelete()" /> <? } ?>
      <?php if(function_exists(str_replace($g_config_table_prefix,"", $g_table)."_selected_bar")) eval(str_replace($g_config_table_prefix,"", $g_table)."_selected_bar();"); ?>
    </div>
  </td>
</tr>
</tbody>
</table>

<script>
$(document).ready(function(){
   j=0; 
   $("#<? echo $g_config_table_prefix.$g_table; ?>sel_toolbar").children().each(function()
   {
      if(this.tagName=="INPUT") j++;
   }); 
   if(j==0) {
    $("#<? echo $g_config_table_prefix.$g_table; ?>sel_toolbar").css("display","none");
      
    
    $("th[name=<? echo $g_config_table_prefix.$g_table; ?>colsell[]]").each(function() {
      $(this).css("display","none");
          
    });
    
    $("td[name=<? echo $g_config_table_prefix.$g_table; ?>colsell[]]").each(function() {
      $(this).css("display","none");    
    });
   }
});
</script>

<? }else{ ?>
  <?php if($aggiungi!="0") { ?><span style="float:left;margin-right:18px;margin-left:105px;padding-bottom:8px;"><?php if($aggiungi!="0") { ?><input type="image" name="act_INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png";' alt="aggiungi" title="aggiungi" class="btnins" <?php if($aggiungi=="0") echo "disabled='disabled'"; ?> /><? } ?></span><? } ?> 
<? } ?>

<hr style="clear:both;border:1px #DDDDDD dashed;">
          
          <?php $objHtml->paginazione($perpag, $totres, $npags, $currpag, "PAGE-GOTO", 10,"1",$ppsel,$ppsel2); ?>
  			</div>
  		</form>
		<? }
      
      if(function_exists(str_replace($g_config_table_prefix,"", $g_table)."_after_print")) eval(str_replace($g_config_table_prefix,"", $g_table)."_after_print();");
      
    }
    
    function setCurrent($id) {
      $objUtility = new Utility;
      
      $g_config_table_prefix=$this->g_config_table_prefix;
      $g_table=$this->g_table;
      
      $objUtility->sessionVarUpdate("id".$g_config_table_prefix.$g_table,$id);
    }
    
    function printSearch($val) {
      $this->g_printSearch=$val;
    }
  }
?>