<?php
// Vérifier si l'utilisateur est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'directrice') {
    header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
    exit();
}

// Initialiser les variables
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Récupérer les messages d'erreur ou de succès
$error_message = isset($_GET['error']) ? urldecode($_GET['error']) : '';
$success_message = isset($_GET['success']) ? urldecode($_GET['success']) : '';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Profil Directrice</title>
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

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b>Directrice</b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
                <p>
                  <?php echo $username; ?>
                  <small><?php echo $role; ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
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
      <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
              <li class="header">NAVIGATION PRINCIPALE</li>
              <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil">
                  <i class="fa fa-dashboard"></i> <span>Accueil</span>
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves">
                  <i class="fa fa-users"></i> <span>Élèves Maternelle</span>
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=classes">
                  <i class="fa fa-graduation-cap"></i> <span>Classes</span>
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=achats">
                  <i class="fa fa-shopping-cart"></i> <span>Achats</span>
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=stock">
                  <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=evenements">
                  <i class="fa fa-calendar"></i> <span>Événements</span>
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=statistiques">
                  <i class="fa fa-bar-chart"></i> <span>Statistiques</span>
                </a>
              </li>
              <li class="active">
                <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil">
                  <i class="fa fa-user"></i> <span>Profil</span>
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
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" alt="Photo de profil">

              <h3 class="profile-username text-center"><?php echo $username; ?></h3>

              <p class="text-muted text-center"><?php echo $role; ?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right"><?php echo $email; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Rôle</b> <a class="pull-right">Directrice</a>
                </li>
                <li class="list-group-item">
                  <b>Section</b> <a class="pull-right">Maternelle</a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=changePassword" class="btn btn-primary btn-block"><b>Changer le mot de passe</b></a>
            </div>
          </div>
        </div>
        
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#settings" data-toggle="tab">Paramètres</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="settings">
                <form class="form-horizontal" method="post" action="<?php echo BASE_URL; ?>index.php?controller=Auth&action=updateProfile" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Nom</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputName" name="username" placeholder="Nom" value="<?php echo $username; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" value="<?php echo $email; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPhoto" class="col-sm-2 control-label">Photo</label>
                    <div class="col-sm-10">
                      <input type="file" class="form-control" id="inputPhoto" name="photo">
                      <p class="help-block">Format JPG, PNG ou GIF. Max 2MB.</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-danger">Mettre à jour</button>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>