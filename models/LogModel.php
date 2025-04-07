<?php
class LogModel {
    private $db;

    public function __construct() {
        // Connexion à la base de données
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Erreur de connexion : " . $this->db->connect_error);
        }
    }

    public function getAll() {
        $sql = "SELECT * FROM logs ORDER BY date DESC";
        $result = $this->db->query($sql);
        
        if (!$result) {
            throw new Exception("Erreur lors de la récupération des logs : " . $this->db->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function add($user_id, $username, $action) {
        $stmt = $this->db->prepare("INSERT INTO logs (user_id, username, action, date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $username, $action);
        
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }

    public function getByUserId($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM logs WHERE user_id = ? ORDER BY date DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $logs = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        
        return $logs;
    }

    public function getByDateRange($start_date, $end_date) {
        $stmt = $this->db->prepare("SELECT * FROM logs WHERE date BETWEEN ? AND ? ORDER BY date DESC");
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $logs = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        
        return $logs;
    }
}
?>