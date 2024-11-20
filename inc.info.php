	<?php 
	$figli=getStruttura("magazzino",$_GET['menid']);
	if($_GET['ecomm_riepilogo']==1) $share=0;else $share=1;
	if($arrStr['nome']=="magazzino") $share=1;else $share=0;
	if(count($figli)>0) $share=0;
	//if($_GET["documents"]!=1 && !isset($_REQUEST["UserReg"])) $share=0;
	
	$tit="";
  if(isset($UserReg)) $tit=ln("I Tuoi Dati");
	if($_GET['documents']=="1") $tit=ln("I Tuoi Documenti");
	if($_GET["HRlogin"]==1 && !isset($_SESSION["userris_id"])) $tit=ln("Inserisci le tue credenziali");
	if($_GET["HRlogin"]==1 && isset($_SESSION["userris_id"])) $tit=ln("Il Tuo Account");
	if($_GET["HRreg"]==1 || isset($_POST['UserReg'])) $tit=ln("Diventa anche tu un Eroe!");
	?>
	
	<section id="contentArea" class="content-area container  spacing-normal clearfix">
      <div class="row">
        <?php if($tit=="") stampaTitoli($_GET['menid'],$share); else ?><h1 class="txt-display"><?php echo $tit; ?></h1>
        <div class="col-sm-12 content clearfix">
          <?php 
          $testi=getTesti();
          echo ln($testi[0]['testo_editor']); ?>
          
          <div class="container-r-reg"></div>
          <?php 
          if(isset($_SESSION["userris_id"]) && $_GET['documents']=="1") {
          	$objHtml->printDocumenti($_SESSION["userris_id"]);
          }elseif($arrStr["nome"]=="magazzino" || $_GET["ecomm_riepilogo"]==1 || $_GET["ecomm_combi"]>0){
          	$objCarrello->stampaCarrello();
          }elseif($_GET["HRlogin"]==1 && !isset($_REQUEST['UserReg']) && $_GET["HRreg"]!=1 && $_GET["menid"]!=2090){
          	if(!isset($_SESSION["userris_id"])) { ?>
              <form id="LoginAreaRis" action='' method='post'>
                <!-- Layout 1 -->
                <div class="ez-wr row">
                  <div class="ez-box col-sm-6"><input type="text" class="theInput form-control" name="utente" value="e-mail" /></div>
                  <div class="ez-box col-sm-6"><input type="password" class="theInput form-control" name="pwd" value="password" /></div>
                  <!-- Module 2A -->
                  <div class="ez-wr clearfix">
                    <div class="ez-fl ez-negmr ez-50 col-sm-6">
                      <div class="ez-box"><input type="submit" class="theSubmit arearis-login btn btn-success btn-block" value="<?php echo ln("Accedi");?>" name="Submit" /></div>
                    </div>
                    <div class="ez-last ez-oh col-sm-6">
                      <!--<div class="ez-box"><input type="submit" class="theSubmit registrati btn btn-primary btn-block" value="<?php echo ln("registrati");?>" name="UserReg" /></div>-->
                    </div>
                  </div>
                  
                  <!-- Plain box -->
                  <div class="ez-wr clearfix"> 
                    <div class="ez-box pwdSend-container col-sm-6"><input type="submit" class="theSubmit password-dimenticata btn btn-warning btn-block" value="<?php echo ln("password dimenticata?");?>" name="pwdSend" /></div> 
                  </div>
                </div>
                
                <div class="rsPwdSend col-sm-6" style="display:none;">
                  <div class="pwdSend-close"></div>
                  <div class="pwdSend-istruzioni"><strong><?php echo ln("Inserisci la User ID o la E-mail con cui ti sei registrato");?></strong></div>
                  <div class="loginarearis-username spacing-xs">User ID</div> <input type="text" class="theInput form-control spacing-xs" name="sendUser" /> 
                  <div class="loginarearis-password spacing-xs">E-mail</div> <input type="text" class="theInput form-control spacing-normal" name="sendEmail" />
                  <input type="submit" class="theSubmit btn btn-success btn-120" value="<?php echo ln("Invia");?>" name="pwdSendDo" />
                </div>
    					</form>
            <? }else{
              $objHtml->printInfoAreaRis();
            } 
          }elseif($_GET["menid"]==2090){
          	include 'inc.inviofile.php';	
          }
          ?>
        </div>

      </div>
    </section><!-- /#ocontentArea -->
