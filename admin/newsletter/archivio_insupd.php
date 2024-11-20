<?php
header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: Public");

require_once("_docroot.php");
require_once(SERVER_DOCROOT."logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$objMailing = new Mailing;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."newsletter/archivio.php");

$strParam = strtolower($objUtility->sessionVarRead("action"));
$intId = $objUtility->sessionVarRead("newsletter_idarchivio");
switch ($strParam) {
	case "ins":
	case "upd":
		if ($strParam == "upd") {
			$rs = $objMailing->archivioGetDetails($conn, $intId);
			if (count($rs) > 0)
				list($key, $row) = each($rs);
		}
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
		<head>
			<?php $objHtml->adminHeadsection() ?>
			<script type="text/javascript">
			<!--
			function checkForm(theform)
			{
				return true;
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
					<?php $objHtml->adminPageTitle("Newsletter", "Archivio, dettaglio") ?>
					<div id="body">
						<div class="inputdata">
							<div class="elemento">
								<div class="value">Newsletter #<?php echo $row["id"] ?>&nbsp;</div>
							</div>
						</div>
						<?php
						$idusersList = $row["iduserslist"];
						if ($idusersList) 
						{
							?>
							<div class="riepilogo-header">Utenti</div>
							<?php
							$arrUsers = explode(";", $idusersList);
							if (is_array($arrUsers)) 
							{
								?>
								<div class="riepilogo">
									<?php
									for ($i=0; $i<count($arrUsers); $i++) 
									{
										$idusers = $arrUsers[$i];
										if ($idusers)
										{
											$rsTmp = $objUsers->getDetails($conn, $idusers);
											if (count($rsTmp))
											{
												list($key, $rowTmp) = each($rsTmp);
												?>
												<div class="item">
													<div class="titolo">[<?php echo $i+1 ?>]&nbsp;<?php echo $rowTmp["ragionesociale"] ?></div>
													<div class="detail">Email: <span class="value"><?php echo $rowTmp["email"] ?></span></div>
												</div>
												<?php
											}
										}
									}
									?>
								</div>
								<?php
							}
						}
						else 
						{
							?>
							<div class="riepilogo-message">Nessun utente selezionato</div>
							<?php
						}
						?>
						<div class="inputdata">
							<div class="elemento">
								<div class="label">oggetto</div>
								<div class="value"><?php echo $row["subject"]; ?>&nbsp;</div>
							</div>
							<div class="elemento">
								<div class="label">testo</div>
								<div class="value"><?php echo $row["testo"]; ?>&nbsp;</div>
							</div>
							<?php 
							if ($row["idoggetti"]) 
							{
								?>
								<div class="elemento">
									<div class="label">allegato</div>
									<div class="value"><?php $objUtility->showObject($conn, $row["idoggetti"], true); ?></div>
								</div>
								<?php
							}
							?>
							<div class="elemento">
								<div class="label">Data creazione</div>
								<div class="value"><?php echo $objUtility->datetimeShow($row["inserimento_data"], "long") ?></div>
							</div>
							<div class="elemento">
								<div class="label">Utente</div>
								<div class="value"><?php echo $row["inserimento_username"]; ?> </div>
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