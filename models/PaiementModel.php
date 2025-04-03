
<?php
class PaiementModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Erreur de connexion: " . $this->db->connect_error);
        }
    }

    public function getPaiementById($id) {
        $stmt = $this->db->prepare("SELECT p.*, e.nom as eleve_nom, e.prenom as eleve_prenom, c.nom as classe, o.nom as option_nom, s.nom as section, f.description as frais_description 
                                FROM paiements_frais p
                                JOIN eleves e ON p.eleve_id = e.id
                                JOIN classes c ON e.classe_id = c.id
                                JOIN options o ON e.option_id = o.id
                                JOIN sections s ON e.section_id = s.id
                                JOIN frais f ON p.frais_id = f.id
                                WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function getAllEleves() {
        $eleves = [];
        $result = $this->db->query("SELECT e.id, e.nom, e.prenom, c.nom as classe, o.nom as option_nom, s.nom as section 
                                FROM eleves e
                                JOIN classes c ON e.classe_id = c.id
                                JOIN options o ON e.option_id = o.id
                                JOIN sections s ON e.section_id = s.id
                                ORDER BY e.nom, e.prenom");
        while ($row = $result->fetch_assoc()) {
            $eleves[] = $row;
        }
        return $eleves;
    }
    
    public function getAllFrais() {
        $frais = [];
        $result = $this->db->query("SELECT * FROM frais ORDER BY description");
        while ($row = $result->fetch_assoc()) {
            $frais[] = $row;
        }
        return $frais;
    }
    
    public function updatePaiement($id, $eleve_id, $frais_id, $amount_paid, $payment_date, $mois, $notes) {
        $stmt = $this->db->prepare("UPDATE paiements_frais SET 
                                eleve_id = ?, 
                                frais_id = ?, 
                                amount_paid = ?, 
                                payment_date = ?, 
                                mois = ?, 
                                notes = ?,
                                updated_at = NOW()
                                WHERE id = ?");
        $stmt->bind_param("iidsssi", $eleve_id, $frais_id, $amount_paid, $payment_date, $mois, $notes, $id);
        $result = $stmt->execute();
        
        // Enregistrer l'action dans les logs
        if ($result) {
            $this->logAction("Modification du paiement ID: $id");
        }
        
        return $result;
    }
    
    private function logAction($action_type) {
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Système';
        
        $stmt = $this->db->prepare("INSERT INTO system_logs (user_id, username, action_type, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $username, $action_type);
        $stmt->execute();
    }
    
    public function __destruct() {
        $this->db->close();
    }
    
    // Add this method to your PaiementModel class
    
    public function getAll() {
        $sql = "
            SELECT p.id, 
                   e.nom AS eleve_nom, 
                   p.classe,
                   o.nom AS option_nom, 
                   p.section, 
                   f.description AS frais_description, 
                   p.amount_paid, 
                   p.payment_date, 
                   m.nom AS mois
            FROM paiements_frais p
            LEFT JOIN eleves e ON p.eleve_id = e.id
            LEFT JOIN options o ON p.option_id = o.id
            LEFT JOIN frais f ON p.frais_id = f.id
            LEFT JOIN mois m ON p.moi_id = m.id
            ORDER BY p.payment_date DESC";
            
        $result = $this->db->query($sql);
    
        if (!$result) {
            throw new Exception("Erreur lors de la récupération des paiements : " . $this->db->error);
        }
    
        return $result->fetch_all(MYSQLI_ASSOC); // Retourne tous les paiements
    }
    
    public function getByPaiementId($paiement_id) {
        // Assurez-vous que $paiement_id est un entier pour éviter les injections SQL
        $paiement_id = (int)$paiement_id;
        
        // Utilisez une requête préparée pour récupérer uniquement le paiement avec l'ID spécifié
        $query = "SELECT p.id, CONCAT(e.nom, ' ', e.post_nom, ' ', e.prenom) as eleve_nom, 
                  p.classe, o.nom as option_nom, p.section, f.description as frais_description, 
                  p.amount_paid, p.payment_date, m.nom as mois 
                  FROM paiements_frais p
                  LEFT JOIN eleves e ON p.eleve_id = e.id
                  LEFT JOIN options o ON p.option_id = o.id
                  LEFT JOIN frais f ON p.frais_id = f.id
                  LEFT JOIN mois m ON p.moi_id = m.id
                  WHERE p.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $paiement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Vérifier si un résultat a été trouvé
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    public function add($eleve_id, $frais_id, $amount_paid, $payment_date, $created_at, $moi_id, $classe, $option_id, $section) {
        // Préparer la requête SQL
        $query = "INSERT INTO paiements_frais (eleve_id, frais_id, amount_paid, payment_date, created_at, moi_id, classe, option_id, section) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Préparer la déclaration
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            die("Erreur de préparation: " . $this->db->error);
        }
        
        // Lier les paramètres
        $stmt->bind_param("iisddisis", $eleve_id, $frais_id, $amount_paid, $payment_date, $created_at, $moi_id, $classe, $option_id, $section);
        
        // Exécuter la requête
        $result = $stmt->execute();
        
        if (!$result) {
            die("Erreur d'exécution: " . $stmt->error);
        }
        
        // Récupérer l'ID du paiement inséré
        $paiement_id = $this->db->insert_id;
        
        // Fermer la déclaration
        $stmt->close();
        
        return $paiement_id;
    }

    public function getLastInsertedId() {
        return $this->db->insert_id;
    }
}
?>
