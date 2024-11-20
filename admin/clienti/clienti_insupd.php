<?php



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
$objClienti = new Clienti;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."clienti/clienti.php");

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
			<script type="text/javascript">
			<!--
			function checkForm() {
				var theform = document.frm;
				<?php $objJs->checkField("username", "text", "USERNAME", "username") ?>
	      		if (theform.password.value != theform.password_conf.value) {
		      		alert('Le password non coincidono');
		      		theform.password.focus();
		      		return false;
	      		}
				if (theform.password.value != '')
				{
					if (confirm("Hai impostato una password per il cliente.\n\nVuoi spedirgliela via mail?")) {
						theform.issendpwd.value = 1;
					}
				}
				theform.submit();
			}
			//-->
			</script>
		</head>
		<body>
		<div id="site">
			<?php $objHtml->adminHead() ?>
			<div id="content">
				<?php $objHtml->adminLeft($conn, $intIdutente) ?>
				<div id="area">
					<?php $objHtml->adminPageTitle("Clienti", "Inserimento dati") ?>
					<div id="body">
						<div class="inputdata">
							<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()"/>
							<input type="hidden" name="issendpwd" value="0"/>
							<div class="elemento">
								<div class="label"><label for="titolo">username </label> * </div>
								<div class="value"><input type="text" name="username" id="username" maxlength="50" class="text" value="<?php echo $row["login"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="password">password </label> <span style="font-style:italic;">[per impostare una password, riempire entrambi i campi]</span></div>
								<div class="value"><input type="password" name="password" id="password" maxlength="50" class="text" value=""/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="password_conf">conferma password </label></div>
								<div class="value"><input type="password" name="password_conf" id="password_conf" maxlength="50" class="text" value=""/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="codicecliente">codice cliente </label></div>
								<div class="value"><input type="text" name="codicecliente" id="codicecliente" maxlength="20" class="text" value="<?php echo $row["codicecliente"] ?>"/></div>
							</div>
							<div class="elemento" style="margin-bottom:30px;">
								<div class="label"><label for="ragionesociale">ragione sociale </label></div>
								<div class="value"><input type="text" name="ragionesociale" id="ragionesociale" maxlength="100" class="text" value="<?php echo $row["ragionesociale"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="partitaiva">partita iva </label></div>
								<div class="value"><input type="text" name="partitaiva" id="partitaiva" maxlength="11" class="text" value="<?php echo $row["partitaiva"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="codicefiscale">codice fiscale </label></div>
								<div class="value"><input type="text" name="codicefiscale" id="codicefiscale" maxlength="16" class="text" value="<?php echo $row["codicefiscale"] ?>"/></div>
							</div>
							<div class="element">
								<div class="label"><label for="indirizzo">indirizzo </label></div>
								<div class="value"><input type="text" name="indirizzo" maxlength="50" class="text" value="<?php echo $row["indirizzo"] ?>"/></div>
							</div>
							<div class="element">
								<div class="label"><label for="citta">citt&#224; </label></div>
								<div class="value"><input type="text" name="citta" maxlength="50" class="text" value="<?php echo $row["citta"] ?>"/></div>
							</div>
							<div class="element">
								<div class="label"><label for="cap">cap </label></div>
								<div class="value"><input type="text" name="cap" maxlength="6" class="textsmall" value="<?php echo $row["cap"] ?>"/></div>
							</div>
							<div class="element">
								<div class="label"><label for="provincia">provincia </label></div>
								<div class="value"><input type="text" name="provincia" maxlength="4" class="textsmall" value="<?php echo $row["provincia"] ?>"/></div>
							</div>
							<div class="element">
								<div class="label"><label for="nazione">nazione </label></div>
								<div class="value"><input type="text" name="nazione" maxlength="50" class="text" value="<?php echo $row["nazione"] ?>"/></div>
							</div>
							<div class="element">
								<div class="label"><label for="telefono">telefono </label></div>
								<div class="value"><input type="text" name="telefono" maxlength="50" class="text" value="<?php echo $row["telefono"] ?>"/></div>
							</div>
							<div class="element">
								<div class="label"><label for="fax">fax </label></div>
								<div class="value"><input type="text" name="fax" maxlength="50" class="text" value="<?php echo $row["fax"] ?>"/></div>
							</div>
							<div class="element">
								<div class="label"><label for="email">email </label></div>
								<div class="value"><input type="text" name="email" maxlength="50" class="text" value="<?php echo $row["email"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="note">note </label></div>
								<div class="value"><textarea name="note" id="note" class="default" rows="5"><?php echo $objUtility->translateForTextarea($row["note"]) ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><input type="checkbox" name="isdisabled" id="isdisabled" value="1"<?php echo ($row["isdisabled"]) ? " checked=\"yes\"" : "" ?>"/>&nbsp;<label for="isdisabled">disabilitato </label></div>
							</div>
							<!--
							<div class="elemento">
								<div class="label"><label for="isbackoffice">abilitato al backoffice </label></div>
								<div class="value"><input type="checkbox" name="isbackoffice" id="isbackoffice" value="1"<?php echo ($row["isbackoffice"]) ? " checked=\"yes\"" : "" ?>"/></div>
							</div>
							-->
							<?php if ($param == "upd") { ?>
								<div class="elemento">
									<div class="label">data creazione </div>
									<div class="value"><?php echo $objUtility->datetimeShow($row["datecreation"], "long") ?></div>
								</div>
							<?php } ?>
							<?php if ($row["isactivated"]) { ?>
								<div class="elemento">
									<div class="label">data attivazione </div>
									<div class="value"><?php echo $objUtility->datetimeShow($row["activationdate"], "long") ?></div>
								</div>
							<?php } ?>
							<div class="elemento">
								<div class="label">&#160;</div>
								<div class="value"><input type="submit" name="act_CLIENTI-INSUPD-DO" value="Salva" class="btn"/></div>
							</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php $objHtml->adminFooter() ?>
		</div>
		</body>
		</html>
		<?php
		break;
}
?>