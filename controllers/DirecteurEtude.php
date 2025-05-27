<?php
class DirecteurEtude {
    
    // Afficher la page d'accueil du préfet
    public function accueil() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directeur_Etude') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue du tableau de bord
        require_once 'views/directeur_etudes/accueil.php';
    }
    
    
  
}
?>
