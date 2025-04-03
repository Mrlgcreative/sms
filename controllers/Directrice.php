
<?php
require 'models/EleveModel.php';
require 'models/ProfesseurModel.php';
require 'models/FraisModel.php';
require 'models/HistoriqueModel.php';
require 'models/ParentModel.php';

class Directrice {
    private $eleveModel;
    private $professeurModel;
    private $fraisModel;
    private $historiqueModel;
    private $parentModel;

    public function __construct() {
        $this->eleveModel = new EleveModel();
        $this->professeurModel = new ProfesseurModel();
        $this->fraisModel = new FraisModel();
        $this->historiqueModel = new HistoriqueModel();
        $this->parentModel = new ParentModel();
    }

    public function accueil() {
        require 'views/directrice/accueil.php';
    }

    public function eleves() {
        $eleves = $this->eleveModel->getAll();
        require 'views/directrice/eleves.php';
    }

    public function professeurs() {
        $professeurs = $this->professeurModel->getAll();
        require 'views/directrice/professeurs.php';
    }

    public function ajoutProfesseur() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $contact = $_POST['contact'];
            $classe = $_POST['classe'];
            $this->professeurModel->add($nom, $prenom, $email, $contact, $classe);
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
        } else {
            require 'views/directrice/ajout_professeur.php';
        }
    }

    public function frais() {
        $frais = $this->fraisModel->getAll();
        require 'views/directrice/frais.php';
    }

    public function ajoutFrais() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $montant = $_POST['montant'];
            $description = $_POST['description'];
            $classe = $_POST['classe'];
            $this->fraisModel->add($montant, $description, $classe);
            header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=frais');
        } else {
            require 'views/directrice/ajout_frais.php';
        }
    }

    public function historiques() {
        $historiques = $this->historiqueModel->getAll('maternelle');
        require 'views/directrice/historiques.php';
    }

    public function parents() {
        $parents = $this->parentModel->getAll();
        require 'views/directrice/parents.php';
    }
}
?>


