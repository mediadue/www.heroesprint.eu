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

$isSearching = ($_POST["cerca"] || ($_POST["cerca"] === "")) ? $_POST["cerca"] : $objUtility->sessionVarRead("search_roles_cerca");
$nome = ($_POST["nome"] || ($_POST["nome"] === "")) ? $_POST["nome"] : $objUtility->sessionVarRead("search_roles_nome");
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
			<?php $objHtml->adminPageTitle("Gruppi", "") ?>
			<div id="body">
				<form action="action.php" method="post">
  				<div class="ins">						
  					<input type="image" name="act_ROLES-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="img"/>
  					<span>aggiungi</span>
  				</div>
				</form>
				
        <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
				<input type="hidden" name="cerca" value="1">
				<div class="inputdata">
					<div class="elemento">
						<div class="label"><label for="nome">nome </label></div>
						<div class="value"><input type="text" name="nome" id="nome" maxlength="50" class="text" value="<?php echo $nome ?>"/></div>
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
						$objUtility->sessionVarUpdate("search_roles_cerca", "1");
						$objUtility->sessionVarUpdate("search_roles_nome", $nome);

						$objUtility->getAction($strAct, $intId);
             
            If ($strAct == "ROLES-PAGE-GOTO") {
							$intPage = (int) $intId; 
						} else {
							$intPage = 1;
						}
						if ($intPage <= 0) $intPage=1;
	
						$rs = $objUsers->rolesGetSearch($conn, $nome, $isSystem);
						$rs = $objUsers->getGestione($_SESSION["user_id"],$rs,"roles");

            if (count($rs)) { ?>
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
									if($isSystem || $row["nome"]!="default") {
                    ?>
  									<div class="item">
  										<div class="titolo"><span class="detail">Gruppo:</span> <?php echo $row["nome"] ?><?php if ($row["issystem"]) { ?> <span class="detail">(System)</span><?php } ?></div>
  										<div class="detail">Id: <span class="value"><?php echo $row["id"] ?></span></div>
  										<?php $tmpSystem=$row["issystem"]; 
                      if(!($isSystem==false && $tmpSystem==true) || $row["issystem"]=='2') {?>
                      <div class="btn-box">
  											<?php if($row["issystem"]!='2'|| $isSystem) { ?>
                          <input type="image" name="act_ROLES-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="icoupd"/>
    											<input type="image" name="act_ROLES-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="icodel" onClick="return confirmDelete()"/>
    										<? } ?>
                        <input type="image" name="act_ROLES-USERS-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_utenti.png" alt="utenti associati" title="utenti associati" class="icoins"/>
  											<?php if($row["issystem"]!='2' || $isSystem) { ?>
                          <input type="image" name="act_ROLES-MENU-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_menu.png" alt="menu associati" title="menu associati" class="icoins"/>
  										  <? } ?>
                      </div>
  										<? } ?>
  									</div>
  									<?php
									}
								}
							}
							?>
							</form>
							<div style="clear:both;"></div>
              <?php
							if ($intPagesTot > 1) 
							{
								?>
								<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
								<?php
								$objHtml->paginazione($intItemsOnPage, $intItemsTot, $intPagesTot, $intPage, "ROLES-PAGE-GOTO");
								?>
								</form>
								<?php
							}
						} 
						else 
						{
							?>
							
								<div class="message">Nessun elemento presente in archivio soddisfa i criteri di ricerca impostati</div>
							
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