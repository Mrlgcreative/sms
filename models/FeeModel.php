
<?php
require_once 'config/config.php';

class FeeModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->db->connect_error) {
            die("Erreur de connexion à la base de données : " . $this->db->connect_error);
        }
    }

    public function getTotalFees() {
        $result = $this->db->query("SELECT COUNT(*) AS total_fees FROM frais");
        $row = $result->fetch_assoc();
        return $row['total_fees'];
    }
}
?>

