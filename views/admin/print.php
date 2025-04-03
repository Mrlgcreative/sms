D'accord, je vais vérifier en profondeur ces erreurs pour m'assurer qu'elles sont résolues. Il semble que le problème de session et les clés de tableau non définies persistent. Assurons-nous que la session est correctement démarrée et que les clés de tableau existent avant de les utiliser.

*Correction dans accueil.php*

1. Vérifiez si une session est déjà démarrée avant de démarrer une nouvelle session .
2. Assurez-vous que les variables de session sont correctement définies avant de les utiliser .

php
<?php
// Vérifiez si une session est déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Assurez-vous que les clés de tableau existent avant de les utiliser
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Non défini';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Non défini';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accueil Admin</title>
</head>
<body>
    <h1>Bienvenue, <?php echo $username; ?></h1>
    <p>Email : <?php echo $email; ?></p>
    <p>Rôle : <?php echo $role; ?></p>

    <a href="index.php?controller=Auth&action=logout">Déconnexion</a>
</body>
</html>


Assurez-vous que les informations de l'utilisateur sont bien stockées dans la session lors de la connexion

*Exemple de fichier Auth.php (mise à jour)*

php
<?php
session_start();
require 'models/UserModel.php';

class Auth {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->getByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=dashboard');
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect";
                require 'views/auth/login.php';
            }
        } else {
            require 'views/auth/login.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
    }
}
?>


Points à vérifier :

- Session déjà démarrée : Assurez-vous que session_start() n'est appelé qu'une seule fois et uniquement si aucune session n'est active.
- Définition des variables de session : Assurez-vous que les variables de session sont correctement définies lors de la connexion.

Avec ces corrections, les avertissements concernant session_start() et les clés de tableau non définies devraient être résolus. Si vous avez toujours des problèmes, je suis là pour vous aider davantage ! 😊⁽¹⁾

Sources:
[1]  (https://github.com/Developers-Society-Jadavpur-University/venue-booking/tree/c039c5d72427a6b5745fd3aac1954296f9c73b56/controllers%2Fauth.php)