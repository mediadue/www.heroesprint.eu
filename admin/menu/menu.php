<?php



include ("_docroot.php");
include (SERVER_DOCROOT . "/logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objHtml = new Html;
$objJs = new Js;
$objMenu = new Menu;
$objObjects = new Objects;
$objUsers = new Users;
$objUtility = new Utility;
$conn = $objDb->connection($objConfig);

session_start();

$objUsers->getCurrentUser($intIdutente, $strUsername);
$objMenu->checkRights($conn, $intIdutente);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script language="JavaScript" type="text/javascript">
function confirmModDelete() {
	if (!(confirm("Cancellazione modulo.\n\nVerranno cancellate anche i menu relativi.\n\nSei sicuro di voler procedere?"))) {
		return false;
	}
}
function confirmMenDelete() {
	if (!(confirm("Cancellazione menu.\n\nSei sicuro di voler procedere?"))) {
		return false;
	}
}

function confirmModDeleteIco() {
	if (!(confirm("Procedere con la rimozione dell'icona?"))) {
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
			<?php $objHtml->adminPageTitle("Moduli", "") ?>
			<div id="body">
				<div class="container">
					<form action="action.php" method="post">
					<?php
					$intIdmod1 = $_GET["idmod1"];
					If (!$intIdmod1) {$intIdmod1 = $objUtility->sessionVarRead("idmod1");}
					$objUtility->sessionVarUpdate("idmod1", $intIdmod1);
					
					$intIdmod = $_GET["idmod"];
					If (!$intIdmod) {$intIdmod = $objUtility->sessionVarRead("idmod");}
					$objUtility->sessionVarUpdate("idmod", $intIdmod);

					$intIdmen = $_GET["idmen"];
					?>
					<div class="column" style="width:30%;">
						<table cellspacing="0" summary="Moduli" class="default">
							<!--<caption>Moduli</caption> -->
							<tr>
								<th scope="col" abbr="">&#160;</th>
								<th scope="col" abbr="">&#160;</th>
								<th scope="col" abbr="Moduli" style="width:90%;">Categorie Principali</th>
								<th scope="col" abbr="">&#160;</th>
								<th scope="col" abbr="">&#160;</th>
							</tr>
							<?php
							$rs = $objMenu->getMenuModuli1($conn, $intIdutente, true);
							if (count($rs)) {
								$i=0;
								while (list($key, $row) = each($rs)) {
									$i++;
									?>
									<tr>
										<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MODULI1-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="btnupd"/></td>
										<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MODULI1-DEL-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="btndel" onClick="return confirmModDelete()"/></td>
                    <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod1) ? " style=\"background: #86D7F7;\"" : "" ?>><a href="menu.php?idmod1=<?php echo $row["id"] ?>"><?php echo $row["titolo"] ?></a>&nbsp;&raquo;</td>
									  <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod1) ? " style=\"background: #86D7F7;\"" : "" ?>><?php if($rs[$i]['id']!="") { ?><input type="image" name="act_MODULI1-CATEGORIE-MOVEDOWN-DO_<?php echo $row["id"]."#".$rs[$i]['id'] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png";' alt="sposta in basso" title="sposta in basso" class="btnupd"/><? } ?></td>
										<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod1) ? " style=\"background: #86D7F7;\"" : "" ?>><?php if($rs[$i-2]['id']!="") { ?><input type="image" name="act_MODULI1-CATEGORIE-MOVEUP-DO_<?php echo $row["id"]."#".$rs[$i-2]['id']; ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png";' alt="sposta in alto" title="sposta in alto" class="btnupd"/><? } ?></td>
                  </tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td>&#160;</td>
									<td>&#160;</td>
									<td>(nessuno)</td>
									<td>&#160;</td>
									<td>&#160;</td>
								</tr>
								<?php
							}
							?>
							<tr>
								<td>&#160;</td>
								<td><input type="image" name="act_MODULI1-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="btnins"/></td>
								<td>&#160;</td>
								<td>&#160;</td>
								<td>&#160;</td>
							</tr>
						</table>
					</div>
					
          <div class="column" style="width:33%;margin-left:10px;">
						<?php if($intIdmod1!="") { ?>
            <table cellspacing="0" summary="Moduli" class="default">
							<!--<caption>Moduli</caption> -->
							<tr>
								<th scope="col" abbr="">&#160;</th>
								<th scope="col" abbr="">&#160;</th>
								<th scope="col" abbr="">&#160;</th>
								<th scope="col" abbr="Moduli" style="width:90%;">Moduli</th>
								<th scope="col" abbr="">&#160;</th>
								<th scope="col" abbr="">&#160;</th>
								<th scope="col" abbr="">&#160;</th>
							</tr>
							<?php
							$rs = $objMenu->getMenuModuli($conn, $intIdutente, true, $intIdmod1);
              if (count($rs)) {
								$i=0;
								$pmenu=0;
                while (list($key, $row) = each($rs)) {
									$i++;
									if($row["id"] == $intIdmod) $pmenu=1;
                  ?>
									<tr>
										<?php 
                    $rs2 = $objMenu->getMenu($conn, $row["id"], $intIdutente, true); 
                    $row2=$rs2[0];
                    if(($row["titolo"]==$row["testo"]) && $row["titolo"]=="-" && count($rs2)>0) {
                    if( $row["id"] == $intIdmod) $pmenu=0;
                    ?>
                      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MENU-UPD-GOTO_<?php echo $row2["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="btnupd"/></td>
											<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MENU-DEL-GOTO_<?php echo $row2["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="btndel" onClick="return confirmMenDelete()"/></td>
											<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><?php if($row2["icona_file"]!="0") { ?><input type="image" name="act_MENU-DELICO-GOTO_<?php echo $row2["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="elimina icona assegnata" class="btndel" onClick="return confirmModDeleteIco()"/><? } ?></td>
                      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod) ? " style=\"background: #86D7F7;\"" : "" ?>><?php echo $row2["nome"] ?></td>
											<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod) ? " style=\"background: #86D7F7;\"" : "" ?>><input type="image" name="act_MENU-ROLES-GOTO_<?php echo $row2["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ruoli.png" alt="ruoli associati" title="ruoli associati" class="btnupd"/></td>  
                    <? } else { ?>
                      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MODULI-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="btnupd"/></td>
										  <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MODULI-DEL-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="btndel" onClick="return confirmModDelete()"/></td>
                      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><?php if($row["icona_file"]!="0") { ?><input type="image" name="act_MODULI-DELICO-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="elimina icona assegnata" class="btndel" onClick="return confirmModDeleteIco()"/><? } ?></td>
                      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod) ? " style=\"background: #86D7F7\"" : "" ?>><a href="menu.php?idmod=<?php echo $row["id"] ?>&idmod1=<?php echo $intIdmod1 ?>"><?php echo $row["titolo"] ?></a>&nbsp;&raquo;</td>
                      <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod) ? " style=\"background: #86D7F7;\"" : "" ?>>&#160;</td>
                    <? } ?>
                    
                    <td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod) ? " style=\"background: #86D7F7;\"" : "" ?>><?php if($rs[$i]['id']!="") { ?><input type="image" name="act_MODULI-CATEGORIE-MOVEDOWN-DO_<?php echo $row["id"]."#".$rs[$i]['id'] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png";' alt="sposta in basso" title="sposta in basso" class="btnupd"/><? } ?></td>
										<td colspan="2" <?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdmod) ? " style=\"background: #86D7F7;\"" : "" ?>><?php if($rs[$i-2]['id']!="") { ?><input type="image" name="act_MODULI-CATEGORIE-MOVEUP-DO_<?php echo $row["id"]."#".$rs[$i-2]['id']; ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png";' alt="sposta in alto" title="sposta in alto" class="btnupd"/><? } ?></td>
                  </tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td>&#160;</td>
									<td>&#160;</td>
									<td>(nessuno)</td>
									<td>&#160;</td>
									<td>&#160;</td>
									<td>&#160;</td>
									<td>&#160;</td>
								</tr>
								<?php
							}
							?>
							<tr>
								<td>&#160;</td>
								<td><input type="image" name="act_MODULI-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="btnins"/></td>
								<td>&#160;</td>
								<td>&#160;</td>
								<td>&#160;</td>
								<td>&#160;</td>
								<td>&#160;</td>
							</tr>
						</table>
					<? } ?>
          </div>
					
					<div class="column" style="width:33%;margin-left:10px;">
						<?php
            if ($pmenu==1) {
							?>
							<table cellspacing="0" summary="Menu" class="default">
								<!--<caption>Menu</caption> -->
								<tr>
									<th scope="col" abbr="">&#160;</th>
									<th scope="col" abbr="">&#160;</th>
									<th scope="col" abbr="">&#160;</th>
									<th scope="col" abbr="Menu" style="width:90%;">Menu</th>
									<th scope="col" abbr="">&#160;</th>
									<th scope="col" abbr="">&#160;</th>
									<th scope="col" abbr="">&#160;</th>
									
								</tr>
								<?php
								$rs = $objMenu->getMenu($conn, $intIdmod, $intIdutente, true);
								if (count($rs)) {
									$j=0;
									while (list($key, $row) = each($rs)) {
										$j++;
										?>
										<tr>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MENU-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="btnupd"/></td>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MENU-DEL-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="btndel" onClick="return confirmMenDelete()"/></td>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><?php if($row["icona_file"]!="0") { ?><input type="image" name="act_MENU-DELICO-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="elimina icona assegnata" class="btndel" onClick="return confirmModDeleteIco()"/><? } ?></td>
                      <td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><?php echo $row["nome"] ?></td>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_MENU-ROLES-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ruoli.png" alt="ruoli associati" title="ruoli associati" class="btnupd"/></td>
										  <td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><?php if($rs[$j]['id']!="") { ?><input type="image" name="act_MENU-CATEGORIE-MOVEDOWN-DO_<?php echo $row["id"]."#".$rs[$j]['id'] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png";' alt="sposta in basso" title="sposta in basso" class="btnupd"/><? } ?></td>
										  <td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><?php if($rs[$j-2]['id']!="") { ?><input type="image" name="act_MENU-CATEGORIE-MOVEUP-DO_<?php echo $row["id"]."#".$rs[$j-2]['id']; ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png" onmouseover='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup_over.png";' onmouseout='this.src = "<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png";' alt="sposta in alto" title="sposta in alto" class="btnupd"/><? } ?></td>
                    </tr>
										<?php
									}
								} else {
									?>
									<tr class="detail">
										<td class="action">&#160;</td>
										<td class="action">&#160;</td>
										<td class="testo">(nessuno)</td>
										<td class="action">&#160;</td>
										<td class="action">&#160;</td>
										<td class="action">&#160;</td>
										<td class="action">&#160;</td>
									</tr>
									<?php
								}
								?>
								<tr>
									<td class="action">&#160;</td>
									<td class="action"><input type="image" name="act_MENU-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="btnins"/></td>
									<td>&#160;</td>
									<td>&#160;</td>
									<td>&#160;</td>
									<td>&#160;</td>
									<td>&#160;</td>
								</tr>
							</table>
							<?php
						}
						?>
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