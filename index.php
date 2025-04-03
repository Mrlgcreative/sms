
<?php
// Remove the BASE_URL definition since it's already defined in config/config.php

// Inclure les fichiers nécessaires
require_once 'includes/security.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion des fichiers de configuration
require 'config/config.php';
require 'config/database.php';

// Vérifier l'expiration de la session
if (isset($_SESSION['user_id']) && isSessionExpired()) {
    // Journaliser la déconnexion due à l'inactivité
    require_once 'models/UserModel.php';
    $userModel = new UserModel();
    $userModel->logActivity($_SESSION['user_id'], $_SESSION['username'], 'Déconnexion automatique (inactivité)');
    
    // Détruire la session
    session_unset();
    session_destroy();
    
    // Rediriger vers la page de connexion avec un message
    header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login&message=' . urlencode('Votre session a expiré. Veuillez vous reconnecter.'));
    exit;
}

// Mettre à jour le timestamp de dernière activité
$_SESSION['last_activity'] = time();

// Vérification des paramètres de l'URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'Auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Inclusion du contrôleur approprié
$controllerFile = 'controllers/' . $controller . '.php';
if (file_exists($controllerFile)) {
    require $controllerFile;
    $controllerClass = new $controller();
    if (method_exists($controllerClass, $action)) {
        $controllerClass->$action();
    } else {
        echo "L'action demandée n'existe pas.";
    }
} else {
    echo "Le contrôleur demandé n'existe pas.";
}
?>

