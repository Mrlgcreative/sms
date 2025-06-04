<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/DirecteurEtude.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'directeur_etudes') {
    header('Location: ../../login.php');
    exit();
}

$directeur = new DirecteurEtude();
$classe_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$classe_id) {
    header('Location: classes.php');
    exit();
}

// Récupérer les informations de la classe
$stmt = $pdo->prepare("SELECT c.*, n.nom as niveau_nom, s.nom as section_nom 
                      FROM classes c 
                      JOIN niveaux n ON c.niveau_id = n.id 
                      JOIN sections s ON c.section_id = s.id 
                      WHERE c.id = ?");
$stmt->execute([$classe_id]);
$classe = $stmt->fetch();

if (!$classe) {
    header('Location: classes.php');
    exit();
}

// Statistiques de la classe
$stmt = $pdo->prepare("SELECT COUNT(*) as total_eleves FROM eleves WHERE classe_id = ?");
$stmt->execute([$classe_id]);
$stats_eleves = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT c.id) as total_cours 
                      FROM cours c 
                      JOIN cours_classes cc ON c.id = cc.cours_id 
                      WHERE cc.classe_id = ?");
$stmt->execute([$classe_id]);
$stats_cours = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as total_examens 
                      FROM examens e 
                      JOIN cours c ON e.cours_id = c.id 
                      JOIN cours_classes cc ON c.id = cc.cours_id 
                      WHERE cc.classe_id = ?");
$stmt->execute([$classe_id]);
$stats_examens = $stmt->fetch();

// Moyenne générale de la classe
$stmt = $pdo->prepare("SELECT AVG(n.note) as moyenne_classe 
                      FROM notes n 
                      JOIN examens e ON n.examen_id = e.id 
                      JOIN cours c ON e.cours_id = c.id 
                      JOIN cours_classes cc ON c.id = cc.cours_id 
                      WHERE cc.classe_id = ?");
$stmt->execute([$classe_id]);
$moyenne_result = $stmt->fetch();
$moyenne_classe = $moyenne_result['moyenne_classe'] ? round($moyenne_result['moyenne_classe'], 2) : 0;

// Élèves de la classe
$stmt = $pdo->prepare("SELECT e.*, u.prenom, u.nom 
                      FROM eleves e 
                      JOIN users u ON e.user_id = u.id 
                      WHERE e.classe_id = ? 
                      ORDER BY u.nom, u.prenom");
$stmt->execute([$classe_id]);
$eleves = $stmt->fetchAll();

// Cours de la classe
$stmt = $pdo->prepare("SELECT c.*, m.nom as matiere_nom, u.prenom as prof_prenom, u.nom as prof_nom 
                      FROM cours c 
                      JOIN matieres m ON c.matiere_id = m.id 
                      JOIN cours_classes cc ON c.id = cc.cours_id 
                      LEFT JOIN users u ON c.professeur_id = u.id 
                      WHERE cc.classe_id = ? 
                      ORDER BY m.nom");
$stmt->execute([$classe_id]);
$cours = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Détails de la Classe</title>
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
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes">
            <i class="fa fa-university"></i> <span>Gestion des Classes</span>
          </a>
        </li>
        
        <li>
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
  </aside>  <!-- Content Wrapper -->
  <div class="content-wrapper">    <!-- Content Header -->
    <section class="content-header">
      <h1>
        Détails de la Classe: <?= htmlspecialchars($classe['nom']) ?>
        <small>Informations détaillées</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes">Classes</a></li>
        <li class="active"><?= htmlspecialchars($classe['nom']) ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Élèves</span>
              <span class="info-box-number"><?= $stats_eleves['total_eleves'] ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-book"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Cours</span>
              <span class="info-box-number"><?= $stats_cours['total_cours'] ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-edit"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Examens</span>
              <span class="info-box-number"><?= $stats_examens['total_examens'] ?></span>
            </div>
          </div>
        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-<?= $moyenne_classe >= 10 ? 'success' : 'danger' ?> elevation-1">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Moyenne</span>
                                <span class="info-box-number"><?= $moyenne_classe ?>/20</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de la classe -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informations de la Classe</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Nom de la classe:</th>
                                        <td><?= htmlspecialchars($classe['nom']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Niveau:</th>
                                        <td><?= htmlspecialchars($classe['niveau_nom']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Section:</th>
                                        <td>
                                            <span class="badge badge-<?= $classe['section_nom'] == 'Primaire' ? 'primary' : 'secondary' ?>">
                                                <?= htmlspecialchars($classe['section_nom']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Capacité max:</th>
                                        <td><?= $classe['capacite_max'] ?> élèves</td>
                                    </tr>
                                    <tr>
                                        <th>Année scolaire:</th>
                                        <td><?= htmlspecialchars($classe['annee_scolaire']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut:</th>
                                        <td>
                                            <span class="badge badge-<?= $classe['statut'] == 'active' ? 'success' : 'danger' ?>">
                                                <?= $classe['statut'] == 'active' ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="btn-group-vertical d-block">
                                    <a href="elevesClasse.php?classe_id=<?= $classe_id ?>" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-users"></i> Gérer les Élèves
                                    </a>
                                    <a href="emploiDuTemps.php?classe_id=<?= $classe_id ?>" class="btn btn-warning btn-block mb-2">
                                        <i class="fas fa-calendar-alt"></i> Emploi du Temps
                                    </a>
                                    <button class="btn btn-success btn-block mb-2" onclick="exporterListeEleves()">
                                        <i class="fas fa-download"></i> Exporter Liste Élèves
                                    </button>
                                    <button class="btn btn-primary btn-block mb-2" onclick="genererRapport()">
                                        <i class="fas fa-chart-bar"></i> Rapport de Classe
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des élèves -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Liste des Élèves</h3>
                    </div>
                    <div class="card-body">
                        <table id="elevesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom complet</th>
                                    <th>Date de naissance</th>
                                    <th>Sexe</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eleves as $eleve): ?>
                                <tr>
                                    <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                                    <td><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($eleve['date_naissance'])) ?></td>
                                    <td>
                                        <i class="fas fa-<?= $eleve['sexe'] == 'M' ? 'mars' : 'venus' ?> text-<?= $eleve['sexe'] == 'M' ? 'primary' : 'pink' ?>"></i>
                                        <?= $eleve['sexe'] == 'M' ? 'Masculin' : 'Féminin' ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $eleve['statut'] == 'active' ? 'success' : 'danger' ?>">
                                            <?= $eleve['statut'] == 'active' ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_student.php?id=<?= $eleve['id'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="carte_eleve.php?id=<?= $eleve['id'] ?>" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-id-card"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Liste des cours -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cours de la Classe</h3>
                    </div>
                    <div class="card-body">
                        <table id="coursTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Professeur</th>
                                    <th>Heures/semaine</th>
                                    <th>Coefficient</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cours as $cour): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cour['matiere_nom']) ?></td>
                                    <td>
                                        <?php if ($cour['prof_prenom']): ?>
                                            <?= htmlspecialchars($cour['prof_prenom'] . ' ' . $cour['prof_nom']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Non assigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $cour['heures_par_semaine'] ?>h</td>
                                    <td><?= $cour['coefficient'] ?></td>
                                    <td>
                                        <a href="voirCours.php?id=<?= $cour['id'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
$(function () {
    $("#elevesTable, #coursTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        }
    });
});

function exporterListeEleves() {
    window.open('../../exports/liste_eleves_classe.php?classe_id=<?= $classe_id ?>', '_blank');
}

function genererRapport() {
    window.open('../../reports/rapport_classe.php?classe_id=<?= $classe_id ?>', '_blank');
}
</script>

</body>
</html>
