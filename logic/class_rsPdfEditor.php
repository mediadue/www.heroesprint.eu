<?php
Class rsPdfEditor {
  var $g_id;
  var $g_table;
  
  function rsPdfEditor($id="",$table="") {
    $this->g_id=$id;
    $this->g_table=$table;     
  }
  
  function action(){
    if($_POST['rsPdfEditAction']!="1") return;
    global $config_table_prefix;
    $objUtility=new Utility();
    $objHtml=new Html();
    $objConfig = new ConfigTool();
    
    if($_POST['rsInitEditor']=="1") {
      $options=unserialize(rawurldecode(stripslashes($_POST['options'])));
      
      $ObjrsPdfEditor=new rsPdfEditor($options['id'],$options['table']);
      $ObjrsPdfEditor->_print();
      exit;
    }
    
    if($_POST['getFields']=="1") {
      $id=$_POST['id'];
      $this->printFields($id);
      exit;
    }
    
    if($_POST['rsExistTemplate']=="1") {
      $name=rawurldecode($_POST['name']);
      
      $rs=getTable("rspdf_layout","","nome='".$name."'");
      if(count($rs)>0) {
        echo ln("Un template con questo nome esiste già, si desidera sovrascriverlo?");
      }else{
        echo "-1";  
      }
      exit;
    }
    
    if($_POST['rsRefreshTemplate']=="1") {
      $this->printTemplate();
      exit;
    }
    
    if($_POST['rsGetTemplate']=="1") {
      $name=rawurldecode($_POST['name']);
      $rs=getTable("rspdf_layout","","nome='".$name."'");
      
      echo $rs[0]['html_editor'];
      exit;
    }
    
    if($_POST['rsDelTemplate']=="1") {
      $name=rawurldecode($_POST['name']);
      
      $sql="DELETE FROM ".$config_table_prefix."rspdf_layout WHERE nome='".$name."'";
      mysql_query($sql);
      
      echo 1;
      exit;
    }
    
    if($_POST['rsSaveTemplate']=="1") {
      $html=$_POST['html'];
      $name=rawurldecode($_POST['name']);
      
      $rs=getTable("rspdf_layout","","nome='".$name."'");
      if(count($rs)==0) {
        $sql="INSERT INTO ".$config_table_prefix."rspdf_layout (nome,html_editor,buffer,attivo) VALUES ('".$name."','".$html."','',1)";
        mysql_query($sql);
      }else{
        $sql="UPDATE ".$config_table_prefix."rspdf_layout SET html_editor='".$html."' WHERE id='".$rs[0]['id']."'";
        mysql_query($sql);  
      }
      
      echo ln("Salvataggio eseguito correttamente");
      exit;
    }
    
    if($_POST['PRINT-PDF-DO']=="1") {
      ?>
  		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html>
        <head>
          <?php $objHtml->adminHeadsection(); ?>
        </head>
      <body>
      <?php
      require_once (SERVER_DOCROOT."logic/fpdf/fpdf_alpha.php");
      
      $pdf=new PDF_ImageAlpha('P','mm','A4');
  		$pdf->SetMargins(0, 0, 0); 
      $pdf->SetAutoPageBreak(false, 0);
      
  		$gtable=$_POST["rsTable"];
      $html=$_POST['html'];
      $arr_users=$_SESSION["rsTable2_".$gtable."_selection"];
      
      if(!is_array($arr_users)) $arr_users=array();
      if(count($arr_users)==0) array_push($arr_users, 0);
      
      for($z=0;$z<count($arr_users);$z++) {
        $pdf->AddPage();
        $p_grafica=false;
        
    		for($i=0;$i<count($html);$i++) {
          $html[$i]=rawurldecode(stripslashes($html[$i]));
          $text=explode("#_#", $html[$i]);    
          
          $x=$text[0];
          $y=$text[1];
          $w=$text[2];
          $h=$text[3]-3;
          
          //CONVERTO IN MILLIMETRI
          //echo "<span style='color:red;'>x=$x<br>y=$y<br>w=$w<br>h=$h<br></span>";
          
          $x=(210*$x)/908;
          $y=(210*$y)/908;
          $w=(210*$w)/908;
          $h=(210*$h)/908;
  
          //echo "<span style='color:red;'>x=$x<br>y=$y<br>w=$w<br>h=$h<br></span>";
          
          $grassetto=$text[4];
          $corsivo=$text[5];
          $textAlign=$text[6];
          $underline=$text[7];
          
          $align="";
          if($textAlign=="center") $align="C";
          if($textAlign=="left") $align="L";
          if($textAlign=="right") $align="R";
          if($textAlign=="justify") $align="J";
          
          $style="";
          if($grassetto=="700") $style=$style."B";
          if($corsivo=="italic") $style=$style."I";
          if($underline=="underline") $style=$style."U";
          
          $fontSize=$text[8];
          $fontSize=str_replace("px", "", $fontSize);
          $fontSize=$fontSize/1.5;
          $line_height=$fontSize/2.6;
          
          $fontFamily=$text[9];
          if($fontFamily=="Times New Roman") $fontFamily="Times";
          
          $rgb=$text[10];
          if($rgb=="") $rgb="0,0,0";
          
          $grafica=$text[11];
          $img=$text[12];
          
          $txt=$text[13];
          
          if(left(trim($txt),2)== "#`" && right(trim($txt),1)=="#"){
            $txt=trim($txt);
            $tmparr=explode("`.", $txt);
            $table=right($tmparr[0],strlen($tmparr[0])-2);
            $table=right($table,strlen($table)-strlen($config_table_prefix));
            $field=left($tmparr[1],strlen($tmparr[1])-1);
            //print_r($_SESSION["rsTable2_".$config_table_prefix.$gtable."_selection"]);
            //print_r($_SESSION[$config_table_prefix.$gtable."checkSel"]);
            if($table!=$gtable) {
              $rs=Table2ByTable1($gtable,$table,$arr_users[$z],"","");
              $txt="";
              if($rs && is_array($_SESSION["rsTable2_".$table."_selection"])){
                while (list($key, $row) = each($rs)) {
                  if(in_array($row['id'], $_SESSION["rsTable2_".$table."_selection"])) {
                    $txt.=$row[$field]."\n";
                  }  
                }
              }else{
                $rs=getTable($table,"","");
                $txt="";
                while (list($key, $row) = each($rs)) {
                  $txt.=$row[$field]."\n";    
                }
              }    
            }else{
              $trow=retRow($table,$arr_users[$z]);
              $txt=$trow[$field];
            } 
          }
          
          if(!$p_grafica && $grafica!="none" && $grafica!="undefined") {
            $p_grafica=true;
            $grafica=substr($grafica, strpos($grafica,$objConfig->get("path-resources-upload")."/"), strlen($grafica));
            $grafica=substr($grafica, strpos($grafica,"/")+1, strlen($grafica));
            $grafica=substr($grafica, 0, strlen($grafica)-2); 
            $pdf->Image($objUtility->getPathResourcesDynamicAbsolute().$grafica,0,0,210,297);  
          }
          //echo "ppp".$x;exit;
          if($x>=1) {
            $pdf->SetXY($x-1,$y);
          }else{
            $pdf->SetXY($x,$y);  
          }
           
          $pdf->SetFont($fontFamily,$style,$fontSize);
           
          if($img=="null" || $img=="undefined") {
            $rgb=explode(",",$rgb);
            $pdf->SetTextColor($rgb[0],$rgb[1],$rgb[2]);
            $pdf->MultiCell($w+2.5,$line_height,utf8_decode($txt),0,$align);
          } else { 
            $img=substr($img, strpos($img,$objConfig->get("path-resources-upload")."/"), strlen($img));
            $img=substr($img, strpos($img,"/")+1, strlen($img));
            $pdf->Image($objUtility->getPathResourcesDynamicAbsolute().$img,$x,$y,$w,$h);  
          }
        }
      }
  		
  		$funique=$objUtility->getFilenameUnique().".pdf";
      $pdf->Output($objUtility->getPathResourcesDynamicAbsolute().$funique,'F');
      ?>
      
      <a id="d_pdf" href="<?php echo $objUtility->getPathResourcesDynamic().$funique; ?>"><?php echo ln("Download del PDF generato il ").date("d/m/y").ln(" alle ore ").date("H:i"); ?></a>
      </body>
      </html>
      <?
      exit;
    }
  }
  
  function _print() {
    global $config_table_prefix;
    $objUtility=new Utility();
    ?>
    <div id="rsPdfEditor" class="rsPdfEditor-container" rsId="<?php echo $this->g_id; ?>">
      <a href='<?php echo $objUtility->getPathBackoffice(); ?>upload/index.php?row=1&id=0&table=<?php echo $config_table_prefix; ?>pdf_image&field=immagine_file&UploadComplete=off_UploadComplete&retField=off_UploadedFile<?php echo $this->g_id; ?>' rel="lyteframe" title=""  rev="width: 700px; height: 460px; scrolling: auto; border: no;" class="off_UploadImage" target="_blank" style="display:none;">Upload</a>
      <input type="hidden" value="" id="off_UploadedFile<?php echo $this->g_id; ?>" />
      
      <a href='<?php echo $objUtility->getPathBackoffice(); ?>upload/index.php?row=1&id=0&table=<?php echo $config_table_prefix; ?>pdf_image&field=immagine_file&UploadComplete=off_UploadComplete&retField=off_UploadedFileSfondo<?php echo $this->g_id; ?>' rel="lyteframe" title=""  rev="width: 700px; height: 460px; scrolling: auto; border: no;" class="off_UploadImageSfondo" target="_blank" style="display:none;">Upload</a>
      <input type="hidden" value="" id="off_UploadedFileSfondo<?php echo $this->g_id; ?>" />
      <div id="testRibbon" class="officebar">
        <ul>
          <li class="current">
          <a href="#" rel="home"><?php echo ln("File"); ?></a>
            <ul>
              <li>
                <span><?php echo ln("Documento"); ?></span>
                <div class="off_button off_textlist">
                  <ul>
                    <li class="rsPdfNewDoc"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/new16.gif" alt="" /><?php echo ln("Nuovo"); ?></a></li>
                    <li class="rsPdfSaveDoc"><a href="#" ><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/disk.png" alt="" /><?php echo ln("Salva"); ?></a></li>
                    <li class="rsPdfSaveDocAs"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/media-floppy.png" alt="" /><?php echo ln("Salva con nome..."); ?></a></li>
                  </ul>
                </div>
              </li>
              <li>
                <span class="rsPdf-CurrTemplate"><?php echo ln("Template"); ?></span>
                <div class="textboxlist">
                  <ul>
                    <li>
                      <?php $this->printTemplate(); ?>&nbsp;
                    </li>
                  </ul>
                </div>
                <div class="off_button off_list">
                  <ul>
                    <li><a href="#"></a></li>
                    <li id="rsLoadTemplate"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/Upload.png" alt="" /></a></li>
                    <li><a href="#"></a></li>
                  </ul>
                </div>
                <div class="off_button off_list">
                  <ul>
                    <li><a href="#"></a></li>
                    <li id="rsDeleteTemplate"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/elimina.gif" alt="" /></a></li>
                    <li><a href="#"></a></li>
                  </ul>
                </div>
              </li>
            </ul>  
          </li>
          <li >
            <a href="#" rel="home"><?php echo ln("Modifica"); ?></a>
            <ul>
              <li>
                <span><?php echo ln("Inserimento"); ?></span>
                <div class="off_button nuovo_testo">
                  <a href="#" rel="table"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/drawtools32.png" alt="" /><?php echo ln("Testo"); ?></a>
                </div>
                <div class="off_button nuova_immagine">
                  <a href="#" rel="table"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/image32.png" alt="" /><?php echo ln("Foto"); ?></a>
                </div>
                <div class="off_button off_textlist">
                  <ul>
                    <li class="stampaPdf"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/print.gif" alt="" /><?php echo ln("Stampa"); ?></a></li>
                    <li class="elimina_testo"><a href="#" ><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/elimina.gif" alt="" /><?php echo ln("Rimuovi elemento"); ?></a></li>
                    <li class="immagine_sfondo"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/quickitems16.gif" alt="" /><?php echo ln("Immagine di sfondo"); ?></a></li>
                  </ul>
                </div>
              </li>
              <li>
                <span><?php echo ln("Sostituzione"); ?></span>
                <div class="textboxlist">
                  <ul>
                    <li><textarea id="testoLettera" name="testoLettera"></textarea></li>
                    <li>&nbsp;</li>
                    <li>&nbsp;</li>
                  </ul>
                </div>
                <div class="off_button off_list">
                  <ul>
                    <li><a href="#"></a></li>
                    <li><a href="#"></a></li>
                    <li id="c_mod"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/replace16.gif" alt="" /></a></li>
                  </ul>
                </div>
              </li>
              <li>
                <span><?php echo ln("Formattazione"); ?></span>
                <div class="off_button off_list">
                  <ul>
                    <li class="all_sinistra"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/all_sinistra.gif" alt="" /></a></li>
                    <li class="grassetto"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/grassetto.gif" alt="" /></a></li>
                    <li><a href="#"></a></li>
                  </ul>
                </div>
                <div class="off_button off_list">
                  <ul>
                    <li class="centrato"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/centrato.gif" alt="" /></a></li>
                    <li class="corsivo"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/corsivo.gif" alt="" /></a></li>
                    <li><a href="#"></a></li>
                  </ul>
                </div>
                <div class="off_button off_list">
                  <ul>
                    <li class="all_destra"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/all_destra.gif" alt="" /></a></li>
                    <li class="sottolineato"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/sottolineato.gif" alt="" /></a></li>
                    <li><a href="#"></a></li>
                  </ul>
                </div>
                <div class="off_button off_list">
                  <ul>         
                    <li class="giustificato"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/giustificato.gif" alt="" /></a></li>
                    <li class="rsPdf-selColor" style="display:none;"><a href="#"></a></li>
                    <li><a href="#"></a></li>
                  </ul>
                </div>
                <div class="textboxlist">
                  <ul>
                    <li>
                      <select id="off_FontFamily" name="off_FontFamily">
                         <option  value="Courier">Courier</option>
                         <option  value="Arial">Arial</option>
                         <option value="Times New Roman" selected>Times New Roman</option>
                         <option value="Symbol">Symbol</option>
                      </select>
                      <br>
                    </li>
                    <li></li>
                    <li></li>
                  </ul>
                </div>
                <div class="textboxlist">
                  <ul>
                    <li>
                      <select id="off_FontSize" name="off_FontSize">
                         <option id="6px" value="6">6px</option>
                         <option id="7px"  value="7">7px</option>
                         <option id="8px" value="8">8px</option>
                         <option id="9px" value="9">9px</option>
                         <option id="10px" value="10">10px</option>
                         <option id="11px" value="11">11px</option>
                         <option id="12px" value="12">12px</option>
                         <option id="13px" value="13">13px</option>
                         <option id="14px" value="14" selected>14px</option>
                         <option id="15px" value="15">15px</option>
                         <option id="16px" value="16">16px</option>
                         <option id="17px" value="17">17px</option>
                         <option id="18px" value="18">18px</option>
                         <option id="19px" value="19">19px</option>
                         <option id="20px" value="20">20px</option>
                         <option id="21px" value="21">21px</option>
                         <option id="22px" value="22">22px</option>
                         <option id="23px" value="23">23px</option>
                         <option id="24px" value="24">24px</option>
                         <option id="25px" value="25">25px</option>
                      </select>
                    </li>
                    <li></li>
                    <li></li>
                  </ul>
                </div>
              </li>
            </ul>
          </li>
          <li>
            <a href="#" rel="home"><?php echo ln("Database"); ?></a>
            <ul>
              <li>
                <span><?php echo ln("Tabelle"); ?></span>
                <div class="textboxlist">
                  <ul>
                    <li>
                      <select id="off_tables" name="off_tables">
                        <?php 
                        $rs=getTable("rstbl2_tabelle","titolo_visualizzato ASC",""); 
                        while (list($key, $row) = each($rs)) { ?>
                          <option id="<?php echo $row['id']; ?>" value="<?php echo $row['tabella']; ?>"><?php echo $row['titolo_visualizzato']; ?></option>
                        <? } ?>     
                      </select>
                    </li>
                  </ul>
                </div>
              </li>
              <li>
                <span><?php echo ln("Campi"); ?></span>
                <div class="textboxlist">
                  <ul>
                    <li>
                      <?php $this->printFields($rs[0]['id']); ?>&nbsp;
                    </li>
                  </ul>
                </div>
                <div class="off_button off_list">
                  <ul>
                    <li><a href="#"></a></li>
                    <li id="c_mod_db"><a href="#"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>images_office/replace16.gif" alt="" /></a></li>
                    <li><a href="#"></a></li>
                  </ul>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>                                          
      <div style="margin-top:130px;">&nbsp;</div>

      <div class="inputdata">
        <form action="rsAction.php" id="stampaLettera" name="stampaLettera" method="post" target="_blank" enctype="multipart/form-data" onsubmit="return checkForm(this)"/>
          <div class="rsPdfLettera-container">
            <div id="lettera" name="lettera" rsTemplate=""></div>
          </div>
				</form>
			</div>
    </div>
    <?php  
  }
  
  function printFields($id_table) { ?>
    <select id="off_fields" name="off_fields">
      <?php 
      $rs=permissionField(Table2ByTable1("rstbl2_tabelle","rstbl2_campi",$id_table,"","Ordinamento ASC")); 
      while (list($key, $row) = each($rs)) { ?>
        <option id="<?php echo $row['id']; ?>" value="<?php echo $row['campo_hidden']; ?>"><?php echo $row['titolo_visualizzato']; ?></option>
      <? } ?>      
    </select><?php
  }
  
  function printTemplate() { ?>
    <select id="off_template" name="off_template">
      <?php 
      $rs=getTable("rspdf_layout","nome ASC","attivo='1'"); 
      while (list($key, $row) = each($rs)) { ?>
        <option id="<?php echo $row['id']; ?>" value="<?php echo $row['id']; ?>"><?php echo $row['nome']; ?></option>
      <? } ?>      
    </select><?php
  }
  
}