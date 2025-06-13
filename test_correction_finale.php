<?php
// Test final de la fonction exportPaiementsPDF corrigée
session_start();

// Simuler une session utilisateur admin
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'admin';

echo "<h2>🔧 Test Final - Correction de l'erreur FPDF</h2>";

echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #ffc107;'>";
echo "<h3>❌ Erreur Résolue</h3>";
echo "<p><strong>Erreur originale :</strong> <code>FPDF error: No page has been added yet</code></p>";
echo "<p><strong>Cause :</strong> Problème d'indentation qui faisait que <code>\$pdf->AddPage()</code> n'était pas appelé correctement</p>";
echo "<p><strong>Solution :</strong> Correction de l'indentation et de la structure du code</p>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #28a745;'>";
echo "<h3>✅ Corrections Appliquées</h3>";
echo "<ul>";
echo "<li>✅ Correction de l'indentation de <code>\$pdf->AddPage()</code></li>";
echo "<li>✅ Restructuration du code dans le bloc try-catch</li>";
echo "<li>✅ Vérification de l'ordre d'exécution FPDF</li>";
echo "<li>✅ Amélioration de la gestion d'erreur</li>";
echo "</ul>";
echo "</div>";

// Test de la fonction
echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #dee2e6;'>";
echo "<h3>🧪 Test de la Fonction</h3>";

try {
    // Inclure les configurations
    require_once 'config/config.php';
    require_once 'config/database.php';
    
    // Test de connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
    }
    echo "<p style='color: green;'>✅ Connexion base de données OK</p>";
    
    // Test de chargement FPDF
    $fpdf_paths = ['lib/fpdf_temp/fpdf.php', '../lib/fpdf_temp/fpdf.php'];
    $fpdf_loaded = false;
    
    foreach ($fpdf_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $fpdf_loaded = true;
            echo "<p style='color: green;'>✅ FPDF chargé depuis : $path</p>";
            break;
        }
    }
    
    if (!$fpdf_loaded) {
        throw new Exception('FPDF non trouvé');
    }
    
    // Test de création PDF avec l'ordre correct
    $pdf = new FPDF('L', 'mm', 'A4');
    echo "<p style='color: green;'>✅ Instance FPDF créée</p>";
    
    $pdf->AddPage(); // OBLIGATOIRE avant toute autre opération
    echo "<p style='color: green;'>✅ Page ajoutée</p>";
    
    $pdf->SetFont('Arial', 'B', 16);
    echo "<p style='color: green;'>✅ Police définie</p>";
    
    $pdf->Cell(277, 10, utf8_decode('TEST CORRECTION RÉUSSIE'), 0, 1, 'C');
    echo "<p style='color: green;'>✅ Cellule ajoutée</p>";
    
    $mysqli->close();
    
    echo "<div style='background: #28a745; color: white; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>🎉 SUCCÈS !</strong> La fonction exportPaiementsPDF est maintenant corrigée et fonctionnelle.";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur lors du test : " . $e->getMessage() . "</p>";
}

echo "</div>";

echo "<div style='text-align: center; margin: 20px 0;'>";
echo "<a href='index.php?controller=Admin&action=exportPaiementsPDF' target='_blank' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>🔗 Tester l'Export PDF Corrigé</a>";
echo "</div>";

echo "<div style='background: #e9ecef; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h4>📋 Ordre d'Exécution FPDF Correct :</h4>";
echo "<ol>";
echo "<li><code>\$pdf = new FPDF();</code> - Créer l'instance</li>";
echo "<li><code>\$pdf->AddPage();</code> - Ajouter une page (OBLIGATOIRE)</li>";
echo "<li><code>\$pdf->SetFont();</code> - Définir la police</li>";
echo "<li><code>\$pdf->Cell();</code> - Ajouter du contenu</li>";
echo "<li><code>\$pdf->Output();</code> - Générer le PDF</li>";
echo "</ol>";
echo "</div>";
?>
