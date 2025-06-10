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

// Récupération des programmes scolaires avec informations des matières et classes
$query = "SELECT ps.*, c.niveau as classe_nom, c.section,
                 COUNT(DISTINCT m.id) as nb_matieres,
                 SUM(m.heures_semaine) as total_heures,
                 AVG(m.coefficient) as coefficient_moyen
          FROM programmes_scolaires ps 
          LEFT JOIN classes c ON ps.classe_id = c.id
          LEFT JOIN matieres m ON ps.id = m.programme_id
          GROUP BY ps.id
          ORDER BY c.section, c.niveau";
$result = $mysqli->query($query);

$programmes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $programmes[] = $row;
    }
}

// Statistiques des programmes
$stats_query = "SELECT 
                  COUNT(DISTINCT ps.id) as total_programmes,
                  COUNT(DISTINCT CASE WHEN c.section = 'primaire' THEN ps.id END) as programmes_primaire,
                  COUNT(DISTINCT CASE WHEN c.section = 'secondaire' THEN ps.id END) as programmes_secondaire,
                  COUNT(DISTINCT m.id) as total_matieres,
                  SUM(m.heures_semaine) as total_heures_semaine
                FROM programmes_scolaires ps 
                LEFT JOIN classes c ON ps.classe_id = c.id
                LEFT JOIN matieres m ON ps.id = m.programme_id";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_assoc();

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
  <title>SMS | Programmes Scolaires</title>
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
  <!-- Programmes Scolaires Styles -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/programmes-scolaires.css">

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
  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil" class="logo">
      <span class="logo-mini"><b>S</b>MS</span>
      <span class="logo-lg"><b>Directeur Études</b></span>
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
      </div>

      <!-- Sidebar Menu -->      <ul class="sidebar-menu" data-widget="tree">
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
        
        <li class="active">
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
        
        <li>
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
        Programmes Scolaires
        <small>Gestion des curricula et matières</small>
      </h1>      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Programmes Scolaires</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- En-tête moderne -->
      <div class="programs-header">
        <div class="header-content">
          <h1>Programmes Scolaires</h1>
          <p>Gestion complète des curricula et matières d'enseignement</p>
        </div>
        <div class="header-actions">
          <button class="action-btn primary" data-toggle="modal" data-target="#nouveauProgrammeModal">
            <i class="fa fa-plus"></i>
            Nouveau Programme
          </button>
          <button class="action-btn secondary" onclick="importPrograms()">
            <i class="fa fa-upload"></i>
            Importer
          </button>
          <button class="action-btn info" onclick="exportPrograms()">
            <i class="fa fa-download"></i>
            Exporter
          </button>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="stats-grid">
        <div class="stat-card primary">
          <div class="stat-icon">
            <i class="fa fa-book"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['total_programmes']; ?></div>
            <div class="stat-label">Total Programmes</div>
          </div>
          <div class="stat-indicator active">
            <span>Actifs</span>
          </div>
        </div>

        <div class="stat-card success">
          <div class="stat-icon">
            <i class="fa fa-child"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['programmes_primaire']; ?></div>
            <div class="stat-label">Primaire</div>
          </div>
          <div class="stat-progress">
            <div class="progress-bar" style="width: <?php echo $stats['total_programmes'] > 0 ? ($stats['programmes_primaire']/$stats['total_programmes'])*100 : 0; ?>%"></div>
          </div>
        </div>

        <div class="stat-card info">
          <div class="stat-icon">
            <i class="fa fa-graduation-cap"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['programmes_secondaire']; ?></div>
            <div class="stat-label">Secondaire</div>
          </div>
          <div class="stat-progress">
            <div class="progress-bar" style="width: <?php echo $stats['total_programmes'] > 0 ? ($stats['programmes_secondaire']/$stats['total_programmes'])*100 : 0; ?>%"></div>
          </div>
        </div>

        <div class="stat-card warning">
          <div class="stat-icon">
            <i class="fa fa-clock-o"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['total_heures_semaine']; ?>h</div>
            <div class="stat-label">Heures/Semaine</div>
          </div>
          <div class="stat-trend stable">
            <i class="fa fa-minus"></i>
            <span>Stable</span>
          </div>
        </div>
      </div>

      <!-- Filtres et vue -->
      <div class="filters-container">
        <div class="search-box">
          <input type="text" id="searchPrograms" placeholder="Rechercher un programme...">
          <i class="fa fa-search"></i>
        </div>
        <div class="filter-controls">
          <select class="filter-select" id="filterSection">
            <option value="">Toutes les sections</option>
            <option value="primaire">Primaire</option>
            <option value="secondaire">Secondaire</option>
          </select>
          <select class="filter-select" id="filterNiveau">
            <option value="">Tous les niveaux</option>
            <option value="CP">CP</option>
            <option value="CE1">CE1</option>
            <option value="CE2">CE2</option>
            <option value="CM1">CM1</option>
            <option value="CM2">CM2</option>
            <option value="6eme">6ème</option>
            <option value="5eme">5ème</option>
            <option value="4eme">4ème</option>
            <option value="3eme">3ème</option>
            <option value="2nde">2nde</option>
            <option value="1ere">1ère</option>
            <option value="Tale">Terminale</option>
          </select>
        </div>
        <div class="view-toggle">
          <button class="view-btn active" data-view="cards">
            <i class="fa fa-th"></i>
          </button>
          <button class="view-btn" data-view="table">
            <i class="fa fa-table"></i>
          </button>
        </div>
      </div>

      <!-- Vue en cartes des programmes -->
      <div class="programs-grid" id="programsCards">
        <?php foreach ($programmes as $programme): ?>
        <div class="program-card" data-section="<?php echo $programme['section']; ?>" data-niveau="<?php echo $programme['classe_nom']; ?>">
          <div class="card-header">
            <div class="program-badge <?php echo $programme['section']; ?>">
              <?php echo ucfirst($programme['section']); ?>
            </div>
            <div class="card-actions">
              <button class="card-action-btn" onclick="editProgram(<?php echo $programme['id']; ?>)">
                <i class="fa fa-edit"></i>
              </button>
              <button class="card-action-btn" onclick="duplicateProgram(<?php echo $programme['id']; ?>)">
                <i class="fa fa-copy"></i>
              </button>
              <button class="card-action-btn danger" onclick="deleteProgram(<?php echo $programme['id']; ?>)">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </div>

          <div class="card-content">
            <h3 class="program-title"><?php echo htmlspecialchars($programme['nom']); ?></h3>
            <p class="program-class">
              <i class="fa fa-university"></i>
              <?php echo htmlspecialchars($programme['classe_nom']); ?>
            </p>
            <p class="program-description">
              <?php echo htmlspecialchars(substr($programme['description'], 0, 100)) . '...'; ?>
            </p>

            <div class="program-stats">
              <div class="stat-item">
                <i class="fa fa-book"></i>
                <span><?php echo $programme['nb_matieres']; ?> Matières</span>
              </div>
              <div class="stat-item">
                <i class="fa fa-clock-o"></i>
                <span><?php echo $programme['total_heures']; ?>h/sem</span>
              </div>
              <div class="stat-item">
                <i class="fa fa-star"></i>
                <span>Coeff. <?php echo number_format($programme['coefficient_moyen'], 1); ?></span>
              </div>
            </div>
          </div>

          <div class="card-footer">
            <button class="action-btn-small primary" onclick="viewProgramDetails(<?php echo $programme['id']; ?>)">
              <i class="fa fa-eye"></i>
              Voir Détails
            </button>
            <button class="action-btn-small success" onclick="assignTeachers(<?php echo $programme['id']; ?>)">
              <i class="fa fa-users"></i>
              Affecter Profs
            </button>
            <div class="program-status <?php echo $programme['statut'] ?? 'actif'; ?>">
              <i class="fa fa-circle"></i>
              <?php echo ucfirst($programme['statut'] ?? 'Actif'); ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Vue tableau détaillée -->
      <div class="programs-table-container" id="programsTable" style="display: none;">
        <div class="table-header">
          <h2><i class="fa fa-table"></i> Vue Détaillée des Programmes</h2>
          <div class="table-actions">
            <button class="action-btn-small" onclick="exportTableData()">
              <i class="fa fa-download"></i>
              Exporter
            </button>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th><i class="fa fa-book"></i> Programme</th>
                <th><i class="fa fa-university"></i> Classe</th>
                <th><i class="fa fa-tag"></i> Section</th>
                <th><i class="fa fa-list"></i> Matières</th>
                <th><i class="fa fa-clock-o"></i> Heures/Sem</th>
                <th><i class="fa fa-star"></i> Coeff. Moy.</th>
                <th><i class="fa fa-calendar"></i> Année</th>
                <th><i class="fa fa-circle"></i> Statut</th>
                <th><i class="fa fa-cogs"></i> Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($programmes as $programme): ?>
              <tr>
                <td>
                  <div class="program-info">
                    <strong><?php echo htmlspecialchars($programme['nom']); ?></strong>
                    <small><?php echo htmlspecialchars(substr($programme['description'], 0, 50)) . '...'; ?></small>
                  </div>
                </td>
                <td>
                  <span class="class-badge">
                    <?php echo htmlspecialchars($programme['classe_nom']); ?>
                  </span>
                </td>
                <td>
                  <span class="section-badge <?php echo $programme['section']; ?>">
                    <?php echo ucfirst($programme['section']); ?>
                  </span>
                </td>
                <td>
                  <span class="number-badge">
                    <?php echo $programme['nb_matieres']; ?>
                  </span>
                </td>
                <td>
                  <span class="hours-display">
                    <?php echo $programme['total_heures']; ?>h
                  </span>
                </td>
                <td>
                  <span class="coefficient-display">
                    <?php echo number_format($programme['coefficient_moyen'], 1); ?>
                  </span>
                </td>
                <td>
                  <?php echo $programme['annee_scolaire'] ?? '2024-2025'; ?>
                </td>
                <td>
                  <span class="status-badge <?php echo $programme['statut'] ?? 'actif'; ?>">
                    <?php echo ucfirst($programme['statut'] ?? 'Actif'); ?>
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn-mini primary" onclick="viewProgramDetails(<?php echo $programme['id']; ?>)">
                      <i class="fa fa-eye"></i>
                    </button>
                    <button class="action-btn-mini warning" onclick="editProgram(<?php echo $programme['id']; ?>)">
                      <i class="fa fa-edit"></i>
                    </button>
                    <button class="action-btn-mini info" onclick="duplicateProgram(<?php echo $programme['id']; ?>)">
                      <i class="fa fa-copy"></i>
                    </button>
                    <button class="action-btn-mini danger" onclick="deleteProgram(<?php echo $programme['id']; ?>)">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="quick-actions">
        <h3>Actions Rapides</h3>
        <div class="quick-actions-grid">
          <button class="quick-action-btn" onclick="createStandardProgram('primaire')">
            <i class="fa fa-child"></i>
            <span>Programme Type Primaire</span>
          </button>
          <button class="quick-action-btn" onclick="createStandardProgram('secondaire')">
            <i class="fa fa-graduation-cap"></i>
            <span>Programme Type Secondaire</span>
          </button>
          <button class="quick-action-btn" onclick="massAssignTeachers()">
            <i class="fa fa-users"></i>
            <span>Affectation en Masse</span>
          </button>
          <button class="quick-action-btn" onclick="generateSchedules()">
            <i class="fa fa-calendar"></i>
            <span>Générer Emplois du Temps</span>
          </button>
        </div>
      </div>

      <!-- Modals -->
      <!-- Modal pour nouveau programme -->
      <div class="modal fade" id="nouveauProgrammeModal" tabindex="-1" role="dialog" aria-labelledby="nouveauProgrammeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="nouveauProgrammeModalLabel">Créer un Nouveau Programme Scolaire</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="formNouveauProgramme">
                <div class="form-group">
                  <label for="nomProgramme">Nom du Programme</label>
                  <input type="text" class="form-control" id="nomProgramme" name="nomProgramme" required>
                </div>
                <div class="form-group">
                  <label for="descriptionProgramme">Description</label>
                  <textarea class="form-control" id="descriptionProgramme" name="descriptionProgramme"></textarea>
                </div>
                <div class="form-group">
                  <label for="classeProgramme">Classe</label>
                  <select class="form-control" id="classeProgramme" name="classeProgramme" required>
                    <option value="">Sélectionner une classe</option>
                    <?php foreach ($classes as $classe): ?>
                    <option value="<?php echo $classe['id']; ?>"><?php echo htmlspecialchars($classe['nom']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="sectionProgramme">Section</label>
                  <select class="form-control" id="sectionProgramme" name="sectionProgramme" required>
                    <option value="">Sélectionner une section</option>
                    <option value="primaire">Primaire</option>
                    <option value="secondaire">Secondaire</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="anneeScolaireProgramme">Année Scolaire</label>
                  <input type="text" class="form-control" id="anneeScolaireProgramme" name="anneeScolaireProgramme" value="<?php echo date('Y'); ?>-<?php echo date('y', strtotime('+1 year')); ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="statutProgramme">Statut</label>
                  <select class="form-control" id="statutProgramme" name="statutProgramme" required>
                    <option value="actif">Actif</option>
                    <option value="brouillon">Brouillon</option>
                    <option value="archive">Archivé</option>
                  </select>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                  <button type="submit" class="btn btn-primary">Créer le Programme</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal pour importation de programmes -->
      <div class="modal fade" id="importProgramsModal" tabindex="-1" role="dialog" aria-labelledby="importProgramsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="importProgramsModalLabel">Importer des Programmes Scolaires</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="formImportPrograms" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="fileImportPrograms">Fichier à importer</label>
                  <input type="file" class="form-control-file" id="fileImportPrograms" name="fileImportPrograms" accept=".csv, .xlsx, .xls" required>
                </div>
                <div class="form-group">
                  <label for="anneeScolaireImport">Année Scolaire</label>
                  <input type="text" class="form-control" id="anneeScolaireImport" name="anneeScolaireImport" value="<?php echo date('Y'); ?>-<?php echo date('y', strtotime('+1 year')); ?>" readonly>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                  <button type="submit" class="btn btn-primary">Importer les Programmes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal pour exportation de programmes -->
      <div class="modal fade" id="exportProgramsModal" tabindex="-1" role="dialog" aria-labelledby="exportProgramsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exportProgramsModalLabel">Exporter des Programmes Scolaires</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="formExportPrograms">
                <div class="form-group">
                  <label for="formatExport">Format d'exportation</label>
                  <select class="form-control" id="formatExport" name="formatExport" required>
                    <option value="csv">CSV</option>
                    <option value="xlsx">Excel (.xlsx)</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="anneeScolaireExport">Année Scolaire</label>
                  <input type="text" class="form-control" id="anneeScolaireExport" name="anneeScolaireExport" value="<?php echo date('Y'); ?>-<?php echo date('y', strtotime('+1 year')); ?>" readonly>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                  <button type="submit" class="btn btn-primary">Exporter les Programmes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </section>
  </div>

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">