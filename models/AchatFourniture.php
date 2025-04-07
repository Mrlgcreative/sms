<?php
class AchatFourniture {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getAllAchats() {
        $query = "SELECT * FROM achats_fournitures ORDER BY date_achat DESC";
        $result = $this->conn->query($query);
        
        $achats = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $achats[] = $row;
            }
        }
        
        return $achats;
    }
    
    public function getTotalDepenses() {
        $query = "SELECT SUM(montant) as total FROM achats_fournitures";
        $result = $this->conn->query($query);
        
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total'] ?: 0;
        }
        
        return 0;
    }
    
    // Autres méthodes pour ajouter, modifier, supprimer des achats
    public function ajouterAchat($date, $fournisseur, $description, $quantite, $montant, $facture_ref) {
        $query = "INSERT INTO achats_fournitures (date_achat, fournisseur, description, quantite, montant, facture_ref) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssids", $date, $fournisseur, $description, $quantite, $montant, $facture_ref);
        
        return $stmt->execute();
    }
    
    public function getAchatById($id) {
        $query = "SELECT * FROM achats_fournitures WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function modifierAchat($id, $date, $fournisseur, $description, $quantite, $montant, $facture_ref) {
        $query = "UPDATE achats_fournitures 
                  SET date_achat = ?, fournisseur = ?, description = ?, 
                      quantite = ?, montant = ?, facture_ref = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssidsi", $date, $fournisseur, $description, $quantite, $montant, $facture_ref, $id);
        
        return $stmt->execute();
    }
    
    public function supprimerAchat($id) {
        $query = "DELETE FROM achats_fournitures WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}
?>