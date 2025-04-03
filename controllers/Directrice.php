
<?php

class Directrice {
    private $eleveModel;
    private $professeurModel;
    private $sessionscolaireModel;
    
    public function __construct() {
        require_once 'models/EleveModel.php';
        require_once 'models/ProfesseurModel.php';
        require_once 'models/SessionscolaireModel.php';
        
        $this->eleveModel = new EleveModel();
        $this->professeurModel = new ProfesseurModel();
        $this->sessionscolaireModel = new SessionscolaireModel();
    }
    
    public function accueil() {
        // Récupérer les informations de session pour la vue
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        
        // Vérifier si c'est une nouvelle connexion
        $showWelcomeMessage = false;
        if (isset($_SESSION['new_login']) && $_SESSION['new_login'] === true) {
            $showWelcomeMessage = true;
            // Réinitialiser le flag pour ne pas afficher le message à chaque chargement de page
            $_SESSION['new_login'] = false;
        }
        
        // Récupérer la session scolaire active
        $session_active = $this->sessionscolaireModel->getActive();
        $current_session = $session_active ? $session_active['annee_debut'] . '-' . $session_active['annee_fin'] : date('Y') . '-' . (date('Y') + 1);
        
        require 'views/comptable/accueil.php';
    }
    
    public function profil() {
        // Vérifier si l'utilisateur est connecté
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue du profil
        require 'views/directrice/profil.php';
    }
    
    // Fonction pour enregistrer les actions des utilisateurs
    public function logAction($action) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            return false;
        }
        
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur inconnu';
        $ip = $_SERVER['REMOTE_ADDR'];
        $action = $mysqli->real_escape_string($action);
        
        // Vérifier si la table et la colonne existent
        $tableCheck = $mysqli->query("SHOW COLUMNS FROM system_logs LIKE 'action'");
        
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $result = $mysqli->query("INSERT INTO system_logs (username, action, ip_address) 
                                    VALUES ('$username', '$action', '$ip')");
        } else {
            // Créer la table si elle n'existe pas
            $mysqli->query("CREATE TABLE IF NOT EXISTS system_logs (
                id INT(11) NOT NULL AUTO_INCREMENT,
                username VARCHAR(255) NOT NULL,
                action VARCHAR(255) NOT NULL,
                action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
            )");
            
            // Réessayer l'insertion
            $result = $mysqli->query("INSERT INTO system_logs (username, action, ip_address) 
                                    VALUES ('$username', '$action', '$ip')");
        }
        
        $mysqli->close();
        
        return isset($result) ? $result : false;
    }
}
?>


