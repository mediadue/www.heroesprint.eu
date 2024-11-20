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
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."oggetti/oggetti.php");

$strParam = strtolower($objUtility->sessionVarRead("action"));
$id = $objUtility->sessionVarRead("idoggetti");

switch ($strParam) {
	case "ins":
	case "upd":
		if ($strParam == "upd") {
			$rs = $objObjects->getDetails($conn, $id);
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
				<?php $objJs->checkField("nome", "text", "NOME", "nome") ?>
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
					<?php $objHtml->adminPageTitle("Oggetti", "") ?>
					<div id="body">
						<div class="inputdata">
							<form action="action.php" id="frm" name="frm" method="post" enctype="multipart/form-data" onsubmit="return checkForm()"/>
							<div class="elemento">
								<div class="label"><label for="nome">nome </label></div>
								<div class="value"><input type="text" name="nome" id="nome" maxlength="100" class="text" value="<?php echo $row["nome"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="estensione">estensione </label></div>
								<div class="value"><?php $objHtml->adminIco($row["originalname"]) ?>&nbsp;<?php echo $row["ext"] ?></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="filename">filename </label></div>
								<div class="value"><?php echo $row["originalname"] ?></div>
							</div>
							<?php
							if ($row["path"]) 
							{ 
								$path = $objUtility->getPathResourcesDynamicAbsolute();
								if ($row["isprivate"])
									$path = $objUtility->getPathResourcesPrivateAbsolute();
								?>
								<div class="elemento">
									<div class="label">dimensione </div>
									<div class="value"><?php echo $objUtility->getFileSizeKb($path . $row["path"]) ?> Kb</div>
								</div>
								<?php
							}
							?>
							<div class="elemento">
								<div class="label"><label for="oggetto">oggetto </label></div>
								<div class="value">
									<br/><a href="<?php echo $objUtility->getPathBackoffice() ?>object_download.php?id=<?php echo $row["id"] ?>" target="_blank">Scarica il file</a><br/><br/>
									<br/><input type="file" name="oggetto" id="oggetto" maxlength="100" class="file"/>
								</div>
							</div>
							<div class="elemento">
								<div class="value"><input type="submit" name="act_OGGETTI-INSUPD-DO" value="Ok" class="btn"/></div>
							</div>
							</form>
						</div>
						<?php
						$rsTmp = $objObjects->getContentNews($conn, $id);
						if (count($rsTmp)) {
							$i=0;
							?>
							<form action="<?php echo $objUtility->getPathBackoffice() ?>news/action.php" method="post">
							<div class="esito">
								<div class="header">L'oggetto e' presente in queste news</div>
								<?php
								while (list($key, $rowTmp) = each($rsTmp)) {
									$i++;
									?>
									<div class="item">
										<div class="titolo"><?php echo $rowTmp["titolo"] ?></div>
									</div>
									<?php
								}
								?>
			 				</div>
							</form>
							<?php
						}
						?>
						<?php
						$rsTmp = $objObjects->getContentDocuments($conn, $id);
						if (count($rsTmp)) {
							$i=0;
							?>
							<form action="<?php echo $objUtility->getPathBackoffice() ?>documents/action.php" method="post">
							<div class="esito">
								<div class="header">L'oggetto e' presente in questi documenti</div>
								<?php
								while (list($key, $rowTmp) = each($rsTmp)) {
									$i++;
									?>
									<div class="item">
										<div class="titolo"><?php echo $rowTmp["login"] ?> - <?php echo $rowTmp["nome"] ?> <?php echo $rowTmp["cognome"] ?></div>
										<div class="detail">Anno: <span class="value"><?php echo $rowTmp["anno"] ?></span></div>
										<div class="detail">Inserito il: <span class="value"><?php echo $objUtility->dateTimeShow($rowTmp["inserimento_data"], "short")?></span></div>
									</div>
									<?php
								}
								?>
			 				</div>
							</form>
							<?php
						}
						?>
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