<?php
  Class rsChat {
    var $g_config_table_prefix;
    
    function rsChat() {
      global $config_table_prefix;
    }
    
    function action(){
      global $config_table_prefix;
      $objUtility = new Utility;
      
      $m_type=$_POST['rsChat_type'];
      
      if($m_type=="printChatPeople") {
        $this->printChatPeople();
        exit;
      }
      
      if($m_type=="printChat") {
        $id=$_POST['rsChatID'];
        
        if($id=="-1") {
          $operators=getTable("chat","ping DESC","isadmin=1");
          $c=1000;
          $id=$operators[0]['sessionid'];
          while (list($key, $row) = each($operators)) {
            $mess=getTable("chat_messages","data DESC","(toid='".$row['sessionid']."' AND (TIME_TO_SEC(TIMEDIFF('".date('Y-m-d H:i:s')."',ping))<=1200))");  
            if(count($mess)<$c) {
              $c=count($mess);
              $id=$row['sessionid'];
            }
          }
        }
        
        if($id!="-1" && $id!="" && $id!="0") $this->printChat($id);
        exit;
      }
      
      if($m_type=="rsChat_close") {
        $str=$_POST['rsChatID'];
        $_SESSION['rsChat'][$str]=0;
        echo "1";
        exit;
      }
      
      if($m_type=="rsChatDrag") {
        $str=rawurldecode($_POST['rsChatMSG']);
        
        $x=$_POST['rsChatID'];
        $y=$_POST['rsChatID2'];
        $_SESSION['rsChat']["c_".$str]['x']=$x;
        $_SESSION['rsChat']["c_".$str]['y']=$y;
        exit;
      }
      
      if($m_type=="rsChatSend") {
        $fromid=$_POST['rsChatID'];
        $toid=$_POST['rsChatID2'];
        $mess=addslashes(rawurldecode(stripslashes($_POST['rsChatMSG'])));
        
        $chatfrom=getTable("chat","","sessionid='".$fromid."'");
        if($chatfrom[0]['id_users']>0) {
          $userfrom=retRow("users",$chatfrom[0]['id_users']);
          $img=retFile($userfrom['immagine_file'],22,22);
        }
        
        $sessionid=session_id();
        if(isset($_POST['phpss'])){
          $ss=explode("#_#", $_POST['phpss']);
          $sessionid=$ss[0];
        };
        
        if(!$img && $fromid==$sessionid) {
          $img=$objUtility->getPathBackofficeResources()."rsChat_useradmin.png";
        }elseif(!$img && $fromid!=$sessionid){
          $img=$objUtility->getPathBackofficeResources()."rsChat-user2.png";  
        }
        
        $sql="INSERT INTO `".$config_table_prefix."chat_messages` (fromid,toid,message,data) VALUES ('".$fromid."','".$toid."','".$mess."','".date('Y-m-d H:i:s')."')";
        mysql_query($sql);
        
        $this->insertRow($img,stripslashes($mess));
        exit;
      }
      
      if($m_type=="rsChat_getNewMess"){
        $this->refreshChat();
        
        if($_POST['rsChatID']=="1") {
          $this->printChatPeople();
          echo "#_rsCHAT_SEP_#";  
        }
        
        $sessionid=session_id();
        if(isset($_POST['phpss'])){
          $ss=explode("#_#", $_POST['phpss']);
          $sessionid=$ss[0];
        };
        
        $chat_mess=getTable("chat_messages","data ASC","(toid='".$sessionid."' AND letto='0')");
        while (list($key, $row) = each($chat_mess)) {
          $fromid=$row['fromid'];  
          $chatfrom=getTable("chat","ping DESC", "sessionid='".$fromid."'");  
      
          if($chatfrom[0]['id_users']>0) {
            $userfrom=retRow("users",$chatfrom[0]['id_users']);
            $img=retFile($userfrom['immagine_file'],22,22);
          }
        
          if(!$img) $img=$objUtility->getPathBackofficeResources()."rsChat-user2.png";
          
          echo $fromid."#_rsCHAT_#";
          
          $this->insertRow($img,$row['message']);
          $sql="UPDATE `".$config_table_prefix."chat_messages` SET letto='1',data='".date('Y-m-d H:i:s')."' WHERE id='".$row['id']."'";
          mysql_query($sql);
          
          echo "#_rsCHAT_SEP_#";
        }
        exit; 
      }
    }
    
    function count() {
      $sessionid=session_id();
      if(isset($_POST['phpss'])){
        $ss=explode("#_#", $_POST['phpss']);
        $sessionid=$ss[0];
      };
      
      $chat=getTable("chat","","sessionid<>'".$sessionid."'");
      return count($chat);
    }
    
    function _print(){
      $objUtility = new Utility;
      
      $this->refreshChat();
      ?>
      <!-- Plain box -->
      <div class="ez-wr rsChat-container">
        <!-- Module 2A -->
        <div class="ez-wr">
          <div class="ez-fl ez-negmr ez-50 rsChat-container-l">
            <!-- Module 3A -->
            <div class="ez-wr">
              <div class="ez-fl  ez-negmx ez-33 rsChat-user" style="width:10px;">
                <img src="<?php echo $objUtility->getPathBackofficeResources(); ?>rsChat-user.gif" />
              </div>
              <div class="ez-fl ez-negmr ez-33 rsChat-stato">
                <div class="ez-box"><img src="<?php echo $objUtility->getPathBackofficeResources(); ?>rsChat-stato-green.gif" /></div>
              </div>
              <div class="ez-last ez-oh rsChat-count">
                <div class="ez-box">Chat (<?php echo $this->count(); ?>)</div>
              </div>
            </div>
          </div>
          <div class="ez-last ez-oh">
            <!-- Module 2A -->
            <div class="ez-wr">
              <div class="ez-fl ez-negmr ez-50 rsChat-maximize">
                <div class="ez-box"></div>
              </div>
              <div class="ez-last ez-oh rsChat-close">
                <div class="ez-box"></div>
              </div>
            </div>
          </div>
        </div>
      </div>			
      <?php
    }
    
    function refreshChat() {
      global $config_table_prefix;
      $sessionid=session_id();
      if(isset($_POST['phpss'])){
        $ss=explode("#_#", $_POST['phpss']);
        $sessionid=$ss[0];
      };
      
      $menid=$_GET['menid'];
      if($menid>0) $menid="'".$menid."'"; else $menid="menid";
      
      $sql="UPDATE `".$config_table_prefix."chat` SET ping='".date('Y-m-d H:i:s')."',menid=".$menid." WHERE sessionid='".$sessionid."'";
      mysql_query($sql); 
      
      $disconnected=getTable("chat","","(TIME_TO_SEC(TIMEDIFF(now(),ping))>10)");
      while (list($key, $row) = each($disconnected)) {
        $sql="DELETE FROM `".$config_table_prefix."chat` WHERE id='".$row['id']."'";
        mysql_query($sql);  
      } 
    }
    
    function addToChat() {
      global $config_table_prefix;
      
      if(is_array($_SESSION['rsChat'])) {
        foreach ($_SESSION['rsChat'] as $key => $value) {
          if($value==1) {
            $this->printChat($key);
          }    
        }
      }
      
      $sessionid=session_id();
      if(isset($_POST['phpss'])){
        $ss=explode("#_#", $_POST['phpss']);
        $sessionid=$ss[0];
      };
      
      $ip=$_SERVER['REMOTE_ADDR'];
      $id_users=$_SESSION["user_id"];
      if($id_users=="") $id_users=$_SESSION["userris_id"];
      if($id_users=="") $id_users="0";
      $agent=mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);
      $referer=mysql_real_escape_string($_SERVER['HTTP_REFERER']);
      if($id_users>0) {
        $usr=retRow("users",$id_users);
        $login=$usr['nome'];
        $isadmin=$usr['isbackoffice'];
      }else{
        $max=getTable("chat","id DESC","");
        $login="User-".($max[0]['id']+1);
      }
      
      $chat=getTable("chat","","sessionid='".$sessionid."'");
      if(count($chat)==0) {
        $sql="INSERT INTO `".$config_table_prefix."chat` (ip,referer,browser,sessionid,id_users,isadmin,nickname,ping) VALUES ('".$ip."','".$referer."','".$agent."','".$sessionid."','".$id_users."','".$isadmin."','".$login."','".date('Y-m-d H:i:s')."')";
        mysql_query($sql);
        if(!($id_users>0)) {
          $tid=mysql_insert_id();
          $login="User-".($tid);
          
          $sql="UPDATE `".$config_table_prefix."chat` SET nickname='".addslashes($login)."',isadmin='".$isadmin."',ping='".date('Y-m-d H:i:s')."' WHERE id='".$tid."'";
          mysql_query($sql);
        }
      }elseif($chat[0]['id_users']!=$id_users){
        $sql="UPDATE `".$config_table_prefix."chat` SET id_users='".$id_users."',nickname='".addslashes($login)."',isadmin='".$isadmin."',ping='".date('Y-m-d H:i:s')."' WHERE sessionid='".$sessionid."'";
        mysql_query($sql);  
      }
      
      $this->refreshChat();
    }
    
    function printChatPeople() {
      $objUtility = new Utility;
      $sessionid=session_id();
      if(isset($_POST['phpss'])){
        $ss=explode("#_#", $_POST['phpss']);
        $sessionid=$ss[0];
      };
      ?>
      <!-- Plain box -->
      <div class="ez-wr rsChatPeople-container">
        <div class="ez-box rsChatPeople-title">Chat</div>
        <div class="rsChatPeople-container-scroll">
          <?php 
          $chat=getTable("chat","id DESC", "sessionid<>'".$sessionid."'"); 
          while (list($key, $row) = each($chat)) { 
            if($row['id_users']>0) {
              $user=retRow("users",$row['id_users']);
              $img=retFile($user['immagine_file'],22,22);
            }
            
            $gallery="";
            if($row['menid']>0) {
              $gallery=retRow("categorie",$row['menid']);
              if(!retFile($gallery['immagine_file'])) {
                $gallery=Table2ByTable1("categorie","fotogallery",$row['menid'],"attivo='1'","Ordinamento ASC");
                $gallery=$gallery[0];  
              }  
            }
            
            if(!$img) $img=$objUtility->getPathBackofficeResources()."rsChat-user2.png";
            ?>
            <!-- Module 3A -->
            <div class="ez-wr rsChatPeople-row" id="<?php echo $row['sessionid']; ?>">
              <div class="ez-fl  ez-negmx ez-33 rsChatPeople-row-img">
                <div class="ez-box"><img src="<?php echo $img; ?>" width="22" height="22" /></div>
              </div>
              <div class="ez-fl ez-negmr ez-33 rsChatPeople-user">
                <div class="ez-box"><?php echo $row['nickname']; ?></div>
              </div>
              <div class="ez-last ez-oh rsChatPeople-stato">
                <div class="ez-box">
                  <?php 
                  if(retFile($gallery['immagine_file'])) { ?>
                    <img src="<?php echo retFile($gallery['immagine_file'],22,22); ?>" width="22" height="22" title="<?php echo "Ref: ".$row['referer']."\n".strip_tags(implode("»",retBriciole("","",$row['menid']))); ?>" />                  
                  <? }else{ ?>
                    <img src="<?php echo $objUtility->getPathBackofficeResources(); ?>rsChat-stato-green.png" title="<?php echo "Ref: ".$row['referer']."\n".strip_tags(implode("»",retBriciole("","",$row['menid']))); ?>" />
                  <? } ?>
                </div>
              </div>
            </div>
    			<? } ?>
        </div>			 
      </div>
  		<?php  
    }
    
    function insertRow($img,$msg) { ?>
      <!-- Module 2A -->
      <div class="ez-wr rsChatPeople-row">
        <div class="ez-fl  ez-negmr ez-50 rsChatPeople-row-img">
          <div class="ez-box"><img src="<?php echo $img; ?>" width="22" height="22" /></div>
        </div>
        <div class="ez-last ez-oh rsChatPeople-mess">
          <div class="ez-box"><?php echo $msg; ?></div>
        </div>
      </div>
      <?php
    }
    
    function printChat($id) {
      global $config_table_prefix;
      $objUtility = new Utility;
      
      $_SESSION['rsChat'][$id]=1;
      if($_SESSION['rsChat']["c_".$id]['x']=="") $_SESSION['rsChat']["c_".$id]['x']="50%";
      if($_SESSION['rsChat']["c_".$id]['y']=="") $_SESSION['rsChat']["c_".$id]['y']="50%";
      
      
      $userid=$_SESSION["user_id"];
      if($userid=="") $userid=$_SESSION["userris_id"];
      $sessionid2=session_id();
      if(isset($_POST['phpss'])){
        $ss=explode("#_#", $_POST['phpss']);
        $sessionid2=$ss[0];
      };
      
      if($userid>0) {
        $user=retRow("users",$userid);
        
        $img[$sessionid2]=retFile($user['immagine_file'],22,22);
      }
      
      if(!$img[$sessionid2]) $img[$sessionid2]=$objUtility->getPathBackofficeResources()."rsChat_useradmin.png";
      
      $chatfrom=getTable("chat","ping DESC", "sessionid='".$id."'"); 
      $sessionid=$chatfrom[0]['sessionid']; 
      
      if($chatfrom[0]['id_users']>0) {
        $userfrom=retRow("users",$chatfrom[0]['id_users']);
        $img[$sessionid]=retFile($userfrom['immagine_file'],22,22);
      }
      
      if(!$img[$sessionid]) $img[$sessionid]=$objUtility->getPathBackofficeResources()."rsChat-user2.png";
      
      $chat_mess=getTable("chat_messages","data ASC","((fromid='".$chatfrom[0]['sessionid']."' AND toid='".$sessionid2."') OR (fromid='".$sessionid2."' AND toid='".$chatfrom[0]['sessionid']."'))");
      ?>
      <!-- Plain box -->
      <div class="ez-wr rsChatPriv-container" id="<?php echo $id; ?>" style="left:<?php echo $_SESSION['rsChat']["c_".$id]['x']; ?>px;top:<?php echo $_SESSION['rsChat']["c_".$id]['y']; ?>px;">
        <!-- Module 2A -->
        <div class="ez-wr rsChatPeople-title">
          <div class="ez-fl ez-negmr ez-50 rsChatPeople-title-l">                                                 
            <div class="ez-box"><?php echo $chatfrom[0]['nickname']; ?></div>
          </div>
          <div class="ez-last ez-oh rsChatPeople-close" id="<?php echo $id; ?>">
            <div class="ez-box"><img src="<?php echo $objUtility->getPathBackofficeResources()."dialog-close.png"; ?>" title="<?php echo ln("chiudi"); ?>" /></div>
          </div>
        </div>
				<!-- Plain box -->
        <div class="ez-wr rsChatPriv-body">
          <div class="rsChatPriv-body-content" id="<?php echo $id; ?>">
            <?php  
            while (list($key, $row) = each($chat_mess)) { 
              $sessionid=$row['fromid'];
              $this->insertRow($img[$sessionid],$row['message']);
              if($row['letto']=="0" && $sessionid!=$sessionid) {
                $sql="UPDATE `".$config_table_prefix."chat_messages` SET letto='1',data='".date('Y-m-d H:i:s')."' WHERE id='".$row['id']."'";
                mysql_query($sql);
              }
            } ?>
          </div> 
        </div>
				
        <!-- Module 2A -->
        <div class="ez-wr rsChatPriv-writemess">
          <div class="ez-fl ez-negmr ez-50 rsChatPriv-writemess-l">
            <div class="ez-box"><img src="<?php echo $objUtility->getPathBackofficeResources()."rsChat-bals.gif"; ?>" width="21" height="20" /></div>
          </div>
          <div class="ez-last ez-oh">
            <div class="ez-box"><input class="rsChatPriv-input" type="text" value="" fromid="<?php echo $sessionid2; ?>" toid="<?php echo $chatfrom[0]['sessionid']; ?>" /><div class="rsChatPriv-send"></div></div>
          </div>
        </div>				 
      </div>		
      <?php  
    }                            
  }
?>