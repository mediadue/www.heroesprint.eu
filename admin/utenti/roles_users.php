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
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."utenti/utenti.php");
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."utenti/roles.php");

$idroles = $objUtility->sessionVarRead("idroles");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<?php $objHtml->adminHeadsection() ?>
	<?php $objHtml->adminHtmlEditor() ?>
	<script type="text/javascript">
	<!--
	function confirmDelete() 
	{
		if (!(confirm("Cancellazione associazione tra gruppo e utente selezionato.\n\nSei sicuro di voler procedere?")))
			return false;
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
			<?php $objHtml->adminPageTitle("Utenti", "Gruppi") ?>
			<div id="body">
				<?php
				$rs = $objUsers->rolesGetDetails($conn, $idroles);
				if (count($rs) > 0)
					list($key, $row) = each($rs);
				?>
				Gruppo: <b><?php echo $row["nome"] ?></b><br/><br/>
				
				<div class="esito">
					<?php
					$objUtility->getAction($strAct, $intId);
					If ($strAct == "ROLES-USERS-PAGE-GOTO")
						$intPage = (int) $intId;
					else
						$intPage = 1;
					if ($intPage <= 0) $intPage=1;

					$rs = $objUsers->rolesGetUsers($conn, $idroles);
					if (count($rs)) 
					{
						?>
						<div class="header">utenti associati: <b><?php echo count($rs) ?></b></div>
						<?php
						$intItemsTot = count($rs);
						$intItemsOnPage = 10;
						$intPagesTot = Ceil($intItemsTot / $intItemsOnPage);
						If ($intPage > $intPagesTot) $intPage = $intPagesTot;
						$intItemsBegin = ($intPage - 1) * $intItemsOnPage + 1;
						if (($intPage * $intItemsOnPage) <= ($intItemsTot))
							$intItemsEnd = ($intPage * $intItemsOnPage);
						else
							$intItemsEnd=$intItemsTot;
						?>
						<form action="action.php" method="post">
						<?php
						$i=0;
						while (list($key, $row) = each($rs)) 
						{ 
							$i++;
							if (($i>=$intItemsBegin) && ($i<=$intItemsEnd)) 
							{
								$rsTmp = $objUsers->getDetails($conn, $row["id"]);
								if (count($rsTmp) > 0)
									list($key, $rowTmp) = each($rsTmp);									
								?>
								<div class="item">
									<div class="detail">Codice: <span class="value"><?php echo $rowTmp["id"] ?></span></div>
                  <div class="detail">Username: <span class="value"><?php echo $rowTmp["login"] ?></span></div>
									<div class="detail">Ragione sociale: <span class="value"><?php echo $rowTmp["ragionesociale"] ?></span></div>
									<div class="detail">Cognome: <span class="value"><?php echo $rowTmp["cognome"] ?></span></div>
                  <div class="detail">Nome: <span class="value"><?php echo $rowTmp["nome"] ?></span></div>
									<div class="detail">Email: <span class="value"><?php echo $rowTmp["email"] ?></span></div>
									<?php
									$rsTmp = $objUsers->usersGetRoles($conn, $row["id"]);
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
												echo $rowTmp["nome"];
												if ($j<(count($rsTmp))) {echo ",&nbsp;";}
											}
											?>
											</span>
										</div>
										<?php
									}
									?>
									<div class="btn-box">
										<input type="image" name="act_UTENTI-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="icoupd"/>
										<input type="image" name="act_ROLES-USERS-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella associazione" title="cancella associazione" class="icodel" onClick="return confirmDelete()"/>
										<input type="image" name="act_ROLES-UTENTI-INS-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ruoli.png" alt="associa altri gruppi" title="associa altri gruppi" class="icoins"/>
									</div>
								</div>
								
								<?php
							}
						}
						?><div style="clear:both;"></div><?
            if ($intPagesTot > 1) 
						{
							?>
              <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
							<?php
							$objHtml->paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPage, "ROLES-USERS-PAGE-GOTO");
							?>
							</form>
							
							<?php
						}
					} 
					else 
					{
						?>
							<div class="message">
								Nessun utente risulta associato al gruppo scelto
							</div>
						<?php
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
