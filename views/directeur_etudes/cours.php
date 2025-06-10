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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <!-- Cours CSS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/cours.css">
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
    </section>    <section class="content">
      <!-- Statistiques modernes -->
      <div class="stats-container">
        <div class="stat-card bg-primary">
          <div class="stat-icon">
            <i class="fa fa-book"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo $stats['total_cours'] ?? 0; ?></h3>
            <p>Total Cours</p>
          </div>
        </div>
        
        <div class="stat-card bg-success">
          <div class="stat-icon">
            <i class="fa fa-child"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo $stats['cours_primaire'] ?? 0; ?></h3>
            <p>Cours Primaire</p>
          </div>
        </div>

        <div class="stat-card bg-warning">
          <div class="stat-icon">
            <i class="fa fa-graduation-cap"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo $stats['cours_secondaire'] ?? 0; ?></h3>
            <p>Cours Secondaire</p>
          </div>
        </div>

        <div class="stat-card bg-danger">
          <div class="stat-icon">
            <i class="fa fa-clock-o"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo $stats['total_heures'] ?? 0; ?>h</h3>
            <p>Total Heures/Semaine</p>
          </div>
        </div>
      </div>

      <!-- Filtres et recherche -->
      <div class="filters-container">
        <div class="filter-group">
          <label for="sectionFilter">Section :</label>
          <select id="sectionFilter" class="filter-select">
            <option value="">Toutes les sections</option>
            <option value="primaire">Primaire</option>
            <option value="secondaire">Secondaire</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="searchCours">Rechercher :</label>
          <input type="text" id="searchCours" placeholder="Titre du cours, professeur..." class="search-input">
        </div>
      </div>

      <!-- Grille des cours -->
      <div class="cours-grid">
        <?php if (!empty($cours)): ?>
          <?php foreach ($cours as $cour): ?>
            <div class="cours-card" data-section="<?php echo $cour['section']; ?>">
              <div class="cours-header">
                <h3 class="cours-title"><?php echo htmlspecialchars($cour['titre']); ?></h3>
                <span class="section-badge <?php echo $cour['section']; ?>">
                  <?php echo ucfirst($cour['section']); ?>
                </span>
              </div>
              
              <div class="cours-description">
                <p><?php echo htmlspecialchars(substr($cour['description'], 0, 100)) . (strlen($cour['description']) > 100 ? '...' : ''); ?></p>
              </div>
              
              <div class="cours-details">
                <div class="detail-item">
                  <i class="fa fa-user"></i>
                  <span class="detail-label">Professeur :</span>
                  <span class="detail-value">
                    <?php if ($cour['professeur_nom']): ?>
                      <?php echo htmlspecialchars($cour['professeur_nom'] . ' ' . $cour['professeur_prenom']); ?>
                    <?php else: ?>
                      <span class="text-muted">Non assigné</span>
                    <?php endif; ?>
                  </span>
                </div>
                
                <div class="detail-item">
                  <i class="fa fa-university"></i>
                  <span class="detail-label">Classe :</span>
                  <span class="detail-value"><?php echo htmlspecialchars($cour['classe_nom'] ?? 'Non assigné'); ?></span>
                </div>
                
                <div class="cours-metrics">
                  <div class="metric">
                    <span class="metric-value"><?php echo $cour['coefficient']; ?></span>
                    <span class="metric-label">Coefficient</span>
                  </div>
                  <div class="metric">
                    <span class="metric-value"><?php echo $cour['heures_semaine']; ?>h</span>
                    <span class="metric-label">Par semaine</span>
                  </div>
                </div>
              </div>
              
              <div class="cours-actions">
                <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=voirCours&id=<?php echo $cour['id']; ?>" 
                   class="action-btn btn-view" title="Voir détails">
                  <i class="fa fa-eye"></i>
                  Détails
                </a>
                <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTemps&cours_id=<?php echo $cour['id']; ?>" 
                   class="action-btn btn-schedule" title="Emploi du temps">
                  <i class="fa fa-calendar"></i>
                  Planning
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="fa fa-book"></i>
            <h3>Aucun cours trouvé</h3>
            <p>Il n'y a actuellement aucun cours dans le système.</p>
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
<!-- DataTables -->
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
// Fonctionnalités de filtrage et recherche
$(function () {
  // Filtrage par section
  $('#sectionFilter').on('change', function() {
    const selectedSection = $(this).val();
    filterCours();
  });
  
  // Recherche en temps réel
  $('#searchCours').on('keyup', function() {
    filterCours();
  });
  
  function filterCours() {
    const sectionFilter = $('#sectionFilter').val();
    const searchTerm = $('#searchCours').val().toLowerCase();
    
    $('.cours-card').each(function() {
      const card = $(this);
      const section = card.data('section');
      const title = card.find('.cours-title').text().toLowerCase();
      const professor = card.find('.detail-value').first().text().toLowerCase();
      
      let showCard = true;
      
      // Filtrage par section
      if (sectionFilter && section !== sectionFilter) {
        showCard = false;
      }
      
      // Filtrage par recherche
      if (searchTerm && !title.includes(searchTerm) && !professor.includes(searchTerm)) {
        showCard = false;
      }
      
      if (showCard) {
        card.removeClass('hidden').addClass('visible');
      } else {
        card.removeClass('visible').addClass('hidden');
      }
    });
  }
  
  // Animation des cartes au chargement
  $('.cours-card').each(function(index) {
    $(this).css('animation-delay', (index * 0.1) + 's');
  });
});
</script>
</body>
</html>
