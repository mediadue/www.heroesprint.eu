<?php
	Class Anagrafica {
		var $g_id;
		var $g_config_table_prefix;
		
		var $objStorico;
		
		function Anagrafica($id) {
			global $config_table_prefix;
			
			$this->g_config_table_prefix=$config_table_prefix;
			$this->g_id=$id;
			$this->objStorico =& new Storico("anagrafica");
		}
		
		function Set($field, $str) {
			$sql="UPDATE ".$this->g_config_table_prefix."consorziata_anagrafica SET $field='$str' WHERE id='".$this->g_id."' ";
			$p_res=mysql_query($sql);
			$this->objStorico->add($field,$str,"modifica campo");
		}
				
		function delCurrent() {
			$sql="DELETE FROM ".$this->g_config_table_prefix."consorziata_anagrafica WHERE (id='".$this->g_id."') ";
			mysql_query($sql);
			
			$this->objStorico->add("",$this->g_id,"eliminazione scheda");
		}
		
		function Get($field) {
			$sql="SELECT $field FROM ".$this->g_config_table_prefix."consorziata_anagrafica WHERE id='".$this->g_id."'";
			$query=mysql_query($sql);
			$arr=mysql_fetch_array($query);
			return ($arr[0]);
		}
		
		function delFile($field) {
			$sql="SELECT $field FROM ".$this->g_config_table_prefix."consorziata_anagrafica WHERE id='".$this->g_id."'";
			$query=mysql_query($sql);
			$arr=mysql_fetch_array($query);
			
			$id_oggetto=$arr[0];
			
			if($id_oggetto=='0') return;
			
			$sql="SELECT * FROM ".$this->g_config_table_prefix."oggetti WHERE id='$id_oggetto'";
			$query=mysql_query($sql);
			$arr=mysql_fetch_array($query);
			
			unlink($arr['path'].$arr['nome'].".".$arr['ext']);
			
			$sql="DELETE FROM ".$this->g_config_table_prefix."oggetti WHERE id='$id_oggetto'";
			mysql_query($sql);
			
			$sql="UPDATE ".$this->g_config_table_prefix."consorziata_anagrafica SET $field=0 WHERE id='".$this->g_id."' ";
			$p_res=mysql_query($sql);
			
			$this->objStorico->add($field,"","elimina file");
		}
		
		function getFile($field) {
			$sql="SELECT $field FROM ".$this->g_config_table_prefix."consorziata_anagrafica WHERE id='".$this->g_id."'";
			$query=mysql_query($sql);
			$arr=mysql_fetch_array($query);
			
			$id_oggetto=$arr[0];
			
			$sql="SELECT * FROM ".$this->g_config_table_prefix."oggetti WHERE id='$id_oggetto'";
			$query=mysql_query($sql);
			$arr=mysql_fetch_array($query);
			
			return $arr;
		}
		
		function addFile($post,$field) {
			$this->objUtility =& new Utility;
			
			$strDestDir = $this->objUtility->getPathResourcesDynamicAbsolute();
			
			$isUploadOk = false;
			$strUnique = $this->objUtility->getFilenameUnique();
			$strDestFile = $strUnique;
			if ($_FILES[$post]["name"]) 
			{
				$strExt = $this->objUtility->getExtFromMime($_FILES[$post]["type"]);
				
				$isUploadOk = move_uploaded_file($_FILES[$post]["tmp_name"], $strDestDir.$strDestFile.".".$strExt);
				
				if ($isUploadOk)
				{
					chmod($strDestDir.$strDestFile.".".$strExt, 0644);
					$strOggettoPath = $strDestDir;
					$strOggettoExt = $strExt;
					$strOggettoOriginalname = $_FILES[$post]["name"];
					
					$sql="INSERT INTO ".$this->g_config_table_prefix."oggetti (nome,ext,path,originalname) VALUES ('$strDestFile','$strOggettoExt','$strOggettoPath','$strOggettoOriginalname') ";
					$query=mysql_query($sql);
					$id_oggetto=mysql_insert_id();
					
					$this->Set($field,$id_oggetto);
					$this->objStorico->add($field,"","aggiungi file");
				}
			}		
    }
	}

?>
