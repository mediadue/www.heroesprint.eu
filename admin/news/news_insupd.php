<?php



include ("_docroot.php");
include (SERVER_DOCROOT . "logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$objNews = new News;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."news/news.php");

$strParam = strtolower($objUtility->sessionVarRead("action"));
$intId = $objUtility->sessionVarRead("idnews");
switch ($strParam) {
	case "ins":
	case "upd":
		if ($strParam == "upd")
		{
			$rs = $objNews->getDetails($conn, $intId);
			if (count($rs) > 0)
				list($key, $row) = each($rs);
		}
		else 
		{
			$row = array("idcategorie"=>$objUtility->sessionVarRead("idnewscat"));
		}
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
		<head>
			<?php $objHtml->adminHeadsection() ?>
			<?php $objHtml->adminHtmlEditor() ?>
			<script type="text/javascript" src="<?php echo $objUtility->getPathBackofficeResources() ?>calendar.js"></script>
			<script type="text/javascript">
			window.addEvent('domready', function() {
			 myCal = new Calendar({
			  datapubblicazione: { datapubblicazione: 'Y-m-d' },
			  datascadenza: { datascadenza: 'Y-m-d' }
			}, {pad:0.5, direction:0.5, offset:1, days:['D','L','M','M','G','V','S'], months:['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre']});
			}); 
			</script>
			<script type="text/javascript">
			<!--
			function checkForm(theform) {
				<?php $objJs->checkField("idcategorie", "select", "IDCATEGORIE", "idcategorie") ?>
				<?php $objJs->checkField("titolo", "text", "TITOLO", "titolo") ?>
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
					<?php $objHtml->adminPageTitle("News", "Inserimento dati") ?>
					<div id="body">
						<div class="inputdata">
							<form action="action.php" id="frm" name="frm" method="post" enctype="multipart/form-data" onsubmit="return checkForm(this)"/>
							<div class="elemento">
								<div class="label"><label for="idcategorie">categoria </label>* </div>
								<div class="value">
									<select name="idcategorie" size="1" class="default">
										<option value="0"></option>
										<?php
										$rs = $objNews->categorieGetList($conn, true);
										if (count($rs)) {
											while (list($key, $rowTmp) = each($rs)) { 
												?>
												<option value="<?php echo $rowTmp["id"] ?>"<?php echo ($rowTmp["id"] == $row["idcategorie"]) ? " selected=\"yes\"" : "" ?>><?php echo $rowTmp["nome"] ?></option>
												<?php
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("it") ?> <label for="titolo">titolo italiano </label>* </div>
								<div class="value"><input type="text" name="titolo" id="titolo" maxlength="255" class="text" value="<?php echo $row["titolo"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("en") ?> <label for="titoloen">titolo inglese </label>* </div>
								<div class="value"><input type="text" name="titoloen" id="titoloen" maxlength="255" class="text" value="<?php echo $row["titoloen"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("fr") ?> <label for="titolofr">titolo francese </label>* </div>
								<div class="value"><input type="text" name="titolofr" id="titolofr" maxlength="255" class="text" value="<?php echo $row["titolofr"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("es") ?> <label for="titoloes">titolo spagnolo </label>* </div>
								<div class="value"><input type="text" name="titoloes" id="titoloes" maxlength="255" class="text" value="<?php echo $row["titoloes"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("it") ?> <label for="abstract">abstract italiano </label> (descrizione breve) </div>
								<div class="value"><textarea name="abstract" rows="5" cols="40" class="default"><?php echo $row["abstract"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("en") ?> <label for="abstracten">abstract inglese </label> (descrizione breve) </div>
								<div class="value"><textarea name="abstracten" rows="5" cols="40" class="default"><?php echo $row["abstracten"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("fr") ?> <label for="abstractfr">abstract francese </label> (descrizione breve) </div>
								<div class="value"><textarea name="abstractfr" rows="5" cols="40" class="default"><?php echo $row["abstractfr"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("es") ?> <label for="abstractes">abstract spagnolo </label> (descrizione breve) </div>
								<div class="value"><textarea name="abstractes" rows="5" cols="40" class="default"><?php echo $row["abstractes"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("it") ?> <label for="testo">testo italiano </label> </div>
								<div class="value"><textarea name="testo" rows="15" cols="40" class="textEditor"><?php echo $row["testo"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("en") ?> <label for="testoen">testo inglese </label> </div>
								<div class="value"><textarea name="testoen" rows="15" cols="40" class="textEditor"><?php echo $row["testoen"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("fr") ?> <label for="testofr">testo francese </label> </div>
								<div class="value"><textarea name="testofr" rows="15" cols="40" class="textEditor"><?php echo $row["testofr"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><?php $objHtml->flag("es") ?> <label for="testoes">testo spagnolo </label> </div>
								<div class="value"><textarea name="testoes" rows="15" cols="40" class="textEditor"><?php echo $row["testoes"]; ?></textarea></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="link">link </label> (pagina da aprire quando si clicca sul titolo) </div>
								<div class="value"><input type="text" name="link" id="link" maxlength="255" class="text" value="<?php echo $row["link"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="imgthumb">anteprima immagine</label> </div>
								<div class="value">
									<?php if ($row["idimgthumb"])
									{
										$objUtility->showObject($conn, $row["idimgthumb"], true);
										?>
										<br/><input type="checkbox" name="imgthumb_del" value="1"/>&#160;Nessuna immagine<br/>
										<?php
									}
									?>
									<input type="file" name="imgthumb" id="imgthumb" maxlength="100" class="file"/><br/><br/>
								</div>
							</div>
							<div class="elemento">
								<div class="label"><label for="imgzoom">immagine (larghezza massima 470 pixel)</label> </div>
								<div class="value">
									<?php if ($row["idimgzoom"])
									{
										$objUtility->showObject($conn, $row["idimgzoom"]);
										?>
										<br/><input type="checkbox" name="imgzoom_del" value="1"/>&#160;Nessuna immagine<br/>
										<?php
									}
									?>
									<input type="file" name="imgzoom" id="imgzoom" maxlength="100" class="file"/><br/><br/>
								</div>
							</div>
							<div class="elemento">
								<div class="label"><label for="datapubblicazione">data pubblicazione</label> </div>
								<div class="value"><input id="datapubblicazione" name="datapubblicazione" type="text" value="<?php echo substr($row["datapubblicazione"], 0, 10) ?>" class="textsmall"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="datascadenza">data scadenza</label> </div>
								<div class="value"><input id="datascadenza" name="datascadenza" type="text" value="<?php echo substr($row["datascadenza"], 0, 10) ?>" class="textsmall"/></div>
							</div>
							<div class="elemento">
								<div class="label"><label for="ishidden">nascondi</label> </div>
								<div class="value"><input type="checkbox" name="ishidden" value="1"<?php echo ($row["ishidden"]) ? " checked=\"yes\"" : "" ?>"/></div>
							</div>							
							<div class="elemento">
								<div class="label"><label for="importanza">importanza</label> (ordinamento, se vuoto ordina per data inserimento)</div>
								<div class="value"><input type="text" name="importanza" id="ordine" maxlength="10" class="textsmall" value="<?php echo $row["importanza"] ?>"/></div>
							</div>
							<div class="elemento">
								<div class="value"><input type="submit" name="act_NEWS-INSUPD-DO" value="Salva" class="btn"/></div>
							</div>
							</form>
							<br/><br/>
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