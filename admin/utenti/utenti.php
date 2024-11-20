<?php
session_start();

header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");

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

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);
$isSystem=$objUsers->isSystem($conn, $intIdutente);

$isSearching = ($_POST["cerca"] || ($_POST["cerca"] === "")) ? $_POST["cerca"] : $objUtility->sessionVarRead("search_users_cerca");
$username = ($_POST["username"] || ($_POST["username"] === "")) ? $_POST["username"] : $objUtility->sessionVarRead("search_users_username");
$nome = ($_POST["nome"] || ($_POST["nome"] === "")) ? $_POST["nome"] : $objUtility->sessionVarRead("search_users_nome");
$cognome = ($_POST["cognome"] || ($_POST["cognome"] === "")) ? $_POST["cognome"] : $objUtility->sessionVarRead("search_users_cognome");
$email = ($_POST["email"] || ($_POST["email"] === "")) ? $_POST["email"] : $objUtility->sessionVarRead("search_users_email");
$ragionesociale = ($_POST["ragionesociale"] || ($_POST["ragionesociale"] === "")) ? $_POST["ragionesociale"] : $objUtility->sessionVarRead("search_users_ragionesociale");

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
			<?php $objHtml->adminPageTitle("Utenti", "") ?>
			<div id="body">
				<form action="action.php" method="post">
					<div class="ins">						
            <input type="image" name="act_UTENTI-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="img"/>
						<span>aggiungi</span>
					</div>
				</form>
				
        <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
  				<input type="hidden" name="cerca" value="1">
  				<div class="inputdata">
  					<div class="elemento">
  						<div class="label"><label for="titolo">username </label></div>
  						<div class="value"><input type="text" name="username" id="username" maxlength="20" class="text" value="<?php echo $username ?>"/></div>
  					</div>
  					<div class="elemento">
  						<div class="label"><label for="cognome">ragione sociale </label></div>
  						<div class="value"><input type="text" name="ragionesociale" id="ragionesociale" maxlength="50" class="text" value="<?php echo $ragionesociale ?>"/></div>
  					</div>
            <div class="elemento">
  						<div class="label"><label for="cognome">cognome </label></div>
  						<div class="value"><input type="text" name="cognome" id="cognome" maxlength="50" class="text" value="<?php echo $cognome ?>"/></div>
  					</div>
  					<div class="elemento">
  						<div class="label"><label for="nome">nome </label></div>
  						<div class="value"><input type="text" name="nome" id="nome" maxlength="50" class="text" value="<?php echo $nome ?>"/></div>
  					</div>
  					<div class="elemento">
  						<div class="label"><label for="email">email </label></div>
  						<div class="value"><input type="text" name="email" id="email" maxlength="50" class="text" value="<?php echo $email ?>"/></div>
  					</div>
  					<div class="elemento">
  						<div class="label">&nbsp;</div>
  						<div class="value"><input type="submit" value="Cerca" class="btn"/></div>
  					</div>
  				</div>
				</form>
				<div class="esito">

					<?php
					if ($isSearching) 
					{
						$objUtility->sessionVarUpdate("search_users_cerca", "1");
						$objUtility->sessionVarUpdate("search_users_username", $username);
						$objUtility->sessionVarUpdate("search_users_nome", $nome);
						$objUtility->sessionVarUpdate("search_users_cognome", $cognome);
						$objUtility->sessionVarUpdate("search_users_email", $email);
						$objUtility->sessionVarUpdate("search_users_ragionesociale", $ragionesociale);

						$objUtility->getAction($strAct, $intId);
						If ($strAct == "UTENTI-PAGE-GOTO") {
							$intPage = (int) $intId;
						} else {
							$intPage = 1;
						}
						if ($intPage <= 0) $intPage=1;

						$rs = $objUsers->getSearch($conn,$ragionesociale, $username, $nome, $cognome, $email, false, true);
						$rs = $objUsers->getGestione($_SESSION["user_id"],$rs,"users");
						
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
							?>
							<form action="action.php" method="post">
							<?php
							$i=0;
							while (list($key, $row) = each($rs)) { 
								$i++;
								if (($i>=$intItemsBegin) && ($i<=$intItemsEnd)) {
									?>
									<div class="item">
										<?php if($row["id"]!="") { ?><div class="detail">Codice: <span class="value"><?php echo $row["id"] ?></span></div><? } ?>
                    <?php if($row["login"]!="") { ?><div class="detail">Username: <span class="value"><?php echo $row["login"] ?></span></div><? } ?>
										<?php if($row["ragionesociale"]!="") { ?><div class="detail">Rag. soc.: <span class="value"><?php echo $row["ragionesociale"] ?></span></div><? } ?>
										<?php if($row["cognome"]!="") { ?><div class="detail">Cognome: <span class="value"><?php echo $row["cognome"] ?></span></div><? } ?>
                    <?php if($row["nome"]!="") { ?><div class="detail">Nome: <span class="value"><?php echo $row["nome"] ?></span></div><? } ?>
										<?php if($row["email"]!="") { ?><div class="detail">Email: <span class="value"><?php echo $row["email"] ?></span></div><? } ?>
										<?php
										$rsTmp = $objUsers->usersGetRoles($conn, $row["id"], true);
										$rsTmp = $objUsers->getGestione($_SESSION["user_id"],$rsTmp,"roles");
                    if (count($rsTmp) > 0) 
										{
											?>
											<div class="detail">
												Gruppi:
												<span class="value">
												<?php
												$j=0;
												while (list($key, $rowTmp) = each($rsTmp))
												{
													$j++;
													if($isSystem || $rowTmp["nome"]!="default") {
                            echo $rowTmp["nome"];
                          }
  												if ($j<(count($rsTmp))) {echo ",&nbsp;";}
												}
												?>
												</span>
											</div>
											<?php
										}
										?>
										<?php //$tmpSystem=$objUsers->isSystem($conn, $row["id"]); 
                    //if(!($isSystem==false && $tmpSystem==true)) { ?>
                    <div class="btn-box">
											<input type="image" name="act_UTENTI-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="icoupd"/>
											<input type="image" name="act_UTENTI-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="icodel" onClick="return confirmDelete()"/>
											<input type="image" name="act_UTENTI-ROLES-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ruoli.png" alt="gruppi associati" title="gruppi associati" class="icoins"/>
										  <input type="image" name="act_UTENTI-EMAIL-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_email.png" alt="gruppi associati" title="invia email/sms" class="icoins"/>
                    </div>
									  <?//} ?>
                  </div>
									<?php
								}
							}
							?>
							</form>
							<div style="clear:both;"></div>
              <?php
							if ($intPagesTot > 1) {
								?>
								<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
								<?php
								$objHtml->paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPage, "UTENTI-PAGE-GOTO");
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
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>