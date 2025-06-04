<?php
require_once 'config/config.php';
require_once 'models/UserModel.php';
require_once 'models/DirectorModel.php';

class Director {
    private $db;
    
    public function __construct() {
        // Vérification de la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérification des droits d'accès
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Director' && $_SESSION['role'] !== 'director')) {
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
    
    /**
     * Vérifie si l'utilisateur est connecté
     * @return bool True si l'utilisateur est connecté, false sinon
     */
    private function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']) && 
               ($_SESSION['role'] === 'Director' || $_SESSION['role'] === 'director');
    }
    
    /**
     * Redirige vers une URL
     * @param string $url L'URL de redirection
     */
    private function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    // Page d'accueil du directeur
    public function accueil() {
        require_once 'views/directeur/accueil.php';
    }
    
    // Gestion des élèves
    public function eleves() {
        $section = isset($_GET['section']) ? $_GET['section'] : '';
        $eleves = [];
        
        if (!empty($section)) {
            $stmt = $this->db->prepare("SELECT e.*, c.nom as classe_nom FROM eleves e 
                                        LEFT JOIN classes c ON e.classe_id = c.id 
                                        WHERE e.section = ? ORDER BY e.nom, e.prenom");
            $stmt->bind_param("s", $section);
        } else {
            $stmt = $this->db->prepare("SELECT e.*, c.nom as classe_nom FROM eleves e 
                                        LEFT JOIN classes c ON e.classe_id = c.id 
                                        ORDER BY e.section, e.nom, e.prenom");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $eleves[] = $row;
        }
        
        $stmt->close();
        
        require_once 'views/directeur/eleves.php';
    }
    
    // Gestion des professeurs
    public function professeurs() {
        $section = isset($_GET['section']) ? $_GET['section'] : '';
        $professeurs = [];
        
        if (!empty($section)) {
            $stmt = $this->db->prepare("SELECT * FROM professeurs WHERE section = ? ORDER BY nom, prenom");
            $stmt->bind_param("s", $section);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM professeurs ORDER BY section, nom, prenom");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $professeurs[] = $row;
        }
        
        $stmt->close();
        
        require_once 'views/directeur/professeurs.php';
    }
    
    // Gestion des classes
    public function classes() {
        $section = isset($_GET['section']) ? $_GET['section'] : '';
        $classes = [];
        
        if (!empty($section)) {
            $stmt = $this->db->prepare("SELECT c.*, COUNT(e.id) as nb_eleves, p.nom as prof_nom, p.prenom as prof_prenom 
                                        FROM classes c 
                                        LEFT JOIN eleves e ON c.id = e.classe_id 
                                        LEFT JOIN professeurs p ON c.titulaire = p.id 
                                        WHERE c.section = ? 
                                        GROUP BY c.id 
                                        ORDER BY c.nom");
            $stmt->bind_param("s", $section);
        } else {
            $stmt = $this->db->prepare("SELECT c.*, COUNT(e.id) as nb_eleves, p.nom as prof_nom, p.prenom as prof_prenom 
                                        FROM classes c 
                                        LEFT JOIN eleves e ON c.id = e.classe_id 
                                        LEFT JOIN professeurs p ON c.titulaire = p.id 
                                        GROUP BY c.id 
                                        ORDER BY c.section, c.nom");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }
        
        $stmt->close();
        
        require_once 'views/directeur/classes.php';
    }
    
    // Voir les élèves d'une classe
    public function voirEleves() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directeur
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directeur') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID de classe est fourni
        if (!isset($_GET['classe']) || empty($_GET['classe'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=classes');
            exit;
        }
        
        // Récupérer l'ID de la classe
        $classe_id = (int)$_GET['classe'];
        
        // Charger la vue des élèves de la classe
        require_once 'views/directeur/eleves_classe.php';
    }
    
    // Voir le profil d'un élève
    public function voirEleve() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['message'] = "ID de l'élève non spécifié.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=eleves');
            exit;
        }
        
        $eleve_id = intval($_GET['id']);
        
        $stmt = $this->db->prepare("SELECT id FROM eleves WHERE id = ?");
        $stmt->bind_param("i", $eleve_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION['message'] = "L'élève sélectionné n'existe pas.";
            $_SESSION['message_type'] = 'danger';
            $stmt->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=eleves');
            exit;
        }
        $stmt->close();
        
        require_once 'views/directeur/voirEleve.php';
    }
    
    // Afficher la carte d'identité d'un élève
    public function carteEleve() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['message'] = "ID de l'élève non spécifié.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=eleves');
            exit;
        }
        
        $eleve_id = intval($_GET['id']);
        
        $stmt = $this->db->prepare("SELECT id FROM eleves WHERE id = ?");
        $stmt->bind_param("i", $eleve_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION['message'] = "L'élève sélectionné n'existe pas.";
            $_SESSION['message_type'] = 'danger';
            $stmt->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=eleves');
            exit;
        }
        $stmt->close();
        
        require_once 'views/directeur/carte_eleve.php';
    }
    
    // Gestion des cours
    public function cours() {
        $cours = [];
        
        $query = "SELECT c.*, p.nom as prof_nom, p.prenom as prof_prenom, cl.nom as classe_nom 
                 FROM cours c 
                 LEFT JOIN professeurs p ON c.professeur_id = p.id 
                 LEFT JOIN classes cl ON c.classe_id = cl.id ";
        
        $result = $this->db->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $cours[] = $row;
        }
        
        require_once 'views/directeur/cours.php';
    }
    
    // Gestion de la discipline
    public function discipline() {
        $incidents = [];
        
        $query = "SELECT i.*, e.nom as eleve_nom, e.prenom as eleve_prenom, c.nom as classe_nom 
                 FROM incidents_disciplinaires i 
                 JOIN eleves e ON i.eleve_id = e.id 
                 JOIN classes c ON e.classe_id = c.id 
                 ORDER BY i.date_incident DESC, e.nom, e.prenom";
        
        $result = $this->db->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $incidents[] = $row;
        }
        
        require_once 'views/directeur/discipline.php';
    }
    
    // Ajouter un incident disciplinaire
    public function ajouterIncident() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            
            if (empty($errors)) {
                $stmt = $this->db->prepare("INSERT INTO incidents_disciplinaires (eleve_id, date_incident, description, sanction, statut, date_creation) 
                                          VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("issss", $eleve_id, $date_incident, $description, $sanction, $statut);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "L'incident disciplinaire a été ajouté avec succès.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Erreur lors de l'ajout de l'incident: " . $this->db->error;
                    $_SESSION['message_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
                $_SESSION['message_type'] = "danger";
            }
        }
        
        header('Location: ' . BASE_URL . 'index.php?controller=Director&action=discipline');
        exit;
    }
    
    // Modifier un incident disciplinaire
    public function modifierIncident() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            
            if (empty($errors)) {
                $stmt = $this->db->prepare("UPDATE incidents_disciplinaires 
                                          SET description = ?, sanction = ?, statut = ?, date_modification = NOW() 
                                          WHERE id = ?");
                $stmt->bind_param("sssi", $description, $sanction, $statut, $incident_id);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "L'incident disciplinaire a été modifié avec succès.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Erreur lors de la modification de l'incident: " . $this->db->error;
                    $_SESSION['message_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
                $_SESSION['message_type'] = "danger";
            }
        }
        
        header('Location: ' . BASE_URL . 'index.php?controller=Director&action=discipline');
        exit;
    }
    
    // Supprimer un incident disciplinaire
    public function supprimerIncident() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            if ($incident_id <= 0) {
                $_SESSION['message'] = "ID d'incident invalide.";
                $_SESSION['message_type'] = 'danger';
                header('Location: ' . BASE_URL . 'index.php?controller=Director&action=discipline');
                exit;
            }
            
            $stmt = $this->db->prepare("DELETE FROM incidents_disciplinaires WHERE id = ?");
            $stmt->bind_param("i", $incident_id);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "L'incident disciplinaire a été supprimé avec succès.";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Erreur lors de la suppression de l'incident: " . $this->db->error;
                $_SESSION['message_type'] = 'danger';
            }
            
            $stmt->close();
        }
        
        header('Location: ' . BASE_URL . 'index.php?controller=Director&action=discipline');
        exit;
    }
    
    // Gestion des absences
    public function absences() {
        $absences = [];
        
        $query = "SELECT a.*, e.nom as eleve_nom, e.prenom as eleve_prenom, c.nom as classe_nom 
                 FROM absences a 
                 JOIN eleves e ON a.eleve_id = e.id 
                 JOIN classes c ON e.classe_id = c.id 
                 ORDER BY a.date_absence DESC, e.nom, e.prenom";
        
        $result = $this->db->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $absences[] = $row;
        }
        
        require_once 'views/directeur/absences.php';
    }
    
    // Ajouter une absence
    public function ajouterAbsence() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
            $date_absence = isset($_POST['date_absence']) ? $_POST['date_absence'] : '';
            $motif = isset($_POST['motif']) ? $_POST['motif'] : '';
            $justifiee = isset($_POST['justifiee']) ? 1 : 0;
            
            // Validation des données
            $errors = [];
            if ($eleve_id <= 0) {
                $errors[] = "Veuillez sélectionner un élève.";
            }
            if (empty($date_absence)) {
                $errors[] = "La date de l'absence est requise.";
            }
            
            if (empty($errors)) {
                $stmt = $this->db->prepare("INSERT INTO absences (eleve_id, date_absence, motif, justifiee, date_creation) 
                                      VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("issi", $eleve_id, $date_absence, $motif, $justifiee);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "L'absence a été ajoutée avec succès.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Erreur lors de l'ajout de l'absence: " . $this->db->error;
                    $_SESSION['message_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
                $_SESSION['message_type'] = "danger";
            }
        }
        
        header('Location: ' . BASE_URL . 'index.php?controller=Director&action=absences');
        exit;
    }
    
    // Gestion des événements scolaires
    public function evenementsScolaires() {
        $evenements = [];
        
        $query = "SELECT * FROM evenements_scolaires ORDER BY date_debut DESC";
        $result = $this->db->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $evenements[] = $row;
        }
        
        require_once 'views/directeur/evenements_scolaires.php';
    }
    
    // Gestion des finances
    public function finances() {
        $transactions = [];
        
        $query = "SELECT * FROM transactions_financieres ORDER BY date DESC";
        $result = $this->db->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        
        require_once 'views/directeur/finances.php';
    }
    
    // Gestion du personnel
    public function personnel() {
        $personnel = [];
        
        $query = "SELECT * FROM personnel ORDER BY role, nom, prenom";
        $result = $this->db->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $personnel[] = $row;
        }
        
        require_once 'views/directeur/personnel.php';
    }
    
    // Génération de rapports
    public function rapports() {
        require_once 'views/directeur/rapports.php';
    }
    
    // Paramètres du système
    public function parametres() {
        require_once 'views/directeur/parametres.php';
    }
    
    // Profil du directeur
    public function profil() {
        $user_id = $_SESSION['user_id'];
        $user = [];
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }
        
        $stmt->close();
        
        require_once 'views/directeur/profil.php';
    }
    
    // Mise à jour du profil
    public function updateProfil() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
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
            
            // Vérifier si l'email existe déjà pour un autre utilisateur
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Cet email est déjà utilisé par un autre compte.";
            }
            $stmt->close();
            
            // Si l'utilisateur souhaite changer son mot de passe
            if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
                if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                    $errors[] = "Tous les champs de mot de passe doivent être remplis pour changer le mot de passe.";
                } elseif ($new_password !== $confirm_password) {
                    $errors[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
                } elseif (strlen($new_password) < 8) {
                    $errors[] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
                } else {
                    // Vérifier le mot de passe actuel
                    $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user_data = $result->fetch_assoc();
                    $stmt->close();
                    
                    if (!password_verify($current_password, $user_data['password'])) {
                        $errors[] = "Le mot de passe actuel est incorrect.";
                    }
                }
            }
            
            // Si pas d'erreurs, mettre à jour le profil
            if (empty($errors)) {
                if (!empty($new_password)) {
                    // Mettre à jour avec le nouveau mot de passe
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $this->db->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("sssssi", $nom, $prenom, $email, $telephone, $hashed_password, $user_id);
                } else {
                    // Mettre à jour sans changer le mot de passe
                    $stmt = $this->db->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $nom, $prenom, $email, $telephone, $user_id);
                }
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Votre profil a été mis à jour avec succès.";
                    $_SESSION['message_type'] = "success";
                    
                    // Mettre à jour les informations de session
                    $_SESSION['nom'] = $nom;
                    $_SESSION['prenom'] = $prenom;
                    $_SESSION['email'] = $email;
                } else {
                    $_SESSION['message'] = "Erreur lors de la mise à jour du profil: " . $this->db->error;
                    $_SESSION['message_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
                $_SESSION['message_type'] = "danger";
            }
        }
        
        header('Location: ' . BASE_URL . 'index.php?controller=Director&action=profil');
        exit;
    }
    
    /**
     * Gestion des présences des professeurs
     */
    public function presencesProfesseurs() {
        // Vérifier si l'ID du professeur est fourni
        if (!isset($_GET['professeur_id']) || empty($_GET['professeur_id'])) {
            $_SESSION['message'] = "ID du professeur non spécifié.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        }
        
        $professeur_id = intval($_GET['professeur_id']);
        
        // Vérifier si le professeur existe
        $stmt = $this->db->prepare("SELECT * FROM professeurs WHERE id = ?");
        $stmt->bind_param("i", $professeur_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION['message'] = "Le professeur sélectionné n'existe pas.";
            $_SESSION['message_type'] = 'danger';
            $stmt->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        }
        
        $professeur = $result->fetch_assoc();
        $stmt->close();
        
        // Traitement des actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = isset($_POST['action']) ? $_POST['action'] : '';
            
            switch ($action) {
                case 'ajouter_presence':
                    $this->ajouterPresenceProfesseur($professeur_id);
                    break;
                case 'modifier_presence':
                    $this->modifierPresenceProfesseur();
                    break;
                case 'supprimer_presence':
                    $this->supprimerPresenceProfesseur();
                    break;
            }
        }
        
        // Récupérer les filtres
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';
        $status_filtre = isset($_GET['status']) ? $_GET['status'] : '';
        
        // Construire la requête pour récupérer les présences
        $whereConditions = ["professeur_id = ?"];
        $params = [$professeur_id];
        $types = "i";
        
        if (!empty($date_debut)) {
            $whereConditions[] = "date >= ?";
            $params[] = $date_debut;
            $types .= "s";
        }
        
        if (!empty($date_fin)) {
            $whereConditions[] = "date <= ?";
            $params[] = $date_fin;
            $types .= "s";
        }
        
        if (!empty($status_filtre)) {
            $whereConditions[] = "status = ?";
            $params[] = $status_filtre;
            $types .= "s";
        }
        
        $whereClause = implode(" AND ", $whereConditions);
        $query = "SELECT * FROM presences_professeurs WHERE $whereClause ORDER BY date DESC";
        
        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $presences = [];
        while ($row = $result->fetch_assoc()) {
            $presences[] = $row;
        }
        $stmt->close();
        
        // Calculer les statistiques
        $statistiques = $this->calculerStatistiquesPresences($professeur_id);
        
        // Charger la vue
        require_once 'views/directeur/presences_professeurs.php';
    }
    
    /**
     * Ajouter une présence pour un professeur
     */
    private function ajouterPresenceProfesseur($professeur_id) {
        $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $commentaire = isset($_POST['commentaire']) ? $_POST['commentaire'] : '';
        
        // Validation
        if (empty($status)) {
            $_SESSION['message'] = "Le statut est requis.";
            $_SESSION['message_type'] = 'danger';
            return;
        }
        
        // Vérifier si une présence existe déjà pour cette date
        $stmt = $this->db->prepare("SELECT id FROM presences_professeurs WHERE professeur_id = ? AND date = ?");
        $stmt->bind_param("is", $professeur_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Mettre à jour l'enregistrement existant
            $presence_id = $result->fetch_assoc()['id'];
            $stmt->close();
            
            $stmt = $this->db->prepare("UPDATE presences_professeurs SET status = ?, commentaire = ? WHERE id = ?");
            $stmt->bind_param("ssi", $status, $commentaire, $presence_id);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "La présence a été mise à jour avec succès.";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Erreur lors de la mise à jour de la présence: " . $this->db->error;
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            // Créer un nouvel enregistrement
            $stmt->close();
            
            $stmt = $this->db->prepare("INSERT INTO presences_professeurs (professeur_id, date, status, commentaire) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $professeur_id, $date, $status, $commentaire);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "La présence a été enregistrée avec succès.";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Erreur lors de l'enregistrement de la présence: " . $this->db->error;
                $_SESSION['message_type'] = 'danger';
            }
        }
        
        $stmt->close();
        
        // Redirection
        header('Location: ' . BASE_URL . 'index.php?controller=Director&action=presencesProfesseurs&professeur_id=' . $professeur_id);
        exit;
    }
    
    /**
     * Modifier une présence existante
     */
    private function modifierPresenceProfesseur() {
        $presence_id = isset($_POST['presence_id']) ? intval($_POST['presence_id']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $commentaire = isset($_POST['commentaire']) ? $_POST['commentaire'] : '';
        
        if ($presence_id <= 0 || empty($status)) {
            $_SESSION['message'] = "Données invalides.";
            $_SESSION['message_type'] = 'danger';
            return;
        }
        
        $stmt = $this->db->prepare("UPDATE presences_professeurs SET status = ?, commentaire = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $commentaire, $presence_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "La présence a été modifiée avec succès.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Erreur lors de la modification de la présence: " . $this->db->error;
            $_SESSION['message_type'] = 'danger';
        }
        
        $stmt->close();
        
        // Récupérer le professeur_id pour la redirection
        $stmt = $this->db->prepare("SELECT professeur_id FROM presences_professeurs WHERE id = ?");
        $stmt->bind_param("i", $presence_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $professeur_id = $result->fetch_assoc()['professeur_id'];
        $stmt->close();
        
        header('Location: ' . BASE_URL . 'index.php?controller=Director&action=presencesProfesseurs&professeur_id=' . $professeur_id);
        exit;
    }
    
    /**
     * Supprimer une présence
     */
    private function supprimerPresenceProfesseur() {
        $presence_id = isset($_POST['presence_id']) ? intval($_POST['presence_id']) : 0;
        
        if ($presence_id <= 0) {
            $_SESSION['message'] = "ID de présence invalide.";
            $_SESSION['message_type'] = 'danger';
            return;
        }
        
        // Récupérer le professeur_id avant suppression
        $stmt = $this->db->prepare("SELECT professeur_id FROM presences_professeurs WHERE id = ?");
        $stmt->bind_param("i", $presence_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION['message'] = "Présence non trouvée.";
            $_SESSION['message_type'] = 'danger';
            $stmt->close();
            return;
        }
        
        $professeur_id = $result->fetch_assoc()['professeur_id'];
        $stmt->close();
        
        // Supprimer la présence
        $stmt = $this->db->prepare("DELETE FROM presences_professeurs WHERE id = ?");
        $stmt->bind_param("i", $presence_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "La présence a été supprimée avec succès.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression de la présence: " . $this->db->error;
            $_SESSION['message_type'] = 'danger';
        }
        
        $stmt->close();
        
        header('Location: ' . BASE_URL . 'index.php?controller=Director&action=presencesProfesseurs&professeur_id=' . $professeur_id);
        exit;
    }
    
    /**
     * Calculer les statistiques de présence pour un professeur
     */
    private function calculerStatistiquesPresences($professeur_id) {
        $stats = [
            'present' => 0,
            'absent' => 0,
            'retard' => 0,
            'excuse' => 0,
            'total' => 0,
            'pourcentage_present' => 0,
            'pourcentage_absent' => 0,
            'pourcentage_retard' => 0,
            'pourcentage_excuse' => 0
        ];
        
        $stmt = $this->db->prepare("SELECT status, COUNT(*) as count FROM presences_professeurs WHERE professeur_id = ? GROUP BY status");
        $stmt->bind_param("i", $professeur_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $stats[$row['status']] = $row['count'];
            $stats['total'] += $row['count'];
        }
        
        $stmt->close();
        
        // Calculer les pourcentages
        if ($stats['total'] > 0) {
            $stats['pourcentage_present'] = round(($stats['present'] / $stats['total']) * 100, 1);
            $stats['pourcentage_absent'] = round(($stats['absent'] / $stats['total']) * 100, 1);
            $stats['pourcentage_retard'] = round(($stats['retard'] / $stats['total']) * 100, 1);
            $stats['pourcentage_excuse'] = round(($stats['excuse'] / $stats['total']) * 100, 1);
        }
        
        return $stats;
    }

    // Destructeur pour fermer la connexion à la base de données
    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }

    /**
     * Afficher le profil détaillé d'un professeur
     */
    public function voirProfesseur() {
        // Vérifier si l'ID du professeur est fourni
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['message'] = "ID du professeur non spécifié.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        }
        
        $prof_id = intval($_GET['id']);
        
        // Vérifier si l'ID est valide
        if ($prof_id <= 0) {
            $_SESSION['message'] = "ID de professeur invalide.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        }
        
        // Récupération des informations du professeur avec ses cours
        $query = "SELECT p.*, GROUP_CONCAT(DISTINCT c.titre SEPARATOR ', ') as cours_nom
              FROM professeurs p
              LEFT JOIN cours c ON p.id = c.professeur_id
              WHERE p.id = ?
              GROUP BY p.id";
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $prof_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            $_SESSION['message'] = "Professeur non trouvé.";
            $_SESSION['message_type'] = 'danger';
            $stmt->close();
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        }
    
        $professeur = $result->fetch_assoc();
        $stmt->close();
    
        // Récupération des cours enseignés par le professeur avec les détails des classes
        $cours_query = "SELECT c.*, cl.nom as classe_nom, cl.section
                    FROM cours c
                    LEFT JOIN classes cl ON c.classe_id = cl.id
                    WHERE c.professeur_id = ?
                    ORDER BY cl.section, cl.nom";
    
        $cours_stmt = $this->db->prepare($cours_query);
        $cours_stmt->bind_param("i", $prof_id);
        $cours_stmt->execute();
        $cours_result = $cours_stmt->get_result();
    
        $cours = [];
        while ($row = $cours_result->fetch_assoc()) {
            $cours[] = $row;
        }
        $cours_stmt->close();
    
        // Récupérer les présences du professeur par mois pour l'onglet présences
        $presence_query = "SELECT 
                        DATE_FORMAT(date, '%Y-%m') as mois,
                        DATE_FORMAT(date, '%M %Y') as mois_nom,
                        date,
                        status,
                        commentaire
                      FROM presences_professeurs
                      WHERE professeur_id = ?
                      ORDER BY date DESC";
    
        $presence_stmt = $this->db->prepare($presence_query);
        $presence_stmt->bind_param("i", $prof_id);
        $presence_stmt->execute();
        $presence_result = $presence_stmt->get_result();
    
        $presences_par_mois = [];
    
        while ($presence = $presence_result->fetch_assoc()) {
            $mois = $presence['mois'];
            $mois_nom = $presence['mois_nom'];
            
            if (!isset($presences_par_mois[$mois])) {
                $presences_par_mois[$mois] = [
                    'nom' => $mois_nom,
                    'presences' => []
                ];
            }
            
            $presences_par_mois[$mois]['presences'][] = $presence;
        }
    
        $presence_stmt->close();
    
        // Calculer les statistiques de présence globales
        $stats_query = "SELECT status, COUNT(*) as count 
                    FROM presences_professeurs 
                    WHERE professeur_id = ? 
                    GROUP BY status";
    
        $stats_stmt = $this->db->prepare($stats_query);
        $stats_stmt->bind_param("i", $prof_id);
        $stats_stmt->execute();
        $stats_result = $stats_stmt->get_result();
    
        $statistiques_presences = [
            'present' => 0,
            'absent' => 0,
            'retard' => 0,
            'excuse' => 0,
            'total' => 0
        ];
    
        while ($stat = $stats_result->fetch_assoc()) {
            $statistiques_presences[$stat['status']] = $stat['count'];
            $statistiques_presences['total'] += $stat['count'];
        }
    
        $stats_stmt->close();
    
        // Charger la vue
        require_once 'views/directeur/voir_professeur.php';
    }
    /**
     * Modifier un professeur existant
     */
    public function modifierProfesseur() {
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
            $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
            $section = isset($_POST['section']) ? trim($_POST['section']) : '';
            $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
            
            // Validation des données
            $errors = [];
            
            if ($id <= 0) {
                $errors[] = "ID de professeur invalide.";
            }
            
            if (empty($nom)) {
                $errors[] = "Le nom est requis.";
            }
            
            if (empty($prenom)) {
                $errors[] = "Le prénom est requis.";
            }
            
            if (empty($email)) {
                $errors[] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide.";
            }
            
            if (empty($contact)) {
                $errors[] = "Le contact est requis.";
            }
            
            if (empty($section)) {
                $errors[] = "La section est requise.";
            } elseif (!in_array($section, ['maternelle', 'primaire', 'secondaire'])) {
                $errors[] = "Section invalide.";
            }
            
            // Vérifier si l'email existe déjà pour un autre professeur
            if (empty($errors)) {
                $stmt = $this->db->prepare("SELECT id FROM professeurs WHERE email = ? AND id != ?");
                $stmt->bind_param("si", $email, $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $errors[] = "Cet email est déjà utilisé par un autre professeur.";
                }
                $stmt->close();
            }
            
            // Si pas d'erreurs, mettre à jour le professeur
            if (empty($errors)) {
                $stmt = $this->db->prepare("UPDATE professeurs SET nom = ?, prenom = ?, email = ?, contact = ?, section = ?, adresse = ? WHERE id = ?");
                $stmt->bind_param("ssssssi", $nom, $prenom, $email, $contact, $section, $adresse, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Le professeur a été modifié avec succès.";
                    $_SESSION['message_type'] = 'success';
                    
                    // Si c'est une requête AJAX, retourner JSON
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => 'Professeur modifié avec succès']);
                        exit;
                    }
                } else {
                    $_SESSION['message'] = "Erreur lors de la modification du professeur: " . $this->db->error;
                    $_SESSION['message_type'] = 'danger';
                    
                    // Si c'est une requête AJAX, retourner JSON
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification: ' . $this->db->error]);
                        exit;
                    }
                }
                
                $stmt->close();
            } else {
                $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
                $_SESSION['message_type'] = 'danger';
                
                // Si c'est une requête AJAX, retourner JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
                    exit;
                }
            }
            
            // Redirection pour les requêtes normales
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des professeurs
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        }
    }

    /**
     * Supprimer un professeur
     */
    public function supprimerProfesseur() {
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider l'ID du professeur
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            // Validation des données
            if ($id <= 0) {
                $_SESSION['message'] = "ID de professeur invalide.";
                $_SESSION['message_type'] = 'danger';
                
                // Si c'est une requête AJAX, retourner JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'ID de professeur invalide']);
                    exit;
                }
                
                header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
                exit;
            }
            
            // Vérifier si le professeur existe
            $stmt = $this->db->prepare("SELECT nom, prenom FROM professeurs WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $_SESSION['message'] = "Professeur non trouvé.";
                $_SESSION['message_type'] = 'danger';
                $stmt->close();
                
                // Si c'est une requête AJAX, retourner JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Professeur non trouvé']);
                    exit;
                }
                
                header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
                exit;
            }
            
            $professeur = $result->fetch_assoc();
            $stmt->close();
            
            // Vérifier s'il y a des cours assignés à ce professeur
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM cours WHERE professeur_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count_cours = $result->fetch_assoc()['count'];
            $stmt->close();
            
            if ($count_cours > 0) {
                $_SESSION['message'] = "Impossible de supprimer ce professeur car il a des cours assignés. Veuillez d'abord réassigner ses cours.";
                $_SESSION['message_type'] = 'danger';
                
                // Si c'est une requête AJAX, retourner JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Impossible de supprimer: professeur a des cours assignés']);
                    exit;
                }
                
                header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
                exit;
            }
            
            // Vérifier s'il y a des présences enregistrées pour ce professeur
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM presences_professeurs WHERE professeur_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count_presences = $result->fetch_assoc()['count'];
            $stmt->close();
            
            // Commencer une transaction pour supprimer en cascade
            $this->db->begin_transaction();
            
            try {
                // Supprimer d'abord les présences du professeur
                if ($count_presences > 0) {
                    $stmt = $this->db->prepare("DELETE FROM presences_professeurs WHERE professeur_id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $stmt->close();
                }
                
                // Supprimer les horaires liés aux cours de ce professeur
                $stmt = $this->db->prepare("DELETE h FROM horaires h 
                                      INNER JOIN cours c ON h.cours_id = c.id 
                                      WHERE c.professeur_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                
                // Supprimer les cours du professeur
                $stmt = $this->db->prepare("DELETE FROM cours WHERE professeur_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                
                // Mettre à jour les classes où ce professeur était titulaire
                $stmt = $this->db->prepare("UPDATE classes SET titulaire = NULL WHERE titulaire = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                
                // Supprimer le professeur
                $stmt = $this->db->prepare("DELETE FROM professeurs WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                
                // Valider la transaction
                $this->db->commit();
                
                $_SESSION['message'] = "Le professeur " . $professeur['prenom'] . " " . $professeur['nom'] . " a été supprimé avec succès.";
                $_SESSION['message_type'] = 'success';
                
                // Si c'est une requête AJAX, retourner JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Professeur supprimé avec succès']);
                    exit;
                }
                
            } catch (Exception $e) {
                // Annuler la transaction en cas d'erreur
                $this->db->rollback();
                
                $_SESSION['message'] = "Erreur lors de la suppression du professeur: " . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
                
                // Si c'est une requête AJAX, retourner JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
                    exit;
                }
            }
            
            // Redirection pour les requêtes normales
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des professeurs
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
            exit;
        }
    }
    
    /**
     * Ajouter un nouveau professeur
     */
    public function ajouterProfesseur() {
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
            $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
            $section = isset($_POST['section']) ? trim($_POST['section']) : '';
            $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
            $date_naissance = isset($_POST['date_naissance']) ? $_POST['date_naissance'] : '';
            $lieu_naissance = isset($_POST['lieu_naissance']) ? trim($_POST['lieu_naissance']) : '';
            $sexe = isset($_POST['sexe']) ? $_POST['sexe'] : '';
            $situation_matrimoniale = isset($_POST['situation_matrimoniale']) ? $_POST['situation_matrimoniale'] : '';
            $nationalite = isset($_POST['nationalite']) ? trim($_POST['nationalite']) : '';
            $diplome = isset($_POST['diplome']) ? trim($_POST['diplome']) : '';
            $specialite = isset($_POST['specialite']) ? trim($_POST['specialite']) : '';
            $date_embauche = isset($_POST['date_embauche']) ? $_POST['date_embauche'] : date('Y-m-d');
            $salaire = isset($_POST['salaire']) ? floatval($_POST['salaire']) : 0;
            $statut = isset($_POST['statut']) ? $_POST['statut'] : 'Actif';
            
            // Validation des données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom est requis.";
            }
            
            if (empty($prenom)) {
                $errors[] = "Le prénom est requis.";
            }
            
            if (empty($email)) {
                $errors[] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide.";
            }
            
            if (empty($contact)) {
                $errors[] = "Le contact est requis.";
            }
            
            if (empty($section)) {
                $errors[] = "La section est requise.";
            } elseif (!in_array($section, ['maternelle', 'primaire', 'secondaire'])) {
                $errors[] = "Section invalide.";
            }
            
            if (empty($sexe)) {
                $errors[] = "Le sexe est requis.";
            } elseif (!in_array($sexe, ['M', 'F'])) {
                $errors[] = "Sexe invalide.";
            }
            
            if (empty($date_naissance)) {
                $errors[] = "La date de naissance est requise.";
            }
            
            if (empty($nationalite)) {
                $errors[] = "La nationalité est requise.";
            }
            
            // Vérifier si l'email existe déjà
            if (empty($errors)) {
                $stmt = $this->db->prepare("SELECT id FROM professeurs WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $errors[] = "Cet email est déjà utilisé par un autre professeur.";
                }
                $stmt->close();
            }
            
            // Si pas d'erreurs, insérer le nouveau professeur
            if (empty($errors)) {
                // Traitement de l'image si téléchargée
                $image_path = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = 'uploads/professeurs/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array(strtolower($file_extension), $allowed_extensions)) {
                        $new_filename = uniqid('prof_') . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $image_path = $upload_path;
                        }
                    }
                }
                
                // Insérer dans la base de données
                $stmt = $this->db->prepare("INSERT INTO professeurs (nom, prenom, email, contact, section, adresse, date_naissance, lieu_naissance, sexe, situation_matrimoniale, nationalite, diplome, specialite, date_embauche, salaire, statut, image, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                
                $stmt->bind_param("ssssssssssssssdss", $nom, $prenom, $email, $contact, $section, $adresse, $date_naissance, $lieu_naissance, $sexe, $situation_matrimoniale, $nationalite, $diplome, $specialite, $date_embauche, $salaire, $statut, $image_path);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Le professeur " . $prenom . " " . $nom . " a été ajouté avec succès.";
                    $_SESSION['message_type'] = 'success';
                    
                    // Si c'est une requête AJAX, retourner JSON
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => 'Professeur ajouté avec succès']);
                        exit;
                    }
                    
                    // Redirection vers la liste des professeurs
                    header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
                    exit;
                } else {
                    $_SESSION['message'] = "Erreur lors de l'ajout du professeur: " . $this->db->error;
                    $_SESSION['message_type'] = 'danger';
                    
                    // Si c'est une requête AJAX, retourner JSON
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $this->db->error]);
                        exit;
                    }
                }
                
                $stmt->close();
            } else {
                $_SESSION['message'] = "Erreurs: " . implode(" ", $errors);
                $_SESSION['message_type'] = 'danger';
                
                // Si c'est une requête AJAX, retourner JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
                    exit;
                }
            }
        }
        
        // Charger la vue du formulaire d'ajout
        require_once 'views/directeur/ajouter_professeur.php';
    }
}
?>
