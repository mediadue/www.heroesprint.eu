<?php

function retProfessione($str,$ins=true) {
  global $config_table_prefix;
  
  $rs=getTable("professione","","professione='$str'");
  if(count($rs)>0) return $rs[0]['id'];
  
  if($ins) {
    $sql="INSERT INTO `".$config_table_prefix."professione` (professione) VALUES ('$str') ";
    mysql_query($sql);
    return mysql_insert_id();
  }
}

function retStatoCivile($str,$ins=true) {
  global $config_table_prefix;
  
  $rs=getTable("stato_civile","","stato_civile='$str'");
  if(count($rs)>0) return $rs[0]['id'];
  
  if($ins) {
    $sql="INSERT INTO `".$config_table_prefix."stato_civile` (stato_civile) VALUES ('$str') ";
    mysql_query($sql);
    return mysql_insert_id();
  }
}

function retHobby($str,$ins=true) {
  global $config_table_prefix;
  
  $rs=getTable("hobby","","hobby='$str'");
  if(count($rs)>0) return $rs[0]['id'];
  
  if($ins) {
    $sql="INSERT INTO `".$config_table_prefix."hobby` (hobby) VALUES ('$str') ";
    mysql_query($sql);
    return mysql_insert_id();
  }
}

?>
