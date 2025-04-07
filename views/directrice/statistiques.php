<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Statistiques</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b><?php echo $role; ?></b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves">
            <i class="fa fa-users"></i> <span>Élèves Maternelle</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=classes">
            <i class="fa fa-graduation-cap"></i> <span>Classes</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=achats">
            <i class="fa fa-shopping-cart"></i> <span>Achats</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=stock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=evenements">
            <i class="fa fa-calendar"></i> <span>Événements</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=statistiques">
            <i class="fa fa-bar-chart"></i> <span>Statistiques</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil">
            <i class="fa fa-user"></i> <span>Profil</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Statistiques
        <small>Aperçu des données</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Statistiques</li>
      </ol>
    </section>

    <section class="content">
      <!-- Statistiques générales -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
            <?php
              // Connexion à la base de données
              $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
              
              if ($mysqli->connect_error) {
                  die("Erreur de connexion: " . $mysqli->connect_error);
              }
              
              // Compter le nombre total d'élèves
              $result = $mysqli->query("SELECT COUNT(*) as total FROM eleves WHERE section = 'Maternelle'");
              $total_eleves = $result->fetch_assoc()['total'];
              
              // Récupérer les statistiques par classe
              $classes_query = $mysqli->query("SELECT classe, 
                                             COUNT(*) as total, 
                                             SUM(CASE WHEN sexe = 'M' THEN 1 ELSE 0 END) as garcons,
                                             SUM(CASE WHEN sexe = 'F' THEN 1 ELSE 0 END) as filles
                                      FROM eleves 
                                      WHERE section = 'Maternelle'
                                      GROUP BY classe");
              
              $classes_stats = [];
              if ($classes_query) {
                  while ($row = $classes_query->fetch_assoc()) {
                      $classes_stats[] = $row;
                  }
              }
              
              // Statistiques par sexe
              $sexe_query = $mysqli->query("SELECT sexe, COUNT(*) as total FROM eleves WHERE section = 'Maternelle' GROUP BY sexe");
              $sexe_stats = [];
              if ($sexe_query) {
                  while ($row = $sexe_query->fetch_assoc()) {
                      $sexe_stats[$row['sexe']] = $row['total'];
                  }
              }
              ?>
              <h3><?php echo $total_eleves; ?></h3>
              <p>Élèves inscrits</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo count($classes_stats); ?></h3>
              <p>Classes</p>
            </div>
            <div class="icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=classes" class="small-box-footer">Voir les classes <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <?php
              // Compter le nombre de garçons de la section maternelle
              $result = $mysqli->query("SELECT COUNT(*) as total FROM eleves WHERE sexe = 'M' AND section = 'Maternelle'");
              $total_garcons = $result->fetch_assoc()['total'];
              ?>
              <h3><?php echo $total_garcons; ?></h3>
              <p>Garçons en Maternelle</p>
            </div>
            <div class="icon">
              <i class="ion ion-male"></i>
            </div>
            <a href="#" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <?php
              // Compter le nombre de filles de la section maternelle
              $result = $mysqli->query("SELECT COUNT(*) as total FROM eleves WHERE sexe = 'F' AND section = 'Maternelle'");
              $total_filles = $result->fetch_assoc()['total'];
              ?>
              <h3><?php echo $total_filles; ?></h3>
              <p>Filles en Maternelle</p>
            </div>
            <div class="icon">
              <i class="ion ion-female"></i>
            </div>
            <a href="#" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      
      <!-- Graphiques -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition par classe</h3>
            </div>
            <div class="box-body">
              <canvas id="pieChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition par sexe</h3>
            </div>
            <div class="box-body">
              <canvas id="barChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Tableau des statistiques par classe -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Statistiques détaillées par classe</h3>
            </div>
            <div class="box-body table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Classe</th>
                    <th>Total élèves</th>
                    <th>Garçons</th>
                    <th>Filles</th>
                    <th>Pourcentage</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Récupérer les statistiques par classe
                  $result = $mysqli->query("SELECT e.classe, 
                                           COUNT(e.id) as total, 
                                           SUM(CASE WHEN e.sexe = 'M' THEN 1 ELSE 0 END) as garcons,
                                           SUM(CASE WHEN e.sexe = 'F' THEN 1 ELSE 0 END) as filles
                                    FROM eleves e
                                    WHERE e.section = 'Maternelle'
                                    GROUP BY e.classe
                                    ORDER BY e.classe");
                  
                  $classes_data = [];
                  $garcons_data = [];
                  $filles_data = [];
                  $colors = [];
                  
                  if ($result) {
                      while ($row = $result->fetch_assoc()) {
                          $classes_data[] = $row['classe'];
                          $garcons_data[] = $row['garcons'];
                          $filles_data[] = $row['filles'];
                          $colors[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.7)';
                          
                          $pourcentage = ($total_eleves > 0) ? round(($row['total'] / $total_eleves) * 100, 2) : 0;
                  ?>
                  <tr>
                    <td><?php echo $row['classe']; ?></td>
                    <td><?php echo $row['total']; ?></td>
                    <td><?php echo $row['garcons']; ?></td>
                    <td><?php echo $row['filles']; ?></td>
                    <td><?php echo $pourcentage; ?>%</td>
                  </tr>
                  <?php
                      }
                  }
                  $mysqli->close();
                  ?>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- ChartJS -->
<script src="bower_components/chart.js/Chart.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    // Données pour le graphique en camembert
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
    var pieChart = new Chart(pieChartCanvas);
    var pieData = {
      labels: <?php echo json_encode($classes_data); ?>,
      datasets: [
        {
          data: <?php echo json_encode(array_map('intval', array_column($result->fetch_all(MYSQLI_ASSOC), 'total'))); ?>,
          backgroundColor: <?php echo json_encode($colors); ?>
        }
      ]
    };
    var pieOptions = {
      responsive: true,
      maintainAspectRatio: false
    };
    pieChart.Pie(pieData, pieOptions);

    // Données pour le graphique en barres
    var barChartCanvas = $('#barChart').get(0).getContext('2d');
    var barChart = new Chart(barChartCanvas);
    var barData = {
      labels: <?php echo json_encode($classes_data); ?>,
      datasets: [
        {
          label: 'Garçons',
          backgroundColor: 'rgba(60,141,188,0.9)',
          data: <?php echo json_encode($garcons_data); ?>
        },
        {
          label: 'Filles',
          backgroundColor: 'rgba(210, 214, 222, 1)',
          data: <?php echo json_encode($filles_data); ?>
        }
      ]
    };
    var barOptions = {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    };
    barChart.Bar(barData, barOptions);
  });
</script>
</body>
</html>