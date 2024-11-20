<?php
session_start();

header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");

require_once("_docroot.php");
require_once(SERVER_DOCROOT."/logic/class_config.php");
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
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."utenti/utenti.php");
$isSystem=$objUsers->isSystem($conn, $intIdutente);

$param = strtolower($objUtility->sessionVarRead("action"));
$id = $objUtility->sessionVarRead("idutenti");
switch ($param) {
	case "ins":
	case "upd":
		if ($param == "upd") {
			$rs = $objUsers->getDetails($conn, $id);
			if (count($rs) > 0) {
				list($key, $row) = each($rs);
			}
		}
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
		<head>
      <?php $objHtml->adminHeadsection() ?>
			<script>
			$(document).ready(function() { 
        function showResponse() {
          if($("#response_user_exist").html()=="false") {
            alert("Il nome utente scelto non è disponibile");
            $('[name=username]').focus();
           }else{
            document.frm.submit();
           } 
        }
        
        var options = { 
          target: '#response_user_exist',
          success:       showResponse   
        }; 
        
        $('#frm').submit(function(){
          return false;  
        });
        
        $('#user_exist').submit(function(){
          $('[name=username_ver]').val($('[name=username]').val());  
        });

        $('#user_exist').ajaxForm(options);
        
        $("input[name=act_DELIMG-DO]").click(function(){
          if(confirm('Si desidera continuare con la rimozione dell\'immagine?')) {
            $("input[name=act_UTENTI-INSUPD-DO]").attr("name","act_DELIMG-DO");
            return true;  
          }else{
            return false;
          }
        });
        
      });
			</script>
			
      <script type="text/javascript">
			<!--
			function mostra(id) {
        var height=document.getElementById(id).style.height;
        document.getElementById(id).style.overflow='hidden';
        
        if(height=="") {
          document.getElementById(id).style.height='1px';
          document.getElementById(id).style.visibility='hidden';
        } else {
          document.getElementById(id).style.height='';
          document.getElementById(id).style.visibility='visible';
          window.scrollTo(0,0);
        }
      }
      
      function Annulla() {
        if(confirm('I dati non salvati verranno persi.\nSi desidera continuare?')) {
          location.href = "utenti.php";
          return false;
        } else {
          return false;
        }
      }
      
			function checkForm() {
				var theform = document.frm;
				<?php $objJs->checkField("username", "text", "USERNAME", "username") ?>
				<?php if ($param == "ins") { ?>
					<?php $objJs->checkField("password", "password", "PASSWORD", "password") ?>
		      		if (theform.password.value != theform.password_conf.value) {
			      		alert('Le password non coincidono');
			      		theform.password.focus();
			      		return false;
		      		}
				<?php } else { ?>
		      		if (theform.password.value != theform.password_conf.value) {
			      		alert('Le password non coincidono');
			      		theform.password.focus();
			      		return false;
		      		}
				<?php } ?>
				<?php //$objJs->checkField("email", "text", "EMAIL", "email") ?>
        if (theform.email.value!=""  && (theform.email.value.indexOf('@')==-1 || theform.email.value.indexOf('@')==(theform.email.value.length-1))) {
          alert('e-mail non valida');
			    theform.email.focus();
			    return false;
        }
        if (theform.password.value != '' && theform.email.value)
				{
					//if (confirm("Hai impostato una password per il cliente.\n\nVuoi spedirgliela via mail?")) {
						//theform.issendpwd.value = 1;
					if (document.frm.issendpwd.value=='0') {
            mostra('box');
						return false;
					}
					//}
				}
				$('#user_exist').submit();
			}

			//-->
			</script>
		</head>
		<body>
		<div id="site">
			<?php $objHtml->adminHead() ?>
			<div id="content">
				<?php $objHtml->adminLeft($conn, $intIdutente,"utenti/utenti.php") ?>
				<div id="area">
					<?php $objHtml->adminPageTitle("Utenti", "Anagrafica","1") ?>
					<div id="body">
						<div class="inputdata" >
							<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()" enctype="multipart/form-data" >
                <input type="hidden" name="issendpwd" value="0"/>
                <input type="hidden" name="act_UTENTI-INSUPD-DO" value="Ok"/>
                <table border="0" width="100%" ><tr><td style="vertical-align:top;width:50%">
                  <div class="elemento">
    								<div class="label"><label for="codice">Codice </label></div>
    								<div class="value"><input type="text" name="codice" id="codice" maxlength="50" class="text" value="<?php echo $row["id"] ?>" disabled/></div>
    							</div>
                  <?php if(retFile($row["immagine_file"],80)) { ?>
                    <div class="elemento">
      								<div class="label"><label for="immagine">Immagine del Profilo </label></div>	
                      <div class="value"><img src="<?php echo retFile($row["immagine_file"],80) ?>" /></div>
                      <div class="label"><label for="immagine">&nbsp; </label></div>
                      <input type="submit" name="act_DELIMG-DO" value="elimina" class="delbtn" />
      							</div>
    							<? } ?>
                  <div class="elemento">
    								<div class="label"><label for="immagine">Immagine del Profilo </label></div>	
                    <div class="value"><input type="file" name="immagine" id="immagine" class="text" value="" /></div>
    							</div>
                  <div class="elemento">
    								<div class="label"><label for="ragionesociale">Ragione sociale </label></div>
    								<div class="value"><input type="text" name="ragionesociale" id="ragionesociale" class="text" value="<?php echo $row["ragionesociale"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="cognome">Cognome </label></div>
    								<div class="value"><input type="text" name="cognome" id="cognome" class="text" value="<?php echo $row["cognome"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="nome">Nome </label></div>
    								<div class="value"><input type="text" name="nome" id="nome" class="text" value="<?php echo $row["nome"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="indirizzo">Indirizzo </label></div>
    								<div class="value"><input type="text" name="indirizzo" class="text" value="<?php echo $row["indirizzo"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="citta"></label></div>
    								<div class="value"><?php comboBox("comuni",$field1="",$field2="",$selected="",$multiple="",$onchange="",$echoId="",$nome="sel_citta",$where="", $class="textsmall"); ?>&nbsp;</div>
    								<div class="label"><label for="citta">Comune </label></div>
    								<div class="value"><input type="text" name="citta" class="text" value="<?php echo $row["comune"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="localita">Località </label></div>
    								<div class="value"><input type="text" name="localita" class="textsmall" value="<?php echo $row["citta"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="provincia">Provincia </label></div>
    								<div class="value"><input type="text" name="provincia" maxlength="4" class="textsmall" value="<?php echo $row["provincia"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="cap">CAP </label></div>
    								<div class="value"><input type="text" name="cap" maxlength="6" class="textsmall" value="<?php echo $row["cap"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="regione">Regione </label></div>
    								<div class="value"><input type="text" name="regione" class="text" value="<?php echo $row["regione"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="nazione">Nazione </label></div>
    								<div class="value"><input type="text" name="nazione" class="text" value="<?php echo $row["nazione"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="regione_estera">Regione estera </label></div>
    								<div class="value"><input type="text" name="regione_estera" class="text" value="<?php echo $row["regione_estera"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="telefono">Telefono </label></div>
    								<div class="value"><input type="text" name="telefono" class="text" value="<?php echo $row["telefono"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="fax">Fax </label></div>
    								<div class="value"><input type="text" name="fax" class="text" value="<?php echo $row["fax"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="cellulare">Cellulare </label></div>
    								<div class="value"><input type="text" name="cellulare" class="text" value="<?php echo $row["cellulare"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="email">E-mail </label></div>
    								<div class="value"><input type="text" name="email" class="text" value="<?php echo $row["email"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="sito">Sito </label></div>
    								<div class="value"><input type="text" name="sito" class="text" value="<?php echo $row["sito"] ?>"/></div>
    							</div>
  							</td><td style="vertical-align:top;">
    							<div class="elemento">
    								<div class="label"><label for="codicefiscale">Cod. Fiscale </label></div>
    								<div class="value"><input type="text" name="codicefiscale" id="codicefiscale" maxlength="16" class="text" value="<?php echo $row["codicefiscale"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="partitaiva">Partita IVA </label></div>
    								<div class="value"><input type="text" name="partitaiva" id="partitaiva" maxlength="11" class="text" value="<?php echo $row["partitaiva"] ?>"/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="note">Note </label></div>
    								<div class="value"><textarea name="note" id="note" class="default" rows="5"><?php echo $objUtility->translateForTextarea($row["note"]) ?></textarea></div>
    							</div>
                  <?php if ($row["isactivated"]) { ?>
    								<div class="elemento">
    									<div class="label">Data attivazione </div>
    									<div class="value"><?php echo $objUtility->datetimeShow($row["activationdate"], "long") ?></div>
    								</div>
    							<?php } ?>
    							<?php if ($row["datecreation"] && $row["datecreation"]!="0000-00-00 00:00:00") { ?>
    								<div class="elemento">
    									<div class="label">Data creazione </div>
    									<div class="value"><?php echo $objUtility->datetimeShow($row["datecreation"], "long") ?></div>
    								</div>
    							<?php } ?>
                  
                  <div class="elemento" style="margin-left:40px;text-align:center;font-size:14px;"><hr style="margin-bottom:10px;">CREDENZIALI D'ACCESSO</div>
                  <div class="elemento">
    								<div class="label"><label for="titolo">User ID </label></div>
    								<div class="value"><input type="text" name="username" id="username" maxlength="50" class="text" value="<?php echo $row["login"] ?>"/></div>
    							</div>
                  <div class="elemento">
    								<div class="label"><label for="password">Password (*) </label></div>
    								<div class="value"><input type="password" name="password" id="password" maxlength="21" class="text" value=""/></div>
    							</div>
    							<div class="elemento">
    								<div class="label"><label for="password_conf">Conferma password (*) </label></div>
    								<div class="value"><input type="password" name="password_conf" id="password_conf" maxlength="21" class="text" value=""/></div>
    							</div>
    							<?php if($isSystem) { ?>
                  <div class="elemento">
    								<div class="label"><label for="isdisabled">Disabilitato </label></div>
    								<div class="value"><input type="checkbox" name="isdisabled" id="isdisabled" value="1"<?php echo ($row["isdisabled"]) ? " checked=\"yes\"" : "" ?>"/></div>
    							</div>
    							<? } ?>
    							<div class="elemento">
    								<div class="label"><label for="isbackoffice">Abilitato al backoffice </label></div>
    								<div class="value"><input type="checkbox" name="isbackoffice" id="isbackoffice" value="1"<?php echo ($row["isbackoffice"]) ? " checked=\"yes\"" : "" ?>"/></div>
    							</div>
    							<?php if($isSystem) { ?>
                  <div class="elemento">
    								<div class="label"><label for="isactivated">Attivato </label></div>
    								<div class="value"><input type="checkbox" name="isactivated" id="isactivated" value="1"<?php echo ($row["isdisabled"]) ? " checked=\"yes\"" : "" ?>"/></div>
    							</div>
    							<? } ?>
    							<div class="elemento" style="margin-left:40px;text-align:left;font-size:10px;"><hr style="margin-bottom:10px;">(*) L'inserimento della password e della relativa conferma, genera l'invio automatico all'utente delle nuove credenziali</div>
  							</td></tr></table>
  							<table border="0" width="100%">
                  <tr>
                    <td colspan="2" style="vertical-align:top;width:100%">
        							<div class="elemento" style="margin-left:40px;text-align:center;font-size:14px;"><hr style="margin-bottom:10px;">ULTERIORI INFORMAZIONI</div>
                    </td>
                  </tr>
                  <tr>
                    <td style="vertical-align:top;width:50%">
                      <div class="elemento">
        								<div class="label"><label for="data_di_nascita">Data di nascita </label></div>
        								<div class="value"><? formdata("data_di_nascita",'','','','',$row["data_di_nascita"],"1900",""); ?></div>
        							</div>
        							<div class="elemento">
        								<div class="label"><label for="sesso">Sesso </label></div>
        								<div class="value"><?php comboBox("sesso","","",$row["id_sesso"],$multiple="",$onchange="",$echoId="",$nome="",$where="", $class="textsmall",$ordine="id"); ?>&nbsp;</div>
        							</div>
                      <div class="elemento">
        								<div class="label"><label for="nato_a"></label></div>
        								<div class="value"><?php comboBox("comuni",$field1="",$field2="",$selected="",$multiple="",$onchange="",$echoId="",$nome="sel_nato_a",$where="", $class="textsmall"); ?>&nbsp;</div>
        								<div class="label"><label for="citta">Nato a </label></div>
        								<div class="value"><input type="text" name="nato_a" maxlength="255" class="text" value="<?php echo $row["nato_a"] ?>"/></div>
        							</div>
                      <div class="elemento">
        								<div class="label"><label for="provincia_di_nascita">Provincia di </label></div>
        								<div class="value"><input type="text" name="provincia_di_nascita" maxlength="4" class="textsmall" value="<?php echo $row["provincia_di_nascita"] ?>"/></div>
        							</div>
        							<div class="elemento">
        								<div class="label"><label for="nucleo_familiare">Nucleo familiare </label></div>
        								<div class="value"><input type="text" name="nucleo_familiare" maxlength="3" class="textsmall" value="<?php echo $row["nucleo_familiare"] ?>"/></div>
        							</div>
                    </td>
                    <td style="vertical-align:top;">  
                      <div class="elemento">
        								<div class="label"><label for="stato_civile">Stato civile </label></div>
        								<div class="value"><?php comboBox("stato_civile","","",$row["id_stato_civile"],$multiple="",$onchange="",$echoId="",$nome="",$where="", $class="default",$ordine="id"); ?>&nbsp;</div>
        							</div>
                      <div class="elemento">
        								<div class="label"><label for="professione">Professione </label></div>
        								<div class="value"><?php comboBox("professione","","",$row["id_professione"],$multiple="",$onchange="",$echoId="",$nome="",$where="", $class="default",$ordine="id"); ?>&nbsp;</div>
        							</div>
        							<div class="elemento">
        								<div class="label"><label for="titolo_di_studio">Titolo di studio </label></div>
        								<div class="value"><?php comboBox("titolo_di_studio","","",$row["id_titolo_di_studio"],$multiple="",$onchange="",$echoId="",$nome="",$where="", $class="default",$ordine="id"); ?>&nbsp;</div>
        							</div>
                      <div class="elemento">
        								<div class="label"><label for="hobby">Hobby principale </label></div>
        								<div class="value"><?php comboBox("hobby1","","",$row["id_hobby1"],$multiple="",$onchange="",$echoId="",$nome="hobby1",$where="", $class="default",$ordine="id"); ?>&nbsp;</div>
        							</div>
        							<div class="elemento">
        								<div class="label"><label for="hobby">Hobby secondario </label></div>
        								<div class="value"><?php comboBox("hobby2","","",$row["id_hobby2"],$multiple="",$onchange="",$echoId="",$nome="hobby2",$where="", $class="default",$ordine="id"); ?>&nbsp;</div>
        							</div>
        							<div class="elemento">
        								<div class="label"><label for="hobby">Altri hobby </label></div>
        								<div class="value"><?php comboBox("hobby3","","",$row["id_hobby3"],$multiple="",$onchange="",$echoId="",$nome="hobby3",$where="", $class="default",$ordine="id"); ?>&nbsp;</div>
        							</div>
                    </td>
                  </tr>
                </table>
                <div class="elemento">
  								<div class="label">&#160;</div>
  								<div class="value">
                    <input type="submit" name="act_UTENTI-INSUPD-DO" value="SALVA" class="btn" />
                    <input type="button" name="act_UTENTI-INSUPD-ANNULLA" value="ANNULLA" class="btn" ONCLICK="return Annulla();" />
                  </div>
  							</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php 
      geografia($idComune="sel_citta", $NomeCapDest="cap", $NomeComuneDest="citta", $NomeProvinciaDest="provincia","","","regione"); 
      geografia($idComune="sel_nato_a", $NomeCapDest="", $NomeComuneDest="nato_a", $NomeProvinciaDest="provincia_di_nascita");
      $objHtml->adminFooter() 
      ?>
		</div>
		<div style="display:none;">
		  <form action="action.php" id="user_exist" name="user_exist" method="post">
        <input type="hidden" name="act_USER-EXIST" value="1" />
        <input type="hidden" name="username_ver" value="" /> 
      </form>
      <div id="response_user_exist"></div>   
		</div>
    </body>
		</html>
		<?php
    break;
}
?>

<?php confirm("Hai impostato una password per il cliente.\n\nVuoi spedirgliela via mail?","SI","NO","document.frm.issendpwd.value","$('#user_exist').submit()","$('#user_exist').submit()"); ?>