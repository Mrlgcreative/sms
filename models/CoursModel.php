
<?php
require_once 'config/config.php';

class CoursModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM cours");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM cours WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getBySection($section) {
        $stmt = $this->db->prepare("SELECT * FROM cours WHERE section = ?");
        $stmt->bind_param("s", $section);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function add($titre, $description, $professeur, $classe, $section, $option) {
        $stmt = $this->db->prepare("INSERT INTO cours (titre, description , professeur_id, classe_id, section, option_ ) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiss", $titre, $description, $professeur, $classe, $section, $option);
        $stmt->execute();
    }
    

    public function update($id, $titre, $description, $professeur, $classe, $section, $option) {
        $stmt = $this->db->prepare("UPDATE cours SET titre = ?, description = ?, professeur_id = ?, classe_id = ?, section = ?, option_ = ? WHERE id = ?");
        $stmt->bind_param("ssiissi", $titre, $description, $professeur, $classe, $section, $option_, $id);
        $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM cours WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getByProfesseur($professeurId) {
        $sql = "SELECT * FROM cours WHERE professeur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $professeurId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cours = [];
        while ($row = $result->fetch_assoc()) {
            $cours[] = $row;
        }
        
        return $cours;
    }

    // Mettre à jour la référence au professeur (option alternative)
    public function updateProfesseurReference($oldProfesseurId, $newProfesseurId) {
        $sql = "UPDATE cours SET professeur_id = ? WHERE professeur_id = ?";
        $stmt = $this->db->prepare($sql);
        
        // Si newProfesseurId est NULL, on doit utiliser bind_param différemment
        if ($newProfesseurId === NULL) {
            $null = NULL;
            $stmt->bind_param("ii", $null, $oldProfesseurId);
        } else {
            $stmt->bind_param("ii", $newProfesseurId, $oldProfesseurId);
        }
        
        return $stmt->execute();
    }
}
?>


