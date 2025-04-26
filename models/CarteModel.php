<?php
class CarteModel {
    private $db;
    
    public function __construct() {
        // Make sure to establish the database connection
        global $mysqli;
        $this->db = $mysqli;
        
        // If the global connection isn't available, create a new one
        if (!$this->db) {
            $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($this->db->connect_error) {
                die("Connection failed: " . $this->db->connect_error);
            }
        }
    }
    
    public function creerCarte($eleve_id, $code_carte, $type_carte) {
        // D'abord, récupérer un ID valide de la table paiements_frais
        $query = "SELECT id FROM paiements_frais LIMIT 1";
        $result = $this->db->query($query);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $statut_id = $row['id'];
        } else {
            // Si aucun ID n'est trouvé, vous pourriez insérer une nouvelle entrée
            // ou utiliser une valeur par défaut si elle existe
            $statut_id = null; // Ou une autre valeur par défaut si la colonne n'accepte pas NULL
        }
        
        // Maintenant, insérer la carte avec un statut_id valide
        $stmt = $this->db->prepare("INSERT INTO cartes_eleves (eleve_id, code_carte, type_carte, statut_id, date_creation) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("issi", $eleve_id, $code_carte, $type_carte, $statut_id);
        return $stmt->execute();
    }
    
    public function desactiverCarte($id) {
        $query = "UPDATE cartes SET statut = 'paye' WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getCartesByEleveId($eleve_id) {
        $query = "SELECT * FROM cartes_eleves WHERE eleve_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $eleve_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getCarteByCode($code) {
        $query = "SELECT * FROM cartes_eleves WHERE code_carte = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}