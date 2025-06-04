<?php
// Assurez-vous que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer les informations de l'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Statistiques générales pour les rapports
$stats = [];

// Statistiques des élèves
$query_eleves = "SELECT 
                    COUNT(*) as total_eleves,
                    COUNT(CASE WHEN sexe = 'M' THEN 1 END) as total_garcons,
                    COUNT(CASE WHEN sexe = 'F' THEN 1 END) as total_filles,
                    COUNT(DISTINCT classe_id) as classes_avec_eleves
                 FROM eleves 
                 WHERE section = 'secondaire'";
$result_eleves = $mysqli->query($query_eleves);
$stats['eleves'] = $result_eleves->fetch_assoc();

// Statistiques des professeurs
$query_profs = "SELECT 
                    COUNT(*) as total_professeurs,
                    COUNT(CASE WHEN sexe = 'M' THEN 1 END) as profs_hommes,
                    COUNT(CASE WHEN sexe = 'F' THEN 1 END) as profs_femmes
                FROM professeurs 
                WHERE section = 'Secondaire' OR section = 'Tous'";
$result_profs = $mysqli->query($query_profs);
$stats['professeurs'] = $result_profs->fetch_assoc();

// Statistiques des classes
$query_classes = "SELECT 
                     COUNT(*) as total_classes,
                     COUNT(DISTINCT niveau) as niveaux_differents
                  FROM classes";
$result_classes = $mysqli->query($query_classes);
$stats['classes'] = $result_classes->fetch_assoc();

// Statistiques des cours
$query_cours = "SELECT 
                   COUNT(*) as total_cours,
                   COUNT(DISTINCT titre) as matieres_differentes
                FROM cours";
$result_cours = $mysqli->query($query_cours);
$stats['cours'] = $result_cours->fetch_assoc();

// Statistiques des examens (si la table existe)
// $query_examens = "SELECT 
//                      COUNT(*) as total_examens,
//                      COUNT(CASE WHEN date_examen >= CURDATE() THEN 1 END) as examens_a_venir,
//                      COUNT(CASE WHEN date_examen < CURDATE() THEN 1 END) as examens_passes
//                   FROM examens";
// $result_examens = $mysqli->query($query_examens);
// if ($result_examens) {
//     $stats['examens'] = $result_examens->fetch_assoc();
// } else {
//     $stats['examens'] = ['total_examens' => 0, 'examens_a_venir' => 0, 'examens_passes' => 0];
// }

// Répartition des élèves par niveau
$query_repartition = "SELECT c.niveau, COUNT(e.id) as nb_eleves
                      FROM classes c
                      LEFT JOIN eleves e ON c.id = e.classe_id AND e.section = 'secondaire'
                      WHERE c.section = 'secondaire'
                      GROUP BY c.niveau
                      ORDER BY c.niveau";
$result_repartition = $mysqli->query($query_repartition);
$repartition_niveaux = [];

// Debug - Vérifier la requête
echo "<!-- Debug requête: " . $query_repartition . " -->\n";
if (!$result_repartition) {
    echo "<!-- Erreur SQL: " . $mysqli->error . " -->\n";
} else {
    echo "<!-- Nombre de lignes retournées: " . $result_repartition->num_rows . " -->\n";
    while ($row = $result_repartition->fetch_assoc()) {
        $repartition_niveaux[] = $row;
        echo "<!-- Niveau trouvé: " . $row['niveau'] . " avec " . $row['nb_eleves'] . " élèves -->\n";
    }
}

// Debugging: Vérifier si on a des données
echo "<!-- Debug: Nombre de niveaux trouvés: " . count($repartition_niveaux) . " -->\n";

// Si aucune donnée avec la première requête, essayer une requête simplifiée
if (empty($repartition_niveaux)) {
    echo "<!-- Tentative avec requête simplifiée -->\n";
    $query_repartition_simple = "SELECT c.niveau, 
                                  (SELECT COUNT(*) FROM eleves e WHERE e.classe_id = c.id AND e.section = 'secondaire') as nb_eleves
                                  FROM classes c 
                                  WHERE c.section = 'secondaire'
                                  ORDER BY c.niveau";
    $result_simple = $mysqli->query($query_repartition_simple);
    if ($result_simple) {
        while ($row = $result_simple->fetch_assoc()) {
            if ($row['nb_eleves'] > 0) { // Ne prendre que les niveaux avec des élèves
                $repartition_niveaux[] = $row;
                echo "<!-- Niveau (simple): " . $row['niveau'] . " avec " . $row['nb_eleves'] . " élèves -->\n";
            }
        }
    }
}

// Moyennes par niveau (initialisation par défaut)
// Vérifier si la table notes existe
$moyennes_niveaux = [];
$table_notes_exists = false;

// Vérifier l'existence de la table notes
$check_table = $mysqli->query("SHOW TABLES LIKE 'notes'");
if ($check_table && $check_table->num_rows > 0) {
    $table_notes_exists = true;
    
    // Si la table notes existe, récupérer les moyennes
    $query_moyennes = "SELECT c.niveau, AVG(n.note) as moyenne_niveau, COUNT(n.id) as nb_notes
                       FROM notes n
                       JOIN eleves e ON n.eleve_id = e.id
                       JOIN classes c ON e.classe_id = c.id
                       WHERE e.section = 'secondaire'
                       GROUP BY c.niveau
                       ORDER BY c.niveau";
    $result_moyennes = $mysqli->query($query_moyennes);
    if ($result_moyennes) {
        while ($row = $result_moyennes->fetch_assoc()) {
            $moyennes_niveaux[] = $row;
        }
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Rapports Globaux</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <!-- Chart.js -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.min.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil" class="logo">
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
              <span class="hidden-xs"><?php echo htmlspecialchars($username); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo htmlspecialchars($username); ?> - <?php echo htmlspecialchars($role); ?>
                  <small>Connecté</small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <p><?php echo htmlspecialchars($username); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
        <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves">
            <i class="fa fa-graduation-cap"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs">
            <i class="fa fa-users"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes">
            <i class="fa fa-university"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours">
            <i class="fa fa-calendar"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=examens">
            <i class="fa fa-edit"></i> <span>Examens</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=resultatsScolaires">
            <i class="fa fa-bar-chart"></i> <span>Résultats</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTemps">
            <i class="fa fa-table"></i> <span>Emplois du temps</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires">
            <i class="fa fa-calendar-check-o"></i> <span>Événements</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapports">
            <i class="fa fa-pie-chart"></i> <span>Rapports</span>
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

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Rapports Globaux
        <small>Vue d'ensemble des statistiques de l'établissement</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Rapports Globaux</li>
      </ol>
    </section>

    <section class="content">
      <!-- Statistiques générales -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo number_format($stats['eleves']['total_eleves']); ?></h3>
              <p>Total Élèves</p>
            </div>
            <div class="icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves" class="small-box-footer">
              Plus d'infos <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo number_format($stats['professeurs']['total_professeurs']); ?></h3>
              <p>Total Professeurs</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs" class="small-box-footer">
              Plus d'infos <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo number_format($stats['classes']['total_classes']); ?></h3>
              <p>Total Classes</p>
            </div>
            <div class="icon">
              <i class="fa fa-university"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes" class="small-box-footer">
              Plus d'infos <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo number_format($stats['cours']['total_cours']); ?></h3>
              <p>Total Cours</p>
            </div>
            <div class="icon">
              <i class="fa fa-book"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours" class="small-box-footer">
              Plus d'infos <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Graphiques et analyses -->
      <div class="row">
        <!-- Répartition par sexe des élèves -->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des Élèves par Sexe</h3>
            </div>
            <div class="box-body">
              <canvas id="pieChartEleves" style="height:250px"></canvas>
              <div class="text-center" style="margin-top: 15px;">
                <span class="description-percentage text-green">
                  <i class="fa fa-male"></i> Garçons: <?php echo $stats['eleves']['total_garcons']; ?> (<?php echo $stats['eleves']['total_eleves'] > 0 ? round(($stats['eleves']['total_garcons']/$stats['eleves']['total_eleves'])*100, 1) : 0; ?>%)
                </span>
                <br>
                <span class="description-percentage text-red">
                  <i class="fa fa-female"></i> Filles: <?php echo $stats['eleves']['total_filles']; ?> (<?php echo $stats['eleves']['total_eleves'] > 0 ? round(($stats['eleves']['total_filles']/$stats['eleves']['total_eleves'])*100, 1) : 0; ?>%)
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Répartition par sexe des professeurs -->
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des Professeurs par Sexe</h3>
            </div>
            <div class="box-body">
              <canvas id="pieChartProfesseurs" style="height:250px"></canvas>
              <div class="text-center" style="margin-top: 15px;">
                <span class="description-percentage text-blue">
                  <i class="fa fa-male"></i> Hommes: <?php echo $stats['professeurs']['profs_hommes']; ?> (<?php echo $stats['professeurs']['total_professeurs'] > 0 ? round(($stats['professeurs']['profs_hommes']/$stats['professeurs']['total_professeurs'])*100, 1) : 0; ?>%)
                </span>
                <br>
                <span class="description-percentage text-red">
                  <i class="fa fa-female"></i> Femmes: <?php echo $stats['professeurs']['profs_femmes']; ?> (<?php echo $stats['professeurs']['total_professeurs'] > 0 ? round(($stats['professeurs']['profs_femmes']/$stats['professeurs']['total_professeurs'])*100, 1) : 0; ?>%)
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>      <!-- Répartition des élèves par niveau -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des Élèves par Niveau</h3>
            </div>
            <div class="box-body">
              <?php if (!empty($repartition_niveaux)): ?>
                <canvas id="barChartNiveaux" style="height:300px"></canvas>
              <?php else: ?>
                <div class="alert alert-info">
                  <h4><i class="icon fa fa-info-circle"></i> Information</h4>
                  Aucune donnée de répartition disponible pour le moment. 
                  Vérifiez que les classes et élèves sont correctement configurés dans le système.
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Moyennes par niveau -->
      <?php if (!empty($moyennes_niveaux)): ?>
      <div class="row">
        <div class="col-md-12">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Moyennes Académiques par Niveau</h3>
            </div>
            <div class="box-body">
              <canvas id="lineChartMoyennes" style="height:300px"></canvas>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Tableau récapitulatif -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Récapitulatif par Niveau</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Niveau</th>
                      <th>Nombre d'Élèves</th>
                      <th>Moyenne Générale</th>
                      <th>Statut</th>
                    </tr>
                  </thead>                  <tbody>
                    <?php if (!empty($repartition_niveaux)): ?>
                      <?php 
                      foreach ($repartition_niveaux as $niveau): 
                        $moyenne = 0;
                        if (!empty($moyennes_niveaux)) {
                          foreach ($moyennes_niveaux as $moy) {
                            if ($moy['niveau'] == $niveau['niveau']) {
                              $moyenne = round($moy['moyenne_niveau'], 2);
                              break;
                            }
                          }
                        }
                      ?>
                        <tr>
                          <td><strong><?php echo htmlspecialchars($niveau['niveau']); ?></strong></td>
                          <td><span class="badge bg-blue"><?php echo $niveau['nb_eleves']; ?></span></td>
                          <td>
                            <?php if ($moyenne > 0): ?>
                              <span class="badge <?php echo $moyenne >= 10 ? 'bg-green' : 'bg-red'; ?>"><?php echo $moyenne; ?>/20</span>
                            <?php else: ?>
                              <span class="text-muted">Non disponible</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php if ($moyenne >= 15): ?>
                              <span class="label label-success">Excellent</span>
                            <?php elseif ($moyenne >= 12): ?>
                              <span class="label label-info">Bien</span>
                            <?php elseif ($moyenne >= 10): ?>
                              <span class="label label-warning">Passable</span>
                            <?php elseif ($moyenne > 0): ?>
                              <span class="label label-danger">À améliorer</span>
                            <?php else: ?>
                              <span class="label label-default">N/A</span>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center text-muted">
                          <i class="fa fa-info-circle"></i> Aucune donnée de répartition disponible pour le moment.
                          <br><small>Vérifiez la configuration des classes et l'inscription des élèves.</small>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid bg-light-blue-gradient">
            <div class="box-header">
              <i class="fa fa-th"></i>
              <h3 class="box-title">Actions Rapides</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-3 col-sm-6">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves" class="btn btn-app">
                    <i class="fa fa-graduation-cap"></i>
                    Voir Élèves
                  </a>
                </div>
                <div class="col-md-3 col-sm-6">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs" class="btn btn-app">
                    <i class="fa fa-users"></i>
                    Voir Professeurs
                  </a>
                </div>
                <div class="col-md-3 col-sm-6">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=resultatsScolaires" class="btn btn-app">
                    <i class="fa fa-trophy"></i>
                    Résultats
                  </a>
                </div>
                <div class="col-md-3 col-sm-6">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=examens" class="btn btn-app">
                    <i class="fa fa-edit"></i>
                    Examens
                  </a>
                </div>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SMS St Sophie</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Chart.js -->
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Graphique en secteurs pour les élèves
  var pieChartCanvasEleves = $('#pieChartEleves').get(0).getContext('2d');
  var pieDataEleves = {
    labels: ['Garçons', 'Filles'],
    datasets: [
      {
        data: [<?php echo $stats['eleves']['total_garcons']; ?>, <?php echo $stats['eleves']['total_filles']; ?>],
        backgroundColor: ['#3c8dbc', '#f56954']
      }
    ]
  };
  var pieOptionsEleves = {
    maintainAspectRatio: false,
    responsive: true,
  };
  new Chart(pieChartCanvasEleves, {
    type: 'pie',
    data: pieDataEleves,
    options: pieOptionsEleves
  });

  // Graphique en secteurs pour les professeurs
  var pieChartCanvasProfesseurs = $('#pieChartProfesseurs').get(0).getContext('2d');
  var pieDataProfesseurs = {
    labels: ['Hommes', 'Femmes'],
    datasets: [
      {
        data: [<?php echo $stats['professeurs']['profs_hommes']; ?>, <?php echo $stats['professeurs']['profs_femmes']; ?>],
        backgroundColor: ['#00a65a', '#f56954']
      }
    ]
  };
  new Chart(pieChartCanvasProfesseurs, {
    type: 'pie',
    data: pieDataProfesseurs,
    options: pieOptionsEleves
  });

  <?php if (!empty($repartition_niveaux)): ?>
  // Graphique en barres pour les niveaux
  var barChartCanvas = $('#barChartNiveaux').get(0).getContext('2d');
  var barChartData = {
    labels: [<?php echo '"' . implode('", "', array_column($repartition_niveaux, 'niveau')) . '"'; ?>],
    datasets: [
      {
        label: 'Nombre d\'élèves',
        backgroundColor: 'rgba(60,141,188,0.9)',
        borderColor: 'rgba(60,141,188,0.8)',
        data: [<?php echo implode(', ', array_column($repartition_niveaux, 'nb_eleves')); ?>]
      }
    ]
  };
  var barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  };
  new Chart(barChartCanvas, {
    type: 'bar',
    data: barChartData,
    options: barChartOptions
  });
  <?php endif; ?>

  <?php if (!empty($moyennes_niveaux)): ?>
  // Graphique linéaire pour les moyennes
  var lineChartCanvas = $('#lineChartMoyennes').get(0).getContext('2d');
  var lineChartData = {
    labels: [<?php echo '"' . implode('", "', array_column($moyennes_niveaux, 'niveau')) . '"'; ?>],
    datasets: [
      {
        label: 'Moyenne sur 20',
        backgroundColor: 'rgba(255,193,7,0.2)',
        borderColor: 'rgba(255,193,7,1)',
        data: [<?php echo implode(', ', array_map(function($item) { return round($item['moyenne_niveau'], 2); }, $moyennes_niveaux)); ?>],
        fill: true,
        tension: 0.4
      }
    ]
  };
  var lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        max: 20
      }
    }
  };
  new Chart(lineChartCanvas, {
    type: 'line',
    data: lineChartData,
    options: lineChartOptions
  });
  <?php endif; ?>
});
</script>

</body>
</html>
