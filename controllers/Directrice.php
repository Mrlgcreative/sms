<?php
class Directrice {
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
            $check_absence_query = "SELECT id FROM absences WHERE eleve_id = ? AND date_absence = ?";
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
            $insert_query = "INSERT INTO absences (eleve_id, date_absence, motif, justifiee, date_creation) 
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
        $check_query = "SELECT a.id FROM absences a 
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
        $update_query = "UPDATE absences SET motif = ?, justifiee = ? WHERE id = ?";
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
        $check_query = "SELECT a.id FROM absences a 
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
        $delete_query = "DELETE FROM absences WHERE id = ?";
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
}


?>
