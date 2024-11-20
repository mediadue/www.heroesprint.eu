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
$objMenu->checkRights($conn, $intIdutente, $objUtility->getPathBackoffice()."documents/documents.php");

$rls=$objUsers->usersGetRolesEx($conn, $intIdutente);
$rls2= array();
while (list($key, $row) = each($rls)) {
  if($idroles=="") $idroles=$row['id'];
  array_push($rls2, $row['nome']);
}

$param = strtolower($objUtility->sessionVarRead("action"));
$id = $objUtility->sessionVarRead("iddoc");

$idusers=$_GET['idusers'];
$anno=$_GET['anno'];
$ishidden=$_GET['ishidden'];
if($ishidden=="true"){$ishidden="1";}else{$ishidden="";}

switch ($param) {
	case "ins":
	case "upd":
		if ($param == "upd")
		{
			$rs = $objDocuments->getDetails($conn, $id);
			if (count($rs) > 0)
			{
				list($key, $row) = each($rs);
			}
		}
		
		if($idusers!="") $row["idusers"]=$idusers;
		if($anno!="") $row["anno"]=$anno;
		if($ishidden!="") $row["ishidden"]=$ishidden;
		
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
		<head>
			<style>
			.elemento {margin-bottom:10px;}
			</style>
      <?php $objHtml->adminHeadsection() ?>
			<script type="text/javascript">
			<!--
      function genHref(tstr) {
        /*var idusers="";
        $("#idusers option:selected").each(function(){
          idusers=idusers+$(this).val()+";";  
        });
        
        tstr=tstr+"?idusers="+idusers+"&anno="+document.frm.anno.value.toString()+"&idtags="+document.frm.idtags.value.toString()+"&ishidden="+document.frm.ishidden.checked;  
        return(tstr);
        */
        $("#frm").submit();
      }
      
			//-->
			</script>
			
			<script type="text/javascript">
      $(document).ready(function() {
        function visFrame() {
          <?php if($param=="upd") echo "return true;"; ?>
          if( $("#idusers option:selected").length>0 && $("#anno").attr("value")!="" && $("#idtags option:selected").attr("value")!="" ) {
            $("#uploadFrame").show("slow");
          } else {
            $("#uploadFrame").hide("slow");   
          } 
        }
        
        $("#annolist").change(function() {
  				var theform = $("#frm");  
          var annoSelected = Math.floor($("#annolist option:selected").attr("value"));
  				if (annoSelected > 0) 
  				{
  					$("#anno").attr("value",annoSelected)
  					$("#annolist").get(0).selectedIndex = 0;
  				}
  				visFrame();
  			});

        $("#idusers").change(function() {
          visFrame();  
        });
        
        $("#idusers").click(function() {
          visFrame();  
        });
        
        $("#anno").change(function() {
          visFrame();  
        });
        
        $("#anno").blur(function() {
          visFrame();  
        });
        
        $("#idtags").change(function() {
          visFrame()  
        });

        $("#idroles").change(function() {
          var id=$("#idroles option:selected").attr("value");
          $("#post-roles input[name=filtRoles]").val(id);
          $("#post-roles").submit();    
        });
          
      }); 
      </script>
		</head>
		<body>
		<form action="" id="post-roles" name="post-roles" method="post" enctype="multipart/form-data" style="display:none;"><input type="hidden" name="filtRoles" value="" /></form>
    <div id="site">
			<?php $objHtml->adminHead() ?>
			<div id="content">
				<?php $objHtml->adminLeft($conn, $intIdutente,"documents/documents.php") ?>
				<div id="area">
					<?php $objHtml->adminPageTitle("Documenti", "Inserimento dati") ?>
					<div id="body">
						<div>
              <form action="action.php" id="frm" name="frm" method="post" enctype="multipart/form-data" style="padding-left:90px;" />
              <div class="elemento" style="">
    						<div class="label"><label for="idroles">Gruppo </label></div>
    						<div class="value">
    							<select name="idroles" id="idroles" size="1" class="default">
    								<option value=""></option>							
    								<?php
    								if(isUserSystem($intIdutente)==0) $rls2=getAllRolesByUser($intIdutente); else $rls2=getAllRoles(); 
                    array_sort($rls2,"nome");
    								for ($i=0; $i<count($rls2); $i++)
    								{
    									if($rls2[$i]['nome']!="developer") {
    									 ?><option value="<?php echo $rls2[$i]['id'] ?>"<?php echo ($rls2[$i]['id']==$_POST['filtRoles']) ? " selected" : "" ?>><?php echo $rls2[$i]['nome']; ?></option><?php
    									}
    								}
    								?>
    							</select>
    						</div>
    					</div>
              <div class="elemento" style="">
    						<div class="label"><label for="idusers">cliente </label></div>
    						<div class="value">
    							<select name="idusers[]" id="idusers" size="1" class="default" <?php if($param=="ins") { ?>MULTIPLE style="height:200px;width:400px;" <? } ?> >							
    								<?php
    								$idroleareariservata = $_POST['filtRoles'];
    								$rsUsers = $objUsers->getRolesUsers($conn, $idroleareariservata);
                    if (count($rsUsers)) {
                      $tmp_userid=array();
                      $tmp_users=array();
                      $prova=array();
                      while (list($key, $rowUsers) = each($rsUsers)) {
    										$user=getTable("users","","id='".$rowUsers['id']."'");
                        $user=$user[0];
                        if(!in_array($user['id'], $tmp_userid)) {    
                          if($user['ragionesociale']!=""){$user['ord'] = $user['ragionesociale'];}else{$user['ord'] = $user['nome']." ".$user['cognome'];}
                          array_push($tmp_userid,$user['id']);
                          array_push($tmp_users,$user);
    									  }
                      }
                      
                      array_sort($tmp_users,"ord");
                      
                      while (list($key, $user) = each($tmp_users)) {
                        ?><option value="<?php echo $user['id'] ?>"<?php echo ($user['id']==$row["idusers"]) ? " selected" : "" ?>><?php echo $user['ord']; ?><?php echo ($user['codicecliente']) ? " [".$user['codicecliente']."]" : "" ?></option><?php
    								  }
                    }
    								?>
    							</select>
    						</div>
    					</div>
							<div class="elemento">
								<div class="label"><label for="anno">anno</label> * </div>
								<div class="value">
									<select id="annolist" name="annolist" size="1" class="default"  style="margin:0 0 3px 0;">
										<option value=""></option>	
										<?php
										$today = date("Ymd", time());
										$todayY = substr($today, 0, 4);
										$yearMin = 1995;
										$yearMax = $todayY;
										for ($i=$yearMax; $i>=$yearMin; $i--) {
											?>
											<option value="<?php echo $i ?>"><?php echo $i ?></option>
											<?php
										}
										?>
									</select>
									<br>
									<input type="text" name="anno" id="anno" maxlength="50" class="textsmall" value="<?php echo $row["anno"] ?>"/>								
								</div>
							</div>
							<div class="elemento">
								<div class="label"><label for="idtags">categoria </label> * </div>
								<div class="value">
									<select name="idtags" id="idtags" size="1" class="default">
										<option value=""></option>							
										<?php
										$idList = "";
										if($_GET['idtags']!="") $idList = '|' . $_GET['idtags'] . '|';
										 
										if ($param != "ins")
										{
											$rs = $objDocuments->tagsGetByDocument($conn, $row["id"]);
											if (count($rs) > 0) 
											{
                        while (list($key, $rowTmp) = each($rs)) 
												{
													$idList .= '|' . $rowTmp["id"] . '|';
												}
											}
										}
										$rsTags = $objDocuments->tagsGetRicerca($conn, false);
										if (count($rsTags))
										{
											while (list($key, $rowTags) = each($rsTags))
											{
                        ?>
												<option value="<?php echo $rowTags["id"] ?>"<?php echo (strpos($idList, '|'.$rowTags["id"].'|') !== false) ? " selected=\"yes\"" : "" ?>><?php echo $rowTags["nome"] ?></option>
												<?php
											}
										}
										?>
									</select>
								</div>
							</div>
							<?php 
							if ($row["idoggetti"])
							{ 
								?>
								<div class="elemento">
									<div class="label">nome del file</div>
									<div class="value">
										<?php $objHtml->adminIco($row["originalname"]) ?>&nbsp;
										<?php
										if ($row["idoggetti"])
										{
											?>
											<a href="<?php echo $objUtility->getPathBackoffice() ?>object_download.php?id=<?php echo $row["idoggetti"] ?>" target="_blank"><?php echo $row["originalname"] ?></a>
											<?php
										}
										else 
										{
											echo $row["originalname"];
										}
										?>

									</div>
								</div>
								<div class="elemento">
									<div class="label">dimensione </div>
									<div class="value"><?php echo $objUtility->getFileSizeKb($objUtility->getPathResourcesPrivateAbsolute() . $row["nome"].".".$row["ext"]) ?> Kb</div>
								</div>
								<?php 
							}
							?>
							
              <!--
              <div class="elemento">
								<div class="label"><label for="file">sostituisci/inserisci file</label> </div>
								<div class="value"><input type="file" name="file" id="file" maxlength="100" class="file"/></div>
							</div>
							-->
							<?php 
							if ($param == "ins")
							{
								?>
								<!--
                <div class="elemento">
									<div class="label"><label for="file2">sostituisci/inserisci file</label> </div>
									<div class="value"><input type="file" name="file2" id="file2" maxlength="100" class="file"/></div>
								</div>
								<div class="elemento">
									<div class="label"><label for="file3">sostituisci/inserisci file</label> </div>
									<div class="value"><input type="file" name="file3" id="file3" maxlength="100" class="file"/></div>
								</div>
								<div class="elemento">
									<div class="label"><label for="file4">sostituisci/inserisci file</label> </div>
									<div class="value"><input type="file" name="file4" id="file4" maxlength="100" class="file"/></div>
								</div>
								<div class="elemento">
									<div class="label"><label for="file5">sostituisci/inserisci file</label> </div>
									<div class="value"><input type="file" name="file5" id="file5" maxlength="100" class="file"/></div>
								</div>
								-->
								

								<?php
							}
							?>
							<div class="elemento">
								<div class="value"><input type="checkbox" name="ishidden" id="ishidden" value="1"<?php echo ($row["ishidden"]) ? " checked=\"yes\"" : "" ?>"/>&nbsp;<span class="label"><label for="ishidden">nascondi all'utente</label></span></div>
							</div>
							<?php 
							if ($row["inserimento_data"])
							{ 
								?>
								<div class="elemento">
									<div class="label">Inserito il</div>
									<div class="value"><?php echo $objUtility->dateTimeShow($row["inserimento_data"], "short")?>&nbsp;</div>
								</div>
								<div class="elemento">
									<div class="label">Inserito da </div>
									<div class="value"><?php echo $row["inserimento_username"] ?>&nbsp;</div>
								</div>
								<?php 
							}
							?>
							<input type="hidden" name="act_DOCUMENTS-INSUPD-DO" />
							<? if($param=="upd") { ?>
                <div class="elemento">
  								<div class="value"><input type="submit" name="act_DOCUMENTS-INSUPD-DO" value="Salva" class="btn"/></div>
  							</div>
							<? } ?>
							</form>
							<br>
                <IFRAME id="uploadFrame" src="<?php echo $objUtility->getPathBackofficeAdmin()?>upload/index.php<? if($param=="upd") echo "?id=0"; ?>" width="600"  height="300" frameborder="0" style="overflow-y:auto;overflow-x:hide;display:none;">
                  [Il tuo programma utente non supporta i frame o è attualmente configurato
                  per non mostrare i frame.]
                </IFRAME>
						</div>
					</div>
				</div>
			</div>
			<?php $objHtml->adminFooter() ?>
		</div>
		</body>
		</html>
		<?php
		break;
}
?>