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

$idusers = $objUtility->sessionVarRead("idutenti");
$isSystem=$objUsers->isSystem($conn, $intIdutente);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<?php $objHtml->adminHeadsection() ?>
	<script type="text/javascript">
	<!--
	function confirmDelete() 
	{
		if (!(confirm("Cancellazione associazione tra utente e gruppo selezionato.\n\nSei sicuro di voler procedere?"))) 
			return false;
	}
	function checkForm() 
	{
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
		<?php $objHtml->adminLeft($conn, $intIdutente,"utenti/roles.php") ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("Utenti", "Gruppi associati",-1) ?>
			<div id="body">
				<?php
				$rs = $objUsers->getDetails($conn, $idusers);
				if (count($rs) > 0) {
					list($key, $row) = each($rs);
				}
				?>
				Utente: <b><?php echo $row["login"] ?></b> <? if(trim($row["nome"])!="" || trim($row["cognome"])!="") {echo "( ".$row["nome"]." ".$row["cognome"]." )";} ?><br/><br/>
				<div class="esito">
					<?php
					$rs = $objUsers->usersGetRoles($conn, $idusers, $isSystem);
					$rs = $objUsers->getGestione($_SESSION["user_id"],$rs,"roles");
          if (count($rs) > 0) 
					{
						?>
						<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()"/>
						<?php
						while (list($key, $row) = each($rs)) 
						{
							if($isSystem || $row["nome"]!="default") {
                ?>
                <div class="item">
  								<div class="titolo"><?php echo $row["nome"] ?></div>
  								<div class="btn-box">
  									<input type="image" name="act_UTENTI-ROLES-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="rimuovi associazione" title="rimuovi associazione" class="icodel" onClick="return confirmDelete()"/>
  								</div>
  							</div>
  							<?php
							}
						}
						?>
						</form>
						<div style="clear:both;"></div>
						<?php
					}
					else 
					{
						?>
						<div class="item" style="float:none;">
							<div class="message">L'utente non risulta associato ad alcun gruppo</div>
						</div>
						<?php
					}
					?>
					<form action="action.php" method="post">
					<div class="ins">						
						<input type="image" name="act_UTENTI-ROLES-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="img"/>
						<span>associa a gruppi</span>
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
