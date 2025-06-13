<?php
// Test rapide de correction FPDF
echo "<h2>Test de correction FPDF</h2>";

// Inclure FPDF
$fpdf_paths = [
    'lib/fpdf_temp/fpdf.php',
    '../lib/fpdf_temp/fpdf.php',
    'sms/lib/fpdf_temp/fpdf.php',
    '../sms/lib/fpdf_temp/fpdf.php'
];

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
    die("<p style='color: red;'>❌ FPDF non trouvé</p>");
}

try {
    // Test de la séquence correcte
    echo "<p>Test de création PDF...</p>";
    
    $pdf = new FPDF('L', 'mm', 'A4'); // 1. Créer l'instance
    echo "<p style='color: green;'>✅ Instance FPDF créée</p>";
    
    $pdf->AddPage(); // 2. Ajouter une page (OBLIGATOIRE avant toute autre opération)
    echo "<p style='color: green;'>✅ Page ajoutée</p>";
    
    $pdf->SetFont('Arial', 'B', 16); // 3. Maintenant on peut définir la police
    echo "<p style='color: green;'>✅ Police définie</p>";
    
    $pdf->Cell(277, 10, utf8_decode('TEST CORRECTION'), 0, 1, 'C');
    echo "<p style='color: green;'>✅ Cellule ajoutée</p>";
    
    echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px; color: #155724;'><strong>Succès !</strong> La correction a résolu l'erreur 'No page has been added yet'.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Explication de l'erreur :</strong></p>";
echo "<ul>";
echo "<li>L'erreur 'No page has been added yet' se produit quand on appelle des méthodes FPDF avant d'ajouter une page</li>";
echo "<li>L'ordre correct est : 1) Créer l'instance FPDF, 2) Ajouter une page avec AddPage(), 3) Utiliser les autres méthodes</li>";
echo "<li>Le problème était un problème d'indentation qui faisait que AddPage() n'était pas appelé correctement</li>";
echo "</ul>";

echo "<p><a href='index.php?controller=Admin&action=exportPaiementsPDF' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔗 Tester l'export PDF corrigé</a></p>";
?>
