<?php
session_start();

header("Expires: Sun, 3 Dec 2000 00:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/html; charset=windows-1252");

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
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."menu/menu.php");
//$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."utenti/roles.php");
$isSystem=$objUsers->isSystem($conn, $intIdutente);

$intIdmen = $objUtility->sessionVarRead("idmen");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<?php $objHtml->adminHeadsection() ?>
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
			<?php $objHtml->adminPageTitle("Menu", "Ruoli") ?>
			<div id="body">
				<form action="action.php" id="frm" name="frm" method="post" onsubmit="return checkForm()"/>
				<?php
				$rs = $objMenu->getMenuDetails($conn, $intIdmen);
				if (count($rs) > 0) {
					list($key, $row) = each($rs);
				}
				?>
				Menu: <b><?php echo $row["nome"] ?></b><br/><br/>
				
				<div class="esito">
					<?php
					$rs = $objMenu->getMenuRoles($conn, $intIdmen);
					$strRoles = "";
					if (count($rs) > 0) {
						while (list($key, $row) = each($rs)) {
							$strRoles .= '|' . $row["id"] . '|';
						}
					}
					?>
					<div class="header">Ruoli associati</div>
					<?php
					$rs = $objUsers->rolesGetSearch($conn, false, true); //nome, isfull
					if (count($rs)) 
					{
						$i=0;
						?>
						<table cellspacing="0" class="default">
							<tr>
								<th scope="col">&#160;</th>
								<th scope="col" style="width:95%;">Ruolo</th>
							</tr>
							<?php
							while (list($key, $row) = each($rs)) 
							{
								$i++;
								?>
								<tr>
									  
                  <?php
                  $hidden="hidden"; 
                  if(!($isSystem==false && $row["issystem"]=='1')) $hidden="checkbox"; ?>
                  <td><input type="<?=$hidden?>" name="id_<?php echo $i ?>" value="<?php echo $row["id"] ?>" <?php echo (strpos($strRoles, '|'.$row["id"].'|') !== false) ? " checked=\"yes\"" : "" ?> />&#160;</td>
									<td><?php echo $row["nome"] ?></td>
								</tr>
								<?php
							}
							?>
						</table>
						<?php
					}
					?>
					<input type="hidden" name="id_tot" value="<?php echo $i ?>"/>
				</div>
				<div class="inputdata">
					<div class="elemento">
						<div class="value"><input type="submit" name="act_MENU-ROLES-DO" value="Salva" class="btn"/></div>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
	<?php $objHtml->adminFooter() ?>
</div>
</body>
</html>