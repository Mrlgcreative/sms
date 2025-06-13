<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Forcer la section à "primaire" pour n'afficher que les classes du primaire
$section = "primaire";

// Requête pour récupérer les classes du primaire uniquement
$classes = [];
$classes_query = "SELECT c.*, p.nom as prof_nom, p.prenom as prof_prenom 
                 FROM classes c 
                 LEFT JOIN professeurs p ON c.titulaire = p.id
                 WHERE c.section = 'primaire'
                 ORDER BY c.niveau, c.nom";

$classes_result = $mysqli->query($classes_query);

if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Requête pour obtenir des statistiques sur les classes primaires
$stats_query = "SELECT 
                COUNT(*) as total_classes,
                COUNT(DISTINCT niveau) as total_niveaux,
                SUM(CASE WHEN titulaire IS NOT NULL THEN 1 ELSE 0 END) as classes_avec_titulaire,
                SUM(CASE WHEN titulaire IS NULL THEN 1 ELSE 0 END) as classes_sans_titulaire
                FROM classes 
                WHERE section = 'primaire'";
                
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_assoc();

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
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Director';

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Classes Primaires</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  
 <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Classes
        <small>Liste des classes - Section Primaire</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Classes Primaires</li>
      </ol>
    </section>

    <section class="content">
      <!-- Boîte d'informations générales -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations générales - Classes Primaires</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-table"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total des classes</span>
                      <span class="info-box-number"><?php echo $stats['total_classes']; ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-bar-chart"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Niveaux différents</span>
                      <span class="info-box-number"><?php echo $stats['total_niveaux']; ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-graduation-cap"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Classes avec titulaire</span>
                      <span class="info-box-number"><?php echo $stats['classes_avec_titulaire']; ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Classes sans titulaire</span>
                      <span class="info-box-number"><?php echo $stats['classes_sans_titulaire']; ?></span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-12">
                  <div class="progress-group">
                    <span class="progress-text">Taux d'attribution des titulaires</span>
                    <span class="progress-number"><b><?php echo $stats['classes_avec_titulaire']; ?></b>/<?php echo $stats['total_classes']; ?></span>
                    <div class="progress">
                      <div class="progress-bar progress-bar-green" style="width: <?php echo ($stats['total_classes'] > 0) ? ($stats['classes_avec_titulaire'] / $stats['total_classes'] * 100) : 0; ?>%"></div>
                    </div>
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
              <h3 class="box-title">Liste des classes primaires</h3>
            </div>
            
            <div class="box-body">
              <table id="classes-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Niveau</th>
                    <th>Professeur </th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $classe): ?>
                      <tr>
                        <td><?php echo $classe['id']; ?></td>
                        <td><?php echo htmlspecialchars($classe['nom']); ?></td>
                        <td><?php echo htmlspecialchars($classe['niveau']); ?></td>
                        <td>
                          <?php 
                            if (!empty($classe['titulaire']) && !empty($classe['titulaire'])) {
                              echo htmlspecialchars($classe['titulaire'] . ' ' . $classe['titulaire']);
                            } else {
                              echo '<span class="text-muted">Non assigné</span>';
                            }
                          ?>
                        </td>
                        <td>
                          <div class="btn-group">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=voirEleves&classe=<?php echo $classe['id']; ?>" class="btn btn-info btn-sm">
                              <i class="fa fa-users"></i> Voir élèves
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center">Aucune classe primaire trouvée</td>
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
  $('#classes-table').DataTable({
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