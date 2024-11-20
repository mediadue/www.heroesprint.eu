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
//$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."menu/menu.php");
$isSystem=$objUsers->isSystem($conn, $intIdutente);

$intId = $objUtility->sessionVarRead("idroles");
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
		<?php $objHtml->adminLeft($conn, $intIdutente,"utenti/roles.php") ?>
		<div id="area">
			<?php $objHtml->adminPageTitle("Gruppi", "Menù Associati") ?>
			<div id="body">
				<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()"/>
				<?php
				$rs = $objUsers->rolesGetDetails($conn, $intId);
				if (count($rs) > 0) {
					list($key, $row) = each($rs);
				}
				?>
				Gruppo: <b><?php echo $row["nome"] ?></b><br/><br/>
				
				<div class="esito">
					<?php
					$rs = $objUsers->getRolesMenu($conn, $intId);
					$strMenu = "";
					if (count($rs) > 0) {
						while (list($key, $row) = each($rs)) {
							$strMenu .= '|' . $row["id"] . '|';
						}
					}
					?>
					<div class="header">Menu associati</div>
					<?php
					$rsModuli = $objMenu->getMenuModuli($conn, $intIdutente, false);
					if (count($rsModuli))
					{
						$i=0;
						?>
						<table cellspacing="0" class="default">
							
							<?php
							while (list($key, $rowModuli) = each($rsModuli))
							{
								$cc=retRow("menu_categorie",$rowModuli["idcategorie"]);
                $tit=$rowModuli["titolo"];
                if($rowModuli["titolo"]=="-") $tit="";
                if($tit!="") $cc['testo'].=" > "; 
                ?>
								<tr>
									<td class="alt" colspan="2"><b><?php echo $cc['testo'].$tit ?></b></td>
								</tr>
								<?php
								$rsMenu = $objMenu->getMenu($conn, $rowModuli["id"], $intIdutente, false);
								if (count($rsMenu))
								{
									while (list($key, $rowMenu) = each($rsMenu))
									{
										$i++;
 
                    $tmpSystem=$rowMenu['issystem']; 
                    if(!($isSystem==false && $tmpSystem=='1')) { ?>
                    <tr>
											<td><input type="checkbox" name="id_<?php echo $i ?>" value="<?php echo $rowMenu["id"] ?>" <?php echo (strpos($strMenu, '|'.$rowMenu["id"].'|') !== false) ? " checked=\"yes\" " : "" ?>/></td>
											<td><?php echo $rowMenu["nome"] ?></td>
										</tr>
										<?php
										}
									}
								}
							}
							?>
						</table>
						<?php } ?>
					<input type="hidden" name="id_tot" value="<?php echo $i ?>"/>
				</div>
				<div class="inputdata">
					<div class="elemento">
						<div class="value"><input type="submit" name="act_ROLES-MENU-DO" value="Salva" class="btn"/></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>
