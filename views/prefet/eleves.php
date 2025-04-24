<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des élèves de la section secondaire avec information de classe
$query = "SELECT e.*, e.classe_id as classe_nom 
          FROM eleves e 
          LEFT JOIN classes c ON e.classe_id = e.id
          WHERE e.section = 'secondaire'
          ORDER BY e.nom, e.prenom";
$result = $mysqli->query($query);

$eleves = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eleves[] = $row;
    }
}

// Récupération des statistiques sur le genre par classe
$stats_query = "SELECT c.nom as classe_nom, 
                SUM(CASE WHEN e.sexe = 'M' THEN 1 ELSE 0 END) as nb_garcons,
                SUM(CASE WHEN e.sexe = 'F' THEN 1 ELSE 0 END) as nb_filles,
                COUNT(*) as total
                FROM eleves e
                JOIN classes c ON e.classe_id = c.id
                WHERE e.section = 'secondaire'
                GROUP BY e.classe_id
                ORDER BY c.nom";
$stats_result = $mysqli->query($stats_query);

$stats_classes = [];
if ($stats_result) {
    while ($row = $stats_result->fetch_assoc()) {
        $stats_classes[] = $row;
    }
}

// Récupération des statistiques globales sur le genre
$stats_global_query = "SELECT 
                      SUM(CASE WHEN sexe = 'M' THEN 1 ELSE 0 END) as nb_garcons,
                      SUM(CASE WHEN sexe = 'F' THEN 1 ELSE 0 END) as nb_filles,
                      COUNT(*) as total
                      FROM eleves
                      WHERE section = 'secondaire'";
$stats_global_result = $mysqli->query($stats_global_query);
$stats_global = $stats_global_result->fetch_assoc();

// Fermer la connexion
$mysqli->close();

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Prefet';

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Élèves</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil" class="logo">
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
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $username; ?> - <?php echo $role; ?>
                  <small><?php echo $email; ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=absences">
            <i class="fa fa-calendar-times-o"></i> <span>Absences</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=discipline">
            <i class="fa fa-gavel"></i> <span>Discipline</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Élèves
        <small>Liste des élèves</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Élèves</li>
      </ol>
    </section>

    <section class="content">
      <!-- Boîte d'informations générales -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Informations générales - Répartition par sexe</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total des élèves</span>
                      <span class="info-box-number"><?php echo $stats_global['total']; ?></span>
                      <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                      </div>
                      <span class="progress-description">
                        Section secondaire 
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="fa fa-male"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Garçons</span>
                      <span class="info-box-number"><?php echo $stats_global['nb_garcons']; ?></span>
                      <div class="progress">
                        <div class="progress-bar" style="width: <?php echo ($stats_global['total'] > 0) ? ($stats_global['nb_garcons'] / $stats_global['total'] * 100) : 0; ?>%"></div>
                      </div>
                      <span class="progress-description">
                        <?php echo ($stats_global['total'] > 0) ? round($stats_global['nb_garcons'] / $stats_global['total'] * 100, 1) : 0; ?>% du total
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="info-box bg-pink">
                    <span class="info-box-icon"><i class="fa fa-female"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Filles</span>
                      <span class="info-box-number"><?php echo $stats_global['nb_filles']; ?></span>
                      <div class="progress">
                        <div class="progress-bar" style="width: <?php echo ($stats_global['total'] > 0) ? ($stats_global['nb_filles'] / $stats_global['total'] * 100) : 0; ?>%"></div>
                      </div>
                      <span class="progress-description">
                        <?php echo ($stats_global['total'] > 0) ? round($stats_global['nb_filles'] / $stats_global['total'] * 100, 1) : 0; ?>% du total
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-12">
                  <h4>Répartition par classe</h4>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Classe</th>
                          <th>Garçons</th>
                          <th>Filles</th>
                          <th>Total</th>
                          <th>Pourcentage garçons</th>
                          <th>Pourcentage filles</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($stats_classes as $classe): ?>
                          <tr>
                            <td><?php echo htmlspecialchars($classe['classe_nom']); ?></td>
                            <td><?php echo $classe['nb_garcons']; ?></td>
                            <td><?php echo $classe['nb_filles']; ?></td>
                            <td><?php echo $classe['total']; ?></td>
                            <td>
                              <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-blue" style="width: <?php echo ($classe['total'] > 0) ? ($classe['nb_garcons'] / $classe['total'] * 100) : 0; ?>%"></div>
                              </div>
                              <span class="badge bg-blue"><?php echo ($classe['total'] > 0) ? round($classe['nb_garcons'] / $classe['total'] * 100, 1) : 0; ?>%</span>
                            </td>
                            <td>
                              <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-pink" style="width: <?php echo ($classe['total'] > 0) ? ($classe['nb_filles'] / $classe['total'] * 100) : 0; ?>%"></div>
                              </div>
                              <span class="badge bg-pink"><?php echo ($classe['total'] > 0) ? round($classe['nb_filles'] / $classe['total'] * 100, 1) : 0; ?>%</span>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des élèves</h3>
            </div>
            <div class="box-body">
              <table id="eleves-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date de naissance</th>
                    <th>Classe</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($eleves)): ?>
                    <?php foreach ($eleves as $eleve): ?>
                      <tr>
                        <td><?php echo $eleve['id']; ?></td>
                        <td><?php echo htmlspecialchars($eleve['nom']); ?></td>
                        <td><?php echo htmlspecialchars($eleve['prenom']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></td>
                        <td><?php echo htmlspecialchars($eleve['classe_nom']); ?></td>
                        <td>
                          <div class="btn-group">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=voirEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-sm">
                              <i class="fa fa-eye"></i> Voir
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=ajouterAbsence&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-warning btn-sm">
                              <i class="fa fa-calendar-times-o"></i> Absence
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=ajouterIncident&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-danger btn-sm">
                              <i class="fa fa-gavel"></i> Incident
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center">Aucun élève trouvé</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  $('#eleves-table').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'language': {
      'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
    }
  });
});
</script>
</body>
</html>