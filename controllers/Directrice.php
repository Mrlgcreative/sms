
<?php

class Directrice {
    private $eleveModel;
    private $professeurModel;
    private $sessionscolaireModel;
    private $db;
    
    public function __construct() {
        require_once 'models/EleveModel.php';
        require_once 'models/ProfesseurModel.php';
        require_once 'models/SessionscolaireModel.php';
        
        $this->eleveModel = new EleveModel();
        $this->professeurModel = new ProfesseurModel();
        $this->sessionscolaireModel = new SessionscolaireModel();
        
        // Connexion à la base de données
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }
    
    public function accueil() {
        // Récupérer les informations de session pour la vue
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        
        // Vérifier si c'est une nouvelle connexion
        $showWelcomeMessage = false;
        if (isset($_SESSION['new_login']) && $_SESSION['new_login'] === true) {
            $showWelcomeMessage = true;
            // Réinitialiser le flag pour ne pas afficher le message à chaque chargement de page
            $_SESSION['new_login'] = false;
        }
        
        // Récupérer la session scolaire active
        $session_active = $this->sessionscolaireModel->getActive();
        $current_session = $session_active ? $session_active['annee_debut'] . '-' . $session_active['annee_fin'] : date('Y') . '-' . (date('Y') + 1);
        
        // Récupérer le nombre d'élèves de la section maternelle
        $result = $this->db->query("SELECT COUNT(*) AS total_maternelle FROM eleves WHERE section = 'maternelle'");
        $row = $result->fetch_assoc();
        $total_maternelle = $row['total_maternelle'];
        
        // Journaliser l'action
        $this->logAction("Accès au tableau de bord directrice");
        
        require 'views/directrice/accueil.php';
    }
    
    public function eleves() {
        // Récupérer les informations de session pour la vue
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        
        // Récupérer la classe depuis l'URL si elle est spécifiée
        $classe = isset($_GET['classe']) ? $_GET['classe'] : '';
        
        // Construire la requête SQL en fonction de la présence ou non d'une classe spécifique
        $query = "SELECT e.*, o.nom AS option_nom 
                  FROM eleves e 
                  LEFT JOIN options o ON e.option_id = o.id 
                  WHERE e.section = 'maternelle'";
        
        if (!empty($classe)) {
            $query .= " AND e.classe = '" . $this->db->real_escape_string($classe) . "'";
        }
        
        $query .= " ORDER BY e.nom, e.post_nom, e.prenom";
        
        $result = $this->db->query($query);
        $eleves = $result->fetch_all(MYSQLI_ASSOC);
        
        // Compter le nombre total d'élèves
        $total_eleves = count($eleves);
        
        // Journaliser l'action
        $this->logAction("Consultation de la liste des élèves de maternelle" . (!empty($classe) ? " (classe: $classe)" : ""));
        
        require 'views/directrice/eleves.php';
    }
    
    public function classes() {
        // Récupérer les informations de session pour la vue
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        
        // Récupérer les statistiques par classe
        $query = "
            SELECT 
                classe, 
                COUNT(*) as total,
                SUM(CASE WHEN sexe = 'M' THEN 1 ELSE 0 END) as garcons,
                SUM(CASE WHEN sexe = 'F' THEN 1 ELSE 0 END) as filles
            FROM eleves 
            WHERE section = 'maternelle' 
            GROUP BY classe 
            ORDER BY classe
        ";
        
        $result = $this->db->query($query);
        $classes = $result->fetch_all(MYSQLI_ASSOC);
        
        // Journaliser l'action
        $this->logAction("Consultation des statistiques par classe de maternelle");
        
        require 'views/directrice/classes.php';
    }
    
    public function statistiques() {
        // Récupérer les informations de session pour la vue
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        
        // Récupérer les statistiques générales
        $stats = [];
        
        // Total des élèves
        $result = $this->db->query("SELECT COUNT(*) AS total FROM eleves WHERE section = 'maternelle'");
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'];
        
        // Répartition par sexe
        $result = $this->db->query("
            SELECT 
                sexe, 
                COUNT(*) as total 
            FROM eleves 
            WHERE section = 'maternelle' 
            GROUP BY sexe
        ");
        
        $stats['sexe'] = [];
        while ($row = $result->fetch_assoc()) {
            $stats['sexe'][$row['sexe']] = $row['total'];
        }
        
        // Répartition par classe
        $result = $this->db->query("
            SELECT 
                classe, 
                COUNT(*) as total 
            FROM eleves 
            WHERE section = 'maternelle' 
            GROUP BY classe 
            ORDER BY classe
        ");
        
        $stats['classes'] = [];
        while ($row = $result->fetch_assoc()) {
            $stats['classes'][$row['classe']] = $row['total'];
        }
        
        // Journaliser l'action
        $this->logAction("Consultation des statistiques détaillées de la section maternelle");
        
        require 'views/directrice/statistiques.php';
    }
    
    public function viewStudent() {
        // Récupérer les informations de session pour la vue
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        
        // Vérifier si l'ID de l'élève est fourni
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=eleves&error=1&message=' . urlencode('ID de l\'élève manquant!'));
            exit();
        }
        
        $eleve_id = (int)$_GET['id'];
        
        // Récupérer les informations de l'élève
        $eleve = $this->eleveModel->getById($eleve_id);
        
        // Vérifier si l'élève existe et appartient à la section maternelle
        if (!$eleve || $eleve['section'] !== 'maternelle') {
            header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=eleves&error=1&message=' . urlencode('Élève non trouvé ou n\'appartient pas à la section maternelle!'));
            exit();
        }
        
        // Journaliser l'action
        $this->logAction("Consultation des détails de l'élève ID: $eleve_id");
        
        require 'views/directrice/view_student.php';
    }
    
    public function profil() {
        // Vérifier si l'utilisateur est connecté
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue du profil
        require 'views/directrice/profil.php';
    }
    
    public function carte() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        require 'views/directrice/carte.php';
    }

    public function achats() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        require_once 'views/directrice/achats.php';
    }

    public function ajouterAchat() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $fournisseur = $_POST['fournisseur'];
            $date_achat = $_POST['date_achat'];
            $description = $_POST['description'];
            $montant = $_POST['montant'];
            $statut = $_POST['statut'];
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Erreur de connexion: " . $mysqli->connect_error);
            }
            
            // Préparer la requête d'insertion
            $query = "INSERT INTO achats_fournitures (fournisseur, date_achat, description, montant, statut) 
                      VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sssds", $fournisseur, $date_achat, $description, $montant, $statut);
            
            // Exécuter la requête
            if ($stmt->execute()) {
                // Rediriger vers la page des achats avec un message de succès
                $_SESSION['message'] = "L'achat a été ajouté avec succès.";
                $_SESSION['message_type'] = "success";
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['message'] = "Erreur lors de l'ajout de l'achat: " . $mysqli->error;
                $_SESSION['message_type'] = "error";
            }
            
            $stmt->close();
            $mysqli->close();
            
            // Rediriger vers la page des achats
            header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=achats');
            exit;
        }
    }

    public function stock() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        require_once 'views/directrice/stock.php';
    }

    public function ajouterArticle() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = $_POST['nom'];
            $categorie = $_POST['categorie'];
            $description = $_POST['description'];
            $quantite = $_POST['quantite'];
            $seuil_alerte = $_POST['seuil_alerte'];
            $emplacement = $_POST['emplacement'];
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Erreur de connexion: " . $mysqli->connect_error);
            }
            
            // Préparer la requête d'insertion
            $query = "INSERT INTO stock_mouvements (nom, categorie, description, quantite, seuil_alerte, emplacement, date_dernier_mouvement) 
                      VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sssiss", $nom, $categorie, $description, $quantite, $seuil_alerte, $emplacement);
            
            // Exécuter la requête
            if ($stmt->execute()) {
                // Rediriger vers la page de stock avec un message de succès
                $_SESSION['message'] = "L'article a été ajouté avec succès.";
                $_SESSION['message_type'] = "success";
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['message'] = "Erreur lors de l'ajout de l'article: " . $mysqli->error;
                $_SESSION['message_type'] = "error";
            }
            
            $stmt->close();
            $mysqli->close();
            
            // Rediriger vers la page de stock
            header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=stock');
            exit;
        }
    }

    public function mouvementStock() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $article_id = $_POST['article_id'];
            $type_mouvement = $_POST['type_mouvement'];
            $quantite_mouvement = $_POST['quantite_mouvement'];
            $date_mouvement = $_POST['date_mouvement'];
            $motif = $_POST['motif'];
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Erreur de connexion: " . $mysqli->connect_error);
            }
            
            // Commencer une transaction
            $mysqli->begin_transaction();
            
            try {
                // Récupérer la quantité actuelle de l'article
                $query = "SELECT quantite FROM stock_mouvements WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("i", $article_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception("Article non trouvé.");
                }
                
                $row = $result->fetch_assoc();
                $quantite_actuelle = $row['quantite'];
                
                // Calculer la nouvelle quantité
                $nouvelle_quantite = $quantite_actuelle;
                if ($type_mouvement === 'entree') {
                    $nouvelle_quantite += $quantite_mouvement;
                } else if ($type_mouvement === 'sortie') {
                    if ($quantite_actuelle < $quantite_mouvement) {
                        throw new Exception("Quantité insuffisante en stock.");
                    }
                    $nouvelle_quantite -= $quantite_mouvement;
                }
                
                // Mettre à jour le stock
                $query = "UPDATE stock_mouvements SET quantite = ?, date_dernier_mouvement = ? WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("isi", $nouvelle_quantite, $date_mouvement, $article_id);
                $stmt->execute();
                
                // Enregistrer le mouvement dans l'historique
                $query = "INSERT INTO stock_mouvements (article_id, type_mouvement, quantite, date_mouvement, motif, utilisateur) 
                          VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                $utilisateur = $_SESSION['username'];
                $stmt->bind_param("isisss", $article_id, $type_mouvement, $quantite_mouvement, $date_mouvement, $motif, $utilisateur);
                $stmt->execute();
                
                // Valider la transaction
                $mysqli->commit();
                
                // Rediriger avec un message de succès
                $_SESSION['message'] = "Le mouvement de stock a été enregistré avec succès.";
                $_SESSION['message_type'] = "success";
                
            } catch (Exception $e) {
                // Annuler la transaction en cas d'erreur
                $mysqli->rollback();
                
                // Rediriger avec un message d'erreur
                $_SESSION['message'] = "Erreur: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
            
            $mysqli->close();
            
            // Rediriger vers la page de stock
            header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=stock');
            exit;
        }
    }

    public function exportStock() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Erreur de connexion: " . $mysqli->connect_error);
        }
        
        // Récupérer les données du stock
        $query = "SELECT * FROM stock_items ORDER BY categorie, nom";
        $result = $mysqli->query($query);
        
        // Définir les en-têtes pour le téléchargement du fichier CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=inventaire_stock_' . date('Y-m-d') . '.csv');
        
        // Créer un fichier de sortie
        $output = fopen('php://output', 'w');
        
        // Ajouter l'en-tête UTF-8 BOM pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Ajouter les en-têtes des colonnes
        fputcsv($output, ['ID', 'Nom', 'Catégorie', 'Description', 'Quantité', 'Seuil d\'alerte', 'Emplacement', 'Dernier mouvement']);
        
        // Ajouter les données
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['nom'],
                $row['categorie'],
                $row['description'],
                $row['quantite'],
                $row['seuil_alerte'],
                $row['emplacement'],
                $row['date_dernier_mouvement']
            ]);
        }
        
        $mysqli->close();
        exit;
    }

    public function evenements() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        require_once 'views/directrice/evenements.php';
    }

    public function ajouterEvenement() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $titre = $_POST['titre'];
            $type = $_POST['type'];
            $description = $_POST['description'];
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            $lieu = $_POST['lieu'];
            $couleur = $_POST['couleur'];
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Erreur de connexion: " . $mysqli->connect_error);
            }
            
            // Préparer la requête d'insertion
            $query = "INSERT INTO evenements_scolaires (titre, type, description, date_debut, date_fin, lieu, couleur, createur) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $mysqli->prepare($query);
            $createur = $_SESSION['username'];
            $stmt->bind_param("ssssssss", $titre, $type, $description, $date_debut, $date_fin, $lieu, $couleur, $createur);
            
            // Exécuter la requête
            if ($stmt->execute()) {
                // Rediriger vers la page des événements avec un message de succès
                $_SESSION['message'] = "L'événement a été ajouté avec succès.";
                $_SESSION['message_type'] = "success";
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['message'] = "Erreur lors de l'ajout de l'événement: " . $mysqli->error;
                $_SESSION['message_type'] = "error";
            }
            
            $stmt->close();
            $mysqli->close();
            
            // Rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=evenements');
            exit;
        }
    }

    public function detailEvenement() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID de l'événement est fourni
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=evenements');
            exit;
        }
        
        $id = $_GET['id'];
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Erreur de connexion: " . $mysqli->connect_error);
        }
        
        // Récupérer les détails de l'événement
        $query = "SELECT * FROM evenements WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Événement non trouvé, rediriger
            $_SESSION['message'] = "Événement non trouvé.";
            $_SESSION['message_type'] = "error";
            header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=evenements');
            exit;
        }
        
        $evenement = $result->fetch_assoc();
        
        $mysqli->close();
        
        // Afficher la vue des détails de l'événement
        require_once 'views/directrice/detail_evenement.php';
    }

    public function exportEvenements() {
        // Vérifier si l'utilisateur est connecté et a le rôle de directrice
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'directrice') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Erreur de connexion: " . $mysqli->connect_error);
        }
        
        // Récupérer les événements
        $query = "SELECT * FROM evenements_scolaires ORDER BY date_debut";
        $result = $mysqli->query($query);
        
        // Définir les en-têtes pour le téléchargement du fichier CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=calendrier_evenements_' . date('Y-m-d') . '.csv');
        
        // Créer un fichier de sortie
        $output = fopen('php://output', 'w');
        
        // Ajouter l'en-tête UTF-8 BOM pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Ajouter les en-têtes des colonnes
        fputcsv($output, ['ID', 'Titre', 'Type', 'Description', 'Date de début', 'Date de fin', 'Lieu', 'Créateur']);
        
        // Ajouter les données
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['titre'],
                $row['type'],
                $row['description'],
                $row['date_debut'],
                $row['date_fin'],
                $row['lieu'],
                $row['createur']
            ]);
        }
        
        $mysqli->close();
        exit;
    }

    // Fonction pour enregistrer les actions des utilisateurs
    public function logAction($action) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur inconnu';
        $ip = $_SERVER['REMOTE_ADDR'];
        $action = $this->db->real_escape_string($action);
        
        // Vérifier si la table et la colonne existent
        $tableCheck = $this->db->query("SHOW COLUMNS FROM system_logs LIKE 'action_type'");
        
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $result = $this->db->query("INSERT INTO system_logs (username, action_type, ip_address) 
                                    VALUES ('$username', '$action', '$ip')");
        } else {
            // Créer la table si elle n'existe pas
            $this->db->query("CREATE TABLE IF NOT EXISTS system_logs (
                id INT(11) NOT NULL AUTO_INCREMENT,
                username VARCHAR(255) NOT NULL,
                action VARCHAR(255) NOT NULL,
                action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
            )");
            
            // Réessayer l'insertion
            $result = $this->db->query("INSERT INTO system_logs (username, action_type, ip_address) 
                                    VALUES ('$username', '$action', '$ip')");
        }
        
        return isset($result) ? $result : false;
    }
}
?>


