<?php
class PrefetModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }
    public function getAll() {
        $result = $this->db->query("SELECT * FROM prefet");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM prefet WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function add($username,  $contact, $email, $adresse, $section) {
        $stmt = $this->db->prepare("INSERT INTO prefet (username,  contact, email, adresse, section) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $contact, $email, $adresse, $section);
        $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM prefets WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function update($id, $username, $contact, $email, $adresse,$section) {
        $stmt = $this->db->prepare("UPDATE users SET username = ?, contact = ?, email = ?, adresse=?, section=? WHERE id = ?");
        $stmt->bind_param("sssssi", $username,  $contact, $email, $adresse, $section, $id);
        $stmt->execute();
    }
}
?>
