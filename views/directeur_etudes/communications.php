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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">

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
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-envelope"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Communications</span>
              <span class="info-box-number"><?php echo $stats['total_communications']; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Publiées</span>
              <span class="info-box-number"><?php echo $stats['communications_publiees']; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-edit"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Brouillons</span>
              <span class="info-box-number"><?php echo $stats['communications_brouillon']; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Urgentes</span>
              <span class="info-box-number"><?php echo $stats['communications_urgentes']; ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main row -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Communications</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#nouvelleCommunicationModal">
                  <i class="fa fa-plus"></i> Nouvelle Communication
                </button>
              </div>
            </div>
            <div class="box-body">
              <table id="communicationsTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Urgence</th>
                    <th>Auteur</th>
                    <th>Date</th>
                    <th>Destinataires</th>
                    <th>Statut</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($communications as $communication): ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($communication['titre']); ?></strong>
                      <?php if ($communication['urgence'] == 'haute'): ?>
                        <i class="fa fa-exclamation-triangle text-red" title="Communication urgente"></i>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="label label-<?php 
                        echo $communication['type'] == 'annonce' ? 'info' : 
                            ($communication['type'] == 'circulaire' ? 'primary' : 'default'); 
                      ?>">
                        <?php echo ucfirst($communication['type']); ?>
                      </span>
                    </td>
                    <td>
                      <span class="label label-<?php 
                        echo $communication['urgence'] == 'haute' ? 'danger' : 
                            ($communication['urgence'] == 'moyenne' ? 'warning' : 'success'); 
                      ?>">
                        <?php echo ucfirst($communication['urgence']); ?>
                      </span>
                    </td>
                    <td>
                      <?php echo htmlspecialchars($communication['auteur_nom'] . ' ' . $communication['auteur_prenom']); ?>
                    </td>
                    <td>
                      <?php echo date('d/m/Y H:i', strtotime($communication['date_creation'])); ?>
                    </td>
                    <td>
                      <span class="badge bg-blue">
                        <?php echo $communication['nb_destinataires_eleves']; ?> élèves
                      </span>
                      <br>
                      <small class="text-muted">
                        <?php echo $communication['nb_destinataires_classes']; ?> classes
                      </small>
                    </td>
                    <td>
                      <?php if ($communication['statut'] == 'publiee'): ?>
                        <span class="label label-success">Publiée</span>
                      <?php elseif ($communication['statut'] == 'brouillon'): ?>
                        <span class="label label-warning">Brouillon</span>
                      <?php else: ?>
                        <span class="label label-default">Archivée</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs" title="Voir détails">
                          <i class="fa fa-eye"></i>
                        </button>
                        <?php if ($communication['statut'] == 'brouillon'): ?>
                        <button type="button" class="btn btn-success btn-xs" title="Publier">
                          <i class="fa fa-send"></i>
                        </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-warning btn-xs" title="Modifier">
                          <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-xs" title="Dupliquer">
                          <i class="fa fa-copy"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-xs" title="Supprimer">
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
        </div>
      </div>

      <!-- Communications récentes -->
      <div class="row">
        <div class="col-md-8">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Communications Récentes</h3>
            </div>
            <div class="box-body">
              <?php foreach (array_slice($communications, 0, 5) as $communication): ?>
              <div class="post">
                <div class="user-block">
                  <img class="img-circle img-bordered-sm" src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" alt="user image">
                  <span class="username">
                    <a href="#"><?php echo htmlspecialchars($communication['auteur_nom'] . ' ' . $communication['auteur_prenom']); ?></a>
                    <?php if ($communication['urgence'] == 'haute'): ?>
                      <span class="label label-danger pull-right">URGENT</span>
                    <?php endif; ?>
                  </span>
                  <span class="description">Publié le <?php echo date('d/m/Y à H:i', strtotime($communication['date_creation'])); ?></span>
                </div>
                <h4><?php echo htmlspecialchars($communication['titre']); ?></h4>
                <p><?php echo htmlspecialchars(substr($communication['contenu'], 0, 150)) . '...'; ?></p>
                <ul class="list-inline">
                  <li><a href="#" class="link-black text-sm"><i class="fa fa-users margin-r-5"></i> <?php echo $communication['nb_destinataires_eleves']; ?> destinataires</a></li>
                  <li class="pull-right">
                    <a href="#" class="link-black text-sm">
                      <i class="fa fa-eye margin-r-5"></i> Voir plus
                    </a>
                  </li>
                </ul>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <!-- Actions rapides -->
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Actions Rapides</h3>
            </div>
            <div class="box-body">
              <button type="button" class="btn btn-block btn-primary btn-sm margin-bottom">
                <i class="fa fa-bullhorn"></i> Annonce Générale
              </button>
              <button type="button" class="btn btn-block btn-warning btn-sm margin-bottom">
                <i class="fa fa-exclamation-triangle"></i> Communication Urgente
              </button>
              <button type="button" class="btn btn-block btn-info btn-sm margin-bottom">
                <i class="fa fa-file-text"></i> Circulaire Administrative
              </button>
              <button type="button" class="btn btn-block btn-success btn-sm">
                <i class="fa fa-calendar"></i> Programmation d'Événement
              </button>
            </div>
          </div>

          <!-- Statistiques d'aujourd'hui -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Aujourd'hui</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-xs-12">
                  <div class="description-block">
                    <h5 class="description-header"><?php echo $stats['communications_aujourd_hui']; ?></h5>
                    <span class="description-text">NOUVELLES COMMUNICATIONS</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
