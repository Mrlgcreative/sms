<?php
class Scan {
    public function index() {
        // Afficher la page principale de scannage
        include 'views/scan/scanner.php';
    }
    
    public function processQRCode() {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        // Récupérer le matricule scanné et le nettoyer
        $matricule = isset($_POST['matricule']) ? trim($_POST['matricule']) : '';
        
        if (empty($matricule)) {
            echo json_encode(['success' => false, 'message' => 'Matricule non fourni']);
            return;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
            return;
        }
        
        // Rechercher l'élève par matricule avec différentes méthodes
        $eleve = null;
        
        // 1. Recherche exacte
        $query = "SELECT e.*, c.nom as classe_nom 
                  FROM eleves e 
                  LEFT JOIN classes c ON e.classe_id = c.id 
                  WHERE e.matricule = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $eleve = $result->fetch_assoc();
        } else {
            // 2. Recherche insensible à la casse
            $query = "SELECT e.*, c.nom as classe_nom 
                      FROM eleves e 
                      LEFT JOIN classes c ON e.classe_id = c.id 
                      WHERE LOWER(e.matricule) = LOWER(?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $matricule);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $eleve = $result->fetch_assoc();
            } else {
                // 3. Recherche par ID si le matricule est un nombre
                if (is_numeric($matricule)) {
                    $query = "SELECT e.*, c.nom as classe_nom 
                              FROM eleves e 
                              LEFT JOIN classes c ON e.classe_id = c.id 
                              WHERE e.id = ?";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("i", $matricule);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $eleve = $result->fetch_assoc();
                    }
                }
                
                // 4. Recherche avec format spécial (SGS-X-XXXXX-XXXX)
                if (!$eleve && strpos($matricule, 'SGS-') === 0) {
                    $parts = explode('-', $matricule);
                    if (count($parts) >= 3) {
                        $eleve_id = (int)$parts[2];
                        $query = "SELECT e.*, c.nom as classe_nom 
                                  FROM eleves e 
                                  LEFT JOIN classes c ON e.classe_id = c.id 
                                  WHERE e.id = ?";
                        $stmt = $mysqli->prepare($query);
                        $stmt->bind_param("i", $eleve_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            $eleve = $result->fetch_assoc();
                        }
                    }
                }
            }
        }
        
        // Fermer le statement
        $stmt->close();
        
        // Si aucun élève n'est trouvé, essayer une recherche partielle
        if (!$eleve) {
            $matricule_like = '%' . $matricule . '%';
            $query = "SELECT e.*, c.nom as classe_nom 
                      FROM eleves e 
                      LEFT JOIN classes c ON e.classe_id = c.id 
                      WHERE e.matricule LIKE ?
                      LIMIT 1";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $matricule_like);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $eleve = $result->fetch_assoc();
            }
            $stmt->close();
        }
        
        // Si toujours aucun élève trouvé
        if (!$eleve) {
            // Afficher les détails du matricule pour le débogage
            echo json_encode([
                'success' => false, 
                'message' => 'Aucun élève trouvé avec ce matricule: "' . $matricule . '"',
                'debug' => [
                    'longueur' => strlen($matricule),
                    'format_hexa' => bin2hex($matricule)
                ]
            ]);
            $mysqli->close();
            return;
        }
        
        // Formater la date de naissance
        $date_naissance = isset($eleve['date_naissance']) ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '';
        
        // Vérifier si l'élève est en ordre avec les frais scolaires
        $frais_info = $this->verifierFraisScolaires($mysqli, $eleve['id']);
        
        // Enregistrer la présence de l'élève (optionnel)
        $presence_enregistree = false;
        
        if (isset($_POST['enregistrer_presence']) && $_POST['enregistrer_presence'] === 'true') {
            $date_actuelle = date('Y-m-d');
            $heure_actuelle = date('H:i:s');
            
            $presence_query = "INSERT INTO presences (eleve_id, date_presence, heure_arrivee, statut) 
                              VALUES (?, ?, ?, 'present') 
                              ON DUPLICATE KEY UPDATE heure_arrivee = ?, statut = 'present'";
            
            $presence_stmt = $mysqli->prepare($presence_query);
            $presence_stmt->bind_param("isss", $eleve['id'], $date_actuelle, $heure_actuelle, $heure_actuelle);
            $presence_stmt->execute();
            $presence_enregistree = true;
            $presence_stmt->close();
        }
        
        // Fermer la connexion
        $mysqli->close();
        
        // Retourner les informations de l'élève
        echo json_encode([
            'success' => true,
            'id' => $eleve['id'],
            'matricule' => isset($eleve['matricule']) ? $eleve['matricule'] : $matricule,
            'nom' => $eleve['nom'],
            'prenom' => $eleve['prenom'],
            'post_nom' => isset($eleve['post_nom']) ? $eleve['post_nom'] : '',
            'classe_nom' => $eleve['classe_nom'],
            'section' => ucfirst($eleve['section']),
            'date_naissance' => $date_naissance,
            'sexe' => $eleve['sexe'],
            'photo' => isset($eleve['photo']) ? $eleve['photo'] : '',
            'presence_enregistree' => $presence_enregistree,
            'frais_info' => $frais_info
        ]);
    }
    
    /**
     * Vérifie si l'élève est en ordre avec les frais scolaires
     * 
     * @param mysqli $mysqli Connexion à la base de données
     * @param int $eleve_id ID de l'élève
     * @return array Informations sur les frais scolaires
     */
    private function verifierFraisScolaires($mysqli, $eleve_id) {
        // Récupérer la session scolaire actuelle
        $session_query = "SELECT * FROM sessions_scolaires WHERE active = 1 LIMIT 1";
        $session_result = $mysqli->query($session_query);
        $session_scolaire = $session_result->fetch_assoc();
        $session_id = $session_scolaire ? $session_scolaire['id'] : 0;
        
        if ($session_id == 0) {
            return [
                'est_en_ordre' => false,
                'total_paye' => 0,
                'total_du' => 0,
                'reste_a_payer' => 0,
                'pourcentage_paye' => 0,
                'paiements' => [],
                'session_scolaire' => 'Aucune session active',
                'message' => 'Aucune session scolaire active trouvée'
            ];
        }
        
        // Récupérer les informations sur les frais de l'élève
        $frais_query = "SELECT 
                          pf.id,
                          pf.montant,
                          pf.date_paiement,
                          pf.type_frais,
                          pf.description,
                          c.montant_total,
                          c.nom as categorie_frais,
                          (pf.montant >= c.montant_total) as est_complet
                        FROM 
                          paiements_frais pf
                        LEFT JOIN 
                          categories_frais c ON pf.categorie_frais_id = c.id
                        WHERE 
                          pf.eleve_id = ? 
                          AND pf.session_id = ?
                        ORDER BY 
                          pf.date_paiement DESC";
        
        $frais_stmt = $mysqli->prepare($frais_query);
        
        if (!$frais_stmt) {
            // Si la requête échoue, essayer une requête plus simple
            $frais_query_simple = "SELECT 
                                    id,
                                    montant,
                                    date_paiement,
                                    type_frais,
                                    description
                                  FROM 
                                    paiements_frais
                                  WHERE 
                                    eleve_id = ? 
                                    AND session_id = ?
                                  ORDER BY 
                                    date_paiement DESC";
            
            $frais_stmt = $mysqli->prepare($frais_query_simple);
            $frais_stmt->bind_param("ii", $eleve_id, $session_id);
            $frais_stmt->execute();
            $frais_result = $frais_stmt->get_result();
            
            $paiements = [];
            $total_paye = 0;
            
            while ($row = $frais_result->fetch_assoc()) {
                $paiements[] = [
                    'id' => $row['id'],
                    'montant_paye' => $row['montant'],
                    'date_paiement' => $row['date_paiement'],
                    'type_frais' => $row['type_frais'],
                    'description' => $row['description'],
                    'montant_total' => 0,
                    'est_complet' => false
                ];
                $total_paye += $row['montant'];
            }
            
            $frais_stmt->close();
            
            // Récupérer le montant total des frais pour la classe de l'élève
            $classe_query = "SELECT classe_id FROM eleves WHERE id = ?";
            $classe_stmt = $mysqli->prepare($classe_query);
            $classe_stmt->bind_param("i", $eleve_id);
            $classe_stmt->execute();
            $classe_result = $classe_stmt->get_result();
            $classe_info = $classe_result->fetch_assoc();
            $classe_id = $classe_info ? $classe_info['classe_id'] : 0;
            $classe_stmt->close();
            
            $total_du_query = "SELECT SUM(montant_total) as total_du FROM frais_classes WHERE classe_id = ? AND session_id = ?";
            $total_du_stmt = $mysqli->prepare($total_du_query);
            
            if ($total_du_stmt) {
                $total_du_stmt->bind_param("ii", $classe_id, $session_id);
                $total_du_stmt->execute();
                $total_du_result = $total_du_stmt->get_result();
                $total_du_row = $total_du_result->fetch_assoc();
                $total_du = $total_du_row ? $total_du_row['total_du'] : 0;
                $total_du_stmt->close();
            } else {
                // Si la table frais_classes n'existe pas, utiliser une valeur par défaut
                $total_du = 0;
            }
            
            $reste_a_payer = $total_du - $total_paye;
            $pourcentage_paye = ($total_du > 0) ? round(($total_paye / $total_du) * 100, 2) : 0;
            $est_en_ordre = ($reste_a_payer <= 0);
            
            return [
                'est_en_ordre' => $est_en_ordre,
                'total_paye' => $total_paye,
                'total_du' => $total_du,
                'reste_a_payer' => $reste_a_payer,
                'pourcentage_paye' => $pourcentage_paye,
                'paiements' => $paiements,
                'session_scolaire' => $session_scolaire ? $session_scolaire['nom'] : 'Inconnue'
            ];
        }
        
        $frais_stmt->bind_param("ii", $eleve_id, $session_id);
        $frais_stmt->execute();
        $frais_result = $frais_stmt->get_result();
        
        $paiements = [];
        $total_paye = 0;
        $total_du = 0;
        $est_en_ordre = true;
        
        while ($row = $frais_result->fetch_assoc()) {
            $paiements[] = $row;
            $total_paye += $row['montant'];
            $total_du += $row['montant_total'];
            
            if (!$row['est_complet']) {
                $est_en_ordre = false;
            }
        }
        
        $frais_stmt->close();
        
        // Si aucun paiement trouvé, vérifier les frais requis pour la classe de l'élève
        if (empty($paiements)) {
            $classe_query = "SELECT classe_id FROM eleves WHERE id = ?";
            $classe_stmt = $mysqli->prepare($classe_query);
            $classe_stmt->bind_param("i", $eleve_id);
            $classe_stmt->execute();
            $classe_result = $classe_stmt->get_result();
            $classe_info = $classe_result->fetch_assoc();
            $classe_id = $classe_info ? $classe_info['classe_id'] : 0;
            $classe_stmt->close();
            
            $frais_requis_query = "SELECT SUM(montant_total) as total_requis 
                                  FROM frais_classes 
                                  WHERE session_id = ? 
                                  AND classe_id = ?";
            $frais_requis_stmt = $mysqli->prepare($frais_requis_query);
            
            if ($frais_requis_stmt) {
                $frais_requis_stmt->bind_param("ii", $session_id, $classe_id);
                $frais_requis_stmt->execute();
                $frais_requis_result = $frais_requis_stmt->get_result();
                $frais_requis = $frais_requis_result->fetch_assoc();
                $total_du = $frais_requis ? $frais_requis['total_requis'] : 0;
                $frais_requis_stmt->close();
            } else {
                // Si la table frais_classes n'existe pas, utiliser une valeur par défaut
                $total_du = 0;
            }
            
            $est_en_ordre = ($total_du == 0);
        }
        
        $reste_a_payer = $total_du - $total_paye;
        $pourcentage_paye = ($total_du > 0) ? round(($total_paye / $total_du) * 100, 2) : 100;
        
        return [
            'est_en_ordre' => $est_en_ordre,
            'total_paye' => $total_paye,
            'total_du' => $total_du,
            'reste_a_payer' => $reste_a_payer,
            'pourcentage_paye' => $pourcentage_paye,
            'paiements' => $paiements,
            'session_scolaire' => $session_scolaire ? $session_scolaire['nom'] : 'Inconnue'
        ];
    }

    public function presences() {
        // Afficher la page de gestion des présences
        include 'views/scan/presences.php';
    }
}
?>