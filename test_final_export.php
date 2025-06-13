<?php
// Script de test final pour l'export PDF
session_start();

// Simuler une session utilisateur admin
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'admin';

// Inclure les configurations
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>Test de la fonction exportPaiementsPDF</h2>";

// Lien pour tester l'export
echo "<p><a href='index.php?controller=Admin&action=exportPaiementsPDF' target='_blank' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔗 Tester l'export PDF</a></p>";

// Tester le chargement de FPDF
echo "<h3>Test du chargement FPDF :</h3>";
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
        echo "<p style='color: green;'>✅ Chemin trouvé : $path</p>";
        try {
            require_once $path;
            $fpdf_loaded = true;
            echo "<p style='color: green;'>✅ FPDF chargé avec succès !</p>";
            break;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur lors du chargement : " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Chemin non trouvé : $path</p>";
    }
}

if ($fpdf_loaded) {
    echo "<h3>Test de création PDF :</h3>";
    try {
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(277, 10, utf8_decode('TEST DE LA FONCTION PDF'), 0, 1, 'C');
        echo "<p style='color: green;'>✅ Test de création PDF réussi !</p>";
        echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px; color: #155724;'><strong>Succès !</strong> La fonction exportPaiementsPDF devrait maintenant fonctionner correctement.</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur lors de la création du PDF : " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px; color: #721c24;'><strong>Erreur !</strong> Impossible de charger FPDF.</p>";
}

// Test de connexion à la base de données
echo "<h3>Test de connexion à la base de données :</h3>";
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
    }
    echo "<p style='color: green;'>✅ Connexion à la base de données réussie !</p>";
    
    // Test de la requête des paiements
    $query = "SELECT COUNT(*) as total FROM paiements_frais";
    $result = $mysqli->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p style='color: green;'>✅ Requête paiements réussie. Nombre de paiements : " . $row['total'] . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Table paiements_frais non trouvée ou vide</p>";
    }
    
    $mysqli->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur de base de données : " . $e->getMessage() . "</p>";
}

echo "<h3>Instructions :</h3>";
echo "<ol>";
echo "<li>Cliquez sur le lien ci-dessus pour tester l'export PDF</li>";
echo "<li>Le PDF devrait se télécharger automatiquement</li>";
echo "<li>Si le téléchargement ne fonctionne pas, vérifiez la console du navigateur pour des erreurs</li>";
echo "<li>Assurez-vous que votre navigateur autorise les téléchargements</li>";
echo "</ol>";
?>
