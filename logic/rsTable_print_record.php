<table cellspacing="0" summary="Moduli" class="default" style="width:96%;text-align:right;"> 

    <?php
    if (count($result)) {
      $i=0;
      //$tres=$objUtility->buildRecordset($tres);

      while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
        $i++;
        ?>
    <tr>
          <?
          $j=0;
          while (list($key, $cell) = each($row)) {
            
            $cell=html_entity_decode($cell);
            
            $field = $key;
    
            $tmpField=$field;
        
            if($table=="oggetti" && $field=="originalname") $tmpField="Nome";
            if($table=="oggetti" && $field=="ext") $tmpField="Estensione";
        
            if(strpos($tmpField, "_thm")) {
              $substr=substr($tmpField, strpos($tmpField, "_thm"), strlen($tmpField)-strpos($tmpField, "_thm"));
              $tmpField=str_replace($substr,"",$tmpField);
            }
        
            if(strpos($tmpField, "_str")) {
              $struttura=substr($tmpField, strpos($tmpField, "_str"), strlen($tmpField)-strpos($tmpField, "_str"));
              $tmpField=str_replace($struttura,"",$tmpField);
              $struttura=str_replace("_str_","",$struttura);
            }
            
            $tmpField=retLabelInsert($config_table_prefix.$table, $field);
            
            if(strpos($key, "_str")) {
              $struttura=substr($key, strpos($key, "_str"), strlen($key)-strpos($key, "_str"));
              $key=str_replace($struttura,"",$key);
              $struttura=str_replace("_str_","",$struttura);
            }

            if( ($g_table!="oggetti" && $key!="id" && !strpos($key, "_hidden")) || ($g_table=="oggetti" && $key!="nome" && $key!="path" && $key!="isprivate" && $key!="id" && !strpos($key, "_hidden") )) {
              if(mysql_field_type($result,$j)=="date") {
                ?>
      </tr><tr>
       <th>
        <? echo $tmpField; ?>
      </th>
      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
                  <?php
                  $cell=explode("-", $cell);
                  if($cell[0]!="0000") {
                    $cell=array_reverse($cell);
                    $cell=implode("-", $cell);
                  } else {
                    $cell="";
                  }
                  echo $cell;
                  ?>&#160;
      </td>
              <?
              } else if(mysql_field_name($result,$j)=="Ordinamento") {
                  ?>
      </tr><tr>
      <th>
        <? echo $tmpField; ?>
      </th>
      <td align='center' <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?> style="text-align:center;">
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
      </tr><tr>
      <th>
        <? echo $tmpField; ?>
      </th>
      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;text-align:center;\"" : "" ?> style="text-align:right;">
          € <?=$cell?>&#160;
      </td>

                <?
                } else if(mysql_field_type($result,$j)=="int" && mysql_field_len($result,$j)==1) {
                    ?>
      </tr><tr>
      <th>
        <? echo $tmpField; ?>
      </th>
      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;text-align:center;\"" : "" ?> style="text-align:center;">
                      <?
                      if($cell==0) $cell="";
                      if($cell==1) $cell="checked";
                      ?>
        <input type="checkbox" name="" id="" disabled="disabled" class="checkbox" <?= $cell ?> value="1"/ >&#160;
      </td>
                  <?
                  } else if(strpos($key, "_file")) {
                      $tmprs = mysql_query("SELECT * FROM ".$config_table_prefix."oggetti WHERE id='$cell'" );
                      $tmprow = mysql_fetch_array($tmprs);
                      ?>
      </tr><tr>
      <th>
        <? echo $tmpField; ?>
      </th>
      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
                        <?php
                        if($cell!="0" && $cell!="") { ?>
        <table cellspacing="0" cellpadding="0" border="0" style="padding:0;margin:0;"><tr><td style="background-color:transparent;border:0;vertical-align: top;padding:0;margin:0;padding-top:3px;padding-right:3px;">
              <input type="hidden" name="allegatodel<?=$tmprow["id"]?>" value="<?=$key?>" />
              <input type="hidden" name="rowid" value="<?=$row["id"]?>" />
            </td><td style="background-color:transparent;border:0;vertical-align: top;padding:0;margin:0;padding-top:3px;padding-right:3px;">
                              <? } ?>
                              <?php
                              if($tmprow['ext']=="jpg" || $tmprow['ext']=="gif" || $tmprow['ext']=="png" || $tmprow['ext']=="bmp") {
                                echo "<a href=\"".$objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']."\" rel=\"lytebox[gallery]\" title=\"".$tmprow['originalname']."\"><img width='60' alt=\"".$tmprow['originalname']."\" src=\"".$objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']."\" style=\"border-top:1px solid #000000; border-left:1px solid #000000; border-bottom:1px solid #ffffff; border-right:1px solid #ffffff; \"></a>";
                                ?>
            </td></tr><tr><td colspan="2"  style="background-color:transparent;border:0;vertical-align: top;padding:0;margin:0;padding-top:3px;padding-left:1px;padding-bottom:5px;">
              <div class="elemento" style="padding-left:6px;">
                <div class="value">
                  <input type="text" name="url" id="urlcopy" maxlength="1024" class="text" value="<?php echo $objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']; ?>" READONLY  width="126" onclick="copia(this)" />
                </div>
              </div>
                              <? if($cell!="0" && $cell!="") { ?> </td></tr></table><? } ?>
                        <?
                        } else { ?>
        <div><a href="<?php echo ($objUtility->getPathResourcesDynamic().$tmprow['nome'].".".$tmprow['ext']); ?>"><?=$tmprow['originalname']?></a></div>
                        <? if($cell!="0" && $cell!="") { ?> </td></tr></table><? } ?>
                <? } ?>
</td>
              <?
              } else if(!(strpos($key, "id_")===FALSE)) {
                  $tmp_table = str_replace("id_", "", $key);
                  $tmprs = mysql_query("SELECT * FROM `".$config_table_prefix.$tmp_table."` WHERE id='$cell'" );
                  
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
</tr><tr>
<th>
        <? echo $tmpField; ?>
      </th>
<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
  &#160; <?php lyteFrame("rs_exec_functions.php?fun=printRecord&param1=$tmp_table&param2=".$cell,$rcell,$rcell); ?>
</td>
                <?
                } else {
                  ?>
<? $c=$c+1; ?>
<script>
var testo<?=$c?>="<?php echo jstr($cell); ?>";
var testostrip<?=$c?>="<?php echo jstr(strip_tags($cell)); ?>";
</script>
</tr><tr>
<th>
        <? echo $tmpField; ?>
      </th>
<td id="<?=$g_table.$c?>" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
                    <?php 
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
</tr><tr>
<th>
        <? echo $tmpField; ?>
      </th>
<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $idmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
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
  </tr><tr><td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod) ? " style=\"background: #F1DCAB;\"" : "" ?>>
    (nessuno)
  </td>
      <?
      }
    }
    ?>
</tr>
<?php
}
?>
</table>
