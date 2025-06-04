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

// Récupération des élèves de la section secondaire avec information de classe
$query = "SELECT e.*, c.niveau as classe_nom 
          FROM eleves e 
          LEFT JOIN classes c ON e.classe_id = c.id
          WHERE e.section = 'secondaire'
          ORDER BY e.nom, e.prenom";
$result = $mysqli->query($query);

$eleves = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eleves[] = $row;
    }
}


// Récupérer les informations de l'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études'; // Spécifique au rôle
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// $displayed_session_name = "Toutes les sessions"; // À adapter si le filtre est implémenté

$eleves_total_for_display = count($eleves);

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Gestion des Élèves</title>
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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    @media print {
      .no-print, .no-print * {
        display: none !important;
      }
      .content-wrapper, .main-footer, .main-header {
        margin-left: 0 !important;
        padding-top: 0 !important;
        -webkit-transform: none !important;
        transform: none !important;
      }
      .box-header .box-title, .content-header h1 {
        font-size: 18px;
      }
    }
  </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">
  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>Système</b> Gestion</span>
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
              </li>              <li class="user-footer">
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
        Gestion des Élèves
        <small>Liste des élèves inscrits</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtudes&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Élèves</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Élèves (Total: <?php echo $eleves_total_for_display; ?>)</h3>
              
            </div>
            
            <div class="box-body">
              <div class="row no-print" style="margin-bottom: 15px;">
                <div class="col-xs-12">
                  <button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Imprimer</button>
                  <!-- <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtudes&action=exportEleves" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Exporter Excel</a> -->
                </div>
              </div>
              
              <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Succès!</h4>
                    <?php echo $_SESSION['success_message']; ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
              <?php endif; ?>

              <?php if (isset($_SESSION['error_message'])): ?>
                  <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
                      <?php echo $_SESSION['error_message']; ?>
                  </div>
                  <?php unset($_SESSION['error_message']); ?>
              <?php endif; ?> 

              <div class="table-responsive">
                <table id="elevesTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Photo</th>
                      <th>Matricule</th>
                      <th>Nom</th>
                      <th>Post-nom</th>
                      <th>Prénom</th>
                      <th>Date Naiss.</th>
                      <th>Classe</th>
                      <th>Section</th>
                      <th>Statut</th>
                      <th class="no-print">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($eleves)): ?>
                      <?php foreach ($eleves as $eleve) : ?>
                        <tr>
                          <td><?php echo htmlspecialchars($eleve['id']); ?></td>
                          <td><img src="<?php echo !empty($eleve['photo']) ? BASE_URL . htmlspecialchars($eleve['photo']) : BASE_URL . 'dist/img/default-student.png'; ?>" alt="Photo élève" class="img-circle" style="width: 40px; height: 40px;"></td>
                          <td><?php echo htmlspecialchars($eleve['matricule']); ?></td>
                          <td><?php echo htmlspecialchars($eleve['nom']); ?></td>
                          <td><?php echo htmlspecialchars($eleve['post_nom']); ?></td>
                          <td><?php echo htmlspecialchars($eleve['prenom']); ?></td>
                          <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($eleve['date_naissance']))); ?></td>
                          <td><?php echo htmlspecialchars($eleve['classe_nom']); ?></td> <!-- Assurez-vous que classe_nom est dans les données -->
                          <td><?php echo htmlspecialchars($eleve['section']); ?></td> <!-- Assurez-vous que section_nom est dans les données -->
                          <td>
                            <?php 
                                $statut = isset($eleve['statut']) ? $eleve['statut'] : 'Actif'; // Valeur par défaut
                                $badge_class = 'label-success';
                                if ($statut === 'Inactif') {
                                    $badge_class = 'label-danger';
                                } elseif ($statut === 'Suspendu') {
                                    $badge_class = 'label-warning';
                                } elseif ($statut === 'Diplômé') {
                                    $badge_class = 'label-info';
                                }
                                echo "<span class='label $badge_class'>" . htmlspecialchars($statut) . "</span>"; 
                            ?>
                          </td>                          <td class="no-print">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=voirEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-xs" title="Voir détails"><i class="fa fa-eye"></i></a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=carteEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-success btn-xs" title="Générer carte"><i class="fa fa-id-card"></i></a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center">Aucun élève trouvé.</td>
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
    <div class="pull-right hidden-xs no-print">
      <b>Version</b> 1.0.0
    </div>
    <strong class="no-print">Copyright &copy; <?php echo date('Y'); ?> <a href="#">Votre École</a>.</strong> Tous droits réservés.
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
    $('#elevesTable').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'language': {
        'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
      },
      'columnDefs': [
        { 'orderable': false, 'targets': [1, 10] } // Désactiver le tri pour la photo et les actions
      ]
    });
  });
</script>
</body>
</html>