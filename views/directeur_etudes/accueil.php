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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/morris.js/morris.css">  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- CSS externe pour le tableau de bord du directeur des études -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/directeur-etudes-dashboard.css">
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
      </div>      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        <li class="active">
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
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapports">
            <i class="fa fa-bar-chart"></i> <span>Rapports</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=communications">
            <i class="fa fa-envelope"></i> <span>Communications</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=carte_eleve">
            <i class="fa fa-credit-card"></i> <span>Cartes Élèves</span>
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
    </section>    <section class="content">
      <!-- Dashboard Header -->
      <div class="row">
        <div class="col-md-12">
          <div class="dashboard-welcome animate-fade-in">
            <h2 style="color: white; margin: 0; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
              <i class="fa fa-graduation-cap"></i> Tableau de bord - Directeur des Études
            </h2>
            <p style="color: rgba(255,255,255,0.9); margin-top: 10px; font-size: 1.1em;">
              Bienvenue, <?php echo htmlspecialchars($username); ?>. Voici un aperçu de votre établissement scolaire.
            </p>
          </div>
        </div>
      </div>

      <!-- Modern Statistics Cards -->
      <div class="row">
        <div class="col-lg-4 col-md-6 animate-fade-in animate-delay-1">
          <div class="modern-stat-card">
            <div class="stat-icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="stat-number"><?php echo $total_eleves_toutes_sections; ?></div>
            <div class="stat-label">Élèves au Secondaire</div>
            <div class="stat-footer">
              <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves">
                Voir tous les élèves <i class="fa fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 animate-fade-in animate-delay-2">
          <div class="modern-stat-card">
            <div class="stat-icon">
              <i class="fa fa-users"></i>
            </div>
            <div class="stat-number"><?php echo $total_professeurs_toutes_sections; ?></div>
            <div class="stat-label">Professeurs Actifs</div>
            <div class="stat-footer">
              <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs">
                Gérer les professeurs <i class="fa fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 animate-fade-in animate-delay-3">
          <div class="modern-stat-card">
            <div class="stat-icon">
              <i class="fa fa-building"></i>
            </div>
            <div class="stat-number"><?php echo $total_classes_toutes_sections; ?></div>
            <div class="stat-label">Classes Secondaires</div>
            <div class="stat-footer">
              <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes">
                Voir les classes <i class="fa fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Access Section -->
      <div class="row animate-fade-in animate-delay-4">
        <div class="col-md-12">
          <div class="modern-box">
            <div class="box-header">
              <h3><i class="fa fa-rocket"></i> Accès Rapide aux Fonctionnalités</h3>
            </div>
            <div class="box-body">
              <div class="quick-access-grid">
                <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours" class="quick-access-item">
                  <div class="icon-wrapper">
                    <i class="fa fa-book"></i>
                  </div>
                  <h4>Gestion des Cours</h4>
                  <p>Organisez et planifiez les cours du secondaire</p>
                </a>
                
                <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires" class="quick-access-item">
                  <div class="icon-wrapper">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <h4>Événements Scolaires</h4>
                  <p>Planifiez et suivez les événements importants</p>
                </a>
                
                <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapports" class="quick-access-item">
                  <div class="icon-wrapper">
                    <i class="fa fa-chart-line"></i>
                  </div>
                  <h4>Rapports & Analyses</h4>
                  <p>Consultez les statistiques et performances</p>
                </a>
                
                <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=communications" class="quick-access-item">
                  <div class="icon-wrapper">
                    <i class="fa fa-envelope"></i>
                  </div>
                  <h4>Communications</h4>
                  <p>Gérez les communications avec l'équipe</p>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Data Analysis Row -->
      <div class="row">
        <!-- Students by Section -->
        <div class="col-md-6 animate-fade-in animate-delay-1">
          <div class="modern-box">
            <div class="box-header">
              <h3><i class="fa fa-pie-chart"></i> Répartition des Élèves</h3>
            </div>
            <div class="box-body">
              <div class="chart-container">
                <canvas id="elevesSectionChart" style="height:300px"></canvas>
              </div>
            </div>
            <div class="box-footer">
              <div class="table-responsive">
                <table class="modern-table table">
                  <thead>
                    <tr>
                      <th><i class="fa fa-tag"></i> Section</th>
                      <th><i class="fa fa-users"></i> Nombre d'élèves</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($eleves_par_section)) {
                      foreach ($eleves_par_section as $row) {
                        echo "<tr>
                                <td><strong>" . htmlspecialchars(ucfirst($row['section'])) . "</strong></td>
                                <td><span class='badge' style='background: linear-gradient(135deg, #6c5ce7, #a29bfe); color: white; padding: 8px 15px; border-radius: 50px;'>{$row['total']}</span></td>
                              </tr>";
                      }
                    } else {
                      echo "<tr><td colspan='2' class='text-center' style='color: #636e72; font-style: italic;'>Aucune donnée disponible</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Upcoming Events -->
        <div class="col-md-6 animate-fade-in animate-delay-2">
          <div class="modern-box">
            <div class="box-header">
              <h3><i class="fa fa-calendar-check"></i> Prochains Événements</h3>
            </div>
            <div class="box-body">
              <?php if (!empty($evenements_importants)): ?>
                <div class="events-container">
                  <?php foreach ($evenements_importants as $event): ?>
                    <div class="event-item">
                      <div class="d-flex justify-content-between align-items-start">
                        <div class="event-content">
                          <div class="event-title">
                            <i class="fa fa-star" style="color: #f39c12; margin-right: 5px;"></i>
                            <?php echo htmlspecialchars($event['titre']); ?>
                          </div>
                          <p style="color: #636e72; margin: 8px 0 0 0; line-height: 1.4;">
                            <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . (strlen($event['description']) > 100 ? '...' : ''); ?>
                          </p>
                        </div>
                        <div class="event-date">
                          <?php echo date('d/m/Y', strtotime($event['date_debut'])); ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="text-center" style="padding: 40px; color: #636e72;">
                  <i class="fa fa-calendar-times" style="font-size: 3em; opacity: 0.3; margin-bottom: 15px;"></i>
                  <p style="font-size: 1.1em; margin: 0;">Aucun événement important à venir</p>
                  <small>Les nouveaux événements apparaîtront ici</small>
                </div>
              <?php endif; ?>
            </div>
            <div class="box-footer">
              <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires" 
                 style="color: #6c5ce7; text-decoration: none; font-weight: 600; display: flex; align-items: center; justify-content: center;">
                <i class="fa fa-calendar-plus" style="margin-right: 8px;"></i>
                Gérer tous les événements
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Quick Tools -->
      <div class="row animate-fade-in animate-delay-3">
        <div class="col-md-12">
          <div class="modern-box">
            <div class="box-header">
              <h3><i class="fa fa-tools"></i> Outils Administratifs</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-3 col-sm-6">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=carte_eleve" 
                     style="display: block; text-decoration: none; color: inherit; text-align: center; padding: 20px; border-radius: 15px; background: rgba(108, 92, 231, 0.1); transition: all 0.3s ease;"
                     onmouseover="this.style.background='rgba(108, 92, 231, 0.2)'"
                     onmouseout="this.style.background='rgba(108, 92, 231, 0.1)'">
                    <i class="fa fa-credit-card" style="font-size: 2.5em; color: #6c5ce7; margin-bottom: 10px;"></i>
                    <h5 style="margin: 0; font-weight: 700;">Cartes Élèves</h5>
                    <small style="color: #636e72;">Gestion des cartes</small>
                  </a>
                </div>
                
                <div class="col-md-3 col-sm-6">
                  <a href="#" 
                     style="display: block; text-decoration: none; color: inherit; text-align: center; padding: 20px; border-radius: 15px; background: rgba(46, 204, 113, 0.1); transition: all 0.3s ease;"
                     onmouseover="this.style.background='rgba(46, 204, 113, 0.2)'"
                     onmouseout="this.style.background='rgba(46, 204, 113, 0.1)'">
                    <i class="fa fa-file-excel" style="font-size: 2.5em; color: #2ecc71; margin-bottom: 10px;"></i>
                    <h5 style="margin: 0; font-weight: 700;">Export Données</h5>
                    <small style="color: #636e72;">Exporter en Excel</small>
                  </a>
                </div>
                
                <div class="col-md-3 col-sm-6">
                  <a href="#" 
                     style="display: block; text-decoration: none; color: inherit; text-align: center; padding: 20px; border-radius: 15px; background: rgba(230, 126, 34, 0.1); transition: all 0.3s ease;"
                     onmouseover="this.style.background='rgba(230, 126, 34, 0.2)'"
                     onmouseout="this.style.background='rgba(230, 126, 34, 0.1)'">
                    <i class="fa fa-print" style="font-size: 2.5em; color: #e67e22; margin-bottom: 10px;"></i>
                    <h5 style="margin: 0; font-weight: 700;">Impressions</h5>
                    <small style="color: #636e72;">Documents PDF</small>
                  </a>
                </div>
                
                <div class="col-md-3 col-sm-6">
                  <a href="#" 
                     style="display: block; text-decoration: none; color: inherit; text-align: center; padding: 20px; border-radius: 15px; background: rgba(231, 76, 60, 0.1); transition: all 0.3s ease;"
                     onmouseover="this.style.background='rgba(231, 76, 60, 0.2)'"
                     onmouseout="this.style.background='rgba(231, 76, 60, 0.1)'">
                    <i class="fa fa-cog" style="font-size: 2.5em; color: #e74c3c; margin-bottom: 10px;"></i>
                    <h5 style="margin: 0; font-weight: 700;">Paramètres</h5>
                    <small style="color: #636e72;">Configuration</small>
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
$(document).ready(function() {
  // Préparer les données pour le graphique des élèves par section
  var sectionLabels = [];
  var sectionData = [];
  var modernColors = [
    '#6c5ce7', // Purple
    '#a29bfe', // Light Purple
    '#fd79a8', // Pink
    '#fdcb6e', // Yellow
    '#6c5ce7', // Purple (repeat)
    '#00b894'  // Green
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
  
  // Configuration du graphique moderne
  var elevesSectionCtx = document.getElementById('elevesSectionChart').getContext('2d');
  
  // Gradient pour le graphique
  var gradient = elevesSectionCtx.createLinearGradient(0, 0, 0, 400);
  gradient.addColorStop(0, '#6c5ce7');
  gradient.addColorStop(1, '#a29bfe');
  
  var elevesSectionChart = new Chart(elevesSectionCtx, {
    type: 'doughnut',
    data: {
      labels: sectionLabels,
      datasets: [{
        label: 'Nombre d\'élèves par Section',
        data: sectionData,
        backgroundColor: modernColors,
        borderColor: '#ffffff',
        borderWidth: 3,
        hoverOffset: 10
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '60%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true,
            font: {
              size: 14,
              weight: '600'
            },
            color: '#2d3436'
          }
        },
        tooltip: {
          backgroundColor: 'rgba(45, 52, 54, 0.9)',
          titleColor: '#ffffff',
          bodyColor: '#ffffff',
          borderColor: '#6c5ce7',
          borderWidth: 2,
          cornerRadius: 10,
          displayColors: true,
          callbacks: {
            label: function(context) {
              var total = context.dataset.data.reduce((a, b) => a + b, 0);
              var percentage = ((context.parsed * 100) / total).toFixed(1);
              return context.label + ': ' + context.parsed + ' élèves (' + percentage + '%)';
            }
          }
        }
      },
      animation: {
        animateRotate: true,
        animateScale: true,
        duration: 2000,
        easing: 'easeOutQuart'
      },
      elements: {
        arc: {
          hoverBorderWidth: 5
        }
      }
    }
  });
  
  // Animation d'entrée pour les cartes
  $('.modern-stat-card').each(function(index) {
    $(this).css({
      'opacity': '0',
      'transform': 'translateY(30px)'
    }).delay(index * 200).animate({
      'opacity': '1'
    }, 600, function() {
      $(this).css('transform', 'translateY(0)');
    });
  });
  
  // Animation au survol pour les liens d'accès rapide
  $('.quick-access-item').hover(
    function() {
      $(this).find('.icon-wrapper').addClass('animated pulse');
    },
    function() {
      $(this).find('.icon-wrapper').removeClass('animated pulse');
    }
  );
  
  // Effet de parallaxe léger sur les cartes
  $(window).scroll(function() {
    var scrollTop = $(this).scrollTop();
    $('.modern-stat-card').each(function() {
      var yPos = -(scrollTop / 10);
      $(this).css('transform', 'translateY(' + yPos + 'px)');
    });
  });
  
  // Compteur animé pour les statistiques
  function animateCount(element, start, end, duration) {
    var range = end - start;
    var current = start;
    var increment = end > start ? 1 : -1;
    var stepTime = Math.abs(Math.floor(duration / range));
    
    var timer = setInterval(function() {
      current += increment;
      $(element).text(current);
      if (current === end) {
        clearInterval(timer);
      }
    }, stepTime);
  }
  
  // Démarrer l'animation des compteurs quand les cartes sont visibles
  $(window).scroll(function() {
    $('.modern-stat-card .stat-number').each(function() {
      var elementTop = $(this).offset().top;
      var elementBottom = elementTop + $(this).outerHeight();
      var viewportTop = $(window).scrollTop();
      var viewportBottom = viewportTop + $(window).height();
      
      if (elementBottom > viewportTop && elementTop < viewportBottom && !$(this).hasClass('animated')) {
        $(this).addClass('animated');
        var finalValue = parseInt($(this).text());
        $(this).text('0');
        animateCount(this, 0, finalValue, 2000);
      }
    });
  });
});
</script>
</body>
</html>