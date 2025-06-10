<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer les informations supplémentaires depuis la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$user_details = [];
if ($user_id > 0) {
    // Modification de la requête pour récupérer également l'image de profil
    $stmt = $mysqli->prepare("SELECT telephone, adresse, image FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_details = $result->fetch_assoc();
        // Mettre à jour la variable de session avec l'image de la base de données si elle existe
        if (!empty($user_details['image'])) {
            $image = $user_details['image'];
            $_SESSION['image'] = $image;
        }
    }
    $stmt->close();
}

$mysqli->close();

// Récupérer les messages de succès ou d'erreur
$success_message = isset($_GET['success']) && isset($_GET['message']) ? $_GET['message'] : '';
$error_message = isset($_GET['error']) && isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Profil Administrateur</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Profil Utilisateur
        <small>Informations personnelles</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
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
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL . $image; ?>" alt="Photo de profil">
              <h3 class="profile-username text-center"><?php echo $username; ?></h3>
              <p class="text-muted text-center"><?php echo $role; ?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right"><?php echo $email; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Téléphone</b> <a class="pull-right"><?php echo isset($user_details['telephone']) ? $user_details['telephone'] : 'Non renseigné'; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Date d'inscription</b> <a class="pull-right"><?php echo date('d/m/Y'); ?></a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#settings" data-toggle="tab">Paramètres</a></li>
              <li><a href="#password" data-toggle="tab">Mot de passe</a></li>
              <li><a href="#avatar" data-toggle="tab">Photo de profil</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="settings">
                <form class="form-horizontal" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=updateProfile" method="post">
                  <div class="form-group">
                    <label for="nom" class="col-sm-2 control-label">Nom d'utilisateur</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom d'utilisateur" value="<?php echo $username; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
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
                      <textarea class="form-control" id="adresse" name="adresse" placeholder="Adresse"><?php echo isset($user_details['adresse']) ? $user_details['adresse'] : ''; ?></textarea>
                    </div>
                  </div>
                  <!-- Suppression du champ education -->
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="password">
                <form class="form-horizontal" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=updatePassword" method="post">
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

              <div class="tab-pane" id="avatar">
                <form class="form-horizontal" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=updateAvatar" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="avatar" class="col-sm-3 control-label">Nouvelle photo</label>
                    <div class="col-sm-9">
                      <input type="file" id="avatar" name="avatar" accept="image/*" required>
                      <p class="help-block">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <button type="submit" class="btn btn-primary">Changer photo</button>
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
    });
  });
</script>
</body>
</html>