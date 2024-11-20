<?php
session_start();



include ("_docroot.php");
include (SERVER_DOCROOT . "logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;

$conn = $objDb->connection($objConfig);
$dbname = $objConfig->get("db-dbname");
global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);
$isSystem = $objUsers->isSystem($conn, $intIdutente);

$strParam = strtolower($objUtility->sessionVarRead("action"));
$intId = $objUtility->sessionVarRead("idmod");
$table = $objUtility->sessionVarRead("table");
$struttura = $objUtility->sessionVarRead("struttura");
$parent = $objUtility->sessionVarRead($table."parent");
$tblparent = $objUtility->sessionVarRead($table."tblparent");
$cpage = $objUtility->sessionVarRead($table."currentPage");
$subPrint = $objUtility->sessionVarRead($table."subPrint");
$colfilter = $objUtility->sessionVarRead($table."colfilter");
$filter = $objUtility->sessionVarRead($table."filter");

$mydir=str_replace(".php", "", $cpage);


//$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."$mydir/$cpage");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <?php $objHtml->adminHeadsection() ?>
        <?php $objHtml->adminHtmlEditor() ?>
        
        <script language="JavaScript" type="text/javascript">
          function confirmAnnulla() {
          	if (!(confirm("Le modifiche non salvate andranno perse.\nSei sicuro di voler procedere?"))) {
          		return false;
          	}
          }
        </script>
    </head>
    <body>
        <div id="site">
            <?php $objHtml->adminHead() ?>
            <div id="content">
                <?php $objHtml->adminLeft($conn, $intIdutente) ?>
                <div id="area">
                    <?php $objHtml->adminPageTitle(retTableLabelInsert($table), "") ?>
                    <div id="body">
                        <div class="inputdata">
                            <form action="rsAction.php" id="frm" name="frm" method="post" enctype="multipart/form-data" />
                              <input type="hidden" name="idmod" value="<?php echo $objUtility->sessionVarRead("idmod") ?>"/>
                              <input type="hidden" name="currentPage" value="<?php echo $cpage; ?>"/>
                              <input type="hidden" name="parent" value="<?php echo $parent; ?>"/>
                              <input type="hidden" name="tblparent" value="<?php echo $tblparent; ?>"/>
                              <input type="hidden" name="colfilter" value="<?php echo $colfilter; ?>"/>
                              <input type="hidden" name="filter" value="<?php echo urlencode($filter); ?>"/>
  
                              <?php if(function_exists(str_replace($config_table_prefix,"", $table)."_after_print_hidden_input")) eval(str_replace($config_table_prefix,"", $table)."_after_print_hidden_input(1);"); ?>
  
                              <?php
                              if($table!=$config_table_prefix."oggetti") {
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
                              }
                              if($table==$config_table_prefix."oggetti") {
                                ?>
                                <div class="elemento">
                                	<div class="label">file<label for="file"> </label>*</div>
                                	<div class="value"><input type="file" name="allegato" id="allegato" maxlength="500" class="text" value=""/></div>
                                </div>
                                <?
                              }
                              ?>
  
                              <div style="clear:both;"></div>
                              <div class="elemento" >
                                  <div class="label">&nbsp;</div>
                                  <div class="value"><input type="submit" name="act_INSUPD-DO" value="Salva" class="btn" style="margin-right:10px;" /><input type="submit" name="act_ANNULLA-DO" value="Annulla" class="btn" onclick="return confirmAnnulla();" /></div>
                                  <div class="value"></div>
                              </div>
                            </form>
                            <?php
                            $arrSubPrint=explode(",",$subPrint); 
                            if($strParam=="upd" && $subPrint!="1") {
                                $sql="SHOW TABLE STATUS FROM $dbname";
                                $query = mysql_query($sql);

                                $g_config_table_prefix=strtolower($config_table_prefix);
                                $g_table=substr($table, strlen($g_config_table_prefix));
                                $tmp_g_table=$g_table;

                                while($res=mysql_fetch_array($query)) {
                                    $sqlWhere="";
                                    if(!(strpos($res[0],$g_config_table_prefix.$tmp_g_table."#")===FALSE) && strpos($res[0],"_nm")!=FALSE) {

                                        $p_table=$res[0];

                                        $res[0]=str_replace($g_config_table_prefix, "", $res[0]);
                                        $res[0]=str_replace("_nm", "", $res[0]);
                                        $tmp_arr=explode("#", $res[0]);
                                        $res[0]=$tmp_arr[1];

                                        if($res[0]!="") {
                                          $g_table=$res[0]; 
                                          if($subPrint=="" || in_array($g_table, $arrSubPrint)) {
                                            $result = mysql_query("SELECT * FROM `$p_table` ");
                                            $field1=mysql_field_name($result,1);
                                            $field2=mysql_field_name($result,2);

                                            $query1 = mysql_query("SELECT * FROM `$p_table` WHERE $field1='$intId' ");

                                            while($res1=mysql_fetch_array($query1)) {
                                                $sqlWhere.="id='".$res1[$field2]."' OR ";
                                            }

                                            $sqlWhere=substr($sqlWhere,0,strlen($sqlWhere)-3);

                                            if($sqlWhere!="") $sqlWhere="($sqlWhere)";
                                            if($sqlWhere=="") $sqlWhere="id='0'";

                                            $parent=$intId;
                                            $tblparent=$p_table;

                                            ?><br><br>
                                            <div class="elemento" >
                                                <?php $tblDefault = new rsTable($g_table);  ?>
                                                <div class="value">
                                                  <?php  
                                                  $tblDefault->_print($sqlWhere,$tblparent,$parent,"","","");
                                                  ?>
                                                </div>
                                            </div><?
                                          }
                                        }
                                    }
                                }
                                ?>
                            <div class="elemento" >
                                <div class="value">-</div>
                            </div>
                            <?
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php $objHtml->adminFooter() ?>
        </div>
    </body>
</html>