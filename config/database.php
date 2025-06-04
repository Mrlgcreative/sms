<?php
// Informations de connexion à la base de données
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'college1');
}

// Connexion à la base de données
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($connection->connect_error) {
            die('Connection failed: ' . $connection->connect_error);
        }

        return $connection;
    }
}
?>
