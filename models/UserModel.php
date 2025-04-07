
<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM users ORDER BY username");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function add($username, $password, $email, $role, $telephone = null, $adresse = null) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, email, role, telephone, adresse, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssss", $username, $hashed_password, $email, $role, $telephone, $adresse);
        $success = $stmt->execute();
        $id = $success ? $this->db->insert_id : 0;
        $stmt->close();
        return $id;
    }

    public function update($id, $username, $email, $role, $telephone = null, $adresse = null) {
        $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ?, role = ?, telephone = ?, adresse = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssssi", $username, $email, $role, $telephone, $adresse, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updatePassword($id, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $current_date = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("UPDATE users SET password = ?, password_change_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $hashed_password, $current_date, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function verifyPassword($username, $password) {
        $user = $this->getByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function __destruct() {
        $this->db->close();
    }

    public function updateProfile($user_id, $username, $email, $password = null) {
        if ($password) {
            // Mettre à jour avec le nouveau mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
        } else {
            // Mettre à jour sans changer le mot de passe
            $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $username, $email, $user_id);
        }
        
        return $stmt->execute();
    }

    public function updateProfilePhoto($user_id, $photo_path) {
        $sql = "UPDATE users SET image = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $photo_path, $user_id);
        return $stmt->execute();
    }

    /**
     * Get the number of failed login attempts for a username and IP address
     * @param string $username The username
     * @param string $ip The IP address
     * @return int The number of failed login attempts
     */
    public function getFailedLoginAttempts($username, $ip) {
        // Get attempts within the last 30 minutes
        $timeLimit = date('Y-m-d H:i:s', strtotime('-30 minutes'));
        
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM failed_logins 
                                   WHERE (username = ? OR ip_address = ?) 
                                   AND attempt_date > ?");
        $stmt->bind_param("sss", $username, $ip, $timeLimit);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        
        return $row['count'];
    }

    /**
     * Add a failed login attempt to the database
     * @param string $username The username
     * @param string $ip The IP address
     * @return bool True if successful, false otherwise
     */
    public function addFailedLoginAttempt($username, $ip) {
        $stmt = $this->db->prepare("INSERT INTO failed_logins (username, ip_address, attempt_date) 
                                   VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $ip);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Reset failed login attempts for a username and IP address
     * @param string $username The username
     * @param string $ip The IP address
     * @return bool True if successful, false otherwise
     */
    public function resetFailedLoginAttempts($username, $ip) {
        $stmt = $this->db->prepare("DELETE FROM failed_logins 
                                   WHERE username = ? OR ip_address = ?");
        $stmt->bind_param("ss", $username, $ip);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Add an active session to the database
     * @param int $user_id The user ID
     * @param string $session_id The session ID
     * @param string $ip The IP address
     * @param string $user_agent The user agent
     * @return bool True if successful, false otherwise
     */
    public function addActiveSession($user_id, $session_id, $ip, $user_agent) {
        $stmt = $this->db->prepare("INSERT INTO active_sessions 
                                   (user_id, session_id, ip_address, user_agent, created_at, last_activity) 
                                   VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("isss", $user_id, $session_id, $ip, $user_agent);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Remove an active session from the database
     * @param string $session_id The session ID
     * @return bool True if successful, false otherwise
     */
    public function removeActiveSession($session_id) {
        $stmt = $this->db->prepare("DELETE FROM active_sessions WHERE session_id = ?");
        $stmt->bind_param("s", $session_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Log user activity
     * @param int $user_id The user ID
     * @param string $username The username
     * @param string $action The action description
     * @return bool True if successful, false otherwise
     */
    public function logActivity($user_id, $username, $action) {
        $stmt = $this->db->prepare("INSERT INTO logs (user_id, username, action, date) 
                                   VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $username, $action);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Authenticate a user with username and password
     * @param string $username The username
     * @param string $password The password
     * @return array|false User data if authentication successful, false otherwise
     */
    public function authenticate($username, $password) {
        // Prepare statement to prevent SQL injection
        $stmt = $this->db->prepare("SELECT id, username, email, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Remove password from the returned data for security
                unset($user['password']);
                return $user;
            }
        }
        
        $stmt->close();
        return false;
    }

    /**
     * Vérifie si un compte est bloqué
     * @param string $username Le nom d'utilisateur
     * @return bool True si le compte est bloqué, false sinon
     */
    public function isAccountLocked($username) {
        $stmt = $this->db->prepare("SELECT locked, lock_expiry FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Si le compte est verrouillé et que la date d'expiration n'est pas dépassée
            if ($user['locked'] == 1) {
                if ($user['lock_expiry'] === null) {
                    return true; // Verrouillage permanent
                }
                
                $expiryDate = new DateTime($user['lock_expiry']);
                $now = new DateTime();
                
                if ($now < $expiryDate) {
                    return true; // Toujours verrouillé
                } else {
                    // Déverrouiller le compte car la période est expirée
                    $this->unlockAccount($username);
                    return false;
                }
            }
        }
        
        $stmt->close();
        return false;
    }

    /**
     * Verrouille un compte
     * @param string $username Le nom d'utilisateur
     * @param string $expiry Date d'expiration du verrouillage (NULL pour permanent)
     * @return bool True si le verrouillage a réussi, false sinon
     */
    public function lockAccount($username, $expiry = null) {
        $stmt = $this->db->prepare("UPDATE users SET locked = 1, lock_expiry = ? WHERE username = ?");
        $stmt->bind_param("ss", $expiry, $username);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Déverrouille un compte
     * @param string $username Le nom d'utilisateur
     * @return bool True si le déverrouillage a réussi, false sinon
     */
    public function unlockAccount($username) {
        $stmt = $this->db->prepare("UPDATE users SET locked = 0, lock_expiry = NULL WHERE username = ?");
        $stmt->bind_param("s", $username);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Vérifie si un changement de mot de passe est requis pour l'utilisateur
     * @param int $userId ID de l'utilisateur
     * @return bool True si un changement de mot de passe est requis, false sinon
     */
    public function isPasswordChangeRequired($userId) {
        $stmt = $this->db->prepare("SELECT password_change_date, password_expiry_days FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Si la date de changement de mot de passe n'est pas définie, pas besoin de changer
            if ($user['password_change_date'] === null) {
                return false;
            }
            
            // Calculer la date d'expiration du mot de passe
            $changeDate = new DateTime($user['password_change_date']);
            $expiryDays = $user['password_expiry_days'] ?: 90; // Par défaut 90 jours
            $expiryDate = clone $changeDate;
            $expiryDate->modify("+{$expiryDays} days");
            
            $now = new DateTime();
            
            // Si la date actuelle est postérieure à la date d'expiration, un changement est requis
            return $now > $expiryDate;
        }
        
        $stmt->close();
        return false;
    }
    
    /**
     * Register a new user
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $role
     * @param string $image
     * @param string $telephone
     * @param string $adresse
     * @param int $password_expiry_days
     * @return int|bool User ID on success, false on failure
     */
    public function register($username, $password, $email, $role = 'user', $image = 'dist/img/default-avatar.png', $telephone = '', $adresse = '', $password_expiry_days = 90) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            return false;
        }
        
        // Check if username already exists
        $check_stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $check_stmt->close();
            $mysqli->close();
            return false;
        }
        $check_stmt->close();
        
        // Check if email already exists
        $check_email_stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $check_email_stmt->bind_param("s", $email);
        $check_email_stmt->execute();
        $check_email_result = $check_email_stmt->get_result();
        
        if ($check_email_result->num_rows > 0) {
            $check_email_stmt->close();
            $mysqli->close();
            return false;
        }
        $check_email_stmt->close();
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Set current date for created_at and password_change_date
        $current_date = date('Y-m-d H:i:s');
        
        // Default values
        $locked = 0;
        $lock_expiry = null;
        
        // Prepare the statement with all columns
        $stmt = $mysqli->prepare("
            INSERT INTO users (
                username, 
                password, 
                role, 
                created_at, 
                email, 
                image, 
                telephone, 
                adresse, 
                locked, 
                lock_expiry, 
                password_change_date, 
                password_expiry_days
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "ssssssssisii", 
            $username, 
            $hashed_password, 
            $role, 
            $current_date, 
            $email, 
            $image, 
            $telephone, 
            $adresse, 
            $locked, 
            $lock_expiry, 
            $current_date, 
            $password_expiry_days
        );
        
        // Execute the statement
        $result = $stmt->execute();
        
        // Get the ID of the newly created user
        $user_id = $mysqli->insert_id;
        
        $stmt->close();
        $mysqli->close();
        
        return $result ? $user_id : false;
    }

    /**
     * Get user by username
     * 
     * @param string $username
     * @return array|false User data or false if not found
     */
    public function getUserByUsername($username) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            return false;
        }
        
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            $mysqli->close();
            return false;
        }
        
        $user = $result->fetch_assoc();
        
        $stmt->close();
        $mysqli->close();
        
        return $user;
    }

    /**
     * Get user by email
     * 
     * @param string $email
     * @return array|false User data or false if not found
     */
    public function getUserByEmail($email) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            return false;
        }
        
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            $mysqli->close();
            return false;
        }
        
        $user = $result->fetch_assoc();
        
        $stmt->close();
        $mysqli->close();
        
        return $user;
    }

    /**
     * Get user by ID
     * 
     * @param int $id
     * @return array|false User data or false if not found
     */
    public function getUserById($id) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            return false;
        }
        
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            $mysqli->close();
            return false;
        }
        
        $user = $result->fetch_assoc();
        
        $stmt->close();
        $mysqli->close();
        
        return $user;
    }
}
?>

