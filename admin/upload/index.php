<?php



include ("_docroot.php");

include (SERVER_DOCROOT . "logic/class_config.php");
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

	if (count($_FILES)) {
        // Handle degraded form uploads here.  Degraded form uploads are POSTed to index.php.  SWFUpload uploads
		// are POSTed to upload.php
	}

  $_SESSION['tmp_arrOggetti']=array();
  $_SESSION['tmp_uploader_n']="";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title></title>
<link href="css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="swfupload/swfupload.js"></script>
<script type="text/javascript" src="js/swfupload.queue.js"></script>
<script type="text/javascript" src="js/fileprogress.js"></script>
<script type="text/javascript" src="js/handlers.js"></script>
<script type="text/javascript">
		var upload1, upload2;
    var retField = "<?php echo $_GET['retField']; ?>";
    var retFileID = "";
    
		window.onload = function() {
      upload1 = new SWFUpload({
				// Backend Settings
				
        upload_url: "<?php echo $objUtility->getPathBackofficeAdmin() ?>upload/upload.php<? echo "?tblparent=".urlencode($_GET['tblparent'])."&parent=".$_GET['parent']."&row=".$_GET['row']."&id=".$_GET['id']."&table=".$_GET['table']."&field=".$_GET['field']; ?>",	// Relative to the SWF file (or you can use absolute paths)
				post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},

				// File Upload Settings
				file_size_limit : "102400",	// 100MB
				file_types : "*.*",
				file_types_description : "All Files",
				file_upload_limit : "<?php if($_GET['id']=="" || $_GET['id']=="0") {echo "20";}else{echo "1";} ?>",
				file_queue_limit : "0",

				// Event Handler Settings (all my handlers are in the Handler.js file)
				file_dialog_start_handler : fileDialogStart,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				<?php if($_GET['UploadComplete']=="") { ?>
          upload_complete_handler : uploadComplete,
        <? } else { ?>
          upload_complete_handler : <?php echo $_GET['UploadComplete']; ?>, 
        <? } ?>

				// Button Settings
				button_image_url : "XPButtonUploadText_61x22.png",	// Relative to the SWF file
				button_placeholder_id : "spanButtonPlaceholder1",
				button_width: 61,
				button_height: 22,
				
				// Flash Settings
				flash_url : "swfupload/swfupload.swf",
				

				custom_settings : {
					progressTarget : "fsUploadProgress1",
					cancelButtonId : "btnCancel1"
				},
				
				// Debug Settings
				debug: false
			});
				upload2 = new SWFUpload({
				// Backend Settings
				upload_url: "upload.php<? echo "?tblparent=".$_GET['tblparent']."&parent=".$_GET['parent']."&row=".$_GET['row']."&id=".$_GET['id']."&table=".$_GET['table']."&field=".$_GET['field']; ?>",	// Relative to the SWF file (or you can use absolute paths)
				post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},

				// File Upload Settings
				file_size_limit : "200",	// 200 kb
				file_types : "*.jpg;*.gif;*.png",
				file_types_description : "Image Files",
				file_upload_limit : "10",
				file_queue_limit : "5",

				// Event Handler Settings (all my handlers are in the Handler.js file)
				file_dialog_start_handler : fileDialogStart,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				// Button Settings
				button_image_url : "XPButtonUploadText_61x22.png",	// Relative to the SWF file
				button_placeholder_id : "spanButtonPlaceholder2",
				button_width: 61,
				button_height: 22,
				
				// Flash Settings
				flash_url : "swfupload/swfupload.swf",

				swfupload_element_id : "flashUI2",		// Setting from graceful degradation plugin
				degraded_element_id : "degradedUI2",	// Setting from graceful degradation plugin

				custom_settings : {
					progressTarget : "fsUploadProgress2",
					cancelButtonId : "btnCancel2"
				},

				// Debug Settings
				debug: false
			});

			

	     }
	</script>
</head>
<body style="background-color:#E1E1E1;">
<div id="content" style="width:450px;overflow:hidden;">
	<h2>Caricamento dei Files</h2>
	<form id="form1" action="index.php" method="post" enctype="multipart/form-data">
		<p>cliccare su 'Upload' e selezionare uno o pìù files da caricare</p>
		<table>
			<tr valign="top">
				<td>
					<div>
						<div class="fieldset flash" id="fsUploadProgress1">
							<span class="legend">Files Upload</span>
						</div>
						<div style="padding-left: 5px;">
							<span id="spanButtonPlaceholder1"></span>
							<input id="btnCancel1" type="button" value="Annulla Uploads" onclick="cancelQueue(upload1);" disabled="disabled" style="margin-left: 2px; height: 22px; font-size: 8pt;" />
							<br />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
</body>
</html>
