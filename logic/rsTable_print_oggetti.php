<?php
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
        $sql.=" ORDER BY Ordinamento ASC";
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

        if(strpos($tmpField, "_str")) {
          $struttura=substr($tmpField, strpos($tmpField, "_str"), strlen($tmpField)-strpos($tmpField, "_str"));
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
            
            if(strpos($key, "_str")) {
              $struttura=substr($key, strpos($key, "_str"), strlen($key)-strpos($key, "_str"));
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
      <td style="<?=$m_style?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
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
                    <input style="margin-left:14px;" type="text" name="url" id="urlcopy" maxlength="1024" class="text" value="<?php echo $objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']; ?>" READONLY  width="126" onclick="copia(this)" />
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
                  ?>
<td style="<?=$m_style?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
  &#160; <?php lyteFrame("rs_exec_functions.php?fun=printRecord&param1=$tmp_table&param2=".$cell,$rcell,$rcell,$class="",$m_style); ?>
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
    <div class="label">url:<label for="url"></label></div>
    <div class="value"><input type="text" name="url" id="url" maxlength="1024" class="text" value="<?php echo $objUtility->getPathResourcesDynamic().$row['nome'].".".$row['ext']; ?>" READONLY /></div>
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
  if($elimina=="0") $initNcols=$initNcols-1;
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