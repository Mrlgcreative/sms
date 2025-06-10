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

  <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

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