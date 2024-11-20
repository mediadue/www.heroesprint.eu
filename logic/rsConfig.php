<?php
setlocale(LC_CTYPE, 'C');

$objConfig = new ConfigTool();
$config_table_prefix = $objConfig->get("db-table-prefix");

require_once (SERVER_DOCROOT."logic/class_db.php");
$objDb = new Db;
$conn = $objDb->connection($objConfig);
unset($objConfig);

if (!defined('JSON_UNESCAPED_SLASHES')) {
    define('JSON_UNESCAPED_SLASHES', 64);
}

require_once (SERVER_DOCROOT."logic/rs_enc.php");
require_once (SERVER_DOCROOT."logic/html2text/html2text.php");
require_once (SERVER_DOCROOT."logic/rsFunctions.php");
require_once (SERVER_DOCROOT."logic/class_html.php");
require_once (SERVER_DOCROOT."logic/class_js.php");
require_once (SERVER_DOCROOT."logic/class_menu.php");
require_once (SERVER_DOCROOT."logic/class_objects.php");
require_once (SERVER_DOCROOT."logic/class_users.php");
require_once (SERVER_DOCROOT."logic/class_utility.php");
require_once (SERVER_DOCROOT."logic/phpmailer/class.phpmailer.php");
require_once (SERVER_DOCROOT."logic/phpmailer/class.smtp.php");
require_once (SERVER_DOCROOT."logic/phpmailer/class.pop3.php");
require_once (SERVER_DOCROOT."logic/class_mailing.php");
require_once (SERVER_DOCROOT."logic/class_newsletter_gruppi.php");
require_once (SERVER_DOCROOT."logic/class_newsletter_utenti.php");
require_once (SERVER_DOCROOT."logic/class_rsTable.php");
require_once (SERVER_DOCROOT."logic/class_rsTable2.php");
require_once (SERVER_DOCROOT."logic/class_rsForm.php");
require_once (SERVER_DOCROOT."logic/class_rsAgentAi.php");
require_once (SERVER_DOCROOT."logic/class_clienti.php");
require_once (SERVER_DOCROOT."logic/class_documents.php");
require_once (SERVER_DOCROOT."logic/class_carrello.php");
require_once (SERVER_DOCROOT."logic/class_paginazione.php");
require_once (SERVER_DOCROOT."logic/class_session.php");
require_once (SERVER_DOCROOT."logic/class_rsWindows.php");
require_once (SERVER_DOCROOT."logic/class_rsWinMod.php");
require_once (SERVER_DOCROOT."logic/class_rsStrutture.php");
require_once (SERVER_DOCROOT."logic/class_rsPdfEditor.php");
require_once (SERVER_DOCROOT."logic/class_rsChat.php");
require_once (SERVER_DOCROOT."logic/class_JavaScriptPacker.php");
require_once (SERVER_DOCROOT."logic/class_JSMin.php");

require_once (SERVER_DOCROOT."include/inc.events.php");
require_once (SERVER_DOCROOT."include/inc.header.php");

if($_POST['phpss']!="") {
  $objphpss = new Session($_POST['phpss']);
  $_SESSION=$objphpss->raw(); 
}

// generazione form data per inserimento/modifica --------------------------------------------- 

function formdata($var,$style,$id,$table,$js,$dateselected=false,$start="",$end=""){
 if($start=="") $start=date("Y")-6;
 if($end=="") $end=date("Y")+3; 
 if ($dateselected)
 {
	$arraydatak=explode("-",$dateselected);
	if(strlen($arraydatak[2])==2) {
    $giorno=$arraydatak[2];
  	$mese=$arraydatak[1];
  	$anno=$arraydatak[0];
  }	else {
    $giorno=$arraydatak[0];
  	$mese=$arraydatak[1];
  	$anno=$arraydatak[2];
  }
 }
 else
 {
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
	<select name="giorno<?=$var?>" class='<?=$style?>-giorno'  <?echo $js;?>>
	<option value='00'>--</option>
					<? $n=1; 
					while ($n<=31){
					?><option value='<?=$n?>' <?if($n==$giorno){echo"selected";}?>><?=$n?></option><?
					$n++;}
					?></select>		
					
	<select name="mese<?=$var?>"  class='<?=$style?>-mese' <?=$js?>>
					<option value='00'>--</option>
					<? $n=1; 
					while ($n<=12){
					?><option value='<?=$n?>'  <?if($n==$mese){echo"selected";}?>><?=$n?></option><?
					$n++;}
					?></select>
					
	<select name="anno<?=$var?>"  class='<?=$style?>-anno'<?=$js?> >
						<option value='0000'>----</option>
					<? $n=$start; 
					while ($n<=$end){
					?><option value='<?=$n?>'  <?if($n==$anno){echo"selected";}?>><?=$n?></option><?
					$n++;}
					?></select><?
}


// -------------------------------------------------------------------------------------------------


function operator($field,$prefix="") {
  if($prefix=="") $prefix="op_";
  ?>
  <select name="<?=$prefix.$field?>" size="1"  style="width:130px;">
    <option value=""></option>
    <option value="LIKE">contiene il testo</option>
    <option value=">=">a partire da</option>
    <option value="<=">fino a</option>
    <option value="=">è uguale a</option>
    <option value=">">è maggiore di</option>
    <option value="<">è minore di</option>
    <option value="<>">è diverso da</option>
  </select>
  <?
}

function preorderTH($table) {
  if($table=="") return;
  global $config_table_prefix;
  $objUtility = new Utility;
  $result = mysql_query("SELECT * FROM ".$config_table_prefix.$table );
  $fields_num = mysql_num_fields($result);
  
  //RICORSIONE
  for($i=0; $i<$fields_num; $i++){
    $ver="";
    $tmp_table="";
    $field=mysql_field_name($result,$i);
    if(strpos($field, "id_")===FALSE) $ver=1;
    
    if($ver!=1) {
      $tmp_table = str_replace("id_", "", $field);
    } else {
      if( ($table!="oggetti" && $field!="id" && !strpos($field, "_hidden")) || ($table=="oggetti" && $field!="nome" && $field!="path" && $field!="isprivate" && $field!="id" && !strpos($field, "_hidden") )) { 
        ?>
        <th scope="col" abbr="Moduli" class="point" onclick="sortTable(this,1);" title="ordina"><? echo str_replace("_"," ",$field); ?><img src="<?php echo $objUtility->getPathBackofficeResources() ?>none.gif" border="0"></th>
        <?
      }
    }
    
    preorderTH($tmp_table);
  }
  //---
}

function preorderTD($table,$id="") {
  if($table=="") return;
  global $config_table_prefix;
  $objUtility = new Utility;
  $sqlWhere="";
  if($id!="") $sqlWhere=" WHERE id='$id'";
  
  $result = mysql_query("SELECT * FROM ".$config_table_prefix.$table.$sqlWhere );
  echo $config_table_prefix.$table;
  $fields_num = mysql_num_fields($result);
  
  //RICORSIONE
  for($i=0; $i<$fields_num; $i++){
    $ver="";
    $tmp_table="";
    $field=mysql_field_name($result,$i);
    if(strpos($field, "id_")===FALSE) $ver=1;
    
    if($ver!=1) {
      $tmp_table = str_replace("id_", "", $field);
      $tmp_id="";
    } else {
      if( ($table!="oggetti" && $field!="id" && !strpos($field, "_hidden")) || ($table=="oggetti" && $field!="nome" && $field!="path" && $field!="isprivate" && $field!="id" && !strpos($field, "_hidden") )) { 
        ?>
        <th scope="col" abbr="Moduli" class="point" onclick="sortTable(this,1);" title="ordina"><? echo str_replace("_"," ",$field); ?><img src="<?php echo $objUtility->getPathBackofficeResources() ?>none.gif" border="0"></th>
        <?
      }
    }
    
    preorderTD($tmp_table,$tmp_id);
  }
  //---
}


// generazione form data per inserimento/modifica --------------------------------------------- 

function formdataArr($var,$style,$id,$table,$js,$dateselected=false){
  if ($dateselected) {
	$arraydatak=explode("-",$dateselected);
	$giorno=$arraydatak[2];
	$mese=$arraydatak[1];
	$anno=$arraydatak[0];	
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
 <select name="giorno<?php echo $var; ?>[]" class='<?php echo $style; ?>1' <?php echo $js; ?>>
   <option value='00'>--</option>
	 <?php 
   $n=1; 
	 while ($n<=31){ ?>
     <option value='<?php echo $n; ?>' <?php if($n==$giorno){echo"selected";}?>><?php echo $n; ?></option><?php
	   $n++;
    }
		?>
  </select>				
	<select name="mese<?php echo $var; ?>[]"  class='<?php echo $style; ?>2' <?php echo $js; ?>>
		<option value='00'>--</option>
		<?php 
    $n=1; 
		while ($n<=12){ ?>
      <option value='<?php echo $n; ?>'  <?php if($n==$mese){echo"selected";}?>><?php echo $n; ?></option><?
			$n++;
    }
		?>
  </select>			
	<select name="anno<?php echo $var; ?>[]"  class='<?php echo $style; ?>3'<?php echo $js; ?>>
		<option value='0000'>----</option>
		<?php 
    $n=1900; 
		while ($n<=2020){ ?>
      <option value='<?php echo $n; ?>'  <?php if($n==$anno){echo"selected";}?>><?php echo $n; ?></option><?
			$n++;
    }
	?></select><?
}

// -------------------------------------------------------------------------------------------------
//if($_POST["UserRegDo"] && file_exists("arearis_userreg.php")) include ("arearis_userreg.php");
//if($_POST["UserUpdDo"] && file_exists("arearis_userupd.php")) include ("arearis_userupd.php");
?>
