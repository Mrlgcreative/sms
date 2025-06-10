<?php
// Test de connexion et de la table achats_fournitures
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>Test de connexion à la base de données</h2>";

// Test de connexion
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    echo "<p style='color: red;'>❌ Erreur de connexion: " . $mysqli->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Connexion réussie à la base de données '" . DB_NAME . "'</p>";
}

// Vérifier si la table existe
$table_check = $mysqli->query("SHOW TABLES LIKE 'achats_fournitures'");
if ($table_check->num_rows > 0) {
    echo "<p style='color: green;'>✅ Table 'achats_fournitures' existe</p>";
    
    // Compter les enregistrements
    $count_result = $mysqli->query("SELECT COUNT(*) as count FROM achats_fournitures");
    $count_row = $count_result->fetch_assoc();
    echo "<p>📊 Nombre d'enregistrements: " . $count_row['count'] . "</p>";
    
} else {
    echo "<p style='color: orange;'>⚠️ Table 'achats_fournitures' n'existe pas</p>";
    echo "<p>Création de la table...</p>";
    
    // Créer la table
    $create_table_sql = "
    CREATE TABLE IF NOT EXISTS achats_fournitures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date_achat DATE NOT NULL,
        fournisseur VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        quantite INT NOT NULL,
        montant DECIMAL(10, 2) NOT NULL,
        facture_ref VARCHAR(100),
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($mysqli->query($create_table_sql)) {
        echo "<p style='color: green;'>✅ Table 'achats_fournitures' créée avec succès</p>";
        
        // Ajouter quelques données d'exemple
        $sample_data = [
            ['2025-06-01', 'Papeterie Centrale', 'Stylos et cahiers', 50, 125.00, 'F001'],
            ['2025-06-02', 'Bureau Express', 'Matériel informatique', 5, 450.00, 'F002'],
            ['2025-06-03', 'Fournitures Scolaires', 'Produits d\'entretien', 20, 85.50, 'F003']
        ];
        
        $insert_stmt = $mysqli->prepare("INSERT INTO achats_fournitures (date_achat, fournisseur, description, quantite, montant, facture_ref) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($sample_data as $data) {
            $insert_stmt->bind_param("sssids", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5]);
            $insert_stmt->execute();
        }
        
        echo "<p style='color: green;'>✅ Données d'exemple ajoutées</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Erreur lors de la création de la table: " . $mysqli->error . "</p>";
    }
}

// Test du modèle
echo "<h2>Test du modèle AchatFourniture</h2>";
require_once 'models/AchatFourniture.php';

try {
    $achatModel = new AchatFourniture($mysqli);
    $achats = $achatModel->getAllAchats();
    $totalDepenses = $achatModel->getTotalDepenses();
    
    echo "<p style='color: green;'>✅ Modèle AchatFourniture fonctionne</p>";
    echo "<p>📊 Nombre d'achats: " . count($achats) . "</p>";
    echo "<p>💰 Total des dépenses: " . number_format($totalDepenses, 2) . " €</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur avec le modèle: " . $e->getMessage() . "</p>";
}

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Connexion Achat Fournitures</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        h2 { 
            color: #333; 
            border-bottom: 2px solid #007bff; 
            padding-bottom: 10px; 
        }
        p { 
            padding: 8px; 
            margin: 5px 0; 
            border-radius: 4px; 
        }
    </style>
</head>
<body>
    <h1>🧪 Test de connexion - Module Achat Fournitures</h1>
    <p><a href="views/comptable/achatFourniture.php" style="color: #007bff;">🔗 Aller à la page Achat Fournitures</a></p>
</body>
</html>
