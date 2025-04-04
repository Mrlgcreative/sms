<?php
class EvenementScolaire {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getAllEvenements() {
        $query = "SELECT * FROM evenements_scolaires ORDER BY date_debut DESC";
        $result = $this->conn->query($query);
        
        $evenements = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $evenements[] = $row;
            }
        }
        
        return $evenements;
    }
    
    public function getEvenementsAVenir($limit = 5) {
        $today = date('Y-m-d');
        $query = "SELECT * FROM evenements_scolaires WHERE date_debut >= ? ORDER BY date_debut ASC LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $today, $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $evenements = [];
        
        while ($row = $result->fetch_assoc()) {
            $evenements[] = $row;
        }
        
        return $evenements;
    }
    
    public function getEvenementsForCalendar() {
        $query = "SELECT id, titre as title, description, date_debut as start, date_fin as end, 
                  lieu as location, couleur as color FROM evenements_scolaires";
        $result = $this->conn->query($query);
        
        $evenements = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $evenements[] = $row;
            }
        }
        
        return $evenements;
    }
    
    public function ajouterEvenement($titre, $description, $date_debut, $date_fin, $lieu, $couleur) {
        $query = "INSERT INTO evenements_scolaires (titre, description, date_debut, date_fin, lieu, couleur) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssss", $titre, $description, $date_debut, $date_fin, $lieu, $couleur);
        
        return $stmt->execute();
    }
    
    public function getEvenementById($id) {
        $query = "SELECT * FROM evenements_scolaires WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function modifierEvenement($id, $titre, $description, $date_debut, $date_fin, $lieu, $couleur) {
        $query = "UPDATE evenements_scolaires 
                  SET titre = ?, description = ?, date_debut = ?, date_fin = ?, 
                      lieu = ?, couleur = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssi", $titre, $description, $date_debut, $date_fin, $lieu, $couleur, $id);
        
        return $stmt->execute();
    }
    
    public function supprimerEvenement($id) {
        $query = "DELETE FROM evenements_scolaires WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}
?>