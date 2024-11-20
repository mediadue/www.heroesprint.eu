<?php
  Class rsWindows {
    var $g_config_table_prefix;
    var $g_hwnd;
    var $g_name;
    var $g_title;
    var $g_append;
    var $g_tables;
    var $g_resizable;
    var $g_tag;
    var $g_options;
    
    function rsWindows($title="") {
      $objUtility=new Utility();
      $this->g_hwnd=$objUtility->getFilenameUnique();
      $this->g_title=$title;
      $this->g_tables=array();
      $this->g_name="";
      $this->g_resizable=true;
      $this->g_tag="";
    }
    
    function action() {    
      global $config_table_prefix;
      
      if(!isset($_POST['rsOpenW'])) return false;
      $objConfig = new ConfigTool();
      $objDb = new Db;
      $objHtml = new Html;
      $objJs = new Js;
      $objMenu = new Menu;
      $objObjects = new Objects;
      $objUsers = new Users;
      $objUtility = new Utility;
      $conn = $objDb->connection($objConfig);
      
      $objUsers->getCurrentUser($intIdutente, $strUsername);
      $objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."navigazione/in_place.php");
      
      $options=unserialize(rawurldecode(stripslashes($_POST['options'])));
      $this->g_options=$options;
      
      if($options['title']!="") {
        $this->g_title=utf8_decode($options['title']);
      }elseif($options['table']!="") {
        $tmptit=getTable("rstbl2_tabelle","","(tabella='".$config_table_prefix.$options['table']."')");
        $this->g_title=$tmptit[0]['titolo_visualizzato'];    
      }
      $this->setResizable($options['resizable']);
      $this->setTag($options['tag']);
      $this->g_hwnd=$options['hwnd'];
      
      $this->_print(rawurldecode(stripslashes(utf8_decode($options['str']))));
      exit;
    }
    
    function setTag($str) {
      $this->g_tag=$str;  
    }
    
    function getTag() {
      return $this->g_tag;  
    }
    
    function setResizable($bool) {
      $this->g_resizable=$bool;  
    }
    
    function getResizable() {
      return $this->g_resizable;  
    }
    
    function setName($name) {
      $this->g_name=$name;  
    }
    
    function getName() {
      return $this->g_name;
    }
    
    function addTable($table) {
      array_push($this->g_tables, $table);  
    }
    
    function _print($content="",$visible=true) {
      global $config_table_prefix;
      $objUtility=new Utility();
      ?>
      <!-- Plain box -->
      <div class="ez-wr rs-windows <?php echo $this->g_hwnd; ?> <?php if(trim($this->g_options['strutture'])!="") echo "rs-windows-for-strutture"  ?>" hwnd="<?php echo $this->g_hwnd; ?>" name="<?php echo $this->g_name; ?>" <?php if($visible==false) echo "style='display:none;'"; ?> rsResizable="<?php echo $this->g_resizable; ?>" rsTag="<?php echo $this->g_tag; ?>" style="<?php if($this->g_options['width']>0) echo "width:".$this->g_options['width']."px;";if($this->g_options['height']>0) echo "height:".$this->g_options['height']."px;"; ?>">
        
        <!-- Plain box -->
        <div class="ez-wr rs-windows-frame-right">
          <div class="ez-box"></div> 
        </div>
						
        <!-- Module 3B -->
        <div class="ez-wr rs-windows-frame-bottom">
          <div class="ez-fl ez-negmr ez-33 rs-windows-frame-bottom-l"> 
            <div class="ez-box"></div>
          </div>
          <div class="ez-fr ez-negml ez-33 rs-windows-frame-bottom-r">
            <div class="ez-box"></div>
          </div>
          <div class="ez-last ez-oh rs-windows-frame-bottom-c">
            <div class="ez-box"></div>
          </div>
        </div> 

        <!-- Module 3B -->
        <div class="ez-wr rs-windows-titolo-container">
          <div class="ez-fl ez-negmr ez-33 rs-windows-icon"> 
            <div class="ez-box"></div>
          </div>
          <div class="ez-fr ez-negml ez-33 rs-windows-top-right-bar">
            <!-- Module 3A -->
            <div class="ez-wr">
              <div class="ez-fl  ez-negmx ez-33 rs-windows-minimize">
                <div class="ez-box"></div>
              </div>
              <div class="ez-fl ez-negmr ez-33 <?php if($this->g_resizable && $this->g_options['maxButton']==true) echo "rs-windows-maximize";else echo "rs-windows-maximize-dis"; ?>">
                <div class="ez-box"></div>
              </div>
              <div class="ez-last ez-oh rs-windows-close">
                <div class="ez-box"></div>
              </div>
            </div>
          </div>
          <div class="ez-last ez-oh rs-windows-titolo">
            <div class="ez-box"><?php echo $this->g_title; ?></div>
          </div>
        </div>
        <!-- Layout 1 -->
        <div class="ez-wr rs-windows-top-container">
          <div class="ez-wr rs-windows-content-container">
            <!-- CONTENT HERE -->
            <?php 
            if($this->g_options['template']!="" && $this->g_options['template']!="#") include $objUtility->getPathBackofficeAdminAbsolute()."rsWindows/".$this->g_options['template'];
            
            echo $content;
            reset($this->g_tables);
            while (list($key, $row) = each($this->g_tables)) {
              if($this->g_options['insert']=="1" && $this->g_options['insertId']>0){
                $row->_insert("",$button_label="",$len_field="",$this->g_options['insertId']);
              }else{
                $row->_print();
              }   
            }
            
            if(is_array($this->g_options['strutture'])) {
              reset($this->g_options['strutture']);
              while (list($key, $row) = each($this->g_options['strutture'])) {
                if(trim($row)!="") stampaStruttura(utf8_decode($row),"-1","-1","","1",-1);    
              }
            }else{
              if(trim($this->g_options['strutture'])!="") stampaStruttura(utf8_decode($this->g_options['strutture']),"-1","-1","","1",-1);
            }
            ?>   
          </div>	
        </div>
      </div><?php  
    }
  }
?>