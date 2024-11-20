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
$objMenu->checkRights($conn, $intIdutente);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script language="JavaScript" type="text/javascript">
function confirmDelete() {
	if (!(confirm("Cancellazione elemento selezionato.\n\nSei sicuro di voler procedere?"))) {
		return false;
	}
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
			<?php $objHtml->adminPageTitle("Clienti", "") ?>
			<div id="body">
				<?php
				$strUsername = $_POST["username"];
				$strCodicecliente = $_POST["codicecliente"];
				$strRagionesociale = $_POST["ragionesociale"];
				$strEmail = $_POST["email"];
				$isSearching = $_POST["cerca"];
				?>
				<div class="inputdata">
					<form action="action.php" method="post">
					<div class="elemento">
						<div class="value"><input type="submit" name="act_CLIENTI-INS-GOTO" value="Aggiungi" class="btn"/></div>
					</div>
					</form>
					<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
					<input type="hidden" name="cerca" value="1">
					<!--
					<div class="elemento">
						<div class="label"><label for="username">username </label></div>
						<div class="value"><input type="text" name="username" id="username" maxlength="20" class="text" value="<?php echo $strUsername ?>"/></div>
					</div>
					-->
					<div class="elemento">
						<div class="label"><label for="cognome">ragione sociale / cognome nome </label></div>
						<div class="value"><input type="text" name="ragionesociale" id="ragionesociale" maxlength="50" class="text" value="<?php echo $strRagionesociale ?>"/></div>
					</div>
					<div class="elemento">
						<div class="label"><label for="codicecliente">codice cliente </label></div>
						<div class="value"><input type="text" name="codicecliente" id="codicecliente" maxlength="20" class="text" value="<?php echo $strCodicecliente ?>"/></div>
					</div>
					<div class="elemento">
						<div class="label"><label for="email">email </label></div>
						<div class="value"><input type="text" name="email" id="email" maxlength="50" class="text" value="<?php echo $strEmail ?>"/></div>
					</div>
					<div class="elemento">
						<div class="value"><input type="submit" value="Cerca" class="btn"/></div>
					</div>
					</form>
				</div>
				<div class="esito">
					<?php
					if ($isSearching) {
						$objUtility->getAction($strAct, $intId);
						If ($strAct == "CLIENTI-PAGE-GOTO") {
							$intPage = (int) $intId;
						} else {
							$intPage = 1;
						}
						if ($intPage <= 0) $intPage=1;
						$idroleareariservata = $objConfig->get("role-areariservata");
						//$rs = $objUsers->getRicerca($conn, $strUsername, $strCodicecliente, $strRagionesociale, $strEmail, $idroleareariservata);
						$rs = $objClienti->getSearch($conn, $strUsername, $strCodicecliente, $strRagionesociale, $strEmail, $idroleareariservata);
						if (count($rs)) {
							?>
							<div class="header">elementi selezionati: <b><?php echo count($rs) ?></b></div>
							<?php
							$intItemsTot = count($rs);
							$intItemsOnPage = 10;
							$intPagesTot = Ceil($intItemsTot / $intItemsOnPage);
							If ($intPage > $intPagesTot) $intPage = $intPagesTot;
							$intItemsBegin = ($intPage - 1) * $intItemsOnPage + 1;
							if (($intPage * $intItemsOnPage) <= ($intItemsTot)) {
								$intItemsEnd = ($intPage * $intItemsOnPage);
							} else {
								$intItemsEnd=$intItemsTot;
							}
							$i=0;
							?>
							<form action="action.php" method="post">
							<?php
							while (list($key, $row) = each($rs)) { 
								$i++;
								if (($i>=$intItemsBegin) && ($i<=$intItemsEnd)) {
									?>
									<div class="item">
										<div class="titolo"><?php echo $row["ragionesociale"] ?></div>
										<div class="detail">Codice cliente: <span class="value"><?php echo $row["codicecliente"] ?></span></div>
										<div class="detail">Email: <span class="value"><?php echo $row["email"] ?></span></div>
										<div class="detail">Telefono: <span class="value"><?php echo $row["telefono"] ?></span></div>
										<div class="btn-box">
											<input type="image" name="act_CLIENTI-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="icoupd"/>
											<input type="image" name="act_CLIENTI-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="icodel" onClick="return confirmDelete()"/>
										</div>
									</div>
									<?php
								}
							}
							?>
							</form>
							<?php
							if ($intPagesTot > 1) {
								?>
								<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
								<input type="hidden" name="cerca" value="1">
								<input type="hidden" name="username" value="<?php echo $objUtility->translateForHidden($strUsername) ?>"/>
								<input type="hidden" name="nome" value="<?php echo $objUtility->translateForHidden($strNome) ?>"/>
								<input type="hidden" name="cognome" value="<?php echo $objUtility->translateForHidden($strCognome) ?>"/>
								<input type="hidden" name="email" value="<?php echo $objUtility->translateForHidden($strEmail) ?>"/>
								<?php
								$objHtml->paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPage, "CLIENTI-PAGE-GOTO");
								?>
								</form>
								<?php
							}
						} else {
							?>
							<div class="item">
								<div class="message">
									Nessun elemento presente in archivio soddisfa i criteri di ricerca impostati
								</div>
							</div>
							<?php
						}
					}
					?>
					<!--
					<form action="action.php" method="post">
					<div class="ins">						
						<input type="image" name="act_CLIENTI-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="img"/>
						aggiungi
					</div>
					</form>
					-->
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>