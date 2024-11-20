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

if(isUserSystem($intIdutente)==0) $rls2=getAllRolesByUser($intIdutente); else $rls2=getAllRoles(); 

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

function select_onclick(id){
  currentElement = document.createElement("input");
  currentElement.setAttribute("type", "hidden");
  currentElement.setAttribute("name", "filtRoles");
  currentElement.setAttribute("value", id);
  document.refresh.appendChild(currentElement);
  
  document.refresh.submit();
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
			<?php $objHtml->adminPageTitle("Documenti", "") ?>
			<div id="body">
				<?php
				$idroles = ($_POST["idroles"] || ($_POST["idroles"] === "")) ? $_POST["idroles"] : $objUtility->sessionVarRead("search_documents_idroles");;
        $idusers = ($_POST["idusers"] || ($_POST["idusers"] === "")) ? $_POST["idusers"] : $objUtility->sessionVarRead("search_documents_idusers");
				$anno = ($_POST["anno"] || ($_POST["anno"] === "")) ? $_POST["anno"] : $objUtility->sessionVarRead("search_documents_anno");;
				$idtags = ($_POST["idtags"] || ($_POST["idtags"] === "")) ? $_POST["idtags"] : $objUtility->sessionVarRead("search_documents_idtags");
				$isSearching = ($_POST["cerca"] || ($_POST["cerca"] === "")) ? $_POST["cerca"] : $objUtility->sessionVarRead("search_documents_cerca");
				?>
				<div class="inputdata">
					<form action="action.php" method="post">
					<div class="elemento">
            <div class="ins">						
              <input type="image" name="act_DOCUMENTS-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="img"/>
  						<span>aggiungi</span>
  					</div>
					</div>
					</form>
					<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="refresh">
					<input type="hidden" name="cerca" value="1">
					<div class="elemento">
						<div class="label"><label for="idusers">gruppi </label></div>
						<div class="value">
							<select name="idroles" size="1" class="default" onchange="select_onclick(this.options[this.selectedIndex].value);">
								<option value=""></option>							
								<?php
                array_sort($rls2,"nome");
								for ($i=0; $i<count($rls2); $i++)
								{
									if($rls2[$i]['nome']!="developer") {
                    ?>
										<option value="<?php echo $rls2[$i]['id'] ?>"<?php echo ($rls2[$i]['id']==$idroles) ? " selected" : "" ?>><?php echo $rls2[$i]['nome'] ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
					</div>
          <div class="elemento">
						<div class="label"><label for="idusers">cliente </label></div>
						<div class="value">
							<select name="idusers" size="1" class="default">
								<option value=""></option>							
								<?php
								$idroleareariservata = $_POST['filtRoles'];
								$rsUsers = $objUsers->getRolesUsers($conn, $idroleareariservata);
                if (count($rsUsers))
								{
									//carico un array per ciascuna colonna da visualizzare, poi ordino
									$arrId = array();
									$arrCodicecliente = array();
									$arrRagionesociale = array();
									while (list($key, $rowUsers) = each($rsUsers))
									{
										$rsTmp = $objUsers->getDetails($conn, $rowUsers["id"]);
										if (count($rsTmp))
										{
											list($key, $rowTmp) = each($rsTmp);
											array_push($arrId, $rowTmp["id"]);
											array_push($arrCodicecliente, $rowTmp["codicecliente"]);
											array_push($arrRagionesociale, $rowTmp["ragionesociale"]);
										}
									}
									array_multisort($arrRagionesociale, SORT_ASC, $arrCodicecliente, SORT_ASC, $arrId, SORT_ASC);
									for ($i=0; $i<count($arrRagionesociale); $i++)
									{
										?>
										<option value="<?php echo $arrId[$i] ?>"<?php echo ($arrId[$i]==$idusers) ? " selected" : "" ?>><?php echo $arrRagionesociale[$i] ?><?php echo ($arrCodicecliente[$i]) ? " [".$arrCodicecliente[$i]."]" : "" ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
					</div>
					<div class="elemento">
						<div class="label"><label for="anno">anno </label></div>
						<div class="value">
							<select name="anno" size="1" class="default">
								<option value=""></option>							
								<?php
									$rs = $objDocuments->getAnni($conn, false, true);
									if (count($rs))
									{
										while (list($key, $rowTmp) = each($rs))
										{
											?>
											<option value="<?php echo $rowTmp["anno"] ?>"<?php echo ($rowTmp["anno"]==$anno) ? " selected" : "" ?>><?php echo $rowTmp["anno"] ?></option>
											<?php
										}
									}
								?>
							</select>
						</div>
					</div>
					<div class="elemento">
						<div class="label"><label for="idtags">categorie </label></div>
						<div class="value">
							<select name="idtags" size="1" class="default">
								<option value=""></option>	
								<?php
								$rs = $objDocuments->tagsGetRicerca($conn, false);
								if (count($rs))
								{									
									while (list($key, $rowTmp) = each($rs))
									{
										?>
										<option value="<?php echo $rowTmp["id"] ?>"<?php echo ($rowTmp["id"]==$idtags) ? " selected" : "" ?>><?php echo $rowTmp["nome"] ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
					</div>
					<div class="elemento">
						<div class="label">&nbsp;</div>
            <div class="value"><input type="submit" value="Cerca" class="btn"/></div>
					</div>
					</form>
				</div>
				<div class="esito">
					<?php
					if ($isSearching) {
						$objUtility->getAction($strAct, $intId);
						If ($strAct == "DOCUMENTS-PAGE-GOTO") {
							$intPage = (int) $intId;
						} else {
							$intPage = 1;
						}
						if ($intPage <= 0) $intPage=1;
						$objUtility->sessionVarUpdate("search_documents_cerca", "1");
						$objUtility->sessionVarUpdate("search_documents_idusers", $idusers);
						$objUtility->sessionVarUpdate("search_documents_anno", $anno);
						$objUtility->sessionVarUpdate("search_documents_idtags", $idtags);
						$objUtility->sessionVarUpdate("search_documents_page", $intPage);
						$rs = $objDocuments->getList($conn, $idusers, $anno, $idtags);
						if (count($rs)) {
							?>
							<div class="header">elementi selezionati: <b><?php echo count($rs) ?></b></div>
							<?php
							$intItemsTot = count($rs);
							$intItemsOnPage = 20;
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
								if (($i>=$intItemsBegin) && ($i<=$intItemsEnd)) 
								{
									$user=retRow("users",$row['idusers']);
                  $tagsList = "";
									$rsTmp = $objDocuments->tagsGetByDocument($conn, $row["id"]);
                  while (list($key, $rowTmp) = each($rsTmp))
										$tagsList .= $rowTmp["nome"] . ", ";
									if ($tagsList) $tagsList = substr($tagsList, 0, strlen($tagsList)-2);
									?>
									<div class="item">
										<div class="titolo"<?php echo ($row["ishidden"]) ? " style=\"color:#999;\"" : "" ?>><?php $objHtml->adminIco($row["originalname"]) ?>&nbsp;<?php echo $row["originalname"] ?></div>
                    <?php if ($row["ishidden"]) { ?>
											<div class="detail">Nascosto</div>
										<?php } ?>
										<div class="detail">Dimensione: <span class="value"><?php echo $objUtility->getFileSizeKb($objUtility->getPathResourcesPrivateAbsolute() . $row["nome"].".".$row["ext"]) ?> Kb</span></div>
										<?php if($user["ragionesociale"]!="") { ?><div class="detail">Rag. soc.: <span class="value"><?php echo $user["ragionesociale"] ?></span></div><? } ?>
										<?php if($user["nome"]!="" || $user["cognome"]!="") { ?><div class="detail">Nome: <span class="value"><?php echo $user["nome"]." ".$user["cognome"]; ?></span></div><? } ?>
                    <div class="detail">Anno: <span class="value"><?php echo $row["anno"] ?></span></div>
										<div class="detail">Categoria: <span class="value"><?php echo $tagsList ?>&nbsp;</span></div>
										<div class="detail">Inserito il: <span class="value"><?php echo $objUtility->dateTimeShow($row["inserimento_data"], "short")?></span></div>
										<?php $myrs=getTable("documents_emailsent","inserimento_data DESC","iddocuments='".$row["id"]."'"); 
										  while (list($key1, $rowTmp1) = each($myrs)) { ?>
                        <div class="detail" style="color:brown;"><?=$key1+1?>) Inviato il <span class="value" style="color:brown;"><?php echo $objUtility->dateTimeShow($rowTmp1["inserimento_data"], "short"); ?>&nbsp;</span></div>
                      <? } ?>
                    <div class="btn-box">
											<input type="image" name="act_DOCUMENTS-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="icoupd"/>
											<input type="image" name="act_DOCUMENTS-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="icodel" onClick="return confirmDelete()"/>
											<input type="image" name="act_DOCUMENTS-EMAIL-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_email.png" alt="invia per email" title="invia per email" class="icoshow"/>
											<a href="<?php echo $objUtility->getPathBackoffice() ?>object_download.php?id=<?php echo $row["idoggetti"] ?>" target="_blank"><img src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_download.png" alt="scarica"/></a>
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
								<div style="clear:both;"></div>
                <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
								<input type="hidden" name="cerca" value="1">
								<input type="hidden" name="idusers" value="<?php echo $objUtility->translateForHidden($idusers) ?>"/>
								<input type="hidden" name="anno" value="<?php echo $objUtility->translateForHidden($anno) ?>"/>
								<input type="hidden" name="idtags" value="<?php echo $objUtility->translateForHidden($idtags) ?>"/>
								<?php
								$objHtml->paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPage, "DOCUMENTS-PAGE-GOTO");
								?>
								</form>
								<?php
							}
						} else {
							?>
							
								<div class="message">
									Nessun elemento presente in archivio soddisfa i criteri di ricerca impostati
								</div>
							
							<?php
						}
					}
					?>
					<!--
					<form action="action.php" method="post">
					<div class="ins">						
						<input type="image" name="act_DOCUMENTS-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="img"/>
						aggiungi
					</div>
					</form>
					-->
					<div style="clear:both;">&nbsp;</div>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>