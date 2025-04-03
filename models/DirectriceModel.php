
<?php
class DirectriceModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAllDirectrice() {
        $result = $this->db->query("SELECT * FROM directrice");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM directrice WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function add($nom, $prenom,$contact, $email,$adresse, $section) {
        $stmt = $this->db->prepare("INSERT INTO directrice (nom, prenom, contact,  email, adresse,  section) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nom, $prenom,$contact, $email, $adresse,   $section);
        $stmt->execute();
    }

    public function update($id, $nom, $prenom,$contact, $email, $adresse, $section) {
        $stmt = $this->db->prepare("UPDATE directrice SET nom = ?, prenom = ?,contact = ?, email = ?, adresse=? , section=? WHERE id = ?");
        $stmt->bind_param("ssssssi", $nom, $prenom,$contact, $email, $adresse,  $section, $id);
        $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM directrices WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

