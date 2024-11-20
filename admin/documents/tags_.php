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
$objMenu->checkRights($conn, $intIdutente);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script language="JavaScript" type="text/javascript">
<!--
function confirmDelete()
{
	if (!(confirm("Cancellazione elemento selezionato.\n\nSei sicuro di voler procedere?")))
	{
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
			<?php $objHtml->adminPageTitle("Documenti", "Tags") ?>
			<div id="body">
				<?php
				$nome = $_POST["nome"];
				$isSearching = $_POST["cerca"];
				?>
				<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
				<input type="hidden" name="cerca" value="1">
				<div class="inputdata">
					<div class="elemento">
						<div class="label"><label for="nome">nome </label></div>
						<div class="value"><input type="text" name="nome" id="nome" maxlength="50" class="text" value="<?php echo $nome ?>"/></div>
					</div>
					<div class="elemento">
						<div class="value"><input type="submit" value="Cerca" class="btn"/></div>
					</div>
				</div>
				</form>
				<div class="esito">
					<?php
					if ($isSearching)
					{
						$objUtility->getAction($strAct, $intId);
						If ($strAct == "TAGS-PAGE-GOTO")
							$intPage = (int) $intId;
						else
							$intPage = 1;
						if ($intPage <= 0) $intPage=1;
						$rs = $objDocuments->tagsGetRicerca($conn, $nome);
						if (count($rs))
						{
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
							while (list($key, $row) = each($rs))
							{ 
								$i++;
								if (($i>=$intItemsBegin) && ($i<=$intItemsEnd))
								{
									?>
									<div class="item">
										<div class="titolo"<?php echo ($row["ishidden"]) ? " style=\"color:#999;\"" : "" ?>><?php echo $i ?>. <?php echo $row["nome"] ?></div>
										<div class="detail">Importanza: <span class="value"><?php echo $row["importanza"] ?></span></div>
										<div class="btn-box">
											<input type="image" name="act_TAGS-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="icoupd"/>
											<input type="image" name="act_TAGS-DEL-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="icodel" onClick="return confirmDelete()"/>
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
								<input type="hidden" name="nome" value="<?php echo $objUtility->translateForHidden($nome) ?>"/>
								<?php
								$objHtml->paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPage, "TAGS-PAGE-GOTO");
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
					<form action="action.php" method="post">
					<div class="ins">						
						<input type="image" name="act_TAGS-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="img"/>
						aggiungi
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