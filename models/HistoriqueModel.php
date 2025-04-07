
<?php
class HistoriqueModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAll($section = null) {
        $sql = "SELECT h.*, u.username FROM historique h LEFT JOIN users u ON h.user_id = u.id ORDER BY h.date_action DESC";
        if ($section) {
            $sql .= " WHERE h.section = ?";
        }
        $stmt = $this->db->prepare($sql);
        if ($section) {
            $stmt->bind_param("s", $section);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function add($action, $entite, $entite_id) {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Utiliser un ID utilisateur par défaut (1 pour admin) si aucun n'est disponible dans la session
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        $date = date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("INSERT INTO historique (user_id, action, date_action) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $action, $date);
        
        return $stmt->execute();
    }
    
    public function getByUser($user_id) {
        $sql = "SELECT * FROM historique WHERE user_id = ? ORDER BY date_action DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
