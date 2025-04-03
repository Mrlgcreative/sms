
<?php
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

class Admin {
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

    public function accueil() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Créer la table system_logs si elle n'existe pas
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        
        // Vérifier si la table system_logs existe
        $table_exists = $mysqli->query("SHOW TABLES LIKE 'system_logs'")->num_rows > 0;
        
        if (!$table_exists) {
            // Créer la table system_logs
            $create_table_sql = "CREATE TABLE system_logs (
                id INT(11) NOT NULL AUTO_INCREMENT,
                username VARCHAR(255) NOT NULL,
                action VARCHAR(255) NOT NULL,
                action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
            )";
            
            $mysqli->query($create_table_sql);
            
            // Insérer quelques données d'exemple
            $sample_data = [
                ["admin", "Connexion au système"],
                ["admin", "Ajout d'un élève"],
                ["admin", "Modification d'un professeur"],
                ["comptable1", "Ajout d'un paiement"],
                ["directeur", "Consultation des rapports"]
            ];
            
            foreach ($sample_data as $data) {
                $username = $data[0];
                $action = $data[1];
                $ip = $_SERVER['REMOTE_ADDR'];
                
                $mysqli->query("INSERT INTO system_logs (username, action, ip_address) 
                               VALUES ('$username', '$action', '$ip')");
            }
        }
        
        $mysqli->close();
        
        // Charger la vue
        require_once 'views/admin/accueil.php';
    }

    public function eleves() {
        $eleves = $this->eleveModel->getAll();
        require 'views/admin/eleve.php';
    }

      
    

    public function professeurs() {
        $professeurs = $this->professeurModel->getAll();
        require 'views/admin/professeurs.php';
    }
    

    public function ajoutProfesseur() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $adresse=$_POST['adresse'];
            $classe_id = $_POST['classe_id'];
            $cours_id=$_POST['cours_id'];
            $section =$_POST['section'];
            
            $this->professeurModel->add($nom, $prenom,$contact, $email,$adresse,   $classe_id,
            $cours_id,  $section);
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=professeurs');
        } else {
            require 'views/admin/ajout_professeur.php';
        }
    }

   

    public function frais() {
        $frais = $this->fraisModel->getAll();
        require 'views/admin/frais.php';
    }

    public function ajoutFrais() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $montant = $_POST['montant'];
            $description = $_POST['description'];
            $section = $_POST['section'];
            $this->fraisModel->add($montant, $description, $section);
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=frais');
        } else {
            require 'views/admin/ajout_frais.php';
        }
    }

    public function paiementProfs() {
        require 'views/admin/paiement_profs.php';
    }

    public function directeurs() {
        $directeurModel = new DirectorModel ();
        $directeur= $directeurModel->getAll();
        require 'views/admin/directeurs.php';
    }

    public function addDirecteur(){
        if ($_SERVER['REQUEST_METHOD']=='POST'){
            $nom= $_POST['nom'];
            $prenom=$_POST['prenom'];
            $contact= $_POST['contact'];
            $email=$_POST['email'];
            $adresse =$_POST ['adresse'];
            $section=$_POST['section'];
            $this->directorModel->add($nom, $prenom, $contact, $email, $adresse, $section);
            header('location: '. BASE_URL .'index.php?controller=Admin&action=directeurs');

        }else{
            require 'views/admin/add_directeur.php';
        }
    }
    

    public function directrices() {
        $directriceModel = new DirectriceModel();
        $directrices= $directriceModel->getAllDirectrice();
        require 'views/admin/directrices.php';
    }

    public function adddirectrice(){
        if ($_SERVER['REQUEST_METHOD'] === "POST"){
            $nom= $_POST['nom'];
            $prenom= $_POST['prenom'];
            $contact=$_POST['contact'];
            $email= $_POST['email'];
            $adresse= $_POST['adresse'];
            $section =$_POST['section'];
            $this->directriceModel->add($nom, $prenom, $contact, $email, $adresse, $section);
            header('location: ' .BASE_URL . 'index.php?controller=Admin&action=directrices');
        }else{
            require 'views/admin/add_directrice.php';
        }
    }

    public function prefets() {
        $prefesModel= new PrefetModel();
        $prefets =$prefesModel->getAll();
        require 'views/admin/prefets.php';
    }

    public function addprefet(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nom= $_POST['nom'];
            $prenom= $_POST['prenom'];
            $contact=$_POST['contact'];
            $email=$_POST['email'];
            $adresse=$_POST['adresse'];
            $section=$_POST['section'];
            $this->prefetModel->add($nom, $prenom, $contact, $email, $adresse, $section);
            header('Location: ' .BASE_URL . 'index.php?controller=Admin&action=prefet');

        }else{
            require 'views/admin/add_prefet.php';
        }
    }

  // Méthode pour lister les employés
public function employes() {
    $employeModel = new EmployeModel();
    $employes = $employeModel->getAll();

    include 'views/admin/employes.php';
}

// Méthode pour ajouter un employé
public function ajoutemployes() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Traitement du formulaire d'ajout d'un employé
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $adresse= $_POST['adresse'];
        $poste = $_POST['poste'];

        $employeModel = new EmployeModel();
        $employeModel->add($nom, $prenom, $email, $contact,$adresse, $poste);

        // Rediriger après ajout
        header("Location: " . BASE_URL . "index.php?controller=Admin&action=employes");
    } else {
        include 'views/admin/ajout_employe.php';
    }
}

// Méthode pour modifier un employé


// Méthode pour supprimer un employé
public function deleteEmploye() {
    $id = $_GET['id'];
    $employeModel = new EmployeModel();
    $employeModel->delete($id);

    // Rediriger après suppression
    header("Location: " . BASE_URL . "index.php?controller=Admin&action=employes");
}

    public function historiques() {
        $historiques = $this->historiqueModel->getAll();
        require 'views/admin/rapport_action.php';
    }

    public function parents() {
        $parents = $this->eleveModel->getAll();
        require 'views/admin/parents.php';
    }

    public function cours() {
        $cours = $this->coursModel->getAll();
        require 'views/admin/cours.php';
    }

   
    public function ajoutCours() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement du formulaire d'ajout d'un cours
            $titre = isset($_POST['titre']) ? $_POST['titre'] : null;
            $description = isset($_POST['description']) ? $_POST['description'] : null;
            $professeur = isset($_POST['professeur_id']) ? $_POST['professeur_id'] : null;
            $classe = isset($_POST['classe_id']) ? $_POST['classe_id'] : null;
            $section = isset($_POST['section']) ? $_POST['section'] : null;
            $option = isset($_POST['option']) ? $_POST['option'] : ''; // option peut être null pour certaines sections

            // Vérification que les champs obligatoires ne sont pas vides
            if ($titre && $description && $professeur && $classe && $section) {
                $coursModel = new CoursModel();
                $coursModel->add($titre, $description, $professeur, $classe, $section, $option);

                // Rediriger après ajout
                header("Location: " . BASE_URL . "index.php?controller=Admin&action=cours");
            } else {
                $error = "Tous les champs obligatoires doivent être remplis.";
                include 'views/admin/ajout_cours.php';
            }
        } else {
            include 'views/admin/ajout_cours.php';
        }
    }


    

    public function classes() {
        $classModel = new ClasseModel();
        $classes = $classModel->getAllClasses();
    
        include 'views/admin/classe.php';
    }

    public function addClasse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $section = $_POST['section'];
            $this->classeModel->add($nom, $section);
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=classes');
        } else {
            require 'views/admin/add_classe.php';
        }
    }

    public function comptable() {
        $comptableModel = new ComptableModel();
        $comptables= $comptableModel->getAll();
        require 'views/admin/comptable.php';
    }

    public function addcomptable(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            $nom= $_POST['nom'];
            $prenom= $_POST['prenom'];
            $contact = $_POST['contact'];
            $email= $_POST['email'];
            $adresse= $_POST['adresse'];
           
            $this->comptableModel->add($nom, $prenom, $contact, $email, $adresse );
            header('Location: ' .BASE_URL . 'index.php?controller=Admin&action=comptable');

        }else{
            require 'views/admin/add_comptable.php';
        }
    }

    
// Méthode pour supprimer un élève

public function deleteEleve() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->eleveModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Élève', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=eleves');
    }
}

// Méthode pour supprimer un professeur
// Méthode pour supprimer un professeur
public function deleteProfesseur() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Vérifier si le professeur a des cours associés
        $coursAssocies = $this->coursModel->getByProfesseur($id);
        
        if (!empty($coursAssocies)) {
            // Option 1: Supprimer les cours associés
            foreach ($coursAssocies as $cours) {
                $this->coursModel->delete($cours['id']);
                // Modifié pour correspondre à la structure de la table historique
                $this->historiqueModel->add('Suppression', 'Cours', $cours['id']);
            }
        }
        
        // Maintenant on peut supprimer le professeur
        $this->professeurModel->delete($id);
        
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Professeur', $id);
        
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=professeurs');
    }
}

// Méthode pour supprimer des frais
public function deleteFrais() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->fraisModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Frais', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=frais');
    }
}

// Méthode pour supprimer un directeur
public function deleteDirecteur() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->directorModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Directeur', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=directeurs');
    }
}

// Méthode pour supprimer une directrice
public function deleteDirectrice() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->directriceModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Directrice', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=directrices');
    }
}

// Méthode pour supprimer un préfet
public function deletePrefet() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->prefetModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Préfet', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=prefet');
    }
}

// Méthode pour supprimer un parent
public function deleteParent() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->parentModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Parent', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=parents');
    }
}

// Méthode pour supprimer un cours
public function deleteCours() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->coursModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Cours', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=cours');
    }
}

// Méthode pour supprimer une classe
public function deleteClasse() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->classeModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Classe', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=classes');
    }
}

// Méthode pour supprimer un comptable
public function deleteComptable() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->comptableModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Comptable', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=comptable');
    }
}

// Méthode pour supprimer un utilisateur
public function deleteUser() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->userModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Utilisateur', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=users');
    }
}


public function nouvelleAnneeScolaire() {
    // Afficher le formulaire pour créer une nouvelle année
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        require 'views/admin/nouvelle_annee.php';
    } 
    // Traiter le formulaire soumis
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $annee_debut = $_POST['annee_debut'];
        $annee_fin = $_POST['annee_fin'];
        $libelle = $_POST['libelle'];
        
        try {
            $this->sessionscolaireModel = new SessionScolaireModel();
            $nouvelle_annee_id = $this->sessionscolaireModel->initialiserNouvelleAnnee($annee_debut, $annee_fin, $libelle);
            
            // Redirection avec message de succès
            header('Location: ' . BASE_URL . 'index.php?controller=admin&action=sessions&success=1');
            exit();
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            header('Location: ' . BASE_URL . 'index.php?controller=admin&action=nouvelleAnneeScolaire&error=' . urlencode($e->getMessage()));
            exit();
        }
    }
}

    public function profil() {
    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Charger la vue du profil
    require 'views/admin/profil.php';
}

public function updateProfile() {
    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Récupérer les données du formulaire
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : '';
    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
    
    // Mettre à jour les informations dans la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    // Modification de la requête pour ne pas inclure la colonne education
    $stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, telephone = ?, adresse = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nom, $email, $telephone, $adresse, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    
    if ($result) {
        // Mettre à jour les informations de session
        $_SESSION['username'] = $nom;
        $_SESSION['email'] = $email;
        
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&success=1&message=' . urlencode('Profil mis à jour avec succès'));
    } else {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('Erreur lors de la mise à jour du profil'));
    }
    
    $mysqli->close();
    exit;
}

public function updatePassword() {
    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Récupérer les données du formulaire
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    
    // Vérifier que les mots de passe correspondent
    if ($password !== $password_confirm) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('Les mots de passe ne correspondent pas'));
        exit;
    }
    
    // Vérifier le mot de passe actuel
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    $stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user || !password_verify($current_password, $user['password'])) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('Mot de passe actuel incorrect'));
        exit;
    }
    
    // Mettre à jour le mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    
    if ($result) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&success=1&message=' . urlencode('Mot de passe mis à jour avec succès'));
    } else {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('Erreur lors de la mise à jour du mot de passe'));
    }
    
    $mysqli->close();
    exit;
}

public function updateAvatar() {
    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Vérifier si un fichier a été téléchargé
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('Erreur lors du téléchargement de l\'image'));
        exit;
    }
    
    // Vérifier le type de fichier
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES['avatar']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.'));
        exit;
    }
    
    // Vérifier la taille du fichier (max 2MB)
    if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('L\'image est trop grande. Taille maximale: 2MB'));
        exit;
    }
    
    // Créer le dossier d'upload s'il n'existe pas
    $upload_dir = 'uploads/avatars/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Générer un nom de fichier unique
    $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $file_name = 'avatar_' . $user_id . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;
    
    // Déplacer le fichier téléchargé
    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('Erreur lors de l\'enregistrement de l\'image'));
        exit;
    }
    
    // Mettre à jour le chemin de l'image dans la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    $stmt = $mysqli->prepare("UPDATE users SET image = ? WHERE id = ?");
    $stmt->bind_param("si", $file_path, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    
    if ($result) {
        // Mettre à jour l'image dans la session
        $_SESSION['image'] = $file_path;
        
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&success=1&message=' . urlencode('Photo de profil mise à jour avec succès'));
    } else {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=' . urlencode('Erreur lors de la mise à jour de la photo de profil'));
    }
    
    $mysqli->close();
    exit;
}

// Méthode pour afficher les détails d'un élève
public function vieweleve() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Récupérer les informations de l'élève
        $eleve = $this->eleveModel->getById($id);
        
        if ($eleve) {
            // Charger la vue du profil de l'élève
            require 'views/admin/vieweleve.php';
        } else {
            // Rediriger vers la liste des élèves si l'élève n'existe pas
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=eleves&error=1&message=' . urlencode('Élève non trouvé'));
            exit;
        }
    } else {
        // Rediriger vers la liste des élèves si aucun ID n'est fourni
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=eleves');
        exit;
    }
}

// Méthode pour afficher les détails d'un professeur
// Remove the extra closing brace that was here

public function rapportactions() {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    // Get the table structure to determine column names
    $structure_query = "DESCRIBE system_logs";
    $structure_result = $mysqli->query($structure_query);
    $columns = [];
    
    if ($structure_result) {
        while ($row = $structure_result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    
    // Récupérer les actions du système
    $actions_query = "SELECT * FROM system_logs ORDER BY id DESC";
    $result = $mysqli->query($actions_query);
    
    $actions = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Ensure all expected keys exist in the data
            $action = [
                'id' => isset($row['id']) ? $row['id'] : '',
                'username' => isset($row['username']) ? $row['username'] : '',
                'action' => isset($row['action']) ? $row['action'] : 
                           (isset($row['action_description']) ? $row['action_description'] : ''),
                'action_time' => isset($row['action_time']) ? $row['action_time'] : 
                                (isset($row['timestamp']) ? $row['timestamp'] : date('Y-m-d H:i:s')),
                'ip_address' => isset($row['ip_address']) ? $row['ip_address'] : ''
            ];
            $actions[] = $action;
        }
    }
    
    // Fermer la connexion
    $mysqli->close();
    
    // Enregistrer cette consultation dans les logs
    $this->logAction("Consultation du rapport d'actions");
    
    // Charger la vue
    require 'views/admin/rapport_actions.php';
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

}

?>
