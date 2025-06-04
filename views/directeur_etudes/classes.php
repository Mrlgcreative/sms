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

// Récupération des classes avec informations sur les élèves
$query = "SELECT c.*, 
          COUNT(e.id) as nb_eleves,
          COUNT(CASE WHEN e.sexe = 'M' THEN 1 END) as nb_garcons,
          COUNT(CASE WHEN e.sexe = 'F' THEN 1 END) as nb_filles
          FROM classes c 
          LEFT JOIN eleves e ON c.id = e.classe_id AND ( e.section = 'secondaire')
          GROUP BY c.id
          ORDER BY c.niveau, c.nom";
$result = $mysqli->query($query);

$classes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Récupérer les informations de l'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$classes_total = count($classes);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Gestion des Classes</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
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
        Gestion des Classes
        <small>Vue d'ensemble des classes</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Classes</li>
      </ol>
    </section>

    <section class="content">
      <!-- Statistiques -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $classes_total; ?></h3>
              <p>Total Classes</p>
            </div>
            <div class="icon">
              <i class="fa fa-university"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo array_sum(array_column($classes, 'nb_eleves')); ?></h3>
              <p>Total Élèves</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo array_sum(array_column($classes, 'nb_garcons')); ?></h3>
              <p>Garçons</p>
            </div>
            <div class="icon">
              <i class="fa fa-male"></i>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo array_sum(array_column($classes, 'nb_filles')); ?></h3>
              <p>Filles</p>
            </div>
            <div class="icon">
              <i class="fa fa-female"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste des classes -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Classes</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                     
                      <th>Niveau</th>
                      <th>Section</th>
                      <th>Total Élèves</th>
                      <th>Garçons</th>
                      <th>Filles</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($classes)): ?>
                      <?php foreach ($classes as $classe): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($classe['id']); ?></td>
                          
                          <td><?php echo htmlspecialchars($classe['niveau']); ?></td>
                          <td><?php echo htmlspecialchars($classe['section']); ?></td>
                          <td>
                            <span class="badge bg-blue"><?php echo $classe['nb_eleves']; ?></span>
                          </td>
                          <td>
                            <span class="badge bg-yellow"><?php echo $classe['nb_garcons']; ?></span>
                          </td>
                          <td>
                            <span class="badge bg-red"><?php echo $classe['nb_filles']; ?></span>
                          </td>
                          <td>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=voirClasse&id=<?php echo $classe['id']; ?>" class="btn btn-info btn-xs" title="Voir détails">
                              <i class="fa fa-eye"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=elevesClasse&id=<?php echo $classe['id']; ?>" class="btn btn-success btn-xs" title="Élèves de la classe">
                              <i class="fa fa-users"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="8" class="text-center">Aucune classe trouvée.</td>
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
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
