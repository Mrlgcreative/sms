<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des statistiques pour la section secondaire uniquement
$total_eleves_secondaire = $mysqli->query("SELECT COUNT(*) AS total FROM eleves WHERE section='secondaire'")->fetch_assoc()['total'];
$total_professeurs = $mysqli->query("SELECT COUNT(*) AS total FROM professeurs")->fetch_assoc()['total'];

// Récupération des élèves par classe (section secondaire uniquement)
$eleves_par_classe = [];
$eleves_par_classe_query = "SELECT e.classe, COUNT(e.id) as total 
                           FROM eleves e 
                           WHERE e.section='secondaire'
                           GROUP BY e.classe 
                           ORDER BY total DESC";
$eleves_par_classe_result = $mysqli->query($eleves_par_classe_query);
if ($eleves_par_classe_result) {
    while ($row = $eleves_par_classe_result->fetch_assoc()) {
        $eleves_par_classe[] = $row;
    }
}

// Récupération des cours par professeur
$cours_par_prof = [];
$cours_par_prof_query = "SELECT p.nom, p.prenom, COUNT(c.id) as total_cours 
                         FROM professeurs p 
                         LEFT JOIN cours c ON p.id = c.professeur_id 
                         GROUP BY p.id 
                         ORDER BY total_cours DESC 
                         LIMIT 5";
$cours_par_prof_result = $mysqli->query($cours_par_prof_query);
if ($cours_par_prof_result) {
    while ($row = $cours_par_prof_result->fetch_assoc()) {
        $cours_par_prof[] = $row;
    }
}

// Récupération des derniers événements scolaires
$evenements = [];
$evenements_query = "SELECT titre, date_evenement, description 
                    FROM evenements_scolaires 
                    WHERE date_evenement >= CURDATE() 
                    ORDER BY date_evenement ASC 
                    LIMIT 3";
$evenements_result = $mysqli->query($evenements_query);
if ($evenements_result) {
    while ($row = $evenements_result->fetch_assoc()) {
        $evenements[] = $row;
    }
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
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Préfet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Tableau de bord Préfet</title>
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
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves Secondaire</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=classes">
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=absences">
            <i class="fa fa-clock-o"></i> <span>Gestion des Absences</span>
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
        Tableau de bord Préfet
        <small>Section Secondaire</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Tableau de bord</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-lg-6 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $total_eleves_secondaire; ?></h3>
              <p>Élèves Secondaire</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=eleves" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-6 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $total_professeurs; ?></h3>
              <p>Professeurs</p>
            </div>
            <div class="icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=professeurs" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Statistiques des élèves par classe -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Élèves Secondaire par classe</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
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
        
        <!-- Cours par professeur -->
        <div class="col-md-6">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Cours par professeur</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Professeur</th>
                      <th>Nombre de cours</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($cours_par_prof)) {
                      foreach ($cours_par_prof as $row) {
                        echo "<tr>
                                <td>{$row['nom']} {$row['prenom']}</td>
                                <td>{$row['total_cours']}</td>
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
      </div>

      <!-- Graphique des élèves par classe -->
      <div class="row">
        <div class="col-md-8">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des élèves par classe</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="elevesChart" style="height:250px"></canvas>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Prochains événements -->
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Prochains événements</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php if (!empty($evenements)): ?>
                <ul class="products-list product-list-in-box">
                  <?php foreach ($evenements as $event): ?>
                    <li class="item">
                      <div class="product-info">
                        <a href="javascript:void(0)" class="product-title">
                          <?php echo htmlspecialchars($event['titre']); ?>
                          <span class="label label-info pull-right"><?php echo date('d/m/Y', strtotime($event['date_evenement'])); ?></span>
                        </a>
                        <span class="product-description">
                          <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . (strlen($event['description']) > 100 ? '...' : ''); ?>
                        </span>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="text-center">Aucun événement à venir</p>
              <?php endif; ?>
            </div>
            <div class="box-footer text-center">
              <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=evenementsScolaires" class="uppercase">Voir tous les événements</a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Accès rapides -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Accès rapides</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=absences" class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Gestion des</span>
                      <span class="info-box-number">Absences</span>
                    </div>
                  </a>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=discipline" class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-gavel"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Gestion de la</span>
                      <span class="info-box-number">Discipline</span>
                    </div>
                  </a>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=emploiTemps" class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-calendar-check-o"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Emploi du</span>
                      <span class="info-box-number">Temps</span>
                    </div>
                  </a>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=bulletins" class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-file-text"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Gestion des</span>
                      <span class="info-box-number">Bulletins</span>
                    </div>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Préparer les données pour le graphique
  var classLabels = [];
  var classData = [];
  var backgroundColors = [
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
  ];
  
  <?php
  if (!empty($eleves_par_classe)) {
    echo "// Données des élèves par classe\n";
    foreach ($eleves_par_classe as $index => $row) {
      echo "classLabels.push('" . addslashes($row['classe']) . "');\n";
      echo "classData.push(" . $row['total'] . ");\n";
    }
  }
  ?>
  
  // Créer le graphique des élèves par classe
  var elevesCtx = document.getElementById('elevesChart').getContext('2d');
  var elevesChart = new Chart(elevesCtx, {
    type: 'bar',
    data: {
      labels: classLabels,
      datasets: [{
        label: 'Nombre d\'élèves',
        data: classData,
        backgroundColor: backgroundColors,
        borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      legend: {
        position: 'top',
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });
});
</script>
</body>
</html>