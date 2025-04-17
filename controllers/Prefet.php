<?php
class Prefet {
    
    // Afficher la page d'accueil du préfet
    public function accueil() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue du tableau de bord
        require_once 'views/prefet/accueil.php';
    }
    
    // Afficher la liste des élèves du secondaire
    public function eleves() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des élèves
        require_once 'views/prefet/eleves.php';
    }
    
    // Afficher la liste des professeurs
    public function professeurs() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des professeurs
        require_once 'views/prefet/professeurs.php';
    }
    
    // Afficher la liste des classes
    public function classes() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des classes
        require_once 'views/prefet/classes.php';
    }
    
    // Afficher la liste des cours
    public function cours() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des cours
        require_once 'views/prefet/cours.php';
    }
    
    // Afficher la page des événements scolaires
    public function evenementsScolaires() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des événements scolaires
        require_once 'views/prefet/evenementsScolaires.php';
    }
    
    // Ajouter un nouvel événement scolaire
    public function ajouterEvenement() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
            $type = isset($_POST['type']) ? trim($_POST['type']) : '';
            $date_debut = isset($_POST['date_debut']) ? trim($_POST['date_debut']) : '';
            $date_fin = isset($_POST['date_fin']) ? trim($_POST['date_fin']) : '';
            $classe = isset($_POST['classe']) && !empty($_POST['classe']) ? (int)$_POST['classe'] : null;
            $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            
            // Valider les données
            if (empty($titre) || empty($type) || empty($date_debut) || empty($date_fin) || empty($lieu)) {
                $_SESSION['error_message'] = "Tous les champs obligatoires doivent être remplis.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Vérifier que la date de fin est après la date de début
            if (strtotime($date_fin) <= strtotime($date_debut)) {
                $_SESSION['error_message'] = "La date de fin doit être postérieure à la date de début.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Préparer la requête SQL
            $query = "INSERT INTO evenements_scolaires (titre, type, date_debut, date_fin, classe_id, lieu, description) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sssssss", $titre, $type, $date_debut, $date_fin, $classe_id, $lieu, $description);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "L'événement a été ajouté avec succès.";
                } else {
                    $_SESSION['error_message'] = "Erreur lors de l'ajout de l'événement: " . $stmt->error;
                }
                
                $stmt->close();
            } else {
                $_SESSION['error_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
            }
            
            $mysqli->close();
            
            // Rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        } else {
            // Si le formulaire n'a pas été soumis, rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        }
    }
    
    // Modifier un événement scolaire existant
    public function modifierEvenement() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
            $type = isset($_POST['type']) ? trim($_POST['type']) : '';
            $date_debut = isset($_POST['date_debut']) ? trim($_POST['date_debut']) : '';
            $date_fin = isset($_POST['date_fin']) ? trim($_POST['date_fin']) : '';
            $classe = isset($_POST['classe']) && !empty($_POST['classe']) ? (int)$_POST['classe'] : null;
            $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            
            // Valider les données
            if ($id <= 0 || empty($titre) || empty($type) || empty($date_debut) || empty($date_fin) || empty($lieu)) {
                $_SESSION['error_message'] = "Tous les champs obligatoires doivent être remplis.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Vérifier que la date de fin est après la date de début
            if (strtotime($date_fin) <= strtotime($date_debut)) {
                $_SESSION['error_message'] = "La date de fin doit être postérieure à la date de début.";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
                exit;
            }
            
            // Préparer la requête SQL
            $query = "UPDATE evenements_scolaires 
                      SET titre = ?, type = ?, date_debut = ?, date_fin = ?, classe = ?, lieu = ?, description = ? 
                      WHERE id = ?";
            
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sssssssi", $titre, $type, $date_debut, $date_fin, $classe, $lieu, $description, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "L'événement a été modifié avec succès.";
                } else {
                    $_SESSION['error_message'] = "Erreur lors de la modification de l'événement: " . $stmt->error;
                }
                
                $stmt->close();
            } else {
                $_SESSION['error_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
            }
            
            $mysqli->close();
            
            // Rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        } else {
            // Si le formulaire n'a pas été soumis, rediriger vers la page des événements
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        }
    }
    
    // Supprimer un événement scolaire
    public function supprimerEvenement() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Récupérer l'ID de l'événement à supprimer
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $_SESSION['error_message'] = "ID d'événement invalide.";
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        }
        
        // Connexion à la base de données
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $_SESSION['error_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
            exit;
        }
        
        // Préparer la requête SQL
        $query = "DELETE FROM evenements_scolaires WHERE id = ?";
        
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "L'événement a été supprimé avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la suppression de l'événement: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
        }
        
        $mysqli->close();
        
        // Rediriger vers la page des événements
        header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=evenementsScolaires');
        exit;
    }
    
    // Afficher la page de gestion des absences
    public function absences() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue des absences
        require_once 'views/prefet/absences.php';
    }
    
    // Afficher la page de gestion de la discipline
    public function discipline() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue de la discipline
        require_once 'views/prefet/discipline.php';
    }
    
    // Afficher le profil du préfet
    public function profil() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue du profil
        require_once 'views/prefet/profil.php';
    }
    
    public function voirEleve() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si l'ID de l'élève est fourni
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=eleves');
            exit;
        }
        
        // Charger la vue du profil de l'élève
        require 'views/prefet/voirEleve.php';
    }
    
    /**
     * Ajoute une nouvelle absence d'élève
     */
    public function ajouterAbsence() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
            $date_absence = isset($_POST['date_absence']) ? $_POST['date_absence'] : '';
            $motif = isset($_POST['motif']) ? $_POST['motif'] : '';
            $justifiee = isset($_POST['justifiee']) ? 1 : 0;
            
            // Validation des données
            $errors = [];
            
            if ($eleve_id <= 0) {
                $errors[] = "Veuillez sélectionner un élève valide.";
            }
            
            if (empty($date_absence)) {
                $errors[] = "La date d'absence est requise.";
            } else {
                // Convertir la date du format français (dd/mm/yyyy) au format MySQL (yyyy-mm-dd)
                $date_parts = explode('/', $date_absence);
                if (count($date_parts) === 3) {
                    $date_absence = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
                } else {
                    $errors[] = "Format de date invalide.";
                }
            }
            
            // Si pas d'erreurs, insérer l'absence dans la base de données
            if (empty($errors)) {
                $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }
                
                // Préparer la requête d'insertion
                $query = "INSERT INTO absences (eleve_id, date_absence, motif, justifiee) VALUES (?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("issi", $eleve_id, $date_absence, $motif, $justifiee);
                    
                    if ($stmt->execute()) {
                        // Rediriger avec un message de succès
                        $_SESSION['flash_message'] = "L'absence a été ajoutée avec succès.";
                        $_SESSION['flash_type'] = "success";
                    } else {
                        // Erreur lors de l'insertion
                        $_SESSION['flash_message'] = "Erreur lors de l'ajout de l'absence: " . $stmt->error;
                        $_SESSION['flash_type'] = "danger";
                    }
                    
                    $stmt->close();
                } else {
                    $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $mysqli->close();
            } else {
                // Stocker les erreurs dans la session
                $_SESSION['flash_message'] = implode("<br>", $errors);
                $_SESSION['flash_type'] = "danger";
            }
            
            // Rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        }
    }
    
    /**
     * Modifie une absence existante
     */
    public function modifierAbsence() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $absence_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $motif = isset($_POST['motif']) ? $_POST['motif'] : '';
            $justifiee = isset($_POST['justifiee']) ? 1 : 0;
            
            // Validation des données
            if ($absence_id <= 0) {
                $_SESSION['flash_message'] = "ID d'absence invalide.";
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
                exit;
            }
            
            // Mettre à jour l'absence dans la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
                exit;
            }
            
            // Préparer la requête de mise à jour
            $query = "UPDATE absences SET motif = ?, justifiee = ? WHERE id = ?";
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sii", $motif, $justifiee, $absence_id);
                
                if ($stmt->execute()) {
                    // Rediriger avec un message de succès
                    $_SESSION['flash_message'] = "L'absence a été modifiée avec succès.";
                    $_SESSION['flash_type'] = "success";
                } else {
                    // Erreur lors de la mise à jour
                    $_SESSION['flash_message'] = "Erreur lors de la modification de l'absence: " . $stmt->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $mysqli->close();
            
            // Rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        }
    }
    
    /**
     * Supprime une absence
     */
    public function supprimerAbsence() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider l'ID de l'absence
            $absence_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            // Validation des données
            if ($absence_id <= 0) {
                $_SESSION['flash_message'] = "ID d'absence invalide.";
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
                exit;
            }
            
            // Supprimer l'absence de la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
                exit;
            }
            
            // Préparer la requête de suppression
            $query = "DELETE FROM absences WHERE id = ?";
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("i", $absence_id);
                
                if ($stmt->execute()) {
                    // Rediriger avec un message de succès
                    $_SESSION['flash_message'] = "L'absence a été supprimée avec succès.";
                    $_SESSION['flash_type'] = "success";
                } else {
                    // Erreur lors de la suppression
                    $_SESSION['flash_message'] = "Erreur lors de la suppression de l'absence: " . $stmt->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $mysqli->close();
            
            // Rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page des absences
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=absences');
            exit;
        }
    }
    
    public function ajouterIncident() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $eleve_id = isset($_POST['eleve_id']) ? intval($_POST['eleve_id']) : 0;
            $date_incident = isset($_POST['date_incident']) ? $_POST['date_incident'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $sanction = isset($_POST['sanction']) ? $_POST['sanction'] : '';
            $statut = isset($_POST['statut']) ? $_POST['statut'] : 'En cours';
            
            // Convertir la date au format MySQL (YYYY-MM-DD)
            $date_formattee = date('Y-m-d', strtotime(str_replace('/', '-', $date_incident)));
            
            // Validation des données
            $erreurs = [];
            
            if (empty($eleve_id)) {
                $erreurs[] = "L'élève est obligatoire";
            }
            
            if (empty($date_incident)) {
                $erreurs[] = "La date de l'incident est obligatoire";
            }
            
            if (empty($description)) {
                $erreurs[] = "La description est obligatoire";
            }
            
            // Si pas d'erreurs, insérer dans la base de données
            if (empty($erreurs)) {
                // Connexion à la base de données
                $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }
                
                // Préparer la requête d'insertion
                $query = "INSERT INTO incidents_disciplinaires (eleve_id, date_incident, description, sanction, statut) 
                          VALUES (?, ?, ?, ?, ?)";
                
                $stmt = $mysqli->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("issss", $eleve_id, $date_formattee, $description, $sanction, $statut);
                    
                    if ($stmt->execute()) {
                        // Succès
                        $_SESSION['flash_message'] = "L'incident disciplinaire a été ajouté avec succès.";
                        $_SESSION['flash_type'] = "success";
                    } else {
                        // Erreur
                        $_SESSION['flash_message'] = "Erreur lors de l'ajout de l'incident: " . $stmt->error;
                        $_SESSION['flash_type'] = "danger";
                    }
                    
                    $stmt->close();
                } else {
                    // Erreur de préparation de la requête
                    $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $mysqli->close();
            } else {
                // Afficher les erreurs
                $_SESSION['flash_message'] = "Erreurs dans le formulaire: " . implode(", ", $erreurs);
                $_SESSION['flash_type'] = "danger";
            }
            
            // Rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        }
    }
    
    public function modifierIncident() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données du formulaire
            $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $sanction = isset($_POST['sanction']) ? $_POST['sanction'] : '';
            $statut = isset($_POST['statut']) ? $_POST['statut'] : 'En cours';
            
            // Validation des données
            $erreurs = [];
            
            if (empty($incident_id)) {
                $erreurs[] = "ID d'incident invalide";
            }
            
            if (empty($description)) {
                $erreurs[] = "La description est obligatoire";
            }
            
            // Si pas d'erreurs, mettre à jour dans la base de données
            if (empty($erreurs)) {
                // Connexion à la base de données
                $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }
                
                // Préparer la requête de mise à jour
                $query = "UPDATE incidents_disciplinaires 
                          SET description = ?, sanction = ?, statut = ? 
                          WHERE id = ?";
                
                $stmt = $mysqli->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("sssi", $description, $sanction, $statut, $incident_id);
                    
                    if ($stmt->execute()) {
                        // Succès
                        $_SESSION['flash_message'] = "L'incident disciplinaire a été modifié avec succès.";
                        $_SESSION['flash_type'] = "success";
                    } else {
                        // Erreur
                        $_SESSION['flash_message'] = "Erreur lors de la modification de l'incident: " . $stmt->error;
                        $_SESSION['flash_type'] = "danger";
                    }
                    
                    $stmt->close();
                } else {
                    // Erreur de préparation de la requête
                    $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $mysqli->close();
            } else {
                // Afficher les erreurs
                $_SESSION['flash_message'] = "Erreurs dans le formulaire: " . implode(", ", $erreurs);
                $_SESSION['flash_type'] = "danger";
            }
            
            // Rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        }
    }
    
    public function supprimerIncident() {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'prefet') {
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider l'ID de l'incident
            $incident_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            // Validation des données
            if (empty($incident_id)) {
                $_SESSION['flash_message'] = "ID d'incident invalide.";
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
                exit;
            }
            
            // Connexion à la base de données
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($mysqli->connect_error) {
                $_SESSION['flash_message'] = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
                $_SESSION['flash_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
                exit;
            }
            
            // Préparer la requête de suppression
            $query = "DELETE FROM incidents_disciplinaires WHERE id = ?";
            
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("i", $incident_id);
                
                if ($stmt->execute()) {
                    // Succès
                    $_SESSION['flash_message'] = "L'incident disciplinaire a été supprimé avec succès.";
                    $_SESSION['flash_type'] = "success";
                } else {
                    // Erreur
                    $_SESSION['flash_message'] = "Erreur lors de la suppression de l'incident: " . $stmt->error;
                    $_SESSION['flash_type'] = "danger";
                }
                
                $stmt->close();
            } else {
                // Erreur de préparation de la requête
                $_SESSION['flash_message'] = "Erreur de préparation de la requête: " . $mysqli->error;
                $_SESSION['flash_type'] = "danger";
            }
            
            $mysqli->close();
            
            // Rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de discipline
            header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=discipline');
            exit;
        }
    }
    
    
    // Afficher la carte d'élève
    public function carteEleve() {
        // Vérifier si l'utilisateur est connecté et a le rôle de préfet
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prefet') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour accéder à cette page.";
            header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
            exit;
        }
        
        // Charger la vue de la carte d'élève
        require_once 'views/prefet/carte_eleve.php';
    }
    
    /**
     * Affiche le profil détaillé d'un professeur
     */
    public function voirProfesseur() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Vérifier si l'ID est valide
        if ($id <= 0) {
            $_SESSION['message'] = "ID de professeur invalide.";
            $_SESSION['message_type'] = "error";
            header("Location: " . BASE_URL . "index.php?controller=Prefet&action=professeurs");
            exit;
        }
        
        // Récupérer les données du professeur
// Connect to database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get professor data
$query = "SELECT * FROM professeurs WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$professeur = $result->fetch_assoc();

$stmt->close();
$mysqli->close();
        
        // Débogage
        if (isset($_GET['debug']) && $_GET['debug'] == 1) {
            echo "<pre>";
            print_r($professeur);
            echo "</pre>";
        }
        
        // Récupérer les cours du professeur
// Connect to database to get professor's courses
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get courses data
$query = "SELECT * FROM cours WHERE professeur_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cours = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
        
        // Charger la vue
        require_once('views/prefet/voirProfesseur.php');
    }
}
?>
