
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
                e.*, 
                e.*, 
                e.nom_pere, 
                e.nom_mere, 
                e.contact_pere, 
                e.contact_mere,
                e.classe AS classes_nom,
                o.nom AS option_nom,
                s.libelle AS annee_scolaire
            FROM eleves e
            LEFT JOIN options o ON e.option_id = o.id
            LEFT JOIN sessions_scolaires s ON e.session_scolaire_id = s.id";
            
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getEleveDetailsById($eleve_id) {
        $stmt = $this->db->prepare("
            SELECT 
                eleves.nom AS eleve_nom,
                eleves.classe AS classe_nom,
                options.nom AS option_nom,
                eleves.section AS section_nom
            FROM 
                eleves
            
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
        $query = "SELECT e.*, e.classe, o.nom as option_nom 
                  FROM eleves e 
                  
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

    public function add($nom, $post_nom, $prenom, $date_naissance, $lieu_naissance, $adresse, $classe, $section, $option_id, $sexe = 'M',  $annee_scolaire = null, $date_inscription = null, $statut = 'actif') {
        // Si annee_scolaire est null, utilisez l'année scolaire actuelle
        if ($annee_scolaire === null) {
            $annee_scolaire = date('Y') . '-' . (date('Y') + 1);
        }
        
        // Si date_inscription est null, utilisez la date d'aujourd'hui
        if ($date_inscription === null) {
            $date_inscription = date('Y-m-d');
        }
        
        // Récupérer les informations des parents depuis le formulaire
        $nom_pere = isset($_POST['nom_pere']) ? $_POST['nom_pere'] : '';
        $nom_mere = isset($_POST['nom_mere']) ? $_POST['nom_mere'] : '';
        $contact_pere = isset($_POST['contact_pere']) ? $_POST['contact_pere'] : '';
        $contact_mere = isset($_POST['contact_mere']) ? $_POST['contact_mere'] : '';
        
        // Insérer l'élève avec toutes les informations
        $stmt = $this->db->prepare("INSERT INTO eleves (nom, post_nom, prenom, date_naissance, sexe, lieu_naissance, adresse, section, classe, option_id, nom_pere, nom_mere, contact_pere, contact_mere,  session_scolaire_id,  statut) VALUES ( ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssssis", $nom, $post_nom, $prenom, $date_naissance, $sexe, $lieu_naissance, $adresse, $section, $classe, $option_id, $nom_pere, $nom_mere, $contact_pere, $contact_mere,  $session_scolaire_id,  $statut);
        $stmt->execute();
        
        // Retourner l'ID de l'élève inséré
        return $this->db->insert_id;
    }

    public function update($id, $nom, $post_nom, $prenom, $date_naissance, $sexe, $lieu_naissance, $adresse, $section, $classe, $option_id, $nom_pere, $nom_mere, $contact_pere, $contact_mere, $session_scolaire_id = null) {
        // Si session_scolaire_id est null, conserver la valeur existante
        if ($session_scolaire_id === null) {
            $stmt = $this->db->prepare("UPDATE eleves SET nom = ?, post_nom = ?, prenom = ?, date_naissance = ?, sexe = ?, lieu_naissance = ?, adresse = ?, section = ?, classe = ?, option_id = ?, nom_pere = ?, nom_mere = ?, contact_pere = ?, contact_mere = ? WHERE id = ?");
            $stmt->bind_param("ssssssssssssssi", $nom, $post_nom, $prenom, $date_naissance, $sexe, $lieu_naissance, $adresse, $section, $classe, $option_id, $nom_pere, $nom_mere, $contact_pere, $contact_mere, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE eleves SET nom = ?, post_nom = ?, prenom = ?, date_naissance = ?, sexe = ?, lieu_naissance = ?, adresse = ?, section = ?, classe = ?, option_id = ?, nom_pere = ?, nom_mere = ?, contact_pere = ?, contact_mere = ?, session_scolaire_id = ? WHERE id = ?");
            $stmt->bind_param("ssssssssssssssii", $nom, $post_nom, $prenom, $date_naissance, $sexe, $lieu_naissance, $adresse, $section, $classe, $option_id, $nom_pere, $nom_mere, $contact_pere, $contact_mere, $session_scolaire_id, $id);
        }
        $stmt->execute();
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

