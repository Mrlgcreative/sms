
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
    
    /**
     * Gère le processus de connexion des utilisateurs
     */
    public function login() {
        // Initialiser la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Rediriger l'utilisateur déjà connecté
        if ($this->isUserLoggedIn()) {
            return;
        }
        
        // Obtenir l'adresse IP du client
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Vérifier si l'IP est bloquée
        if ($this->isIPBlocked($ip)) {
            return;
        }
        
        // Traiter la soumission du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!$this->validateCSRFToken()) {
                return;
            }
            
            // Récupérer et nettoyer les données du formulaire
            $username = cleanInput($_POST['username']);
            $password = $_POST['password'];
            
            // Vérifier si le compte est verrouillé
            if ($this->isAccountLocked($username, $ip)) {
                return;
            }
            
            // Vérifier les tentatives de connexion échouées
            if ($this->hasTooManyFailedAttempts($username, $ip)) {
                return;
            }
            
            // Authentifier l'utilisateur
            $user = $this->userModel->authenticate($username, $password);
            
            if ($user) {
                $this->handleSuccessfulLogin($user, $username, $ip);
            } else {
                $this->handleFailedLogin($username, $ip);
            }
        } else {
            // Afficher le formulaire de connexion
            generateCSRFToken();
            require 'views/auth/login.php';
        }
    }
    
    /**
     * Vérifie si l'utilisateur est déjà connecté
     * @return bool True si l'utilisateur est redirigé
     */
    private function isUserLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole($_SESSION['role']);
            exit;
            return true;
        }
        return false;
    }
    
    /**
     * Vérifie si l'adresse IP est bloquée
     * @param string $ip L'adresse IP à vérifier
     * @return bool True si l'IP est bloquée et l'utilisateur est redirigé
     */
    private function isIPBlocked($ip) {
        if (isIPBlocked($ip)) {
            $_SESSION['error'] = "Votre adresse IP a été bloquée. Veuillez contacter l'administrateur.";
            $this->logger->security("Tentative de connexion depuis une IP bloquée", ['ip' => $ip]);
            require 'views/auth/login.php';
            exit;
            return true;
        }
        return false;
    }
    
    /**
     * Valide le token CSRF
     * @return bool True si le token est valide
     */
    private function validateCSRFToken() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = "Erreur de sécurité. Veuillez réessayer.";
            $this->logger->security("Tentative de connexion avec un token CSRF invalide", ['ip' => $ip]);
            logSecurityBreach('login', $ip, 'Invalid CSRF token');
            require 'views/auth/login.php';
            exit;
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie si le compte est verrouillé
     * @param string $username Le nom d'utilisateur
     * @param string $ip L'adresse IP
     * @return bool True si le compte est verrouillé
     */
    private function isAccountLocked($username, $ip) {
        if ($this->userModel->isAccountLocked($username)) {
            $_SESSION['error'] = "Ce compte est temporairement verrouillé. Veuillez réessayer plus tard ou contacter l'administrateur.";
            $this->logger->security("Tentative de connexion à un compte verrouillé", ['username' => $username, 'ip' => $ip]);
            require 'views/auth/login.php';
            exit;
            return true;
        }
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur a trop de tentatives de connexion échouées
     * @param string $username Le nom d'utilisateur
     * @param string $ip L'adresse IP
     * @return bool True si trop de tentatives échouées
     */
    private function hasTooManyFailedAttempts($username, $ip) {
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
            return true;
        }
        return false;
    }
    
    /**
     * Gère une connexion réussie
     * @param array $user Les données de l'utilisateur
     * @param string $username Le nom d'utilisateur
     * @param string $ip L'adresse IP
     */
    private function handleSuccessfulLogin($user, $username, $ip) {
        // Réinitialiser les tentatives de connexion échouées
        $this->userModel->resetFailedLoginAttempts($username, $ip);
        
        // Régénérer l'ID de session pour éviter les attaques de fixation de session
        session_regenerate_id(true);
        
        // Enregistrer les informations de session
        // Inside your login function, after successful authentication:
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        // Add this line to store the image path
        $_SESSION['image'] = !empty($user['image']) ? $user['image'] : 'dist/img/user2-160x160.jpg';
        $_SESSION['new_login'] = true;
        $_SESSION['last_activity'] = time();
        
        // Log the role for debugging
        $this->logger->info("User role for redirection", ['role' => $user['role']]);
        
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
        
        //  // Vérifier si le mot de passe doit être changé
        // if ($this->userModel->isPasswordChangeRequired($user['id'])) {
        //    $_SESSION['password_change_required'] = true;
        //     header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=changePassword');
        //       exit;
        //  }
        
        // Rediriger en fonction du rôle
        $this->redirectBasedOnRole($user['role']);
        exit;
    }
    
    /**
     * Gère une tentative de connexion échouée
     * @param string $username Le nom d'utilisateur
     * @param string $ip L'adresse IP
     */
    private function handleFailedLogin($username, $ip) {
        // Enregistrer la tentative de connexion échouée
        $this->userModel->addFailedLoginAttempt($username, $ip);
        $this->logger->warning("Échec de connexion", ['username' => $username, 'ip' => $ip]);
        
        $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect.";
        require 'views/auth/login.php';
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
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=accueil');
                break;
            case 'director':
                header('Location: ' . BASE_URL . 'index.php?controller=Director&action=accueil');
                break;
            case 'directrice':
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=accueil');
                break;
            case 'prefet':
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=accueil');
                break;
            case 'enseignant':
                header('Location: ' . BASE_URL . 'index.php?controller=Enseignant&action=accueil');
                break;
            case 'etudiant':
                header('Location: ' . BASE_URL . 'index.php?controller=Etudiant&action=accueil');
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
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $role = isset($_POST['role']) ? $_POST['role'] : 'user';
            $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
            $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
            
            // Validate form data
            $errors = [];
            
            if (empty($username)) {
                $errors[] = "Le nom d'utilisateur est requis";
            }
            
            if (empty($email)) {
                $errors[] = "L'email est requis";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            }
            
            if (empty($password)) {
                $errors[] = "Le mot de passe est requis";
            } elseif (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }
            
            // If no errors, register the user
            if (empty($errors)) {
                // Check if username already exists
                if ($this->userModel->getUserByUsername($username)) {
                    $errors[] = "Ce nom d'utilisateur existe déjà";
                } 
                // Check if email already exists
                else if ($this->userModel->getUserByEmail($email)) {
                    $errors[] = "Cet email est déjà utilisé";
                } else {
                    // Default image path
                    $image = 'dist/img/default-avatar.png';
                    
                    // Handle image upload if provided
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = 'dist/img/users/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $new_filename = 'user_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                        $target_file = $upload_dir . $new_filename;
                        
                        // Check file type
                        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                        if (in_array($_FILES['image']['type'], $allowed_types)) {
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                                $image = $target_file;
                            }
                        }
                    }
                    
                    // Set password expiry days (default 90 days)
                    $password_expiry_days = 90;
                    
                    // Register the user with all fields
                    $user_id = $this->userModel->register(
                        $username, 
                        $password, 
                        $email, 
                        $role, 
                        $image, 
                        $telephone, 
                        $adresse, 
                        $password_expiry_days
                    );
                    
                    if ($user_id) {
                        // Log the registration
                        $this->logger->info("Nouvel utilisateur enregistré", ['username' => $username, 'role' => $role]);
                        
                        // Redirect to login page
                        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login&success=1&message=' . urlencode('Inscription réussie. Vous pouvez maintenant vous connecter.'));
                        exit;
                    } else {
                        $errors[] = "L'inscription a échoué. Veuillez réessayer.";
                    }
                }
            }
        }
        
        // Get available roles for the dropdown
        $roles = ['admin', 'comptable', 'prefet', 'directeur', 'directrice', 'enseignant', 'etudiant'];
        
        // Load the registration view
        require 'views/auth/register.php';
    }
    
    // Add this method to your Auth controller class
    
    /**
     * Affiche et traite le formulaire de changement de mot de passe
     */
    public function changePassword() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['error'] = "Erreur de sécurité. Veuillez réessayer.";
                $this->logger->security("Tentative de changement de mot de passe avec un token CSRF invalide", 
                    ['user_id' => $_SESSION['user_id'], 'ip' => $_SERVER['REMOTE_ADDR']]);
                require 'views/auth/change_password.php';
                exit;
            }
            
            $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            // Valider les entrées
            if (empty($current_password)) {
                $errors[] = "Le mot de passe actuel est requis";
            }
            
            if (empty($new_password)) {
                $errors[] = "Le nouveau mot de passe est requis";
            } elseif (strlen($new_password) < 6) {
                $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères";
            }
            
            if ($new_password !== $confirm_password) {
                $errors[] = "Les nouveaux mots de passe ne correspondent pas";
            }
            
            // Vérifier que le nouveau mot de passe est différent de l'ancien
            if ($new_password === $current_password) {
                $errors[] = "Le nouveau mot de passe doit être différent de l'ancien";
            }
            
            // Si pas d'erreurs, changer le mot de passe
            if (empty($errors)) {
                // Vérifier que le mot de passe actuel est correct
                $user = $this->userModel->getUserById($_SESSION['user_id']);
                
                if ($user && password_verify($current_password, $user['password'])) {
                    // Mettre à jour le mot de passe
                    $result = $this->userModel->updatePassword($_SESSION['user_id'], $new_password);
                    
                    if ($result) {
                        // Journaliser le changement de mot de passe
                        $this->userModel->logActivity($_SESSION['user_id'], $_SESSION['username'], 'Changement de mot de passe');
                        $this->logger->info("Mot de passe changé avec succès", ['user_id' => $_SESSION['user_id']]);
                        
                        // Réinitialiser le flag de changement de mot de passe requis
                        if (isset($_SESSION['password_change_required'])) {
                            unset($_SESSION['password_change_required']);
                        }
                        
                        $success = true;
                        
                        // Rediriger vers la page d'accueil après 3 secondes
                        header("Refresh: 3; URL=" . BASE_URL . "index.php");
                    } else {
                        $errors[] = "Erreur lors de la mise à jour du mot de passe";
                    }
                } else {
                    $errors[] = "Le mot de passe actuel est incorrect";
                    
                    // Journaliser la tentative échouée
                    $this->logger->warning("Échec de changement de mot de passe - mot de passe actuel incorrect", 
                        ['user_id' => $_SESSION['user_id'], 'ip' => $_SERVER['REMOTE_ADDR']]);
                }
            }
        }
        
        // Générer un nouveau token CSRF
        generateCSRFToken();
        
        // Charger la vue
        require 'views/auth/change_password.php';
    }
}
?>
