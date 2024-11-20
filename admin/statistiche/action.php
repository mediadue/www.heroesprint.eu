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

function eliminafiles($dirname){
	if(file_exists($dirname) && is_file($dirname)) {
		unlink($dirname);
	} elseif (is_dir($dirname)) {
		$handle = opendir($dirname);
		while (false !== ($file = readdir($handle))) { 
			if(is_file($dirname.$file)){
				unlink($dirname.$file);
			}
		}
		$handle = closedir($handle);
		rmdir($dirname);
	}
	mkdir($dirname);
}

session_start();

global $config_table_prefix;

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objUtility->getAction($strAct, $intId);

switch ($strAct) 
{
	case "GEN-GRAPH-NUM-SCONTRINI":
		$action = strtolower($objUtility->sessionVarRead("action"));

    //******GENERO IL GRAFICO
		include $objUtility->getPathBackofficeAdminAbsolute()."libchart/classes/libchart.php";
		eliminafiles("generated/");
    $f=$objUtility->getFilenameUnique().".png";
		
		$key="data";
		
		$anno="anno".$key;
    $mese="mese".$key;
    $giorno="giorno".$key;
    
    $anno2="anno".$key."_a";
    $mese2="mese".$key."_a";
    $giorno2="giorno".$key."_a";
    
    $anno=$_POST[$anno];
    $mese=$_POST[$mese];
    $giorno=$_POST[$giorno];
    
    $anno2=$_POST[$anno2];
    $mese2=$_POST[$mese2];
    $giorno2=$_POST[$giorno2];
		
		if( ($anno=="0000" && $mese=="00") || ($anno2=="0000" && $mese2=="00") ) exit;
		
	  $chart = new PieChart(900, 550);
  	$dataSet = new XYDataSet();

    while(true) {
		  $sql="SELECT id FROM `".$config_table_prefix."acquisti` WHERE ";
		  if($anno!="0000") $sql.="YEAR(data)='".$anno."'";
      if($mese!="00") $sql.=" AND MONTH(data)='".$mese."'";
      
      $sql=str_replace("WHERE  AND", "WHERE", $sql);
      
      $sql=$sql." GROUP BY codice_vendita";
      
      $res=mysql_query($sql);
      $n=mysql_num_rows($res);
      
      //echo $sql.$n."<br>";
      
      if($mese!="00" && $anno!="0000") {
        $dataSet->addPoint(new Point($mese."/".$anno." ($n scontrini)", $n));
        $mese=$mese+1;
        if($anno>=$anno2 && $mese>$mese2) break;
        if($mese>12) {
          $mese="1";
          $anno=$anno+1;
        }
      }
      
      if($mese=="00" && $anno!="0000") {
        $dataSet->addPoint(new Point($anno." ($n scontrini)", $n));
        if($anno>=$anno2) break;
        $anno=$anno+1;
      }
      
      if($mese!="00" && $anno=="0000" && $mese<=$mese2) {
        $dataSet->addPoint(new Point($mese." ($n scontrini)", $n));
        if($mese>=$mese2) break;
        $mese=$mese+1;
      }
		}

		$chart->setDataSet($dataSet);
		
		$chart->setTitle("Scontrini emessi per periodo");
	  $chart->render("generated/".$f);
	  
    ?><center><img src="generated/<?=$f?>" style="border:1px gray dashed;margin-top:20px;"></center><?
		
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
		
		case "GEN-TEMPI-RITORNO":
		$action = strtolower($objUtility->sessionVarRead("action"));
		$key="data";
		
		$anno="anno".$key;
    $mese="mese".$key;
    $giorno="giorno".$key;
    
    $anno2="anno".$key."_a";
    $mese2="mese".$key."_a";
    $giorno2="giorno".$key."_a";
    
    $anno=$_POST[$anno];
    $mese=$_POST[$mese];
    $giorno=$_POST[$giorno];
    
    $anno2=$_POST[$anno2];
    $mese2=$_POST[$mese2];
    $giorno2=$_POST[$giorno2];
		
		if( ($anno=="0000" && $mese=="00") || ($anno2=="0000" && $mese2=="00") ) exit;

    $sql="SELECT DISTINCT(user_hidden) FROM `".$config_table_prefix."acquisti` WHERE ";
	  if($anno!="0000") $sql.="YEAR(data)>='".$anno."'";
    if($mese!="00") $sql.=" AND MONTH(data)>='".$mese."'";
    if($anno2!="0000") $sql.=" AND YEAR(data)<='".$anno2."'";
    if($mese2!="00") $sql.=" AND MONTH(data)<='".$mese2."'";
    
    $sql=str_replace("WHERE  AND", "WHERE", $sql);

    $res=mysql_query($sql);
    $idusers_acquisti=array();
    while($row = mysql_fetch_array($res)) {
	    array_push($idusers_acquisti, $row['user_hidden']); 
	  }
	  
	  $users=getTable("users","","");
    $i=0;
    ?><table cellspacing="2" border="0" summary="Moduli" class="default" style="width:96%;"><tr><th>Cliente</th><th>Data ultimo acquisto</th></tr><?
    $_SESSION[$config_table_prefix."userscheckSel"]=array();
    while (list($key, $row) = each($users)) {
      if(!in_array($row['id'], $idusers_acquisti)) {
        if($row['ragionesociale']!=""){
          array_push($_SESSION[$config_table_prefix."userscheckSel"], $row['id']);
          $i++;
          $ultimo_acquisto=getTable("acquisti","data DESC","user_hidden='".$row['id']."'");
          $ultimo_acquisto=$ultimo_acquisto[0];
          ?><tr><td><?php echo $row['ragionesociale']; ?></td><td><?=dataITA($ultimo_acquisto['data']); ?></td></tr><?
        }
      }
	  }
    
    ?></table><br><br><div style="color:red;font-size:14px;font-weight:bold;">Per un totale di <?=$i?> clienti che non hanno effettuato acquisti nel periodo selezionato.</div><?
    
    
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
		
		
		case "GEN-GRAPH-CLIENTI-ACQUISTANO":
  		$action = strtolower($objUtility->sessionVarRead("action"));
    
      //******GENERO IL GRAFICO
  		include $objUtility->getPathBackofficeAdminAbsolute()."libchart/classes/libchart.php";
  		eliminafiles("generated/");
      $f=$objUtility->getFilenameUnique().".png";
      
  		$tipo=$_POST['fornitore'];
  
  	  $chart = new HorizontalBarChart(500, 1200);
    	$dataSet = new XYDataSet();
      
      if($tipo=="") {
        $rs=getTable("acquisti","","");
        $totale=count($rs);
        
        $sql="SELECT id_magazzino_articoli,COUNT(id_magazzino_articoli) AS quanti FROM `".$config_table_prefix."acquisti` GROUP BY id_magazzino_articoli";
        $result=mysql_query($sql);
        $acquisti=array();
        while($row = mysql_fetch_array($result)) {
          $sql="SELECT Fornitore FROM `".$config_table_prefix."magazzino_articoli` WHERE id='".$row['id_magazzino_articoli']."'";
          $result2=mysql_query($sql);
          $row2=mysql_fetch_array($result2);
          $fornitore=$row2['Fornitore'];
          $quanti=$row['quanti'];
          $acquisti[$fornitore]=$acquisti[$fornitore]+$quanti;
        }
       
        array_multisort($acquisti);
        while (list($key, $value) = each($acquisti)) {
         $dataSet->addPoint(new Point($key." ($value)", $value)); 
        } 
      }else{
        $chart = new PieChart(900, 550);
        $rs=getTable("acquisti","","");
        $totale=count($rs);
        
        $sql="SELECT user_hidden,id_magazzino_articoli,COUNT(id_magazzino_articoli) AS quanti FROM `".$config_table_prefix."acquisti` GROUP BY id_magazzino_articoli";
        $result=mysql_query($sql);
        $acquisti=array();
        while($row = mysql_fetch_array($result)) {
          $sql="SELECT id FROM `".$config_table_prefix."magazzino_articoli` WHERE (id='".$row['id_magazzino_articoli']."' AND Fornitore='$tipo')";
          $result2=mysql_query($sql);
          if(mysql_num_rows($result2)>0) {
             $cliente="r".$row['user_hidden'];
             $quanti=$row['quanti'];
             $acquisti[$cliente]=$acquisti[$cliente]+$quanti;  
          }
        }
       
        array_multisort($acquisti);
        $_SESSION[$config_table_prefix."userscheckSel"]=array();
        while (list($key, $value) = each($acquisti)) {
         $key=str_replace("r", "", $key);
         $rr=retRow("users",$key);
         $dataSet->addPoint(new Point($rr['ragionesociale']." ($value)", $value));
         array_push($_SESSION[$config_table_prefix."userscheckSel"], $key); 
        }   
      }
      
  		$chart->setDataSet($dataSet);
  		
  		$chart->setTitle("Vendite per marchio");
      $chart->render("generated/".$f);
  	  
      ?><center><img src="generated/<?=$f?>" style="border:1px gray dashed;margin-top:20px;"></center><?
  		
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
		
		case "GEN-GRAPH-CLIENTI-ACQUISTANO-CAT":
  		$action = strtolower($objUtility->sessionVarRead("action"));
    
      //******GENERO IL GRAFICO
  		include $objUtility->getPathBackofficeAdminAbsolute()."libchart/classes/libchart.php";
  		eliminafiles("generated/");
      $f=$objUtility->getFilenameUnique().".png";
      
  		$tipo=$_POST['categoria'];
  
  	  $chart = new HorizontalBarChart(500, 1200);
    	$dataSet = new XYDataSet();
      
      if($tipo=="") {
        $sql="SELECT COUNT(id) AS q FROM `".$config_table_prefix."acquisti`";
        $query=mysql_query($sql);
        $rs=mysql_fetch_array($query);
        $totale=$rs['q'];
        
        $sql="SELECT id_magazzino_articoli,COUNT(id_magazzino_articoli) AS quanti FROM `".$config_table_prefix."acquisti` GROUP BY id_magazzino_articoli";
        $result=mysql_query($sql);
        $acquisti=array();
        while($row = mysql_fetch_array($result)) {
          $sql="SELECT id_categorie_str_magazzino FROM `".$config_table_prefix."magazzino_articoli` WHERE id='".$row['id_magazzino_articoli']."'";
          $result2=mysql_query($sql);
          $row2=mysql_fetch_array($result2);
          $cat=$row2['id_categorie_str_magazzino'];
          $quanti=$row['quanti'];
          $acquisti[$cat]=$acquisti[$cat]+$quanti;
        }
       
        $acquisti2=array();
        while (list($key, $value) = each($acquisti)) {
         $cat3=getPadre($key);
         $cat2=getPadre($cat3['id']);
         $cat1=getPadre($cat2['id']);
         
         $cat3=retRow("categorie",$cat3['id']);
         $cat2=retRow("categorie",$cat2['id']);
         $cat1=retRow("categorie",$cat1['id']);
         
         $cat3=$cat3['nome'];
         $cat2=$cat2['nome'];
         $cat1=$cat1['nome'];
         
         $acquisti2[$cat1]=$acquisti2[$cat1]+$value;
         $acquisti2[$cat2]=$acquisti2[$cat2]+$value;
         $acquisti2[$cat3]=$acquisti2[$cat3]+$value; 
        }
          
        array_multisort($acquisti2);
        while (list($key, $value) = each($acquisti2)) {
         $dataSet->addPoint(new Point($key." (".round(($value/$totale)*100, 2)."%)", $value)); 
        } 
      }else{
        $chart = new PieChart(900, 550);
        
        $figli=getStruttura("magazzino",$tipo);
        array_unshift($figli, $tipo);
        $strWh="";
        while (list($key, $value) = each($figli)) {
          $strWh=$strWh."id_categorie_str_magazzino='".$value."' OR ";
        } 
        $strWh=substr($strWh, 0, strlen($strWh)-3);
        $magazzino_articoli=getTable("magazzino_articoli","",$strWh);
        
        $strWh="";
        while (list($key, $value) = each($magazzino_articoli)) {
          $strWh=$strWh."id_magazzino_articoli='".$value['id']."' OR ";
        }
        $strWh=substr($strWh, 0, strlen($strWh)-3);
        $strWhMG=$strWh;
        $acquisti=getTable("acquisti","",$strWh);
        
        $strWh="";
        while (list($key, $value) = each($acquisti)) {
          $strWh=$strWh."id='".$value['user_hidden']."' OR ";
        }
        $strWh=substr($strWh, 0, strlen($strWh)-3); 
        $users=getTable("users","",$strWh);
        
        while (list($key, $value) = each($users)) {
          $sql="SELECT COUNT(id) AS q FROM `".$config_table_prefix."acquisti` WHERE user_hidden='".$value['id']."' AND ($strWhMG)";
          $query=mysql_query($sql);
          $rs=mysql_fetch_array($query);
          
          $dataSet->addPoint(new Point($value['ragionesociale']." (".$rs['q']." articoli)", $rs['q'])); 
          array_push($_SESSION[$config_table_prefix."userscheckSel"], $value['id']);
        } 
      }
      
  		$chart->setDataSet($dataSet);
  		
  		$chart->setTitle("Vendite per categoria");
      $chart->render("generated/".$f);
  	  
      ?><center><img src="generated/<?=$f?>" style="border:1px gray dashed;margin-top:20px;"></center><?
  		
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
		
		
		case "GEN-GRAPH-FREQ-ACQ":
  		$action = strtolower($objUtility->sessionVarRead("action"));
  
      //******GENERO IL GRAFICO
  		include $objUtility->getPathBackofficeAdminAbsolute()."libchart/classes/libchart.php";
  		eliminafiles("generated/");
      $f=$objUtility->getFilenameUnique().".png";

      $column="data";
  		$users=$_POST['users'];

  	  $chart = new PieChart(900, 550);
    	$dataSet = new XYDataSet();
      
      if(!is_array($users)) break;
      
      while (list($key, $id) = each($users)) {
        $anno="anno".$column;
        $mese="mese".$column;
        $giorno="giorno".$column;
        
        $anno2="anno".$column."_a";
        $mese2="mese".$column."_a";
        $giorno2="giorno".$column."_a";
        
        $anno=$_POST[$anno];
        $mese=$_POST[$mese];
        $giorno=$_POST[$giorno];
        
        $anno2=$_POST[$anno2];
        $mese2=$_POST[$mese2];
        $giorno2=$_POST[$giorno2];
        
        if( ($anno=="0000" && $mese=="00") || ($anno2=="0000" && $mese2=="00") ) exit;
      
        while(true) {
          if($mese!="00" && $anno!="0000") {
            $sql2="SELECT COUNT(id) AS NumRows FROM `".$config_table_prefix."acquisti` WHERE (user_hidden='$id' AND YEAR(data)='".$anno."' AND MONTH(data)='".$mese."')";    
            $res2=mysql_query($sql2);
            $r=mysql_fetch_array($res2);
            $udata=retRow("users",$id);
            if($r['NumRows']>0) $dataSet->addPoint(new Point($udata['ragionesociale']." nel periodo $mese/$anno (".$r['NumRows']." articoli venduti)", $r['NumRows']));

            $mese=$mese+1;
            if($anno>=$anno2 && $mese>$mese2) break;
            if($mese>12) {
              $mese="1";
              $anno=$anno+1;
            }
          }
          
          if($mese=="00" && $anno!="0000") {
            $sql2="SELECT COUNT(id) AS NumRows FROM `".$config_table_prefix."acquisti` WHERE (user_hidden='$id' AND YEAR(data)='".$anno."')";    
            $res2=mysql_query($sql2);
            $r=mysql_fetch_array($res2);
            $udata=retRow("users",$id);
            if($r['NumRows']>0) $dataSet->addPoint(new Point($udata['ragionesociale']." nell'anno $anno (".$r['NumRows']." articoli venduti)", $r['NumRows']));
            if($anno>=$anno2) break;
            $anno=$anno+1;
          }
          
          if($mese!="00" && $anno=="0000" && $mese<=$mese2) {
            $sql2="SELECT COUNT(id) AS NumRows FROM `".$config_table_prefix."acquisti` WHERE (user_hidden='$id' AND MONTH(data)='".$mese."')";    
            $res2=mysql_query($sql2);
            $r=mysql_fetch_array($res2);
            $udata=retRow("users",$id);
            if($r['NumRows']>0) $dataSet->addPoint(new Point($udata['ragionesociale']." nel mese $mese (".$r['NumRows']." articoli venduti)", $r['NumRows']));

            if($mese>=$mese2) break;
            $mese=$mese+1;
          }
    		}
  		}

  		$chart->setDataSet($dataSet);
  		
  		$chart->setTitle("Frequenza vendite");
  	  $chart->render("generated/".$f);
  	  
      ?><center><img src="generated/<?=$f?>" style="border:1px gray dashed;margin-top:20px;"></center><?
  		
      if ($errorMsg)
  		{
  			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
  		} 
  		else
  		{
  			$esitoMsg = "Operazione eseguita correttamente";
  		}
  		break;
		//********************************************************************************************************************
		
		case "BTN-CLICK-UTENTI-MAIL":
      users_utentiEmail();
		  break;
		
    //********************************************************************************************************************
		case "GEN-GRAPH-SPESA-ENTRATA":
  		$action = strtolower($objUtility->sessionVarRead("action"));
  
      //******GENERO IL GRAFICO
  		include $objUtility->getPathBackofficeAdminAbsolute()."libchart/classes/libchart.php";
  		eliminafiles("generated/");
      $f=$objUtility->getFilenameUnique().".png";
      
  		$tipo=$_POST['tipo'];

  	  $chart = new HorizontalBarChart(900,15000);
    	$dataSet = new XYDataSet();
      
      $sql="SELECT user_hidden,SUM(prezzo_scontato_cry) AS spesa FROM `".$config_table_prefix."acquisti` WHERE (user_hidden<>'') GROUP BY user_hidden ORDER BY spesa DESC";
      $result = mysql_query($sql);
      $_SESSION[$config_table_prefix."userscheckSel"]=array();
      $myres=$objUtility->buildRecordset($result);
      $myres=array_reverse($myres); 
      while (list($key, $row) = each($myres)) {
        $prezzo=$row['spesa'];
        if( $tipo=="0" || ($tipo=="1" && $prezzo<51) || ($tipo=="2" && $prezzo>50 && $prezzo<151) || ($tipo=="3" && $prezzo>150 && $prezzo<501) || ($tipo=="4" && $prezzo>500) ) {
          $user=retRow("users",$row['user_hidden']);
          $dataSet->addPoint(new Point($user['ragionesociale'], $row['spesa'])); 
          array_push($_SESSION[$config_table_prefix."userscheckSel"], $user['id']);
        } 
      }
      
  		$chart->setDataSet($dataSet);
  		
  		$chart->setTitle("Spesa media per cliente (valori in euro)");
      $chart->render("generated/".$f);
  	  
      ?><center><img src="generated/<?=$f?>" style="border:1px gray dashed;margin-top:20px;"></center><?
  		
      if ($errorMsg)
  		{
  			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
  		} 
  		else
  		{
  			$esitoMsg = "Operazione eseguita correttamente";
  		}
  		break;
		//********************************************************************************************************************
		
		case "GEN-GRAPH-TASSO-FIDELIZZAZIONE":
  		$action = strtolower($objUtility->sessionVarRead("action"));
  
      //******GENERO IL GRAFICO
  		include $objUtility->getPathBackofficeAdminAbsolute()."libchart/classes/libchart.php";
  		eliminafiles("generated/");
      $f=$objUtility->getFilenameUnique().".png";

  	  $key="data";
  		
  		$anno="anno".$key;
      $mese="mese".$key;
      $giorno="giorno".$key;
      
      $anno2="anno".$key."_a";
      $mese2="mese".$key."_a";
      $giorno2="giorno".$key."_a";
      
      $anno=$_POST[$anno];
      $mese=$_POST[$mese];
      $giorno=$_POST[$giorno];
      
      $anno2=$_POST[$anno2];
      $mese2=$_POST[$mese2];
      $giorno2=$_POST[$giorno2];
  		
  		if( ($anno=="0000" && $mese=="00") || ($anno2=="0000" && $mese2=="00") ) exit;
  		
  	  $chart = new LineChart(900, 550);
    	$dataSet = new XYDataSet();
    	
      while(true) {
        $sql="SELECT COUNT(id) AS numUsers FROM `".$config_table_prefix."acquisti` WHERE user_hidden<>'' ";
        if($anno!="0000") $sql.="AND YEAR(data)='".$anno."'";
        if($mese!="00") $sql.=" AND MONTH(data)='".$mese."'";
        
        $sql=str_replace("WHERE  AND", "WHERE", $sql);
        $sql.=" GROUP BY user_hidden";
        
        $result = mysql_query($sql);
        $numUsers=0;
        while($row = mysql_fetch_array($result)) {
          $numUsers++;
        }
        
        $sql="SELECT COUNT(id) AS ritorni FROM `".$config_table_prefix."acquisti` WHERE user_hidden<>'' ";
        if($anno!="0000") $sql.="AND YEAR(data)='".$anno."'";
        if($mese!="00") $sql.=" AND MONTH(data)='".$mese."'";
        $sql=str_replace("WHERE  AND", "WHERE", $sql);
        $sql.=" GROUP BY codice_vendita";
        
        $result = mysql_query($sql);
        $ritorni=0;
        while($row = mysql_fetch_array($result)) {
          $ritorni++;  
        }
        $ritorni=$ritorni-$numUsers;
        
        $n=0;
        if($ritorni>0) $n=($numUsers/$ritorni)*100;
          
        if($mese!="00" && $anno!="0000") {
          $dataSet->addPoint(new Point($mese."/".$anno, $n));
          $mese=$mese+1;
          if($anno>=$anno2 && $mese>$mese2) break;
          if($mese>12) {
            $mese="1";
            $anno=$anno+1;
          }
        }
        
        if($mese=="00" && $anno!="0000") {
          $dataSet->addPoint(new Point($anno, $n));
          if($anno>=$anno2) break;
          $anno=$anno+1;
        }
        
        if($mese!="00" && $anno=="0000" && $mese<=$mese2) {
          $dataSet->addPoint(new Point($mese, $n));
          if($mese>=$mese2) break;
          $mese=$mese+1;
        }
  		}
  
  		$chart->setDataSet($dataSet);
  		
  		$chart->setTitle("Tasso di fidelizzazione");
      $chart->render("generated/".$f);
  	  
      ?><center><img src="generated/<?=$f?>" style="border:1px gray dashed;margin-top:20px;"></center><?
  		
      if ($errorMsg)
  		{
  			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
  		} 
  		else
  		{
  			$esitoMsg = "Operazione eseguita correttamente";
  		}
  		break;
		//********************************************************************************************************************
		
		
		case "GEN-GRAPH-CLIENTI-MIGLIORI":
  		$action = strtolower($objUtility->sessionVarRead("action"));
  
      //******GENERO IL GRAFICO
  		include $objUtility->getPathBackofficeAdminAbsolute()."libchart/classes/libchart.php";
  		eliminafiles("generated/");
      $f=$objUtility->getFilenameUnique().".png";

  	  $key="data";
  		
  		$anno="anno".$key;
      $mese="mese".$key;
      $giorno="giorno".$key;
      
      $anno2="anno".$key."_a";
      $mese2="mese".$key."_a";
      $giorno2="giorno".$key."_a";
      
      $anno=$_POST[$anno];
      $mese=$_POST[$mese];
      $giorno=$_POST[$giorno];
      
      $anno2=$_POST[$anno2];
      $mese2=$_POST[$mese2];
      $giorno2=$_POST[$giorno2];
  		
  		if( ($anno=="0000" && $mese=="00") || ($anno2=="0000" && $mese2=="00") ) exit;
  		
  	  $chart = new HorizontalBarChart(900, 15000);
    	$dataSet = new XYDataSet();

      $sql="SELECT user_hidden,SUM(prezzo_scontato_cry) AS spesa FROM `".$config_table_prefix."acquisti` WHERE (user_hidden<>'' AND data>='$anno-$mese-$giorno' AND data<='$anno2-$mese2-$giorno2') GROUP BY user_hidden ORDER BY spesa DESC";
      
      $result = mysql_query($sql);
      $_SESSION[$config_table_prefix."userscheckSel"]=array();
      $myres=$objUtility->buildRecordset($result);
      $myres=array_reverse($myres); 
      while (list($key, $row) = each($myres)) {
        $user=retRow("users",$row['user_hidden']);
        $dataSet->addPoint(new Point($user['ragionesociale'], $row['spesa'])); 
        array_push($_SESSION[$config_table_prefix."userscheckSel"], $user['id']); 
      }
      
      $chart->setDataSet($dataSet);
  		$chart->setTitle("Clienti migliori");
      $chart->render("generated/".$f);
  	  
      ?><center><img src="generated/<?=$f?>" style="border:1px gray dashed;margin-top:20px;"></center><?
  		
      if ($errorMsg)
  		{
  			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
  		} 
  		else
  		{
  			$esitoMsg = "Operazione eseguita correttamente";
  		}
  		break;
		//********************************************************************************************************************
		
		
		case "GEN-GRAPH-IMP-MED-SCONTRINO":
  		$action = strtolower($objUtility->sessionVarRead("action"));
  
      //******GENERO IL GRAFICO
  		include $objUtility->getPathBackofficeAdminAbsolute()."libchart/classes/libchart.php";
  		eliminafiles("generated/");
      $f=$objUtility->getFilenameUnique().".png";
  		
  		$key="data";
  		
  		$anno="anno".$key;
      $mese="mese".$key;
      $giorno="giorno".$key;
      
      $anno2="anno".$key."_a";
      $mese2="mese".$key."_a";
      $giorno2="giorno".$key."_a";
      
      $anno=$_POST[$anno];
      $mese=$_POST[$mese];
      $giorno=$_POST[$giorno];
      
      $anno2=$_POST[$anno2];
      $mese2=$_POST[$mese2];
      $giorno2=$_POST[$giorno2];
  		
  		if( ($anno=="0000" && $mese=="00") || ($anno2=="0000" && $mese2=="00") ) exit;
  		
  	  $chart = new VerticalBarChart(900, 550);
    	$dataSet = new XYDataSet();
    	
      while(true) {
  		  $sql="SELECT COUNT(id) AS NumRows,SUM(prezzo_scontato_cry) AS totale FROM `".$config_table_prefix."acquisti` WHERE ";
  		  if($anno!="0000") $sql.="YEAR(data)='".$anno."'";
        if($mese!="00") $sql.=" AND MONTH(data)='".$mese."'";
        
        $sql=str_replace("WHERE  AND", "WHERE", $sql);
        
        $sql=$sql." GROUP BY codice_vendita";
        
        $res=mysql_query($sql);
        $tot=0;
        $c=0;
        while ($n=mysql_fetch_array($res)) {
          $c++;
          $tot=$tot+$n['totale'];  
        }
        if($c==0) $c=1;
        $n=round($tot/$c,2);
        
        //echo $sql.$n."<br>";
        
        if($mese!="00" && $anno!="0000") {
          $dataSet->addPoint(new Point($mese."/".$anno, $n));
          $mese=$mese+1;
          if($anno>=$anno2 && $mese>$mese2) break;
          if($mese>12) {
            $mese="1";
            $anno=$anno+1;
          }
        }
        
        if($mese=="00" && $anno!="0000") {
          $dataSet->addPoint(new Point($anno, $n));
          if($anno>=$anno2) break;
          $anno=$anno+1;
        }
        
        if($mese!="00" && $anno=="0000" && $mese<=$mese2) {
          $dataSet->addPoint(new Point($mese, $n));
          if($mese>=$mese2) break;
          $mese=$mese+1;
        }
  		}
  
  		$chart->setDataSet($dataSet);
  		
  		$chart->setTitle("Importo medio scontrini per periodo");
  	  $chart->render("generated/".$f);
  	  
      ?><center><img src="generated/<?=$f?>" style="border:1px gray dashed;margin-top:20px;"></center><?
  		
      if ($errorMsg)
  		{
  			$esitoMsg = "<br>Attenzione, si sono verificati dei problemi durante l'esecuzione dell'operazione richiesta<br/>";
  		} 
  		else
  		{
  			$esitoMsg = "Operazione eseguita correttamente";
  		}
  		break;
}
?>