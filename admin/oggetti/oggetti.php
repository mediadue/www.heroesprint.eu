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
$objMenu->checkRights($conn, $intIdutente);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script language="JavaScript" type="text/javascript">
<!--
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
			<?php $objHtml->adminPageTitle("Oggetti", "") ?>
			<div id="body">
				<?php
				$strNome = $_POST["nome"];
				$strExt = $_POST["ext"];
				$strFilename = $_POST["filename"];
				$isSearching = $_POST["cerca"];
				?>
				<form action="oggetti.php" method="post">
				<input type="hidden" name="cerca" value="1">
				<div class="inputdata">
					<div class="elemento">
						<div class="label"><label for="nome">nome </label></div>
						<div class="value"><input type="text" name="nome" id="nome" maxlength="100" class="text" value="<?php echo $strNome ?>"/></div>
					</div>
					<div class="elemento">
						<div class="label"><label for="ext">estensione </label></div>
						<div class="value"><input type="text" name="ext" id="ext" maxlength="3" class="textsmall" value="<?php echo $strExt ?>"/></div>
					</div>
					<div class="elemento">
						<div class="label"><label for="filename">filename </label></div>
						<div class="value"><input type="text" name="filename" id="filename" maxlength="100" class="text" value="<?php echo $strFilename ?>"/></div>
					</div>
					<div class="elemento">
						<div class="value"><input type="submit" value="Cerca" class="btn"/></div>
					</div>
				</div>
				</form>
				<div class="esito">
					<?php
					if ($isSearching) {
						//calcolo la pagina da visualizzare
						$objUtility->getAction($strAct, $intId);
						If ($strAct == "NEWS-PAGE-GOTO") {
							$intPage = (int) $intId;
						} else {
							$intPage = 1;
						}
						if ($intPage <= 0) $intPage=1;
						//faccio la query
						$rs = $objObjects->getRicerca($conn, $strNome, $strExt, $strFilename);
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
							?>
							<form action="action.php" method="post">
							<?php
							$i=0;
							while (list($key, $row) = each($rs)) { 
								$i++;
								if (($i>=$intItemsBegin) && ($i<=$intItemsEnd)) {
									?>
									<div class="item">
										<div class="titolo"><?php echo $i ?>. <?php echo $row["nome"] ?></div>
										<div class="detail">Estensione: <span class="value"><?php echo $row["ext"] ?></span></div>
										<div class="detail">File: <span class="value"><?php echo $row["originalname"] ?></span></div>
										<div class="detail">File fisico: <span class="value"><?php echo $row["path"] ?></span></div>
										<div class="btn-box">
											<input type="image" name="act_OGGETTI-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="icoupd"/>
											<input type="image" name="act_OGGETTI-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="icodel" onClick="return confirmDelete()"/>
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
								<form action="oggetti.php" method="post">
								<input type="hidden" name="cerca" value="1">
								<input type="hidden" name="nome" value="<?php echo $objUtility->translateForHidden($strNome) ?>"/>
								<input type="hidden" name="estensione" value="<?php echo $objUtility->translateForHidden($strExt) ?>"/>
								<input type="hidden" name="filename" value="<?php echo $objUtility->translateForHidden($strFilename) ?>"/>
								<?php
								$objHtml->paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPage, "OGGETTI-PAGE-GOTO");
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
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>