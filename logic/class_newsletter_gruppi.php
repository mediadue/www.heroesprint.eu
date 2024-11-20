<?php
	Class NewsletterGruppi {
		var $g_config_table_prefix;
		var $id_current;
		
		function NewsletterGruppi () {
			global $config_table_prefix;
			$this->id_current=0;
			$this->g_config_table_prefix=$config_table_prefix;
		}
		
		function retCount() {
			$sql="SELECT id FROM ".$this->g_config_table_prefix."roles ";
			$query=mysql_query($sql);
			$c=mysql_num_rows($query);
			return ($c);
		}
		
		function setCurrent($index) {
			$sql="SELECT id FROM ".$this->g_config_table_prefix."roles ";
			$query=mysql_query($sql);
			
			$j=0;
			while($res=mysql_fetch_array($query)) {
				if($j==$index) {
					$this->id_current=$res['id'];
				}
				$j++; 
			}
		}
		
		function setCurrentByID($id) {
			$sql="SELECT id FROM ".$this->g_config_table_prefix."roles WHERE (id='$id' ) ";
			$query=mysql_query($sql);
			
			if(mysql_num_rows($query)>0) {
				$this->id_current=$id;
			}
		}
		
		function getCurrentID() {
			return $this->id_current;
		}
		
		function addUser($id_user) {
			$sql="SELECT id FROM ".$this->g_config_table_prefix."users WHERE (id='$id_user' ) ";
			$query=mysql_query($sql);
			
			if(mysql_num_rows($query)>0) {
        $sql="SELECT id FROM `".$this->g_config_table_prefix."roles_users_nm` WHERE (idusers='$id_user' AND  idroles='".$this->id_current."') ";
			  $query=mysql_query($sql);
			  
			  if(mysql_num_rows($query)==0) {
			     $sql="INSERT INTO `".$this->g_config_table_prefix."roles_users_nm` (idusers,idroles) VALUES ('$id_user','".$this->id_current."') ";
      		 $query=mysql_query($sql);
        }
			}
		}
		
		function delUser($id_user) {
			$sql="DELETE FROM `".$this->g_config_table_prefix."roles_users_nm` WHERE (idroles='".$this->id_current."' AND idusers='$id_user' ) ";
			mysql_query($sql);
		}
		
		function getUserList() {
			$sql="SELECT idusers FROM `".$this->g_config_table_prefix."roles_users_nm` WHERE (idroles='".$this->id_current."' ) ";
			$query=mysql_query($sql);
			
			$utility = new Utility;
			$rs = $utility->buildRecordset($query);
			return $rs;
		}
    
		function Set($field, $str) {
			$sql="UPDATE ".$this->g_config_table_prefix."roles SET $field='$str' WHERE id='".$this->id_current."' ";
			$p_res=mysql_query($sql);
		}
				
		function Get($field) {
			$sql="SELECT $field FROM ".$this->g_config_table_prefix."roles WHERE id='".$this->id_current."'";
			$query=mysql_query($sql);
			$arr=mysql_fetch_array($query);
			return ($arr[0]);
		}
		
		function delCurrent () {
			$sql="DELETE FROM `".$this->g_config_table_prefix."roles_users_nm` WHERE (idroles='".$this->id_current."') ";
			mysql_query($sql);
			
			$sql="DELETE FROM ".$this->g_config_table_prefix."roles WHERE id='".$this->id_current."' ";
			mysql_query($sql);
			
      $this->id_current=0;
		}
		
		function delAll () {
			$sql="TRUNCATE TABLE `".$this->g_config_table_prefix."roles_users_nm` WHERE (id_consorziata='".$this->id_consorziata."') ";
			mysql_query($sql);
			
			$sql="TRUNCATE TABLE ".$this->g_config_table_prefix."roles ";
			mysql_query($sql);
			
			$this->id_current=0;
		}
		
		function createNew() {
			//
			$sql="INSERT INTO ".$this->g_config_table_prefix."roles (nome) VALUES ('') ";
			$query=mysql_query($sql);
			$this->id_current=mysql_insert_id(); 
		}
		
		function getSearch($nome="",$cognome="",$regione="",$comune="",$email="",$datadinascita="") {
			$sql = "SELECT id FROM ".$this->g_config_table_prefix."roles ";	
			$query = mysql_query ($sql);
			$j=0;
			while($res=mysql_fetch_array($query)) {
				$ok1=false;
				$ok2=true;

				$sql2 = "SELECT * FROM ".$this->g_config_table_prefix."roles WHERE (id='".$res['id']."'".$this->sqlANDtxt("Nome",$nome).$this->sqlANDtxt("Cognome",$cognome).$this->sqlANDtxt("Regione",$regione).$this->sqlANDtxt("Comune",$comune).$this->sqlANDtxt("email",$email).$this->sqlANDtxt("DataDiNascita",$datadinascita).") ";	
				$query2 = mysql_query ($sql2);
				
				if($res2=mysql_fetch_array($query2)) {
					$ok1=true;
				}	
				
				if($ok1==true && $ok2==true) {
					$arr[$j]=$res2;
					$j++;
				}	
			}
			return $arr;		
		}
		
		function retGruppi() {
      $sql="SELECT idroles FROM `".$this->g_config_table_prefix."roles_users_nm` WHERE (idroles='".$this->id_current."') ";
      $query=mysql_query($sql);
			
			$utility = new Utility;
			$rs = $utility->buildRecordset($query);
			return $rs;
    }
		
		function sqlAND($field,$str) {
			if($str!="") {
				$ret = " AND $field<>0";
				return $ret;
			} else {
				return "";
			}
		}
		
		function sqlANDdatamaggiore($field,$str) {
			if($str!="") {
				$cdate=date("Y-m-d");
				$ret = " AND $field>'$cdate'";
				return $ret;
			} else {
				return "";
			}
		}
		
		function sqlANDdataminore($field,$str) {
			if($str!="") {
				$cdate=date("Y-m-d");
				$ret = " AND $field<'$cdate'";
				return $ret;
			} else {
				return "";
			}
		}
		
		function sqlANDtxt($field,$str) {
			if($str!="") {
				$ret = " AND $field='$str'";
				return $ret;
			} else {
				return "";
			}
		}
		
		function GetList() {
			$sql = "SELECT * FROM ".$this->g_config_table_prefix."roles ";	
			$query = mysql_query ($sql);
			
			$utility = new Utility;
			$rs = $utility->buildRecordset($query);
			return $rs;
		}
		
		function checked($id_utente) {
			$sql="SELECT idusers FROM `".$this->g_config_table_prefix."roles_users_nm` WHERE (idroles='".$this->id_current."' AND idusers='$id_utente')";
			$query=mysql_query($sql);
			
      if(mysql_num_rows($query)>0) {
			   return "checked";
			} else {
          return "";
      }
		}
	}

?>
<?php //#rs-enc-module123;# ?>