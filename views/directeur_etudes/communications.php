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

// Récupération des communications/annonces
$query = "SELECT c.*, 
                 u.nom as auteur_nom, u.prenom as auteur_prenom,
                 COUNT(cl.id) as nb_destinataires_classes,
                 COUNT(DISTINCT e.id) as nb_destinataires_eleves
          FROM communications c 
          LEFT JOIN users u ON c.auteur_id = u.id
          LEFT JOIN communication_classes cc ON c.id = cc.communication_id
          LEFT JOIN classes cl ON cc.classe_id = cl.id
          LEFT JOIN eleves e ON cl.id = e.classe_id
          GROUP BY c.id
          ORDER BY c.date_creation DESC";
$result = $mysqli->query($query);

$communications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $communications[] = $row;
    }
}

// Statistiques des communications
$stats_query = "SELECT 
                  COUNT(c.id) as total_communications,
                  COUNT(CASE WHEN c.statut = 'publiee' THEN 1 END) as communications_publiees,
                  COUNT(CASE WHEN c.statut = 'brouillon' THEN 1 END) as communications_brouillon,
                  COUNT(CASE WHEN c.urgence = 'haute' THEN 1 END) as communications_urgentes,
                  COUNT(CASE WHEN DATE(c.date_creation) = CURDATE() THEN 1 END) as communications_aujourd_hui
                FROM communications c";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Récupération des classes pour le formulaire
$classes_query = "SELECT id, niveau, section FROM classes ORDER BY section, niveau";
$classes_result = $mysqli->query($classes_query);
$classes = [];
if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
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
  <title>SMS | Communications</title>
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
  <!-- Communications Styles -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/communications.css">

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
      </div>      <!-- Sidebar Menu -->      <ul class="sidebar-menu" data-widget="tree">
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

      </ul>
    </section>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <h1>
        Communications
        <small>Annonces et communications scolaires</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>views/directeur_etudes/dashboard.php"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Communications</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- En-tête moderne -->
      <div class="communications-header">
        <div class="header-content">
          <h1>Communications</h1>
          <p>Gestion des annonces et communications scolaires</p>
        </div>
        <div class="header-actions">
          <button class="action-btn primary" data-toggle="modal" data-target="#nouvelleCommunicationModal">
            <i class="fa fa-plus"></i>
            Nouvelle Communication
          </button>
          <button class="action-btn secondary" onclick="exportCommunications()">
            <i class="fa fa-download"></i>
            Exporter
          </button>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="stats-grid">
        <div class="stat-card primary">
          <div class="stat-icon">
            <i class="fa fa-envelope"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['total_communications']; ?></div>
            <div class="stat-label">Total Communications</div>
          </div>
          <div class="stat-trend positive">
            <i class="fa fa-arrow-up"></i>
            <span>+12%</span>
          </div>
        </div>

        <div class="stat-card success">
          <div class="stat-icon">
            <i class="fa fa-check"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['communications_publiees']; ?></div>
            <div class="stat-label">Publiées</div>
          </div>
          <div class="stat-trend positive">
            <i class="fa fa-arrow-up"></i>
            <span>+8%</span>
          </div>
        </div>

        <div class="stat-card warning">
          <div class="stat-icon">
            <i class="fa fa-edit"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['communications_brouillon']; ?></div>
            <div class="stat-label">Brouillons</div>
          </div>
          <div class="stat-trend stable">
            <i class="fa fa-minus"></i>
            <span>0%</span>
          </div>
        </div>

        <div class="stat-card danger">
          <div class="stat-icon">
            <i class="fa fa-exclamation-triangle"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?php echo $stats['communications_urgentes']; ?></div>
            <div class="stat-label">Urgentes</div>
          </div>
          <div class="stat-trend negative">
            <i class="fa fa-arrow-down"></i>
            <span>-15%</span>
          </div>
        </div>
      </div>

      <!-- Filtres et recherche -->
      <div class="filters-container">
        <div class="search-box">
          <input type="text" id="searchCommunications" placeholder="Rechercher une communication...">
          <i class="fa fa-search"></i>
        </div>
        <div class="filter-buttons">
          <button class="filter-btn active" data-filter="all">Toutes</button>
          <button class="filter-btn" data-filter="publiee">Publiées</button>
          <button class="filter-btn" data-filter="brouillon">Brouillons</button>
          <button class="filter-btn" data-filter="urgente">Urgentes</button>
        </div>
      </div>

      <!-- Liste des communications -->
      <div class="communications-grid">
        <?php foreach ($communications as $communication): ?>
        <div class="communication-card" data-status="<?php echo $communication['statut']; ?>" data-urgence="<?php echo $communication['urgence']; ?>">
          <div class="card-header">
            <div class="card-title">
              <h3><?php echo htmlspecialchars($communication['titre']); ?></h3>
              <?php if ($communication['urgence'] == 'haute'): ?>
                <span class="urgency-badge urgent">
                  <i class="fa fa-exclamation-triangle"></i>
                  Urgent
                </span>
              <?php endif; ?>
            </div>
            <div class="card-meta">
              <span class="meta-item">
                <i class="fa fa-user"></i>
                <?php echo htmlspecialchars($communication['auteur_nom'] . ' ' . $communication['auteur_prenom']); ?>
              </span>
              <span class="meta-item">
                <i class="fa fa-clock-o"></i>
                <?php echo date('d/m/Y H:i', strtotime($communication['date_creation'])); ?>
              </span>
            </div>
          </div>

          <div class="card-content">
            <div class="communication-excerpt">
              <?php echo substr(strip_tags($communication['contenu']), 0, 150) . '...'; ?>
            </div>
            
            <div class="communication-tags">
              <span class="tag type-<?php echo $communication['type']; ?>">
                <?php echo ucfirst($communication['type']); ?>
              </span>
              <span class="tag urgence-<?php echo $communication['urgence']; ?>">
                <?php echo ucfirst($communication['urgence']); ?>
              </span>
              <span class="tag status-<?php echo $communication['statut']; ?>">
                <?php echo ucfirst($communication['statut']); ?>
              </span>
            </div>

            <div class="communication-stats">
              <div class="stat-item">
                <i class="fa fa-users"></i>
                <span><?php echo $communication['nb_destinataires_eleves']; ?> élèves</span>
              </div>
              <div class="stat-item">
                <i class="fa fa-university"></i>
                <span><?php echo $communication['nb_destinataires_classes']; ?> classes</span>
              </div>
            </div>
          </div>

          <div class="card-actions">
            <button class="action-btn-small primary" onclick="voirCommunication(<?php echo $communication['id']; ?>)">
              <i class="fa fa-eye"></i>
              Voir
            </button>
            <button class="action-btn-small secondary" onclick="modifierCommunication(<?php echo $communication['id']; ?>)">
              <i class="fa fa-edit"></i>
              Modifier
            </button>
            <?php if ($communication['statut'] == 'brouillon'): ?>
            <button class="action-btn-small success" onclick="publierCommunication(<?php echo $communication['id']; ?>)">
              <i class="fa fa-send"></i>
              Publier
            </button>
            <?php endif; ?>
            <button class="action-btn-small danger" onclick="supprimerCommunication(<?php echo $communication['id']; ?>)">
              <i class="fa fa-trash"></i>
              Supprimer
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Actions rapides -->
      <div class="quick-actions">
        <h3>Actions Rapides</h3>
        <div class="quick-actions-grid">
          <button class="quick-action-btn" data-toggle="modal" data-target="#nouvelleAnnonceModal">
            <i class="fa fa-bullhorn"></i>
            <span>Annonce Générale</span>
          </button>
          <button class="quick-action-btn urgent" data-toggle="modal" data-target="#communicationUrgenteModal">
            <i class="fa fa-exclamation-triangle"></i>
            <span>Communication Urgente</span>
          </button>
          <button class="quick-action-btn" data-toggle="modal" data-target="#circulaireModal">
            <i class="fa fa-file-text"></i>
            <span>Circulaire Administrative</span>
          </button>
          <button class="quick-action-btn" data-toggle="modal" data-target="#evenementModal">
            <i class="fa fa-calendar"></i>
            <span>Programmation d'Événement</span>
          </button>
        </div>
      </div>
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

<!-- Nouvelle Communication Modal -->
<div class="modal fade" id="nouvelleCommunicationModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Nouvelle Communication</h4>
      </div>
      <div class="modal-body">
        <form id="nouvelleCommunicationForm">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="urgence">Urgence</label>
                <select class="form-control" id="urgence" name="urgence">
                  <option value="basse">Basse</option>
                  <option value="moyenne">Moyenne</option>
                  <option value="haute">Haute</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="type">Type</label>
                <select class="form-control" id="type" name="type">
                  <option value="annonce">Annonce</option>
                  <option value="circulaire">Circulaire</option>
                  <option value="information">Information</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="destinataires">Destinataires</label>
                <select class="form-control" id="destinataires" name="destinataires[]" multiple>
                  <option value="tous">Toutes les classes</option>
                  <?php foreach ($classes as $classe): ?>
                  <option value="<?php echo $classe['id']; ?>">
                    <?php echo htmlspecialchars($classe['niveau'] . ' (' . ucfirst($classe['section']) . ')'); ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="contenu">Contenu *</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="8" required></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-warning">Enregistrer comme brouillon</button>
        <button type="button" class="btn btn-success">Publier maintenant</button>
      </div>
    </div>
  </div>
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
  $('#communicationsTable').DataTable({
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

  // Initialiser select multiple
  $('#destinataires').select2({
    placeholder: "Sélectionner les destinataires",
    allowClear: true
  });
});
</script>

</body>
</html>

<?php
$mysqli->close();
?>
