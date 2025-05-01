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
    
    // Afficher la page de gestion des emplois du temps
    public function emploiDuTemps() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=accueil');
            exit;
        }
        
        // Récupérer uniquement les classes de la section secondaire pour le formulaire de sélection
        $classes = [];
        $classes_query = "SELECT id, nom FROM classes WHERE section = 'secondaire' ORDER BY nom ASC";
        $classes_result = $mysqli->query($classes_query);
        if ($classes_result) {
            while ($row = $classes_result->fetch_assoc()) {
                $classes[] = $row;
            }
        }
        
        // Récupérer les cours pour l'emploi du temps si une classe est sélectionnée
        $emploi_du_temps = [];
        $cours_disponibles = [];
        $classe_id = isset($_GET['classe_id']) ? intval($_GET['classe_id']) : 0;
        
        if ($classe_id > 0) {
            // Vérifier que la classe sélectionnée est bien dans la section secondaire
            $check_classe_query = "SELECT id FROM classes WHERE id = ? AND section = 'secondaire'";
            $check_classe_stmt = $mysqli->prepare($check_classe_query);
            $check_classe_stmt->bind_param("i", $classe_id);
            $check_classe_stmt->execute();
            $check_classe_result = $check_classe_stmt->get_result();
            
            if ($check_classe_result->num_rows === 0) {
                $_SESSION['error_message'] = "La classe sélectionnée n'appartient pas à la section secondaire.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=emploiDuTemps');
                exit;
            }
            
            $check_classe_stmt->close();
            
            // Récupérer uniquement les cours disponibles pour cette classe avec les professeurs du secondaire
            $cours_query = "SELECT c.id, c.titre, p.nom AS prof_nom, p.prenom AS prof_prenom 
                            FROM cours c
                            JOIN professeurs p ON c.professeur_id = p.id
                            WHERE c.classe_id = ? AND p.section = 'secondaire'
                            ORDER BY c.titre ASC";
            
            $cours_stmt = $mysqli->prepare($cours_query);
            $cours_stmt->bind_param("i", $classe_id);
            $cours_stmt->execute();
            $cours_result = $cours_stmt->get_result();
            
            while ($row = $cours_result->fetch_assoc()) {
                $cours_disponibles[] = $row;
            }
            
            $cours_stmt->close();
            
            // Récupérer l'emploi du temps existant avec uniquement les professeurs du secondaire
            $query = "SELECT e.*, c.titre, p.nom AS prof_nom, p.prenom AS prof_prenom 
                      FROM horaires e
                      JOIN cours c ON e.cours_id = c.id
                      JOIN professeurs p ON c.professeur_id = p.id
                      WHERE e.classe_id = ? AND p.section = 'secondaire'
                      ORDER BY e.jour, e.heure_debut";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("i", $classe_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Organiser les données par jour et heure
            while ($row = $result->fetch_assoc()) {
                $jour = $row['jour'];
                $heure = $row['heure_debut'] . '-' . $row['heure_fin'];
                $emploi_du_temps[$jour][$heure] = $row;
            }
            
            $stmt->close();
        }
        
        $mysqli->close();
        
        // Charger la vue de l'emploi du temps
        require_once 'views/prefet/emploiDuTemps.php';
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
            $query = "INSERT INTO evenements_scolaires (titre, type, date_debut, date_fin, classe_id, lieu, description) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sssssss", $titre, $type, $date_debut, $date_fin, $classe_id, $lieu, $description);
                
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
        /**
     * Ajouter un nouveau cours
     */
    public function ajouterCours() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Si la méthode est GET, afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=cours');
                exit;
            }
            
            // Récupérer uniquement les professeurs de la section secondaire pour le formulaire
            $professeurs = [];
            $professeurs_query = "SELECT id, nom, prenom FROM professeurs WHERE section = 'secondaire' ORDER BY nom ASC";
            $professeurs_result = $mysqli->query($professeurs_query);
            if ($professeurs_result) {
                while ($row = $professeurs_result->fetch_assoc()) {
                    $professeurs[] = $row;
                }
            }
            
            // Récupérer uniquement les classes de la section secondaire pour le formulaire
            $classes = [];
            $classes_query = "SELECT id, nom FROM classes WHERE section = 'secondaire' ORDER BY nom ASC";
            $classes_result = $mysqli->query($classes_query);
            if ($classes_result) {
                while ($row = $classes_result->fetch_assoc()) {
                    $classes[] = $row;
                }
            }
            
            $mysqli->close();
            
            // Inclure la vue du formulaire d'ajout de cours
            include 'views/prefet/ajouterCours.php';
            return;
        }
        
        // Si la méthode est POST, traiter le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
            $classe_id = isset($_POST['classe_id']) ? intval($_POST['classe_id']) : 0;
            $professeur_id = isset($_POST['professeur_id']) ? intval($_POST['professeur_id']) : 0;
            $coefficient = isset($_POST['coefficient']) ? intval($_POST['coefficient']) : 1;
            $heures_semaine = isset($_POST['heures_semaine']) ? intval($_POST['heures_semaine']) : 2;
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            
            // Validation des données
            if (empty($nom) || $classe_id <= 0 || $professeur_id <= 0) {
                $_SESSION['error_message'] = "Veuillez remplir tous les champs obligatoires.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=cours');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=cours');
                exit;
            }
            
            // Vérifier si le cours existe déjà pour cette classe
            $check_query = "SELECT id FROM cours WHERE titre = ? AND classe_id = ?";
            $check_stmt = $mysqli->prepare($check_query);
            $check_stmt->bind_param("si", $nom, $classe_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $_SESSION['error_message'] = "Un cours avec ce nom existe déjà pour cette classe.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=cours');
                exit;
            }
            
            // Insérer le nouveau cours
            $insert_query = "INSERT INTO cours (titre, classe_id, professeur_id, coefficient, heures_semaine, description) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("siiiss", $nom, $classe_id, $professeur_id, $coefficient, $heures_semaine, $description);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Le cours a été ajouté avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'ajout du cours: " . $mysqli->error;
            }
            
            $stmt->close();
            $mysqli->close();
            
            // Redirection vers la liste des cours
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=cours');
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
    
    public function voirEleve() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID de l'élève est fourni
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=eleves');
            exit;
        }
        
        // Charger la vue du profil de l'élève
        require 'views/prefet/voirEleve.php';
    }
    
    /**
     * Ajoute une nouvelle absence d'élève
     */
    public function ajouterAbsence() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
            $date_absence = isset($_POST['date_absence']) ? $_POST['date_absence'] : '';
            $motif = isset($_POST['motif']) ? $_POST['motif'] : '';
            $justifiee = isset($_POST['justifiee']) ? 1 : 0;
            
            // Validation des données
            $errors = [];
            
            if ($eleve_id <= 0) {
                $errors[] = "Veuillez sélectionner un élève valide.";
            }
            
            if (empty($date_absence)) {
                $errors[] = "La date d'absence est requise.";
            } else {
                // Convertir la date du format français (dd/mm/yyyy) au format MySQL (yyyy-mm-dd)
                $date_parts = explode('/', $date_absence);
                if (count($date_parts) === 3) {
                    $date_absence = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
                } else {
                    $errors[] = "Format de date invalide.";
                }
            }
            
            // Si pas d'erreurs, insérer l'absence dans la base de données
            if (empty($errors)) {
                $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }
                
                // Préparer la requête d'insertion
                $query = "INSERT INTO absences (eleve_id, date_absence, motif, justifiee) VALUES (?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("issi", $eleve_id, $date_absence, $motif, $justifiee);
                    
                    if ($stmt->execute()) {
                        // Rediriger avec un message de succès
                        $_SESSION['flash_message'] = "L'absence a été ajoutée avec succès.";
                        $_SESSION['flash_type'] = "success";
                    } else {
                        // Erreur lors de l'insertion
                        $_SESSION['flash_message'] = "Erreur lors de l'ajout de l'absence: " . $stmt->error;
                        $_SESSION['flash_type'] = "danger";
                    }
                    
                    $stmt->close();
                } else {
                    $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $mysqli->close();
            } else {
                // Stocker les erreurs dans la session
                $_SESSION['flash_message'] = implode("<br>", $errors);
                $_SESSION['flash_type'] = "danger";
            }
            
            // Rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        }
    }
    
    /**
     * Modifie une absence existante
     */
    public function modifierAbsence() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $absence_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $motif = isset($_POST['motif']) ? $_POST['motif'] : '';
            $justifiee = isset($_POST['justifiee']) ? 1 : 0;
            
            // Validation des données
            if ($absence_id <= 0) {
                $_SESSION['flash_message'] = "ID d'absence invalide.";
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
                exit;
            }
            
            // Mettre à jour l'absence dans la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
                exit;
            }
            
            // Préparer la requête de mise à jour
            $query = "UPDATE absences SET motif = ?, justifiee = ? WHERE id = ?";
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sii", $motif, $justifiee, $absence_id);
                
                if ($stmt->execute()) {
                    // Rediriger avec un message de succès
                    $_SESSION['flash_message'] = "L'absence a été modifiée avec succès.";
                    $_SESSION['flash_type'] = "success";
                } else {
                    // Erreur lors de la mise à jour
                    $_SESSION['flash_message'] = "Erreur lors de la modification de l'absence: " . $stmt->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $mysqli->close();
            
            // Rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        }
    }
    
    /**
     * Supprime une absence
     */
    public function supprimerAbsence() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider l'ID de l'absence
            $absence_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            // Validation des données
            if ($absence_id <= 0) {
                $_SESSION['flash_message'] = "ID d'absence invalide.";
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
                exit;
            }
            
            // Supprimer l'absence de la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
                exit;
            }
            
            // Préparer la requête de suppression
            $query = "DELETE FROM absences WHERE id = ?";
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("i", $absence_id);
                
                if ($stmt->execute()) {
                    // Rediriger avec un message de succès
                    $_SESSION['flash_message'] = "L'absence a été supprimée avec succès.";
                    $_SESSION['flash_type'] = "success";
                } else {
                    // Erreur lors de la suppression
                    $_SESSION['flash_message'] = "Erreur lors de la suppression de l'absence: " . $stmt->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $mysqli->close();
            
            // Rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        }
    }
    
    public function ajouterIncident() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
            $date_incident = isset($_POST['date_incident']) ? $_POST['date_incident'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $sanction = isset($_POST['sanction']) ? $_POST['sanction'] : '';
            $statut = isset($_POST['statut']) ? $_POST['statut'] : 'En cours';
            
            // Convertir la date au format MySQL (YYYY-MM-DD)
            $date_formattee = date('Y-m-d', strtotime(str_replace('/', '-', $date_incident)));
            
            // Validation des données
            $erreurs = [];
            
            if (empty($eleve_id)) {
                $erreurs[] = "L'élève est obligatoire";
            }
            
            if (empty($date_incident)) {
                $erreurs[] = "La date de l'incident est obligatoire";
            }
            
            if (empty($description)) {
                $erreurs[] = "La description est obligatoire";
            }
            
            // Si pas d'erreurs, insérer dans la base de données
            if (empty($erreurs)) {
                // Connexion à la base de données
                $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }
                
                // Préparer la requête d'insertion
                $query = "INSERT INTO incidents_disciplinaires (eleve_id, date_incident, description, sanction, statut) 
                          VALUES (?, ?, ?, ?, ?)";
                
                $stmt = $mysqli->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("issss", $eleve_id, $date_formattee, $description, $sanction, $statut);
                    
                    if ($stmt->execute()) {
                        // Succès
                        $_SESSION['flash_message'] = "L'incident disciplinaire a été ajouté avec succès.";
                        $_SESSION['flash_type'] = "success";
                    } else {
                        // Erreur
                        $_SESSION['flash_message'] = "Erreur lors de l'ajout de l'incident: " . $stmt->error;
                        $_SESSION['flash_type'] = "danger";
                    }
                    
                    $stmt->close();
                } else {
                    // Erreur de préparation de la requête
                    $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $mysqli->close();
            } else {
                // Afficher les erreurs
                $_SESSION['flash_message'] = "Erreurs dans le formulaire: " . implode(", ", $erreurs);
                $_SESSION['flash_type'] = "danger";
            }
            
            // Rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        }
    }
    
    public function modifierIncident() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $sanction = isset($_POST['sanction']) ? $_POST['sanction'] : '';
            $statut = isset($_POST['statut']) ? $_POST['statut'] : 'En cours';
            
            // Validation des données
            $erreurs = [];
            
            if (empty($incident_id)) {
                $erreurs[] = "ID d'incident invalide";
            }
            
            if (empty($description)) {
                $erreurs[] = "La description est obligatoire";
            }
            
            // Si pas d'erreurs, mettre à jour dans la base de données
            if (empty($erreurs)) {
                // Connexion à la base de données
                $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }
                
                // Préparer la requête de mise à jour
                $query = "UPDATE incidents_disciplinaires 
                          SET description = ?, sanction = ?, statut = ? 
                          WHERE id = ?";
                
                $stmt = $mysqli->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("sssi", $description, $sanction, $statut, $incident_id);
                    
                    if ($stmt->execute()) {
                        // Succès
                        $_SESSION['flash_message'] = "L'incident disciplinaire a été modifié avec succès.";
                        $_SESSION['flash_type'] = "success";
                    } else {
                        // Erreur
                        $_SESSION['flash_message'] = "Erreur lors de la modification de l'incident: " . $stmt->error;
                        $_SESSION['flash_type'] = "danger";
                    }
                    
                    $stmt->close();
                } else {
                    // Erreur de préparation de la requête
                    $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $mysqli->close();
            } else {
                // Afficher les erreurs
                $_SESSION['flash_message'] = "Erreurs dans le formulaire: " . implode(", ", $erreurs);
                $_SESSION['flash_type'] = "danger";
            }
            
            // Rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        }
    }
    
    public function supprimerIncident() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider l'ID de l'incident
            $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            // Validation des données
            if (empty($incident_id)) {
                $_SESSION['flash_message'] = "ID d'incident invalide.";
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
                exit;
            }
            
            // Préparer la requête de suppression
            $query = "DELETE FROM incidents_disciplinaires WHERE id = ?";
            
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("i", $incident_id);
                
                if ($stmt->execute()) {
                    // Succès
                    $_SESSION['flash_message'] = "L'incident disciplinaire a été supprimé avec succès.";
                    $_SESSION['flash_type'] = "success";
                } else {
                    // Erreur
                    $_SESSION['flash_message'] = "Erreur lors de la suppression de l'incident: " . $stmt->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                // Erreur de préparation de la requête
                $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $mysqli->close();
            
            // Rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        }
    }
    
    
    // Afficher la carte d'élève
    public function carteEleve() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue de la carte d'élève
        require_once 'views/prefet/carte_eleve.php';
    }
    
    /**
     * Affiche le profil détaillé d'un professeur
     */
    public function voirProfesseur() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Vérifier si l'ID est valide
        if ($id <= 0) {
            $_SESSION['message'] = "ID de professeur invalide.";
            $_SESSION['message_type'] = "error";
            header("Location: " . BASE_URL . "index.php?controller=Prefet&action=professeurs");
            exit;
        }
        
        // Récupérer les données du professeur
// Connect to database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get professor data
$query = "SELECT * FROM professeurs WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$professeur = $result->fetch_assoc();

$stmt->close();
$mysqli->close();
        
        // Débogage
        if (isset($_GET['debug']) && $_GET['debug'] == 1) {
            echo "<pre>";
            print_r($professeur);
            echo "</pre>";
        }
        
        // Récupérer les cours du professeur
// Connect to database to get professor's courses
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get courses data
$query = "SELECT * FROM cours WHERE professeur_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cours = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
        
        // Charger la vue
        require_once('views/prefet/voirProfesseur.php');
    }
    
    /**
     * Affiche les élèves d'une classe spécifique
     */
    public function voirEleves() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID de classe est fourni
        if (!isset($_GET['classe']) || empty($_GET['classe'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=classes');
            exit;
        }
        
        // Récupérer l'ID de la classe
        $classe_id = (int)$_GET['classe'];
        
        // Charger la vue des élèves de la classe
        require_once 'views/prefet/eleves_classe.php';
    }
    
    /**
     * Marque directement un élève comme absent pour la journée
     */
    public function marquerAbsent() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID de l'élève est fourni
        if (!isset($_GET['eleve']) || empty($_GET['eleve'])) {
            $_SESSION['error_message'] = "Aucun élève spécifié.";
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=classes');
            exit;
        }
        
        // Récupérer l'ID de l'élève
        $eleve_id = (int)$_GET['eleve'];
        
        // Récupérer l'ID de la classe pour la redirection
        $classe_id = isset($_GET['classe']) ? (int)$_GET['classe'] : 0;
        
        // Date d'aujourd'hui
        $date = date('Y-m-d');
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=classes');
            exit;
        }
        
        // Vérifier si l'élève existe et est dans une classe du secondaire
        $check_query = "SELECT e.id, e.nom, e.prenom, c.nom as classe_nom 
                       FROM eleves e 
                       JOIN classes c ON e.classe_id = c.id 
                       WHERE e.id = ? AND e.section = 'secondaire'";
        $stmt = $mysqli->prepare($check_query);
        $stmt->bind_param("i", $eleve_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION['error_message'] = "Élève non trouvé ou n'appartient pas à une classe du secondaire.";
            $mysqli->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=classes');
            exit;
        }
        
        $eleve = $result->fetch_assoc();
        
        // Vérifier si l'absence existe déjà pour aujourd'hui
        $check_absence_query = "SELECT id FROM absences WHERE eleve_id = ? AND date_absence = ?";
        $stmt = $mysqli->prepare($check_absence_query);
        $stmt->bind_param("is", $eleve_id, $date);
        $stmt->execute();
        $absence_result = $stmt->get_result();
        
        if ($absence_result->num_rows > 0) {
            // L'absence existe déjà
            $_SESSION['info_message'] = "L'élève " . $eleve['prenom'] . " " . $eleve['nom'] . " est déjà marqué absent pour aujourd'hui.";
        } else {
            // Insérer la nouvelle absence
            $insert_query = "INSERT INTO absences (eleve_id, date_absence, motif, justifiee, date_creation) 
                            VALUES (?, ?, 'Non spécifié', 0, NOW())";
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("is", $eleve_id, $date);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "L'élève " . $eleve['prenom'] . " " . $eleve['nom'] . " a été marqué absent pour aujourd'hui.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'enregistrement de l'absence: " . $mysqli->error;
            }
        }
        
        $mysqli->close();
        
        // Redirection
        if (isset($_GET['redirect']) && $_GET['redirect'] == 1 && $classe_id > 0) {
            // Rediriger vers la page de la classe
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=voirEleves&classe=' . $classe_id);
        } else {
            // Rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
        }
        exit;
    }
    // ... existing code ...

public function presenceProfesseur() {
    // Vérifier si l'utilisateur est connecté et a les droits
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Récupérer l'ID du professeur
    $professeurId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($professeurId <= 0) {
        $_SESSION['error'] = "ID de professeur invalide.";
        header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=professeurs');
        exit;
    }
    
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    // Récupérer les informations du professeur
    $query = "SELECT * FROM professeurs WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $professeurId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Professeur non trouvé.";
        header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=professeurs');
        exit;
    }
    
    $professeur = $result->fetch_assoc();
    
    // Traitement du formulaire de présence
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = $_POST['date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'present';
        $commentaire = $_POST['commentaire'] ?? '';
        
        // Vérifier si une entrée existe déjà pour cette date et ce professeur
        $check_query = "SELECT id FROM presences_professeurs WHERE professeur_id = ? AND date = ?";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("is", $professeurId, $date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Mettre à jour l'entrée existante
            $presence_id = $check_result->fetch_assoc()['id'];
            $update_query = "UPDATE presences_professeurs SET status = ?, commentaire = ? WHERE id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param("ssi", $status, $commentaire, $presence_id);
            $update_stmt->execute();
        } else {
            // Créer une nouvelle entrée
            $insert_query = "INSERT INTO presences_professeurs (professeur_id, date, status, commentaire) VALUES (?, ?, ?, ?)";
            $insert_stmt = $mysqli->prepare($insert_query);
            $insert_stmt->bind_param("isss", $professeurId, $date, $status, $commentaire);
            $insert_stmt->execute();
        }
        
        $_SESSION['success'] = "Présence enregistrée avec succès.";
        header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=presenceProfesseur&id=' . $professeurId);
        exit;
    }
    
    // Récupérer l'historique des présences
    $presences_query = "SELECT * FROM presences_professeurs WHERE professeur_id = ? ORDER BY date DESC LIMIT 30";
    $presences_stmt = $mysqli->prepare($presences_query);
    $presences_stmt->bind_param("i", $professeurId);
    $presences_stmt->execute();
    $presences_result = $presences_stmt->get_result();
    $presences = [];
    
    while ($row = $presences_result->fetch_assoc()) {
        $presences[] = $row;
    }
    
    $mysqli->close();
    
    // Charger la vue
    require_once 'views/prefet/presence_professeur.php';
}

// ... existing code ...
    
    

    /**
     * Affiche les élèves d'une classe spécifique
     */
   
    /**
     * Ajoute un horaire à l'emploi du temps
     */
    public function ajouterHoraire() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Méthode non autorisée.";
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=emploiDuTemps');
            exit;
        }
        
        // Récupérer les données du formulaire
        $classe_id = isset($_POST['classe_id']) ? intval($_POST['classe_id']) : 0;
        $jour = isset($_POST['jour']) ? trim($_POST['jour']) : '';
        $heure = isset($_POST['heure']) ? trim($_POST['heure']) : '';
        $cours_id = isset($_POST['cours_id']) ? intval($_POST['cours_id']) : 0;
        
        // Validation minimale - seul le cours_id est obligatoire
        if ($cours_id <= 0) {
            $_SESSION['error_message'] = "Veuillez sélectionner un cours.";
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=emploiDuTemps&classe_id=' . $classe_id);
            exit;
        }
        
        // Si le jour ou l'heure ne sont pas spécifiés, utiliser des valeurs par défaut
        if (empty($jour)) {
            $jour = 'Lundi'; // Valeur par défaut pour le jour
        }
        
        if (empty($heure)) {
            $heure = '08:00-09:00'; // Valeur par défaut pour l'heure
        }
        
        // Extraire les heures de début et de fin
        $heures_parts = explode('-', $heure);
        $heure_debut = isset($heures_parts[0]) ? trim($heures_parts[0]) : '08:00';
        $heure_fin = isset($heures_parts[1]) ? trim($heures_parts[1]) : '09:00';
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=emploiDuTemps&classe_id=' . $classe_id);
            exit;
        }
        
        // Vérifier si un cours existe déjà à cette heure et ce jour pour cette classe
        $check_query = "SELECT id FROM horaires WHERE classe_id = ? AND jour = ? AND heure_debut = ? AND heure_fin = ?";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("isss", $classe_id, $jour, $heure_debut, $heure_fin);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Si un cours existe déjà, le mettre à jour au lieu d'en ajouter un nouveau
            $row = $check_result->fetch_assoc();
            $horaire_id = $row['id'];
            
            $update_query = "UPDATE horaires SET cours_id = ? WHERE id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param("ii", $cours_id, $horaire_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['success_message'] = "L'horaire a été mis à jour avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour de l'horaire: " . $mysqli->error;
            }
            
            $update_stmt->close();
        } else {
            // Insérer le nouvel horaire
            $insert_query = "INSERT INTO horaires (classe_id, cours_id, jour, heure_debut, heure_fin) 
                        VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("iisss", $classe_id, $cours_id, $jour, $heure_debut, $heure_fin);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "L'horaire a été ajouté avec succès à l'emploi du temps.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'ajout de l'horaire: " . $mysqli->error;
            }
            
            $stmt->close();
        }
        
        $check_stmt->close();
        $mysqli->close();
        
        // Redirection vers la page d'emploi du temps avec la classe sélectionnée
        header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=emploiDuTemps&classe_id=' . $classe_id);
        exit;
    }
  
}
?>
