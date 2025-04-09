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
  $_SESSION['email'] = ['email'];
}
if (!isset($_SESSION['role'])) {
  $_SESSION['role'] = ['role'];
}

// Récupérer les valeurs des clés
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

// Déterminer quelle section afficher
$section = isset($_GET['section']) ? $_GET['section'] : '';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St sofie | Inscription</title>
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
  <style>
    .section-button {
      padding: 30px;
      margin: 20px;
      font-size: 24px;
      border-radius: 10px;
    }
    .maternelle {
      background-color: #3c8dbc;
      color: white;
    }
    .primaire {
      background-color: #00a65a;
      color: white;
    }
    .secondaire {
      background-color: #f39c12;
      color: white;
    }
    
    /* Style pour les notifications toast */
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
    }
    
    .toast {
      min-width: 300px;
      margin-bottom: 10px;
      padding: 15px;
      border-radius: 4px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      animation: fadeIn 0.5s, fadeOut 0.5s 4.5s;
      opacity: 0;
      animation-fill-mode: forwards;
    }
    
    .toast-success {
      background-color: #00a65a;
      color: white;
    }
    
    .toast-error {
      background-color: #dd4b39;
      color: white;
    }
    
    .toast-info {
      background-color: #00c0ef;
      color: white;
    }
    
    .toast-warning {
      background-color: #f39c12;
      color: white;
    }
    
    .toast-close {
      float: right;
      font-weight: bold;
      cursor: pointer;
    }
    
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    
    @keyframes fadeOut {
      from {opacity: 1;}
      to {opacity: 0; display: none;}
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Conteneur pour les notifications toast -->
<div class="toast-container" id="toast-container"></div>
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>index.php?controller=Comptable&action=accueil" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>St</b>Henry</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b> <?php echo ($role); ?></b></span>
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
              <!-- Reste du menu déroulant -->
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
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-child"></i> <span>Eleves</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
            <i class="fa fa-edit"></i> <span>Inscription</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
            <i class="fa fa-money"></i> <span>Paiement frais</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
            <i class="fa fa-list"></i> <span>Eleves en ordres</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file"></i> <span>Rapports</span>
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
        Inscription des élèves
        <?php if ($section): ?>
          <small>Section <?php echo ucfirst($section); ?></small>
        <?php else: ?>
          <small>Choisissez une section</small>
        <?php endif; ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Inscription</li>
        <?php if ($section): ?>
          <li class="active"><?php echo ucfirst($section); ?></li>
        <?php endif; ?>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php if (!$section): ?>
      <!-- Affichage des boutons de section -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Sélectionnez la section pour l'inscription</h3>
            </div>
            <div class="box-body text-center">
              <div class="row">
                <div class="col-md-4">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions&section=maternelle" class="btn btn-lg btn-block section-button maternelle">
                    <i class="fa fa-child"></i> Maternelle
                  </a>
                </div>
                <div class="col-md-4">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions&section=primaire" class="btn btn-lg btn-block section-button primaire">
                    <i class="fa fa-book"></i> Primaire
                  </a>
                </div>
                <div class="col-md-4">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions&section=secondaire" class="btn btn-lg btn-block section-button secondaire">
                    <i class="fa fa-graduation-cap"></i> Secondaire
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php elseif ($section == 'maternelle'): ?>
      <!-- Formulaire d'inscription Maternelle -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'inscription - Section Maternelle</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=enregistrerEleve" enctype="multipart/form-data">
              <input type="hidden" name="section" value="maternelle">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="matricule">Matricule</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="matricule" name="matricule" value="<?php echo 'SGS-'.date('Y').'-'.str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); ?>" readonly>
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default" id="regenerate-matricule"><i class="fa fa-refresh"></i></button>
                        </span>
                      </div>
                      <small class="text-muted">Matricule généré automatiquement</small>
                    </div>
                    <div class="form-group">
                      <label for="nom">Nom</label>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-group">
                      <label for="post_nom">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom" name="post_nom" placeholder="Post-nom" required>
                    </div>
                    <div class="form-group">
                      <label for="prenom">Prénom</label>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="lieu_naissance">Lieu de naissance</label>
                      <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" placeholder="Lieu de naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="adresse">Adresse</label>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                  <div class="form-group">
                      <label for="photo-secondaire">Photo de l'élève</label>
                      <input type="file" id="photo-secondaire" name="photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview-secondaire" src="dist/img/default-student.png" alt="Aperçu de la photo" style="width: 100%; height: auto;">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="classe_id">Classe</label>
                      <select class="form-control" id="classe_id" name="classe_id" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes as $classe) : ?>
                          <option value="<?php echo $classe['nom']; ?>"><?php echo $classe['id']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="sexe">Sexe</label>
                      <select class="form-control" name="sexe" id="sexe">
                        <option value="">-- Sélectionner un sexe --</option>
                        <option value="M">Masculin</option>
                        <option value="F">Feminin</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="nom_pere">Nom du père</label>
                      <input type="text" class="form-control" id="nom_pere" name="nom_pere" placeholder="Nom du père" required>
                    </div>
                    <div class="form-group">
                      <label for="nom_mere">Nom de la mère</label>
                      <input type="text" class="form-control" id="nom_mere" name="nom_mere" placeholder="Nom de la mère" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere" name="contact_pere" placeholder="Contact du père" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_mere">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere" name="contact_mere" placeholder="Contact de la mère" required>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php elseif ($section == 'primaire'): ?>
      <!-- Formulaire d'inscription Primaire -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'inscription - Section Primaire</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=enregistrerEleve">
              <input type="hidden" name="section" value="primaire">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                  <div class="form-group">
                      <label for="matricule">Matricule</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="matricule" name="matricule" value="<?php echo 'SGS-'.date('Y').'-'.str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); ?>" readonly>
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default" id="regenerate-matricule-secondaire"><i class="fa fa-refresh"></i></button>
                        </span>
                      </div>
                      <small class="text-muted">Matricule généré automatiquement</small>
                    </div>
                    <div class="form-group">
                      <label for="nom">Nom</label>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-group">
                      <label for="post_nom">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom" name="post_nom" placeholder="Post-nom" required>
                    </div>
                    <div class="form-group">
                      <label for="prenom">Prénom</label>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="lieu_naissance">Lieu de naissance</label>
                      <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" placeholder="Lieu de naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="adresse">Adresse</label>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                  <div class="form-group">
                      <label for="photo-secondaire">Photo de l'élève</label>
                      <input type="file" id="photo-secondaire" name="photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview-secondaire" src="dist/img/default-student.png" alt="Aperçu de la photo" style="width: 100%; height: auto;">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="classe">Classe</label>
                      <select class="form-control" id="classe" name="classe" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes as $classe) : ?>
                          <option value="<?php echo $classe['nom']; ?>"><?php echo $classe['id']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="nom_pere">Nom du père</label>
                      <input type="text" class="form-control" id="nom_pere" name="nom_pere" placeholder="Nom du père" required>
                    </div>
                    <div class="form-group">
                      <label for="nom_mere">Nom de la mère</label>
                      <input type="text" class="form-control" id="nom_mere" name="nom_mere" placeholder="Nom de la mère" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere" name="contact_pere" placeholder="Contact du père" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_mere">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere" name="contact_mere" placeholder="Contact de la mère" required>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-success">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php elseif ($section == 'secondaire'): ?>
      <!-- Formulaire d'inscription Secondaire -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'inscription - Section Secondaire</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=enregistrerEleve" enctype="multipart/form-data">
              <input type="hidden" name="section" value="secondaire">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="matricule">Matricule</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="matricule" name="matricule" value="<?php echo 'SGS-'.date('Y').'-'.str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); ?>" readonly>
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default" id="regenerate-matricule-secondaire"><i class="fa fa-refresh"></i></button>
                        </span>
                      </div>
                      <small class="text-muted">Matricule généré automatiquement</small>
                    </div>
                    <div class="form-group">
                      <label for="nom">Nom</label>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-group">
                      <label for="post_nom">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom" name="post_nom" placeholder="Post-nom" required>
                    </div>
                    <div class="form-group">
                      <label for="prenom">Prénom</label>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="lieu_naissance">Lieu de naissance</label>
                      <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" placeholder="Lieu de naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="adresse">Adresse</label>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="photo-secondaire">Photo de l'élève</label>
                      <input type="file" id="photo-secondaire" name="photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview-secondaire" src="dist/img/default-student.png" alt="Aperçu de la photo" style="width: 100%; height: auto;">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="classe">Classe</label>
                      <select class="form-control" id="classe" name="classe" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes as $classe) : ?>
                          <option value="<?php echo $classe['nom']; ?>"><?php echo $classe['id']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="option_id">Option</label>
                      <select class="form-control" id="option_id" name="option_id" required>
                        <option value="">-- Sélectionner une option --</option>
                        <?php foreach ($options as $option) : ?>
                          <option value="<?php echo $option['id']; ?>"><?php echo $option['nom']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="nom_pere">Nom du père</label>
                      <input type="text" class="form-control" id="nom_pere" name="nom_pere" placeholder="Nom du père" required>
                    </div>
                    <div class="form-group">
                      <label for="nom_mere">Nom de la mère</label>
                      <input type="text" class="form-control" id="nom_mere" name="nom_mere" placeholder="Nom de la mère" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere" name="contact_pere" placeholder="Contact du père" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_mere">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere" name="contact_mere" placeholder="Contact de la mère" required>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-warning">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php endif; ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; 2023 <a href="#">St Sofie</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function() {
    // Photo preview pour maternelle
    $("#photo").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#photo-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Photo preview pour primaire
    $("#photo-primaire").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#photo-preview-primaire').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Photo preview pour secondaire
    $("#photo-secondaire").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#photo-preview-secondaire').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Regenerate matricule pour maternelle
    $("#regenerate-matricule").click(function() {
      var year = new Date().getFullYear();
      var random = Math.floor(Math.random() * 9999) + 1;
      var paddedRandom = random.toString().padStart(4, '0');
      $("#matricule").val('SGS-' + year + '-' + paddedRandom);
    });
    
    // Regenerate matricule pour primaire
    $("#regenerate-matricule-primaire").click(function() {
      var year = new Date().getFullYear();
      var random = Math.floor(Math.random() * 9999) + 1;
      var paddedRandom = random.toString().padStart(4, '0');
      $("#matricule").val('SGS-' + year + '-' + paddedRandom);
    });
    
    // Regenerate matricule pour secondaire
    $("#regenerate-matricule-secondaire").click(function() {
      var year = new Date().getFullYear();
      var random = Math.floor(Math.random() * 9999) + 1;
      var paddedRandom = random.toString().padStart(4, '0');
      $("#matricule").val('SGS-' + year + '-' + paddedRandom);
    });
  });
</script>
</body>
</html>