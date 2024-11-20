<?php
  $errors = array();
  
  function enc_is_binary($file,$enc_key) {
    $source=enc_loadFile($file,"b");
    
    $key = $enc_key;
    
    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $tf=base64_decode($source);
    if($tf!="") $decrypted_data = mdecrypt_generic($td, $tf );
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    
    $decrypted_data = rtrim($decrypted_data,"\0");
    
    if(strpos($decrypted_data, "#rs-enc-module123;#")!==FALSE) {
      return 1;  
    }else{
      return 0;
    }
  } 
  
  function rs_pencrypt($file) {
    $enc_key="XhG3v0r0JfXkLmT0q0x1IiJc";
    if(enc_is_binary($file,$enc_key)==1) return;
    
    $source=enc_loadFile($file); 
    $source.="<?php //#rs-enc-module123;# ?>";
    $key = $enc_key;
    
    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $encrypted_data = mcrypt_generic($td, $source);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    
    $fp = fopen($file, 'w');
    fwrite($fp,base64_encode($encrypted_data));
    fclose($fp);
  }
  
  function rs_pdecrypt($file,$enc_key) {
    $source=enc_loadFile($file,"b");
    
    $key = $enc_key;
    
    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $tf=base64_decode($source);
    if($tf!="") $decrypted_data = mdecrypt_generic($td, $tf );
    
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    
    $decrypted_data = rtrim($decrypted_data,"\0");
    
    if(strpos($decrypted_data, "#rs-enc-module123;#")!==FALSE) {
      return $decrypted_data;  
    }else{
      return false;
    } 
  }
  
  function enc_loadFile($sFilename, $bin="") {
    if (!file_exists($sFilename)) return -3;
    $rHandle = fopen($sFilename, 'r'.$bin);
    if (!$rHandle) return -2;
    
    $sData = '';
    while(!feof($rHandle)){
      $sData .= fread($rHandle, filesize($sFilename));
    }
        
    fclose($rHandle);                                                                              
    return $sData;
  }
  
  function enc_getFilenameUnique () {
  	$enc_letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
  	
    $strChars = date("YmdHis", time());
  	srand((double)microtime()*1000000); 
  	for ($i=1; $i<=3; $i++) {
  		$intPos = rand(0, strlen($enc_letters));
  		$chrChar = substr ($enc_letters, $intPos, 1);
  		$strChars .= $chrChar;
  	}
  	return md5($strChars);
  }
  
  $enc_fname="enc_".enc_getFilenameUnique();
  
  $tcode='function '.$enc_fname.'($errno, $errstr) {global $errors;$errors[] = array("errno"=>$errno, "errstr"=>$errstr);}';
  eval($tcode);
                            
  function enc_include($file){
    global $config_table_prefix;
    global $objConfig;
    global $objUtility;
    
    $dec=rs_pdecrypt($file,"XhG3v0r0JfXkLmT0q0x1IiJc");
    
    if($dec!==false) {
      evale("?>".$dec);  
    }else{                             
      $tmphost=trim(strtolower($_SERVER['SERVER_NAME']));
      if($tmphost!="localhost" && $tmphost!="192.168.1.236"){
        rs_pencrypt($file); 
        enc_include($file); 
      }else{
        eval("require_once '".$file."';");
      }
    } 
  }
  
  function dec_include($file,$key){
    $dec=rs_pdecrypt($file,$key);
    if($dec!==false) {
      $dec=str_replace("<?php //#rs-enc-module123;# ?>", "", $dec);
      file_put_contents($file, $dec);    
    }
    
    include $file;   
  }
  
  function evale ($code) {
    global $errors;
    global $enc_fname;
    global $config_table_prefix;
    global $objConfig;
    global $objUtility;
    
    $errors = array(); // Reset errors
    
    $orig_hndl = set_error_handler($enc_fname,E_ALL);
    $orig_hndl = set_error_handler($enc_fname,E_ALL);
    
    if($orig_hndl == $enc_fname) {
      $code=str_replace("<?php //#rs-enc-module123;# ?>", "", $code); 
      eval($code);
    }              
    
    restore_error_handler();
    if (count($errors) > 0) {
      return(false);
    } else {
      return(true);
    }
  }
?>