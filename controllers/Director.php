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
                                        LEFT JOIN professeurs p ON c.professeur_principal_id = p.id 
                                        WHERE c.section = ? 
                                        GROUP BY c.id 
                                        ORDER BY c.nom");
            $stmt->bind_param("s", $section);
        } else {
            $stmt = $this->db->prepare("SELECT c.*, COUNT(e.id) as nb_eleves, p.nom as prof_nom, p.prenom as prof_prenom 
                                        FROM classes c 
                                        LEFT JOIN eleves e ON c.id = e.classe_id 
                                        LEFT JOIN professeurs p ON c.professeur_principal_id = p.id 
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
        $classe = isset($_GET['classe']) ? $_GET['classe'] : '';
        
        if (empty($classe)) {
            $_SESSION['message'] = 'Aucune classe spécifiée.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=classes');
            exit;
        }
        
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
                 LEFT JOIN classes cl ON c.classe_id = cl.id 
                 ORDER BY c.jour, c.heure_debut";
        
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
                 FROM incidents_discipline i 
                 JOIN eleves e ON i.eleve_id = e.id 
                 JOIN classes c ON e.classe_id = c.id 
                 ORDER BY i.date DESC, e.nom, e.prenom";
        
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
                $stmt = $this->db->prepare("INSERT INTO incidents_discipline (eleve_id, date_incident, description, sanction, statut, date_creation) 
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
                $stmt = $this->db->prepare("UPDATE incidents_discipline 
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
            
            $stmt = $this->db->prepare("DELETE FROM incidents_discipline WHERE id = ?");
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
                 ORDER BY a.date DESC, e.nom, e.prenom";
        
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
                $stmt = $this->db->prepare("INSERT INTO absences (eleve_id, date, motif, justifiee, date_creation) 
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
    
    // Destructeur pour fermer la connexion à la base de données
    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }
}
?>