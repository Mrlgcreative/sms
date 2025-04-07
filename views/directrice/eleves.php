<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';

// Récupérer la classe depuis l'URL si elle est spécifiée
$classe = isset($_GET['classe']) ? $_GET['classe'] : '';

// Récupérer les messages d'erreur ou de succès
$error_message = isset($_GET['message']) && isset($_GET['error']) ? urldecode($_GET['message']) : '';
$success_message = isset($_GET['message']) && isset($_GET['success']) ? urldecode($_GET['message']) : '';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Élèves Maternelle</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b><?php echo $role; ?></b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
                <p><?php echo $role; ?></p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Rechercher...">
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </form>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves">
            <i class="fa fa-users"></i> <span>Élèves Maternelle</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=classes">
            <i class="fa fa-graduation-cap"></i> <span>Classes</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=achats">
            <i class="fa fa-shopping-cart"></i> <span>Achats</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=stock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=evenements">
            <i class="fa fa-calendar"></i> <span>Événements</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=statistiques">
            <i class="fa fa-bar-chart"></i> <span>Statistiques</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil">
            <i class="fa fa-user"></i> <span>Profil</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Élèves de la Section Maternelle
        <?php if (!empty($classe)): ?>
        <small>Classe: <?php echo $classe; ?></small>
        <?php endif; ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Élèves Maternelle</li>
        <?php if (!empty($classe)): ?>
        <li class="active"><?php echo $classe; ?></li>
        <?php endif; ?>
      </ol>
    </section>

    <section class="content">
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des élèves de maternelle<?php echo !empty($classe) ? ' - Classe ' . $classe : ''; ?></h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      Filtrer par classe <span class="fa fa-caret-down"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves">Toutes les classes</a></li>
                      <li class="divider"></li>
                      <?php
                      // Connexion à la base de données
                      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                      
                      if ($mysqli->connect_error) {
                          die("Erreur de connexion: " . $mysqli->connect_error);
                      }
                      
                      // Récupérer les classes disponibles
                      $result_classes = $mysqli->query("
                          SELECT DISTINCT classe 
                          FROM eleves 
                          WHERE section = 'maternelle' 
                          ORDER BY classe
                      ");
                      
                      while ($row = $result_classes->fetch_assoc()) {
                          echo '<li><a href="' . BASE_URL . 'index.php?controller=directrice&action=eleves&classe=' . urlencode($row['classe']) . '">' . $row['classe'] . '</a></li>';
                      }
                      ?>
                    </ul>
                  </div>
                  <input type="text" name="table_search" class="form-control pull-right" placeholder="Rechercher...">
                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive">
              <table id="eleves-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Post-nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
                    <th>Sexe</th>
                    <th>Date de naissance</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Construire la requête SQL en fonction de la présence ou non d'une classe spécifique
                  $query = "SELECT e.*, o.nom AS option_nom 
                            FROM eleves e 
                            LEFT JOIN options o ON e.option_id = o.id 
                            WHERE e.section = 'maternelle'";
                  
                  if (!empty($classe)) {
                      $query .= " AND e.classe = '" . $mysqli->real_escape_string($classe) . "'";
                  }
                  
                  $query .= " ORDER BY e.nom, e.post_nom, e.prenom";
                  
                  $result = $mysqli->query($query);
                  
                  if ($result) {
                      while ($eleve = $result->fetch_assoc()) {
                  ?>
                  <tr>
                    <td><?php echo $eleve['id']; ?></td>
                    <td><?php echo $eleve['nom']; ?></td>
                    <td><?php echo $eleve['post_nom']; ?></td>
                    <td><?php echo $eleve['prenom']; ?></td>
                    <td><?php echo $eleve['classe']; ?></td>
                    <td><?php echo $eleve['sexe'] == 'M' ? 'Garçon' : 'Fille'; ?></td>
                    <td><?php echo !empty($eleve['date_naissance']) ? date('d/m/Y', strtotime($eleve['date_naissance'])) : 'Non renseigné'; ?></td>
                    <!-- In the actions column of the students table -->
                    <td>
                      <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=viewStudent&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-xs">
                        <i class="fa fa-eye"></i> Détails
                      </a>
                      <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=carte&id=<?php echo $eleve['id']; ?>" class="btn btn-primary btn-xs">
                        <i class="fa fa-id-card"></i> Carte
                      </a>
                    </td>
                  </tr>
                  <?php
                      }
                  }
                  
                  // Fermer la connexion à la base de données
                  $mysqli->close();
                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Post-nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
                    <th>Sexe</th>
                    <th>Date de naissance</th>
                    <th>Actions</th>
                  </tr>
                </tfoot>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    $('#eleves-table').DataTable({
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
    
    // Recherche en temps réel
    $('input[name="table_search"]').on('keyup', function() {
      $('#eleves-table').DataTable().search($(this).val()).draw();
    });
  });
</script>
</body>
</html>