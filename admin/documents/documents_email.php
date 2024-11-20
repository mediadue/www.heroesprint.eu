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
$objDocuments = new Documents;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."documents/documents.php");

$param = strtolower($objUtility->sessionVarRead("action"));
$id = $objUtility->sessionVarRead("iddoc");
switch ($param) {
	case "ins":
	case "upd":
		if ($param == "upd")
		{
			$rs = $objDocuments->getDetails($conn, $id);
			if (count($rs) > 0)
			{
				list($key, $row) = each($rs);
				$rsCli = $objUsers->getDetails($conn, $row["idusers"]);
				if (count($rsCli) > 0) 
				{
					list($key, $rowCli) = each($rsCli);
				}
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
				<?php $objJs->checkField("subject", "text", "OGGETTO", "subject") ?>
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
					<?php $objHtml->adminPageTitle("gestione documenti", "invio per email") ?>
					<div id="body">
						<div class="inputdata">
							<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()"/>
							<div class="elemento">
								<div class="label">documento </div>
								<div class="value"><?php $objHtml->adminIco($row["originalname"]) ?>&nbsp;<a href="<?php echo $objUtility->getPathBackoffice() ?>object_download.php?id=<?php echo $row["idoggetti"] ?>" target="_blank"><?php echo $row["originalname"] ?></a></div>
							</div>
							<?php if($rowCli["ragionesociale"]!="") { ?>
                <div class="elemento">
  								<div class="label">ragione sociale </div>
  								<div class="value"><?php echo $rowCli["ragionesociale"] ?></div>
  							</div>
							<? } ?>
							<?php if($rowCli["nome"]!="" || $rowCli["cognome"]!="") { ?>
                <div class="elemento">
  								<div class="label">cliente </div>
  								<div class="value"><?php echo $rowCli["nome"]." ".$rowCli["cognome"]; ?></div>
  							</div>
							<? } ?>
							<div class="elemento">
								<div class="label">email </div>
								<div class="value"><?php echo $rowCli["email"] ?></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="subject">oggetto</label> * </div>
								<div class="value"><input type="text" name="subject" id="subject" maxlength="128" class="text" /></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="testo">testo </label> </div>
								<div class="value">
									<textarea name="testo" id="testo" rows="10" cols="40" class="default"></textarea>
								</div>
							</div>
							<div class="elemento">
								<div class="label"><label for="testo">&nbsp;</label></div>
                <div class="value"><input type="submit" name="act_DOCUMENTS-EMAIL-INSUPD-DO" value="Invia" class="btn"/ onclick="return confirm('Confermi l\'invio?');" ></div>
							</div>
							</form>
							<div class="esito">
								<?php
								$rs = $objDocuments->emailList($conn, $id);
								if (count($rs)) 
								{
									?>
									<div class="header" style="color:red; font-size:120%;">email gi&agrave; inviate con questo documento: <b><?php echo count($rs) ?></b></div>
									<form action="action.php" method="post">
									<?php
									while (list($key, $row) = each($rs)) 
									{ 
										?>
										<div class="item">
											<div class="detail">Oggetto: <span class="value"><?php echo $row["subject"] ?></span></div>
											<div class="detail">Testo: <span class="value"><?php echo $row["testo"] ?></span></div>
											<div class="detail">Inviata il: <span class="value"><?php echo $objUtility->dateTimeShow($row["inserimento_data"], "short")?></span></div>
											<div class="detail">Inviata da: <span class="value"><?php echo $row["inserimento_username"] ?></span></div>
										</div>
										<?php
									}
									?>
									</form>
									<?php
								}
								else 
								{
									?>
									<div class="item" style="width:400px;height:auto;">
										<div class="message">
											Nessun email ancora inviata per questo documento
										</div>
									</div>
									<?php
								}
								?>
								<div style="clear:both;">&nbsp;</div>
							</div>
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