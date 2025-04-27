
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
        $stmt = $this->db->prepare("SELECT p.*, e.nom as eleve_nom, e.prenom as eleve_prenom, e.classe_id, c.nom as classe_nom, o.nom as option_nom, e.section, f.description as frais_description 
                                FROM paiements_frais p
                                JOIN eleves e ON p.eleve_id = e.id
                                LEFT JOIN classes c ON e.classe_id = c.id
                                LEFT JOIN options o ON e.option_id = o.id
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
        $result = $this->db->query("SELECT e.id, e.nom, e.prenom, c.nom as classe, o.nom as option_nom, e.section 
                                FROM eleves e
                                LEFT JOIN classes c ON e.classe_id = c.id
                                LEFT JOIN options o ON e.option_id = o.id
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
    
   // Dans PaiementModel.php
public function getAll() {
    // The error is in this query - it's trying to use pf.mois_id which doesn't exist
    $query = "SELECT pf.*, e.nom as eleve_nom, e.post_nom as eleve_post_nom, e.prenom as eleve_prenom, 
              c.nom as classe_nom, o.nom as option_nom, f.description as frais_description, 
              m.nom as mois 
              FROM paiements_frais pf
              LEFT JOIN eleves e ON pf.eleve_id = e.id
              LEFT JOIN classes c ON pf.classe_id = c.id
              LEFT JOIN options o ON pf.option_id = o.id
              LEFT JOIN frais f ON pf.frais_id = f.id
              LEFT JOIN mois m ON pf.moi_id = m.id
              ORDER BY pf.id DESC";
    
    // Replace pf.mois_id with pf.moi_id in the JOIN condition
    
    $result = $this->db->query($query);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // For debugging, you can log the error
        error_log("SQL Error in PaiementModel->getAll(): " . $this->db->error);
        return [];
    }
}
    
    public function getByPaiementId($paiement_id) {
        // Assurez-vous que $paiement_id est un entier pour éviter les injections SQL
        $paiement_id = (int)$paiement_id;
        
        // Utilisez une requête préparée pour récupérer uniquement le paiement avec l'ID spécifié
        $query = "SELECT p.id, CONCAT(e.nom, ' ', e.post_nom, ' ', e.prenom) as eleve_nom, 
                  c.nom as classe_nom, o.nom as option_nom, p.section, f.description as frais_description, 
                  p.amount_paid, p.payment_date, m.nom as mois 
                  FROM paiements_frais p
                  LEFT JOIN eleves e ON p.eleve_id = e.id
                  LEFT JOIN classes c ON p.classe_id = c.id
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

    public function add($eleve_id, $frais_id, $amount_paid, $payment_date, $created_at, $moi_id, $classe_id, $option_id, $section) {
        $query = "INSERT INTO paiements_frais (eleve_id, frais_id, amount_paid, payment_date, created_at, moi_id, classe_id, option_id, section, statut) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'payé')";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iidssiiss", $eleve_id, $frais_id, $amount_paid, $payment_date, $created_at, $moi_id, $classe_id, $option_id, $section);
        
        return $stmt->execute();
    }

    /**
     * Supprime un paiement par son ID
     * 
     * @param int $id L'identifiant du paiement à supprimer
     * @return bool True si la suppression a réussi, false sinon
     */
    public function delete($id) {
        // Vérifier si le paiement existe avant de le supprimer
        $check = $this->getPaiementById($id);
        if (!$check) {
            return false;
        }
        
        // Préparer la requête de suppression
        $stmt = $this->db->prepare("DELETE FROM paiements_frais WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        
        // Enregistrer l'action dans les logs
        if ($result) {
            $this->logAction("Suppression du paiement ID: $id");
        }
        
        $stmt->close();
        return $result;
    }

    public function getLastInsertedId() {
        return $this->db->insert_id;
    }
    
    /**
     * Met à jour un paiement existant
     * 
     * @param int $id ID du paiement à mettre à jour
     * @param int $eleve_id ID de l'élève
     * @param int $frais_id ID du frais
     * @param string $amount_paid Montant payé
     * @param string $payment_date Date du paiement
     * @param int $moi_id ID du mois
     * @param string $classe Classe de l'élève
     * @param int $option_id ID de l'option
     * @param string $section Section de l'élève
     * @return bool True si la mise à jour a réussi, false sinon
     */
    public function update($id, $eleve_id, $frais_id, $amount_paid, $payment_date, $moi_id, $classe_id, $option_id, $section) {
        // Vérifier si le paiement existe
        $check = $this->getPaiementById($id);
        if (!$check) {
            return false;
        }
        
        // Préparer la requête de mise à jour
        $query = "UPDATE paiements_frais SET 
                  eleve_id = ?, 
                  frais_id = ?, 
                  amount_paid = ?, 
                  payment_date = ?, 
                  moi_id = ?, 
                  classe_id = ?, 
                  option_id = ?, 
                  section = ?,
                  updated_at = NOW()
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            die("Erreur de préparation: " . $this->db->error);
        }
        
        // Lier les paramètres
        $stmt->bind_param("iisdiiisi", $eleve_id, $frais_id, $amount_paid, $payment_date, $moi_id, $classe_id, $option_id, $section, $id);
        
        // Exécuter la requête
        $result = $stmt->execute();
        
        if (!$result) {
            die("Erreur d'exécution: " . $stmt->error);
        }
        
        // Enregistrer l'action dans les logs
        if ($result) {
            $this->logAction("Mise à jour du paiement ID: $id");
        }
        
        $stmt->close();
        return $result;
    }
}
?>
