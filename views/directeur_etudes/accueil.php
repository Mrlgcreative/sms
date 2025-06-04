<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des statistiques globales
$total_eleves_toutes_sections = $mysqli->query("SELECT COUNT(*) AS total FROM eleves WHERE section='secondaire'")->fetch_assoc()['total'];
$total_professeurs_toutes_sections = $mysqli->query("SELECT COUNT(*) AS total FROM professeurs WHERE section ='secondaire'")->fetch_assoc()['total'];
$total_classes_toutes_sections = $mysqli->query("SELECT COUNT(*) AS total FROM classes WHERE section='secondaire'")->fetch_assoc()['total'];

// Récupération des élèves par section
$eleves_par_section = [];
$eleves_par_section_query = "SELECT section, COUNT(*) as total 
                             FROM eleves  WHERE section='secondaire'
                             GROUP BY section 
                             ORDER BY section ASC";
$eleves_par_section_result = $mysqli->query($eleves_par_section_query);
if ($eleves_par_section_result) {
    while ($row = $eleves_par_section_result->fetch_assoc()) {
        $eleves_par_section[] = $row;
    }
}

// Récupération des performances académiques générales (exemple simplifié)
$performances_generales = [];
// Exemple de requête (à adapter) :
/*
$performances_query = "SELECT m.nom_matiere, AVG(r.note) as moyenne_generale 
                       FROM resultats r
                       JOIN cours co ON r.cours_id = co.id
                       JOIN matieres m ON co.matiere_id = m.id
                       GROUP BY m.nom_matiere 
                       ORDER BY moyenne_generale DESC LIMIT 5";
$performances_result = $mysqli->query($performances_query);
if ($performances_result) {
    while ($row = $performances_result->fetch_assoc()) {
        $performances_generales[] = $row;
    }
}
*/

// Récupération des derniers événements scolaires importants
$evenements_importants = [];
$evenements_query = "SELECT titre, date_debut, description 
                     FROM evenements_scolaires 
                     WHERE date_debut >= CURDATE() -- AND importance >= 2 (si vous avez un champ importance)
                     ORDER BY date_debut ASC 
                     LIMIT 3";
$evenements_result = $mysqli->query($evenements_query);
if ($evenements_result) {
    while ($row = $evenements_result->fetch_assoc()) {
        $evenements_importants[] = $row;
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
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Tableau de bord Directeur des Études</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
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
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $username; ?> - <?php echo $role; ?>
                  <small><?php echo $email; ?></small>
                </p>              </li>
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
          <p><?php echo $username; ?></p>
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
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Tableau de bord Directeur des Études
        <small>Vue d'ensemble</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Tableau de bord</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-lg-4 col-xs-6">
          <div class="small-box bg-primary">
            <div class="inner">
              <h3><?php echo $total_eleves_toutes_sections; ?></h3>
              <p>Total Élèves </p>
            </div>
            <div class="icon">
              <i class="ion ion-ios-people"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-4 col-xs-6">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?php echo $total_professeurs_toutes_sections; ?></h3>
              <p>Total Professeurs </p>
            </div>
            <div class="icon">
              <i class="ion ion-university"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-4 col-xs-6">
          <div class="small-box bg-teal">
            <div class="inner">
              <h3><?php echo $total_classes_toutes_sections; ?></h3>
              <p>Total Classes</p>
            </div>
            <div class="icon">
              <i class="ion ion-easel"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Statistiques des élèves par Section -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Élèves par Section</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Section</th>
                      <th>Nombre d'élèves</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($eleves_par_section)) {
                      foreach ($eleves_par_section as $row) {
                        echo "<tr>
                                <td>" . htmlspecialchars(ucfirst($row['section'])) . "</td>
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
        
        <!-- Performances Académiques Générales (Exemple) -->
        <div class="col-md-6">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Performances Académiques (Exemple)</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Indicateur</th>
                      <th>Valeur</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($performances_generales)) {
                      foreach ($performances_generales as $row) {
                        // Adaptez l'affichage en fonction de la structure de $performances_generales
                        echo "<tr>
                                <td>" . htmlspecialchars($row['nom_matiere']) . " (Moyenne)</td> 
                                <td>" . number_format($row['moyenne_generale'], 2) . "</td>
                              </tr>";
                      }
                    } else {
                      echo "<tr><td colspan='2' class='text-center'>Données de performance non disponibles. Configurez la requête.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Graphique des élèves par section -->
      <div class="row">
        <div class="col-md-8">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des élèves par Section</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="elevesSectionChart" style="height:250px"></canvas>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Prochains événements importants -->
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Prochains Événements Importants</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php if (!empty($evenements_importants)): ?>
                <ul class="products-list product-list-in-box">
                  <?php foreach ($evenements_importants as $event): ?>
                    <li class="item">
                      <div class="product-info">
                        <a href="javascript:void(0)" class="product-title">
                          <?php echo htmlspecialchars($event['titre']); ?>
                          <span class="label label-danger pull-right"><?php echo date('d/m/Y', strtotime($event['date_debut'])); ?></span>
                        </a>
                        <span class="product-description">
                          <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . (strlen($event['description']) > 100 ? '...' : ''); ?>
                        </span>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="text-center">Aucun événement important à venir</p>
              <?php endif; ?>
            </div>
            <div class="box-footer text-center">
              <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires" class="uppercase">Voir tous les événements</a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Accès rapides pour Directeur des Études -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Accès rapides</h3>
            </div>
            <div class="box-body">
              <div class="row">                <div class="col-md-3 col-sm-6 col-xs-12">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=programmesScolaires" class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-university"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Programmes</span>
                      <span class="info-box-number">Scolaires</span>
                    </div>
                  </a>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=examens" class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-edit"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Gestion des</span>
                      <span class="info-box-number">Examens</span>
                    </div>
                  </a>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=resultatsScolaires" class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-line-chart"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Résultats</span>
                      <span class="info-box-number">Scolaires</span>
                    </div>
                  </a>
                </div>
                 <div class="col-md-3 col-sm-6 col-xs-12">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapportsGlobaux" class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-pie-chart"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Rapports</span>
                      <span class="info-box-number">Globaux</span>
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
  // Préparer les données pour le graphique des élèves par section
  var sectionLabels = [];
  var sectionData = [];
  var backgroundColors = [
    'rgba(0, 192, 239, 0.8)', // Info
    'rgba(0, 166, 90, 0.8)',  // Success
    'rgba(243, 156, 18, 0.8)', // Warning
    'rgba(221, 75, 57, 0.8)',  // Danger
    'rgba(96, 92, 168, 0.8)', // Purple
    'rgba(60, 141, 188, 0.8)'  // Primary
  ];
  
  <?php
  if (!empty($eleves_par_section)) {
    echo "// Données des élèves par section\n";
    foreach ($eleves_par_section as $index => $row) {
      echo "sectionLabels.push('" . addslashes(ucfirst($row['section'])) . "');\n";
      echo "sectionData.push(" . $row['total'] . ");\n";
    }
  }
  ?>
  
  // Créer le graphique des élèves par section (camembert)
  var elevesSectionCtx = document.getElementById('elevesSectionChart').getContext('2d');
  var elevesSectionChart = new Chart(elevesSectionCtx, {
    type: 'doughnut', // ou 'pie'
    data: {
      labels: sectionLabels,
      datasets: [{
        label: 'Nombre d\'élèves par Section',
        data: sectionData,
        backgroundColor: backgroundColors,
        borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: 'right',
      }
    }
  });
});
</script>
</body>
</html>