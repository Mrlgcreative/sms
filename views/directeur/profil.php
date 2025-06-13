<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Director';

// Récupérer les informations de l'utilisateur depuis la base de données
$user_data = [];
if ($user_id > 0) {
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$mysqli->connect_error) {
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
        }
        
        $stmt->close();
        $mysqli->close();
    }
}

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Traitement du formulaire de mise à jour du profil
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Connexion à la base de données
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        $error_message = "Erreur de connexion à la base de données: " . $mysqli->connect_error;
    } else {
        // Récupérer les données du formulaire
        $new_username = $mysqli->real_escape_string($_POST['username']);
        $new_email = $mysqli->real_escape_string($_POST['email']);
        $new_password = isset($_POST['password']) && !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
        
        // Traitement de l'image
        $new_image = $image; // Par défaut, garder l'image actuelle
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profiles/';
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Vérifier le type de fichier
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['profile_image']['type'], $allowed_types)) {
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $new_image = $target_file;
                } else {
                    $error_message = "Erreur lors du téléchargement de l'image.";
                }
            } else {
                $error_message = "Seuls les fichiers JPG, PNG et GIF sont autorisés.";
            }
        }
        
        // Mise à jour des informations de l'utilisateur
        if (empty($error_message)) {
            $query = "UPDATE users SET username = ?, email = ?";
            $params = [$new_username, $new_email];
            $types = "ss";
            
            if ($new_password !== null) {
                $query .= ", password = ?";
                $params[] = $new_password;
                $types .= "s";
            }
            
            $query .= ", image = ? WHERE id = ?";
            $params[] = $new_image;
            $params[] = $user_id;
            $types .= "si";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                // Mettre à jour les variables de session
                $_SESSION['username'] = $new_username;
                $_SESSION['email'] = $new_email;
                $_SESSION['image'] = $new_image;
                
                $success_message = "Profil mis à jour avec succès!";
                
                // Mettre à jour les variables locales
                $username = $new_username;
                $email = $new_email;
                $image = $new_image;
            } else {
                $error_message = "Erreur lors de la mise à jour du profil: " . $stmt->error;
            }
            
            $stmt->close();
        }
        
        $mysqli->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Profil Utilisateur</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

 
 <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Profil Utilisateur
        <small>Gérer vos informations personnelles</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <!-- Profil Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL . $image; ?>" alt="User profile picture">
              <h3 class="profile-username text-center"><?php echo $username; ?></h3>
              <p class="text-muted text-center"><?php echo $role; ?></p>
            </div>
          </div>

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">À propos de moi</h3>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-envelope margin-r-5"></i> Email</strong>
              <p class="text-muted"><?php echo $email; ?></p>
              
              <hr>
              
              <strong><i class="fa fa-clock-o margin-r-5"></i> Dernière connexion</strong>
              <p class="text-muted"><?php echo isset($user_data['last_login']) ? date('d/m/Y H:i', strtotime($user_data['last_login'])) : 'Non disponible'; ?></p>
            </div>
          </div>
        </div>
        
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#settings" data-toggle="tab">Paramètres</a></li>
              <li><a href="#password" data-toggle="tab">Mot de passe</a></li>
            </ul>
            <div class="tab-content">
              <!-- Messages de succès ou d'erreur -->
              <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h4><i class="icon fa fa-check"></i> Succès!</h4>
                  <?php echo $success_message; ?>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
                  <?php echo $error_message; ?>
                </div>
              <?php endif; ?>
              
              <!-- Onglet Paramètres -->
              <div class="active tab-pane" id="settings">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="username" class="col-sm-2 control-label">Nom d'utilisateur</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="profile_image" class="col-sm-2 control-label">Photo de profil</label>
                    <div class="col-sm-10">
                      <input type="file" id="profile_image" name="profile_image">
                      <p class="help-block">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB.</p>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" name="update_profile" class="btn btn-primary">Mettre à jour</button>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Onglet Mot de passe -->
              <div class="tab-pane" id="password">
                <form class="form-horizontal" method="post" action="">
                  <div class="form-group">
                    <label for="current_password" class="col-sm-3 control-label">Mot de passe actuel</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="password" class="col-sm-3 control-label">Nouveau mot de passe</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="password" name="password">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="confirm_password" class="col-sm-3 control-label">Confirmer le mot de passe</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <button type="submit" name="update_profile" class="btn btn-primary">Changer le mot de passe</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Validation du formulaire de mot de passe
  $('form').submit(function(e) {
    var password = $('#password').val();
    var confirm_password = $('#confirm_password').val();
    
    if (password !== confirm_password) {
      alert('Les mots de passe ne correspondent pas!');
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>