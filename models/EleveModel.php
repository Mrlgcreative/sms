
<?php
class EleveModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) ;
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAll() {
        $query = "
            SELECT 
                e.id, 
                e.nom, 
                e.post_nom, 
                e.prenom, 
                e.date_naissance, 
                e.lieu_naissance, 
                e.adresse, 
                e.section, 
                e.matricule,
                e.photo,
                e.professions,
                e.nom_pere, 
                e.nom_mere, 
                e.contact_pere, 
                e.contact_mere,
                c.niveau as classe_nom,
                o.nom AS option_nom,
                s.libelle AS annee_scolaire
            FROM eleves e
            LEFT JOIN classes c ON e.classe_id = c.id
            LEFT JOIN options o ON e.option_id = o.id
            LEFT JOIN sessions_scolaires s ON e.session_scolaire_id = s.id";
            
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getEleveDetailsById($eleve_id) {
        $stmt = $this->db->prepare("
            SELECT 
                eleves.nom AS eleve_nom,
                classes.niveau AS classe_nom,
                options.nom AS option_nom,
                eleves.section AS section_nom
            FROM 
                eleves
            LEFT JOIN classes ON eleves.classe_id = classes.id
            LEFT JOIN options ON eleves.option_id = options.id
            WHERE 
                eleves.id = ?
        ");
        $stmt->bind_param("i", $eleve_id); // Lier l'ID
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Retourne les détails sous forme associative
    }
    
    
    
    public function getById($id) {
        $query = "SELECT e.*, c.niveau as classe_nom, o.nom as option_nom 
                  FROM eleves e 
                  LEFT JOIN classes c ON e.classe_id = c.id
                  LEFT JOIN options o ON e.option_id = o.id 
                  WHERE e.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    public function add($nom, $post_nom, $prenom, $date_naissance, $lieu_naissance, $adresse, $classe_id, $section, $option_id, $sexe = 'M,F', $annee_scolaire = null, $date_inscription = null, $statut = 'actif', $matricule = '', $photo = 'dist/img/default-student.png',$professions='', $nom_pere = '', $nom_mere = '', $contact_pere = '', $contact_mere = '', $inscription_id = null) {
        // Si annee_scolaire est null, utilisez l'année scolaire actuelle
        if ($annee_scolaire === null) {
            $annee_scolaire = date('Y') . '-' . (date('Y') + 1);
        }
        
        // Si date_inscription est null, utilisez la date d'aujourd'hui
        if ($date_inscription === null) {
            $date_inscription = date('Y-m-d');
        }
        
        // Récupérer l'ID de la session scolaire
        $query = "SELECT id FROM sessions_scolaires WHERE libelle = ? OR CONCAT(annee_debut, '-', annee_fin) = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $annee_scolaire, $annee_scolaire);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $session_scolaire_id = $row['id'];
        } else {
            // Si la session scolaire n'existe pas, utilisez la première disponible
            $result = $this->db->query("SELECT id FROM sessions_scolaires ORDER BY id DESC LIMIT 1");
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $session_scolaire_id = $row['id'];
            } else {
                $session_scolaire_id = 1; // Valeur par défaut
            }
        }
        
        // Vérifier si l'inscription_id existe dans la table inscriptions
        if ($inscription_id !== null) {
            $check_query = "SELECT id FROM inscriptions WHERE id = ?";
            $check_stmt = $this->db->prepare($check_query);
            $check_stmt->bind_param("i", $inscription_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            // Si l'inscription_id n'existe pas, le mettre à NULL
            if ($check_result->num_rows == 0) {
                $inscription_id = null;
            }
            $check_stmt->close();
        }
        
        // Commencer une transaction
        $this->db->begin_transaction();
        
        try {
            // Insérer l'élève avec toutes les informations
            $stmt = $this->db->prepare("INSERT INTO eleves (nom, post_nom, prenom, date_naissance, sexe, lieu_naissance, adresse, section, classe_id, option_id, professions, nom_pere, nom_mere, contact_pere, contact_mere, session_scolaire_id, statut, matricule, photo, inscription_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Correction de la chaîne de types pour correspondre exactement aux paramètres
            $stmt->bind_param("sssssssssisssssisssi", 
                $nom, 
                $post_nom, 
                $prenom, 
                $date_naissance, 
                $sexe, 
                $lieu_naissance, 
                $adresse, 
                $section, 
                $classe_id, 
                $option_id, 
                $professions, 
                $nom_pere, 
                $nom_mere, 
                $contact_pere, 
                $contact_mere, 
                $session_scolaire_id, 
                $statut, 
                $matricule, 
                $photo, 
                $inscription_id
            );
            
            $stmt->execute();
            $eleve_id = $this->db->insert_id;
            
            // Insérer dans la table inscriptions
            $inscription_stmt = $this->db->prepare("INSERT INTO inscriptions (eleve_id, classe_id, option_id, session_scolaire_id, date_inscription, type, statut) VALUES (?, ?, ?, ?, ?, 'inscription', 'actif')");
            $inscription_stmt->bind_param("iiiss", 
                $eleve_id, 
                $classe_id, 
                $option_id, 
                $session_scolaire_id, 
                $date_inscription
            );
            $inscription_stmt->execute();
            
            // Mettre à jour l'élève avec l'ID de l'inscription
            $inscription_id = $this->db->insert_id;
            $update_stmt = $this->db->prepare("UPDATE eleves SET inscription_id = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $inscription_id, $eleve_id);
            $update_stmt->execute();
            
            // Valider la transaction
            $this->db->commit();
            
            // Retourner l'ID de l'élève inséré
            return $eleve_id;
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $this->db->rollback();
            throw $e;
        }
    }

    public function update($id, $nom, $post_nom, $prenom, $date_naissance, $sexe, $lieu_naissance, $adresse, $section, $classe_id, $option_id,$professions, $nom_pere, $nom_mere, $contact_pere, $contact_mere, $session_scolaire_id = null, $matricule = null, $photo = null, $inscription_id = null) {
        // Construire la requête en fonction des paramètres fournis
        $query = "UPDATE eleves SET nom = ?, post_nom = ?, prenom = ?, date_naissance = ?, sexe = ?, lieu_naissance = ?, adresse = ?, section = ?, classe_id = ?, option_id = ?,professions = ?, nom_pere = ?, nom_mere = ?, contact_pere = ?, contact_mere = ?";
        $params = [$nom, $post_nom, $prenom, $date_naissance, $sexe, $lieu_naissance, $adresse, $section, $classe_id, $option_id,$professions, $nom_pere, $nom_mere, $contact_pere, $contact_mere];
        $types = "ssssssssiisssss";
        
        if ($session_scolaire_id !== null) {
            $query .= ", session_scolaire_id = ?";
            $params[] = $session_scolaire_id;
            $types .= "i";
        }
        
        if ($matricule !== null) {
            $query .= ", matricule = ?";
            $params[] = $matricule;
            $types .= "s";
        }
        
        if ($photo !== null) {
            $query .= ", photo = ?";
            $params[] = $photo;
            $types .= "s";
        }
        
        if ($inscription_id !== null) {
            $query .= ", inscription_id = ?";
            $params[] = $inscription_id;
            $types .= "i";
        }
        
        $query .= " WHERE id = ?";
        $params[] = $id;
        $types .= "i";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->affected_rows > 0;
    }

    public function delete($id) {
        // Commencer une transaction
        $this->db->begin_transaction();
        
        try {
            // Supprimer directement l'élève sans archivage
            $stmt = $this->db->prepare("DELETE FROM eleves WHERE id = ?");
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            
            // Valider la transaction
            $this->db->commit();
            
            return $result;
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $this->db->rollback();
            throw $e;
        }
    }
}
?>

