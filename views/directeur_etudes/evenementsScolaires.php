<?php
// Assurez-vous que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des événements scolaires
$evenements = [];
$search_query = '';
$date_debut = '';
$date_fin = '';
$type_evenement = '';

// Traitement des filtres
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
    $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';
    $type_evenement = isset($_GET['type_evenement']) ? $_GET['type_evenement'] : '';
}

// Construction de la requête avec filtres
$query = "SELECT e.*, 
          DATE_FORMAT(e.date_debut, '%d/%m/%Y') as date_debut_fr,
          DATE_FORMAT(e.date_fin, '%d/%m/%Y') as date_fin_fr,
          TIME_FORMAT(e.heure_debut, '%H:%i') as heure_debut_fr,
          TIME_FORMAT(e.heure_fin, '%H:%i') as heure_fin_fr
          FROM evenements_scolaires
          WHERE 1=1";

$params = [];
$types = "";

if (!empty($search_query)) {
    $query .= " AND (e.titre LIKE ? OR e.description LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $types .= "ss";
}

if (!empty($date_debut)) {
    $query .= " AND e.date_debut >= ?";
    $params[] = $date_debut;
    $types .= "s";
}

if (!empty($date_fin)) {
    $query .= " AND e.date_fin <= ?";
    $params[] = $date_fin;
    $types .= "s";
}

if (!empty($type_evenement)) {
    $query .= " AND e.type = ?";
    $params[] = $type_evenement;
    $types .= "s";
}

$query .= " ORDER BY e.date_debut DESC, e.heure_debut ASC";

if (!empty($params)) {
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query($query);
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $evenements[] = $row;
    }
}

// Statistiques des événements
$stats_query = "SELECT 
                COUNT(*) as total_evenements,
                COUNT(CASE WHEN date_debut >= CURDATE() THEN 1 END) as evenements_futurs,
                COUNT(CASE WHEN date_debut < CURDATE() THEN 1 END) as evenements_passes,
                COUNT(CASE WHEN type = 'examen' THEN 1 END) as examens,
                COUNT(CASE WHEN type = 'reunion' THEN 1 END) as reunions,
                COUNT(CASE WHEN type = 'formation' THEN 1 END) as formations,
                COUNT(CASE WHEN type = 'ceremonie' THEN 1 END) as ceremonies
                FROM evenements";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : [];

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Événements Scolaires</title>
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
          <p><?php echo htmlspecialchars($username); ?></p>
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
        
        <li class="active">
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

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <h1>
        Événements Scolaires
        <small>Gestion du calendrier des événements</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Événements Scolaires</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      
      <!-- Statistiques des événements -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $stats['total_evenements'] ?? 0; ?></h3>
              <p>Total Événements</p>
            </div>
            <div class="icon">
              <i class="fa fa-calendar"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $stats['evenements_futurs'] ?? 0; ?></h3>
              <p>Événements à Venir</p>
            </div>
            <div class="icon">
              <i class="fa fa-clock-o"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $stats['examens'] ?? 0; ?></h3>
              <p>Examens</p>
            </div>
            <div class="icon">
              <i class="fa fa-edit"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo $stats['reunions'] ?? 0; ?></h3>
              <p>Réunions</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtres et recherche -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Filtres de recherche</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <form method="GET" class="form-horizontal">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Recherche</label>
                  <input type="text" name="search" class="form-control" placeholder="Titre ou description..." 
                         value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Date début</label>
                  <input type="date" name="date_debut" class="form-control" value="<?php echo htmlspecialchars($date_debut); ?>">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Date fin</label>
                  <input type="date" name="date_fin" class="form-control" value="<?php echo htmlspecialchars($date_fin); ?>">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Type d'événement</label>
                  <select name="type_evenement" class="form-control">
                    <option value="">Tous les types</option>
                    <option value="examen" <?php echo $type_evenement === 'examen' ? 'selected' : ''; ?>>Examen</option>
                    <option value="reunion" <?php echo $type_evenement === 'reunion' ? 'selected' : ''; ?>>Réunion</option>
                    <option value="formation" <?php echo $type_evenement === 'formation' ? 'selected' : ''; ?>>Formation</option>
                    <option value="ceremonie" <?php echo $type_evenement === 'ceremonie' ? 'selected' : ''; ?>>Cérémonie</option>
                    <option value="vacances" <?php echo $type_evenement === 'vacances' ? 'selected' : ''; ?>>Vacances</option>
                    <option value="autre" <?php echo $type_evenement === 'autre' ? 'selected' : ''; ?>>Autre</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <div>
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="?" class="btn btn-default">Réinitialiser</a>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Actions</h3>
            </div>
            <div class="box-body">
              <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addEventModal">
                <i class="fa fa-plus"></i> Ajouter un Événement
              </button>
              <button type="button" class="btn btn-info" onclick="window.print()">
                <i class="fa fa-print"></i> Imprimer le Calendrier
              </button>
              <a href="#" class="btn btn-warning">
                <i class="fa fa-download"></i> Exporter en PDF
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste des événements -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Liste des Événements</h3>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table id="eventsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Type</th>
                  <th>Date Début</th>
                  <th>Date Fin</th>
                  <th>Heure</th>
                  <th>Lieu</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($evenements as $evenement): ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($evenement['titre']); ?></strong>
                      <?php if (!empty($evenement['description'])): ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($evenement['description'], 0, 50)) . '...'; ?></small>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php 
                      $type_labels = [
                        'examen' => '<span class="label label-warning">Examen</span>',
                        'reunion' => '<span class="label label-info">Réunion</span>',
                        'formation' => '<span class="label label-success">Formation</span>',
                        'ceremonie' => '<span class="label label-primary">Cérémonie</span>',
                        'vacances' => '<span class="label label-default">Vacances</span>',
                        'autre' => '<span class="label label-secondary">Autre</span>'
                      ];
                      echo $type_labels[$evenement['type']] ?? '<span class="label label-default">Non défini</span>';
                      ?>
                    </td>
                    <td><?php echo $evenement['date_debut_fr']; ?></td>
                    <td><?php echo $evenement['date_fin_fr']; ?></td>
                    <td>
                      <?php if (!empty($evenement['heure_debut_fr'])): ?>
                        <?php echo $evenement['heure_debut_fr']; ?>
                        <?php if (!empty($evenement['heure_fin_fr'])): ?>
                          - <?php echo $evenement['heure_fin_fr']; ?>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="text-muted">Toute la journée</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($evenement['lieu'] ?? 'Non précisé'); ?></td>
                    <td>
                      <?php
                      $aujourd_hui = date('Y-m-d');
                      if ($evenement['date_debut'] > $aujourd_hui) {
                        echo '<span class="label label-info">À venir</span>';
                      } elseif ($evenement['date_fin'] >= $aujourd_hui) {
                        echo '<span class="label label-success">En cours</span>';
                      } else {
                        echo '<span class="label label-default">Terminé</span>';
                      }
                      ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs" title="Voir détails"
                                onclick="showEventDetails(<?php echo $evenement['id']; ?>)">
                          <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-xs" title="Modifier"
                                onclick="editEvent(<?php echo $evenement['id']; ?>)">
                          <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-xs" title="Supprimer"
                                onclick="deleteEvent(<?php echo $evenement['id']; ?>)">
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

    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> SMS.</strong> Tous droits réservés.
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
  $('#eventsTable').DataTable({
    'paging': true,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': true,
    'autoWidth': false,
    'pageLength': 15,
    'language': {
      'url': '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
    }
  });
});

function showEventDetails(eventId) {
  // Logique pour afficher les détails de l'événement
  alert('Afficher détails événement ID: ' + eventId);
}

function editEvent(eventId) {
  // Logique pour modifier l'événement
  alert('Modifier événement ID: ' + eventId);
}

function deleteEvent(eventId) {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
    // Logique pour supprimer l'événement
    alert('Supprimer événement ID: ' + eventId);
  }
}
</script>

</body>
</html>
