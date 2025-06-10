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

// Récupération des classes secondaires avec informations sur les élèves
$query = "SELECT c.*, 
          COUNT(e.id) as nb_eleves,
          COUNT(CASE WHEN e.sexe = 'M' THEN 1 END) as nb_garcons,
          COUNT(CASE WHEN e.sexe = 'F' THEN 1 END) as nb_filles
          FROM classes c 
          LEFT JOIN eleves e ON c.id = e.classe_id AND e.section = 'secondaire'
          WHERE c.section = 'secondaire'
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
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <!-- CSS externe pour la gestion des classes -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/classes.css">
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
            <i class="fa fa-graduation-cap"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs">
            <i class="fa fa-users"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes">
            <i class="fa fa-university"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours">
            <i class="fa fa-calendar"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=examens">
            <i class="fa fa-edit"></i> <span>Examens</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=resultatsScolaires">
            <i class="fa fa-bar-chart"></i> <span>Résultats</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTemps">
            <i class="fa fa-table"></i> <span>Emplois du temps</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires">
            <i class="fa fa-calendar-check-o"></i> <span>Événements</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapports">
            <i class="fa fa-pie-chart"></i> <span>Rapports</span>
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
    </section>    <section class="content">
      <div class="classes-container">
        <!-- En-tête moderne -->
        <div class="page-header animate-fade-in-up">
          <h1><i class="fa fa-university"></i> Gestion des Classes</h1>
          <p>Vue d'ensemble et gestion des classes du secondaire</p>
        </div>

        <!-- Boutons d'action -->
        <div class="action-buttons animate-fade-in-up animate-delay-1">
          <a href="#" class="modern-btn btn-success-modern">
            <i class="fa fa-plus"></i> Nouvelle Classe
          </a>
          <a href="#" class="modern-btn btn-primary-modern">
            <i class="fa fa-download"></i> Exporter
          </a>
        </div>

        <!-- Statistiques modernes -->
        <div class="row stats-row animate-fade-in-up animate-delay-2">
          <div class="col-lg-3 col-md-6">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fa fa-university"></i>
              </div>
              <div class="stat-number"><?php echo $classes_total; ?></div>
              <div class="stat-label">Total Classes</div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fa fa-users"></i>
              </div>
              <div class="stat-number"><?php echo array_sum(array_column($classes, 'nb_eleves')); ?></div>
              <div class="stat-label">Total Élèves</div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fa fa-male"></i>
              </div>
              <div class="stat-number"><?php echo array_sum(array_column($classes, 'nb_garcons')); ?></div>
              <div class="stat-label">Garçons</div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fa fa-female"></i>
              </div>
              <div class="stat-number"><?php echo array_sum(array_column($classes, 'nb_filles')); ?></div>
              <div class="stat-label">Filles</div>
            </div>
          </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="filters-container animate-fade-in-up animate-delay-3">
          <div class="filters-row">
            <div class="form-group-modern">
              <label class="form-label-modern">Rechercher une classe</label>
              <div class="search-box">
                <input type="text" class="form-control-modern search-input" placeholder="Nom de classe, niveau...">
                <i class="fa fa-search search-icon"></i>
              </div>
            </div>
            <div class="form-group-modern">
              <label class="form-label-modern">Filtrer par niveau</label>
              <select class="form-control-modern">
                <option value="">Tous les niveaux</option>
                <option value="6ème">6ème</option>
                <option value="5ème">5ème</option>
                <option value="4ème">4ème</option>
                <option value="3ème">3ème</option>
                <option value="2nde">2nde</option>
                <option value="1ère">1ère</option>
                <option value="Tle">Terminale</option>
              </select>
            </div>
            <div class="form-group-modern">
              <button class="modern-btn btn-primary-modern">
                <i class="fa fa-filter"></i> Filtrer
              </button>
            </div>
          </div>
        </div>

        <!-- Grille des classes -->
        <?php if (!empty($classes)): ?>
          <div class="classes-grid animate-fade-in-up animate-delay-4">
            <?php foreach ($classes as $classe): ?>
              <div class="classe-card hover-effect">
                <div class="classe-header">
                  <h3 class="classe-title"><?php echo htmlspecialchars($classe['nom'] ?? 'Classe ' . $classe['niveau']); ?></h3>
                  <span class="classe-level"><?php echo htmlspecialchars($classe['niveau']); ?></span>
                </div>
                
                <div class="classe-stats">
                  <div class="stat-item">
                    <div class="stat-item-number"><?php echo $classe['nb_eleves']; ?></div>
                    <div class="stat-item-label">Élèves</div>
                  </div>
                  <div class="stat-item">
                    <div class="stat-item-number"><?php echo $classe['nb_garcons']; ?> / <?php echo $classe['nb_filles']; ?></div>
                    <div class="stat-item-label">G / F</div>
                  </div>
                </div>

                <div class="classe-actions">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=elevesClasse&id=<?php echo $classe['id']; ?>" 
                     class="classe-btn btn-info-classe" title="Voir les élèves">
                    <i class="fa fa-users"></i> Élèves
                  </a>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTemps&classe_id=<?php echo $classe['id']; ?>" 
                     class="classe-btn btn-warning-classe" title="Emploi du temps">
                    <i class="fa fa-calendar"></i> EDT
                  </a>
                  <a href="#" class="classe-btn btn-danger-classe" title="Modifier la classe">
                    <i class="fa fa-edit"></i> Modifier
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state animate-fade-in-up animate-delay-4">
            <div class="empty-state-icon">
              <i class="fa fa-university"></i>
            </div>
            <h3 class="empty-state-title">Aucune classe trouvée</h3>
            <p class="empty-state-description">Il n'y a actuellement aucune classe dans le système.</p>
            <a href="#" class="modern-btn btn-success-modern">
              <i class="fa fa-plus"></i> Créer une nouvelle classe
            </a>
          </div>
        <?php endif; ?>
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
