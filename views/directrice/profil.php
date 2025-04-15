<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$nom = isset($_SESSION['nom']) ? $_SESSION['nom'] : 'Utilisateur';
$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'directrice';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer les informations supplémentaires depuis la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$user_details = [];
if ($user_id > 0) {
    // Modification de la requête pour ne récupérer que les colonnes existantes
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
$success_message = isset($_SESSION['flash_message']) && $_SESSION['flash_type'] == 'success' ? $_SESSION['flash_message'] : '';
$error_message = isset($_SESSION['flash_message']) && $_SESSION['flash_type'] == 'danger' ? $_SESSION['flash_message'] : '';

// Effacer les messages après les avoir récupérés
if (isset($_SESSION['flash_message'])) {
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
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

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>Système</b> Gestion</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo BASE_URL . $image; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $prenom . ' ' . $nom; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $prenom . ' ' . $nom; ?> - <?php echo $role; ?>
                  <small><?php echo $email; ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=profil" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=logout" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $prenom . ' ' . $nom; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=parents">
            <i class="fa fa-users"></i> <span>Parents</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=discipline">
            <i class="fa fa-gavel"></i> <span>Discipline</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=absences">
            <i class="fa fa-calendar-times-o"></i> <span>Absences</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=notes">
            <i class="fa fa-pencil-square-o"></i> <span>Notes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=emploiDuTemps">
            <i class="fa fa-calendar"></i> <span>Emploi du temps</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=evenements">
            <i class="fa fa-bullhorn"></i> <span>Événements</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=communications">
            <i class="fa fa-envelope"></i> <span>Communications</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=rapports">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

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
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL . $image; ?>" alt="Photo de profil">
              <h3 class="profile-username text-center"><?php echo $prenom . ' ' . $nom; ?></h3>
              <p class="text-muted text-center">Directrice</p>

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
                <form class="form-horizontal" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=updateProfile" method="post">
                  <div class="form-group">
                    <label for="nom" class="col-sm-2 control-label">Nom</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" value="<?php echo $nom; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="prenom" class="col-sm-2 control-label">Prénom</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo $prenom; ?>">
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
                <form class="form-horizontal" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=updateAvatar" method="post" enctype="multipart/form-data">
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