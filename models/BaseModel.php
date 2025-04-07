<?php
class BaseModel {
    protected $db;

    public function __construct() {
        global $mysqli; // Récupérer l'objet mysqli depuis database.php
        $this->db = $mysqli;
    }

    // Récupère les informations de l'utilisateur connecté
    public function getLoggedInUser() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role'],
            ];
        }
        return null; // Pas d'utilisateur connecté
    }
}
?>
