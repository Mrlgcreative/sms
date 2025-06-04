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

// Récupération des résultats scolaires avec informations des élèves, matières et examens
$query = "SELECT e.nom, e.prenom, e.matricule,
                 c.niveau as classe_nom, c.section,
                 m.nom as matiere_nom,
                 ex.type_examen, ex.date_examen,
                 n.note, n.appreciation,
                 AVG(n.note) OVER (PARTITION BY e.id, m.id) as moyenne_matiere,
                 AVG(n.note) OVER (PARTITION BY e.id) as moyenne_generale
          FROM notes n
          JOIN eleves e ON n.eleve_id = e.id
          JOIN examens ex ON n.examen_id = ex.id
          JOIN matieres m ON ex.matiere_id = m.id
          JOIN classes c ON e.classe_id = c.id
          ORDER BY e.nom, e.prenom, m.nom, ex.date_examen DESC";
$result = $mysqli->query($query);

$resultats = [];
$eleves_stats = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $resultats[] = $row;
        
        // Calculer les statistiques par élève
        $key = $row['matricule'];
        if (!isset($eleves_stats[$key])) {
            $eleves_stats[$key] = [
                'nom' => $row['nom'],
                'prenom' => $row['prenom'],
                'matricule' => $row['matricule'],
                'classe' => $row['classe_nom'],
                'section' => $row['section'],
                'notes' => [],
                'moyenne_generale' => 0
            ];
        }
        $eleves_stats[$key]['notes'][] = $row['note'];
        $eleves_stats[$key]['moyenne_generale'] = $row['moyenne_generale'];
    }
}

// Statistiques générales
$stats_query = "SELECT 
                  COUNT(DISTINCT e.id) as total_eleves_notes,
                  COUNT(DISTINCT n.id) as total_notes,
                  AVG(n.note) as moyenne_generale,
                  MIN(n.note) as note_min,
                  MAX(n.note) as note_max,
                  COUNT(CASE WHEN n.note >= 10 THEN 1 END) * 100.0 / COUNT(n.note) as taux_reussite
                FROM notes n
                JOIN eleves e ON n.eleve_id = e.id";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Classement des meilleurs élèves
$classement_query = "SELECT e.nom, e.prenom, e.matricule, c.niveau as classe_nom,
                            AVG(n.note) as moyenne,
                            COUNT(n.id) as nb_notes
                     FROM eleves e
                     JOIN notes n ON e.id = n.eleve_id
                     JOIN classes c ON e.classe_id = c.id
                     GROUP BY e.id
                     HAVING nb_notes >= 3
                     ORDER BY moyenne DESC
                     LIMIT 10";
$classement_result = $mysqli->query($classement_query);
$classement = [];
if ($classement_result) {
    while ($row = $classement_result->fetch_assoc()) {
        $classement[] = $row;
    }
}

// Récupérer les informations de l'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Résultats Scolaires</title>
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

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>index.php" class="logo">
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
              <span class="hidden-xs"><?php echo htmlspecialchars($username); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                <p>
                  <?php echo htmlspecialchars($username); ?>
                  <small><?php echo htmlspecialchars($role); ?></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>views/directeur_etudes/profil.php" class="btn btn-default btn-flat">Profil</a>
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
          <p><?php echo htmlspecialchars($username); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>      <!-- Sidebar Menu -->
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
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours">
            <i class="fa fa-chalkboard-teacher"></i> <span>Gestion des Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=examens">
            <i class="fa fa-edit"></i> <span>Gestion des Examens</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=resultatsScolaires">
            <i class="fa fa-trophy"></i> <span>Résultats Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTempsGeneral">
            <i class="fa fa-calendar-alt"></i> <span>Emplois du temps</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires">
            <i class="fa fa-calendar-check"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapportsGlobaux">
            <i class="fa fa-pie-chart"></i> <span>Rapports Globaux</span>
          </a>
        </li>

      </ul>
    </section>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <h1>
        Résultats Scolaires
        <small>Analyse des performances académiques</small>
      </h1>      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Résultats Scolaires</li>
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
              <span class="info-box-text">Élèves Évalués</span>
              <span class="info-box-number"><?php echo $stats['total_eleves_notes']; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-file-text"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Notes</span>
              <span class="info-box-number"><?php echo $stats['total_notes']; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-star"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Moyenne Générale</span>
              <span class="info-box-number"><?php echo number_format($stats['moyenne_generale'], 2); ?>/20</span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-<?php echo $stats['taux_reussite'] >= 70 ? 'green' : ($stats['taux_reussite'] >= 50 ? 'yellow' : 'red'); ?>">
              <i class="fa fa-trophy"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text">Taux de Réussite</span>
              <span class="info-box-number"><?php echo number_format($stats['taux_reussite'], 1); ?>%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main row -->
      <div class="row">
        <!-- Tableau des résultats -->
        <div class="col-md-8">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Détail des Résultats</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-sm">
                  <i class="fa fa-print"></i> Imprimer Bulletin
                </button>
                <button type="button" class="btn btn-success btn-sm">
                  <i class="fa fa-download"></i> Exporter Excel
                </button>
              </div>
            </div>
            <div class="box-body">
              <table id="resultatsTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Matière</th>
                    <th>Type Examen</th>
                    <th>Date</th>
                    <th>Note</th>
                    <th>Appréciation</th>
                    <th>Moy. Matière</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($resultats as $resultat): ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($resultat['nom'] . ' ' . $resultat['prenom']); ?></strong>
                      <br><small class="text-muted"><?php echo htmlspecialchars($resultat['matricule']); ?></small>
                    </td>
                    <td>
                      <span class="label label-<?php echo $resultat['section'] == 'primaire' ? 'success' : 'info'; ?>">
                        <?php echo htmlspecialchars($resultat['classe_nom']); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars($resultat['matiere_nom']); ?></td>
                    <td>
                      <span class="label label-default"><?php echo htmlspecialchars($resultat['type_examen']); ?></span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($resultat['date_examen'])); ?></td>
                    <td>
                      <span class="badge bg-<?php echo $resultat['note'] >= 16 ? 'green' : ($resultat['note'] >= 14 ? 'blue' : ($resultat['note'] >= 10 ? 'yellow' : 'red')); ?>">
                        <?php echo number_format($resultat['note'], 2); ?>/20
                      </span>
                    </td>
                    <td>
                      <small><?php echo htmlspecialchars($resultat['appreciation'] ?: 'Aucune'); ?></small>
                    </td>
                    <td>
                      <strong><?php echo number_format($resultat['moyenne_matiere'], 2); ?>/20</strong>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Classement des meilleurs -->
        <div class="col-md-4">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Top 10 - Meilleurs Élèves</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-condensed">
                  <thead>
                    <tr>
                      <th>Rang</th>
                      <th>Élève</th>
                      <th>Moyenne</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($classement as $index => $eleve): ?>
                    <tr>
                      <td>
                        <?php if ($index == 0): ?>
                          <i class="fa fa-trophy text-yellow"></i> 1er
                        <?php elseif ($index == 1): ?>
                          <i class="fa fa-medal text-gray"></i> 2ème
                        <?php elseif ($index == 2): ?>
                          <i class="fa fa-medal text-orange"></i> 3ème
                        <?php else: ?>
                          <?php echo $index + 1; ?>ème
                        <?php endif; ?>
                      </td>
                      <td>
                        <strong><?php echo htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']); ?></strong>
                        <br><small class="text-muted"><?php echo htmlspecialchars($eleve['classe_nom']); ?></small>
                      </td>
                      <td>
                        <span class="badge bg-<?php echo $eleve['moyenne'] >= 16 ? 'green' : ($eleve['moyenne'] >= 14 ? 'blue' : 'yellow'); ?>">
                          <?php echo number_format($eleve['moyenne'], 2); ?>/20
                        </span>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Statistiques de distribution -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Distribution des Notes</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-green">
                      <i class="fa fa-caret-up"></i> Note Max
                    </span>
                    <h5 class="description-header"><?php echo number_format($stats['note_max'], 2); ?>/20</h5>
                    <span class="description-text">MAXIMUM</span>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="description-block">
                    <span class="description-percentage text-red">
                      <i class="fa fa-caret-down"></i> Note Min
                    </span>
                    <h5 class="description-header"><?php echo number_format($stats['note_min'], 2); ?>/20</h5>
                    <span class="description-text">MINIMUM</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2024 <a href="#">School Management System</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  $('#resultatsTable').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'language'    : {
      'url': '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    }
  });
});
</script>

</body>
</html>

<?php
$mysqli->close();
?>
