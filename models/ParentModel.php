
<?php
class ParentModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM parents");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM parents WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function add($nom, $prenom, $contact, $email) {
        $stmt = $this->db->prepare("INSERT INTO parents (nom, prenom, contact, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nom, $prenom, $contact, $email);
        $stmt->execute();
    }

   // Méthode pour récupérer tous les parents
   public function getAllParents() {
    $sql = "SELECT id, nom FROM parents";
    $result = $this->db->query($sql);

    if (!$result) {
        throw new Exception("Erreur lors de la récupération des parents : " . $this->db->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}


    public function update($id, $nom, $prenom, $contact, $email) {
        $stmt = $this->db->prepare("UPDATE parents SET nom = ?, prenom = ?, contact = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nom, $prenom, $contact, $email, $id);
        $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM parents WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
