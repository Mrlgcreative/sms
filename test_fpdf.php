<?php
// Script de diagnostic pour FPDF
echo "<h2>Diagnostic FPDF</h2>";

// Tester les chemins possibles pour FPDF
$fpdf_paths = [
    'lib/fpdf/fpdf.php',
    '../lib/fpdf/fpdf.php',
    'sms/lib/fpdf/fpdf.php',
    '../sms/lib/fpdf/fpdf.php',
    __DIR__ . '/lib/fpdf/fpdf.php',
    __DIR__ . '/../lib/fpdf/fpdf.php'
];

echo "<h3>Test des chemins FPDF :</h3>";
foreach ($fpdf_paths as $path) {
    if (file_exists($path)) {
        echo "<p style='color: green;'>✅ Trouvé : $path</p>";
        try {
            require_once $path;
            echo "<p style='color: green;'>✅ FPDF chargé avec succès depuis $path</p>";
            
            // Test de création d'un PDF simple
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(40, 10, 'Test PDF');
            echo "<p style='color: green;'>✅ Test de création PDF réussi</p>";
            break;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur lors du chargement : " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Non trouvé : $path</p>";
    }
}

echo "<h3>Informations PHP :</h3>";
echo "<p>Version PHP : " . phpversion() . "</p>";
echo "<p>Répertoire de travail : " . getcwd() . "</p>";
echo "<p>Script actuel : " . __FILE__ . "</p>";

// Vérifier les extensions PHP nécessaires
echo "<h3>Extensions PHP :</h3>";
$required_extensions = ['mbstring', 'gd'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✅ Extension $ext : installée</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Extension $ext : non installée (recommandée)</p>";
    }
}

// Lister les fichiers dans le répertoire lib
echo "<h3>Contenu du répertoire lib :</h3>";
$lib_paths = ['lib/', '../lib/', 'sms/lib/'];
foreach ($lib_paths as $lib_path) {
    if (is_dir($lib_path)) {
        echo "<p><strong>Répertoire $lib_path :</strong></p>";
        $files = scandir($lib_path);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<p>- $file</p>";
            }
        }
    }
}
?>
