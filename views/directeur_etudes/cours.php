<?php
// Assurez-vous que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des cours avec informations sur les professeurs et classes
$query = "SELECT c.*, 
          p.nom as professeur_nom, p.prenom as professeur_prenom,
          cl.nom as classe_nom, cl.niveau as classe_niveau
          FROM cours c 
          LEFT JOIN professeurs p ON c.professeur_id = p.id
          LEFT JOIN classes cl ON c.classe_id = cl.id
          WHERE c.section IN ('secondaire')
          ORDER BY c.titre, cl.niveau";
$result = $mysqli->query($query);

$cours = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cours[] = $row;
    }
}

// Récupérer les statistiques
$stats_query = "SELECT 
    COUNT(*) as total_cours,
    COUNT(CASE WHEN section = 'primaire' THEN 1 END) as cours_primaire,
    COUNT(CASE WHEN section = 'secondaire' THEN 1 END) as cours_secondaire,
    SUM(heures_semaine) as total_heures
    FROM cours 
    WHERE section IN ('primaire', 'secondaire')";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Récupérer les informations de l'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Gestion des Cours</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil" class="logo">
      <span class="logo-mini"><b>S</b>MS</span>
      <span class="logo-lg"><b>Directeur Études</b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['user_image']) ? BASE_URL . $_SESSION['user_image'] : BASE_URL . 'dist/img/user_default.png'; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo htmlspecialchars($username); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo isset($_SESSION['user_image']) ? BASE_URL . $_SESSION['user_image'] : BASE_URL . 'dist/img/user_default.png'; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo htmlspecialchars($username); ?> - <?php echo htmlspecialchars($role); ?>
                  <small>Membre depuis <?php echo isset($_SESSION['user_creation_date']) ? date('M. Y', strtotime($_SESSION['user_creation_date'])) : 'N/A'; ?></small>
                </p>
              </li>
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
          <img src="<?php echo isset($_SESSION['user_image']) ? BASE_URL . $_SESSION['user_image'] : BASE_URL . 'dist/img/user_default.png'; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo htmlspecialchars($username); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>      <ul class="sidebar-menu" data-widget="tree">
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
        Gestion des Cours
        <small>Vue d'ensemble des cours dispensés</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Cours</li>
      </ol>
    </section>

    <section class="content">
      <!-- Statistiques -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $stats['total_cours'] ?? 0; ?></h3>
              <p>Total Cours</p>
            </div>
            <div class="icon">
              <i class="fa fa-book"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $stats['cours_primaire'] ?? 0; ?></h3>
              <p>Cours Primaire</p>
            </div>
            <div class="icon">
              <i class="fa fa-child"></i>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $stats['cours_secondaire'] ?? 0; ?></h3>
              <p>Cours Secondaire</p>
            </div>
            <div class="icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo $stats['total_heures'] ?? 0; ?>h</h3>
              <p>Total Heures/Semaine</p>
            </div>
            <div class="icon">
              <i class="fa fa-clock-o"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste des cours -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Cours</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="coursTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Titre</th>
                      <th>Description</th>
                      <th>Professeur</th>
                      <th>Classe</th>
                      <th>Section</th>
                      <th>Coefficient</th>
                      <th>Heures/Semaine</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($cours)): ?>
                      <?php foreach ($cours as $cour): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($cour['id']); ?></td>
                          <td><?php echo htmlspecialchars($cour['titre']); ?></td>
                          <td><?php echo htmlspecialchars(substr($cour['description'], 0, 50)) . (strlen($cour['description']) > 50 ? '...' : ''); ?></td>
                          <td>
                            <?php if ($cour['professeur_nom']): ?>
                              <?php echo htmlspecialchars($cour['professeur_nom'] . ' ' . $cour['professeur_prenom']); ?>
                            <?php else: ?>
                              <span class="text-muted">Non assigné</span>
                            <?php endif; ?>
                          </td>
                          <td><?php echo htmlspecialchars($cour['classe_nom'] ?? 'Non assigné'); ?></td>
                          <td>
                            <span class="label <?php echo ($cour['section'] == 'primaire') ? 'label-primary' : 'label-success'; ?>">
                              <?php echo ucfirst($cour['section']); ?>
                            </span>
                          </td>
                          <td>
                            <span class="badge bg-blue"><?php echo $cour['coefficient']; ?></span>
                          </td>
                          <td>
                            <span class="badge bg-orange"><?php echo $cour['heures_semaine']; ?>h</span>
                          </td>
                          <td>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=voirCours&id=<?php echo $cour['id']; ?>" class="btn btn-info btn-xs" title="Voir détails">
                              <i class="fa fa-eye"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTemps&cours_id=<?php echo $cour['id']; ?>" class="btn btn-success btn-xs" title="Emploi du temps">
                              <i class="fa fa-calendar"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="9" class="text-center">Aucun cours trouvé.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SMS St Sophie</a>.</strong> Tous droits réservés.
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
  $('#coursTable').DataTable({
    'paging': true,
    'lengthChange': true,
    'searching': true,
    'ordering': true,
    'info': true,
    'autoWidth': false,
    'language': {
      'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
    }
  });
});
</script>
</body>
</html>
