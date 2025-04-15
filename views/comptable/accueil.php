<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer le nombre d'élèves inscrits
$result = $mysqli->query("SELECT COUNT(*) AS total_eleves FROM eleves");
$row = $result->fetch_assoc();
$total_eleves = $row['total_eleves'];

// Récupérer le nombre total d'élèves
$total_eleves_total = $total_eleves;

// Récupérer le nombre de professeurs
$result = $mysqli->query("SELECT COUNT(*) AS total_professeurs FROM professeurs");
$row = $result->fetch_assoc();
$total_professeurs = $row['total_professeurs'];

// Récupérer le nombre de directeurs
$result = $mysqli->query("SELECT COUNT(*) AS total_directeurs FROM users WHERE role='director'");
$row = $result->fetch_assoc();
$total_directeurs = $row['total_directeurs'];

// Récupérer le nombre des employés
$result = $mysqli->query("SELECT COUNT(*) AS total_employes FROM employes");
$row = $result->fetch_assoc();
$total_employes = $row['total_employes'];

// Récupérer le nombre de directrices
$result = $mysqli->query("SELECT COUNT(*) AS total_directrices FROM users WHERE role='directrice'");
$row = $result->fetch_assoc();
$total_directrices = $row['total_directrices'];

// Récupérer le nombre de préfets
$result = $mysqli->query("SELECT COUNT(*) AS total_prefets FROM users WHERE role='prefet'");
$row = $result->fetch_assoc();
$total_prefets = $row['total_prefets'];

// Récupérer le nombre de comptables
$result = $mysqli->query("SELECT COUNT(*) AS total_comptables FROM users WHERE role='comptable'");
$row = $result->fetch_assoc();
$total_comptables = $row['total_comptables'];

// Récupérer le nombre total de frais de paiement
$result = $mysqli->query("SELECT SUM(amount_paid) AS total_frais FROM paiements_frais");
$row = $result->fetch_assoc();
$total_frais = $row['total_frais'];

// Récupérer le nombre d'élèves qui ont déjà payé
$result = $mysqli->query("SELECT COUNT(DISTINCT eleve_id) AS eleves_payes FROM paiements_frais");
$row = $result->fetch_assoc();
$eleves_payes = $row['eleves_payes'] ?? 0;

// Récupérer les statistiques des activités du système
$result = $mysqli->query("SELECT 
    SUM(CASE WHEN action_type LIKE '%ajout%' OR action_type LIKE '%add%' OR action_type LIKE '%création%' OR action_type LIKE '%create%' THEN 1 ELSE 0 END) as add_count,
    SUM(CASE WHEN action_type LIKE '%modif%' OR action_type LIKE '%edit%' OR action_type LIKE '%update%' OR action_type LIKE '%mise à jour%' THEN 1 ELSE 0 END) as edit_count,
    SUM(CASE WHEN action_type LIKE '%suppr%' OR action_type LIKE '%delete%' OR action_type LIKE '%effac%' THEN 1 ELSE 0 END) as delete_count,
    SUM(CASE WHEN action_type LIKE '%login%' OR action_type LIKE '%connexion%' THEN 1 ELSE 0 END) as login_count,
    SUM(CASE WHEN action_type LIKE '%logout%' OR action_type LIKE '%déconnexion%' THEN 1 ELSE 0 END) as logout_count,
    SUM(CASE WHEN action_type LIKE '%paiement%' OR action_type LIKE '%payment%' THEN 1 ELSE 0 END) as payment_count
    FROM system_logs");
$row = $result->fetch_assoc();
$add_count = $row['add_count'] ?? 0;
$edit_count = $row['edit_count'] ?? 0;
$delete_count = $row['delete_count'] ?? 0;
$login_count = $total_eleves_total; // Utiliser le total des élèves ici
$logout_count = $total_frais; // Utiliser le total des frais payés ici
$payment_count = $eleves_payes; // Utiliser le nombre d'élèves qui ont payé

// Fermer la connexion à la base de données


// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
if ($user_id>0) {
  # code...
}
// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';
$current_session = isset($current_session) ? $current_session : date('Y') . '-' . (date('Y') + 1);
if (!$mysqli->connect_error) {
  $stmt = $mysqli->prepare("SELECT image FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    if (!empty($user_data['image'])) {
      $_SESSION['image'] = $user_data['image'];
    }
  }
  
  $stmt->close();
  $mysqli->close();
}


// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Tableau de bord</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Comptable&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b><?php echo $role; ?></b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <!-- Dans la section de l'en-tête principal -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/'; ?>" class="img-circle" alt="Image utilisateur">
                <p><?php echo $role; ?></p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=profil" class="btn btn-default btn-flat">Profil</a>
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
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
            <i class="fa fa-pencil"></i> <span>Inscription</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
            <i class="fa fa-money"></i> <span>Paiement frais</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
            <i class="fa fa-check-circle"></i> <span>Élèves en ordre</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Tableau de bord
        <small>Panneau de contrôle</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Tableau de bord</li>
      </ol>

      <?php if (isset($showWelcomeMessage) && $showWelcomeMessage): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Bienvenue <?php echo htmlspecialchars($username); ?> !</strong> Vous êtes maintenant connecté en tant que <?php echo htmlspecialchars($role); ?>.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
    </section>

    <section class="content">
      <!-- Année scolaire actuelle -->
      <div class="row">
        <div class="col-lg-12 col-xs-12">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3>Année Scolaire <?php echo $current_session; ?></h3>
              <p>Session Actuelle</p>
            </div>
            <div class="icon">
              <i class="fa fa-calendar"></i>
            </div>
            <a href="#" class="small-box-footer">Plus d'informations <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      
      <!-- Statistiques -->
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
            <a href="#" class="small-box-footer">Voir plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $total_eleves_total; ?></h3>
              <p>Élèves total</p>
            </div>
            <div class="icon">
              <i class="fa fa-group"></i>
            </div>
            <a href="#" class="small-box-footer">Voir plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $total_professeurs; ?></h3>
              <p>Professeurs</p>
            </div>
            <div class="icon">
              <i class="fa fa-user"></i>
            </div>
            <a href="#" class="small-box-footer">Voir plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo $total_directeurs;?></h3>
              <p>Directeurs</p>
            </div>
            <div class="icon">
              <i class="fa fa-male"></i>
            </div>
            <a href="#" class="small-box-footer">Voir Plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $total_directrices; ?></h3>
              <p>Directrices</p>
            </div>
            <div class="icon">
              <i class="fa fa-female"></i>
            </div>
            <a href="#" class="small-box-footer">Voir plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $total_prefets; ?></h3>
              <p>Préfets</p>
            </div>
            <div class="icon">
              <i class="fa fa-male"></i>
            </div>
            <a href="#" class="small-box-footer">Voir plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $total_comptables; ?></h3>
              <p>Comptables</p>
            </div>
            <div class="icon">
              <i class="fa fa-calculator"></i>
            </div>
            <a href="#" class="small-box-footer">Voir plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $total_employes; ?></h3>
              <p>Employés</p>
            </div>
            <div class="icon">
              <i class="fa fa-group"></i>
            </div>
            <a href="#" class="small-box-footer">Voir plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo $total_frais; ?></h3>
              <p>Frais de paiement</p>
            </div>
            <div class="icon">
              <i class="fa fa-dollar"></i>
            </div>
            <a href="#" class="small-box-footer">Voir Plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?php echo $eleves_payes; ?></h3>
              <p>Élèves ayant payé</p>
            </div>
            <div class="icon">
              <i class="fa fa-check"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements" class="small-box-footer">Voir Plus <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Graphique des activités du système -->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Statistiques des Activités du Système</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="chart">
            <canvas id="activityChart" style="height:230px"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>

<script>
$(function () {
  // Graphique des activités
  var ctx = document.getElementById('activityChart').getContext('2d');
  var activityChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Ajouts', 'Modifications', 'Suppressions', 'Total Élèves', 'Frais Payés', 'Élèves Payants'],
      datasets: [{
        label: 'Statistiques du système',
        data: [<?php echo $add_count; ?>, <?php echo $edit_count; ?>, <?php echo $delete_count; ?>, <?php echo $login_count; ?>, <?php echo $logout_count; ?>, <?php echo $payment_count; ?>],
        backgroundColor: 'rgba(60, 141, 188, 0.2)',
        borderColor: '#3c8dbc',
        borderWidth: 3,
        pointRadius: 5,
        pointBackgroundColor: '#3c8dbc',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: '#3c8dbc',
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
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
<!-- Modal pour changer la photo de profil -->
<div class="modal fade" id="changePhotoModal" tabindex="-1" role="dialog" aria-labelledby="changePhotoModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="changePhotoModalLabel">Changer ma photo de profil</h4>
      </div>
      <form action="<?php echo BASE_URL; ?>index.php?controller=User&action=updateProfilePhoto" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label for="profile_photo">Sélectionner une nouvelle photo</label>
            <input type="file" id="profile_photo" name="profile_photo" class="form-control" accept="image/*" required>
            <p class="help-block">Formats acceptés: JPG, PNG, GIF. Taille maximale: 2MB</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
<?php if (isset($showWelcomeMessage) && $showWelcomeMessage): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Bienvenue <?php echo htmlspecialchars($username); ?> !</strong> Vous êtes maintenant connecté en tant que <?php echo htmlspecialchars($role); ?>.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<!-- Continuer avec le reste de votre contenu -->
