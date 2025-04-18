<?php
class Error {
    /**
     * Affiche la page d'erreur 404
     */
    public function notFound() {
        header("HTTP/1.0 404 Not Found");
        include 'views/errors/404.php';
        exit;
    }
    
    /**
     * Affiche une page d'erreur générique
     */
    public function serverError($message = 'Une erreur est survenue.') {
        header("HTTP/1.0 500 Internal Server Error");
        include 'views/errors/500.php';
        exit;
    }
    
    /**
     * Affiche une page d'accès refusé
     */
    public function forbidden() {
        header("HTTP/1.0 403 Forbidden");
        include 'views/errors/403.php';
        exit;
    }
}
?>