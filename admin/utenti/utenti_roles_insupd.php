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
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."utenti/roles.php");
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."utenti/utenti.php");
$isSystem=$objUsers->isSystem($conn, $intIdutente);

$idusers = $objUtility->sessionVarRead("idutenti");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<?php $objHtml->adminHeadsection() ?>
	<?php $objHtml->adminHtmlEditor() ?>
	<script type="text/javascript">
	<!--
	function checkForm() {
		var theform = document.frm;
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
			<?php $objHtml->adminPageTitle("Utenti", "Associa Gruppi") ?>
			<div id="body">
				<?php
				$rs = $objUsers->getDetails($conn, $idusers);
				if (count($rs) > 0) {
					list($key, $row) = each($rs);
				}
				?>
				Utente: <b><?php echo $row["login"] ?></b><? if($row["nome"]!="" || $row["cognome"]!="" ) { echo "("; }?><?php echo $row["nome"] ?> <?php echo $row["cognome"] ?><? if($row["nome"]!="" || $row["cognome"]!="" ) { echo ")"; }?><br/><br/>
				<?php
				$rs = $objUsers->usersGetRolesAvailable($conn, $idusers);
				$rs = $objUsers->getGestione($_SESSION["user_id"],$rs,"roles");
        if (count($rs))
				{						
					?>
					<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()"/>
					<div class="esito">
						<table cellspacing="0" class="default">
							<tr>
								<th scope="col">&nbsp;</th>
								<th scope="col" style="width:95%;">Gruppi disponibili</th>
							</tr>
							<?php
							$i=0;
							while (list($key, $row) = each($rs))
							{
								$i++;
								?>
								<?php $tmpSystem=$objUsers->isSystem($conn, $row["id"]); 
                if(!($isSystem==false && $tmpSystem==true) && ($row["nome"]!="default" || $isSystem==true)) {?>
                <tr>
									<td><input type="checkbox" name="id_<?php echo $i ?>" value="<?php echo $row["id"] ?>"/></td>
									<td><?php echo $row["nome"] ?></td>
								</tr>
								<?php
								}
							}
							?>
						</table>
						<input type="hidden" name="id_tot" value="<?php echo $i ?>"/>
					</div>
					<div class="inputdata">
						<div class="elemento">
							<div class="value"><input type="submit" name="act_UTENTI-ROLES-INS-DO" value="Salva" class="btn"/></div>
						</div>
					</div>
					</form>
					<?php
				}
				else 
				{
					?>
					<div class="esito">
						<div class="item" style="float:none;">
							<div class="message">Non ci sono ruoli disponibili per l'associazione</div>
						</div>
					</div>
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
