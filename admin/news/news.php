<?php



require_once("_docroot.php");
require_once(SERVER_DOCROOT."logic/class_config.php");
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
$objMenu->checkRights($conn, $intIdutente);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php $objHtml->adminHeadsection() ?>
<script language="JavaScript" type="text/javascript">
<!--
function confirmCatDelete() 
{
	if (!(confirm("Cancellazione categoria.\n\nVerranno cancellate anche le news relative.\n\nSei sicuro di voler procedere?")))
	{
		return false;
	}
}
function confirmNewsDelete() 
{
	if (!(confirm("Cancellazione news.\n\nSei sicuro di voler procedere?")))
	{
		return false;
	}
}
function NewsMoveUp(row)
{
	var curform = document.frm;
	idsource = eval("curform.id"+row+".value");
	rowdest = row-1;
	iddest = eval("curform.id"+rowdest+".value");
	document.location = 'lstprod.php?act=order&ids='+idsource+'&idd='+iddest;
}
function NewsMoveDown(row)
{
	var curform = document.frm;
	idsource = eval("curform.id"+row+".value");
	rowdest = row+1;
	iddest = eval("curform.id"+rowdest+".value");
	document.location = 'lstprod.php?act=order&ids='+idsource+'&idd='+iddest;
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
			<?php $objHtml->adminPageTitle("News", "Categorie") ?>
			<div id="body">
				<div class="container">
					<form action="action.php" name="frm" method="post">
					<?php
					$intIdcat = $_GET["idcat"];
					If (!$intIdcat) {$intIdcat = $objUtility->sessionVarRead("idprodcat");}
					$objUtility->sessionVarUpdate("idprodcat", $intIdcat);
					?>
					<div class="column" style="width:29.5%;">
						<table cellspacing="0" summary="Categorie" class="default">
							<tr>
								<!--<th scope="col" abbr="">&#160;</th>-->
								<!--<th scope="col" abbr="">&#160;</th>-->
								<th scope="col" abbr="Categorie" style="width:90%;"><!--<i>(imp)</i>&nbsp;-->Categorie</th>
							</tr>
							<?php
							$rs = $objNews->categorieGetList($conn, true); //isfull
							if (count($rs))
							{
								$i=0;
								while (list($key, $row) = each($rs))
								{
									$i++;
									?>
									<tr>
										<!--<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_CATEGORIE-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="btnupd"/></td>-->
										<!--<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_CATEGORIE-DEL-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="btndel" onClick="return confirmCatDelete()"/></td>-->
										<td<?php echo (($i % 2) == 0) ? " class=\"alt\"" : "" ?><?php echo ($row["id"] == $intIdcat) ? " style=\"background: #86D7F7;\"" : "" ?>><!--<i>(<?php echo $row["importanza"] ?>)</i>&nbsp;--><a href="news.php?idcat=<?php echo $row["id"] ?>"><?php echo $row["nome"] ?></a>&#160;&#187;</td>
									</tr>
									<?php
								}
							} 
							else 
							{
								?>
								<tr>
									<!--<td>&#160;</td>-->
									<!--<td>&#160;</td>-->
									<td>(nessuno)</td>
								</tr>
								<?php
							}
							?>
							<!--
							<tr>
								<td>&#160;</td>
								<td><input type="image" name="act_CATEGORIE-INS-GOTO" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="btnins"/></td>
								<td>&#160;</td>
							</tr>
							-->
						</table>
					</div>
					<div class="column" style="float:right; width:69.5%;">
						<?php
						if ($intIdcat)
						{
							?>
							<table cellspacing="0" summary="News" class="default">
								<tr>
									<th scope="col" abbr="">&nbsp;</th>
									<th scope="col" abbr="">&nbsp;</th>
									<th scope="col" abbr="News" style="width:90%;">News&nbsp;<i>(data inserimento)</i></th>
									<th scope="col" abbr="">&nbsp;</th>
									<th scope="col" abbr="">&nbsp;</th>
								</tr>
								<?php
								$rs = $objNews->getList($conn, $intIdcat, true); //idcat, isfull
								if (count($rs)) {
									$j=0;
									while (list($key, $row) = each($rs)) 
									{
										$j++;
										?>
										<tr>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_NEWS-UPD-GOTO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_upd.png" alt="visualizza/modifica" title="visualizza/modifica" class="btnupd"/></td>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>><input type="image" name="act_NEWS-DEL-DO_<?php echo $row["id"] ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_del.png" alt="cancella" title="cancella" class="btndel" onClick="return confirmNewsDelete()"/></td>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>>												
												<?php echo $row["titolo"] ?>&nbsp;
												<i>(<?php echo $objUtility->dateShow($row["inserimento_data"], "short")?>)</i>
												<input type="hidden" name="id<?=$j?>" value="<?=$row["id"]?>"?>
											</td>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>>												
												<?php 
												if ($j>1 && (count($rs)!=1))
												{
													?>
													<input type="image" name="act_NEWS-MOVEUP-DO_<?php echo $j ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_moveup.png" alt="sposta in alto" title="sposta in alto" class="btnupd"/>
													<?php
												}
												else 
												{
													?>
													&nbsp;
													<?php
												}
												?>
											</td>
											<td<?php echo (($j % 2) == 0) ? " class=\"alt\"" : "" ?>>
												<?php 
												if ($j < count($rs))
												{
													?>
													<input type="image" name="act_NEWS-MOVEDOWN-DO_<?php echo $j ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_movedown.png" alt="sposta in basso" title="sposta in basso" class="btnupd"/>
													<?php
												}
												else 
												{
													?>
													&nbsp;
													<?php
												}
												?>
											</td>
										</tr>
										<?php
									}
								} else {
									?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="3">(nessuna)</td>
									</tr>
									<?php
								}
								?>
								<tr>
									<td>&#160;</td>
									<td><input type="image" name="act_NEWS-INS-GOTO_<?php echo $intIdcat ?>" src="<?php echo $objUtility->getPathBackofficeResources() ?>ico_ins.png" alt="aggiungi" title="aggiungi" class="btnins"/></td>
									<td colspan="3">&nbsp;</td>
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