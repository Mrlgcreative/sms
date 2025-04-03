<?php
/**
 * Classe de journalisation pour l'application
 */
class Logger {
    private $logFile;
    private $db;
    private $tableChecked = false;
    
    /**
     * Constructeur
     * @param string $logFile Chemin du fichier de log (optionnel)
     */
    public function __construct($logFile = null) {
        if ($logFile === null) {
            $this->logFile = __DIR__ . '/../logs/app_' . date('Y-m-d') . '.log';
        } else {
            $this->logFile = $logFile;
        }
        
        // Créer le répertoire de logs s'il n'existe pas
        $logDir = dirname($this->logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Connexion à la base de données
        $this->db = new mysqli("localhost", "root", "", "college1");
    }
    
    /**
     * Vérifie et crée la table de logs si nécessaire
     */
    private function checkLogTable() {
        if ($this->tableChecked) {
            return;
        }
        
        // Vérifier si la table existe
        $result = $this->db->query("SHOW TABLES LIKE 'logs'");
        if ($result->num_rows == 0) {
            // Créer la table si elle n'existe pas
            $this->db->query("CREATE TABLE logs (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NULL,
                level VARCHAR(20) NOT NULL DEFAULT 'INFO',
                username VARCHAR(100) NOT NULL,
                action VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                ip_address VARCHAR(45) NULL,
                context TEXT NULL,
                date DATETIME NOT NULL,
                PRIMARY KEY (id),
                KEY user_id (user_id),
                KEY date (date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } else {
            // Vérifier si les colonnes nécessaires existent
            $result = $this->db->query("SHOW COLUMNS FROM logs LIKE 'message'");
            if ($result->num_rows == 0) {
                // Ajouter les colonnes manquantes
                $this->db->query("ALTER TABLE logs 
                    ADD COLUMN message TEXT NOT NULL AFTER action,
                    ADD COLUMN level VARCHAR(20) NOT NULL DEFAULT 'INFO' AFTER user_id,
                    ADD COLUMN ip_address VARCHAR(45) NULL AFTER message,
                    ADD COLUMN context TEXT NULL AFTER ip_address");
            }
        }
        
        $this->tableChecked = true;
    }
    
    /**
     * Écrit un message dans le fichier de log
     * @param string $level Niveau de log (INFO, WARNING, ERROR, etc.)
     * @param string $message Message à journaliser
     * @param array $context Contexte supplémentaire (optionnel)
     */
    public function log($level, $message, $context = []) {
        $date = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Anonyme';
        
        // Formater le contexte
        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' ' . json_encode($context);
        }
        
        // Formater le message de log
        $logMessage = "[$date] [$level] [$ip] [$user] $message$contextStr" . PHP_EOL;
        
        // Écrire dans le fichier
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        
        // Enregistrer dans la base de données si c'est une action importante
        if ($level === 'INFO' || $level === 'WARNING' || $level === 'ERROR' || $level === 'SECURITY') {
            $this->checkLogTable(); // Vérifier la structure de la table
            $this->logToDatabase($level, $message, $user, $ip, $contextStr);
        }
    }
    
    /**
     * Enregistre un log dans la base de données
     */
    private function logToDatabase($level, $message, $user, $ip, $contextStr) {
        try {
            $stmt = $this->db->prepare("INSERT INTO logs (level, username, action, message, ip_address, context, date) VALUES (?, ?, 'Action utilisateur', ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $level, $user, $message, $ip, $contextStr);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            // Écrire l'erreur dans le fichier de log
            $errorMessage = "[" . date('Y-m-d H:i:s') . "] [ERROR] Erreur de journalisation en base de données: " . $e->getMessage() . PHP_EOL;
            file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
        }
    }
    
    /**
     * Journalise une information
     * @param string $message Message à journaliser
     * @param array $context Contexte supplémentaire (optionnel)
     */
    public function info($message, $context = []) {
        $this->log('INFO', $message, $context);
    }
    
    /**
     * Journalise un avertissement
     * @param string $message Message à journaliser
     * @param array $context Contexte supplémentaire (optionnel)
     */
    public function warning($message, $context = []) {
        $this->log('WARNING', $message, $context);
    }
    
    /**
     * Journalise une erreur
     * @param string $message Message à journaliser
     * @param array $context Contexte supplémentaire (optionnel)
     */
    public function error($message, $context = []) {
        $this->log('ERROR', $message, $context);
    }
    
    /**
     * Journalise une action de sécurité
     * @param string $message Message à journaliser
     * @param array $context Contexte supplémentaire (optionnel)
     */
    public function security($message, $context = []) {
        $this->log('SECURITY', $message, $context);
    }
}