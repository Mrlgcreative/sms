<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Frais</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<style>
  @media print {
      .no-print {
        display: none;
      }
    }
</style>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>Système</b> Gestion</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo BASE_URL . $image; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $username; ?> - <?php echo $role; ?>
                  <small><?php echo $email; ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=achatFournitures">
            <i class="fa fa-shopping-cart"></i> <span>Achats Fournitures</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=gestionStock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-dollar"></i> <span>Frais</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutfrais"><i class="fa fa-circle-o"></i> Ajouter frais</a></li>
            <li class="active"><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=frais"><i class="fa fa-circle-o"></i> Voir Frais</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutprofesseur"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Préfets</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=prefets"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span>Direction</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs"><i class="fa fa-circle-o"></i> Voir Directeurs</a></li>
            
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directrices"><i class="fa fa-circle-o"></i> Voir Directrices</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-calculator"></i> <span>Comptables</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=comptable"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Classes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span>Cours</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutcours"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Employés</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutemployes"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=rapportactions">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Frais
        <small>Liste des frais scolaires</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Frais</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des frais scolaires</h3>
              <div class="box-tools">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutfrais" class="btn btn-primary btn-sm">
                  <i class="fa fa-plus"></i> Ajouter un frais
                </a>
                <button class="btn btn-success btn-sm" onclick="printContent('printable')">
                  <i class="fa fa-print"></i> Imprimer
                </button>
              </div>
            </div>
            
            <div class="box-body" id="printable">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Montant</th>
                    <th>Description</th>
                    <th>Section</th>
                    <th class="no-print" >Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($frais as $frai) : ?>
                    <tr>
                      <td><?php echo $frai['id']; ?></td>
                      <td><?php echo $frai['montant']; ?></td>
                      <td><?php echo $frai['description']; ?></td>
                      <td><?php echo $frai['section']; ?></td>
                      <td class="no-print">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=editFrais&id=<?php echo $frai['id']; ?>" class="btn btn-warning btn-xs">
                          <i class="fa fa-edit"></i> Modifier
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=deleteFrais&id=<?php echo $frai['id']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce frais?');">
                          <i class="fa fa-trash"></i> Supprimer
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
               
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SGS - Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo BASE_URL; ?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo BASE_URL; ?>dist/js/demo.js"></script>
<!-- Page script -->
<script>
  $(function () {
    $('#example1').DataTable({
      'responsive': true,
      'autoWidth': false,
      'language': {
        'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
      }
    });
  });

  function printContent(elementId) {
    var content = document.getElementById(elementId).innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = '<h1 style="text-align: center;">Liste des Frais Scolaires</h1>' + content;
    window.print();
    document.body.innerHTML = originalContent;
  }
</script>
</body>
</html>