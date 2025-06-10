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

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupération des classes depuis la base de données
$classes = [];
$query = "SELECT c.*, CONCAT(p.nom, ' ', p.prenom) as prof_nom 
          FROM classes c 
          LEFT JOIN professeurs p ON c.prof_id = p.id 
          ORDER BY c.nom";
$result = $mysqli->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
    $result->free();
}

// Gestion de la suppression d'une classe
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Vérifier si la classe est utilisée par des élèves
    $check_query = "SELECT COUNT(*) as count FROM eleves WHERE classe_id = ?";
    $stmt = $mysqli->prepare($check_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $check_result = $stmt->get_result();
    $row = $check_result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['message'] = "Impossible de supprimer cette classe car elle est associée à des élèves.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Supprimer la classe
        $delete_query = "DELETE FROM classes WHERE id = ?";
        $stmt = $mysqli->prepare($delete_query);
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Classe supprimée avec succès.";
            $_SESSION['message_type'] = "success";
            
            // Rediriger pour éviter la soumission multiple
            header("Location: " . BASE_URL . "index.php?controller=Admin&action=classes");
            exit();
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression de la classe: " . $mysqli->error;
            $_SESSION['message_type'] = "danger";
        }
    }
    
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Classes</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Classes
        <small>Liste des classes</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Classes</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des classes</h3>
              <div class="box-tools">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse" class="btn btn-primary btn-sm">
                  <i class="fa fa-plus"></i> Ajouter une classe
                </a>
                <button type="button" class="btn btn-success btn-sm" onclick="printContent('printable')">
                  <i class="fa fa-print"></i> Imprimer
                </button>
              </div>
            </div>
            
            <?php if(isset($_SESSION['message'])): ?>
              <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-<?php echo $_SESSION['message_type'] == 'success' ? 'check' : 'ban'; ?>"></i> Alerte!</h4>
                <?php echo $_SESSION['message']; ?>
              </div>
              <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <div class="box-body" id="printable">
              <table id="classesTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Niveau</th>
                    <th>Section</th>
                    <th>Titulaire</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($classes as $classe): ?>
                    <tr>
                      <td><?php echo $classe['id']; ?></td>
                      <td><?php echo $classe['niveau']; ?></td>
                      <td><?php echo isset($classe['niveau']) ? $classe['niveau'] : 'N/A'; ?></td>
                      <td><?php echo $classe['section']; ?></td>
                      <td><?php echo isset($classe['titulaire']) ? $classe['titulaire'] : (isset($classe['prof_nom']) ? $classe['prof_nom'] : 'N/A'); ?></td>
                      <td>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=editClasse&id=<?php echo $classe['id']; ?>" class="btn btn-info btn-xs">
                          <i class="fa fa-pencil"></i> Modifier
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes&delete_id=<?php echo $classe['id']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette classe?');">
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
    $('#classesTable').DataTable({
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
    
    document.body.innerHTML = '<h1 style="text-align: center;">Liste des Classes</h1>' + content;
    window.print();
    document.body.innerHTML = originalContent;
    
    // Réinitialiser DataTables après l'impression
    $(function () {
      $('#classesTable').DataTable({
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