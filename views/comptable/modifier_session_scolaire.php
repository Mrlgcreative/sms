<?php
// Vérifiez si une session est déjà active avant d'appeler session_start()
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les clés si elles ne sont pas déjà définies
if (!isset($_SESSION['username'])) {
  $_SESSION['username'] = 'username';
}
if (!isset($_SESSION['email'])) {
  $_SESSION['email'] = 'email';
}
if (!isset($_SESSION['role'])) {
  $_SESSION['role'] = 'role';
}

// Récupérer les valeurs des clés
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

// Récupérer le message d'erreur s'il existe
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
if (isset($_SESSION['error'])) {
  unset($_SESSION['error']);
}

// Récupérer le message de succès s'il existe
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
if (isset($_SESSION['success'])) {
  unset($_SESSION['success']);
}

// Vérifier si l'ID de la session scolaire est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID de la session scolaire non spécifié.";
    header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions_scolaires');
    exit;
}

$session_id = intval($_GET['id']);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Modifier Session Scolaire</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>index.php?controller=Comptable&action=accueil" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>St</b>S</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b><?php echo $role; ?></b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
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
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      <!-- search form -->
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=achatFournitures">
            <i class="fa fa-pencil"></i> <span>Achat fourniture</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscris">
            <i class="fa fa-users"></i> <span>Élèves reinscris</span>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription">
            <i class="fa fa-refresh"></i> <span>Réinscription</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=sessions_scolaires">
            <i class="fa fa-calendar"></i> <span>Sessions Scolaires</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Modifier une Session Scolaire
        <small>Mettre à jour les informations</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=sessions_scolaires">Sessions Scolaires</a></li>
        <li class="active">Modifier</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
      
      <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>
      
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire de modification</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=updateSessionScolaire">
              <input type="hidden" name="id" value="<?php echo $session_id; ?>">
              <div class="box-body">
                <div class="form-group">
                  <label for="nom">Nom de la session</label>
                  <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: Année scolaire 2023-2024" value="<?php echo isset($session_scolaire['nom']) ? $session_scolaire['nom'] : ''; ?>" required>
                </div>
                <div class="form-group">
                  <label for="date_debut">Date de début</label>
                  <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?php echo isset($session_scolaire['date_debut']) ? $session_scolaire['date_debut'] : ''; ?>" required>
                </div>
                <div class="form-group">
                  <label for="date_fin">Date de fin</label>
                  <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?php echo isset($session_scolaire['date_fin']) ? $session_scolaire['date_fin'] : ''; ?>" required>
                </div>
                <div class="form-group">
                  <label for="statut">Statut</label>
                  <select class="form-control" id="statut" name="statut">
                    <option value="active" <?php echo (isset($session_scolaire['statut']) && $session_scolaire['statut'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo (isset($session_scolaire['statut']) && $session_scolaire['statut'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    <option value="terminee" <?php echo (isset($session_scolaire['statut']) && $session_scolaire['statut'] == 'terminee') ? 'selected' : ''; ?>>Terminée</option>
                  </select>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=sessions_scolaires" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>