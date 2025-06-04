<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/DirecteurEtude.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'directeur_etudes') {
    header('Location: ../../login.php');
    exit();
}

$directeur = new DirecteurEtude();
$cours_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$cours_id) {
    header('Location: cours.php');
    exit();
}

// Récupérer les informations du cours
$stmt = $pdo->prepare("SELECT c.*, m.nom as matiere_nom, u.prenom as prof_prenom, u.nom as prof_nom,
                              s.nom as section_nom, n.nom as niveau_nom
                      FROM cours c 
                      JOIN matieres m ON c.matiere_id = m.id 
                      LEFT JOIN users u ON c.professeur_id = u.id 
                      JOIN sections s ON c.section_id = s.id
                      JOIN niveaux n ON c.niveau_id = n.id
                      WHERE c.id = ?");
$stmt->execute([$cours_id]);
$cours = $stmt->fetch();

if (!$cours) {
    header('Location: cours.php');
    exit();
}

// Classes assignées à ce cours
$stmt = $pdo->prepare("SELECT cl.*, cc.id as assignation_id 
                      FROM classes cl 
                      JOIN cours_classes cc ON cl.id = cc.classe_id 
                      WHERE cc.cours_id = ?
                      ORDER BY cl.nom");
$stmt->execute([$cours_id]);
$classes_assignees = $stmt->fetchAll();

// Examens du cours
$stmt = $pdo->prepare("SELECT e.*, 
                              COUNT(n.id) as nb_notes,
                              AVG(n.note) as moyenne_examen
                      FROM examens e 
                      LEFT JOIN notes n ON e.id = n.examen_id 
                      WHERE e.cours_id = ? 
                      GROUP BY e.id
                      ORDER BY e.date_examen DESC");
$stmt->execute([$cours_id]);
$examens = $stmt->fetchAll();

// Statistiques des élèves
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT el.id) as total_eleves
                      FROM eleves el
                      JOIN classes cl ON el.classe_id = cl.id
                      JOIN cours_classes cc ON cl.id = cc.classe_id
                      WHERE cc.cours_id = ?");
$stmt->execute([$cours_id]);
$stats_eleves = $stmt->fetch();

// Moyenne générale du cours
$stmt = $pdo->prepare("SELECT AVG(n.note) as moyenne_generale
                      FROM notes n 
                      JOIN examens e ON n.examen_id = e.id 
                      WHERE e.cours_id = ?");
$stmt->execute([$cours_id]);
$moyenne_result = $stmt->fetch();
$moyenne_generale = $moyenne_result['moyenne_generale'] ? round($moyenne_result['moyenne_generale'], 2) : 0;

// Planning des cours (emploi du temps)
$stmt = $pdo->prepare("SELECT et.*, cl.nom as classe_nom
                      FROM emploi_temps et
                      JOIN classes cl ON et.classe_id = cl.id
                      WHERE et.cours_id = ?
                      ORDER BY et.jour_semaine, et.heure_debut");
$stmt->execute([$cours_id]);
$emploi_temps = $stmt->fetchAll();

// Ressources pédagogiques
$stmt = $pdo->prepare("SELECT * FROM ressources_pedagogiques 
                      WHERE cours_id = ? 
                      ORDER BY date_ajout DESC");
$stmt->execute([$cours_id]);
$ressources = $stmt->fetchAll();

$jours_semaine = [
    1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 
    4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'
];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Détails du Cours</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil" class="logo">
      <span class="logo-mini"><b>SMS</b></span>
      <span class="logo-lg"><b>School</b>MS</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Utilisateur'; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                <p>
                  <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Utilisateur'; ?>
                  <small>Directeur des Études</small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=profil" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>actions/logout.php" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Utilisateur'; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves">
            <i class="fa fa-graduation-cap"></i> <span>Gestion des Élèves</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs">
            <i class="fa fa-users"></i> <span>Gestion des Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=programmesScolaires">
            <i class="fa fa-book"></i> <span>Programmes Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes">
            <i class="fa fa-university"></i> <span>Gestion des Classes</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours">
            <i class="fa fa-calendar"></i> <span>Gestion des Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=examens">
            <i class="fa fa-edit"></i> <span>Gestion des Examens</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=resultatsScolaires">
            <i class="fa fa-bar-chart"></i> <span>Résultats Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTemps">
            <i class="fa fa-table"></i> <span>Emplois du temps</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires">
            <i class="fa fa-calendar-check-o"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapportsGlobaux">
            <i class="fa fa-pie-chart"></i> <span>Rapports Globaux</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=communications">
            <i class="fa fa-envelope"></i> <span>Communications</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>
    <title>Détails du Cours | SMS</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <!-- Chart.js -->
    <script src="../../plugins/chart.js/Chart.min.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="accueil.php" class="brand-link">
            <i class="fas fa-graduation-cap brand-image img-circle elevation-3"></i>
            <span class="brand-text font-weight-light">SMS - Directeur</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="accueil.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Tableau de bord</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="eleves.php" class="nav-link">
                            <i class="nav-icon fas fa-user-graduate"></i>
                            <p>Élèves</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="classes.php" class="nav-link">
                            <i class="nav-icon fas fa-school"></i>
                            <p>Classes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="cours.php" class="nav-link active">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Cours</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="examens.php" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Examens</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="resultatsScolaires.php" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Résultats</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="programmesScolaires.php" class="nav-link">
                            <i class="nav-icon fas fa-list-alt"></i>
                            <p>Programmes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="communications.php" class="nav-link">
                            <i class="nav-icon fas fa-bullhorn"></i>
                            <p>Communications</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profil.php" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Mon Profil</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Cours: <?= htmlspecialchars($cours['matiere_nom']) ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="accueil.php">Accueil</a></li>
                            <li class="breadcrumb-item"><a href="cours.php">Cours</a></li>
                            <li class="breadcrumb-item active"><?= htmlspecialchars($cours['matiere_nom']) ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Élèves</span>
                                <span class="info-box-number"><?= $stats_eleves['total_eleves'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-school"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Classes</span>
                                <span class="info-box-number"><?= count($classes_assignees) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-clipboard-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Examens</span>
                                <span class="info-box-number"><?= count($examens) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-<?= $moyenne_generale >= 10 ? 'success' : 'danger' ?> elevation-1">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Moyenne</span>
                                <span class="info-box-number"><?= $moyenne_generale ?>/20</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Informations du cours -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informations du Cours</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Matière:</th>
                                        <td><?= htmlspecialchars($cours['matiere_nom']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Professeur:</th>
                                        <td>
                                            <?php if ($cours['prof_prenom']): ?>
                                                <i class="fas fa-user text-success"></i>
                                                <?= htmlspecialchars($cours['prof_prenom'] . ' ' . $cours['prof_nom']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <i class="fas fa-user-times text-warning"></i>
                                                    Non assigné
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Section:</th>
                                        <td>
                                            <span class="badge badge-<?= $cours['section_nom'] == 'Primaire' ? 'primary' : 'secondary' ?>">
                                                <?= htmlspecialchars($cours['section_nom']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Niveau:</th>
                                        <td><?= htmlspecialchars($cours['niveau_nom']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Heures/semaine:</th>
                                        <td><?= $cours['heures_par_semaine'] ?>h</td>
                                    </tr>
                                    <tr>
                                        <th>Coefficient:</th>
                                        <td><?= $cours['coefficient'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Année scolaire:</th>
                                        <td><?= htmlspecialchars($cours['annee_scolaire']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Actions et ressources -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Actions Rapides</h3>
                            </div>
                            <div class="card-body">
                                <div class="btn-group-vertical d-block">
                                    <button class="btn btn-primary btn-block mb-2" onclick="creerExamen()">
                                        <i class="fas fa-plus"></i> Créer un Examen
                                    </button>
                                    <button class="btn btn-info btn-block mb-2" onclick="consulterNotes()">
                                        <i class="fas fa-chart-bar"></i> Consulter les Notes
                                    </button>
                                    <button class="btn btn-warning btn-block mb-2" onclick="genererRapport()">
                                        <i class="fas fa-file-pdf"></i> Rapport du Cours
                                    </button>
                                    <button class="btn btn-success btn-block mb-2" onclick="ajouterRessource()">
                                        <i class="fas fa-upload"></i> Ajouter Ressource
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($ressources)): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Ressources Pédagogiques</h3>
                            </div>
                            <div class="card-body">
                                <?php foreach (array_slice($ressources, 0, 5) as $ressource): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file-alt text-info mr-2"></i>
                                    <a href="../../uploads/ressources/<?= $ressource['fichier'] ?>" target="_blank">
                                        <?= htmlspecialchars($ressource['titre']) ?>
                                    </a>
                                    <small class="text-muted ml-auto">
                                        <?= date('d/m/Y', strtotime($ressource['date_ajout'])) ?>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                                <?php if (count($ressources) > 5): ?>
                                <p class="text-center">
                                    <a href="#" onclick="voirToutesRessources()">Voir toutes les ressources...</a>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Classes assignées -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Classes Assignées</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($classes_assignees as $classe): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary elevation-1">
                                        <i class="fas fa-school"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?= htmlspecialchars($classe['nom']) ?></span>
                                        <span class="info-box-number">
                                            <?php
                                            // Compter les élèves de cette classe
                                            $stmt = $pdo->prepare("SELECT COUNT(*) as nb FROM eleves WHERE classe_id = ?");
                                            $stmt->execute([$classe['id']]);
                                            $nb_eleves = $stmt->fetch();
                                            echo $nb_eleves['nb'];
                                            ?> élèves
                                        </span>
                                        <div class="info-box-more">
                                            <a href="voirClasse.php?id=<?= $classe['id'] ?>" class="btn btn-sm btn-primary">
                                                Voir détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Emploi du temps -->
                <?php if (!empty($emploi_temps)): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Emploi du Temps</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Jour</th>
                                    <th>Classe</th>
                                    <th>Heure début</th>
                                    <th>Heure fin</th>
                                    <th>Salle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($emploi_temps as $seance): ?>
                                <tr>
                                    <td>
                                        <strong><?= $jours_semaine[$seance['jour_semaine']] ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($seance['classe_nom']) ?></td>
                                    <td><?= date('H:i', strtotime($seance['heure_debut'])) ?></td>
                                    <td><?= date('H:i', strtotime($seance['heure_fin'])) ?></td>
                                    <td>
                                        <?= $seance['salle'] ? htmlspecialchars($seance['salle']) : '<span class="text-muted">Non définie</span>' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Examens -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Examens du Cours</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($examens)): ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Coefficient</th>
                                    <th>Notes saisies</th>
                                    <th>Moyenne</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($examens as $examen): ?>
                                <tr>
                                    <td><?= htmlspecialchars($examen['titre']) ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= htmlspecialchars($examen['type']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($examen['date_examen'])) ?></td>
                                    <td><?= $examen['coefficient'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= $examen['nb_notes'] > 0 ? 'success' : 'warning' ?>">
                                            <?= $examen['nb_notes'] ?> notes
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($examen['moyenne_examen']): ?>
                                            <span class="badge badge-<?= $examen['moyenne_examen'] >= 10 ? 'success' : 'danger' ?>">
                                                <?= round($examen['moyenne_examen'], 2) ?>/20
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="examens.php?voir=<?= $examen['id'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Aucun examen créé pour ce cours.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Graphique des performances -->
                <?php if (!empty($examens)): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Évolution des Performances</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart" style="height: 300px;"></canvas>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2024 SMS.</strong> Tous droits réservés.
    </footer>
</div>

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>

<script>
// Graphique des performances
<?php if (!empty($examens)): ?>
var ctx = document.getElementById('performanceChart').getContext('2d');
var performanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?= implode(',', array_map(function($e) { return '"' . date('d/m/Y', strtotime($e['date_examen'])) . '"'; }, $examens)) ?>],
        datasets: [{
            label: 'Moyenne de l\'examen',
            data: [<?= implode(',', array_map(function($e) { return $e['moyenne_examen'] ? round($e['moyenne_examen'], 2) : 0; }, $examens)) ?>],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 20,
                title: {
                    display: true,
                    text: 'Note sur 20'
                }
            }
        },
        plugins: {
            legend: {
                display: true
            }
        }
    }
});
<?php endif; ?>

function creerExamen() {
    window.location.href = 'examens.php?nouveau&cours_id=<?= $cours_id ?>';
}

function consulterNotes() {
    window.location.href = 'resultatsScolaires.php?cours_id=<?= $cours_id ?>';
}

function genererRapport() {
    window.open('../../reports/rapport_cours.php?cours_id=<?= $cours_id ?>', '_blank');
}

function ajouterRessource() {
    // Modal ou redirection vers page d'ajout de ressource
    alert('Fonctionnalité à implémenter');
}

function voirToutesRessources() {
    // Modal ou page dédiée aux ressources
    alert('Fonctionnalité à implémenter');
}
</script>

</body>
</html>
