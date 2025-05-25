
<?php
require 'models/EleveModel.php';
require 'models/PaiementModel.php';
require 'models/ParentModel.php';
require 'models/ClasseModel.php';
require 'models/OptionModel.php';
require 'models/FraisModel.php';
require 'models/MoisModel.php';
require 'models/SessionScolaireModel.php';
require 'models/UserModel.php'; // Ajout du UserModel


 // Assurez-vous d'inclure le fichier de configuration pour la connexion à la base de données

class Comptable {
    private $eleveModel;
    private $paiementModel;
    private $optionModel;
    private $classeModel;
    private $moisModel;
    private $fraismodel;
    private $sessionscolaireModel;
    private $userModel; // Ajout de la propriété userModel
    private $db;

    public function __construct() {
        $this->eleveModel = new EleveModel();
        $this->paiementModel = new PaiementModel();
        $this->optionModel = new OptionModel();
        $this->classeModel=new ClasseModel();
        $this->fraismodel= new FraisModel();
        $this->moisModel= new MoisModel();
        $this->sessionscolaireModel=new SessionScolaireModel();
        $this->userModel = new UserModel(); // Initialisation du UserModel
        $this->db = new mysqli("localhost", "root", "", "college1");
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
        
        require 'views/comptable/accueil.php';
    }

    public function paiements() {
        // Vérification de l'existence des paramètres GET
        $paiement_id = isset($_GET['paiement_id']) ? (int)$_GET['paiement_id'] : null;
        $eleve_id = isset($_GET['eleve_id']) ? (int)$_GET['eleve_id'] : null;
        $option_id = isset($_GET['option_id']) ? (int)$_GET['option_id'] : null;
        $session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : null;
    
        // Récupérer la session scolaire active
        $session_active = $this->sessionscolaireModel->getActive();
        
        // Vérification et récupération des données de paiement
        $paiements = [];
        if ($paiement_id) {
            $paiements = $this->paiementModel->getByPaiementId($paiement_id); // Méthode spécifique pour un paiement
            if (!$paiements) {
                $paiements = []; // Aucun paiement trouvé
            } else {
                // Convertir en tableau si c'est un seul résultat
                $paiements = [$paiements];
            }
        } elseif ($eleve_id) {
            // Récupérer les paiements d'un élève spécifique
            $paiements = $this->paiementModel->getByEleveId($eleve_id);
        } elseif ($session_id) {
            // Récupérer les paiements d'une session scolaire spécifique
            $paiements = $this->paiementModel->getBySessionId($session_id);
        } else {
            // Récupération de tous les paiements
            $paiements = $this->paiementModel->getAll();
        }
        
        // Enrichir les données de paiement avec les informations de session scolaire
        foreach ($paiements as &$paiement) {
            if (isset($paiement['session_scolaire_id']) && $paiement['session_scolaire_id']) {
                $session = $this->sessionscolaireModel->getById($paiement['session_scolaire_id']);
                if ($session) {
                    $paiement['libelle'] = $session['libelle'] ?? ($session['annee_debut'] . '-' . $session['annee_fin']);
                } else {
                    $paiement['libelle'] = 'Session inconnue';
                }
            } else {
                $paiement['libelle'] = 'Session non spécifiée';
            }
        }
        unset($paiement); // Détruire la référence
        
        // Traitement des options pour chaque paiement
        $option = null;
        if ($option_id) {
            $option = $this->optionModel->getAll($option_id);
            if (!$option) {
                die("Option non defini");
            }
        }
        
        // Récupérer toutes les sessions scolaires pour le filtre
        $sessions_scolaires = $this->sessionscolaireModel->getAll();
        
        // Vérifier si l'utilisateur est connecté
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        
        // Récupérer les données pour les filtres
        $classes = $this->classeModel->getAllClasses();
        $mois = $this->moisModel->getAll();
        $frais = $this->fraismodel->getAll();
        
        // Calculer le nombre total de paiements
        $total_paiements = count($paiements);
    
        // Charger la vue avec les données
        require 'views/comptable/paiement.php';
    }

    public function getOptionIdByName() {
        if (isset($_POST['option_name'])) {
            $option_name = $_POST['option_name'];
            
            // Get option ID from name
            $option = $this->optionModel->getByName($option_name);
            
            if ($option) {
                echo $option['id'];
            } else {
                echo "0"; // Return 0 if option not found
            }
        } else {
            echo "0";
        }
        exit;
    }


    /**
 * Récupère les mois non payés par un élève spécifique
 */
/**
 * Récupère les mois non payés par un élève spécifique
 */
/**
 * Récupère les mois non payés par un élève spécifique
 */
public function fetchMoisNonPayes() {
    if (!isset($_POST['eleve_id']) || empty($_POST['eleve_id'])) {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }
    
    $eleveId = $_POST['eleve_id'];
    
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Erreur de connexion: ' . $mysqli->connect_error]);
        exit;
    }
    
    // Vérifier le nom correct de la colonne dans la table paiements_frais
    // Récupérer les mois déjà payés par l'élève (où statut = 'payé' ou équivalent)
    $query = "SELECT DISTINCT moi_id FROM paiements_frais WHERE eleve_id = ? AND statut = 'payé'";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $eleveId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $moisPayes = [];
    while ($row = $result->fetch_assoc()) {
        $moisPayes[] = $row['moi_id'];
    }
    
    // Récupérer tous les mois disponibles qui ne sont pas dans la liste des mois payés
    $moisNonPayes = [];
    if (empty($moisPayes)) {
        // Si aucun mois n'a été payé, récupérer tous les mois
        $query = "SELECT id, nom FROM mois ORDER BY id";
    } else {
        // Sinon, récupérer uniquement les mois non payés
        $query = "SELECT id, nom FROM mois WHERE id NOT IN (" . implode(',', $moisPayes) . ") ORDER BY id";
    }
    
    $result = $mysqli->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $moisNonPayes[] = [
            'id' => $row['id'],
            'nom' => $row['nom']
        ];
    }
    
    $mysqli->close();
    
    // Définir l'en-tête Content-Type pour indiquer que la réponse est du JSON
    header('Content-Type: application/json');
    // Envoyer la réponse JSON
    echo json_encode($moisNonPayes);
    // Terminer l'exécution du script pour éviter tout contenu supplémentaire
    exit;
}

/**
 * Récupère tous les mois disponibles
 */
public function getAllMois() {
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Erreur de connexion: ' . $mysqli->connect_error]);
        exit;
    }
    
    // Récupérer tous les mois
    $query = "SELECT id, nom FROM mois ORDER BY id";
    $result = $mysqli->query($query);
    
    $allMois = [];
    while ($row = $result->fetch_assoc()) {
        $allMois[] = [
            'id' => $row['id'],
            'nom' => $row['nom']
        ];
    }
    
    $mysqli->close();
    
    // Définir l'en-tête Content-Type pour indiquer que la réponse est du JSON
    header('Content-Type: application/json');
    // Envoyer la réponse JSON
    echo json_encode($allMois);
    // Terminer l'exécution du script pour éviter tout contenu supplémentaire
    exit;
}


public function verifierEleveExistant() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom'];
        $post_nom = $_POST['post_nom'];
        $prenom = $_POST['prenom'];
       
        
        // Vérifier si l'élève existe déjà dans la base de données
        $query = "SELECT COUNT(*) as count FROM eleves 
                 WHERE nom = ? AND post_nom = ? AND prenom = ? ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $nom, $post_nom, $prenom);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            echo "existe";
        } else {
            echo "non_existe";
        }
        exit;
    }
}

/**
 * Récupère tous les mois disponibles
 */

 public function ajoutPaiement() {
    // Traitement du formulaire soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Récupération des données du formulaire
        $eleve_id = isset($_POST['eleve_id']) ? (int)$_POST['eleve_id'] : 0;
        $frai_id = isset($_POST['frais_id']) ? (int)$_POST['frais_id'] : 0;
        $amount_paid = isset($_POST['amount_paid']) ? (float)$_POST['amount_paid'] : 0;
        
        // Formatage des dates
        $payment_date = !empty($_POST['payment_date']) ? date('Y-m-d', strtotime($_POST['payment_date'])) : date('Y-m-d');
        $created_at = !empty($_POST['created_at']) ? date('Y-m-d', strtotime($_POST['created_at'])) : date('Y-m-d');
        
        // Autres données du formulaire
        $moi_id = isset($_POST['mois']) ? (int)$_POST['mois'] : 0;
        $classe_id = isset($_POST['classe_id']) ? $_POST['classe_id'] : '';
        $option_id = isset($_POST['option_id']) && !empty($_POST['option_id']) ? (int)$_POST['option_id'] : null;
        $section = isset($_POST['section']) ? $_POST['section'] : '';
        $reinscription_id = isset($_POST['reinscription_id']) ? (int)$_POST['reinscription_id'] : null;
        
        // Validation des données essentielles
        if (empty($eleve_id) || empty($frai_id) || empty($amount_paid)) {
            $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=ajoutpaiement');
            exit();
        }
        
        // Récupération de la session scolaire active
        $sessionScolaireModel = new SessionScolaireModel();
        $session_active = $sessionScolaireModel->getActive();
        $session_scolaire_id = null;
        
        if ($session_active) {
            $session_scolaire_id = $session_active['id'];
            
            // Vérification des paiements existants pour cette session
            $paiements_existants = $this->paiementModel->getPaiementsByEleveAndSession($eleve_id, $session_scolaire_id);
            
            // Initialisation du mois à septembre si premier paiement
            if (empty($paiements_existants) && empty($moi_id)) {
                $moi_id = 9; // Septembre (à adapter selon votre configuration)
            }
        } else {
            // Aucune session active trouvée
            $_SESSION['error'] = "Aucune session scolaire active n'a été trouvée. Veuillez en activer une avant d'ajouter un paiement.";
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=ajoutpaiement');
            exit();
        }
        
        // Journalisation pour débogage
        error_log("Payment Date: " . $payment_date);
        error_log("Created At: " . $created_at);
        error_log("Session Scolaire ID: " . $session_scolaire_id);
        error_log("Mois ID: " . $moi_id);
        
        // Traitement de la classe (conversion nom vers ID si nécessaire)
        if (!is_numeric($classe_id)) {
            $classeObj = $this->classeModel->getByNom($classe_id);
            
            if ($classeObj) {
                $classe_id = $classeObj['id'];
            } else {
                $_SESSION['error'] = "La classe spécifiée n'existe pas dans la base de données.";
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=ajoutpaiement');
                exit();
            }
        }
        
        // Vérification de l'existence de l'élève si des données d'inscription sont fournies
        if ((!isset($_POST['forcer_inscription']) || $_POST['forcer_inscription'] != '1') && 
            isset($_POST['nom']) && isset($_POST['post_nom']) && isset($_POST['prenom'])) {
            
            $nom = $_POST['nom'];
            $post_nom = $_POST['post_nom'];
            $prenom = $_POST['prenom'];
            
            $query = "SELECT COUNT(*) as count FROM eleves 
                     WHERE nom = ? AND post_nom = ? AND prenom = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sss", $nom, $post_nom, $prenom);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                $_SESSION['error'] = "Cet élève est déjà inscrit. Veuillez vérifier les informations.";
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=inscriptions&section=' . $section);
                exit();
            }
        }
        
        // Récupération de l'option de l'élève si non spécifiée
        if (empty($option_id)) {
            $eleve = $this->eleveModel->getById($eleve_id);
            if ($eleve && isset($eleve['option_id']) && !empty($eleve['option_id'])) {
                $option_id = $eleve['option_id'];
            }
        }
        
        // Vérification de l'existence de l'option
        if (!empty($option_id)) {
            $option = $this->optionModel->getById($option_id);
            if (!$option) {
                $option_id = null;
            }
        }
        
        // Traitement spécifique pour les réinscriptions
        if ($reinscription_id) {
            // Initialisation du mois à septembre si non spécifié
            if (empty($moi_id)) {
                $moi_id = 9;
            }
            
            // Ajout du paiement avec réinscription
            $this->paiementModel->addWithReinscription(
                $eleve_id, $frai_id, $amount_paid, 
                $payment_date, $created_at, $moi_id, 
                $classe_id, $option_id, $section, $reinscription_id,
                $session_scolaire_id
            );
        } else {
            // Ajout du paiement standard
            $this->paiementModel->add(
                $eleve_id, $frai_id, $amount_paid, 
                $payment_date, $created_at, $moi_id, 
                $classe_id, $option_id, $section,
                $session_scolaire_id
            );
        }
        
        // Récupération de l'ID du paiement pour le reçu
        $paiement_id = $this->paiementModel->getLastInsertedId();
        
        // Redirection avec message de succès
        $_SESSION['success'] = "Le paiement a été ajouté avec succès!";
        header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=paiements&success=1&message=' . urlencode('Le paiement a été ajouté avec succès!'));
        exit();
    } 
    // Affichage du formulaire
    else {
        // Récupération des données pour le formulaire
        $eleveModel = new EleveModel();
        $eleves = $eleveModel->getAll();
        
        $fraismodel = new FraisModel();
        $frais = $fraismodel->getAll();
        
        $moisModel = new MoisModel();
        $mois = $moisModel->getAll();
        
        $optionModel = new OptionModel();
        $options = $optionModel->getAll();
        
        // Récupération de la session scolaire active
        $sessionScolaireModel = new SessionScolaireModel();
        $session_active = $sessionScolaireModel->getActive();
        
        // Vérification des paramètres de réinscription
        $reinscription_id = isset($_GET['reinscription_id']) ? (int)$_GET['reinscription_id'] : null;
        $eleve_id = isset($_GET['eleve_id']) ? (int)$_GET['eleve_id'] : null;
        
        // Chargement de la vue
        require 'views/comptable/ajout_paiement.php';
    }
}
    
    // Nouvelle méthode pour générer le reçu
    public function genererRecu($paiement_id) {
        // Récupérer les informations du paiement
        $paiement = $this->paiementModel->getByPaiementId($paiement_id);
        
        if (!$paiement) {
            // Redirection avec message d'erreur si le paiement n'existe pas
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=paiements&error=1&message=' . urlencode('Paiement introuvable!'));
            exit();
        }
        
        // Récupérer les informations de l'élève
        $eleve = $this->eleveModel->getById($paiement['eleve_id']);
        
        // Récupérer les informations du frais
        $frais = $this->fraismodel->getById($paiement['frais_id']);
        
        // Récupérer les informations du mois
        $mois = $this->moisModel->getById($paiement['mois_id']);
        
        // Générer un numéro de reçu unique
        $receipt_number = 'RECU-' . date('Ymd') . '-' . $paiement_id;
        
        // Charger la vue du reçu
        require 'views/comptable/recu.php';
        exit();
    }
    
    // Méthode pour afficher un reçu existant
    public function afficherRecu() {
        if (isset($_GET['paiement_id'])) {
            $paiement_id = (int)$_GET['paiement_id'];
            $this->genererRecu($paiement_id);
        } else {
            // Redirection avec message d'erreur
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=paiements&error=1&message=' . urlencode('ID de paiement manquant!'));
            exit();
        }
    }


    public function fetchEleveDetails() {
        // Définir le type de réponse comme texte brut
        if (isset($_POST['eleve_id'])) {
            $eleve_id = (int)$_POST['eleve_id']; // Récupérer et sécuriser l'eleve_id
    
            try {
                // Appel au modèle pour récupérer les détails de l'élève
                $eleve = $this->eleveModel->getEleveDetailsById($eleve_id);
    
                if ($eleve) {
                    // Retourner les données sous forme de texte brut séparé par des délimiteurs
                    echo "{$eleve['eleve_nom']};{$eleve['classe_nom']};{$eleve['option_nom']};{$eleve['section_nom']}";
                } else {
                    echo "Aucun élève trouvé pour cet ID.";
                }
            } catch (Exception $e) {
                echo "Erreur lors de la récupération des informations de l'élève : " . $e->getMessage();
            }
        } else {
            echo "ID de l'élève manquant.";
        }
        exit; // Arrêter l'exécution pour éviter toute sortie supplémentaire
    }
    
    
    public function fetchFraisMontant() {
        // Vérifier si l'ID du frais est présent dans la requête POST
        if (isset($_POST['frais_id'])) {
            $frais_id = (int)$_POST['frais_id']; // Récupération et conversion en entier pour éviter les erreurs
    
            try {
                // Appel au modèle pour obtenir les informations du frais
                $frais = $this->fraismodel->getById($frais_id); // Récupère les données du frais
                if ($frais) {
                    // Retourne directement le montant
                    echo $frais['montant'];
                } else {
                    // Retourne un message d'erreur si aucun frais n'est trouvé
                    echo "Aucun frais trouvé pour cet ID.";
                }
            } catch (Exception $e) {
                // Gestion des erreurs en cas d'exception
                echo "Erreur lors de la récupération des frais : " . $e->getMessage();
            }
        } else {
            // Retourne un message si frais_id est absent
            echo "ID du frais manquant.";
        }
        exit; // Arrête l'exécution du script pour éviter tout contenu additionnel
    }
    
    public function getAllfrais() {
        $result = $this->db->query("SELECT * FROM frais");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function isComptableLoggedIn() {
        // Vérifier si la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier si l'utilisateur est connecté et a le rôle d'administrateur
        return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'comptable';
    }


    public function achatFournitures() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isComptableLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue
        require_once 'views/comptable/achatFourniture.php';
    }



    public function gestionStock() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isComptableLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue
        require_once 'views/admin/gestionStock.php';
    }
    
    
    
    public function ajouterAchat() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isComptableLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $date = isset($_POST['date_achat']) ? $_POST['date_achat'] : date('Y-m-d');
            $fournisseur = isset($_POST['fournisseur']) ? $_POST['fournisseur'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 0;
            $montant = isset($_POST['montant']) ? floatval($_POST['montant']) : 0;
            $facture_ref = isset($_POST['facture_ref']) ? $_POST['facture_ref'] : '';
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }
            
            // Instancier le modèle
            require_once 'models/AchatFourniture.php';
            $achatModel = new AchatFourniture($mysqli);
            
            // Ajouter l'achat
            $result = $achatModel->ajouterAchat($date, $fournisseur, $description, $quantite, $montant, $facture_ref);
            
            if ($result) {
                // Enregistrer l'action dans les logs
                $this->logAction("Ajout d'un achat de fourniture: " . $description);
                
                // Rediriger avec un message de succès
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=achatFournitures&success=1&message=' . urlencode('Achat ajouté avec succès'));
            } else {
                // Rediriger avec un message d'erreur
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=achatFournitures&error=1&message=' . urlencode('Erreur lors de l\'ajout de l\'achat'));
            }
            
            $mysqli->close();
            exit;
        } else {
            // Afficher le formulaire d'ajout
            require_once 'views/comptable/ajout_achat.php';
        }
    }




    public function supprimerAchat() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isComptableLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }
            
            // Instancier le modèle
            require_once 'models/AchatFourniture.php';
            $achatModel = new AchatFourniture($mysqli);
            
            // Récupérer les informations de l'achat avant suppression pour le log
            $achat = $achatModel->getAchatById($id);
            
            // Supprimer l'achat
            $result = $achatModel->supprimerAchat($id);
            
            if ($result) {
                // Enregistrer l'action dans les logs
                $description = isset($achat['description']) ? $achat['description'] : 'Achat #' . $id;
                $this->logAction("Suppression d'un achat de fourniture: " . $description);
                
                // Rediriger avec un message de succès
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=achatFournitures&success=1&message=' . urlencode('Achat supprimé avec succès'));
            } else {
                // Rediriger avec un message d'erreur
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=achatFournitures&error=1&message=' . urlencode('Erreur lors de la suppression de l\'achat'));
            }
            
            $mysqli->close();
        } else {
            // Rediriger si aucun ID n'est fourni
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=achatFournitures&error=1&message=' . urlencode('ID d\'achat non spécifié'));
        }
        exit;
    }


        
    
    public function ajouterArticle() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isComptableLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $categorie = isset($_POST['categorie']) ? $_POST['categorie'] : '';
            $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 0;
            $prix_unitaire = isset($_POST['prix_unitaire']) ? floatval($_POST['prix_unitaire']) : 0;
            $date_ajout = isset($_POST['date_ajout']) ? $_POST['date_ajout'] : date('Y-m-d');
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }
            
            // Instancier le modèle
            require_once 'models/Stock.php';
            $articleModel = new Stock($mysqli);
            
            // Ajouter l'article
            $result = $articleModel->ajouterArticle($nom, $description, $categorie, $quantite, $prix_unitaire, $date_ajout);
            
            if ($result) {
                // Enregistrer l'action dans les logs
                $this->logAction("Ajout d'un article au stock: " . $nom);
                
                // Rediriger avec un message de succès
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=gestionStock&success=1&message=' . urlencode('Article ajouté avec succès'));
            } else {
                // Rediriger avec un message d'erreur
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=gestionStock&error=1&message=' . urlencode('Erreur lors de l\'ajout de l\'article'));
            }
            
            $mysqli->close();
            exit;
        } else {
            // Afficher le formulaire d'ajout
            require_once 'views/admin/ajout_article.php';
        }
    }

// Fonction pour enregistrer les actions des utilisateurs
public function logAction($action) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        return false;
    }
    
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur inconnu';
    $ip = $_SERVER['REMOTE_ADDR'];
    $action = $mysqli->real_escape_string($action);
    
    // Check if the table exists and has the correct structure
    $tableCheck = $mysqli->query("SHOW COLUMNS FROM system_logs LIKE 'action'");
    
    if ($tableCheck && $tableCheck->num_rows > 0) {
        // Column exists, proceed with insert
        $result = $mysqli->query("INSERT INTO system_logs (username, action, ip_address) 
                                VALUES ('$username', '$action', '$ip')");
    } else {
        // Try with the correct column name (check your table structure)
        $result = $mysqli->query("DESCRIBE system_logs");
        $columns = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            
            // If we found a column that might store the action description
            if (in_array('action_description', $columns)) {
                $result = $mysqli->query("INSERT INTO system_logs (username, action_description, ip_address) 
                                        VALUES ('$username', '$action', '$ip')");
            } else {
                // Create the table with the correct structure if it doesn't exist
                $mysqli->query("CREATE TABLE IF NOT EXISTS system_logs (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    username VARCHAR(255) NOT NULL,
                    action VARCHAR(255) NOT NULL,
                    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    ip_address VARCHAR(50) NOT NULL,
                    PRIMARY KEY (id)
                )");
                
                // Try the insert again
                $result = $mysqli->query("INSERT INTO system_logs (username, action, ip_address) 
                                        VALUES ('$username', '$action', '$ip')");
            }
        }
    }
    
    $mysqli->close();
    
    return isset($result) ? $result : false;
}

   
    public function inscris() {
        $eleves = $this->eleveModel->getAll();
        $classes = $this->getClasses(); 
        $options= $this->getOptions();


        require 'views/comptable/inscris.php';
    }
    public function getClasses() {
        $result = $this->db->query("SELECT nom FROM classes");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getOptions() {
        $options = $this->db->query("SELECT  nom FROM options");
        return $options->fetch_all(MYSQLI_ASSOC);
    }

  

 
    
    public function inscriptions() {
        // Vérification des informations de session pour la vue
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        
        // Récupérer la section depuis l'URL
        $section = isset($_GET['section']) ? $_GET['section'] : '';
        // Récupérer les classes et options en fonction de la section
        $classes = $this->classeModel->getAllClasses($section);
        
        // Si la section est secondaire, récupérer les options
        $options = [];
        if ($section == 'secondaire') {
            $options = $this->optionModel->getAll();
        }
        
        require 'views/comptable/inscriptions.php';
    }


    public function enregistrerEleve() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupérer les données du formulaire
            $matricule = $_POST['matricule']; // Récupération du matricule généré automatiquement
            $nom = $_POST['nom'];
            $post_nom = $_POST['post_nom'];
            $prenom = $_POST['prenom'];
            $date_naissance = $_POST['date_naissance'];
            $lieu_naissance = $_POST['lieu_naissance'];
            $adresse = $_POST['adresse'];
            $classe_id = $_POST['classe_id'];
            $section = $_POST['section'];
            $option_id = isset($_POST['option_id']) ? $_POST['option_id'] : null;
            $sexe = isset($_POST['sexe']) ? $_POST['sexe'] : 'M';
            $profession = isset($_POST['profession']) ? $_POST['profession'] : '';
            $nom_pere = $_POST['nom_pere'];
            $nom_mere = $_POST['nom_mere'];
            $contact_pere = $_POST['contact_pere'];
            $contact_mere = $_POST['contact_mere'];
            $inscription_id = isset($_POST['inscription_id']) ? $_POST['inscription_id'] : 1; // Ajout du type d'inscription (1 = Inscription par défaut)
            
            // Traitement de l'upload de photo
            $photo_path = 'dist/img/default-student.png'; // Chemin par défaut
            
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/eleves/';
                
                // Créer le répertoire s'il n'existe pas
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Générer un nom de fichier unique
                $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $file_name = $matricule . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                
                // Déplacer le fichier téléchargé
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    $photo_path = $upload_path;
                }
            }
            
            // Récupérer l'année scolaire sélectionnée ou utiliser l'année active par défaut
            if (isset($_POST['annee_scolaire']) && !empty($_POST['annee_scolaire'])) {
                $annee_scolaire = $_POST['annee_scolaire'];
            } else {
                // Récupérer l'année scolaire active
                $this->sessionscolaireModel = new SessionScolaireModel();
                $session_active = $this->sessionscolaireModel->getActive();
                $annee_scolaire = $session_active ? $session_active['annee_debut'] . '-' . $session_active['annee_fin'] : date('Y') . '-' . (date('Y') + 1);
            }
            
            // Date d'inscription = aujourd'hui
            $date_inscription = date('Y-m-d');
            
            // Statut par défaut
            $statut = 'actif';
            
            // Enregistrer l'élève avec la photo, le matricule et le type d'inscription
            $eleve_id = $this->eleveModel->add(
                $nom, 
                $post_nom, 
                $prenom, 
                $date_naissance, 
                $lieu_naissance, 
                $adresse, 
                $classe_id, 
                $section, 
                $option_id, 
                $sexe, 
                $annee_scolaire, 
                $date_inscription, 
                $statut,
                $matricule,
                $photo_path,
                $profession,
                $nom_pere,
                $nom_mere,
                $contact_pere,
                $contact_mere,
                $inscription_id // Ajout du paramètre inscription_id
            );
            
            // Journaliser l'activité si l'inscription a réussi
            if ($eleve_id) {
                // Message simple pour tous les types d'inscription
                $type_inscription_texte = "Inscription";
                
                $this->logActivity('add', $type_inscription_texte . ' d\'un élève: ' . $nom . ' ' . $prenom . ' avec matricule: ' . $matricule);
                
                // Redirection avec message de succès
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=inscris&success=1&message=' . urlencode('L\'élève a été inscrit avec succès!'));
                exit();
            } else {
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=inscriptions&error=1&message=' . urlencode('Erreur lors de l\'inscription de l\'élève!'));
                exit();
            }
        }
    }
   
   
    public function recu() {
        if (isset($_GET['paiement_id'])) {
            $paiement_id = $_GET['paiement_id'];
            
            // Récupérer les informations du paiement
            $paiement = $this->paiementModel->getByPaiementId($paiement_id);
            
            if (!$paiement) {
                echo "Paiement introuvable.";
                return;
            }
            
            // Générer un numéro de reçu unique
            $receipt_number = 'RECU-' . date('Ymd', strtotime($paiement['payment_date'])) . '-' . $paiement_id;
            
            // Charger la vue du reçu
            require 'views/comptable/recu.php';
        } else {
            echo "ID du paiement manquant.";
        }
    }
    public function classes() {
        $classModel = new ClasseModel();
        $classes = $classModel->getAllClasses();
    
        include 'views/admin/classe.php';
    }

    
public function profil() {
    // Récupérer les informations de session pour la vue
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    if (!$user_id) {
        // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Récupérer les informations de l'utilisateur depuis la base de données
    $user = $this->userModel->getById($user_id);
    
    if ($user) {
        // Mettre à jour les variables de session avec les données les plus récentes
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['image'] = $user['image'] ?? 'dist/img/user2-160x160.jpg'; // Utiliser 'image' au lieu de 'photo_profil'
    }
    
    // Charger la vue du profil
    require 'views/comptable/profil.php';
}

public function updateProfile() {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login&error=1&message=' . urlencode('Vous devez être connecté pour modifier votre profil.'));
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    // Récupérer les données du formulaire
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : '';
    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
    $education = isset($_POST['education']) ? $_POST['education'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    
    // Vérifier si le mot de passe doit être mis à jour
    $update_password = false;
    if (!empty($password)) {
        if ($password !== $password_confirm) {
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=profil&error=1&message=' . urlencode('Les mots de passe ne correspondent pas.'));
            exit;
        }
        $update_password = true;
    }
    
    // Mettre à jour les informations de l'utilisateur en fonction de son rôle
    $success = false;
    
    if ($role == 'comptable') {
        // Mise à jour des informations de base
        $success = $this->userModel->update($user_id, $nom, $email, $role, $telephone, $adresse);
        
        // Mise à jour du mot de passe si nécessaire
        if ($success && $update_password) {
            $success = $this->userModel->updatePassword($user_id, $password);
        }
        
        // Mettre à jour les informations de session
        if ($success) {
            $_SESSION['username'] = $nom;
            $_SESSION['email'] = $email;
        }
    }
    // Ajouter d'autres rôles selon votre structure de base de données
    // elseif ($role == 'directeur') { ... }
    
    if ($success) {
        header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=profil&success=1&message=' . urlencode('Profil mis à jour avec succès.'));
    } else {
        header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=profil&error=1&message=' . urlencode('Erreur lors de la mise à jour du profil.'));
    }
    exit;
}

public function logActivity($action_type, $description) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Invité';
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $this->db->prepare("INSERT INTO system_logs (user_id, username, action_type, action_description, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action_type, $description, $ip_address);
    $stmt->execute();
    $stmt->close();
}



public function exportPaiementsPDF() {
    // Récupérer tous les paiements
    $paiements = $this->paiementModel->getAll();
    
    // Vérifier si des paiements existent
    if (empty($paiements)) {
        $_SESSION['error'] = "Aucun paiement à exporter.";
        header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=paiements');
        exit();
    }


    // Générer le contenu HTML directement dans la page
    // Définir les en-têtes pour indiquer que c'est une page à imprimer
    header('Content-Type: text/html; charset=utf-8');
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des Paiements</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            h1, h2 { text-align: center; }
            .footer { margin-top: 20px; text-align: right; font-weight: bold; }
            .no-print { display: none; }
            @media print {
                body { -webkit-print-color-adjust: exact; }
                @page { size: landscape; }
                .no-print { display: none; }
                .print-button { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="print-button" style="text-align: center; margin-bottom: 20px;">
            <button onclick="window.print();" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Imprimer / Enregistrer en PDF
            </button>
            <a href="' . BASE_URL . 'index.php?controller=comptable&action=paiements" style="margin-left: 10px; padding: 10px 20px; background-color: #f44336; color: white; text-decoration: none; border-radius: 4px;">
                Retour
            </a>
        </div>
        
        <h1>Liste des Paiements</h1>
        <h2>Date: ' . date('d/m/Y') . '</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom de l\'élève</th>
                    <th>Classe</th>
                    <th>Option</th>
                    <th>Section</th>
                    <th>Frais</th>
                    <th>Montant Payé</th>
                    <th>Date Paiement</th>
                    <th>Mois</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($paiements as $paiement) {
        echo '<tr>
            <td>' . $paiement['id'] . '</td>
            <td>' . htmlspecialchars($paiement['eleve_nom']) . '</td>
            <td>' . htmlspecialchars($paiement['classe_nom']) . '</td>
            <td>' . htmlspecialchars(isset($paiement['option_nom']) ? $paiement['option_nom'] : 'Non défini') . '</td>
            <td>' . htmlspecialchars($paiement['section']) . '</td>
            <td>' . htmlspecialchars($paiement['frais_description']) . '</td>
            <td>' . $paiement['amount_paid'] . '</td>
            <td>' . $paiement['payment_date'] . '</td>
            <td>' . htmlspecialchars($paiement['mois']) . '</td>
        </tr>';
    }
    
    echo '</tbody>
        </table>
        
        <div class="footer">
            Total des paiements: ' . count($paiements) . '
        </div>
        
        <script>
            // Afficher automatiquement la boîte de dialogue d\'impression après 1 seconde
            // Mais seulement si l\'utilisateur clique sur le bouton
            document.querySelector(".print-button button").addEventListener("click", function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        </script>
    </body>
    </html>';
    
    exit();
}
/**
 * Met à jour la photo de profil de l'utilisateur
 */
public function updateProfilePhoto() {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Vérifier si un fichier a été téléchargé
    if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] != 0) {
        $_SESSION['error'] = "Erreur lors du téléchargement du fichier";
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=profil');
        exit;
    }
    
    // Vérifier le type de fichier
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['profile_photo']['type'], $allowed_types)) {
        $_SESSION['error'] = "Type de fichier non autorisé";
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=profil');
        exit;
    }
    
    // Vérifier la taille du fichier (2MB max)
    if ($_FILES['profile_photo']['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = "Le fichier est trop volumineux (max 2MB)";
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=profil');
        exit;
    }
    
    // Créer le dossier uploads s'il n'existe pas
    $upload_dir = 'uploads/avatars/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Générer un nom de fichier unique
    $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
    $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $file_extension;
    $target_file = $upload_dir . $new_filename;
    
    // Déplacer le fichier téléchargé
    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
        // Mettre à jour la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['error'] = "Erreur de connexion à la base de données";
            header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=profil');
            exit;
        }
        
        // Récupérer l'ancienne image pour la supprimer si elle existe
        $stmt = $mysqli->prepare("SELECT image FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $old_image = $row['image'];
            // Supprimer l'ancienne image si ce n'est pas l'image par défaut
            if (!empty($old_image) && $old_image != 'dist/img/user2-160x160.jpg' && file_exists($old_image)) {
                unlink($old_image);
            }
        }
        $stmt->close();
        
        // Mettre à jour l'image dans la base de données
        $stmt = $mysqli->prepare("UPDATE users SET image = ? WHERE id = ?");
        $stmt->bind_param("si", $target_file, $user_id);
        
        if ($stmt->execute()) {
            // Mettre à jour la session
            $_SESSION['image'] = $target_file;
            $_SESSION['success'] = "Photo de profil mise à jour avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour de la base de données";
        }
        
        $stmt->close();
        $mysqli->close();
    } else {
        $_SESSION['error'] = "Erreur lors du déplacement du fichier";
    }
    
    header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=profil');
    exit;
}


public function supprimerPaiement() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Vérifier si l'ID du paiement est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['error'] = "ID du paiement non spécifié.";
        header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=paiements');
        exit;
    }
    
    $paiement_id = (int)$_GET['id'];
    
    // Vérifier si le paiement existe
    $paiement = $this->paiementModel->getByPaiementId($paiement_id);
    if (!$paiement) {
        $_SESSION['error'] = "Paiement introuvable.";
        header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=paiements');
        exit;
    }
    
    // Supprimer le paiement
    $success = $this->paiementModel->delete($paiement_id);
    
    if ($success) {
        // Enregistrer l'action dans les logs
        $this->logActivity('delete', "Suppression du paiement #$paiement_id pour l'élève " . $paiement['eleve_nom']);
        
        $_SESSION['success'] = "Le paiement a été supprimé avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression du paiement.";
    }
    
    header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=paiements');
    exit;
}



// Ajouter ces méthodes au contrôleur Comptable existant

public function modifierPaiement() {
    // Démarrer la session si elle n'est pas déjà démarrée
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Vérifier si l'utilisateur est connecté et a les droits
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comptable') {
        // Ajouter un message de débogage pour voir ce qui se passe
        $_SESSION['error'] = "Accès refusé. Rôle: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'non défini');
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si l'ID du paiement est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=paiements&error=1&message=' . urlencode('ID du paiement non spécifié'));
        exit;
    }
    
    $paiement_id = $_GET['id'];
    
    // Charger le modèle
    require_once 'models/PaiementModel.php';
    $paiementModel = new PaiementModel();
    
    // Récupérer les informations du paiement
    $paiement = $paiementModel->getByPaiementId($paiement_id);
    
    if (!$paiement) {
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=paiements&error=1&message=' . urlencode('Paiement non trouvé'));
        exit;
    }
    
    // Charger les modèles nécessaires
    require_once 'models/EleveModel.php';
    require_once 'models/FraisModel.php';
    require_once 'models/MoisModel.php';
    require_once 'models/OptionModel.php';
    
    $eleveModel = new EleveModel();
    $fraisModel = new FraisModel();
    $moisModel = new MoisModel();
    $optionModel = new OptionModel();
    
    // Récupérer les données nécessaires pour le formulaire
    $eleves = $eleveModel->getAll();
    $frais = $fraisModel->getAll();
    $mois = $moisModel->getAll();
    $options = $optionModel->getAll();
    
    // Charger la vue
    require_once 'views/comptable/modifierPaiement.php';
}

public function updatePaiement() {
    // Démarrer la session si elle n'est pas déjà démarrée
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Vérifier si l'utilisateur est connecté et a les droits
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comptable') {
        $_SESSION['error'] = "Accès refusé. Rôle: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'non défini');
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Le reste de votre code reste inchangé...
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=paiements');
        exit;
    }
    
    // Récupérer les données du formulaire
    $paiement_id = isset($_POST['paiement_id']) ? intval($_POST['paiement_id']) : 0;
    $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
    $frais_id = isset($_POST['frais_id']) ? intval($_POST['frais_id']) : 0;
    $amount_paid = isset($_POST['amount_paid']) ? $_POST['amount_paid'] : 0;
    $payment_date = isset($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d');
    $moi_id = isset($_POST['moi_id']) ? intval($_POST['moi_id']) : 0;
    $classe = isset($_POST['classe']) ? $_POST['classe'] : '';
    $option_id = isset($_POST['option_id']) ? intval($_POST['option_id']) : 0;
    $section = isset($_POST['section']) ? $_POST['section'] : '';
    
    // Validation des données
    if ($paiement_id <= 0 || $eleve_id <= 0 || $frais_id <= 0 || $amount_paid <= 0 || empty($payment_date) || $moi_id <= 0) {
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=modifierPaiement&id=' . $paiement_id . '&error=1&message=' . urlencode('Veuillez remplir tous les champs obligatoires'));
        exit;
    }
    
    // Charger le modèle
    require_once 'models/PaiementModel.php';
    $paiementModel = new PaiementModel();
    
    try {
        // Mettre à jour le paiement
        $created_at = date('Y-m-d H:i:s'); // Date de mise à jour
        $paiementModel->update($paiement_id, $eleve_id, $frais_id, $amount_paid, $payment_date, $created_at, $moi_id, $classe, $option_id, $section);
        
        // Enregistrer l'action dans les logs
        require_once 'models/LogModel.php';
        $logModel = new LogModel();
        $logModel->add($_SESSION['user_id'], $_SESSION['username'], 'Modification du paiement ID: ' . $paiement_id);
        
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=paiements&success=1&message=' . urlencode('Paiement mis à jour avec succès'));
    } catch (Exception $e) {
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=modifierPaiement&id=' . $paiement_id . '&error=1&message=' . urlencode('Erreur lors de la mise à jour du paiement: ' . $e->getMessage()));
    }
    exit;
}
public function logActivit($type, $description) {
    require_once 'models/LogModel.php';
    $logModel = new LogModel();
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Système';
    
    return $logModel->add($user_id, $username, $description);
}

/**
 * Exporte les paiements au format Excel
 */
public function exportPaiements() {
    // Vérifier si l'utilisateur est connecté et a les droits
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comptable') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Récupérer tous les paiements
    $paiements = $this->paiementModel->getAll();
    
    // Vérifier si des paiements existent
    if (empty($paiements)) {
        $_SESSION['error'] = "Aucun paiement à exporter.";
        header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=paiements');
        exit();
    }
    
    // Définir les en-têtes pour le téléchargement du fichier Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="liste_paiements_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Générer le contenu HTML qui sera interprété comme un fichier Excel
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            h1, h2 { text-align: center; }
        </style>
    </head>
    <body>
        <h1>Liste des Paiements</h1>
        <h2>Date d\'exportation: ' . date('d/m/Y') . '</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom de l\'élève</th>
                    <th>Classe</th>
                    <th>Option</th>
                    <th>Section</th>
                    <th>Frais</th>
                    <th>Montant Payé</th>
                    <th>Date Paiement</th>
                    <th>Mois</th>
                    <th>Date Création</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($paiements as $paiement) {
        echo '<tr>
            <td>' . $paiement['id'] . '</td>
            <td>' . htmlspecialchars($paiement['eleve_nom']) . '</td>
            <td>' . htmlspecialchars($paiement['classe']) . '</td>
            <td>' . htmlspecialchars(isset($paiement['option_nom']) ? $paiement['option_nom'] : 'Non défini') . '</td>
            <td>' . htmlspecialchars($paiement['section']) . '</td>
            <td>' . htmlspecialchars($paiement['frais_description']) . '</td>
            <td>' . $paiement['amount_paid'] . '</td>
            <td>' . date('d/m/Y', strtotime($paiement['payment_date'])) . '</td>
            <td>' . htmlspecialchars($paiement['mois']) . '</td>
            <td>' . date('d/m/Y H:i', strtotime($paiement['created_at'])) . '</td>
        </tr>';
    }
    
    echo '</tbody>
        </table>
        
        <div style="margin-top: 20px; text-align: right; font-weight: bold;">
            Total des paiements: ' . count($paiements) . '
        </div>
    </body>
    </html>';
    
    // Journaliser l'activité
    $this->logActivity('export', 'Exportation de la liste des paiements au format Excel');
    
    exit();
}






public function rapportactions() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Vérifier si l'utilisateur est connecté et a les droits
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comptable') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Charger le modèle de logs
    require_once 'models/LogModel.php';
    $logModel = new LogModel();
    
    // Récupérer tous les logs
    $logs = $logModel->getAll();
    
    // Calculer les statistiques
    $stats = [
        'ajouts' => 0,
        'modifications' => 0,
        'suppressions' => 0
    ];
    
    foreach ($logs as $log) {
        $action = strtolower($log['action']);
        if (strpos($action, 'ajout') !== false || strpos($action, 'add') !== false) {
            $stats['ajouts']++;
        } elseif (strpos($action, 'modif') !== false || strpos($action, 'update') !== false) {
            $stats['modifications']++;
        } elseif (strpos($action, 'suppr') !== false || strpos($action, 'delete') !== false) {
            $stats['suppressions']++;
        }
    }
    
    // Charger la vue
    require_once 'views/comptable/rapportactions.php';
}

public function exportLogs() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Vérifier si l'utilisateur est connecté et a les droits
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comptable') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Charger le modèle de logs
    require_once 'models/LogModel.php';
    $logModel = new LogModel();
    
    // Récupérer tous les logs
    $logs = $logModel->getAll();
    
    // Générer le fichier Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="rapport_actions_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
        <h2>Rapport des actions - ' . date('d/m/Y') . '</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($logs as $index => $log) {
        echo '<tr>
            <td>' . ($index + 1) . '</td>
            <td>' . htmlspecialchars($log['username']) . '</td>
            <td>' . htmlspecialchars($log['action']) . '</td>
            <td>' . date('d/m/Y H:i:s', strtotime($log['date'])) . '</td>
        </tr>';
    }
    
    echo '</tbody>
        </table>
        
        <div class="footer">
            Total des actions: ' . count($logs) . '
        </div>
    </body>
    </html>';
    
    exit();
}

// Méthode utilitaire pour enregistrer les actions dans les logs
// ... existing code ...

/**
 * Affiche le profil détaillé d'un élève
 */
public function viewStudent() {
    // Vérifier si l'utilisateur est connecté et a le rôle de comptable
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comptable') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Récupérer l'ID de l'élève depuis l'URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=inscris&error=1&message=' . urlencode("ID d'élève invalide"));
        exit;
    }
    
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        die("Erreur de connexion: " . $mysqli->connect_error);
    }
    
    // Récupérer les informations de l'élève
    $stmt = $mysqli->prepare("
        SELECT e.*, c. niveau as niveau, e. section, o.nom AS option_nom, s.libelle
        FROM eleves e
        LEFT JOIN sessions_scolaires s ON e.session_scolaire_id= s.id
        LEFT JOIN classes c ON e. classe_id = c.id
        LEFT JOIN options o ON e.option_id = o.id
        WHERE e.id = ?
    ");
    
    if (!$stmt) {
        die("Erreur de préparation: " . $mysqli->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $eleve = $result->fetch_assoc();
    $stmt->close();
    
    if (!$eleve) {
        header('Location: ' . BASE_URL . 'index.php?controller=Comptable&action=inscris&error=1&message=' . urlencode("Élève non trouvé"));
        exit;
    }
    
    // Récupérer les paiements de l'élève
 
$paiements = [];
$stmt = $mysqli->prepare("
    SELECT p.*, p.payment_date AS date_paiement, f.description AS type_paiement, m.nom AS mois ,
           p.amount_paid AS montant, CONCAT('REF-', p.id) AS reference,
           'Validé' AS statut, f.description AS frais_description
    FROM paiements_frais p
    LEFT JOIN mois m ON p.moi_id = m.id
    LEFT JOIN frais f ON p.frais_id = f.id
    WHERE p.eleve_id = ?
    ORDER BY p.payment_date DESC
");

if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $paiements = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
    
    // Récupérer les notes de l'élève
    $cours = [];
$stmt = $mysqli->prepare("
    SELECT c.*, p.nom AS professeur_nom
    FROM cours c
    LEFT JOIN professeurs p ON c.professeur_id = p.id
    WHERE c.classe_id = ? AND c.section = ? AND (c.option_ = ? OR c.option_ IS NULL)
    ORDER BY c.created_at DESC
");
    
if ($stmt) {
    $classe_id = isset($eleve['classe_id']) ? $eleve['classe_id'] : 0;
    $section = $eleve['section'];
    $option = isset($eleve['option_id']) ? $eleve['option_id'] : 0;
        $stmt->bind_param("isi", $classe_id, $section, $option);
    $stmt->execute();
    $result = $stmt->get_result();
    $cours = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    }
    
    // Fermer la connexion à la base de données
    $mysqli->close();
    
    // Journaliser l'activité
 
    
    // Charger la vue
    require 'views/comptable/view_student.php';
}




/**
 * Affiche la page de réinscription des élèves
 */
public function reinscription() {
    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'comptable') {
        $_SESSION['error'] = "Vous n'avez pas les droits pour accéder à cette page.";
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Récupérer la section si elle est définie
    $section = isset($_GET['section']) ? $_GET['section'] : '';
    
    // Récupérer les sessions scolaires
    $sessions_scolaires = $this->sessionscolaireModel->getAll();
    
    // Récupérer les classes selon la section
    $classes = [];
    $classes_primaire = [];
    $classes_secondaire = [];
    
    if ($section == 'maternelle') {
        $classes = $this->classeModel->getAllClasses('maternelle');
    } elseif ($section == 'primaire') {
        $classes_primaire = $this->classeModel->getAllClasses('primaire');
    } elseif ($section == 'secondaire') {
        $classes_secondaire = $this->classeModel->getAllClasses('secondaire');
    }
    
    // Récupérer les messages d'erreur ou de succès
    $error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
    $success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
    
    // Effacer les messages après les avoir récupérés
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    
    // Charger la vue
    require_once 'views/comptable/reinscription.php';
}




/**
 * Recherche un élève pour la réinscription
 */
public function rechercherEleve() {
    // Vérifier si la requête est en AJAX
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Requête non autorisée']);
        exit;
    }
    
    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'comptable') {
        echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas les droits pour effectuer cette action']);
        exit;
    }
    
    // Récupérer les paramètres de recherche
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $section = isset($_POST['section']) ? $_POST['section'] : '';
    
    if (empty($search) || empty($section)) {
        echo json_encode(['success' => false, 'message' => 'Paramètres de recherche incomplets']);
        exit;
    }
    
    // Rechercher les élèves
    $search = '%' . $search . '%';
    
    $query = "SELECT e.id, e.matricule, e.nom, e.post_nom, e.prenom, c.niveau as classe 
              FROM eleves e 
            
              JOIN classes c ON e.classe_id = c.id 
              WHERE (e.matricule LIKE ? OR e.nom LIKE ? OR e.post_nom LIKE ? OR e.prenom LIKE ?) 
              AND c.section = ? 
              AND e.session_scolaire_id = (SELECT MAX(id) FROM sessions_scolaires)
              ORDER BY e.nom, e.post_nom, e.prenom";
    
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("sssss", $search, $search, $search, $search, $section);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $eleves = [];
    while ($row = $result->fetch_assoc()) {
        $eleves[] = $row;
    }
    
    echo json_encode(['success' => true, 'eleves' => $eleves]);
    exit;
}

/**
 * Récupère les détails d'un élève pour la réinscription
 */
public function getEleveDetails() {
    // Vérifier si la requête est en AJAX
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Requête non autorisée']);
        exit;
    }
    
    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'comptable') {
        echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas les droits pour effectuer cette action']);
        exit;
    }
    
    // Récupérer l'ID de l'élève
    $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
    
    if ($eleve_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID d\'élève invalide']);
        exit;
    }
    
    // Récupérer les détails de l'élève
    $query = "SELECT e.*, c.niveau as classe, c.id as classe_id, e.session_scolaire_id 
              FROM eleves e 
           
              JOIN classes c ON e.classe_id = c.id 
              WHERE e.id = ? 
              AND e.session_scolaire_id = (SELECT MAX(id) FROM sessions_scolaires)";
    
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $eleve_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $eleve = $result->fetch_assoc();
    
    if (!$eleve) {
        echo json_encode(['success' => false, 'message' => 'Élève non trouvé']);
        exit;
    }
    
    // Ajouter le chemin complet de la photo si elle existe
    if (!empty($eleve['photo'])) {
        $eleve['photo'] = BASE_URL . 'uploads/eleves/' . $eleve['photo'];
    } else {
        $eleve['photo'] = BASE_URL . 'dist/img/default-student.png';
    }
    
    echo json_encode(['success' => true, 'eleve' => $eleve]);
    exit;
}

public function reinscris() {
    // Vérifier si l'utilisateur est connecté et a les droits de comptable
    if (!$this->isComptableLoggedIn()) {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Requête SQL pour récupérer toutes les réinscriptions avec leur statut de paiement
    $query = "SELECT r.id, e.id as eleve_id, e.nom as nom_eleve, e.post_nom, e.prenom, e.date_naissance, e.photo, e.matricule,
       c_new.niveau as nouvelle_classe, c_old.niveau as ancienne_classe, r.date_reinscription,
       CASE 
           WHEN p.amount_paid >= f.montant THEN 'Complet'
           WHEN p.amount_paid > 0 THEN 'Partiel'
           ELSE 'Non payé'
       END as statut_paiement
       FROM historique_reinscriptions r
       LEFT JOIN eleves e ON r.eleve_id = e.id
       LEFT JOIN classes c_new ON r.nouvelle_classe_id = c_new.id
       LEFT JOIN classes c_old ON r.ancienne_classe_id = c_old.id
       LEFT JOIN paiements_frais p ON r.id = p.reinscription_id
       LEFT JOIN frais f ON p.frais_id = f.id
       ORDER BY r.date_reinscription DESC";
        // Exécution de la requête
    $result = $this->db->query($query);
    
    if ($result) {
        $eleves = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $eleves = [];
        // Enregistrer l'erreur pour le débogage
        error_log("Erreur SQL dans reinscris(): " . $this->db->error);
    }
    
    // Comptage des élèves réinscrits
    $count_query = $this->db->query("SELECT COUNT(*) as total_eleves FROM historique_reinscriptions");
    $row = $count_query->fetch_assoc();
    $total_eleves = $row['total_eleves'] ?? 0;
    
    // Récupérer les classes et options pour les filtres
    $classes = $this->classeModel->getAllClasses();
    $options = $this->optionModel->getAll();
    
    // Récupérer la session scolaire active
    $session_active = $this->sessionscolaireModel->getActive();
    $current_session = $session_active ? $session_active['annee_debut'] . '-' . $session_active['annee_fin'] : date('Y') . '-' . (date('Y') + 1);
    
    // Charger la vue
    require 'views/comptable/reinscris.php';
}


/**
 * Méthode pour réinscrire un élève dans une nouvelle classe
 */
public function reinscrireEleve() {
    // Vérifier si l'utilisateur est connecté et a les droits de comptable
    if (!$this->isComptableLoggedIn()) {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $eleve_id = isset($_POST['eleve_id']) ? (int)$_POST['eleve_id'] : 0;
        $nouvelle_classe_id = isset($_POST['nouvelle_classe_id']) ? (int)$_POST['nouvelle_classe_id'] : 0;
        $section = isset($_POST['section']) ? $_POST['section'] : '';
        
        // Vérifier que les données nécessaires sont présentes
        if ($eleve_id <= 0 || $nouvelle_classe_id <= 0) {
            $_SESSION['error'] = "Veuillez fournir toutes les informations nécessaires pour la réinscription.";
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=reinscription&section=' . $section);
            exit();
        }
        
        // Récupérer les informations actuelles de l'élève
        $query = "SELECT e.*, c.niveau AS classe_nom FROM eleves e 
                 LEFT JOIN classes c ON e.classe_id = c.id 
                 WHERE e.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $eleve_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $eleve = $result->fetch_assoc();
        
        if (!$eleve) {
            $_SESSION['error'] = "Élève introuvable.";
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=reinscription&section=' . $section);
            exit();
        }
        
        // Récupérer les informations de la nouvelle classe
        $query = "SELECT niveau FROM classes WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $nouvelle_classe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $nouvelle_classe = $result->fetch_assoc();
        
        if (!$nouvelle_classe) {
            $_SESSION['error'] = "Classe introuvable.";
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=reinscription&section=' . $section);
            exit();
        }
        
        // Enregistrer l'historique de la réinscription
        $ancienne_classe_id = $eleve['classe_id'];
        $ancienne_classe_nom = $eleve['classe_nom'];
        
        $query = "INSERT INTO historique_reinscriptions (eleve_id, ancienne_classe_id, nouvelle_classe_id, date_reinscription) 
                  VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iii", $eleve_id, $ancienne_classe_id, $nouvelle_classe_id);
        $stmt->execute();
        
        // Mettre à jour les autres informations de l'élève si nécessaire
        $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : $eleve['adresse'];
        $contact_pere = isset($_POST['contact_pere']) ? $_POST['contact_pere'] : $eleve['contact_pere'];
        $contact_mere = isset($_POST['contact_mere']) ? $_POST['contact_mere'] : $eleve['contact_mere'];
        
        // Traitement de la photo si une nouvelle est fournie
        $photo = $eleve['photo']; // Garder l'ancienne photo par défaut
        
        if (isset($_FILES['nouvelle_photo']) && $_FILES['nouvelle_photo']['size'] > 0) {
            $upload_dir = 'uploads/eleves/';
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_FILES['nouvelle_photo']['name'];
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['nouvelle_photo']['tmp_name'], $upload_path)) {
                $photo = $upload_path;
            }
        }
        
        // Mettre à jour les informations de l'élève dans la base de données
        $query = "UPDATE eleves SET classe_id = ?, adresse = ?, contact_pere = ?, contact_mere = ?, photo = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("issssi", $nouvelle_classe_id, $adresse, $contact_pere, $contact_mere, $photo, $eleve_id);
        $result = $stmt->execute();
        
        if ($result) {
            $_SESSION['success'] = "L'élève " . $eleve['nom'] . " " . $eleve['post_nom'] . " " . $eleve['prenom'] . 
                                  " a été réinscrit avec succès. Ancienne classe : " . $ancienne_classe_nom . 
                                  ", Nouvelle classe : " . $nouvelle_classe['niveau'];
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=reinscription&section=' . $section);
            exit();
        } else {
            $_SESSION['error'] = "Une erreur s'est produite lors de la réinscription de l'élève.";
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=reinscription&section=' . $section);
            exit();
        }
    } else {
        // Rediriger vers la page de réinscription si accès direct
        header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=reinscription');
        exit();
    }
}

    // Afficher la page de gestion des sessions scolaires
    public function sessions_scolaires() {
        // Récupérer toutes les sessions scolaires
        $sessions = $this->sessionscolaireModel->getAll();
        
        // Récupérer la session active
        $session_active = $this->sessionscolaireModel->getActive();
        
        // Charger la vue
        require 'views/comptable/sessions_scolaires.php';
    }
    
    // Ajouter une nouvelle session scolaire
    public function ajouterSessionScolaire() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $libelle = isset($_POST['libelle']) ? $_POST['libelle'] : '';
            $annee_debut = isset($_POST['annee_debut']) ? (int)$_POST['annee_debut'] : 0;
            $annee_fin = isset($_POST['annee_fin']) ? (int)$_POST['annee_fin'] : 0;
           
            $est_active = isset($_POST['est_active']) ? 1 : 0;
            
            // Valider les données
            if (empty($libelle) || $annee_debut <= 0 || $annee_fin <= 0 ) {
                $_SESSION['error'] = "Tous les champs sont obligatoires.";
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
                exit;
            }
            
            // Ajouter la session
            $result = $this->sessionscolaireModel->add($annee_debut, $annee_fin, $libelle, $est_active);
            
            if ($result) {
                $_SESSION['success'] = "La session scolaire a été ajoutée avec succès.";
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de l'ajout de la session scolaire.";
            }
            
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
            exit;
        }
    }
    
    // Activer une session scolaire
    public function activerSessionScolaire() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            // Désactiver toutes les sessions
            $this->sessionscolaireModel->desactiverTout();
            
            // Activer la session spécifiée
            $result = $this->sessionscolaireModel->activer($id);
            
            if ($result) {
                $_SESSION['success'] = "La session scolaire a été activée avec succès.";
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de l'activation de la session scolaire.";
            }
            
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
            exit;
        }
    }
    
    // Modifier une session scolaire
    public function modifierSessionScolaire() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            // Si c'est une soumission de formulaire
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer les données du formulaire
                $libelle = isset($_POST['libelle']) ? $_POST['libelle'] : '';
                $annee_debut = isset($_POST['annee_debut']) ? (int)$_POST['annee_debut'] : 0;
                $annee_fin = isset($_POST['annee_fin']) ? (int)$_POST['annee_fin'] : 0;
                $date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : '';
                $date_fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : '';
                $est_active = isset($_POST['est_active']) ? 1 : 0;
                
                // Valider les données
                if (empty($libelle) || $annee_debut <= 0 || $annee_fin <= 0 || empty($date_debut) || empty($date_fin)) {
                    $_SESSION['error'] = "Tous les champs sont obligatoires.";
                    header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=modifierSessionScolaire&id=' . $id);
                    exit;
                }
                
                // Si la session doit être active, désactiver toutes les autres sessions
                if ($est_active) {
                    $this->sessionscolaireModel->desactiverTout();
                }
                
                // Mettre à jour la session
                $result = $this->sessionscolaireModel->update($id, $annee_debut, $annee_fin, $libelle, $est_active);
                
                if ($result) {
                    $_SESSION['success'] = "La session scolaire a été modifiée avec succès.";
                    header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
                } else {
                    $_SESSION['error'] = "Une erreur est survenue lors de la modification de la session scolaire.";
                    header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=modifierSessionScolaire&id=' . $id);
                }
                exit;
            }
            
            // Récupérer les données de la session à modifier
            $session = $this->sessionscolaireModel->getById($id);
            
            if (!$session) {
                $_SESSION['error'] = "Session scolaire introuvable.";
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
                exit;
            }
            
            // Charger la vue de modification
            require 'views/comptable/modifier_session_scolaire.php';
        } else {
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
            exit;
        }
    }
    
    // Supprimer une session scolaire
    public function supprimerSessionScolaire() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            // Vérifier si la session est active
            $session = $this->sessionscolaireModel->getById($id);
            
            if ($session && $session['est_active'] == 1) {
                $_SESSION['error'] = "Impossible de supprimer une session scolaire active.";
                header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
                exit;
            }
            
            // Supprimer la session
            $result = $this->sessionscolaireModel->delete($id);
            
            if ($result) {
                $_SESSION['success'] = "La session scolaire a été supprimée avec succès.";
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la suppression de la session scolaire.";
            }
            
            header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
            exit;
        }
    }

}
?>