
<?php
require_once 'models/UserModel.php';
require_once 'includes/security.php';
require_once 'includes/logger.php';

class Auth {
    private $userModel;
    private $logger;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->logger = new Logger();
    }
    
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole($_SESSION['role']);
            exit;
        }
        
        // Vérifier si l'adresse IP est bloquée
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isIPBlocked($ip)) {
            $_SESSION['error'] = "Votre adresse IP a été bloquée. Veuillez contacter l'administrateur.";
            $this->logger->security("Tentative de connexion depuis une IP bloquée", ['ip' => $ip]);
            require 'views/auth/login.php';
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['error'] = "Erreur de sécurité. Veuillez réessayer.";
                $this->logger->security("Tentative de connexion avec un token CSRF invalide", ['ip' => $ip]);
                logSecurityBreach('login', $ip, 'Invalid CSRF token');
                require 'views/auth/login.php';
                exit;
            }
            
            $username = cleanInput($_POST['username']);
            $password = $_POST['password'];
            
            // Vérifier si le compte est verrouillé
            if ($this->userModel->isAccountLocked($username)) {
                $_SESSION['error'] = "Ce compte est temporairement verrouillé. Veuillez réessayer plus tard ou contacter l'administrateur.";
                $this->logger->security("Tentative de connexion à un compte verrouillé", ['username' => $username, 'ip' => $ip]);
                require 'views/auth/login.php';
                exit;
            }
            
            // Vérifier si le nombre de tentatives de connexion est dépassé
            $failedAttempts = $this->userModel->getFailedLoginAttempts($username, $ip);
            if ($failedAttempts >= 5) {
                // Bloquer l'IP pour 30 minutes
                $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                blockIP($ip, 'Trop de tentatives de connexion échouées', $expiry);
                
                // Verrouiller le compte pour 30 minutes
                $this->userModel->lockAccount($username, $expiry);
                
                $_SESSION['error'] = "Trop de tentatives de connexion échouées. Votre compte a été temporairement verrouillé.";
                $this->logger->security("Compte verrouillé après trop de tentatives", ['username' => $username, 'ip' => $ip]);
                logSecurityBreach('login', $ip, 'Too many failed login attempts');
                require 'views/auth/login.php';
                exit;
            }
            
            $user = $this->userModel->authenticate($username, $password);
            
            if ($user) {
                // Réinitialiser les tentatives de connexion échouées
                $this->userModel->resetFailedLoginAttempts($username, $ip);
                
                // Régénérer l'ID de session pour éviter les attaques de fixation de session
                session_regenerate_id(true);
                
                // Enregistrer les informations de session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['new_login'] = true;
                $_SESSION['last_activity'] = time();
                
                // Enregistrer la session active
                $this->userModel->addActiveSession(
                    $user['id'], 
                    session_id(), 
                    $ip, 
                    $_SERVER['HTTP_USER_AGENT']
                );
                
                // Journaliser la connexion réussie
                $this->userModel->logActivity($user['id'], $user['username'], 'Connexion réussie');
                $this->logger->info("Connexion réussie", ['username' => $username, 'ip' => $ip]);
                
                // Vérifier si le mot de passe doit être changé
                if ($this->userModel->isPasswordChangeRequired($user['id'])) {
                    $_SESSION['password_change_required'] = true;
                    header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=changePassword');
                    exit;
                }
                
                // Rediriger en fonction du rôle
                $this->redirectBasedOnRole($user['role']);
                exit;
            } else {
                // Enregistrer la tentative de connexion échouée
                $this->userModel->addFailedLoginAttempt($username, $ip);
                $this->logger->warning("Échec de connexion", ['username' => $username, 'ip' => $ip]);
                
                $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect.";
                require 'views/auth/login.php';
            }
        } else {
            // Générer un nouveau token CSRF
            generateCSRFToken();
            require 'views/auth/login.php';
        }
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            // Supprimer la session active de la base de données
            $this->userModel->removeActiveSession(session_id());
            
            // Journaliser la déconnexion
            $this->userModel->logActivity($_SESSION['user_id'], $_SESSION['username'], 'Déconnexion');
        }
        
        // Détruire la session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page de connexion
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    private function redirectBasedOnRole($role) {
        switch ($role) {
            case 'admin':
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=accueil');
                break;
            case 'comptable':
                header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=accueil');
                break;
            case 'directeur':
                header('Location: ' . BASE_URL . 'index.php?controller=Directeur&action=accueil');
                break;
            default:
                header('Location: ' . BASE_URL . 'index.php');
                break;
        }
    }
    
    /**
     * Affiche et traite le formulaire d'inscription
     */
    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole($_SESSION['role']);
            exit;
        }
        
        // Générer un nouveau token CSRF
        generateCSRFToken();
        
        // Charger la vue d'inscription
        require 'views/auth/register.php';
    }
}
?>
