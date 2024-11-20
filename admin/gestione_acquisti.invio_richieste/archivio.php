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
$objMenu->checkRights($conn, $intIdutente);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>calendar.js"></script>
<script type="text/javascript">
<!--
window.addEvent('domready', function() {
	myCalOpen = new Calendar({open_datafrom:'Y-m-d', open_datato:'Y-m-d'}, {pad:0, offset:1, days:['D','L','M','M','G','V','S'], months:['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre']});
	myCalClose = new Calendar({close_datafrom:'Y-m-d', close_datato:'Y-m-d'}, {pad:0, offset:1, days:['D','L','M','M','G','V','S'], months:['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre']});
}); 
-->
</script>
<script language="JavaScript" type="text/javascript">
function checkForm(theform) {
	if (theform.datafrom.value != '') {
		<?php $objJs->checkField("datafrom", "data", "DATA INIZIO", "datafrom") ?>
	}	
	if (theform.datato.value != '') {
		<?php $objJs->checkField("datato", "data", "DATA FINE", "datato") ?>
	}
	return true;
}
function confirmDelete() {
	if (!(confirm("Cancellazione newsletter.\n\nSei sicuro di voler procedere?"))) {
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
			<?php $objHtml->adminPageTitle("Newsletter", "Archivio") ?>
			<div id="body">
				<?php
				$arrToday = getdate (time());
				$dataFrom = $_POST["datafrom"];
				$dataTo = $_POST["datato"];
				$isPostBack = (int) $_POST["postback"];
				
        ?>
				<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" onsubmit="return checkForm(this)"/>
				<input type="hidden" name="postback" value="1">
				<div class="inputdata">
					<div class="elemento">
						<span class="label"><label for="open_datafrom">data inizio </label></span>
						<span class="value"><input id="open_datafrom" name="datafrom" type="text" value="<?php echo $dataFrom ?>" class="textsmall"/></span>
					</div>
					<div class="elemento">
						<span class="label"><label for="open_datato">data fine </label></span>
						<span class="value"><input id="open_datato" name="datato" type="text" value="<?php echo $dataTo ?>" class="textsmall"/></span>
					</div>
					<div class="elemento">
						<span class="label">&nbsp;</span>
						<span class="value"><input type="submit" value="Cerca" class="btn"/></span>
					</div>
				</div>
				</form>
				<div class="esito">
					<?php
					if ($isPostBack)
					{
						//calcolo la pagina da visualizzare
						$objUtility->getAction($strAct, $intId);
						If ($strAct == "ARCHIVIO-PAGE-GOTO")
							$intPage = (int) $intId;
						else
							$intPage = 1;
						if ($intPage <= 0) $intPage=1;
	
						//faccio la query
						//$objUtility->sessionVarUpdate("search_catalogoarchivio_cerca", "1");
						//$objUtility->sessionVarUpdate("search_catalogoarchivio_datafrom", $strDataFrom);
						//$objUtility->sessionVarUpdate("search_catalogoarchivio_datato", $strDataTo);
						$rs = $objMailing->archivioGetList($conn, $dataFrom, $dataTo);
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
							$i=0;
							?>
							<form action="action.php" method="post">
							<?php
							while (list($key, $row) = each($rs))
							{ 
								$i++;
								if (($i>=$intItemsBegin) && ($i<=$intItemsEnd))
								{
									?>
									<div class="item">
										<div class="titolo"><?php echo $i ?>. Newsletter #<?php echo $row["id"] ?></div>
										<div class="detail">Creato il: <span class="value"><?php echo $objUtility->dateShow($row["inserimento_data"], "long") ?>&nbsp;</span></div>
										<div class="detail">Utente: <span class="value"><?php echo $row["inserimento_username"] ?></span>&nbsp;</div>
										<div class="btn-box">
											<input type="image" name="act_ARCHIVIO-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="icoupd"/>
											<input type="image" name="act_ARCHIVIO-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="icodel" onClick="return confirmDelete()"/>
										</div>
									</div>
									<?php
								}
							}
							?>
							</form>
							<?php
							if ($intPagesTot > 1)
							{
								?>
								<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
								<input type="hidden" name="postback" value="1">
								<input type="hidden" name="datafrom" value="<?php echo $objUtility->translateForHidden($_POST["datafrom"]) ?>"/>
								<input type="hidden" name="datato" value="<?php echo $objUtility->translateForHidden($_POST["datato"]) ?>"/>
								<?php
								if ($intPagesTot > 1) {
									$objHtml->paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPage, "ARCHIVIO-PAGE-GOTO");
								}
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