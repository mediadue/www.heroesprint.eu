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
        $agent->registerCommandHandler('build_form>', array($this, 'productBuildFormHandler'));
        $agent->registerCommandHandler('compat_check>', array($this, 'compatCheckHandler'));
        $agent->registerCommandHandler('dimension_normalize>', array($this, 'dimensionNormalizeHandler'));
        $agent->registerCommandHandler('price_check>', array($this, 'priceCheckHandler'));
    }

    function action() {
        // Gestisci la richiesta
        $this->_agent->handleRequest();
    }

    function test($data) {
        // Simula la logica di handleRequest direttamente
        $command = isset($data['command']) ? $data['command'] : '';
        $res = $this->_agent->dispatchCommand($command, $data);
        
        return $res;
    }
    
    function getCategoryData($categoryID) {
        // Recupera i dati generali della categoria
        $category = retRow("categorie", $categoryID);
        if (!$category || $category['attivo'] == 0) {
            return false; // Categoria non trovata o inattiva
        }

        return $category;
    }

    function getCategoryCharacteristics($categoryID) {
        // Recupera le caratteristiche legate alla categoria
        return getTable("ecommerce_caratteristiche", "Ordinamento ASC", "idcategorie_hidden = $categoryID AND attivo = 1");
    }

    function getCharacteristicValues($characteristicID) {
        // Recupera i valori legati alla caratteristica
        return getTable("ecommerce_valori", "Ordinamento ASC", "idcaratteristiche_hidden = $characteristicID AND attivo = 1");
    }

    function getCharacteristicDett($characteristicID) {
        // Recupera i dettagli della caratteristica
        return retRow("ewiz_caratteristiche_list", $characteristicID);
    }

    function getValidCombinations($categoryID) {
        // Recupera gli abbinamenti attivi per la categoria
        return getTable("ecommerce_abbinamenti", "", "id_categorie = $categoryID AND attivo = 1");
    }
    
    function buildCategoryStructure($categoryID) {
        // Recupera i dati della categoria
        $category = $this->getCategoryData($categoryID);
        if (!$category) {
            return array('error' => 'Categoria non trovata o inattiva.');
        }

        // Inizializza la struttura
        $structure = array(
            'category_id' => $categoryID,
            'category_name' => $category['nome'],
            'characteristics' => array(),
            'valid_combinations' => array()
        );

        // Recupera le caratteristiche
        $characteristics = $this->getCategoryCharacteristics($categoryID);
        foreach ($characteristics as $characteristic) {
            // Recupera i valori per la caratteristica
            $values = $this->getCharacteristicValues($characteristic['id']);
            $valuesDett = $this->getCharacteristicDett($characteristic['id_ewiz_caratteristiche_list']);
            $valueData = array();

            foreach ($values as $value) {
                $valueData[] = array(
                    'value_id' => $value['id'],
                    'value_name' => $value['nome']
                );
            }

            // Aggiungi la caratteristica alla struttura
            $structure['characteristics'][] = array(
                'characteristic_id' => $characteristic['id'],
                'characteristic_name' => $valuesDett['nome'],
                'values' => $valueData
            );
        }

        // Recupera tutte le combinazioni valide per la categoria
        $allCombinations = $this->getValidCombinations($categoryID);
        $combinationMap = array();

        foreach ($allCombinations as $combination) {
            $charID = $combination['id_ecommerce_caratteristiche'];
            $valueID = $combination['id_ecommerce_valori'];

            // Crea una combinazione unica
            if (!isset($combinationMap["$charID-$valueID"])) {
                $combinationMap["$charID-$valueID"] = array(
                    'characteristic_id' => $charID,
                    'value_id' => $valueID,
                    'compatible_with' => array()
                );
            }

            $combinationMap["$charID-$valueID"]['compatible_with'][] = array(
                'compatible_characteristic_id' => $combination['id_ecommerce_caratteristiche'],
                'compatible_value_id' => $combination['id_ecommerce_valori']
            );
        }

        // Trasforma la mappa in array
        foreach ($combinationMap as $combination) {
            // Elimina duplicati nei compatibili
            $combination['compatible_with'] = array_unique($combination['compatible_with'], SORT_REGULAR);
            $structure['valid_combinations'][] = $combination;
        }

        return $structure;
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
            $res = $api->sendError('Nessun prodotto trovato');
            return $res;
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
            $res = $api->sendError("Articolo con ID " . $catID . " non trovato");
            return $res;
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
            $res = $api->sendError("Nessuna immagine trovata per il prodotto con ID $catID");
            return $res;
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
    
    function initializeArticleData($articleID) {
        $objUtility = new Utility();

        // Recupera informazioni generali sull'articolo
        $articleData = array();
        $articleData['generali'] = retRow("categorie", $articleID);
        if (!$articleData['generali'] || $articleData['generali']['attivo'] == 0) {
            return false; // Articolo non attivo o non trovato
        }

        // Recupera i testi associati all'articolo
        $articleData['testi'] = getTesti($articleID);
        $ecommerceGenerali = getTable("magazzino_articoli", "", "(id_categorie_str_magazzino='$articleID' AND del_hidden='0')");
        if ($ecommerceGenerali[0]['Descr1'] != "") {
            $articleData['testi'][0]['testo_editor'] = tinyBug($ecommerceGenerali[0]['Descr1']);
        }

        // Recupera il ramo e le caratteristiche
        $ramo = getRamo($articleID);
        $articleData['caratteristiche'] = array();
        $articleData['magazzino'] = array();
        while (list($key, $nodo) = each($ramo)) {
            $tmpMagazzino = Table2ByTable1("categorie", "magazzino_articoli_collegati", $nodo['id'], "attivo='1'", "Ordinamento ASC");
            $articleData['magazzino'] = array_merge($articleData['magazzino'], $tmpMagazzino);

            $tmpCaratteristiche = getTable("ecommerce_caratteristiche", "Ordinamento ASC", "(idcategorie_hidden='" . $nodo['id'] . "' AND attivo='1')");
            while (list($key2, $tmpCar) = each($tmpCaratteristiche)) {
                $carList = retRow("ewiz_caratteristiche_list", $tmpCar['id_ewiz_caratteristiche_list']);
                $carList['id'] = $tmpCar['id'];
                $carList['is_for_mockup'] = $tmpCar['is_for_mockup'];
                array_push($articleData['caratteristiche'], $carList);
            }
        }

        // Recupera combinazioni e informazioni del carrello
        $articleData['combinazioni'] = isset($_SESSION['ecomm'][$articleID]) ? $_SESSION['ecomm'][$articleID] : array();
        $articleData['info_carrello'] = getTable("ecommerce_carrello", "", "attivo='1'");

        return $articleData;
    }
    
    function retAbbinamenti($id_value) {
        $ecommerce_abbinamenti = Table2ByTable1("ecommerce_valori", "ecommerce_abbinamenti", $id_value, "attivo=1", "");
        $field = $this->retAbbinamentiDett($ecommerce_abbinamenti);

        return $field;
    }
    
    function retAbbinamentiDett($ecommerce_abbinamenti) {
        $field = array();

        foreach ($ecommerce_abbinamenti as $abb) {
            $cat = retRow("categorie", $abb['id_categorie']);
            $caratteristica = retRow("ecommerce_caratteristiche", $abb['id_ecommerce_caratteristiche']);
            $caratteristica_dett = retRow("ewiz_caratteristiche_list", $caratteristica['id_ewiz_caratteristiche_list']);
            $valore = retRow("ecommerce_valori", $abb['id_ecommerce_valori']);

            $ecommerce_abbinamenti_str = array(
                'categorie' => $cat['nome'],
                'ecommerce_caratteristiche' => $caratteristica_dett['nome'],
                'ecommerce_valori' => $valore['nome']
            );

            array_push($field, $ecommerce_abbinamenti_str);
        }

        return $field;
    }

    function productBuildFormHandler($command, $data, $api) {
        $api->log("Esecuzione comando: " . $command);
        $self = $this;

        // Estrai l'ID del prodotto dal comando
        $cmd = $api->getNavigationCommand($command);
        $productID = intval($cmd[1]);

        // Inizializza i dati del prodotto
        $articleData = $this->initializeArticleData($productID);
        if (!$articleData) {
            $res = $api->sendError("Prodotto non trovato o non attivo.");
            return $res;
        }

        // Estrarre i dati necessari
        $productGenerali = $articleData['generali'];
        $productCaratteristiche = $articleData['caratteristiche'];

        // Genera la struttura del form
        $form = array();
        foreach ($productCaratteristiche as $car) {
            $ecommerce_tipologie = retRow("ecommerce_tipologie", $car['id_ecommerce_tipologie']);
            $ecommerce_valori = getTable("ecommerce_valori", "Ordinamento ASC", "idcaratteristiche_hidden = " . $car['id']); // Recupera valori una volta

            // Base del campo
            $field = array(
                'id' => $car['id'],
                'name' => $car['nome'],
                'type' => $this->mapFieldType($car['id_ecommerce_tipologie']),
                'description_type' => isset($ecommerce_tipologie['nome']) ? $ecommerce_tipologie['nome'] : '',
                'required' => (bool)$car['obbligatorio'],
                'description' => isset($car['descrizione']) ? $car['descrizione'] : ''
            );

            // Aggiungere validazione per i tipi range e quantity-select
            if ($field['type'] === 'range' || $field['type'] === 'quantity-select') {
                if (!empty($ecommerce_valori)) {
                    $s = intval($ecommerce_valori[0]['nome']);
                    $tot = count($ecommerce_valori) - 1;
                    $e = intval($ecommerce_valori[$tot]['nome']);

                    $field['validation'] = array(
                        'min_value' => $s,
                        'max_value' => $e
                    );
                }
            }
            
            // Aggiungere validazione per il tipo dimensions
            if ($field['type'] === 'dimensions') {
                if (!empty($ecommerce_valori)) {
                    $s = strtolower($ecommerce_valori[0]['nome']);
                    $dims = explode("x", $s);
                    
                    if($dims != FALSE && count($dims) == 2) {
                        $field['max_dimensions'] = array(
                            'combination1' => $dims[0] . "x" . $dims[1],
                            'combination2' => $dims[1] . "x" . $dims[0]
                        );
                    }
                    
                }
            }

            // Se il tipo è select o multiselect, aggiungi le opzioni
            if (in_array($field['type'], array('select', 'multiselect', 'image-select', 'checkbox'))) {
                $options = array_map(function ($opt) use ($self, $car) {
                    $ecommerce_abbinamenti = $self->retAbbinamenti($opt['id']);

                    $option = array(
                        'value' => $opt['id'],
                        'label' => $opt['nome']
                    );

                    if (!empty($ecommerce_abbinamenti)) {
                        $option['not_for'] = $ecommerce_abbinamenti;
                    }

                    return $option;
                }, $ecommerce_valori);

                // Filtra le opzioni per rimuovere quelle vuote
                $options = array_filter($options, function ($opt) {
                    return !empty($opt);
                });

                // Aggiungi le opzioni solo se non sono vuote
                if (!empty($options)) {
                    $field['options'] = $options;
                }
            }

            // Aggiungi il campo al form solo se ha opzioni o se non è un tipo che richiede opzioni
            if (!isset($field['options']) || !empty($field['options'])) {
                $form[] = $field;
            }
        }

        // Restituisci il form in formato JSON
        $ret_arr = array(
            'product_id' => $productID,
            'product_name' => $productGenerali['nome'],
            'form' => $form
        );
        
        return json_encode($ret_arr, JSON_UNESCAPED_SLASHES);
    }

    function getCompatibleProducts($json, $characteristic, $value) {
        $compatibleProducts = array();
        foreach ($json['form'] as $field) {
            foreach ($field['options'] as $option) {
                $incompatible = false;
                foreach ($option['not_for'] as $rule) {
                    if ($rule['ecommerce_caratteristiche'] === $characteristic && $rule['ecommerce_valori'] === $value) {
                        $incompatible = true;
                        break;
                    }
                }
                if (!$incompatible) {
                    $compatibleProducts[] = $option['label'];
                }
            }
        }
        return $compatibleProducts;
    }
    
    function dimensionNormalizeHandler($command, $data, $api) {
       // Estrai l'ID della caratteristica dal comando
       $cmd = $api->getNavigationCommand($command);
       $carID = intval($cmd[1]); 
       $dim_to_normalize = $cmd[2];

       // Estrai le dimensioni da normalizzare
       list($width, $height) = explode('x', $dim_to_normalize);
       $width = intval($width);
       $height = intval($height);

       $car = retRow("ecommerce_caratteristiche", $carID);
       $car_dett = $this->getCharacteristicDett($car['id_ewiz_caratteristiche_list']);
       $field_type = $this->mapFieldType($car_dett['id_ecommerce_tipologie']);

       if($field_type == "dimensions") {
           $val = Table2ByTable1("ecommerce_caratteristiche", "ecommerce_valori", $carID, "attivo=1", "Ordinamento ASC");
           
           // Estrai le dimensioni massime dal primo valore (assumiamo che sia nel formato "70x100")
           list($max_width1, $max_height1) = explode('x', $val[0]['nome']);
           $max_width1 = intval($max_width1);
           $max_height1 = intval($max_height1);
           
           // Le due combinazioni possibili
           $combinations = array(
               array('width' => $max_width1, 'height' => $max_height1),
               array('width' => $max_height1, 'height' => $max_width1)
           );
           
           // Verifica quale combinazione funziona meglio
           $normalized = array(
               'original' => array('width' => $width, 'height' => $height),
               'normalized' => null,
               'explanation' => ''
           );
           
           // Prova la prima combinazione (es: 70x100)
           if ($width <= $combinations[0]['width'] && $height <= $combinations[0]['height']) {
               $normalized['normalized'] = array('width' => $width, 'height' => $height);
               $normalized['explanation'] = "Le dimensioni richieste rientrano nei limiti";
           }
           // Prova la seconda combinazione (es: 100x70)
           else if ($width <= $combinations[1]['width'] && $height <= $combinations[1]['height']) {
               $normalized['normalized'] = array('width' => $width, 'height' => $height);
               $normalized['explanation'] = "Le dimensioni richieste rientrano nei limiti";
           }
           // Normalizza se necessario
           else {
               // Trova la combinazione che richiede meno normalizzazione
               $norm1 = array(
                   'width' => min($width, $combinations[0]['width']),
                   'height' => min($height, $combinations[0]['height'])
               );
               
               $norm2 = array(
                   'width' => min($width, $combinations[1]['width']),
                   'height' => min($height, $combinations[1]['height'])
               );
               
               // Scegli la normalizzazione che mantiene l'area maggiore
               $area1 = $norm1['width'] * $norm1['height'];
               $area2 = $norm2['width'] * $norm2['height'];
               
               if ($area1 >= $area2) {
                   $normalized['normalized'] = $norm1;
                   $normalized['explanation'] = "Dimensioni normalizzate a {$norm1['width']}x{$norm1['height']} per rispettare i limiti massimi di {$max_width1}x{$max_height1}";
               } else {
                   $normalized['normalized'] = $norm2;
                   $normalized['explanation'] = "Dimensioni normalizzate a {$norm2['width']}x{$norm2['height']} per rispettare i limiti massimi di {$max_height1}x{$max_width1}";
               }
           }
           
           // Output del risultato
           $output = array(
               'input' => $dim_to_normalize,
               'valid' => (bool)($normalized['normalized']['width'] == $width && $normalized['normalized']['height'] == $height),
               'normalized' => $normalized['normalized']['width'] . 'x' . $normalized['normalized']['height'],
               'explanation' => $normalized['explanation'],
               'max_dimensions' => array(
                   'combination1' => "{$combinations[0]['width']}x{$combinations[0]['height']}",
                   'combination2' => "{$combinations[1]['width']}x{$combinations[1]['height']}"
               )
           );
           
           return json_encode($output, JSON_UNESCAPED_SLASHES);
       }
    }
    
    function compatCheckHandler($command, $data, $api) {
        // Estrai l'ID della caratteristica dal comando
        $cmd = $api->getNavigationCommand($command);
        $carID = intval($cmd[1]);
        
        //Recupera i valori della caratteristica
        $car_valori = Table2ByTable1("ecommerce_caratteristiche", "ecommerce_valori", $carID, "attivo=1", "Ordinamento ASC");
        
        // Recupera gli abbinamenti relativi alla caratteristica
        $ecommerce_abbinamenti = getTable("ecommerce_abbinamenti", "", "id_ecommerce_caratteristiche = $carID AND attivo=1");
        
        // Ottieni i dettagli degli abbinamenti
        $ecommerce_abbinamenti_dett = $this->retAbbinamentiDett($ecommerce_abbinamenti);
        
        $valori_non_compatibili = array();
        $valori_compatibili = array();
        $id_valori_compatibili = array();
        
        // Aggiungi i nomi dei valori corrispondenti a ciascun abbinamento
        foreach ($ecommerce_abbinamenti_dett as $key => $abb_dett) { // Usa il riferimento per modificare direttamente l'array
            
            // Recupera i valori associati all'abbinamento
            $ecommerce_valori = Table1ByTable2("ecommerce_valori", "ecommerce_abbinamenti", $ecommerce_abbinamenti[$key]['id'], "attivo=1", "Ordinamento ASC");
            foreach ($ecommerce_valori as $val) {
                $ecommerce_caratteristiche = Table1ByTable2("ecommerce_caratteristiche", "ecommerce_valori", $val['id'], "attivo=1", "Ordinamento ASC");
                $car_dett = $this->getCharacteristicDett($ecommerce_caratteristiche[0]['id_ewiz_caratteristiche_list']);
                
                array_push($id_valori_compatibili, $ecommerce_caratteristiche[0]['id']);
                
                //$valori_non_compatibili = array_merge($valori_non_compatibili, array($val['id'], $val['nome']));
                array_push($valori_non_compatibili, $abb_dett['ecommerce_valori'] . " -> " . $car_dett['nome'] . " -> " . $val['nome']);
            }
        }
        
        $id_valori_compatibili = array_unique($id_valori_compatibili);
        
        $car = retRow("ecommerce_caratteristiche", $carID);
        $cat = retRow("categorie", $car['idcategorie_hidden']);
        
        $ecommerce_caratteristiche = $this->getCategoryCharacteristics($cat['id']);
        
        $ecommerce_valori2 = array();
        foreach ($ecommerce_caratteristiche as $car) {
            reset($car_valori);
            foreach ($car_valori as $val1) {
                $car_dett = $this->getCharacteristicDett($car['id_ewiz_caratteristiche_list']);
                $ecommerce_valori = $this->getCharacteristicValues($car['id']);
                
                foreach ($ecommerce_valori as &$val) {
                    $val['nome'] = $val1['nome'] . " -> " . $car_dett['nome'] . " -> " . $val['nome'];
                }
                
                if($car['id'] != $carID && in_array($car['id'], $id_valori_compatibili)) $ecommerce_valori2 = array_merge($ecommerce_valori2, $ecommerce_valori);
            }
        }
        
        // Verifica quali valori sono compatibili
        foreach ($ecommerce_valori2 as $val) {
            if (!in_array($val['nome'], $valori_non_compatibili)) {
                array_push($valori_compatibili, $val['nome']);
            }
        }

        // Output del risultato
        $output = array(
            'non_compatibili' => $valori_non_compatibili,
            'compatibili' => $valori_compatibili,
        );
        
        return json_encode($output, JSON_UNESCAPED_SLASHES);
    }
    
    function priceCheckHandler($command, $data, $api) {
        // Estrai il payload JSON dal comando
        $cmd = $api->getNavigationCommand($command);
        $payload = json_decode($cmd[1], true);
        
        // Verifica struttura base del payload
        if (!isset($payload['product_id'])) {
            return json_encode(array(
                'error' => true,
                'message' => "Formato richiesta non valido. La richiesta deve contenere 'product_id'",
                'format_example' => array(
                    'product_id' => 2109,
                    'form_data' => array(
                        'fields' => array(
                            array('id' => '31', 'value' => '1'),
                            array('id' => '30', 'value' => '70x100')
                        )
                    )
                )
            ));
        }

        if (!isset($payload['form_data']) || !isset($payload['form_data']['fields']) || !is_array($payload['form_data']['fields'])) {
            return json_encode(array(
                'error' => true,
                'message' => "Formato richiesta non valido. La richiesta deve contenere 'form_data.fields' come array",
                'format_example' => array(
                    'product_id' => 2109,
                    'form_data' => array(
                        'fields' => array(
                            array('id' => '31', 'value' => '1'),
                            array('id' => '30', 'value' => '70x100')
                        )
                    )
                )
            ));
        }

        // Verifica formato dei campi
        foreach ($payload['form_data']['fields'] as $field) {
            if (!isset($field['id']) || !isset($field['value'])) {
                return json_encode(array(
                    'error' => true,
                    'message' => "Formato campo non valido. Ogni campo deve avere 'id' e 'value'",
                    'field_format_example' => array(
                        'id' => '31',    // ID del campo dal build_form
                        'value' => '1'    // Valore selezionato
                    )
                ));
            }
        }

        // Se arriviamo qui, il formato è corretto
        $server_request = array(
            'product_id' => $payload['product_id'],
            'form_data' => array(
                'fields' => $payload['form_data']['fields']
            )
        );
        
        // Invia la richiesta al server
        $response = $this->submitForm($server_request);
        
        // Formatta e restituisce la risposta del server
        return json_encode($response, JSON_UNESCAPED_SLASHES);
    }
    
    // Funzione per mappare i tipi di campi
    function mapFieldType($typeID) {
        $types = array(
            1 => 'text',
            2 => 'checkbox',
            3 => 'select',
            4 => 'multiselect',
            5 => 'range',
            6 => 'color',
            7 => 'image-select',
            8 => 'quantity-number',
            9 => 'file_upload',
            10 => 'quantity-select',
            11 => 'dimensions'
        );
        return isset($types[$typeID]) ? $types[$typeID] : 'unknown';
    }
}

// Test
//"compat_check>49"
//"build_form>2112"
//"dimension_normalize>30>75x95"
$data = array(
    "command" => 'price_check>{
    "product_id": 2109,
    "form_data": {
        "fields": [
            { "id": "31", "value": "10" },  
            { "id": "30", "value": "70x100" }, 
            { "id": "32", "value": "87" },  
            { "id": "48", "value": "151" },  
            { "id": "35", "value": "96" },  
            { "id": "36", "value": "98" },  
            { "id": "33", "value": "111" },  
            { "id": "34", "value": "112" }   
        ]
    }
}'
);


//$data = array("command" => 'build_form>2109');

$agentRules = new rsAgentRules($agent);

//$categoryStructure = $agentRules->buildCategoryStructure(2088);
//echo json_encode($categoryStructure); exit;

$res = $agentRules->test($data);

echo $res;

