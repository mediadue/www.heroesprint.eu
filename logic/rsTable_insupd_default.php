<?php
if($strParam=="upd") {
  $result = mysql_query("SELECT $colfilter FROM `".$table."` WHERE id='$intId'");
  $field = mysql_fetch_array($result,MYSQL_ASSOC);

  $j=0;
  while (list($key, $cell) = each($field)) {
    if($key!="id" && !strpos($key, "_hidden") && $key!="Ordinamento") {
      $vprint=true;
      if(function_exists(str_replace($config_table_prefix,"", $table)."_".$key."_upd_before_print")) eval("\$vprint=".str_replace($config_table_prefix,"", $table)."_".$key."_upd_before_print();");
      $objUtility->sessionVarUpdate(str_replace($config_table_prefix,"", $table)."_".$key."_upd_before_print", $vprint);
      
      if($vprint) {
        $tmpkey=$key;
  
        if(strpos($tmpkey, "_thm")) {
          $substr=substr($tmpkey, strpos($tmpkey, "_thm"), strlen($tmpkey)-strpos($tmpkey, "_thm"));
          $tmpkey=str_replace($substr,"",$tmpkey);
        }
  
        $strprfx="";
        $struttura="";
        if(strpos($key, "_str_")) {
          $struttura=substr($key, strpos($key, "_str_"), strlen($key)-strpos($key, "_str_"));
          $key=str_replace($struttura,"",$key);
          $struttura=str_replace("_str_","",$struttura);
          $strprfx="_str_".$struttura;
        }  
  
        $tmpkey=retLabelInsert($table, $key);
        if($struttura!="") $tmpkey=$struttura; 
        
        
        if(strpos($key, "_editor")) {
          ?>
  <div class="elemento">
    <div class="label"><? echo $tmpkey; ?><label for="<? echo $tmpkey; ?>"> </label></div>
    <div class="value">
      <textarea name="<?=$key?>" id="<?=$key?>" rows="15" cols="40" class="textEditor"><?=$cell?></textarea>
    </div>
  </div>
        <?
        } else if(mysql_field_type($result,$j)=="date") {
        ?>
  <div class="elemento">
    <div class="label"><? echo str_replace("_"," ",$tmpkey); ?><label for="<? echo str_replace("_"," ",$key); ?>"> </label></div>
    <div class="value">
                <? formdata($key,'','','','',$cell); ?>
    </div>
  </div>
          <?
          } else if(mysql_field_type($result,$j)=="int" && mysql_field_len($result,$j)==1) {
              ?>
  <div class="elemento">
  <div class="label"> <label for="<? echo str_replace("_"," ",$key); ?>"> </label></div>
                <?
                if($cell==0) $cell="";
                if($cell==1) $cell="checked";
                ?>
    <table cellspacing="0" summary="<?=$table?>" >
      <tr>
        <td width="20"><input type="checkbox" name="<?=$key?>" id="<?=$key?>" class="checkbox" <?= $cell ?> value="1"/ ></td>
        <td><? echo str_replace("_"," ",$key); ?></td>
      </tr>
    </table>
  </div>
            <?
            } else if(strpos($key, "_file")) {
                ?>
                <!--
  <div class="elemento">
    <div class="label"><? echo str_replace("_"," ",$tmpkey); ?><label for="<? echo str_replace("_"," ",$tmpkey); ?>"> </label></div>
    <div class="value"><input type="file" name="allegatos[]" id="allegatos" maxlength="500" class="text" value=""/></div>
  </div>
  -->
              <?
              } else if(!(strpos($key, "id_")===FALSE)) {
                  $tmp_table = str_replace("id_", "", $key);
                  $wwh="";
                  if($tmp_table=="gestione_layout" && !$isSystem) $wwh="WHERE id_users='$intIdutente'";
                  $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix.$tmp_table." $wwh");
                  $fnum = mysql_num_fields($tmprs);
  
                  ?>
  <div class="value" style="padding-top:10px;">
    <div class="label"><label for="<? echo str_replace("_"," ",$tmpkey); ?>"><? echo str_replace("_"," ",$tmpkey); ?></label></div>
    <?php if($struttura=="") { ?>
    <select name="<?=$key.$strprfx?>" size="1">
      <option value=""></option>
                      <?php
                      $utility = new Utility;
                      $rsTmp = $utility->buildRecordset($tmprs);
                      
                      $rcell="";
                      for($ii=0; $ii<$fnum; $ii++) {
                        $fld=mysql_field_name($tmprs,$ii);
                        if((strpos($fld, "_lst"))) {$rcell=1;break;}
                      }

                      if($rcell=="") {
                        $fld=mysql_field_name($tmprs,1);
                      }
                      
                      $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix.$tmp_table." ORDER BY $fld");
                      $rsTmp = $utility->buildRecordset($tmprs);
                      
                      while (list($keyTmp, $rowTmp) = each($rsTmp)) {
                        $rcell="";
                        for($ii=0; $ii<$fnum; $ii++) {
                          $fld=mysql_field_name($tmprs,$ii);
                          if((strpos($fld, "_lst"))) $rcell.=$rowTmp[$fld];
                        }
                        if($rcell=="") {
                          $fld=mysql_field_name($tmprs,1);
                          $rcell=$rowTmp[$fld];
                        }
  
                        if(strpos($key, "_str_")) {
                          $struttura=substr($key, strpos($key, "_str_"), strlen($key)-strpos($key, "_str_"));
                          $key=str_replace($struttura,"",$key);
                          $struttura=str_replace("_str_","",$struttura);
                        }
  
                        if($struttura=="") $tmpAdd=true;
  
                        if($struttura!="") {
                          $tmpAdd=false;
                          $arrstr=getStruttura("$struttura");
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
    <? } else {  
      if($_GET['menid']=="") $_GET['menid']=$cell;
      ?><div style="padding-left:140px;clear:both;"><?
      stampaStruttura($struttura,$_GET['menid'],"-1","","2",-1); 
      ?>
      </div>
        <div class="value"><input type="hidden" name="<?=$key.$strprfx?>" id="<? echo $key; ?>" value="<?=$_GET['menid']?>"/></div>      
      <? } ?>
  </div>
                <?php
                } else {
                  ?>
  <div class="elemento">
    <div class="label"><? echo $tmpkey; ?><label for="<? echo $tmpkey; ?>"> </label></div>
    <div class="value"><input type="text" name="<? echo $key; ?>" id="<? echo $key; ?>"  class="text" value="<?php echo $cell; ?>"/></div>
  </div>
                <?
                }
      }
    }
    $j++;
  }
} else if($strParam=="ins") {
    $result = mysql_query("SELECT $colfilter FROM `".$table."`");

    $j=0;
    while ($j < mysql_num_fields($result)) {
      $field = mysql_fetch_field($result);
      $key=$field->name;
      $vprint=true;
      if(function_exists(str_replace($config_table_prefix,"", $table)."_".$key."_ins_before_print")) eval("\$vprint=".str_replace($config_table_prefix,"", $table)."_".$key."_ins_before_print();");
      $objUtility->sessionVarUpdate(str_replace($config_table_prefix,"", $table)."_".$key."_ins_before_print", $vprint);
      
      if($vprint) {
        $tmpkey=$key;
        
        if(strpos($tmpkey, "_thm")) {
          $substr=substr($tmpkey, strpos($tmpkey, "_thm"), strlen($tmpkey)-strpos($tmpkey, "_thm"));
          $tmpkey=str_replace($substr,"",$tmpkey);
        }
  
        $strprfx="";
        $struttura="";
        if(strpos($key, "_str_")) {
          $struttura=substr($key, strpos($key, "_str_"), strlen($key)-strpos($key, "_str_"));
          $key=str_replace($struttura,"",$key);
          $struttura=str_replace("_str_","",$struttura);
          $strprfx="_str_".$struttura;
        }
  
        $tmpkey=retLabelInsert($table, $key);
        if($struttura!="") $tmpkey=$struttura;
  
        if($key!="id" && !strpos($key, "_hidden") && $key!="Ordinamento") {
          if(strpos($key, "_editor")) {
            ?>
                <div class="elemento">
                  <div class="label"><? echo $tmpkey; ?><label for="<? echo $tmpkey; ?>"> </label></div>
                  <div class="value">
                    <textarea name="<?=$key?>" id="<?=$key?>" rows="15" cols="40" class="textEditor"></textarea>
                  </div>
                </div>
                        <?
                        } else if(mysql_field_type($result,$j)=="date") {
                            ?>
                <div class="elemento">
                  <div class="label"><? echo $tmpkey; ?><label for="<? echo $tmpkey; ?>"> </label></div>
                  <div class="value">
                                <? formdata($key,'','','','',''); ?>
                  </div>
                </div>
                          <?
                          } else if(mysql_field_type($result,$j)=="int" && mysql_field_len($result,$j)==1) {
                              ?>
                <div class="elemento">
                                <?
                                if($cell==0) $cell="";
                                if($cell==1) $cell="checked";
                                ?>
                  <div class="label">&nbsp;</div><table cellspacing="0" summary="<?=$table?>" class="default">
                    <tr>
                      <td width="20"><input type="checkbox" name="<?=$key?>" id="<?=$key?>" class="checkbox" <?= $cell ?> value="1"/ ></td>
                      <td></div><? echo str_replace("_"," ",$key); ?></td>
                    </tr>
                  </table>
                </div>
                            <?
                            } else if(strpos($key, "_file")) {
                                ?>
                <!--
                <div class="elemento">
                  <div class="label"><? echo str_replace("_"," ",$tmpkey); ?><label for="<? echo str_replace("_"," ",$tmpkey); ?>"> </label></div>
                  <div class="value"><input type="file" name="allegatos[]" id="allegatos" maxlength="500" class="text" value=""/></div>
                </div>
                -->
                <?
                } else if(!(strpos($key, "id_")===FALSE)) {
                    $tmp_table = str_replace("id_", "", $key);
                    $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix.$tmp_table." LIMIT 0,1");
                    $fnum = mysql_num_fields($tmprs);
  
                    ?>
                    <div class="value">
                      <div class="label"><label for="<? echo str_replace("_"," ",$tmpkey); ?>"><? echo str_replace("_"," ",$tmpkey); ?></label></div>
                      
                      <?php if($struttura=="") { ?>
                      <select name="<?=$key.$strprfx?>" size="1">
                        <option value=""></option>
                        <?php
                        $utility = new Utility;
                        $rsTmp = $utility->buildRecordset($tmprs);
                        
                        $rcell="";
                        for($ii=0; $ii<$fnum; $ii++) {
                          $fld=mysql_field_name($tmprs,$ii);
                          if((strpos($fld, "_lst"))) {$rcell=1;break;}
                        }

                        if($rcell=="") {
                          $fld=mysql_field_name($tmprs,1);
                        }
                        
                        $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix.$tmp_table." ORDER BY $fld");
                        $rsTmp = $utility->buildRecordset($tmprs);
                        
                        while (list($keyTmp, $rowTmp) = each($rsTmp)) {
                          $rcell="";
                          for($ii=0; $ii<$fnum; $ii++) {
                            $fld=mysql_field_name($tmprs,$ii);
                            if((strpos($fld, "_lst"))) $rcell.=$rowTmp[$fld];
                          }
  
                          if($rcell=="") {
                            $fld=mysql_field_name($tmprs,1);
                            $rcell=$rowTmp[$fld];
                          }
  
                          if($struttura=="") $tmpAdd=true;
  
                          if($struttura!="") {
                            $tmpAdd=false;
                            $arrstr=getStruttura("$struttura");
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
                      <? } else {  
                        ?><div style="padding-left:140px;clear:both;"><?
                        stampaStruttura($struttura,$_GET['menid'],"-1","","2",-1);
                        ?></div><? 
                      ?>
                        <div class="value"><input type="hidden" name="<?=$key.$strprfx?>" id="<? echo $key; ?>"  value="<?=$_GET['menid']?>"/></div>      
                      <? } ?>
                    </div>
                  <?php
                  } else {
                  ?>
                  <div class="elemento">
                    <div class="label"><? echo $tmpkey; ?><label for="<? echo $tmpkey; ?>"> </label></div>
                    <div class="value"><input type="text" name="<? echo $key; ?>" id="<? echo $key; ?>"  class="text" value=""/></div>
                  </div>
                  <?
                  }
        }
      }
      $j++;
    }
  }
?>