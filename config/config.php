<?php
// Définir l'URL de base de l'application
define('BASE_URL', 'http://localhost/SGS/');

// Fonction d'auto-chargement des classes de modèles
spl_autoload_register(function ($class_name) {
    require 'models/' . $class_name . '.php';
});
?>

