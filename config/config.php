<?php
// Définir l'URL de base de l'application
define('BASE_URL', 'http://localhost/sms/');

// Fonction d'auto-chargement des classes de modèles
spl_autoload_register(function ($class_name) {
    // Vérifier si la classe ZipArchive est demandée et ne pas tenter de la charger
    if ($class_name === 'ZipArchive') {
        return;
    }
    $file = 'models/' . $class_name . '.php';
    if (file_exists($file)) {
        require $file;
    } else {
        // Optionnel: log l'erreur ou lance une exception si le fichier modèle n'est pas trouvé
        // error_log("Fichier modèle non trouvé : " . $file);
    }
});
?>

