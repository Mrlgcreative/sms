<?php
/**
 * Fichier contenant les fonctions de sécurité pour l'application
 */

/**
 * Nettoie une chaîne de caractères pour éviter les injections SQL
 * @param string $input La chaîne à nettoyer
 * @return string La chaîne nettoyée
 */
function cleanInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Vérifie si une session est active et si l'utilisateur est connecté
 * @param string $role Le rôle requis pour accéder à la page (optionnel)
 * @return bool True si l'utilisateur est connecté et a le rôle requis, false sinon
 */
function isAuthenticated($role = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    if ($role !== null && $_SESSION['role'] !== $role) {
        return false;
    }
    
    return true;
}

/**
 * Génère un token CSRF pour protéger les formulaires
 * @return string Le token CSRF
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie si le token CSRF est valide
 * @param string $token Le token à vérifier
 * @return bool True si le token est valide, false sinon
 */
function validateCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    return true;
}

/**
 * Journalise une tentative d'accès non autorisée
 * @param string $page La page concernée
 * @param string $ip L'adresse IP de l'utilisateur
 * @param string $details Détails supplémentaires
 */
function logSecurityBreach($page, $ip, $details = '') {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $stmt = $db->prepare("INSERT INTO security_logs (page, ip_address, details, date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $page, $ip, $details);
    $stmt->execute();
    $stmt->close();
    $db->close();
}

/**
 * Vérifie si une adresse IP est bloquée
 * @param string $ip L'adresse IP à vérifier
 * @return bool True si l'IP est bloquée, false sinon
 */
function isIPBlocked($ip) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM blocked_ips WHERE ip_address = ? AND (expiry_date IS NULL OR expiry_date > NOW())");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    $db->close();
    
    return ($row['count'] > 0);
}

/**
 * Bloque une adresse IP
 * @param string $ip L'adresse IP à bloquer
 * @param string $reason La raison du blocage
 * @param string $expiry_date Date d'expiration du blocage (NULL pour permanent)
 */
function blockIP($ip, $reason, $expiry_date = null) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $stmt = $db->prepare("INSERT INTO blocked_ips (ip_address, reason, block_date, expiry_date) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("sss", $ip, $reason, $expiry_date);
    $stmt->execute();
    $stmt->close();
    $db->close();
}

/**
 * Vérifie si un mot de passe respecte la politique de sécurité
 * @param string $password Le mot de passe à vérifier
 * @return array Tableau contenant le résultat (true/false) et un message d'erreur si nécessaire
 */
function validatePassword($password) {
    $result = [
        'valid' => true,
        'message' => ''
    ];
    
    // Vérifier la longueur minimale (8 caractères)
    if (strlen($password) < 8) {
        $result['valid'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins 8 caractères.";
        return $result;
    }
    
    // Vérifier la présence d'au moins une lettre majuscule
    if (!preg_match('/[A-Z]/', $password)) {
        $result['valid'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins une lettre majuscule.";
        return $result;
    }
    
    // Vérifier la présence d'au moins une lettre minuscule
    if (!preg_match('/[a-z]/', $password)) {
        $result['valid'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins une lettre minuscule.";
        return $result;
    }
    
    // Vérifier la présence d'au moins un chiffre
    if (!preg_match('/[0-9]/', $password)) {
        $result['valid'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins un chiffre.";
        return $result;
    }
    
    // Vérifier la présence d'au moins un caractère spécial
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $result['valid'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins un caractère spécial.";
        return $result;
    }
    
    return $result;
}

/**
 * Vérifie si la session actuelle a expiré
 * @param int $maxLifetime Durée maximale d'inactivité en secondes (par défaut 30 minutes)
 * @return bool True si la session a expiré, false sinon
 */
function isSessionExpired($maxLifetime = 1800) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
        return false;
    }
    
    if ((time() - $_SESSION['last_activity']) > $maxLifetime) {
        return true;
    }
    
    // Mettre à jour le timestamp de dernière activité
    $_SESSION['last_activity'] = time();
    return false;
}

/**
 * Valide un email
 * @param string $email L'email à valider
 * @return bool True si l'email est valide, false sinon
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un nombre entier
 * @param mixed $value La valeur à valider
 * @param int $min Valeur minimale (optionnel)
 * @param int $max Valeur maximale (optionnel)
 * @return bool True si la valeur est un entier valide, false sinon
 */
function validateInteger($value, $min = null, $max = null) {
    if (!is_numeric($value) || intval($value) != $value) {
        return false;
    }
    
    $value = intval($value);
    
    if ($min !== null && $value < $min) {
        return false;
    }
    
    if ($max !== null && $value > $max) {
        return false;
    }
    
    return true;
}

/**
 * Valide une date
 * @param string $date La date à valider (format Y-m-d)
 * @return bool True si la date est valide, false sinon
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Valide un numéro de téléphone
 * @param string $phone Le numéro de téléphone à valider
 * @return bool True si le numéro est valide, false sinon
 */
function validatePhone($phone) {
    // Format international avec ou sans +
    return preg_match('/^\+?[0-9]{10,15}$/', $phone) === 1;
}