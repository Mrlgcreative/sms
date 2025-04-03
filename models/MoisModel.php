
<?php



class MoisModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM mois");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllsection($section){
        $stmt= $this->db->prepare("SELECT * FROM mois WHERE section=?");
        $stmt->bind_param("s", $section);
        $stmt->execute();
        $result=$stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM mois WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function add($montant, $description, $section) {
        $stmt = $this->db->prepare("INSERT INTO mois (montant, description, section) VALUES (?, ?, ?)");
        $stmt->bind_param("dss", $montant, $description, $section);
        $stmt->execute();
    }

    public function update($id, $montant, $description, $section) {
        $stmt = $this->db->prepare("UPDATE mois SET montant = ?, description = ?, section = ? WHERE id = ?");
        $stmt->bind_param("dssi", $montant, $description, $section, $id);
        $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM mois WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    public function getMoisPaye($eleve_id) {
        $stmt = $this->db->prepare("
            SELECT m.nom 
            FROM mois m
            INNER JOIN paiements p ON m.id = p.mois_id
            WHERE p.eleve_id = ?
        ");
        $stmt->bind_param("i", $eleve_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $mois_payes = [];
        while ($row = $result->fetch_assoc()) {
            $mois_payes[] = $row['nom'];
        }
        return $mois_payes;
    }


}
?>

