<?php
// Script de test pour le contrôleur DirecteurEtude
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulation d'une session pour les tests
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'test_directeur';
$_SESSION['role'] = 'directeur_etude';
$_SESSION['section'] = 'secondaire';

// Inclure les fichiers nécessaires
require_once 'config/config.php';
require_once 'config/database.php';

// Test de chargement du contrôleur
try {
    require_once 'controllers/DirecteurEtude.php';
    echo "✓ Contrôleur DirecteurEtude chargé avec succès\n";
    
    // Test d'instantiation du contrôleur
    $directeurEtude = new DirecteurEtude();
    echo "✓ Instance du contrôleur créée avec succès\n";
    
    // Test des méthodes publiques ajoutées
    $methodsToTest = [
        'voirClasse',
        'elevesClasse', 
        'voirCours',
        'examens',
        'resultatsScolaires',
        'programmesScolaires',
        'communications'
    ];
    
    $reflection = new ReflectionClass($directeurEtude);
    
    foreach ($methodsToTest as $method) {
        if ($reflection->hasMethod($method)) {
            echo "✓ Méthode '$method' trouvée\n";
        } else {
            echo "✗ Méthode '$method' non trouvée\n";
        }
    }
    
    echo "\n=== Test des méthodes privées utilitaires ===\n";
    
    $privateMethods = [
        'calculateClasseAverage',
        'calculateAttendanceRate',
        'calculateCoursAverage',
        'calculateSuccessRate',
        'getExamensByCours',
        'getExamensWithFilters',
        'getResultatsWithFilters',
        'getProgrammesWithFilters',
        'getCommunicationsWithFilters',
        'getAnneesScolaires',
        'getNiveauxDisponibles'
    ];
    
    foreach ($privateMethods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "✓ Méthode privée '$method' trouvée\n";
        } else {
            echo "✗ Méthode privée '$method' non trouvée\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Erreur lors du test : " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test de connexion à la base de données ===\n";
try {
    $connection = getDBConnection();
    echo "✓ Connexion à la base de données réussie\n";
    $connection->close();
} catch (Exception $e) {
    echo "✗ Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
}
?>
