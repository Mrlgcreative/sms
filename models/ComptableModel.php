
<?php
require_once 'config/config.php';

class ComptableModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    // Méthode pour récupérer tous les paiements
    public function getAll() {
        $result = $this->db->query("SELECT * FROM comptable");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM comptable WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Méthode pour ajouter un comptable
    public function add($nom, $prenom,$contact, $email,$adresse) {
        $stmt = $this->db->prepare("INSERT INTO comptable (nom, prenom, contact,  email, adresse ) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom, $prenom,$contact, $email, $adresse);
        $stmt->execute();
    }

    public function update($nom, $prenom, $contact, $email, $adresse){
        $stmt = $this->db->prepare("UPDATE comptable SET nom = ?, prenom = ?, contact=?, email=?, adresse=? WHERE id = ?");
        $stmt->bind_param("sssssi", $nom, $prenom,$contact,$email, $adresse, $id);
        $stmt->execute();
    }

    public function delete($id) {
        // Modifier le nom de la table de 'comptables' à 'comptable' (ou au nom correct de votre table)
        $sql = "DELETE FROM comptable WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Méthode pour fermer la connexion à la base de données
    public function closeConnection() {
        if ($this->db) {
            $this->db->close();
        }
   }
}
?>
