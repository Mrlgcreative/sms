<?php
// Script de test pour la fonctionnalité de réinscription
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>Test de la fonctionnalité de réinscription</h2>\n";

// Test 1: Vérifier la connexion à la base de données
echo "<h3>1. Test de connexion à la base de données</h3>\n";
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    echo "❌ Erreur de connexion: " . $mysqli->connect_error . "\n";
} else {
    echo "✅ Connexion à la base de données réussie\n";
}

// Test 2: Vérifier que les tables nécessaires existent
echo "<h3>2. Vérification des tables</h3>\n";
$tables_requises = ['eleves', 'classes', 'sessions_scolaires', 'parents'];
foreach ($tables_requises as $table) {
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Table '$table' existe\n";
    } else {
        echo "❌ Table '$table' manquante\n";
    }
}

// Test 3: Vérifier les modèles
echo "<h3>3. Test des modèles</h3>\n";
try {
    require_once 'models/EleveModel.php';
    require_once 'models/ClasseModel.php';
    require_once 'models/SessionScolaireModel.php';
    
    $eleveModel = new EleveModel();
    $classeModel = new ClasseModel();
    $sessionModel = new SessionScolaireModel();
    
    echo "✅ Tous les modèles chargés avec succès\n";
    
    // Test des méthodes
    $classes = $classeModel->getAllClasses();
    echo "✅ Récupération des classes: " . count($classes) . " classe(s) trouvée(s)\n";
    
    $sessions = $sessionModel->getAllSessions();
    echo "✅ Récupération des sessions: " . count($sessions) . " session(s) trouvée(s)\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du chargement des modèles: " . $e->getMessage() . "\n";
}

// Test 4: Vérifier la structure de la table eleves
echo "<h3>4. Structure de la table élèves</h3>\n";
$result = $mysqli->query("DESCRIBE eleves");
if ($result) {
    echo "✅ Colonnes de la table élèves:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "❌ Impossible de décrire la table élèves\n";
}

// Test 5: Vérifier les données de test
echo "<h3>5. Données de test disponibles</h3>\n";
$count_eleves = $mysqli->query("SELECT COUNT(*) as count FROM eleves")->fetch_assoc()['count'];
$count_classes = $mysqli->query("SELECT COUNT(*) as count FROM classes")->fetch_assoc()['count'];
$count_sessions = $mysqli->query("SELECT COUNT(*) as count FROM sessions_scolaires")->fetch_assoc()['count'];

echo "✅ Nombre d'élèves: $count_eleves\n";
echo "✅ Nombre de classes: $count_classes\n";
echo "✅ Nombre de sessions scolaires: $count_sessions\n";

// Test 6: Simulation d'une recherche d'élèves de l'année précédente
echo "<h3>6. Test de recherche d'élèves pour réinscription</h3>\n";
try {
    $query = "SELECT e.*, c.niveau as classe_actuelle 
              FROM eleves e 
              LEFT JOIN classes c ON e.classe_id = c.id 
              LIMIT 5";
    $result = $mysqli->query($query);
    
    if ($result && $result->num_rows > 0) {
        echo "✅ Élèves trouvés pour test de réinscription:\n";
        while ($eleve = $result->fetch_assoc()) {
            echo "  - " . htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) . 
                 " (Classe: " . htmlspecialchars($eleve['classe_actuelle'] ?? 'Non assignée') . ")\n";
        }
    } else {
        echo "⚠️ Aucun élève trouvé pour le test de réinscription\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur lors de la recherche d'élèves: " . $e->getMessage() . "\n";
}

$mysqli->close();

echo "<h3>Résumé</h3>\n";
echo "✅ Le système de réinscription est prêt à être testé.\n";
echo "📝 Pour tester complètement, visitez: <a href='" . BASE_URL . "index.php?controller=Admin&action=reinscris'>Page de réinscription</a>\n";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
h2, h3 { color: #333; }
pre { background: #fff; padding: 10px; border-radius: 5px; border-left: 4px solid #007cba; }
</style>
