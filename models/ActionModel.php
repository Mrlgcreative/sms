<?php
require_once 'config/config.php';

class ActionModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function logAction($user_id, $action) {
        $stmt = $this->db->prepare("INSERT INTO actions (user_id, action) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $action);
        $stmt->execute();
    }

    public function getUserActions($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM actions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllActions() {
        $result = $this->db->query("SELECT * FROM actions");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>