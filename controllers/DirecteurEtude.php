<?php
require 'config/database.php';
require 'models/EleveModel.php';
require 'models/ProfesseurModel.php';
require 'models/FraisModel.php';
require 'models/HistoriqueModel.php';
require 'models/UserModel.php';
require 'models/ParentModel.php';
require 'models/CoursModel.php';
require 'models/ClasseModel.php';
require 'models/ComptableModel.php';
require 'models/DirectorModel.php';
require 'models/DirectriceModel.php';
require 'models/PrefetModel.php';
require 'models/EmployeModel.php';
require 'models/SessionScolaireModel.php';
class DirecteurEtude {

    private $eleveModel;
    private $professeurModel;
    private $fraisModel;
    private $historiqueModel;
    private $userModel;
    private $parentModel;
    private $coursModel;
    private $classeModel;
    private $comptableModel;
    private $directorModel;
    private $directriceModel;
    private $prefetModel;
    private $sessionscolaireModel;
    private $employeModel;
    private $db;

    public function __construct() {
        $this->eleveModel = new EleveModel();
        $this->professeurModel = new ProfesseurModel();
        $this->fraisModel = new FraisModel();
        $this->historiqueModel = new HistoriqueModel();
        $this->userModel = new UserModel();
        $this->parentModel = new ParentModel();
        $this->coursModel = new CoursModel();
        $this->classeModel = new ClasseModel();
        $this->comptableModel=new ComptableModel();
        $this->directorModel= new DirectorModel();  
        $this->directriceModel=new DirectriceModel();
        $this->prefetModel=new PrefetModel();
        $this->sessionscolaireModel=new SessionScolaireModel();
     }
    
    // Fonction utilitaire pour vérifier l'authentification du directeur d'études
    private function checkAuth() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directeur_Etude') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
    }    
    // Afficher la page d'accueil du directeur d'études
    public function accueil() {
        $this->checkAuth();
        
        // Charger la vue du tableau de bord
        require_once 'views/directeur_etudes/accueil.php';
    }
    
    // Afficher l'emploi du temps des classes primaires et secondaires
    public function emploiDuTemps() {
        $this->checkAuth();
        
        // Récupérer la classe sélectionnée depuis l'URL
        $classe_id = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : 0;
        
        // Charger la vue de l'emploi du temps
        require_once 'views/directeur_etudes/emploiDuTemps.php';
    }
    
    // Afficher la liste des élèves
    public function eleves() {
        $this->checkAuth();
        
        // Récupérer l'ID de la session scolaire depuis la requête GET, s'il existe
        $session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : null;

        // Récupérer tous les élèves ou ceux filtrés par session
        $lesEleves = $this->eleveModel->getAll($session_id);
        
        // Récupérer toutes les sessions scolaires pour le dropdown
        $sessions_scolaires = $this->sessionscolaireModel->getAll();

        $data = [
            'eleves' => $lesEleves,
            'sessions_scolaires' => $sessions_scolaires,
            'current_session_id' => $session_id
        ];
        require_once 'views/directeur_etudes/eleves.php';
    }
    
    // Afficher la liste des professeurs
    public function professeurs() {
        $this->checkAuth();
        
        // Charger la vue des professeurs
        require_once 'views/directeur_etudes/professeurs.php';
    }
    
    // Afficher la liste des classes
    public function classes() {
        $this->checkAuth();
        
        // Charger la vue des classes
        require_once 'views/directeur_etudes/classes.php';
    }
    
    // Afficher la liste des cours
    public function cours() {
        $this->checkAuth();
        
        // Charger la vue des cours
        require_once 'views/directeur_etudes/cours.php';
    }
    
    // Afficher la page des événements scolaires
    public function evenementsScolaires() {
        $this->checkAuth();
        
        // Charger la vue des événements scolaires
        require_once 'views/directeur_etudes/evenementsScolaires.php';
    }
    
    // Ajouter un nouvel événement scolaire
    public function ajouterEvenement() {
        $this->checkAuth();
        
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
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
                exit;
            }
            
            // Vérifier que la date de fin est après la date de début
            if (strtotime($date_fin) <= strtotime($date_debut)) {
                $_SESSION['error_message'] = "La date de fin doit être postérieure à la date de début.";
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
                exit;
            }
            
            // Préparer la requête SQL
            $query = "INSERT INTO evenements_scolaires (titre, type, date_debut, date_fin,  lieu, description) 
                      VALUES (?, ?, ?, ?,  ?, ?)";
            
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("ssssss", $titre, $type, $date_debut, $date_fin,  $lieu, $description);
                
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
            exit;
        } else {
            // Si le formulaire n'a pas été soumis, rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
            exit;
        }
    }
    
    // Modifier un événement scolaire existant
    public function modifierEvenement() {
        $this->checkAuth();
        
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
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
                exit;
            }
            
            // Vérifier que la date de fin est après la date de début
            if (strtotime($date_fin) <= strtotime($date_debut)) {
                $_SESSION['error_message'] = "La date de fin doit être postérieure à la date de début.";
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
            exit;
        } else {
            // Si le formulaire n'a pas été soumis, rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
            exit;
        }
    }
    
    // Supprimer un événement scolaire
    public function supprimerEvenement() {
        $this->checkAuth();
        
        // Récupérer l'ID de l'événement à supprimer
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $_SESSION['error_message'] = "ID d'événement invalide.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
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
        header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=evenementsScolaires');
        exit;
    }
    
    // Ajouter un nouveau cours
    public function ajouterCours() {
        $this->checkAuth();
        
        // Si la méthode est GET, afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=cours');
                exit;
            }
            
            // Récupérer tous les professeurs (primaire et secondaire) pour le formulaire
            $professeurs = [];
            $professeurs_query = "SELECT id, nom, prenom FROM professeurs ORDER BY nom ASC";
            $professeurs_result = $mysqli->query($professeurs_query);
            if ($professeurs_result) {
                while ($row = $professeurs_result->fetch_assoc()) {
                    $professeurs[] = $row;
                }
            }
            
            // Récupérer toutes les classes (primaire et secondaire) pour le formulaire
            $classes = [];
            $classes_query = "SELECT id, nom FROM classes ORDER BY nom ASC";
            $classes_result = $mysqli->query($classes_query);
            if ($classes_result) {
                while ($row = $classes_result->fetch_assoc()) {
                    $classes[] = $row;
                }
            }
            
            $mysqli->close();
            
            // Inclure la vue du formulaire d'ajout de cours
            include 'views/directeur_etudes/ajouterCours.php';
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
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=cours');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=cours');
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
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=cours');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=cours');
            exit;
        }
    }
    
    // Afficher la page de gestion des absences
    public function absences() {
        $this->checkAuth();
        
        // Charger la vue des absences
        require_once 'views/directeur_etudes/absences.php';
    }
    
    // Afficher la page de gestion de la discipline
    public function discipline() {
        $this->checkAuth();
        
        // Charger la vue de la discipline
        require_once 'views/directeur_etudes/discipline.php';
    }
    
    // Afficher le profil du directeur d'études
    public function profil() {
        $this->checkAuth();
        
        // Charger la vue du profil
        require_once 'views/directeur_etudes/profil.php';
    }    // Afficher les détails d'un élève spécifique
    public function voirEleve($id = null) {
        $this->checkAuth();

        // Récupérer l'ID de l'élève depuis la requête GET si non passé en argument
        if ($id === null && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
        } elseif ($id === null) {
            $_SESSION['error_message'] = "Aucun identifiant d'élève fourni.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=eleves');
            exit;
        }

        // Récupérer les informations de base de l'élève
        $eleve = $this->eleveModel->getById($id);

        if (!$eleve) {
            $_SESSION['error_message'] = "Élève non trouvé.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=eleves');
            exit;
        }

        $data = [
            'eleve' => $eleve,
        ];

        require_once 'views/directeur_etudes/view_student.php';
    }
    
    // Ajouter une nouvelle absence d'élève
    public function ajouterAbsence() {
        $this->checkAuth();
        
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
            exit;
        }
    }
    
    // Modifier une absence existante
    public function modifierAbsence() {
        $this->checkAuth();
        
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
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
                exit;
            }
            
            // Mettre à jour l'absence dans la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
            exit;
        }
    }
    
    // Supprimer une absence
    public function supprimerAbsence() {
        $this->checkAuth();
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider l'ID de l'absence
            $absence_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            // Validation des données
            if ($absence_id <= 0) {
                $_SESSION['flash_message'] = "ID d'absence invalide.";
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
                exit;
            }
            
            // Supprimer l'absence de la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
            exit;
        }
    }
    
    // Ajouter un incident disciplinaire
    public function ajouterIncident() {
        $this->checkAuth();
        
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=discipline');
            exit;
        }
    }
    
    // Modifier un incident disciplinaire
    public function modifierIncident() {
        $this->checkAuth();
        
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=discipline');
            exit;
        }
    }
    
    // Supprimer un incident disciplinaire
    public function supprimerIncident() {
        $this->checkAuth();
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider l'ID de l'incident
            $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            // Validation des données
            if (empty($incident_id)) {
                $_SESSION['flash_message'] = "ID d'incident invalide.";
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=discipline');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=discipline');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=discipline');
            exit;
        }
    }
    
    // Afficher la carte d'élève
    public function carteEleve() {
        $this->checkAuth();
        
        // Charger la vue de la carte d'élève
        require_once 'views/directeur_etudes/carte_eleve.php';
    }
    
    // Afficher le profil détaillé d'un professeur
    public function voirProfesseur() {
        $this->checkAuth();
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Vérifier si l'ID est valide
        if ($id <= 0) {
            $_SESSION['message'] = "ID de professeur invalide.";
            $_SESSION['message_type'] = "error";
            header("Location: " . BASE_URL . "index.php?controller=DirecteurEtude&action=professeurs");
            exit;
        }
        
        // Récupérer les données du professeur
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
        
        // Récupérer les cours du professeur
        $query = "SELECT * FROM cours WHERE professeur_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cours = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        $mysqli->close();
        
        // Charger la vue
        require_once('views/directeur_etudes/voirProfesseur.php');
    }
    
    // Afficher les élèves d'une classe spécifique
    public function voirEleves() {
        $this->checkAuth();
        
        // Vérifier si l'ID de classe est fourni
        if (!isset($_GET['classe']) || empty($_GET['classe'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=classes');
            exit;
        }
        
        // Récupérer l'ID de la classe
        $classe_id = (int)$_GET['classe'];
        
        // Charger la vue des élèves de la classe
        require_once 'views/directeur_etudes/eleves_classe.php';
    }
    
    // Marquer directement un élève comme absent pour la journée
    public function marquerAbsent() {
        $this->checkAuth();
        
        // Vérifier si l'ID de l'élève est fourni
        if (!isset($_GET['eleve']) || empty($_GET['eleve'])) {
            $_SESSION['error_message'] = "Aucun élève spécifié.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=classes');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=classes');
            exit;
        }
        
        // Vérifier si l'élève existe
        $check_query = "SELECT e.id, e.nom, e.prenom, c.nom as classe_nom 
                       FROM eleves e 
                       JOIN classes c ON e.classe_id = c.id 
                       WHERE e.id = ?";
        $stmt = $mysqli->prepare($check_query);
        $stmt->bind_param("i", $eleve_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION['error_message'] = "Élève non trouvé.";
            $mysqli->close();
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=classes');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=voirEleves&classe=' . $classe_id);
        } else {
            // Rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=absences');
        }
        exit;
    }
    
    // Gestion de la présence des professeurs
    public function presenceProfesseur() {
        $this->checkAuth();
        
        // Récupérer l'ID du professeur
        $professeurId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($professeurId <= 0) {
            $_SESSION['error'] = "ID de professeur invalide.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=professeurs');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=professeurs');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=presenceProfesseur&id=' . $professeurId);
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
        require_once 'views/directeur_etudes/presence_professeur.php';
    }
    
    // Ajouter un horaire à l'emploi du temps
    public function ajouterHoraire() {
        $this->checkAuth();
        
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Méthode non autorisée.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=emploiDuTemps');
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=emploiDuTemps&classe_id=' . $classe_id);
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
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=emploiDuTemps&classe_id=' . $classe_id);
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
        header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=emploiDuTemps&classe_id=' . $classe_id);
        exit;
    }

    // Afficher la vue détaillée d'une classe
    public function voirClasse() {
        $this->checkAuth();
        
        $classe_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($classe_id <= 0) {
            $_SESSION['error_message'] = "ID de classe invalide.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=classes');
            exit;
        }

        // Récupérer les informations de la classe
        $classe = $this->classeModel->getById($classe_id);
        if (!$classe) {
            $_SESSION['error_message'] = "Classe non trouvée.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=classes');            exit;
        }

        // Récupérer les élèves de la classe
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $stmt = $mysqli->prepare("SELECT * FROM eleves WHERE classe_id = ?");
        $stmt->bind_param("i", $classe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $eleves = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Récupérer les cours de la classe
        $stmt = $mysqli->prepare("SELECT * FROM cours WHERE classe_id = ?");
        $stmt->bind_param("i", $classe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cours = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $mysqli->close();
        
        // Récupérer les statistiques de la classe
        $stats = [
            'total_eleves' => count($eleves),
            'moyenne_generale' => $this->calculateClasseAverage($classe_id),
            'taux_presence' => $this->calculateAttendanceRate($classe_id)
        ];

        $data = [
            'classe' => $classe,
            'eleves' => $eleves,
            'cours' => $cours,
            'stats' => $stats
        ];
        
        require_once 'views/directeur_etudes/voirClasse.php';
    }

    // Gérer les élèves d'une classe spécifique
    public function elevesClasse() {
        $this->checkAuth();
        
        $classe_id = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : 0;
        
        if ($classe_id <= 0) {
            $_SESSION['error_message'] = "ID de classe invalide.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=classes');
            exit;
        }

        // Récupérer les informations de la classe
        $classe = $this->classeModel->getById($classe_id);
        if (!$classe) {
            $_SESSION['error_message'] = "Classe non trouvée.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=classes');
            exit;
        }        // Récupérer les élèves de la classe
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $stmt = $mysqli->prepare("SELECT * FROM eleves WHERE classe_id = ?");
        $stmt->bind_param("i", $classe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $eleves = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $mysqli->close();
          // Récupérer les élèves non assignés à une classe pour l'ajout
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $stmt = $mysqli->prepare("SELECT * FROM eleves WHERE classe_id IS NULL OR classe_id = 0");
        $stmt->execute();
        $result = $stmt->get_result();
        $elevesDisponibles = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $mysqli->close();
        
        // Récupérer toutes les classes pour les transferts
        $toutes_classes = $this->classeModel->getAll();

        $data = [
            'classe' => $classe,
            'eleves' => $eleves,
            'elevesDisponibles' => $elevesDisponibles,
            'toutes_classes' => $toutes_classes
        ];
        
        require_once 'views/directeur_etudes/elevesClasse.php';
    }

    // Afficher la vue détaillée d'un cours
    public function voirCours() {
        $this->checkAuth();
        
        $cours_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($cours_id <= 0) {
            $_SESSION['error_message'] = "ID de cours invalide.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=cours');
            exit;
        }

        // Récupérer les informations du cours
        $cours = $this->coursModel->getById($cours_id);
        if (!$cours) {
            $_SESSION['error_message'] = "Cours non trouvé.";
            header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=cours');
            exit;
        }

        // Récupérer le professeur du cours
        $professeur = $this->professeurModel->getById($cours['professeur_id']);
          // Récupérer les classes qui suivent ce cours
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $stmt = $mysqli->prepare("SELECT DISTINCT c.* FROM classes c INNER JOIN cours co ON c.id = co.classe_id WHERE co.id = ?");
        $stmt->bind_param("i", $cours_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $classes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $mysqli->close();
        
        // Récupérer les examens liés à ce cours
        $examens = $this->getExamensByCours($cours_id);
        
        // Récupérer les statistiques du cours
        $stats = [
            'moyenne_generale' => $this->calculateCoursAverage($cours_id),
            'taux_reussite' => $this->calculateSuccessRate($cours_id),
            'nb_examens' => count($examens)
        ];

        $data = [
            'cours' => $cours,
            'professeur' => $professeur,
            'classes' => $classes,
            'examens' => $examens,
            'stats' => $stats
        ];
        
        require_once 'views/directeur_etudes/voirCours.php';
    }

    // Afficher la page de gestion des examens
    public function examens() {
        $this->checkAuth();
        
        // Récupérer les filtres
        $classe_id = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : 0;
        $cours_id = isset($_GET['cours_id']) ? (int)$_GET['cours_id'] : 0;
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';

        // Récupérer tous les examens avec filtres
        $examens = $this->getExamensWithFilters($classe_id, $cours_id, $date_debut, $date_fin);
        
        // Récupérer les classes pour le filtre
        $classes = $this->classeModel->getAll();
        
        // Récupérer les cours pour le filtre
        $cours = $this->coursModel->getAll();

        $data = [
            'examens' => $examens,
            'classes' => $classes,
            'cours' => $cours,
            'filters' => [
                'classe_id' => $classe_id,
                'cours_id' => $cours_id,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin
            ]
        ];
        
        require_once 'views/directeur_etudes/examens.php';
    }

    // Afficher la page des résultats scolaires
    public function resultatsScolaires() {
        $this->checkAuth();
        
        // Récupérer les filtres
        $classe_id = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : 0;
        $trimestre = isset($_GET['trimestre']) ? $_GET['trimestre'] : '';
        $annee_scolaire = isset($_GET['annee_scolaire']) ? $_GET['annee_scolaire'] : '';

        // Récupérer les résultats avec filtres
        $resultats = $this->getResultatsWithFilters($classe_id, $trimestre, $annee_scolaire);
        
        // Récupérer les classes pour le filtre
        $classes = $this->classeModel->getAll();
        
        // Récupérer les années scolaires disponibles
        $annees_scolaires = $this->getAnneesScolaires();

        $data = [
            'resultats' => $resultats,
            'classes' => $classes,
            'annees_scolaires' => $annees_scolaires,
            'filters' => [
                'classe_id' => $classe_id,
                'trimestre' => $trimestre,
                'annee_scolaire' => $annee_scolaire
            ]
        ];
        
        require_once 'views/directeur_etudes/resultatsScolaires.php';
    }

    // Afficher la page des programmes scolaires
    public function programmesScolaires() {
        $this->checkAuth();
        
        // Récupérer les filtres
        $niveau = isset($_GET['niveau']) ? $_GET['niveau'] : '';
        $classe_id = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : 0;

        // Récupérer les programmes avec filtres
        $programmes = $this->getProgrammesWithFilters($niveau, $classe_id);
        
        // Récupérer les classes pour le filtre
        $classes = $this->classeModel->getAll();
        
        // Récupérer les niveaux disponibles
        $niveaux = $this->getNiveauxDisponibles();

        $data = [
            'programmes' => $programmes,
            'classes' => $classes,
            'niveaux' => $niveaux,
            'filters' => [
                'niveau' => $niveau,
                'classe_id' => $classe_id
            ]
        ];
        
        require_once 'views/directeur_etudes/programmesScolaires.php';
    }

    // Afficher la page de communications
    public function communications() {
        $this->checkAuth();
        
        // Charger la vue des communications
        require_once 'views/directeur_etudes/communications.php';
    }

    // Afficher la page des rapports globaux
    public function rapportsGlobaux() {
        $this->checkAuth();
        
        // Charger la vue des rapports globaux
        require_once 'views/directeur_etudes/rapports.php';
    }

    // Méthodes utilitaires privées pour les calculs statistiques
    
    private function calculateClasseAverage($classe_id) {
        // Calculer la moyenne générale de la classe
        // Implémentation simplifiée - à adapter selon la structure de la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT AVG(note) as moyenne FROM notes n 
                  INNER JOIN eleves e ON n.eleve_id = e.id 
                  WHERE e.classe_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $classe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        $mysqli->close();
        
        return $row['moyenne'] ? round($row['moyenne'], 2) : 0;
    }

    private function calculateAttendanceRate($classe_id) {
        // Calculer le taux de présence de la classe
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT 
                    COUNT(*) as total_sessions,
                    SUM(CASE WHEN a.statut = 'present' THEN 1 ELSE 0 END) as presences
                  FROM absences a 
                  INNER JOIN eleves e ON a.eleve_id = e.id 
                  WHERE e.classe_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $classe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        $mysqli->close();
        
        if ($row['total_sessions'] > 0) {
            return round(($row['presences'] / $row['total_sessions']) * 100, 2);
        }
        return 0;
    }

    private function calculateCoursAverage($cours_id) {
        // Calculer la moyenne générale du cours
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT AVG(note) as moyenne FROM notes WHERE cours_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $cours_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        $mysqli->close();
        
        return $row['moyenne'] ? round($row['moyenne'], 2) : 0;
    }

    private function calculateSuccessRate($cours_id) {
        // Calculer le taux de réussite du cours (notes >= 10)
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT 
                    COUNT(*) as total_notes,
                    SUM(CASE WHEN note >= 10 THEN 1 ELSE 0 END) as reussites
                  FROM notes WHERE cours_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $cours_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        $mysqli->close();
        
        if ($row['total_notes'] > 0) {
            return round(($row['reussites'] / $row['total_notes']) * 100, 2);
        }
        return 0;
    }

    private function getExamensByCours($cours_id) {
        // Récupérer les examens d'un cours
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT * FROM examens WHERE cours_id = ? ORDER BY date_examen DESC";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $cours_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $examens = [];
        while ($row = $result->fetch_assoc()) {
            $examens[] = $row;
        }
        
        $stmt->close();
        $mysqli->close();
        
        return $examens;
    }

    private function getExamensWithFilters($classe_id, $cours_id, $date_debut, $date_fin) {
        // Récupérer les examens avec filtres
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT e.*, c.nom as cours_nom, cl.nom as classe_nom 
                  FROM examens e 
                  INNER JOIN cours c ON e.cours_id = c.id 
                  INNER JOIN classes cl ON c.classe_id = cl.id 
                  WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if ($classe_id > 0) {
            $query .= " AND cl.id = ?";
            $params[] = $classe_id;
            $types .= "i";
        }
        
        if ($cours_id > 0) {
            $query .= " AND c.id = ?";
            $params[] = $cours_id;
            $types .= "i";
        }
        
        if (!empty($date_debut)) {
            $query .= " AND e.date_examen >= ?";
            $params[] = $date_debut;
            $types .= "s";
        }
        
        if (!empty($date_fin)) {
            $query .= " AND e.date_examen <= ?";
            $params[] = $date_fin;
            $types .= "s";
        }
        
        $query .= " ORDER BY e.date_examen DESC";
        
        $stmt = $mysqli->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $examens = [];
        while ($row = $result->fetch_assoc()) {
            $examens[] = $row;
        }
        
        $stmt->close();
        $mysqli->close();
        
        return $examens;
    }

    private function getResultatsWithFilters($classe_id, $trimestre, $annee_scolaire) {
        // Récupérer les résultats avec filtres
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                         c.nom as cours_nom, cl.nom as classe_nom 
                  FROM resultats r 
                  INNER JOIN eleves e ON r.eleve_id = e.id 
                  INNER JOIN cours c ON r.cours_id = c.id 
                  INNER JOIN classes cl ON e.classe_id = cl.id 
                  WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if ($classe_id > 0) {
            $query .= " AND cl.id = ?";
            $params[] = $classe_id;
            $types .= "i";
        }
        
        if (!empty($trimestre)) {
            $query .= " AND r.trimestre = ?";
            $params[] = $trimestre;
            $types .= "s";
        }
        
        if (!empty($annee_scolaire)) {
            $query .= " AND r.annee_scolaire = ?";
            $params[] = $annee_scolaire;
            $types .= "s";
        }
        
        $query .= " ORDER BY cl.nom, e.nom, e.prenom";
        
        $stmt = $mysqli->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $resultats = [];
        while ($row = $result->fetch_assoc()) {
            $resultats[] = $row;
        }
        
        $stmt->close();
        $mysqli->close();
        
        return $resultats;
    }    private function getProgrammesWithFilters($niveau, $classe_id) {
        // Récupérer les cours (programmes) avec filtres
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT c.id, c.titre as nom, c.description, c.coefficient, c.heures_semaine, 
                         c.titre as cours_nom, cl.nom as classe_nom, cl.niveau, cl.section,
                         p.nom as prof_nom, p.prenom as prof_prenom
                  FROM cours c 
                  INNER JOIN classes cl ON c.classe_id = cl.id 
                  LEFT JOIN professeurs p ON c.professeur_id = p.id
                  WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if (!empty($niveau)) {
            $query .= " AND cl.niveau = ?";
            $params[] = $niveau;
            $types .= "s";
        }
        
        if ($classe_id > 0) {
            $query .= " AND cl.id = ?";
            $params[] = $classe_id;
            $types .= "i";
        }
        
        $query .= " ORDER BY cl.nom, c.titre";
        
        $stmt = $mysqli->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $programmes = [];
        while ($row = $result->fetch_assoc()) {
            $programmes[] = $row;
        }
        
        $stmt->close();
        $mysqli->close();
        
        return $programmes;
    }

    private function getCommunicationsWithFilters($type, $destinataire, $date_debut, $date_fin) {
        // Récupérer les communications avec filtres
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT * FROM communications WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if (!empty($type)) {
            $query .= " AND type = ?";
            $params[] = $type;
            $types .= "s";
        }
        
        if (!empty($destinataire)) {
            $query .= " AND destinataire = ?";
            $params[] = $destinataire;
            $types .= "s";
        }
        
        if (!empty($date_debut)) {
            $query .= " AND date_creation >= ?";
            $params[] = $date_debut;
            $types .= "s";
        }
        
        if (!empty($date_fin)) {
            $query .= " AND date_creation <= ?";
            $params[] = $date_fin;
            $types .= "s";
        }
        
        $query .= " ORDER BY date_creation DESC";
        
        $stmt = $mysqli->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $communications = [];
        while ($row = $result->fetch_assoc()) {
            $communications[] = $row;
        }
        
        $stmt->close();
        $mysqli->close();
        
        return $communications;
    }

    private function getAnneesScolaires() {
        // Récupérer les années scolaires disponibles
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT DISTINCT annee_scolaire FROM resultats ORDER BY annee_scolaire DESC";
        $result = $mysqli->query($query);
        
        $annees = [];
        while ($row = $result->fetch_assoc()) {
            $annees[] = $row['annee_scolaire'];
        }
        
        $mysqli->close();
        
        return $annees;    } 

    private function getNiveauxDisponibles() {
        // Récupérer les niveaux disponibles
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $query = "SELECT DISTINCT niveau FROM classes ORDER BY niveau";
        $result = $mysqli->query($query);
        
        $niveaux = [];
        while ($row = $result->fetch_assoc()) {
            $niveaux[] = $row['niveau'];
        }
        
        $mysqli->close();
        
        return $niveaux;
    }
  
}
?>
