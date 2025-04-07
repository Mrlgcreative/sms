<?php
class Stock {
    private $db;
    
    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }
    
    public function getAllItems() {
        $query = "SELECT * FROM stock_items ORDER BY categorie, nom";
        $result = $this->db->query($query);
        
        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    public function getItemsEnAlerte() {
        $query = "SELECT * FROM stock_items WHERE quantite <= seuil_alerte ORDER BY quantite ASC";
        $result = $this->db->query($query);
        
        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    public function getItemsByCategorie($categorie) {
        $query = "SELECT * FROM stock_items WHERE categorie = ? ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $categorie);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $items = [];
        
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    
    
    public function getItemById($id) {
        $query = "SELECT * FROM stock_items WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function modifierItem($id, $nom, $categorie, $quantite, $seuil_alerte, $emplacement, $description = '') {
        $query = "UPDATE stock_items 
                  SET nom = ?, categorie = ?, quantite = ?, 
                      seuil_alerte = ?, emplacement = ?, description = ? 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssiissi", $nom, $categorie, $quantite, $seuil_alerte, $emplacement, $description, $id);
        
        return $stmt->execute();
    }
    
    public function supprimerItem($id) {
        $query = "DELETE FROM stock_items WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    public function ajouterMouvement($item_id, $type, $quantite, $date, $commentaire, $utilisateur) {
        // Récupérer l'item actuel
        $item = $this->getItemById($item_id);
        if (!$item) {
            return false;
        }
        
        // Calculer la nouvelle quantité
        $nouvelle_quantite = $item['quantite'];
        if ($type == 'entree') {
            $nouvelle_quantite += $quantite;
        } else if ($type == 'sortie') {
            $nouvelle_quantite -= $quantite;
            if ($nouvelle_quantite < 0) {
                return false; // Impossible d'avoir un stock négatif
            }
        }
        
        // Commencer une transaction
        $this->db->begin_transaction();
        
        try {
            // Mettre à jour le stock
            $query1 = "UPDATE stock_items SET quantite = ? WHERE id = ?";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bind_param("ii", $nouvelle_quantite, $item_id);
            $stmt1->execute();
            
            // Enregistrer le mouvement
            $query2 = "INSERT INTO stock_mouvements (item_id, type, quantite, date, commentaire, utilisateur) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param("isisss", $item_id, $type, $quantite, $date, $commentaire, $utilisateur);
            $stmt2->execute();
            
            // Valider la transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->db->rollback();
            return false;
        }
    }
    
    public function getMouvements($limit = 50) {
        $query = "SELECT m.*, i.nom as item_nom, i.categorie 
                  FROM stock_mouvements m 
                  JOIN stock_items i ON m.item_id = i.id 
                  ORDER BY m.date DESC 
                  LIMIT ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $mouvements = [];
        
        while ($row = $result->fetch_assoc()) {
            $mouvements[] = $row;
        }
        
        return $mouvements;
    }
    
    public function ajouterArticle($nom, $description, $categorie, $quantite, $prix_unitaire, $date_ajout) {
        // Prepare the SQL statement
        $stmt = $this->db->prepare("INSERT INTO stock_items (nom, description, categorie, quantite, date_ajout) 
                                   VALUES (?, ?, ?, ?, ?)");
        
        // Bind parameters - fixed to match the 5 parameters in the SQL statement
        $stmt->bind_param("sssss", $nom, $description, $categorie, $quantite, $date_ajout);
        
        // Execute the statement
        $result = $stmt->execute();
        
        // Close the statement
        $stmt->close();
        
        return $result;
    }

   
    
    public function supprimerArticle($id) {
        // Prepare the SQL statement
        $stmt = $this->db->prepare("DELETE FROM stock_items WHERE id = ?");
        
        // Bind parameter
        $stmt->bind_param("i", $id);
        
        // Execute the statement
        $result = $stmt->execute();
        
        // Close the statement
        $stmt->close();
        
        return $result;
    }
}
?>
