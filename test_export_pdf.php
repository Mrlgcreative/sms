<?php
// Test simple de la fonction exportPaiementsPDF
session_start();
$_SESSION['user_id'] = 1; // Simuler une session utilisateur

// Inclure les configurations
require_once 'config/config.php';
require_once 'config/database.php';

// Tester le chargement de FPDF
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
        echo "Tentative de chargement de FPDF depuis : $path<br>";
        require_once $path;
        $fpdf_loaded = true;
        echo "FPDF chargé avec succès !<br>";
        break;
    } else {
        echo "Chemin non trouvé : $path<br>";
    }
}

if (!$fpdf_loaded) {
    die('Erreur: Bibliothèque FPDF non trouvée.');
}

// Tester la création d'un PDF simple
try {
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(277, 10, 'Test PDF - Export Paiements', 0, 1, 'C');
    
    echo "PDF créé avec succès !<br>";
    echo "La fonction exportPaiementsPDF devrait maintenant fonctionner.<br>";
    echo "<a href='index.php?controller=Admin&action=exportPaiementsPDF' target='_blank'>Tester l'export PDF</a>";
    
} catch (Exception $e) {
    echo "Erreur lors de la création du PDF : " . $e->getMessage();
}
?>
