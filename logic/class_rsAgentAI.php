<?php
class rsAgentAI {
    var $sessionId;
    var $commandHandlers = array(); // Mappa dei comandi e relative funzioni

    function rsAgentAI() {
        session_start();
    }

    function handleRequest() {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->sessionId = isset($data['session_id']) ? $data['session_id'] : null;

        // Inizializza o valida la sessione
        if (!$this->sessionId || !$this->isSessionValid($this->sessionId)) {
            $this->createNewSession();
        }

        $command = isset($data['command']) ? $data['command'] : '';
        $this->dispatchCommand($command, $data);
    }

    // Registra una funzione esterna per un comando
    function registerCommandHandler($commandName, $handler) {
        $this->commandHandlers[$commandName] = $handler;
    }

    function dispatchCommand($command, $data) {
        foreach ($this->commandHandlers as $commandName => $handler) {
            if (strpos($command, $commandName) === 0) {
                // Esegue la funzione registrata
                $result = call_user_func($handler, $command, $data, $this);
                return $result; // Ritorna il risultato dell'handler
            }
        }

        // Comando non riconosciuto
        $this->sendError('Comando non riconosciuto');
    }

    function createNewSession() {
        $this->sessionId = uniqid();
        $_SESSION[$this->sessionId] = array('created_at' => time());
        return json_encode(array('session_id' => $this->sessionId));
    }

    function isSessionValid($sessionId) {
        return isset($_SESSION[$sessionId]);
    }

    function log($message) {
        file_put_contents('api.log', date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
    }

    function sendError($message) {
        $this->log("Errore: $message");
        return json_encode(array('error' => $message));
    }

    function getSessionId() {
        return $this->sessionId;
    }

    function getSessionData() {
        return isset($_SESSION[$this->sessionId]) ? $_SESSION[$this->sessionId] : null;
    }

    function setSessionData($key, $value) {
        $_SESSION[$this->sessionId][$key] = $value;
    }

    function getNavigationCommand($command) {
        $parts = explode('>', $command);
        if (count($parts) < 2) {
            $this->sendError('Comando nav non valido');
        }

        return $parts;
    }
}
?>

