<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des statistiques
$total_eleves = $mysqli->query("SELECT COUNT(*) AS total FROM eleves")->fetch_assoc()['total'];
$total_professeurs = $mysqli->query("SELECT COUNT(*) AS total FROM professeurs")->fetch_assoc()['total'];
$total_directeurs = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE role='director'")->fetch_assoc()['total'];
$total_directrices = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE role='directrice'")->fetch_assoc()['total'];
$total_prefets = $mysqli->query("SELECT COUNT(*) AS total FROM prefet")->fetch_assoc()['total'];
$total_comptables = $mysqli->query("SELECT COUNT(*) AS total FROM comptable")->fetch_assoc()['total'];
$total_frais = $mysqli->query("SELECT SUM(amount_paid) AS total FROM paiements_frais")->fetch_assoc()['total'] ?? 0;

// Récupérer le nombre de classes et d'employés avant de fermer la connexion
$total_classes = $mysqli->query("SELECT COUNT(*) AS total FROM classes")->fetch_assoc()['total'];
$total_employes = $mysqli->query("SELECT COUNT(*) AS total FROM employes")->fetch_assoc()['total'];

// Récupération des élèves par classe
$eleves_par_classe = [];
$eleves_par_classe_query = "SELECT e.classe, COUNT(e.id) as total 
                           FROM eleves e 
                           GROUP BY e.classe 
                           ORDER BY total DESC 
                           LIMIT 5";
$eleves_par_classe_result = $mysqli->query($eleves_par_classe_query);
if ($eleves_par_classe_result) {
    while ($row = $eleves_par_classe_result->fetch_assoc()) {
        $eleves_par_classe[] = $row;
    }
}

// Récupération des actions par utilisateur
$actions_query = "SELECT l.username, COUNT(*) as total_actions 
                 FROM logs l 
                 GROUP BY l.username 
                 ORDER BY total_actions DESC 
                 LIMIT 10";
$actions_result = $mysqli->query($actions_query);
$actions_data = [];
$actions_labels = [];
$actions_values = [];

if ($actions_result && $actions_result->num_rows > 0) {
    while ($row = $actions_result->fetch_assoc()) {
        $actions_labels[] = $row['username'];
        $actions_values[] = $row['total_actions'];
    }
} else {
    // Données par défaut si aucune action n'est trouvée
    $actions_labels = ['Aucune donnée'];
    $actions_values = [0];
}

// Fermer la connexion après avoir récupéré toutes les données nécessaires
$mysqli->close();

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Tableau de bord</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=profil" class="btn btn-default btn-flat">Profil</a>
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
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>

        <!-- Nouveaux liens ajoutés -->
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=achatFournitures">
            <i class="fa fa-shopping-cart"></i> <span>Achats Fournitures</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=gestionStock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        <!-- Fin des nouveaux liens -->
      
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dollar"></i> <span>Frais</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=Frais"><i class="fa fa-circle-o"></i> Voir Frais</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutfrais"><i class="fa fa-circle-o"></i> Ajouter frais</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutprofesseur"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Préfets</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addPrefet"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=prefets"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span>Direction</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addDirecteur"><i class="fa fa-circle-o"></i> Ajouter Directeur</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs"><i class="fa fa-circle-o"></i> Voir Directeurs</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=adddirectrice"><i class="fa fa-circle-o"></i> Ajouter Directrice</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directrices"><i class="fa fa-circle-o"></i> Voir Directrices</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-calculator"></i> <span>Comptables</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addcomptable"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=comptable"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Classes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span>Cours</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutcours"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Employés</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutemployes"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=rapportactions">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Tableau de bord
        <small>Aperçu général</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Tableau de bord</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $total_eleves; ?></h3>
              <p>Élèves inscrits</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $total_professeurs; ?></h3>
              <p>Professeurs</p>
            </div>
            <div class="icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $total_directeurs + $total_directrices; ?></h3>
              <p>Direction</p>
            </div>
            <div class="icon">
              <i class="fa fa-user-secret"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo number_format($total_frais, 2); ?></h3>
              <p>Total des frais</p>
            </div>
            <div class="icon">
              <i class="fa fa-dollar"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=Frais" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Deuxième rangée de statistiques -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?php echo $total_prefets; ?></h3>
              <p>Préfets</p>
            </div>
            <div class="icon">
              <i class="fa fa-user"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=prefets" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-maroon">
            <div class="inner">
              <h3><?php echo $total_comptables; ?></h3>
              <p>Comptables</p>
            </div>
            <div class="icon">
              <i class="fa fa-calculator"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=comptable" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-olive">
            <div class="inner">
              <h3><?php echo $total_classes; ?></h3>
              <p>Classes</p>
            </div>
            <div class="icon">
              <i class="fa fa-table"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-teal">
            <div class="inner">
              <h3><?php echo $total_employes; ?></h3>
              <p>Employés</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Statistiques détaillées de la direction -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Détails de la Direction</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="fa fa-male"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Directeurs</span>
                      <span class="info-box-number"><?php echo $total_directeurs; ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-box bg-fuchsia">
                    <span class="info-box-icon"><i class="fa fa-female"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Directrices</span>
                      <span class="info-box-number"><?php echo $total_directrices; ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistiques des élèves par classe -->
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Élèves par classe</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Classe</th>
                    <th>Nombre d'élèves</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (!empty($eleves_par_classe)) {
                    foreach ($eleves_par_classe as $row) {
                      echo "<tr>
                              <td>{$row['classe']}</td>
                              <td>{$row['total']}</td>
                            </tr>";
                    }
                  } else {
                    echo "<tr><td colspan='2' class='text-center'>Aucune donnée disponible</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      
      
      <!-- Nouveau graphique pour les actions des utilisateurs -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Actions par utilisateur</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php if (empty($actions_labels) || (count($actions_labels) == 1 && $actions_labels[0] == 'Aucune donnée')): ?>
                <div class="alert alert-info">
                  <h4><i class="icon fa fa-info"></i> Information</h4>
                  Aucune donnée d'action utilisateur n'est disponible pour le moment.
                </div>
              <?php else: ?>
                <div class="chart">
                  <canvas id="actionsChart" style="height:250px"></canvas>
                </div>
              <?php endif; ?>
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <h5 class="description-header text-info">Actions</h5>
                    <span class="description-text">ACTIVITÉS DES UTILISATEURS</span>
                  </div>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted">Ce graphique montre le nombre total d'actions effectuées par chaque utilisateur dans le système.</p>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Graphique du personnel
  var ctx = document.getElementById('personnelChart').getContext('2d');
  var personnelChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Professeurs', 'Directeurs', 'Directrices', 'Préfets', 'Comptables'],
      datasets: [{
        label: 'Nombre',
        data: [
          <?php echo $total_professeurs; ?>, 
          <?php echo $total_directeurs; ?>, 
          <?php echo $total_directrices; ?>, 
          <?php echo $total_prefets; ?>, 
          <?php echo $total_comptables; ?>
        ],
        backgroundColor: [
          'rgba(0, 166, 90, 0.8)',
          'rgba(60, 141, 188, 0.8)',
          'rgba(243, 156, 18, 0.8)',
          'rgba(0, 192, 239, 0.8)',
          'rgba(221, 75, 57, 0.8)'
        ],
        borderColor: [
          'rgba(0, 166, 90, 1)',
          'rgba(60, 141, 188, 1)',
          'rgba(243, 156, 18, 1)',
          'rgba(0, 192, 239, 1)',
          'rgba(221, 75, 57, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });
  
  // Nouveau graphique pour les actions des utilisateurs
  <?php if (!empty($actions_labels) && !(count($actions_labels) == 1 && $actions_labels[0] == 'Aucune donnée')): ?>
  var actionsCtx = document.getElementById('actionsChart').getContext('2d');
  var actionsChart = new Chart(actionsCtx, {
    type: 'horizontalBar',
    data: {
      labels: <?php echo json_encode($actions_labels); ?>,
      datasets: [{
        label: 'Nombre d\'actions',
        data: <?php echo json_encode($actions_values); ?>,
        backgroundColor: [
          'rgba(60, 141, 188, 0.8)',
          'rgba(0, 166, 90, 0.8)',
          'rgba(243, 156, 18, 0.8)',
          'rgba(221, 75, 57, 0.8)',
          'rgba(0, 192, 239, 0.8)',
          'rgba(210, 214, 222, 0.8)',
          'rgba(216, 27, 96, 0.8)',
          'rgba(156, 39, 176, 0.8)',
          'rgba(63, 81, 181, 0.8)',
          'rgba(0, 150, 136, 0.8)'
        ],
        borderColor: [
          'rgba(60, 141, 188, 1)',
          'rgba(0, 166, 90, 1)',
          'rgba(243, 156, 18, 1)',
          'rgba(221, 75, 57, 1)',
          'rgba(0, 192, 239, 1)',
          'rgba(210, 214, 222, 1)',
          'rgba(216, 27, 96, 1)',
          'rgba(156, 39, 176, 1)',
          'rgba(63, 81, 181, 1)',
          'rgba(0, 150, 136, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      legend: {
        position: 'top',
      },
      scales: {
        xAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });
  <?php endif; ?>
});
</script>
</body>
</html>