<?php 
if(isset($_GET["email"])) $email=$_GET["email"];
if(isset($_GET["idord"])) $idordine=$_GET["idord"];
?>

<!-- Plain box -->
<div class="ez-wr invio-file-container col-sm-12">
  <div class="ez-box invio-file-text">
    <?php echo $testi[0]['testo_editor']; ?>  
  </div>
  
  <div class="rsLoading">Loading...</div>
  <!-- Module 2A -->
  <div class="ez-wr invio-file-email invio-file-row ">
    <div class="ez-fl ez-negmr ez-50 invio-file-left ">
      <div class="ez-box"><?php echo ln("E-mail usata per il pagamento")."*"; ?></div>
    </div>
    <div class="ez-last ez-oh invio-file-right ">
      <div class="ez-box"><input class="invio-file-email form-control input-lg" type="text" value="<?php echo $email; ?>"/></div>
    </div>
  </div>
  
  <!-- Module 2A -->
  <div class="ez-wr invio-file-ordine invio-file-row ">
    <div class="ez-fl ez-negmr ez-50 invio-file-left ">
      <div class="ez-box"><?php echo ln("Codice ordine")."*"; ?></div>
    </div>
    <div class="ez-last ez-oh invio-file-right">
      <div class="ez-box"><input class="invio-file-ordine form-control input-lg" type="text" value="<?php echo $idordine; ?>" /></div>
    </div>
  </div> 
  
  <!-- Module 2A -->
  <div class="ez-wr invio-file-upload-container">
    <div class="ez-fl ez-negmr ez-50 invio-file-upload-left">
      <div class="ez-box"><div class="ez-box invio-file-submit"><input class="btn-invio-file-submit btn btn-primary btn-120" type="button" value="<?php echo ln("VAI"); ?>" /></div></div>
    </div>
    <div class="ez-last ez-oh invio-file-upload-right">
      <div class="ez-box">
	      <div class="ez-box invio-file-upload">
	      <form id="hrs_file_upload" style="width:100%;text-align:center;margin-left:auto;margin-right:auto;">
					<div id="queue"></div>
					<input id="file_upload" name="file_upload" type="file" multiple="true" class="file_upload-btn" >
				</form>
	      </div>
      </div>
    </div>
  </div>
	
  <div class="invio-file-table-title"><?php echo ln("I file da te caricati per l'ordine N° "); ?><span class="invio-file-table-title-num" style='color:#A01D21;font-weight:bold;'></span></div>
  <div class="invio-file-table"></div>
  <div class="invio-file-text">
  	<label for="invio-file-note"><?php echo ln("Inserire qui le indicazioni per la stampa (quantità per ogni file caricato, esigenze particolari ecc..)"); ?></label>
  	<textarea class="form-control spacing-xs" rows="4" cols="50" id="invio-file-note"></textarea>
  	<input class="btn-invio-file-note-save btn btn-success btn-120" style="margin-top:20px;" type="button" value="<?php echo ln("SALVA"); ?>" />
  </div>
</div>