<?php
	Class rsForm {
		var $g_table;
		var $g_name;
		var $g_estensioni_ammesse; //array('.jpg','.gif');
		var $g_dimensione_massima; // in byte
		
		function rsForm($form,$validate="",$nome="",$estensioni_ammesse="",$dimensione_massima="") {
			$rs=getTable("lista_forms","","Nome='$form' AND Attivo='1'");
			while (list($key, $row) = each($rs)) {
			   $id=$row['id'];
			   $name=$row['Nome'];
      }
      if($nome!="") $name=$nome;
      $this->g_table=$id;
      $this->g_name=$name;
      $this->g_estensioni_ammesse=$estensioni_ammesse;
      $this->g_dimensione_massima=$dimensione_massima;
      $this->validate=$validate;
			$this->jsControl();
			
			?>
			<script language="JavaScript"> 
        function mostra(id) {
          var height=document.getElementById(id).style.height;
          document.getElementById(id).style.overflow='hidden';
          
          if(height=="") {
            document.getElementById(id).style.height='1px';
            document.getElementById(id).style.visibility='hidden';
          } else {
            document.getElementById(id).style.height='';
            document.getElementById(id).style.visibility='visible';
          }
        }
        
        function doCheck(field) {
          var str=document.getElementById(field).value; 
          str=str.replace(",",".");
          var dstr="";
          for(var i=0;i<str.length;i++) {
            if(str.charAt(i)=='.' || str.charAt(i)=='0' || str.charAt(i)=='1' || str.charAt(i)=='2' || str.charAt(i)=='3' || str.charAt(i)=='4' || str.charAt(i)=='5' || str.charAt(i)=='6' || str.charAt(i)=='7' || str.charAt(i)=='8' || str.charAt(i)=='9') {
              dstr=dstr+str.charAt(i); 
            }
          }
          document.getElementById(field).value=dstr;
        }
      </script>
			<?
		}
		
		function getGenerali() {
      $rs=Table2ByTable1("lista_forms","form_dati_generali",$this->g_table,"Attivo='1'","");
      return $rs[0];  
    }
    
    function retLabel($field) {
      $rs=Table2ByTable1("lista_forms","form",$this->g_table,"","Ordinamento");
      while (list($key, $row) = each($rs)) {
        if($row['Campo_lst']==$field) return ln($row['label']);
      }
      return false;  
    }
		
		function _print($tcampo="",$tid="",$tcampo2="",$tid2="") {
      global $config_table_prefix;
      ?>
      <script>
      $(document).ready(function(){
        <?php if($this->validate=="") { ?> 
          $("form[name=form<?php echo $this->g_name; ?>]").live("submit",function(){
            return <?php echo $this->g_name; ?>checkForm();  
          });
        <? } ?>
      });
      </script>
      <div id="forms" class="style5">
        <div id="responseform"></div>
        <form enctype="multipart/form-data" id="form<?php echo $this->g_name; ?>" name="form<?php echo $this->g_name; ?>" action="" method="post">	
        	<input type="hidden" name="<?=$this->g_table?>" value="1" />
          <fieldset>
            <?php
            $rs=Table2ByTable1("lista_forms","form_dati_generali",$this->g_table,"Attivo='1'","");
            //$rs=getTable("form_dati_generali","","Attivo='1'");
            while (list($key, $row) = each($rs)) {
              ?><legend><?php echo ln($row['Titolo']); ?></legend><?
            }
            
        	  $rs=Table2ByTable1("lista_forms","form",$this->g_table,"Attivo='1'","Ordinamento");
            //$rs=getTable($this->g_table,"Ordinamento","Attivo='1'");
            $i=0;
            while (list($key, $row) = each($rs)) {
              $i++;
              $combo="";
              $tipo=retRow("form_tipi",$row['id_form_tipi']);
              $tab=retRow("tabelle",$tipo['id_tabelle']);
              $tab=str_replace($config_table_prefix, "",$tab['Nome']);
              $combo=getTable($tab);
              $tipo=$tipo['tipo'];
              ?>
               
              <?php if($tipo=="hidden") { ?>
                <input type="hidden" name="<?php echo $row['Campo_lst']; ?>" value="<?php echo $row['Valore']; ?>" />	
        		  <? } elseif($tipo!="submit") { ?>
              <div class="rsForm-row <?php if($i==1) echo 'rsForm-first'; ?>">					
                <?php if($tipo=="text") { ?>
                  <label for="<?php echo $row['Campo_lst']; ?>">&#160;<?php echo ln($row['label']); ?></label>
                  <input type="text" class="rsForm-text" name="<?php echo $row['Campo_lst']; ?>" size="<?php if($row['Altezza']!="0") echo "height:".$row['Altezza']."px;"; ?><?php echo $row['Larghezza']; ?>" style="<?php if($row['Larghezza']!="0") echo "width:".$row['Larghezza']."px;"; ?>background-image:url(<?php echo retFile($row['Icona_file']); ?>);" value="<?php echo $row['Valore']; ?>" <?php if($row['Sola_lettura']!="0") echo "DISABLED"; ?> />					
          		  <? }
          		  if($tipo=="born-date") { ?>
                  <label for="<?php echo $row['Campo_lst']; ?>">&#160;<?php echo ln($row['label']); ?></label>
                  <div class="rsForm-data"><? formdata($row['Campo_lst'],'','','','',$row['Valore'],"1900","2000"); ?></div>			
          		  <? }
          		  if($tipo=="date") { ?>
                  <label for="<?php echo $row['Campo_lst']; ?>">&#160;<?php echo ln($row['label']); ?></label>
                  <div class="rsForm-data"><? formdata($row['Campo_lst'],'','','','',$row['Valore']); ?></div>			
          		  <? }
          		  if($tipo=="currency") { ?>
                  <label for="<?php echo $row['Campo_lst']; ?>">&#160;<?php echo ln($row['label']); ?></label>
                  <input type="text" class="rsForm-text" name="<?php echo $row['Campo_lst']; ?>" id="<?php echo $row['Campo_lst']; ?>" size="<?php if($row['Altezza']!="0") echo "height:".$row['Altezza']."px;"; ?><?php echo $row['Larghezza']; ?>" style="<?php if($row['Larghezza']!="0") echo "width:".$row['Larghezza']."px;"; ?>background-image:url(<?php echo retFile($row['Icona_file']); ?>);" value="<?php echo $row['Valore']; ?>" <?php if($row['Sola_lettura']!="0") echo "DISABLED"; ?> onKeyUp="doCheck(this.id);" />					
          		  <? }
          		  if($tipo=="label") { ?>
                  <div class="rsForm-description"><?php echo $row['Valore']; ?></div>				
          		  <? }
          		  if($tipo=="textarea") { ?>
          		    <label for="<?php echo $row['Campo_lst']; ?>">&#160;<?php echo ln($row['label']); ?></label>
                  <textarea name="<?php echo $row['Campo_lst']; ?>" cols="30" rows="10" style="<?php if($row['Altezza']!="0") echo "height:".$row['Altezza']."px;"; ?><?php if($row['Larghezza']!="0") echo "width:".$row['Larghezza']."px;"; ?>background-image:url(<?php echo retFile($row['Icona_file']); ?>);" <?php if($row['Sola_lettura']!="0") echo "READONLY"; ?>><?php echo $row['Valore']; ?></textarea>		
          		  <? }
          		  if($tipo=="checkbox") { ?>
                  <input type="checkbox" class="rsForm-checkbox" name="<?php echo $row['Campo_lst']; ?>" value="1" <?php if($row['Sola_lettura']!="0") echo "DISABLED"; ?> <?php if($row['Valore']=="1") echo "CHECKED"; ?> />	
          		    <label for="<?php echo $row['Campo_lst']; ?>" class="rsForm-checkbox-label">&#160;<?php echo ln($row['label']); ?></label>
                <? }
          		  if(count($combo)>0) { ?>
                  <label for="<?php echo $row['Campo_lst']; ?>">&#160;<?php echo ln($row['label']); ?></label>
                  <?php comboBox($tab,$field1="",$field2="",$selected="",$multiple="",$onchange="",$echoId="-1",$nome=$row['Campo_lst']);	
          		  }
          		  if($tipo=="upload") { ?>
                  <label for="<?php echo $row['Campo_lst']; ?>">&#160;<?php echo ln($row['label']); ?></label>
                  <input class='rsForm-file' name='<?php echo $row['Campo_lst']; ?>' type='file' />
                <? }
          		  //if($tipo=="submit") $submit=$row['id']; ?>
              </div>
              <?
              }
            }
            
            $row=retRow("form",$submit); ?>
            <div class="rsForm-submit"><input type="image" src="css/images/b_send.gif" alt="invia" width="52" height="19" border="0" /></div>			
        	</fieldset>	
          <?php
            if($tcampo!="") {
              ?><input type="hidden" name="<?php echo $tcampo; ?>" value="<?php echo $tid; ?>" /><?
            }
            if($tcampo2!="") {
              ?><input type="hidden" name="<?php echo $tcampo2; ?>" value="<?php echo $tid2; ?>" /><?
            }
          ?>				
        </form>	
      </div>
      <?
    }
    
    function elaboraForm($retmess="") {
      $objConfig = new ConfigTool();
      $objUtility = new Utility;
      $objMailing = new Mailing;
      global $config_table_prefix;
      
      //scorro tutti i $_FILES per prelevare gli allegati
      $allegati=array();
      reset($_FILES);
      while (list($key, $value) = each($_FILES)) { 
        $ret = $this->insOggetti($value);
        if(strpos($ret, "#OK#")!==FALSE) {
          $myid=str_replace("#OK#", "", $ret);
          $allegati[$key] = $myid;
        }
      }

      if($_POST[$this->g_table]=='1') {
        $dbname = $objConfig->get("db-dbname");
        $res=TRUE;
        $this->elaborato=1;
        $rs=Table2ByTable1("lista_forms","form_dati_generali",$this->g_table,"Attivo='1'","");
        //$rs=getTable($this->g_table."_dati_generali","","Attivo='1'");
        while (list($key, $row) = each($rs)) {
          $destinatario=$row['Destinatario'];
          $mittente=retRow("form",$row['id_form']);
          $mittente=$mittente['Campo_lst'];
          $mittente=$_POST[$mittente];
          $oggetto=ln($row['Oggetto']);
          $conferma=ln($row['Messaggio_conferma_editor']);
          $errore=ln($row['Messaggio_errore_editor']);
          $conferma_email=ln($row['Email_conferma_editor']);
        }
        
        $messaggio="";
        $allegato="";
        $allegato_type="";
        $allegato_name="";
        
        $rs=Table2ByTable1("lista_forms","form",$this->g_table,"Attivo='1'","Ordinamento ASC");
        //$rs=getTable($this->g_table,"Ordinamento","Attivo='1'");
        $i=0;
        while(list($key, $row) = each($rs)) {
          $campo=$row['Campo_lst'];
          $field=$row['label'];
          if($row['Sola_lettura']!="1") {
            if($row['id_form_tipi']=="13") {
              $_POST[$campo]=$_POST['anno'.$campo]."-".$_POST['mese'.$campo]."-".$_POST['giorno'.$campo];  
            }
            
            if($_POST[$campo]!="") { 
              $messaggio="$field: ".$_POST[$campo]."<br>".$messaggio;
              if($row['Larghezza']!='1') $sql_fields.="`$campo` LONGTEXT NOT NULL ,";
              if($row['Larghezza']=='1') $sql_fields.="`$campo` BOOL NOT NULL ,";
              $sql_1.="$campo, ";
              $sql_2.="'".$_POST[$campo]."', ";
              $sql_3.="$campo='".$_POST[$campo]."' AND ";
            }
 
            if(isset($_FILES[$campo])) {
              $sql_fields.="`$campo` INT(6) NOT NULL ,";
              $sql_1.="$campo, ";
              $sql_2.="'".$allegati[$campo]."', ";
              //$sql_3.="$campo='".$allegati[$campo]."' AND ";
            }
          }
        }
        
        $sql_fields=substr($sql_fields, 0, strlen($sql_fields)-2); 
        $sql_1=substr($sql_1, 0, strlen($sql_1)-2);
        $sql_2=substr($sql_2, 0, strlen($sql_2)-2);
        $sql_3=substr($sql_3, 0, strlen($sql_3)-5);
        
        $sql1="SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = '$dbname'
        AND table_name = '".$config_table_prefix."form_archivio_".$this->g_name."'";
        $query1 = mysql_query ($sql1);
        
        if(mysql_num_rows($query1)==0) {
          $sql2="CREATE TABLE `".$config_table_prefix."form_archivio_".$this->g_name."` (`id` INT( 6 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , $sql_fields, `Data` DATE NOT NULL ,`Ora` TIME NOT NULL ,`Errori` BOOL NOT NULL) ENGINE = MYISAM; "; 
          $query2 = mysql_query ($sql2);
          
          $sql2="CREATE TABLE `".$config_table_prefix."lista_forms#form_archivio_".$this->g_name."_nm` (`id` INT( 6 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , `id_lista_forms`  INT( 6 ) NOT NULL , `id_form_archivio_".$this->g_name."`  INT( 6 ) NOT NULL) ENGINE = MYISAM; ";
          $query2 = mysql_query ($sql2);
        } else {
           $tmparr=explode(",", $sql_fields);
          while (list($key, $row) = each($tmparr)) {
            $sql="ALTER TABLE `".$config_table_prefix."form_archivio_".$this->g_name."` ADD COLUMN $row";
            mysql_query($sql); 
          }
        }
        
        $sql="SELECT id FROM `".$config_table_prefix."form_archivio_".$this->g_name."` WHERE $sql_3";
        $query = mysql_query ($sql);
        
        if(!$query) return;
        
        if(mysql_num_rows($query)==0) {
          $sql3="INSERT INTO `".$config_table_prefix."form_archivio_".$this->g_name."` ($sql_1, Data, Ora, Errori) VALUES ($sql_2,CURDATE(),CURTIME(),'".!$res."')";
          $query3 = mysql_query ($sql3);
          $id=mysql_insert_id();

          $sql3="INSERT INTO `".$config_table_prefix."lista_forms#form_archivio_".$this->g_name."_nm` (id_lista_forms,id_form_archivio_".$this->g_name.") VALUES ('".$this->g_table."','$id')";
          $query3 = mysql_query ($sql3);
        }else{
          $retarr=$objUtility->buildRecordset($query);
          
          if(function_exists("form_".$this->g_name."_after_elabora")) eval("form_".$this->g_name."_after_elabora(\$retarr[0]['id'],0);");
          return $retarr[0]['id']; 
        }
        
        $sql="SHOW COLUMNS FROM `".$config_table_prefix."form_archivio_".$this->g_name."`";
        $cols=mysql_query($sql);
        $cols = $objUtility->buildRecordset($cols);
        $root=$_SERVER['SERVER_NAME'].$objUtility->getPathRoot();
        $tmpuser=retRow("form_archivio_".$this->g_name,$id);
 
        while (list($key, $row) = each($cols)) {
          $field=$row['Field'];
          
          $conferma=str_replace("#$field#", $tmpuser[$field], $conferma);
          $conferma_email=str_replace("#$field#", $tmpuser[$field], $conferma_email);
          $errore=str_replace("#$field#", $tmpuser[$field], $errore);
          $oggetto=str_replace("#$field#", $tmpuser[$field], $oggetto);
          
          $conferma=str_replace("#SERVER_NAME#", $_SERVER['SERVER_NAME'], $conferma);
          $conferma_email=str_replace("#SERVER_NAME#", $_SERVER['SERVER_NAME'], $conferma_email);
          $errore=str_replace("#SERVER_NAME#", $_SERVER['SERVER_NAME'], $errore);
          $oggetto=str_replace("#SERVER_NAME#", $_SERVER['SERVER_NAME'], $oggetto);
          
          $conferma=str_replace("#SERVER_ROOT#", $root, $conferma);
          $conferma_email=str_replace("#SERVER_ROOT#", $root, $conferma_email);
          $errore=str_replace("#SERVER_ROOT#", $root, $errore);
          $oggetto=str_replace("#SERVER_ROOT#", $root, $oggetto);
          
          $conferma=str_replace("#RS-NOME-AZIENDA#", $objConfig->get("email-fromname"), $conferma);
          $conferma_email=str_replace("#RS-NOME-AZIENDA#", $objConfig->get("email-fromname"), $conferma_email);
          $errore=str_replace("#RS-NOME-AZIENDA#", $objConfig->get("email-fromname"), $errore);
          $oggetto=str_replace("#RS-NOME-AZIENDA#", $objConfig->get("email-fromname"), $oggetto);
        }
        
        if($destinatario!="") {
          $res=$objMailing->mmail($destinatario,$mittente,$oggetto,$messaggio,$allegato,$allegato_type,$allegato_name);
        }
        
        if($retmess!=-1) {
          if($conferma_email!="" && $mittente!="") {
            $res=$objMailing->mmail($mittente,$objConfig->get("email-from"),$oggetto,$conferma_email,$allegato,$allegato_type,$allegato_name);
          }
          
          
          if($res) box($conferma);
          if(!$res) box($errore);
        }
        
        if(function_exists("form_".$this->g_name."_after_elabora")) eval("form_".$this->g_name."_after_elabora(\$id,1);");
        return $id;
      }
    }
    
    function ctrlContattaci() {
      $rs=Table2ByTable1("lista_forms","form",$this->g_table,"Attivo='1' AND Obbligatorio='1'","");
      //$rs=getTable($this->g_table,"","Attivo='1' AND Obbligatorio='1'");
      if(!$rs) return;
      $objJs = new Js;
      
      while (list($key, $row) = each($rs)) {
        $tipo=retRow("form_tipi",$row['id_form_tipi']);
        
        $combo="";  
        $tab=retRow("tabelle",$tipo['id_tabelle']);
        $tab=str_replace($config_table_prefix, "",$tab['Nome']);
        $combo=getTable($tab);
        
        $tipo=$tipo['tipo'];
        if($tipo=="currency") $tipo="text";
        if($tipo=="upload") $tipo="text";
        if($tipo=="born-date" || $tipo=="date") $tipo="date";
        
        if(count($combo)>0) $tipo="select";
        
        $objJs->checkField($row['Campo_lst'], $tipo, strtoupper($row['Campo_lst']), $row['Campo_lst']);
      }
    }
    
    function jsControl() {
      $objJs = new Js;
      $objJs->dateHelper();
      ?>
      <script>
        function <?php echo $this->g_name; ?>checkForm() {
      		var theform = document.form<?php echo $this->g_name; ?>;
      		<?php $this->ctrlContattaci(); ?>
      		<?php if($this->validate=="") { ?>return true;<? } ?>
      	}
    	</script>
    	<?
    }
  

    function insOggetti($dati) {
      /*  questa funzione prende il nome dell'allegato, da $dati
      sposta il file nella directory specificata
      inserisce i dati dell'allegato nella tabella oggetti
      e restituisce l'id dell'inserimento*/
        
      global $config_table_prefix;
      $objUtility = new Utility;
      
      $dimensione_massima=$this->g_dimensione_massima;
      if($dimensione_massima=="") $dimensione_massima=2048000; //dimensione massima consentita per file in byte -> 1024 byte = 1 Kb
      $dimensione_massima_Kb=$dimensione_massima/1024;
      $cartella_upload=$objUtility->getPathResourcesDynamicAbsolute(); 
      
      $ext = explode(".", $dati['name']);
  		$ext=array_reverse($ext); 
      $ext=$ext[0];
      
      $filtrare=0;
      if(is_array($this->g_estensioni_ammesse)) $filtrare=1; //filtrare x estensioni ammesse? 1=si 0=no
      $array_estensioni_ammesse=$this->g_estensioni_ammesse; //estensioni ammesse
      
      if($dati['size']==0){
      	return ln("Nessun file selezionato per l'upload");
      }
      elseif($dati['size']>$dimensione_massima){
      	return ln("Il file selezionato per l'upload supera dimensione massima di")." ".$dimensione_massima_Kb." Kb";
      }else{
      	$nome_file=$dati['name'];
      	$errore="";
      	if($filtrare==1){
      		if(!in_array($ext,$array_estensioni_ammesse)){
      			$errore.=ln("Upload file non ammesso. Estensioni ammesse:").implode(", ",$array_estensioni_ammesse)."<br/>";
      		}
      	}
      	if(!file_exists($cartella_upload)){
      		$errore.=ln("La cartella di destinazione non esiste")."</br>";
      	}
      	
      	if($errore==""){
          $nome = $objUtility->getFilenameUnique();
          if(move_uploaded_file($dati['tmp_name'], $cartella_upload.$nome.".".$ext)){
      			//ora che il file è stato spostato, salvo la nuova riga nella tabella
      			$isprivate ='1';
      			
      			$sql = sprintf("INSERT INTO ".$config_table_prefix."oggetti (nome,path,originalname,ext,isprivate) VALUES ('%s','%s','%s','%s','%s')", $nome, $cartella_upload,$dati['name'], $ext, $isprivate);
      			mysql_query($sql);
      			$id=mysql_insert_id();
            return "#OK#".$id;
      		}else{
      			return ln("Impossibile effettuare l'upload del file");
      		}
      	}else{
      		return $errore;
      	}
      }    
    }  
  }
?>