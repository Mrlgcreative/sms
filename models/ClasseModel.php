
<?php
class ClasseModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAll() {
        $result = $this->db->query("SELECT c.*, p.nom as prof_nom, p.prenom as prof_prenom 
                                    FROM classes c 
                                    LEFT JOIN professeurs p ON c.prof_id = p.id");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT c.*, p.nom as prof_nom, p.prenom as prof_prenom 
                                    FROM classes c 
                                    LEFT JOIN professeurs p ON c.prof_id = p.id 
                                    WHERE c.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function add($nom, $niveau, $section, $titulaire = null, $prof_id = null) {
        $stmt = $this->db->prepare("INSERT INTO classes (nom, niveau, section, titulaire, prof_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nom, $niveau, $section, $titulaire, $prof_id);
        return $stmt->execute();
    }

    public function update($id, $nom, $niveau, $section, $titulaire = null, $prof_id = null) {
        $stmt = $this->db->prepare("UPDATE classes SET nom = ?, niveau = ?, section = ?, titulaire = ?, prof_id = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $nom, $niveau, $section, $titulaire, $prof_id, $id);
        return $stmt->execute();
    }

    public function getAllClasses() {
        $sql = "SELECT c.*, p.nom as prof_nom, p.prenom as prof_prenom 
                FROM classes c 
                LEFT JOIN professeurs p ON c.prof_id = p.id 
                ORDER BY c.niveau";
        $result = $this->db->query($sql);

        if (!$result) {
            throw new Exception("Erreur lors de la récupération des classes : " . $this->db->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function delete($id) {
        $sql = "DELETE FROM classes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getClassesBySection($section) {
        $stmt = $this->db->prepare("SELECT * FROM classes WHERE section = ? ORDER BY nom");
        $stmt->bind_param("s", $section);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getClassesWithStats() {
        $sql = "SELECT *,c.id, c.nom, c.niveau as classe_nom, 
                COUNT(e.id) as total_eleves,
                p.nom as prof_nom, p.prenom as prof_prenom
                FROM classes c
                LEFT JOIN eleves e ON e.classe = c.id
                LEFT JOIN professeurs p ON c.prof_id = p.id
                GROUP BY c.id
                ORDER BY c.niveau";
        $result = $this->db->query($sql);
        
        if (!$result) {
            throw new Exception("Erreur lors de la récupération des statistiques de classes : " . $this->db->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Récupère une classe par son nom
     * @param string $nom Le nom de la classe
     * @return array|null La classe trouvée ou null
     */
    public function getByNom($niveau) {
        $stmt = $this->db->prepare("SELECT * FROM classes WHERE niveau = ?");
        $stmt->bind_param("s", $niveau);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}
?>

