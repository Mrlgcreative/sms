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

// Récupération des résultats scolaires avec informations des élèves, matières et examens
$query = "SELECT e.nom, e.prenom, e.matricule,
                 c.niveau as classe_nom, c.section,
                 m.nom as matiere_nom,
                 ex.type_examen, ex.date_examen,
                 n.note, n.appreciation,
                 AVG(n.note) OVER (PARTITION BY e.id, m.id) as moyenne_matiere,
                 AVG(n.note) OVER (PARTITION BY e.id) as moyenne_generale
          FROM notes n
          JOIN eleves e ON n.eleve_id = e.id
          JOIN examens ex ON n.examen_id = ex.id
          JOIN matieres m ON ex.matiere_id = m.id
          JOIN classes c ON e.classe_id = c.id
          ORDER BY e.nom, e.prenom, m.nom, ex.date_examen DESC";
$result = $mysqli->query($query);

$resultats = [];
$eleves_stats = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $resultats[] = $row;
        
        // Calculer les statistiques par élève
        $key = $row['matricule'];
        if (!isset($eleves_stats[$key])) {
            $eleves_stats[$key] = [
                'nom' => $row['nom'],
                'prenom' => $row['prenom'],
                'matricule' => $row['matricule'],
                'classe' => $row['classe_nom'],
                'section' => $row['section'],
                'notes' => [],
                'moyenne_generale' => 0
            ];
        }
        $eleves_stats[$key]['notes'][] = $row['note'];
        $eleves_stats[$key]['moyenne_generale'] = $row['moyenne_generale'];
    }
}

// Statistiques générales
$stats_query = "SELECT 
                  COUNT(DISTINCT e.id) as total_eleves_notes,
                  COUNT(DISTINCT n.id) as total_notes,
                  AVG(n.note) as moyenne_generale,
                  MIN(n.note) as note_min,
                  MAX(n.note) as note_max,
                  COUNT(CASE WHEN n.note >= 10 THEN 1 END) * 100.0 / COUNT(n.note) as taux_reussite
                FROM notes n
                JOIN eleves e ON n.eleve_id = e.id";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Classement des meilleurs élèves
$classement_query = "SELECT e.nom, e.prenom, e.matricule, c.niveau as classe_nom,
                            AVG(n.note) as moyenne,
                            COUNT(n.id) as nb_notes
                     FROM eleves e
                     JOIN notes n ON e.id = n.eleve_id
                     JOIN classes c ON e.classe_id = c.id
                     GROUP BY e.id
                     HAVING nb_notes >= 3
                     ORDER BY moyenne DESC
                     LIMIT 10";
$classement_result = $mysqli->query($classement_query);
$classement = [];
if ($classement_result) {
    while ($row = $classement_result->fetch_assoc()) {
        $classement[] = $row;
    }
}

// Récupérer les informations de l'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Résultats Scolaires</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <!-- Résultats Scolaires Styles -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/resultats-scolaires.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>index.php" class="logo">
      <span class="logo-mini"><b>SMS</b></span>
      <span class="logo-lg"><b>School</b>MS</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo htmlspecialchars($username); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                <p>
                  <?php echo htmlspecialchars($username); ?>
                  <small><?php echo htmlspecialchars($role); ?></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>views/directeur_etudes/profil.php" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>actions/logout.php" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo htmlspecialchars($username); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>      <!-- Sidebar Menu -->
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
            <i class="fa fa-chalkboard-teacher"></i> <span>Gestion des Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=examens">
            <i class="fa fa-edit"></i> <span>Gestion des Examens</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=resultatsScolaires">
            <i class="fa fa-trophy"></i> <span>Résultats Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTempsGeneral">
            <i class="fa fa-calendar-alt"></i> <span>Emplois du temps</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires">
            <i class="fa fa-calendar-check"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapportsGlobaux">
            <i class="fa fa-pie-chart"></i> <span>Rapports Globaux</span>
          </a>
        </li>

      </ul>
    </section>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <h1>
        Résultats Scolaires
        <small>Analyse des performances académiques</small>
      </h1>      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Résultats Scolaires</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- En-tête moderne -->
      <div class="results-header">
        <div class="header-content">
          <h1>Résultats Scolaires</h1>
          <p>Analyse complète des performances académiques</p>
        </div>
        <div class="header-actions">
          <button class="action-btn primary" onclick="exportResults()">
            <i class="fa fa-download"></i>
            Exporter PDF
          </button>
          <button class="action-btn secondary" onclick="printResults()">
            <i class="fa fa-print"></i>
            Imprimer
          </button>
          <button class="action-btn success" onclick="generateReport()">
            <i class="fa fa-chart-line"></i>
            Générer Rapport
          </button>
        </div>
      </div>

      <!-- Statistiques principales -->
      <div class="stats-grid">
        <div class="stat-card primary">
          <div class="stat-icon">
            <i class="fa fa-users"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['total_eleves_notes']; ?></div>
            <div class="stat-label">Élèves Évalués</div>
          </div>
          <div class="stat-progress">
            <div class="progress-bar" style="width: 85%"></div>
            <span class="progress-text">85% des élèves</span>
          </div>
        </div>

        <div class="stat-card success">
          <div class="stat-icon">
            <i class="fa fa-file-text"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['total_notes']; ?></div>
            <div class="stat-label">Total Notes</div>
          </div>
          <div class="stat-trend positive">
            <i class="fa fa-arrow-up"></i>
            <span>+15%</span>
          </div>
        </div>

        <div class="stat-card warning">
          <div class="stat-icon">
            <i class="fa fa-star"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo number_format($stats['moyenne_generale'], 2); ?>/20</div>
            <div class="stat-label">Moyenne Générale</div>
          </div>
          <div class="stat-indicator <?php echo $stats['moyenne_generale'] >= 12 ? 'good' : ($stats['moyenne_generale'] >= 10 ? 'average' : 'poor'); ?>">
            <?php echo $stats['moyenne_generale'] >= 12 ? 'Excellent' : ($stats['moyenne_generale'] >= 10 ? 'Correct' : 'À améliorer'); ?>
          </div>
        </div>

        <div class="stat-card info">
          <div class="stat-icon">
            <i class="fa fa-check-circle"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo number_format($stats['taux_reussite'], 1); ?>%</div>
            <div class="stat-label">Taux de Réussite</div>
          </div>
          <div class="stat-circle">
            <svg class="progress-ring" width="60" height="60">
              <circle class="progress-ring-circle" 
                      cx="30" cy="30" r="25" 
                      style="stroke-dasharray: <?php echo 2 * 3.14159 * 25; ?>; stroke-dashoffset: <?php echo 2 * 3.14159 * 25 * (1 - $stats['taux_reussite']/100); ?>;">
              </circle>
            </svg>
          </div>
        </div>
      </div>

      <!-- Filtres et recherche -->
      <div class="filters-container">
        <div class="search-box">
          <input type="text" id="searchResults" placeholder="Rechercher un élève...">
          <i class="fa fa-search"></i>
        </div>
        <div class="filter-selects">
          <select class="filter-select" id="filterClasse">
            <option value="">Toutes les classes</option>
            <option value="6eme">6ème</option>
            <option value="5eme">5ème</option>
            <option value="4eme">4ème</option>
            <option value="3eme">3ème</option>
            <option value="2nde">2nde</option>
            <option value="1ere">1ère</option>
            <option value="Tale">Terminale</option>
          </select>
          <select class="filter-select" id="filterNote">
            <option value="">Toutes les notes</option>
            <option value="excellent">Excellent (≥16)</option>
            <option value="bon">Bon (14-15.99)</option>
            <option value="correct">Correct (12-13.99)</option>
            <option value="passable">Passable (10-11.99)</option>
            <option value="insuffisant">Insuffisant (<10)</option>
          </select>
        </div>
      </div>

      <!-- Top des élèves -->
      <div class="top-students">
        <div class="section-header">
          <h2><i class="fa fa-trophy"></i> Top 10 des Meilleurs Élèves</h2>
          <div class="section-actions">
            <button class="view-btn active" data-view="grid">
              <i class="fa fa-th"></i>
            </button>
            <button class="view-btn" data-view="list">
              <i class="fa fa-list"></i>
            </button>
          </div>
        </div>
        
        <div class="students-grid">
          <?php foreach ($classement as $index => $eleve): ?>
          <div class="student-card rank-<?php echo $index + 1; ?>">
            <div class="rank-badge">
              <?php if ($index == 0): ?>
                <i class="fa fa-crown gold"></i>
              <?php elseif ($index == 1): ?>
                <i class="fa fa-medal silver"></i>
              <?php elseif ($index == 2): ?>
                <i class="fa fa-medal bronze"></i>
              <?php else: ?>
                <span class="rank-number"><?php echo $index + 1; ?></span>
              <?php endif; ?>
            </div>
            
            <div class="student-avatar">
              <img src="<?php echo BASE_URL; ?>assets/images/avatars/student-default.png" alt="Avatar">
            </div>
            
            <div class="student-info">
              <h3><?php echo htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']); ?></h3>
              <p class="student-class"><?php echo htmlspecialchars($eleve['classe_nom']); ?></p>
              <p class="student-matricule"><?php echo htmlspecialchars($eleve['matricule']); ?></p>
            </div>
            
            <div class="student-score">
              <div class="score-circle <?php echo $eleve['moyenne'] >= 16 ? 'excellent' : ($eleve['moyenne'] >= 14 ? 'bon' : ($eleve['moyenne'] >= 12 ? 'correct' : ($eleve['moyenne'] >= 10 ? 'passable' : 'insuffisant'))); ?>">
                <?php echo number_format($eleve['moyenne'], 2); ?>
              </div>
              <span class="score-label">/ 20</span>
            </div>
            
            <div class="student-stats">
              <span class="stat-item">
                <i class="fa fa-file-text"></i>
                <?php echo $eleve['nb_notes']; ?> notes
              </span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Tableau des résultats détaillés -->
      <div class="results-table-container">
        <div class="table-header">
          <h2><i class="fa fa-table"></i> Résultats Détaillés</h2>
          <div class="table-actions">
            <button class="action-btn-small" onclick="expandAllRows()">
              <i class="fa fa-expand"></i>
              Tout Développer
            </button>
            <button class="action-btn-small" onclick="collapseAllRows()">
              <i class="fa fa-compress"></i>
              Tout Réduire
            </button>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="results-table" id="resultsTable">
            <thead>
              <tr>
                <th><i class="fa fa-user"></i> Élève</th>
                <th><i class="fa fa-university"></i> Classe</th>
                <th><i class="fa fa-book"></i> Matière</th>
                <th><i class="fa fa-calendar"></i> Date Examen</th>
                <th><i class="fa fa-edit"></i> Type</th>
                <th><i class="fa fa-star"></i> Note</th>
                <th><i class="fa fa-comment"></i> Appréciation</th>
                <th><i class="fa fa-chart-line"></i> Moyenne Matière</th>
                <th><i class="fa fa-trophy"></i> Moyenne Générale</th>
                <th><i class="fa fa-cogs"></i> Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($resultats as $resultat): ?>
              <tr class="result-row">
                <td class="student-cell">
                  <div class="student-info-mini">
                    <strong><?php echo htmlspecialchars($resultat['prenom'] . ' ' . $resultat['nom']); ?></strong>
                    <small><?php echo htmlspecialchars($resultat['matricule']); ?></small>
                  </div>
                </td>
                <td>
                  <span class="class-badge">
                    <?php echo htmlspecialchars($resultat['classe_nom']); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($resultat['matiere_nom']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($resultat['date_examen'])); ?></td>
                <td>
                  <span class="exam-type-badge <?php echo strtolower($resultat['type_examen']); ?>">
                    <?php echo htmlspecialchars($resultat['type_examen']); ?>
                  </span>
                </td>
                <td>
                  <div class="note-display <?php echo $resultat['note'] >= 16 ? 'excellent' : ($resultat['note'] >= 14 ? 'bon' : ($resultat['note'] >= 12 ? 'correct' : ($resultat['note'] >= 10 ? 'passable' : 'insuffisant'))); ?>">
                    <?php echo number_format($resultat['note'], 2); ?>/20
                  </div>
                </td>
                <td>
                  <span class="appreciation">
                    <?php echo htmlspecialchars($resultat['appreciation'] ?: 'Aucune'); ?>
                  </span>
                </td>
                <td>
                  <div class="average-display">
                    <?php echo number_format($resultat['moyenne_matiere'], 2); ?>/20
                  </div>
                </td>
                <td>
                  <div class="general-average <?php echo $resultat['moyenne_generale'] >= 12 ? 'good' : ($resultat['moyenne_generale'] >= 10 ? 'average' : 'poor'); ?>">
                    <?php echo number_format($resultat['moyenne_generale'], 2); ?>/20
                  </div>
                </td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn-mini primary" onclick="viewStudentDetails('<?php echo $resultat['matricule']; ?>')">
                      <i class="fa fa-eye"></i>
                    </button>
                    <button class="action-btn-mini warning" onclick="editNote(<?php echo $resultat['note']; ?>)">
                      <i class="fa fa-edit"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Graphiques d'analyse -->
      <div class="charts-section">
        <div class="charts-grid">
          <div class="chart-card">
            <div class="chart-header">
              <h3><i class="fa fa-bar-chart"></i> Distribution des Notes</h3>
            </div>
            <div class="chart-body">
              <canvas id="notesDistributionChart"></canvas>
            </div>
          </div>
          
          <div class="chart-card">
            <div class="chart-header">
              <h3><i class="fa fa-line-chart"></i> Évolution des Moyennes</h3>
            </div>
            <div class="chart-body">
              <canvas id="averageEvolutionChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- ...existing code... -->
    </section>
  </div>

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2024 <a href="#">School Management System</a>.</strong> Tous droits réservés.
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
  $('#resultatsTable').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'language'    : {
      'url': '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    }
  });
});
</script>

</body>
</html>

<?php
$mysqli->close();
?>
