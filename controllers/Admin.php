<?php
require 'models/EleveModel.php';
require 'models/ProfesseurModel.php';
require 'models/FraisModel.php';
require 'models/HistoriqueModel.php';
require 'models/UserModel.php';
require 'models/ParentModel.php';
require 'models/CoursModel.php';
require 'models/ClasseModel.php';
require 'models/ComptableModel.php';
require 'models/DirectorModel.php';
require 'models/DirectriceModel.php';
require 'models/PrefetModel.php';
require 'models/EmployeModel.php';
require 'models/SessionScolaireModel.php';
require 'models/PaiementModel.php';
require 'models/MoisModel.php';
require_once 'includes/logger.php';

class Admin {
    private $eleveModel;
    private $professeurModel;
    private $fraisModel;
    private $historiqueModel;
    private $userModel;
    private $parentModel;
    private $coursModel;
    private $classeModel;
    private $comptableModel;
    private $directorModel;
    private $directriceModel;
    private $prefetModel;
    private $sessionscolaireModel;
    private $employeModel;
    private $paiementModel;
    private $db;    
    private $logger;
    public function __construct() {
        $this->eleveModel = new EleveModel();
        $this->professeurModel = new ProfesseurModel();
        $this->fraisModel = new FraisModel();
        $this->historiqueModel = new HistoriqueModel();
        $this->userModel = new UserModel();
        $this->parentModel = new ParentModel();
        $this->coursModel = new CoursModel();
        $this->classeModel = new ClasseModel();
        $this->comptableModel=new ComptableModel();
        $this->directorModel= new DirectorModel();  
        $this->directriceModel=new DirectriceModel();
        $this->prefetModel=new PrefetModel();
        $this->sessionscolaireModel=new SessionScolaireModel();
        $this->paiementModel = new PaiementModel();
        $this->employeModel = new EmployeModel();
        $this->logger = new Logger();
     }

    public function accueil() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Créer la table system_logs si elle n'existe pas
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        
        // Vérifier si la table system_logs existe
        $table_exists = $mysqli->query("SHOW TABLES LIKE 'system_logs'")->num_rows > 0;
        
        if (!$table_exists) {
            // Créer la table system_logs
            $create_table_sql = "CREATE TABLE system_logs (
                id INT(11) NOT NULL AUTO_INCREMENT,
                username VARCHAR(255) NOT NULL,
                action VARCHAR(255) NOT NULL,
                action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
            )";
            
            $mysqli->query($create_table_sql);
            
            // Insérer quelques données d'exemple
            $sample_data = [
                ["admin", "Connexion au système"],
                ["admin", "Ajout d'un élève"],
                ["admin", "Modification d'un professeur"],
                ["comptable1", "Ajout d'un paiement"],
                ["directeur", "Consultation des rapports"]
            ];
            
            foreach ($sample_data as $data) {
                $username = $data[0];
                $action = $data[1];
                $ip = $_SERVER['REMOTE_ADDR'];
                
                $mysqli->query("INSERT INTO system_logs (username, action, ip_address) 
                               VALUES ('$username', '$action', '$ip')");
            }
        }
        
        $mysqli->close();
        
        // Charger la vue
        require_once 'views/admin/accueil.php';
    }

     public function paiements() {
        // Vérification de l'existence des paramètres GET
        $paiement_id = isset($_GET['paiement_id']) ? (int)$_GET['paiement_id'] : null;
        $eleve_id = isset($_GET['eleve_id']) ? (int)$_GET['eleve_id'] : null;
        $option_id = isset($_GET['option_id']) ? (int)$_GET['option_id'] : null;
        $session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : null;
    
        // Récupérer la session scolaire active
        $session_active = $this->sessionscolaireModel->getActive();
        
        // Vérification et récupération des données de paiement
        $paiements = [];
        if ($paiement_id) {
            $paiements = $this->paiementModel->getByPaiementId($paiement_id); // Méthode spécifique pour un paiement
            if (!$paiements) {
                $paiements = []; // Aucun paiement trouvé
            } else {
                // Convertir en tableau si c'est un seul résultat
                $paiements = [$paiements];
            }
        } elseif ($eleve_id) {
            // Récupérer les paiements d'un élève spécifique
            $paiements = $this->paiementModel->getByEleveId($eleve_id);
        } elseif ($session_id) {
            // Récupérer les paiements d'une session scolaire spécifique
            $paiements = $this->paiementModel->getBySessionId($session_id);
        } else {
            // Récupération de tous les paiements
            $paiements = $this->paiementModel->getAll();
        }
        
        // Enrichir les données de paiement avec les informations de session scolaire
        foreach ($paiements as &$paiement) {
            if (isset($paiement['session_scolaire_id']) && $paiement['session_scolaire_id']) {
                $session = $this->sessionscolaireModel->getById($paiement['session_scolaire_id']);
                if ($session) {
                    $paiement['libelle'] = $session['libelle'] ?? ($session['annee_debut'] . '-' . $session['annee_fin']);
                } else {
                    $paiement['libelle'] = 'Session inconnue';
                }
            } else {
                $paiement['libelle'] = 'Session non spécifiée';
            }
        }
        unset($paiement); // Détruire la référence
          // Traitement des options pour chaque paiement
        $option = null;
        if ($option_id) {
            // Note: optionModel n'est pas défini, on peut commenter cette partie ou ajouter le modèle
            // $option = $this->optionModel->getAll($option_id);
            // if (!$option) {
            //     die("Option non defini");
            // }
        }
        
        // Récupérer toutes les sessions scolaires pour le filtre
        $sessions_scolaires = $this->sessionscolaireModel->getAll();
        
        // Vérifier si l'utilisateur est connecté
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        
        // Récupérer les données pour les filtres
        $classes = $this->classeModel->getAllClasses();
        // Note: moisModel n'est pas défini, on utilise un tableau par défaut
        $mois = [
            ['nom' => 'Janvier'], ['nom' => 'Février'], ['nom' => 'Mars'], ['nom' => 'Avril'],
            ['nom' => 'Mai'], ['nom' => 'Juin'], ['nom' => 'Juillet'], ['nom' => 'Août'],
            ['nom' => 'Septembre'], ['nom' => 'Octobre'], ['nom' => 'Novembre'], ['nom' => 'Décembre']
        ];
        $frais = $this->fraisModel->getAll(); // Correction: fraisModel au lieu de fraismodel
        
        // Calculer le nombre total de paiements
        $total_paiements = count($paiements);
        
        // Charger la vue avec les données
        require 'views/admin/paiement.php';
    }

    public function exportPaiementsPDF() {
        // Démarrer le buffer de sortie pour capturer toute sortie indésirable
        ob_start();
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }        try {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($mysqli->connect_error) {
                throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
            }

            // Récupérer tous les paiements avec les informations des élèves
            $query = "SELECT p.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                             c.nom as classe_nom, o.nom as option_nom, e.section,
                             f.description as frais_description,
                             MONTHNAME(p.payment_date) as mois_nom,
                             MONTH(p.payment_date) as mois_numero
                      FROM paiements_frais p
                      LEFT JOIN eleves e ON p.eleve_id = e.id
                      LEFT JOIN classes c ON e.classe_id = c.id
                      LEFT JOIN options o ON e.option_id = o.id
                      LEFT JOIN frais f ON p.frais_id = f.id
                      ORDER BY p.payment_date DESC";
            
            $result = $mysqli->query($query);
            $paiements = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Ajouter le mois en français
                    $mois_fr = [
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    ];
                    $row['mois'] = $mois_fr[$row['mois_numero']] ?? 'Inconnu';
                    $paiements[] = $row;
                }
            }

            // Récupérer le nombre total de paiements et le montant total
            $total_result = $mysqli->query("SELECT COUNT(*) AS total_paiements, SUM(amount_paid) AS montant_total FROM paiements_frais");
            $totals = $total_result->fetch_assoc();
            $total_paiements = $totals['total_paiements'];
            $montant_total = $totals['montant_total'] ?? 0;

            $mysqli->close();            // Inclure FPDF - essayer différents chemins
            $fpdf_paths = [
                'lib/fpdf_temp/fpdf.php',
                '../lib/fpdf_temp/fpdf.php',
                'sms/lib/fpdf_temp/fpdf.php',
                '../sms/lib/fpdf_temp/fpdf.php',
                'lib/fpdf/fpdf.php',
                '../lib/fpdf/fpdf.php'
            ];
            
            $fpdf_loaded = false;
            foreach ($fpdf_paths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    $fpdf_loaded = true;
                    break;
                }
            }
              if (!$fpdf_loaded) {
                throw new Exception('Bibliothèque FPDF non trouvée. Veuillez vérifier l\'installation.');
            }

            // Créer une nouvelle instance PDF
            $pdf = new FPDF('L', 'mm', 'A4'); // Format paysage
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            // En-tête du document
            $pdf->Cell(277, 10, utf8_decode('SYSTÈME DE GESTION SCOLAIRE'), 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(277, 10, utf8_decode('RAPPORT DES PAIEMENTS'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(277, 5, utf8_decode('Généré le: ' . date('d/m/Y à H:i')), 0, 1, 'C');
            $pdf->Ln(10);

            // Résumé des statistiques
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(277, 8, utf8_decode('RÉSUMÉ'), 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(138, 6, utf8_decode('Nombre total de paiements: ' . $total_paiements), 0, 0, 'L');
            $pdf->Cell(139, 6, utf8_decode('Montant total: ' . number_format($montant_total, 2, ',', ' ') . ' $'), 0, 1, 'L');
            $pdf->Ln(5);            // En-têtes du tableau
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(230, 230, 230);
            
            // Définir les largeurs des colonnes
            $w = array(10, 35, 25, 25, 20, 25, 25, 25, 20, 25, 20);
            $headers = array('N°', 'Nom', 'Prénom', 'Classe', 'Section', 'Type de frais', 'Montant', 'Date', 'Mois', 'Option', 'ID');

            // Afficher les en-têtes
            for($i = 0; $i < count($headers); $i++) {
                $pdf->Cell($w[$i], 8, utf8_decode($headers[$i]), 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Contenu du tableau
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetFillColor(255, 255, 255);            
            $num = 1;
            foreach ($paiements as $paiement) {
                // Vérifier si on a besoin d'une nouvelle page
                if ($pdf->GetY() > 180) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->SetFillColor(230, 230, 230);
                    
                    // Réafficher les en-têtes
                    for($i = 0; $i < count($headers); $i++) {
                        $pdf->Cell($w[$i], 8, utf8_decode($headers[$i]), 1, 0, 'C', true);
                    }
                    $pdf->Ln();
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->SetFillColor(255, 255, 255);
                }                // Données de la ligne
                $data = array(
                    $num,
                    substr($paiement['eleve_nom'] ?? '', 0, 20),
                    substr($paiement['eleve_prenom'] ?? '', 0, 15),
                    substr($paiement['classe_nom'] ?? '', 0, 12),
                    substr($paiement['section'] ?? '', 0, 10),
                    substr($paiement['frais_description'] ?? '', 0, 15),
                    number_format($paiement['amount_paid'], 0) . '$',
                    date('d/m/Y', strtotime($paiement['payment_date'])),
                    substr($paiement['mois'] ?? '', 0, 10),
                    substr($paiement['option_nom'] ?? '', 0, 12),
                    $paiement['id']
                );

                // Afficher la ligne
                for($i = 0; $i < count($data); $i++) {
                    $pdf->Cell($w[$i], 6, utf8_decode($data[$i]), 1, 0, 'C');
                }
                $pdf->Ln();
                $num++;
            }

            // Pied de page avec le total
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(277, 8, utf8_decode('TOTAL GÉNÉRAL: ' . number_format($montant_total, 2, ',', ' ') . ' $'), 0, 1, 'R');

            // Enregistrer l'action dans l'historique
            $this->logAction("Exportation PDF de la liste des paiements");

            // Nettoyer tous les tampons de sortie
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Envoyer les en-têtes HTTP pour le téléchargement PDF
            $filename = 'paiements_' . date('Y-m-d_H-i-s') . '.pdf';
            
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            // Générer et envoyer le PDF
            $pdf->Output($filename, 'D'); // 'D' pour téléchargement forcé
        
        } catch (Exception $e) {
            // Nettoyer le buffer de sortie en cas d'erreur
            ob_clean();
            
            // Log de l'erreur
            error_log("Erreur lors de l'export PDF : " . $e->getMessage());
            
            // Rediriger avec message d'erreur
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=paiements&error=1&message=' . urlencode('Erreur lors de l\'export PDF : ' . $e->getMessage()));
        }
          exit;
    }    public function exportPaiements() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }

        try {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($mysqli->connect_error) {
                throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
            }

            // Récupérer tous les paiements avec les informations des élèves
            $query = "SELECT p.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                             c.nom as classe_nom, o.nom as option_nom, e.section,
                             f.description as frais_description,
                             MONTHNAME(p.payment_date) as mois_nom,
                             MONTH(p.payment_date) as mois_numero
                      FROM paiements_frais p
                      LEFT JOIN eleves e ON p.eleve_id = e.id
                      LEFT JOIN classes c ON e.classe_id = c.id
                      LEFT JOIN options o ON e.option_id = o.id
                      LEFT JOIN frais f ON p.frais_id = f.id
                      ORDER BY p.payment_date DESC";
            
            $result = $mysqli->query($query);
            $paiements = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Ajouter le mois en français
                    $mois_fr = [
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    ];
                    $row['mois'] = $mois_fr[$row['mois_numero']] ?? 'Inconnu';
                    $paiements[] = $row;
                }
            }

            // Récupérer le nombre total de paiements et le montant total
            $total_result = $mysqli->query("SELECT COUNT(*) AS total_paiements, SUM(amount_paid) AS montant_total FROM paiements_frais");
            $totals = $total_result->fetch_assoc();
            $total_paiements = $totals['total_paiements'];
            $montant_total = $totals['montant_total'] ?? 0;

            $mysqli->close();            // Générer un fichier Excel HTML avec styles (extension correcte)
            $filename = 'Rapport_Paiements_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // Headers pour Excel
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Débuter le contenu Excel avec des styles CSS
            echo '<?xml version="1.0" encoding="UTF-8"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                      xmlns:o="urn:schemas-microsoft-com:office:office"
                      xmlns:x="urn:schemas-microsoft-com:office:excel"
                      xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
                      xmlns:html="http://www.w3.org/TR/REC-html40">
            
            <Styles>
                <Style ss:ID="header">
                    <Font ss:Bold="1" ss:Size="16" ss:Color="#FFFFFF"/>
                    <Interior ss:Color="#2E86AB" ss:Pattern="Solid"/>
                    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
                    <Borders>
                        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
                        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
                        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
                        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
                    </Borders>
                </Style>
                
                <Style ss:ID="title">
                    <Font ss:Bold="1" ss:Size="20" ss:Color="#2E86AB"/>
                    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
                </Style>
                
                <Style ss:ID="subtitle">
                    <Font ss:Bold="1" ss:Size="12" ss:Color="#666666"/>
                    <Alignment ss:Horizontal="Center"/>
                </Style>
                
                <Style ss:ID="summary">
                    <Font ss:Bold="1" ss:Size="11" ss:Color="#2E86AB"/>
                    <Interior ss:Color="#E8F4FD" ss:Pattern="Solid"/>
                    <Borders>
                        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    </Borders>
                </Style>
                
                <Style ss:ID="data">
                    <Font ss:Size="10"/>
                    <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
                    <Borders>
                        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    </Borders>
                </Style>
                
                <Style ss:ID="dataAlt">
                    <Font ss:Size="10"/>
                    <Interior ss:Color="#F8F9FA" ss:Pattern="Solid"/>
                    <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
                    <Borders>
                        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    </Borders>
                </Style>
                
                <Style ss:ID="currency">
                    <Font ss:Size="10" ss:Bold="1" ss:Color="#28A745"/>
                    <NumberFormat ss:Format="###,##0.00&quot; $&quot;"/>
                    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
                    <Borders>
                        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    </Borders>
                </Style>
                
                <Style ss:ID="total">
                    <Font ss:Bold="1" ss:Size="12" ss:Color="#FFFFFF"/>
                    <Interior ss:Color="#28A745" ss:Pattern="Solid"/>
                    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
                    <Borders>
                        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
                        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
                        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
                        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
                    </Borders>
                </Style>
            </Styles>
            
            <Worksheet ss:Name="Rapport des Paiements">
                <Table>
                    <Column ss:Width="40"/>
                    <Column ss:Width="120"/>
                    <Column ss:Width="120"/>
                    <Column ss:Width="80"/>
                    <Column ss:Width="80"/>
                    <Column ss:Width="120"/>
                    <Column ss:Width="90"/>
                    <Column ss:Width="90"/>
                    <Column ss:Width="80"/>
                    <Column ss:Width="100"/>
                    <Column ss:Width="70"/>
                    <Column ss:Width="70"/>
                    <Column ss:Width="70"/>
            ';

            // En-tête du document
            echo '<Row ss:Height="25">
                    <Cell ss:MergeAcross="12" ss:StyleID="title">
                        <Data ss:Type="String">📊 SYSTÈME DE GESTION SCOLAIRE</Data>
                    </Cell>
                  </Row>';
            
            echo '<Row ss:Height="20">
                    <Cell ss:MergeAcross="12" ss:StyleID="subtitle">
                        <Data ss:Type="String">RAPPORT DÉTAILLÉ DES PAIEMENTS</Data>
                    </Cell>
                  </Row>';
            
            echo '<Row ss:Height="15">
                    <Cell ss:MergeAcross="12" ss:StyleID="subtitle">
                        <Data ss:Type="String">Généré le ' . date('d/m/Y à H:i') . '</Data>
                    </Cell>
                  </Row>';
            
            // Ligne vide
            echo '<Row ss:Height="10"><Cell/></Row>';
            
            // Résumé des statistiques
            echo '<Row ss:Height="25">
                    <Cell ss:MergeAcross="5" ss:StyleID="summary">
                        <Data ss:Type="String">📈 RÉSUMÉ STATISTIQUE</Data>
                    </Cell>
                    <Cell ss:MergeAcross="6" ss:StyleID="summary">
                        <Data ss:Type="String">🎯 INFORMATIONS CLÉS</Data>
                    </Cell>
                  </Row>';
            
            echo '<Row ss:Height="20">
                    <Cell ss:MergeAcross="2" ss:StyleID="summary">
                        <Data ss:Type="String">Nombre total de paiements:</Data>
                    </Cell>
                    <Cell ss:MergeAcross="2" ss:StyleID="summary">
                        <Data ss:Type="Number">' . $total_paiements . '</Data>
                    </Cell>
                    <Cell ss:MergeAcross="3" ss:StyleID="summary">
                        <Data ss:Type="String">Montant total:</Data>
                    </Cell>
                    <Cell ss:MergeAcross="3" ss:StyleID="currency">
                        <Data ss:Type="Number">' . $montant_total . '</Data>
                    </Cell>
                  </Row>';
            
            // Ligne vide
            echo '<Row ss:Height="10"><Cell/></Row>';

            // En-têtes des colonnes
            echo '<Row ss:Height="30">';
            $headers = [
                '🔢 N°', '👤 Nom', '👤 Prénom', '🎓 Classe', '📋 Section',
                '💰 Type de frais', '💵 Montant payé', '📅 Date paiement',
                '📆 Mois', '🎯 Option', '🆔 ID Paiement', '🆔 ID Élève', '🆔 ID Frais'
            ];
            
            foreach ($headers as $header) {
                echo '<Cell ss:StyleID="header"><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
            }
            echo '</Row>';

            // Données des paiements
            $num = 1;
            foreach ($paiements as $index => $paiement) {
                $styleID = ($index % 2 == 0) ? 'data' : 'dataAlt';
                $currencyStyle = ($index % 2 == 0) ? 'currency' : 'currency';
                
                echo '<Row ss:Height="25">';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="Number">' . $num . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . htmlspecialchars($paiement['eleve_nom'] ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . htmlspecialchars($paiement['eleve_prenom'] ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . htmlspecialchars($paiement['classe_nom'] ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . htmlspecialchars($paiement['section'] ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . htmlspecialchars($paiement['frais_description'] ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $currencyStyle . '"><Data ss:Type="Number">' . ($paiement['amount_paid'] ?? 0) . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . date('d/m/Y', strtotime($paiement['payment_date'])) . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . htmlspecialchars($paiement['mois'] ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . htmlspecialchars($paiement['option_nom'] ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="Number">' . ($paiement['id'] ?? 0) . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="Number">' . ($paiement['eleve_id'] ?? 0) . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="Number">' . ($paiement['frais_id'] ?? 0) . '</Data></Cell>';
                echo '</Row>';
                $num++;
            }

            // Ligne vide
            echo '<Row ss:Height="10"><Cell/></Row>';
            
            // Ligne de total            echo '<Row ss:Height="30">';
            echo '<Cell ss:MergeAcross="5" ss:StyleID="total"><Data ss:Type="String">🎯 TOTAL GÉNÉRAL</Data></Cell>';
            echo '<Cell ss:StyleID="total"><Data ss:Type="Number">' . $montant_total . '</Data></Cell>';
            echo '<Cell ss:MergeAcross="5" ss:StyleID="total"><Data ss:Type="String">✅ ' . $total_paiements . ' paiements</Data></Cell>';
            echo '</Row>';
            
            echo '</Table></Worksheet></Workbook>';

            // Enregistrer l'action dans l'historique
            $this->logAction("Exportation Excel de la liste des paiements - Format professionnel avec " . $total_paiements . " paiements");
            
        } catch (Exception $e) {
            // Log de l'erreur
            error_log("Erreur lors de l'export Excel : " . $e->getMessage());
            
            // Rediriger avec message d'erreur
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=paiements&error=1&message=' . urlencode('Erreur lors de l\'export Excel : ' . $e->getMessage()));
        }
        
        exit;
    }

    public function exportPaiementsCSV() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }

        try {
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($mysqli->connect_error) {
                throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
            }

            // Récupérer tous les paiements
            $query = "SELECT p.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                             c.nom as classe_nom, o.nom as option_nom, e.section,
                             f.description as frais_description,
                             MONTH(p.payment_date) as mois_numero
                      FROM paiements_frais p
                      LEFT JOIN eleves e ON p.eleve_id = e.id
                      LEFT JOIN classes c ON e.classe_id = c.id
                      LEFT JOIN options o ON e.option_id = o.id
                      LEFT JOIN frais f ON p.frais_id = f.id
                      ORDER BY p.payment_date DESC";
            
            $result = $mysqli->query($query);
            $paiements = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $mois_fr = [
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    ];
                    $row['mois'] = $mois_fr[$row['mois_numero']] ?? 'Inconnu';
                    $paiements[] = $row;
                }
            }

            // Statistiques
            $total_result = $mysqli->query("SELECT COUNT(*) AS total_paiements, SUM(amount_paid) AS montant_total FROM paiements_frais");
            $totals = $total_result->fetch_assoc();
            $total_paiements = $totals['total_paiements'];
            $montant_total = $totals['montant_total'] ?? 0;
            $mysqli->close();

            // Headers CSV
            $filename = 'Rapport_Paiements_CSV_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-tête stylisé
            fputcsv($output, ['=== SYSTÈME DE GESTION SCOLAIRE ==='], ';');
            fputcsv($output, ['RAPPORT DES PAIEMENTS'], ';');
            fputcsv($output, ['Généré le: ' . date('d/m/Y à H:i')], ';');
            fputcsv($output, ['Par: ' . ($_SESSION['username'] ?? 'Admin')], ';');
            fputcsv($output, ['===================================='], ';');
            fputcsv($output, [''], ';');

            // Statistiques
            fputcsv($output, ['RÉSUMÉ STATISTIQUE'], ';');
            fputcsv($output, ['Nombre de paiements:', $total_paiements], ';');
            fputcsv($output, ['Montant total:', number_format($montant_total, 2, ',', ' ') . ' $'], ';');
            fputcsv($output, [''], ';');

            // En-têtes des colonnes
            $headers = [
                'N°', 'Nom', 'Prénom', 'Classe', 'Section',
                'Type de frais', 'Montant (USD)', 'Date paiement',
                'Mois', 'Option', 'ID Paiement', 'ID Élève', 'ID Frais'
            ];
            fputcsv($output, $headers, ';');

            // Données
            $num = 1;
            foreach ($paiements as $paiement) {
                $data = [
                    sprintf('%03d', $num),
                    strtoupper($paiement['eleve_nom'] ?? ''),
                    ucwords(strtolower($paiement['eleve_prenom'] ?? '')),
                    $paiement['classe_nom'] ?? '',
                    $paiement['section'] ?? '',
                    $paiement['frais_description'] ?? '',
                    number_format($paiement['amount_paid'], 2, ',', ' ') . ' $',
                    date('d/m/Y', strtotime($paiement['payment_date'])),
                    $paiement['mois'] ?? '',
                    $paiement['option_nom'] ?? '',
                    $paiement['id'],
                    $paiement['eleve_id'] ?? '',
                    $paiement['frais_id'] ?? ''
                ];
                fputcsv($output, $data, ';');
                $num++;
            }

            // Total
            fputcsv($output, [''], ';');
            fputcsv($output, ['TOTAL GÉNÉRAL:', '', '', '', '', '', number_format($montant_total, 2, ',', ' ') . ' $'], ';');

            $this->logAction("Export CSV amélioré - " . $total_paiements . " paiements");
            fclose($output);
            
        } catch (Exception $e) {
            error_log("Erreur export CSV : " . $e->getMessage());
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=paiements&error=1&message=' . urlencode('Erreur export CSV'));
        }
        exit;
    }
    public function eleves() {
    
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        
        // Vérifier si un filtre de classe est appliqué
        $classe_filter = isset($_GET['classe']) ? intval($_GET['classe']) : 0;
        $where_clause = $classe_filter > 0 ? " WHERE e.classe_id = $classe_filter" : "";
        
        // Requête pour récupérer les élèves avec les informations de classe
        $query = "SELECT e.*, c.niveau AS classe_nom, c.id AS classe_id, o.nom AS option_nom 
                  FROM eleves e 
                  LEFT JOIN classes c ON e.classe_id = c.id 
                  LEFT JOIN options o ON e.option_id = o.id
                  $where_clause
                  ORDER BY e.nom, e.post_nom, e.prenom";
        
        $result = $mysqli->query($query);
        
        if (!$result) {
            die("Query failed: " . $mysqli->error);
        }
        
        $eleves = [];
        while ($row = $result->fetch_assoc()) {
            $eleves[] = $row;
        }
        
        $result->free();
        $mysqli->close();
        
        // Charger la vue
        require_once 'views/admin/eleve.php';
    }

      
    

    public function professeurs() {
        $professeurs = $this->professeurModel->getAll();
        require 'views/admin/professeurs.php';
    }
    

    public function ajoutProfesseur() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $adresse=$_POST['adresse'];
            $classe_id = $_POST['classe_id'];
            $cours_id=$_POST['cours_id'];
            $section =$_POST['section'];
            
            $this->professeurModel->add($nom, $prenom,$contact, $email,$adresse,   $classe_id,
            $cours_id,  $section);
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=professeurs');
        } else {
            require 'views/admin/ajout_professeur.php';
        }
    }

    /**
 * Débloque une adresse IP
 */
public function unblockIP() {
    // Vérifier si l'utilisateur est connecté et a le rôle d'administrateur
    if (!isAuthenticated('admin')) {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=accueil&unblock_error=1');
        exit;
    }
    
    // Récupérer l'adresse IP à débloquer
    $ip_address = isset($_POST['ip_address']) ? cleanInput($_POST['ip_address']) : '';
    
    if (empty($ip_address)) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=accueil&unblock_error=1');
        exit;
    }
    
    // Tenter de débloquer l'IP
    $success = unblockIP($ip_address);
    
    if ($success) {
        // Journaliser l'action
        $this->logAction("Déblocage de l'adresse IP: " . $ip_address);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=accueil&unblock_success=1');
    } else {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=accueil&unblock_error=1');
    }
    exit;

}

   

    public function frais() {
        $frais = $this->fraisModel->getAll();
        require 'views/admin/frais.php';
    }

    public function ajoutFrais() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $montant = $_POST['montant'];
            $description = $_POST['description'];
            $section = $_POST['section'];
            $this->fraisModel->add($montant, $description, $section);
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=frais');
        } else {
            require 'views/admin/ajout_frais.php';
        }
    }

    public function paiementProfs() {
        require 'views/admin/paiement_profs.php';
    }

    public function directeurs() {
        $directeurModel = new DirectorModel ();
        $directeur= $directeurModel->getAll();
        require 'views/admin/directeurs.php';
    }

        /**
     * Gère l'édition d'un préfet existant
     */
    public function editPrefet() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = "ID du préfet non spécifié";
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=prefets');
            exit;
        }
        
        $id = intval($_GET['id']);
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
           $username=isset($_POST['username'])? $_POST['username'] : '';
            $contact = isset($_POST['contact']) ? $_POST['contact'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
            $section = isset($_POST['section']) ? $_POST['section'] : '';
            
            // Validation des données
            if (empty($username) || empty($contact) || empty($email) || empty($adresse) || empty($section)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editPrefet&id=' . $id);
                exit;
            }
            
            // Mettre à jour les données du préfet
            $result = $this->prefetModel-> update($id, $username, $contact, $email, $adresse, $section);
            
            if ($result) {
                // Enregistrer l'action dans l'historique
                $this->historiqueModel->add('Modification', 'Préfet', $id);
                
                $_SESSION['success'] = "Le préfet a été modifié avec succès";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=prefets');
            } else {
                $_SESSION['error'] = "Erreur lors de la modification du préfet";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editPrefet&id=' . $id);
            }
            exit;
        }
        
        // Récupérer les données du préfet
        $prefet = $this->prefetModel->getById($id);
        
        if (!$prefet) {
            $_SESSION['error'] = "Préfet non trouvé";
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=prefets');
            exit;
        }
        
        // Charger la vue d'édition
        require 'views/admin/edit_prefet.php';
    }

        /**
     * Gère l'édition d'un professeur existant
     */
    public function editProfesseur() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = "ID du professeur non spécifié";
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=professeurs');
            exit;
        }
        
        $id = intval($_GET['id']);
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
            $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
            $contact = isset($_POST['contact']) ? $_POST['contact'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
            $classe_id = isset($_POST['classe_id']) ? intval($_POST['classe_id']) : 0;
            $cours_id = isset($_POST['cours_id']) ? intval($_POST['cours_id']) : 0;
            $section = isset($_POST['section']) ? $_POST['section'] : '';
            
            // Validation des données
            if (empty($nom) || empty($prenom) || empty($contact) || empty($email) || empty($adresse) || empty($section)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editProfesseur&id=' . $id);
                exit;
            }
            
            // Mettre à jour les données du professeur
            $result = $this->professeurModel->update($id, $nom, $prenom, $contact, $email, $adresse, $classe_id, $cours_id, $section);
            
            if ($result) {
                // Enregistrer l'action dans l'historique
                $this->historiqueModel->add('Modification', 'Professeur', $id);
                
                $_SESSION['success'] = "Le professeur a été modifié avec succès";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=professeurs');
            } else {
                $_SESSION['error'] = "Erreur lors de la modification du professeur";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editProfesseur&id=' . $id);
            }
            exit;
        }
        
        // Récupérer les données du professeur
        $professeur = $this->professeurModel->getById($id);
        
        if (!$professeur) {
            $_SESSION['error'] = "Professeur non trouvé";
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=professeurs');
            exit;
        }
        
        // Récupérer la liste des classes pour le formulaire
        $classes = $this->classeModel->getAllClasses();
        
        // Récupérer la liste des cours pour le formulaire
        $cours = $this->coursModel->getAll();
        
        // Charger la vue d'édition
        require 'views/admin/edit_professeur.php';
    }

    /**
     * Gère l'édition d'un frais existant
     */
    public function editFrais() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = "ID du frais non spécifié";
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=frais');
            exit;
        }
        
        $id = intval($_GET['id']);
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $montant = isset($_POST['montant']) ? floatval($_POST['montant']) : 0;
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $section = isset($_POST['section']) ? $_POST['section'] : '';
            
            // Validation des données
            if ($montant <= 0) {
                $_SESSION['error'] = "Le montant doit être supérieur à zéro";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editFrais&id=' . $id);
                exit;
            }
            
            if (empty($description)) {
                $_SESSION['error'] = "La description est requise";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editFrais&id=' . $id);
                exit;
            }
            
            if (empty($section)) {
                $_SESSION['error'] = "La section est requise";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editFrais&id=' . $id);
                exit;
            }
            
            // Mettre à jour les données du frais
            $result = $this->fraisModel->update($id, $montant, $description, $section);
            
            if ($result) {
                // Enregistrer l'action dans l'historique
                $this->historiqueModel->add('Modification', 'Frais', $id);
                
                $_SESSION['success'] = "Le frais a été modifié avec succès";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=frais');
            } else {
                $_SESSION['error'] = "Erreur lors de la modification du frais";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editFrais&id=' . $id);
            }
            exit;
        }
        
        // Récupérer les données du frais
        $frais = $this->fraisModel->getById($id);
        
        if (!$frais) {
            $_SESSION['error'] = "Frais non trouvé";
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=frais');
            exit;
        }
        
        // Charger la vue d'édition
        require 'views/admin/edit_frais.php';
    }
    /**
     * Gère l'édition d'une directrice existante
     */
    public function editDirectrice() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = "ID de la directrice non spécifié";
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=directrices');
            exit;
        }
        
        $id = intval($_GET['id']);
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
            $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
            $contact = isset($_POST['contact']) ? $_POST['contact'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
            $section = isset($_POST['section']) ? $_POST['section'] : '';
            
            // Validation des données
            if (empty($nom) || empty($prenom) || empty($contact) || empty($email) || empty($adresse) || empty($section)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editDirectrice&id=' . $id);
                exit;
            }
            
            // Mettre à jour les données de la directrice
            $result = $this->directriceModel->update($id, $nom, $prenom, $contact, $email, $adresse, $section);
            
            if ($result) {
                // Enregistrer l'action dans l'historique
                $this->historiqueModel->add('Modification', 'Directrice', $id);
                
                $_SESSION['success'] = "La directrice a été modifiée avec succès";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=directrices');
            } else {
                $_SESSION['error'] = "Erreur lors de la modification de la directrice";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=editDirectrice&id=' . $id);
            }
            exit;
        }
        
        // Récupérer les données de la directrice
        $directrice = $this->directriceModel->getById($id);
        
        if (!$directrice) {
            $_SESSION['error'] = "Directrice non trouvée";
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=directrices');
            exit;
        }
        
        // Charger la vue d'édition
        require 'views/admin/edit_directrice.php';
    }
   

    public function addDirecteur(){
        if ($_SERVER['REQUEST_METHOD']=='POST'){
            $nom= $_POST['nom'];
            $prenom=$_POST['prenom'];
            $contact= $_POST['contact'];
            $email=$_POST['email'];
            $adresse =$_POST ['adresse'];
            $section=$_POST['section'];
            $this->directorModel->add($nom, $prenom, $contact, $email, $adresse, $section);
            header('location: '. BASE_URL .'index.php?controller=Admin&action=directeurs');

        }else{
            require 'views/admin/add_directeur.php';
        }
    }
    

    public function directrices() {
        $directriceModel = new DirectriceModel();
        $directrices= $directriceModel->getAllDirectrice();
        require 'views/admin/directrices.php';
    }

    public function adddirectrice(){
        if ($_SERVER['REQUEST_METHOD'] === "POST"){
            $nom= $_POST['nom'];
            $prenom= $_POST['prenom'];
            $contact=$_POST['contact'];
            $email= $_POST['email'];
            $adresse= $_POST['adresse'];
            $section =$_POST['section'];
            $this->directriceModel->add($nom, $prenom, $contact, $email, $adresse, $section);
            header('location: ' .BASE_URL . 'index.php?controller=Admin&action=directrices');
        }else{
            require 'views/admin/add_directrice.php';
        }
    }

    public function prefets() {
        $prefesModel= new PrefetModel();
        $prefets =$prefesModel->getAll();
        require 'views/admin/prefets.php';
    }

    public function achatFournitures() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue
        require_once 'views/admin/achatFournitures.php';
    }

    public function addprefet(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nom= $_POST['nom'];
            $prenom= $_POST['prenom'];
            $contact=$_POST['contact'];
            $email=$_POST['email'];
            $adresse=$_POST['adresse'];
            $section=$_POST['section'];
            $this->prefetModel->add($nom, $prenom, $contact, $email, $adresse, $section);
            header('Location: ' .BASE_URL . 'index.php?controller=Admin&action=prefet');

        }else{
            require 'views/admin/add_prefet.php';
        }
    }

  // Méthode pour lister les employés
public function employes() {
    $employeModel = new EmployeModel();
    $employes = $employeModel->getAll();

    include 'views/admin/employes.php';
}

// Méthode pour ajouter un employé
public function ajoutemployes() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Traitement du formulaire d'ajout d'un employé
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $adresse= $_POST['adresse'];
        $poste = $_POST['poste'];

        $employeModel = new EmployeModel();
        $employeModel->add($nom, $prenom, $email, $contact,$adresse, $poste);

        // Rediriger après ajout
        header("Location: " . BASE_URL . "index.php?controller=Admin&action=employes");
    } else {
        include 'views/admin/ajout_employe.php';
    }
}

// Méthode pour modifier un employé


// Méthode pour supprimer un employé
public function deleteEmploye() {
    $id = $_GET['id'];
    $employeModel = new EmployeModel();
    $employeModel->delete($id);

    // Rediriger après suppression
    header("Location: " . BASE_URL . "index.php?controller=Admin&action=employes");
}

    public function historiques() {
        $historiques = $this->historiqueModel->getAll();
        require 'views/admin/rapport_action.php';
    }

    public function parents() {
        $parents = $this->eleveModel->getAll();
        require 'views/admin/parents.php';
    }

    public function cours() {
        $cours = $this->coursModel->getAll();
        $profs=$this->professeurModel->getAll();
        require 'views/admin/cours.php';
    }

   
    public function ajoutCours() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement du formulaire d'ajout d'un cours
            $titre = isset($_POST['titre']) ? $_POST['titre'] : null;
            $description = isset($_POST['description']) ? $_POST['description'] : null;
            $professeur = isset($_POST['professeur_id']) ? $_POST['professeur_id'] : null;
            $classe = isset($_POST['classe_id']) ? $_POST['classe_id'] : null;
            $section = isset($_POST['section']) ? $_POST['section'] : null;
            $option = isset($_POST['option']) ? $_POST['option'] : ''; // option peut être null pour certaines sections

            // Vérification que les champs obligatoires ne sont pas vides
            if ($titre && $description && $professeur && $classe && $section) {
                $coursModel = new CoursModel();
                $coursModel->add($titre, $description, $professeur, $classe, $section, $option);

                // Rediriger après ajout
                header("Location: " . BASE_URL . "index.php?controller=Admin&action=cours");
            } else {
                $error = "Tous les champs obligatoires doivent être remplis.";
                include 'views/admin/ajout_cours.php';
            }
        } else {
            include 'views/admin/ajout_cours.php';
        }
    }


    

    public function classes() {
        $classModel = new ClasseModel();
        $classes = $classModel->getAllClasses();
    
        include 'views/admin/classe.php';
    }

    public function addClasse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $niveau = isset($_POST['niveau']) ? $_POST['niveau'] : '';
            $section = $_POST['section'];
            $titulaire = isset($_POST['titulaire']) ? $_POST['titulaire'] : null;
            $prof_id = isset($_POST['prof_id']) ? intval($_POST['prof_id']) : null;
            
            // Validation des données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom de la classe est requis.";
            }
            
            if (empty($niveau)) {
                $errors[] = "Le niveau est requis.";
            }
            
            if (empty($section)) {
                $errors[] = "La section est requise.";
            }
            
            if (empty($errors)) {
                $this->classeModel->add($nom, $niveau, $section, $titulaire, $prof_id);
                $_SESSION['message'] = "Classe ajoutée avec succès.";
                $_SESSION['message_type'] = "success";
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=classes');
                exit();
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=addClasse');
                exit();
            }
        } else {
            require 'views/admin/add_classe.php';
        }
    }

    public function comptable() {
        $comptableModel = new ComptableModel();
        $comptables= $comptableModel->getAll();
        require 'views/admin/comptable.php';
    }

    public function addcomptable(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            $nom= $_POST['nom'];
            $prenom= $_POST['prenom'];
            $contact = $_POST['contact'];
            $email= $_POST['email'];
            $adresse= $_POST['adresse'];
           
            $this->comptableModel->add($nom, $prenom, $contact, $email, $adresse );
            header('Location: ' .BASE_URL . 'index.php?controller=Admin&action=comptable');

        }else{
            require 'views/admin/add_comptable.php';
        }
    }

    
// Méthode pour supprimer un élève

public function deleteEleve() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->eleveModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Élève', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=eleves');
    }
}

// Méthode pour supprimer un professeur
// Méthode pour supprimer un professeur
public function deleteProfesseur() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Vérifier si le professeur a des cours associés
        $coursAssocies = $this->coursModel->getByProfesseur($id);
        
        if (!empty($coursAssocies)) {
            // Option 1: Supprimer les cours associés
            foreach ($coursAssocies as $cours) {
                $this->coursModel->delete($cours['id']);
                // Modifié pour correspondre à la structure de la table historique
                $this->historiqueModel->add('Suppression', 'Cours', $cours['id']);
            }
        }
        
        // Maintenant on peut supprimer le professeur
        $this->professeurModel->delete($id);
        
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Professeur', $id);
        
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=professeurs');
    }
}

// Méthode pour supprimer des frais
public function deleteFrais() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->fraisModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Frais', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=frais');
    }
}

// Méthode pour supprimer un directeur
public function deleteDirecteur() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->directorModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Directeur', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=directeurs');
    }
}

// Méthode pour supprimer une directrice
public function deleteDirectrice() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->directriceModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Directrice', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=directrices');
    }
}

// Méthode pour supprimer un préfet
public function deletePrefet() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->prefetModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Préfet', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=prefet');
    }
}

// Méthode pour supprimer un parent
public function deleteParent() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->parentModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Parent', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=parents');
    }
}

// Méthode pour supprimer un cours
public function deleteCours() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->coursModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Cours', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=cours');
    }
}

// Méthode pour supprimer une classe
public function deleteClasse() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->classeModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Classe', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=classes');
    }
}

// Méthode pour supprimer un comptable
public function deleteComptable() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->comptableModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Comptable', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=comptable');
    }
}

// Méthode pour supprimer un utilisateur
public function deleteUser() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->userModel->delete($id);
        // Enregistrer l'action dans l'historique
        $this->historiqueModel->add('Suppression', 'Utilisateur', $id);
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=users');
    }
}


public function nouvelleAnneeScolaire() {
    // Afficher le formulaire pour créer une nouvelle année
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        require 'views/admin/nouvelle_annee.php';
    } 
    // Traiter le formulaire soumis
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $annee_debut = $_POST['annee_debut'];
        $annee_fin = $_POST['annee_fin'];
        $libelle = $_POST['libelle'];
        
        try {
            $this->sessionscolaireModel = new SessionScolaireModel();
            $nouvelle_annee_id = $this->sessionscolaireModel->initialiserNouvelleAnnee($annee_debut, $annee_fin, $libelle);
            
            // Redirection avec message de succès
            header('Location: ' . BASE_URL . 'index.php?controller=admin&action=sessions&success=1');
            exit();
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            header('Location: ' . BASE_URL . 'index.php?controller=admin&action=nouvelleAnneeScolaire&error=' . urlencode($e->getMessage()));
            exit();
        }
    }
}

    public function profil() {
    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Charger la vue du profil
    require 'views/admin/profil.php';
}

public function updateProfile() {    // Vérifier si l'utilisateur est connecté
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Vérifier si un fichier a été téléchargé
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] != 0) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=Erreur lors du téléchargement du fichier');
        exit;
    }
    
    // Vérifier le type de fichier
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['avatar']['type'], $allowed_types)) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=Type de fichier non autorisé');
        exit;
    }
    
    // Vérifier la taille du fichier (2MB max)
    if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=Le fichier est trop volumineux (max 2MB)');
        exit;
    }
    
    // Créer le dossier uploads s'il n'existe pas
    $upload_dir = 'uploads/avatars/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Générer un nom de fichier unique
    $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $file_extension;
    $target_file = $upload_dir . $new_filename;
    
    // Déplacer le fichier téléchargé
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
        // Mettre à jour la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=Erreur de connexion à la base de données');
            exit;
        }
        
        // Récupérer l'ancienne image pour la supprimer si elle existe
        $stmt = $mysqli->prepare("SELECT image FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $old_image = $row['image'];
            // Supprimer l'ancienne image si ce n'est pas l'image par défaut
            if (!empty($old_image) && $old_image != 'dist/img/user2-160x160.jpg' && file_exists($old_image)) {
                unlink($old_image);
            }
        }
        $stmt->close();
        
        // Mettre à jour l'image dans la base de données
        $stmt = $mysqli->prepare("UPDATE users SET image = ? WHERE id = ?");
        $stmt->bind_param("si", $target_file, $user_id);
        
        if ($stmt->execute()) {
            // Mettre à jour la session
            $_SESSION['image'] = $target_file;
            
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&success=1&message=Photo de profil mise à jour avec succès');
        } else {
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=Erreur lors de la mise à jour de la base de données');
        }
        
        $stmt->close();
        $mysqli->close();
    } else {
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=profil&error=1&message=Erreur lors du déplacement du fichier');
    }
    
    exit;
}

// Méthode pour afficher les détails d'un élève
public function vieweleve() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Récupérer les informations de l'élève
        $eleve = $this->eleveModel->getById($id);
        
        if ($eleve) {
            // Charger la vue du profil de l'élève
            require 'views/admin/vieweleve.php';
        } else {
            // Rediriger vers la liste des élèves si l'élève n'existe pas
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=eleves&error=1&message=' . urlencode('Élève non trouvé'));
            exit;
        }
    } else {
        // Rediriger vers la liste des élèves si aucun ID n'est fourni
        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=eleves');
        exit;
    }
}

// Méthode pour afficher les détails d'un professeur
// Remove the extra closing brace that was here

public function rapportactions() {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
        exit;
    }
    
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    // Get the table structure to determine column names
    $structure_query = "DESCRIBE system_logs";
    $structure_result = $mysqli->query($structure_query);
    $columns = [];
    
    if ($structure_result) {
        while ($row = $structure_result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    
    // Récupérer les actions du système
    $actions_query = "SELECT * FROM system_logs ORDER BY id DESC";
    $result = $mysqli->query($actions_query);
    
    $actions = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Ensure all expected keys exist in the data
            $action = [
                'id' => isset($row['id']) ? $row['id'] : '',
                'username' => isset($row['username']) ? $row['username'] : '',
                'action' => isset($row['action']) ? $row['action'] : 
                           (isset($row['action_description']) ? $row['action_description'] : ''),
                'action_time' => isset($row['action_time']) ? $row['action_time'] : 
                                (isset($row['timestamp']) ? $row['timestamp'] : date('Y-m-d H:i:s')),
                'ip_address' => isset($row['ip_address']) ? $row['ip_address'] : ''
            ];
            $actions[] = $action;
        }
    }
    
    // Fermer la connexion
    $mysqli->close();
    
    // Enregistrer cette consultation dans les logs
    $this->logAction("Consultation du rapport d'actions");
    
    // Charger la vue
    require 'views/admin/rapport_actions.php';
}

    // Ajouter ces méthodes au contrôleur Admin
    
    /**
     * Vérifie si un administrateur est connecté
     * @return bool Retourne true si un administrateur est connecté, false sinon
     */
    private function isAdminLoggedIn() {
        // Vérifier si la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier si l'utilisateur est connecté et a le rôle d'administrateur
        return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
   
    
   


    public function evenementsScolaires() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue
        require_once 'views/admin/evenementsScolaires.php';
    }
    
    // Méthode pour ajouter un événement scolaire
    public function ajouterEvenement() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $titre = isset($_POST['titre']) ? $_POST['titre'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : '';
            $date_fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : '';
            $lieu = isset($_POST['lieu']) ? $_POST['lieu'] : '';
            $responsable = isset($_POST['responsable']) ? $_POST['responsable'] : '';
            $couleur = isset($_POST['couleur']) ? $_POST['couleur'] : '#3c8dbc';
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }
            
            // Instancier le modèle
            require_once 'models/EvenementScolaire.php';
            $evenementModel = new EvenementScolaire($mysqli);
            
            // Ajouter l'événement
            $result = $evenementModel->ajouterEvenement($titre, $description, $date_debut, $date_fin, $lieu, $responsable, $couleur);
            
            if ($result) {
                // Enregistrer l'action dans les logs
                $this->logAction("Ajout d'un événement scolaire: " . $titre);
                
                // Rediriger avec un message de succès
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=evenementsScolaires&success=1&message=' . urlencode('Événement ajouté avec succès'));
            } else {
                // Rediriger avec un message d'erreur
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=ajouterEvenement&error=1&message=' . urlencode('Erreur lors de l\'ajout de l\'événement'));
            }
            
            $mysqli->close();
            exit;
        } else {
            // Afficher le formulaire d'ajout
            require_once 'views/admin/ajout_evenement.php';
        }
    }
    
    // Méthode pour modifier un événement scolaire
    public function modifierEvenement() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=evenementsScolaires&error=1&message=' . urlencode('ID d\'événement non spécifié'));
            exit;
        }
        
        $id = intval($_GET['id']);
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        
        // Instancier le modèle
        require_once 'models/EvenementScolaire.php';
        $evenementModel = new EvenementScolaire($mysqli);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $titre = isset($_POST['titre']) ? $_POST['titre'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : '';
            $date_fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : '';
            $lieu = isset($_POST['lieu']) ? $_POST['lieu'] : '';
            $responsable = isset($_POST['responsable']) ? $_POST['responsable'] : '';
            $couleur = isset($_POST['couleur']) ? $_POST['couleur'] : '#3c8dbc';
            
            // Modifier l'événement
            $result = $evenementModel->modifierEvenement($id, $titre, $description, $date_debut, $date_fin, $lieu, $responsable, $couleur);
            
            if ($result) {
                // Enregistrer l'action dans les logs
                $this->logAction("Modification d'un événement scolaire: " . $titre);
                
                // Rediriger avec un message de succès
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=evenementsScolaires&success=1&message=' . urlencode('Événement modifié avec succès'));
            } else {
                // Rediriger avec un message d'erreur
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=modifierEvenement&id=' . $id . '&error=1&message=' . urlencode('Erreur lors de la modification de l\'événement'));
            }
            
            $mysqli->close();
            exit;
        } else {
            // Récupérer les informations de l'événement
            $evenement = $evenementModel->getEvenementById($id);
            
            if (!$evenement) {
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=evenementsScolaires&error=1&message=' . urlencode('Événement non trouvé'));
                exit;
            }
            
            // Afficher le formulaire de modification
            require_once 'views/admin/modifier_evenement.php';
        }
    }
    
    public function carteEleve() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Récupérer l'ID de l'élève
        $eleve_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Charger la vue de la carte d'élève
        require_once 'views/admin/carte.php';
    }
    
    // Méthode pour supprimer un événement scolaire
    public function supprimerEvenement() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=evenementsScolaires&error=1&message=' . urlencode('ID d\'événement non spécifié'));
            exit;
        }
        
        $id = intval($_GET['id']);
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        
        // Instancier le modèle
        require_once 'models/EvenementScolaire.php';
        $evenementModel = new EvenementScolaire($mysqli);
        
        // Récupérer les informations de l'événement avant suppression pour le log
        $evenement = $evenementModel->getEvenementById($id);
        
        if (!$evenement) {
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=evenementsScolaires&error=1&message=' . urlencode('Événement non trouvé'));
            exit;
        }
        
        // Supprimer l'événement
        $result = $evenementModel->supprimerEvenement($id);
        
        if ($result) {
            // Enregistrer l'action dans les logs
            $this->logAction("Suppression d'un événement scolaire: " . $evenement['titre']);
            
            // Rediriger avec un message de succès
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=evenementsScolaires&success=1&message=' . urlencode('Événement supprimé avec succès'));
        } else {
            // Rediriger avec un message d'erreur
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=evenementsScolaires&error=1&message=' . urlencode('Erreur lors de la suppression de l\'événement'));
        }
        
        $mysqli->close();
        exit;
    }
    
    // Méthode pour récupérer les détails d'un événement (pour AJAX)
    public function getEvenementDetails() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }
        
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID d\'événement non spécifié']);
            exit;
        }
        
        $id = intval($_GET['id']);
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
            exit;
        }
        
        // Instancier le modèle
        require_once 'models/EvenementScolaire.php';
        $evenementModel = new EvenementScolaire($mysqli);
        
        // Récupérer les informations de l'événement
        $evenement = $evenementModel->getEvenementById($id);
        
        if (!$evenement) {
            echo json_encode(['success' => false, 'message' => 'Événement non trouvé']);
        } else {
            echo json_encode(['success' => true, 'data' => $evenement]);
        }
        
        $mysqli->close();
        exit;
    }
    
    // Méthode pour mettre à jour un événement via AJAX
    public function updateEvenement() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['event_id'])) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            exit;
        }
        
        $id = intval($_POST['event_id']);
        $titre = isset($_POST['event_title']) ? $_POST['event_title'] : '';
        $date_debut = isset($_POST['event_start']) ? $_POST['event_start'] : '';
        $date_fin = isset($_POST['event_end']) ? $_POST['event_end'] : '';
        $lieu = isset($_POST['event_location']) ? $_POST['event_location'] : '';
        $responsable = isset($_POST['event_responsible']) ? $_POST['event_responsible'] : '';
        $description = isset($_POST['event_description']) ? $_POST['event_description'] : '';
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
            exit;
        }
        
        // Instancier le modèle
        require_once 'models/EvenementScolaire.php';
        $evenementModel = new EvenementScolaire($mysqli);
        
        // Récupérer l'événement actuel pour obtenir la couleur
        $evenement = $evenementModel->getEvenementById($id);
        if (!$evenement) {
            echo json_encode(['success' => false, 'message' => 'Événement non trouvé']);
            exit;
        }
        
        $couleur = $evenement['couleur'];
        
        // Mettre à jour l'événement
        $result = $evenementModel->modifierEvenement($id, $titre, $description, $date_debut, $date_fin, $lieu, $responsable, $couleur);
        
        if ($result) {
            // Enregistrer l'action dans les logs
            $this->logAction("Mise à jour d'un événement scolaire via AJAX: " . $titre);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de l\'événement']);
        }
        
        $mysqli->close();
        exit;
    }

    
    public function gestionStock() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue
        require_once 'views/admin/gestionStock.php';
    }

    public function supprimerAchat() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }
            
            // Instancier le modèle
            require_once 'models/AchatFourniture.php';
            $achatModel = new AchatFourniture($mysqli);
            
            // Récupérer les informations de l'achat avant suppression pour le log
            $achat = $achatModel->getAchatById($id);
            
            // Supprimer l'achat
            $result = $achatModel->supprimerAchat($id);
            
            if ($result) {
                // Enregistrer l'action dans les logs
                $description = isset($achat['description']) ? $achat['description'] : 'Achat #' . $id;
                $this->logAction("Suppression d'un achat de fourniture: " . $description);
                
                // Rediriger avec un message de succès
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=achatFournitures&success=1&message=' . urlencode('Achat supprimé avec succès'));
            } else {
                // Rediriger avec un message d'erreur
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=achatFournitures&error=1&message=' . urlencode('Erreur lors de la suppression de l\'achat'));
            }
            
            $mysqli->close();
        } else {
            // Rediriger si aucun ID n'est fourni
            header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=achatFournitures&error=1&message=' . urlencode('ID d\'achat non spécifié'));
        }
        exit;
    }


        
    
    public function ajouterArticle() {
        // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
        if (!$this->isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $categorie = isset($_POST['categorie']) ? $_POST['categorie'] : '';
            $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 0;
            $prix_unitaire = isset($_POST['prix_unitaire']) ? floatval($_POST['prix_unitaire']) : 0;
            $date_ajout = isset($_POST['date_ajout']) ? $_POST['date_ajout'] : date('Y-m-d');
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }
            
            // Instancier le modèle
            require_once 'models/Stock.php';
            $articleModel = new Stock($mysqli);
            
            // Ajouter l'article
            $result = $articleModel->ajouterArticle($nom, $description, $categorie, $quantite, $prix_unitaire, $date_ajout);
            
            if ($result) {
                // Enregistrer l'action dans les logs
                $this->logAction("Ajout d'un article au stock: " . $nom);
                
                // Rediriger avec un message de succès
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=gestionStock&success=1&message=' . urlencode('Article ajouté avec succès'));
            } else {
                // Rediriger avec un message d'erreur
                header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=gestionStock&error=1&message=' . urlencode('Erreur lors de l\'ajout de l\'article'));
            }
            
            $mysqli->close();
            exit;
        } else {
            // Afficher le formulaire d'ajout
            require_once 'views/admin/ajout_article.php';
        }
    }

// Fonction pour enregistrer les actions des utilisateurs
public function logAction($action) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        return false;
    }
    
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur inconnu';
    $ip = $_SERVER['REMOTE_ADDR'];
    $action = $mysqli->real_escape_string($action);
    
    // Check if the table exists and has the correct structure
    $tableCheck = $mysqli->query("SHOW COLUMNS FROM system_logs LIKE 'action'");
    
    if ($tableCheck && $tableCheck->num_rows > 0) {
        // Column exists, proceed with insert
        $result = $mysqli->query("INSERT INTO system_logs (username, action, ip_address) 
                                VALUES ('$username', '$action', '$ip')");
    } else {
        // Try with the correct column name (check your table structure)
        $result = $mysqli->query("DESCRIBE system_logs");
        $columns = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            
            // If we found a column that might store the action description
            if (in_array('action_description', $columns)) {
                $result = $mysqli->query("INSERT INTO system_logs (username, action_description, ip_address) 
                                        VALUES ('$username', '$action', '$ip')");
            } else {
                // Create the table with the correct structure if it doesn't exist
                $mysqli->query("CREATE TABLE IF NOT EXISTS system_logs (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    username VARCHAR(255) NOT NULL,
                    action VARCHAR(255) NOT NULL,
                    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    ip_address VARCHAR(50) NOT NULL,
                    PRIMARY KEY (id)
                )");
                
                // Try the insert again
                $result = $mysqli->query("INSERT INTO system_logs (username, action, ip_address) 
                                        VALUES ('$username', '$action', '$ip')");
            }
        }
    }
    
    $mysqli->close();
      return isset($result) ? $result : false;
}

    public function reinscris() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['username'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        // Traitement du formulaire de réinscription
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterReinscription();
            return;
        }

        // Récupération des élèves inscrits l'année précédente pour réinscription
        $eleves_precedents = $this->eleveModel->getElevesAnnePrecedente();
        
        // Récupération des classes disponibles
        $classes = $this->classeModel->getAllClasses();
        
        // Récupération des sessions scolaires
        $sessions = $this->sessionscolaireModel->getAllSessions();

        // Inclure la vue de réinscription
        include 'views/admin/reinscris.php';
    }

    private function traiterReinscription() {
        if (isset($_POST['eleves_reinscription']) && is_array($_POST['eleves_reinscription'])) {
            $session_id = $_POST['session_scolaire'];
            $elevesReinscris = 0;
            $erreurs = [];

            foreach ($_POST['eleves_reinscription'] as $eleve_id) {
                $nouvelle_classe = $_POST['nouvelle_classe_' . $eleve_id] ?? null;
                
                if ($nouvelle_classe) {
                    try {
                        // Créer une nouvelle inscription pour l'élève
                        $result = $this->eleveModel->reinscrireEleve($eleve_id, $nouvelle_classe, $session_id);
                        
                        if ($result) {
                            $elevesReinscris++;
                            
                            // Log de l'action
                            $this->logAction("Réinscription élève ID: " . $eleve_id);
                        } else {
                            $erreurs[] = "Erreur lors de la réinscription de l'élève ID: " . $eleve_id;
                        }
                    } catch (Exception $e) {
                        $erreurs[] = "Erreur pour l'élève ID " . $eleve_id . ": " . $e->getMessage();
                    }
                }
            }

            // Redirection avec message de succès ou d'erreur
            if ($elevesReinscris > 0) {
                $_SESSION['success_message'] = $elevesReinscris . " élève(s) réinscrit(s) avec succès.";
                if (!empty($erreurs)) {
                    $_SESSION['error_message'] = implode('<br>', $erreurs);
                }
            } else {
                $_SESSION['error_message'] = "Aucune réinscription effectuée. " . implode('<br>', $erreurs);
            }

            header('Location: ' . BASE_URL . '/admin/reinscris');
            exit();
        }
    }

 public function ajoutUsers() {
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $role = isset($_POST['role']) ? $_POST['role'] : 'user';
            $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
            $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
            
            // Validate form data
            $errors = [];
            
            if (empty($username)) {
                $errors[] = "Le nom d'utilisateur est requis";
            }
            
            if (empty($email)) {
                $errors[] = "L'email est requis";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            }
            
            if (empty($password)) {
                $errors[] = "Le mot de passe est requis";
            } elseif (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }
            
            // If no errors, register the user
            if (empty($errors)) {
                // Check if username already exists
                if ($this->userModel->getUserByUsername($username)) {
                    $errors[] = "Ce nom d'utilisateur existe déjà";
                } 
                // Check if email already exists
                else if ($this->userModel->getUserByEmail($email)) {
                    $errors[] = "Cet email est déjà utilisé";
                } else {
                    // Default image path
                    $image = 'dist/img/default-avatar.png';
                    
                    // Handle image upload if provided
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = 'dist/img/users/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $new_filename = 'user_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                        $target_file = $upload_dir . $new_filename;
                        
                        // Check file type
                        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                        if (in_array($_FILES['image']['type'], $allowed_types)) {
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                                $image = $target_file;
                            }
                        }
                    }
                    
                    // Set password expiry days (default 90 days)
                    $password_expiry_days = 90;
                    
                    // Register the user with all fields
                    $user_id = $this->userModel->register(
                        $username, 
                        $password, 
                        $email, 
                        $role, 
                        $image, 
                        $telephone, 
                        $adresse, 
                        $password_expiry_days
                    );
                    
                    if ($user_id) {
                        // Log the registration
                        $this->logger->info("Nouvel utilisateur enregistré", ['username' => $username, 'role' => $role]);
                        
                        // Redirect to login page
                        header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=ajoutUsers&success=1&message=' . urlencode('Inscription réussie. Vous pouvez maintenant vous connecter.'));
                        exit;
                    } else {
                        $errors[] = "L'inscription a échoué. Veuillez réessayer.";
                    }
                }
            }
        }
        
        // Get available roles for the dropdown
        $roles = ['admin', 'comptable', 'prefet', 'directeur', 'directrice', 'enseignant', 'etudiant'];
          // Load the registration view
        require 'views/admin/ajout_users.php';
    }

    public function users() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }

        try {
            // Récupérer tous les utilisateurs
            $users = $this->userModel->getAll();
            
            // Logger l'action
            $this->logger->log($_SESSION['username'] ?? 'Utilisateur inconnu', 'Consultation de la liste des utilisateurs');
            
            // Inclure la vue
            include 'views/admin/users.php';
            
        } catch (Exception $e) {
            // Logger l'erreur
            $this->logger->log($_SESSION['username'] ?? 'Utilisateur inconnu', 'Erreur lors de la consultation des utilisateurs: ' . $e->getMessage());
            
            // Afficher une page d'erreur ou rediriger
            $error_message = "Une erreur est survenue lors du chargement des utilisateurs.";
            include 'views/error/error.php';
        }
    }


}
?>