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
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';

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
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Profil Utilisateur</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- En-tête principal -->
  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Comptable&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b><?php echo $role; ?></b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <!-- Dans la section de l'en-tête principal -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo $image; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo $image; ?>" class="img-circle" alt="Image utilisateur">
                <p><?php echo $role; ?></p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=profil" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=logout" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
              <!-- Reste du menu déroulant -->
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  
  <!-- Barre latérale gauche -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo $image; ?>" class="img-circle" alt="Image utilisateur">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Rechercher...">
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </form>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
            <i class="fa fa-pencil"></i> <span>Inscription</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
            <i class="fa fa-money"></i> <span>Paiement frais</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
            <i class="fa fa-check-circle"></i> <span>Élèves en ordre</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <!-- Contenu principal -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Profil Utilisateur
        <small>Informations personnelles</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <!-- Boîte de profil -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo $image; ?>" alt="Photo de profil">
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
              <strong><i class="fa fa-user margin-r-5"></i> Rôle</strong>
              <p class="text-muted"><?php echo $role; ?></p>
            </div>
          </div>
        </div>
        
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#settings" data-toggle="tab">Paramètres</a></li>
              <li><a href="#activity" data-toggle="tab">Activité</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="settings">
                <form class="form-horizontal" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=updateProfile" method="post">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Nom d'utilisateur</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputName" name="username" placeholder="Nom d'utilisateur" value="<?php echo $username; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" value="<?php echo $email; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">Nouveau mot de passe</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Laissez vide pour ne pas changer">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPasswordConfirm" class="col-sm-2 control-label">Confirmer mot de passe</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPasswordConfirm" name="password_confirm" placeholder="Confirmer le nouveau mot de passe">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-danger">Mettre à jour</button>
                    </div>
                  </div>
                </form>
              </div>
              
              <div class="tab-pane" id="activity">
                <div class="post">
                  <p>Historique des activités sera affiché ici...</p>
                </div>
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
        <form action="<?php echo BASE_URL; ?>index.php?controller=Comptable&action=updateProfilePhoto" method="post" enctype="multipart/form-data">
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

  <!-- Pied de page -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- Scripts JS -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<!-- Afficher les messages d'alerte -->
<?php if(isset($_SESSION['success'])): ?>
<script>
  $(document).ready(function() {
    alert('<?php echo $_SESSION['success']; ?>');
    <?php unset($_SESSION['success']); ?>
  });
</script>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
<script>
  $(document).ready(function() {
    alert('<?php echo $_SESSION['error']; ?>');
    <?php unset($_SESSION['error']); ?>
  });
</script>
<?php endif; ?>

</body>
</html>