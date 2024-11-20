<?php
//ini_set('xdebug.max_nesting_level', 500);
session_start();

if(isset($_SESSION["userris_id"]) && $_GET['menid']=="5") {
  header("location: index.php?documents=1"); 
  exit;
}
 
require_once ("rsHeader.php");
require_once ("_docroot.php");
require_once (SERVER_DOCROOT."logic/class_config.php");

$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$objHtml = new Html;
$agent = new rsAgentAI();

class rsAgentRules {
    var $_agent;
    var $_no_description = "Nessuna descrizione disponibile";

    function rsAgentRules($agent) {
        $this->_agent = $agent;

        // Registra i comandi personalizzati
        $agent->registerCommandHandler('list_products', array($this, 'productsListHandler'));
        $agent->registerCommandHandler('nav>', array($this, 'productNavigationHandler'));
        $agent->registerCommandHandler('list_images>', array($this, 'productImagesHandler'));
    }

    function action() {
        // Gestisci la richiesta
        $this->_agent->handleRequest();
    }

    function test($data) {
        // Simula la logica di handleRequest direttamente
        $command = isset($data['command']) ? $data['command'] : '';
        $this->_agent->dispatchCommand($command, $data);
    }

    // Helper per recuperare valori con fallback
    private function getOrDefault($array, $key, $default) {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    // Funzione personalizzata per listare i prodotti
    function productsListHandler($command, $data, $api) {
        $api->log("Esecuzione comando: " . $command);

        // Recupera la struttura del magazzino
        $magazzino = getStrutturaFull("magazzino", "", -1);
        if (!$magazzino) {
            $api->sendError('Nessun prodotto trovato');
        }

        $products = array();
        foreach ($magazzino as $row) {
            $articolo = retArticoloFromCat($row["id"]);
            if (!$articolo) continue;

            $products[] = array(
                'id' => (int)$row["id"],
                'name' => $row["nome"],
                'description' => $this->getOrDefault($articolo, "Descr_AI", $this->_no_description),
                'price' => currencyITA(retPrezzoScontato($articolo))
            );
        }

        echo json_encode(array('products' => $products), JSON_UNESCAPED_SLASHES);
    }

    function productNavigationHandler($command, $data, $api) {
        $api->log("Navigazione: " . $command);

        $cmd = $api->getNavigationCommand($command);
        $catID = intval($cmd[1]);

        $cat = retRow("categorie", $catID);
        $articolo = retArticoloFromCat($catID);
        if (!$articolo) {
            $api->sendError("Articolo con ID " . $catID . " non trovato");
        }

        $response = array(
            'id' => (int)$catID,
            'name' => $this->getOrDefault($cat, "nome", "Sconosciuto"),
            'description' => strip_tags($this->getOrDefault($articolo, "Descr1", $this->_no_description)),
            'description_AI' => $this->getOrDefault($articolo, "Descr_AI", $this->_no_description),
            'price' => currencyITA(retPrezzoScontato($articolo))
        );

        echo json_encode($response, JSON_UNESCAPED_SLASHES);
    }

    function productImagesHandler($command, $data, $api) {
        $api->log("Esecuzione comando: " . $command);

        $cmd = $api->getNavigationCommand($command);
        $catID = intval($cmd[1]);

        $cat = retRow("categorie", $catID);
        $images = Table2ByTable1("categorie", "fotogallery", $catID, "attivo=1", "Ordinamento ASC");
        if (!$images) {
            $api->sendError("Nessuna immagine trovata per il prodotto con ID $catID");
        }

        $imagePaths = array();
        foreach ($images as $image) {
            if (isset($image["immagine_file"])) {
                $thumbnail = cerServerName() . retFile($image["immagine_file"], 0, 200);
                $imagePaths[] = $thumbnail;
            }
        }

        echo json_encode(array(
            'product_id' => $catID,
            'name' => $this->getOrDefault($cat, "nome", "Sconosciuto"),
            'images' => $imagePaths
        ), JSON_UNESCAPED_SLASHES);
    }
}

// Test
$data = array(
    "command" => "nav>2088"
);

$agentRules = new rsAgentRules($agent);
$agentRules->test($data);

