<?php
// Test rapide des inclusions
try {
    echo "Test 1: Inclusion de config.php...<br>";
    require_once '../../config/config.php';
    echo "✅ config.php inclus avec succès<br>";
    echo "BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'NON DÉFINI') . "<br><br>";
    
    echo "Test 2: Inclusion de database.php...<br>";
    require_once '../../config/database.php';
    echo "✅ database.php inclus avec succès<br>";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NON DÉFINI') . "<br>";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NON DÉFINI') . "<br><br>";
    
    echo "Test 3: Connexion à la base de données...<br>";
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        echo "❌ Erreur de connexion: " . $mysqli->connect_error . "<br>";
    } else {
        echo "✅ Connexion réussie<br><br>";
    }
    
    echo "Test 4: Inclusion du modèle...<br>";
    require_once '../../models/AchatFourniture.php';
    echo "✅ Modèle AchatFourniture inclus avec succès<br>";
    
    $achatModel = new AchatFourniture($mysqli);
    echo "✅ Instance du modèle créée<br><br>";
    
    echo "Test 5: Récupération des données...<br>";
    $achats = $achatModel->getAllAchats();
    $totalDepenses = $achatModel->getTotalDepenses();
    echo "✅ Données récupérées avec succès<br>";
    echo "Nombre d'achats: " . count($achats) . "<br>";
    echo "Total dépenses: " . number_format($totalDepenses, 2) . " €<br><br>";
    
    echo "<strong style='color: green;'>🎉 TOUS LES TESTS RÉUSSIS !</strong><br>";
    echo "<a href='achatFourniture.php'>Aller à la page principale</a>";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
