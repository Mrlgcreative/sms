
<?php
require 'models/EleveModel.php';
require 'models/ProfesseurModel.php';
require 'models/FraisModel.php';
require 'models/HistoriqueModel.php';
require 'models/ParentModel.php';
require 'models/CoursModel.php';
require 'models/ClasseModel.php';
require 'models/OptionModel.php';

class Prefet {
    private $eleveModel;
    private $professeurModel;
    private $fraisModel;
    private $historiqueModel;
    private $parentModel;
    private $coursModel;
    private $classeModel;
    private $optionModel;

    public function __construct() {
        $this->eleveModel = new EleveModel();
        $this->professeurModel = new ProfesseurModel();
        $this->fraisModel = new FraisModel();
        $this->historiqueModel = new HistoriqueModel();
        $this->parentModel = new ParentModel();
        $this->coursModel = new CoursModel();
        $this->classeModel = new ClasseModel();
        $this->optionModel = new OptionModel();
    }

    public function accueil() {
        require 'views/prefet/accueil.php';
    }

    public function eleves() {
        $eleves = $this->eleveModel->getAll();
        require 'views/prefet/eleves.php';
    }

    public function professeurs() {
        $professeurs = $this->professeurModel->getAll();
        require 'views/prefet/professeurs.php';
    }

    public function ajoutProfesseur() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $contact = $_POST['contact'];
            $classe = $_POST['classe'];
            $this->professeurModel->add($nom, $prenom, $email, $contact, $classe);
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=professeurs');
        } else {
            require 'views/prefet/ajout_professeur.php';
        }
    }

    public function frais() {
        $frais = $this->fraisModel->getAll();
        require 'views/prefet/frais.php';
    }

    public function ajoutFrais() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $montant = $_POST['montant'];
            $description = $_POST['description'];
            $classe = $_POST['classe'];
            $this->fraisModel->add($montant, $description, $classe);
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=frais');
        } else {
            require 'views/prefet/ajout_frais.php';
        }
    }

    public function historiques() {
        $historiques = $this->historiqueModel->getAll('secondaire');
        require 'views/prefet/historiques.php';
    }

    public function parents() {
        $parents = $this->parentModel->getAll();
        require 'views/prefet/parents.php';
    }

    public function cours() {
        $cours = $this->coursModel->getAll();
        require 'views/prefet/cours.php';
    }

    public function ajoutCours() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $this->coursModel->add($titre, $description,$p);
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=cours');
        } else {
            require 'views/prefet/ajout_cours.php';
        }
    }

    public function classes() {
        $classes = $this->classeModel->getAll();
        require 'views/prefet/classes.php';
    }

    public function ajoutClasse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $section = $_POST['section'];
            $this->classeModel->add($nom, $section);
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=classes');
}else{
    require 'views/prefet/ajout_classe.php';
        }
    }

    public function options() {
        $options = $this->optionModel->getAll();
        require 'views/prefet/options.php';
    }

    public function ajoutOption() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $this->optionModel->add($nom, $description);
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=options');
        } else {
            require 'views/prefet/ajout_option.php';
        }
    }
}
?>
