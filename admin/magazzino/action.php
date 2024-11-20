<?php
header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: Public");

require_once("_docroot.php");
require_once(SERVER_DOCROOT."logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$objMailing = new Mailing;
$conn = $objDb->connection($objConfig);

session_start();

global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);

$objUtility->getAction($strAct, $intId);

switch ($strAct) {

	case "MARGINALITA-ARTICOLO":
	  $articolo=$_POST['articolo'];
	  if($articolo=="") exit;
	  
	  $rs=getTable("magazzino_articoli","","");
    $tmarginalita=0;
    $tmarginalita_perc=0;
    $i=0;
    while (list($key, $row) = each($rs)) {
	    $i++;
      $marginalita=$row['Prezzo_cry']-$row['Costo_cry'];
      $marginalita_perc=round(($marginalita/$row['Prezzo_cry'])*100,2);
      
      $tmarginalita=$tmarginalita+$marginalita;
      $tmarginalita_perc=$tmarginalita_perc+$marginalita_perc;  
    }
	  $tmarginalita=round($tmarginalita/$i,2);
	  $tmarginalita_perc=round($tmarginalita_perc/$i,2);
	  
    $rs=retRow("magazzino_articoli",$articolo);
	  $marginalita=$rs['Prezzo_cry']-$rs['Costo_cry'];
    $marginalita_perc=round(($marginalita/$rs['Prezzo_cry'])*100,2);
    
    ?>
    <br><br>
    <div style="color:red;font-size:14px;font-weight:bold;padding-bottom:5px;">Marginalit&agrave; per articolo</div>
    <table cellspacing="2" border="0" summary="Moduli" class="default" style="width:96%;"><tr><th>Costo</th><th>Prezzo di vendita</th><th>Marginalit&agrave;</th><th>In percentuale sul prezzo di vendita</th><th>Su valori medi complessivi di marginalit&agrave;</th></tr>
    <tr style="color:red;font-size:14px;font-weight:bold;"><td><?=$rs['Costo_cry']?></td><td><?=$rs['Prezzo_cry']?></td><td><?=$marginalita?></td><td><?=$marginalita_perc?>%</td><td><?php echo "$tmarginalita ($tmarginalita_perc%)";?></td></tr>
    </table>
    <?
	  
    if ($errorMsg)
		{
			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
		} 
		else
		{
			$esitoMsg = "Operazione eseguita correttamente";
		}
		break;
		//**********************************************************************************************************************
		
		case "MARGINALITA-CATEGORIA":
	    $tipo=$_POST['categoria'];
      
      $rs=getTable("magazzino_articoli","","");
      $tmarginalita=0;
      $tmarginalita_perc=0;
      $i=0;
      while (list($key, $row) = each($rs)) {
        $cat=retRow("magazzino_articoli",$row['id']);
        $ramo=getRamo($cat['id_categorie_str_magazzino']);
        $ramo2=array();
        while (list($key2, $value2) = each($ramo)) {
          array_push($ramo2, $value2['id']);  
        }

        if(in_array($tipo, $ramo2)) {  
          $sql="SELECT id FROM `".$config_table_prefix."magazzino_articoli` WHERE (id='".$row['id']."' AND Prezzo_cry>0)";
          $result2=mysql_query($sql);
          if(mysql_num_rows($result2)>0) {
            $i++;
            $costo_medio=$costo_medio+$row['Costo_cry'];
            $prezzo_medio=$prezzo_medio+$row['Prezzo_cry'];
            
            $marginalita=$row['Prezzo_cry']-$row['Costo_cry'];
            $marginalita_perc=round(($marginalita/$row['Prezzo_cry'])*100,2);
            
            $tmarginalita=$tmarginalita+$marginalita;
            $tmarginalita_perc=$tmarginalita_perc+$marginalita_perc;  
          }
        }
      }
      
      if($i>0) { 
        $costo_medio=round($costo_medio/$i,2);
        $prezzo_medio=round($prezzo_medio/$i,2);
        
        $tmarginalita=round($tmarginalita/$i,2);
  	    $tmarginalita_perc=round($tmarginalita_perc/$i,2);
      }
      
      ?>
      <br><br>
      <div style="color:red;font-size:14px;font-weight:bold;padding-bottom:5px;">Marginalit&agrave; per categoria</div>
      <table cellspacing="2" border="0" summary="Moduli" class="default" style="width:96%;"><tr><th>Costo medio</th><th>Prezzo di vendita medio</th><th>Marginalit&agrave; media</th><th>In percentuale sul prezzo medio di vendita</th></tr>
      <tr style="color:red;font-size:14px;font-weight:bold;"><td><?=$costo_medio?></td><td><?=$prezzo_medio?></td><td><?=$tmarginalita?></td><td><?=$tmarginalita_perc?>%</td></tr>
      </table>
      <?
      
      if ($errorMsg)
  		{
  			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
  		} 
  		else
  		{
  			$esitoMsg = "Operazione eseguita correttamente";
  		}
  		break;
  		//**********************************************************************************************************************
		
		case "MARGINALITA-MARCHIO":
	    $tmarginalita=0;
      $tmarginalita_perc=0;
      $i=0;
      
      $tipo=$_POST['fornitore'];
      
      $rs=getTable("magazzino_articoli","","Fornitore='$tipo' AND Prezzo_cry>0");

      while (list($key, $row) = each($rs)) {
        $i++;
        $costo_medio=$costo_medio+$row['Costo_cry'];
        $prezzo_medio=$prezzo_medio+$row['Prezzo_cry'];
        
        $marginalita=$row['Prezzo_cry']-$row['Costo_cry'];
        $marginalita_perc=round(($marginalita/$row['Prezzo_cry'])*100,2);
        
        $tmarginalita=$tmarginalita+$marginalita;
        $tmarginalita_perc=$tmarginalita_perc+$marginalita_perc;
      }
      
      if($i>0) {
        $costo_medio=round($costo_medio/$i,2);
        $prezzo_medio=round($prezzo_medio/$i,2);
        
        $tmarginalita=round($tmarginalita/$i,2);
  	    $tmarginalita_perc=round($tmarginalita_perc/$i,2);
      }
      ?>
      <br><br>
      <div style="color:red;font-size:14px;font-weight:bold;padding-bottom:5px;">Marginalit&agrave; per fornitore</div>
      <table cellspacing="2" border="0" summary="Moduli" class="default" style="width:96%;"><tr><th>Costo medio</th><th>Prezzo di vendita medio</th><th>Marginalit&agrave; media</th><th>In percentuale sul prezzo medio di vendita</th></tr>
        <tr style="color:red;font-size:14px;font-weight:bold;"><td><?=$costo_medio?></td><td><?=$prezzo_medio?></td><td><?=$tmarginalita?></td><td><?=$tmarginalita_perc?>%</td></tr>
      </table>
      <?
      
      if ($errorMsg)
  		{
  			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
  		} 
  		else
  		{
  			$esitoMsg = "Operazione eseguita correttamente";
  		}
  		break;
  		//**********************************************************************************************************************
  		
}
?>