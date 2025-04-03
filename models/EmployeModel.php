<?php
class EmployeModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM employes");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM employes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function add($nom, $prenom, $email, $contact,$adresse, $poste) {
        $stmt = $this->db->prepare("INSERT INTO employes (nom, prenom, email, contact, adresse, poste) VALUES (?, ?, ?, ?, ?,?)");
        $stmt->bind_param("ssssss", $nom, $prenom, $email, $contact, $adresse, $poste);
        $stmt->execute();
    }

    public function update($id, $nom, $prenom, $email, $contact, $adresse, $poste) {
        $stmt = $this->db->prepare("UPDATE employes SET nom = ?, prenom = ?, email = ?, contact = ?, adresse=?, poste = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $nom, $prenom, $email, $contact,$adresse, $poste, $id);
        $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM employes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>