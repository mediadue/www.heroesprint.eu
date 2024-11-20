<?php
	Class Storico {
    var $g_config_table_prefix;
    var $oggetto;
		
		function Storico($oggetto) {
			global $config_table_prefix;
			
			$this->g_config_table_prefix=$config_table_prefix;
			$this->oggetto=$oggetto;
		}
		
		function add($campo,$valore,$operazione) {
        $oggetto=$this->oggetto;
        
        $id_consorziata=$_SESSION['id_consorziata'];
        
        $sql="SELECT anagrafica FROM ".$this->g_config_table_prefix."consorziata WHERE id='$id_consorziata'";
  			$query=mysql_query($sql);
  			$arr=mysql_fetch_array($query);
  			$id_anagrafica=$arr['anagrafica'];
  			
  			$sql="SELECT * FROM ".$this->g_config_table_prefix."consorziata_anagrafica WHERE id='$id_anagrafica'";
  			$query=mysql_query($sql);
  			$arr=mysql_fetch_array($query);
  			$ragionesociale=$arr['RagioneSociale'];
  			
  			$objUsers = new Users;
  			$objUsers->getCurrentUser($intIdutente, $strUsername);
  			
  			$login=$strUsername;
  			
  			$sql="SELECT * FROM ".$this->g_config_table_prefix."users WHERE id='$intIdutente'";
  			$query=mysql_query($sql);
  			$arr=mysql_fetch_array($query);
  			
  			$nome=$arr['nome'];
  			$cognome=$arr['cognome'];
  			
  			$data=date("y-m-d");
  			
  			$ora=date("h:m:s",time());
  			
  			$sql="INSERT INTO ".$this->g_config_table_prefix."storico (consorziata,login,nome,cognome,data,ora,campo,valore,oggetto,operazione) VALUES ('$ragionesociale','$login','$nome','$cognome','$data','$ora','$campo','$valore','$oggetto','$operazione') ";
				$query=mysql_query($sql);
	}
	
	function GetList() {
		$sql = "SELECT * FROM ".$this->g_config_table_prefix."storico ORDER BY data,ora ASC";	
		$query = mysql_query ($sql);
		
		$utility = new Utility;
		$rs = $utility->buildRecordset($query);
		return $rs;
	}
		
}

?>
