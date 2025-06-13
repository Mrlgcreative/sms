<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer les informations de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupération des directeurs depuis la table users
$directeurs = [];
$query = "SELECT id, username, email, telephone as contact, adresse FROM users WHERE role = 'director' ORDER BY username, email";
$result = $mysqli->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $directeurs[] = $row;
    }
    $result->free();
}

// Gestion de la suppression d'un directeur
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $delete_query = "DELETE FROM users WHERE id = ? AND role = 'director'";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Directeur supprimé avec succès.";
        $_SESSION['message_type'] = "success";
        
        // Rediriger pour éviter la soumission multiple
        header("Location: " . BASE_URL . "index.php?controller=Admin&action=directeurs");
        exit();
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du directeur: " . $mysqli->error;
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Directeurs</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>
             

  
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Directeurs
        <small>Liste des directeurs</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Directeurs</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des directeurs</h3>
              <div class="box-tools">
                
                <button type="button" class="btn btn-success btn-sm no-print" onclick="printContent('printable')">
                  <i class="fa fa-print"></i> Imprimer
                </button>
              </div>
            </div>
            
            <?php if(isset($_SESSION['message'])): ?>
              <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible no-print">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-<?php echo $_SESSION['message_type'] == 'success' ? 'check' : 'ban'; ?>"></i> Alerte!</h4>
                <?php echo $_SESSION['message']; ?>
              </div>
              <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <div class="box-body" id="printable">
              <table id="directeursTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>N°</th>
                    <th>Nom</th>
                   
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Adresse</th>
                   
                    <th class="no-print">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $index = 0; foreach ($directeurs as $directeur): $index++; ?>
                    <tr>
                      <td><?php echo $index; ?></td>
                      <td><?php echo $directeur['username']; ?></td>
                      <td><?php echo $directeur['contact']; ?></td>
                      <td><?php echo $directeur['email']; ?></td>
                      <td><?php echo $directeur['adresse']; ?></td>
                     
                      <td class="no-print">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=editdirecteur&id=<?php echo $directeur['id']; ?>" class="btn btn-info btn-xs">
                          <i class="fa fa-pencil"></i> Modifier
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs&delete_id=<?php echo $directeur['id']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce directeur?');">
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
<script>
  $(function () {
    $('#directeursTable').DataTable({
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
  });
  
  function printContent(elementId) {
    var content = document.getElementById(elementId).innerHTML;
    var originalContent = document.body.innerHTML;
    
    document.body.innerHTML = '<h1 style="text-align: center;">Liste des Directeurs</h1>' + content;
    window.print();
    document.body.innerHTML = originalContent;
    
    // Réinitialiser DataTables après l'impression
    $(function () {
      $('#directeursTable').DataTable({
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
    });
  }
</script>
</body>
</html>