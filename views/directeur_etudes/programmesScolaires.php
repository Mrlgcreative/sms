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

// Récupération des programmes scolaires avec informations des matières et classes
$query = "SELECT ps.*, c.niveau as classe_nom, c.section,
                 COUNT(DISTINCT m.id) as nb_matieres,
                 SUM(m.heures_semaine) as total_heures,
                 AVG(m.coefficient) as coefficient_moyen
          FROM programmes_scolaires ps 
          LEFT JOIN classes c ON ps.classe_id = c.id
          LEFT JOIN matieres m ON ps.id = m.programme_id
          GROUP BY ps.id
          ORDER BY c.section, c.niveau";
$result = $mysqli->query($query);

$programmes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $programmes[] = $row;
    }
}

// Statistiques des programmes
$stats_query = "SELECT 
                  COUNT(DISTINCT ps.id) as total_programmes,
                  COUNT(DISTINCT CASE WHEN c.section = 'primaire' THEN ps.id END) as programmes_primaire,
                  COUNT(DISTINCT CASE WHEN c.section = 'secondaire' THEN ps.id END) as programmes_secondaire,
                  COUNT(DISTINCT m.id) as total_matieres,
                  SUM(m.heures_semaine) as total_heures_semaine
                FROM programmes_scolaires ps 
                LEFT JOIN classes c ON ps.classe_id = c.id
                LEFT JOIN matieres m ON ps.id = m.programme_id";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_assoc();

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
  <title>SMS | Programmes Scolaires</title>
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
  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil" class="logo">
      <span class="logo-mini"><b>S</b>MS</span>
      <span class="logo-lg"><b>Directeur Études</b></span>
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
      </div>

      <!-- Sidebar Menu -->      <ul class="sidebar-menu" data-widget="tree">
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
        
        <li class="active">
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
        
        <li>
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
        Programmes Scolaires
        <small>Gestion des curricula et matières</small>
      </h1>      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Programmes Scolaires</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-book"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Programmes</span>
              <span class="info-box-number"><?php echo $stats['total_programmes']; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-graduation-cap"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Primaire</span>
              <span class="info-box-number"><?php echo $stats['programmes_primaire']; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-university"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Secondaire</span>
              <span class="info-box-number"><?php echo $stats['programmes_secondaire']; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-clock-o"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Heures/Semaine</span>
              <span class="info-box-number"><?php echo $stats['total_heures_semaine']; ?>h</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main row -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Programmes Scolaires</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addProgrammeModal">
                  <i class="fa fa-plus"></i> Créer un Programme
                </button>
              </div>
            </div>
            <div class="box-body">
              <table id="programmesTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Classe</th>
                    <th>Section</th>
                    <th>Année Scolaire</th>
                    <th>Matières</th>
                    <th>Total Heures</th>
                    <th>Coefficient Moyen</th>
                    <th>Statut</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($programmes as $programme): ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($programme['classe_nom']); ?></strong>
                    </td>
                    <td>
                      <span class="label label-<?php echo $programme['section'] == 'primaire' ? 'success' : 'info'; ?>">
                        <?php echo ucfirst($programme['section']); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars($programme['annee_scolaire']); ?></td>
                    <td>
                      <span class="badge bg-blue"><?php echo $programme['nb_matieres']; ?> matières</span>
                    </td>
                    <td>
                      <strong><?php echo $programme['total_heures']; ?>h</strong>/semaine
                    </td>
                    <td>
                      <?php if ($programme['coefficient_moyen']): ?>
                        <?php echo number_format($programme['coefficient_moyen'], 1); ?>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($programme['statut'] == 'actif'): ?>
                        <span class="label label-success">Actif</span>
                      <?php elseif ($programme['statut'] == 'brouillon'): ?>
                        <span class="label label-warning">Brouillon</span>
                      <?php else: ?>
                        <span class="label label-default">Archivé</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs" title="Voir détails">
                          <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-xs" title="Voir matières">
                          <i class="fa fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-xs" title="Modifier">
                          <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-success btn-xs" title="Dupliquer">
                          <i class="fa fa-copy"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-xs" title="Supprimer">
                          <i class="fa fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Programme par Section -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Programmes Primaire</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-condensed">
                  <thead>
                    <tr>
                      <th>Classe</th>
                      <th>Matières</th>
                      <th>Heures</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($programmes as $programme): ?>
                      <?php if ($programme['section'] == 'primaire'): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($programme['classe_nom']); ?></td>
                        <td><span class="badge bg-green"><?php echo $programme['nb_matieres']; ?></span></td>
                        <td><?php echo $programme['total_heures']; ?>h</td>
                      </tr>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Programmes Secondaire</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-condensed">
                  <thead>
                    <tr>
                      <th>Classe</th>
                      <th>Matières</th>
                      <th>Heures</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($programmes as $programme): ?>
                      <?php if ($programme['section'] == 'secondaire'): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($programme['classe_nom']); ?></td>
                        <td><span class="badge bg-blue"><?php echo $programme['nb_matieres']; ?></span></td>
                        <td><?php echo $programme['total_heures']; ?>h</td>
                      </tr>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </tbody>
                </table>
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
  $('#programmesTable').DataTable({
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
