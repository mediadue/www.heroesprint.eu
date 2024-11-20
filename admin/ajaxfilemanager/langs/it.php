<?php
	/**
	 * language pack
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	define('DATE_TIME_FORMAT', 'd/M/Y H:i:s');
	//Common
	//Menu
	
	
	
	
	define('MENU_SELECT', 'Seleziona');
	define('MENU_DOWNLOAD', 'Download');
	define('MENU_PREVIEW', 'Anteprima');
	define('MENU_RENAME', 'Rinomina');
	define('MENU_EDIT', 'Modifica');
	define('MENU_CUT', 'Taglia');
	define('MENU_COPY', 'Copia');
	define('MENU_DELETE', 'Elimina');
	define('MENU_PLAY', 'Play');
	define('MENU_PASTE', 'Incolla');
	
	//Label
		//Top Action
		define('LBL_ACTION_REFRESH', 'Aggiorna');
		define('LBL_ACTION_DELETE', 'Cancella');
		define('LBL_ACTION_CUT', 'Taglia');
		define('LBL_ACTION_COPY', 'Copia');
		define('LBL_ACTION_PASTE', 'Incolla');
		define('LBL_ACTION_CLOSE', 'Chiudi');
		define('LBL_ACTION_SELECT_ALL', 'Seleziona tutto');
		//File Listing
	define('LBL_NAME', 'Nome');
	define('LBL_SIZE', 'Dimensioni');
	define('LBL_MODIFIED', 'Modificato il');
		//File Information
	define('LBL_FILE_INFO', 'Informazioni sul File:');
	define('LBL_FILE_NAME', 'Nome:');	
	define('LBL_FILE_CREATED', 'Creato:');
	define('LBL_FILE_MODIFIED', 'Modificato:');
	define('LBL_FILE_SIZE', 'Dimensioni:');
	define('LBL_FILE_TYPE', 'Tipo:');
	define('LBL_FILE_WRITABLE', 'scrittura?');
	define('LBL_FILE_READABLE', 'lettura?');
		//Folder Information
	define('LBL_FOLDER_INFO', 'Informazioni sulla Cartella');
	define('LBL_FOLDER_PATH', 'Cartella:');
	define('LBL_CURRENT_FOLDER_PATH', 'Percorso corrente:');
	define('LBL_FOLDER_CREATED', 'Creata:');
	define('LBL_FOLDER_MODIFIED', 'Modificata:');
	define('LBL_FOLDER_SUDDIR', 'Sottocartelle:');
	define('LBL_FOLDER_FIELS', 'Files:');
	define('LBL_FOLDER_WRITABLE', 'scrittura?');
	define('LBL_FOLDER_READABLE', 'lettura?');
	define('LBL_FOLDER_ROOT', 'Cartella Base');
		//Preview
	define('LBL_PREVIEW', 'Anteprima');
	define('LBL_CLICK_PREVIEW', 'Clicca qui per un anteprima.');
	//Buttons
	define('LBL_BTN_SELECT', 'Seleziona');
	define('LBL_BTN_CANCEL', 'Annulla');
	define('LBL_BTN_UPLOAD', 'Upload');
	define('LBL_BTN_CREATE', 'Crea');
	define('LBL_BTN_CLOSE', 'Chiudi');
	define('LBL_BTN_NEW_FOLDER', 'Nuova Cartella');
	define('LBL_BTN_NEW_FILE', 'Nuovo File');
	define('LBL_BTN_EDIT_IMAGE', 'Modifca');
	define('LBL_BTN_VIEW', 'Select View');
	define('LBL_BTN_VIEW_TEXT', 'Testo');
	define('LBL_BTN_VIEW_DETAILS', 'Dettagli');
	define('LBL_BTN_VIEW_THUMBNAIL', 'Anteprima');
	define('LBL_BTN_VIEW_OPTIONS', 'Guarda dentro:');
	//pagination
	define('PAGINATION_NEXT', 'Prossimo');
	define('PAGINATION_PREVIOUS', 'Precedente');
	define('PAGINATION_LAST', 'Ultimo');
	define('PAGINATION_FIRST', 'Primo');
	define('PAGINATION_ITEMS_PER_PAGE', 'Visualizza %s file per pagina');
	define('PAGINATION_GO_PARENT', 'Vai a livello superiore');
	//System
	define('SYS_DISABLED', 'Permesso negato: sistema disabilitato.');
	
	//Cut
	define('ERR_NOT_DOC_SELECTED_FOR_CUT', 'Nessun file da tagliare.');
	//Copy
	define('ERR_NOT_DOC_SELECTED_FOR_COPY', 'Nessun file da copiare.');
	//Paste
	define('ERR_NOT_DOC_SELECTED_FOR_PASTE', 'Nessun file da incollare.');
	define('WARNING_CUT_PASTE', 'Sei sicuro di voler spostare i file selezionati nella cartella corrente?');
	define('WARNING_COPY_PASTE', 'Sei sicuro di voler spostare i file copiati nella cartella corrente?');
	define('ERR_NOT_DEST_FOLDER_SPECIFIED', 'Non hai specificato una cartella di destinazione.');
	define('ERR_DEST_FOLDER_NOT_FOUND', 'La cartella di destinazione non esiste.');
	define('ERR_DEST_FOLDER_NOT_ALLOWED', 'Non puoi muovere i file i questa cartellaZ');
	define('ERR_UNABLE_TO_MOVE_TO_SAME_DEST', 'Impoaaibile spostare il file (%s): Cartella di destinazione coincidente con quella di origine.');
	define('ERR_UNABLE_TO_MOVE_NOT_FOUND', 'Impoaaibile spostare il file (%s): Il file non esiste.');
	define('ERR_UNABLE_TO_MOVE_NOT_ALLOWED', 'Impoaaibile spostare il file (%s): Accesso negato.');
 
	define('ERR_NOT_FILES_PASTED', 'Nessun file incollato.');

	//Search
	define('LBL_SEARCH', 'Search');
	define('LBL_SEARCH_NAME', 'Nome parziale/completo del file:');
	define('LBL_SEARCH_FOLDER', 'Cerca in:');
	define('LBL_SEARCH_QUICK', 'Ricerca Veloce');
	define('LBL_SEARCH_MTIME', 'Data di modifica del file(Intervallo):');
	define('LBL_SEARCH_SIZE', 'Dimensioni del file:');
	define('LBL_SEARCH_ADV_OPTIONS', 'Opzioni Avanzate');
	define('LBL_SEARCH_FILE_TYPES', 'Tipi di file:');
	define('SEARCH_TYPE_EXE', 'Applicazione');
	
	define('SEARCH_TYPE_IMG', 'Immagine');
	define('SEARCH_TYPE_ARCHIVE', 'Archivio');
	define('SEARCH_TYPE_HTML', 'HTML');
	define('SEARCH_TYPE_VIDEO', 'Video');
	define('SEARCH_TYPE_MOVIE', 'Filmato');
	define('SEARCH_TYPE_MUSIC', 'Musica');
	define('SEARCH_TYPE_FLASH', 'Flash');
	define('SEARCH_TYPE_PPT', 'PowerPoint');
	define('SEARCH_TYPE_DOC', 'Document');
	define('SEARCH_TYPE_WORD', 'Word');
	define('SEARCH_TYPE_PDF', 'PDF');
	define('SEARCH_TYPE_EXCEL', 'Excel');
	define('SEARCH_TYPE_TEXT', 'Text');
	define('SEARCH_TYPE_UNKNOWN', 'Sconosciuto');
	define('SEARCH_TYPE_XML', 'XML');
	define('SEARCH_ALL_FILE_TYPES', 'Tutti i formati');
	define('LBL_SEARCH_RECURSIVELY', 'Ricerca Ricorsiva:');
	define('LBL_RECURSIVELY_YES', 'S&igrave;');
	define('LBL_RECURSIVELY_NO', 'No');
	define('BTN_SEARCH', 'Cerca Ora');
	//thickbox
	define('THICKBOX_NEXT', 'Prossimo&gt;');
	define('THICKBOX_PREVIOUS', '&lt;Precedente');
	define('THICKBOX_CLOSE', 'Chiudi');
	//Calendar
	define('CALENDAR_CLOSE', 'Chiudi');
	define('CALENDAR_CLEAR', 'Pulisci');
	define('CALENDAR_PREVIOUS', '&lt;Precedente');
	define('CALENDAR_NEXT', 'Prossimo&gt;');
	define('CALENDAR_CURRENT', 'Oggi');
	define('CALENDAR_MON', 'Lun');
	define('CALENDAR_TUE', 'Mar');
	define('CALENDAR_WED', 'Mer');
	define('CALENDAR_THU', 'Gio');
	define('CALENDAR_FRI', 'Ven');
	define('CALENDAR_SAT', 'Sab');
	define('CALENDAR_SUN', 'Dom');
	define('CALENDAR_JAN', 'Gen');
	define('CALENDAR_FEB', 'Feb');
	define('CALENDAR_MAR', 'Mar');
	define('CALENDAR_APR', 'Apr');
	define('CALENDAR_MAY', 'Mag');
	define('CALENDAR_JUN', 'Giu');
	define('CALENDAR_JUL', 'Lug');
	define('CALENDAR_AUG', 'Ago');
	define('CALENDAR_SEP', 'Set');
	define('CALENDAR_OCT', 'Ott');
	define('CALENDAR_NOV', 'Nov');
	define('CALENDAR_DEC', 'Dic');
	//ERROR MESSAGES
		//deletion
	define('ERR_NOT_FILE_SELECTED', 'Selezionare un file.');
	define('ERR_NOT_DOC_SELECTED', 'Nessun documento selezionato per la cancellazione.');
	define('ERR_DELTED_FAILED', 'Impossibile cancellare i documenti selezionati.');
	define('ERR_FOLDER_PATH_NOT_ALLOWED', 'Non si hanno i permessi sulla cartella.');
		//class manager
	define('ERR_FOLDER_NOT_FOUND', 'Impossibile trovare la cartella: ');
		//rename
	define('ERR_RENAME_FORMAT', 'Scegliere un nome che contenga solo lettere, numeri, spazi, trattini e underscore.');
	define('ERR_RENAME_EXISTS', 'Scegliere un nome che sia univoco nella cartella.');
	define('ERR_RENAME_FILE_NOT_EXISTS', 'Il file/cartella non esiste.');
	define('ERR_RENAME_FAILED', 'Operazione fallita, riprovare in un secondo momento.');
	define('ERR_RENAME_EMPTY', 'Specificare un nome.');
	define('ERR_NO_CHANGES_MADE', 'Nessun cambiamento effettuato.');
	define('ERR_RENAME_FILE_TYPE_NOT_PERMITED', 'Non si hanno i permessi per modificare file con questa estensione.');
		//folder creation
	define('ERR_FOLDER_FORMAT', 'Scegliere un nome che contenga solo lettere, numeri, spazi, trattini e underscore.');
	define('ERR_FOLDER_EXISTS', 'Scegliere un nome che sia univoco nella cartella.');
	define('ERR_FOLDER_CREATION_FAILED', 'Operazione fallita, riprovare in un secondo momento.');
	define('ERR_FOLDER_NAME_EMPTY', 'Specificare un nome.');
	define('FOLDER_FORM_TITLE', 'Creazione Nuova Cartella');
	define('FOLDER_LBL_TITLE', 'Titolo per la Cartella:');
	define('FOLDER_LBL_CREATE', 'Crea la Cartella');
	//New File
	define('NEW_FILE_FORM_TITLE', 'Creazione Nuovo File');
	define('NEW_FILE_LBL_TITLE', 'Nome del File:');
	define('NEW_FILE_CREATE', 'Crea il File');
		//file upload
	define('ERR_FILE_NAME_FORMAT', 'Scegliere un nome che contenga solo lettere, numeri, spazi, trattini e underscore.');
	define('ERR_FILE_NOT_UPLOADED', 'Nessun file selezionato per l upload.');
	define('ERR_FILE_TYPE_NOT_ALLOWED', 'Non si dispone dei permessi per effettuare l upload di questo tipo di file.');
	define('ERR_FILE_MOVE_FAILED', 'Trasferimento non riucito.');
	define('ERR_FILE_NOT_AVAILABLE', 'Il file non esiste più.');   
	define('ERROR_FILE_TOO_BID', 'File &egrave; troppo grande. (massimo: %s)');
	define('FILE_FORM_TITLE', 'Upload di File');
	define('FILE_LABEL_SELECT', 'Scegli un file');
	define('FILE_LBL_MORE', 'Aggiungi File per l Upload');
	define('FILE_CANCEL_UPLOAD', 'Annulla trasferimento');
	define('FILE_LBL_UPLOAD', 'Upload');
	//file download
	define('ERR_DOWNLOAD_FILE_NOT_FOUND', 'Nessun file selezionato per il download.');
	//Rename
	define('RENAME_FORM_TITLE', 'Rinominazione');
	define('RENAME_NEW_NAME', 'Nuovo Nome');
	define('RENAME_LBL_RENAME', 'Rinomina');

	//Tips
	define('TIP_FOLDER_GO_DOWN', 'Click singolo per selezionare questa cartella...');
	define('TIP_DOC_RENAME', 'Doppio click per modificare...');
	define('TIP_FOLDER_GO_UP', 'Click singolo per selezionare la cartella superiore...');
	define('TIP_SELECT_ALL', 'Seleziona Tutto');
	define('TIP_UNSELECT_ALL', 'Deseleziona Tutto');
	//WARNING
	define('WARNING_DELETE', 'Sei sicuro di voler eleiminare i documenti selezionati.');
	define('WARNING_IMAGE_EDIT', 'Selezionare un immagine da modificare.');
	define('WARNING_NOT_FILE_EDIT', 'Selezionare un file da modificare.');
	define('WARING_WINDOW_CLOSE', 'Sei sicuro di voler chiudere la finestra?');
	//Preview
	define('PREVIEW_NOT_PREVIEW', 'Anteprima non disponibile.');
	define('PREVIEW_OPEN_FAILED', 'Impossibile aprire il file.');
	define('PREVIEW_IMAGE_LOAD_FAILED', 'Impossibile caricare l immagine');

	//Login
	define('LOGIN_PAGE_TITLE', 'Login Form');
	define('LOGIN_FORM_TITLE', 'Login Form');
	define('LOGIN_USERNAME', 'Username:');
	define('LOGIN_PASSWORD', 'Password:');
	define('LOGIN_FAILED', 'Username o Password errati.');
	
	
	//88888888888   Below for Image Editor   888888888888888888888
		//Warning 
		define('IMG_WARNING_NO_CHANGE_BEFORE_SAVE', 'Non hai apportato nessuna modifica all immagine.');
		
		//General
		define('IMG_GEN_IMG_NOT_EXISTS', 'l immagine non esiste');
		define('IMG_WARNING_LOST_CHANAGES', 'Tutte le modifiche non salvate andranno perse, vuoi continuare?');
		define('IMG_WARNING_REST', 'Tutte le modifiche non salvate andranno perse, procedere?');
		define('IMG_WARNING_EMPTY_RESET', 'Nessuna modifica effettuata da molto tempo');
		define('IMG_WARING_WIN_CLOSE', 'Confermi la chiusura della finestra?');
		define('IMG_WARNING_UNDO', 'Confermi il rispristino dell immagine allo stato precedente?');
		define('IMG_WARING_FLIP_H', 'Sei sicuro di voler ribaltare l immagine orizzontalmente?');
		define('IMG_WARING_FLIP_V', 'Sei sicuro di voler ribaltare l immagine verticalmente?');
		define('IMG_INFO', 'Informazioni sull immagine');
		
		//Mode
			define('IMG_MODE_RESIZE', 'Ridimensiona:');
			define('IMG_MODE_CROP', 'Ritaglia:');
			define('IMG_MODE_ROTATE', 'Ruota:');
			define('IMG_MODE_FLIP', 'Ribalta:');		
		//Button
		
			define('IMG_BTN_ROTATE_LEFT', '90&deg; <');
			define('IMG_BTN_ROTATE_RIGHT', '90&deg; >');
			define('IMG_BTN_FLIP_H', 'Ribalta Orizzontalmente');
			define('IMG_BTN_FLIP_V', 'Ribalta Verticalmente');
			define('IMG_BTN_RESET', 'Annulla tutto');
			define('IMG_BTN_UNDO', 'Indietro');
			define('IMG_BTN_SAVE', 'Slava');
			define('IMG_BTN_CLOSE', 'Chiudi');
			define('IMG_BTN_SAVE_AS', 'Slava con nome');
			define('IMG_BTN_CANCEL', 'Annulla');
		//Checkbox
			define('IMG_CHECKBOX_CONSTRAINT', 'Manitieni Rapporto');
		//Label
			define('IMG_LBL_WIDTH', 'Larghezza:');
			define('IMG_LBL_HEIGHT', 'Altezza:');
			define('IMG_LBL_X', 'X:');
			define('IMG_LBL_Y', 'Y:');
			define('IMG_LBL_RATIO', 'Rapporto:');
			define('IMG_LBL_ANGLE', 'Angolo:');
			define('IMG_LBL_NEW_NAME', 'Nuovo Nome:');
			define('IMG_LBL_SAVE_AS', 'Salva come Form');
			define('IMG_LBL_SAVE_TO', 'Slava In:');
			define('IMG_LBL_ROOT_FOLDER', 'Cartella Base');
		//Editor
		//Save as 
		define('IMG_NEW_NAME_COMMENTS', 'Non inserire l estensione dell immagine.');
		define('IMG_SAVE_AS_ERR_NAME_INVALID', 'Scegliere un nome che contenga solo lettere, numeri, spazi, trattini e underscore.');
		define('IMG_SAVE_AS_NOT_FOLDER_SELECTED', 'Selezionare una cartella di destinazione.');	
		define('IMG_SAVE_AS_FOLDER_NOT_FOUND', 'La cartella di destinazione non esiste.');
		define('IMG_SAVE_AS_NEW_IMAGE_EXISTS', 'Esiste un immagine con lo stesso nome.');

		//Save
		define('IMG_SAVE_EMPTY_PATH', 'Percorso senza immagini.');
		define('IMG_SAVE_NOT_EXISTS', 'Immagine insesistente.');
		define('IMG_SAVE_PATH_DISALLOWED', 'Non sia hanno i permessi per accedere a questo file.');
		define('IMG_SAVE_UNKNOWN_MODE', 'Inaspettato Image Operation Mode');
		define('IMG_SAVE_RESIZE_FAILED', 'Ridimensionamento non riuscito.');
		define('IMG_SAVE_CROP_FAILED', 'Ritaglio non riuscito.');
		define('IMG_SAVE_FAILED', 'Slavataggio non riuscito.');
		define('IMG_SAVE_BACKUP_FAILED', 'Impossibili fare il backup dell immagine originale.');
		define('IMG_SAVE_ROTATE_FAILED', 'Impossibile ruotare l immagine.');
		define('IMG_SAVE_FLIP_FAILED', 'Impossibile ribaltare l immagine.');
		define('IMG_SAVE_SESSION_IMG_OPEN_FAILED', 'Impossibile aprire l immagine dalla sessione.');
		define('IMG_SAVE_IMG_OPEN_FAILED', 'Impossibile aprire l immagine');
		
		
		//UNDO
		define('IMG_UNDO_NO_HISTORY_AVAIALBE', 'Impossibile tornare indietro.');
		define('IMG_UNDO_COPY_FAILED', 'Impossibile ripristinare l immagine.');
		define('IMG_UNDO_DEL_FAILED', 'Impossibile eliminare l immagine di sessione');
	
	//88888888888   Above for Image Editor   888888888888888888888
	
	//88888888888   SessionF   888888888888888888888
		define('SESSION_PERSONAL_DIR_NOT_FOUND', 'Impossibile trovare la cartella dedicata che &egrave; stata creata sotto la cartella di sessione');
		define('SESSION_COUNTER_FILE_CREATE_FAILED', 'Impossibile aprire il file contatore di sessione.');
		define('SESSION_COUNTER_FILE_WRITE_FAILED', 'Impossibile scrivere sul file contatore di sessione.');
	//88888888888   SessionF   888888888888888888888
	
	//88888888888   Below for Text Editor   888888888888888888888
		define('TXT_FILE_NOT_FOUND', 'File non trovato.');
		define('TXT_EXT_NOT_SELECTED', 'Selezionare una estensione per il file');
		define('TXT_DEST_FOLDER_NOT_SELECTED', 'Selezionare una cartella di destinazione');
		define('TXT_UNKNOWN_REQUEST', 'Richiesta sconosciuta.');
		define('TXT_DISALLOWED_EXT', 'Si hanno i permessi per modificare/aggiungere questi tipi di file.');
		define('TXT_FILE_EXIST', 'Un file simile esiste già.');
		define('TXT_FILE_NOT_EXIST', 'Nessun riscontro.');
		define('TXT_CREATE_FAILED', 'Creazione di un nuovo file fallita.');
		define('TXT_CONTENT_WRITE_FAILED', 'Errore nello scrivere il contenuto del file.');
		define('TXT_FILE_OPEN_FAILED', 'Errore nell aprire il file.');
		define('TXT_CONTENT_UPDATE_FAILED', 'Errore nell aggiornare il contenuto del file.');
		define('TXT_SAVE_AS_ERR_NAME_INVALID', 'Scegliere un nome che contenga solo lettere, numeri, spazi, trattini e underscore.');
	//88888888888   Above for Text Editor   888888888888888888888
	
	
?>