<?php
class Prefet {
    
    // Afficher la page d'accueil du préfet
    public function accueil() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue du tableau de bord
        require_once 'views/prefet/accueil.php';
    }
    
    // Afficher la liste des élèves du secondaire
    public function eleves() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des élèves
        require_once 'views/prefet/eleves.php';
    }
    
    // Afficher la liste des professeurs
    public function professeurs() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des professeurs
        require_once 'views/prefet/professeurs.php';
    }
    
    // Afficher la liste des classes
    public function classes() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des classes
        require_once 'views/prefet/classes.php';
    }
    
    // Afficher la liste des cours
    public function cours() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des cours
        require_once 'views/prefet/cours.php';
    }
    
    // Afficher la page des événements scolaires
    public function evenementsScolaires() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des événements scolaires
        require_once 'views/prefet/evenementsScolaires.php';
    }
    
    // Ajouter un nouvel événement scolaire
    public function ajouterEvenement() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
            $type = isset($_POST['type']) ? trim($_POST['type']) : '';
            $date_debut = isset($_POST['date_debut']) ? trim($_POST['date_debut']) : '';
            $date_fin = isset($_POST['date_fin']) ? trim($_POST['date_fin']) : '';
            $classe = isset($_POST['classe']) && !empty($_POST['classe']) ? (int)$_POST['classe'] : null;
            $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            
            // Valider les données
            if (empty($titre) || empty($type) || empty($date_debut) || empty($date_fin) || empty($lieu)) {
                $_SESSION['error_message'] = "Tous les champs obligatoires doivent être remplis.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Vérifier que la date de fin est après la date de début
            if (strtotime($date_fin) <= strtotime($date_debut)) {
                $_SESSION['error_message'] = "La date de fin doit être postérieure à la date de début.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Préparer la requête SQL
            $query = "INSERT INTO evenements_scolaires (titre, type, date_debut, date_fin, classe, lieu, description) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sssssss", $titre, $type, $date_debut, $date_fin, $classe, $lieu, $description);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "L'événement a été ajouté avec succès.";
                } else {
                    $_SESSION['error_message'] = "Erreur lors de l'ajout de l'événement: " . $stmt->error;
                }
                
                $stmt->close();
            } else {
                $_SESSION['error_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
            }
            
            $mysqli->close();
            
            // Rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        } else {
            // Si le formulaire n'a pas été soumis, rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        }
    }
    
    // Modifier un événement scolaire existant
    public function modifierEvenement() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
            $type = isset($_POST['type']) ? trim($_POST['type']) : '';
            $date_debut = isset($_POST['date_debut']) ? trim($_POST['date_debut']) : '';
            $date_fin = isset($_POST['date_fin']) ? trim($_POST['date_fin']) : '';
            $classe = isset($_POST['classe']) && !empty($_POST['classe']) ? (int)$_POST['classe'] : null;
            $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            
            // Valider les données
            if ($id <= 0 || empty($titre) || empty($type) || empty($date_debut) || empty($date_fin) || empty($lieu)) {
                $_SESSION['error_message'] = "Tous les champs obligatoires doivent être remplis.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Vérifier que la date de fin est après la date de début
            if (strtotime($date_fin) <= strtotime($date_debut)) {
                $_SESSION['error_message'] = "La date de fin doit être postérieure à la date de début.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Préparer la requête SQL
            $query = "UPDATE evenements_scolaires 
                      SET titre = ?, type = ?, date_debut = ?, date_fin = ?, classe = ?, lieu = ?, description = ? 
                      WHERE id = ?";
            
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sssssssi", $titre, $type, $date_debut, $date_fin, $classe, $lieu, $description, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "L'événement a été modifié avec succès.";
                } else {
                    $_SESSION['error_message'] = "Erreur lors de la modification de l'événement: " . $stmt->error;
                }
                
                $stmt->close();
            } else {
                $_SESSION['error_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
            }
            
            $mysqli->close();
            
            // Rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        } else {
            // Si le formulaire n'a pas été soumis, rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        }
    }
    
    // Supprimer un événement scolaire
    public function supprimerEvenement() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Récupérer l'ID de l'événement à supprimer
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $_SESSION['error_message'] = "ID d'événement invalide.";
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        }
        
        // Préparer la requête SQL
        $query = "DELETE FROM evenements_scolaires WHERE id = ?";
        
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "L'événement a été supprimé avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la suppression de l'événement: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
        }
        
        $mysqli->close();
        
        // Rediriger vers la page des événements
        header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
        exit;
    }
    
    // Afficher la page de gestion des absences
    public function absences() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des absences
        require_once 'views/prefet/absences.php';
    }
    
    // Afficher la page de gestion de la discipline
    public function discipline() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue de la discipline
        require_once 'views/prefet/discipline.php';
    }
    
    // Afficher le profil du préfet
    public function profil() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue du profil
        require_once 'views/prefet/profil.php';
    }
}
?>