
<?php
require 'models/EleveModel.php';
require 'models/ProfesseurModel.php';
require 'models/FraisModel.php';
require 'models/HistoriqueModel.php';
require 'models/ParentModel.php';
require 'models/CoursModel.php';
require 'models/ClasseModel.php';

class Director {
    private $eleveModel;
    private $professeurModel;
    private $fraisModel;
    private $historiqueModel;
    private $parentModel;
    private $coursModel;
    private $classeModel;

    public function __construct() {
        $this->eleveModel = new EleveModel();
        $this->professeurModel = new ProfesseurModel();
        $this->fraisModel = new FraisModel();
        $this->historiqueModel = new HistoriqueModel();
        $this->parentModel = new ParentModel();
        $this->coursModel = new CoursModel();
        $this->classeModel = new ClasseModel();
    }

    public function accueil() {
        require 'views/director/accueil.php';
    }

    public function eleves() {
        $eleves = $this->eleveModel->getAll();
        require 'views/director/eleves.php';
    }

    public function professeurs() {
        $professeurs = $this->professeurModel->getAll();
        require 'views/director/professeurs.php';
    }

    public function ajoutProfesseur() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $contact = $_POST['contact'];
            $classe = $_POST['classe'];
            $this->professeurModel->add($nom, $prenom, $email, $contact, $classe);
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=professeurs');
        } else {
            require 'views/director/ajout_professeur.php';
        }
    }

    public function frais() {
        $frais = $this->fraisModel->getAll();
        require 'views/director/frais.php';
    }

    public function ajoutFrais() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $montant = $_POST['montant'];
            $description = $_POST['description'];
            $classe = $_POST['classe'];
            $this->fraisModel->add($montant, $description, $classe);
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=frais');
        } else {
            require 'views/director/ajout_frais.php';
        }
    }

    public function historiques() {
        $historiques = $this->historiqueModel->getAll('primaire');
        require 'views/director/historiques.php';
    }

    public function parents() {
        $parents = $this->parentModel->getAll();
        require 'views/director/parents.php';
    }

    public function cours() {
        $cours = $this->coursModel->getAll();
        require 'views/director/cours.php';
    }

    public function ajoutCours() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $this->coursModel->add($nom, $description);
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=cours');
        } else {
            require 'views/director/ajout_cours.php';
        }
    }

    public function classes() {
        $classes = $this->classeModel->getAll();
        require 'views/director/classes.php';
    }

    public function ajoutClasse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $section = $_POST['section'];
            $this->classeModel->add($nom, $section);
            header('Location: ' . BASE_URL . 'index.php?controller=Director&action=classes');
        } else {
            require 'views/director/ajout_classe.php';
        }
    }
}
?>
