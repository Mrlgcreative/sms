<?php

require_once 'config/config.php';

class Directrice {
private $db;
    
    public function __construct() {
        // Vérification de la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérification des droits d'accès
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Directrice' && $_SESSION['role'] !== 'directrice')) {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Connexion à la base de données
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }
    
    // Page d'accueil pour la directrice
    public function accueil() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue du tableau de bord
        require_once 'views/directrice/accueil.php';
    }

    /**
     * Vérifie si l'utilisateur est connecté
     * @return bool True si l'utilisateur est connecté, false sinon
     */
    private function isLoggedIn() {
        return isset($_SESSION['role']);
    }
    
    /**
     * Redirige vers une URL
     * @param string $url L'URL de redirection
     */
    private function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

/**
 * Ajoute une nouvelle absence d'élève
 */
public function ajouterAbsence() {
    // Vérifier si l'utilisateur est connecté et a les droits
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $this->redirect('index.php?controller=Auth&action=login');
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer et valider les données du formulaire
        $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
        $date_absence = isset($_POST['date_absence']) ? $_POST['date_absence'] : '';
        $motif = isset($_POST['motif']) ? $_POST['motif'] : '';
        $justifiee = isset($_POST['justifiee']) ? 1 : 0;
        $section = isset($_POST['section']) ? $_POST['section'] : 'maternelle';
        
        // Validation des données
        $errors = [];
        
        if (empty($eleve_id)) {
            $errors[] = "L'élève est requis.";
        }
        
        if (empty($date_absence)) {
            $errors[] = "La date d'absence est requise.";
        } else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_absence)) {
            $errors[] = "Le format de la date est invalide. Utilisez le format YYYY-MM-DD.";
        }
        
        // Si aucune erreur, procéder à l'ajout
        if (empty($errors)) {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                $this->redirect('index.php?controller=Directrice&action=absences&section=' . $section);
            }
            
            // Vérifier que l'élève existe et appartient à la section spécifiée
            $check_eleve_query = "SELECT id FROM eleves WHERE id = ? AND section = ?";
            $check_stmt = $mysqli->prepare($check_eleve_query);
            $check_stmt->bind_param("is", $eleve_id, $section);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows === 0) {
                $_SESSION['flash_message'] = "L'élève sélectionné n'existe pas ou n'appartient pas à la section " . $section;
                $_SESSION['flash_type'] = "danger";
                $check_stmt->close();
                $mysqli->close();
                $this->redirect('index.php?controller=Directrice&action=absences&section=' . $section);
            }
            $check_stmt->close();
            
            // Vérifier si une absence existe déjà pour cet élève à cette date
            $check_absence_query = "SELECT id FROM absences_m WHERE eleve_id = ? AND date_absence = ?";
            $check_absence_stmt = $mysqli->prepare($check_absence_query);
            $check_absence_stmt->bind_param("is", $eleve_id, $date_absence);
            $check_absence_stmt->execute();
            $check_absence_result = $check_absence_stmt->get_result();
            
            if ($check_absence_result->num_rows > 0) {
                $_SESSION['flash_message'] = "Une absence est déjà enregistrée pour cet élève à cette date.";
                $_SESSION['flash_type'] = "warning";
                $check_absence_stmt->close();
                $mysqli->close();
                $this->redirect('index.php?controller=Directrice&action=absences&section=' . $section);
            }
            $check_absence_stmt->close();
            
            // Préparer et exécuter la requête d'insertion
            $insert_query = "INSERT INTO absences_m (eleve_id, date_absence, motif, justifiee, created_at) 
                            VALUES (?, ?, ?, ?, NOW())";
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("issi", $eleve_id, $date_absence, $motif, $justifiee);
            
            if ($stmt->execute()) {
                $_SESSION['flash_message'] = "L'absence a été ajoutée avec succès.";
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "Erreur lors de l'ajout de l'absence: " . $stmt->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $stmt->close();
            $mysqli->close();
        } else {
            // Afficher les erreurs
            $_SESSION['flash_message'] = "Erreurs de validation: " . implode(" ", $errors);
            $_SESSION['flash_type'] = "danger";
        }
        
        // Rediriger vers la page des absences
        $this->redirect('index.php?controller=Directrice&action=absences&section=' . $section);
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page des absences
        $this->redirect('index.php?controller=Directrice&action=absences');
    }
}

    // Gestion des élèves
    public function eleves() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des élèves
        require_once 'views/directrice/eleves.php';
    }
    
    // Gestion des professeurs
    public function professeurs() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des professeurs
        require_once 'views/directrice/professeurs.php';
    }
    
    // Gestion des classes
    public function classes() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des classes
        require_once 'views/directrice/classes.php';
    }
    
    // Gestion des cours
    public function cours() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des cours
        require_once 'views/directrice/cours.php';
    }
    
    // Gestion des événements scolaires
    public function evenementsScolaires() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des événements scolaires
        require_once 'views/directrice/evenements_scolaires.php';
    }
    
     public function getEvenementDetails() {
        if (!isset($_GET['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }
        
        $id = intval($_GET['id']);
        
        $stmt = $this->db->prepare("SELECT * FROM evenements_scolaires WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $event = $result->fetch_assoc();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $event]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Événement non trouvé']);
        }
        
        $stmt->close();
        exit;
    }
    // Gestion des absences
    public function absences() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des absences
        require_once 'views/directrice/absences.php';
    }
    
    // Gestion de la discipline
    public function discipline() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue de la discipline
        require_once 'views/directrice/discipline.php';
    }
    
    // Gestion des finances
    public function finances() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des finances
        require_once 'views/directrice/finances.php';
    }
    
    // Gestion des rapports
    public function rapports() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des rapports
        require_once 'views/directrice/rapports.php';
    }
    
/**
 * Modifie une absence existante
 */
public function modifierAbsence() {
    // Vérifier si l'utilisateur est connecté et a les droits
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $this->redirect('index.php?controller=Auth&action=login');
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer et valider les données du formulaire
        $absence_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $motif = isset($_POST['motif']) ? $_POST['motif'] : '';
        $justifiee = isset($_POST['justifiee']) ? 1 : 0;
        
        // Validation des données
        if (empty($absence_id)) {
            $_SESSION['flash_message'] = "ID d'absence invalide.";
            $_SESSION['flash_type'] = "danger";
            $this->redirect('index.php?controller=Directrice&action=absences');
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            $_SESSION['flash_type'] = "danger";
            $this->redirect('index.php?controller=Directrice&action=absences');
        }
        
        // Vérifier que l'absence existe et appartient à un élève de la section maternelle
        $check_query = "SELECT a.id FROM absences_m a 
                        JOIN eleves e ON a.eleve_id = e.id 
                        WHERE a.id = ? AND e.section = 'maternelle'";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("i", $absence_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            $_SESSION['flash_message'] = "L'absence sélectionnée n'existe pas ou n'appartient pas à un élève de la section maternelle.";
            $_SESSION['flash_type'] = "danger";
            $check_stmt->close();
            $mysqli->close();
            $this->redirect('index.php?controller=Directrice&action=absences');
        }
        $check_stmt->close();
        
        // Préparer et exécuter la requête de mise à jour
        $update_query = "UPDATE absences_m SET motif = ?, justifiee = ? WHERE id = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("sii", $motif, $justifiee, $absence_id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "L'absence a été modifiée avec succès.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Erreur lors de la modification de l'absence: " . $stmt->error;
            $_SESSION['flash_type'] = "danger";
        }
        
        $stmt->close();
        $mysqli->close();
        
        // Rediriger vers la page des absences
        $this->redirect('index.php?controller=Directrice&action=absences');
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page des absences
        $this->redirect('index.php?controller=Directrice&action=absences');
    }
}

/**
 * Supprime une absence
 */
public function supprimerAbsence() {
    // Vérifier si l'utilisateur est connecté et a les droits
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $this->redirect('index.php?controller=Auth&action=login');
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer et valider les données du formulaire
        $absence_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        // Validation des données
        if (empty($absence_id)) {
            $_SESSION['flash_message'] = "ID d'absence invalide.";
            $_SESSION['flash_type'] = "danger";
            $this->redirect('index.php?controller=Directrice&action=absences');
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            $_SESSION['flash_type'] = "danger";
            $this->redirect('index.php?controller=Directrice&action=absences');
        }
        
        // Vérifier que l'absence existe et appartient à un élève de la section maternelle
        $check_query = "SELECT a.id FROM absences_m a 
                        JOIN eleves e ON a.eleve_id = e.id 
                        WHERE a.id = ? AND e.section = 'maternelle'";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("i", $absence_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            $_SESSION['flash_message'] = "L'absence sélectionnée n'existe pas ou n'appartient pas à un élève de la section maternelle.";
            $_SESSION['flash_type'] = "danger";
            $check_stmt->close();
            $mysqli->close();
            $this->redirect('index.php?controller=Directrice&action=absences');
        }
        $check_stmt->close();
        
        // Préparer et exécuter la requête de suppression
        $delete_query = "DELETE FROM absences_m WHERE id = ?";
        $stmt = $mysqli->prepare($delete_query);
        $stmt->bind_param("i", $absence_id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "L'absence a été supprimée avec succès.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Erreur lors de la suppression de l'absence: " . $stmt->error;
            $_SESSION['flash_type'] = "danger";
        }
        
        $stmt->close();
        $mysqli->close();
        
        // Rediriger vers la page des absences
        $this->redirect('index.php?controller=Directrice&action=absences');
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page des absences
        $this->redirect('index.php?controller=Directrice&action=absences');
    }
}
/**
 * Ajoute une nouvelle classe
 */
public function ajouterClasse() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $niveau = isset($_POST['niveau']) ? trim($_POST['niveau']) : '';
        $titulaire = isset($_POST['titulaire']) ? trim($_POST['titulaire']) : '';
        $salle = isset($_POST['salle']) ? trim($_POST['salle']) : '';
        $section = isset($_POST['section']) ? trim($_POST['section']) : 'maternelle';
        
        // Validation des données
        if (empty($nom) || empty($niveau) || empty($titulaire) || empty($salle)) {
            $_SESSION['message'] = 'Tous les champs sont obligatoires.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['message'] = 'Erreur de connexion à la base de données: ' . $mysqli->connect_error;
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        
        // Vérifier si la classe existe déjà
        $check_query = "SELECT id FROM classes WHERE nom = ? AND section = ?";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("ss", $nom, $section);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['message'] = 'Une classe avec ce nom existe déjà dans cette section.';
            $_SESSION['message_type'] = 'danger';
            $check_stmt->close();
            $mysqli->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        $check_stmt->close();
        
        // Insérer la nouvelle classe
        $insert_query = "INSERT INTO classes (nom, niveau, titulaire, salle, section) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);
        $insert_stmt->bind_param("sssss", $nom, $niveau, $titulaire, $salle, $section);
        
        if ($insert_stmt->execute()) {
            $_SESSION['message'] = 'La classe a été ajoutée avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de l\'ajout de la classe: ' . $mysqli->error;
            $_SESSION['message_type'] = 'danger';
        }
        
        $insert_stmt->close();
        $mysqli->close();
        
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
        exit;
    }
}

/**
 * Modifie une classe existante
 */
public function modifierClasse() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $niveau = isset($_POST['niveau']) ? trim($_POST['niveau']) : '';
        $titulaire = isset($_POST['titulaire']) ? trim($_POST['titulaire']) : '';
        $salle = isset($_POST['salle']) ? trim($_POST['salle']) : '';
        $section = isset($_POST['section']) ? trim($_POST['section']) : 'maternelle';
        
        // Validation des données
        if ($id <= 0 || empty($nom) || empty($niveau) || empty($titulaire) || empty($salle)) {
            $_SESSION['message'] = 'Tous les champs sont obligatoires.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['message'] = 'Erreur de connexion à la base de données: ' . $mysqli->connect_error;
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        
        // Vérifier si une autre classe avec le même nom existe déjà
        $check_query = "SELECT id FROM classes WHERE nom = ? AND section = ? AND id != ?";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("ssi", $nom, $section, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['message'] = 'Une autre classe avec ce nom existe déjà dans cette section.';
            $_SESSION['message_type'] = 'danger';
            $check_stmt->close();
            $mysqli->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        $check_stmt->close();
        
        // Mettre à jour la classe
        $update_query = "UPDATE classes SET nom = ?, niveau = ?, titulaire = ?, salle = ? WHERE id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param("ssssi", $nom, $niveau, $titulaire, $salle, $id);
        
        if ($update_stmt->execute()) {
            $_SESSION['message'] = 'La classe a été modifiée avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la modification de la classe: ' . $mysqli->error;
            $_SESSION['message_type'] = 'danger';
        }
        
        $update_stmt->close();
        $mysqli->close();
        
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
        exit;
    }
}

/**
 * Supprime une classe
 */
public function supprimerClasse() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        // Validation des données
        if ($id <= 0) {
            $_SESSION['message'] = 'ID de classe invalide.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['message'] = 'Erreur de connexion à la base de données: ' . $mysqli->connect_error;
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        
        // Vérifier que la classe existe et appartient à la section maternelle
        $check_query = "SELECT id FROM classes WHERE id = ? AND section = 'maternelle'";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            $_SESSION['message'] = 'La classe sélectionnée n\'existe pas ou n\'appartient pas à la section maternelle.';
            $_SESSION['message_type'] = 'danger';
            $check_stmt->close();
            $mysqli->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
        }
        $check_stmt->close();
        
        // Vérifier si des élèves sont associés à cette classe
        $check_eleves_query = "SELECT COUNT(*) as total FROM eleves WHERE classe_id = ?";
        $check_eleves_stmt = $mysqli->prepare($check_eleves_query);
        $check_eleves_stmt->bind_param("i", $id);
        $check_eleves_stmt->execute();
        $check_eleves_result = $check_eleves_stmt->get_result();
        $eleves_count = $check_eleves_result->fetch_assoc()['total'];
        $check_eleves_stmt->close();
        
        if ($eleves_count > 0) {
            // Option 1: Empêcher la suppression
            $_SESSION['message'] = 'Impossible de supprimer cette classe car elle contient ' . $eleves_count . ' élève(s).';
            $_SESSION['message_type'] = 'warning';
            $mysqli->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
            exit;
            
            // Option 2: Supprimer la classe et mettre à jour les élèves (décommentez si nécessaire)
            /*
            // Mettre à jour les élèves pour qu'ils n'aient plus de classe
            $update_eleves_query = "UPDATE eleves SET classe_id = NULL WHERE classe_id = ?";
            $update_eleves_stmt = $mysqli->prepare($update_eleves_query);
            $update_eleves_stmt->bind_param("i", $id);
            $update_eleves_stmt->execute();
            $update_eleves_stmt->close();
            */
        }
        
        // Supprimer la classe
        $delete_query = "DELETE FROM classes WHERE id = ?";
        $delete_stmt = $mysqli->prepare($delete_query);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['message'] = 'La classe a été supprimée avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la suppression de la classe: ' . $mysqli->error;
            $_SESSION['message_type'] = 'danger';
        }
        
        $delete_stmt->close();
        $mysqli->close();
        
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
        exit;
    }
}

/**
 * Affiche les élèves d'une classe spécifique
 */
public function voirEleves() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    $classe = isset($_GET['classe']) ? $_GET['classe'] : '';
    
    if (empty($classe)) {
        $_SESSION['message'] = 'Aucune classe spécifiée.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=classes');
        exit;
    }
    
    // Charger la vue des élèves de la classe
    require_once 'views/directrice/eleves_classe.php';
}





/**
 * Affiche le profil détaillé d'un élève
 */
public function voirEleve() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si l'ID de l'élève est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['message'] = "ID de l'élève non spécifié.";
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=eleves');
        exit;
    }
    
    $eleve_id = intval($_GET['id']);
    
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        $_SESSION['message'] = 'Erreur de connexion à la base de données: ' . $mysqli->connect_error;
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=eleves');
        exit;
    }
    
    // Vérifier que l'élève existe et appartient à la section maternelle
    $check_query = "SELECT id FROM eleves WHERE id = ? AND section = 'maternelle'";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("i", $eleve_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $_SESSION['message'] = "L'élève sélectionné n'existe pas ou n'appartient pas à la section maternelle.";
        $_SESSION['message_type'] = 'danger';
        $check_stmt->close();
        $mysqli->close();
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=eleves');
        exit;
    }
    $check_stmt->close();
    $mysqli->close();
    
    // Charger la vue du profil de l'élève
    require_once 'views/directrice/voirEleve.php';
}



/**
 * Affiche la carte d'identité d'un élève
 */
public function carteEleve() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si l'ID de l'élève est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['message'] = "ID de l'élève non spécifié.";
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=eleves');
        exit;
    }
    
    $eleve_id = intval($_GET['id']);
    
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        $_SESSION['message'] = 'Erreur de connexion à la base de données: ' . $mysqli->connect_error;
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=eleves');
        exit;
    }
    
    // Vérifier que l'élève existe et appartient à la section maternelle
    $check_query = "SELECT id FROM eleves WHERE id = ? AND section = 'maternelle'";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("i", $eleve_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $_SESSION['message'] = "L'élève sélectionné n'existe pas ou n'appartient pas à la section maternelle.";
        $_SESSION['message_type'] = 'danger';
        $check_stmt->close();
        $mysqli->close();
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=eleves');
        exit;
    }
    $check_stmt->close();
    $mysqli->close();
    
    // Charger la vue de la carte d'élève
    require_once 'views/directrice/carte_eleve.php';
}

/**
 * Ajoute un nouvel incident disciplinaire
 */
public function ajouterIncident() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
        $date_incident = isset($_POST['date_incident']) ? $_POST['date_incident'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $sanction = isset($_POST['sanction']) ? $_POST['sanction'] : '';
        $statut = isset($_POST['statut']) ? $_POST['statut'] : 'En cours';
        
        // Convertir le format de date si nécessaire (dd/mm/yyyy -> yyyy-mm-dd)
        if (strpos($date_incident, '/') !== false) {
            $date_parts = explode('/', $date_incident);
            if (count($date_parts) === 3) {
                $date_incident = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        // Validation des données
        $errors = [];
        if ($eleve_id <= 0) {
            $errors[] = "Veuillez sélectionner un élève.";
        }
        if (empty($date_incident)) {
            $errors[] = "La date de l'incident est requise.";
        }
        if (empty($description)) {
            $errors[] = "La description de l'incident est requise.";
        }
        
        // Si pas d'erreurs, insérer l'incident dans la base de données
        if (empty($errors)) {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['message_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
                exit;
            }
            
            // Vérifier que l'élève existe et appartient à la section maternelle
            $check_query = "SELECT id FROM eleves WHERE id = ? AND section = 'maternelle'";
            $check_stmt = $mysqli->prepare($check_query);
            $check_stmt->bind_param("i", $eleve_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows === 0) {
                $_SESSION['message'] = "L'élève sélectionné n'existe pas ou n'appartient pas à la section maternelle.";
                $_SESSION['message_type'] = "danger";
                $check_stmt->close();
                $mysqli->close();
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
                exit;
            }
            $check_stmt->close();
            
            // Insérer l'incident
            $insert_query = "INSERT INTO incidents_disciplinaires_m (eleve_id, date_incident, description, sanction, statut, date_creation) 
                           VALUES (?, ?, ?, ?, ?, NOW())";
            $insert_stmt = $mysqli->prepare($insert_query);
            $insert_stmt->bind_param("issss", $eleve_id, $date_incident, $description, $sanction, $statut);
            
            if ($insert_stmt->execute()) {
                $_SESSION['message'] = "L'incident disciplinaire a été ajouté avec succès.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Erreur lors de l'ajout de l'incident: " . $mysqli->error;
                $_SESSION['message_type'] = "danger";
            }
            
            $insert_stmt->close();
            $mysqli->close();
        } else {
            // Afficher les erreurs
            $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
            $_SESSION['message_type'] = "danger";
        }
        
        // Rediriger vers la page de discipline
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page de discipline
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    }
}

/**
 * Modifie un incident disciplinaire existant
 */
public function modifierIncident() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $sanction = isset($_POST['sanction']) ? $_POST['sanction'] : '';
        $statut = isset($_POST['statut']) ? $_POST['statut'] : 'En cours';
        
        // Validation des données
        $errors = [];
        if ($incident_id <= 0) {
            $errors[] = "ID d'incident invalide.";
        }
        if (empty($description)) {
            $errors[] = "La description de l'incident est requise.";
        }
        
        // Si pas d'erreurs, mettre à jour l'incident dans la base de données
        if (empty($errors)) {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['message_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
                exit;
            }
            
            // Vérifier que l'incident existe
            $check_query = "SELECT i.id FROM incidents_disciplinaires_m i 
                           JOIN eleves e ON i.eleve_id = e.id 
                           WHERE i.id = ?";
            $check_stmt = $mysqli->prepare($check_query);
            $check_stmt->bind_param("i", $incident_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows === 0) {
                $_SESSION['message'] = "L'incident sélectionné n'existe pas.";
                $_SESSION['message_type'] = "danger";
                $check_stmt->close();
                $mysqli->close();
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
                exit;
            }
            $check_stmt->close();
            
            // Mettre à jour l'incident
            $update_query = "UPDATE incidents_disciplinaires_m 
                           SET description = ?, sanction = ?, statut = ?, date_modification = NOW() 
                           WHERE id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param("sssi", $description, $sanction, $statut, $incident_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['message'] = "L'incident disciplinaire a été modifié avec succès.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Erreur lors de la modification de l'incident: " . $mysqli->error;
                $_SESSION['message_type'] = "danger";
            }
            
            $update_stmt->close();
            $mysqli->close();
        } else {
            // Afficher les erreurs
            $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
            $_SESSION['message_type'] = "danger";
        }
        
        // Rediriger vers la page de discipline
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page de discipline
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    }
}

/**
 * Ajoute un nouvel incident disciplinaire pour la section maternelle
 */
public function ajouterIncidentM() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
        $classe_id = isset($_POST['classe_id']) ? intval($_POST['classe_id']) : null;
        $date_incident = isset($_POST['date_incident']) ? $_POST['date_incident'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $sanction = isset($_POST['sanction']) ? $_POST['sanction'] : '';
        $statut = isset($_POST['statut']) ? $_POST['statut'] : 'En cours';
        
        // Convertir le format de date si nécessaire (dd/mm/yyyy -> yyyy-mm-dd)
        if (strpos($date_incident, '/') !== false) {
            $date_parts = explode('/', $date_incident);
            if (count($date_parts) === 3) {
                $date_incident = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        // Validation des données
        $errors = [];
        if ($eleve_id <= 0) {
            $errors[] = "Veuillez sélectionner un élève.";
        }
        if (empty($date_incident)) {
            $errors[] = "La date de l'incident est requise.";
        }
        if (empty($description)) {
            $errors[] = "La description de l'incident est requise.";
        }
        
        // Si pas d'erreurs, insérer l'incident dans la base de données
        if (empty($errors)) {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['message_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
                exit;
            }
            
            // Vérifier que l'élève existe et appartient à la section maternelle
            $check_query = "SELECT id FROM eleves WHERE id = ? AND section = 'maternelle'";
            $check_stmt = $mysqli->prepare($check_query);
            $check_stmt->bind_param("i", $eleve_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows === 0) {
                $_SESSION['message'] = "L'élève sélectionné n'existe pas ou n'appartient pas à la section maternelle.";
                $_SESSION['message_type'] = "danger";
                $check_stmt->close();
                $mysqli->close();
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
                exit;
            }
            $check_stmt->close();
            
            // Insérer l'incident
            $insert_query = "INSERT INTO incidents_disciplinaires_m (eleve_id, classe_id, date_incident, description, sanction, statut, date_creation) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $insert_stmt = $mysqli->prepare($insert_query);
            $insert_stmt->bind_param("iissss", $eleve_id, $classe_id, $date_incident, $description, $sanction, $statut);
            
            if ($insert_stmt->execute()) {
                $_SESSION['message'] = "L'incident disciplinaire a été ajouté avec succès.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Erreur lors de l'ajout de l'incident: " . $mysqli->error;
                $_SESSION['message_type'] = "danger";
            }
            
            $insert_stmt->close();
            $mysqli->close();
        } else {
            // Afficher les erreurs
            $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
            $_SESSION['message_type'] = "danger";
        }
        
        // Rediriger vers la page de discipline
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page de discipline
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    }
}

/**
 * Modifie un incident disciplinaire existant pour la section maternelle
 */
public function modifierIncidentM() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $sanction = isset($_POST['sanction']) ? $_POST['sanction'] : '';
        $statut = isset($_POST['statut']) ? $_POST['statut'] : 'En cours';
        
        // Validation des données
        $errors = [];
        if ($incident_id <= 0) {
            $errors[] = "ID d'incident invalide.";
        }
        if (empty($description)) {
            $errors[] = "La description de l'incident est requise.";
        }
        
        // Si pas d'erreurs, mettre à jour l'incident dans la base de données
        if (empty($errors)) {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['message_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
                exit;
            }
            
            // Vérifier que l'incident existe
            $check_query = "SELECT i.id FROM incidents_disciplinaires_m i 
                           JOIN eleves e ON i.eleve_id = e.id 
                           WHERE i.id = ?";
            $check_stmt = $mysqli->prepare($check_query);
            $check_stmt->bind_param("i", $incident_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows === 0) {
                $_SESSION['message'] = "L'incident sélectionné n'existe pas.";
                $_SESSION['message_type'] = "danger";
                $check_stmt->close();
                $mysqli->close();
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
                exit;
            }
            $check_stmt->close();
            
            // Mettre à jour l'incident
            $update_query = "UPDATE incidents_disciplinaires_m 
                           SET description = ?, sanction = ?, statut = ?, date_modification = NOW() 
                           WHERE id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param("sssi", $description, $sanction, $statut, $incident_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['message'] = "L'incident disciplinaire a été modifié avec succès.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Erreur lors de la modification de l'incident: " . $mysqli->error;
                $_SESSION['message_type'] = "danger";
            }
            
            $update_stmt->close();
            $mysqli->close();
        } else {
            // Afficher les erreurs
            $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
            $_SESSION['message_type'] = "danger";
        }
        
        // Rediriger vers la page de discipline
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page de discipline
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    }
}

/**
 * Supprime un incident disciplinaire pour la section maternelle
 /**
 * Supprime un incident disciplinaire pour la section maternelle
 */
public function supprimerIncidentMaternelle() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        // Validation des données
        if ($incident_id <= 0) {
            $_SESSION['flash_message'] = "ID d'incident invalide.";
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=disciplineMaternelle');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['flash_message'] = 'Erreur de connexion à la base de données: ' . $mysqli->connect_error;
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=disciplineMaternelle');
            exit;
        }
        
        // Vérifier que l'incident existe
        $check_query = "SELECT i.id FROM incidents_disciplinaires_m i 
                       JOIN eleves e ON i.eleve_id = e.id 
                       WHERE i.id = ?";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("i", $incident_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            $_SESSION['flash_message'] = "L'incident sélectionné n'existe pas.";
            $_SESSION['flash_type'] = 'danger';
            $check_stmt->close();
            $mysqli->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=disciplineMaternelle');
            exit;
        }
        $check_stmt->close();
        
        // Supprimer l'incident
        $delete_query = "DELETE FROM incidents_disciplinaires_m WHERE id = ?";
        $delete_stmt = $mysqli->prepare($delete_query);
        $delete_stmt->bind_param("i", $incident_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['flash_message'] = "L'incident disciplinaire a été supprimé avec succès.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = "Erreur lors de la suppression de l'incident: " . $mysqli->error;
            $_SESSION['flash_type'] = 'danger';
        }
        
        $delete_stmt->close();
        $mysqli->close();
        
        // Rediriger vers la page de discipline maternelle
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page de discipline maternelle
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=discipline');
        exit;
    }
}
/**
 * Affiche et gère le profil de la directrice
 */
public function profil() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $message = '';
    $message_type = '';
    
    // Si le formulaire est soumis pour mettre à jour le profil
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validation des données
        $errors = [];
        if (empty($nom)) $errors[] = "Le nom est requis.";
        if (empty($prenom)) $errors[] = "Le prénom est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format d'email invalide.";
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            $_SESSION['flash_type'] = "danger";
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
            exit;
        }
        
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $check_email_query = "SELECT id FROM utilisateurs WHERE email = ? AND id != ?";
        $check_email_stmt = $mysqli->prepare($check_email_query);
        $check_email_stmt->bind_param("si", $email, $user_id);
        $check_email_stmt->execute();
        $check_email_result = $check_email_stmt->get_result();
        
        if ($check_email_result->num_rows > 0) {
            $errors[] = "Cet email est déjà utilisé par un autre compte.";
        }
        $check_email_stmt->close();
        
        // Si l'utilisateur souhaite changer son mot de passe
        if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
            // Vérifier que tous les champs de mot de passe sont remplis
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $errors[] = "Tous les champs de mot de passe doivent être remplis pour changer le mot de passe.";
            } elseif ($new_password !== $confirm_password) {
                $errors[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
            } elseif (strlen($new_password) < 8) {
                $errors[] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
            } else {
                // Vérifier le mot de passe actuel
                $check_password_query = "SELECT mot_de_passe FROM utilisateurs WHERE id = ?";
                $check_password_stmt = $mysqli->prepare($check_password_query);
                $check_password_stmt->bind_param("i", $user_id);
                $check_password_stmt->execute();
                $check_password_result = $check_password_stmt->get_result();
                $user_data = $check_password_result->fetch_assoc();
                $check_password_stmt->close();
                
                if (!password_verify($current_password, $user_data['mot_de_passe'])) {
                    $errors[] = "Le mot de passe actuel est incorrect.";
                }
            }
        }
        
        // Si pas d'erreurs, mettre à jour le profil
        if (empty($errors)) {
            // Préparer la requête de mise à jour
            if (!empty($new_password)) {
                // Mettre à jour avec le nouveau mot de passe
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ?, mot_de_passe = ? WHERE id = ?";
                $update_stmt = $mysqli->prepare($update_query);
                $update_stmt->bind_param("sssssi", $nom, $prenom, $email, $telephone, $hashed_password, $user_id);
            } else {
                // Mettre à jour sans changer le mot de passe
                $update_query = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?";
                $update_stmt = $mysqli->prepare($update_query);
                $update_stmt->bind_param("ssssi", $nom, $prenom, $email, $telephone, $user_id);
            }
            
            if ($update_stmt->execute()) {
                $_SESSION['flash_message'] = "Votre profil a été mis à jour avec succès.";
                $_SESSION['flash_type'] = "success";
                
                // Mettre à jour les informations de session
                $_SESSION['nom'] = $nom;
                $_SESSION['prenom'] = $prenom;
                $_SESSION['email'] = $email;
            } else {
                $_SESSION['flash_message'] = "Erreur lors de la mise à jour du profil: " . $mysqli->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $update_stmt->close();
        } else {
            $_SESSION['flash_message'] = "Erreurs: " . implode(" ", $errors);
            $_SESSION['flash_type'] = "danger";
        }
        
        $mysqli->close();
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
        exit;
    }
    
    // Charger la vue du profil
    require_once 'views/directrice/profil.php';
}

/**
 * Met à jour la photo de profil de la directrice
 */
public function updateAvatar() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        
        // Vérifier si un fichier a été téléchargé
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = "Aucun fichier n'a été téléchargé.";
            $_SESSION['flash_type'] = "danger";
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
            exit;
        }
        
        $file = $_FILES['avatar'];
        
        // Vérifier les erreurs de téléchargement
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => "Le fichier téléchargé dépasse la taille maximale autorisée par PHP.",
                UPLOAD_ERR_FORM_SIZE => "Le fichier téléchargé dépasse la taille maximale autorisée par le formulaire.",
                UPLOAD_ERR_PARTIAL => "Le fichier n'a été que partiellement téléchargé.",
                UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant.",
                UPLOAD_ERR_CANT_WRITE => "Échec de l'écriture du fichier sur le disque.",
                UPLOAD_ERR_EXTENSION => "Une extension PHP a arrêté le téléchargement du fichier."
            ];
            
            $error_message = isset($error_messages[$file['error']]) ? $error_messages[$file['error']] : "Erreur inconnue lors du téléchargement.";
            
            $_SESSION['flash_message'] = $error_message;
            $_SESSION['flash_type'] = "danger";
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
            exit;
        }
        
        // Vérifier le type de fichier
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['flash_message'] = "Type de fichier non autorisé. Seuls les formats JPG, PNG et GIF sont acceptés.";
            $_SESSION['flash_type'] = "danger";
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
            exit;
        }
        
        // Vérifier la taille du fichier (max 2MB)
        $max_size = 2 * 1024 * 1024; // 2MB en octets
        if ($file['size'] > $max_size) {
            $_SESSION['flash_message'] = "Le fichier est trop volumineux. La taille maximale autorisée est de 2MB.";
            $_SESSION['flash_type'] = "danger";
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
            exit;
        }
        
        // Créer le dossier d'upload s'il n'existe pas
        $upload_dir = 'uploads/avatars/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Générer un nom de fichier unique
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'directrice_' . $user_id . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        // Déplacer le fichier téléchargé vers le dossier de destination
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
                exit;
            }
            
            // Récupérer l'ancienne image pour la supprimer plus tard
            $get_old_image_query = "SELECT image FROM users WHERE id = ?";
            $get_old_image_stmt = $mysqli->prepare($get_old_image_query);
            $get_old_image_stmt->bind_param("i", $user_id);
            $get_old_image_stmt->execute();
            $get_old_image_result = $get_old_image_stmt->get_result();
            $old_image = $get_old_image_result->fetch_assoc()['image'];
            $get_old_image_stmt->close();
            
            // Mettre à jour l'image de profil dans la base de données
            $update_query = "UPDATE users SET image = ? WHERE id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param("si", $upload_path, $user_id);
            
            if ($update_stmt->execute()) {
                // Supprimer l'ancienne image si elle existe et n'est pas l'image par défaut
                if (!empty($old_image) && $old_image !== 'dist/img/user2-160x160.jpg' && file_exists($old_image)) {
                    unlink($old_image);
                }
                
                // Mettre à jour la session
                $_SESSION['image'] = $upload_path;
                
                $_SESSION['flash_message'] = "Votre photo de profil a été mise à jour avec succès.";
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "Erreur lors de la mise à jour de la photo de profil: " . $mysqli->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $update_stmt->close();
            $mysqli->close();
        } else {
            $_SESSION['flash_message'] = "Erreur lors du téléchargement du fichier.";
            $_SESSION['flash_type'] = "danger";
        }
        
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
        exit;
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page de profil
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=profil');
        exit;
    }
}
/**
 * Affiche le profil détaillé d'un professeur
 */
public function voirProfesseur() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si l'ID du professeur est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['message'] = "ID du professeur non spécifié.";
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
        exit;
    }
    
    $prof_id = intval($_GET['id']);
    
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        $_SESSION['message'] = 'Erreur de connexion à la base de données: ' . $mysqli->connect_error;
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
        exit;
    }
    
    // Vérifier que le professeur existe et appartient à la section maternelle
    $check_query = "SELECT id FROM professeurs WHERE id = ? AND section = 'maternelle'";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("i", $prof_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $_SESSION['message'] = "Le professeur sélectionné n'existe pas ou n'appartient pas à la section maternelle.";
        $_SESSION['message_type'] = 'danger';
        $check_stmt->close();
        $mysqli->close();
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
        exit;
    }
    $check_stmt->close();
    $mysqli->close();
    
    // Charger la vue du profil du professeur
    require_once 'views/directrice/voirProfesseur.php';
}


/**
 * Marque un professeur comme présent via checkbox
 */
public function marquerPresenceProfesseur() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        exit;
    }
    
    // Vérifier si la requête est AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $professeur_id = isset($_POST['professeur_id']) ? intval($_POST['professeur_id']) : 0;
        $date_presence = isset($_POST['date_presence']) ? $_POST['date_presence'] : date('Y-m-d');
        $heure_arrivee = isset($_POST['heure_arrivee']) ? $_POST['heure_arrivee'] : date('H:i:s');
        
        if ($professeur_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de professeur invalide']);
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
            exit;
        }
        
        // Vérifier si une présence existe déjà pour ce professeur à cette date
        $check_query = "SELECT id FROM presences WHERE professeur_id = ? AND date_presence = ?";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("is", $professeur_id, $date_presence);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Une présence existe déjà, renvoyer un succès sans rien faire
            echo json_encode(['success' => true, 'message' => 'Présence déjà enregistrée']);
            $check_stmt->close();
            $mysqli->close();
            exit;
        }
        $check_stmt->close();
        
        // Insérer la nouvelle présence
        $query = "INSERT INTO presences (professeur_id, date_presence, heure_arrivee, created_at, updated_at) 
                 VALUES (?, ?, ?, NOW(), NOW())";
        
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("iss", $professeur_id, $date_presence, $heure_arrivee);
            
            if ($stmt->execute()) {
                $presence_id = $mysqli->insert_id;
                echo json_encode(['success' => true, 'message' => 'Présence enregistrée avec succès', 'presence_id' => $presence_id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de la présence: ' . $stmt->error]);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur de préparation de la requête: ' . $mysqli->error]);
        }
        
        $mysqli->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
    exit;
}




/**
 * Recherche les présences des professeurs pour une période donnée
 */
public function rechercherPresencesProfesseurs() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        exit;
    }
    
    // Vérifier si la requête est AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $professeur_id = isset($_POST['professeur_id']) ? intval($_POST['professeur_id']) : 0;
        $date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : date('Y-m-d', strtotime('-30 days'));
        $date_fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : date('Y-m-d');
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
            exit;
        }
        
        // Construire la requête en fonction des paramètres
        $query = "SELECT p.id, p.professeur_id, p.date_presence, p.heure_arrivee, p.heure_depart, 
                 pr.nom, pr.prenom
                 FROM presences p
                 JOIN professeurs pr ON p.professeur_id = pr.id
                 WHERE p.date_presence BETWEEN ? AND ?";
        
        $params = [$date_debut, $date_fin];
        $types = "ss";
        
        if ($professeur_id > 0) {
            $query .= " AND p.professeur_id = ?";
            $params[] = $professeur_id;
            $types .= "i";
        }
        
        $query .= " ORDER BY p.date_presence DESC, p.heure_arrivee DESC";
        
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $presences = [];
                
                while ($row = $result->fetch_assoc()) {
                    $presences[] = $row;
                }
                
                echo json_encode(['success' => true, 'presences' => $presences]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la requête: ' . $stmt->error]);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur de préparation de la requête: ' . $mysqli->error]);
        }
        
        $mysqli->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
    exit;
}

/**
 * Ajoute une nouvelle présence pour un professeur
 */
public function ajouterPresenceProfesseur() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $professeur_id = isset($_POST['professeur_id']) ? intval($_POST['professeur_id']) : 0;
        $date_presence = isset($_POST['date_presence']) ? $_POST['date_presence'] : date('Y-m-d');
        $heure_arrivee = isset($_POST['heure_arrivee']) ? $_POST['heure_arrivee'] : date('H:i:s');
        $heure_depart = !empty($_POST['heure_depart']) ? $_POST['heure_depart'] : null;
        
        // Valider les données
        if ($professeur_id <= 0) {
            $_SESSION['error'] = "Veuillez sélectionner un professeur valide.";
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['error'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
            exit;
        }
        
        // Vérifier si une présence existe déjà pour ce professeur à cette date
        $check_query = "SELECT id FROM presences WHERE professeur_id = ? AND date_presence = ? AND heure_depart IS NULL";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("is", $professeur_id, $date_presence);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = "Ce professeur a déjà une présence en cours pour cette date.";
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
            $check_stmt->close();
            $mysqli->close();
            exit;
        }
        $check_stmt->close();
        
        // Préparer la requête d'insertion
        $query = "INSERT INTO presences (professeur_id, date_presence, heure_arrivee, heure_depart, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("isss", $professeur_id, $date_presence, $heure_arrivee, $heure_depart);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "La présence du professeur a été enregistrée avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de l'enregistrement de la présence: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $_SESSION['error'] = "Erreur de préparation de la requête: " . $mysqli->error;
        }
        
        $mysqli->close();
        
        // Rediriger vers la page des professeurs
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
        exit;
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page des professeurs
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
        exit;
    }
}

/**
 * Enregistre l'heure de départ d'un professeur
 */
public function enregistrerDepartProfesseur() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        exit;
    }
    
    // Vérifier si la requête est AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $presence_id = isset($_POST['presence_id']) ? intval($_POST['presence_id']) : 0;
        $heure_depart = isset($_POST['heure_depart']) ? $_POST['heure_depart'] : date('H:i:s');
        
        if ($presence_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de présence invalide']);
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
            exit;
        }
        
        // Mettre à jour l'heure de départ
        $query = "UPDATE presences SET heure_depart = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("si", $heure_depart, $presence_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Départ enregistré avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du départ']);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur de préparation de la requête']);
        }
        
        $mysqli->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
    exit;
}

/**
 * Supprime un enregistrement de présence
 */
public function supprimerPresenceProfesseur() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        exit;
    }
    
    // Vérifier si la requête est AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $presence_id = isset($_POST['presence_id']) ? intval($_POST['presence_id']) : 0;
        
        if ($presence_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de présence invalide']);
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
            exit;
        }
        
        // Supprimer l'enregistrement
        $query = "DELETE FROM presences WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $presence_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Présence supprimée avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la présence']);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur de préparation de la requête']);
        }
        
        $mysqli->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
    exit;
}


 public function ajouterEvenement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si c'est une requête AJAX
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            try {
                // Traitement du formulaire
                $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : '';
                $date_fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : '';
                $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : '';
                $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';
                $couleur = isset($_POST['couleur']) ? $_POST['couleur'] : '#3c8dbc';
                
                // Validation
                $errors = [];
                
                if (empty($titre)) {
                    $errors[] = "Le titre est requis.";
                }
                
                if (empty($date_debut)) {
                    $errors[] = "La date de début est requise.";
                }
                
                if (empty($date_fin)) {
                    $errors[] = "La date de fin est requise.";
                }
                
                if (empty($lieu)) {
                    $errors[] = "Le lieu est requis.";
                }
                
                if (empty($responsable)) {
                    $errors[] = "Le responsable est requis.";
                }
                
                // Convertir les dates si nécessaire
                if (!empty($date_debut) && strpos($date_debut, 'T') !== false) {
                    $date_debut = str_replace('T', ' ', $date_debut);
                    if (strlen($date_debut) === 16) {
                        $date_debut .= ':00';
                    }
                }
                
                if (!empty($date_fin) && strpos($date_fin, 'T') !== false) {
                    $date_fin = str_replace('T', ' ', $date_fin);
                    if (strlen($date_fin) === 16) {
                        $date_fin .= ':00';
                    }
                }
                
                // Vérifier que la date de fin est après la date de début
                if (!empty($date_debut) && !empty($date_fin)) {
                    if (strtotime($date_fin) <= strtotime($date_debut)) {
                        $errors[] = "La date de fin doit être postérieure à la date de début.";
                    }
                }
                
                // Vérifier que les dates sont valides
                if (!empty($date_debut) && !strtotime($date_debut)) {
                    $errors[] = "Format de date de début invalide.";
                }
                
                if (!empty($date_fin) && !strtotime($date_fin)) {
                    $errors[] = "Format de date de fin invalide.";
                }
                
                if (empty($errors)) {
                    // Instancier le modèle
                    require_once 'models/EvenementScolaire.php';
                    $evenementModel = new EvenementScolaire($this->db);
                    
                    // Ajouter l'événement
                    $result = $evenementModel->ajouterEvenement($titre, $description, $date_debut, $date_fin, $lieu, $responsable, $couleur);
                    
                    if ($result['success']) {
                        if ($isAjax) {
                            // Récupérer l'événement créé pour le retourner au calendrier
                            $nouvel_evenement = $evenementModel->getEvenementById($result['id']);
                            
                            // Formater pour FullCalendar
                            $calendar_event = [
                                'id' => $nouvel_evenement['id'],
                                'title' => $nouvel_evenement['titre'],
                                'start' => $nouvel_evenement['date_debut'],
                                'end' => $nouvel_evenement['date_fin'],
                                'backgroundColor' => $nouvel_evenement['couleur'],
                                'borderColor' => $nouvel_evenement['couleur'],
                                'allDay' => false,
                                'description' => $nouvel_evenement['description'],
                                'location' => $nouvel_evenement['lieu'],
                                'responsible' => $nouvel_evenement['responsable']
                            ];
                            
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => true,
                                'message' => "L'événement \"$titre\" a été ajouté avec succès.",
                                'event_id' => $result['id'],
                                'calendar_event' => $calendar_event
                            ]);
                            exit;
                        } else {
                            $_SESSION['message'] = "L'événement \"$titre\" a été ajouté avec succès.";
                            $_SESSION['message_type'] = 'success';
                            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=evenementsScolaires');
                            exit;
                        }
                    } else {
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => false,
                                'message' => "Erreur lors de l'ajout de l'événement: " . $this->db->error
                            ]);
                            exit;
                        } else {
                            $_SESSION['message'] = "Erreur lors de l'ajout de l'événement: " . $this->db->error;
                            $_SESSION['message_type'] = 'danger';
                        }
                    }
                } else {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => implode(" ", $errors)
                        ]);
                        exit;
                    } else {
                        $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
                        $_SESSION['message_type'] = 'danger';
                    }
                }
                
            } catch (Exception $e) {
                error_log("Erreur ajouterEvenement: " . $e->getMessage());
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
                    exit;
                } else {
                    $_SESSION['message'] = 'Erreur serveur: ' . $e->getMessage();
                    $_SESSION['message_type'] = 'danger';
                }
            }
        }
        
        // Si ce n'est pas AJAX, charger la vue du formulaire
        if (!isset($isAjax) || !$isAjax) {
            require_once 'views/directeur/ajouter_evenement.php';
        }
    }


/**
 * Ajoute un nouveau cours
 */
public function ajouterCours() {
    // Vérifier si l'utilisateur est connecté et a le rôle de directrice
    if (!$this->isLoggedIn() || $_SESSION['role'] !== 'directrice') {
        $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $classe_id = isset($_POST['classe_id']) ? intval($_POST['classe_id']) : 0;
        $professeur_id = isset($_POST['professeur_id']) ? intval($_POST['professeur_id']) : 0;
        $jour = isset($_POST['jour']) ? trim($_POST['jour']) : '';
        $heure_debut = isset($_POST['heure_debut']) ? trim($_POST['heure_debut']) : '';
        $heure_fin = isset($_POST['heure_fin']) ? trim($_POST['heure_fin']) : '';
        
        // Validation des données
        $errors = [];
        if (empty($titre)) {
            $errors[] = "Le titre du cours est requis.";
        }
        if ($classe_id <= 0) {
            $errors[] = "Veuillez sélectionner une classe valide.";
        }
        if ($professeur_id <= 0) {
            $errors[] = "Veuillez sélectionner un professeur valide.";
        }
        if (empty($jour)) {
            $errors[] = "Le jour du cours est requis.";
        }
        if (empty($heure_debut)) {
            $errors[] = "L'heure de début est requise.";
        }
        if (empty($heure_fin)) {
            $errors[] = "L'heure de fin est requise.";
        }
        
        // Si pas d'erreurs, insérer le cours dans la base de données
        if (empty($errors)) {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['message_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=cours');
                exit;
            }
            
            // Vérifier que la classe existe et appartient à la section maternelle
            $check_classe_query = "SELECT id FROM classes WHERE id = ? AND section = 'maternelle'";
            $check_classe_stmt = $mysqli->prepare($check_classe_query);
            $check_classe_stmt->bind_param("i", $classe_id);
            $check_classe_stmt->execute();
            $check_classe_result = $check_classe_stmt->get_result();
            
            if ($check_classe_result->num_rows === 0) {
                $errors[] = "La classe sélectionnée n'existe pas ou n'appartient pas à la section maternelle.";
                $check_classe_stmt->close();
            } else {
                $check_classe_stmt->close();
                
                // Vérifier que le professeur existe et appartient à la section maternelle
                $check_prof_query = "SELECT id FROM professeurs WHERE id = ? AND section = 'maternelle'";
                $check_prof_stmt = $mysqli->prepare($check_prof_query);
                $check_prof_stmt->bind_param("i", $professeur_id);
                $check_prof_stmt->execute();
                $check_prof_result = $check_prof_stmt->get_result();
                
                if ($check_prof_result->num_rows === 0) {
                    $errors[] = "Le professeur sélectionné n'existe pas ou n'appartient pas à la section maternelle.";
                    $check_prof_stmt->close();
                } else {
                    $check_prof_stmt->close();
                    
                    // Vérifier s'il y a un conflit d'horaire pour le professeur
                    $check_conflit_query = "SELECT id FROM cours 
                                          WHERE professeur_id = ? AND jour = ? 
                                          AND ((heure_debut <= ? AND heure_fin > ?) 
                                          OR (heure_debut < ? AND heure_fin >= ?) 
                                          OR (heure_debut >= ? AND heure_fin <= ?))";
                    $check_conflit_stmt = $mysqli->prepare($check_conflit_query);
                    $check_conflit_stmt->bind_param("isssssss", $professeur_id, $jour, $heure_fin, $heure_debut, $heure_fin, $heure_debut, $heure_debut, $heure_fin);
                    $check_conflit_stmt->execute();
                    $check_conflit_result = $check_conflit_stmt->get_result();
                    
                    if ($check_conflit_result->num_rows > 0) {
                        $errors[] = "Il y a un conflit d'horaire pour ce professeur à ce moment.";
                    }
                    $check_conflit_stmt->close();
                }
            }
            
            // Si toujours pas d'erreurs, insérer le cours
            if (empty($errors)) {
                $insert_query = "INSERT INTO cours (titre, description, classe_id, professeur_id, jour, heure_debut, heure_fin, date_creation) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                $insert_stmt = $mysqli->prepare($insert_query);
                $insert_stmt->bind_param("ssiisss", $titre, $description, $classe_id, $professeur_id, $jour, $heure_debut, $heure_fin);
                
                if ($insert_stmt->execute()) {
                    $_SESSION['message'] = "Le cours a été ajouté avec succès.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Erreur lors de l'ajout du cours: " . $mysqli->error;
                    $_SESSION['message_type'] = "danger";
                }
                
                $insert_stmt->close();
            }
            
            $mysqli->close();
        }
        
        // S'il y a des erreurs, les afficher
        if (!empty($errors)) {
            $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
            $_SESSION['message_type'] = "danger";
        }
        
        // Rediriger vers la page des cours
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=cours');
        exit;
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page des cours
        header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=cours');
        exit;
    }
}


}


?>
