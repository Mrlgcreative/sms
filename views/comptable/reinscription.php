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

// Déterminer quelle section afficher
$section = isset($_GET['section']) ? $_GET['section'] : '';

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
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Réinscription</title>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscriptionEleve">
            <i class="fa fa-refresh"></i> <span>Réinscriptions</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
            <i class="fa fa-edit"></i> <span>Inscription</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription">
            <i class="fa fa-refresh"></i> <span>Réinscription</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
            <i class="fa fa-money"></i> <span>Paiement frais</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
            <i class="fa fa-list"></i> <span>Élèves en ordre</span>
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
        Réinscription des anciens élèves
        <?php if ($section): ?>
          <small>Section <?php echo ucfirst($section); ?></small>
        <?php else: ?>
          <small>Choisissez une section</small>
        <?php endif; ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Réinscription</li>
        <?php if ($section): ?>
          <li class="active"><?php echo ucfirst($section); ?></li>
        <?php endif; ?>
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
      
      <?php if (!$section): ?>
      <!-- Affichage des boutons de section -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Sélectionnez la section pour la réinscription</h3>
            </div>
            <div class="box-body text-center">
              <div class="row">
                <div class="col-md-4">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription&section=maternelle" class="btn btn-lg btn-block section-button maternelle">
                    <i class="fa fa-child"></i> Maternelle
                  </a>
                </div>
                <div class="col-md-4">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription&section=primaire" class="btn btn-lg btn-block section-button primaire">
                    <i class="fa fa-book"></i> Primaire
                  </a>
                </div>
                <div class="col-md-4">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription&section=secondaire" class="btn btn-lg btn-block section-button secondaire">
                    <i class="fa fa-graduation-cap"></i> Secondaire
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php elseif ($section == 'maternelle'): ?>
      <!-- Formulaire de réinscription Maternelle -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire de réinscription - Section Maternelle</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- Recherche d'élève -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Rechercher un élève</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="search-eleve" placeholder="Entrez le matricule, nom ou prénom de l'élève">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" id="btn-search-eleve"><i class="fa fa-search"></i> Rechercher</button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row" id="search-results" style="display: none;">
                <div class="col-md-12">
                  <div class="box box-solid">
                    <div class="box-header with-border">
                      <h3 class="box-title">Résultats de la recherche</h3>
                    </div>
                    <div class="box-body">
                      <table class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Post-nom</th>
                            <th>Prénom</th>
                            <th>Classe actuelle</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody id="search-results-body">
                          <!-- Les résultats seront ajoutés ici dynamiquement -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Formulaire de réinscription -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscrireEleve" enctype="multipart/form-data" id="form-reinscription" style="display: none;">
              <input type="hidden" name="section" value="maternelle">
              <input type="hidden" name="eleve_id" id="eleve_id">
              <input type="hidden" name="session_scolaire_id" value="<?php echo isset($sessions_scolaires[0]['id']) ? $sessions_scolaires[0]['id'] : ''; ?>">
              
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="matricule">Matricule</label>
                      <input type="text" class="form-control" id="matricule" name="matricule" readonly>
                    </div>
                    <div class="form-group">
                      <label for="nom">Nom</label>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="post_nom">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom" name="post_nom" placeholder="Post-nom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="prenom">Prénom</label>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance" name="date_naissance" readonly>
                    </div>
                    <div class="form-group">
                      <label for="adresse">Adresse</label>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="photo">Photo actuelle de l'élève</label>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview" src="dist/img/default-student.png" alt="Photo de l'élève" style="width: 100%; height: auto;">
                      </div>
                      <label for="nouvelle_photo" style="margin-top: 10px;">Mettre à jour la photo (optionnel)</label>
                      <input type="file" id="nouvelle_photo" name="nouvelle_photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                    </div>
                    <div class="form-group">
                      <label for="classe_actuelle">Classe actuelle</label>
                      <input type="text" class="form-control" id="classe_actuelle" readonly>
                    </div>
                    <div class="form-group">
                      <label for="nouvelle_classe_id">Nouvelle classe</label>
                      <select class="form-control" id="nouvelle_classe_id" name="nouvelle_classe_id" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes as $classe) : ?>
                          <?php if ($classe['section'] == 'maternelle') : ?>
                            <option value="<?php echo $classe['id']; ?>"><?php echo $classe['niveau']; ?></option>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere" name="contact_pere" placeholder="Contact du père">
                    </div>
                    <div class="form-group">
                      <label for="contact_mere">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere" name="contact_mere" placeholder="Contact de la mère">
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Réinscrire</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php elseif ($section == 'primaire'): ?>
      <!-- Formulaire de réinscription Primaire -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire de réinscription - Section Primaire</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- Recherche d'élève -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Rechercher un élève</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="search-eleve-primaire" placeholder="Entrez le matricule, nom ou prénom de l'élève">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-success" id="btn-search-eleve-primaire"><i class="fa fa-search"></i> Rechercher</button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row" id="search-results-primaire" style="display: none;">
                <div class="col-md-12">
                  <div class="box box-solid">
                    <div class="box-header with-border">
                      <h3 class="box-title">Résultats de la recherche</h3>
                    </div>
                    <div class="box-body">
                      <table class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Post-nom</th>
                            <th>Prénom</th>
                            <th>Classe actuelle</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody id="search-results-body-primaire">
                          <!-- Les résultats seront ajoutés ici dynamiquement -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Formulaire de réinscription -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscrireEleve" enctype="multipart/form-data" id="form-reinscription-primaire" style="display: none;">
              <input type="hidden" name="section" value="primaire">
              <input type="hidden" name="eleve_id" id="eleve_id_primaire">
              <input type="hidden" name="session_scolaire_id" value="<?php echo isset($sessions_scolaires[0]['id']) ? $sessions_scolaires[0]['id'] : ''; ?>">
              
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="matricule_primaire">Matricule</label>
                      <input type="text" class="form-control" id="matricule_primaire" name="matricule" readonly>
                    </div>
                    <div class="form-group">
                      <label for="nom_primaire">Nom</label>
                      <input type="text" class="form-control" id="nom_primaire" name="nom" placeholder="Nom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="post_nom_primaire">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom_primaire" name="post_nom" placeholder="Post-nom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="prenom_primaire">Prénom</label>
                      <input type="text" class="form-control" id="prenom_primaire" name="prenom" placeholder="Prénom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance_primaire">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance_primaire" name="date_naissance" readonly>
                    </div>
                    <div class="form-group">
                      <label for="adresse_primaire">Adresse</label>
                      <input type="text" class="form-control" id="adresse_primaire" name="adresse" placeholder="Adresse">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="photo_primaire">Photo actuelle de l'élève</label>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview-primaire" src="dist/img/default-student.png" alt="Photo de l'élève" style="width: 100%; height: auto;">
                      </div>
                      <label for="nouvelle_photo_primaire" style="margin-top: 10px;">Mettre à jour la photo (optionnel)</label>
                      <input type="file" id="nouvelle_photo_primaire" name="nouvelle_photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                    </div>
                    <div class="form-group">
                      <label for="classe_actuelle_primaire">Classe actuelle</label>
                      <input type="text" class="form-control" id="classe_actuelle_primaire" readonly>
                    </div>
                    <div class="form-group">
                      <label for="nouvelle_classe_id_primaire">Nouvelle classe</label>
                      <select class="form-control" id="nouvelle_classe_id_primaire" name="nouvelle_classe_id" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes_primaire as $classe) : ?>
                          <?php if ($classe['section'] == 'primaire') : ?>
                            <option value="<?php echo $classe['id']; ?>"><?php echo $classe['niveau']; ?></option>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere_primaire">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere_primaire" name="contact_pere" placeholder="Contact du père">
                    </div>
                    <div class="form-group">
                      <label for="contact_mere_primaire">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere_primaire" name="contact_mere" placeholder="Contact de la mère">
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-success">Réinscrire</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php elseif ($section == 'secondaire'): ?>
      <!-- Formulaire de réinscription Secondaire -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire de réinscription - Section Secondaire</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- Recherche d'élève -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Rechercher un élève</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="search-eleve-secondaire" placeholder="Entrez le matricule, nom ou prénom de l'élève">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-warning" id="btn-search-eleve-secondaire"><i class="fa fa-search"></i> Rechercher</button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row" id="search-results-secondaire" style="display: none;">
                <div class="col-md-12">
                  <div class="box box-solid">
                    <div class="box-header with-border">
                      <h3 class="box-title">Résultats de la recherche</h3>
                    </div>
                    <div class="box-body">
                      <table class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Post-nom</th>
                            <th>Prénom</th>
                            <th>Classe actuelle</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody id="search-results-body-secondaire">
                          <!-- Les résultats seront ajoutés ici dynamiquement -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Formulaire de réinscription -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscrireEleve" enctype="multipart/form-data" id="form-reinscription-secondaire" style="display: none;">
              <input type="hidden" name="section" value="secondaire">
              <input type="hidden" name="eleve_id" id="eleve_id_secondaire">
              <input type="hidden" name="session_scolaire_id" value="<?php echo isset($sessions_scolaires[0]['id']) ? $sessions_scolaires[0]['id'] : ''; ?>">
              
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="matricule_secondaire">Matricule</label>
                      <input type="text" class="form-control" id="matricule_secondaire" name="matricule" readonly>
                    </div>
                    <div class="form-group">
                      <label for="nom_secondaire">Nom</label>
                      <input type="text" class="form-control" id="nom_secondaire" name="nom" placeholder="Nom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="post_nom_secondaire">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom_secondaire" name="post_nom" placeholder="Post-nom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="prenom_secondaire">Prénom</label>
                      <input type="text" class="form-control" id="prenom_secondaire" name="prenom" placeholder="Prénom" readonly>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance_secondaire">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance_secondaire" name="date_naissance" readonly>
                    </div>
                    <div class="form-group">
                      <label for="adresse_secondaire">Adresse</label>
                      <input type="text" class="form-control" id="adresse_secondaire" name="adresse" placeholder="Adresse">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="photo_secondaire">Photo actuelle de l'élève</label>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview-secondaire" src="dist/img/default-student.png" alt="Photo de l'élève" style="width: 100%; height: auto;">
                      </div>
                      <label for="nouvelle_photo_secondaire" style="margin-top: 10px;">Mettre à jour la photo (optionnel)</label>
                      <input type="file" id="nouvelle_photo_secondaire" name="nouvelle_photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                    </div>
                    <div class="form-group">
                      <label for="classe_actuelle_secondaire">Classe actuelle</label>
                      <input type="text" class="form-control" id="classe_actuelle_secondaire" readonly>
                    </div>
                    <div class="form-group">
                      <label for="nouvelle_classe_id_secondaire">Nouvelle classe</label>
                      <select class="form-control" id="nouvelle_classe_id_secondaire" name="nouvelle_classe_id" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes_secondaire as $classe) : ?>
                          <?php if ($classe['section'] == 'secondaire') : ?>
                            <option value="<?php echo $classe['id']; ?>"><?php echo $classe['niveau']; ?></option>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere_secondaire">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere_secondaire" name="contact_pere" placeholder="Contact du père">
                    </div>
                    <div class="form-group">
                      <label for="contact_mere_secondaire">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere_secondaire" name="contact_mere" placeholder="Contact de la mère">
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-warning">Réinscrire</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription" class="btn btn-default">Annuler</a>
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
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2023 <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>

<script>
$(document).ready(function() {
  // Fonction pour afficher les notifications toast
  function showToast(message, type) {
    var toastClass = 'toast-' + type;
    var toast = $('<div class="toast ' + toastClass + '"><span class="toast-close">&times;</span>' + message + '</div>');
    $('#toast-container').append(toast);
    
    // Fermer le toast au clic sur le bouton de fermeture
    toast.find('.toast-close').on('click', function() {
      toast.remove();
    });
    
    // Supprimer automatiquement le toast après 5 secondes
    setTimeout(function() {
      toast.remove();
    }, 5000);
  }
  
  // Gestion de la recherche d'élève pour la section maternelle
  $('#btn-search-eleve').on('click', function() {
    var searchTerm = $('#search-eleve').val();
    if (searchTerm.length < 3) {
      showToast('Veuillez entrer au moins 3 caractères pour la recherche.', 'warning');
      return;
    }
    
    // Appel AJAX pour rechercher l'élève
    $.ajax({
      url: '<?php echo BASE_URL; ?>index.php?controller=comptable&action=rechercherEleve',
      type: 'POST',
      data: {
        search: searchTerm,
        section: 'maternelle'
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Afficher les résultats
          $('#search-results-body').empty();
          if (response.eleves.length > 0) {
            $.each(response.eleves, function(index, eleve) {
              var row = '<tr>' +
                '<td>' + eleve.matricule + '</td>' +
                '<td>' + eleve.nom + '</td>' +
                '<td>' + eleve.post_nom + '</td>' +
                '<td>' + eleve.prenom + '</td>' +
                '<td>' + eleve.classe + '</td>' +
                '<td><button type="button" class="btn btn-primary btn-sm select-eleve" data-id="' + eleve.id + '"><i class="fa fa-check"></i> Sélectionner</button></td>' +
                '</tr>';
              $('#search-results-body').append(row);
            });
            $('#search-results').show();
          } else {
            showToast('Aucun élève trouvé avec ces critères.', 'info');
          }
        } else {
          showToast(response.message, 'error');
        }
      },
      error: function() {
        showToast('Erreur lors de la recherche. Veuillez réessayer.', 'error');
      }
    });
  });
  
  // Gestion de la recherche d'élève pour la section primaire
  $('#btn-search-eleve-primaire').on('click', function() {
    var searchTerm = $('#search-eleve-primaire').val();
    if (searchTerm.length < 3) {
      showToast('Veuillez entrer au moins 3 caractères pour la recherche.', 'warning');
      return;
    }
    
    // Appel AJAX pour rechercher l'élève
    $.ajax({
      url: '<?php echo BASE_URL; ?>index.php?controller=comptable&action=rechercherEleve',
      type: 'POST',
      data: {
        search: searchTerm,
        section: 'primaire'
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Afficher les résultats
          $('#search-results-body-primaire').empty();
          if (response.eleves.length > 0) {
            $.each(response.eleves, function(index, eleve) {
              var row = '<tr>' +
                '<td>' + eleve.matricule + '</td>' +
                '<td>' + eleve.nom + '</td>' +
                '<td>' + eleve.post_nom + '</td>' +
                '<td>' + eleve.prenom + '</td>' +
                '<td>' + eleve.classe + '</td>' +
                '<td><button type="button" class="btn btn-success btn-sm select-eleve-primaire" data-id="' + eleve.id + '"><i class="fa fa-check"></i> Sélectionner</button></td>' +
                '</tr>';
              $('#search-results-body-primaire').append(row);
            });
            $('#search-results-primaire').show();
          } else {
            showToast('Aucun élève trouvé avec ces critères.', 'info');
          }
        } else {
          showToast(response.message, 'error');
        }
      },
      error: function() {
        showToast('Erreur lors de la recherche. Veuillez réessayer.', 'error');
      }
    });
  });
  
  // Gestion de la recherche d'élève pour la section secondaire
  $('#btn-search-eleve-secondaire').on('click', function() {
    var searchTerm = $('#search-eleve-secondaire').val();
    if (searchTerm.length < 3) {
      showToast('Veuillez entrer au moins 3 caractères pour la recherche.', 'warning');
      return;
    }
    
    // Appel AJAX pour rechercher l'élève
    $.ajax({
      url: '<?php echo BASE_URL; ?>index.php?controller=comptable&action=rechercherEleve',
      type: 'POST',
      data: {
        search: searchTerm,
        section: 'secondaire'
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Afficher les résultats
          $('#search-results-body-secondaire').empty();
          if (response.eleves.length > 0) {
            $.each(response.eleves, function(index, eleve) {
              var row = '<tr>' +
                '<td>' + eleve.matricule + '</td>' +
                '<td>' + eleve.nom + '</td>' +
                '<td>' + eleve.post_nom + '</td>' +
                '<td>' + eleve.prenom + '</td>' +
                '<td>' + eleve.classe + '</td>' +
                '<td><button type="button" class="btn btn-warning btn-sm select-eleve-secondaire" data-id="' + eleve.id + '"><i class="fa fa-check"></i> Sélectionner</button></td>' +
                '</tr>';
              $('#search-results-body-secondaire').append(row);
            });
            $('#search-results-secondaire').show();
          } else {
            showToast('Aucun élève trouvé avec ces critères.', 'info');
          }
        } else {
          showToast(response.message, 'error');
        }
      },
      error: function() {
        showToast('Erreur lors de la recherche. Veuillez réessayer.', 'error');
      }
    });
  });
  
  // Sélection d'un élève pour la section maternelle
  $(document).on('click', '.select-eleve', function() {
    var eleveId = $(this).data('id');
    
    // Appel AJAX pour récupérer les détails de l'élève
    $.ajax({
      url: '<?php echo BASE_URL; ?>index.php?controller=comptable&action=getEleveDetails',
      type: 'POST',
      data: {
        eleve_id: eleveId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Remplir le formulaire avec les détails de l'élève
          $('#eleve_id').val(response.eleve.id);
          $('#matricule').val(response.eleve.matricule);
          $('#nom').val(response.eleve.nom);
          $('#post_nom').val(response.eleve.post_nom);
          $('#prenom').val(response.eleve.prenom);
          $('#date_naissance').val(response.eleve.date_naissance);
          $('#adresse').val(response.eleve.adresse);
          $('#contact_pere').val(response.eleve.contact_pere);
          $('#contact_mere').val(response.eleve.contact_mere);
          $('#classe_actuelle').val(response.eleve.classe);
          
          // Afficher la photo de l'élève si disponible
          if (response.eleve.photo) {
            $('#photo-preview').attr('src', response.eleve.photo);
          } else {
            $('#photo-preview').attr('src', 'dist/img/default-student.png');
          }
          
          // Afficher le formulaire de réinscription
          $('#form-reinscription').show();
          
          // Faire défiler jusqu'au formulaire
          $('html, body').animate({
            scrollTop: $('#form-reinscription').offset().top - 100
          }, 500);
        } else {
          showToast(response.message, 'error');
        }
      },
      error: function() {
        showToast('Erreur lors de la récupération des détails de l\'élève. Veuillez réessayer.', 'error');
      }
    });
  });
  
  // Sélection d'un élève pour la section primaire
  $(document).on('click', '.select-eleve-primaire', function() {
    var eleveId = $(this).data('id');
    
    // Appel AJAX pour récupérer les détails de l'élève
    $.ajax({
      url: '<?php echo BASE_URL; ?>index.php?controller=comptable&action=getEleveDetails',
      type: 'POST',
      data: {
        eleve_id: eleveId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Remplir le formulaire avec les détails de l'élève
          $('#eleve_id_primaire').val(response.eleve.id);
          $('#matricule_primaire').val(response.eleve.matricule);
          $('#nom_primaire').val(response.eleve.nom);
          $('#post_nom_primaire').val(response.eleve.post_nom);
          $('#prenom_primaire').val(response.eleve.prenom);
          $('#date_naissance_primaire').val(response.eleve.date_naissance);
          $('#adresse_primaire').val(response.eleve.adresse);
          $('#contact_pere_primaire').val(response.eleve.contact_pere);
          $('#contact_mere_primaire').val(response.eleve.contact_mere);
          $('#classe_actuelle_primaire').val(response.eleve.classe);
          
          // Afficher la photo de l'élève si disponible
          if (response.eleve.photo) {
            $('#photo-preview-primaire').attr('src', response.eleve.photo);
          } else {
            $('#photo-preview-primaire').attr('src', 'dist/img/default-student.png');
          }
          
          // Afficher le formulaire de réinscription
          $('#form-reinscription-primaire').show();
          
          // Faire défiler jusqu'au formulaire
          $('html, body').animate({
            scrollTop: $('#form-reinscription-primaire').offset().top - 100
          }, 500);
        } else {
          showToast(response.message, 'error');
        }
      },
      error: function() {
        showToast('Erreur lors de la récupération des détails de l\'élève. Veuillez réessayer.', 'error');
      }
    });
  });
  
  // Sélection d'un élève pour la section secondaire
  $(document).on('click', '.select-eleve-secondaire', function() {
    var eleveId = $(this).data('id');
    
    // Appel AJAX pour récupérer les détails de l'élève
    $.ajax({
      url: '<?php echo BASE_URL; ?>index.php?controller=comptable&action=getEleveDetails',
      type: 'POST',
      data: {
        eleve_id: eleveId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Remplir le formulaire avec les détails de l'élève
          $('#eleve_id_secondaire').val(response.eleve.id);
          $('#matricule_secondaire').val(response.eleve.matricule);
          $('#nom_secondaire').val(response.eleve.nom);
          $('#post_nom_secondaire').val(response.eleve.post_nom);
          $('#prenom_secondaire').val(response.eleve.prenom);
          $('#date_naissance_secondaire').val(response.eleve.date_naissance);
          $('#adresse_secondaire').val(response.eleve.adresse);
          $('#contact_pere_secondaire').val(response.eleve.contact_pere);
          $('#contact_mere_secondaire').val(response.eleve.contact_mere);
          $('#classe_actuelle_secondaire').val(response.eleve.classe);
          
          // Afficher la photo de l'élève si disponible
          if (response.eleve.photo) {
            $('#photo-preview-secondaire').attr('src', response.eleve.photo);
          } else {
            $('#photo-preview-secondaire').attr('src', 'dist/img/default-student.png');
          }
          
          // Afficher le formulaire de réinscription
          $('#form-reinscription-secondaire').show();
          
          // Faire défiler jusqu'au formulaire
          $('html, body').animate({
            scrollTop: $('#form-reinscription-secondaire').offset().top - 100
          }, 500);
        } else {
          showToast(response.message, 'error');
        }
      },
      error: function() {
        showToast('Erreur lors de la récupération des détails de l\'élève. Veuillez réessayer.', 'error');
      }
    });
  });
  
  // Prévisualisation de la photo avant upload
  function readURL(input, previewId) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      
      reader.onload = function(e) {
        $(previewId).attr('src', e.target.result);
      }
      
      reader.readAsDataURL(input.files[0]);
    }
  }
  
  $("#nouvelle_photo").change(function() {
    readURL(this, "#photo-preview");
  });
  
  $("#nouvelle_photo_primaire").change(function() {
    readURL(this, "#photo-preview-primaire");
  });
  
  $("#nouvelle_photo_secondaire").change(function() {
    readURL(this, "#photo-preview-secondaire");
  });
});
</script>
</body>
</html>