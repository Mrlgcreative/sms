<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';

// Récupérer l'image de profil depuis la base de données si l'utilisateur est connecté
if ($user_id > 0) {
  // Connexion à la base de données
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  
  if (!$mysqli->connect_error) {
    $stmt = $mysqli->prepare("SELECT image FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
      $user_data = $result->fetch_assoc();
      if (!empty($user_data['image'])) {
        $_SESSION['image'] = $user_data['image'];
      }
    }
    
    $stmt->close();
    $mysqli->close();
  }
}

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer les informations supplémentaires depuis la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$user_details = [];
if ($user_id > 0) {
    // Récupérer plus d'informations sur l'utilisateur
    $stmt = $mysqli->prepare("SELECT telephone, adresse FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_details = $result->fetch_assoc();
    }
    $stmt->close();
}

$mysqli->close();

// Récupérer les messages de succès ou d'erreur
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Effacer les messages après les avoir récupérés
if (isset($_SESSION['success'])) {
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Profil Directrice</title>
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
        <small>Informations personnelles</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <?php if (!empty($success_message)): ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-check"></i> Succès!</h4>
        <?php echo htmlspecialchars($success_message); ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($error_message)): ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
        <?php echo htmlspecialchars($error_message); ?>
      </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-3">
          <!-- Boîte de profil -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL . $image; ?>" alt="Photo de profil">
              <h3 class="profile-username text-center"><?php echo $username; ?></h3>
              <p class="text-muted text-center"><?php echo $role; ?></p>
              
              <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#changePhotoModal">
                <i class="fa fa-camera"></i> Changer ma photo
              </button>
            </div>
          </div>

          <!-- À propos de moi -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">À propos de moi</h3>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-envelope margin-r-5"></i> Email</strong>
              <p class="text-muted"><?php echo $email; ?></p>
              <hr>
              <strong><i class="fa fa-phone margin-r-5"></i> Téléphone</strong>
              <p class="text-muted"><?php echo isset($user_details['telephone']) ? $user_details['telephone'] : 'Non renseigné'; ?></p>
              <hr>
              <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
              <p class="text-muted"><?php echo isset($user_details['adresse']) ? $user_details['adresse'] : 'Non renseignée'; ?></p>
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
              <div class="active tab-pane" id="settings">
                <form class="form-horizontal" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=updateProfile" method="post">
                  <div class="form-group">
                    <label for="nom" class="col-sm-2 control-label">Nom d'utilisateur</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="nom" name="username" placeholder="Nom d'utilisateur" value="<?php echo $username; ?>" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="telephone" class="col-sm-2 control-label">Téléphone</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Téléphone" value="<?php echo isset($user_details['telephone']) ? $user_details['telephone'] : ''; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="adresse" class="col-sm-2 control-label">Adresse</label>
                    <div class="col-sm-10">
                      <textarea class="form-control" id="adresse" name="adresse" placeholder="Adresse" rows="3"><?php echo isset($user_details['adresse']) ? $user_details['adresse'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="password">
                <form class="form-horizontal" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=updatePassword" method="post">
                  <div class="form-group">
                    <label for="current_password" class="col-sm-3 control-label">Mot de passe actuel</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Mot de passe actuel" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="password" class="col-sm-3 control-label">Nouveau mot de passe</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="password" name="password" placeholder="Nouveau mot de passe" required>
                      <span class="help-block">Le mot de passe doit contenir au moins 8 caractères</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="password_confirm" class="col-sm-3 control-label">Confirmer mot de passe</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirmer mot de passe" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <button type="submit" class="btn btn-primary">Changer mot de passe</button>
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

  <!-- Modal pour changer la photo de profil -->
  <div class="modal fade" id="changePhotoModal" tabindex="-1" role="dialog" aria-labelledby="changePhotoModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="changePhotoModalLabel">Changer ma photo de profil</h4>
        </div>
        <form action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=updateAvatar" method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="form-group">
              <label for="profile_photo">Sélectionner une nouvelle photo</label>
              <input type="file" id="profile_photo" name="profile_photo" accept="image/*" required>
              <p class="help-block">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
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

<!-- Afficher les messages d'alerte -->
<script>
  $(document).ready(function() {
    // Masquer les alertes après 5 secondes
    setTimeout(function() {
      $('.alert').fadeOut('slow');
    }, 5000);
    
    // Validation du formulaire de mot de passe
    $('form').submit(function(e) {
      var password = $('#password').val();
      var confirm = $('#password_confirm').val();
      
      if (password && confirm && password !== confirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas!');
      }
      
      // Vérifier la longueur du mot de passe
      if (password && password.length < 8) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 8 caractères!');
      }
    });
  });
</script>
</body>
</html>