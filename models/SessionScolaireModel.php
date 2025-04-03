<?php
class SessionScolaireModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAll() {
        $query = "SELECT * FROM sessions_scolaires ORDER BY annee_debut DESC";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getActive() {
        $query = "SELECT * FROM sessions_scolaires WHERE est_active = 1 LIMIT 1";
        $result = $this->db->query($query);
        return $result->fetch_assoc();
    }

    public function add($annee_debut, $annee_fin, $libelle, $est_active = 0) {
        // Si on active cette session, désactiver toutes les autres
        if ($est_active) {
            $this->desactiverTout();
        }
        
        $stmt = $this->db->prepare("INSERT INTO sessions_scolaires (annee_debut, annee_fin, libelle, est_active) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $annee_debut, $annee_fin, $libelle, $est_active);
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function update($id, $annee_debut, $annee_fin, $libelle, $est_active) {
        // Si on active cette session, désactiver toutes les autres
        if ($est_active) {
            $this->desactiverTout();
        }
        
        $stmt = $this->db->prepare("UPDATE sessions_scolaires SET annee_debut = ?, annee_fin = ?, libelle = ?, est_active = ? WHERE id = ?");
        $stmt->bind_param("iisii", $annee_debut, $annee_fin, $libelle, $est_active, $id);
        $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM sessions_scolaires WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    private function desactiverTout() {
        $this->db->query("UPDATE sessions_scolaires SET est_active = 0");
    }
    
    // Nouvelle méthode pour initialiser une nouvelle année scolaire
    public function initialiserNouvelleAnnee($annee_debut, $annee_fin, $libelle) {
        // Commencer une transaction
        $this->db->begin_transaction();
        
        try {
            // 1. Archiver les données de l'année précédente
            $this->archiverAnneePrecedente();
            
            // 2. Créer la nouvelle année scolaire et l'activer
            $nouvelle_annee_id = $this->add($annee_debut, $annee_fin, $libelle, 1);
            
            // 3. Réinitialiser certaines tables pour la nouvelle année
            $this->reinitialiserTables();
            
            // Si tout s'est bien passé, valider la transaction
            $this->db->commit();
            return $nouvelle_annee_id;
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $this->db->rollback();
            throw $e;
        }
    }
    
    // Archiver les données de l'année précédente
    private function archiverAnneePrecedente() {
        // Récupérer l'année active actuelle
        $annee_active = $this->getActive();
        
        if (!$annee_active) {
            return; // Pas d'année active à archiver
        }
        
        // Créer une table d'archive pour les élèves si elle n'existe pas
        $this->db->query("
            CREATE TABLE IF NOT EXISTS eleves_archives (
                id INT AUTO_INCREMENT PRIMARY KEY,
                eleve_id INT,
                nom VARCHAR(100),
                post_nom VARCHAR(100),
                prenom VARCHAR(100),
                date_naissance DATE,
                sexe VARCHAR(10),
                lieu_naissance VARCHAR(100),
                adresse TEXT,
                section VARCHAR(50),
                classe VARCHAR(50),
                option_id INT,
                nom_pere VARCHAR(100),
                nom_mere VARCHAR(100),
                contact_pere VARCHAR(50),
                contact_mere VARCHAR(50),
                annee_scolaire VARCHAR(20),
                date_inscription DATE,
                statut VARCHAR(20),
                date_archivage TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Archiver les élèves de l'année active
        $this->db->query("
            INSERT INTO eleves_archives (
                eleve_id, nom, post_nom, prenom, date_naissance, sexe, lieu_naissance, 
                adresse, section, classe, option_id, nom_pere, nom_mere, 
                contact_pere, contact_mere, annee_scolaire, date_inscription, statut
            )
            SELECT 
                id, nom, post_nom, prenom, date_naissance, sexe, lieu_naissance, 
                adresse, section, classe, option_id, nom_pere, nom_mere, 
                contact_pere, contact_mere, annee_scolaire, date_inscription, statut
            FROM eleves
            WHERE annee_scolaire = '{$annee_active['annee_debut']}-{$annee_active['annee_fin']}'
        ");
        
        // Vous pouvez ajouter d'autres tables à archiver ici
        // Par exemple: paiements, présences, notes, etc.
    }
    
    // Réinitialiser certaines tables pour la nouvelle année
    private function reinitialiserTables() {
        // Exemple: réinitialiser la table des paiements
        // $this->db->query("TRUNCATE TABLE paiements");
        
        // Vous pouvez réinitialiser d'autres tables selon vos besoins
        // Attention à ne pas supprimer les données importantes comme les élèves
        
        // Mettre à jour le statut des élèves pour la nouvelle année
        // Par exemple, passer les élèves en classe supérieure
        $this->promouvoirEleves();
    }
    
    // Promouvoir les élèves à la classe supérieure
    private function promouvoirEleves() {
        // Cette fonction dépend de votre logique métier
        // Voici un exemple simplifié:
        
        // 1. Maternelle
        $this->db->query("
            UPDATE eleves 
            SET classe = CASE
                WHEN classe = '1er' THEN '2eme'
                WHEN classe = '2eme' THEN '3eme'
                WHEN classe = '3eme' THEN '1er' -- Passage en primaire
                ELSE classe
            END,
            section = CASE
                WHEN classe = '3eme' THEN 'primaire'
                ELSE section
            END
            WHERE section = 'maternelle'
        ");
        
        // 2. Primaire
        $this->db->query("
            UPDATE eleves 
            SET classe = CASE
                WHEN classe = '1er' THEN '2eme'
                WHEN classe = '2eme' THEN '3eme'
                WHEN classe = '3eme' THEN '4eme'
                WHEN classe = '4eme' THEN '5eme'
                WHEN classe = '5eme' THEN '6eme'
                WHEN classe = '6eme' THEN '7eme' -- Passage en secondaire
                ELSE classe
            END,
            section = CASE
                WHEN classe = '6eme' THEN 'secondaire'
                ELSE section
            END
            WHERE section = 'primaire'
        ");
        
        // 3. Secondaire
        $this->db->query("
            UPDATE eleves 
            SET classe = CASE
                WHEN classe = '7eme' THEN '8eme'
                WHEN classe = '8eme' THEN '1ere'
                WHEN classe = '1ere' THEN '2eme'
                WHEN classe = '2eme' THEN '3eme'
                WHEN classe = '3eme' THEN '4eme'
                WHEN classe = '4eme' THEN 'diplômé' -- Fin des études
                ELSE classe
            END,
            statut = CASE
                WHEN classe = '4eme' THEN 'diplômé'
                ELSE statut
            END
            WHERE section = 'secondaire'
        ");
    }
}
?>