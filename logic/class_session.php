<?php
/**
 * Database stored session class
 * 
 * @author Ben Phelps
 * @version 1.0
 * @copyright Ben Phelps - BenPhelps.me, 30 June, 2010
 * @package Session
 **/

/**
 * Session Class
 * Store session data in database, create, set, remove, destroy
 * @package Session
 * @author Ben Phelps
 **/
class Session {
	
 	var $session_id="";
 	var $browser="";
 	var $ip="";
 	var $id_users=0;
  var $config_table_prefix=""; 
	
	function Session($init="") {
    global $config_table_prefix;
    $this->config_table_prefix=$config_table_prefix;
    
    if($init!="") {
      $tarr=explode("#_#", $init);
      $this->session_id=$tarr[0];
      $this->browser=$tarr[1];
      $this->ip=$tarr[2];
      $this->id_users=$tarr[3];
    }else{
      $this->session_id=session_id();
      $this->browser=mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);
      $this->ip=$_SERVER['REMOTE_ADDR'];
      $this->id_users=$_SESSION["userris_id"];
      if($this->id_users=="") $this->id_users=0;  
    }
    
    $this->clear();  
  }
	
	function retPost() {
    return $this->session_id."#_#".$this->browser."#_#".$this->ip."#_#".$this->id_users;
  }
	
  function clear() {
    $key = $this->session_id;  
    $browser = $this->browser;
    $ip = $this->ip;
    $id_users=$this->id_users;
    
    $sql="DELETE FROM `".$this->config_table_prefix."session` WHERE ((CURRENT_TIMESTAMP-data_ora)>5184000 AND id_users>'0')";
    mysql_query($sql);
    
    $sql="DELETE FROM `".$this->config_table_prefix."session` WHERE ((CURRENT_TIMESTAMP-data_ora)>3600 AND id_users='0')";
    mysql_query($sql);
  }
	
	function restoreByUserID($id_users) {
    $rs=getTable("session","id DESC","(`id_users`='{$id_users}')");
    if(count($rs)==0) return false;
    
    $result = unserialize($rs[0]['data']);
    $this->id_users=$id_users;
    $_SESSION = $result; 
  }
  
  function retByUserID($id_users){
    $rs=getTable("session","id DESC","(`id_users`='{$id_users}')");
    if(count($rs)==0) return false;
    
    $result = unserialize($rs[0]['data']);
    $this->id_users=$id_users;
    return $result;   
  }
	
	function create() {
      // create a unique id, this is kinda overkill but it will be unique
			//$key = sha1(uniqid(sha1(uniqid(null, true)), true));
			//if($this->session_temp=="") $this->session_temp=session_id();
      $key = $this->session_id;  
      $browser = $this->browser;
      $ip = $this->ip;
      $id_users=$this->id_users;
			
			$rs=getTable("session","","(`key`='{$key}' AND `browser`='{$browser}' AND `ip`='{$ip}' AND `id_users` = '{$id_users}')");
			if(count($rs)>0) return false;
			
			// create an empty seesion and serialize it
			$session = serialize(array());
      	
			// build the sql
			$sql = "INSERT INTO `".$this->config_table_prefix."session` (`id`, `data`, `key`, `browser`, `ip`, `id_users`) VALUES (NULL, '{$session}', '{$key}', '{$browser}', '{$ip}', '{$id_users}');";
          		
			// run the sql
			$query = mysql_query($sql);
			
			// return true
			return $key;
	}
  
  function createEx() {
      // create a unique id, this is kinda overkill but it will be unique
			//$key = sha1(uniqid(sha1(uniqid(null, true)), true));
			//if($this->session_temp=="") $this->session_temp=session_id();
      $key = $this->session_id;  
      $browser = $this->browser;
      $ip = $this->ip;
      $id_users=$this->id_users;
			
			$rs=getTable("session","","(`key`='{$key}' AND `browser`='{$browser}' AND `ip`='{$ip}' AND `id_users` = '{$id_users}')");
			if(count($rs)>0) return $rs[0]['id'];
			
			// create an empty seesion and serialize it
			$session = serialize(array());
      	
			// build the sql
			$sql = "INSERT INTO `".$this->config_table_prefix."session` (`id`, `data`, `key`, `browser`, `ip`, `id_users`) VALUES (NULL, '{$session}', '{$key}', '{$browser}', '{$ip}', '{$id_users}');";
          		
			// run the sql
			$query = mysql_query($sql);
			
			// return true
			return mysql_insert_id();
	}

	/**
	 * Save an array to the database
	 *
	 * @param array
	 * @return null
	 * @access private
	 * @author Ben Phelps
	 **/
	function save($session) {
    // pull the key from temp storage or the session value
		$key = $this->session_id;  
    $browser = $this->browser;
    $ip = $this->ip;
    $id_users=$this->id_users;
    
    // turn the array into a string
    $session = addslashes(serialize($session));
		
		// uild the sql
		$sql = "UPDATE `".$this->config_table_prefix."session` SET data = '{$session}' WHERE (`key` = '{$key}' AND `browser` = '{$browser}' AND `ip`='{$ip}' AND `id_users` = '{$id_users}')";
    
    // run the sql
		$query = mysql_query($sql);
	}
	
	/**
	 * Return the raw session array
	 *
	 * @return array
	 * @access public
	 * @see raw()
	 * @author Ben Phelps
	 **/
	function raw() {
    $key = $this->session_id;  
    $browser = $this->browser;
    $ip = $this->ip;
		$id_users=$this->id_users;
		
		// build the sql
    //$query = "SELECT * FROM `".$this->config_table_prefix."session` WHERE `key` = '{$key}' AND `browser` = '{$browser}' AND `ip` = '{$ip}';";
		$query = "SELECT * FROM `".$this->config_table_prefix."session` WHERE (`key` = '{$key}' AND `browser` = '{$browser}' AND `ip`='{$ip}' AND `id_users` = '{$id_users}')";
		
		// run the sql
		$result = mysql_query($query);
		$result = mysql_fetch_array($result);
		
		// turn the string into an array 
    $result = unserialize($result['data']);
		 
		// return an array
		return $result;
	}
  
  function rawByID($id) {
		// build the sql
    //$query = "SELECT * FROM `".$this->config_table_prefix."session` WHERE `key` = '{$key}' AND `browser` = '{$browser}' AND `ip` = '{$ip}';";
		$query = "SELECT * FROM `".$this->config_table_prefix."session` WHERE id=".$id;
		
		// run the sql
		$result = mysql_query($query);
		$result = mysql_fetch_array($result);
		
		// turn the string into an array 
    $result = unserialize($result['data']);
		 
		// return an array
		return $result;
	}
	
	/**
	 * Destroy the session cookie ending any session
	 *
	 * @return void
	 * @access public
	 * @author Ben Phelps
	 **/
	function destroy() {
	  $key = $this->session_id;  
    $browser = $this->browser;
    $ip = $this->ip;
		$id_users=$this->id_users;
		
    // set the seesion to null
		$this->session_temp = NULL;
		
		// set php to delete in garbage collection
		unset($this->session_id);
		
		$sql="DELETE FROM `".$this->config_table_prefix."session` WHERE (`key` = '{$key}' AND `browser` = '{$browser}' AND `ip`='{$ip}')";
		$query = mysql_query($sql);
	}
	
	/**
	 * Set a session variable 
	 *
	 * @return boolean
	 * @access public
	 * @author Ben Phelps
	 **/
	function set($key, $value, $overwrite = true) {
		// pull the seesion data from the database
		$session = $this->raw();
		
		// check if we have one set. and check if we are overwriting
		if( isset($session[$key]) && $overwrite == false)
		{
			// one is set and overwrite is false
			return FALSE;
		}
		else
		{
			// if one is set, overwite is true so we ignore it
			// set the value
			$session[$key] = $value;
			
			// save the new array to the database
			$this->save($session);
			
			// return true
			return TRUE;
		}
	}
	
	/**
	 * Fetch a session value 
	 *
	 * @return string|boolean
	 * @access public
	 * @author Ben Phelps
	 **/
	function get($key) {
		// pull session array from database
		$session = $this->raw();
		
		// check if the value is set
		if(isset($session[$key]))
		{
			// it was, so return it
			return $session[$key];
		}
		else
		{
			// it was not so return false
			return FALSE;
		}
	}
	
	/**
	 * Delete a session value
	 *
	 * @return boolean
	 * @access public
	 * @author Ben Phelps
	 **/
	function drop($key) {
		// read the session array from the database
		$session = $this->raw();
		
		// check if it is set
		if(isset($session[$key]))
		{
			// it is so we can delete it
			$session = $this->remove_key($session, $key);
			
			// save the new array
			$this->save($session);
			
			// send true
			return TRUE;
		}
		else
		{
			// was nt found in the array so send false
			return FALSE;
		}
	}
	
	function remove_key() {
		// get arguments passed to the function
		$args  = func_get_args();
		// ?? do magic
		return array_diff_key($args[0],array_flip(array_slice($args,1)));
	}
}
?>