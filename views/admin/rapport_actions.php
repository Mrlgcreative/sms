<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Rapport des Actions</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Rapport des Actions
        <small>Historique des activités</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Rapport des Actions</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des actions effectuées dans le système</h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                  <input type="text" name="table_search" class="form-control pull-right" placeholder="Rechercher">
                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="box-body table-responsive">
              <table id="actions-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Date et heure</th>
                    <th>Adresse IP</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (isset($actions) && !empty($actions)): ?>
                    <?php foreach ($actions as $action): ?>
                      <tr>
                        <td><?php echo $action['id']; ?></td>
                        <td><?php echo $action['username']; ?></td>
                        <td><?php echo $action['action']; ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($action['action_time'])); ?></td>
                        <td><?php echo $action['ip_address']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center">Aucune action enregistrée</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Date et heure</th>
                    <th>Adresse IP</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Actions par utilisateur</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <canvas id="userActionsChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Actions par jour</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <canvas id="dailyActionsChart" style="height:250px"></canvas>
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
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // DataTable pour la liste des actions
  $('#actions-table').DataTable({
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
  
  <?php if (isset($actions) && !empty($actions)): ?>
  // Préparation des données pour les graphiques
  var userLabels = <?php 
    $users = [];
    $userCounts = [];
    $userColors = [];
    
    $colors = [
      'rgba(60, 141, 188, 0.8)',
      'rgba(0, 166, 90, 0.8)',
      'rgba(243, 156, 18, 0.8)',
      'rgba(221, 75, 57, 0.8)',
      'rgba(0, 192, 239, 0.8)'
    ];
    
    $userStats = [];
    foreach ($actions as $action) {
      if (!isset($userStats[$action['username']])) {
        $userStats[$action['username']] = 0;
      }
      $userStats[$action['username']]++;
    }
    
    arsort($userStats);
    $userStats = array_slice($userStats, 0, 5);
    
    foreach ($userStats as $user => $count) {
      $users[] = $user;
      $userCounts[] = $count;
      $userColors[] = $colors[array_rand($colors)];
    }
    
    echo json_encode($users);
  ?>;
  
  var userCounts = <?php echo json_encode($userCounts); ?>;
  var userColors = <?php echo json_encode($userColors); ?>;
  
  // Graphique des actions par utilisateur
  var userCtx = document.getElementById('userActionsChart').getContext('2d');
  var userChart = new Chart(userCtx, {
    type: 'pie',
    data: {
      labels: userLabels,
      datasets: [{
        data: userCounts,
        backgroundColor: userColors
      }]
    },
    options: {
      responsive: true,
      legend: {
        position: 'right',
      }
    }
  });
  
  // Préparation des données pour le graphique par jour
  var dailyLabels = [];
  var dailyCounts = [];
  
  <?php
    $dailyStats = [];
    foreach ($actions as $action) {
      $date = date('Y-m-d', strtotime($action['action_time']));
      if (!isset($dailyStats[$date])) {
        $dailyStats[$date] = 0;
      }
      $dailyStats[$date]++;
    }
    
    // Trier par date
    ksort($dailyStats);
    
    // Limiter aux 7 derniers jours
    $dailyStats = array_slice($dailyStats, -7, 7, true);
    
    foreach ($dailyStats as $date => $count) {
      echo "dailyLabels.push('".date('d/m/Y', strtotime($date))."');\n";
      echo "dailyCounts.push(".$count.");\n";
    }
  ?>
  
  // Graphique des actions par jour
  var dailyCtx = document.getElementById('dailyActionsChart').getContext('2d');
  var dailyChart = new Chart(dailyCtx, {
    type: 'line',
    data: {
      labels: dailyLabels,
      datasets: [{
        label: 'Nombre d\'actions',
        data: dailyCounts,
        backgroundColor: 'rgba(60, 141, 188, 0.2)',
        borderColor: 'rgba(60, 141, 188, 1)',
        pointBackgroundColor: 'rgba(60, 141, 188, 1)',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: 'rgba(60, 141, 188, 1)',
        borderWidth: 2,
        fill: true
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
  <?php endif; ?>
});
</script>
</body>
</html>