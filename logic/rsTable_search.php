  <?php
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
