<?php
class EvenementScolaire {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getAllEvenements() {
        try {
            $query = "SELECT * FROM evenements_scolaires ORDER BY date_debut DESC";
            $result = $this->conn->query($query);
            
            $evenements = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $evenements[] = $row;
                }
            }
            
            return $evenements;
        } catch (Exception $e) {
            error_log("Erreur getAllEvenements: " . $e->getMessage());
            return [];
        }
    }
    
    public function getEvenementsAVenir($limit = 5) {
        try {
            $today = date('Y-m-d H:i:s');
            $query = "SELECT * FROM evenements_scolaires WHERE date_debut >= ? ORDER BY date_debut ASC LIMIT ?";
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("si", $today, $limit);
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $evenements = [];
            
            while ($row = $result->fetch_assoc()) {
                $evenements[] = $row;
            }
            
            $stmt->close();
            return $evenements;
        } catch (Exception $e) {
            error_log("Erreur getEvenementsAVenir: " . $e->getMessage());
            return [];
        }
    }
    
    public function getEvenementsForCalendar() {
        try {
            $query = "SELECT id, titre as title, description, date_debut as start, date_fin as end, 
                      lieu as location, couleur as backgroundColor, couleur as borderColor, responsable 
                      FROM evenements_scolaires ORDER BY date_debut ASC";
            $result = $this->conn->query($query);
            
            $evenements = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // S'assurer que les couleurs sont définies
                    if (empty($row['backgroundColor'])) {
                        $row['backgroundColor'] = '#3c8dbc';
                        $row['borderColor'] = '#3c8dbc';
                    }
                    
                    $evenements[] = [
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'start' => $row['start'],
                        'end' => $row['end'],
                        'backgroundColor' => $row['backgroundColor'],
                        'borderColor' => $row['borderColor'],
                        'allDay' => false,
                        'description' => $row['description'],
                        'location' => $row['location'],
                        'responsible' => $row['responsable']
                    ];
                }
            }
            
            return $evenements;
        } catch (Exception $e) {
            error_log("Erreur getEvenementsForCalendar: " . $e->getMessage());
            return [];
        }
    }
    
    public function ajouterEvenement($titre, $description, $date_debut, $date_fin, $lieu, $responsable, $couleur = '#3c8dbc') {
        try {
            $query = "INSERT INTO evenements_scolaires (titre, description, date_debut, date_fin, lieu, responsable, couleur, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("sssssss", $titre, $description, $date_debut, $date_fin, $lieu, $responsable, $couleur);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
            $insert_id = $this->conn->insert_id;
            $stmt->close();
            
            return ['success' => true, 'id' => $insert_id];
            
        } catch (Exception $e) {
            error_log("Erreur ajouterEvenement: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getEvenementById($id) {
        try {
            $query = "SELECT * FROM evenements_scolaires WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $evenement = $result->fetch_assoc();
            $stmt->close();
            
            return $evenement;
        } catch (Exception $e) {
            error_log("Erreur getEvenementById: " . $e->getMessage());
            return null;
        }
    }
    
    public function modifierEvenement($id, $titre, $description, $date_debut, $date_fin, $lieu, $responsable, $couleur = '#3c8dbc') {
        try {
            $query = "UPDATE evenements_scolaires 
                      SET titre = ?, description = ?, date_debut = ?, date_fin = ?, 
                          lieu = ?, responsable = ?, couleur = ?, updated_at = NOW() 
                      WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("sssssssi", $titre, $description, $date_debut, $date_fin, $lieu, $responsable, $couleur, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $affected_rows > 0;
            
        } catch (Exception $e) {
            error_log("Erreur modifierEvenement: " . $e->getMessage());
            return false;
        }
    }
    
    public function supprimerEvenement($id) {
        try {
            $query = "DELETE FROM evenements_scolaires WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $affected_rows > 0;
            
        } catch (Exception $e) {
            error_log("Erreur supprimerEvenement: " . $e->getMessage());
            return false;
        }
    }
    
    public function evenementExists($id) {
        try {
            $query = "SELECT COUNT(*) as count FROM evenements_scolaires WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return $row['count'] > 0;
        } catch (Exception $e) {
            error_log("Erreur evenementExists: " . $e->getMessage());
            return false;
        }
    }
    
    public function getStatistiques() {
        $stats = [
            'total' => 0,
            'a_venir' => 0,
            'en_cours' => 0,
            'passes' => 0
        ];
        
        try {
            // Total
            $query = "SELECT COUNT(*) as count FROM evenements_scolaires";
            $result = $this->conn->query($query);
            if ($result) {
                $stats['total'] = $result->fetch_assoc()['count'];
            }
            
            // À venir
            $query = "SELECT COUNT(*) as count FROM evenements_scolaires WHERE date_debut > NOW()";
            $result = $this->conn->query($query);
            if ($result) {
                $stats['a_venir'] = $result->fetch_assoc()['count'];
            }
            
            // En cours
            $query = "SELECT COUNT(*) as count FROM evenements_scolaires WHERE date_debut <= NOW() AND date_fin >= NOW()";
            $result = $this->conn->query($query);
            if ($result) {
                $stats['en_cours'] = $result->fetch_assoc()['count'];
            }
            
            // Passés
            $query = "SELECT COUNT(*) as count FROM evenements_scolaires WHERE date_fin < NOW()";
            $result = $this->conn->query($query);
            if ($result) {
                $stats['passes'] = $result->fetch_assoc()['count'];
            }
            
        } catch (Exception $e) {
            error_log("Erreur getStatistiques: " . $e->getMessage());
        }
        
        return $stats;
    }
}
?>