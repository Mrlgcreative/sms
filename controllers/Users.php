<?php
require 'models/UserModel.php';
require_once 'includes/logger.php';

class Users {
    private $userModel;
    private $logger;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->logger = new Logger();
    }

    public function users() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }

        try {
            // Récupérer tous les utilisateurs
            $users = $this->userModel->getAllUsers();
            
            // Logger l'action
            $this->logger->log($_SESSION['username'] ?? 'Utilisateur inconnu', 'Consultation de la liste des utilisateurs');
            
            // Inclure la vue
            include 'views/users/users.php';
            
        } catch (Exception $e) {
            // Logger l'erreur
            $this->logger->log($_SESSION['username'] ?? 'Utilisateur inconnu', 'Erreur lors de la consultation des utilisateurs: ' . $e->getMessage());
            
            // Afficher une page d'erreur ou rediriger
            $error_message = "Une erreur est survenue lors du chargement des utilisateurs.";
            include 'views/error/error.php';
        }
    }

    public function ajouterUtilisateur() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userData = [
                    'username' => $_POST['username'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'role' => $_POST['role'] ?? 'user',
                    'nom' => $_POST['nom'] ?? '',
                    'prenom' => $_POST['prenom'] ?? '',
                    'telephone' => $_POST['telephone'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];

                // Validation des données
                if (empty($userData['username']) || empty($userData['email']) || empty($userData['password'])) {
                    throw new Exception("Tous les champs obligatoires doivent être remplis.");
                }

                // Vérifier si l'utilisateur existe déjà
                if ($this->userModel->userExists($userData['username'], $userData['email'])) {
                    throw new Exception("Un utilisateur avec ce nom d'utilisateur ou cet email existe déjà.");
                }

                // Hacher le mot de passe
                $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

                // Ajouter l'utilisateur
                $result = $this->userModel->createUser($userData);

                if ($result) {
                    $this->logger->log($_SESSION['username'] ?? 'Admin', 'Ajout d\'un nouvel utilisateur: ' . $userData['username']);
                    $success_message = "Utilisateur ajouté avec succès.";
                } else {
                    throw new Exception("Erreur lors de l'ajout de l'utilisateur.");
                }

            } catch (Exception $e) {
                $this->logger->log($_SESSION['username'] ?? 'Admin', 'Erreur lors de l\'ajout d\'un utilisateur: ' . $e->getMessage());
                $error_message = $e->getMessage();
            }
        }

        // Inclure la vue pour ajouter un utilisateur
        include 'views/users/ajouter_utilisateur.php';
    }

    public function modifierUtilisateur() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }

        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . 'index.php?controller=Users&action=users');
            exit;
        }

        try {
            // Récupérer les données de l'utilisateur
            $user = $this->userModel->getUserById($userId);
            if (!$user) {
                throw new Exception("Utilisateur non trouvé.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userData = [
                    'id' => $userId,
                    'username' => $_POST['username'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'role' => $_POST['role'] ?? 'user',
                    'nom' => $_POST['nom'] ?? '',
                    'prenom' => $_POST['prenom'] ?? '',
                    'telephone' => $_POST['telephone'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];

                // Validation des données
                if (empty($userData['username']) || empty($userData['email'])) {
                    throw new Exception("Le nom d'utilisateur et l'email sont obligatoires.");
                }

                // Mettre à jour le mot de passe si fourni
                if (!empty($_POST['password'])) {
                    $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                // Modifier l'utilisateur
                $result = $this->userModel->updateUser($userData);

                if ($result) {
                    $this->logger->log($_SESSION['username'] ?? 'Admin', 'Modification de l\'utilisateur: ' . $userData['username']);
                    $success_message = "Utilisateur modifié avec succès.";
                    // Recharger les données de l'utilisateur
                    $user = $this->userModel->getUserById($userId);
                } else {
                    throw new Exception("Erreur lors de la modification de l'utilisateur.");
                }
            }

        } catch (Exception $e) {
            $this->logger->log($_SESSION['username'] ?? 'Admin', 'Erreur lors de la modification d\'un utilisateur: ' . $e->getMessage());
            $error_message = $e->getMessage();
        }

        // Inclure la vue pour modifier un utilisateur
        include 'views/users/modifier_utilisateur.php';
    }

    public function supprimerUtilisateur() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }

        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . 'index.php?controller=Users&action=users');
            exit;
        }

        try {
            // Vérifier que l'utilisateur ne se supprime pas lui-même
            if ($userId == $_SESSION['user_id']) {
                throw new Exception("Vous ne pouvez pas supprimer votre propre compte.");
            }

            // Récupérer les informations de l'utilisateur avant suppression
            $user = $this->userModel->getUserById($userId);
            if (!$user) {
                throw new Exception("Utilisateur non trouvé.");
            }

            // Supprimer l'utilisateur
            $result = $this->userModel->deleteUser($userId);

            if ($result) {
                $this->logger->log($_SESSION['username'] ?? 'Admin', 'Suppression de l\'utilisateur: ' . $user['username']);
                $success_message = "Utilisateur supprimé avec succès.";
            } else {
                throw new Exception("Erreur lors de la suppression de l'utilisateur.");
            }

        } catch (Exception $e) {
            $this->logger->log($_SESSION['username'] ?? 'Admin', 'Erreur lors de la suppression d\'un utilisateur: ' . $e->getMessage());
            $error_message = $e->getMessage();
        }

        // Rediriger vers la liste des utilisateurs
        header('Location: ' . BASE_URL . 'index.php?controller=Users&action=users' . 
               (isset($success_message) ? '&success=' . urlencode($success_message) : '') .
               (isset($error_message) ? '&error=' . urlencode($error_message) : ''));
        exit;
    }
}
?>
